# Guia Prático dos Agentes — UTecnologia Saúde

**Data:** 2026-09-03
**Complementa:** `docs/arquitetura-agentes.md` (arquitetura completa) e a seção 18 do `CLAUDE.md`.
**Arquivos dos agentes:** `.claude/agents/*.md`

Este guia descreve cada agente em linguagem direta e lista **tarefas práticas**
que ele pode assumir hoje — a maioria puxada do roadmap (`CLAUDE.md` §15) e dos
débitos técnicos (§14).

> Os agentes só ficam dispatcháveis (`subagent_type: <nome>`) depois de
> reiniciar a sessão do Claude Code, que carrega `.claude/agents/` no start.

---

## Fluxo de uso

```
Você descreve a demanda
   → orquestrador classifica, quebra em tarefas, aponta domínios + ordem
   → sessão principal aciona cada agente de domínio na ordem
   → cada agente roda o pipeline superpowers
     (brainstorming → writing-plans → TDD → code-review → verification)
   → agente-dev-infra fecha com migração + deploy
```

Demanda pequena, de um único domínio e sem risco de produção pode ir direto
ao agente de domínio, sem passar pelo orquestrador.

---

## `orquestrador` — triagem e roteamento

**O que é.** O ponto de entrada. Recebe a demanda que você descreve, classifica
(feature nova / evolução / bug / operação recorrente / decisão de produto /
infra — pode combinar), quebra em tarefas numeradas com dependências, aponta
quais agentes entram e em que ordem, e lista pré-requisitos/bloqueadores,
riscos e o que fica fora de escopo. Devolve um plano de execução num formato
fixo. **Não escreve código.**

**Ferramentas.** Só leitura: `Read`, `Grep`, `Glob`, `WebSearch`.

**Aciona quando.** A demanda toca 2+ domínios, mexe em banco/migração, altera
pagamento ou webhook, ou é decisão de priorização.

**Tarefas práticas.**

- Triar um item novo do roadmap (`CLAUDE.md` §15) antes de virar spec.
- Quebrar "bloqueio automático de tenant por inadimplência" (§15.2) em tarefas
  cross-domain com ordem e dependências.
- Decidir a ordem quando uma demanda toca clínico + frontend + dev-infra ao
  mesmo tempo.
- Revisar um plano existente em `docs/superpowers/plans/` e apontar
  dependências não mapeadas.
- Classificar um relato de bug e apontar o agente dono + a skill
  (`systematic-debugging`).
- Dizer quando uma demanda é pequena o bastante para pular o próprio
  orquestrador.

---

## `agente-clinico` — núcleo clínico e acesso hierárquico

**O que é.** Dono de usuários, níveis (1–5), árvore de escopo por `id_user` e
`get_scope_user_ids()`, regras de cadastro por nível, agenda operacional,
prontuário (os 3 campos genéricos + `campos_extras` por especialidade), exames
e `usuarios_especialidades`.

**Código.** Controllers `adm/Usuarios`, `adm/Atendimento`, `adm/Especialidades`;
`Padrao_model` (funções de escopo/árvore); `adm/Usuarios_model`; views
`adm/usuarios/new/*` e `adm/atendimento/*`.

**Não faz.** Cobrança/tenant, WhatsApp, migração de schema (entrega o método
idempotente pronto pro `agente-dev-infra`), redesign visual das views.

**Tarefas práticas.**

- Prontuário adaptado por especialidade — labels/campos diferentes por tipo de
  profissional (`CLAUDE.md` §17, backlog §15.3).
- Relatórios PDF de prontuário (§15.3).
- Controle de limites do plano em tempo real ao cadastrar usuário
  (`max_profissionais`, `max_colaboradores`, `max_pacientes`) — a leitura da
  regra vem do `agente-saas-billing`.
- Corrigir bug de escopo (um usuário enxergando registro que não deveria) —
  via `systematic-debugging`.
- Adicionar campo ou validação nova na agenda / no formulário de atendimento.
- Entregar função de leitura reutilizável que lista pacientes de um tenant com
  agendamentos futuros (consumida por WhatsApp / relatórios).
- Melhorar o checklist operacional de exames.

---

## `agente-saas-billing` — SaaS multi-tenant e pagamento recorrente

