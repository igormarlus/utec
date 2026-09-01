# SEO/GEO — Confirmação e Lembrete de Consulta por WhatsApp — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Publicar uma frente de conteúdo orgânico (1 landing dedicada + 7 artigos de blog + entradas de menu/rodapé/sitemap) que atrai clínicas e profissionais de saúde que buscam confirmar, lembrar e notificar agendamentos por WhatsApp, usando o recurso de confirmação/lembrete do UTecnologia Saúde como isca para o trial.

**Architecture:** Segue o padrão já consolidado do projeto para páginas SEO: rota em `application/config/routes.php`, método enxuto em `Home.php`, view PHP estática em `application/views/public/seo/` copiada de uma página recente, artigos publicados via arquivo `.sql` (importado manualmente), e URLs distribuídas nos 3 XML de sitemap. As mudanças de menu (`index-front.php`), rodapé e sitemaps ficam num passo "publicar" separado, aplicado só quando o cron de lembrete automático estiver no ar.

**Tech Stack:** PHP 7, CodeIgniter 3.1.10, HTML/CSS inline nas views, SQL para `blog_posts`, XML de sitemap, PowerShell, git.

**Spec:** `docs/superpowers/specs/2026-08-31-seo-geo-whatsapp-confirmacao-consulta-design.md`

---

## Mapa de arquivos

| Arquivo | Ação | Responsabilidade |
|---------|------|-----------------|
| `application/config/routes.php` | Modificar | Rota `confirmacao-de-consulta-por-whatsapp` → `home/seo_confirmacao_whatsapp` |
| `application/controllers/Home.php` | Modificar | Método `seo_confirmacao_whatsapp()` — só carrega a view |
| `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php` | Criar | Landing: hero, "como funciona", features, "em resumo", FAQ, "leia também", CTA, 3 JSON-LD |
| `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql` | Criar | Seed com 7 artigos (`INSERT INTO blog_posts`) |
| `docs/seo-geo-agente-ledger.md` | Modificar | Registrar a nova frente + keywords testadas nesta rodada |
| `application/views/index-front.php` | Modificar | Item no dropdown "Sistema" + link na coluna "Recursos" do rodapé (passo "publicar") |
| `sitemap.xml` | Modificar | `<url>` da landing (passo "publicar") |
| `sitemap-blog.xml` | Modificar | 7 `<url>` de blog (passo "publicar") |
| `sitemap-index.xml` | Modificar | `lastmod` = `2026-08-31` nos dois blocos (passo "publicar") |

**Branch:** `feat/seo-whatsapp-confirmacao-consulta` (já criada; o design doc já foi commitado nela).

**Convenções de validação deste projeto** (não há suíte automatizada para páginas públicas):
- `php -l <arquivo>` após cada mudança em `.php` — esperado: `No syntax errors detected in <arquivo>`.
- Smoke test manual abrindo a URL local.
- Inspeção visual de `.sql` e `.xml`.

---

## Task 1: Rota e método da landing

**Files:**
- Modify: `application/config/routes.php`
- Modify: `application/controllers/Home.php`

- [x] **Step 1: Confirmar que a rota e o método ainda não existem**

Run:
```powershell
rg -n "confirmacao-de-consulta-por-whatsapp|seo_confirmacao_whatsapp" application/config/routes.php application/controllers/Home.php
```
Expected: sem resultados (exit code 1).

- [x] **Step 2: Adicionar a rota em `application/config/routes.php`**

Localize a linha (fim do bloco de rotas `seo_*`):
```php
$route['sistema-para-medicina-do-trabalho'] = 'home/seo_sistema_medicina_trabalho';
```

Substitua por:
```php
$route['sistema-para-medicina-do-trabalho'] = 'home/seo_sistema_medicina_trabalho';
$route['confirmacao-de-consulta-por-whatsapp'] = 'home/seo_confirmacao_whatsapp';
```

- [x] **Step 3: Adicionar o método em `application/controllers/Home.php`**

Localize o método `seo_sistema_medicina_trabalho()` (padrão dos métodos `seo_*`):
```php
	public function seo_sistema_medicina_trabalho()
	{
		$this->load->view('public/seo/sistema-para-medicina-do-trabalho');
	}
```

Logo depois dele, adicione:
```php
	public function seo_confirmacao_whatsapp()
	{
		$this->load->view('public/seo/confirmacao-de-consulta-por-whatsapp');
	}
```

> Se o nome exato do método de medicina do trabalho divergir, ancore em qualquer método `seo_*` existente — o corpo é sempre só `$this->load->view('public/seo/<slug>');`.

- [x] **Step 4: Validar sintaxe**

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

- [x] **Step 5: Commit**

```bash
git add application/config/routes.php application/controllers/Home.php
git commit -m "feat: rota da landing confirmacao de consulta por whatsapp"
```

---

## Task 2: Landing `confirmacao-de-consulta-por-whatsapp.php`

**Files:**
- Create: `application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php`
- Reference: `application/views/public/seo/sistema-para-medicina-do-trabalho.php`

A estratégia é copiar a página de referência e substituir blocos. O `<head>` (gtag, fontes) e o
`<style>` inteiro são reaproveitados **sem alteração**. A nav e o footer têm ajustes pontuais.

- [x] **Step 1: Copiar a página de referência**

Run:
```powershell
Copy-Item application/views/public/seo/sistema-para-medicina-do-trabalho.php application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php
```
Expected: arquivo criado, sem saída de erro.

- [x] **Step 2: Substituir `<title>`, description e canonical**

Localize:
```html
    <title>Sistema para Medicina do Trabalho — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínicas de medicina do trabalho e medicina ocupacional: agenda de exames admissionais, periódicos e demissionais, prontuário e gestão de equipe. Experimente grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-medicina-do-trabalho">
```

Substitua por:
```html
    <title>Confirmação e Lembrete de Consulta por WhatsApp — Sistema para Clínicas | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirme e lembre consultas automaticamente pelo WhatsApp: o paciente responde com um toque e a agenda da clínica atualiza sozinha. Envio pela API oficial da Meta. Teste grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">
```

- [x] **Step 3: Substituir o bloco Open Graph + Twitter**

Localize:
```html
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-medicina-do-trabalho">
    <meta property="og:title" content="Sistema para Medicina do Trabalho — UTecnologia Saúde">
    <meta property="og:description" content="Agenda de exames ocupacionais, prontuário e gestão de equipe para clínicas de medicina do trabalho. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Medicina do Trabalho — UTecnologia Saúde">
    <meta name="twitter:description" content="Agenda, prontuário e gestão para clínicas de medicina do trabalho e medicina ocupacional. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
```

