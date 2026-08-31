# SEO/GEO Leads Phase 1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Entregar a primeira onda do roadmap SEO/GEO orientado à conversão com dois comparativos novos, reforço da página `software-para-medicos`, três artigos comerciais de objeção/decisão e atualização dos sitemaps públicos.

**Architecture:** A implementação usa o padrão já consolidado do projeto: rotas em `application/config/routes.php`, métodos simples em `Home.php`, views PHP estáticas em `application/views/public/seo/` e publicação de artigos por SQL seed/manual import. Como o projeto não possui suíte automatizada dedicada para essas páginas públicas, a validação será feita com `php -l`, revisão de diff e smoke test manual das URLs locais.

**Tech Stack:** PHP 7, CodeIgniter 3, HTML/CSS inline nas views, SQL para `blog_posts`, XML de sitemap, PowerShell, git.

---

## Mapa de arquivos

| Arquivo | Ação | O que muda |
|---------|------|-----------|
| `application/config/routes.php` | Modificar | Adicionar rotas públicas para `alternativa-shosp` e `alternativa-clinica-nas-nuvens` |
| `application/controllers/Home.php` | Modificar | Adicionar métodos `seo_alternativa_shosp()` e `seo_alternativa_clinica_nuvens()` |
| `application/views/public/seo/alternativa-shosp.php` | Criar | Nova landing comparativa orientada a migração/decisão |
| `application/views/public/seo/alternativa-clinica-nas-nuvens.php` | Criar | Nova landing comparativa orientada a migração/decisão |
| `application/views/public/seo/software-para-medicos.php` | Modificar | Reforçar GEO + conversão com bloco-resumo, objeções e links internos |
| `docs/seo-geo-agente-blog-fase-1-2026-08-20.sql` | Criar | Seed com 3 artigos comerciais novos |
| `sitemap.xml` | Modificar | Incluir as novas URLs públicas de comparativos |
| `sitemap-blog.xml` | Modificar | Incluir os slugs novos de blog publicados nesta fase |
| `sitemap-index.xml` | Modificar | Atualizar `lastmod` após mudanças em `sitemap.xml` e `sitemap-blog.xml` |

## Task 1: Expor as novas rotas públicas de comparativos

**Files:**
- Modify: `application/config/routes.php`
- Modify: `application/controllers/Home.php`

- [ ] **Step 1: Confirmar que as rotas ainda não existem**

Run:
```powershell
rg -n "alternativa-shosp|alternativa-clinica-nas-nuvens" application/config/routes.php application/controllers/Home.php
```

Expected:
```text
sem resultados
```

- [ ] **Step 2: Adicionar as rotas em `application/config/routes.php`**

Localize o bloco:
```php
$route['alternativa-feegow']               = 'home/seo_alternativa_feegow';
$route['alternativa-odontoclinic']         = 'home/seo_alternativa_odontoclinic';
$route['sistema-gratuito-para-clinicas']   = 'home/seo_sistema_gratuito';
```

Substitua por:
```php
$route['alternativa-feegow']               = 'home/seo_alternativa_feegow';
$route['alternativa-odontoclinic']         = 'home/seo_alternativa_odontoclinic';
$route['alternativa-shosp']                = 'home/seo_alternativa_shosp';
$route['alternativa-clinica-nas-nuvens']   = 'home/seo_alternativa_clinica_nuvens';
$route['sistema-gratuito-para-clinicas']   = 'home/seo_sistema_gratuito';
```

- [ ] **Step 3: Adicionar os métodos em `application/controllers/Home.php`**

Localize o trecho:
```php
	public function seo_alternativa_odontoclinic()
	{
		$this->load->view('public/seo/alternativa-odontoclinic');
	}

	public function seo_sistema_gratuito()
	{
		$this->load->view('public/seo/sistema-gratuito-para-clinicas');
	}
```

Substitua por:
```php
	public function seo_alternativa_odontoclinic()
	{
		$this->load->view('public/seo/alternativa-odontoclinic');
	}

	public function seo_alternativa_shosp()
	{
		$this->load->view('public/seo/alternativa-shosp');
	}

	public function seo_alternativa_clinica_nuvens()
	{
		$this->load->view('public/seo/alternativa-clinica-nas-nuvens');
	}

	public function seo_sistema_gratuito()
	{
		$this->load->view('public/seo/sistema-gratuito-para-clinicas');
	}
```

