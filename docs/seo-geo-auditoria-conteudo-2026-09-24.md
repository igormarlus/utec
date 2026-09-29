# Auditoria de conteúdo SEO/GEO — UTecnologia Saúde

**Data:** 2026-09-24  
**Escopo:** comparar os planos e auditorias de SEO/GEO com as páginas públicas, as funcionalidades WhatsApp documentadas e os dois manuais em PDF.  
**Limite desta revisão:** análise dos arquivos locais. Não houve validação de produção, consulta ao Search Console/GA4 nem conferência dos preços ativos no banco.

## Resumo

A base de SEO técnico e conteúdo para WhatsApp está implementada em boa parte. A página dedicada descreve confirmação, lembrete, chatbot por perfil e remarcação/cancelamento automáticos, incluindo regras e casos encaminhados à equipe. O hub também está ligado à home, a landings e a artigos do blog.

As principais lacunas encontradas são de consistência e medição:

1. Os dois PDFs em `docs/` descrevem notificações e comunicação com pacientes como funcionalidades futuras, embora os documentos operacionais do projeto registrem confirmação, lembretes e chatbot por WhatsApp já implantados.
2. A chamada de WhatsApp na home é mais genérica e não explicita a remarcação/cancelamento automáticos que a página dedicada já explica.
3. O monitoramento atual mede visitas e conversões atribuídas a fontes de IA; não mede menções e citações da marca nas respostas dessas ferramentas.
4. Algumas páginas comerciais mantêm preços escritos diretamente no HTML. A home, por sua vez, lê planos do banco; valores e condições dessas páginas precisam ser conferidos antes de qualquer atualização.
5. O ledger SEO/GEO não inclui os conteúdos do cluster WhatsApp publicados em setembro sobre chatbot, agenda e remarcação.

## Inventário e evidências

### 1. Manuais PDF

Arquivos examinados:

- `docs/utec-saude-manual-profissional.pdf` — 6 páginas, versão editorial gerada em 06/05/2026.
- `docs/manual-uso-sistema.pdf` — 6 páginas, conteúdo muito semelhante ao manual profissional.

Os dois ainda descrevem o paciente como “futuramente” tendo notificações/comunicação. Também chamam notificações de “base” ou listam a adição de notificações automatizadas como próximo passo. Isso contradiz o estado atual documentado em `CLAUDE.md` §10.3.1: confirmação, lembrete automático, respostas por botão, avisos à equipe e chatbot com consulta de agenda, cancelamento e remarcação.

**Recomendação:** substituir os dois materiais por uma versão controlada e datada, ou escolher um manual principal e arquivar o duplicado. Atualizar o fluxo WhatsApp e registrar suas condições reais (telefone cadastrado; antecedência mínima de 24 horas e grade do profissional para remarcação automática; outros casos seguem para a equipe). Revisar também os trechos que descrevem o acesso de pacientes como futuro.

Antes de publicar, localizar ou definir o arquivo-fonte editável do PDF. A busca textual local não encontrou um gerador/fonte associado aos nomes dos dois PDFs.

### 2. Home e landings

`application/views/index-front.php` já apresenta a confirmação por WhatsApp no menu e um card na seção de recursos. O card explica que pacientes e profissionais recebem opções conforme o perfil, mas não menciona diretamente lembretes, notificações à equipe, remarcação ou cancelamento automáticos. A descrição estruturada da home também permanece resumida a confirmação e opções por perfil.

Em contraste, `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php` já apresenta:

- confirmação ao criar agendamento e lembrete automático;
- atualização do estado do agendamento pela resposta do paciente;
- consulta, remarcação e cancelamento pelo chatbot;
- regra de 24 horas, disponibilidade configurada e fallback para a equipe;
- identificação do perfil e restrições de telefone duplicado;
- FAQs visíveis e JSON-LD correspondente.

`application/views/public/seo/sistema-para-clinicas.php` já descreve remarcação/cancelamento pelo chatbot e consulta da agenda de hoje/amanhã pela equipe. `application/views/public/seo/casos-de-uso.php` traz cenários de WhatsApp e informa claramente que são ilustrativos, não depoimentos de clientes reais.

**Recomendação:** harmonizar a descrição curta da home com a página dedicada, usando um resumo e link para os detalhes. Manter as condições e limites na página dedicada; não reproduzir todo o FAQ nas demais landings.

**Validação comercial pendente:** a home exibe os planos a partir de dados do banco, mas várias landings imprimem valores fixos no código. Conferir valores, limites, política de cancelamento e disponibilidade do trial com os planos ativos antes de alterar essas páginas. O preço não foi consultado no banco nesta auditoria.

**Validação de prova social pendente:** os cenários da página de casos estão explicitamente identificados como ilustrativos, o que é adequado. Os depoimentos exibidos na home devem ser confirmados como depoimentos reais autorizados antes de serem usados como evidência externa, cases ou material para diretórios.

### 3. SEO técnico e rastreamento de IA

`robots.txt` aponta para `sitemap-index.xml`, `sitemap.xml` e `sitemap-blog.xml`, e possui grupo específico permitindo `OAI-SearchBot`. O índice agrega os sitemaps principal e do blog. Isso cobre uma parte importante da descoberta técnica; ainda é necessário verificar no Search Console e nos logs se as URLs são rastreadas e indexadas.

O bloco geral de `robots.txt` contém `Disallow: /docs/`. Portanto, os PDFs em `docs/` não são acessíveis ao Googlebot para rastreamento enquanto estiverem nesse caminho. O grupo específico `OAI-SearchBot` tem `Allow: /`; o bloqueio do grupo geral não deve ser descrito como bloqueio universal a todos os crawlers. Ainda assim, os PDFs não constam nos sitemaps examinados nem têm uma página pública de apresentação dedicada.

