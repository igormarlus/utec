# Programa de Prova Social, Cases e Depoimentos — spec de negócio

- **Data:** 2026-09-03
- **Autor:** agente-produto
- **Status:** proposta para revisão do orquestrador / Igor
- **Tipo:** spec de negócio (o "o quê" e o "porquê" — antecede o brainstorming técnico de cada domínio)
- **Escopo de escrita:** documento em `docs/`. Nenhuma mudança de código aqui.
- **Deriva de:** `docs/produto/2026-09-03-estrategia-vendas-organicas.md` (spec-mãe) — canal 9 (seção 2), janela 30 dias item 3 (4.1), repriorização do `agente-seo-geo` item 2 (4.3), "prova" por segmento (seção 5), política Capterra/G2 (9.2). É o entregável **10.4** da spec-mãe.
- **Brainstorming:** conduzido de forma assíncrona a partir do brief do orquestrador e do ajuste de revisão crítico — a base de clientes hoje é majoritariamente de **interessados e trials**, não há 3–5 clientes nomeáveis prontos, então o programa é **faseado e honesto** (Fase 0 sem cliente nomeado → Fase 1 com 1–2 → Fase 2 com 3–5).

---

## 1. Objetivo

Prova social é **multiplicador de conversão de todos os outros canais** — site, landings `seo_*`, blog, LinkedIn, cold outreach, parcerias, indicação e diretórios de software. Não é canal de topo de funil: não gera demanda sozinha, mas eleva a taxa de avanço em cada ponto onde já existe atenção.

O que o programa entrega:

1. Um **acervo reutilizável** de peças de prova (depoimentos curtos, cases, números agregados, screenshots, selos) que qualquer canal pode puxar.
2. Um **processo de coleta** com roteiro de entrevista e consentimento por escrito, barato para o cliente (< 1h de tempo dele).
3. Uma **política neutra de reviews em diretório** que não arrisca penalidade de Capterra/G2/GetApp.
4. Um caminho **faseado**: começa hoje sem depender de nenhum cliente nomeado e amadurece conforme a base cresce.

### 1.1 O que NÃO é

- **Não** é a tática de cadastro de vendor em diretórios (anti-bot, textos de perfil) — isso é do `agente-seo-geo` e já está em `docs/seo-offpage-linkbuilding-2026-06-04.md`, `docs/seo-offpage-textos-perfis-vendor-2026-07-13.md` e na memória `project_seo_offpage`. Esta spec só **fixa a regra** de como convidar para review (seção 7).
- **Não** é o programa de indicação (spec derivada 10.3 da spec-mãe).
- **Não** especifica a implementação da função de agregação de métricas clínicas — isso é do `agente-clinico` (seção 6 e 9.3).
- **Não** desenha a seção de prova social nem o template de página de case — isso é do `agente-frontend`; aqui só se define o conteúdo, os campos e o comportamento esperado.
- **Não** usa serviços pagos de depoimento/review nem depoimento fictício. Fase 0 publica **apenas fato verificável** do produto e do uso agregado.

### 1.2 Critério de aceite de valor

- Fase 0 no ar em **até 2 semanas**, sem nenhum cliente nomeado.
- 1º case nomeado (Fase 1) publicado em **até 60 dias**.
- 3 cases — **1 por segmento do ICP** — em até **90–120 dias**.
- Toda métrica publicada tem período, base amostral e fonte declarados, e lastro guardado para auditoria.
- Nenhuma violação registrada de política Meta / Capterra / G2 / LGPD.
- Pelo menos 1 peça de prova social em uso em **cada canal ativo** (landing, LinkedIn, outreach, parceria).

---

## 2. Tipos de prova social e onde cada um entra

