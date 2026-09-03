# Roadmap Consolidado — Vendas Orgânicas UTecnologia Saúde

- **Data:** 2026-09-03
- **Autor:** consolidação (sessão principal, a partir dos 5 documentos do `agente-produto`)
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** roadmap de execução — unifica a spec-mãe e os 4 entregáveis derivados numa única linha do tempo, com caminho crítico, pacotes de trabalho por agente de domínio, KPIs e riscos deduplicados.
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.

## Documentos de origem

| # | Documento | Papel |
|---|-----------|-------|
| 0 | `docs/produto/2026-09-03-estrategia-vendas-organicas.md` | Spec-mãe — canais, foco, janelas, mensagens por ICP, funil, capacidade, KPIs |
| 1 | `docs/produto/2026-09-03-playbook-parcerias-comunidades.md` | Playbook operacional — parcerias (motor) + comunidades (apoio) |
| 2 | `docs/produto/2026-09-03-playbook-cold-outreach-linkedin.md` | Playbook operacional — piloto de outbound + LinkedIn + eventos |
| 3 | `docs/superpowers/specs/2026-09-03-programa-indicacao-design.md` | Spec de negócio — referral member-get-member |
| 4 | `docs/superpowers/specs/2026-09-03-prova-social-cases-design.md` | Spec de negócio — prova social, cases, reviews |

Este roadmap **não substitui** os cinco — cada um segue como referência detalhada do seu tema. Aqui fica a visão única de sequência e de quem faz o quê.

---

## 1. Quadro unificador

```
                         ┌─────────────────────────────────────────────┐
   BASE (já roda)  ─────► │  SEO / GEO / conteúdo / diretórios           │  agente-seo-geo
   só repriorizar         │  (docs de SEO já existentes — não reabrir)   │
                         └─────────────────────────────────────────────┘
                                            │  leads mornos, fluxo estável
   MOTOR (começa já) ─────►  ┌───────────────┴───────────────┐
                            │  Indicação (member-get-member) │  agente-produto → agente-saas-billing
                            │  Parcerias com multiplicadores │  agente-produto (opera)
                            └───────────────┬───────────────┘  leads quentes, baixo volume, alta conversão
                                            │
   CAMADA DE CONVERSÃO ───►  ┌──────────────┴────────────────┐
   (sobre o que 1–3 geram)  │  Prova social / cases         │  agente-produto → frontend + seo-geo + clinico
                            │  WhatsApp opt-in (nurture)     │  (sem número transacional)
                            └──────────────┬────────────────┘
                                            │
   PILOTO CONTROLADO ─────►  ┌──────────────┴────────────────┐
   (fora do foco; não escalar) │ Cold outreach (50–100)     │  agente-produto (piloto)
                              │ LinkedIn orgânico (lento)    │
                              │ 1 webinar com parceiro       │
                              └───────────────────────────────┘

   GARGALO TRANSVERSAL: provisionamento manual de tenant, 1 operador.
   Teto ~20–30 trials/mês. Todo canal volumoso/spiky fica contido até haver onboarding self-service.
```

**Regra de ouro de priorização de fila:** indicação e parceria (leads quentes) **nunca** cedem lugar de fila nem de tempo de operador para o outbound. LinkedIn roda "por baixo", contínuo e leve. No máximo **um** canal gerador de pico ativo por vez (lote de e-mail **ou** webinar).

---

## 2. Caminho crítico — o que destrava tudo

Cinco pré-requisitos. Os dois primeiros bloqueiam a **medição** e a **prova social**; sem eles o resto do roadmap roda no escuro.

