# Horários de Atendimento do Profissional — Design

**Data:** 2026-09-22
**Status:** aprovado em brainstorming, aguardando revisão da spec
**Domínios:** clínico (agenda) + dev-infra (migração) + frontend (tela/avisos)

## 1. Objetivo

Permitir que cada profissional (prestador, nível 3) tenha uma **grade semanal de
atendimento**, uma **duração fixa de consulta** e **bloqueios pontuais**
(férias, feriado, tarde indisponível). A partir disso o sistema calcula os
horários livres confrontando a grade com os agendamentos existentes.

Motivação principal: no futuro próximo o chatbot de IA do WhatsApp vai consultar
esses horários livres para marcar consultas sem intervenção humana. Nesta
entrega, o cálculo também é usado para **avisar** (nunca bloquear) a recepção
na agenda manual.

## 2. Decisões tomadas

| Tema | Decisão |
|------|---------|
| Duração da consulta | Fixa por profissional, editável na mesma tela da grade. `NULL` = 30 min |
| Exceções | Grade semanal + bloqueios pontuais (início/fim DATETIME + motivo). Sem feriados automáticos |
| Quem edita | Nível 1 tudo; nível 2 os prestadores do seu escopo; nível 3 só o próprio; nível 4 **só visualiza** |
| Agenda manual | Mostra horários livres e alerta de encaixe; salvar continua sempre permitido |
| Armazenamento | Tabelas relacionais + helper puro de cálculo (abordagem 1) |
| Ocupação | Agendamentos com `status IN (0,1,2)` ocupam; `status = 3` (cancelado, inclusive via WhatsApp) libera. Agendamento sem `id_prestador` é ignorado |
| Duração de agendamentos existentes | Cada agendamento ocupa a duração **atual** do profissional (não há duração gravada por consulta) |
| Prestador sem grade | `tem_grade = false`; agenda manual segue exatamente como hoje, sem aviso |

## 3. Dados

Migração nova `adm/dev/migrar_horarios_atendimento` (idempotente, protegida por
`nivel == 1`, no padrão de `Dev.php` com `ensure_column`).

```sql
CREATE TABLE IF NOT EXISTS prestador_horarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_prestador INT NOT NULL,
  dia_semana TINYINT NOT NULL,        -- 0 = domingo ... 6 = sábado (date('w'))
  hora_inicio TIME NOT NULL,
  hora_fim TIME NOT NULL,
  created_at DATETIME NULL,
  INDEX idx_prest_dia (id_prestador, dia_semana)
);

CREATE TABLE IF NOT EXISTS prestador_bloqueios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_prestador INT NOT NULL,
  inicio DATETIME NOT NULL,
  fim DATETIME NOT NULL,
  motivo VARCHAR(150) NULL,
  id_user_cad INT NULL,
  created_at DATETIME NULL,
  INDEX idx_prest_inicio (id_prestador, inicio)
);

-- usuarios.duracao_atendimento_min INT NULL DEFAULT NULL
```

Sem `tenant_id` nas tabelas novas: o escopo deriva de `id_prestador` pela árvore
`id_user`, como no restante da agenda.

## 4. Tela `adm/horarios`

- Controller `application/controllers/adm/Horarios.php`, view
  `application/views/adm/horarios/index.php`.
- Item de menu "Horários de atendimento" em `includes/adm/menu.php`, níveis 1–4.
- **Seletor de profissional:** nível 3 abre direto no próprio; níveis 2 e 4
  escolhem entre os prestadores (nível 3) do escopo (`get_scope_user_ids`);
  nível 1 vê todos os prestadores.
- **Card Duração:** select com 10, 15, 20, 30, 40, 45, 50, 60, 90, 120 min.
- **Card Grade semanal:** 7 linhas (dom–sáb). Cada linha: checkbox "atende" +
  1..N intervalos `início–fim` com botão "+ intervalo". Atalho JS "copiar
  segunda para seg–sex". Um único POST (`adm/horarios/salvar`) grava duração +
  grade; a grade do prestador é substituída (DELETE + INSERT) numa transação.
- **Card Bloqueios:** lista dos bloqueios com `fim >= agora` (início, fim,
  motivo, excluir) + formulário de inclusão (`adm/horarios/bloquear`) com
  atalho "dia inteiro" (00:00–23:59). Exclusão: `adm/horarios/desbloquear/{id}`.
- **Validação no servidor:** horários `HH:MM`; `fim > início`; intervalos do
  mesmo dia sem sobreposição; `dia_semana` 0–6; duração dentro da lista
  permitida; bloqueio com `fim > inicio`. Erro → flashdata e volta à tela sem
  gravar nada.
- **Permissão** (`Horarios::pode_editar($id_prestador)`): nível 1 sempre;
  nível 3 somente `id_prestador == próprio id`; nível 2 se o prestador estiver
  no escopo; nível 4 nunca. Visualização: nível 1 ou prestador no escopo.
  POST sem permissão → 403. Nível 4 vê a tela com campos desabilitados e sem
  botões de salvar.

## 5. Cálculo de disponibilidade

### 5.1 Helper puro `application/helpers/disponibilidade_helper.php`