**O que é.** Dono de `saas_tenants`, `saas_subscriptions`, ciclos,
`saas_billing_events`, `produtos`/planos (plan_code, limites, trial,
setup_fee), `pedidos`/`carrinho`, integração Mercado Pago (Preapproval +
webhook), validação HMAC, bloqueio por inadimplência (`tenant_allows_access()`)
e controle de limites de plano.

**Código.** Controllers `adm/Saas`, `adm/Produtos`, `User`; `adm/Saas_model`;
library `Mercadopago_saas`; `config/mercadopago.php` (só leitura); views
`adm/saas/*`.

**Ferramentas extras.** Browser MCP para validar o checkout no sandbox do
Mercado Pago e inspecionar respostas de webhook.

**Não faz.** Usuários da clínica; o disparo de WhatsApp de cobrança (define a
regra e a cota por plano, quem envia é o `agente-whatsapp`); migração; deploy.

**Tarefas práticas.**

- Validar o webhook Mercado Pago com assinatura HMAC em produção (§15.2, débito
  §14).
- Baixar o evento de cobrança para o ciclo local em `saas_subscription_cycles`
  (§15.2).
- Bloqueio / desbloqueio automático de tenant por inadimplência (§15.2) —
  expondo um estado consultável (`ativo | em atraso | suspenso`) para
  controllers e views.
- Tela de configuração comercial: credenciais MP + parâmetros SaaS por tenant
  (§15.3).
- Dashboard de métricas para o admin: MRR, churn, tenants ativos (§15.3).
- Onboarding self-service — cadastro de clínica sem intervenção do admin
  (§15.3).
- Portal do cliente — o tenant acompanha assinatura e faturas (§15.3).

---

## `agente-whatsapp` — Cloud API própria + chatbot legado

**O que é.** Dono do template de confirmação com botões quick-reply, do disparo
ao criar agendamento, do limite trial/free (3 por tenant), do webhook GET/POST
com HMAC, das transições idempotentes de resposta
(`registrar_resposta_webhook`), dos avisos internos + sino, das etiquetas na
agenda, do cron de lembrete (`GET /cron/lembrete-whatsapp`), das flags, dos
templates dedicados pendentes na Meta e do chatbot por perfis. Chatbot legado
em `chwtppbr_db` (`db2`/`dbbot`).

**Código.** Controllers `Webhooks`, `adm/Whatsapp`, `adm/Notificacoes`, `Cron`;
library `Whatsapp_agendamento`; helper `whatsapp_agendamento_helper`;
`Whatsapp_model`, `Notificacoes_model`; `config/whatsapp.php`; bridge
`webhooks/whatsapp/index.php`; testes `tests/whatsapp_*` (PHP puro).

**Ferramentas extras.** `curl` para a Meta Graph API; browser MCP para o Meta
Business Manager; leitura de console/network para depurar o webhook.

**Regra dura.** Falha externa do WhatsApp nunca bloqueia o salvamento do
agendamento; webhook é entrada não confiável — validar HMAC antes de agir.

**Não faz.** Criar agendamento/prontuário; decidir a cota por plano (o
`agente-saas-billing` define, este aplica); migração.

**Tarefas práticas.**

- Aprovar na Meta os 2 templates dedicados de lembrete
  (`docs/whatsapp-lembrete-templates-pendente.md`, pendência §10.3.1) e trocar
  o reuso do template de confirmação.
- Mover o token do cron para a env `WHATSAPP_CRON_TOKEN` no cPanel (hoje usa o
  fallback do arquivo) — em conjunto com o `agente-dev-infra`.
- Ligar a lane do profissional (flag `whatsapp.lembrete_profissional_ativo`)
  quando o template sem botões for aprovado.
- Implementar as janelas D-1 / manhã do lembrete
  (`docs/whatsapp-lembrete-templates-pendente.md`).
- Adicionar um novo `tipo_notificacao` (ex.: `aviso_inadimplencia`) com dedupe
  por ciclo.
- Depurar entrega de mensagem falhando — status `erro` em
  `whatsapp_notificacoes`.
- Concluir / evoluir o chatbot por perfis
  (`docs/superpowers/specs/2026-09-01-chatbot-whatsapp-perfis-design.md`).