| Ordem | Pré-requisito | Por que é caminho crítico | Domínio(s) | Tipo de trabalho |
|:---:|---|---|---|---|
| **P0** | **Instrumentar `adm/leads`**: `fonte` + `origem_detalhe` obrigatórios na captura; status pós-trial (`trial_ativo`, `assinante`) ou vínculo a `tenant_id`; SLA de 1º contato por categoria; cadência de 5 toques (D0/D+2/D+5/D+10/D+15); alerta de lead fora do SLA; painel com taxas lead→trial e trial→assinante por `fonte`. | **Nenhum canal é medível sem isso.** Indicação, parceria, outbound, evento e prova social dependem de atribuição por `fonte`/`origem_detalhe`. É item nº 1 da janela 30 dias da spec-mãe. | `agente-clinico` (controller/model `adm/Leads`) + `agente-dev-infra` (migração idempotente em `Dev.php` sobre `leads_capturados`) + `agente-frontend` (campo "indicado por" em `/experimentar`) | Migração + código + UI |
| **P0** | **Função de agregação anonimizada de métricas clínicas** (tarefa 6b da triagem): taxa de falta antes/depois do lembrete, nº de confirmações/lembretes por WhatsApp, nº de agendamentos, tenants em produção, tempo até registro de prontuário. Saída sem PII, sem identificar tenant, com janela temporal + tamanho da base. Piso sugerido ≥ 5 tenants. | Desbloqueia os **números da Fase 0 de prova social** (spec 4) e o **link bait por dados originais** do `agente-seo-geo`. Sem ela, a Fase 0 fica só com selo + screenshots (sem percentual). | `agente-clinico` (brainstorming próprio; provável query sobre schema atual, sem migração) | Código (leitura/agregação) |
| **P1** | **Subdomínio / domínio de envio de e-mail separado do transacional**, com SPF/DKIM/DMARC próprios. | Pré-requisito operacional do **piloto de cold outreach**. Enviar e-mail frio do domínio transacional arrisca a entrega das confirmações/lembretes dos clientes pagantes. | `agente-dev-infra` (DNS + config; nada de deploy sem plano) | Infra |
| **P1** | **Campo "Indicado por / código" em `/experimentar`** (com pré-preenchimento por `?ref=`). | Serve **indicação e parcerias** ao mesmo tempo — grava `origem_detalhe`. Sem ele não há KPI por indicador nem por parceiro. Encaixa no P0 (mesma tela). | `agente-frontend` (+ `agente-clinico` para o vínculo no lead) | UI |
| **P2** (condicional) | **Mecânica de cupom / crédito / benefício no sistema**: aplicável ao próximo ciclo, com estado (`reconhecido → em_carência → aplicado / caducado / estornado`), sem reduzir o campo que alimenta o MRR, com trilha em `saas_billing_events`. | Compartilhada entre **indicação fase 2** e **comissão/benefício de parceria**. Só entra com gatilho de volume (§10.0 da spec 3). Até lá, tudo é manual em `adm/saas` + planilha. | `agente-saas-billing` (desenho) + `agente-dev-infra` (migração `saas_referrals` + colunas) | Migração + código |

> **P0 pode e deve começar já.** P1 em paralelo. P2 é backlog condicionado — **não** bloqueia o arranque.

---

## 3. Linha do tempo consolidada

Cada item traz **[dono]** e **(dep: …)**. "Manual" = execução do operador, sem engenharia.

### 3.1 Janela 0–30 dias — destravar medição e ligar o motor alavancado

**Engenharia (caminho crítico):**

1. **[clinico + dev-infra + frontend]** P0 — instrumentar `adm/leads` (`fonte`/`origem_detalhe` + status pós-trial + SLA + cadência + alerta + painel por `fonte`). *(dep: —)*
2. **[clinico]** P0 — brainstorming + build da função de agregação anonimizada de métricas clínicas (piso ≥ 5 tenants). *(dep: —)*
3. **[dev-infra]** P1 — criar subdomínio de envio de e-mail + SPF/DKIM/DMARC. *(dep: —)*
4. **[frontend + clinico]** P1 — campo "Indicado por / código" em `/experimentar` com `?ref=` + vínculo no lead. *(dep: item 1 na mesma tela)*

**Operação (manual, começa já):**

5. **[produto]** Indicação fase 1 — pedir ativamente a **todos** os clientes ativos e trials em andamento; registrar em planilha de controle (§9.4 da spec 3); aplicar desconto de 50% na 1ª mensalidade do indicado no provisionamento. Alvo: **≥ 1 assinante atribuído em 60 dias**. *(dep: —; melhora com item 4)*
6. **[produto]** Prova social **Fase 0** — publicar selo "em produção desde…", screenshots anotados de ambiente de demo, e os números agregados **assim que o item 2 entregar** (até lá, afirmação qualitativa). Meta: no ar em ≤ 2 semanas. *(dep: item 2 para os números)*
7. **[produto]** Mapear e qualificar **20 parceiros multiplicadores** (score 0–12, planilha com os 5 estágios). Priorizar Tier 1: contadores de saúde + associações/sindicatos de clínicas. Meta 30d: 6–10 em estágio (a), 2–3 em (b). *(dep: —)*
8. **[produto + frontend]** Aplicar as **mensagens por segmento do ICP** (spec-mãe §5) em `/experimentar`, no follow-up por WhatsApp e nos scripts de abordagem. *(dep: —)*
9. **[seo-geo]** Receber a repriorização (§4.3 da spec-mãe) e **realinhar a fila** do ciclo semanal, sem reescrever: páginas de decisão comercial primeiro → fechar Capterra/G2/GetApp (cadastro manual) → coletar as 3 primeiras reviews pela **política neutra** → interlink para `/experimentar` → expansão por especialidade **depois**. Atualizar o ledger. *(dep: —)*

