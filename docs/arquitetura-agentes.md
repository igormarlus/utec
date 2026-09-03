# Arquitetura de Agentes — UTecnologia Saúde

**Data:** 2026-09-02
**Status:** Proposto e aprovado para planejamento técnico
**Serve como:** documento de referência permanente + spec da implementação em
`docs/superpowers/plans/2026-09-02-arquitetura-agentes.md`.

---

## 1. Objetivo

Organizar a evolução do UTecnologia Saúde em torno de um **orquestrador de
triagem** e **7 subagentes de domínio**, para que cada demanda seja
classificada, quebrada em tarefas, roteada ao dono certo e executada seguindo
sempre o mesmo pipeline (spec → plano → implementação com TDD → review →
verificação → deploy).

O ganho esperado: menos decisão ad hoc, contexto especializado por área,
fronteiras claras de responsabilidade e um caminho previsível do pedido até a
produção.

---

## 2. Princípios

1. **A sessão principal é quem delega.** No Claude Code um subagente não
   dispara outro subagente. O `orquestrador` produz um plano de execução; a
   sessão principal aciona os agentes de domínio na ordem indicada.
2. **Um dono por domínio.** Cada arquivo/tabela/fluxo tem exatamente um agente
   responsável. Demandas que cruzam domínios são sequenciadas pelo
   orquestrador, não resolvidas por um agente invadindo a área do outro.
3. **Menor privilégio.** `orquestrador` e `agente-produto` não editam código.
   Deploy e migração só pelo `agente-dev-infra`.
4. **Compor, não duplicar.** Os agentes usam as skills que já existem
   (superpowers, `seo-geo-agent`, `ftp`) em vez de reimplementar o processo.
5. **Nunca quebrar produção.** CI3 em produção com clientes. Sem migração de
   framework. Migrações idempotentes e aplicadas manualmente. Deploy revisado.
6. **Registrar o não-óbvio.** Decisões de arquitetura que não ficam evidentes
   no código vão para a memória do projeto, com prefixo do domínio.
7. **Português.** Specs, planos, comunicação e comentários em pt-BR.

---

## 3. Modelo de orquestração

### 3.1 Fluxo de uma demanda

```
Você descreve a demanda
        │
        ▼
┌───────────────────────┐
│  orquestrador          │  (subagente, read-only)
│  - classifica          │
│  - decompõe em tarefas │
│  - aponta domínios     │
│  - define ordem/deps   │
│  - lista riscos        │
│  - indica specs a criar│
└───────────┬───────────┘
        │  devolve o plano de execução
        ▼
Sessão principal executa, acionando por domínio:
        │
        ├─► agente-<domínio A>  ──► brainstorming? → writing-plans → executing-plans/TDD → requesting-code-review → verification
        ├─► agente-<domínio B>  ──► (idem, depende de A conforme o plano)
        │
        ▼
agente-dev-infra  ──► php -l, teste local, migração (se houver), deploy FTP, healthcheck
```

### 3.2 O que o orquestrador devolve

Formato fixo de saída (para a sessão principal consumir):

```
DEMANDA: <reformulação em 1 frase>
TIPO: <feature nova | evolução | bug | operação recorrente | decisão de produto | infra — pode combinar>
DOMÍNIOS AFETADOS: [lista de agentes]
TAREFAS:
  1. [agente-x] <o que fazer>  (depende de: -)
  2. [agente-y] <o que fazer>  (depende de: 1)
  ...
PRÉ-REQUISITOS/BLOQUEADORES: [débitos abertos ou recursos ainda não construídos sobre os quais a demanda se apoia — distinto de RISCOS e FORA DE ESCOPO; "nenhum" quando não houver]
SPECS/PLANOS A CRIAR: [caminhos em docs/ — inclui docs/superpowers/ e a raiz de docs/]
RISCOS: [produção, dados, credenciais, limites de plano, webhook, etc.]
FORA DE ESCOPO: [o que deliberadamente não entra]
PRÓXIMO PASSO: <qual(is) agente(s) aciona primeiro e com qual skill — mais de um quando paralelizável>
```

> Ajustes 1–7 vieram do piloto de 2026-09-02 (`docs/arquitetura-agentes-piloto-2026-09-02.md`).
> Fronteiras transversais recorrentes (encanamento de includes compartilhados,
> seleção de destinatários por escopo clínico, schema, cota por plano) estão
> na seção "Fronteiras que se repetem" de `.claude/agents/orquestrador.md`.