O tracking implantado (`ai_referrals`, conversões e painel de tráfego de IA) mede visitas atribuídas a origens de IA e conversões no site. Isso não mede impressões, menções ou citações sem clique. O próprio `docs/monitoramento_geo_ia.md` separa essa medição do Brand Radar, cuja implementação aparece como fase futura.

**Recomendação:** primeiro estabelecer uma planilha simples de visibilidade com um conjunto estável de perguntas em português, verificadas periodicamente em cada produto de IA; registrar menção, URL citada, concorrentes citados e data. Automatizar apenas se a amostragem manual mostrar utilidade. Isso é uma amostra de visibilidade, não uma reprodução completa das respostas entregues a todos os usuários.

### 4. Conteúdo SEO/GEO e documentação de operação

O ledger (`docs/seo-geo-agente-ledger.md`) lista a landing de WhatsApp e os sete artigos de confirmação/lembrete publicados em setembro. O sitemap do blog contém ainda um cluster posterior com páginas sobre automação WhatsApp, LGPD, remarcação, agenda do dia e chatbot. Esses itens não aparecem no inventário do ledger, que ainda sugere avaliar alguns deles como futuros.

**Recomendação:** atualizar o ledger e o relatório de SEO/GEO com o estado de setembro, registrar as novas URLs e substituir tarefas já concluídas. Auditar os artigos recém-publicados para conferir consistência com o produto e links de ida e volta para a landing principal.

### 5. Dados estruturados e recursos de busca com IA

As páginas examinadas usam `SoftwareApplication`, `BreadcrumbList` e `FAQPage` em diferentes combinações. O FAQ visível deve continuar sendo útil ao leitor e refletir exatamente o JSON-LD. Porém, a documentação de atualizações do Google registra que o resultado enriquecido de FAQ deixou de ser exibido em 7 de maio de 2026. Assim, não tratar `FAQPage` como vantagem garantida de resultado visual ou como atalho de GEO.

As recomendações do Google para AI Overviews/AI Mode continuam baseadas em fundamentos de Search: rastreabilidade, conteúdo útil disponível em texto, links internos e marcação coerente com o conteúdo visível. Não há schema especial de IA que garanta inclusão. A OpenAI recomenda permitir `OAI-SearchBot` para que páginas públicas possam ser consideradas para resumos e citações; isso também não garante aparição.

## Próximos passos em ordem

### Prioridade 1 — corrigir a fonte institucional desatualizada

1. Encontrar/criar a fonte editável dos PDFs.
2. Consolidar os dois manuais em uma versão atual, com data e status das funcionalidades.
3. Tornar essa versão acessível por uma página HTML pública com resumo e link para download; avaliar colocar o arquivo público fora de `/docs/` e incluí-lo na descoberta/sitemap, depois de aprovado.

### Prioridade 2 — unificar a comunicação do WhatsApp

1. Atualizar o card e o texto estruturado da home para resumir o benefício atual e apontar para a landing.
2. Preservar na landing dedicada os limites, fallback humano e requisitos de operação.
3. Conferir preços e condições nas landings contra os planos ativos antes de qualquer mudança de copy.

### Prioridade 3 — completar a medição GEO

1. Criar o primeiro conjunto de 10–20 perguntas comerciais/informacionais.
2. Registrar plataforma, idioma/região quando visível, presença da marca, URLs citadas, concorrentes e data.
3. Rodar a mesma amostra mensalmente e relacionar mudanças a citações, visitas de IA e conversões já registradas.

### Prioridade 4 — atualizar o controle editorial

1. Atualizar `seo-geo-agente-ledger.md` e o relatório de setembro.
2. Revisar os artigos do novo cluster WhatsApp e os links internos.
3. Confirmar a origem e autorização dos depoimentos da home antes de expandir seu uso como prova social externa.

## Fontes oficiais consultadas

- [Google Search: recursos de IA e seu site](https://developers.google.com/search/docs/appearance/ai-features) — fundamentos de SEO, rastreabilidade, conteúdo textual e marcação coerente; não existe schema especial obrigatório para AI Overviews/AI Mode.
- [Google Search: histórico de atualizações](https://developers.google.com/search/updates) — remoção dos resultados enriquecidos de FAQ em maio de 2026.
- [OpenAI: FAQ para editores e desenvolvedores](https://help.openai.com/en/articles/12627856-publishers-and-developers-faq) — acesso do `OAI-SearchBot` e rastreamento de referências com `utm_source=chatgpt.com`.

## Arquivos locais principais

- `CLAUDE.md` §10.3.1 — estado e implantação das funcionalidades WhatsApp.
- `docs/monitoramento_geo_ia.md` — distinção entre tráfego atribuído a IA e monitoramento de citações.
- `docs/seo-geo-auditoria-2026-06-02.md` — recomendações SEO/GEO anteriores; alguns itens já foram implementados.
- `docs/seo-geo-agente-ledger.md` — inventário editorial que precisa de atualização.
- `application/views/index-front.php` — home, card WhatsApp, prova social e preços dinâmicos.
- `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php` — página principal de WhatsApp e FAQ.
- `application/views/public/seo/sistema-para-clinicas.php` — integração do WhatsApp na landing geral.
- `application/views/public/seo/casos-de-uso.php` — cenários ilustrativos e recursos atuais.
- `robots.txt`, `sitemap-index.xml`, `sitemap.xml` e `sitemap-blog.xml` — instruções de rastreamento e inventário de URLs.