Substitua por:
```html
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">
    <meta property="og:title" content="Confirmação e Lembrete de Consulta por WhatsApp — UTecnologia Saúde">
    <meta property="og:description" content="O paciente confirma ou cancela a consulta pelo WhatsApp com um toque e a agenda da clínica atualiza sozinha. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Confirmação e Lembrete de Consulta por WhatsApp — UTecnologia Saúde">
    <meta name="twitter:description" content="Confirmação no agendamento e lembrete automático por WhatsApp, com resposta do paciente por botão. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
```

- [x] **Step 4: Substituir a `<section class="hero">` inteira**

Localize o bloco que começa em `<section class="hero">` e termina no `</section>` correspondente
(logo antes de `<section class="prontuario-section">`). Substitua **todo** esse bloco por:

```html
<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">WhatsApp para Clínicas</div>
                <h1>Confirmação e lembrete de consulta por <em>WhatsApp</em></h1>
                <p class="hero-text">
                    Assim que a consulta é marcada, o paciente recebe a confirmação no WhatsApp.
                    No dia anterior e na manhã do atendimento, o sistema envia o lembrete sozinho.
                    O paciente confirma ou cancela com um toque — e a agenda da sua clínica se
                    atualiza na hora.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
                <div class="trust-line">
                    <span>Sem cartão de crédito</span>
                    <span>API oficial da Meta</span>
                    <span>A partir de R$ 79/mês</span>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Funciona para:</span>
                    <span class="funciona-chip">Consultório individual</span>
                    <span class="funciona-chip">Clínica com recepção</span>
                    <span class="funciona-chip">Odontologia</span>
                    <span class="funciona-chip">Psicologia e Fisioterapia</span>
                    <span class="funciona-chip">Clínica com várias agendas</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">WhatsApp · Confirmação de Consulta</span>
                </div>
                <div class="card-body" style="background:#e5ddd5;">
                    <div style="background:#fff;border-radius:10px;padding:12px 14px;font-size:13px;color:var(--ink);box-shadow:0 1px 1px rgba(0,0,0,.08);margin-bottom:10px;">
                        Olá, Maria! Sua consulta com a Dra. Ana está marcada para <strong>quinta, 12/09, às 14h30</strong>. Podemos confirmar?
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <span style="flex:1;text-align:center;border:1px solid var(--teal);color:var(--teal);border-radius:8px;padding:7px 0;font-weight:700;">✓ Confirmar</span>
                            <span style="flex:1;text-align:center;border:1px solid #c04040;color:#c04040;border-radius:8px;padding:7px 0;font-weight:700;">✕ Cancelar</span>
                        </div>
                    </div>
                    <div style="background:#d9fdd3;border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ink);max-width:85%;margin-left:auto;box-shadow:0 1px 1px rgba(0,0,0,.08);margin-bottom:10px;">
                        ✓ Confirmar
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ink);box-shadow:0 1px 1px rgba(0,0,0,.08);">
                        Perfeito, sua consulta está <strong>confirmada</strong>. ✅ Até quinta!
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
```

- [x] **Step 5: Substituir a `<section class="prontuario-section">` inteira por "Como funciona"**

Localize o bloco `<section class="prontuario-section"> ... </section>` (o bloco escuro com o mock
de prontuário) e substitua **todo** ele por:

```html
<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Como funciona</div>
            <h2>Três passos, nenhum trabalho manual</h2>
            <p>Do agendamento à baixa na agenda, o fluxo roda sozinho — a recepção só acompanha.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:0 auto;">
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 1</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">Agendou, já avisou</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">Com o checkbox de WhatsApp ligado, o paciente recebe a confirmação no momento em que a consulta entra na agenda.</p>
            </div>
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 2</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">Lembrete automático</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">Um processo roda de hora em hora e dispara o lembrete nas janelas que você definir: um dia antes e/ou na manhã da consulta.</p>
            </div>
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 3</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">A agenda se atualiza</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">O paciente toca em confirmar ou cancelar. O status muda sozinho e a recepção recebe um aviso interno.</p>
            </div>
        </div>
    </div>
</section>
```

- [x] **Step 6: Substituir a `<section class="section">` de recursos e anexar o bloco "Em resumo"**

Localize o bloco de recursos — começa com `<section class="section">` seguido de
`<div class="section-label">Recursos</div>` — e vai até o `</section>` correspondente.
Substitua **todo** ele por:

```html
<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>O que a confirmação por WhatsApp faz pela sua clínica</h2>
        <p class="section-sub">Confirmação no agendamento, lembrete automático e resposta do paciente que cai direto na agenda.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Confirmação no agendamento</h3>
                <p>Marcou a consulta com o WhatsApp ligado? O paciente recebe na hora a mensagem com os botões de confirmar e cancelar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <h3>Lembrete automático</h3>
                <p>Um processo roda de hora em hora e envia o lembrete nas janelas que você escolher: véspera e/ou manhã da consulta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👆</div>
                <h3>Resposta que cai na agenda</h3>
                <p>Quando o paciente confirma ou cancela, o status do agendamento muda sozinho. Cancelou? O horário fica livre para remarcar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>Aviso para a recepção</h3>
                <p>Cada resposta vira um aviso interno para quem marcou a consulta e para o profissional. O sino no topo mostra o que falta ler.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏷️</div>
                <h3>Etiqueta na agenda</h3>
                <p>A agenda mostra "Confirmado via WhatsApp" ou "Cancelado via WhatsApp" em cada horário, no computador e no celular.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🟢</div>
                <h3>API oficial da Meta</h3>
                <p>O envio usa a WhatsApp Cloud API, com número verificado e modelo de mensagem aprovado — não é automação de celular nem número pessoal.</p>
            </div>
        </div>
        <p class="hero-text" style="font-size:15px;background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;max-width:680px;margin:40px auto 0;">
            <strong>Em resumo:</strong> o UTecnologia Saúde confirma a consulta no momento do agendamento
            e envia lembretes automáticos por WhatsApp na véspera e no dia. O paciente responde pelos
            botões da própria mensagem e a agenda se atualiza sem ninguém digitar nada. O que ainda é
            manual: reagendar (o paciente confirma ou cancela, mas não escolhe outro horário sozinho)
            e qualquer conversa fora do modelo aprovado pela Meta.
        </p>
    </div>
</section>
```

- [x] **Step 7: Substituir a `<section class="section" style="background:var(--white);">` de FAQ**