### 3.3 Quando pular o orquestrador

Demanda de um único domínio, pequena e sem risco de produção (ex.: ajustar um
label numa view) pode ir direto ao agente de domínio. O orquestrador é
obrigatório quando: toca 2+ domínios, mexe em banco/migração, altera fluxo de
pagamento ou webhook, ou é decisão de priorização.

---

## 4. Orquestrador

| Campo | Valor |
| --- | --- |
| **Arquivo** | `.claude/agents/orquestrador.md` |
| **Missão** | Triagem, decomposição, roteamento e sequenciamento de demandas. Não implementa. |
| **Ferramentas** | `Read`, `Grep`, `Glob`, `WebSearch` apenas. Sem `Edit`, `Write`, `Bash` mutante. |
| **Modelo** | herda o da sessão (sem override) |
| **Entrada** | descrição livre da demanda |
| **Saída** | o bloco estruturado da seção 3.2 |
| **Fontes de verdade** | `CLAUDE.md`, este documento, `docs/superpowers/specs/` e `plans/`, `application/config/routes.php`, roadmap (CLAUDE.md §15), débitos técnicos (§14) |
| **Não faz** | escrever código, criar arquivos, rodar migração/deploy, tomar decisão de negócio sozinho (encaminha ao `agente-produto`) |
| **Memória** | grava padrões de roteamento recorrentes em `memory/` com prefixo `orq_` |

---

## 5. Agentes de domínio

Formato comum de cada arquivo `.claude/agents/<nome>.md`:

```
---
name: <nome>
description: <quando acionar — usado pelo roteamento automático>
tools: <lista ou omitido para todas>
---
<system prompt: missão, mapa de código, tabelas, o que NÃO faz,
skills a usar, convenção de memória>
```

### 5.1 `agente-clinico`

| Campo | Valor |
| --- | --- |
| **Domínio** | Núcleo clínico e acesso hierárquico |
| **Código-alvo** | `application/controllers/adm/Usuarios.php`, `adm/Atendimento.php`, `adm/Especialidades.php`; `application/models/Padrao_model.php` (funções de escopo/árvore), `application/models/adm/Usuarios_model.php`; `application/views/adm/usuarios/new/*`, `application/views/adm/atendimento/*` |
| **Tabelas** | `usuarios`, `usuarios_niveis`, `agendamentos` (inclui prontuário: `atendimento_inicial`, `avaliacao`, `reavaliacao`, `campos_extras`), `exames`, `usuarios_exames`, `usuarios_exames_atendimento`, `usuarios_especialidades`, `especialidades_campos_config` |
| **Responsável por** | CRUD de usuários por nível, árvore de `id_user` e `get_scope_user_ids()`, regras de cadastro por nível (§5.3 CLAUDE.md), agenda operacional, prontuário genérico e por especialidade (§17), checklist de exames, upload de fotos e de arquivos de paciente |
| **Não faz** | cobrança/tenant (→ `agente-saas-billing`), disparo/estado de WhatsApp (→ `agente-whatsapp`), migração de schema (→ `agente-dev-infra`), redesign visual das views (→ `agente-frontend` faz o CSS/markup; este agente define os campos e a lógica) |
| **MCP/ferramentas** | — |
| **Skills** | `superpowers:brainstorming`, `writing-plans`, `test-driven-development`, `systematic-debugging`, `requesting-code-review` |
| **Memória** | prefixo `clin_` |

### 5.2 `agente-saas-billing`

| Campo | Valor |
| --- | --- |
| **Domínio** | SaaS multi-tenant, planos, assinaturas e pagamento recorrente |
| **Código-alvo** | `application/controllers/adm/Saas.php`, `adm/Produtos.php`, `application/controllers/User.php`; `application/models/adm/Saas_model.php`; `application/libraries/Mercadopago_saas.php`; `application/config/mercadopago.php` (só leitura de credenciais); `application/views/adm/saas/*` |
| **Tabelas** | `saas_tenants`, `saas_subscriptions`, `saas_subscription_cycles`, `saas_billing_events`, `produtos` (campos `plan_code`, `billing_interval`, `billing_interval_count`, `trial_days`, `setup_fee`, `max_profissionais/colaboradores/pacientes`), `pedidos`, `carrinho`, `carrinho_hist` |
| **Responsável por** | provisionamento de tenant (`provision_tenant`), geração de checkout Preapproval, webhook Mercado Pago (`webhooks/mercadopago`), validação HMAC do webhook (débito §14), baixa de ciclo pago em `saas_subscription_cycles`, bloqueio/desbloqueio automático por inadimplência (`tenant_allows_access()`), controle de limites de plano, telas de tenant bloqueado |
| **Não faz** | criar/editar usuários da clínica (→ `agente-clinico`), lógica de WhatsApp de cobrança (→ `agente-whatsapp` executa; este agente define a regra de negócio), deploy (→ `agente-dev-infra`) |
| **MCP/ferramentas** | Browser MCP (`playwright`/`chrome-devtools`) para validar o fluzo de checkout no sandbox do Mercado Pago e inspecionar retornos |
| **Skills** | `superpowers:brainstorming`, `writing-plans`, `test-driven-development`, `systematic-debugging`, `requesting-code-review` |
| **Memória** | prefixo `saas_` |

