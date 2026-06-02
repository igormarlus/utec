# Auditoria SEO e GEO - UTecnologia

Data: 2026-06-02

## Resumo executivo

O site ja tem uma base melhor do que parece a primeira vista: rotas amigaveis, landing pages por especialidade, blog publicado, `canonical`, `og`, JSON-LD em varias paginas e `robots.txt` funcional.

Os dois problemas mais relevantes encontrados nesta revisao foram:

1. A `<head>` das paginas individuais do blog estava com HTML quebrado em [application/views/public/blog/post.php](C:/htdocs/utec/application/views/public/blog/post.php:15), o que podia invalidar `canonical` e metadados.
2. O sitemap do blog existia, mas nao estava sendo anunciado no [robots.txt](C:/htdocs/utec/robots.txt:1), reduzindo a descoberta sistematica dos posts.

Esses dois pontos ja foram corrigidos.

## O que foi ajustado agora

### 1. Correcao do HTML quebrado no post do blog

Arquivo: [application/views/public/blog/post.php](C:/htdocs/utec/application/views/public/blog/post.php:15)

Problema:
- a tag `canonical` estava sem fechamento correto
- as tags de favicon e apple-touch-icon ficaram "engolidas" dentro do `href`
- isso gerava HTML invalido no topo da pagina

Impacto possivel:
- interpretacao errada do `canonical`
- metadados incompletos para Google e redes sociais
- sinais confusos de indexacao

### 2. Exposicao do sitemap do blog no robots

Arquivo: [robots.txt](C:/htdocs/utec/robots.txt:1)

Ajustes feitos:
- adicionado `Sitemap: https://utecnologia.com.br/sitemap-blog.xml`
- adicionado bloco especifico para `OAI-SearchBot`

## Sobre o "404 no Google, mas a URL abre normal"

Com base no codigo atual, o cenario mais provavel nao e "a rota esta quebrada hoje". A URL abre no site e a rota do blog esta correta em [application/config/routes.php](C:/htdocs/utec/application/config/routes.php:91).

As hipoteses mais provaveis sao:

1. O Google rastreou a pagina quando a `<head>` estava quebrada e registrou um problema antigo.
2. O Search Console esta reportando uma variacao da URL, nao exatamente a URL que voce abriu no navegador.
3. A URL entrou no relatorio por descoberta em sitemap, link antigo ou variante de URL.
4. O problema pode ter sido transitorio no momento do crawl do Googlebot.

Observacao importante:
- quando o Search Console fala em `404`, o ideal e olhar a URL exata no `Inspecao de URL` e em `Descoberta`.
- o proprio Google documenta que, se a URL foi enviada para indexacao, voce deve verificar de onde ela foi descoberta e atualizar sitemap/redirecionamento se necessario.

## Diagnostico tecnico do SEO atual

### Pontos positivos

- `base_url` definido com HTTPS em [application/config/config.php](C:/htdocs/utec/application/config/config.php:26)
- rotas limpas e amigaveis
- blog com slugs legiveis
- `canonical` presente nas paginas de blog e landing pages
- JSON-LD em blog e em varias paginas comerciais
- `robots.txt` nao bloqueia o blog
- existe `sitemap.xml` e existe `sitemap-blog.xml`
- paginas por especialidade ja foram iniciadas, o que ajuda muito a estrategia semantica

### Pontos de risco / melhoria

#### 1. Descoberta do blog dependia pouco do sitemap

Antes desta revisao, o `robots.txt` so apontava para `sitemap.xml`, nao para `sitemap-blog.xml`.

Risco:
- posts novos podem demorar mais para serem descobertos
- cobertura de indexacao menos consistente

#### 2. Sitemaps separados sem um indice central

Hoje existem:
- [sitemap.xml](C:/htdocs/utec/sitemap.xml)
- [sitemap-blog.xml](C:/htdocs/utec/sitemap-blog.xml)

Melhoria recomendada:
- criar um sitemap index central listando os dois arquivos
- ou incluir o blog no sitemap principal

#### 3. Schema do blog pode ficar mais forte

