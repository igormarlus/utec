# SEO/GEO — Confirmação e lembrete de consulta por WhatsApp (isca de leads)

Data: 2026-08-31
Status: proposta validada em conversa, aguardando revisão final do usuário

## 1. Objetivo

Criar uma frente de conteúdo orgânico que atrai clínicas, consultórios e profissionais de saúde
que procuram **confirmar, lembrar e notificar agendamentos por WhatsApp**, usando o recurso de
confirmação/lembrete de agendamento do UTecnologia Saúde como isca para o trial de 30 dias.

É a primeira rodada de um tema novo (não é especialidade nem comparativo). Escopo desta rodada:
**1 landing dedicada + 3 artigos de blog** (o 3º artigo é opcional / corte natural se houver
pouco fôlego editorial).

## 2. Critério de sucesso

Nos 30 a 60 dias após a publicação, esta frente deve gerar:

- cliques para `/experimentar` vindos da nova landing e dos 3 artigos
- cobertura orgânica dos clusters "mensagem de confirmação de consulta", "lembrete de consulta"
  e "sistema de agendamento com whatsapp"
- citação em respostas assistidas por IA para perguntas de "como confirmar consulta pelo WhatsApp"
  e "como reduzir faltas de pacientes"
- tráfego interno dos artigos para a landing e da landing para o trial

Indicador principal: geração de oportunidade comercial (trial/contato), não volume de sessões.

## 3. Contexto

### 3.1 Recurso que já existe (CLAUDE.md 10.3.1)

- Ao criar um agendamento com o checkbox "Enviar confirmação pelo WhatsApp", o sistema dispara
  um template aprovado da Meta (header de imagem + 2 botões quick-reply confirmar/cancelar) e
  grava `whatsapp_notificacoes`.
- O paciente responde pelo botão → o sistema responde por texto, atualiza `agendamentos.status`
  (cancelar → 3, reconfirmar → 0) e gera avisos internos (`notificacoes_usuarios`).
- A agenda (`adm/atendimento`) mostra a etiqueta "Confirmado / Cancelado via WhatsApp"; há sino
  de avisos não lidos no topo.
- Webhook com validação HMAC, transição idempotente, limite trial/free de 3 disparos por tenant
  sem assinatura ativa.

### 3.2 Recurso que será construído em seguida (dependência)

O usuário decidiu posicionar o **lembrete automático** como recurso ativo nas páginas. Ele será
entregue logo após esta rodada de conteúdo, com o seguinte comportamento assumido pelo texto:

- **cron de hora em hora** varre agendamentos futuros e dispara o mesmo template aprovado para
  janelas configuráveis (**D-1** e/ou **no mesmo dia**);
- reaproveita todo o stack atual: `whatsapp_notificacoes`, webhook, resposta automática por
  texto, avisos internos, etiqueta na agenda, política de limite trial;
- confirmação/cancelamento do paciente cai na agenda automaticamente, igual ao fluxo de hoje.

O conteúdo desta rodada deve descrever exatamente esse comportamento — nada além dele.

### 3.3 Disciplina herdada da estratégia Fase 2

- núcleo editorial menor, mais forte e mais próximo da decisão;
- não criar URL nova sem intenção claramente diferente;
- manter promessas estritamente alinhadas ao produto;
- toda página estratégica com resposta curta no topo, "para quem serve", FAQ com dúvida real de
  comprador e pelo menos uma limitação honesta.

## 4. Pesquisa de keywords (Google Autocomplete, pt-BR / gl=br, 2026-08-31)

### 4.1 Sinal forte — intenção informacional (material de blog)

| Cluster | Variações retornadas |
|---|---|
| `mensagem de confirmação de consulta` | + pelo whatsapp, + modelo, + automática, + odontológica, + médica, + "de presença", + "de não confirmação", + "de agendamento de consulta", + "como fazer mensagem de confirmação de consulta no whatsapp" |
| `lembrete de consulta` | + whatsapp, + mensagem, + modelo, + agendada, + médica, + odontológica, + "lembrete de confirmação de consulta" |
| `mensagem de lembrete de consulta` | + odontológica, + modelo |

### 4.2 Sinal forte — intenção de ferramenta (material de landing)

| Cluster | Variações retornadas |
|---|---|
| `sistema de agendamento com whatsapp` | + grátis, + "via whatsapp", + "integrado ao whatsapp" |
| `whatsapp para clínicas` | + chatbot, + "automação de whatsapp para clínicas" |

