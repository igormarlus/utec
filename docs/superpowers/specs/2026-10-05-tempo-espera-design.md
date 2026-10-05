# Tempo de espera (check-in → início → fim) — Design

Data: 2026-10-05 · Origem: roadmap `docs/produto/2026-10-03-roadmap-aceleracao-utec.md` (onda 1, "Tempo médio de espera")
Status: aprovado no brainstorming · Branch: `feat/tempo-espera` (empilhada sobre `feat/ficha-paciente`, que contém os rótulos — a agenda já foi alterada por eles)

## Objetivo
Registrar **chegada** (check-in pela recepção), **início** e **fim** de cada atendimento e mostrar:
- na agenda, por atendimento: "Chegou 14:05 · esperou 18 min · consulta 32 min";
- em Relatórios clínicos (`adm/usuarios/relatorios_clinicos`, view `adm/relatorios/clinicos`): espera média, atraso médio e duração média no período/profissional filtrados + tabela por profissional.

Fora de escopo: painel de fila ao vivo com contador, "você é o próximo" pelo WhatsApp.

## Situação atual
`agendamentos.status`: 0 pendente · 1 em atendimento · 2 finalizado · 3 cancelado. Nenhum horário é gravado.
Todas as telas (agenda desktop/mobile, calendário, prontuário) mudam status por `Atendimento::set_status_agenda($id, $status_atual)`, que só aplica a transição 0→1, 1→2, 2→0, 3→0. Cancelamento manual: `Atendimento.php:~650` (status 3). Remarcação: `Atendimento::remarcar_agenda()`.

## Dados — migração `Dev::migrar_tempos_atendimento` (idempotente, nível 1)
Via `ensure_column` existente, em `agendamentos`:
`chegada_em DATETIME NULL`, `chegada_por INT NULL`, `inicio_atendimento_em DATETIME NULL`, `fim_atendimento_em DATETIME NULL`.

## Regras de gravação
Função pura `utec_tempo_campos_transicao($status_atual, $status_novo, $agora)` → array de colunas a atualizar:
| Transição | Campos |
|---|---|
| 0 → 1 (iniciar) | `inicio_atendimento_em = $agora`, `fim_atendimento_em = NULL` |
| 1 → 2 (finalizar) | `fim_atendimento_em = $agora` |
| 2 → 0 (reabrir) | `inicio_atendimento_em = NULL`, `fim_atendimento_em = NULL` (chegada mantida) |
| 3 → 0 (descancelar) | `chegada_em = NULL`, `chegada_por = NULL`, `inicio_atendimento_em = NULL`, `fim_atendimento_em = NULL` |
| qualquer outra | `array()` |

- `set_status_agenda()` mescla esse array no UPDATE de status **só se** as 4 colunas existirem (`field_exists('inicio_atendimento_em', 'agendamentos')` cobre a migração).
- `remarcar_agenda()`: após o UPDATE bem-sucedido, zera os 4 campos (mesma guarda).
- Cancelamento (status 3) não mexe nos horários.

**Check-in** — `Atendimento::checkin($id_agenda)` (POST):
- `can_access_agendamento($id)`; nível do logado 1–4; migração presente; senão 403/404/flash de erro.
- Ação `marcar` (padrão): só se `status = 0`, `data_agenda = hoje` e `chegada_em IS NULL` → grava `chegada_em = agora`, `chegada_por = id logado`.
- Ação `desfazer` (`acao=desfazer` no POST): só se `chegada_em` não é NULL e `inicio_atendimento_em IS NULL` → zera os dois.
- Regras de elegibilidade em função pura `utec_tempo_pode_checkin($ag, $hoje)` / `utec_tempo_pode_desfazer_checkin($ag)`.
- Redireciona para o campo `voltar` do POST quando ele casa com `#^adm/[a-z0-9_/]*$#i` (mesma regra dos rótulos), senão para `adm/atendimento`; flash `tempo_ok` / `tempo_erro`.