### 5.3 `agente-whatsapp`

| Campo | Valor |
| --- | --- |
| **Domínio** | WhatsApp: Cloud API própria (confirmação/lembrete/webhook) e chatbot legado |
| **Código-alvo** | `application/controllers/Webhooks.php`, `application/controllers/adm/Whatsapp.php`, `adm/Notificacoes.php`, `application/controllers/Cron.php`; `application/libraries/Whatsapp_agendamento.php`; `application/helpers/whatsapp_agendamento_helper.php`; `application/models/Whatsapp_model.php`, `Notificacoes_model.php`; `application/config/whatsapp.php`; `webhooks/whatsapp/` (bridge); `tests/whatsapp_*` |
| **Tabelas** | `whatsapp_config`, `whatsapp_notificacoes` (`tipo_notificacao`, `status_envio`, `status_confirmacao`, `wamid`), `notificacoes_usuarios`; chatbot legado em `chwtppbr_db` via `db2`/`dbbot`, `pi_whats_users` |
| **Responsável por** | template aprovado + botões quick-reply, disparo na criação de agendamento, limite trial/free (`utec_whatsapp_politica_limite`), webhook GET (verify_token) e POST (HMAC `X-Hub-Signature-256`), transições idempotentes de resposta (`registrar_resposta_webhook`), avisos internos + sino, etiquetas na agenda/prontuário, cron de lembrete (`Cron::lembrete_whatsapp`, `GET /cron/lembrete-whatsapp`), flag `whatsapp.lembrete_profissional_ativo`, aprovação e troca dos templates dedicados (`docs/whatsapp-lembrete-templates-pendente.md`), chatbot por perfis (`docs/superpowers/specs/2026-09-01-chatbot-whatsapp-perfis-design.md`) |
| **Não faz** | criar agendamento ou prontuário (→ `agente-clinico`), decidir regra de cota por plano (→ `agente-saas-billing` define; este aplica), migração de schema (→ `agente-dev-infra`) |
| **MCP/ferramentas** | `curl` para Meta Graph API; Browser MCP para o Meta Business Manager (status/edição de templates); `read_console_messages`/`read_network_requests` para depurar webhook |
| **Skills** | `superpowers:brainstorming`, `writing-plans`, `test-driven-development` (os `tests/whatsapp_*` são PHP puro, sem framework), `systematic-debugging`, `requesting-code-review` |
| **Memória** | prefixo `wa_` |

### 5.4 `agente-seo-geo`