Hoje o post usa `BlogPosting`, o que e bom. Mas ainda pode melhorar com:
- `BreadcrumbList` em script separado
- `Person` ou `Organization` mais consistente para autor
- `image`, `datePublished`, `dateModified` e `mainEntityOfPage` sempre completos

#### 4. Falta uma estrategia editorial por cluster

O blog existe, mas ainda precisa funcionar como suporte claro para:
- odontologia
- psicologia
- fisioterapia
- medicos
- clinicas multiprofissionais

Hoje a estrutura comercial esta melhor do que a estrutura de conteudo satelite.

#### 5. Falta padrao forte de interlinking

Toda pagina de blog com intencao comercial deveria linkar para a landing mais relacionada, por exemplo:
- artigo odontologico -> `/software-para-clinicas-odontologicas`
- artigo de psicologia -> `/sistema-para-psicologos`
- artigo de prontuario -> `/sistema-prontuario-eletronico`

#### 6. Possivel inconsistencias de host canonico

O sistema esta configurado para `https://utecnologia.com.br/`, mas o `.htaccess` atual so forca HTTPS e nao forca `www` ou `sem www`.

Melhoria recomendada:
- decidir se o host oficial e `utecnologia.com.br`
- redirecionar qualquer variante para esse host

#### 7. Conteudo potencialmente fino em alguns artigos

Sem revisar cada artigo individualmente no banco, o principal risco do blog e:
- artigos curtos demais
- introducoes genericas
- pouco diferencial pratico
- falta de experiencia real de consultorio/clinica

Isso e especialmente importante porque o nicho e YMYL/saude.

## Prioridades de SEO

### Prioridade alta

1. Monitorar no Search Console a URL exata que aparece como `404`.
2. Solicitar nova indexacao dos posts mais importantes apos a correcao da `<head>`.
3. Criar sitemap index ou consolidar todos os sitemaps.
4. Revisar os 5-10 posts mais importantes para:
   - title
   - meta description
   - H1 unico
   - H2 com perguntas reais
   - links internos
   - CTA coerente com a landing certa
5. Criar uma malha de links entre blog e landings por especialidade.

### Prioridade media

1. Adicionar autor mais explicito nos artigos.
2. Padronizar bloco "revisado/atualizado em".
3. Revisar imagens de capa com `alt` e dimensoes consistentes.
4. Criar paginas comerciais faltantes para:
   - nutricionistas
   - clinicas medicas
   - outras especialidades com demanda

### Prioridade estrutural

1. Medir Core Web Vitals das paginas principais.
2. Revisar consolidacao de host canonico.
3. Automatizar geracao de sitemap do blog com base no banco.

## Estrategia editorial recomendada

### Cluster 1: Odontologia

Landing principal:
- `/software-para-clinicas-odontologicas`

Artigos satelite:
- Como escolher um software para clinicas odontologicas
- Agenda para dentistas: como reduzir faltas e encaixes perdidos
- Prontuario odontologico digital: o que uma clinica pequena precisa
- Planilha ou software odontologico: quando a troca compensa
- Como organizar retornos e historico de pacientes em clinica odontologica

### Cluster 2: Psicologia

Landing principal:
- `/sistema-para-psicologos`

Artigos satelite:
- Prontuario eletronico para psicologos
- Como organizar agenda de psicologo
- Sistema para clinica de psicologia: o que avaliar

### Cluster 3: Fisioterapia

Landing principal:
- `/sistema-para-clinica-de-fisioterapia`

Artigos satelite:
- Como organizar retornos em fisioterapia
- Agenda para fisioterapeutas
- Sistema para fisioterapia em clinica com varios profissionais

### Cluster 4: Prontuario / Operacao

Landing principal:
- `/sistema-prontuario-eletronico`

Artigos satelite:
- Como organizar prontuarios eletronicos
- Como padronizar evolucao de pacientes
- LGPD para clinicas pequenas

## GEO: o que vale fazer agora

Aqui a linha mais segura e: nao existe uma "meta tag magica" de GEO. O que funciona melhor hoje e tornar o conteudo facil de citar, verificar e resumir por sistemas de busca com IA.

### O que implementar ja

