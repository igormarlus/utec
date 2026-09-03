# Estratégia de Aquisição de Clientes por Canais Orgânicos — spec-mãe

- **Data:** 2026-09-03
- **Autor:** agente-produto
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** spec de negócio (o "o quê" e o "porquê" — antecede brainstorming técnico)
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.

---

## 1. Objetivo e recorte

### 1.1 O que é

Definir a estratégia-mãe de aquisição de clientes pagantes para o UTecnologia
Saúde **sem investimento em mídia paga**, executável por **um único operador**
(Igor), respeitando o gargalo atual de **provisionamento manual de tenant**.

A spec organiza os canais orgânicos disponíveis, escolhe 3–4 focos, define
priorização em janelas de 30/60/90 dias, as mensagens por segmento do ICP, o
funil canal → lead → trial → assinatura com o SLA mínimo de follow-up, o teto
de capacidade de 1 operador e o framework de KPIs. Serve de entrada para o
orquestrador decompor em tarefas técnicas e para os playbooks e specs
derivados (seção 10).

### 1.2 O que NÃO é

- **Não** é a estratégia de SEO on-page, GEO, keyword research, landings
  `seo_*`, blog, diretórios de software (Capterra, G2, GetApp), guest posts
  nem link building. Isso pertence ao `agente-seo-geo` e já está documentado em:
  - `docs/seo-offpage-linkbuilding-2026-06-04.md` (7 pilares de offpage)
  - `docs/superpowers/specs/2026-08-20-seo-geo-leads-design.md` (GEO orientado à conversão)
  - `docs/superpowers/specs/2026-08-31-seo-geo-leads-fase-2-design.md` (objeção comercial primeiro)
  - memória `project_seo_offpage` (status por pilar, bloqueios de cadastro)

  Esta spec apenas **referencia** esses docs e indica a **repriorização** que o
  `agente-seo-geo` deve aplicar (seção 4.3). Não detalha tática de SEO.
- **Não** é mídia paga (Google Ads, Meta Ads, LinkedIn Ads) — fora de escopo
  por decisão de negócio nesta fase.
- **Não** define implementação técnica de nenhum ajuste em `adm/leads`,
  `produtos`, WhatsApp ou dashboard. A spec diz o comportamento esperado; o
  orquestrador roteia para os agentes de domínio.
- **Não** cria onboarding self-service — segue no roadmap como item futuro
  (`CLAUDE.md` §15.3). A estratégia é desenhada para conviver com o
  provisionamento manual.

### 1.3 Premissas

- Fase comercial: provisionamento **manual pelo admin** (`adm/saas`).
- Operação: **1 pessoa** acumula produto, vendas, provisionamento e suporte.
- Já existe: trial de 30 dias (`/experimentar?tipo=clinica|profissional`),
  captura de leads em `adm/leads` (tabela `leads_capturados`, **sem SLA de
  follow-up**), landings `seo_*`, blog, monitoramento de tráfego de IA, FB
  Conversions API, WhatsApp Cloud API própria (limite de 3 disparos por tenant
  sem assinatura ativa — recurso de confirmação de agendamento, não de vendas).
- Planos: Solo R$ 79/mês, Clínica R$ 199/mês, Pro R$ 399/mês, Enterprise
  negociado (`CLAUDE.md` §2.3).
- ICP: (a) clínica pequena 1–5 médicos, (b) profissional autônomo, (c) clínica
  média 5–20 (`CLAUDE.md` §2.2).

---

## 2. Inventário de canais sem mídia paga

### 2.1 Matriz esforço × impacto × tempo-até-resultado

Esforço estimado para **1 operador**. Custo financeiro de todos é ~R$ 0
(o custo real é hora de operador). "Tempo até 1º resultado" = primeiro lead
qualificado atribuível ao canal.

