<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Prontuário Eletrônico para Clínicas — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de prontuário eletrônico completo para clínicas e consultórios. Anamnese, evolução clínica, exames e histórico do paciente em um registro digital seguro e acessível. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-prontuario-eletronico">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-prontuario-eletronico">
    <meta property="og:title" content="Sistema de Prontuário Eletrônico — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário eletrônico completo para clínicas e consultórios. Anamnese, evolução, exames e histórico do paciente. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema de Prontuário Eletrônico — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico para clínicas e consultórios. 30 dias grátis.">
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
        .vs-table { background:#fff; border-radius:20px; border:1px solid var(--border); overflow:hidden; }
        .vs-header { display:grid; grid-template-columns:1fr 1fr 1fr; background:#f8fafc; padding:16px 24px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
        .vs-header .col-utec { color:var(--primary); }
        .vs-row { display:grid; grid-template-columns:1fr 1fr 1fr; padding:14px 24px; border-top:1px solid var(--border); font-size:14px; align-items:center; }
        .vs-row:nth-child(even) { background:#fafbfc; }
        .check { color:var(--accent); font-weight:700; }
        .cross { color:#ef4444; font-weight:700; }
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
        @media(max-width:900px) { .hero-inner { grid-template-columns:1fr; } .features-grid { grid-template-columns:1fr 1fr; } h1 { font-size:30px; } .vs-header,.vs-row { grid-template-columns:1fr 1fr; } .vs-header .col-papel,.vs-row .col-papel { display:none; } }
        @media(max-width:600px) { .features-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Prontuário Eletrônico</div>
                <h1><em>Sistema de prontuário eletrônico</em> para clínicas e consultórios</h1>
                <p class="hero-text">
                    Substitua o papel e as planilhas por um prontuário eletrônico estruturado, acessível de qualquer dispositivo,
                    com histórico completo de cada paciente organizado e seguro.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>sistema-para-clinicas" class="btn-outline">Ver especialidades</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">Campos do prontuário</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">📝</div> Anamnese estruturada</div>
                    <div class="badge-item"><div class="badge-icon">📈</div> Evolução clínica por consulta</div>
                    <div class="badge-item"><div class="badge-icon">🏥</div> Hipótese diagnóstica</div>
                    <div class="badge-item"><div class="badge-icon">💊</div> Conduta e prescrição</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Exames solicitados e resultados</div>
                    <div class="badge-item"><div class="badge-icon">📁</div> Arquivos e documentos</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que o prontuário eletrônico precisa ter</h2>
        <p class="section-sub">Uma plataforma pensada para o fluxo real de atendimento clínico.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Anamnese e Histórico</h3>
                <p>Registre queixa principal, histórico da doença atual, antecedentes pessoais e familiares na primeira consulta. Disponível em todas as consultas seguintes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Evolução por Atendimento</h3>
                <p>Cada consulta registra uma evolução clínica separada, mantendo uma timeline cronológica completa da trajetória do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames Integrados</h3>
                <p>Solicite exames durante o atendimento, registre resultados e armazene laudos diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Documentos e Arquivos</h3>
                <p>Anexe PDFs, imagens e outros arquivos clínicos ao prontuário. Tudo associado ao histórico do paciente e acessível quando necessário.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Acesso por Perfil</h3>
                <p>O prontuário é acessível apenas ao profissional responsável. Recepcionistas e colaboradores administrativos não visualizam os registros clínicos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Acesso Online Seguro</h3>
                <p>Acesse o prontuário de qualquer dispositivo com internet. Sem instalação local, sem risco de perda de dados por falha de hardware.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Prontuário digital vs papel</div>
        <h2>Por que migrar para o prontuário eletrônico?</h2>
        <p class="section-sub" style="margin-bottom:40px;">Compare o prontuário em papel com o sistema digital.</p>
        <div class="vs-table">
            <div class="vs-header">
                <div>Critério</div>
                <div class="col-utec">UTecnologia Saúde</div>
                <div class="col-papel">Prontuário em papel</div>
            </div>
            <div class="vs-row"><div>Acessível de qualquer lugar</div><div class="check">✓ Sim</div><div class="col-papel cross">✗ Não</div></div>
            <div class="vs-row"><div>Busca rápida por paciente</div><div class="check">✓ Imediata</div><div class="col-papel cross">✗ Manual</div></div>
            <div class="vs-row"><div>Risco de perda ou deterioração</div><div class="check">✓ Zero</div><div class="col-papel cross">✗ Alto</div></div>
            <div class="vs-row"><div>Legibilidade garantida</div><div class="check">✓ Sempre</div><div class="col-papel cross">✗ Caligrafia</div></div>
            <div class="vs-row"><div>Histórico cronológico automático</div><div class="check">✓ Sim</div><div class="col-papel cross">✗ Manual</div></div>
            <div class="vs-row"><div>Exames vinculados ao registro</div><div class="check">✓ Integrado</div><div class="col-papel cross">✗ Separado</div></div>
        </div>
    </div>
</section>

<section class="section" style="background:#f0f9ff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre prontuário eletrônico</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O prontuário eletrônico tem validade legal no Brasil?</div>
                <div class="faq-a">Sim. O prontuário eletrônico tem validade legal no Brasil. A Resolução CFM 1.821/2007 regulamenta o uso de prontuário eletrônico do paciente para médicos. Para demais profissionais, cada conselho profissional pode ter regulamentações específicas, recomendamos consultar o respectivo CFP, CFF, COFFITO, etc.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso migrar meu histórico de prontuários em papel para o sistema?</div>
                <div class="faq-a">Você pode cadastrar os pacientes e criar o prontuário digital a partir da próxima consulta, inserindo um resumo do histórico anterior. Também é possível anexar documentos escaneados do prontuário antigo diretamente no perfil do paciente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Os dados ficam seguros caso eu cancele minha conta?</div>
                <div class="faq-a">Recomendamos exportar ou fazer backup dos registros importantes antes de encerrar a conta. O sistema permite acessar todos os registros durante o período ativo da assinatura.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso ter o prontuário de pacientes de vários profissionais na mesma clínica?</div>
                <div class="faq-a">Sim. O sistema é multi-profissional. Cada médico, psicólogo, dentista ou terapeuta gerencia os prontuários dos seus próprios pacientes, com controle hierárquico de acesso.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Modernize o prontuário da sua clínica</h2>
            <p>30 dias grátis para experimentar o prontuário eletrônico completo.<br>Sem cartão de crédito. Sem instalação.</p>
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
                <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
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
  "name": "Sistema de Prontuário Eletrônico — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-prontuario-eletronico",
  "description": "Sistema de prontuário eletrônico completo para clínicas e consultórios.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Prontuário Eletrônico", "item": "https://utecnologia.com.br/sistema-prontuario-eletronico"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O prontuário eletrônico tem validade legal no Brasil?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. A Resolução CFM 1.821/2007 regulamenta o uso de prontuário eletrônico para médicos no Brasil. Demais profissionais devem consultar o respectivo conselho de classe."}},
    {"@type": "Question", "name": "Posso migrar meu histórico de prontuários em papel?", "acceptedAnswer": {"@type": "Answer", "text": "Você pode cadastrar pacientes e criar o prontuário digital a partir da próxima consulta, inserindo um resumo do histórico anterior, e também anexar documentos escaneados."}},
    {"@type": "Question", "name": "Posso ter prontuários de vários profissionais na mesma clínica?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema é multi-profissional, com controle hierárquico de acesso. Cada profissional gerencia os prontuários dos seus próprios pacientes."}}
  ]
}
</script>
</body>
</html>