1. Permitir crawl de buscadores de IA relevantes.
   - Ja deixamos `OAI-SearchBot` explicito no `robots.txt`.
   - Decidir depois se quer permitir `GPTBot` para treino, o que e uma decisao separada.

2. Produzir paginas com resposta objetiva logo no topo.
   - Um bloco inicial que responde em 2-4 frases: "o que e", "para quem serve", "quando usar", "quanto custa".

3. Usar perguntas e respostas reais.
   - FAQ bem escrita ajuda tanto Google quanto sistemas que sintetizam resposta.

4. Estruturar entidades com clareza.
   - produto: UTecnologia Saude
   - publico: dentistas, psicologos, fisioterapeutas, medicos
   - casos de uso: agenda, prontuario, equipe, anexos, historico

5. Reforcar sinais de confianca.
   - autor
   - data de publicacao
   - data de atualizacao
   - pagina sobre
   - pagina contato
   - politica de privacidade
   - declaracoes honestas sobre limites do produto

6. Criar conteudo citavel.
   - listas
   - comparativos
   - tabelas
   - definicoes curtas
   - passos operacionais

7. Usar links internos muito claros.
   - artigos -> landing principal
   - landing -> artigos de apoio
   - comparativos -> pagina principal da especialidade

8. Manter consistencia de naming.
   - usar sempre os mesmos nomes principais: "UTecnologia Saude", "software para clinicas odontologicas", "prontuario odontologico online", etc.

### O que nao vale superestimar agora

1. `llms.txt`
   - pode ser interessante para experimentacao, mas hoje nao e base confiavel de descoberta.
   - eu trataria como opcional, nao prioridade.

2. Gerar conteudo genérico so porque "IA gosta"
   - isso costuma piorar SEO e GEO ao mesmo tempo.

3. Criar dezenas de paginas quase iguais
   - o ideal e especializacao real por publico e intencao, nao troca mecanica de keyword.

## GEO: playbook pratico para o UTecnologia

### Formato ideal de pagina comercial

1. Resposta curta no topo
2. Lista de recursos
3. Para quem serve
4. Limitacoes honestas
5. FAQ
6. Comparacao com alternativa conhecida
7. CTA

### Formato ideal de artigo

1. Responder a pergunta principal logo no inicio
2. Trazer 3-7 criterios objetivos
3. Mostrar cenarios de consultorio ou clinica
4. Fechar com recomendacao clara
5. Linkar para a landing certa

### Temas bons para IA citar

- definicoes
- comparativos
- checklists
- vantagens e desvantagens
- custos
- "como escolher"
- "quando vale a pena trocar"
- "quais recursos importam"

## Proximos passos recomendados

### Fase 1 - ja

1. Reenviar indexacao dos posts principais.
2. Conferir no Search Console a URL exata marcada como `404`.
3. Validar se o status muda apos a correcao da `<head>`.

### Fase 2 - esta semana

1. Criar sitemap index.
2. Revisar os artigos mais importantes do blog.
3. Padronizar interlinking entre blog e landings.
4. Criar mais 2-3 artigos no cluster de odontologia.

### Fase 3 - este mes

1. Criar paginas comerciais faltantes por especialidade.
2. Criar comparativos adicionais.
3. Medir Search Console por cluster de keyword.
4. Medir quais paginas aparecem em respostas de IA com mais frequencia.

## Referencias oficiais usadas nesta auditoria

Google:
- Structured data: https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- Missing pages / indexing: https://support.google.com/webmasters/answer/7474347?hl=en
- 404 handling: https://support.google.com/webmasters/answer/2445990?hl=en

OpenAI:
- Crawlers e `OAI-SearchBot`: https://developers.openai.com/api/docs/bots

## Observacao final

Minha leitura e que o projeto esta numa fase muito boa para crescer em SEO e GEO porque ja tem:
- produto claro
- nicho definido
- varias especialidades possiveis
- arquitetura de landing pages iniciada

O maior ganho agora vem menos de "mais paginas aleatorias" e mais de:
- consolidar rastreabilidade
- fortalecer interlinking
- criar clusters por especialidade
- produzir conteudo realmente citavel e util para mecanismos de busca com IA