### 4.3 Sinal moderado

- `confirmação de consulta por whatsapp` / `confirmação de consulta pelo whatsapp modelos`
- `agenda online com whatsapp` / `agenda online whatsapp`
- `sistema de confirmação de consultas`

### 4.4 Sem sinal de autocomplete (usar só no corpo do texto, nunca como slug/H1)

`reduzir faltas de pacientes`, `diminuir faltas no consultório`, `no-show`,
`disparo de whatsapp para pacientes`, `software confirmação de consulta`,
`confirmação de consulta automática`, `mensagem automática para paciente`,
`software para enviar lembrete de consulta`, `sistema para clínica com whatsapp`.

### 4.5 Ruído de vertical vizinha

"salão de beleza" aparece em `confirmação de agendamento via whatsapp salão de beleza`; chatbot
para "estética" e "veterinária" aparece no entorno de `chatbot para clínica`. Todo o conteúdo
fica ancorado em saúde/clínica/consultório. O espaço de "chatbot / omnichannel" é concorrido —
esta é uma jogada de isca de recurso, não disputa com vendor de chatbot.

## 5. Decisão estratégica

Publicar **1 landing** + **cluster de 3 artigos** que alimentam a landing.

- Confirmação (no agendamento) e lembrete (cron D-1 / mesmo dia) são **a mesma feature e a mesma
  intenção de compra** → **uma única URL**, com H1/title cobrindo os dois termos. Não se cria
  `/lembrete-de-consulta-por-whatsapp` como página separada (violaria o guardrail de intenção).
- `sistema-de-agendamento-com-whatsapp` teve sinal de compra mais forte, mas induz a leitura
  "paciente agenda sozinho pelo WhatsApp", que **não** é o recurso. Fica como termo secundário
  trabalhado no corpo e no `<title>`, não como slug.

## 6. Arquitetura de arquivos

Segue o padrão já consolidado do projeto (idêntico às 22 landings SEO existentes).

| Arquivo | Ação | O que muda |
|---|---|---|
| `application/config/routes.php` | Modificar | Nova rota `confirmacao-de-consulta-por-whatsapp` → `home/seo_confirmacao_whatsapp`, no bloco de rotas `seo_*` |
| `application/controllers/Home.php` | Modificar | Novo método `seo_confirmacao_whatsapp()` — só `$this->load->view('public/seo/confirmacao-de-consulta-por-whatsapp')` |
| `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php` | Criar | Nova landing, base visual copiada de `sistema-para-medicina-do-trabalho.php` |
| `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql` | Criar | Seed com os 3 artigos (`INSERT INTO blog_posts`, mesmas colunas de `docs/blog-posts-seed.sql`), `id_categoria` = 1 com aviso para conferir |
| `sitemap.xml` | Modificar | Nova `<url>` da landing — `changefreq monthly`, `priority 0.8`, `lastmod 2026-08-31` |
| `sitemap-blog.xml` | Modificar | 3 novas `<url>` dos slugs de blog |
| `sitemap-index.xml` | Modificar | Atualizar `lastmod` dos dois blocos `<sitemap>` para `2026-08-31` |

Rota de blog já existente: `$route['blog/(:any)'] = 'blog/post/$1'` — os artigos ficam
acessíveis em `/blog/{slug}` assim que o SQL for aplicado. Nenhuma rota nova de blog é necessária.

## 7. Anatomia da landing

Estrutura e CSS reaproveitados de `sistema-para-medicina-do-trabalho.php` (mesmo `<style>`,
mesma `topnav`, mesmo `footer`, gtag). Blocos:

1. **head** — `<title>` cobrindo os dois clusters, ex.:
   `Confirmação e Lembrete de Consulta por WhatsApp — Sistema para Clínicas | UTecnologia Saúde`.
   `meta description`, canonical `https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp`,
   OG + Twitter, favicon.
2. **hero** — eyebrow "WhatsApp para Clínicas"; H1 `Confirmação e lembrete de consulta por WhatsApp`;
   parágrafo de resposta curta (2–4 frases) do que o recurso faz; CTA "Testar 30 dias grátis" +
   "Ver planos"; `trust-line` (sem cartão, 100% online, a partir de R$ 79/mês);
   `funciona-strip` com chips: Consultório individual · Clínica com recepção · Odontologia ·
   Psicologia / Fisioterapia · Clínica com várias agendas.