| Tipo | O que é | Onde entra | Depende de cliente nomeado? |
|------|---------|-----------|-----------------------------|
| **Depoimento curto** | 1–2 frases + identificação (nome + papel + segmento, ou anonimizado "clínica de fisioterapia, GO") + 1 métrica opcional | Seção de prova na `index-front.php`, landings `seo_*`, comparativos `/alternativa-*`, posts do LinkedIn, follow-up por WhatsApp/e-mail | Fase 1+ (Fase 0 usa versão anonimizada só se houver relato real registrado) |
| **Case completo** | Estrutura fixa dor → antes → virada → depois → métrica → citação (seção 5.3). Página dedicada `/casos/{slug}` + versão **PDF** para outreach e parceria | Página de case, PDF anexado em e-mail de outreach/parceria, link em post do LinkedIn, citação em guest post | Fase 1 (leve) / Fase 2 (completo) |
| **Review em diretório** | Avaliação do cliente no Capterra / G2 / GetApp (nota + texto livre, redigida pelo próprio cliente) | Perfil do produto no diretório; citação da nota média no site quando houver volume mínimo do diretório | Sim — **dono: `agente-seo-geo`** (seção 7) |
| **Logo wall** | Grade de logos de clínicas clientes | Homepage, página de case, material de parceria | Sim — **só com autorização escrita** do uso de marca (seção 5.4) |
| **Números de uso agregados** | Contadores reais e anonimizados do próprio sistema ("N confirmações de agendamento processadas por WhatsApp", "X clínicas em produção") | Homepage, landings, PDF de vendas, pauta de PR (link bait — spec-mãe SEO offpage) | **Não** — é o núcleo da Fase 0 |
| **Selo de tempo em produção** | "UTecnologia Saúde — em produção desde [ano]" +, quando houver, disponibilidade/uptime | Rodapé da homepage, landings, assinatura de e-mail | **Não** |
| **Screenshots anotados do produto** | Prints reais de agenda, prontuário, confirmação por WhatsApp, árvore de acesso — de ambiente de demonstração, **sem dado real de paciente** | Landings de especialidade (reforça o mock que já existe), página de case, blog, PDF | **Não** |
| **Resultado agregado com métrica** | Afirmação de resultado lastreada em dado agregado do sistema ("clínicas que ativaram o lembrete automático registram menos faltas" → com o % quando a agregação existir e for defensável) | Homepage, landing de clínica pequena, comparativos | **Não** para a afirmação qualitativa; o **número** depende da função de agregação do `agente-clinico` (seção 6) |

Regra transversal: **nenhuma peça vai ao ar sem lastro**. Depoimento sem consentimento escrito, métrica sem base declarada e logo sem autorização de marca ficam fora.

---

## 3. Plano faseado 0 → 1 → 2

### 3.1 Fase 0 — agora, sem nenhum cliente nomeado

Objetivo: colocar prova social crível no ar em 2 semanas usando só o que já é verdade e verificável.

O que dá para publicar já:

- **Números de uso agregados** extraídos do sistema, anonimizados:
  - nº de confirmações de agendamento processadas por WhatsApp (contagem real em `whatsapp_notificacoes`);
  - nº de lembretes automáticos disparados;
  - nº de clínicas/consultórios em produção (tenants ativos);
  - nº de agendamentos gerenciados no sistema.
  - Cada número com rótulo de período e data de atualização (ex.: "acumulado até set/2026").
- **Selo "em produção desde [ano]"** no rodapé e nas landings.
- **Screenshots anotados** dos recursos-chave (agenda, prontuário, confirmação por WhatsApp, hierarquia de acesso), tirados de ambiente de demonstração, sem PII.
- **Afirmação de resultado qualitativa** enquanto o número não existe: "Clínicas que ativam o lembrete automático de WhatsApp têm menos faltas na agenda." O **percentual** entra só quando a função de agregação do `agente-clinico` estiver pronta e o dado for defensável (base ≥ N clínicas, período declarado).
- **Logos** apenas de quem já autorizou por escrito o uso de marca — se ninguém autorizou ainda, o logo wall **não entra** na Fase 0.