Localize o bloco de FAQ — `<section class="section" style="background:var(--white);">` com
`<div class="section-label">Perguntas frequentes</div>` — até o `</section>` correspondente.
Substitua **todo** ele por:

```html
<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre a confirmação por WhatsApp</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Preciso do WhatsApp Business API para usar?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O envio é feito pela WhatsApp Cloud API, a versão oficial da Meta para empresas. A conexão é configurada uma vez na área de administração (número, token e modelo de mensagem aprovado). Não funciona com o WhatsApp comum do celular.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O paciente consegue reagendar pela mensagem?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. A mensagem tem dois botões: confirmar e cancelar. Se o paciente cancela, o horário fica livre e a recepção remarca pelo sistema ou combina um novo horário com o paciente. Escolher outro horário pelo próprio WhatsApp não faz parte do recurso.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Quantas mensagens posso enviar no teste grátis?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Durante o teste, sem uma assinatura ativa, o envio é limitado a 3 disparos por clínica — o suficiente para ver o fluxo completo funcionando. Com o plano ativo, o limite acompanha o seu volume de agendamentos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Isso é um chatbot de atendimento?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. O recurso serve para confirmar e lembrar consultas agendadas. Ele não responde dúvidas livres, não faz triagem e não conduz conversa aberta — o paciente confirma, cancela ou recebe a mensagem de texto automática.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Funciona para consultório com um profissional só?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Solo (R$ 79/mês) já inclui a confirmação e o lembrete por WhatsApp, com 1 profissional e 2 colaboradores. A recepção recebe os avisos de resposta mesmo em operação pequena.</div>
            </div>
        </div>
    </div>
</section>
```

- [x] **Step 8: Substituir a `<section class="section">` de CTA final e antepor "Leia também"**

Localize o bloco final — `<section class="section">` com `<div class="cta-wrap">` — até o
`</section>`. Substitua **todo** ele por:

```html
<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Leia também</div>
        <h2>Guias práticos de confirmação e lembrete</h2>
        <p class="section-sub">Modelos de mensagem prontos e o que fazer para diminuir as faltas.</p>
        <div class="features-grid">
            <a class="feature-card" href="<?=base_url()?>blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp">
                <div class="feature-icon">💬</div>
                <h3>Modelos de mensagem de confirmação para WhatsApp</h3>
                <p>Textos prontos para copiar, por tipo de consulta.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/mensagem-de-lembrete-de-consulta-quando-enviar">
                <div class="feature-icon">⏰</div>
                <h3>Mensagem de lembrete: modelos e quando enviar</h3>
                <p>As melhores janelas (véspera e manhã do dia) e os erros comuns.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/confirmacao-de-consulta-manual-ou-automatica">
                <div class="feature-icon">⚖️</div>
                <h3>Confirmação manual ou automática?</h3>
                <p>Quando o volume de agendamentos justifica automatizar.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/como-reduzir-faltas-de-pacientes-no-consultorio">
                <div class="feature-icon">📉</div>
                <h3>Como reduzir faltas de pacientes</h3>
                <p>Sete práticas para a agenda não ter buraco de última hora.</p>
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Menos faltas, agenda sempre confirmada</h2>
            <p class="cta-sub">30 dias grátis para testar a confirmação e o lembrete por WhatsApp na sua clínica.<br>Sem cartão de crédito. Começa em minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta grátis →</a>
        </div>
    </div>
</section>
```

- [x] **Step 9: Ajustar os links do rodapé**

Localize:
```html
            <div class="footer-links">
                <a href="<?=base_url()?>">Início</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
```

Substitua por:
```html
            <div class="footer-links">
                <a href="<?=base_url()?>">Início</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
```

- [x] **Step 10: Substituir os três blocos JSON-LD**

Localize os três `<script type="application/ld+json"> ... </script>` no fim do `<body>` e
substitua **os três** por:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp",
  "description": "Confirmação e lembrete de consulta por WhatsApp para clínicas e consultórios, com resposta do paciente por botão e atualização automática da agenda.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
    {"@type": "ListItem", "position": 3, "name": "Confirmação de Consulta por WhatsApp", "item": "https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Preciso do WhatsApp Business API para usar?", "acceptedAnswer": {"@type": "Answer", "text": "O envio é feito pela WhatsApp Cloud API, a versão oficial da Meta para empresas. A conexão é configurada uma vez na área de administração e não funciona com o WhatsApp comum do celular."}},
    {"@type": "Question", "name": "O paciente consegue reagendar pela mensagem?", "acceptedAnswer": {"@type": "Answer", "text": "Não. A mensagem tem dois botões: confirmar e cancelar. Se o paciente cancela, o horário fica livre e a recepção remarca pelo sistema. Escolher outro horário pelo próprio WhatsApp não faz parte do recurso."}},
    {"@type": "Question", "name": "Quantas mensagens posso enviar no teste grátis?", "acceptedAnswer": {"@type": "Answer", "text": "Durante o teste, sem assinatura ativa, o envio é limitado a 3 disparos por clínica. Com o plano ativo, o limite acompanha o volume de agendamentos."}},
    {"@type": "Question", "name": "Isso é um chatbot de atendimento?", "acceptedAnswer": {"@type": "Answer", "text": "Não. O recurso confirma e lembra consultas agendadas. Não responde dúvidas livres, não faz triagem e não conduz conversa aberta."}},
    {"@type": "Question", "name": "Funciona para consultório com um profissional só?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo (R$ 79/mês) já inclui a confirmação e o lembrete por WhatsApp, com 1 profissional e 2 colaboradores."}}
  ]
}
</script>
```

- [x] **Step 11: Validar sintaxe**

Run:
```powershell
php -l application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php
```
Expected:
```text
No syntax errors detected in application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php
```

- [x] **Step 12: Smoke test manual**

Abrir `http://localhost/utec/confirmacao-de-consulta-por-whatsapp` e conferir:
- `<title>` = "Confirmação e Lembrete de Consulta por WhatsApp — Sistema para Clínicas | UTecnologia Saúde"
- H1 visível com "WhatsApp" em itálico/destaque
- mock de conversa no hero renderiza com os dois botões
- seção escura "Como funciona" com 3 passos
- 6 cards de recursos + parágrafo "Em resumo"
- FAQ abre/fecha ao clicar
- bloco "Leia também" com 4 cards clicáveis
- CTA final "Criar conta grátis" aponta para `/experimentar`
- ver fonte da página: 3 blocos `application/ld+json` presentes (SoftwareApplication, BreadcrumbList, FAQPage)

- [x] **Step 13: Commit**

