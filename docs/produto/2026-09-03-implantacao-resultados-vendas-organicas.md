# Implantação e Resultados Projetados — Vendas Orgânicas UTecnologia Saúde

- **Data:** 2026-09-03
- **Autor:** consolidação (sessão principal)
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** plano de implantação em ondas + projeção de resultados em cenários
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.
- **Base:** `docs/produto/2026-09-03-roadmap-vendas-organicas-consolidado.md` e os 5 documentos de origem citados nele.

> As projeções da Parte B são **estimativas com premissas explícitas**, não previsões. Servem para calibrar expectativa e disparar decisões (escalar / cortar / contratar). Todos os números devem ser substituídos pelos reais assim que a instrumentação de `adm/leads` (P0) começar a medir.

---

# Parte A — Plano de implantação

## A.1 Estrutura em ondas

Quatro ondas sobrepostas. Cada onda tem uma **pré-condição** (não começa antes), **entregas**, **dono**, um critério de **"pronto quando"** e uma **nota de deploy**. Entre as ondas há um **gate de decisão**.

```
Semana:        1   2   3   4   5   6   7   8   9  10  11  12  ...
Onda 0 ────────█████████
  destravar    P0 + P1
Onda 1 ────────────█████████████████████████████
  motor            indicação fase 1 · parcerias · SEO repriorizado
Onda 2 ────────────────────█████████████████████
  conversão               prova social Fase 0 → Fase 1 · seção na landing
Onda 3 ────────────────────────────█████████████
  piloto                          outbound · LinkedIn · 1 webinar
GATE 90d ──────────────────────────────────────────────► escalar / cortar / contratar
```

---

## A.2 Onda 0 — Destravar (semanas 1–3)

**Pré-condição:** nenhuma. Começa já.

**Objetivo:** tornar todo canal medível e remover os bloqueios técnicos das ondas seguintes.

| # | Entrega | Dono | Pronto quando | Deploy |
|---|---------|------|---------------|--------|
| 0.1 | **Migração `leads_capturados`** — colunas `fonte`, `origem_detalhe`, status pós-trial (`trial_ativo`/`assinante`) ou vínculo a `tenant_id`. Idempotente, em `adm/Dev.php`, protegida por `nivel == 1`. | `agente-dev-infra` | Migração roda 2× sem erro; colunas existem; `php -l` limpo. | FTP do `Dev.php`; executar a rota logado como nível 1; conferir schema. |
| 0.2 | **`adm/Leads` — captura e fluxo** — `fonte`/`origem_detalhe` obrigatórios na criação; fila prioritária para `indicacao` e `parceria`; SLA por categoria (mesmo dia p/ `/experimentar`; 4h úteis p/ indicação/parceria; 1 dia útil p/ orgânico); cadência de 5 toques D0/D+2/D+5/D+10/D+15; alerta de lead fora do SLA; painel com taxa lead→trial e trial→assinante por `fonte`. | `agente-clinico` | Criar lead sem `fonte` é bloqueado; painel mostra as taxas por `fonte`; alerta aparece ao estourar SLA (teste com data forçada). | Pipeline superpowers completo (brainstorming → plan → TDD → review → verification); `agente-dev-infra` faz o deploy e healthcheck. |
| 0.3 | **Campo "Indicado por / código" em `/experimentar`** — texto opcional, pré-preenchível por `?ref=`; grava `origem_detalhe`; copy curta do benefício de entrada. | `agente-frontend` (+ `agente-clinico` no vínculo) | `/experimentar?ref=TESTE` chega em `adm/leads` com `fonte=indicacao`, `origem_detalhe=TESTE`. | Junto com 0.2. |
| 0.4 | **Função de agregação anonimizada de métricas clínicas** — taxa de falta antes/depois do lembrete, nº de confirmações/lembretes por WhatsApp, nº de agendamentos, tenants em produção, tempo até registro de prontuário. Saída sem PII, sem identificar tenant, com janela + tamanho da base. Piso ≥ 5 tenants (o agente confirma). | `agente-clinico` | Retorna os números agregados de um período com o rótulo de base; recusa publicar abaixo do piso. | Provável query sobre schema atual — sem migração. Deploy se virar endpoint/rota interna. |
| 0.5 | **Subdomínio de envio de e-mail** separado do transacional + SPF/DKIM/DMARC próprios. | `agente-dev-infra` | Teste de envio autenticado passa (mail-tester ou equivalente ≥ 9/10); domínio transacional intacto. | DNS + config no cPanel/host. |
| 0.6 | **Kickoff operacional (sem código)** — planilha de controle de indicações; planilha de 20 parceiros com os 5 estágios; planilha-CRM de outbound com `fonte_url`/`data_captura`; selo "em produção desde…" e screenshots anotados prontos. | `agente-produto` | As 3 planilhas criadas com colunas do roadmap; selo + 3–4 screenshots aprovados. | — |

