# Playbook — Cold Outreach Segmentado + LinkedIn Orgânico + Eventos/Webinars

- **Data:** 2026-09-03
- **Autor:** agente-produto
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** playbook operacional (o "como") — deriva da spec de negócio
- **Spec-mãe:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md` (entregável 10.2)
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.

---

## 1. Objetivo e recorte

### 1.1 O que é

Roteiro operacional para os três sub-canais do bloco **outbound direto** da
spec-mãe — cold outreach (canal 6), LinkedIn orgânico (canal 7) e
eventos/webinars (canal 10). A spec-mãe deliberadamente colocou os três **fora
do foco** e os autorizou apenas como **piloto pequeno e controlado** (spec-mãe
§3.2). Este playbook diz como rodar esse piloto sem violar LGPD nem a política
Meta, sem estourar o teto de provisionamento manual (~20–30 trials/mês) e sem
consumir a hora de operador que rende mais nos canais alavancados (indicação e
parceria).

### 1.2 O que NÃO é

- **Não** é autorização para escalar. É piloto. A decisão de virar canal
  recorrente é aos 90 dias, com base qualitativa + custo de operador (§9.4 e
  spec-mãe §4.4 item 2).
- **Não** é o playbook de parcerias e comunidades — esse é
  `docs/produto/2026-09-03-playbook-parcerias-comunidades.md` (spec-mãe §10.1).
  Aqui, "parceiro" aparece só como co-produtor de webinar.
- **Não** define implementação técnica de captura de lead, campo `fonte`,
  formulário de inscrição ou dashboard. O playbook descreve o **comportamento
  esperado**; o orquestrador roteia para os agentes de domínio (§12).
- **Não** contempla WhatsApp frio. WhatsApp sem opt-in viola a política Meta e
  arrisca o número transacional dos clientes (spec-mãe §9.1). WhatsApp entra
  **só depois** do opt-in do prospect e de **número separado** da operação de
  confirmação de agendamento (§4.5).
- **Não** contempla mídia paga (LinkedIn Ads, e-mail patrocinado, impulsionar
  post) — fora de escopo por decisão de negócio.

### 1.3 Premissas herdadas da spec-mãe

- **1 operador** (Igor) acumula produto, vendas, provisionamento e suporte.
- **Provisionamento manual** de tenant (`adm/saas`) é o gargalo central.
- Teto de capacidade: **~6–10 trials/semana**, alvo conservador **20–30
  trials/mês** (spec-mãe §7.2). Custo ~**1,5–2 h de operador por trial que
  ativa** (spec-mãe §7.1).
- Trial de 30 dias em `/experimentar?tipo=clinica|profissional`, sem cartão.
- Planos: Solo R$ 79, Clínica R$ 199, Pro R$ 399, Enterprise negociado.
- ICP: (a) clínica pequena 1–5 médicos, (b) profissional autônomo, (c) clínica
  média 5–20 (`CLAUDE.md` §2.2). Mensagens por segmento: spec-mãe §5.
- LinkedIn do Igor: **rede pequena**. Página da empresa: **assumir que não
  existe** — criar é pré-requisito de §6.2.

---

## 2. Princípios operacionais

1. **Piloto valida processo e mensagem, não conversão.** O piloto de 50–100
   contatos (spec-mãe §4.2 item 5) tem N pequeno demais para medir taxa de
   conversão com significância estatística. Ele valida **entregabilidade**,
   **processo** (dá para operar sozinho?) e **qualidade da mensagem** (taxa de
   resposta, taxa de spam/reclamação, objeções ouvidas). Ver §9.1.
2. **Casar ação com capacidade da semana.** Cold outreach e eventos geram
   **pico** de trial que o provisionamento manual não absorve. Nenhuma leva de
   e-mail frio e nenhum webinar entram no calendário sem folga de
   provisionamento confirmada para a semana seguinte (§7.6 e §11).
3. **LinkedIn é ativo de retorno lento (2–4 meses).** Rede pequena. Não se
   cobra resultado no curto prazo. É construção de reputação **a reboque da
   prova social** (cases da spec-mãe §4.1 item 3), não motor de lead.
4. **E-mail antes de WhatsApp; nunca do domínio/número transacional.** Toda a
   §4.5 e §10 existem para proteger a entregabilidade do e-mail dos clientes e
   o número de confirmação de agendamento.
5. **Time-box duro.** Os três sub-canais somados consomem **no máximo ~3
   h/semana** de operador durante o piloto (§11). Se passar disso sem retorno
   qualitativo claro, corta antes dos 90 dias.
6. **Value-first no relacionamento.** Sem pitch antes de sinal de interesse.
   Sem ferramenta de automação de DM. Mensagem escrita à mão. Pitch agressivo
   em nicho pequeno = dano de marca difícil de reverter (§10).

---

## 3. Cold outreach — list building do ICP conforme LGPD

### 3.1 Fontes públicas aceitáveis

Alvo: **a clínica (PJ)** ou **o profissional enquanto negócio** (consultório
com CNPJ / autônomo que se anuncia comercialmente). Nunca a pessoa física como
tal.

| Fonte | O que extrair | Observação |
|-------|---------------|------------|
| **CNES** (Cadastro Nacional de Estabelecimentos de Saúde — DataSUS, base pública) | Razão social / nome fantasia, município, tipo de estabelecimento, telefone comercial quando publicado | Melhor fonte para segmentar por porte e região. Sem dados de pessoa física. |
| **Site institucional da clínica** | E-mail de contato comercial (`contato@`, `clinica@`, `recepcao@`), telefone fixo, especialidades atendidas, cidade | Página "Contato" / "Fale conosco". Dado publicado pela própria empresa para ser contactada. |
| **Google Maps / Perfil de Empresa no Google** | Nome comercial, telefone comercial, site, categoria (ex.: "Clínica odontológica") | Filtrar por cidade + especialidade. Usar o site para achar o e-mail comercial. |
| **Diretórios profissionais públicos** (Doctoralia, BoaConsulta e similares, na parte pública de listagem) | **Preferir a página da clínica/estabelecimento**, não o perfil do profissional pessoa física; nome comercial, cidade, especialidade | Se só houver o profissional autônomo, tratá-lo como negócio e usar canal comercial publicado (e-mail/telefone do consultório). |
| **Conselhos regionais** (CRO, CRM, CREFITO, CRP) quando publicam lista de inscritos / de estabelecimentos | Nome, cidade, situação de registro | Usar só o que o conselho torna público. Não cruzar com outras bases para enriquecer dado pessoal. |
| **Associações e sindicatos de clínicas** — lista pública de associados | Nome da clínica, cidade, contato comercial | Também é insumo de parceria (playbook 10.1). |
| **Páginas de empresa no LinkedIn / Instagram comercial da clínica** | Site, e-mail comercial no perfil, cidade | Só o que está no perfil público de negócio. |

**Regra de ouro:** para cada linha da lista, registrar **URL da fonte** e
**data de captura**. Se não dá para apontar a fonte pública, a linha não entra.

### 3.2 O que NÃO usar

- **Listas compradas, alugadas ou "mailings" de brokers de dados.** Sem base
  legal, alto risco de reclamação e de blacklist de domínio.
- **Scraping de dados pessoais** — e-mail pessoal, celular pessoal, CPF,
  endereço residencial, nome de familiares. Nada que seja da pessoa física e
  não do negócio.
- **Qualquer dado sensível** (LGPD art. 5º II): saúde, opinião, filiação
  sindical de indivíduo, etc. O fato de o alvo ser da área da saúde **não**
  autoriza tratar dado sensível — só se trata dado cadastral de empresa.
- **Coleta em massa de membros de grupos** de WhatsApp/Facebook/Telegram.
- **Bases de outros sistemas** (clientes de terceiros, ex-empregadores,
  parceiros) sem consentimento/contrato que permita.
- **Enriquecimento cruzado** que reconstrua o perfil da pessoa física a partir
  de várias fontes.
- **E-mails "pega-tudo" adivinhados** em volume (`nome.sobrenome@dominio`
  gerado por padrão) — piora bounce e reputação; ver §5.5.

### 3.3 Como estruturar a lista

Planilha única (Google Sheets ou LibreOffice) — é o "CRM" do piloto. Colunas:

| Coluna | Conteúdo |
|--------|----------|
| `id` | sequencial |
| `tipo_alvo` | `clinica_pequena` \| `autonomo` \| `clinica_media` |
| `nome_negocio` | razão social ou nome fantasia (PJ / consultório) |
| `especialidade` | especialidade principal (para personalização §5.2) |
| `cidade_uf` | município + UF |
| `email_comercial` | preferir role-based (`contato@`, `clinica@`); um por linha |
| `telefone_comercial` | fixo comercial; celular só se publicado como contato do negócio |
| `site` | URL |
| `fonte_url` | **obrigatório** — de onde veio o dado |
| `data_captura` | **obrigatório** |
| `lote` | id da leva (ex.: `2026-10-odonto-sp`) |
| `status_sequencia` | `nao_iniciado` \| `t1` … `t5` \| `respondeu` \| `opt_out` \| `bounce` \| `suprimido` |
| `data_ultimo_toque` | |
| `resultado` | `sem_resposta` \| `positivo` \| `negativo` \| `spam` \| `opt_out` |
| `objecao` | texto curto — alimenta o catálogo de objeções (§9.3) |
| `virou_lead` | sim/não + link para o registro em `adm/leads` |

Aba separada **`supressao`** (lista de descadastro) — ver §4.3. Toda importação
de novo lote é conferida contra ela antes do primeiro toque.

- **Tamanho do piloto:** 50–100 contatos no total (spec-mãe §4.2 item 5),
  divididos em **2–3 lotes** de ~25–40, cada lote de **uma especialidade e uma
  praça** (ex.: odontologia em uma cidade), para que a personalização seja real
  e a leitura das objeções seja limpa.
- **Não** rodar mais de um lote por janela de 2 semanas, e nunca junto com um
  webinar (§7.6, §11).

### 3.4 Higiene e verificação

- Verificar sintaxe e MX do domínio antes de enviar (ferramenta gratuita de
  verificação de e-mail, uso pontual).
- Remover papéis genéricos de baixa entrega quando houver alternativa melhor
  (`sac@`, `noreply@`).
- Bounce forte (5xx) → `status = bounce`, remover, não retentar.
- De-duplicar por domínio: no máximo **1 contato por clínica** no piloto.

---

## 4. Base legal LGPD documentada

### 4.1 Legítimo interesse — avaliação (LIA)

Base legal: **legítimo interesse** (LGPD art. 7º, IX), aplicável a prospecção
**B2B** com dados de fontes públicas/profissionais. Registrar por escrito, uma
página, arquivada em `docs/` ou no drive do negócio:

| Elemento da LIA | Conteúdo para este piloto |
|-----------------|---------------------------|
| **Finalidade** | Divulgar, a clínicas e consultórios, uma solução de gestão clínica potencialmente útil à sua operação, convidando a um teste gratuito. |
| **Necessidade** | Dados mínimos: nome do negócio, e-mail/telefone comercial, especialidade, cidade. Nenhum dado sensível. Sem perfilamento além de "é uma clínica/consultório da especialidade X na cidade Y". |
| **Balanceamento (impacto × expectativa)** | Baixo impacto: contato em ambiente profissional, sobre assunto pertinente ao negócio do destinatário; nenhum dado sensível; frequência baixa (≤ 5 toques). Expectativa razoável do titular de receber oferta B2B pertinente no canal comercial que ele mesmo publicou. |
| **Salvaguardas** | Opt-out em **todas** as mensagens e honrado em até 2 dias úteis; lista de supressão permanente; sem revenda/compartilhamento dos dados; retenção limitada (§4.4); origem pública registrada por linha; volume pequeno; sem WhatsApp frio. |
| **Conclusão** | Legítimo interesse adequado para o piloto B2B, condicionado às salvaguardas acima. Reavaliar se o canal escalar. |

### 4.2 Registro do tratamento (ROPA)

Entrada única no registro de operações de tratamento do negócio:

- **Operação:** prospecção comercial B2B por e-mail.
- **Categorias de dados:** cadastrais de PJ/consultório — nome, e-mail
  comercial, telefone comercial, especialidade, cidade, site.
- **Categorias de titulares:** representantes/contatos comerciais de clínicas e
  consultórios.
- **Origem:** fontes públicas (CNES, sites institucionais, Google Maps,
  diretórios e conselhos públicos, associações) — URL por linha na planilha.
- **Base legal:** legítimo interesse (art. 7º, IX) — LIA em §4.1.
- **Compartilhamento:** nenhum. Dados ficam na planilha do operador.
- **Retenção:** §4.4.
- **Segurança:** planilha de acesso restrito ao operador; sem cópia em serviço
  de terceiros não controlado.
- **Direitos do titular:** canal de contato (o próprio e-mail de envio +
  `contato@`) para acesso, correção e eliminação; opt-out imediato.

### 4.3 Opt-out — regra e modelo de texto

- Presente em **todas** as mensagens, incluindo a primeira.
- Sem fricção: basta **responder** a mensagem com uma palavra, ou clicar num
  link de descadastro.
- Honrado em **até 2 dias úteis**; na prática, no mesmo dia.
- Vai para a aba **`supressao`** (e-mail + domínio + data). **Permanente** — não
  se contata de novo, nem em lote futuro, nem por outro canal.
- Qualquer resposta negativa ("não tenho interesse", "parem de enviar", "como
  vocês pegaram meu e-mail") é tratada como opt-out, mesmo sem a palavra-chave.

**Modelo de rodapé (e-mail):**

> Você recebeu este e-mail porque é uma clínica/consultório de [especialidade]
> em [cidade] e imaginamos que gestão de agenda e prontuário seja um tema
> pertinente. Se não fizer sentido, é só responder **SAIR** que removemos seu
> contato definitivamente — ou clique aqui: [link de descadastro]. Não
> enviaremos mais nada.
> UTecnologia Saúde · [CNPJ] · [endereço] · contato@utecnologia.com.br

**Modelo de resposta ao opt-out:**

> Pronto, removemos seu contato da nossa lista e ele não será mais usado.
> Obrigado pelo retorno e desculpe o incômodo. Se um dia quiser conhecer,
> estamos em utecnologia.com.br.

### 4.4 Retenção

- **Contato sem engajamento** (nunca respondeu): manter no máximo **12 meses**
  a partir do último toque; depois, eliminar da planilha ativa.
- **Opt-out:** manter **apenas** na aba `supressao` (e-mail + domínio + data),
  pelo tempo necessário para honrar o pedido — indefinidamente, já que a
  finalidade da supressão é justamente não recontatar.
- **Bounce definitivo:** eliminar.
- **Virou lead** (respondeu com interesse): sai da planilha de prospecção e
  passa a viver em `adm/leads` sob a base legal daquele relacionamento
  (execução de diligências pré-contratuais a pedido do titular).
- Sem backup paralelo da lista de prospecção fora do controle do operador.

### 4.5 E-mail preferível a WhatsApp

- **E-mail comercial** para PJ/consultório é a via defensável e a padrão do
  piloto.
- **WhatsApp frio: não.** Viola a política Meta (mensagem ativa fora da janela
  de 24h sem opt-in / sem template pertinente) e arrisca **o número usado na
  confirmação de agendamento dos clientes** — bloqueio por spam de prospecção
  seria um incidente operacional para clientes pagantes (spec-mãe §9.1).
- **WhatsApp só depois do opt-in:** se o prospect responde ao e-mail e **pede**
  para falar por WhatsApp, ou marca opt-in num formulário, aí sim — e a partir
  de um **número separado** da operação transacional. Registrar o opt-in
  (print / linha na planilha com data).
- **Telefone comercial** (ligação) é aceitável em baixo volume para follow-up
  de quem já recebeu e-mail; não deixa rastro de reputação de domínio. Não é o
  foco do piloto, mas está liberado como toque manual.

---

## 5. Sequência de e-mail frio

### 5.1 Estrutura e cadência

**5 toques** ao longo de ~18 dias. Corpo curto (≤ 90 palavras), texto puro ou
HTML mínimo, **1 link no máximo**, assinatura simples, rodapé de opt-out
(§4.3).

| Toque | Dia | Ângulo | CTA |
|-------|-----|--------|-----|
| **T1** | D0 | Dor específica da especialidade + uma frase do que resolve | Pergunta leve (interesse em ver?) — link `/experimentar` |
| **T2** | D+3 | Prova: mini-case ou número (ex.: redução de faltas com lembrete WhatsApp) | Link `/experimentar` |
| **T3** | D+7 | Objeção antecipada ("já uso planilha / caderno / outro sistema") + como é migrar | `/contato` para conversa de 15 min |
| **T4** | D+12 | Recurso concreto de valor: link para artigo do blog pertinente à especialidade (sem pedir nada) | Sem CTA de venda — só o conteúdo |
| **T5** | D+18 | Encerramento educado ("vou parar de escrever; se um dia fizer sentido, estou aqui") | Link `/experimentar`, sem pressão |

- Todos os toques na **mesma thread** (responder ao próprio e-mail anterior).
- Enviar **terça a quinta**, horário comercial.
- Qualquer resposta humana **interrompe a sequência** e vira tratamento manual.

### 5.2 Personalização mínima viável por especialidade

Não é personalização 1:1. É **1 linha por lote** que prova que a mensagem não é
spam genérico:

- Referência à **especialidade** e à **dor correspondente** da spec-mãe §5
  (odontologia: dente/procedimento/retorno; fisioterapia: sessões/evolução;
  psicologia: sessão confidencial; pediatria: dados do responsável; etc.).
- Referência à **cidade** ("clínicas de [cidade]").
- Se rápido de ver no site: 1 detalhe real (nº de profissionais, "vi que vocês
  atendem também [X]"). Opcional — não travar o lote por isso.

### 5.3 Templates por segmento

Placeholders: `[Especialidade]`, `[cidade]`, `[nome_negocio]`, `[artigo]`.

**Segmento (a) — Clínica pequena (1–5 médicos)**

> **Assunto T1:** agenda da [nome_negocio] ainda no papel/planilha?
>
> Olá! Falo de clínicas de [Especialidade] em [cidade] que ainda controlam
> agenda em planilha e prontuário espalhado — remarcação por telefone, falta
> sem controle, zero visão de faturamento.
>
> O UTecnologia Saúde junta agenda, prontuário e confirmação por WhatsApp num
> lugar só. Dá pra tirar a clínica da planilha em cerca de uma semana.
>
> Faz sentido eu te mandar um acesso de teste (30 dias, sem cartão)?
>
> [rodapé opt-out]

> **Assunto T2:** o lembrete que derruba as faltas
>
> Complementando: o recurso que as clínicas mais comentam é a confirmação
> automática por WhatsApp — o paciente confirma ou cancela no botão e a agenda
> se atualiza sozinha. Menos horário vago, menos telefone.
>
> Teste 30 dias sem cartão: utecnologia.com.br/experimentar?tipo=clinica
>
> [rodapé opt-out]

**Segmento (b) — Profissional autônomo**

> **Assunto T1:** consultório organizado sem contratar secretária
>
> Olá! Muitos profissionais de [Especialidade] em [cidade] tocam tudo no
> WhatsApp e no caderno — e perdem histórico de paciente.
>
> O UTecnologia Saúde te dá agenda online, prontuário digital e lembrete
> automático de consulta, sem precisar de secretária.
>
> Quer que eu te mande um teste de 30 dias, sem cartão?
>
> [rodapé opt-out]

**Segmento (c) — Clínica média (5–20 profissionais)**

> **Assunto T1:** quem vê o quê na [nome_negocio]?
>
> Olá! Em clínicas com 5–20 profissionais o problema deixa de ser agenda e
> passa a ser gestão: controlar o que cada um enxerga, padronizar prontuário
> entre especialidades, fechar relatório sem garimpar planilha.
>
> O UTecnologia Saúde tem hierarquia de acesso nativa e relatórios
> centralizados. Posso mostrar em 15 min numa call?
>
> [rodapé opt-out]

**T3 (comum, ajustar o começo por segmento) — objeção de migração**

> **Assunto:** e o trabalho de migrar?
>
> A dúvida que sempre aparece: "vou ter que parar tudo pra trocar de sistema?"
> Não. Começa pela agenda, o histórico entra aos poucos, e nos primeiros dias
> eu acompanho de perto. Quer conversar 15 min sem compromisso?
> utecnologia.com.br/contato
>
> [rodapé opt-out]

**T4 — valor puro**

> **Assunto:** [artigo] — talvez útil pra [cidade]
>
> Sem agenda comercial aqui: escrevi/publiquei isto e lembrei de você —
> [título do artigo do blog]: [link]. Se ajudar, ótimo.
>
> [rodapé opt-out]

**T5 — encerramento**

> **Assunto:** encerrando por aqui
>
> Não quero virar ruído na sua caixa. Vou parar de escrever. Se um dia gestão
> de agenda e prontuário virar prioridade, o teste de 30 dias está sempre em
> utecnologia.com.br/experimentar. Sucesso pra [nome_negocio]!
>
> [rodapé opt-out]

### 5.4 Quando parar / suprimir

| Situação | Ação |
|----------|------|
| Resposta negativa / pedido de descadastro | Opt-out imediato (§4.3), `resultado = negativo` ou `opt_out` |
| Reclamação de spam / "como conseguiram meu e-mail" | Opt-out + anotar como incidente; se repetir, revisar fonte do lote |
| Bounce definitivo | Remover, `status = bounce` |
| Sem nenhuma resposta após T5 | `status = suprimido`; **não recontatar por 6 meses**; depois disso, só se houver novidade de produto relevante |
| Resposta positiva | Parar a sequência, tratamento manual, **criar lead em `adm/leads`** com `fonte = outbound` (§8) |
| Auto-reply / "estou de férias" | Pausar a sequência, retomar na data indicada |

### 5.5 Configuração de envio e reputação de domínio

**Risco central: queimar a reputação do domínio prejudica o e-mail
transacional dos clientes** (confirmações, avisos). Mitigações **obrigatórias**:

- **Nunca** enviar do domínio/servidor transacional. Usar um **subdomínio ou
  domínio de envio separado** (ex.: `mail.utecnologia.com.br` ou um domínio
  dedicado a divulgação), com **SPF, DKIM e DMARC** próprios.
- **Aquecimento:** começar com ~5–10 e-mails/dia na 1ª semana, subir
  gradualmente; teto do piloto **≤ 20–30 envios/dia**.
- Enviar como pessoa (Igor), não como `marketing@`; assinatura real; sem
  imagem, sem múltiplos links, sem encurtador.
- Monitorar: **taxa de bounce < 3%**, **taxa de reclamação de spam < 0,1%**. Se
  estourar, **parar a leva** e revisar lista/fonte.
- No piloto, **envio manual ou mail-merge simples** (poucas dezenas). Um
  sequenciador de e-mail dedicado é **decisão de ferramenta paga aos 90 dias**
  (§9.4), não do piloto.

---

## 6. Social selling no LinkedIn

Retorno esperado **2–4 meses**, rede pequena. **Não** se cobra lead no
trimestre. É ativo de reputação a reboque dos cases (spec-mãe §4.1 item 3).

### 6.1 Otimização do perfil do Igor

| Elemento | Antes (provável) | Depois — orientado a quem ele ajuda |
|----------|------------------|-------------------------------------|
| **Headline** | "Desenvolvedor / TI" | `Ajudo clínicas e consultórios a sair da planilha · Fundador do UTecnologia Saúde — agenda, prontuário e WhatsApp num sistema só` |
| **Foto + banner** | foto genérica | foto profissional; banner com a proposta de valor e o site |
| **Seção "Sobre"** | currículo técnico | 1º parágrafo: para quem é e qual dor resolve. 2º: o que é o produto em 2 linhas. 3º: prova (nº de clínicas, um resultado de case). 4º: CTA leve — "teste 30 dias em utecnologia.com.br/experimentar ou me chame aqui". |
| **Destaque / Featured** | vazio | 3 itens: um case, um artigo do blog, a página `/experimentar` |
| **Experiência** | cargos antigos | entrada "UTecnologia Saúde" com descrição do produto e do ICP |
| **URL personalizada** | `/in/igor-xxxx-123` | `/in/igormarlus` ou similar |
| **Modo criação de conteúdo** | off | on, com tópicos: gestão de clínica, prontuário digital, produtividade em consultório |
| **Serviços / "Disponível para"** | — | "Software de gestão para clínicas e consultórios" |

### 6.2 Página da empresa

- **Assumir que não existe → criar** a Company Page do UTecnologia Saúde
  (logo, banner, descrição com ICP e proposta de valor, site, setor "Software",
  CTA "Visite o site" → `/experimentar`). Tarefa de operação, não de código.
- A página **não** carrega alcance sozinha no começo. Uso: repostar os posts do
  Igor, servir de destino "oficial", e permitir que clientes marquem a empresa.
- Postar no perfil do Igor (alcance real) e **compartilhar pela página**. Não
  postar só pela página.
- Pedir aos poucos clientes atuais que **sigam** a página e associem o
  emprego/parceria quando fizer sentido.

### 6.3 Calendário de conteúdo — 2–3 posts/semana

Fonte: **cases** (spec-mãe §4.1 item 3) e **artigos do blog** (do
`agente-seo-geo`). Mix ~80% valor / 20% produto. Sem link externo no corpo do
post (mata alcance) — link no 1º comentário.

| Dia | Tipo | Exemplo |
|-----|------|---------|
| Seg | Dor do ICP / dica prática | "3 sinais de que sua clínica passou do ponto de largar a planilha" |
| Qua | Bastidor / opinião | "Por que fizemos a confirmação de consulta por botão de WhatsApp, e não por link" |
| Sex (quinzenal) | Mini-case / prova social | "A [tipo de clínica] que cortou X% de faltas em 2 meses — o que mudou" |
| Alternado | Repost de artigo do blog com comentário próprio | resumo em 3 bullets + "artigo completo no 1º comentário" |

- Escrever à mão, primeira pessoa, PT-BR, formato "gancho na 1ª linha +
  quebras curtas".
- Reaproveitar cada case em 3 formatos (post narrativo, carrossel de bullets,
  citação do cliente).
- **Sem** ferramenta de auto-post/auto-DM.

### 6.4 Engajamento e conexão

- **15 min/dia** comentando de forma substantiva (não "ótimo post!") em
  publicações de **médicos, dentistas, fisioterapeutas, psicólogos, gestores de
  clínica e de operadoras, e contadores de saúde**.
- **Conexão com nota**, sem pitch:
  > "Olá [nome], acompanho conteúdo sobre gestão de [especialidade] e seus
  > posts sobre [tema] me chamaram atenção. Vou adorar acompanhar seu
  > trabalho por aqui."
- **Depois de aceito**, nada de pitch imediato. Semana seguinte, mensagem leve
  referenciando algo que a pessoa publicou, oferecendo um recurso (artigo,
  checklist) **sem pedir call**. Só falar de produto quando a pessoa perguntar
  ou demonstrar interesse.
- Quem responde com interesse → `/contato` ou `/experimentar` e **lead em
  `adm/leads` com `fonte = linkedin`, `origem_detalhe = linkedin-dm`**.
- Meta de volume: ~5–10 conexões novas qualificadas/semana. Sem ferramenta de
  automação — LinkedIn bane e a marca fica associada a spam.

### 6.5 Expectativa de retorno e o que medir

Nos primeiros 90 dias, medir **indicadores de construção de ativo**, não
conversão:

- Visualizações de perfil / semana (tendência).
- Taxa de aceite de convite.
- Impressões e comentários por post (tendência).
- Nº de DMs recebidas espontaneamente.
- Nº de leads `fonte = linkedin` (esperado: **baixo**, 0–2/mês — normal).

Se em 90 dias os indicadores de ativo sobem de forma consistente, o canal
segue mesmo com 0 assinante atribuído — o payoff é 6+ meses.

---

## 7. Eventos / webinars

### 7.1 Formato e princípios

- **Online, ao vivo, 30–40 min** + 10–15 min de perguntas.
- **Pequeno:** 20–50 inscritos. **Um** por trimestre no máximo.
- **Sempre com parceiro co-produtor** (associação/sindicato de clínicas,
  curso/faculdade, contador de saúde — do playbook 10.1). Parceiro traz a
  audiência; Igor traz o conteúdo. Divulgação e crédito divididos.
- **Nunca** agendar sem folga de provisionamento confirmada para as 2 semanas
  seguintes (§7.6).

### 7.2 Escolha do parceiro e tema

- **Parceiro:** já ter tido pelo menos 1 conversa de relacionamento (não
  abordar do zero pedindo webinar). Priorizar quem tem base de e-mail/WhatsApp
  ativa de clínicas.
- **Tema = dor do ICP**, não demo de produto. Exemplos:
  - "Como reduzir faltas na clínica com confirmação automática de consulta"
  - "Sair da planilha sem parar a clínica: agenda e prontuário digital na
    prática"
  - "Gestão de acesso da equipe em clínica multiprofissional: quem vê o quê"
  - "O que o contador precisa que a clínica organize (e como um sistema ajuda)"
    — quando o parceiro é contábil
- Produto aparece nos **últimos 10 min** como forma de resolver a dor + oferta
  de trial guiado.

### 7.3 Divulgação orgânica

- **Base do parceiro** (e-mail e/ou WhatsApp broadcast aos associados/alunos) —
  principal fonte de inscrição.
- **Comunidades** onde o Igor já tem presença genuína — seguindo as regras de
  não-spam do `docs/seo-offpage-linkbuilding-2026-06-04.md` §4.2 e do playbook
  10.1.
- **LinkedIn** do Igor + do parceiro + páginas das duas empresas.
- **Banner no blog** e no rodapé dos e-mails de relacionamento.
- **Sem** impulsionamento pago.

### 7.4 Captura de inscrição

- Formulário simples (nome, e-mail, nome da clínica, especialidade, cidade,
  **opt-in de contato**).
- Gera **lead com `fonte = evento`** e **`origem_detalhe` = nome do evento**
  (ex.: `webinar-faltas-2026-10`). Comportamento esperado; implementação é
  handoff (§12).
- Página de inscrição pode ser uma landing dedicada ou um formulário
  encaminhado pelo parceiro — o que importa é o `fonte`/`origem_detalhe`
  chegarem em `adm/leads`.
- **Limite de inscrições** definido pela capacidade da semana (§7.6) — fechar
  inscrição ao atingir o teto.

### 7.5 Follow-up pós-evento

Segmentar inscritos: **compareceu e ficou até o fim / fez pergunta** = mais
quente; **inscreveu e não veio** = mais frio (mandar replay).

| Quando | Ação | Público |
|--------|------|---------|
| D0 (mesmo dia) | E-mail de agradecimento + gravação + 1 recurso (checklist/artigo) | Todos os inscritos |
| D+2 | Mensagem pessoal (e-mail; WhatsApp só se opt-in) a quem ficou até o fim / perguntou — "vi que você perguntou sobre X, quer que eu te mostre?" | Quentes |
| D+5 | Oferta de trial de 30 dias / call de 15 min | Quentes sem resposta |
| D+5 | E-mail com a gravação + "se quiser testar, tá aqui" | Frios / ausentes |
| Depois | Entra na **cadência de 5 toques** de `adm/leads` (D0/D+2/D+5/D+10/D+15 — spec-mãe §6.2) contada a partir do 1º contato pós-evento | Quem respondeu / demonstrou interesse |

Sem resposta ao fim da cadência → `descartado` (mantém `fonte = evento` para
medição).

### 7.6 Casamento com a capacidade de provisionamento

Regra dura (spec-mãe §7, §3.3):

1. Antes de **marcar data**, confirmar que as **2 semanas seguintes ao evento**
   têm **≥ 6–8 h/semana** livres de provisionamento (equivale a ~4–6 trials
   novos absorvíveis por semana **além** do fluxo normal dos outros canais).
2. **Teto de inscrições** = (capacidade livre da semana em trials) ÷ (taxa
   esperada inscrito→trial, assumir conservador ~15–25%). Ex.: se dá para
   absorver 6 trials extras/semana e ~20% dos inscritos pedem trial, fechar
   inscrição em ~30.
3. **Escalonar os convites de trial** pós-evento: não disparar "crie seu trial
   agora" para todos no D0. Convidar em levas (ex.: 5–6/dia) na ordem de
   engajamento, para o provisionamento acompanhar.
4. **Nunca** rodar um lote de cold outreach na mesma janela de 2 semanas do
   webinar (§11).
5. Se a capacidade da semana **cair** depois de marcado (suporte estourou,
   férias), **adiar o webinar** — é preferível a entregar onboarding ruim e
   queimar o parceiro.

---

## 8. Integração com o funil

### 8.1 `fonte` / `origem_detalhe` por sub-canal

| Sub-canal | `fonte` | `origem_detalhe` | Fila (spec-mãe §6.1) |
|-----------|---------|------------------|----------------------|
| Cold e-mail (resposta positiva) | `outbound` | id do lote (ex.: `2026-10-odonto-sp`) | Normal |
| LinkedIn (DM / comentário → contato) | `linkedin` | `linkedin-dm` \| `linkedin-post` \| `linkedin-perfil` | Normal |
| Webinar / evento (inscrição) | `evento` | nome do evento (ex.: `webinar-faltas-2026-10`) | **Agendada com a capacidade da semana** |

Todos os três são leads **frios/mornos** — entram na fila **Normal**, atrás dos
leads quentes de indicação e parceria. Evento tem tratamento de **fila
agendada** (não empurra o provisionamento; entra conforme §7.6).

### 8.2 Duas cadências distintas — não confundir

- **Cadência de prospecção (pré-lead):** a sequência de **5 e-mails frios** de
  §5.1 (D0/D+3/D+7/D+12/D+18). O contato **ainda não é lead**; vive na planilha
  de outreach. Objetivo: obter a primeira resposta.
- **Cadência de nurture (pós-lead):** os **5 toques** de `adm/leads`
  (D0/D+2/D+5/D+10/D+15, alternando WhatsApp/e-mail/telefone — spec-mãe §6.2).
  Começa **quando o contato responde com interesse** e é criado em `adm/leads`.

Uma alimenta a outra. A métrica de "taxa de resposta" (§9) é da **cadência de
prospecção**; a de "lead→trial→assinante" é da **cadência de nurture** e já é
responsabilidade do funil geral da spec-mãe.

### 8.3 SLA (spec-mãe §6.2)

| Evento | 1º contato |
|--------|-----------|
| Resposta positiva a e-mail frio (`outbound`) | **até 1 dia útil** |
| DM/comentário no LinkedIn com interesse | **até 1 dia útil** |
| Inscrito de evento que **pediu** demo/trial na inscrição ou no chat | **mesmo dia** |
| Inscrito de evento sem pedido explícito | segue o follow-up de §7.5 |
| Qualquer um que entrou em `/experimentar` (virou trial) | **mesmo dia** (intenção máxima) |

---

## 9. Métricas e critério de parada/escala

### 9.1 O que o piloto valida — e o que NÃO valida

**O piloto de 50–100 contatos (cold outreach) valida:**

- **Entregabilidade:** os e-mails chegam? (taxa de entrega, bounce, reclamação
  de spam, se o subdomínio de envio aguenta.)
- **Processo:** um operador sozinho consegue montar lista LGPD-safe, enviar,
  acompanhar e responder sem estourar o time-box? Quanto custa em horas?
- **Qualidade da mensagem:** a taxa de resposta e o teor das respostas dizem se
  o assunto, o ângulo e a proposta de valor por segmento ressoam. As
  **objeções** ouvidas são o produto mais valioso do piloto.

**O piloto NÃO valida:**

- **Taxa de conversão com significância estatística.** N = 50–100 é pequeno
  demais. 1 ou 2 assinantes a mais ou a menos mudam o número inteiro. Não
  extrapolar "converteu X%, logo 1.000 contatos dão 10X".
- Qual lote/especialidade "converte melhor" — a amostra por lote (~25–40) não
  sustenta essa comparação.

**Portanto, o critério de "escalar ou não" aos 90 dias é qualitativo + custo de
operador (§9.4), não um número de conversão.**

### 9.2 Métricas por sub-canal

**Cold outreach (por lote e agregado):**

| Métrica | Alvo / referência |
|---------|-------------------|
| Taxa de entrega | ≥ 95% |
| Taxa de bounce | < 3% (senão, parar e revisar lista) |
| Taxa de reclamação de spam | < 0,1% (senão, parar imediatamente) |
| Taxa de resposta (qualquer resposta humana) | sinal bom ≥ 3–5% |
| Taxa de resposta **positiva** (quer ver / quer conversar) | acompanhar tendência; sem alvo rígido |
| Taxa de resposta **negativa / opt-out** | acompanhar; alta = mensagem ou lista ruim |
| Horas de operador por lead gerado | comparar com indicação/parceria |
| Objeções catalogadas | §9.3 — meta é **cobertura**, não número |

**LinkedIn:** só indicadores de ativo (§6.5). Sem alvo de conversão em 90 dias.

**Eventos:**

| Métrica | Referência |
|---------|-----------|
| Inscritos | dentro do teto de capacidade (§7.6) |
| Taxa de comparecimento | 30–50% do inscrito é normal em webinar orgânico |
| Participantes engajados (pergunta / ficou até o fim) | contagem absoluta |
| Trials originados (`fonte = evento`) | dentro da capacidade absorvida |
| Horas de operador para produzir + rodar + follow-up | comparar com o retorno |

### 9.3 Objeções catalogadas (template)

Manter uma aba/lista viva. Para cada objeção: **texto ouvido**, **quantas
vezes**, **segmento**, **resposta que funcionou**, **é gap de produto /
posicionamento / preço?**

Categorias esperadas:

- "Já uso [planilha / caderno / concorrente X]."
- "Não tenho tempo de migrar agora."
- "Muito caro / não cabe no orçamento."
- "Preciso ver funcionando / falar com alguém."
- "Como vocês pegaram meu contato?" (sinal de fonte ou abordagem ruim — tratar).
- "Não sou eu que decido isso."
- "Faz [recurso específico]?" (gap de produto — levar ao roadmap).

Objeções recorrentes de **posicionamento/preço** viram insumo para o
`agente-produto` (revisão de mensagem §5 da spec-mãe / de pricing). Objeções de
**produto** viram itens de backlog para o orquestrador.

### 9.4 Decisão aos 90 dias (spec-mãe §4.4 item 2)

Reunir: métricas §9.2, catálogo de objeções §9.3, horas de operador gastas no
total, e comparação com o custo/hora dos canais alavancados (indicação,
parceria).

**Perguntas de decisão (qualitativas):**

1. O e-mail frio **entregou** de forma consistente e sem risco à reputação do
   domínio transacional?
2. As respostas foram **majoritariamente civis** e revelaram objeções
   **acionáveis** (ajudaram produto/posicionamento), mesmo que poucas viraram
   trial?
3. O custo em **horas de operador por lead** foi **competitivo** com indicação
   e parceria — ou muito pior?
4. Deu para operar **dentro do time-box de ~3 h/semana** sem roubar tempo dos
   canais que já convertem?
5. Existe um **caminho claro de melhoria** (lista melhor, mensagem melhor) ou o
   teto já apareceu?

**Saídas possíveis:**

| Cenário | Decisão |
|---------|---------|
| Entregou bem, objeções úteis, custo/hora aceitável, dá para melhorar | **Continuar como canal recorrente de baixa intensidade** (1 lote/mês, manual). Avaliar **ferramenta paga** (sequenciador de e-mail, verificador de lista) só se o gargalo for **execução manual**, não resposta. |
| Entregou bem mas custo/hora pior que parceria/indicação e sem folga de operador | **Pausar.** Retomar só quando houver 2ª pessoa (spec-mãe §7.4 item 5) ou quando os canais alavancados saturarem. |
| Problema de entregabilidade / reclamação / risco ao domínio | **Encerrar cold e-mail.** Manter só telefone comercial de follow-up e os outros canais. |
| Objeções mostraram gap claro de produto/posicionamento | Encerrar/pausar o outreach, **levar o aprendizado ao roadmap**, retomar depois de resolvido. |

**LinkedIn:** decisão aos 90 dias é só "os indicadores de ativo sobem?" — se
sim, mantém a cadência leve; não se avalia por conversão. **Eventos:** decisão
é por evento — repetir com o mesmo parceiro se comparecimento e leads
compensaram as horas.

**2ª pessoa ou ferramenta paga (spec-mãe §4.4 item 2):** só entram na conversa
se a decisão for "continuar como recorrente" **e** o gargalo for capacidade de
execução — nunca para "tentar mais volume" com mensagem/lista não validadas.

---

## 10. Riscos e mitigações

| # | Risco | Impacto | Mitigação |
|---|-------|---------|-----------|
| 1 | **Reputação de domínio/IP no e-mail frio** — bounce/reclamação derrubam a entrega do e-mail **transacional dos clientes** | Alto — incidente para clientes pagantes | Domínio/subdomínio de envio **separado** do transacional; SPF/DKIM/DMARC; aquecimento; ≤ 20–30/dia; bounce < 3% e spam < 0,1% como gatilho de parada (§5.5) |
| 2 | **Ban do número no WhatsApp** — o número da confirmação de agendamento é o mesmo da operação; prospecção por WhatsApp o bloqueia | Alto — clientes reais ficam sem confirmação de consulta | **Zero WhatsApp frio.** WhatsApp de vendas só pós-opt-in e de **número separado** (§4.5, spec-mãe §9.1) |
| 3 | **Reclamação LGPD / ANPD** | Médio-alto — reputação + sanção | LIA escrita (§4.1); ROPA (§4.2); só fonte pública com URL registrada; sem dado sensível; opt-out honrado + supressão permanente (§4.3); retenção limitada (§4.4) |
| 4 | **Tempo de operador consumido sem retorno** — 3 sub-canais frios competindo com indicação/parceria que já convertem | Médio — custo de oportunidade | Time-box **≤ 3 h/semana** somado (§11); parada antecipada se estourar sem retorno qualitativo; indicação/parceria **nunca** cedem lugar a outbound |
| 5 | **Dano de marca por pitch agressivo no LinkedIn** — nicho pequeno, reputação viaja | Médio — difícil reverter | Value-first; sem auto-DM/auto-post; conexão com nota sem pitch; produto só após sinal de interesse (§6.4) |
| 6 | **Pico de trial de webinar/leva de e-mail estoura o provisionamento** — onboarding ruim na primeira impressão e com o parceiro do evento junto | Alto — queima lead qualificado + relação com parceiro | Casamento com a capacidade (§7.6); teto de inscrição; convites de trial escalonados; adiar o webinar se a capacidade cair; nunca leva de e-mail + webinar na mesma janela (§11) |
| 7 | **Fonte de lista mal escolhida** — muitos "como conseguiram meu e-mail?" | Médio — sinal de risco LGPD e de reputação | Revisar a fonte do lote ao 2º sinal; preferir e-mail que a própria clínica publicou como canal de contato (§3.1) |
| 8 | **Concentração em 1 pessoa** — férias/doença param o piloto | Baixo (piloto) / Médio (se virar recorrente) | Planilha + este playbook permitem que um terceiro opere o essencial; não iniciar leva nova às vésperas de ausência |

---

## 11. Ritmo operacional semanal e time-box

**Time-box total dos 3 sub-canais durante o piloto: ~3 h/semana.**

| Ritual | Frequência | Tempo | Conteúdo |
|--------|-----------|-------|----------|
| LinkedIn — publicar | 2–3×/semana | ~20 min/post | Post do calendário §6.3 (perfil + compartilhar pela página) |
| LinkedIn — engajar/conectar | diário | ~15 min | Comentar + convites com nota (§6.4) |
| Cold outreach — envios do lote ativo | terça a quinta | ~20 min/dia | Disparar o toque do dia; responder quem respondeu |
| Cold outreach — montar próximo lote | quando o lote atual chega em T3 | ~1–2 h (uma vez por lote) | List building §3 + conferência contra supressão |
| Revisão semanal | 1×/semana | ~20 min | Atualizar planilha; checar bounce/spam; **checar capacidade de provisionamento da próxima semana** antes de liberar novo lote ou marcar webinar |
| Webinar — produção | 1×/trimestre | esforço concentrado, **fora** do time-box semanal, só com folga de capacidade | §7 |

**Calendário de sequenciamento (janela de 60 dias da spec-mãe §4.2):**

- **LinkedIn:** ligado desde a semana 1, intensidade leve e contínua — não gera
  pico, não precisa de folga de capacidade.
- **Cold outreach:** 2–3 lotes de ~25–40, um por vez, cada um iniciado só numa
  semana com folga de provisionamento confirmada. ~2 semanas entre o início de
  um lote e o próximo.
- **Webinar:** 1 na janela, agendado para uma semana **sem lote de e-mail
  ativo** e com folga de capacidade nas 2 semanas seguintes (§7.6).
- **Regra:** no máximo **um** canal gerador de pico (lote de e-mail **ou**
  webinar) "quente" por vez. LinkedIn sempre roda por baixo.

---

## 12. Handoff para o orquestrador

### 12.1 O que muda no roadmap (`CLAUDE.md` §15)

- **Nenhum item novo de produto.** Este playbook opera **dentro** do que a
  spec-mãe já colocou no roadmap: a instrumentação de `adm/leads` com
  `fonte`/`origem_detalhe`, status pós-trial e SLA/cadência (spec-mãe §4.1
  item 1 / §11.1). Os valores `outbound`, `linkedin`, `evento` de `fonte` e o
  tratamento de **fila agendada** para `evento` são requisitos que essa
  instrumentação precisa contemplar.
- **Depende de** (não bloqueia, mas reduz muito o valor sem): os **3 cases**
  (spec-mãe §4.1 item 3) e os **artigos do blog** — são a matéria-prima do
  LinkedIn (§6.3) e do toque T2/T4 do e-mail (§5.3).
- **Confirmar com `agente-dev-infra`:** existência de subdomínio/domínio de
  envio separado do transacional com SPF/DKIM/DMARC (§5.5). Se não existir, é
  pré-requisito operacional do piloto de cold e-mail.

### 12.2 Domínios afetados

- **`agente-clinico` / `agente-frontend`** — captura de lead precisa aceitar
  `fonte ∈ {outbound, linkedin, evento}` e `origem_detalhe` livre; formulário
  de inscrição de webinar (landing ou form encaminhável) que grave
  `fonte = evento` + nome do evento e o opt-in de contato.
- **`agente-dev-infra`** — subdomínio/registro DNS de envio separado
  (SPF/DKIM/DMARC) para o cold e-mail; nada de deploy sem plano. Migração de
  `fonte`/`origem_detalhe` já é da spec-mãe.
- **`agente-saas-billing`** — visão de funil por `fonte` (spec-mãe §4.4 item 5)
  deve discriminar os 3 sub-canais; nada específico a implementar aqui além
  disso.
- **`agente-seo-geo`** — cases publicados e artigos do blog são insumo direto
  deste playbook; alinhar quais artigos existem para linkar em §5.3/§6.3. Sem
  mudança de prioridade além da já fixada na spec-mãe §4.3.
- **`agente-whatsapp`** — **nenhuma mudança**. Registro explícito de que o
  playbook **não** usa o número transacional para prospecção e não pede
  ampliação de cota por causa de outbound.
- **`agente-produto`** — dono do catálogo de objeções (§9.3), da decisão de 90
  dias (§9.4) e da eventual revisão de mensagem/pricing que as objeções
  motivem.

### 12.3 Caminho da spec / playbook e critérios de aceite de valor

- **Playbook:** `docs/produto/2026-09-03-playbook-cold-outreach-linkedin.md`
  (este documento).
- **Spec-mãe:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md`.

