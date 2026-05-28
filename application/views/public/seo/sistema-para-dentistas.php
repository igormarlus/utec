<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Dentistas e Clínicas Odontológicas — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para dentistas com agenda, prontuário odontológico e gestão de pacientes. Para consultório e clínica odontológica de todos os tamanhos. Experimente grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-dentistas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-dentistas">
    <meta property="og:title" content="Sistema para Dentistas — UTecnologia Saúde">
    <meta property="og:description" content="Agenda, prontuário e gestão de pacientes para dentistas e clínicas odontológicas. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Dentistas — UTecnologia Saúde">
    <meta name="twitter:description" content="Agenda, prontuário e gestão para dentistas e clínicas odontológicas. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --primary:#0ea5e9; --primary-dark:#0284c7; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --panel:#ffffff; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
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
        .badge-icon { width:28px; height:28px; background:#f0f9ff; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
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
                <div class="eyebrow">Software Odontológico</div>
                <h1><em>Sistema para dentistas</em> e clínicas odontológicas</h1>
                <p class="hero-text">
                    Organize sua agenda de atendimentos, mantenha o prontuário de cada paciente atualizado
                    e gerencie sua clínica ou consultório odontológico com eficiência — 100% online.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">Funciona para</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🦷</div> Dentistas autônomos</div>
                    <div class="badge-item"><div class="badge-icon">🏥</div> Clínicas odontológicas</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Consultórios com recepcionista</div>
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário por paciente</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Controle de exames e raio-x</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>O que o sistema oferece para sua clínica odontológica</h2>
        <p class="section-sub">Do agendamento à documentação clínica — tudo organizado em um único sistema.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Odontológico</h3>
                <p>Registre a evolução de cada atendimento, procedimentos realizados, histórico de queixas e documentos clínicos do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de Consultas</h3>
                <p>Organize os atendimentos por dentista, filtre por data e status. Cancele, remarque e confirme consultas com facilidade.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Imagens</h3>
                <p>Solicite exames e armazene laudos, radiografias e outros arquivos diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Multi-Dentistas</h3>
                <p>Clínica com vários dentistas? Cada profissional gerencia seus próprios pacientes com acesso individualizado.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏢</div>
                <h3>Gestão de Equipe</h3>
                <p>Cadastre recepcionistas e auxiliares com permissões específicas. A equipe operacional cuida da agenda sem acessar os prontuários.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Controle de Atendimentos</h3>
                <p>Visualize quantos atendimentos cada dentista realizou, em qual período e com qual status — para gestão e faturamento.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para dentistas</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema tem odontograma?</div>
                <div class="faq-a">O UTecnologia Saúde é uma plataforma de gestão clínica geral, com prontuário de texto livre estruturado, agenda e exames. O odontograma gráfico (mapeamento de dentes) não está disponível neste momento, mas os registros de procedimentos e evoluções são documentados no prontuário do paciente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso armazenar radiografias no sistema?</div>
                <div class="faq-a">Sim. O sistema permite anexar arquivos (PDF, imagens) diretamente no prontuário do paciente. Você pode armazenar laudos, radiografias digitais e outros documentos clínicos associados ao histórico de cada paciente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Funciona para consultório com apenas 1 dentista?</div>
                <div class="faq-a">Sim. O plano Solo é ideal para dentistas autônomos: 1 profissional, 2 colaboradores (ex: recepcionista) e pacientes ilimitados, por R$ 79/mês.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">A recepcionista pode acessar a agenda sem ver os prontuários?</div>
                <div class="faq-a">Sim. Colaboradores (nível 4) têm acesso à agenda e ao cadastro de pacientes, mas não ao prontuário clínico — que fica restrito ao profissional de saúde responsável.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Organize sua clínica odontológica</h2>
            <p>30 dias grátis para você testar agenda, prontuário e gestão de equipe.<br>Sem cartão de crédito. Começa em minutos.</p>
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
  "@type": "WebPage",
  "name": "Sistema para Dentistas — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-para-dentistas",
  "description": "Sistema para dentistas com agenda, prontuário odontológico e gestão de pacientes.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Sistema para Dentistas", "item": "https://utecnologia.com.br/sistema-para-dentistas"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema tem odontograma?", "acceptedAnswer": {"@type": "Answer", "text": "O UTecnologia Saúde é uma plataforma de gestão clínica geral. O odontograma gráfico não está disponível, mas os registros de procedimentos e evoluções são documentados em prontuário estruturado."}},
    {"@type": "Question", "name": "Posso armazenar radiografias no sistema?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema permite anexar arquivos (PDF, imagens) diretamente no prontuário do paciente, incluindo laudos e radiografias digitais."}},
    {"@type": "Question", "name": "Funciona para consultório com apenas 1 dentista?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo é ideal para dentistas autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês."}}
  ]
}
</script>
</body>
</html>