---

## `agente-seo-geo` — conteúdo, link building e tráfego de IA

**O que é.** Dono das landings `seo_*` (`Home.php`/`Blog.php` +
`views/public/seo/`), de `blog_posts`, do keyword research por Google
Autocomplete, do ledger, dos sitemaps, do link building (Capterra, G2, guest
posts, comparativos), do dashboard de Tráfego de IA
(`ai_referrals`/`ai_conversions`, `adm/Marketing`), da Facebook Conversions API
e da LGPD do monitoramento. Dono da skill `seo-geo-agent` (ciclo semanal,
limite de 5 landings + 5 artigos por execução).

**Ferramentas extras.** `chrome-devtools`/`playwright` (SERP, diretórios,
Lighthouse/LCP), `WebSearch`/`WebFetch` (concorrentes, backlinks), `curl` (API
do Autocomplete).

**Não faz.** `git`/FTP/deploy nem `INSERT/UPDATE/DELETE` — gera o SQL e os
arquivos e entrega ao `agente-dev-infra`; redesign estrutural das landings
(`agente-frontend`); página especulativa sem demanda confirmada.

**Tarefas práticas.**

- Rodar o ciclo semanal (`/seo-geo-agent`): keyword research, decisão
  criar/recomendar/descartar, ledger, relatório, e-mail.
- Criar landing para uma especialidade sem página (comparar o seed de 42
  especialidades em `Dev.php` contra as rotas `seo_*`).
- Perfis em Capterra e G2 e prospecção de guest posts
  (`project_seo_offpage`).
- Artigo comparativo com um concorrente do mercado brasileiro de gestão
  clínica.
- Auditar Lighthouse / LCP das landings publicadas e listar correções para o
  `agente-frontend`.
- GEO / Brand Radar — Parte 2 de `docs/monitoramento_geo_ia.md` (fase futura).
- Purga de dados de monitoramento de IA com mais de 18 meses
  (`adm/dev/purgar_monitoramento_ia`).

---

## `agente-frontend` — UI, views e landing pages

**O que é.** Dono do markup, estilo e interação das views admin (foco nas
pastas `new/`), das landing pages (`index-front.php`, `public/`), do
`css/clicklinica-main.css`, do redesign gradual do template Adminto
(Bootstrap 4 + jQuery), da responsividade, da acessibilidade e da remoção de
textos em inglês remanescentes.

**Ferramentas extras.** `playwright` + `chrome-devtools` (render real, console,
requests falhos, Lighthouse, LCP, auditoria de a11y); skill
`browser-automation` para QA; skill `frontend-design` para direção visual.

**Não faz.** Lógica de controller/model; rotas; decidir quais campos existem no
prontuário (`agente-clinico`); conteúdo/estratégia das landings SEO
(`agente-seo-geo`). Só renderiza a flag que o agente dono da regra expõe.

**Tarefas práticas.**

- Continuar o redesign das views admin em `adm/*/new/` (paleta, tipografia,
  componentes) seguindo `docs/superpowers/specs/2026-06-04-redesign-admin-views-design.md`.
- Remover textos em inglês nas views ("Start typing to search...", etc. — §14).
- Auditar acessibilidade das telas principais (semântica, ARIA, foco,
  contraste, tap targets) e corrigir.
- Aplicar as melhorias da landing page
  (`docs/superpowers/specs/2026-06-04-landing-page-melhorias-design.md`).
- Componentizar elementos repetidos (pills de status, cards, chips de métrica,
  bottom sheet).
- QA de render de uma tela depois que outro agente mexeu no controller/model
  que a alimenta.
- Ajustar a responsividade da agenda no mobile (operável com um polegar).
- Renderizar um selo/badge novo no `includes/adm/top.php` a partir de uma flag
  já exposta pelo controller.

---

## `agente-dev-infra` — migrações, deploy e infra

**O que é.** Dono das migrações idempotentes em `adm/Dev.php` (protegidas por
`nivel == 1`, com `?desfazer=1`), das rotas, de `application/config/*`, do
deploy via skill `ftp` (respeitando o cap de ~8 conexões), do cron no cPanel,
dos tokens/variáveis de ambiente, do `php -l` + teste local antes de publicar e
do healthcheck pós-deploy. **Guardião do "não quebrar produção" — o único
agente que publica.**