### 3.2 Janela 30–60 dias — ativar parcerias, 1ª medição, piloto de outbound

10. **[produto]** **2–3 parcerias em estágio (c) acordo de co-marketing ou (d) 1º lead encaminhado** (não apenas contato). Enviar o "kit do parceiro" em 48h após (c). *(dep: item 7)*
11. **[produto]** Prova social **Fase 1** — 1–2 cases nomeados leves (depoimento curto + 1 métrica), cobrindo 2 segmentos do ICP. Consentimento escrito item a item. *(dep: item 2 para métricas do sistema; cliente engajado disposto)*
12. **[frontend]** Seção de prova social na `index-front.php` (números agregados + faixa de depoimentos + selo; degrada bem só com Fase 0). *(dep: itens 2, 6)*
13. **[produto]** **Piloto de cold outreach** — 50–100 contatos em 2–3 lotes de ~25–40, um por especialidade+praça. LIA + ROPA escritas **antes** do 1º envio. Valida entregabilidade/processo/mensagem, **não** conversão. *(dep: item 3; capacidade da semana)*
14. **[produto]** Iniciar cadência de LinkedIn do Igor — perfil otimizado, Company Page criada, 2–3 posts/semana a partir de cases e blog. Sem cobrança de resultado no curto prazo. *(dep: itens 9, 11 para matéria-prima)*
15. **[produto]** **1 webinar pequeno com um parceiro** — só com folga de provisionamento confirmada para as 2 semanas seguintes; teto de inscrição = capacidade livre ÷ taxa esperada; convites de trial escalonados. *(dep: item 10; nunca na mesma janela de um lote de e-mail)*
16. **[produto]** **1ª revisão de KPIs por canal** (§8.3 da spec-mãe). *(dep: item 1)*
17. **[saas-billing]** Consolidar e documentar o processo manual de indicação (ou implementar a mecânica, se o orquestrador priorizar); definir se o desconto da 1ª mensalidade do indicado é redução de fatura ou CAC — de forma **consistente** com o tratamento de trial. *(dep: item 5 rodando)*

### 3.3 Janela 60–90 dias — dobrar o que converte, decidir escalonamento

18. **[produto]** Parcerias: **dobrar** as que chegaram a (d) com ≥ 2 leads ou ≥ 1 assinante; **cortar** as paradas em (a)/(b) ou em (c) há > 6 semanas sem data. Manter portfólio de 4–6 ativas em (c)+. Meta 90d: 5–6 em (c), 2–3 em (d), 1 em (e) *se acontecer*. *(dep: item 10)*
19. **[produto]** **Decisão sobre cold outreach** (§9.4 da spec 2) — qualitativa + custo de operador: vira canal recorrente de baixa intensidade? precisa de ferramenta paga ou 2ª pessoa? encerra? *(dep: item 13)*
20. **[produto]** Prova social **Fase 2** — 3–5 cases completos com página `/casos/{slug}` + PDF, 1–2 por segmento. Interlink pelo `agente-seo-geo`. *(dep: itens 11, 21, 22)*
21. **[frontend]** Template `/casos` (índice) + `/casos/{slug}` — estrutura dor→antes→virada→depois→métrica→citação→screenshot→CTA; tags OG/Twitter; componente de depoimento reutilizável. *(dep: item 12)*
22. **[dev-infra]** Rotas `/casos` e `/casos/{slug}`. *(dep: item 21; sem migração prevista)*
23. **[produto → orquestrador]** **Avaliar o teto de provisionamento** (§7 da spec-mãe) — se a demanda encostar em 20–30 trials/mês, elevar no roadmap **onboarding self-service** e **controle de limites de plano em tempo real**. *(dep: item 16)*
24. **[saas-billing]** Especificar o **dashboard de funil produto-led / MRR por canal** (funde-se ao "Dashboard de métricas para o admin" já no §15.3 do CLAUDE.md). *(dep: item 1)*
25. **[produto]** Refinar os playbooks 1 e 2 com dados reais dos 90 dias; atualizar o ledger de SEO com resultados. *(dep: itens 16, 19)*