| Campo | Valor |
| --- | --- |
| **Domínio** | SEO on-page, GEO, conteúdo, link building e monitoramento de tráfego de IA |
| **Código-alvo** | métodos `seo_*` em `application/controllers/Home.php` e `Blog.php`; `application/views/public/seo/*`; `application/config/routes.php` (bloco `seo_*`); `application/config/ai_sources.php`; `application/controllers/adm/Marketing.php`; `application/views/adm/marketing/trafego_ia.php`; `application/models/FbApi_model.php`; `sitemap.xml`, `sitemap-blog.xml`; `Padrao_model::track_ai_referral()` / `detect_ai_source()` / `mark_ai_conversion()` |
| **Tabelas** | `blog_posts`, `blog_categorias`, `ai_referrals`, `ai_conversions`, `api_conv_fb`, `acessos` |
| **Docs/ledger** | `docs/seo-*`, `docs/seo-geo-agente-ledger.md`, `docs/monitoramento_geo_ia.md`, relatórios `docs/seo-geo-agente-relatorio-*.md` |
| **Responsável por** | ciclo semanal via skill `seo-geo-agent` (keyword research por Google Autocomplete, decisão criar/recomendar/descartar, landing + artigo dentro do limite 5+5, ledger, relatório, email), link building (Capterra, G2, guest posts, comparativos — `project_seo_offpage`), dashboard de Tráfego de IA, Facebook Conversions API, LGPD do monitoramento (`ip_hash`, retenção via `adm/dev/purgar_monitoramento_ia`) |
| **Não faz** | `git`/FTP/deploy e `INSERT/UPDATE/DELETE` no banco (regras duras da skill `seo-geo-agent`) — entrega SQL/arquivos para o `agente-dev-infra` publicar; redesign estrutural das landings (→ `agente-frontend`) |
| **MCP/ferramentas** | `chrome-devtools`/`playwright` (SERP, perfis de diretório, Lighthouse/LCP das landings), `WebSearch`/`WebFetch` (concorrentes, oportunidades de backlink), `curl` (Autocomplete API) |
| **Skills** | `seo-geo-agent` (dono), `superpowers:brainstorming` e `writing-plans` para mudanças estruturais, `requesting-code-review` |
| **Memória** | prefixo `seo_` — complementa `project_seo_offpage` e `project_seo_onpage_conteudo` |

### 5.5 `agente-frontend`

| Campo | Valor |
| --- | --- |
| **Domínio** | UI: views admin em modernização, landing pages, CSS, template, UX/acessibilidade |
| **Código-alvo** | `application/views/adm/**` (com foco nas pastas `new/`), `application/views/index-front.php`, `application/views/public/*`, `css/clicklinica-main.css`, `js/*`, `bower_components/*` (uso, não edição), `includes/adm/*` (menu, top) |
| **Responsável por** | markup/estilo/interação das telas, redesign gradual do Adminto (Bootstrap 4 + jQuery), consistência visual (Lato no admin, Inter na landing), responsividade, acessibilidade, mock de prontuário nas landings de especialidade, componentes reaproveitáveis |
| **Não faz** | lógica de controller/model (→ agente de domínio correspondente), rotas (→ `agente-dev-infra`), decisão de quais campos existem no prontuário (→ `agente-clinico`), conteúdo/estratégia das landings SEO (→ `agente-seo-geo`) |
| **MCP/ferramentas** | `playwright` + `chrome-devtools` para renderização real, erros de console, requests falhos, Lighthouse, LCP e auditoria de a11y; skill `browser-automation` para QA |
| **Skills** | `frontend-design:frontend-design`, `superpowers:brainstorming`, `writing-plans`, `browser-automation`, `requesting-code-review` |
| **Memória** | prefixo `ui_` |

### 5.6 `agente-dev-infra`

| Campo | Valor |
| --- | --- |
| **Domínio** | Migrações, roteamento, configuração, deploy, cron e ambiente. **Guardião do "não quebrar produção".** |
| **Código-alvo** | `application/controllers/adm/Dev.php`, `application/config/*` (`routes.php`, `database.php`, `config.php`, etc.), `index.php`, `.htaccess`, `webhooks/*/index.php` (bridges), `.vscode/ftp-sync.json` (só leitura) |
| **Tabelas** | qualquer uma — mas só via método idempotente em `Dev.php`, nunca SQL solto |
| **Responsável por** | novas migrações (método em `Dev.php` protegido por `nivel == 1`, idempotente, com `?desfazer=1` quando fizer sentido), rotas, deploy via skill `ftp` (respeitando o cap de ~8 conexões), agendamento de cron no cPanel, tokens/vars de ambiente, `php -l` e teste local antes de publicar, healthcheck pós-deploy dos webhooks, atualização do `CLAUDE.md` (seções de status/deploy) |
| **Não faz** | lógica de negócio de qualquer domínio — só recebe os arquivos prontos e publica; nunca edita `system/` (core CI3) |
| **MCP/ferramentas** | `curl` para healthcheck de endpoints; `php -l`; skill `ftp`; skill `schedule` para rotinas de cloud agent |
| **Skills** | `ftp` (dono), `superpowers:executing-plans`, `verification-before-completion`, `finishing-a-development-branch`, `schedule` |
| **Memória** | prefixo `infra_` — registra tokens/rotas/cron publicados e status de deploy |

### 5.7 `agente-produto`