- [ ] **Step 4: Validar sintaxe dos arquivos alterados**

Run:
```powershell
php -l application/config/routes.php
php -l application/controllers/Home.php
```

Expected:
```text
No syntax errors detected in application/config/routes.php
No syntax errors detected in application/controllers/Home.php
```

- [ ] **Step 5: Commit**

```bash
git add application/config/routes.php application/controllers/Home.php
git commit -m "feat: expor novas rotas seo de comparativos"
```

---

## Task 2: Criar a landing `alternativa-shosp`

**Files:**
- Create: `application/views/public/seo/alternativa-shosp.php`
- Reference: `application/views/public/seo/alternativa-feegow.php`

- [ ] **Step 1: Copiar a base visual da página `alternativa-feegow`**

Run:
```powershell
Copy-Item application/views/public/seo/alternativa-feegow.php application/views/public/seo/alternativa-shosp.php
```

Expected:
```text
arquivo criado sem erro
```

- [ ] **Step 2: Substituir metadados, hero e CTA principal**

No arquivo novo, troque o bloco inicial:
```php
    <title>Alternativa ao Feegow — Sistema para Clínicas com Mais Recursos | UTecnologia Saúde</title>
    <meta name="description" content="Procurando uma alternativa ao Feegow? Conheça o UTecnologia Saúde — sistema SaaS para clínicas com prontuário eletrônico, agenda inteligente e planos acessíveis. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-feegow">
```

Por:
```php
    <title>Alternativa ao Shosp — Sistema para Clínicas com Trial e Implantação Simples | UTecnologia Saúde</title>
    <meta name="description" content="Procurando uma alternativa ao Shosp? Compare um sistema para clínicas com prontuário, agenda, trial grátis e operação simples para consultórios e clínicas em crescimento.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-shosp">
```

Troque o hero:
```php
        <div class="eyebrow">Comparativo de Sistemas</div>
        <h1>Procurando uma <em>alternativa ao Feegow</em>?</h1>
        <p class="hero-text">
            O UTecnologia Saúde é um sistema SaaS para clínicas médicas com prontuário eletrônico,
            agenda inteligente e gestão de equipe — com planos acessíveis para consultórios e clínicas de todos os tamanhos.
        </p>
```

Por:
```php
        <div class="eyebrow">Comparativo de Sistemas</div>
        <h1>Procurando uma <em>alternativa ao Shosp</em>?</h1>
        <p class="hero-text">
            O UTecnologia Saúde atende clínicas e consultórios que querem um sistema online com prontuário,
            agenda, equipe e 30 dias de trial sem cartão. A proposta aqui é simplicidade operacional, onboarding rápido
            e clareza para decidir antes de contratar.
        </p>
```

- [ ] **Step 3: Ajustar tabela comparativa e FAQ para objeções reais de migração**

Na tabela, substitua as linhas genéricas por estas:
```php
                    <tr><td>Trial sem cartão de crédito</td><td class="check">✓ 30 dias</td><td class="partial">⚠ Confirmar no fornecedor</td></tr>
                    <tr><td>Plano de entrada para operação menor</td><td class="check">✓ A partir de R$ 79/mês</td><td class="partial">⚠ Avaliar plano comercial</td></tr>
                    <tr><td>Agenda + prontuário + exames no mesmo fluxo</td><td class="check">✓ Incluído</td><td class="partial">⚠ Varia por plano</td></tr>
                    <tr><td>Curva de implantação simples</td><td class="check">✓ Foco em adoção rápida</td><td class="partial">⚠ Depende do projeto de implantação</td></tr>
```

Na FAQ, substitua o bloco de perguntas pelo seguinte núcleo:
```php
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Quando faz sentido buscar uma alternativa ao Shosp?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Quando a clínica quer avaliar uma operação mais simples de implantar, testar com trial antes de assinar e comparar o custo total com o estágio atual do negócio.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O UTecnologia Saúde serve para clínica pequena ou consultório?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano de entrada foi estruturado para profissional solo e consultórios menores, sem perder a possibilidade de subir para clínica com equipe depois.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Dá para avaliar sem migrar tudo de uma vez?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. A recomendação é começar pelos pacientes ativos e pela agenda corrente. Isso reduz atrito e permite validar o sistema com segurança antes de uma migração maior.</div>
            </div>
```

