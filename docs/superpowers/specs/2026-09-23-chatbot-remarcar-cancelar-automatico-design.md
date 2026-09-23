# Chatbot WhatsApp — Remarcação e Cancelamento Automáticos — Design

**Data:** 2026-09-23
**Status:** aprovado em brainstorming, aguardando revisão da spec
**Domínios:** whatsapp (chatbot) + clínico (agenda / disponibilidade)
**Depende de:** `docs/superpowers/specs/2026-09-22-horarios-atendimento-design.md` (em produção desde 2026-09-22)

## 1. Objetivo

Hoje o menu do paciente no chatbot (`Whatsapp_chatbot`) tem `Cancelar` e
`Remarcar`, mas ambos só **pedem um motivo e abrem uma solicitação** para a
equipe (`Notificacoes_model::criar_solicitacao_chatbot`). Nada muda na agenda.

Esta entrega faz o bot **executar** as duas ações sozinho, usando a grade de
atendimento e os agendamentos existentes (`Disponibilidade_model`), com as
ferramentas oficiais do WhatsApp (listas e botões interativos) — sem IA.

## 2. Decisões tomadas

| Tema | Decisão |
|------|---------|
| Escopo | Remarcar + cancelar automáticos. **Marcar consulta nova fica para depois** |
| Antecedência | Fixa em **24h**. Consulta a menos de 24h de agora → fluxo atual (motivo + solicitação para a equipe). Novo horário escolhido também precisa estar a ≥ 24h de agora |
| Motivo | Só no cancelamento, com botão "Prefiro não informar". Remarcação sem motivo |
| Profissional | Remarcação sempre com o **mesmo** `id_prestador` |
| Janela de busca | Até 30 dias à frente; lista de até 10 dias com vaga |
| Tecnologia | Listas (`utec_whatsapp_payload_lista`) e botões (`utec_whatsapp_payload_botoes`) já existentes. Sem WhatsApp Flows, sem IA |
| Aviso à equipe — cancelamento | Aviso interno + WhatsApp com o template já aprovado `agendamento_cancelado_equipe` (`Whatsapp_agendamento::notificar_equipe($contexto, 'cancelar')`) |
| Aviso à equipe — remarcação | Aviso interno. WhatsApp com template novo `agendamento_remarcado_equipe` **atrás de flag** (`whatsapp.notificar_remarcacao_equipe_ativo`, env `WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE=1`), desligado até a Meta aprovar |
| Status na agenda | Remarcada pelo bot = tratada como **confirmada via WhatsApp**; cancelada pelo bot = **cancelada via WhatsApp** |

## 3. Fluxo do paciente

Identificação, idempotência de eventos e sessão por telefone continuam como
hoje (`Whatsapp_chatbot::processar`, `whatsapp_chatbot_eventos`,
`whatsapp_chatbot_sessoes`).

### 3.1 Remarcar

1. Menu → **Remarcar** → lista das próximas consultas (já existe:
   `responder_lista_consultas`, ids `chat:paciente:remarcar:{id}`).
2. Paciente escolhe a consulta. O bot recarrega o agendamento com
   `obter_agendamento_chatbot()` (garante que é do paciente) e aplica a regra
   de 24h:
   - **< 24h** → fluxo atual inalterado (sessão `solicitacao/motivo`).
   - **Prestador sem grade** (`tem_grade = false`) → também fluxo atual, com
     texto "Este profissional ainda não tem horários online. Informe o motivo
     para a equipe remarcar."
   - **≥ 24h** → segue.
3. Lista **"Escolha o dia"**: até 10 dias com vaga do mesmo prestador, de
   `agora + 24h` até 30 dias, ignorando o próprio agendamento no cálculo.
   Linha: título `Qui 25/09`, descrição `8 horários livres`.
   Sem dias → texto "Não há horários livres nos próximos 30 dias. Informe o
   motivo para a equipe remarcar." e cai no fluxo de solicitação.