| # | Canal | Esforço (1 op) | Impacto potencial | Tempo até 1º resultado | Tipo de lead | Observação |
|---|-------|----------------|-------------------|------------------------|--------------|------------|
| 1 | SEO / conteúdo / GEO | Médio-alto, mas **terceirizado ao `agente-seo-geo`** (ciclo semanal) | Alto e composto | 3–6 meses | Morno, intenção variável | Já em execução. Produto só repriorize (4.3). |
| 2 | Diretórios de software (Capterra, G2, GetApp) | Baixo-médio (cadastro + coleta de reviews) | Médio-alto (tráfego de intenção de compra) | 1–3 meses (precisa de reviews) | Quente | Dono: `agente-seo-geo`. Anti-bot conhecido — cadastro manual do Igor (memória `project_seo_offpage`). |
| 3 | Comunidades e grupos de médicos / profissionais (Facebook, WhatsApp, Telegram, `comunidades.net`) | Alto e contínuo (presença diária; ~2 semanas de "aquecimento" antes de citar produto) | Médio | 1–2 meses | Morno | Risco de ban por autopromoção. Regra em `docs/seo-offpage-linkbuilding-2026-06-04.md` §4.2. |
| 4 | Parcerias com multiplicadores (contadores/contabilidade para saúde, conselhos regionais CRO/CREFITO/CRP/CRM, associações e sindicatos de clínicas, distribuidoras odonto, cursos/faculdades) | Médio (prospecção + relacionamento; esforço concentrado no início) | **Alto e alavancado** (1 parceiro = fluxo recorrente pré-qualificado) | 2–4 meses (ciclo de confiança) | Quente, pré-qualificado pelo intermediário | Melhor relação impacto/hora para 1 operador. |
| 5 | Indicação / referral (member-get-member) | Baixo-médio (montar mecânica + pedir ativamente) | **Alto quando há base** (base pequena hoje) | Imediato com clientes atuais; cresce com a base | Quentíssimo (chega quase fechado) | MRR precisa distinguir bruto/líquido se houver desconto (9.5). |
| 6 | Cold outreach segmentado (e-mail B2B, telefone; WhatsApp só com opt-in) | Alto (list building + personalização + follow-up) | Médio-alto e **previsível/escalável** | 2–6 semanas | Frio | Risco LGPD (9.3) e política Meta (9.1). Gera picos que estouram o provisionamento manual. |
| 7 | LinkedIn orgânico (perfil do Igor + página da empresa) | Médio (2–3 posts/semana + social selling) | Baixo-médio no curto (rede pequena), composto | 2–4 meses | Morno | Detalhe tático em `docs/seo-offpage-linkbuilding-2026-06-04.md` §4.3. |
| 8 | WhatsApp com opt-in (nutrição de leads que já optaram) | Baixo (após o lead entrar) | **Alto na conversão** lead→trial (canal preferido do ICP) | Imediato | Converte lead existente | Camada de conversão, não canal de topo. Política Meta (9.1). |
| 9 | Prova social / cases / depoimentos | Médio (produzir 3–5 cases + consentimento) | **Alto (multiplicador de todos os outros canais)** | 3–6 semanas | Aumenta conversão de todos | Alimenta site, LinkedIn, outreach e reviews de diretório ao mesmo tempo. |
| 10 | Eventos e webinars (online; congressos de especialidade; feiras odonto) | Alto (organizar/participar) | Médio (lote de leads qualificados de uma vez) | 4–8 semanas | Morno-quente | Gera pico — pode saturar o provisionamento. Fazer pequeno e com parceiro. |
| 11 | Produto-led (trial `/experimentar`) | Médio (otimizar a página + onboarding manual) | **Alto — é o destino de todos os canais** | Imediato | — (é a conversão) | O provisionamento manual é o gargalo central (seção 7). |

### 2.2 Leitura da matriz para 1 operador

- Canais **compostos e de baixo esforço marginal** (1, 2) já rodam via
  `agente-seo-geo` — mantê-los, só repriorizar.
- Canais **alavancados** (4, 5) entregam o melhor retorno por hora de 1
  operador e produzem leads quentes que exigem **pouco provisionamento e pouco
  hand-holding** — encaixam no gargalo.