**Ferramentas extras.** `curl` para healthcheck; `php -l`; skill `ftp` (dono);
skill `schedule` para rotinas / cloud agents.

**Não faz.** Lógica de negócio de nenhum domínio (recebe os arquivos prontos e
publica); nunca edita `system/`; nunca aplica SQL solto.

**Tarefas práticas.**

- Desativar `save_queries = TRUE` em produção (`config/database.php` — §4.1,
  débito §14).
- Criar a migração idempotente para uma coluna/índice novo pedido por outro
  agente (método em `Dev.php` + linha na tabela do `CLAUDE.md` §13).
- Publicar via FTP os arquivos já revisados/testados e registrar o status de
  deploy no `CLAUDE.md`.
- Agendar / ajustar tarefas de cron no cPanel (ex.: lembrete de hora em hora).
- Healthcheck dos webhooks Mercado Pago e WhatsApp (`curl` — sem token deve dar
  403).
- Mover credenciais hardcoded para variáveis de ambiente
  (`MERCADOPAGO_*`, `WHATSAPP_CRON_TOKEN`).
- Rodar migrações `adm/dev/migrar_*` pendentes e anotar o resultado.
- Limpeza gradual de comentários `#` e código comentado (§14, baixa
  severidade).

---

## `agente-produto` — estratégia e negócio

**O que é.** Dono da priorização do roadmap (`CLAUDE.md` §15), da curadoria dos
débitos técnicos (§14), da definição de planos/pricing e limites (§2.3), do ICP
e posicionamento (§2.2), da análise de concorrentes, da redação de specs de
negócio antes do brainstorming técnico e dos critérios de aceite de valor.

**Ferramentas.** `Read`, `Grep`, `Glob`, `Write`, `Edit`, `WebSearch`,
`WebFetch` — **sem `Bash`**. Escreve **apenas em `docs/`**.

**Não faz.** Qualquer `Edit`/`Write` em `application/`, `css/`, `js/`,
`.claude/`; decisões de implementação técnica; migração ou deploy.

**Handoff.** Ao fechar uma decisão, devolve ao `orquestrador` o que muda no
roadmap e quais domínios são afetados.

**Tarefas práticas.**

- Repriorizar o roadmap §15 com base nos clientes interessados na aquisição.
- Escrever a spec de negócio do onboarding self-service (o "o quê" e o "porquê"
  antes do brainstorming técnico).
- Fechar a tabela de planos para o checkout: valores, `plan_code`, ciclo,
  `trial_days`, `setup_fee` e os três limites por plano.
- Análise competitiva estruturada: Feegow, iClinic, Ninsaúde, Clínica nas
  Nuvens, Amplimed — funcionalidade, pricing, posicionamento.
- Definir os critérios de aceite de valor do portal do cliente.
- Traduzir os débitos técnicos §14 em impacto de negócio e reordená-los por
  risco/valor.
- Decidir se estética, veterinária e laboratório entram no escopo de SEO
  (hoje fora, só com decisão explícita).
- Escrever a spec de negócio do dashboard de métricas do admin (MRR, churn).

---

## Fronteiras que se repetem

Fixadas na seção "Fronteiras que se repetem" de `.claude/agents/orquestrador.md`:

| Situação | Quem faz |
|---|---|
| `ALTER` / nova coluna / novo índice | `agente-dev-infra` (método idempotente em `Dev.php`); o agente de domínio entrega o método pronto |
| Cota / limite por plano | `agente-saas-billing` define a regra; quem dispara (`agente-whatsapp`, `agente-clinico`) aplica |
| Encanamento de dados para includes compartilhados (`top.php`, `menu.php`) e helpers de `Padrao_model` usados em views | o agente dono da regra expõe o helper/flag; o `agente-frontend` só renderiza |
| Seleção de destinatários por escopo clínico (pacientes de um tenant, agendamentos futuros) | `agente-clinico` entrega a função de leitura; quem dispara consome |
| Publicar em produção (FTP, cron, migração) | sempre `agente-dev-infra` |
| Decisão de priorizar / precificar / cortar escopo | `agente-produto` antes do brainstorming técnico |