### 3.4 Backlog condicionado (pós-90 dias / por gatilho)

- **[saas-billing + dev-infra]** **Indicação fase 2** (automação) — tabela `saas_referrals`, código do indicador, `?ref=`, vínculo em `leads_capturados`, registro de crédito com estado/carência/clawback, antifraude automática, UI no painel do tenant e em `adm/saas`, relatórios. **Gatilho:** ≥ 5 indicações/mês por 2 meses, **ou** ≥ 15 clientes ativos elegíveis, **ou** controle manual > 2 h/semana.
- **[frontend + dev-infra]** **Landing co-branded** `/p/{slug}` — só se um parceiro provar volume em estágio (d).
- **[saas-billing]** Mecânica única de **cupom/benefício** que sirva indicação e comissão de parceria (one-time × recorrente, MRR bruto × líquido).
- **[produto → orquestrador]** Reabrir cold outreach em escala **só** com 2ª pessoa ou quando indicação/parceria saturarem.
- **[produto]** Templates dedicados de WhatsApp (categoria marketing + opt-in) se o nurture por WhatsApp justificar — **sempre** com número separado do transacional e aprovação Meta.

---

## 4. Pacotes de trabalho por agente de domínio

Ordem dentro de cada pacote = ordem de execução.

### `agente-clinico`
1. **P0** — instrumentar `adm/Leads`: `fonte`/`origem_detalhe` obrigatórios, status pós-trial / vínculo a `tenant_id`, fila prioritária para `indicacao` e `parceria`, SLA por categoria, cadência de 5 toques, alerta de SLA, painel por `fonte`. (com `dev-infra` + `frontend`)
2. **P0** — função de agregação anonimizada de métricas clínicas (tarefa 6b): brainstorming próprio, define o piso de base (≥ 5 tenants), saída sem PII / sem identificar tenant, com janela + base amostral; recorte por tenant para case específico sujeito a confirmação do cliente. Provável query sobre schema atual.
3. Vínculo `lead ↔ indicador` e exibição da origem `indicacao` no lead. (fase 2 da indicação, condicionado)

### `agente-dev-infra` (único que publica em produção)
1. **P0** — migração idempotente em `adm/Dev.php` sobre `leads_capturados`: colunas `fonte`, `origem_detalhe`, status pós-trial / `tenant_id`, e o que a instrumentação do `agente-clinico` exigir. Protegida por `nivel == 1`.
2. **P1** — subdomínio/domínio de envio de e-mail separado do transacional + SPF/DKIM/DMARC. Pré-requisito do piloto de outbound.
3. **90d** — rotas `/casos` e `/casos/{slug}` (sem migração).
4. **Backlog condicionado** — migração `saas_referrals` + colunas de código do indicador (só no gatilho da fase 2).
5. Deploy FTP + `php -l` + healthcheck de cada entrega acima. Nada de deploy sem plano.

### `agente-frontend`
1. **P0/P1** — campo "Indicado por / código" em `/experimentar` com `?ref=`; copy curta do benefício de entrada.
2. **P1** — aplicar mensagens por segmento do ICP (spec-mãe §5) em `/experimentar` e nas landings.
3. **60d** — seção de prova social na `index-front.php` (números agregados + depoimentos + selo; degrada bem só com Fase 0); bloco de números reutilizável alimentado pela função de agregação (não hard-coded).
4. **90d** — template `/casos` + `/casos/{slug}`; componente de depoimento reutilizável para landings `seo_*` e comparativos; tags OG/Twitter; checklist de revisão de imagem (sem PII em screenshot).
5. **Backlog condicionado** — bloco "Indique e ganhe" no painel do tenant; landing co-branded `/p/{slug}`.
> Não mexe em controller/model nem em rotas — recebe o comportamento do `agente-clinico` / `agente-produto`.

