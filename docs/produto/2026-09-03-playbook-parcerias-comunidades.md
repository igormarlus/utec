# Playbook de Parcerias com Multiplicadores e Presença em Comunidades

- **Data:** 2026-09-03
- **Autor:** agente-produto
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** playbook operacional (deriva de spec de negócio aprovada)
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.
- **Deriva de:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md`
  (spec-mãe) — canal 4 da seção 2, recomendação 3.2, janela 30 dias item 5
  (4.1), janela 60 dias item 1 (4.2). Este é o **entregável 10.1** daquela spec.

---

## 0. Nota de método

Este playbook passou pelo raciocínio de `superpowers:brainstorming` de forma
condensada (exploração de contexto, intenção, restrições e alternativas de
estrutura), sem os portões interativos de aprovação — o entregável é um
**playbook derivado de uma spec já aprovada**, não uma feature nova. A estrutura
escolhida foi *processo como espinha* (mapear → qualificar → abordar → acordar →
relacionar → medir) com **fichas por tipo de multiplicador** como referência de
consulta (seção 3). A alternativa descartada era organizar tudo por tipo de
parceiro, que repetiria o processo seis vezes.

---

## 1. Recorte e premissas

### 1.1 Parcerias são o motor; comunidades são apoio

A spec-mãe trata **parcerias com multiplicadores** e **presença em comunidades**
como coisas de peso muito diferente:

- **Parcerias = canal-foco (motor).** Esforço concentrado no início, cauda
  longa, cada relação vira uma fila recorrente de leads pré-qualificados. É o
  melhor retorno por hora de um operador. Ocupa ~75% deste playbook (seções
  2–8).
- **Comunidades = atividade de apoio de baixa intensidade.** Não é motor, não
  tem meta de lead, não escala com 1 operador e tem risco assimétrico (o dano
  de um ban supera o ganho de alguns leads). Ocupa uma seção menor (seção 9),
  focada em **regras de convivência sem virar spam**.

### 1.2 Premissas herdadas da spec-mãe

- **1 operador** (Igor) acumula produto, vendas, provisionamento e suporte.
- **Provisionamento manual** de tenant. Teto de **~20–30 trials/mês**
  (spec-mãe seção 7).
- Trial de 30 dias em `/experimentar?tipo=clinica|profissional`.
- Planos: Solo R$ 79/mês, Clínica R$ 199/mês, Pro R$ 399/mês.
- ICP: (a) clínica pequena 1–5 médicos, (b) profissional autônomo, (c) clínica
  média 5–20 (`CLAUDE.md` §2.2).
- Captura de leads em `adm/leads` (`leads_capturados`); o campo `fonte` /
  `origem_detalhe` e o SLA de follow-up são **pré-requisito de medição** e já
  estão na janela de 30 dias da spec-mãe (4.1 item 1).

### 1.3 Ciclo de confiança realista: 2–4 meses

Parceria com multiplicador **não produz assinante rápido**. O intermediário
precisa entender o produto, confiar que a recomendação não vai queimá-lo com a
base dele, encaixar a divulgação no calendário próprio (evento, newsletter,
período letivo) e, só então, o cliente indicado passa pelo trial de 30 dias
antes de assinar.

**Consequência para as metas:**

- O **assinante atribuído a parceria provavelmente cai fora da janela de 90
  dias**. Isso é esperado, não é falha do canal.
- O **KPI de curto prazo é o nº de parcerias em estágio (c) ou (d)** — acordo
  fechado e primeiro lead encaminhado (definição na seção 1.4).
- A meta de 60 dias da spec-mãe — *"2–3 parcerias piloto ativas"* — significa
  **2–3 parcerias em estágio (c) ou (d)**, e **não** 2–3 contatos feitos.

### 1.4 Definição de "parceria ativa" — os cinco estágios

Todo parceiro na planilha (seção 4.3) está em exatamente um destes estágios. A
data de entrada em cada estágio é registrada para medir o tempo a→d.

| Estágio | Nome | Definição objetiva | Conta como "parceria ativa"? |
|---------|------|--------------------|------------------------------|
| **(a)** | Contato feito | Primeira mensagem / e-mail / ligação enviada e registrada na planilha. | **Não.** |
| **(b)** | Reunião realizada | Conversa de 20–40 min (call ou presencial) em que a proposta de valor **para o parceiro** foi apresentada e um formato de co-marketing foi explorado. Parceiro demonstrou interesse. | **Não** (é progresso, não "ativa"). |
| **(c)** | Acordo de co-marketing (verbal ou escrito) | Parceiro concordou com **um formato específico** (um dos 4 modelos da seção 6), com a condição definida (cupom / comissão / conteúdo / selo) e um combinado de quando começa. Pode ser um e-mail de confirmação — não exige contrato formal. | **Sim.** |
| **(d)** | Primeiro lead encaminhado | O parceiro enviou o primeiro contato de cliente/associado, **ou** publicou o cupom/link e chegou o primeiro lead com `fonte=parceria` + `origem_detalhe=<nome do parceiro>`. | **Sim.** |
| **(e)** | Primeiro assinante atribuído | Um lead desse parceiro virou **assinante pagante** após o trial. | **Sim** (é o objetivo final; provável só após 2–4 meses). |

**Metas por janela:**

| Janela | Meta |
|--------|------|
| 30 dias | 20 parceiros mapeados e qualificados (planilha completa); 6–10 em estágio (a); 2–3 em (b). |
| 60 dias | **2–3 parcerias em estágio (c) ou (d)** (meta da spec-mãe); 4–6 em (b)+. |
| 90 dias | 5–6 em estágio (c); 2–3 em (d); 1 em (e) **se acontecer** — não é obrigatório para o canal ser considerado saudável. |

---

## 2. Tipos de multiplicador priorizados

Um **multiplicador** é uma organização que já tem a confiança e o canal de
comunicação de muitos membros do ICP, e para quem recomendar o UTecnologia
**agrega valor sem canibalizar** o negócio dela.

### 2.1 Racional de priorização

Cada tipo foi pontuado por quatro critérios:

1. **Concentração de ICP** na carteira/base do parceiro.
2. **Facilidade de fechar com 1 operador** — decisor acessível, sem exigir
   jurídico pesado, licitação ou aprovação de assembleia.
3. **Velocidade do ciclo** até o estágio (d).
4. **Ausência de restrição institucional** a endosso comercial (conselhos são
   o ponto de atenção).

| Tier | Tipo de multiplicador | ICP | Fechar c/ 1 op | Ciclo | Restrição | Quando atacar |
|------|-----------------------|-----|----------------|-------|-----------|---------------|
| **1** | Contadores / escritórios de contabilidade **especializados em saúde** | Alta | Alta (decisão do próprio escritório) | Curto | Nenhuma | **Já** |
| **1** | Associações e sindicatos de clínicas e de especialidades | Alta | Média (decisor acessível; acordo de benefício é comum) | Médio | Baixa | **Já** |
| **2** | Distribuidoras de material odontológico | Alta (odonto) | Média (via representante / gerência regional) | Médio | Nenhuma | Em paralelo |
| **2** | Cursos de pós-graduação e faculdades da área | Média-alta (futuros autônomos) | Média (coordenação de curso / setor de carreiras) | Médio (calendário letivo) | Nenhuma | Em paralelo |
| **3** | Seccionais e delegacias de conselhos regionais (CRO, CREFITO, CRP, CRM, CRN, CRFa) | Altíssima | Baixa (autarquia, processo formal) | Longo | **Alta** — muitos vedam endosso comercial | Só via evento / benefício ao inscrito; se surgir contato |
| **3** | Cooperativas médicas (Unimed e cooperativas de especialidade) | Alta | Baixa (decisão institucional, burocrática) | Longo | Média | Só se abrirem a porta |

### 2.2 Por que cada tipo alcança o ICP

**Contadores especializados em saúde.**
Praticamente toda clínica e boa parte dos autônomos PJ têm contador. O escritório
de nicho (PJ médica, equiparação hospitalar, Simples × Lucro Presumido,
pró-labore) tem a carteira **concentrada exatamente no ICP**. É consultado nos
momentos de decisão — abertura de CNPJ, organização financeira, "estou perdendo
dinheiro sem saber por quê" — que são a janela perfeita para recomendar um
sistema de gestão. E a recomendação do contador **carrega muita confiança**: o
cliente costuma seguir.
*O contador ganha:* cliente mais organizado = menos retrabalho no escritório
(faturamento estruturado, menos "cadê a nota"), diferenciação da consultoria
("nós ajudamos a digitalizar a clínica"), e comissão/crédito ou brinde.

**Associações e sindicatos de clínicas e de especialidades.**
Sindicatos de estabelecimentos de serviços de saúde, associações de clínicas,
sociedades regionais de especialidade. A base associada **é** a clínica pequena
e média. São entidades privadas — bem menos restrição que conselho —, têm
newsletter, eventos, cursos e, muitas, um "clube de vantagens".
*A entidade ganha:* mais um benefício no clube de vantagens (ajuda a reter
associado), receita de repasse ou patrocínio de evento, conteúdo pronto para a
base, imagem de quem moderniza a categoria.

**Distribuidoras de material odontológico.**
Toda clínica odonto compra material recorrentemente (Dental Cremer, Dental Speed,
Surya, distribuidoras regionais). O representante **visita o consultório
presencialmente** — canal de recomendação boca a boca muito forte. Já mapeadas
no SEO offpage como parceiros de conteúdo (`docs/seo-offpage-linkbuilding-2026-06-04.md`,
Pilar 6). Não vendem software: zero canibalização.
*A distribuidora ganha:* valor agregado ao cliente, diferenciação do
representante, comissão possível, ação co-branded ("kit para montar seu
consultório").

**Cursos de pós-graduação e faculdades da área.**
Alunos de especialização clínica e recém-formados são **futuros autônomos e
sócios de clínica** — o segmento "profissional autônomo" do ICP no momento exato
de montar consultório. Instituições gostam de entregar um "kit de vida
profissional" ao egresso. Clínicas-escola também são clientes diretos em
potencial.
*A instituição ganha:* benefício ao aluno/egresso, conteúdo para a disciplina de
gestão/empreendedorismo em saúde, laboratório com sistema real, argumento de
empregabilidade.

**Seccionais e delegacias de conselhos regionais.**
Todo profissional habilitado é registrado no conselho. As seccionais e delegacias
têm comunicação direta com a base (newsletter, eventos, educação continuada,
feiras) e alcance massivo, segmentado por especialidade e região.
**Atenção:** conselho é autarquia; muitos vedam endossar produto comercial
específico e **não podem receber comissão**. O caminho viável é: patrocínio de
evento, palestra **educativa sem pitch**, condição para inscritos e listagem em
"benefícios ao inscrito" — nunca comissão.
*O conselho ganha:* benefício ao inscrito, conteúdo de educação continuada
(gestão de consultório, LGPD em prontuário), imagem de categoria modernizada.

**Cooperativas médicas.**
Cooperativas agregam centenas a milhares de cooperados, muitos com consultório
próprio além do atendimento pela cooperativa. Têm setor de relacionamento com o
cooperado e clube de vantagens. Alcance grande, mas **ciclo de decisão
institucional longo e burocrático**.
*A cooperativa ganha:* clube de vantagens ao cooperado, apoio à estruturação dos
consultórios (reduz reclamação, melhora indicadores de qualidade).

---

## 3. Fichas rápidas por tipo (referência de consulta)

Para cada tipo: onde encontrar, quem é o decisor, o gancho de abertura e a
combinação de modelos de acordo recomendada (modelos detalhados na seção 6).

| Tipo | Decisor típico | Gancho de abertura | Combinação de acordo recomendada |
|------|----------------|--------------------|----------------------------------|
| Contador de saúde | Sócio / responsável técnico do escritório | "Seus clientes clínica que vivem no caos de planilha te dão retrabalho" | (a) crédito/benefício + (c) webinar + (d) listagem |
| Associação / sindicato de clínicas | Diretor executivo / gerente de relacionamento | "Um benefício novo para o clube de vantagens que retém associado" | (b) condição para associados + (c) conteúdo + (d) clube de vantagens |
| Distribuidora odonto | Gerência regional / coordenador de marketing | "Valor agregado ao cliente sem você vender software" | (a) comissão *one-time* **ou** (b) cupom no kit + (d) co-branded |
| Curso / faculdade | Coordenação de curso / setor de carreiras | "Kit de vida profissional para o egresso montar o consultório" | (b) benefício ao aluno/egresso + (c) aula de gestão + (d) listagem |
| Seccional de conselho | Presidência da seccional / setor administrativo | "Palestra de educação continuada + benefício ao inscrito" | (b) condição para inscritos + (c) palestra sem pitch + (d) listagem. **Nunca (a).** |
| Cooperativa médica | Setor de relacionamento com cooperado | "Mais um benefício no clube de vantagens do cooperado" | (b) clube de vantagens + (d) listagem; (c) se abrirem espaço |

---

## 4. Encontrar e qualificar 20 parceiros na região

### 4.1 Fontes de prospecção

| Tipo | Onde procurar |
|------|---------------|
| Contadores de saúde | Google: `contabilidade para médicos <cidade>`, `contador clínica <cidade>`, `escritório contábil saúde <cidade>`. Instagram: perfis de "contabilidade médica" (fazem muito marketing). LinkedIn: `contador` + `saúde` + cidade. **Rede do Igor:** perguntar a cada cliente atual "quem é seu contador? ele atende outras clínicas?" |
| Associações / sindicatos | Google: `sindicato clínicas <estado>`, `associação de clínicas <cidade>`, `sociedade <especialidade> <estado>`. Federações estaduais de estabelecimentos de saúde. Lista de sociedades filiadas às associações médicas estaduais. |
| Distribuidoras odonto | Representantes que já atendem clínicas conhecidas (perguntar aos clientes odonto). Feiras odontológicas regionais. Google: `dental <cidade> distribuidora`, `depósito dentário <cidade>`. |
| Cursos / faculdades | Cursos de odonto, fisio, psicologia, nutrição, medicina e **pós-graduações** na região (instituições locais + redes nacionais). Contato: coordenação do curso, setor de carreiras/egressos, professores da disciplina de gestão. |
| Seccionais de conselho | Site do conselho estadual (CRO, CREFITO, CRP, CRM, CRN, CRFa) → lista de seccionais/delegacias regionais e o calendário de eventos. |
| Cooperativas | Unimed regional (setor de relacionamento com cooperado / clube de vantagens); cooperativas de especialidade (anestesia, etc.). |
| Transversal | LinkedIn (diretores de associação, coordenadores de curso); Google Alerts para `<cidade>` + `clínica` + `evento`; indicações dos próprios parceiros já fechados. |

### 4.2 Critério de qualificação (score 0–2 em cada — máx. 12)

1. **Concentração de ICP:** quantos clientes/associados/alunos são clínicas ou
   profissionais de saúde? (0 = poucos; 2 = a base é quase toda ICP)
2. **Canal ativo com a base:** newsletter, evento, visita presencial, clube de
   vantagens, aula. (0 = nenhum; 2 = vários e recorrentes)
3. **Fecha com 1 operador:** decisor acessível, sem exigir jurídico pesado,
   licitação ou assembleia. (0 = processo institucional longo; 2 = uma conversa
   resolve)
4. **Sem restrição institucional** a endosso comercial. (0 = conselho com
   vedação explícita; 2 = entidade privada sem restrição)
5. **Proximidade / relação prévia:** indicação, cliente em comum, contato do
   Igor. (0 = frio total; 2 = já há relação)
6. **Reciprocidade real agora:** o UTec tem algo que interessa à base do
   parceiro neste momento. (0 = encaixe forçado; 2 = encaixe evidente)

**Corte:** score **≥ 7** entra na lista de 20. Dos 20, escolher **6–8 do Tier
1** para trabalhar primeiro (seção 2.1). Os demais ficam na fila.

### 4.3 Planilha de acompanhamento

Uma linha por parceiro. Colunas:

| Coluna | Conteúdo |
|--------|----------|
| Nome / tipo / região | — |
| Decisor | Nome, cargo, e-mail, WhatsApp, canal preferido |
| Origem da prospecção | Como chegou até ele |
| Score de qualificação | 0–12 (seção 4.2) |
| **Estágio atual** | (a) / (b) / (c) / (d) / (e) — seção 1.4 |
| **Data de entrada em cada estágio** | `a: 03/09` · `b: 12/09` · `c: …` — permite medir tempo a→d |
| Modelo de acordo | Indicação simples / condição p/ associados / conteúdo conjunto / selo (seção 6) |
| Condição acordada | Ex.: "trial de 60 dias para associados; cupom `SINDCLIN`" |
| Próxima ação + data | O toque seguinte da cadência (seção 7) |
| Leads encaminhados | Contagem (mês a mês) |
| Trials / assinantes atribuídos | Vindos de `adm/leads` com `origem_detalhe` = este parceiro |
| MRR atribuído | Bruto e líquido (se houver desconto/comissão) |
| Status | ativo / em observação / pausado / cortado (+ data e motivo) |

> Formato: planilha simples (Google Sheets). Não é caso de sistema — o volume é
> baixo e o dono é um só.

---

## 5. Scripts de primeira abordagem

Regras gerais:

- **A proposta de valor é para o PARCEIRO, não para a clínica.** O parceiro não
  liga para o quanto o UTec é bom; liga para o que **ele** ganha: comissão ou
  crédito, condição para a base dele, conteúdo co-branded sem ele produzir,
  brinde/curso, e a imagem de quem moderniza os associados/clientes.
- Primeiro contato **curto**. O objetivo é conseguir a reunião (estágio b), não
  fechar no e-mail.
- Mensagem curta (WhatsApp/DM) só depois de um primeiro e-mail ou se houver
  relação prévia. WhatsApp frio sem opt-in tem risco de política Meta
  (spec-mãe 9.1) — preferir e-mail para PJ.
- Assinar como **Igor, fundador** — transparência.

### 5.1 Contador de saúde

**E-mail:**

```
Assunto: Parceria — seus clientes de clínica organizados (menos retrabalho pra vocês)

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde (utecnologia.com.br), um sistema de
gestão para clínicas e consultórios: agenda, prontuário, faturamento e
confirmação por WhatsApp num lugar só.