- [ ] **Step 4: Atualizar os JSON-LD da página**

Substitua os trechos de `url`, `description`, breadcrumb e FAQ para apontarem para `https://utecnologia.com.br/alternativa-shosp` e para as mesmas perguntas do HTML. Use este bloco `SoftwareApplication`:
```php
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/alternativa-shosp",
  "description": "Alternativa ao Shosp com prontuário eletrônico, agenda, trial grátis e implantação simples para clínicas e consultórios.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
```

- [ ] **Step 5: Validar sintaxe e abrir a página localmente**

Run:
```powershell
php -l application/views/public/seo/alternativa-shosp.php
```

Expected:
```text
No syntax errors detected in application/views/public/seo/alternativa-shosp.php
```

Smoke test manual:
- abrir `http://localhost/utec/alternativa-shosp`
- conferir `title`, H1, CTA de trial, FAQ expandindo e schema presente no HTML final

- [ ] **Step 6: Commit**

```bash
git add application/views/public/seo/alternativa-shosp.php
git commit -m "feat: landing seo alternativa ao shosp"
```

---

## Task 3: Criar a landing `alternativa-clinica-nas-nuvens`

**Files:**
- Create: `application/views/public/seo/alternativa-clinica-nas-nuvens.php`
- Reference: `application/views/public/seo/alternativa-feegow.php`

- [ ] **Step 1: Copiar a base visual da página `alternativa-feegow`**

Run:
```powershell
Copy-Item application/views/public/seo/alternativa-feegow.php application/views/public/seo/alternativa-clinica-nas-nuvens.php
```

Expected:
```text
arquivo criado sem erro
```

- [ ] **Step 2: Trocar metadados e hero para o contexto “na nuvem”**

Substitua:
```php
    <title>Alternativa ao Feegow — Sistema para Clínicas com Mais Recursos | UTecnologia Saúde</title>
    <meta name="description" content="Procurando uma alternativa ao Feegow? Conheça o UTecnologia Saúde — sistema SaaS para clínicas com prontuário eletrônico, agenda inteligente e planos acessíveis. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-feegow">
```

Por:
```php
    <title>Alternativa ao Clínica nas Nuvens — Sistema Online para Clínicas | UTecnologia Saúde</title>
    <meta name="description" content="Compare uma alternativa ao Clínica nas Nuvens com prontuário eletrônico, agenda, trial grátis e operação 100% online para consultórios e clínicas.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-clinica-nas-nuvens">
```

Troque o conteúdo principal do hero por:
```php
        <div class="eyebrow">Comparativo de Sistemas</div>
        <h1>Buscando uma <em>alternativa ao Clínica nas Nuvens</em>?</h1>
        <p class="hero-text">
            O UTecnologia Saúde é uma opção 100% online para clínicas que querem agenda, prontuário,
            equipe e trial de 30 dias antes da decisão comercial. A proposta é ajudar a clínica a comparar
            custo, simplicidade e aderência ao fluxo real do atendimento.
        </p>
```

- [ ] **Step 3: Reescrever os blocos de prova e FAQ para a comparação certa**

Na tabela comparativa, mantenha a estrutura e substitua o núcleo por:
```php
                    <tr><td>Sistema 100% online</td><td class="check">✓ Sim</td><td class="check">✓ Sim</td></tr>
                    <tr><td>Trial grátis sem cartão</td><td class="check">✓ 30 dias</td><td class="partial">⚠ Confirmar política atual</td></tr>
                    <tr><td>Plano de entrada acessível</td><td class="check">✓ A partir de R$ 79/mês</td><td class="partial">⚠ Avaliar plano comercial</td></tr>
                    <tr><td>Fluxo de agenda + prontuário + exames</td><td class="check">✓ Integrado</td><td class="partial">⚠ Pode variar por configuração</td></tr>
```

Na FAQ, use:
```php
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Qual a principal diferença nessa comparação?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">As duas soluções seguem a lógica online. A diferença prática a validar está na combinação entre preço de entrada, simplicidade de implantação e aderência ao fluxo da sua clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Quando vale trocar de sistema online?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Quando a clínica sente atrito na rotina, dificuldade para a equipe adotar o sistema atual ou quer reduzir risco comercial testando uma alternativa com trial antes da migração total.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O UTecnologia Saúde funciona para clínica pequena?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Ele foi desenhado para consultórios e clínicas em crescimento, com possibilidade de começar pequeno e evoluir a operação sem trocar de plataforma imediatamente.</div>
            </div>
```