O que **não** se faz na Fase 0: nenhum depoimento fictício, nenhuma citação atribuída a "um cliente", nenhum número sem cálculo guardado.

### 3.2 Fase 1 — 1–2 cases nomeados, formato leve

Gatilho: existe pelo menos 1 cliente que (a) usa o sistema de forma consistente, (b) tem um resultado observável e (c) topa aparecer.

Entrega por case:

- **Depoimento curto** (1–2 frases) + identificação + **1 métrica** (do sistema ou do relato).
- Publicado na seção de prova da homepage e em 1 landing do segmento correspondente.
- Consentimento escrito assinado (seção 5.4).
- Sem página dedicada ainda — formato card.

Meta: **1º case nomeado em até 60 dias**; idealmente o 2º logo em seguida, cobrindo 2 segmentos distintos do ICP.

### 3.3 Fase 2 — 3–5 cases completos

Gatilho: base amadurece, há clientes representando os 3 segmentos do ICP com pelo menos ~2–3 meses de uso.

Entrega por case:

- **Case completo** na estrutura da seção 5.3, com página `/casos/{slug}` e PDF.
- 1–3 métricas com período e método.
- Screenshot anotado do recurso citado.
- Interlink com as landings/comparativos do segmento (execução do `agente-seo-geo`).
- **Alvo: 1–2 cases por segmento do ICP → 3–5 no total.**

Fase 2 é contínua: a cada trimestre, revisar cases (métrica ainda válida? consentimento ainda vigente?) e adicionar 1 novo se a base permitir.

---

## 4. Seleção de clientes para case

### 4.1 Critérios (todos os quatro, não apenas um)

1. **Engajamento real:** loga com regularidade, usa mais de um módulo (agenda + prontuário, ou agenda + WhatsApp), não é trial parado.
2. **Resultado mensurável:** existe pelo menos um número que melhorou — do sistema (faltas, confirmações, volume) ou do relato (horas na secretaria, tempo de fechamento de relatório).
3. **Representa um segmento do ICP:** clínica pequena 1–5, autônomo, ou clínica média 5–20 (`CLAUDE.md` §2.2). Priorizar diversidade de segmento antes de acumular cases do mesmo perfil.
4. **Relação boa com o Igor:** responde mensagem, já deu feedback espontâneo, não tem pendência comercial nem ticket de suporte aberto e mal resolvido.

### 4.2 Quantos por segmento

| Segmento | Fase 1 | Fase 2 (alvo) |
|----------|:------:|:-------------:|
| Clínica pequena (1–5) | 1 | 2 |
| Profissional autônomo | 0–1 | 1–2 |
| Clínica média (5–20) | 0–1 | 1 |
| **Total** | **1–2** | **3–5** |

Clínica média é a mais difícil de conseguir cedo (ciclo mais longo, menos clientes desse porte na base) — não travar o programa esperando por ela; publicar os outros e manter a vaga aberta.

### 4.3 Como abordar o pedido

1. **Momento certo:** depois de um sinal positivo (elogio espontâneo, renovação, resposta boa numa conversa de acompanhamento). Nunca junto de cobrança ou de um problema aberto.
2. **Canal:** o que o cliente já usa com o Igor (WhatsApp ou ligação). Mensagem curta, sem formulário longo de cara.
3. **Enquadramento:** "quero contar a história da [clínica] pra ajudar outras clínicas parecidas a decidir — são 20–30 min de conversa, você revisa tudo antes de publicar e pode voltar atrás quando quiser."
4. **Deixar claro o custo:** < 1h no total (conversa + revisão do texto).
5. **Sem contrapartida financeira condicionada** — case não é review de diretório; misturar os dois confunde a política da seção 7. Um agradecimento não condicionado (ex.: destaque na comunidade, brinde) é aceitável e deve ser o mesmo para todos os que participam.
6. **Aceitar o "não" e o "sim parcial":** cliente pode topar depoimento anônimo mas não logo, ou número mas não foto. O consentimento (5.4) tem opções separadas.