**Critérios de aceite de valor (janela de 60–90 dias do piloto):**

1. **LGPD documentada e viva:** LIA (§4.1) e ROPA (§4.2) escritas antes do 1º
   envio; 100% das linhas da lista com `fonte_url` e `data_captura`; 100% dos
   opt-outs honrados em ≤ 2 dias úteis e na aba de supressão.
2. **Zero incidente de reputação:** bounce < 3% e reclamação de spam < 0,1% no
   agregado; **nenhum** efeito observável na entrega do e-mail transacional dos
   clientes; **nenhum** uso do número/WhatsApp transacional para prospecção.
3. **Rastreabilidade no funil:** 100% dos leads gerados pelos 3 sub-canais
   entram em `adm/leads` com `fonte` e `origem_detalhe` corretos; é possível
   dizer "quantos leads/trials vieram de `outbound`, `linkedin` e `evento`
   neste mês" em < 5 min.
4. **Capacidade preservada:** nenhuma leva de e-mail ou webinar rodou sem folga
   de provisionamento confirmada; conversão **trial→assinante não caiu**
   durante o piloto; nenhum trial de evento ficou sem 1º contato humano na 1ª
   semana.
5. **Aprendizado capturado:** catálogo de objeções (§9.3) preenchido com pelo
   menos as categorias esperadas; decisão de 90 dias (§9.4) registrada com
   base qualitativa + horas de operador — **não** com base em taxa de conversão
   extrapolada.
6. **Time-box respeitado:** esforço somado dos 3 sub-canais ficou em ~3
   h/semana; indicação e parceria não perderam prioridade de fila nem de tempo
   para o outbound.