4. Lista **"Escolha o horário"** do dia: até 10 linhas. Se houver mais de 10
   horários, mostra 9 + linha **"Ver mais horários"** (página seguinte). Só
   horários a ≥ 24h de agora.
5. Botões: **"Confirmar"** (corpo: "Remarcar para Qui 25/09 às 14:30 com
   Dr. X?"), **"Outro dia"**, **"Voltar"**.
6. **Confirmar** → gravação atômica (seção 5). Sucesso → texto "Consulta
   remarcada para 25/09 às 14:30 com Dr. X." Horário tomado entre a escolha e
   a confirmação → "Esse horário acabou de ser ocupado. Escolha outro." e
   reenvia a lista de horários do mesmo dia (ou a de dias, se o dia esgotou).

### 3.2 Cancelar

1. Menu → **Cancelar** → lista das próximas consultas (já existe).
2. Regra de 24h: **< 24h** → fluxo atual (motivo + solicitação).
3. **≥ 24h** → bot pede o motivo em texto livre, com botão
   **"Prefiro não informar"** (e "Voltar"). Texto com < 3 caracteres →
   repete o pedido.
4. Botões: **"Sim, cancelar"** (corpo: "Cancelar a consulta de Qui 25/09 às
   14:30 com Dr. X?") / **"Voltar"**.
5. **Sim, cancelar** → gravação atômica (seção 5): `status = 3`. Texto
   "Consulta cancelada. Se quiser remarcar depois, é só chamar aqui."

### 3.3 Regras comuns

- "Voltar" em qualquer etapa limpa a sessão e mostra o menu.
- Clique em botão/lista antigo (sessão expirada, outra consulta, etapa
  diferente) → o servidor revalida tudo a partir do id do clique; se não
  fizer sentido, responde "Essa opção expirou." + menu. Nunca grava com dados
  só da sessão.
- Consulta já cancelada (`status = 3`) ou já passada → "Consulta não
  encontrada ou indisponível." (comportamento atual de
  `agendamento_paciente_valido`, estendido para data passada).
- A regra de 24h é reavaliada no momento de gravar (o paciente pode demorar).

## 4. Payloads (ids de lista/botão)

O regex atual de `extrair_comando` (`chat:perfil:comando[:id]`) continua
servindo o menu. Os passos novos usam um prefixo próprio, tratado antes:

| Passo | Id | Exemplo |
|-------|----|---------|
| Dia escolhido | `rem:{id_ag}:d:{Ymd}` | `rem:812:d:20260925` |
| Página de horários | `rem:{id_ag}:p:{Ymd}:{pagina}` | `rem:812:p:20260925:2` |
| Horário escolhido | `rem:{id_ag}:h:{Ymd}{Hi}` | `rem:812:h:202609251430` |
| Confirmar remarcação | `rem:{id_ag}:ok:{Ymd}{Hi}` | `rem:812:ok:202609251430` |
| Outro dia | `rem:{id_ag}:dias` | `rem:812:dias` |
| Pular motivo | `can:{id_ag}:sem_motivo` | `can:812:sem_motivo` |
| Confirmar cancelamento | `can:{id_ag}:ok` | `can:812:ok` |

Ids têm no máximo 200 caracteres (limite da Meta, folgado). O motivo do
cancelamento fica na sessão (`dados_json.motivo`), não no id.

## 5. Gravação atômica

Nova operação no model (nome proposto `Whatsapp_model::remarcar_agendamento_chatbot()`
e `cancelar_agendamento_chatbot()`), em transação:

1. `SELECT ... FROM agendamentos WHERE id = ? FOR UPDATE` — confere que ainda
   é do paciente (`id_paciente`), que `status = 0` (só consulta pendente;
   consulta em andamento, realizada ou cancelada não é alterada pelo bot) e
   que a data/hora atual da consulta ainda está a ≥ 24h de agora.
2. **Remarcar:** trava os agendamentos do prestador no dia de destino
   (`SELECT id FROM agendamentos WHERE id_prestador = ? AND data_agenda = ?
   FOR UPDATE`), chama `Disponibilidade_model::verificar_horario($id_prestador,
   $data, $hora, $id_ag)` e exige `situacao = 'livre'`, e o novo horário a
   ≥ 24h. Atualiza `data_agenda`, `hora_agenda`, `data_hora_agenda`,
   `status = 0`, `id_user_alt = id do paciente`.
3. **Cancelar:** `status = 3`, `id_user_alt = id do paciente`.
4. Registra linha em `whatsapp_notificacoes` com `tipo_notificacao =
   'chatbot_remarcado'` (`status_confirmacao = 'confirmado'`) ou
   `'chatbot_cancelado'` (`'cancelado'`), `status_envio = 'enviado'`,
   `respondido_em = agora`, `tenant_id`, `telefone_destino`. Como as consultas
   de "última resposta" pegam a linha mais recente (excluindo só
   `equipe_*`), a agenda e o prontuário passam a mostrar "Confirmado /
   Cancelado via WhatsApp" sem mudança nas views. Essas linhas **não**
   consomem a cota de 3 disparos do trial (não são envios de template).
5. Commit. Retorna `['ok' => bool, 'motivo_falha' => 'ocupado|prazo|indisponivel|erro', 'agendamento' => row]`.

Efeito colateral aceito: como o cron de lembrete pula agendamentos com
alguma linha `status_confirmacao = 'confirmado'`, a consulta remarcada pelo
bot não recebe lembrete — mesmo comportamento de uma consulta confirmada pelo
botão do template hoje.

## 6. Avisos para a equipe

Após o commit (falha de envio nunca desfaz a gravação):

- **Aviso interno** para quem marcou (`id_user`) e o profissional
  (`id_prestador`), deduplicado (`utec_notificacoes_destinatarios_agendamento`),
  tipos novos `whatsapp_chatbot_remarcado` / `whatsapp_chatbot_cancelado`.
  Mensagem: "Fulano remarcou a consulta de 23/09 14:00 para 25/09 14:30 pelo
  WhatsApp." / "Fulano cancelou a consulta de 25/09 14:30 pelo WhatsApp.
  Motivo: …" (ou "sem motivo informado"). URL: prontuário do paciente.
- **WhatsApp à equipe:**
  - cancelamento → `notificar_equipe($contexto, 'cancelar')` (template aprovado).
  - remarcação → `notificar_equipe($contexto, 'remarcar')` só se o flag
    `notificar_remarcacao_equipe_ativo` estiver ligado; template
    `agendamento_remarcado_equipe` (variáveis: paciente, data/hora antiga,
    data/hora nova, profissional) documentado em
    `docs/whatsapp-remarcacao-template-pendente.md` para submissão na Meta.

## 7. Organização do código

| Unidade | Responsabilidade |
|---------|------------------|
| `application/libraries/Whatsapp_chatbot_agenda.php` (nova) | Fluxos remarcar/cancelar: decide a etapa, monta listas/botões, chama o model, dispara avisos. Recebe `$perfil` e o evento; devolve o mesmo formato de resultado de `Whatsapp_chatbot` |
| `Whatsapp_chatbot` (alterado) | Só encaminha: ids `rem:`/`can:`, a escolha da consulta em `cancelar`/`remarcar` e as sessões dos fluxos novos passam para `Whatsapp_chatbot_agenda`. Fluxo `solicitacao/motivo` atual fica intacto |
| `whatsapp_agendamento_helper.php` (alterado) | Funções puras: montar/parsear ids `rem:`/`can:`, regra de 24h (`agora`, `data`, `hora` → bool), paginação de horários (9 + "ver mais"), rótulos `Qui 25/09`, textos de confirmação, tipos de aviso interno |
| `Disponibilidade_model` (alterado) | Novo `dias_com_vaga($id_prestador, $minimo_datetime, $dias = 30, $limite = 10, $ignorar = 0)` → `[['data'=>'Y-m-d','qtd'=>int], ...]`, carregando config uma vez e agendamentos/bloqueios do período em 2 consultas (resolve o débito de consultas repetidas do review anterior). `horarios_livres()` ganha 4º parâmetro opcional `$minimo_datetime = null`: quando informado, descarta horários anteriores a ele (usado para o corte de 24h). Chamadas atuais não mudam |
| `Whatsapp_model` (alterado) | `remarcar_agendamento_chatbot()`, `cancelar_agendamento_chatbot()` (seção 5) |
| `Notificacoes_model` (alterado) | `criar_aviso_chatbot_agenda($contexto, $acao, $dados)` para os 2 tipos novos |
| `Whatsapp_agendamento::notificar_equipe` + `utec_whatsapp_template_equipe_nome` (alterado) | Suporte à ação `remarcar` atrás do flag |
| `application/config/whatsapp.php` (alterado) | Flag `notificar_remarcacao_equipe_ativo` |

Sessão: `fluxo = 'agenda_remarcar'` (etapas `dia`, `hora`, `confirmar`) e
`fluxo = 'agenda_cancelar'` (etapas `motivo`, `confirmar`), `dados_json` com
`id_agendamento` e, no cancelamento, `motivo`.

## 8. Erros e degradação

- Tabelas de horários ausentes / prestador sem grade → fluxo de solicitação
  atual (nada quebra).
- Falha no envio da resposta do bot → registrada como hoje em
  `finalizar_evento_chatbot`; a gravação na agenda já feita permanece.
- Falha em aviso interno ou WhatsApp à equipe → logada, não desfaz nada.
- Reentrega do mesmo evento pela Meta → barrada pela idempotência existente.
- Duplo clique em "Confirmar" → a segunda execução encontra o agendamento já
  no novo horário; `verificar_horario` ignora o próprio id, então a data igual
  à atual é tratada como sucesso idempotente ("Sua consulta já está marcada
  para …"), sem nova linha de log nem novo aviso.

## 9. Testes

- **Puros** (`tests/whatsapp_chatbot_agenda_test.php`): parse/montagem de
  ids, regra de 24h nas bordas (exatamente 24h, 23h59), paginação de
  horários (0, 9, 10, 11, 25 itens), rótulos de dia da semana, textos.
- **Disponibilidade** (`tests/disponibilidade_helper_test.php` estendido): o
  cálculo por período usado por `dias_com_vaga` (função pura nova no helper
  de disponibilidade que recebe grade + agendamentos + bloqueios do período).
- **Fonte** (`tests/whatsapp_chatbot_agenda_source_test.php`): `FOR UPDATE`
  nas duas operações, `verificar_horario` chamado antes do UPDATE, fluxo
  `solicitacao/motivo` preservado, flag de remarcação na config, linhas
  `chatbot_*` não entram na contagem de cota.
- Testes existentes `tests/whatsapp_chatbot*_test.php` continuam passando.
- Verificação manual em produção com número de teste (não há ambiente local
  com banco): remarcar, cancelar com e sem motivo, consulta < 24h, horário
  ocupado entre escolha e confirmação.

## 10. Fechamento

- Manual (`Manual_conteudo.php`): tópico no capítulo de WhatsApp explicando
  que o paciente pode remarcar/cancelar sozinho com 24h de antecedência e
  como a equipe é avisada.
- CLAUDE.md §10.3.1: bloco "Remarcação/cancelamento automáticos".
- Deploy por FTP + submissão do template `agendamento_remarcado_equipe` na Meta.

## 11. Fora do escopo

- Paciente marcar consulta nova.
- Trocar de profissional na remarcação.
- Limite de remarcações por paciente / por consulta.
- Antecedência configurável por clínica.
- IA conversacional e WhatsApp Flows.
- Perfis profissional/admin/atendente remarcarem pelo bot.