---

## 5. Processo de coleta

### 5.1 Roteiro de entrevista (dor → antes → depois → métrica)

Conversa de 20–30 min, gravada só com autorização. Perguntas na ordem:

1. **Antes** — "Antes do UTecnologia, como vocês faziam a agenda / o prontuário / a confirmação de consulta? O que mais incomodava no dia a dia?"
2. **Dor / gatilho** — "O que fez procurar um sistema? Teve algum episódio específico que foi a gota d'água?"
3. **Adoção** — "Como foi começar a usar? Quanto tempo até estar rodando de verdade?"
4. **Depois** — "O que mudou na rotina? Descreve um dia típico antes e um depois."
5. **Métrica** — "Tem algum número que você acompanha? Faltas, retorno de paciente, horas de secretaria, tempo pra fechar o relatório do mês? Se não tiver de cabeça, eu levanto do sistema de forma agregada e te mostro pra você confirmar."
6. **Citação** — "Se um colega te perguntasse se vale a pena, o que você diria?" (é daqui que sai a frase publicada)
7. **Honestidade** — "O que ainda falta? O que você melhoraria?" (não vai pro material, mas alimenta o roadmap e mantém a conversa verdadeira)

Saída da entrevista: rascunho do depoimento/case + lista de métricas a confirmar (as do sistema vão para a fila de agregação do `agente-clinico`).

### 5.2 Fluxo de produção

1. `agente-produto` conduz a entrevista e escreve o rascunho (depoimento curto ou case completo).
2. Métricas do sistema: pedido de agregação anonimizada ao `agente-clinico` (seção 6); o cliente **confirma** o número antes de publicar.
3. Cliente revisa o texto final e **aprova por escrito**.
4. `agente-seo-geo` publica (página de case, interlink, distribuição) e `agente-frontend` insere na seção de prova / logo wall.
5. Lastro (cálculo da métrica, e-mail de aprovação, termo assinado) arquivado.

### 5.3 Estrutura do case completo

- **Título:** resultado + segmento (ex.: "Clínica de fisioterapia reduziu faltas ativando o lembrete por WhatsApp").
- **Identificação:** nome da clínica, cidade/UF, especialidade, porte (nº de profissionais), plano — conforme o que o consentimento liberar.
- **Contexto / antes:** como operavam, ferramentas, dor principal.
- **A virada:** por que escolheram o UTecnologia, o que implantaram, quanto tempo até rodar.
- **Depois:** o que mudou na operação.
- **Resultado:** 1–3 métricas, cada uma com **período** e **método** (ex.: "faltas caíram de X% para Y% comparando os 60 dias antes e os 60 dias depois de ativar o lembrete — dado do sistema").
- **Citação direta:** 1–2 frases do cliente.
- **Screenshot anotado** do recurso citado.
- **CTA** para o segmento: `/experimentar?tipo=clinica|profissional` ou `/contato`.
- **Metadados de compartilhamento:** título e descrição para OG/Twitter, imagem de card (especificação de campo — o `agente-frontend` implementa).

### 5.4 Consentimento por escrito

Documento curto (1 página), assinado (digital ou foto do papel) antes de qualquer publicação. Campos:

- **Identificação:** cliente/clínica, responsável que autoriza, e-mail.
- **Autorizo o uso de** (marcação independente, item a item):
  - [ ] meu nome pessoal
  - [ ] nome / razão social da clínica
  - [ ] logotipo da clínica
  - [ ] foto (minha e/ou da fachada)
  - [ ] métrica quantitativa de resultado (com os números confirmados por mim)
  - [ ] depoimento textual / citação
