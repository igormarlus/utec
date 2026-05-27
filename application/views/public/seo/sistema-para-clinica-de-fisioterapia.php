<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Clínica de Fisioterapia — Prontuário e Agenda de Sessões | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínica de fisioterapia com prontuário de evolução, agenda de sessões e controle de pacientes. Para fisioterapeutas autônomos e clínicas de fisio. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia">
    <meta property="og:title" content="Sistema para Clínica de Fisioterapia — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário de evolução, agenda de sessões e gestão de pacientes para fisioterapia. Teste 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Clínica de Fisioterapia — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário e agenda para clínica de fisioterapia. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#0f172a; --muted:#475569; --primary:#059669; --primary-dark:#047857; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter',sans-serif; color:var(--ink); background:var(--paper); line-height:1.6; }
        a { color:var(--primary-dark); }
        .wrap { max-width:1100px; margin:0 auto; padding:0 20px; }
        .topnav { background:#fff; border-bottom:1px solid var(--border); padding:14px 0; }
        .topnav .wrap { display:flex; justify-content:space-between; align-items:center; }
        .brand { font-size:17px; font-weight:800; color:var(--ink); text-decoration:none; }
        .brand span { color:#0ea5e9; }
        .nav-links { display:flex; gap:24px; align-items:center; }
        .nav-links a { font-size:14px; font-weight:500; color:var(--muted); text-decoration:none; }
        .btn-nav { background:var(--primary); color:#fff !important; padding:8px 18px; border-radius:999px; font-weight:700 !important; font-size:13px !important; }
        .hero { padding:80px 0 60px; background:linear-gradient(160deg,#f0fdf4 0%,#f8fafc 60%); }
        .hero-inner { display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center; }
        .eyebrow { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); margin-bottom:12px; }
        h1 { font-size:42px; font-weight:800; line-height:1.12; margin-bottom:20px; }
        h1 em { font-style:normal; color:var(--primary); }
        .hero-text { font-size:18px; color:var(--muted); line-height:1.7; margin-bottom:32px; }
        .hero-cta { display:flex; gap:12px; flex-wrap:wrap; }
        .btn-primary { display:inline-block; background:var(--primary); color:#fff; padding:14px 28px; border-radius:999px; font-weight:700; font-size:15px; text-decoration:none; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { display:inline-block; border:1.5px solid var(--border); color:var(--muted); padding:13px 24px; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none; }
        .hero-badge { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:28px; box-shadow:var(--shadow); }
        .badge-title { font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:16px; }
        .badge-list { display:flex; flex-direction:column; gap:10px; }
        .badge-item { display:flex; align-items:center; gap:10px; font-size:14px; font-weight:500; }
        .badge-icon { width:28px; height:28px; background:#f0fdf4; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
        .section { padding:72px 0; }
        .section-label { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); text-align:center; margin-bottom:12px; }
        h2 { font-size:32px; font-weight:800; text-align:center; margin-bottom:16px; }
        .section-sub { font-size:17px; color:var(--muted); text-align:center; max-width:580px; margin:0 auto 48px; }
        .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
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
        @media(max-width:900px) { .hero-inner { grid-template-columns:1fr; } .features-grid { grid-template-columns:1fr 1fr; } h1 { font-size:30px; } }
        @media(max-width:600px) { .features-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Software para Fisioterapia</div>
                <h1><em>Sistema para clínica de fisioterapia</em> com evolução de sessões e agenda</h1>
                <p class="hero-text">
                    Registre a evolução de cada sessão, acompanhe o progresso do paciente ao longo do tratamento
                    e organize a agenda da sua clínica de fisioterapia — tudo em um sistema online.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">Funciona para</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🏃</div> Fisioterapeutas autônomos</div>
                    <div class="badge-item"><div class="badge-icon">🏥</div> Clínicas de fisioterapia</div>
                    <div class="badge-item"><div class="badge-icon">💪</div> RPG, pilates terapêutico</div>
                    <div class="badge-item"><div class="badge-icon">🦴</div> Ortopedia e reabilitação</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Sessões recorrentes</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos para fisioterapia</div>
        <h2>O que o sistema oferece para clínicas de fisioterapia</h2>
        <p class="section-sub">Da triagem à alta clínica — cada sessão documentada e acompanhada.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Evolução por Sessão</h3>
                <p>Registre a evolução de cada sessão de fisioterapia — técnicas aplicadas, resposta do paciente e observações clínicas — em uma timeline organizada.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de Sessões</h3>
                <p>Organize os atendimentos diários. Marque sessões recorrentes, filtre por fisioterapeuta e visualize a ocupação de toda a semana.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3>Ficha Inicial do Paciente</h3>
                <p>Registro da avaliação inicial: queixa, histórico, diagnóstico de encaminhamento, objetivos do tratamento e plano terapêutico.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Acompanhamento do Tratamento</h3>
                <p>Visualize a progressão do paciente através do histórico de evoluções. Compare sessões e identifique a evolução do quadro clínico.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Exames e Imagens</h3>
                <p>Armazene raio-X, ressonâncias e laudos médicos diretamente no prontuário do paciente para referência durante as sessões.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Multi-Fisioterapeuta</h3>
                <p>Para clínicas com vários fisioterapeutas: cada profissional gerencia sua agenda e seus pacientes com isolamento de acesso.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#f0fdf4;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para fisioterapia</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é adequado para fisioterapeuta autônomo?</div>
                <div class="faq-a">Sim. O plano Solo é ideal para fisioterapeutas autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia sua agenda e as evoluções de sessão de forma simples.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso registrar as sessões de tratamento ao longo do tempo?</div>
                <div class="faq-a">Sim. Cada sessão registra uma evolução no prontuário do paciente. A timeline cronológica mostra a progressão completa do tratamento, sessão por sessão, desde a avaliação inicial até a alta.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Funciona para clínicas que atendem convênios?</div>
                <div class="faq-a">O sistema gerencia o lado clínico (prontuário, agenda, evoluções). Para faturamento e gestão de convênios, recomendamos verificar a compatibilidade com os processos específicos do seu convênio.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Organize sua clínica de fisioterapia</h2>
            <p>30 dias grátis para testar prontuário, agenda de sessões e gestão de pacientes.<br>Sem cartão de crédito. Comece em minutos.</p>
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
                <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sistema para Clínica de Fisioterapia — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia",
  "description": "Sistema para clínica de fisioterapia com prontuário de evolução, agenda de sessões e controle de pacientes.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Fisioterapia", "item": "https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema é adequado para fisioterapeuta autônomo?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo é ideal: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês."}},
    {"@type": "Question", "name": "Posso registrar as sessões de tratamento ao longo do tempo?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Cada sessão registra uma evolução no prontuário. A timeline mostra a progressão completa do tratamento desde a avaliação inicial até a alta."}}
  ]
}
</script>
</body>
</html>