```bash
git add application/views/public/seo/confirmacao-de-consulta-por-whatsapp.php
git commit -m "feat: landing confirmacao e lembrete de consulta por whatsapp"
```

---

## Task 3: Seed SQL dos 7 artigos de blog

**Files:**
- Create: `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql`
- Reference: `docs/blog-posts-seed.sql` (colunas e formato)

Colunas de `blog_posts`, nesta ordem:
`id_categoria, titulo, slug, resumo, conteudo, meta_titulo, meta_descricao, autor, tempo_leitura, publicado, criado_em, publicado_em`.

Um único `INSERT INTO blog_posts ... VALUES` com 7 tuplas, vírgula entre elas e `;` só na última.
`id_categoria` = `1` em todas (mesma categoria do artigo "Gestão de clínica médica"), com aviso
no cabeçalho para o usuário conferir. `autor` = `'UTecnologia Saúde'`, `publicado` = `1`,
`criado_em` e `publicado_em` = horários crescentes em `2026-08-31`.

- [x] **Step 1: Criar o arquivo com o cabeçalho e a abertura do INSERT**

Crie `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql` com:

```sql
-- ============================================================
-- UTecnologia Saúde — Cluster "Confirmação/Lembrete por WhatsApp"
-- Rodada: 2026-08-31
-- Importar via phpMyAdmin: banco utecnologiacom_db
-- ATENÇÃO: id_categoria = 1 em todos (mesma categoria de
-- "Gestão de clínica médica" em docs/blog-posts-seed.sql).
-- Confira/ajuste o id antes de rodar, se necessário.
-- GATE: só publicar (aplicar este SQL + sitemaps + menu) quando
-- o cron de lembrete automático estiver no ar.
-- ============================================================

INSERT INTO `blog_posts`
  (`id_categoria`, `titulo`, `slug`, `resumo`, `conteudo`, `meta_titulo`, `meta_descricao`, `autor`, `tempo_leitura`, `publicado`, `criado_em`, `publicado_em`)
VALUES
```

- [x] **Step 2: Adicionar a tupla do ARTIGO 1**

Anexe ao arquivo (primeira tupla, termina com vírgula):

```sql
-- ARTIGO 1
(1,
 'Modelos de mensagem de confirmação de consulta para WhatsApp',
 'modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp',
 'Textos prontos para confirmar consultas pelo WhatsApp, por tipo de atendimento, com boas práticas de horário e tom — e como automatizar o envio.',
 '<p>Confirmar a consulta pelo WhatsApp reduz falta e retrabalho, mas a mensagem precisa ser curta, clara e fácil de responder. Abaixo estão modelos prontos para copiar e adaptar ao nome da sua clínica.</p><h2>Modelo para consulta médica</h2><p>Olá, [nome do paciente]! Sua consulta com [profissional] está marcada para [dia] às [hora], na [clínica]. Podemos confirmar? Responda <strong>SIM</strong> para confirmar ou <strong>NÃO</strong> para remarcar.</p><h2>Modelo para retorno</h2><p>Oi, [nome]! Passando para confirmar seu retorno com [profissional] em [dia], às [hora]. Se precisar remarcar, é só responder esta mensagem.</p><h2>Modelo para primeira consulta</h2><p>Olá, [nome]! Sua primeira consulta na [clínica] está agendada para [dia] às [hora]. Chegue 10 minutos antes e traga documento e carteirinha do convênio, se tiver. Podemos confirmar?</p><h2>Modelo para exame</h2><p>Oi, [nome]! Seu exame de [tipo] está marcado para [dia] às [hora]. Preparo: [instrução]. Confirma a presença?</p><h2>Boas práticas</h2><ul><li>Envie a confirmação com 24 a 48 horas de antecedência.</li><li>Use o primeiro nome do paciente e o nome do profissional.</li><li>Ofereça sempre uma forma simples de responder (SIM/NÃO ou botões).</li><li>Evite enviar em horário noturno.</li><li>Não coloque diagnóstico nem detalhe clínico na mensagem.</li></ul><h2>Como automatizar isso</h2><p>Digitar mensagem por mensagem consome o tempo da recepção. No <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">sistema de confirmação de consulta por WhatsApp</a> da UTecnologia Saúde, a mensagem sai sozinha quando a consulta é agendada, com botões de confirmar e cancelar, e a resposta do paciente atualiza a agenda automaticamente. Veja também <a href="https://utecnologia.com.br/blog/mensagem-de-lembrete-de-consulta-quando-enviar">quando enviar o lembrete</a>. Para testar na sua clínica, <a href="https://utecnologia.com.br/experimentar">crie uma conta grátis por 30 dias</a>.</p>',
 'Modelos de mensagem de confirmação de consulta para WhatsApp',
 'Textos prontos para confirmar consultas pelo WhatsApp por tipo de atendimento, com boas práticas de horário e tom, e como automatizar o envio.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
```

- [x] **Step 3: Adicionar a tupla do ARTIGO 2**

```sql
-- ARTIGO 2
(1,
 'Mensagem de lembrete de consulta: modelos e quando enviar',
 'mensagem-de-lembrete-de-consulta-quando-enviar',
 'Qual a diferença entre lembrete e confirmação, as melhores janelas para enviar (véspera e manhã do dia) e modelos prontos para WhatsApp.',
 '<p>O lembrete de consulta serve para trazer o paciente de volta à memória perto da data. É diferente da confirmação, que pede uma resposta. Muitas clínicas usam os dois: confirmam no agendamento e lembram na véspera.</p><h2>Lembrete ou confirmação?</h2><p><strong>Confirmação:</strong> pede que o paciente responda (SIM/NÃO ou botão). Serve para liberar o horário se ele não puder vir.</p><p><strong>Lembrete:</strong> só avisa. Não exige resposta, mas pode oferecer a opção de cancelar.</p><h2>Quando enviar o lembrete</h2><ul><li><strong>24 horas antes (véspera):</strong> janela principal. Tempo suficiente para o paciente se organizar ou avisar que não vem.</li><li><strong>Na manhã do dia:</strong> segundo toque para consultas da tarde. Reduz a falta de quem esqueceu.</li><li>Evite lembrar com mais de 3 dias de antecedência: o paciente esquece de novo.</li></ul><h2>Modelo de lembrete simples</h2><p>Oi, [nome]! Lembrete da sua consulta com [profissional] amanhã, [dia], às [hora], na [clínica]. Se não puder vir, avise por aqui para liberarmos o horário.</p><h2>Modelo de lembrete no dia</h2><p>Bom dia, [nome]! Sua consulta é hoje às [hora], na [clínica]. Até logo!</p><h2>Erros comuns</h2><ul><li>Enviar cedo demais e não repetir perto da data.</li><li>Lembrete sem dizer o profissional e o endereço.</li><li>Não dar opção de cancelar — o paciente que não pode vir simplesmente falta.</li></ul><h2>Fazendo isso sem trabalho manual</h2><p>No <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">recurso de confirmação e lembrete por WhatsApp</a> da UTecnologia Saúde, um processo roda de hora em hora e dispara o lembrete nas janelas que você definir. O paciente confirma ou cancela pelo botão e a agenda se atualiza. Veja também os <a href="https://utecnologia.com.br/blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp">modelos de mensagem de confirmação</a> e <a href="https://utecnologia.com.br/experimentar">teste grátis por 30 dias</a>.</p>',
 'Mensagem de lembrete de consulta: modelos e quando enviar',
 'Diferença entre lembrete e confirmação, as melhores janelas de envio pelo WhatsApp e modelos prontos para reduzir faltas na clínica.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 09:10:00', '2026-08-31 09:10:00'),
```