### `agente-saas-billing`
1. **30–60d** — definir tratamento do desconto de 1ª mensalidade do indicado (redução de fatura × CAC), consistente com o tratamento de trial; consolidar o processo manual de crédito em `adm/saas`.
2. **90d** — especificar o dashboard de funil produto-led / MRR por canal (funde-se ao item de dashboard do admin no §15.3).
3. **Avaliar** — cota de WhatsApp ampliada durante o trial (para o lead ver a confirmação por WhatsApp funcionando).
4. **Backlog condicionado (gatilho §10.0 da spec 3)** — mecânica de indicação no sistema: registro do crédito (opções a/b/c da §7.3) **sem contaminar o MRR**, máquina de estados, carência de 60 dias, clawback, antifraude automática (match CNPJ/CPF/e-mail/telefone/pagamento; uso real do tenant; lead prévio de outro canal), teto de exposição (~R$ 900/mês na fase 1), relatórios custo × MRR.
5. **Regra fixa** — nenhuma comissão recorrente de parceria entra sem modelagem de CAC/margem; se algum dia adotar desconto recorrente, todos os relatórios passam a separar MRR bruto × líquido e churn calcula sobre o bruto.

### `agente-seo-geo`
1. **Já** — aplicar a repriorização (§4.3 da spec-mãe), sem reescrever nada: páginas de decisão comercial → Capterra/G2/GetApp + 3 primeiras reviews → interlink comercial → expansão por especialidade depois. Atualizar o ledger e reportar.
2. **Contínuo** — coletar reviews pela **política neutra** (spec 4 §7): lista de convite = todos os clientes elegíveis, mesmo texto, mesmo agradecimento simbólico sem condicionar à nota, registro de convites para auditoria, responder todas as avaliações. Conferir os termos de cada vendor antes de campanha.
3. **Quando houver números** — distribuir os agregados da função do `agente-clinico` como pauta de PR / link bait; link reclamation das citações sem link.
4. **90d** — publicar os cases (`/casos/{slug}`) e interlink com landings de especialidade, comparativos `/alternativa-*` e blog do segmento.
> **Não reabrir** a estratégia de SEO on-page / GEO / keyword research / diretórios / guest posts / link building — já documentada. Só recebe repriorização de fila.

### `agente-whatsapp`
- **Nenhuma mudança de código.** Registro explícito: o outbound e a coleta de depoimento/review **não** usam o número transacional de confirmação de agendamento. Se algum dia houver nurture/prospção por WhatsApp, é **número separado**, opt-in explícito e template categoria marketing aprovado na Meta. Avaliar com `saas-billing` a cota de disparos durante o trial (item 3 do pacote de billing).

### `agente-produto`
1. Executa os playbooks 1 e 2 (prospecção, qualificação, abordagem, acordos, cadência, catálogo de objeções).
2. Opera a indicação fase 1 (pedido ativo, planilha de controle, concessão manual de crédito) e mantém teto de exposição, valores de recompensa e o gatilho da fase 2.
3. Conduz as entrevistas de case, escreve os rascunhos, coleta consentimento.
4. Dono das revisões de KPI (quinzenal + mensal), da decisão de 90 dias sobre outbound, e de levar objeções de posicionamento/preço e gaps de produto ao orquestrador.
> Não edita código; escreve só em `docs/`.

### `orquestrador`
- Sequencia a implementação a partir deste roadmap, começando por P0. Recebe de volta: objeções que virem gap de produto (do playbook 2), decisão de escalonamento de capacidade (item 23), gatilho da fase 2 da indicação.

---

## 5. Mudanças consolidadas no roadmap do produto (`CLAUDE.md` §15)

**Novos itens — prioridade alta:**
- Instrumentação de origem + SLA/cadência de follow-up em `adm/leads` (`fonte`, `origem_detalhe`, status pós-trial, alerta de SLA, painel por `fonte`). **Pré-requisito de medição de todo o roadmap orgânico.**
- Função de agregação anonimizada de métricas clínicas (piso ≥ 5 tenants) — desbloqueia prova social Fase 0 e link bait.

**Novos itens — §15.3 (backlog → médio):**
- Programa de prova social — Fase 0 (números agregados + selo + screenshots) + seção na `index-front.php`.
- Template de página de case (`/casos`) + PDF de case para outreach.
- Programa de indicação — mecânica no sistema (fase 2, condicionada ao gatilho de volume) com tratamento de MRR bruto/líquido.
- Subdomínio de envio de e-mail separado do transacional (SPF/DKIM/DMARC) — pré-requisito do piloto de outbound.