**Gate 0 → 1:** 0.1, 0.2, 0.3 em produção e testados. 0.4 e 0.5 podem terminar durante a Onda 1 sem bloquear o motor (só bloqueiam, respectivamente, o número da Fase 0 de prova social e o piloto de outbound).

---

## A.3 Onda 1 — Motor (semanas 2–8)

**Pré-condição:** Gate 0 → 1.

**Objetivo:** ligar os dois canais de maior alavancagem por hora de operador.

| # | Entrega | Dono | Pronto quando |
|---|---------|------|---------------|
| 1.1 | **Indicação fase 1** — pedir a **todos** os clientes ativos e trials engajados; registrar na planilha; aplicar 50% na 1ª mensalidade do indicado no provisionamento; conceder R$ 79 de crédito ao indicador na 1ª fatura paga + carência de 60 dias. | `agente-produto` | Todos os clientes ativos receberam o pedido ao menos 1×; ≥ 1 indicação registrada. |
| 1.2 | **Parcerias — prospecção** — qualificar os 20 (score 0–12); abordar Tier 1 (contadores de saúde + associações/sindicatos de clínicas). | `agente-produto` | 20 na planilha com estágio e datas; 6–10 em (a); 2–3 em (b) até a semana 4. |
| 1.3 | **Parcerias — primeiros acordos** — fechar 2–3 em estágio (c) acordo de co-marketing ou (d) 1º lead; enviar o "kit do parceiro" em 48h após (c). | `agente-produto` | 2–3 parcerias em (c)/(d) até a semana 8. |
| 1.4 | **SEO/GEO — repriorização** — realinhar a fila do ciclo semanal (páginas de decisão comercial → Capterra/G2/GetApp + 3 primeiras reviews pela política neutra → interlink comercial → especialidade depois). Atualizar o ledger. | `agente-seo-geo` | Ledger reflete a nova ordem; 1ª página de decisão comercial publicada; cadastro de diretório iniciado. |
| 1.5 | **Mensagens por ICP** aplicadas em `/experimentar`, follow-up e scripts. | `agente-produto` + `agente-frontend` | Landing e scripts usam dor→gancho→prova→CTA por segmento. |
| 1.6 | **Definição de billing** — desconto da 1ª mensalidade do indicado é redução de fatura ou CAC? (consistente com o tratamento de trial). Consolidar o processo manual de crédito em `adm/saas`. | `agente-saas-billing` | Decisão documentada; operador sabe exatamente como lançar o crédito. |

**Gate 1 → continuar:** revisão de KPIs por canal na semana 8 (`agente-produto`). Indicação com ≥ 1 assinante ou pipeline claro; parcerias com ≥ 2 em (c)/(d).

---

## A.4 Onda 2 — Conversão (semanas 4–10)

**Pré-condição:** 0.4 entregue (para o número); Onda 1 gerando leads (para ter o que converter melhor).

**Objetivo:** elevar a taxa de avanço de todos os canais com prova social.