- [x] **Step 4: Adicionar a tupla do ARTIGO 3**

```sql
-- ARTIGO 3
(1,
 'Como montar a mensagem de confirmação de consulta no WhatsApp: passo a passo',
 'como-fazer-mensagem-de-confirmacao-de-consulta-no-whatsapp',
 'O que não pode faltar na mensagem, o tom certo, como oferecer a resposta sim/não e o que muda ao usar o WhatsApp Business API com modelo aprovado.',
 '<p>Uma boa mensagem de confirmação tem que ser lida e respondida em segundos. Este passo a passo mostra como montar a sua.</p><h2>1. Comece pelo nome</h2><p>Use o primeiro nome do paciente. Mensagem que começa com "Prezado(a) senhor(a)" parece cobrança e é ignorada.</p><h2>2. Diga o essencial em uma linha</h2><p>Profissional, dia, hora e local. Nada além disso no primeiro parágrafo.</p><h2>3. Peça a confirmação de forma objetiva</h2><p>"Podemos confirmar? Responda SIM ou NÃO." Se usar botões, melhor ainda: o paciente responde com um toque.</p><h2>4. Ajuste o tom à clínica</h2><p>Consultório de psicologia costuma usar tom mais acolhedor; clínica de exames, tom mais direto. Mantenha o mesmo padrão em todas as mensagens.</p><h2>5. Não coloque dado clínico</h2><p>Nada de diagnóstico, resultado ou motivo da consulta. Isso é dado sensível e não deve trafegar por mensagem.</p><h2>6. Defina o horário de envio</h2><p>Entre 8h e 20h, 24 a 48 horas antes. Envio de madrugada gera bloqueio e reclamação.</p><h2>O que muda com o WhatsApp Business API</h2><p>Para enviar em escala sem risco de bloqueio, a clínica usa a WhatsApp Cloud API (versão oficial da Meta) com um <strong>modelo de mensagem aprovado</strong>. O texto livre fica limitado; a estrutura da confirmação segue o modelo homologado, com botões padronizados.</p><h2>Ou deixe o sistema fazer</h2><p>O <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">recurso de confirmação por WhatsApp</a> da UTecnologia Saúde já vem com o modelo aprovado e os botões de confirmar/cancelar. A mensagem sai quando a consulta é agendada e a resposta atualiza a agenda. <a href="https://utecnologia.com.br/experimentar">Crie uma conta grátis</a> para ver o fluxo.</p>',
 'Como montar a mensagem de confirmação de consulta no WhatsApp',
 'Passo a passo para escrever a mensagem de confirmação de consulta: o que incluir, o tom certo, horário de envio e o que muda com o WhatsApp Business API.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 09:20:00', '2026-08-31 09:20:00'),
```

- [x] **Step 5: Adicionar a tupla do ARTIGO 4**

```sql
-- ARTIGO 4
(1,
 'Confirmação de consulta: manual pelo WhatsApp ou automática?',
 'confirmacao-de-consulta-manual-ou-automatica',
 'O custo real de confirmar consulta na mão, quando o volume de agendamentos justifica automatizar e o que muda com a resposta do paciente caindo direto na agenda.',
 '<p>Toda clínica confirma consulta de algum jeito. A pergunta é se vale a pena continuar fazendo isso na mão.</p><h2>O custo do processo manual</h2><ul><li>Tempo da recepção: alguns minutos por paciente, todo dia, multiplicado pela agenda inteira.</li><li>Esquecimento: em dia cheio, parte das confirmações não é enviada.</li><li>Controle solto: as respostas ficam no celular de uma pessoa, não na agenda.</li><li>Falta sem aviso: sem confirmação ativa, o horário vago só aparece quando o paciente não chega.</li></ul><h2>Quando o volume justifica automatizar</h2><p>Se a clínica passa de 15 a 20 agendamentos por dia, ou tem mais de um profissional, o processo manual começa a falhar. A partir daí, automatizar paga o próprio custo em agenda aproveitada.</p><h2>O que muda com a confirmação automática</h2><p>A mensagem sai sozinha no agendamento e o lembrete roda de hora em hora. O paciente confirma ou cancela pelo botão e o <strong>status do agendamento muda sem ninguém digitar</strong>. Cancelou? O horário fica livre para a recepção remarcar.</p><h2>O que ainda é decisão da clínica</h2><p>Reagendar continua sendo feito pela recepção — o paciente confirma ou cancela, mas não escolhe outro horário sozinho. E conversas fora do modelo aprovado não fazem parte do recurso.</p><h2>Testando na prática</h2><p>O <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">sistema de confirmação por WhatsApp</a> da UTecnologia Saúde faz esse fluxo de ponta a ponta. Veja também <a href="https://utecnologia.com.br/blog/como-reduzir-faltas-de-pacientes-no-consultorio">como reduzir faltas de pacientes</a> e <a href="https://utecnologia.com.br/blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp">modelos de mensagem</a>. Para experimentar, <a href="https://utecnologia.com.br/experimentar">crie uma conta grátis por 30 dias</a>.</p>',
 'Confirmação de consulta: manual pelo WhatsApp ou automática?',
 'Compare o custo de confirmar consulta na mão com a confirmação automática por WhatsApp e veja quando o volume de agendamentos justifica automatizar.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 09:30:00', '2026-08-31 09:30:00'),
```

- [x] **Step 6: Adicionar a tupla do ARTIGO 5**