3. **hero-card** (mock) — simulação de uma conversa de WhatsApp: mensagem do template com os
   botões "Confirmar" / "Cancelar" e a resposta automática do sistema. Substitui o mock de
   formulário do modelo original.
4. **bloco escuro "Como funciona"** (equivalente ao `prontuario-section`) — 3 passos:
   (a) agendou → dispara a confirmação na hora; (b) cron de hora em hora envia o lembrete D-1 /
   no dia; (c) paciente toca no botão → agenda atualiza sozinha e a recepção recebe o aviso.
5. **features-grid** (6 cards) — Confirmação no agendamento · Lembrete automático (D-1 / mesmo dia) ·
   Resposta do paciente cai na agenda · Aviso interno para a recepção · Etiqueta "Confirmado via
   WhatsApp" na agenda · Sem instalar nada / usa a API oficial da Meta.
6. **bloco "Em resumo"** — parágrafo curto citável por IA: para quem serve, o que é automático,
   o que ainda é manual.
7. **FAQ** (4–5 itens) com JSON-LD `FAQPage`. Perguntas reais de comprador + **limitações
   honestas** (ver seção 9). Ex.: "Precisa do WhatsApp Business API?"; "O paciente consegue
   reagendar pelo WhatsApp?"; "Funciona com o meu número atual?"; "Quantas mensagens posso
   enviar no teste?"; "Isso é um chatbot de atendimento?".
8. **CTA final** (`cta-wrap`) + **footer** com links internos (seção 10).
9. **3 blocos JSON-LD** — `SoftwareApplication` (url da landing, `offers` R$ 79/BRL),
   `BreadcrumbList` (Início → Sistema para Clínicas → Confirmação por WhatsApp),
   `FAQPage` (mesmas perguntas do HTML).

## 8. Os 3 artigos de blog

Todos: ~700–900 palavras, HTML em `conteudo`, `autor` "UTecnologia Saúde", `publicado` = 1,
`id_categoria` = 1 (conferir), pelo menos 1 link interno para a landing
`/confirmacao-de-consulta-por-whatsapp` e 1 para `/experimentar`.

| # | Slug | Título | Ângulo / estrutura | Cluster-alvo |
|---|---|---|---|---|
| 1 | `modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp` | Modelos de mensagem de confirmação de consulta para WhatsApp | Introdução curta + blocos de modelos prontos para copiar (consulta médica, retorno, odontologia, exame, primeira consulta), boas práticas de horário/tom, e um bloco "como automatizar isso" que leva à landing | `mensagem de confirmação de consulta` (+ modelo, whatsapp, odontológica, médica) |
| 2 | `mensagem-de-lembrete-de-consulta-quando-enviar` | Mensagem de lembrete de consulta: modelos e quando enviar | Diferença entre lembrete e confirmação; janelas recomendadas (D-1, manhã do dia); modelos prontos; erros comuns (enviar cedo demais, sem opção de resposta); bloco de automação → landing | `lembrete de consulta` + `mensagem de lembrete de consulta` |
| 3 *(opcional)* | `confirmacao-de-consulta-manual-ou-automatica` | Confirmação de consulta: fazer manual pelo WhatsApp ou automatizar? | Artigo-ponte de decisão: custo do processo manual (tempo da recepção, esquecimento, falta), quando o volume justifica automatizar, o que muda com botão de resposta que atualiza a agenda; CTA forte para trial | objeção comercial ("reduzir faltas" no corpo, sem virar slug) |

Se cortar o #3: publicar só 1 e 2 e ajustar `sitemap-blog.xml` e o `.sql` para 2 registros.

## 9. Posicionamento e honestidade

**Pode afirmar como recurso ativo:**

- confirmação disparada no momento do agendamento;
- lembrete automático em D-1 e/ou no mesmo dia via cron de hora em hora;
- botão de confirmar/cancelar que atualiza o status na agenda automaticamente;
- resposta automática por texto ao paciente;
- aviso interno para quem marcou a consulta e para o profissional;
- etiqueta de status na agenda (desktop e mobile);
- uso da API oficial da Meta (WhatsApp Cloud API).

**Não afirmar (entra como limitação honesta na FAQ / no corpo):**