- Canais de **alto esforço contínuo ou spiky** (3, 6, 10) e de **retorno
  lento** (7) ficam em piloto pequeno ou backlog; não podem ser o motor agora.
- (8, 9) não são canais de topo: são **camadas de conversão** aplicadas sobre
  o que os outros geram.

---

## 3. Escolha dos canais-foco (3–4) e o gargalo

### 3.1 Abordagens consideradas

- **Abordagem A — Inbound composto primeiro** (SEO/GEO/diretórios/conteúdo).
  Baixo esforço marginal, retorno alto e cumulativo, já em curso.
  *Trade-off:* 3–6 meses até volume relevante; não gera nada "esta semana".
- **Abordagem B — Alavancagem por terceiros** (parcerias + indicação).
  Poucas relações geram fluxo recorrente pré-qualificado; cabe em 1 operador;
  leads quentes = provisionamento rápido e conversão alta.
  *Trade-off:* ciclo de confiança lento no arranque; indicação depende de base
  instalada, que hoje é pequena.
- **Abordagem C — Outbound direto** (cold outreach + LinkedIn + eventos).
  Previsível e escalável, resultado em semanas.
  *Trade-off:* alto esforço contínuo de 1 pessoa; risco LGPD/Meta; gera picos
  de trial que o provisionamento manual não absorve.

### 3.2 Recomendação

**Motor B + base A (já rodando) + camada de conversão (8 + 9); C só em piloto
pequeno e controlado.**

Canais-foco:

1. **SEO / GEO / conteúdo + diretórios** (canais 1 e 2) — base já em execução
   pelo `agente-seo-geo`. Esforço marginal do produto = uma repriorização
   (4.3). Fluxo estável e crescente de leads mornos.
2. **Indicação / referral** (canal 5) — maior alavancagem por hora de 1
   operador. Começa **já**, com os clientes atuais e os trials ativos. Leads
   chegam quase fechados → provisionamento rápido.
3. **Parcerias com multiplicadores** (canal 4) — contadores de saúde +
   associações/sindicatos de clínicas + seccionais de conselho. Esforço
   concentrado no início, cauda longa. Cada relação = fila recorrente de leads
   pré-qualificados.
4. **WhatsApp opt-in + prova social como camada de conversão** (canais 8 e 9)
   — aplicada sobre os leads que 1–3 geram, para elevar lead→trial→assinatura
   **sem aumentar a carga de provisionamento**.

Deliberadamente **fora do foco agora** (backlog / piloto):

- Cold outreach em escala (canal 6): alto esforço + risco LGPD/Meta + geraria
  pico de trials fora da capacidade. Só um **piloto de 50–100 contatos** em 60
  dias para validar mensagem.
- Eventos/webinars grandes (canal 10): spiky. Só **1 webinar pequeno com
  parceiro** em 60 dias.
- LinkedIn como canal primário (canal 7): retorno lento. Manter cadência leve
  (2 posts/semana) a reboque da prova social, sem cobrar resultado no curto.
- Comunidades (canal 3): manter presença genuína e de baixa intensidade;
  não é motor.

### 3.3 O gargalo: provisionamento manual

Todo canal que gere **pico** de trials quebra a primeira impressão, porque 1
operador não provisiona nem faz onboarding de um lote de uma vez. Por isso a
escolha privilegia **canais de leads quentes, de volume baixo-médio e alta
conversão** (indicação, parceria) e trata os canais volumosos/spiky com
cautela. O teto e o plano de escalonamento estão na seção 7.

---

## 4. Priorização 30 / 60 / 90 dias

### 4.1 Janela 30 dias — instrumentar e ligar o motor alavancado