- [ ] **Step 4: Atualizar JSON-LD e links internos**

Use este bloco `SoftwareApplication`:
```php
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/alternativa-clinica-nas-nuvens",
  "description": "Alternativa ao Clínica nas Nuvens com prontuário eletrônico, agenda, equipe e trial grátis para clínicas e consultórios.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
```

Atualize também:
- `og:url`
- `twitter:title`
- breadcrumb item 2
- bloco FAQ JSON-LD

- [ ] **Step 5: Validar sintaxe e smoke test**

Run:
```powershell
php -l application/views/public/seo/alternativa-clinica-nas-nuvens.php
```

Expected:
```text
No syntax errors detected in application/views/public/seo/alternativa-clinica-nas-nuvens.php
```

Smoke test manual:
- abrir `http://localhost/utec/alternativa-clinica-nas-nuvens`
- conferir URL canônica, H1, tabela comparativa e FAQ

- [ ] **Step 6: Commit**

```bash
git add application/views/public/seo/alternativa-clinica-nas-nuvens.php
git commit -m "feat: landing seo alternativa ao clinica nas nuvens"
```

---

## Task 4: Reforçar `software-para-medicos.php` para GEO + conversão

**Files:**
- Modify: `application/views/public/seo/software-para-medicos.php`

- [ ] **Step 1: Inserir um bloco-resumo direto no hero**

Logo após o parágrafo principal do hero, adicione:
```php
                <p class="hero-text" style="font-size:15px;background:#ffffff;border:1px solid var(--border);border-radius:12px;padding:14px 16px;max-width:620px;">
                    <strong>Em resumo:</strong> este software médico serve melhor para consultório e clínica que querem agenda,
                    prontuário e operação online sem instalação. Se você precisa de um sistema ultraespecializado por equipamento
                    ou integração muito específica de hospital, vale validar esse ponto antes da contratação.
                </p>
```

- [ ] **Step 2: Adicionar seção “quando este software faz sentido”**

Antes da seção de FAQ, insira:
```php
<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Decisão prática</div>
        <h2>Quando este software faz sentido para o médico</h2>
        <p class="section-sub">A página precisa responder rápido para quem serve e quando não serve.</p>
        <div class="personas">
            <div class="persona-card highlight">
                <div class="persona-title">Faz sentido se você quer</div>
                <ul>
                    <li>Prontuário, agenda e exames no mesmo fluxo</li>
                    <li>Sistema online sem instalação local</li>
                    <li>Começar com trial antes de assinar</li>
                    <li>Plano para consultório e evolução para clínica</li>
                </ul>
            </div>
            <div class="persona-card">
                <div class="persona-title">Vale validar antes se você precisa</div>
                <ul>
                    <li>Integrações muito específicas fora do fluxo atual do produto</li>
                    <li>Funcionalidades hospitalares não prometidas nesta página</li>
                    <li>Campos ultraespecializados que dependem de nicho muito restrito</li>
                    <li>Processos regulatórios que exigem conferência técnica própria</li>
                </ul>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 3: Reforçar links internos de decisão**

No bloco final de CTA, substitua:
```php
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta médica grátis →</a>
```

Por:
```php
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta médica grátis →</a>
            <p style="margin-top:16px;font-size:14px;color:rgba(255,255,255,.72);">
                Compare também com <a href="<?=base_url()?>sistema-para-consultorio-medico" style="color:#fff;text-decoration:underline;">consultório médico</a>,
                <a href="<?=base_url()?>sistema-para-clinica-medica" style="color:#fff;text-decoration:underline;">clínica médica</a> e
                <a href="<?=base_url()?>alternativa-shosp" style="color:#fff;text-decoration:underline;">alternativa ao Shosp</a>.
            </p>
```

- [ ] **Step 4: Validar sintaxe e revisão manual**

Run:
```powershell
php -l application/views/public/seo/software-para-medicos.php
```

Expected:
```text
No syntax errors detected in application/views/public/seo/software-para-medicos.php
```

Smoke test manual:
- abrir `http://localhost/utec/software-para-medicos`
- conferir bloco “Em resumo”
- conferir seção “Quando este software faz sentido”
- conferir links finais para páginas relacionadas