- **Canais de publicação autorizados:** site utecnologia.com.br, página de case, PDF de vendas/apresentação, LinkedIn, diretórios de software, material para parceiros. (Cliente pode excluir canais.)
- **Revisão prévia:** o texto final me foi apresentado e aprovo esta versão.
- **Vigência:** por tempo indeterminado até revogação.
- **Direito de revogar:** a qualquer momento, por mensagem escrita (e-mail ou WhatsApp). A UTecnologia remove o material do site e dos canais sob seu controle em **até 7 dias úteis**. Materiais já impressos/distribuídos a terceiros podem não ser recolhíveis.
- **Sem contrapartida financeira condicionada** à publicação ou ao teor do depoimento.
- **LGPD:** finalidade (divulgação institucional), controlador (UTecnologia Saúde), contato para pedidos de titular. Sem dados sensíveis de saúde de pacientes em nenhuma hipótese.

Custo para o cliente: entrevista 20–30 min + revisão do texto ~10 min = **menos de 1 hora**.

---

## 6. Métricas de resultado a capturar

### 6.1 Extraíveis do sistema, de forma anonimizada

A **agregação anonimizada é fronteira do `agente-clinico`** — corresponde à tarefa **6b da triagem do orquestrador** para esta spec. Esta spec lista o que se quer; o `agente-clinico` define como calcular sem expor PII e sem cruzar tenants indevidamente.

| Métrica | Fonte provável | Uso na prova social |
|---------|----------------|---------------------|
| Taxa de falta (no-show) antes/depois de ativar o lembrete | `agendamentos.status` + data de ativação do lembrete do tenant | Núcleo do gancho de clínica pequena |
| Nº de confirmações de agendamento por WhatsApp | `whatsapp_notificacoes.status_confirmacao = confirmado` | Número agregado da Fase 0 + case |
| Nº de lembretes automáticos disparados | `whatsapp_notificacoes.tipo_notificacao` | Número agregado da Fase 0 |
| Nº de agendamentos gerenciados / mês | `agendamentos` | Número agregado |
| Nº de clínicas em produção / tempo médio em produção | `saas_tenants` ativos | Selo + número agregado |
| Tempo entre início do atendimento e registro do prontuário | timestamps de `agendamentos` (se disponíveis) | Case de autônomo / clínica média ("prontuário fechado no mesmo dia") |
| Nº de pacientes ativos por clínica | `usuarios` nível 5 no escopo do tenant | Contexto do case (porte), nunca lista nominal |

Requisitos de negócio para a agregação (para o `agente-clinico` dimensionar):

- Saída **sem PII** e **sem identificar tenant** quando o número for publicado como agregado ("clínicas em geral").
- Quando o número for de **um case específico**, ele sai do escopo daquele tenant e **o cliente confirma** antes de publicar.
- Toda métrica traz **janela temporal** e **tamanho da base** (nº de clínicas / nº de agendamentos considerados).
- Número agregado só é publicável com base mínima que impeça reidentificação (o `agente-clinico` define o piso; sugestão de partida: ≥ 5 tenants).

### 6.2 Dependentes do relato do cliente

Coletadas na entrevista, confirmadas por escrito:

- horas de trabalho administrativo economizadas por semana;
- redução de retrabalho (ex.: "parei de refazer a agenda no papel");
- custo evitado (ex.: "não precisei contratar uma segunda secretária");
- tempo de fechamento do relatório mensal (clínica média);
- satisfação geral e o que mudou na percepção da equipe/pacientes.

### 6.3 Como declarar métrica de forma verdadeira e verificável

- **Sempre** com período de medição, base amostral e fonte (sistema ou relato) visíveis ou a um clique.
- **Preferir intervalo** a número cravado quando a base é pequena ("entre 20% e 30%" em vez de "27,4%").
- **Nunca extrapolar** de 1 cliente para "as clínicas". "A Clínica X reduziu…" ≠ "Clínicas reduzem…".
- Recorte honesto no denominador: "clínicas que **ativaram** o lembrete", não "todas as clínicas".
- **Guardar o cálculo** de cada número publicado (query/planilha + data). Se na revisão trimestral o número cair ou perder base, atualizar ou remover — nunca deixar número velho no ar.
- Número inflado ou indefensável é **risco**, não benefício: vira objeção do concorrente e mina a confiança do lead.