**Reforço a item já existente:**
- "Dashboard de métricas para o admin" (§15.3) passa a incluir funil produto-led e MRR/economia de programa por canal.
- "Onboarding self-service" e "controle de limites de plano em tempo real" sobem de prioridade **se** a demanda encostar no teto de 20–30 trials/mês (revisão aos 90 dias).

**Repriorização (sem item novo) — `agente-seo-geo`:**
- Páginas de decisão comercial + Capterra/G2/GetApp + primeiras reviews (política neutra) **antes** da expansão por especialidade.

**Sem mudança de pricing/planos.**

---

## 6. Funil unificado

```
  Canal ──► Lead (adm/leads, com fonte + origem_detalhe) ──► Trial (/experimentar) ──► Assinante
```

| Canal | `fonte` | `origem_detalhe` | Fila | SLA 1º contato |
|-------|---------|------------------|------|----------------|
| SEO / GEO / blog / diretórios | `organico` \| `diretorio` | — | Normal | 1 dia útil |
| Indicação | `indicacao` | nome do indicador (+ `tenant_id` do indicador) | **Prioritária** | 4h úteis |
| Parceria | `parceria` | nome do parceiro | **Prioritária** | 4h úteis |
| Cold outreach (piloto) | `outbound` | id do lote (`2026-10-odonto-sp`) | Normal | 1 dia útil |
| LinkedIn | `linkedin` | `linkedin-dm` \| `linkedin-post` \| `linkedin-perfil` | Normal | 1 dia útil |
| Evento / webinar | `evento` | nome do evento (`webinar-faltas-2026-10`) | **Agendada com a capacidade da semana** | mesmo dia se pediu demo; senão follow-up de evento |
| Entrou direto em `/experimentar` | mantém a `fonte` de origem | — | — | mesmo dia (intenção máxima) |
| WhatsApp opt-in | mantém a `fonte` original | — | — | nurture do lead existente |

**Cadência de nurture pós-lead:** 5 toques — D0, D+2, D+5, D+10, D+15 — alternando WhatsApp / e-mail / telefone. Sem resposta no D+15 → `descartado` (mantém a `fonte` para medição).

**Capacidade:** ~1,5–2 h de operador por trial que ativa → teto **20–30 trials/mês**. Sinais de estouro: provisionar lead quente leva > 1 dia útil; trial sem contato humano na 1ª semana; alta de "não sei usar"; queda de trial→assinante com volume subindo. Resposta: fila priorizada por `fonte`, janelas fixas de provisionamento, onboarding assíncrono, elevar self-service no roadmap, 2ª pessoa, segurar canais volumosos (nunca a indicação).

---

## 7. KPIs consolidados

### 7.1 Funil (agregado, revisão mensal)
- Leads/mês, trials/mês, novos assinantes/mês.
- Conversão composta lead→trial→assinante.
- MRR novo/mês e **MRR por canal**.
- Churn de trial (não ativou) e de assinante em 90 dias.
- **Utilização de capacidade:** trials provisionados ÷ teto.

### 7.2 Por canal (revisão quinzenal)
- Nº de leads/mês por `fonte`; custo por lead **em horas de operador**; taxa lead→trial e trial→assinante por `fonte`; tempo médio de 1º contato vs SLA; CAC em horas de operador por assinante.
- **Indicação:** % de clientes ativos que indicaram (≥ 20% em 90d), indicações por indicador (≥ 1,5), taxa indicação→assinante (≥ 20%), custo do programa ÷ MRR de indicados (≤ 1,0 no 1º ciclo), payback (≤ 1,5 mês), fraude não detectada = 0.
- **Parcerias:** nº de parceiros por estágio (a/b/c/d/e) — **KPI-chefe de curto prazo**; taxa de avanço entre estágios; tempo a→d; leads por parceiro; MRR atribuído (bruto e líquido); **concentração** (alerta se > 50% do MRR de parceria vem de um parceiro).
- **Cold outreach (piloto):** taxa de entrega (≥ 95%), bounce (< 3% — senão parar), reclamação de spam (< 0,1% — senão parar), taxa de resposta (sinal bom ≥ 3–5%), objeções catalogadas (meta = cobertura), horas de operador por lead. **Não** se mede conversão com significância.
- **LinkedIn:** só indicadores de ativo (visualizações de perfil, taxa de aceite de convite, impressões/comentários por post, DMs espontâneas). Leads esperados: 0–2/mês — normal.
- **Eventos:** inscritos (dentro do teto), comparecimento (30–50% é normal), participantes engajados, trials originados, horas de operador × retorno.
- **Prova social:** nº de depoimentos publicados (≥ 2 em 60d, ≥ 5 em 120d), nº de cases (1 por segmento em 90–120d), tempo até o 1º case nomeado (≤ 60d), reviews por diretório (≥ 3 em 90d) e nota média (≥ 4,0 observado), % de canais ativos com ≥ 1 peça de prova, efeito na conversão da landing (antes/depois via tráfego de IA + FB CAPI).

