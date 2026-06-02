<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Consultório Médico — Simples, Online e Completo | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para consultório médico com agenda online, prontuário eletrônico e controle de pacientes. Para médicos autônomos e pequenos consultórios. Plano Solo a partir de R$ 79/mês. Teste grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-consultorio-medico">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-consultorio-medico">
    <meta property="og:title" content="Sistema para Consultório Médico — UTecnologia Saúde">
    <meta property="og:description" content="Agenda, prontuário e controle de pacientes para médicos autônomos. Plano Solo a partir de R$ 79/mês.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Consultório Médico — UTecnologia Saúde">
    <meta name="twitter:description" content="Agenda e prontuário para médicos autônomos. A partir de R$ 79/mês.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --primary:#0ea5e9; --primary-dark:#0284c7; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter',sans-serif; color:var(--ink); background:var(--paper); line-height:1.6; }
        a { color:var(--primary-dark); }
        .wrap { max-width:1100px; margin:0 auto; padding:0 20px; }
        .topnav { background:#fff; border-bottom:1px solid var(--border); padding:14px 0; }
        .topnav .wrap { display:flex; justify-content:space-between; align-items:center; }
        .brand { font-size:17px; font-weight:800; color:var(--ink); text-decoration:none; }
        .brand span { color:var(--primary); }
        .nav-links { display:flex; gap:24px; align-items:center; }
        .nav-links a { font-size:14px; font-weight:500; color:var(--muted); text-decoration:none; }
        .btn-nav { background:var(--primary); color:#fff !important; padding:8px 18px; border-radius:999px; font-weight:700 !important; font-size:13px !important; }
        .hero { padding:80px 0 60px; background:linear-gradient(160deg,#f0f9ff 0%,#f8fafc 60%); }
        .hero-inner { display:grid; grid-template-columns:3fr 2fr; gap:60px; align-items:center; }
        .eyebrow { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); margin-bottom:12px; }
        h1 { font-size:42px; font-weight:800; line-height:1.12; margin-bottom:20px; }
        h1 em { font-style:normal; color:var(--primary); }
        .hero-text { font-size:18px; color:var(--muted); line-height:1.7; margin-bottom:32px; }
        .hero-cta { display:flex; gap:12px; flex-wrap:wrap; }
        .btn-primary { display:inline-block; background:var(--primary); color:#fff; padding:14px 28px; border-radius:999px; font-weight:700; font-size:15px; text-decoration:none; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { display:inline-block; border:1.5px solid var(--border); color:var(--muted); padding:13px 24px; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none; }
        .plan-highlight { background:#fff; border:2px solid var(--primary); border-radius:20px; padding:28px; box-shadow:0 0 0 4px rgba(14,165,233,.08); }
        .plan-label { font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--primary); margin-bottom:6px; }
        .plan-name { font-size:22px; font-weight:800; margin-bottom:4px; }
        .plan-desc { font-size:13px; color:var(--muted); margin-bottom:16px; }
        .plan-price { font-size:36px; font-weight:800; color:var(--primary); }
        .plan-price span { font-size:14px; font-weight:500; color:var(--muted); }
        .plan-features { list-style:none; margin-top:16px; display:flex; flex-direction:column; gap:8px; }
        .plan-features li { font-size:13px; color:var(--muted); }
        .plan-features li::before { content:"✓ "; color:var(--accent); font-weight:700; }
        .section { padding:72px 0; }
        .section-label { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); text-align:center; margin-bottom:12px; }
        h2 { font-size:32px; font-weight:800; text-align:center; margin-bottom:16px; }
        .section-sub { font-size:17px; color:var(--muted); text-align:center; max-width:580px; margin:0 auto 48px; }
        .features-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:24px; max-width:760px; margin:0 auto; }
        .feature-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:28px; }
        .feature-icon { font-size:28px; margin-bottom:14px; }
        .feature-card h3 { font-size:16px; font-weight:700; margin-bottom:8px; }
        .feature-card p { font-size:14px; color:var(--muted); line-height:1.6; }
        .faq-list { max-width:720px; margin:0 auto; display:flex; flex-direction:column; gap:12px; }
        .faq-item { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; }
        .faq-q { font-size:15px; font-weight:700; margin-bottom:8px; }
        .faq-a { font-size:14px; color:var(--muted); line-height:1.7; }
        .cta-box { background:var(--primary); border-radius:24px; padding:56px 40px; text-align:center; }
        .cta-box h2 { color:#fff; font-size:30px; margin-bottom:12px; }
        .cta-box p { color:rgba(255,255,255,.85); font-size:16px; margin-bottom:28px; }
        .btn-white { display:inline-block; background:#fff; color:var(--primary-dark); padding:14px 32px; border-radius:999px; font-weight:800; font-size:15px; text-decoration:none; }
        .footer { background:#0f172a; color:rgba(255,255,255,.6); padding:32px 0; }
        .footer-inner { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .footer-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:13px; margin-left:20px; }
        .footer-brand { font-size:14px; font-weight:700; color:#fff; }
        @media(max-width:900px) { .hero-inner { grid-template-columns:1fr; } h1 { font-size:30px; } }
        @media(max-width:600px) { .features-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Para médicos autônomos</div>
                <h1><em>Sistema para consultório médico</em> simples, online e acessível</h1>
                <p class="hero-text">
                    Gerencia sua agenda, mantém o prontuário eletrônico dos seus pacientes e controla
                    seus atendimentos — tudo em um sistema online feito para médicos que trabalham no consultório.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="plan-highlight">
                <div class="plan-label">Ideal para consultórios</div>
                <div class="plan-name">Plano Solo</div>
                <div class="plan-desc">Para médicos autônomos e consultórios pequenos</div>
                <div class="plan-price">R$ 79<span>/mês</span></div>
                <ul class="plan-features">
                    <li>1 profissional médico</li>
                    <li>2 colaboradores (recepcionista)</li>
                    <li>Pacientes ilimitados</li>
                    <li>Prontuário eletrônico completo</li>
                    <li>Agenda inteligente</li>
                    <li>Exames e documentos</li>
                    <li>Trial de 30 dias grátis</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos para consultório</div>
        <h2>O que o sistema oferece para seu consultório</h2>
        <p class="section-sub">Simples de usar, completo no que importa.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda do Consultório</h3>
                <p>Visualize e gerencie todos os seus atendimentos. Filtre por data e status. A recepcionista agenda pelo sistema, você apenas atende.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Digital</h3>
                <p>Registre anamnese, evolução clínica, diagnóstico e conduta. Histórico completo do paciente acessível em toda consulta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Controle de Exames</h3>
                <p>Solicite exames, registre resultados e armazene laudos diretamente no prontuário do paciente — sem precisar de pastas físicas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Recepcionista no Sistema</h3>
                <p>Cadastre até 2 colaboradores no plano Solo. A recepcionista gerencia a agenda sem acessar os prontuários clínicos.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para consultório médico</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é adequado para médico que trabalha sozinho no consultório?</div>
                <div class="faq-a">Sim. O plano Solo foi criado exatamente para essa situação: 1 médico, até 2 colaboradores (ex: recepcionista) e pacientes ilimitados por R$ 79/mês. Você gerencia tudo sem precisar de um sistema corporativo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Minha recepcionista pode agendar consultas pelo sistema?</div>
                <div class="faq-a">Sim. Você cadastra a recepcionista como colaboradora. Ela tem acesso à agenda e ao cadastro de pacientes para agendar, confirmar e cancelar consultas, sem visualizar os prontuários clínicos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso instalar algum programa no computador do consultório?</div>
                <div class="faq-a">Não. O sistema é 100% online e acessível por qualquer navegador — no computador do consultório, no tablet ou no celular. Não requer instalação, servidor local ou manutenção técnica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">E se eu precisar crescer e adicionar mais médicos depois?</div>
                <div class="faq-a">Basta migrar para um plano maior. O plano Clínica (R$ 199/mês) suporta até 5 médicos e o plano Pro (R$ 399/mês) até 20 médicos, sem perda de dados.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Comece a usar no seu consultório hoje</h2>
            <p>30 dias grátis para organizar sua agenda e prontuários.<br>Plano Solo a partir de R$ 79/mês. Sem cartão para o trial.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>">Início</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/sistema-para-consultorio-medico",
  "description": "Sistema para consultório médico com agenda online, prontuário eletrônico e controle de pacientes.",
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
    {"@type": "ListItem", "position": 3, "name": "Sistema para Consultório Médico", "item": "https://utecnologia.com.br/sistema-para-consultorio-medico"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema é adequado para médico que trabalha sozinho?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo foi criado para isso: 1 médico, até 2 colaboradores e pacientes ilimitados por R$ 79/mês."}},
    {"@type": "Question", "name": "Minha recepcionista pode agendar consultas pelo sistema?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Você cadastra a recepcionista como colaboradora com acesso à agenda e cadastro de pacientes, sem acesso aos prontuários clínicos."}},
    {"@type": "Question", "name": "Preciso instalar algum programa no consultório?", "acceptedAnswer": {"@type": "Answer", "text": "Não. O sistema é 100% online, acessível por qualquer navegador — sem instalação, servidor local ou manutenção técnica."}}
  ]
}
</script>
</body>
</html>

