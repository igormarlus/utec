# Programa de Indicação (Member-Get-Member) — Spec de Negócio

- **Data:** 2026-09-03
- **Autor:** agente-produto
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** spec de negócio (o "o quê" e o "porquê" — antecede o brainstorming técnico do `agente-saas-billing`)
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.
- **Deriva de:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md` — canal 5 (§2.1), recomendação de foco (§3.2), janela 30 dias item 2 (§4.1), integridade de MRR (§9.5). Este é o entregável derivado 10.3.
- **Entrada para:** `agente-saas-billing` (mecânica no sistema, tratamento de MRR, antifraude). Toca também `agente-clinico` (`adm/leads`), `agente-frontend` (`/experimentar`, painel do tenant) e `agente-dev-infra` (migração).

---

## 0. Processo de brainstorming (registro)

### 0.1 Contexto explorado

- `CLAUDE.md` §2 (planos Solo R$ 79 / Clínica R$ 199 / Pro R$ 399 / Enterprise negociado; trial 30 dias; provisionamento manual; 1 operador), §4 (tabelas SaaS: `saas_tenants`, `saas_subscriptions`, `saas_subscription_cycles`, `saas_billing_events`; `leads_capturados`).
- Spec-mãe: indicação é o canal de **maior alavancagem por hora de operador**, mas depende de **base instalada**, que hoje é **pequena**. MRR precisa distinguir bruto/líquido se houver desconto recorrente.
- Pré-requisito da spec-mãe (janela 30 dias, item 1): `adm/leads` ganha `fonte` + `origem_detalhe`. A **fase 1 deste programa não depende** desse item (opera em anotação manual); a **fase 2 depende**.
- Integração de pagamento: Mercado Pago Preapproval via `Mercadopago_saas.php`. Ciclos em `saas_subscription_cycles`; eventos financeiros e de webhook em `saas_billing_events`. Bloqueio automático por inadimplência e baixa automática de ciclo pago ainda **pendentes** (`CLAUDE.md` §14) — a mecânica de recompensa **não pode depender** de automação que ainda não existe.

### 0.2 Premissa central desta spec

**A base de pagantes é pequena hoje.** O programa é motor de **médio prazo**: não gera volume no mês 1. A **fase 1** é pedir ativamente aos poucos clientes ativos e trials em andamento que existem, **operada 100% manualmente** (anotação no lead + concessão manual de crédito). **Automação é fase 2**, condicionada a volume que justifique o esforço de engenharia (gatilho em §10.0).

### 0.3 Abordagens de incentivo consideradas (trade-offs)

| Modelo | Como funciona | Prós | Contras | Efeito no MRR |
|--------|---------------|------|---------|---------------|
| **A. Crédito único na fatura do indicador** (recomendado) | Ao converter a indicação, o indicador recebe 1 crédito pontual abatido do próximo ciclo | Simples de contabilizar; é despesa de aquisição, não redução de assinatura; não contamina MRR/churn; fácil de operar manual | Menos "recorrente" como gancho de retenção | **Não altera MRR.** Registrado como custo de aquisição |
| **B. Desconto recorrente por N meses** (indicador) | Mensalidade do indicador cai X% por N ciclos | Reforça retenção do indicador | Contamina MRR (bruto ≠ líquido); precisa de relatório separado; difícil de operar manual; empilha com múltiplas indicações | Exige **separar MRR bruto vs líquido** em todos os relatórios (spec-mãe §9.5) |
| **C. Trial estendido para o indicado** (30 → 45 d) | Indicado ganha mais tempo grátis | Atrai o indicado | **Atrasa a 1ª fatura paga** → atrasa o gatilho da recompensa → arrisca o alvo de "≥1 assinante em 60 dias"; nenhum ganho de receita | Adia reconhecimento de receita |
| **D. Desconto no 1º mês do indicado** | 1ª mensalidade do indicado com desconto fixo | Não atrasa a fatura paga; gatilho dispara no prazo; barato | Custo some no fluxo se o indicado não converter (não há custo antes da 1ª fatura) | 1ª fatura entra menor; MRR do 2º ciclo em diante é cheio |
| **E. Mão dupla (os dois ganham)** = A + D | Indicador: crédito único. Indicado: desconto no 1º mês | O indicado tem motivo para citar quem indicou; tira o constrangimento do indicador ("consegui um desconto pra você" > "eu ganho comissão"); alinhado a member-get-member de SaaS | Duas pontas de custo — mitigado por teto e por só pagar na 1ª fatura paga | Indicador: nenhum. Indicado: 1ª fatura menor |

**Recomendação: E (mão dupla) = crédito único para o indicador + desconto no 1º mês para o indicado.** Evita contaminar MRR, não atrasa a conversão paga e dá narrativa limpa para o indicador. Se o time preferir B (desconto recorrente), a spec **exige** que todos os relatórios de MRR passem a reportar bruto e líquido separadamente e que churn seja calculado sobre o bruto.

---

## 1. Objetivo

### 1.1 O que é

Um mecanismo para que **clientes pagantes** do UTecnologia Saúde indiquem **colegas** (outro médico, outra clínica, um profissional autônomo conhecido) e sejam recompensados quando essa indicação vira **assinante pagante**. O indicado também recebe um benefício de entrada.

O programa é **member-get-member puro**: relação de confiança entre pares do mesmo mercado, com leads que chegam quase fechados e exigem pouco provisionamento — encaixe direto no gargalo de 1 operador (spec-mãe §3.3).

### 1.2 O que NÃO é

- **Não** é programa de afiliados nem de parceiros/multiplicadores (contadores de saúde, conselhos, associações, distribuidoras). Isso é outro playbook: `docs/produto/2026-09-03-playbook-parcerias-comunidades.md` (spec-mãe canal 4 / entregável 10.1). Diferença essencial: parceiro é um **intermediário profissional com fluxo recorrente e possível contrato/comissão**; indicador aqui é um **cliente indicando pontualmente um conhecido**, sem contrato, sem meta, com teto baixo.
- **Não** é reembolso, cashback em dinheiro ou saque. A recompensa é **crédito na própria fatura** do indicador.
- **Não** cria onboarding self-service nem depende dele.
- **Não** define implementação técnica — a spec enquadra opções e restrições; o desenho da mecânica no sistema é do `agente-saas-billing`.

### 1.3 Alvo numérico (critério de aceite herdado da spec-mãe)

**≥ 1 assinante novo atribuído a indicação em até 60 dias** a partir do início da fase 1. Demais KPIs na §11; demais critérios na §12.

---

## 2. Modelo de incentivo (decisão)

### 2.1 Estrutura recomendada — mão dupla

| Ponta | Recompensa fase 1 (manual) | Recompensa fase 2 (automatizada) |
|-------|----------------------------|----------------------------------|
| **Indicador** (cliente que indica) | **Crédito único de R$ 79** abatido do próximo ciclo, por indicação convertida | **Crédito único = 1 mensalidade do plano assinado pelo indicado**, limitado a um **teto de R$ 199** (= plano Clínica) |
| **Indicado** (novo cliente) | **50% de desconto na 1ª mensalidade**, limitado a R$ 100 de desconto | Igual |

**Por que R$ 79 fixo na fase 1:** é 1 mês do plano Solo, simples de conceder manualmente sem depender de saber qual plano o indicado fechou no momento da concessão. Quando a fase 2 tiver rastreio automático do plano do indicado, migrar para "1 mês do plano do indicado, teto R$ 199" — assim indicações de clínicas maiores valem mais, sem criar exposição desproporcional com Pro/Enterprise.

**Por que teto no desconto do indicado:** um indicado que fecha Pro (R$ 399) teria R$ 199 de desconto no 1º mês — desproporcional. Teto de R$ 100 mantém o custo previsível.

**Interação com o gatilho:** a recompensa do indicador é calculada sobre o **valor cheio do plano do indicado**, não sobre a 1ª fatura (que vem com o desconto de entrada). Isso precisa ficar explícito para o `agente-saas-billing`.

### 2.2 O que foi descartado e por quê

- **Desconto recorrente para o indicador (modelo B):** contamina MRR e churn, é difícil de operar manualmente e empilha de forma imprevisível quando um cliente indica várias pessoas. Só adotar se o time aceitar reportar MRR bruto vs líquido em definitivo.
- **Trial estendido para o indicado (modelo C):** adia a 1ª fatura paga e, com ela, o gatilho da recompensa — coloca em risco o alvo de "≥ 1 assinante em 60 dias". Sem ganho de receita.
- **Recompensa em dinheiro/saque:** custo operacional (transferência, nota, imposto) e risco de o programa parecer esquema de comissão. Crédito na fatura resolve.

---

## 3. Elegibilidade

### 3.1 Indicador

Pode indicar o cliente que, **na data da conversão da indicação**:

- Tem **assinatura ativa** (`saas_subscriptions.status = active` ou equivalente).
- **Não está em trial** (trial não é cliente pagante).
- **Não está inadimplente** (`past_due` / `pending` / `expired` / `canceled` desqualificam).
- **Não é o próprio operador / conta interna / conta de teste.**
- Está dentro do escopo do produto (tenant real de clínica/consultório, não conta administrativa).

Se o indicador **perde a elegibilidade** entre a indicação e a conversão (cancelou, ficou inadimplente), a recompensa **não é concedida** enquanto a situação não se regularizar; se não regularizar em 60 dias após a conversão do indicado, a recompensa **caduca**.

### 3.2 Teto de indicações por cliente

- **Fase 1:** até **5 indicações convertidas** por indicador em janela de **12 meses corridos**. Acima disso, o operador avalia caso a caso (pode ser sinal de multiplicador — migrar a relação para o playbook de parcerias).
- Não há teto de indicações *registradas* (o cliente pode indicar quantos quiser); o teto é sobre **quantas geram recompensa**.

### 3.3 Indicado

Só gera recompensa o indicado que:

- Origina um **tenant novo** (`saas_tenants` sem registro anterior; responsável sem `tenant_id` prévio).
- **Não é a mesma entidade do indicador** (ver antifraude §6): CNPJ, CPF do responsável, e-mail, telefone e — quando visível — meio de pagamento diferentes.
- **Não consta em `leads_capturados` como lead ativo de outro canal** dentro dos **90 dias** anteriores à data da indicação. Se já existe lead ativo de outra `fonte` (orgânico, parceria, evento, outbound), **o canal original prevalece** (first-touch — §4.3) e a indicação **não é recompensada** (registra-se a co-influência para análise, sem custo).
  - "Lead ativo" = status diferente de `descartado` / `perdido` e com atividade nos últimos 90 dias.
  - Lead antigo já `descartado` **não bloqueia** uma indicação nova.
- Converte em **assinante pagante** (gatilho na §5).

---

## 4. Janela e regra de atribuição

### 4.1 Como o indicado informa quem indicou

- **Fase 1 (manual):** dois caminhos, ambos válidos:
  1. O **indicador avisa o operador** (WhatsApp/e-mail) que vai indicar Fulano, e/ou o próprio Fulano procura o operador dizendo "o Dr. Ciclano me indicou". O operador registra no lead.
  2. O indicado se cadastra em `/experimentar` e escreve no campo livre / na conversa de follow-up quem indicou.
- **Fase 2 (automatizada):** campo **"Indicado por"** em `/experimentar` (nome + e-mail/telefone do indicador) **ou** um **código do indicador** que o cliente compartilha. O código é a via preferida por ser inequívoca.

### 4.2 Prazo entre indicação e cadastro

- O indicado precisa **iniciar o trial (cadastro em `/experimentar`) em até 60 dias** após a indicação ser registrada. Passou de 60 dias, a indicação **expira** e precisa ser refeita.
- Da conversão do trial em assinante: sem prazo rígido, mas a recompensa segue a regra de gatilho (§5) e o alvo do programa mira 60 dias ponta a ponta.

### 4.3 First-touch vs last-touch

**First-touch.** Vale o **primeiro** registro de origem associado àquele contato/tenant:

- Se a indicação é o primeiro toque registrado → indicação leva o crédito.
- Se já havia lead ativo de outro canal (§3.3) → o outro canal leva a atribuição e a indicação **não** é recompensada.
- Entre **duas indicações** para o mesmo contato: vale a de **timestamp mais antigo** (data da anotação no lead na fase 1; `created_at` do registro de indicação na fase 2).

### 4.4 Conflito — dois clientes reivindicam a mesma indicação

1. Vale quem tem o **registro mais antigo** e verificável (mensagem datada, anotação no lead, registro no sistema).
2. Sem registro anterior claro, o operador pergunta ao **indicado** quem de fato o levou ao produto; a resposta do indicado decide.
3. Persistindo a ambiguidade, **nenhuma recompensa é paga** (default conservador — nunca pagar duas vezes pela mesma conversão). A decisão e o motivo ficam documentados no controle de indicações (§9.4).

---

## 5. Gatilho da recompensa

### 5.1 Quando o indicador ganha

**Na 1ª fatura paga do indicado** — isto é, o **primeiro ciclo em `saas_subscription_cycles` com status pago/confirmado** para a assinatura do tenant indicado.

Não no cadastro do trial, não na ativação do trial. Motivo:

- Alinha o incentivo com **receita real** — o programa só custa quando gerou cliente pagante.
- **Antifraude:** cadastro e ativação de trial são baratos de forjar; fatura paga via Mercado Pago não é.
- Evita crédito por trial que nunca converte (a maioria, nesta fase).

### 5.2 Carência antes de aplicar o crédito

O crédito é **reconhecido** na 1ª fatura paga, mas só **aplicado** na fatura do indicador **após 60 dias corridos** dessa 1ª fatura paga do indicado, e desde que o indicado **continue ativo e adimplente** nesse período. Isso cobre o cenário de indicado que assina, paga um mês e cancela (§6.4).

### 5.3 Quando o indicado ganha

O desconto de entrada do indicado é aplicado **na 1ª mensalidade**, no momento do checkout / geração do primeiro ciclo. Não depende de carência (o risco de fraude nessa ponta é baixo — o indicado está pagando).

---

## 6. Antifraude

| Vetor | Descrição | Mitigação |
|-------|-----------|-----------|
| **Autoindicação** | Cliente cria um segundo tenant "de um colega" que é ele mesmo | Bloquear quando CNPJ / CPF do responsável / e-mail / telefone / domínio de e-mail / meio de pagamento coincidem entre indicador e indicado. Na fase 1, checagem manual do operador antes de conceder. Na fase 2, checagem automática + fila de revisão para casos suspeitos |
| **Tenants-fantasma** | Indicado assina só para gerar a recompensa, sem uso real | Só reconhecer a recompensa se o tenant indicado tiver **sinal de uso real** na data do gatilho: ao menos 1 profissional ativo **e** ao menos 1 paciente **ou** 1 agendamento criado. Sem uso → segurar e revisar |
| **Indicado que cancela logo após a recompensa** | Assina, dispara o gatilho, cancela no mês seguinte | **Carência de 60 dias** antes de aplicar o crédito (§5.2). Se cancelar ou ficar inadimplente dentro da carência → recompensa **não é concedida**. Se já concedida por engano → **clawback**: estorno no próximo ciclo do indicador |
| **Múltiplas contas / farming** | Um indicador movimenta muitas indicações de baixa qualidade | Teto de 5 conversões / 12 meses (§3.2). Volume acima disso vira análise manual e possível migração para parceria formal |
| **Conluio de rede** | Grupo de clientes se indicando em círculo | Detectável por padrão (mesmos IPs, mesma região, mesmo intervalo, reciprocidade). Fase 1: operador percebe pelo baixo volume. Fase 2: relatório de rede de indicações para revisão |
| **Indicação retroativa** | Cliente "reivindica" alguém que já era lead/cliente | Regras de §3.3 (não pode ser lead ativo de outro canal) e §4 (first-touch, prazo de 60 dias, registro datado) |

**Princípio:** na dúvida, **segurar e revisar**, nunca conceder automaticamente. O custo de um falso negativo (atrasar um crédito legítimo) é baixo; o de um falso positivo (pagar fraude) corrói a confiança no programa.

---

## 7. Impacto financeiro e registro no sistema

### 7.1 Efeito no MRR

- **Crédito único ao indicador (modelo recomendado):** **não reduz MRR.** É **custo de aquisição de cliente (CAC)**, lançado como despesa de marketing/aquisição. O ciclo do indicador continua valendo o preço cheio do plano para fins de MRR; o crédito é um abatimento pontual de caixa naquele ciclo.
- **Desconto na 1ª mensalidade do indicado:** afeta **apenas a 1ª fatura**. O MRR do indicado deve ser reconhecido pelo **valor recorrente do plano** (2º ciclo em diante), não pela 1ª fatura com desconto. Alternativamente, reconhecer o MRR cheio desde o início e tratar o desconto como custo de aquisição — decisão do `agente-saas-billing`, desde que **consistente** com o tratamento de trial.
- **Se o time adotar desconto recorrente ao indicador (modelo B):** obrigatório separar **MRR bruto** (soma dos preços de tabela das assinaturas ativas) de **MRR líquido** (após descontos de indicação) em **todos** os relatórios e no dashboard de métricas (`CLAUDE.md` §15.3). Churn e expansion calculados sobre o **bruto**.

### 7.2 Teto de exposição do programa

- **Fase 1:** no máximo **5 créditos concedidos por mês** (~R$ 400/mês de exposição no crédito ao indicador) + descontos de 1ª mensalidade dos respectivos indicados (~R$ 500/mês no pior caso). **Exposição máxima ~R$ 900/mês.**
- Se a demanda ultrapassar isso, é **sinal positivo** — reavaliar teto, orçamento e a passagem para fase 2, junto do orquestrador.
- O operador acompanha o acumulado do mês no controle de indicações (§9.4).

### 7.3 Como registrar (enquadramento para o `agente-saas-billing`)

A **decisão de implementação é do `agente-saas-billing`**. A spec só delimita as opções e as restrições:

**Opções de registro do crédito ao indicador:**

- **(a) Cupom / crédito aplicável no próximo ciclo** — o crédito fica "pendurado" na assinatura do indicador e abate o próximo `saas_subscription_cycles`.
- **(b) Linha de ajuste no ciclo** — um campo de desconto/ajuste no próprio `saas_subscription_cycles` do indicador, com referência à indicação.
- **(c) Evento em `saas_billing_events`** — um evento tipo `referral_credit` que o processo de cobrança consome ao gerar o próximo ciclo.

**Restrições obrigatórias (qualquer opção escolhida):**

1. O registro precisa permitir **reconciliar** crédito ↔ indicação ↔ tenant indicado ↔ 1ª fatura paga que o disparou. Rastreabilidade ponta a ponta.
2. O registro **não pode reduzir o campo que alimenta o cálculo de MRR** da assinatura do indicador. MRR do indicador permanece pelo preço do plano.
3. O crédito precisa ter **estado** (`reconhecido` → `em_carencia` → `aplicado` | `caducado` | `estornado`) para suportar clawback (§6.4) e carência (§5.2).
4. O desconto da 1ª mensalidade do indicado deve ser **identificável como desconto de indicação** (não confundível com desconto comercial genérico ou setup fee).
5. Todo movimento gera trilha em `saas_billing_events` para auditoria.

---

## 8. Termos do programa (rascunho)

Para publicar em `/experimentar` (link "Regras do Programa de Indicação") e enviar ao indicador quando ele entra no programa. Revisar com quem cuida do jurídico antes de publicar.

1. **Quem pode indicar:** cliente com assinatura ativa e em dia. Trials e inadimplentes não participam.
2. **Quem pode ser indicado:** profissional ou clínica que **ainda não é cliente** e **não está em negociação** com o UTecnologia Saúde por outro canal.
3. **Recompensa do indicador:** crédito único abatido da fatura seguinte, no valor vigente do programa (hoje R$ 79 / futuramente 1 mês do plano do indicado, teto R$ 199), concedido **após 60 dias** da 1ª mensalidade paga do indicado, desde que o indicado siga ativo e adimplente.
4. **Recompensa do indicado:** 50% de desconto na 1ª mensalidade (teto R$ 100).
5. **Limite:** até 5 indicações recompensadas por cliente a cada 12 meses.
6. **Prazo:** o indicado tem 60 dias, a partir da indicação, para iniciar o teste.
7. **Não cumulativo:** o benefício do indicado não se soma a outras promoções de entrada; a indicação não é reconhecida se o indicado já era lead ativo de outro canal.
8. **Antifraude:** indicações que aparentem autoindicação, contas-fantasma ou conluio são recusadas; créditos concedidos indevidamente podem ser estornados.
9. **Direito de alterar ou encerrar:** o UTecnologia Saúde pode alterar valores, regras ou **encerrar o programa a qualquer momento**, com aviso prévio de 30 dias; indicações **já registradas** dentro das regras vigentes são honradas.
10. **LGPD (ver §8.1):** ao indicar, o cliente declara ter **autorização do indicado** para informar o contato dele, ou opta por apenas compartilhar o link/código do programa e deixar que o próprio indicado se cadastre.

### 8.1 Tratamento LGPD do contato do indicado

O indicador **não pode simplesmente entregar o telefone de um colega** sem base legal. Dois modos, sendo o A o padrão:

- **Modo A — link/código (preferido, sem repasse de dado pessoal):** o indicador recebe um **link ou código** e o repassa ao colega. O **próprio indicado** se cadastra em `/experimentar` e informa quem o indicou. O dado pessoal do indicado entra no sistema **por ação do próprio indicado**, com o consentimento coletado na origem (formulário). Zero repasse de contato de terceiro.
- **Modo B — indicador fornece o contato (exceção, com salvaguardas):** só é aceito se o indicador **declara ter o consentimento do colega** para compartilhar nome e telefone/e-mail. Nesse caso, o **primeiro contato do operador com o indicado** obrigatoriamente:
  1. Identifica a origem ("Fulano, da Clínica X, indicou você ao UTecnologia Saúde");
  2. Oferece **opt-out imediato e sem custo** ("se não quiser receber, respondo aqui e apago seu contato");
  3. Registra a **base legal** (consentimento declarado pelo indicador) e a data no controle de indicações.
  - Sem manifestação positiva do indicado em **até 2 toques**, o contato é **descartado e apagado**.
- **Uso do nome do indicador:** ao entrar no programa, o indicador consente que seu **nome** seja mencionado no contato com o indicado ("Fulano indicou você"). Nada além do nome é exposto.
- **Minimização:** o sistema guarda do indicado apenas o necessário para o follow-up comercial (nome, contato, origem). Sem dados sensíveis. Retenção segue a política de `leads_capturados`.

---

## 9. Operação da fase 1 (100% manual)

### 9.1 Quando pedir a indicação

- **No "momento de valor":** logo após o cliente viver um ganho concreto — primeira semana rodando a agenda, primeiro lote de confirmações por WhatsApp, primeiro mês fechado sem papel.
- **Na renovação tranquila:** cliente adimplente há 2+ meses, sem tickets abertos.
- **Após elogio espontâneo:** cliente que elogia o produto em conversa/suporte → pedir na hora.
- Cadência: no máximo **1 pedido a cada 60 dias** por cliente, para não cansar a base pequena.

### 9.2 Script de pedido (WhatsApp / e-mail)

> "Que bom que a [agenda/confirmação por WhatsApp] está ajudando aí na [clínica/consultório]! Rapidinho: você conhece algum colega que ainda se vira com planilha ou caderno e que ia se dar bem com o UTecnologia? Se indicar e a pessoa assinar, você ganha **R$ 79 de crédito** na sua próxima fatura e ela entra com **50% de desconto no primeiro mês**. Pode me passar o nome (e, se já falou com a pessoa, o contato), ou te mando um link pra você mesmo encaminhar."

### 9.3 Como o operador concede o crédito (manual)

1. Registrar a indicação assim que souber (§9.4).
2. Quando o indicado inicia o trial: vincular no lead (`fonte = indicacao`, `origem_detalhe = nome do indicador + tenant_id do indicador`).
3. Aplicar o **desconto de 50% na 1ª mensalidade do indicado** ao gerar o checkout/primeiro ciclo em `adm/saas`.
4. Quando o **1º ciclo do indicado consta como pago**: marcar a recompensa como **reconhecida**; anotar a data.
5. **60 dias depois**, se o indicado segue ativo e adimplente e o tenant tem uso real (§6): aplicar **R$ 79 de crédito** no próximo ciclo do indicador — via cupom/ajuste manual em `adm/saas` — e registrar um `saas_billing_events` (ou, na falta de suporte, anotar no controle) com a referência da indicação.
6. Avisar o indicador que o crédito entrou.
7. Se o indicado cancelar/ficar inadimplente dentro dos 60 dias: **não conceder**; registrar o motivo.

### 9.4 Onde registrar — controle de indicações (planilha na fase 1)

Planilha única (ou aba no controle comercial existente) com uma linha por indicação:

| Campo | Exemplo |
|-------|---------|
| Data da indicação | 2026-09-10 |
| Indicador (nome + tenant_id) | Dr. Ciclano — tenant 12 |
| Elegibilidade do indicador na data | Ativo/adimplente ✔ |
| Indicado (nome + contato) | Clínica Y — (11) 9xxxx |
| Modo LGPD | A (link) / B (contato com consentimento declarado) |
| Já era lead de outro canal? | Não |
| Data início do trial | 2026-09-18 |
| Plano fechado | Clínica |
| Data 1ª fatura paga | 2026-10-20 |
| Uso real na data do gatilho | 2 profissionais, 30 pacientes ✔ |
| Recompensa reconhecida em | 2026-10-20 |
| Carência vence em | 2026-12-19 |
| Crédito aplicado em (ciclo do indicador) | 2026-12-20 — R$ 79 |
| Status | aplicado / em carência / caducado / estornado / recusado |
| Observações | — |

### 9.5 Como medir na fase 1

Do próprio controle acima: nº de pedidos feitos, nº de indicações registradas, nº que viraram trial, nº que viraram assinante, crédito concedido no mês, MRR gerado pelos indicados. Alimenta os KPIs da §11 e a revisão quinzenal da spec-mãe (§8.3).

---

## 10. Fase 2 — automação (requisitos para o `agente-saas-billing`)

### 10.0 Gatilho para iniciar a fase 2

Só vale o esforço de engenharia quando **pelo menos um** for verdadeiro:

- ≥ 5 indicações registradas por mês por 2 meses seguidos; **ou**
- ≥ 15 clientes ativos elegíveis como indicadores (base grande o bastante para o programa ter tração); **ou**
- o controle manual (§9.4) passar a consumir > 2 h/semana do operador.

Até lá, mantém-se a fase 1.

### 10.1 O que precisa existir no sistema

- **Código do indicador:** identificador curto e único gerado por tenant (ou por assinatura ativa). Exposto ao cliente no painel do tenant.
- **Tabela de indicações** (nome sugerido `saas_referrals`, a critério do `agente-saas-billing`), com no mínimo: `id`, `indicador_tenant_id`, `codigo`, `indicado_lead_id`, `indicado_tenant_id`, `indicado_subscription_id`, `status` (máquina de estados: `registrada → cadastrada → trial → convertida → em_carencia → recompensada → clawback` / `expirada` / `recusada`), `motivo`, `valor_credito_indicador`, `valor_desconto_indicado`, `data_indicacao`, `data_trial`, `data_1a_fatura_paga`, `carencia_ate`, `data_credito_aplicado`, timestamps.
- **Vínculo lead ↔ indicador:** coluna em `leads_capturados` (ex.: `referral_codigo` e/ou `referred_by_tenant_id`), coerente com o campo `fonte`/`origem_detalhe` que a spec-mãe já prevê.
- **Registro do crédito:** conforme §7.3, com estado e trilha em `saas_billing_events`.
- **Checagens antifraude automáticas:** comparação de CNPJ/CPF/e-mail/telefone/domínio entre indicador e indicado; verificação de lead prévio de outro canal (§3.3); verificação de uso real do tenant (§6); fila de revisão manual para casos marcados como suspeitos.
- **Migração idempotente** em `application/controllers/adm/Dev.php` (padrão do projeto, `CLAUDE.md` §13): criar tabela de indicações + colunas em `leads_capturados` + eventual coluna de código no tenant/assinatura; reexecutável sem efeito colateral. Deploy só pelo `agente-dev-infra`.

### 10.2 Pontos de UI (para o `agente-frontend`)

- **`/experimentar`:** campo opcional **"Indicado por (nome ou código)"**, pré-preenchível por querystring quando o indicado chega por link (`?ref=CODIGO`). Copy curta explicando o benefício de entrada.
- **Painel do tenant (área logada do cliente):** bloco **"Indique e ganhe"** com o código/link do cliente, botão de compartilhar por WhatsApp, e a lista das indicações dele com status (registrada / em teste / assinante / crédito aplicado).
- **`adm/saas`:** visão administrativa das indicações — revisar, aprovar/recusar, marcar uso real, conceder crédito, ver acumulado do mês vs teto de exposição.

### 10.3 Relatórios (fundem-se ao dashboard de métricas do admin — `CLAUDE.md` §15.3)

- Funil de indicação: indicações → trials → assinantes, com taxas.
- MRR gerado por indicados vs custo do programa (crédito concedido + desconto de 1ª fatura).
- Exposição de crédito no mês corrente vs teto.
- Lista de clawbacks e recusas, com motivo.
- Ranking de indicadores (para identificar quem deveria virar parceiro formal).

---

## 11. KPIs

| KPI | Definição | Meta inicial |
|-----|-----------|--------------|
| **% de clientes ativos que indicaram** | clientes com ≥ 1 indicação registrada ÷ clientes ativos | ≥ 20% em 90 dias |
| **Indicações por cliente indicador** | média de indicações registradas entre quem indicou | ≥ 1,5 |
| **Taxa indicação → trial** | trials iniciados ÷ indicações registradas | ≥ 40% |
| **Taxa trial → assinante (indicados)** | assinantes ÷ trials de indicação | ≥ 50% (deve superar a média geral — lead quente) |
| **Taxa indicação → assinante (composta)** | assinantes ÷ indicações registradas | ≥ 20% |
| **MRR de indicados** | soma do MRR das assinaturas originadas por indicação | crescente |
| **Custo do programa** | crédito concedido a indicadores + desconto de 1ª fatura de indicados no período | ≤ teto de exposição (§7.2) |
| **Custo / MRR gerado** | custo do programa ÷ MRR de indicados no período | ≤ 1,0 no 1º ciclo do indicado (paga-se em ≤ 1 mês) |
| **Payback** | meses de MRR do indicado para cobrir o custo daquela indicação | ≤ 1,5 mês |
| **Fraude detectada / recusada** | indicações recusadas por antifraude ÷ total | monitorar; alvo de fraude **não detectada** = 0 |

Revisão: quinzenal (junto da varredura de roadmap do `agente-produto`) e mensal no bloco de MRR por canal da spec-mãe (§8.3).

---

## 12. Critérios de aceite de valor

1. **≥ 1 assinante novo atribuído a indicação em até 60 dias** do início da fase 1.
2. **Nenhuma violação de LGPD** no repasse de contato do indicado: todo contato tem modo (A ou B) e base legal registrados; todo opt-out do indicado é honrado em ≤ 24 h.
3. **Todo crédito concedido é rastreável e reconciliável com o MRR:** dá para apontar, para cada crédito, qual indicação e qual 1ª fatura paga o originaram, e o MRR reportado não fica distorcido pelo programa (ou, se houver desconto recorrente, bruto e líquido estão separados em todos os relatórios).
4. **Zero fraude não detectada:** nenhuma recompensa paga a autoindicação, tenant-fantasma ou conluio identificável em auditoria posterior.
5. **Custo dentro do teto de exposição** (§7.2) em todos os meses da fase 1.
6. **A conversão trial → assinante geral não cai** com a entrada dos leads de indicação (eles devem, ao contrário, puxar a média para cima).

---

## 13. Handoff para o orquestrador

### 13.1 O que muda no roadmap (`CLAUDE.md` §15)

- **Fase 1 (manual) — começa já, sem código:** processo operacional das §9, controle de indicações em planilha, scripts de pedido. Não é item de engenharia; é execução do operador. Depende apenas de o operador poder aplicar desconto na 1ª mensalidade e crédito manual em `adm/saas` (já suportado no provisionamento manual).
- **Backlog → médio (fase 2, condicionada ao gatilho §10.0):** mecânica de indicação no sistema — tabela `saas_referrals`, código do indicador, vínculo em `leads_capturados`, registro de crédito com estado/carência/clawback, checagens antifraude, UI no painel do tenant e em `adm/saas`, relatórios.
- **Reforço a item já previsto:** o dashboard de métricas do admin (`§15.3`) passa a incluir funil e economia do programa de indicação.
- **Dependência:** a fase 2 usa o campo `fonte`/`origem_detalhe` de `leads_capturados` que a spec-mãe (janela 30 dias, item 1) já colocou como prioridade alta.

### 13.2 Domínios afetados

- **`agente-saas-billing`** — dono da mecânica: modelo de incentivo no sistema, registro do crédito (§7.3) sem contaminar MRR, carência e clawback, máquina de estados da indicação, checagens antifraude, relatórios de custo vs MRR. Também define se o desconto da 1ª mensalidade do indicado é tratado como redução de fatura ou como CAC.
- **`agente-clinico`** — `adm/leads` (controller/model `adm/Leads`): vínculo do lead ao indicador, status de atribuição, exibição da origem `indicacao`.
- **`agente-frontend`** — campo "Indicado por / código" em `/experimentar` (com `?ref=`), bloco "Indique e ganhe" no painel do tenant, tela de gestão de indicações em `adm/saas`, página pública de regras do programa.
- **`agente-dev-infra`** — migração idempotente em `Dev.php` (tabela de indicações + colunas em `leads_capturados` + código no tenant/assinatura); único a publicar em produção.
- **`agente-produto`** — mantém o teto de exposição, os valores de recompensa e o gatilho da fase 2; acompanha KPIs; decide migrar indicador de alto volume para o playbook de parcerias.

### 13.3 Caminho da spec e critérios de aceite

- **Spec de negócio:** `docs/superpowers/specs/2026-09-03-programa-indicacao-design.md` (este documento).
- **Spec-mãe:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md`.
- **Critérios de aceite de valor:** §12 acima. Alvo âncora: **≥ 1 assinante atribuído a indicação em 60 dias**, sem violação de LGPD, com crédito 100% rastreável e reconciliável com o MRR.