### 7.3 Cadência de revisão
| Frequência | O que revisa |
|------------|--------------|
| Semanal | Fila de leads, cumprimento de SLA, trials da semana vs capacidade, bounce/spam do outbound |
| Quinzenal | KPIs por canal, ajuste de foco (junto da varredura de roadmap do `agente-produto`) |
| Mensal | MRR por canal, dobrar/cortar canal e parceria, revisão de capacidade |
| Trimestral | ICP/posicionamento, escolha de canais-foco, validade das métricas e consentimentos de case |

---

## 8. Registro consolidado de riscos

| # | Risco | Origem | Mitigação |
|---|-------|--------|-----------|
| 1 | **Política Meta / WhatsApp** — prospecção sem opt-in bane o número; o número pode ser o mesmo da confirmação de agendamento dos clientes → incidente operacional | specs 0, 2, 4 | Zero WhatsApp frio. WhatsApp de vendas só pós-opt-in e de **número separado** do transacional. Template categoria marketing só aprovado na Meta. Depoimento/review 1:1, sem disparo em massa. |
| 2 | **Reputação de domínio de e-mail** — bounce/spam do cold outreach derruba a entrega do e-mail transacional dos clientes | spec 2 | Subdomínio/domínio de envio **separado** (P1), SPF/DKIM/DMARC, aquecimento, ≤ 20–30/dia, parar a leva se bounce ≥ 3% ou spam ≥ 0,1%. |
| 3 | **Política Capterra / G2 / GetApp** — review incentivado/gated ou cherry-picking = penalidade no perfil | spec 4 | Política neutra documentada (spec 4 §7): convidar todos os elegíveis, mesmo texto, mesmo agradecimento sem condicionar à nota, registro de convites, conferir termos do vendor. Case ≠ review — listas e processos separados. |
| 4 | **LGPD** — listas de outbound, repasse de contato na indicação/parceria, foto/nome/logo em case | specs 2, 3, 4 | Outbound: LIA + ROPA escritas antes do 1º envio, só fonte pública com URL registrada, sem dado sensível, opt-out honrado + supressão permanente, retenção 12 meses. Indicação: modo link/código é padrão; repasse de contato só com consentimento declarado + opt-out no 1º toque. Case: consentimento escrito item a item; sem PII de paciente; screenshot só de ambiente de demo. |
| 5 | **Integridade de MRR** — desconto/comissão recorrente corrói MRR e distorce churn | specs 0, 1, 3 | Preferir **crédito único** (é CAC, não reduz MRR) e **condição para associados** (sem pagamento ao parceiro). Comissão só one-time sobre a 1ª mensalidade, com modelagem de CAC. Se adotar recorrente: separar MRR bruto × líquido em todos os relatórios, churn sobre o bruto. |
| 6 | **Capacidade do operador único** — pico de trial (webinar, leva de e-mail) quebra a primeira impressão | specs 0, 1, 2 | Teto 20–30 trials/mês; casar cada ação de pico com folga de provisionamento confirmada; teto de inscrição por capacidade; convites de trial escalonados; adiar o webinar se a capacidade cair; nunca lote de e-mail + webinar na mesma janela; indicação nunca cede fila. |
| 7 | **Concentração em 1 pessoa** — férias/doença param o funil | specs 1, 2, 4 | Playbooks + planilhas permitem que um terceiro opere o essencial; não iniciar leva/lote às vésperas de ausência; Fase 0 de prova social não depende de ninguém. |
| 8 | **Atribuição ausente** — sem `fonte`/`origem_detalhe` não dá para saber qual canal converte | spec 0 | P0 é o item nº 1 do roadmap; `origem_detalhe` obrigatório; se o parceiro não disse quem indicou, perguntar ao lead no 1º contato. |
| 9 | **Ciclo de parceria longo (2–4 meses)** — cobrança de assinante dentro dos 90 dias reprova um canal saudável | spec 1 | KPI de curto prazo é nº de parcerias em (c)/(d), não MRR fechado. Estágio (e) pode não ocorrer na janela e isso não reprova o canal. |
| 10 | **Base pequena de clientes** — indicação sem combustível, cases sem gente nomeável | specs 3, 4 | Indicação fase 1 = pedir aos poucos que existem, 100% manual; automação só com gatilho de volume. Prova social faseada: Fase 0 sem cliente nomeado. |
| 11 | **Conflito com política de conselho** — autarquia veda endosso comercial / comissão | spec 1 | Verificar estatuto/regimento antes de abordar seccional; nunca propor comissão a conselho; só formato educativo (palestra sem pitch) + benefício ao inscrito + listagem. |
| 12 | **Métrica inflada / indefensável** em prova social | spec 4 | Lastro obrigatório (cálculo + data), intervalo em vez de número cravado, recorte honesto no denominador ("clínicas que ativaram o lembrete"), revisão trimestral, cliente confirma o número do case. |
| 13 | **Dano de marca** — pitch agressivo no LinkedIn / spam em comunidade / co-branding com parceiro ruim | specs 1, 2 | Value-first, sem automação de DM/post; regra de aquecimento de ~2 semanas em comunidade (reafirma SEO offpage §4.2); due diligence leve antes de co-branding. |