| # | Entrega | Dono | Pronto quando |
|---|---------|------|---------------|
| 2.1 | **Prova social Fase 0** — selo + screenshots + números agregados (0.4) publicados. Afirmação qualitativa enquanto o número não sai. | `agente-produto` | No ar em ≤ 2 semanas após 0.4; todo número com período + base + fonte. |
| 2.2 | **Seção de prova social na `index-front.php`** — números + faixa de depoimentos + selo; degrada bem só com Fase 0. | `agente-frontend` | Seção renderiza com e sem depoimentos; bloco de números vem da função (não hard-coded). |
| 2.3 | **Prova social Fase 1** — 1–2 cases nomeados leves (depoimento + 1 métrica), 2 segmentos do ICP; consentimento escrito item a item. | `agente-produto` | 1º case nomeado publicado em ≤ 60 dias. |
| 2.4 | **Reviews em diretório** — coleta pela política neutra (todos os elegíveis, mesmo texto, mesmo agradecimento). | `agente-seo-geo` | ≥ 1 review publicada; registro de convites arquivado. |

**Gate 2 → continuar:** seção de prova no ar; efeito na conversão da landing observável no tráfego de IA + FB CAPI (mesmo que ainda ruído).

---

## A.5 Onda 3 — Piloto controlado (semanas 6–12)

**Pré-condição:** 0.5 entregue; capacidade de provisionamento com folga confirmada na semana; **nunca** dois canais de pico ativos ao mesmo tempo.

**Objetivo:** validar processo e mensagem do outbound sem arriscar reputação nem capacidade. **Não é para escalar.**

| # | Entrega | Dono | Pronto quando |
|---|---------|------|---------------|
| 3.1 | **LIA + ROPA escritas** antes do 1º envio de e-mail frio. | `agente-produto` | Documentos arquivados em `docs/` ou no drive do negócio. |
| 3.2 | **Cold outreach** — 50–100 contatos, 2–3 lotes de ~25–40 (uma especialidade + praça por lote), sequência de 5 toques em ~18 dias, ≤ 20–30 envios/dia com aquecimento. | `agente-produto` | Lotes rodados; catálogo de objeções preenchido; bounce < 3%, spam < 0,1%. |
| 3.3 | **LinkedIn** — perfil do Igor otimizado, Company Page criada, 2–3 posts/semana a partir de cases e blog. | `agente-produto` | Cadência mantida por ≥ 6 semanas; indicadores de ativo em tendência. |
| 3.4 | **1 webinar com parceiro** — teto de inscrição = capacidade livre ÷ taxa esperada; convites de trial escalonados; adiar se a capacidade cair. | `agente-produto` | Evento realizado; trials absorvidos sem degradar onboarding. |

**Gate 3 (90 dias) — decisão sobre o outbound:** qualitativa + custo de operador (§9.4 da spec 2). Saídas: canal recorrente de baixa intensidade / pausar até 2ª pessoa / encerrar cold e-mail / levar aprendizado de produto ao roadmap.

---

## A.6 Checklist de deploy (por entrega técnica — `agente-dev-infra`)

1. `php -l` em todo arquivo alterado.
2. Migração roda 2× seguidas sem efeito colateral (idempotência).
3. Backup/registro do schema antes de aplicar em produção.
4. FTP só dos arquivos alterados (skill `ftp`).
5. Rodar a rota de migração logado como nível 1.
6. Healthcheck: página-alvo carrega, fluxo principal não quebrou, `/experimentar` cria lead com `fonte`.
7. Registrar no CLAUDE.md (§13 rotas de Dev, §15 roadmap) o que mudou.

---

## A.7 Gates de decisão — resumo

| Gate | Quando | Passa se | Não passa → |
|------|--------|----------|-------------|
| 0 → 1 | fim da semana 3 | 0.1–0.3 em produção e testados | Segurar Onda 1; priorizar a instrumentação. |
| 1 → continuar | semana 8 | indicação ≥ 1 assinante ou pipeline; ≥ 2 parcerias em (c)/(d) | Revisar abordagem de indicação/parceria antes de investir mais horas. |
| 2 → continuar | semana 10 | seção de prova no ar; 1º case nomeado a caminho | Continuar com Fase 0 agregada; não travar. |
| 3 (90 dias) | semana 12–13 | ver A.5 | Encerrar/pausar outbound; realocar horas ao motor. |
| **Capacidade** | contínuo | trials/mês < ~25 | Fila por `fonte`, onboarding assíncrono, elevar self-service, avaliar 2ª pessoa; segurar canais volumosos (nunca a indicação). |