---

## 7. Política de reviews em diretório (fixa a regra da spec-mãe 9.2)

**Dono da execução:** `agente-seo-geo`. Esta spec fixa a regra; a operação (link de convite, textos de perfil, cadência) segue em `project_seo_offpage` e nos docs de SEO offpage.

**Permitido:**

- Convidar **todos** os clientes a avaliar — não só os satisfeitos.
- Oferecer **o mesmo** agradecimento simbólico (brinde / gift card de valor baixo) a **todos que avaliarem**, sem olhar a nota.
- Enviar link direto para a página de avaliação do diretório.
- Responder **todas** as avaliações, positivas e negativas, sem defensividade.

**Proibido:**

- Pagar por avaliação positiva.
- Condicionar o agradecimento à nota ou ao teor.
- Selecionar só clientes felizes para o convite (cherry-picking).
- Redigir ou editar a avaliação pelo cliente.
- Usar contas internas ou de conhecidos para avaliar.

**Processo neutro de convite:**

1. Lista de convite = **todos** os clientes ativos elegíveis (mesmo critério para todos), não uma seleção manual.
2. Mesmo texto de convite para todos, sem sugerir nota.
3. Registro de quem foi convidado e quando (para auditoria da neutralidade).
4. Agradecimento enviado a quem **confirmar** que avaliou, independentemente da nota — e igual para todos.
5. Seguir os termos do vendor de cada diretório (Capterra/G2/GetApp têm regras próprias sobre incentivo — checar antes de qualquer campanha).

Case ≠ review de diretório: o case é curadoria editorial (a UTecnologia escolhe quem tem história boa); a review é aberta e não curada. **Não misturar os dois processos nem as duas listas.**

---

## 8. Ligação com as mensagens por segmento (spec-mãe seção 5)

| Segmento | Gancho (spec-mãe) | Prova que reforça |
|----------|-------------------|-------------------|
| **Clínica pequena (1–5)** | "Tire a clínica da planilha em uma semana — agenda, prontuário e confirmação por WhatsApp no mesmo lugar." | Case/agregado de **redução de faltas** com o lembrete automático + **nº de confirmações por WhatsApp no mês**. Screenshot anotado da tela de confirmação. Selo "em produção desde…". |
| **Profissional autônomo** | "Seu consultório organizado sem contratar secretária: agenda online, prontuário e lembretes automáticos." | Depoimento de **autônomo da mesma especialidade** + **horas economizadas por semana** (relato) + "prontuário fechado no mesmo dia" (sistema). |
| **Clínica média (5–20)** | "Controle quem vê o quê e acompanhe a clínica inteira num painel — hierarquia de acesso nativa e relatórios centralizados." | Case de clínica **multiprofissional** + **redução de tempo no fechamento do relatório mensal** + screenshot anotado da árvore de acesso / relatório consolidado. |

Regra de aplicação: cada landing e cada comparativo do segmento puxa a peça de prova daquele segmento. Enquanto não houver case do segmento, usa-se o número agregado da Fase 0 com o recorte mais próximo.

---

## 9. Handoff

### 9.1 `agente-frontend` — o que precisa construir (só especificação aqui; design é dele)

- **Seção de prova social na `index-front.php`:** bloco com números de uso agregados (contadores), faixa de depoimentos curtos (card: citação + identificação + métrica), logo wall (condicional à autorização), selo "em produção desde…". Deve degradar bem quando só houver Fase 0 (sem depoimentos).
- **Template de página de case** (`/casos` índice + `/casos/{slug}`): renderiza a estrutura da seção 5.3 (contexto, virada, depois, métrica com período/método, citação, screenshot anotado, CTA por segmento).
- **Metadados de compartilhamento:** campos e tags OG/Twitter (title, description, imagem de card) para cada página de case e para a seção de prova.
- **Componente de depoimento reutilizável** para reaproveitar nas landings `seo_*` e nos comparativos.
- **Bloco de números agregados reutilizável**, alimentado pela saída da função de agregação (não por número hard-coded).
- Não define os números nem o texto — recebe do `agente-produto` / `agente-seo-geo`.