```sql
-- ARTIGO 5
(1,
 'Como reduzir faltas de pacientes no consultório',
 'como-reduzir-faltas-de-pacientes-no-consultorio',
 'Sete práticas para diminuir o no-show na clínica: confirmação ativa, lembrete na véspera, política de remarcação, lista de espera e agenda realista.',
 '<p>Falta de paciente é buraco na agenda e prejuízo direto. Não existe solução única, mas um conjunto de práticas simples derruba bastante o índice de no-show.</p><h2>1. Confirmação ativa</h2><p>Não basta lembrar: peça uma resposta. Quem não pode vir avisa e o horário é liberado.</p><h2>2. Lembrete na véspera e no dia</h2><p>Um toque 24 horas antes e outro na manhã das consultas da tarde. Simples e eficaz.</p><h2>3. Facilite o cancelamento</h2><p>Se cancelar dá trabalho, o paciente prefere sumir. Um botão de cancelar na mensagem resolve.</p><h2>4. Política de remarcação clara</h2><p>Deixe explícito como remarcar e em quanto tempo de antecedência. Combine isso já na primeira consulta.</p><h2>5. Lista de espera</h2><p>Tenha pacientes dispostos a antecipar. Quando abre um horário, você preenche no mesmo dia.</p><h2>6. Agenda realista</h2><p>Overbooking gera atraso, atraso gera insatisfação e insatisfação gera falta na próxima. Ajuste o tempo médio de consulta ao que acontece de verdade.</p><h2>7. Acompanhe o número</h2><p>Meça a taxa de falta por profissional e por período. Sem número, não dá para saber se as ações funcionam.</p><h2>Automatizando os passos 1 a 3</h2><p>O <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">recurso de confirmação e lembrete por WhatsApp</a> da UTecnologia Saúde cobre confirmação ativa, lembrete automático e cancelamento por botão, com baixa direta na agenda. Veja também <a href="https://utecnologia.com.br/blog/o-que-fazer-quando-paciente-nao-confirma-consulta">o que fazer quando o paciente não confirma</a>. <a href="https://utecnologia.com.br/experimentar">Teste grátis por 30 dias</a>.</p>',
 'Como reduzir faltas de pacientes no consultório: 7 práticas',
 'Sete práticas para diminuir o no-show: confirmação ativa, lembrete por WhatsApp, cancelamento fácil, lista de espera e agenda realista.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 09:40:00', '2026-08-31 09:40:00'),
```

- [x] **Step 7: Adicionar a tupla do ARTIGO 6**

```sql
-- ARTIGO 6
(1,
 'O que fazer quando o paciente não confirma a consulta',
 'o-que-fazer-quando-paciente-nao-confirma-consulta',
 'Um fluxo prático para o horário parado: prazo-limite de confirmação, mensagem de cobrança, quando liberar a vaga e como usar a lista de espera.',
 '<p>O paciente recebeu a mensagem e não respondeu. Segurar o horário às cegas é perder agenda; cancelar cedo demais é arriscar o paciente aparecer. O caminho é ter um fluxo.</p><h2>1. Defina um prazo-limite</h2><p>Exemplo: confirmação até 18h do dia anterior. O paciente sabe a regra desde o agendamento.</p><h2>2. Envie uma segunda mensagem</h2><p>Perto do prazo, um toque final: "Ainda não recebemos sua confirmação para [dia] às [hora]. Podemos manter o horário?"</p><h2>3. Sem resposta até o prazo, acione a lista de espera</h2><p>Ofereça a vaga para quem quer antecipar. Se alguém aceita, o horário não fica ocioso.</p><h2>4. Libere ou mantenha com critério</h2><p>Paciente recorrente e pontual: vale manter mesmo sem confirmar. Primeira consulta sem confirmação: prioridade para a lista de espera.</p><h2>5. Registre o padrão</h2><p>Quem falta sem avisar com frequência entra num acompanhamento à parte — confirmação reforçada ou cobrança de taxa, conforme a política da clínica.</p><h2>Como o sistema ajuda</h2><p>No <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">recurso de confirmação por WhatsApp</a> da UTecnologia Saúde, o agendamento sem resposta fica visível na agenda como não confirmado, e o cancelamento pelo botão libera a vaga na hora. Veja também <a href="https://utecnologia.com.br/blog/como-reduzir-faltas-de-pacientes-no-consultorio">as 7 práticas para reduzir faltas</a>. <a href="https://utecnologia.com.br/experimentar">Crie uma conta grátis</a>.</p>',
 'O que fazer quando o paciente não confirma a consulta',
 'Fluxo prático para o horário sem confirmação: prazo-limite, mensagem de cobrança, lista de espera e quando liberar a vaga.',
 'UTecnologia Saúde', 5, 1, '2026-08-31 09:50:00', '2026-08-31 09:50:00'),
```

- [x] **Step 8: Adicionar a tupla do ARTIGO 7 (última — termina com `;`)**

```sql
-- ARTIGO 7
(1,
 'Mensagem de confirmação de consulta odontológica: modelos e rotina',
 'mensagem-de-confirmacao-de-consulta-odontologica',
 'Modelos de mensagem para confirmar consulta odontológica por tipo de atendimento e como organizar a rotina de confirmação na recepção do consultório.',
 '<p>No consultório odontológico, a confirmação muda conforme o tipo de atendimento: avaliação, procedimento longo, retorno ou manutenção de aparelho. Abaixo, modelos e uma rotina simples para a recepção.</p><h2>Modelo para avaliação</h2><p>Olá, [nome]! Sua avaliação com [dentista] está marcada para [dia] às [hora], na [clínica]. Podemos confirmar? Responda SIM ou NÃO.</p><h2>Modelo para procedimento</h2><p>Oi, [nome]! Seu procedimento com [dentista] é [dia] às [hora]. Reserve cerca de [duração]. Confirma a presença?</p><h2>Modelo para retorno</h2><p>Olá, [nome]! Passando para confirmar seu retorno em [dia], às [hora]. Se precisar remarcar, responda esta mensagem.</p><h2>Modelo para manutenção de aparelho</h2><p>Oi, [nome]! Sua manutenção do aparelho está agendada para [dia] às [hora]. Podemos confirmar?</p><h2>Rotina de confirmação na recepção</h2><ul><li>Confirme com 48 horas de antecedência para procedimentos longos e 24 horas para avaliações e retornos.</li><li>Marque na agenda quem confirmou, quem recusou e quem não respondeu.</li><li>Para os que não responderam, faça um segundo contato no dia anterior.</li><li>Tenha uma lista de espera para preencher cancelamentos de última hora.</li></ul><h2>Automatizando a rotina</h2><p>O <a href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">recurso de confirmação por WhatsApp</a> da UTecnologia Saúde envia a mensagem no agendamento e o lembrete automático depois, com botões de confirmar e cancelar que atualizam a agenda. Veja o <a href="https://utecnologia.com.br/sistema-para-dentistas">sistema para dentistas</a> e os <a href="https://utecnologia.com.br/blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp">modelos gerais de mensagem</a>. <a href="https://utecnologia.com.br/experimentar">Teste grátis por 30 dias</a>.</p>',
 'Mensagem de confirmação de consulta odontológica: modelos e rotina',
 'Modelos de mensagem para confirmar consulta odontológica por tipo de atendimento e como organizar a rotina de confirmação na recepção.',
 'UTecnologia Saúde', 6, 1, '2026-08-31 10:00:00', '2026-08-31 10:00:00');
```