---

# Parte B — Resultados projetados

## B.1 Metodologia

Projeção por **funil de canal**: `leads → trials → assinantes → MRR novo`. Taxas assumidas a partir dos alvos de KPI das specs e de referências de SaaS B2B SMB no Brasil. **Três cenários** (conservador / base / otimista) que diferem sobretudo na **taxa de resposta ao pedido de indicação**, na **velocidade de fechamento de parcerias** e na **conversão trial→assinante**.

### Premissas de partida (ajustar ao real assim que P0 medir)

| Premissa | Valor assumido | Como confirmar |
|----------|----------------|----------------|
| Clientes pagantes hoje | **~5** (base pequena, majoritariamente trial/interessados) | `saas_subscriptions` ativas |
| Trials/mês hoje | **~5–10** | `adm/leads` após P0 |
| Conversão trial→assinante hoje (geral) | **~20%** | `adm/leads` após P0 |
| Conversão trial→assinante — leads quentes (indicação/parceria) | **40–50%** (alvo das specs) | medição por `fonte` |
| Ticket médio do novo assinante | **~R$ 150/mês** (mix Solo/Clínica, poucos Pro) | `saas_subscriptions` |
| Teto de provisionamento | **20–30 trials/mês** | operação |
| Uplift de conversão pela prova social | **+10–20% relativo** sobre trial→assinante dos outros canais | comparar antes/depois |
| Media paga | **R$ 0** | — |

### O que a projeção NÃO tenta prever

- Volume orgânico de SEO/GEO já existente (não há baseline neste doc — a repriorização melhora **conversão** do que já entra e os diretórios começam a pingar no mês 2–3).
- Resultado isolado da prova social (modelada como uplift, não como linha própria).
- Efeito de churn de assinante nos primeiros 90 dias (assume-se desprezível na janela; entra nas projeções de 6 e 12 meses como "líquido").

---

## B.2 Projeção — 90 dias (fim da Onda 3)

**Novos assinantes na janela, por canal:**

| Canal | Conservador | Base | Otimista | Observação |
|-------|:----------:|:----:|:--------:|------------|
| Indicação (fase 1) | 0–1 | 1–2 | 2–3 | 2 / 4 / 7 indicações registradas → trials → assinantes. Alvo da spec: ≥ 1 em 60 dias. |
| Parcerias | 0 | 0–1 | 1–2 | Assinante em geral cai **fora** dos 90 dias. Entrega real = pipeline: 3 / 5 / 6 em (c). |
| SEO/GEO (reforço comercial + diretórios) | 0–1 | 1–2 | 3 | Incremento sobre o baseline atual; diretórios maduram no mês 2–3. |
| Piloto outbound | 0 | 0–1 | 1 | Amostra pequena; valida processo, não conversão. |
| Webinar (1 evento) | 0–1 | 1–2 | 2–3 | Limitado pela capacidade de absorção da semana. |
| LinkedIn | 0 | 0 | 0–1 | Ativo de retorno lento (6+ meses). |
| **Total novos assinantes (90d)** | **1–3** | **4–7** | **9–14** | Com uplift de prova social já embutido no cenário base/otimista. |
| **MRR novo adicionado (R$/mês)** | **150–450** | **600–1.100** | **1.400–2.400** | ticket médio ~R$ 150. |

**Pipeline ao fim dos 90 dias (não vira receita ainda, mas é o motor de 6 meses):**

| Item | Conservador | Base | Otimista |
|------|:----------:|:----:|:--------:|
| Parcerias em estágio (c) | 3 | 5–6 | 6+ |
| Parcerias em estágio (d) — 1º lead | 1 | 2 | 3 |
| Clientes que já indicaram | 1–2 | 2–3 | 4+ |
| Cases publicados (nomeados) | 0–1 | 1–2 | 2–3 |
| Reviews em diretório | 0–1 | 2–3 | 3+ por diretório |

---

## B.3 Projeção — 6 meses