### 9.2 `agente-seo-geo` — o que precisa fazer

- **Publicar os cases** (páginas `/casos/{slug}`) e fazer o **interlink** com landings de especialidade, comparativos `/alternativa-*` e artigos do blog do segmento correspondente (casa com a repriorização 4.3 da spec-mãe).
- **Coletar reviews** em Capterra / G2 / GetApp seguindo a **política neutra da seção 7** — lista de convite = todos os clientes elegíveis, mesmo texto, mesmo agradecimento, sem filtrar por nota.
- **Distribuir** os números agregados como pauta de PR / link bait (já previsto em `docs/seo-offpage-linkbuilding-2026-06-04.md` — "link bait por dados originais").
- **Link reclamation:** quando um portal citar a UTecnologia sem link, pedir o link (rotina já no plano offpage).
- Responder todas as avaliações nos diretórios.

### 9.3 `agente-clinico` — o que precisa entregar

- **Função de agregação anonimizada de métricas clínicas** — tarefa **6b da triagem do orquestrador** para esta spec. Entrada: as métricas da seção 6.1. Saída: números agregados sem PII e sem identificar tenant, com janela temporal e tamanho da base; e, para case específico, o recorte de um tenant sujeito à confirmação do cliente.
- Definir o **piso de base** para publicação de número agregado (impedir reidentificação; sugestão de partida ≥ 5 tenants).
- Passa por seu próprio `superpowers:brainstorming` antes de implementar.

### 9.4 `agente-dev-infra`

- Criar a(s) **rota(s)** `/casos` e `/casos/{slug}` quando o `agente-frontend` tiver o template.
- Nenhuma migração prevista por esta spec (a agregação pode ser query em cima do schema atual — o `agente-clinico` confirma).

### 9.5 O que muda no roadmap (`CLAUDE.md` §15)

- **Novo item, §15.3 (backlog → médio):** "Programa de prova social — Fase 0 (números agregados + selo + screenshots) e seção na landing". Pré-requisito da Fase 0 com número: função de agregação anonimizada do `agente-clinico`.
- **Novo item, §15.3:** "Template de página de case (`/casos`) + PDF de case para outreach".
- **Repriorização (sem item novo) do `agente-seo-geo`:** coleta de reviews nos mesmos moldes já previstos, agora com a política neutra da seção 7 explicitada; interlink dos cases com o núcleo comercial.
- **Sem mudança de pricing/planos.**

### 9.6 Critérios de aceite de valor (repetição consolidada)

1. Fase 0 no ar em ≤ 2 semanas, sem cliente nomeado, só com fato verificável.
2. 1º case nomeado em ≤ 60 dias; 3 cases (1 por segmento) em ≤ 90–120 dias.
3. Toda métrica publicada tem período + base + fonte, com lastro arquivado.
4. Reviews coletadas por processo neutro documentado; zero violação de política de diretório.
5. Prova social em uso em cada canal ativo (landing, LinkedIn, outreach, parceria).
6. Efeito na conversão da landing medível antes/depois via tráfego de IA + FB CAPI.

---

## 10. KPIs

