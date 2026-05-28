<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Clínica Médica — Prontuário, Agenda e Exames | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínica médica com prontuário eletrônico completo, agenda inteligente por profissional e controle de exames. Para clínico geral, especialistas e consultórios. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinica-medica">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinica-medica">
    <meta property="og:title" content="Sistema para Clínica Médica — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário eletrônico, agenda inteligente e gestão completa para clínica médica. Teste 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Clínica Médica — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico, agenda e gestão para clínica médica. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --subtle:#94a3b8; --primary:#0ea5e9; --primary-dark:#0284c7; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --panel:#ffffff; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
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
        h1 { font-size:42px; font-weight:800; line-height:1.12; color:var(--ink); margin-bottom:20px; }
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
        .prontuario-section { background:#fff; }
        .pront-grid { display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; }
        .pront-list { display:flex; flex-direction:column; gap:16px; margin-top:24px; }
        .pront-item { display:flex; gap:14px; align-items:flex-start; }
        .pront-dot { width:8px; height:8px; background:var(--primary); border-radius:50%; margin-top:6px; flex-shrink:0; }
        .pront-item h4 { font-size:14px; font-weight:700; margin-bottom:4px; }
        .pront-item p { font-size:13px; color:var(--muted); }
        .plans-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .plan-card { background:#fff; border:1.5px solid var(--border); border-radius:var(--radius); padding:28px; }
        .plan-card.featured { border-color:var(--primary); box-shadow:0 0 0 3px rgba(14,165,233,.1); }
        .plan-badge { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--primary); margin-bottom:8px; }
        .plan-name { font-size:20px; font-weight:800; margin-bottom:4px; }
        .plan-price { font-size:32px; font-weight:800; color:var(--primary); margin:12px 0 4px; }
        .plan-price span { font-size:14px; font-weight:500; color:var(--muted); }
        .plan-features { list-style:none; margin-top:16px; display:flex; flex-direction:column; gap:8px; }
        .plan-features li { font-size:13px; color:var(--muted); }
        .plan-features li::before { content:"✓ "; color:var(--accent); font-weight:700; }
        .faq-list { max-width:720px; margin:0 auto; display:flex; flex-direction:column; gap:12px; }
        .faq-item { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; }
        .faq-q { font-size:15px; font-weight:700; margin-bottom:8px; }
        .faq-a { font-size:14px; color:var(--muted); line-height:1.7; }
        .cta-box { background:var(--primary); border-radius:24px; padding:56px 40px; text-align:center; }
        .cta-box h2 { color:#fff; font-size:30px; margin-bottom:12px; }
        .cta-box p { color:rgba(255,255,255,.85); font-size:16px; margin-bottom:28px; }
        .btn-white { display:inline-block; background:#fff; color:var(--primary-dark); padding:14px 32px; border-radius:999px; font-weight:800; font-size:15px; text-decoration:none; }
        .footer { background:var(--ink); color:rgba(255,255,255,.6); padding:32px 0; }
        .footer-inner { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .footer-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:13px; margin-left:20px; }
        .footer-brand { font-size:14px; font-weight:700; color:#fff; }
        @media(max-width:900px) { .hero-inner,.pront-grid { grid-template-columns:1fr; } .features-grid,.plans-grid { grid-template-columns:1fr 1fr; } h1 { font-size:30px; } }
        @media(max-width:600px) { .features-grid,.plans-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:34px;width:auto;display:block"></a>
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
                <div class="eyebrow">Software Médico</div>
                <h1><em>Sistema para clínica médica</em> com prontuário, agenda e exames integrados</h1>
                <p class="hero-text">
                    Gerencie sua clínica médica com um sistema completo: prontuário eletrônico estruturado,
                    agenda inteligente por médico e controle de exames solicitados — tudo em um só lugar.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">Ideal para</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🩺</div> Médicos clínicos gerais</div>
                    <div class="badge-item"><div class="badge-icon">🏥</div> Clínicas com múltiplos médicos</div>
                    <div class="badge-item"><div class="badge-icon">👨‍⚕️</div> Especialistas autônomos</div>
                    <div class="badge-item"><div class="badge-icon">📋</div> Consultórios com prontuário digital</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Clínicas que solicitam exames</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section features">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>O que o sistema oferece para clínicas médicas</h2>
        <p class="section-sub">Do atendimento ao prontuário — cada etapa documentada e acessível de forma organizada.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico Médico</h3>
                <p>Registre anamnese, sinais vitais, evolução clínica, hipóteses diagnósticas, CID e conduta. Histórico completo do paciente disponível em toda consulta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda Médica Inteligente</h3>
                <p>Visualize os atendimentos do dia, semana ou mês por médico. Filtre por status (aguardando, em consulta, concluído). Cancele e remarque sem retrabalho.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Solicitação de Exames</h3>
                <p>Solicite exames no prontuário, registre resultados e armazene laudos em PDF diretamente no arquivo do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Multi-Médico</h3>
                <p>Adicione vários médicos na mesma clínica. Cada um acessa apenas os próprios pacientes, com controle hierárquico de permissões.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios de Produção</h3>
                <p>Número de consultas por médico, por período e por status. Gestão da produtividade da clínica com dados reais.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Segurança por Perfil</h3>
                <p>Médico, recepcionista e administrador têm acessos diferentes. Os dados dos pacientes são protegidos e isolados por clínica.</p>
            </div>
        </div>
    </div>
</section>

<section class="section prontuario-section">
    <div class="wrap">
        <div class="pront-grid">
            <div>
                <div class="section-label" style="text-align:left">Prontuário eletrônico</div>
                <h2 style="text-align:left">Tudo que o médico precisa registrar em cada consulta</h2>
                <p style="color:var(--muted);font-size:16px;margin-top:12px;">O prontuário do UTecnologia Saúde foi estruturado para o fluxo real de uma consulta médica, do registro inicial ao histórico de retornos.</p>
                <div class="pront-list">
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Anamnese estruturada</h4><p>Queixa principal, história da doença atual, antecedentes pessoais e familiares.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Exame físico e sinais vitais</h4><p>PA, FC, temperatura, peso, altura, SpO2 e achados relevantes.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Hipótese diagnóstica e CID</h4><p>Registro das hipóteses com codificação CID-10 para controle e relatórios.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Conduta e prescrição</h4><p>Documentação da conduta adotada, prescrições e orientações ao paciente.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Timeline de atendimentos</h4><p>Toda a história de consultas do paciente em ordem cronológica e acessível.</p></div></div>
                </div>
            </div>
            <div style="background:#f0f9ff;border-radius:24px;padding:36px;">
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);margin-bottom:20px;">Planos disponíveis</div>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid var(--border);">
                        <div style="font-size:13px;font-weight:700;">Solo</div>
                        <div style="font-size:24px;font-weight:800;color:var(--primary);">R$ 79<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
                        <div style="font-size:13px;color:var(--muted);margin-top:4px;">1 médico · 2 colaboradores</div>
                    </div>
                    <div style="background:#fff;border-radius:12px;padding:16px;border:2px solid var(--primary);">
                        <div style="font-size:13px;font-weight:700;">Clínica</div>
                        <div style="font-size:24px;font-weight:800;color:var(--primary);">R$ 199<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
                        <div style="font-size:13px;color:var(--muted);margin-top:4px;">Até 5 médicos · 10 colaboradores</div>
                    </div>
                    <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid var(--border);">
                        <div style="font-size:13px;font-weight:700;">Pro</div>
                        <div style="font-size:24px;font-weight:800;color:var(--primary);">R$ 399<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
                        <div style="font-size:13px;color:var(--muted);margin-top:4px;">Até 20 médicos · 50 colaboradores</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#f0f9ff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para clínica médica</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O prontuário segue os padrões do CFM?</div>
                <div class="faq-a">O sistema oferece campos estruturados para anamnese, evolução clínica, hipóteses diagnósticas, CID e conduta — alinhados às boas práticas de documentação médica. Para necessidades específicas de conformidade regulatória, recomendamos validar com seu conselho profissional.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso ter uma clínica com vários médicos?</div>
                <div class="faq-a">Sim. O sistema suporta múltiplos profissionais na mesma clínica. Os planos Clínica e Pro suportam de 5 a 20 médicos, cada um com sua própria agenda e acesso ao prontuário dos seus pacientes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema funciona para clínico geral e especialistas?</div>
                <div class="faq-a">Sim. O prontuário é flexível o suficiente para clínico geral, cardiologistas, dermatologistas, endocrinologistas, ginecologistas, neurologistas e outras especialidades médicas que necessitam de registro de consultas e histórico de pacientes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como a recepcionista acessa o sistema?</div>
                <div class="faq-a">Você cadastra a recepcionista como colaboradora (nível 4). Ela tem acesso à agenda e ao cadastro de pacientes, mas não ao prontuário clínico — que fica restrito ao médico responsável.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Experimente o sistema para clínica médica</h2>
            <p>30 dias grátis para você e sua equipe. Sem cartão de crédito.<br>Do cadastro ao primeiro prontuário registrado em minutos.</p>
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
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário eletrônico</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sistema para Clínica Médica — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-para-clinica-medica",
  "description": "Sistema para clínica médica com prontuário eletrônico completo, agenda e controle de exames.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Sistema para Clínica Médica", "item": "https://utecnologia.com.br/sistema-para-clinica-medica"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O prontuário segue os padrões do CFM?", "acceptedAnswer": {"@type": "Answer", "text": "O sistema oferece campos estruturados para anamnese, evolução clínica, hipóteses diagnósticas, CID e conduta — alinhados às boas práticas de documentação médica."}},
    {"@type": "Question", "name": "Posso ter uma clínica com vários médicos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema suporta múltiplos profissionais. Os planos Clínica e Pro suportam de 5 a 20 médicos, cada um com sua própria agenda e prontuário."}},
    {"@type": "Question", "name": "O sistema funciona para clínico geral e especialistas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Funciona para clínico geral, cardiologistas, dermatologistas, endocrinologistas, ginecologistas, neurologistas e outras especialidades médicas."}}
  ]
}
</script>
</body>
</html>