O motor **compõe**: a base de clientes cresce → mais indicadores; parcerias em (d) começam a converter; diretórios amadurecem; cases nutrem LinkedIn e outbound.

| | Conservador | Base | Otimista |
|--|:----------:|:----:|:--------:|
| Novos assinantes acumulados (líquido) | 4–8 | 12–20 | 25–40 |
| MRR novo acumulado (R$/mês) | 600–1.400 | 2.000–3.500 | 4.500–7.500 |
| Trials/mês ao fim do período | 6–10 | 12–20 | 22–30 (encostando no teto) |
| Canais com conversão lead→assinante positiva | 1–2 | 2–3 | 3–4 |

**Leitura:** no cenário **otimista**, o volume de trials encosta no teto de capacidade — o gargalo deixa de ser demanda e passa a ser **provisionamento manual**. Isso **antecipa** a decisão de onboarding self-service / 2ª pessoa (item 23 do roadmap).

---

## B.4 Projeção — 12 meses

| | Conservador | Base | Otimista |
|--|:----------:|:----:|:--------:|
| Novos assinantes acumulados (líquido) | 10–18 | 28–45 | 60–90 |
| MRR novo acumulado (R$/mês) | 1.800–3.200 | 5.000–8.500 | 11.000–18.000 |
| Restrição dominante | demanda | equilíbrio | **capacidade de operador** |

**No cenário otimista de 12 meses**, sustentar o ritmo exige onboarding self-service **ou** uma 2ª pessoa dedicada a provisionamento — sem isso, o teto de ~25–30 trials/mês limita o resultado real ao patamar do cenário **base**, independentemente da demanda gerada.

---

## B.5 Sensibilidade — o que mais move o resultado

Ordenado por impacto no MRR de 12 meses:

1. **Conversão trial→assinante dos leads quentes** (indicação + parceria). Passar de 40% para 55% quase dobra a contribuição desses canais. Alavanca: qualidade do onboarding manual + prova social + rapidez no SLA de 4h.
2. **Nº de parcerias que chegam a (d) e convertem.** Cada parceria ativa que entrega ~1–2 leads/mês é uma "fila" permanente. 2 parcerias produtivas > 20 paradas em (b).
3. **% de clientes que indicam.** De 20% para 40% dobra o combustível da indicação — e o custo marginal é só o pedido bem feito no momento de valor.
4. **Teto de capacidade.** Elevar de 25 para 50 trials/mês (self-service ou 2ª pessoa) destrava todo o cenário otimista; sem isso ele é inatingível.
5. **Maturação dos diretórios (Capterra/G2/GetApp).** Tráfego de intenção de compra; efeito só aparece no mês 3+ e cresce com o nº de reviews.
6. **Ticket médio.** Deslocar o mix para o plano Clínica (R$ 199) em vez de Solo (R$ 79) — via parcerias com clínicas médias e mensagens de segmento (c) — move o MRR sem mexer no volume.

---

## B.6 Custo vs retorno

**Custo financeiro direto:** ~R$ 0 de mídia. Exposição de crédito da indicação fase 1 ≤ **~R$ 900/mês** no pico. Listagens de diretório no plano gratuito. Subdomínio de e-mail: custo desprezível.

**Custo real = horas de operador (1 pessoa):**

| Atividade | Horas/semana (regime) |
|-----------|----------------------|
| Provisionamento + onboarding de trials | 10–15 |
| Prospecção e relacionamento de parcerias | 3–5 |
| Pedido e gestão de indicações | ~1 |
| Entrevistas e produção de cases | ~2 (concentrado) |
| Piloto de outbound + LinkedIn | ~3 (time-box) |
| Revisão de KPIs e planilhas | ~1 |
| **Total** | **~20–27 h/semana** |

→ Aquisição orgânica + operação consome **quase um turno integral** do operador. É o argumento central para, ao passar do cenário base, contratar apoio de provisionamento antes de qualquer outra coisa.

**Payback do programa de indicação:** com ticket ~R$ 150 e custo por conversão (R$ 79 crédito + ~R$ 75 desconto de 1ª fatura ≈ R$ 154), o payback é de **~1 mês** de mensalidade do indicado. A partir de ~5 assinantes vindos de indicação, o MRR gerado cobre a exposição mensal do programa com folga.