- [x] **Step 9: Revisar a estrutura do arquivo**

Run:
```powershell
Get-Content -Raw docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql | Select-String -Pattern "^\(1," -AllMatches | ForEach-Object { $_.Matches.Count }
rg -n "^\);|^ 'UTecnologia Saúde', \d, 1," docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql
```
Checklist manual:
- exatamente **um** `INSERT INTO \`blog_posts\``
- **7** tuplas abrindo com `(1,`
- vírgula ao fim das tuplas 1 a 6; ponto e vírgula só ao fim da tupla 7
- nenhum apóstrofo não escapado dentro dos textos (todos os modelos usam "" ou reticências, sem `'`)
- todos os slugs batem com os da Task 2 (bloco "Leia também") e com a Task 5 (sitemap-blog)

- [x] **Step 10: Commit**

```bash
git add docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql
git commit -m "docs: seed de 7 artigos do cluster confirmacao por whatsapp"
```

---

## Task 4: Atualizar o ledger do agente SEO/GEO

**Files:**
- Modify: `docs/seo-geo-agente-ledger.md`

- [x] **Step 1: Registrar as keywords testadas nesta rodada**

Na seção `## 1. Keywords testadas`, adicione uma linha na tabela:

```markdown
| `confirmação de consulta por whatsapp`, `mensagem de confirmação de consulta`, `lembrete de consulta`, `mensagem de lembrete de consulta`, `sistema de agendamento com whatsapp`, `whatsapp para clínicas`, `mensagem de confirmação de consulta odontológica` | 2026-08-31 | **Demanda confirmada** — clusters informacionais fortes (mensagem/lembrete/modelo) + intenção de ferramenta (`sistema de agendamento com whatsapp` + grátis/via/integrado). Landing + 7 artigos criados — ver seção 2. Sem sinal: "reduzir faltas", "no-show", "disparo de whatsapp", "confirmação de consulta automática" (usados só no corpo) | 2026-11-30 |
```

- [x] **Step 2: Registrar a nova frente na seção de páginas existentes**

Na seção `## 2. Páginas e artigos existentes` → `### Landing pages`, acrescente ao fim da lista:
`, **confirmacao-de-consulta-por-whatsapp (novo, 2026-08-31 — frente "WhatsApp para clínicas")**`.

Em `### Artigos de blog` → "Gerados como `.sql` pendente de aplicação:", adicione:

```markdown
- `docs/seo-geo-blog-whatsapp-confirmacao-2026-08-31.sql` → 7 artigos do cluster "confirmação/lembrete por WhatsApp" (slugs: `modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp`, `mensagem-de-lembrete-de-consulta-quando-enviar`, `como-fazer-mensagem-de-confirmacao-de-consulta-no-whatsapp`, `confirmacao-de-consulta-manual-ou-automatica`, `como-reduzir-faltas-de-pacientes-no-consultorio`, `o-que-fazer-quando-paciente-nao-confirma-consulta`, `mensagem-de-confirmacao-de-consulta-odontologica`) — **pendente de aplicação; publicar só com o cron de lembrete no ar**
```

- [x] **Step 3: Anotar o item de watchlist para a próxima rodada de concorrentes**

Ao fim da seção `## 3. Descartes`, ou numa nota logo abaixo dela, adicione:

```markdown
> **Para a próxima rodada do bloco 2 (Concorrentes):** auditar o espaço de `sistema de agendamento com whatsapp` / `whatsapp para clínicas` (automação de WhatsApp, chatbot para clínicas) — ainda não mapeado no monitor. Avaliar 2 artigos da "próxima leva" do design de 2026-08-31: `enviar-mensagem-para-paciente-whatsapp-lgpd` e `sistema-de-agendamento-com-whatsapp-o-que-da-para-automatizar`.
```

- [x] **Step 4: Commit**

```bash
git add docs/seo-geo-agente-ledger.md
git commit -m "docs: ledger seo/geo registra frente de confirmacao por whatsapp"
```

---

## Task 5: Passo "publicar" — menu, rodapé e sitemaps

> **GATE:** aplicar esta task **somente quando o cron de lembrete automático estiver no ar**.
> Até lá, a landing fica acessível por URL direta para revisão, mas fora do menu e dos sitemaps.
> Se o usuário decidir antecipar (opção C+B do design), executar normalmente.

**Files:**
- Modify: `application/views/index-front.php`
- Modify: `sitemap.xml`
- Modify: `sitemap-blog.xml`
- Modify: `sitemap-index.xml`

- [x] **Step 1: Adicionar o item no dropdown "Sistema" do header**

Em `application/views/index-front.php`, localize (dentro do primeiro `nav-item`, dropdown "Sistema"):
```php
                    <a href="<?=base_url()?>software-para-clinicas">
                        <span class="dd-icon">💻</span>
                        <span class="dd-text"><span class="dd-label">Software para clínicas</span><span class="dd-desc">SaaS 100% online, sem instalação</span></span>
                    </a>
                    <div class="nav-divider"></div>
```

Substitua por:
```php
                    <a href="<?=base_url()?>software-para-clinicas">
                        <span class="dd-icon">💻</span>
                        <span class="dd-text"><span class="dd-label">Software para clínicas</span><span class="dd-desc">SaaS 100% online, sem instalação</span></span>
                    </a>
                    <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">
                        <span class="dd-icon">📲</span>
                        <span class="dd-text"><span class="dd-label">Confirmação por WhatsApp</span><span class="dd-desc">Lembrete e confirmação de consulta</span></span>
                    </a>
                    <div class="nav-divider"></div>
```

- [x] **Step 2: Adicionar o link na coluna "Recursos" do rodapé**