| Campo | Valor |
| --- | --- |
| **Domínio** | Estratégia de produto e negócio. **Não edita código.** |
| **Escrita permitida** | apenas em `docs/` (specs de negócio, análises, roadmap) |
| **Responsável por** | priorização do roadmap (CLAUDE.md §15), curadoria dos débitos técnicos (§14), definição de planos/pricing e limites (§2.3), ICP e posicionamento (§2.2), análise de concorrentes, redação de specs de negócio antes do `superpowers:brainstorming` técnico, critérios de aceite de valor |
| **Não faz** | qualquer `Edit` em `application/`, `css/`, `js/`; decisões de implementação técnica (→ orquestrador + agentes de domínio) |
| **MCP/ferramentas** | `WebSearch`/`WebFetch` (mercado, concorrentes, benchmarks de pricing) |
| **Skills** | `superpowers:brainstorming` (para specs de negócio), `writing-plans` só quando o entregável é um documento |
| **Memória** | prefixo `prod_` — complementa `project_utec` |

---

## 6. Domínio dormant — RPG

O módulo educacional RPG (`rpg_*`, `application/controllers/rpg/`,
`application/models/rpg/`) está isolado e sem atividade no roadmap. **Não tem
agente dedicado.** Se voltar ao roadmap, criar `.claude/agents/agente-rpg.md`
no mesmo padrão da seção 5, com código-alvo `rpg/` e tabelas `rpg_personagens`,
`rpg_personagens_atributos`, `rpg_items`, `rpg_user_inventory`,
`rpg_locations`, `rpg_dialogos`, `rpg_progress`. Até lá, demandas pontuais de
RPG são tratadas pela sessão principal sem subagente.

---

## 7. Composição com as skills existentes

| Skill | Quem usa | Papel |
| --- | --- | --- |
| `superpowers:using-superpowers` | todos | ponto de entrada, verificação de skills |
| `superpowers:brainstorming` | domínio + produto | antes de qualquer feature nova |
| `superpowers:writing-plans` | domínio | transforma spec em plano de tarefas |
| `superpowers:executing-plans` / `subagent-driven-development` | domínio + dev-infra | execução do plano |
| `superpowers:test-driven-development` | clínico, saas-billing, whatsapp | ciclo teste→código |
| `superpowers:systematic-debugging` | domínio | qualquer bug antes de propor fix |
| `superpowers:requesting-code-review` | domínio | ao fechar tarefa/feature (dispara subagente revisor — cobre o eixo "função" sem precisar de agente dedicado) |
| `superpowers:verification-before-completion` | dev-infra | antes de afirmar "pronto"/deploy |
| `superpowers:finishing-a-development-branch` | dev-infra | merge/PR/cleanup |
| `superpowers:using-git-worktrees` | dev-infra | isolamento de feature grande |
| `seo-geo-agent` | seo-geo | ciclo semanal autônomo |
| `ftp` | dev-infra | deploy de arquivos para produção |
| `frontend-design:frontend-design` | frontend | direção visual |
| `browser-automation` | frontend, seo-geo | QA de páginas |
| `schedule` | dev-infra | cloud agents / rotinas |

> Revisão de código é transversal mas **não vira agente**: `requesting-code-review`
> já roda num subagente próprio. Mesma lógica para "QA" (`browser-automation`).

---

## 8. Ferramentas de vanguarda por agente

| Recurso | Onde entra |
| --- | --- |
| **MCP `playwright` / `chrome-devtools`** | `agente-frontend` (render, console, LCP, a11y, Lighthouse), `agente-seo-geo` (SERP, diretórios), `agente-saas-billing` (checkout MP sandbox), `agente-whatsapp` (debug de webhook) |
| **MCP `claude-in-chrome`** | alternativa quando a tarefa precisa da sessão real do Chrome do Igor (login em painéis: Meta, Mercado Pago, Capterra, G2) |
| **`WebSearch` / `WebFetch`** | `orquestrador`, `agente-produto`, `agente-seo-geo` |
| **Cloud agents (`schedule`)** | fase 2 — ver seção 10 |
| **Memória de projeto** | todos — decisões não-óbvias, com prefixo de domínio |
| **Hooks** | ver seção 9 |

---

## 9. Hooks propostos (settings.local.json)

Executados pelo harness, não pelo modelo. A implementação detalha o JSON.

1. **PostToolUse em `Edit`/`Write` de `*.php`** → roda `php -l` no arquivo
   alterado e devolve o resultado como feedback. Pega erro de sintaxe antes do
   deploy.
2. **PreToolUse em `Edit`/`Write` sob `system/`** → bloqueia com mensagem
   "core CI3 é imutável (CLAUDE.md §12)".