---

## B.7 O que derruba a projeção

| Fator | Efeito nos números | Sinal de alerta |
|-------|--------------------|-----------------|
| P0 atrasa | Sem atribuição, não dá para saber o que converte → decisões no escuro, canais mantidos por "sensação" | Onda 1 rodando sem painel por `fonte` |
| Base de clientes não indica | Motor #1 sem combustível → cai para o cenário conservador | < 15% dos clientes ativos indicam em 60 dias |
| Parcerias não saem de (b) | 2–4 meses viram 6+; contribuição de parceria some do ano | 90 dias com 0 parcerias em (c) |
| Provisionamento estoura | Onboarding ruim → trial→assinante despenca justamente quando o volume sobe | Trial sem contato humano na 1ª semana; alta de "não sei usar" |
| Incidente de reputação (e-mail/WhatsApp) | Entrega transacional dos clientes afetada → problema maior que qualquer lead | Bounce ≥ 3%, spam ≥ 0,1%, reclamação de número |
| Churn precoce | MRR "novo" não é líquido; 6–12 meses ficam abaixo do projetado | Cancelamento nos primeiros 60 dias > 10% |
| Operador sobrecarregado | Tudo desacelera junto; nenhum canal amadurece | > 27 h/semana em GTM+ops de forma sustentada |

---

## B.8 Leading indicators (acompanhamento semanal)

Antes de o MRR se mover, estes indicadores dizem se a trajetória é a do cenário base ou a do conservador:

- **Semana 2–4:** painel por `fonte` funcionando; ≥ 1 indicação registrada; ≥ 10 parceiros em (a).
- **Semana 4–6:** 1º lead de indicação vira trial; 2–3 parceiros em (b); Fase 0 de prova social no ar.
- **Semana 6–8:** ≥ 2 parcerias em (c); 1º case nomeado a caminho; catálogo de objeções do outbound começando.
- **Semana 8–10:** 1º assinante de indicação; SLA de 1º contato cumprido em ≥ 90% dos leads quentes; 1ª review de diretório.
- **Semana 10–12:** parceria em (d) com 1º lead encaminhado; conversão trial→assinante geral **não caiu**; decisão de outbound preparada.

Se ao fim da semana 8 **nenhum** assinante de indicação e **nenhuma** parceria em (c), a trajetória é a do cenário **conservador** — revisar abordagem antes de abrir a Onda 3.

---

## B.9 Quando recalibrar a projeção

- **Semana 4:** substituir as premissas de B.1 pelos números reais que o P0 já mede (trials/mês, conversão atual, ticket).
- **Semana 8:** primeira revisão de cenário com dados de indicação e pipeline de parceria.
- **90 dias:** recalcular 6 e 12 meses com as taxas observadas por `fonte`; decidir sobre capacidade (self-service / 2ª pessoa).
- **Trimestral daí em diante:** junto da varredura de roadmap do `agente-produto`.

---

## B.10 Resumo executivo

- **Implantação:** 4 ondas sobrepostas em ~12 semanas. Onda 0 (destravar medição) é pré-requisito de tudo e começa já. Motor (indicação + parcerias) na Onda 1; prova social na Onda 2; piloto de outbound só na Onda 3 e sem escalar. Gate de decisão aos 90 dias.
- **Resultado provável (cenário base):** **4–7 novos assinantes em 90 dias** (+R$ 600–1.100/mês de MRR), **12–20 em 6 meses** (+R$ 2.000–3.500/mês), **28–45 em 12 meses** (+R$ 5.000–8.500/mês) — com pipeline de parceria e base de indicadores crescendo por baixo.
- **Custo:** ~R$ 0 de mídia; exposição de crédito ≤ ~R$ 900/mês; **~20–27 h/semana de operador** — o verdadeiro limite.
- **O que decide entre base e otimista:** conversão dos leads quentes, nº de parcerias produtivas, % de clientes que indicam e, acima de tudo, **elevar o teto de provisionamento** (self-service ou 2ª pessoa) antes que ele vire a trava.