- [ ] **Step 5: Commit**

```bash
git add application/views/public/seo/software-para-medicos.php
git commit -m "feat: reforcar geo e conversao da landing software para medicos"
```

---

## Task 5: Criar os artigos comerciais de objeção e decisão

**Files:**
- Create: `docs/seo-geo-agente-blog-fase-1-2026-08-20.sql`

- [ ] **Step 1: Criar o arquivo SQL da nova rodada**

Crie o arquivo com este cabeçalho:
```sql
-- ============================================================
-- UTecnologia Saúde — Artigos comerciais fase 1
-- Rodada: 2026-08-20
-- Importar via phpMyAdmin: banco utecnologiacom_db
-- Ajustar `id_categoria` se necessário antes de publicar
-- ============================================================
```

- [ ] **Step 2: Inserir o artigo “quanto custa um software para clínica”**

Adicione este registro:
```sql
(1,
 'Quanto custa um software para clínica? O que avaliar além do preço',
 'quanto-custa-um-software-para-clinica',
 'Entenda o que realmente pesa no custo de um software para clínica e por que olhar só a mensalidade pode levar a uma escolha ruim.',
 '<p>O preço mensal é só uma parte da decisão...</p><h2>1. Mensalidade não é custo total</h2><p>Compare implantação, curva de adoção e risco de troca futura.</p><h2>2. Plano barato pode sair caro</h2><p>Se o sistema não acompanha o crescimento da clínica, a troca vem cedo.</p><h2>3. Trial reduz risco</h2><p>Um trial real permite validar agenda, prontuário e adoção antes da assinatura.</p><p>O <a href="https://utecnologia.com.br/software-para-clinicas">software para clínicas</a> da UTecnologia Saúde oferece 30 dias de teste para essa validação prática.</p>',
 'Quanto custa um software para clínica?',
 'Veja o que avaliar no custo de um software para clínica: mensalidade, implantação, adoção da equipe e risco de troca futura.',
 'UTecnologia Saúde', 5, 1, '2026-08-20 10:00:00', '2026-08-20 10:00:00')
```

- [ ] **Step 3: Inserir o artigo “como migrar da planilha para um sistema clínico”**

Adicione este registro, separado por vírgula do anterior:
```sql
(1,
 'Como migrar da planilha para um sistema clínico sem travar a rotina',
 'como-migrar-da-planilha-para-sistema-clinico',
 'Um passo a passo simples para clínicas e consultórios saírem da planilha com menos atrito e mais segurança operacional.',
 '<p>Migrar da planilha não precisa ser um projeto gigante...</p><h2>1. Comece pela agenda atual</h2><p>O primeiro ganho é operacional: parar de perder informação do dia.</p><h2>2. Cadastre primeiro os pacientes ativos</h2><p>Nem todo histórico precisa entrar no dia um.</p><h2>3. Teste com trial antes da mudança maior</h2><p>Validar a rotina reduz resistência da equipe.</p><p>Se quiser comparar esse cenário na prática, veja o <a href="https://utecnologia.com.br/sistema-para-clinicas">sistema para clínicas</a> e o trial gratuito da UTecnologia Saúde.</p>',
 'Como migrar da planilha para um sistema clínico',
 'Passo a passo para sair da planilha e começar a usar um sistema clínico sem paralisar a recepção nem perder o controle da rotina.',
 'UTecnologia Saúde', 5, 1, '2026-08-20 10:05:00', '2026-08-20 10:05:00')
```

- [ ] **Step 4: Inserir o artigo “software gratuito para clínicas: trial vs gratuito”**