3. **PreToolUse em `Bash` com `git push` ou `node .claude/skills/ftp/upload.js`**
   → exige confirmação e lembra do cap de ~8 conexões FTP.
4. *(opcional)* **PostToolUse** que roda os `tests/whatsapp_*` quando um
   arquivo de `application/**/whatsapp*` ou `application/helpers/whatsapp_*`
   muda.

---

## 10. Automação agendada (fase 2)

| Rotina | Skill/mecanismo | Frequência | Dono |
| --- | --- | --- | --- |
| Ciclo SEO/GEO | `seo-geo-agent` headless | semanal | `agente-seo-geo` |
| Healthcheck webhooks (MP + WhatsApp respondem 200/403 esperado) | `schedule` (cloud agent) + `curl` | diária | `agente-dev-infra` |
| Verificação do cron de lembrete (rodou na última hora? erros em `whatsapp_notificacoes`?) | `schedule` | diária | `agente-whatsapp` |
| Varredura de roadmap/débitos (o orquestrador sugere próximos itens) | `schedule` | quinzenal | `agente-produto` |

Fase 1 entrega os arquivos de agente + hooks. Fase 2 liga as rotinas acima
uma a uma, só depois que o uso manual estiver estável.

---

## 11. Matriz de permissões (menor privilégio)

| Agente | Read | Grep/Glob | Edit/Write código | Write `docs/` | Bash mutante | Migração | Deploy/FTP | Browser MCP |
| --- | :-: | :-: | :-: | :-: | :-: | :-: | :-: | :-: |
| `orquestrador` | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `agente-produto` | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `agente-clinico` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ (pede ao dev-infra) | ❌ | ❌ |
| `agente-saas-billing` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ (pede ao dev-infra) | ❌ | ✅ |
| `agente-whatsapp` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ (pede ao dev-infra) | ❌ | ✅ |
| `agente-seo-geo` | ✅ | ✅ | ✅ | ✅ | ⚠️ sem `git`/FTP/SQL | ❌ | ❌ | ✅ |
| `agente-frontend` | ✅ | ✅ | ✅ (views/css/js) | ✅ | ✅ | ❌ | ❌ | ✅ |
| `agente-dev-infra` | ✅ | ✅ | ✅ (config/rotas/Dev.php) | ✅ | ✅ | ✅ | ✅ | ❌ |

`tools:` no frontmatter reforça isso onde o Claude Code permite; o resto é
regra explícita no system prompt de cada agente.

---

## 12. Convenção de memória

- Arquivo por fato, em `~/.claude/projects/C--htdocs-utec/memory/`.
- `name:` começa com o prefixo do domínio: `clin_`, `saas_`, `wa_`, `seo_`,
  `ui_`, `infra_`, `prod_`, `orq_`.
- `metadata.type`: `project` (trabalho em andamento), `feedback` (como
  trabalhar), `reference` (link externo), `user`.
- Ponteiro de uma linha em `MEMORY.md`.
- Não duplicar o que já está no código, git ou `CLAUDE.md`.

---

## 13. Roadmap de adoção

**Fase 1 — Estrutura (este plano)**
1. Criar `.claude/agents/orquestrador.md` + 7 agentes de domínio.
2. Adicionar os hooks 1–3 da seção 9.
3. Adicionar a seção "18. Arquitetura de Agentes" no `CLAUDE.md` apontando
   para este documento.
4. Rodar uma demanda-piloto ponta a ponta pelo orquestrador e ajustar.

**Fase 2 — Automação**
5. Ligar o healthcheck de webhooks.
6. Ligar a verificação do cron de lembrete.
7. Ligar a varredura quinzenal de roadmap.
8. Hook opcional 4 (testes de WhatsApp no PostToolUse).

**Manutenção**
- Cada agente novo/alterado atualiza este documento na mesma PR.
- Revisar fronteiras a cada trimestre ou quando um agente "vazar" para o
  domínio de outro com frequência.

---

## 14. Referências

- `CLAUDE.md` — visão de produto, arquitetura, convenções, roadmap, débitos.
- `docs/superpowers/specs/` e `docs/superpowers/plans/` — histórico de features.
- `docs/seo-geo-agente-ledger.md` — estado do ciclo de conteúdo.
- `.claude/skills/` — skills próprias (`seo-geo-agent`, `ftp`).
- Skills superpowers — pipeline de desenvolvimento.
