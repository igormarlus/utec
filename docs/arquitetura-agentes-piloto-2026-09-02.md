# Piloto da Arquitetura de Agentes — 2026-09-02

Execução da Task 11 do plano `docs/superpowers/plans/2026-09-02-arquitetura-agentes.md`.
Objetivo: rodar o `orquestrador` numa demanda cross-domain e calibrar os arquivos de agente.

## Como foi executado

Os arquivos `.claude/agents/*.md` só ficam dispatcháveis como `subagent_type`
após reiniciar a sessão do Claude Code. Para não bloquear o piloto, um
subagente `general-purpose` recebeu como instrução **ler e seguir na íntegra**
`.claude/agents/orquestrador.md` (+ `CLAUDE.md`, `docs/arquitetura-agentes.md`
§§3/5/11, `routes.php`, os 7 `agente-*.md`) e processar a demanda.

## Demanda usada

> "Quando um tenant fica inadimplente, quero que o paciente receba um aviso
> pelo WhatsApp informando que os agendamentos podem ser afetados, e que a
> agenda do admin mostre um selo de 'conta suspensa' no topo enquanto o tenant
> estiver bloqueado."

## Saída da triagem (resumo)

- **TIPO:** feature nova, ancorada no débito técnico aberto de bloqueio
  automático de tenant por inadimplência (CLAUDE.md §14 / §15.2).
- **DOMÍNIOS:** `agente-produto`, `agente-saas-billing`, `agente-whatsapp`,
  `agente-clinico`, `agente-frontend`, `agente-dev-infra`.
- **Ordem:** produto (spec de negócio) → saas-billing (estado consultável de
  suspensão + cota de disparo) → clínico (função que lista pacientes-alvo) →
  whatsapp (template + disparo) e frontend (selo) em paralelo → dev-infra
  (migração/rota/deploy/healthcheck).
- **Riscos capturados:** atrito com a clínica pagante (avisar o paciente da
  inadimplência), pré-requisito aberto (bloqueio automático + HMAC do webhook
  MP), custo/limite/ban de disparo em massa no WhatsApp, LGPD (mensagem não
  pode expor situação financeira da clínica), dedupe 1x por ciclo,
  `tipo_notificacao` possivelmente ENUM, `top.php` renderiza em todas as telas
  (escopo do selo), reversão ao regularizar.
- **Fora de escopo:** construir do zero o bloqueio/desbloqueio automático e o
  HMAC (é dependência), portal do cliente, aviso por e-mail/SMS, remarcação
  automática em massa, chatbot legado.

**Avaliação:** o roteamento foi correto e o padrão recorrente
"X define a regra, Y aplica" (saas-billing → whatsapp; clínico → frontend;
schema → dev-infra) funcionou. Dois pontos exigiram inferência: quem é dono do
"encanamento" que abastece um include compartilhado (`top.php`), e a fronteira
clínico ⇄ whatsapp na seleção de destinatários.

## Ajustes aplicados

Aplicados a `.claude/agents/orquestrador.md` e espelhados em
`docs/arquitetura-agentes.md` §3:

1. Novo campo no formato de saída: **`PRÉ-REQUISITOS/BLOQUEADORES:`** —
   débitos abertos ou recursos ainda não construídos sobre os quais a demanda
   se apoia, distinto de `RISCOS` e `FORA DE ESCOPO`.
2. `SPECS/PLANOS A CRIAR` passa a aceitar caminhos em **`docs/`** (não só
   `docs/superpowers/`) — templates-pendente e docs de operação ficam na raiz
   de `docs/`.
3. `TIPO` pode ser **combinado** (ex.: "feature nova + infra", "feature nova
   dependente de débito").
4. `PRÓXIMO PASSO` pode **nomear mais de um agente** quando os arranques forem
   paralelizáveis.
5. Nova regra: feature **nova e sensível de negócio** → `agente-produto`
   (spec + brainstorming) **antes** do brainstorming técnico do domínio.
6. Nova nota de fronteira: **encanamento de dados para includes compartilhados**
   (`includes/adm/top.php`, `menu.php`) e helpers de `Padrao_model` consumidos
   por views — o agente dono da regra expõe o helper/flag; o `agente-frontend`
   apenas renderiza.
7. Nova nota de fronteira: **seleção de destinatários por escopo clínico**
   (pacientes de um tenant, agendamentos futuros) é do `agente-clinico`, que
   entrega a função de leitura reutilizável; quem dispara consome.

Nenhum arquivo de agente de domínio precisou de mudança — as fronteiras deles
já estavam claras; os ajustes foram todos no contrato de saída do orquestrador
e em duas notas de fronteira transversais.

## Pendências

- Rodar o piloto de novo com o `orquestrador` dispatchado de verdade
  (`subagent_type: orquestrador`) após reiniciar a sessão, para confirmar que
  o Claude Code carrega o agente e que o `tools:` restrito (read-only) é
  respeitado.
- Task 9 (hooks) permanece não aplicada por decisão do usuário — design em
  `docs/arquitetura-agentes.md` §9.