| # | Entregável | Domínio (handoff) |
|---|------------|-------------------|
| 1 | **Instrumentar `adm/leads` com origem + SLA** — campo `fonte`/`canal` e `origem_detalhe` (quem indicou / qual parceiro / qual campanha) obrigatórios na captura; status pós-trial (`trial_ativo`, `assinante`) ou vínculo a `tenant_id`; alerta de lead sem 1º contato acima do SLA (seção 6). | `agente-clinico` (controller/model `adm/Leads`) + `agente-dev-infra` (migração do campo) + `agente-frontend` (form `/experimentar` com "indicado por") |
| 2 | **Programa de indicação — v0 manual** — mecânica definida (crédito único vs desconto — ver spec derivada 10.3), operada com cupom/registro em `observacoes` do lead enquanto não há sistema. Pedir indicação ativamente a **todos** os clientes atuais e trials ativos. | `agente-produto` (spec) → `agente-saas-billing` (mecânica no sistema, MRR) |
| 3 | **3 cases + depoimentos** com clientes atuais (formato e consentimento na spec derivada 10.4). | `agente-produto` (spec) → `agente-seo-geo` + `agente-frontend` (publicação) |
| 4 | **Repriorização ao `agente-seo-geo`** (ver 4.3) — páginas de decisão comercial + finalizar Capterra/G2 (manual) + coletar reviews dos mesmos 3 clientes dos cases. | `agente-seo-geo` |
| 5 | **Mapear 20 parceiros multiplicadores** da região (contadores de saúde, sindicato/associação de clínicas, seccional de conselho) — planilha com contato e status (playbook derivado 10.1). | `agente-produto` |
| 6 | **Mensagens por segmento** (seção 5) aplicadas em `/experimentar`, no follow-up por WhatsApp e nos scripts de abordagem. | `agente-produto` + `agente-frontend` (copy da landing) |

### 4.2 Janela 60 dias — ativar parcerias e primeira medição

| # | Entregável | Domínio |
|---|------------|---------|
| 1 | **2–3 parcerias piloto ativas** — co-marketing: conteúdo conjunto, condição para associados, encaminhamento de leads. | `agente-produto` |
| 2 | **Programa de indicação com mecânica no sistema** (se priorizado pelo orquestrador) ou processo manual consolidado e documentado. | `agente-saas-billing` |
| 3 | **1 webinar pequeno com um parceiro** (associação/curso) — lote controlado e agendado de leads, casado com a capacidade de provisionamento da semana. | `agente-produto` |
| 4 | **Cases publicados** no site e no LinkedIn; iniciar cadência do Igor no LinkedIn (2 posts/semana a partir dos cases e artigos do blog). | `agente-seo-geo` + `agente-frontend` |
| 5 | **Piloto de cold outreach** — 50–100 contatos B2B conforme LGPD (9.3), opt-out claro, só para validar mensagem. **Não escalar.** | `agente-produto` (playbook 10.2) |
| 6 | **1ª revisão de KPIs por canal** (seção 8). | `agente-produto` |

### 4.3 Repriorização a aplicar no `agente-seo-geo` (não é tática nova)

Dentro do que já está nos docs de SEO/GEO, a ordem que serve esta estratégia:

1. **Páginas de decisão comercial primeiro** — comparativos (`alternativa-*`),
   "quanto custa", "como migrar da planilha", "trial vs gratuito" — conforme
   `docs/superpowers/specs/2026-08-31-seo-geo-leads-fase-2-design.md` §10.
2. **Fechar Capterra + G2 + GetApp** (cadastro manual do Igor — anti-bot
   documentado em `project_seo_offpage`) e **coletar as 3 primeiras reviews**
   dos mesmos clientes usados nos cases (seção 4.1 item 3).
3. **Interlinking** das páginas de decisão para `/experimentar` e `/contato`
   com a mensagem por segmento da seção 5.
4. Expansão por especialidade **depois** do núcleo comercial (mantém a decisão
   já registrada na fase 2).

O `agente-seo-geo` mantém a autonomia do ciclo semanal (`seo-geo-agent`); esta
spec só fixa a **ordem de prioridade** enquanto a estratégia de vendas
orgânicas estiver ativa.

