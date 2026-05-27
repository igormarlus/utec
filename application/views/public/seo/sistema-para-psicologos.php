<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Psicólogos — Prontuário de Sessões e Agenda Online | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para psicólogos com prontuário de sessões, agenda online e controle de pacientes. Para consultório e clínica de psicologia. Organizado, seguro e 100% online. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-psicologos">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-psicologos">
    <meta property="og:title" content="Sistema para Psicólogos — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário de sessões, agenda e gestão de pacientes para psicólogos. 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Psicólogos — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário, agenda e gestão para psicólogos. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#0f172a; --muted:#475569; --primary:#7c3aed; --primary-dark:#6d28d9; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --panel:#ffffff; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
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
        .hero { padding:80px 0 60px; background:linear-gradient(160deg,#f5f3ff 0%,#f8fafc 60%); }
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
        .badge-icon { width:28px; height:28px; background:#f5f3ff; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
        .section { padding:72px 0; }
        .section-label { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); text-align:center; margin-bottom:12px; }
        h2 { font-size:32px; font-weight:800; text-align:center; margin-bottom:16px; }
        .section-sub { font-size:17px; color:var(--muted); text-align:center; max-width:580px; margin:0 auto 48px; }
        .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .feature-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:28px; }
        .feature-icon { font-size:28px; margin-bottom:14px; }
        .feature-card h3 { font-size:16px; font-weight:700; margin-bottom:8px; }
        .feature-card p { font-size:14px; color:var(--muted); line-height:1.6; }
        .privacy-box { background:linear-gradient(135deg,#f5f3ff,#ede9fe); border:1px solid #ddd6fe; border-radius:20px; padding:40px; margin-top:48px; }
        .privacy-box h3 { font-size:20px; font-weight:800; margin-bottom:12px; }
        .privacy-box p { font-size:15px; color:var(--muted); line-height:1.7; }
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
                <div class="eyebrow">Software para Psicologia</div>
                <h1><em>Sistema para psicólogos</em> com prontuário de sessões e agenda organizada</h1>
                <p class="hero-text">
                    Registre evoluções de sessão, gerencie sua agenda de atendimentos e mantenha o histórico completo
                    de cada paciente — de forma segura, sigilosa e acessível de qualquer lugar.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">Funciona para</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🧠</div> Psicólogos clínicos autônomos</div>
                    <div class="badge-item"><div class="badge-icon">🏢</div> Clínicas de psicologia com equipe</div>
                    <div class="badge-item"><div class="badge-icon">👨‍👩‍👧</div> Psicólogos infantis e familiares</div>
                    <div class="badge-item"><div class="badge-icon">🏢</div> Psicólogos organizacionais</div>
                    <div class="badge-item"><div class="badge-icon">📱</div> Atendimentos online e presenciais</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos para psicologia</div>
        <h2>O que o sistema oferece para psicólogos</h2>
        <p class="section-sub">Uma plataforma que se encaixa na rotina do consultório ou clínica de psicologia.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Evolução de Sessão</h3>
                <p>Registre a evolução de cada sessão no prontuário do paciente. Mantenha o histórico cronológico de todo o processo terapêutico.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de Sessões</h3>
                <p>Organize sua agenda semanal de atendimentos. Visualize os horários livres, confirme e cancele sessões com facilidade.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3>Ficha do Paciente</h3>
                <p>Dados de cadastro, contato, histórico clínico e documentos do paciente em um perfil completo e organizado.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Anexo de Documentos</h3>
                <p>Anexe laudos, relatórios, avaliações psicológicas e outros documentos diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Clínica com Vários Psicólogos</h3>
                <p>Para clínicas com mais de um psicólogo: cada profissional gerencia seus próprios pacientes com privacidade e isolamento de dados.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Sigilo Garantido</h3>
                <p>Acesso protegido por login e senha. Dados dos pacientes isolados por clínica — apenas profissionais autorizados têm acesso.</p>
            </div>
        </div>

        <div class="privacy-box">
            <h3>🔒 Sigilo profissional e segurança dos dados</h3>
            <p>O UTecnologia Saúde foi desenvolvido com privacidade em mente. Os dados de cada clínica são armazenados em ambiente isolado (multi-tenant), com acesso restrito a usuários cadastrados e autorizados. Cada psicólogo acessa apenas seus próprios registros, sem exposição a dados de outros profissionais da plataforma.</p>
        </div>
    </div>
</section>

<section class="section" style="background:#f5f3ff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para psicólogos</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é adequado para psicólogos autônomos?</div>
                <div class="faq-a">Sim. O plano Solo foi criado exatamente para profissionais autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia sua agenda e prontuários de forma simples, sem precisar de infraestrutura de clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Os registros das sessões ficam protegidos?</div>
                <div class="faq-a">Sim. O acesso ao sistema é protegido por login e senha. Os dados dos pacientes são isolados por clínica — nenhum dado de uma clínica é visível em outra. Para profissionais que trabalham em mais de um local, cada ambiente é completamente separado.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso usar para clínica de psicologia com vários terapeutas?</div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 psicólogos e o plano Pro até 20. Cada profissional tem sua própria agenda e acessa apenas os prontuários dos seus pacientes, mantendo o sigilo entre profissionais da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema atende psicólogos que trabalham online?</div>
                <div class="faq-a">Sim. Por ser 100% online, o sistema é acessível de qualquer dispositivo — ideal para quem atende de forma remota ou em múltiplos locais. Você acessa prontuários, agenda e histórico de qualquer lugar com internet.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Experimente o sistema para psicólogos</h2>
            <p>30 dias grátis para você organizar seu consultório ou clínica.<br>Sem cartão de crédito. Sem compromisso.</p>
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
                <a href="<?=base_url()?>sistema-para-clinica-de-fisioterapia">Fisioterapia</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sistema para Psicólogos — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-para-psicologos",
  "description": "Sistema para psicólogos com prontuário de sessões, agenda online e controle de pacientes.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Sistema para Psicólogos", "item": "https://utecnologia.com.br/sistema-para-psicologos"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema é adequado para psicólogos autônomos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo foi criado para profissionais autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês."}},
    {"@type": "Question", "name": "Os registros das sessões ficam protegidos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O acesso é protegido por login e senha, com dados isolados por clínica. Apenas profissionais autorizados têm acesso."}},
    {"@type": "Question", "name": "O sistema atende psicólogos que trabalham online?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Por ser 100% online, é acessível de qualquer dispositivo — ideal para atendimentos remotos ou em múltiplos locais."}}
  ]
}
</script>
</body>
</html>