Sem acesso a banco; testável via CLI. Horários trabalhados em minutos desde
00:00 internamente.

- `utec_disp_gerar_slots(array $intervalos, int $duracao_min): array`
  Intervalos `[['08:00','12:00'], ...]` → lista ordenada de `'HH:MM'`. Slot cujo
  fim ultrapassa o fim do intervalo é descartado (08:00–12:00 com 50 min →
  08:00, 08:50, 09:40, 10:30; 11:20 é descartado).
- `utec_disp_remover_ocupados(array $slots, array $horas_agendadas, array $bloqueios_dia, int $duracao_min): array`
  Remove slot que se sobrepõe a `[hora_agendada, hora_agendada + duração)` de
  qualquer agendamento, ou a qualquer bloqueio do dia (bloqueios já recortados
  para o dia como pares `['HH:MM','HH:MM']`). Sobreposição = `a_ini < b_fim && b_ini < a_fim`.
- `utec_disp_recortar_bloqueios_no_dia(array $bloqueios, string $data): array`
  Converte bloqueios DATETIME (que podem atravessar vários dias) em pares de
  horário do dia informado.
- `utec_disp_classificar_horario(string $hora, array $intervalos, array $horas_agendadas, array $bloqueios_dia, int $duracao_min): string`
  Retorna `bloqueado` | `fora_da_grade` | `ocupado` | `livre` (nessa ordem de
  precedência). `fora_da_grade` = `[hora, hora+duração)` não cabe inteiro em
  nenhum intervalo do dia.

### 5.2 Model `application/models/Disponibilidade_model.php`

Guarda por `table_exists`/`field_exists` (padrão de `Whatsapp_model`): sem
schema → `tem_grade = false`, nunca erro.

- `get_config($id_prestador)` → `['duracao' => int, 'grade' => [dia => [[ini,fim],...]], 'tem_grade' => bool]`.
- `listar_bloqueios_futuros($id_prestador)`.
- `horarios_livres($id_prestador, $data, $ignorar_agendamento_id = 0)` →
  `['tem_grade' => bool, 'duracao' => int, 'livres' => ['HH:MM', ...]]`.
  Busca agendamentos do dia (`id_prestador`, `data_agenda`, `status IN (0,1,2)`),
  bloqueios que tocam o dia, aplica o helper. Se `$data` é hoje, remove
  horários `<=` agora (fuso do servidor, mesmo critério do cron de lembrete).
- `verificar_horario($id_prestador, $data, $hora, $ignorar_agendamento_id = 0)` →
  `['tem_grade' => bool, 'situacao' => livre|ocupado|fora_da_grade|bloqueado]`.
- `proximos_livres($id_prestador, $a_partir_de, $limite = 10)` → lista de
  `['data' => 'Y-m-d', 'hora' => 'HH:MM']`, varrendo no máximo 30 dias. Método
  destinado ao chatbot futuro; entra agora por ser barato e testável.

## 6. Integração com a agenda manual (somente aviso)

- Endpoint JSON `GET adm/horarios/livres?id_prestador=&data=&ignorar=` →
  `horarios_livres()` + checagem de visualização (403 fora do escopo).
- Nos formulários de **novo agendamento** e **remarcação** (views de
  atendimento/prontuário onde hoje existem `data_agenda`/`hora_agenda`): ao
  escolher profissional + data, JS busca o endpoint e mostra os livres como
  botões que preenchem `hora_agenda`. Ao digitar/alterar a hora, se ela não
  estiver entre os livres e `tem_grade = true`, exibe alerta amarelo "Horário
  fora da disponibilidade do profissional — será salvo como encaixe".
- Prestador sem grade: nada é exibido.
- `Atendimento::cadastrar()` e `Atendimento::remarcar_agenda()` **não mudam**:
  nenhuma validação nova no servidor.

## 7. Testes

- `tests/disponibilidade_helper_test.php` (padrão `php tests/...`, `assert` +
  `exit(1)`), cobrindo: geração de slots simples; slot parcial descartado;
  múltiplos intervalos no dia; sobreposição com agendamento (inclusive
  agendamento fora do passo da grade, ex. 08:15 com slots de 30 min);
  bloqueio parcial; bloqueio de dia inteiro; bloqueio que atravessa dias;
  classificação nas 4 situações.
- `php -l` em todos os arquivos PHP novos/alterados.
- Verificação manual local da tela e do endpoint, quando houver ambiente.

## 8. Fechamento

- Capítulo "Horários de atendimento" em `application/libraries/Manual_conteudo.php`
  para níveis 2, 3 e 4 (regra da seção 19 do CLAUDE.md).
- CLAUDE.md: tabelas novas (4.2), controller (6.2), model (7.x), migração (13).
- Deploy: arquivos via FTP + rodar `adm/dev/migrar_horarios_atendimento` em produção.

## 9. Fora do escopo

- Chatbot oferecendo ou marcando horários (próxima entrega, consumirá
  `Disponibilidade_model`).
- Duração por tipo de atendimento ou gravada por agendamento.
- Feriados automáticos.
- Bloqueio (hard) de agendamento fora da grade.