### 4.4 Janela 90 dias — dobrar o que converte, decidir escalonamento

| # | Entregável | Domínio |
|---|------------|---------|
| 1 | **Dobrar as parcerias que converteram; cortar as que não.** | `agente-produto` |
| 2 | **Decisão sobre cold outreach** — vira canal recorrente? Precisa de ferramenta e/ou 2ª pessoa? Baseado no piloto de 60 dias. | `agente-produto` |
| 3 | **Avaliar teto de provisionamento** (seção 7) — se a demanda encostar no teto, elevar a prioridade de **onboarding self-service** e **controle de limites em tempo real** no roadmap (`CLAUDE.md` §15.3). | `agente-produto` → orquestrador |
| 4 | **Playbooks 10.1 e 10.2 refinados** com dados reais dos 90 dias. | `agente-produto` |
| 5 | **Dashboard de funil produto-led / MRR por canal** — especificar e priorizar (funde-se ao item "Dashboard de métricas para o admin" já no §15.3). | `agente-saas-billing` |

---

## 5. Mensagens e proposta de valor por segmento do ICP

Estrutura fixa por segmento: **dor → gancho → prova → CTA**. Usar nas landings,
no follow-up e nos scripts de parceria/indicação.

### 5.1 Clínica pequena (1–5 médicos)

- **Dor:** agenda em papel/planilha, remarcação por telefone, faltas sem
  controle, prontuário espalhado, nenhuma visão de faturamento.
- **Gancho:** "Tire a clínica da planilha em uma semana — agenda, prontuário e
  confirmação por WhatsApp no mesmo lugar."
- **Prova:** case de clínica semelhante que reduziu faltas com o lembrete
  automático de WhatsApp; nº de confirmações no mês.
- **CTA:** teste grátis 30 dias, sem cartão — `/experimentar?tipo=clinica`.

### 5.2 Profissional autônomo

- **Dor:** mistura agenda pessoal e de pacientes, controla tudo no WhatsApp e
  no caderno, perde histórico, sem prontuário digital.
- **Gancho:** "Seu consultório organizado sem contratar secretária: agenda
  online, prontuário e lembretes automáticos."
- **Prova:** depoimento de um autônomo da mesma especialidade; horas
  economizadas por semana.
- **CTA:** teste grátis 30 dias — `/experimentar?tipo=profissional`.

### 5.3 Clínica média (5–20 profissionais)

- **Dor:** gestão de equipe (quem atende quem), controle de quem vê o quê,
  relatórios consolidados, padronização de prontuário entre especialidades.
- **Gancho:** "Controle quem vê o quê e acompanhe a clínica inteira num painel
  — hierarquia de acesso nativa e relatórios centralizados."
- **Prova:** case de clínica multiprofissional; redução de tempo no fechamento
  de relatório mensal.
- **CTA:** agendar demonstração (`/contato`) + trial guiado.

### 5.4 Diferenciais transversais (usar como reforço, não como abertura)

Multi-tenant nativo, árvore de acesso hierárquica, prontuário + agenda + exames
no mesmo fluxo, WhatsApp integrado (`CLAUDE.md` §2.6). O módulo RPG educacional
é gancho de conteúdo/engajamento, não argumento de venda primário.

---

## 6. Funil: canal → lead (`adm/leads`) → trial → assinatura

### 6.1 Onde cada canal injeta o lead

| Canal | Ponto de entrada | `fonte` sugerida | Fila |
|-------|------------------|------------------|------|
| SEO / GEO / blog / diretórios | Form `/experimentar` e `/contato` | `organico`, `diretorio` | Normal |
| Indicação | WhatsApp/contato do Igor ou form com campo "quem indicou" | `indicacao` + `origem_detalhe` = nome de quem indicou | **Prioritária** |
| Parceria | Encaminhamento do parceiro ou landing co-branded | `parceria` + `origem_detalhe` = nome do parceiro | **Prioritária** |
| WhatsApp opt-in | — (nutre lead já existente) | mantém a `fonte` original | — |
| LinkedIn | DM/comentário → `/contato` ou `/experimentar` | `linkedin` | Normal |
| Cold outreach (piloto) | Resposta → `/contato` | `outbound` | Normal |
| Webinar/evento | Inscrição | `evento` + `origem_detalhe` = nome do evento | Agendada com a capacidade da semana |