---

## 9. Critérios de aceite de valor consolidados (90 dias)

**Medição e instrumentação**
1. Todo lead em `adm/leads` tem `fonte` preenchida; ≥ 90% com 1º contato dentro do SLA da categoria.
2. "Quantos assinantes vieram de cada canal neste mês" é respondível em < 5 minutos.
3. Função de agregação de métricas clínicas entregue, com piso de base definido; ≥ 1 número agregado publicado com período + base + fonte.

**Canais**
4. Pelo menos 2 canais-foco com conversão lead→assinante medida e positiva.
5. Indicação: ≥ 1 assinante novo atribuído em 60 dias; todo crédito rastreável e reconciliável com o MRR; zero fraude não detectada.
6. Parcerias: 20 mapeadas/qualificadas; ≥ 5 em estágio (c) e ≥ 2 em (d). (Assinante atribuído pode não ocorrer na janela — não reprova o canal.)
7. Cold outreach: LIA + ROPA vivas; bounce < 3% e spam < 0,1%; nenhum efeito na entrega do e-mail transacional; catálogo de objeções preenchido; decisão de 90 dias registrada (qualitativa + horas de operador).
8. Prova social: Fase 0 no ar em ≤ 2 semanas só com fato verificável; 1º case nomeado em ≤ 60 dias; ≥ 1 peça de prova em uso em cada canal ativo.
9. SEO/GEO: fila repriorizada e refletida no ledger; Capterra/G2/GetApp com as 3 primeiras reviews por processo neutro documentado.

**Segurança / conformidade / saúde da operação**
10. Nenhuma violação registrada de política Meta / Capterra / G2 / LGPD.
11. Conversão trial→assinante **não cai** enquanto o volume de trials cresce até o teto de ~20–30/mês.
12. Nenhuma comissão recorrente ativa sem modelagem de CAC/margem; MRR não distorcido pelos programas (ou bruto × líquido separados).

---

## 10. Próximo passo

**Começar por P0, em paralelo:**
- `agente-clinico` + `agente-dev-infra` + `agente-frontend` → instrumentação de `adm/leads` (roda o pipeline superpowers: brainstorming → writing-plans → TDD → code-review → verification, e `agente-dev-infra` fecha com deploy).
- `agente-clinico` → função de agregação anonimizada (brainstorming próprio).
- `agente-dev-infra` → subdomínio de e-mail (P1, independente).

**Em paralelo, sem engenharia:**
- `agente-produto` → indicação fase 1 (pedido ativo à base), mapeamento dos 20 parceiros, prova social Fase 0 (selo + screenshots).
- `agente-seo-geo` → aplicar a repriorização da fila e atualizar o ledger.

As demais janelas (30–60–90) entram conforme as dependências da seção 3 forem sendo fechadas.