Boa parte dos escritórios de contabilidade de saúde tem aquele cliente que vive
na planilha e no caderno — e isso vira retrabalho pra vocês na hora de fechar o
mês.

A ideia da parceria: vocês indicam o UTec para esses clientes e recebem [uma
comissão sobre a primeira mensalidade / créditos no sistema, se usarem / um
valor por cliente que assina]. Os clientes de vocês entram com [30 dias extras
de teste]. E eu preparo um material co-branded ("organização financeira e
operacional do consultório") que vocês podem enviar pra base sem escrever nada.

Consigo te mostrar em 20 minutos numa call esta semana?

Igor
Fundador — UTecnologia Saúde
[telefone] · utecnologia.com.br
```

**Mensagem curta (após e-mail ou com relação prévia):**

```
Oi [Nome], Igor do UTecnologia Saúde. Queria te propor uma parceria de
indicação: seus clientes de clínica saem da planilha e vocês ganham comissão/
crédito por indicação, além de um material pronto pra enviar pra base. Te mando
um resumo por e-mail ou prefere uma call rápida?
```

### 5.2 Associação / sindicato de clínicas

**E-mail:**

```
Assunto: Novo benefício para o clube de vantagens da [Entidade]

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde (utecnologia.com.br) — sistema de
gestão para clínicas: agenda, prontuário, exames e confirmação por WhatsApp
integrados.

Queria propor um benefício exclusivo para os associados da [Entidade]:
- Condição especial para quem é associado (ex.: [60 dias de teste + implantação
  sem custo]).
- Um webinar/material co-branded para a base ("[tema de gestão]"), produzido por
  nós — a [Entidade] só divulga.
- Listagem do UTec na página de vantagens da [Entidade], e podemos divulgar a
  [Entidade] para a nossa base também.

Isso agrega valor ao associado (ajuda na retenção) sem custo para a [Entidade].
Podemos conversar 30 minutos?

Igor
Fundador — UTecnologia Saúde
```

**Mensagem curta:**

```
Oi [Nome], Igor do UTecnologia Saúde. Temos uma condição exclusiva pra oferecer
como benefício aos associados da [Entidade] + um material de gestão co-branded
pra vocês enviarem pra base. Zero custo pra entidade. Consigo te apresentar
numa call de 30 min?
```

### 5.3 Distribuidora de material odontológico

**E-mail:**

```
Assunto: Valor agregado para seus clientes de clínica — sem vender software

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde — sistema de gestão para clínicas
odontológicas (agenda, prontuário odonto, confirmação por WhatsApp).

Vocês já estão dentro do consultório toda semana. A proposta: quando fizer
sentido, o representante indica o UTec como a parte de software do "kit para
organizar o consultório". Vocês recebem [comissão por cliente que assina] ou
oferecem um [cupom exclusivo] junto do kit de compras. Sem canibalizar nada —
não vendemos material, vocês não vendem software.

Também podemos fazer um conteúdo co-branded ("como o software reduz o trabalho
administrativo na clínica odonto") para o blog e a newsletter de vocês.

Vale uma conversa?

Igor
Fundador — UTecnologia Saúde
```

**Mensagem curta:**

```
Oi [Nome], Igor do UTecnologia Saúde (gestão pra clínica odonto). Proposta pro
time de vendas de vocês: indicar o UTec como o software do "kit consultório
organizado", com comissão ou cupom exclusivo pros clientes. Te mando os
detalhes?
```

### 5.4 Curso de pós-graduação / faculdade

**E-mail:**

```
Assunto: Kit de vida profissional para os egressos de [Curso]

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde — sistema de gestão para consultórios
e clínicas.

Todo aluno que se forma em [Odonto/Fisio/Psico/Nutrição] e vai montar
consultório enfrenta a mesma parede: agenda, prontuário e cobrança sem
organização nenhuma.

Proposta para a [Instituição]:
- Condição exclusiva para alunos e egressos (ex.: [90 dias de teste + desconto
  nos 3 primeiros meses]).
- Uma aula/conteúdo sobre gestão e organização de consultório para a disciplina
  de empreendedorismo em saúde — eu apresento, sem pitch de venda.
- Listagem do UTec no material de apoio ao egresso.

A [Instituição] entrega mais valor ao aluno e reforça o discurso de
empregabilidade. Podemos conversar?

Igor
Fundador — UTecnologia Saúde
```

**Mensagem curta:**

```
Oi [Nome], Igor do UTecnologia Saúde. Queria oferecer uma condição de sistema
de gestão exclusiva pros egressos de [Curso] + uma aula de organização de
consultório pra disciplina de gestão. Sem custo pra instituição. Consigo
apresentar numa call?
```

### 5.5 Seccional de conselho regional

**E-mail** (note: sem menção a comissão):

```
Assunto: Palestra de educação continuada + benefício ao inscrito — [Seccional]

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde — sistema de gestão para consultórios
e clínicas.

Gostaria de contribuir com a [Seccional] em duas frentes, sem qualquer custo:
- Uma palestra de educação continuada para os inscritos sobre organização de
  consultório e LGPD no prontuário eletrônico — conteúdo educativo, sem
  divulgação de produto.
- Uma condição exclusiva para os profissionais inscritos na [Seccional]
  (período de teste estendido), que a [Seccional] pode listar entre os
  benefícios ao inscrito.

Antes de qualquer coisa: vocês têm previsão regimental para listar esse tipo de
benefício e para palestras de parceiros? Me oriento pelo que for adequado à
[Seccional].

Igor
Fundador — UTecnologia Saúde
```

**Mensagem curta:** evitar. Conselho responde melhor a e-mail formal e ofício.

### 5.6 Cooperativa médica

**E-mail:**

```
Assunto: Benefício para o clube de vantagens do cooperado — gestão de consultório

Olá, [Nome],

Sou o Igor, fundador do UTecnologia Saúde — sistema de gestão para consultórios
(agenda, prontuário, confirmação por WhatsApp).

Muitos cooperados atendem também no consultório próprio e lidam com a mesma
dificuldade de organização. Proposta: uma condição exclusiva para cooperados
(período de teste estendido + implantação sem custo), listada no clube de
vantagens, e um material de apoio que o setor de relacionamento pode enviar.

Sem custo para a cooperativa. Podemos conversar sobre o processo de vocês para
incluir um novo benefício?

Igor
Fundador — UTecnologia Saúde
```

---

## 6. Modelos de acordo de co-marketing

Quatro modelos. A maioria das parcerias combina 2–3 (ver seção 3). Sempre pedir
o modelo (d) como piso.

### 6.1 (a) Indicação simples com comissão ou crédito

**Como funciona:** o parceiro indica — encaminha o contato ou entrega um
link/cupom com o nome dele. Por assinante que fecha e permanece pagando por um
período mínimo (ex.: 60 dias), o parceiro recebe **comissão** (% da primeira
mensalidade **ou** valor fixo) **ou** um **benefício não-monetário** (meses
grátis, se ele mesmo for usuário; brinde; vaga em curso).

| Prós para 1 operador | Contras para 1 operador |
|----------------------|-------------------------|
| Incentivo claro e fácil de explicar. | Comissão **monetária corrói margem** — Solo R$ 79, 20% = R$ 15,80; e há o controle manual de *payout*. |
| Atribuição direta via `origem_detalhe`. | Comissão alta atrai indicação de baixa qualidade. |
| Benefício não-monetário evita saída de caixa e simplifica o contábil. | Precisa de combinado sobre **estorno em churn precoce** e da nota fiscal do parceiro. |
| Funciona bem com contador e distribuidora. | **Conselho não pode receber comissão** — modelo proibido para o Tier 3-conselho. |

**Recomendação:** preferir **crédito/benefício** ou comissão **somente sobre a
1ª mensalidade** (*one-time*). **Evitar comissão recorrente** na fase de 1
operador — ver risco 8.2.

### 6.2 (b) Condição para associados/clientes (cupom/desconto para a base)

**Como funciona:** o parceiro divulga à base um benefício — trial estendido
(ex.: 60 dias em vez de 30), `setup_fee` zerada, ou 10–15% off nos 3 primeiros
meses. Cupom único do parceiro (código = nome, ex.: `SINDCLIN`). **Sem
pagamento ao parceiro.**

| Prós | Contras |
|------|---------|
| **Zero saída de caixa direta.** | Desconto recorrente **corrói MRR** — usar benefício temporário e distinguir MRR bruto/líquido (spec-mãe 9.5). |
| Funciona com **conselho, associação, cooperativa** — é benefício ao associado, não comissão. | Pode atrair quem só quer o desconto. |
| Escala sem trabalho por lead. | Exige o **cupom existir no sistema** — hoje não há. Registro manual em `observacoes` do lead + benefício aplicado no provisionamento (handoff `agente-saas-billing`). |
| Fortalece o clube de vantagens do parceiro (valor para ele) e dá atribuição limpa. | — |

**Recomendação:** **trial estendido / setup grátis** é melhor que desconto
recorrente. Melhor modelo para o Tier institucional (conselho, cooperativa,
associação).

### 6.3 (c) Conteúdo conjunto (webinar, material, e-mail para a base)

**Como funciona:** o UTec produz e co-apresenta com o parceiro um webinar ou
material (ex.: "Organização financeira e operacional do consultório" com o
contador; "LGPD no prontuário" com a associação). O parceiro divulga à base e
dispara um e-mail. CTA para `/experimentar` com `origem_detalhe`.

| Prós | Contras |
|------|---------|
| Gera **lote de leads qualificados de uma vez**. | Gera **pico de trials** — cuidar da capacidade de provisionamento (spec-mãe seção 7); agendar na semana de menor carga. |
| Posiciona o UTec como autoridade. | Esforço de produção e de agenda; *no-show* alto em webinar. |
| Dá ao parceiro conteúdo de valor sem ele produzir. | Resultado concentrado num dia — difícil manter cadência. |
| Não mexe em pricing nem em margem. Reaproveitável (grava e vira isca). | — |

**Recomendação:** no máximo **1 por parceiro por trimestre**; casar a data com a
janela de capacidade; **sempre gravar** para a cauda longa. Casa com o item
"1 webinar pequeno com um parceiro" da spec-mãe (4.2 item 3).

### 6.4 (d) Selo / listagem no site do parceiro

**Como funciona:** o parceiro lista o UTec em página de "parceiros",
"convênios", "clube de vantagens" ou "ferramentas recomendadas", com link
(idealmente com UTM/`origem_detalhe`). Pode ser recíproco.

| Prós | Contras |
|------|---------|
| Esforço quase **zero** depois de publicado; permanente. | Conversão **baixa e difusa** (tráfego passivo). |
| **Backlink** — valor de SEO (cruza com `agente-seo-geo`). | Atribuição fraca sem UTM/cupom. |
| Sem custo e sem impacto em margem. Funciona com quase todo tipo de parceiro. | Sozinho **não move o ponteiro** — é complemento. Precisa lembrar o parceiro de publicar e manter. |

**Recomendação:** pedir **sempre**, como piso de qualquer acordo. Mas **nunca
contar como a parceria** — é aditivo aos modelos (a), (b) e (c).

---

## 7. Cadência de relacionamento pós-acordo

O objetivo desta cadência é **sair de (c) para (d) em até 2–3 semanas** e manter
o parceiro engajado depois.

| Momento | Ação | Objetivo |
|---------|------|----------|
| **Semana 0** (fechou em (c)) | Enviar o **kit do parceiro** em até 48h (conteúdo na seção 7.1). | Parceiro tem tudo pronto — ele não vai criar material. |
| **Semana 1** | Confirmar que recebeu tudo; **marcar a data** do 1º disparo à base / publicação do selo. | Compromisso com data. |
| **Semanas 2–3** | Acompanhar o 1º disparo; assim que chegar o 1º lead, **avançar para (d)** na planilha. | Ativar a parceria de fato. |
| **Quinzenal (primeiros 60 dias)** | 1 toque leve: "chegou algum contato? precisa de algo? tenho um case novo pra sua base". **Reportar ao parceiro** o que aconteceu com quem ele indicou. | Fecha o *loop*, gera confiança. |
| **Mensal (após rodar)** | Mini-relatório: leads / trials / assinantes vindos dele, MRR, + 1 conteúdo novo para reusar. | Mostra resultado, renova o combinado. |
| **Trimestral** | Revisão do acordo: está convertendo? **dobrar ou cortar** (seção 8.5). | Decisão de portfólio. |

### 7.1 O kit do parceiro (materiais que ele precisa)

1. **1-pager** do que é o UTec e para quem (usar as mensagens por segmento da
   spec-mãe seção 5).
2. A **condição/benefício acordado**, por escrito, e sua validade.
3. O **link/cupom com `origem_detalhe`** já embutido.
4. **2–3 textos prontos** — post, e-mail para a base, mensagem de WhatsApp — que
   o parceiro só copia e cola.
5. **Artes** (se houver): banner de newsletter, card de post.
6. **FAQ de 5 objeções** ("é seguro?", "meus dados ficam onde?", "preciso
   instalar?", "e se eu não gostar?", "quanto custa depois do teste?").

### 7.2 Como facilitar o encaminhamento

Duas opções, em ordem de preferência:

1. **Campo "indicado por" / "parceiro" em `/experimentar`** — preenche
   `origem_detalhe`. Simples, já previsto na spec-mãe (4.1 item 1). **Padrão
   para todo parceiro.**
2. **Landing co-branded** (`/experimentar?parceiro=<slug>` ou `/p/{slug}`) com o
   logo do parceiro e a condição já aplicada. Converte mais e é mais
   profissional para o parceiro, mas exige trabalho de `agente-frontend` /
   `agente-dev-infra`. **Só para parceiros que já provaram volume em (d).**

Enquanto o `agente-saas-billing` não implementa cupom de verdade: registrar
manualmente em `observacoes` do lead e aplicar o benefício no provisionamento
(cruza com a spec de indicação — spec-mãe 10.3).

---

## 8. Integração com o funil

### 8.1 Onde o lead de parceria entra

| Caminho | Como o lead chega | `fonte` | `origem_detalhe` |
|---------|-------------------|---------|------------------|
| Encaminhamento direto | Parceiro manda o contato ao Igor (WhatsApp/e-mail); Igor cadastra em `adm/leads`. | `parceria` | `<nome do parceiro>` |
| Autoatendimento | Associado/cliente entra sozinho por `/experimentar` e informa o parceiro no campo "indicado por". | `parceria` | `<nome do parceiro>` (do campo) |
| Landing co-branded | Lead vem de `/p/{slug}`. | `parceria` | derivado do `slug` |

### 8.2 Regras de funil (herdadas da spec-mãe seção 6)

- **Fila prioritária** — mesmo nível de indicação (spec-mãe 6.1).
- **SLA de 1º contato: até 4h úteis** (spec-mãe 6.2). Cadência de 5 toques —
  D0, D+2, D+5, D+10, D+15 — alternando WhatsApp / e-mail / telefone. Sem
  resposta no D+15 → `descartado` (e avisar o parceiro com tato).
- **`origem_detalhe` SEMPRE preenchido.** Sem isso não há KPI por parceiro. Se
  o parceiro não disse quem indicou, **perguntar ao lead no 1º contato**.
- **Status pós-trial:** `trial_ativo` / `assinante` vinculado ao `tenant_id` —
  alimenta o estágio (e) e o MRR atribuído.
- **Capacidade:** leads de parceria são poucos e quentes → cabem no teto de
  20–30 trials/mês. **Só o modelo (c) webinar gera pico** — agendar na semana
  de menor carga de provisionamento (spec-mãe 7.4).

### 8.3 Critério de aceite de valor do funil de parceria

- 100% dos leads de parceria com `origem_detalhe` preenchido.
- ≥ 90% dos leads de parceria com 1º contato em ≤ 4h úteis.
- É possível responder "quantos trials e assinantes vieram de cada parceiro
  neste mês" em < 5 min.

---

## 9. KPIs do canal e critério de dobrar/cortar

### 9.1 KPIs

| KPI | Definição | Alvo / leitura |
|-----|-----------|----------------|
| **Nº de parceiros por estágio (a/b/c/d/e)** | Contagem na planilha. **KPI-chefe de curto prazo.** | 60d: 2–3 em (c)/(d). 90d: 5–6 em (c), 2–3 em (d), 1 em (e) se der. |
| Taxa de avanço entre estágios | a→b, b→c, c→d (%). | Identifica onde trava. b→c baixo = proposta de valor fraca; c→d baixo = kit ruim ou parceiro sem calendário. |
| Tempo médio por estágio (foco a→d) | Semanas entre datas na planilha. | Esperado: a→c 3–6 semanas; c→d 2–3 semanas; d→e 6–16 semanas. |
| Leads por parceiro / mês | Só dos que estão em (d)/(e). | Comparar entre parceiros para decidir onde investir. |
| Taxa lead→trial→assinante de parceria | vs. média geral do funil. | Esperado **acima da média** (leads pré-qualificados). Abaixo = parceiro mandando fora do ICP. |
| MRR atribuído por parceiro e do canal | Bruto **e** líquido (após desconto/comissão — spec-mãe 9.5). | — |
| Custo do canal | Horas de operador + comissão/desconto pago. CAC por assinante de parceria. | Comparar com CAC dos outros canais. |
| **Concentração** | % do MRR de parceria vindo do maior parceiro. | **Alerta se > 50%** — risco 8.1. |

### 9.2 Critério de dobrar / manter / cortar (aos 90 dias, por parceria)

| Decisão | Condição | Ação |
|---------|----------|------|
| **Dobrar** | Chegou a (d) e produziu **≥ 2 leads OU ≥ 1 assinante**, e o parceiro responde aos toques. | Adicionar webinar, landing co-branded, melhorar a condição, pedir 2º disparo à base. |
| **Manter em observação** | Chegou a (c) mas não a (d) por **calendário do parceiro** (evento, período letivo) e **há data marcada**. | Reavaliar em +30 dias. |
| **Cortar** | 90 dias ainda em (a)/(b); **ou** em (c) há > 6 semanas sem publicar/encaminhar e sem data; **ou** parou de responder 2 toques seguidos; **ou** os leads vieram todos fora do ICP. | E-mail educado de encerramento; manter só o selo (d) se existir; **liberar a hora do operador para prospectar 2 novos**. |

**Regra de portfólio:** manter **4–6 parcerias ativas em (c)+** ao mesmo tempo.
Para cada corte, prospectar **2 novos**.

---

## 10. Comunidades — presença de apoio, sem virar spam

> **Esta seção é deliberadamente pequena.** Comunidades **não são o motor de
> aquisição.** São uma atividade de apoio de baixa intensidade (~1–2h/semana no
> total), tocada em paralelo, **sem meta de lead**.

### 10.1 Para que serve (e para que não serve)

**Serve para:** ouvir as dores reais do ICP (insumo direto para produto e para a
copy das landings), presença de marca discreta, e capturar o lead ocasional que
pergunta "alguém conhece um sistema pra clínica?".

**Não serve como** canal de volume: alto esforço contínuo, retorno baixo e
imprevisível, não escala com 1 operador, e o risco é assimétrico (ver 10.5).

### 10.2 Onde estar (no máximo 3–4 grupos)

- Grupos de Facebook de dentistas e de gestores de clínica.
- Grupos de WhatsApp/Telegram de gestores de clínica e de especialidades.
- Fóruns tipo `comunidades.net` — Fisioterapia, NutraFisio (já citados em
  `docs/seo-offpage-linkbuilding-2026-06-04.md`, Pilar 4).
- Grupos do LinkedIn (Gestão de Clínicas Brasil, Startups Saúde Brasil,
  Dentistas Empreendedores).

### 10.3 Regra de aquecimento

**~2 semanas contribuindo genuinamente antes de qualquer menção ao produto.**
Esta regra já está firmada em
**`docs/seo-offpage-linkbuilding-2026-06-04.md` §4.2** ("Regra de participação
em comunidades", 4 itens). Este playbook **não a reinventa — apenas a reafirma**
e a estende com o "pode / não pode" abaixo.

### 10.4 Pode / Não pode

| ✅ Pode | ❌ Não pode |
|--------|-----------|
| Responder dúvida de gestão / prontuário / agenda com conteúdo útil. | Entrar num grupo e postar link. |
| Linkar um **artigo do blog** (não a landing de venda) quando ele responde de fato à pergunta. | Divulgação em massa / copiar-colar em vários grupos. |
| Mencionar o UTec **só quando alguém pergunta explicitamente** por recomendação de sistema. | DM não solicitada para membros do grupo. |
| **Identificar-se como fundador** ao mencionar o produto (transparência evita ban e *backlash*). | Postar cupom de parceiro em grupo aberto. |
| Registrar as dores recorrentes como insumo de produto. | Discutir preço de forma promocional; brigar com concorrente no grupo. |

### 10.5 Risco de ban e dano de marca

- Ban do grupo **+ associação negativa à marca** — dano difícil de reverter
  (admins de comunidade conversam entre si).
- O número de WhatsApp usado nas comunidades pode ser **o mesmo da operação
  transacional** dos clientes (confirmação de agendamento — spec-mãe 9.1). Um
  ban por spam de comunidade viraria **incidente operacional para os clientes**.
- Por isso: **baixa intensidade, sempre genuíno, nunca automatizado.**

### 10.6 Por que não é motor

Alto esforço contínuo para retorno baixo e imprevisível; não escala com 1
operador; o *downside* de um ban supera o *upside* de alguns leads. Fica como
**camada de escuta + presença**, subordinada ao LinkedIn e à prova social — não
concorre com as parcerias por tempo de operador.

---

## 11. Riscos

### 11.1 Dependência de poucas relações

Se 1–2 parceiros respondem pela maior parte do MRR de parceria, a saída de um
derruba o canal.
**Mitigação:** manter 4–6 parcerias ativas em (c)+ ao mesmo tempo; teto de **50%
do MRR de parceria por parceiro** (KPI de concentração, 9.1); diversificar
tipos de multiplicador (não só contadores).

### 11.2 Comissão que corrói margem

O plano Solo (R$ 79) tem pouca folga; comissão **recorrente** pode inviabilizar
a unidade econômica.
**Mitigação:** preferir **crédito/benefício não-monetário** e **condição para
associados** (modelo 6.2, sem pagamento ao parceiro); se houver comissão, só
**one-time** sobre a 1ª mensalidade; **modelar o CAC antes de fechar** qualquer
comissão; revisar a cada trimestre. Nenhuma comissão recorrente entra sem passar
pelo `agente-saas-billing` (impacto em MRR bruto/líquido — spec-mãe 9.5).

### 11.3 Conflito com política de conselho

Conselhos são autarquias; muitos **vedam endosso comercial** de produto
específico e **proíbem recebimento de comissão**. Uma parceria mal desenhada
pode gerar constrangimento ou nota de repúdio.
**Mitigação:** antes de abordar seccional/delegacia, **verificar no
estatuto/regimento** e **perguntar diretamente** ao setor jurídico/administrativo
se aceitam "benefício ao inscrito" e patrocínio de evento; **nunca propor
comissão a conselho**; usar só formato **educativo (palestra sem pitch) +
benefício ao inscrito + listagem**.

### 11.4 LGPD no compartilhamento de contatos

Se o parceiro repassa lista de clientes/associados à UTec **sem base legal**,
ambos violam a LGPD (agravado por serem dados ligados a saúde).
**Mitigação:**
- **Preferir que o associado se cadastre sozinho** (campo "indicado por" em
  `/experimentar`) ou que **o parceiro faça o 1º contato** e só repasse quem
  respondeu/consentiu.
- Se houver repasse de contato individual: **registrar o consentimento** (print
  / e-mail do cliente autorizando) e a **finalidade**; nada de lista em massa.
- Acordo (mesmo por e-mail) com **cláusula de proteção de dados**: finalidade,
  não reuso, descarte.
- **Nunca** compartilhar ou solicitar dados sensíveis de saúde de pacientes.

### 11.5 Parceiro que promete e não entrega

Acordo em (c) que nunca vira (d).
**Mitigação:** data combinada por escrito na semana 1 (seção 7); kit pronto para
remover fricção; **corte aos 90 dias** (9.2).

### 11.6 Marca associada a parceiro problemático

Co-branding com um parceiro de má reputação respinga no UTec.
**Mitigação:** *due diligence* leve antes de qualquer co-branding — reputação do
parceiro, reclamações públicas, coerência com o discurso da UTec.

---

## 12. Handoff para o orquestrador

### 12.1 O que muda no roadmap (`CLAUDE.md` §15)

**Nenhum item novo** além dos que a spec-mãe já introduziu. Este playbook
**reforça duas dependências** e sinaliza um item opcional:

- **Reforço (já na janela 30 dias da spec-mãe):** campo **"indicado por" /
  "parceiro"** em `/experimentar` → grava `origem_detalhe`. Sem ele não há KPI
  por parceiro.
- **Reforço (backlog → médio):** mecânica de **cupom / benefício de parceiro**
  no sistema (hoje manual em `observacoes` + aplicado no provisionamento).
  Cruza com a spec de indicação (spec-mãe 10.3).
- **Opcional (backlog):** **landing co-branded** `/p/{slug}` — só se algum
  parceiro provar volume em estágio (d).

### 12.2 Domínios afetados

| Domínio | O que faz |
|---------|-----------|
| `agente-produto` | Executa este playbook: prospecção, qualificação, abordagem, acordos, cadência, KPIs. |
| `agente-frontend` | Campo "indicado por" em `/experimentar`; eventual landing co-branded. |
| `agente-clinico` | `fonte` / `origem_detalhe` em `adm/Leads`, fila prioritária, SLA de 4h úteis (controller/model). |
| `agente-dev-infra` | Migração idempotente dos campos em `leads_capturados` (método em `adm/Dev.php`). |
| `agente-saas-billing` | Cupom / comissão / benefício de parceiro; MRR bruto/líquido por parceiro; distinção one-time × recorrente. |
| `agente-seo-geo` | Selos/listagens recíprocas (backlinks); reaproveitamento do conteúdo co-branded (webinar, material) no blog. |

### 12.3 Caminho do playbook e critérios de aceite de valor

- **Playbook:** `docs/produto/2026-09-03-playbook-parcerias-comunidades.md`
  (este documento).
- **Spec-mãe:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md`.

**Critérios de aceite de valor (90 dias):**

1. **20 parceiros mapeados e qualificados** na planilha, com estágio e datas de
   transição preenchidos.
2. **≥ 5 parcerias em estágio (c)** e **≥ 2 em estágio (d)**.
3. **100% dos leads de parceria com `origem_detalhe`** preenchido; **≥ 90% com
   1º contato em ≤ 4h úteis**.
4. **Nenhuma violação** de política de conselho ou de LGPD registrada.
5. **Nenhuma comissão recorrente ativa** que não tenha passado por modelagem de
   CAC/margem com o `agente-saas-billing`.
6. **Comunidades:** presença em **≤ 4 grupos**, **zero ban**, **zero post
   promocional fora de contexto**.

> Observação sobre a janela: **assinante atribuído a parceria (estágio (e)) pode
> não ocorrer em 90 dias** e isso **não reprova o canal** — o critério de
> sucesso de curto prazo é o nº de parcerias em (c)/(d) (item 2), não o MRR
> fechado.