### 6.2 SLA mínimo de follow-up que `adm/leads` precisa ter

Hoje `adm/leads` **só captura** — não há SLA nem cadência. Sem isso, os canais
quentes (indicação, parceria) desperdiçam lead. Requisitos de negócio (a
implementação é do orquestrador → `agente-clinico` / `agente-dev-infra`):

- **Campo `fonte` + `origem_detalhe` obrigatórios** na captura de todo lead.
- **Tempo de 1º contato:**
  - Lead de `/experimentar` (intenção máxima): **mesmo dia**.
  - Indicação e parceria (leads quentes): **até 4h úteis**.
  - Lead orgânico geral: **até 1 dia útil**.
- **Cadência de follow-up:** 5 toques — **D0, D+2, D+5, D+10, D+15** —
  alternando WhatsApp / e-mail / telefone. Sem resposta no D+15 → `descartado`.
- **Status do funil:** os atuais cobrem topo/meio
  (`pendente → contatado → interessado → descartado`). Falta fechar o fundo:
  status `trial_ativo` e `assinante`, ou vínculo do lead ao `tenant_id` gerado.
- **Painel deve mostrar:** leads sem 1º contato acima do SLA (alerta), e as
  taxas **lead→trial** e **trial→assinante** por `fonte`.

### 6.3 Critério de aceite de valor do funil

- 100% dos leads têm `fonte` preenchida.
- ≥ 90% dos leads recebem 1º contato dentro do SLA da sua categoria.
- É possível responder "quantos assinantes vieram de cada canal neste mês" em
  menos de 5 minutos.

---

## 7. Dimensionamento de capacidade (1 operador, provisionamento manual)

### 7.1 Custo de operador por trial que vira ativo

- Provisionar o tenant (`adm/saas`: tenant + plano + usuário owner + orientação
  de primeiro acesso): **~20–40 min**.
- Acompanhamento da 1ª semana (1–2 contatos de onboarding): **~30–60 min**.
- Total realista: **~1,5–2 h de operador por trial que ativa de verdade.**

### 7.2 Teto

- 1 operador que também faz produto/vendas/suporte deve orçar no máximo
  **~10–15 h/semana** para provisionamento + onboarding.
- Isso dá **~6–10 trials/semana** com qualidade → **teto de ~25–40
  trials/mês**. Alvo conservador para a fase atual: **20–30 trials/mês.**

### 7.3 Sinais de que passou do teto

- Tempo até provisionar um lead quente > 1 dia útil.
- Trial entrando sem nenhum contato humano na 1ª semana.
- Aumento de "não sei usar" / "não consegui começar".
- Queda na conversão **trial→assinante** enquanto o volume de trials sobe.

### 7.4 O que fazer ao passar disso

1. **Fila priorizada por `fonte`** — indicação e parceria na frente.
2. **Janelas fixas de provisionamento** (ex.: 2 blocos/dia) para não
   fragmentar o dia do operador.
3. **Onboarding assíncrono** — checklist + vídeo curto para reduzir o toque
   humano na 1ª semana.
4. **Elevar no roadmap** (`CLAUDE.md` §15.3): onboarding self-service e
   controle de limites de plano em tempo real.
5. **2ª pessoa** (assistente/estagiário) treinada só para provisionar e
   acompanhar onboarding.
6. **Segurar temporariamente os canais de topo mais volumosos** (pausar
   piloto de outreach, adiar webinar) — **nunca** segurar indicação.

---

## 8. Framework de KPIs e cadência de revisão

### 8.1 KPIs por canal