Adicione este registro como terceiro item do `VALUES`:
```sql
(1,
 'Software gratuito para clínicas: o que existe, o que é trial e o que vale a pena',
 'software-gratuito-para-clinicas-trial-vs-gratuito',
 'Nem todo “gratuito” ajuda a clínica de verdade. Entenda a diferença entre software grátis, plano limitado e trial completo.',
 '<p>Muita busca por software gratuito nasce da tentativa de reduzir risco...</p><h2>1. Gratuito nem sempre significa utilizável</h2><p>Muitos planos grátis travam exatamente nos recursos que a clínica precisa.</p><h2>2. Trial completo pode ser mais útil que plano free</h2><p>Você testa a operação real antes de decidir.</p><h2>3. O que comparar</h2><p>Agenda, prontuário, equipe, suporte e limite de uso.</p><p>O <a href="https://utecnologia.com.br/sistema-gratuito-para-clinicas">trial gratuito para clínicas</a> da UTecnologia Saúde segue essa lógica de validação prática.</p>',
 'Software gratuito para clínicas: trial vs gratuito',
 'Entenda a diferença entre software grátis, plano limitado e trial completo para clínicas antes de decidir apenas pelo menor custo.',
 'UTecnologia Saúde', 5, 1, '2026-08-20 10:10:00', '2026-08-20 10:10:00')
```

- [ ] **Step 5: Revisar a estrutura SQL**

Run:
```powershell
Get-Content -Raw docs/seo-geo-agente-blog-fase-1-2026-08-20.sql
```

Checklist:
- um único `INSERT INTO blog_posts`
- três blocos `(...)`
- vírgula após o primeiro e segundo item
- ponto e vírgula apenas no último item

- [ ] **Step 6: Commit**

```bash
git add docs/seo-geo-agente-blog-fase-1-2026-08-20.sql
git commit -m "docs: seed de artigos comerciais fase 1 seo geo"
```

---

## Task 6: Atualizar os sitemaps públicos

**Files:**
- Modify: `sitemap.xml`
- Modify: `sitemap-blog.xml`
- Modify: `sitemap-index.xml`

- [ ] **Step 1: Adicionar as novas páginas comparativas em `sitemap.xml`**

Insira estes blocos antes das páginas institucionais:
```xml
  <url>
    <loc>https://utecnologia.com.br/alternativa-shosp</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/alternativa-clinica-nas-nuvens</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
```

- [ ] **Step 2: Adicionar os novos slugs no `sitemap-blog.xml`**

Insira estes blocos antes de `</urlset>`:
```xml
  <url>
    <loc>https://utecnologia.com.br/blog/quanto-custa-um-software-para-clinica</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/como-migrar-da-planilha-para-sistema-clinico</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/software-gratuito-para-clinicas-trial-vs-gratuito</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
```

- [ ] **Step 3: Atualizar `lastmod` em `sitemap-index.xml`**

Substitua:
```xml
    <lastmod>2026-07-14</lastmod>
```

Por:
```xml
    <lastmod>2026-08-20</lastmod>
```

Faça isso nos dois blocos `<sitemap>`.

- [ ] **Step 4: Validar visualmente os XMLs**

Run:
```powershell
Get-Content -Raw sitemap.xml
Get-Content -Raw sitemap-blog.xml
Get-Content -Raw sitemap-index.xml
```

Checklist:
- XML continua bem formado
- URLs novas aparecem exatamente uma vez
- datas `2026-08-20` só nas entradas novas desta fase

- [ ] **Step 5: Commit**

```bash
git add sitemap.xml sitemap-blog.xml sitemap-index.xml
git commit -m "chore: atualizar sitemaps da fase 1 seo geo"
```

---

## Self-Review

**Spec coverage:**
- [x] Comparativos com concorrentes priorizados (`alternativa-shosp`, `alternativa-clinica-nas-nuvens`) — Tasks 1, 2 e 3
- [x] Reforço de GEO orientado à conversão em página estratégica existente (`software-para-medicos`) — Task 4
- [x] Conteúdo comercial de custo, migração e trial vs gratuito — Task 5
- [x] Distribuição via sitemaps — Task 6
- [x] Escopo limitado à primeira onda prática de 30 dias, sem misturar a expansão de 60/90 dias — arquitetura e mapa de arquivos

**Placeholder scan:**
- [x] Sem `TODO`, `TBD` ou “implement later”
- [x] Todos os arquivos têm caminho exato
- [x] Todos os comandos têm saída esperada ou checklist de validação

**Type consistency:**
- [x] `seo_alternativa_shosp` ↔ `alternativa-shosp.php` ↔ rota `alternativa-shosp`
- [x] `seo_alternativa_clinica_nuvens` ↔ `alternativa-clinica-nas-nuvens.php` ↔ rota `alternativa-clinica-nas-nuvens`
- [x] Slugs de blog repetidos com a mesma grafia no SQL e no `sitemap-blog.xml`