## Cálculos (helper `application/helpers/tempo_atendimento_helper.php`, puro)
- `utec_tempo_minutos_entre($de, $ate)` → int minutos (`$ate - $de`, arredondado para baixo) ou `null` se algum vazio/ inválido.
- `utec_tempo_metricas($ag)` (`$ag` com `data_agenda`, `hora_agenda`, `chegada_em`, `inicio_atendimento_em`, `fim_atendimento_em`) → `array('espera'=>?int, 'atraso'=>?int, 'duracao'=>?int)`:
  - espera = início − chegada; `null` se <0 ou >480;
  - atraso = início − (data_agenda + hora_agenda); `null` se |atraso| > 480; pode ser negativo;
  - duração = fim − início; `null` se <0 ou >480.
- `utec_tempo_formatar($min)` → `"18 min"`, `"1 h 05 min"`, `"-5 min"` (adiantado), `''` para `null`.
- `utec_tempo_resumo_agenda($ag)` → texto da agenda: partes não vazias entre "Chegou HH:MM", "esperou X", "consulta Y", unidas por " · ".

## Relatórios clínicos
`Usuarios::relatorios_clinicos()` já monta `$where_agenda_sql` (período, escopo, profissional). Acrescentar (só se as colunas existem):
- uma consulta agregada sobre `agendamentos a` com o mesmo WHERE:
  `AVG(CASE WHEN chegada/início válidos e 0 ≤ diff ≤ 480 THEN TIMESTAMPDIFF(MINUTE, a.chegada_em, a.inicio_atendimento_em) END)` e `COUNT` correspondente; idem atraso (`CONCAT(a.data_agenda,' ',a.hora_agenda)` → início, |diff| ≤ 480) e duração (início → fim, 0..480);
- a mesma agregação com `GROUP BY a.id_prestador` + `pr.nome`, ordenada por nome.
- View `adm/relatorios/clinicos`: 3 cards ("Espera média", "Atraso médio", "Duração média", valor formatado + "n atendimentos") e tabela "Tempos por profissional"; se as colunas não existem, a seção não aparece; se n = 0, mostra "Sem dados no período".

## Agenda (`views/adm/usuarios/new/atendimentos.php`)
- Desktop (linha da tabela) e mobile (fila e finalizados): abaixo do nome/horário, `utec_tempo_resumo_agenda($agenda)` em texto pequeno quando não vazio.
- Botão "Chegou" (form POST com `voltar`) quando `utec_tempo_pode_checkin`; "Desfazer chegada" (link discreto, POST) quando `utec_tempo_pode_desfazer_checkin`.
- `Atendimento::Index()` usa `SELECT a.*` — as colunas novas já vêm; a view carrega o helper (`get_instance()->load->helper('tempo_atendimento')`) e verifica as colunas com `field_exists` uma vez.

## Testes (`tests/tempo_atendimento_*`, PHP 7.2)
- Helper: cada linha da tabela de transição + transição inválida; minutos entre; métricas (ausentes, negativos, > 480, atraso negativo); formatação; resumo; elegibilidade do check-in (status ≠ 0, outro dia, já chegou) e do desfazer (já iniciado).
- Fonte: migração (4 colunas), `set_status_agenda` usa a função de transição com guarda `field_exists`, `remarcar_agenda` zera, `checkin` com `can_access_agendamento` + POST + nível, relatório com `TIMESTAMPDIFF`, views com os hooks e escape.

## Entrega
Manual: capítulo "Agenda" (check-in, tempos na agenda e médias em Relatórios clínicos). CLAUDE.md: colunas, rota `checkin`, migração.
Deploy (comparar servidor com a branch antes): helper → `Dev.php` → `Atendimento.php` → `Usuarios.php` → view `relatorios/clinicos` → `Manual_conteudo.php` → `atendimentos.php` por último. Rodar `adm/dev/migrar_tempos_atendimento`.