- Nº de leads/mês por `fonte`.
- Custo por lead **em horas de operador** (o custo financeiro é ~0).
- Taxa **lead→trial** por `fonte`.
- Taxa **trial→assinante** por `fonte`.
- Tempo médio de 1º contato por `fonte` vs SLA (seção 6.2).
- CAC **em horas de operador** por assinante.
- Parcerias: nº de parceiros ativos, leads por parceiro, MRR atribuído.
- Indicação: % de clientes que indicaram, indicações por cliente, MRR de
  indicados.

### 8.2 KPIs de funil (agregado)

- Leads/mês, trials/mês, novos assinantes/mês.
- Conversão lead→trial→assinante (composta).
- MRR novo/mês e **MRR por canal**.
- Churn de trial (não ativou) e churn de assinante em 90 dias.
- **Utilização de capacidade:** trials provisionados ÷ teto (seção 7.2).

### 8.3 Cadência de revisão

| Frequência | O que revisa |
|------------|--------------|
| Semanal | Fila de leads, cumprimento de SLA, trials da semana vs capacidade |
| Quinzenal | KPIs por canal, ajuste de foco (junto com a varredura de roadmap do `agente-produto` — `docs/arquitetura-agentes.md` §10) |
| Mensal | MRR por canal, decisão de dobrar/cortar canal, revisão de capacidade |
| Trimestral | Revisão de ICP/posicionamento e da própria escolha de canais-foco |

---

## 9. Dependências e riscos

### 9.1 Política Meta / WhatsApp

- Mensagem ativa fora da janela de 24h exige **template aprovado**. WhatsApp
  frio, sem opt-in, **viola a política** e arrisca o número.
- O número da conta pode ser o **mesmo** usado na confirmação de agendamento
  dos clientes — bloqueio por spam de prospecção seria um **incidente
  operacional para os clientes**.
- **Mitigação:** WhatsApp de vendas **só** para leads com opt-in explícito no
  form; se houver outbound por WhatsApp, usar **número separado** da operação
  transacional.
- O limite de **3 disparos/tenant sem assinatura** é do recurso de confirmação
  de agendamento — durante o trial o lead vê pouco do recurso de WhatsApp.
  Avaliar com o `agente-saas-billing` liberar cota maior durante o trial.

### 9.2 Política Capterra / G2 sobre review incentivado

- Permitido: oferecer o **mesmo** brinde/gift card a **todos** que avaliarem,
  **sem condicionar à nota** e **sem filtrar quem é convidado**.
- Proibido: pagar por review positiva, selecionar só clientes satisfeitos,
  redigir a review pelo cliente.
- Seguir os termos do vendor de cada diretório. Dono: `agente-seo-geo`.

### 9.3 LGPD em listas de outreach

- Base legal para B2B pode ser **legítimo interesse**, mas exige: dados de
  fontes públicas/profissionais, finalidade clara, **opt-out fácil e honrado**,
  registro do tratamento, **sem dados sensíveis**.
- E-mail para PJ/profissional é mais defensável que WhatsApp pessoal.
- **Preferir inbound.** Se fizer outbound: documentar a base legal, manter
  lista de descadastro, limitar o piloto (50–100 contatos) até validar.

### 9.4 Concentração em 1 pessoa

- Férias/doença do operador = funil para.
- **Mitigação:** playbooks e checklists (seção 10) para que um terceiro possa
  operar o essencial.

### 9.5 Integridade de MRR com desconto de indicação

- Se o desconto de indicação abater da mensalidade, o **MRR reportado** precisa
  distinguir **MRR bruto** de **MRR líquido**.
- **Crédito único** é mais fácil de contabilizar que **desconto recorrente**.
- Definir com o `agente-saas-billing` como registrar (cupom, crédito, ajuste de
  ciclo em `saas_subscription_cycles`) para não contaminar relatórios de
  MRR/churn. Detalhe na spec derivada 10.3.

### 9.6 Ausência de dashboard de funil produto-led

- Hoje não há visão trial→assinatura por origem. Sem o campo `fonte` no lead
  (9 / seção 6.2) e sem status pós-trial, **não dá para medir canal**.