Localize:
```php
            <a href="<?=base_url()?>software-para-medicos">Software para Médicos</a>
            <a href="<?=base_url()?>sistema-gratuito-para-clinicas">Trial Gratuito</a>
```

Substitua por:
```php
            <a href="<?=base_url()?>software-para-medicos">Software para Médicos</a>
            <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">Confirmação por WhatsApp</a>
            <a href="<?=base_url()?>sistema-gratuito-para-clinicas">Trial Gratuito</a>
```

- [x] **Step 3: Validar sintaxe de `index-front.php`**

Run:
```powershell
php -l application/views/index-front.php
```
Expected:
```text
No syntax errors detected in application/views/index-front.php
```

- [x] **Step 4: Adicionar a landing em `sitemap.xml`**

Localize o bloco:
```xml
  <url>
    <loc>https://utecnologia.com.br/alternativa-clinica-nas-nuvens</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- Páginas institucionais — E-A-T / Google YMYL health signals -->
```

Substitua por:
```xml
  <url>
    <loc>https://utecnologia.com.br/alternativa-clinica-nas-nuvens</loc>
    <lastmod>2026-08-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- Páginas institucionais — E-A-T / Google YMYL health signals -->
```

- [x] **Step 5: Adicionar os 7 artigos em `sitemap-blog.xml`**

Localize o fim do arquivo:
```xml
  <url>
    <loc>https://utecnologia.com.br/blog/software-medico-como-escolher-para-consultorio-ou-clinica</loc>
    <lastmod>2026-06-02</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

</urlset>
```

Substitua por:
```xml
  <url>
    <loc>https://utecnologia.com.br/blog/software-medico-como-escolher-para-consultorio-ou-clinica</loc>
    <lastmod>2026-06-02</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/mensagem-de-lembrete-de-consulta-quando-enviar</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/como-fazer-mensagem-de-confirmacao-de-consulta-no-whatsapp</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/confirmacao-de-consulta-manual-ou-automatica</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/como-reduzir-faltas-de-pacientes-no-consultorio</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/o-que-fazer-quando-paciente-nao-confirma-consulta</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc>https://utecnologia.com.br/blog/mensagem-de-confirmacao-de-consulta-odontologica</loc>
    <lastmod>2026-08-31</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

</urlset>
```

- [x] **Step 6: Atualizar `lastmod` em `sitemap-index.xml`**

Substitua o conteúdo inteiro por:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://utecnologia.com.br/sitemap.xml</loc>
    <lastmod>2026-08-31</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://utecnologia.com.br/sitemap-blog.xml</loc>
    <lastmod>2026-08-31</lastmod>
  </sitemap>
</sitemapindex>
```

- [x] **Step 7: Validar os XML e o menu**

Run:
```powershell
[xml](Get-Content -Raw sitemap.xml); [xml](Get-Content -Raw sitemap-blog.xml); [xml](Get-Content -Raw sitemap-index.xml); "XML ok"
rg -c "confirmacao-de-consulta-por-whatsapp" sitemap.xml
rg -c "utecnologia.com.br/blog/" sitemap-blog.xml
```
Expected:
- `XML ok` impresso sem exceção (os 3 continuam bem formados)
- `sitemap.xml`: 1 ocorrência da landing
- `sitemap-blog.xml`: contagem antiga + 7

Smoke test manual: abrir `http://localhost/utec/` e conferir:
- dropdown "Sistema" mostra "Confirmação por WhatsApp" antes do divisor, apontando para `/confirmacao-de-consulta-por-whatsapp`
- rodapé, coluna "Recursos", tem o link "Confirmação por WhatsApp"

- [x] **Step 8: Commit**

```bash
git add application/views/index-front.php sitemap.xml sitemap-blog.xml sitemap-index.xml
git commit -m "chore: publicar landing confirmacao por whatsapp no menu e sitemaps"
```

---

## Self-Review

**Spec coverage:**
- [x] 1 landing `/confirmacao-de-consulta-por-whatsapp` com hero, "como funciona", features, "em resumo", FAQ, "leia também", CTA, 3 JSON-LD — Task 2
- [x] Rota + método no padrão `seo_*` — Task 1
- [x] 7 artigos de blog do cluster, com links internos para a landing, `/experimentar` e entre si — Task 3
- [x] Posicionamento: lembrete automático (cron horário, véspera/dia) descrito como ativo; limitações honestas na FAQ (reagendar, chatbot, limite trial, modelo aprovado) — Task 2 Steps 6-7, Task 3
- [x] Menu principal (dropdown "Sistema") + rodapé (coluna "Recursos") — Task 5 Steps 1-2
- [x] `sitemap.xml` (landing), `sitemap-blog.xml` (7 URLs), `sitemap-index.xml` (lastmod) — Task 5 Steps 4-6
- [x] Gate: menu + sitemaps num passo separado, condicionado ao cron — Task 5 header
- [x] Ledger atualizado com keywords testadas + nova frente + nota de watchlist — Task 4
- [x] Validação por `php -l` + smoke test + XML bem formado (sem suíte automatizada) — Steps de validação em cada task
- [x] Artigos "próxima leva" (LGPD, "o que dá para automatizar") documentados, fora do escopo — Task 4 Step 3

**Placeholder scan:** sem `TODO`/`TBD`/"implementar depois". Todo bloco de código é conteúdo final. Os `[nome]`, `[dia]`, `[hora]` dentro dos modelos de mensagem são marcadores intencionais do texto do artigo (o leitor preenche), não lacunas do plano.

**Type/slug consistency:**
- Rota `confirmacao-de-consulta-por-whatsapp` ↔ método `seo_confirmacao_whatsapp` ↔ view `confirmacao-de-consulta-por-whatsapp.php` — consistente em Tasks 1, 2, 5.
- Slugs de blog idênticos em Task 2 (bloco "Leia também"), Task 3 (`slug` de cada tupla + links internos entre artigos), Task 4 (ledger) e Task 5 (sitemap-blog): `modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp`, `mensagem-de-lembrete-de-consulta-quando-enviar`, `como-fazer-mensagem-de-confirmacao-de-consulta-no-whatsapp`, `confirmacao-de-consulta-manual-ou-automatica`, `como-reduzir-faltas-de-pacientes-no-consultorio`, `o-que-fazer-quando-paciente-nao-confirma-consulta`, `mensagem-de-confirmacao-de-consulta-odontologica`.
- Colunas do `INSERT` = colunas de `docs/blog-posts-seed.sql`, mesma ordem.
- Datas `2026-08-31` só nas entradas novas de sitemap; `lastmod` do index atualizado para a mesma data.