| KPI | Como medir | Alvo inicial |
|-----|-----------|--------------|
| Nº de depoimentos curtos publicados | contagem no site | ≥ 2 em 60 dias, ≥ 5 em 120 dias |
| Nº de cases completos publicados | páginas `/casos/{slug}` | 1 por segmento do ICP em 90–120 dias (3–5 total) |
| Tempo até o 1º case nomeado | data de publicação − hoje | ≤ 60 dias |
| Nº de reviews por diretório | painel Capterra / G2 / GetApp | ≥ 3 por diretório ativo em 90 dias |
| Nota média por diretório | painel do diretório | ≥ 4,0 / 5 (observado, não forçado) |
| Uso da prova nos outros canais | % de posts de LinkedIn / e-mails de outreach / decks de parceria que incluem uma peça de prova | ≥ 1 peça por canal ativo; ≥ 50% dos e-mails de outreach |
| Efeito na conversão da landing | taxa visita → `/experimentar` e → `/contato`, antes vs. depois de publicar a seção de prova (tráfego de IA + FB CAPI, cross-check com Search Console) | melhora relativa mensurável em 60 dias |
| Cobertura de números agregados atualizados | % de números publicados com data de atualização ≤ 90 dias | 100% |
| Consentimentos vigentes | termos assinados ÷ peças nomeadas publicadas | 100% |

Cadência de revisão: mensal junto da revisão de KPIs por canal da spec-mãe (seção 8.3); revisão trimestral de validade das métricas e dos consentimentos.

---

## 11. Riscos

| Risco | Impacto | Mitigação |
|-------|---------|-----------|
| **Cliente revoga o consentimento** | Peça sai do ar, buraco na seção de prova | Termo prevê remoção em ≤ 7 dias úteis; manter ≥ 1 peça de reserva por segmento; seção degrada para números agregados sem quebrar o layout |
| **Métrica inflada ou indefensável** | Vira objeção do concorrente, mina confiança do lead, risco reputacional | Lastro obrigatório (cálculo + data), intervalo em vez de número cravado, recorte honesto no denominador, revisão trimestral, cliente confirma o número do case |
| **Review incentivado mal executado** | Penalidade / remoção do perfil no Capterra/G2/GetApp | Política neutra da seção 7 documentada e seguida; lista de convite = todos os elegíveis; mesmo agradecimento para todos; registro de convites para auditoria; conferir termos do vendor antes de cada campanha |
| **LGPD em foto / nome / logo** | Exposição indevida de dado pessoal ou de marca | Consentimento escrito item a item, com finalidade e controlador; sem dado sensível de paciente em nenhuma peça; screenshots de ambiente de demonstração |
| **Concentração em poucos clientes** | Programa fica refém de 2–3 clientes; se um sai, a prova despenca | Fase 0 (agregada) não depende de ninguém; pipeline contínuo de novos cases a cada trimestre; meta de diversidade por segmento |
| **Número da conta de WhatsApp usado para pedir depoimento/review em massa** | Risco de bloqueio do número transacional (spec-mãe 9.1) | Pedido de depoimento/review é 1:1, no canal que o cliente já usa com o Igor, sem disparo em massa por template; se houver volume, usar e-mail |
| **Case exposto vira alvo de crítica pública** | Concorrente ou usuário insatisfeito comenta negativamente | Escolher clientes com relação sólida (critério 4.1.4); manter o texto factual e verificável; responder com transparência |
| **Screenshot vaza PII de paciente** | Incidente LGPD | Só ambiente de demonstração; checklist de revisão de imagem antes de publicar (execução do `agente-frontend` / `agente-seo-geo`) |

---

## 12. Referências

- `docs/produto/2026-09-03-estrategia-vendas-organicas.md` — spec-mãe (canal 9, 4.1, 4.3, seção 5, 9.2).
- `docs/seo-offpage-linkbuilding-2026-06-04.md` — Pilar 1 (diretórios) e Pilar 7 (reviews e prova social).
- `docs/seo-offpage-textos-perfis-vendor-2026-07-13.md` — textos de perfil de vendor.
- Memória `project_seo_offpage` — status de cadastro em Capterra/G2/GetApp e barreiras anti-bot.
- `CLAUDE.md` §2 (posicionamento, ICP, diferenciais), §10.3.1 (WhatsApp confirmação/lembrete), §15 (roadmap).
- `docs/arquitetura-agentes.md` §5 — fronteiras dos agentes envolvidos.
</content>
</invoke>