- o paciente **não** reagenda sozinho pelo WhatsApp (só confirma ou cancela);
- **não** é chatbot de atendimento / triagem / dúvidas livres;
- sem canal de SMS ou ligação de voz — o canal é WhatsApp;
- sem integração com Google Agenda / Outlook;
- envio no teste limitado pela política atual (3 disparos por tenant sem assinatura ativa);
- template precisa seguir os modelos aprovados pela Meta (não é texto 100% livre no disparo).

**Gate de publicação:** a landing e os artigos podem ser escritos e revisados agora, mas a
**entrada nos sitemaps e a divulgação só acontecem quando o cron de lembrete estiver no ar**.
Até lá, as afirmações de "lembrete automático D-1" descrevem recurso ainda não publicado.
Alternativa aceita pelo usuário: seguir com C+B assumindo a entrega próxima.

## 10. Interlinking

- **Landing → trial:** CTA do hero, CTA final, botão da nav.
- **Landing → landings de decisão:** footer com links para `sistema-para-clinicas`,
  `sistema-para-clinica-medica`, `sistema-para-dentistas` (odontologia teve sinal forte no
  cluster), `sistema-prontuario-eletronico`.
- **Artigos → landing:** cada artigo com 1–2 âncoras para `/confirmacao-de-consulta-por-whatsapp`
  no bloco de automação.
- **Artigos entre si:** #1 ↔ #2 (confirmação vs lembrete), #3 puxa #1 e #2.
- **Landings existentes → nova landing (fase seguinte, fora desta rodada):** avaliar um link em
  `sistema-para-clinicas` e `sistema-para-dentistas` depois que a página provar tração.

## 11. Validação

Sem suíte automatizada para páginas públicas — mesma abordagem das rodadas SEO anteriores:

- `php -l` em `routes.php`, `Home.php` e na nova view;
- inspeção visual do `.sql` (um `INSERT`, N blocos `(...)`, vírgula entre itens, `;` só no último);
- smoke test manual: abrir `http://localhost/utec/confirmacao-de-consulta-por-whatsapp` e conferir
  `title`, H1, CTA, FAQ expandindo, 3 blocos JSON-LD no HTML final;
- `Get-Content -Raw` nos 3 XML de sitemap: continua bem formado, URLs novas aparecem 1x,
  `lastmod 2026-08-31` só nas entradas desta rodada;
- revisão de diff.

## 12. Fora de escopo desta rodada

- Implementar o cron de lembrete automático (projeto de produto separado, vem logo depois).
- Aplicar o `.sql` no banco (o usuário roda manualmente no phpMyAdmin).
- `git commit` / `push` / deploy FTP.
- Segunda landing por termo ("lembrete", "agendamento com whatsapp").
- Blocos de "confirmação por WhatsApp" nas landings existentes (fase seguinte).
- Retomar a expansão por especialidade (dermatologia etc. — item separado do ledger).

## 13. Riscos e mitigação

| Risco | Mitigação |
|---|---|
| Publicar antes do cron existir → lead frustrado no trial | Gate de sitemap/divulgação atrelado ao cron no ar (seção 9) |
| Canibalizar `sistema-para-clinicas` / futura página "agendamento com whatsapp" | URL única de intenção clara; termo de "agendamento" fica só no corpo/title |
| Conteúdo de "modelos de mensagem" virar genérico e não converter | Cada artigo com bloco de automação + CTA e âncora para a landing (padrão "ponte" da Fase 2) |
| Concorrência de vendors de chatbot | Não competir nesse termo; foco em "confirmação/lembrete de consulta" e no diferencial do botão que atualiza a agenda |
| Espaço "sistema de agendamento com whatsapp" ainda não auditado pelo monitor | Registrar no ledger para varredura de concorrentes na próxima rodada do agente |

## 14. Sequência de execução

1. Rota + método no `Home.php` (`php -l`).
2. View da landing a partir da base de `sistema-para-medicina-do-trabalho.php` (`php -l`, smoke test).
3. `.sql` com os 3 artigos (revisão visual).
4. Sitemaps (`sitemap.xml`, `sitemap-blog.xml`, `sitemap-index.xml`) — **só quando o cron estiver
   pronto para ir ao ar**, ou conforme decisão do usuário de antecipar.
5. Atualizar `docs/seo-geo-agente-ledger.md`: nova frente de conteúdo, keywords testadas nesta
   rodada, e nota para auditar concorrentes de "agendamento com whatsapp".
6. Revisão do usuário → `git add` / `commit` (feito pelo usuário ou sob pedido explícito).