- É **pré-requisito** para operar esta estratégia — vira entregável do roadmap
  (seção 4.1 item 1 e 4.4 item 5).

### 9.7 Dependência do provisionamento manual

- Risco central: qualquer canal que gere pico quebra a primeira impressão.
  Toda a seção 3 e 7 existe para gerenciar esse risco.

---

## 10. Próximos entregáveis derivados (referências futuras — não escrever agora)

- **`docs/produto/2026-09-03-playbook-parcerias-comunidades.md`** — como
  prospectar, abordar e ativar contadores de saúde, conselhos regionais,
  associações/sindicatos de clínicas e comunidades: scripts de abordagem,
  cadência de relacionamento, condições de co-marketing, regras de participação
  em comunidade sem virar spam.
- **`docs/produto/2026-09-03-playbook-cold-outreach-linkedin.md`** — list
  building do ICP conforme LGPD, sequências de e-mail, roteiro de social
  selling no LinkedIn (perfil do Igor + página), métricas e critério de
  parada/escala do piloto.
- **`docs/superpowers/specs/2026-09-03-programa-indicacao-design.md`** —
  mecânica de referral (crédito único vs desconto recorrente), registro no
  sistema, impacto em MRR bruto/líquido, antifraude, elegibilidade. Entrada
  para o `agente-saas-billing`.
- **`docs/superpowers/specs/2026-09-03-prova-social-cases-design.md`** —
  formato de case, processo de coleta de depoimento e consentimento, onde
  publicar (site, LinkedIn, diretórios), ligação com as reviews de Capterra/G2.
  Entrada para `agente-seo-geo` + `agente-frontend`.

---

## 11. Handoff para o orquestrador

### 11.1 O que muda no roadmap (`CLAUDE.md` §15)

- **Novo item, prioridade alta (pré-requisito de medição):** instrumentar
  `adm/leads` com `fonte`/`origem_detalhe`, status pós-trial e SLA/cadência de
  follow-up (seções 4.1 item 1 e 6.2).
- **Backlog → médio:** programa de indicação (mecânica no sistema + tratamento
  de MRR).
- **Avaliar com billing:** cota de WhatsApp ampliada durante o trial.
- **Fundir com "Dashboard de métricas para o admin" (§15.3):** dashboard de
  funil produto-led / MRR por canal.
- **Repriorização (sem item novo) para o `agente-seo-geo`:** páginas de decisão
  comercial + Capterra/G2 + primeiras reviews antes de expansão por
  especialidade (seção 4.3).

### 11.2 Domínios afetados

- `agente-seo-geo` — repriorização do ciclo, coleta de reviews, publicação de
  cases no site.
- `agente-saas-billing` — mecânica de indicação, cota de trial, MRR por canal,
  dashboard de funil.
- `agente-clinico` — campos e fluxo de `adm/Leads` (controller/model).
- `agente-dev-infra` — migração dos campos `fonte`/`origem_detalhe`/status em
  `leads_capturados` (método idempotente em `Dev.php`); nenhum deploy sem
  plano.
- `agente-frontend` — form `/experimentar` com campo "indicado por" e copy por
  segmento (seção 5); publicação dos cases.

### 11.3 Caminho da spec e critérios de aceite de valor

- **Spec:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md` (este
  documento).
- **Critérios de aceite de valor (90 dias):**
  1. Todo lead em `adm/leads` tem `fonte` preenchida; ≥ 90% com 1º contato
     dentro do SLA.
  2. "Quantos assinantes vieram de cada canal neste mês" é respondível em < 5
     min.
  3. Pelo menos 2 canais-foco com conversão lead→assinante medida e positiva.
  4. Programa de indicação com ≥ 1 assinante novo atribuído em 60 dias.
  5. Nenhuma violação registrada de política Meta / Capterra / LGPD.
  6. Conversão trial→assinante **não cai** enquanto o volume de trials cresce
     até o teto de ~20–30/mês.
