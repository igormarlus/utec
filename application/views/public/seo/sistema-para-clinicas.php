<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Clínicas — Gestão Completa com Prontuário e Agenda | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínicas médicas com prontuário eletrônico, agenda por profissional e gestão de equipe. Multi-especialidade, 100% online. Teste grátis 30 dias — sem cartão de crédito.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinicas">
    <meta property="og:title" content="Sistema para Clínicas — UTecnologia Saúde">
    <meta property="og:description" content="Sistema para clínicas com prontuário eletrônico, agenda e gestão de equipe. Multi-especialidade. Trial 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Clínicas — UTecnologia Saúde">
    <meta name="twitter:description" content="Sistema para clínicas com prontuário eletrônico, agenda e gestão de equipe. Trial 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600;9..144,700&family=Outfit:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600;9..144,700&family=Outfit:wght@400;500;600;700;800&display=swap"></noscript>
    <style>
    :root{
      --navy:#0a2540;--teal:#007fa3;--teal-lt:#e0f4f8;--teal-md:#b3dfe9;
      --accent:#00b4d8;--green:#10b981;
      --ink:#0a2540;--muted:#4a6080;--subtle:#8fa3b8;
      --border:#dce7ef;--paper:#f5f8fb;--white:#ffffff;
      --radius:14px;--shadow:0 4px 32px rgba(10,37,64,.10);
      --shadow-lg:0 12px 48px rgba(10,37,64,.16);
      --ff-display:'Fraunces',Georgia,serif;--ff-body:'Outfit',sans-serif;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--ff-body);color:var(--ink);background:var(--paper);line-height:1.6;}
    a{color:var(--teal);text-decoration:none;}
    .wrap{max-width:1100px;margin:0 auto;padding:0 20px;}
    .topnav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:14px 0;}
    .topnav .wrap{display:flex;justify-content:space-between;align-items:center;}
    .nav-links{display:flex;gap:24px;align-items:center;}
    .nav-links a{font-size:14px;font-weight:500;color:var(--muted);}
    .nav-links a:hover{color:var(--teal);}
    .btn-nav{background:var(--teal);color:var(--white)!important;padding:8px 20px;border-radius:999px;font-weight:700!important;font-size:13px!important;}
    .hero{padding:80px 0 72px;background:linear-gradient(145deg,var(--teal-lt) 0%,var(--paper) 55%);}
    .hero-inner{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;}
    .eyebrow{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);margin-bottom:14px;}
    h1{font-family:var(--ff-display);font-size:44px;font-weight:700;line-height:1.1;color:var(--ink);margin-bottom:20px;}
    h1 em{font-style:italic;color:var(--teal);}
    .hero-text{font-size:17px;color:var(--muted);line-height:1.75;margin-bottom:28px;}
    .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
    .btn-primary{display:inline-block;background:var(--teal);color:var(--white);padding:14px 28px;border-radius:999px;font-weight:700;font-size:15px;}
    .btn-primary:hover{background:#006d8c;color:var(--white);}
    .btn-outline{display:inline-block;border:2px solid var(--border);color:var(--muted);padding:13px 24px;border-radius:999px;font-weight:600;font-size:14px;}
    .funciona-strip{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;}
    .funciona-chip{font-size:12px;font-weight:600;color:var(--teal);background:var(--teal-lt);border:1px solid var(--teal-md);padding:5px 12px;border-radius:999px;}
    .funciona-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--subtle);align-self:center;}
    .hero-card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow-lg);overflow:hidden;}
    .topbar-dots{background:var(--navy);padding:10px 16px;display:flex;gap:6px;align-items:center;}
    .topbar-dots span{width:10px;height:10px;border-radius:50%;background:rgba(255,255,255,.3);}
    .topbar-dots span:first-child{background:#ff5f57;}
    .topbar-dots span:nth-child(2){background:#ffbd44;}
    .topbar-dots span:nth-child(3){background:#28c940;}
    .card-title-bar{font-size:12px;font-weight:600;color:rgba(255,255,255,.7);margin-left:8px;}
    .card-body{padding:20px;}
    .fm-group{margin-bottom:14px;}
    .fm-label{font-size:11px;font-weight:700;color:var(--subtle);text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px;display:block;}
    .fm-input{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);font-family:var(--ff-body);}
    .fm-grid2{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
    .fm-select{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);font-family:var(--ff-body);}
    .fm-textarea{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);font-family:var(--ff-body);resize:none;}
    .fm-btn{display:block;width:100%;background:var(--teal);color:var(--white);border:none;padding:11px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--ff-body);text-align:center;margin-top:4px;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário Eletrônico Multi-Especialidade';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
    .prontuario-mock{background:#0e2d4a;border:1px solid rgba(0,127,163,.4);border-radius:16px;overflow:hidden;box-shadow:var(--shadow-lg);}
    .pmock-topbar{background:var(--navy);padding:10px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid rgba(255,255,255,.08);}
    .pmock-dots{display:flex;gap:5px;}
    .pmock-dots span{width:10px;height:10px;border-radius:50%;}
    .pmock-dots span:nth-child(1){background:#ff5f57;}
    .pmock-dots span:nth-child(2){background:#ffbd44;}
    .pmock-dots span:nth-child(3){background:#28c940;}
    .pmock-title{font-size:12px;font-weight:600;color:rgba(255,255,255,.7);}
    .pmock-body{padding:20px;display:flex;flex-direction:column;gap:14px;}
    .pmock-group{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:14px 16px;}
    .pmock-group-label{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);margin-bottom:8px;}
    .pmock-row{display:flex;gap:10px;}
    .pmock-row.col3>*{flex:1;}
    .pmock-field-input{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:8px 10px;font-size:12px;color:rgba(255,255,255,.75);width:100%;}
    .pmock-act{display:flex;gap:10px;justify-content:flex-end;padding-top:4px;}
    .pmock-act button{padding:8px 18px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:default;font-family:var(--ff-body);}
    .pmock-act .btn-save{background:rgba(0,127,163,.3);color:var(--teal-md);}
    .pmock-act .btn-finish{background:var(--teal);color:var(--white);}
    .section{padding:72px 0;}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);text-align:center;margin-bottom:12px;}
    h2{font-family:var(--ff-display);font-size:36px;font-weight:700;text-align:center;color:var(--ink);margin-bottom:14px;}
    .section-sub{font-size:17px;color:var(--muted);text-align:center;max-width:560px;margin:0 auto 48px;line-height:1.65;}
    /* specialties dark section — preserved */
    .specialties{background:linear-gradient(160deg, #0f172a 0%, #1e3a5f 100%);padding:80px 0;}
    .spec-header{text-align:center;margin-bottom:44px;}
    .spec-eyebrow{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .spec-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .spec-header p{font-size:16px;color:rgba(255,255,255,.6);max-width:540px;margin:0 auto;}
    .spec-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
    .spec-card{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:var(--radius);padding:20px;text-align:center;transition:background .2s;}
    .spec-card:hover{background:rgba(255,255,255,.12);}
    .spec-icon{font-size:28px;margin-bottom:10px;}
    .spec-name{font-family:var(--ff-display);font-size:14px;font-weight:600;color:var(--white);}
    /* features */
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .feature-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;transition:transform .2s;}
    .feature-card:hover{transform:translateY(-3px);}
    .feature-icon{width:44px;height:44px;background:var(--teal-lt);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;}
    .feature-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .feature-card p{font-size:14px;color:var(--muted);line-height:1.65;}
    /* steps */
    .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;}
    .step{text-align:center;}
    .step-num{width:48px;height:48px;background:var(--teal);color:var(--white);border-radius:999px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;margin:0 auto 16px;}
    .step h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .step p{font-size:14px;color:var(--muted);}
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:10px;}
    .faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .faq-q{font-size:15px;font-weight:600;color:var(--ink);padding:18px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;}
    .faq-q:hover{color:var(--teal);}
    .faq-chevron{color:var(--subtle);font-size:18px;transition:transform .25s;flex-shrink:0;}
    .faq-item.open .faq-chevron{transform:rotate(180deg);}
    .faq-a{font-size:14px;color:var(--muted);line-height:1.7;padding:0 24px;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s;}
    .faq-item.open .faq-a{max-height:400px;padding:0 24px 18px;}
    .cta-wrap{background:var(--navy);border-radius:24px;padding:64px 48px;text-align:center;position:relative;overflow:hidden;}
    .cta-wrap::before{content:'';position:absolute;top:-50%;left:-10%;width:60%;height:200%;background:radial-gradient(ellipse,rgba(0,180,216,.2) 0%,transparent 70%);pointer-events:none;}
    .cta-wrap h2{font-family:var(--ff-display);font-size:34px;font-weight:700;color:var(--white);margin-bottom:14px;position:relative;}
    .cta-sub{font-size:16px;color:rgba(255,255,255,.7);margin-bottom:32px;position:relative;}
    .btn-white{display:inline-block;background:var(--white);color:var(--navy);padding:15px 36px;border-radius:999px;font-weight:800;font-size:15px;position:relative;}
    .btn-white:hover{background:var(--teal-lt);color:var(--navy);}
    .footer{background:#06172b;padding:36px 0;}
    .footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
    .footer-brand{font-family:var(--ff-display);font-size:15px;font-weight:700;color:rgba(255,255,255,.9);}
    .footer-links a{color:rgba(255,255,255,.5);font-size:13px;margin-left:20px;}
    .footer-links a:hover{color:rgba(255,255,255,.85);}
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.steps-grid{grid-template-columns:1fr;}.spec-grid{grid-template-columns:repeat(2,1fr);}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}.spec-grid{grid-template-columns:1fr 1fr;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>">Início</a>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
            <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema para Clínicas</div>
                <h1>O <em>sistema para clínicas</em> que organiza sua rotina clínica do começo ao fim</h1>
                <p class="hero-text">
                    UTecnologia Saúde é a plataforma SaaS que integra prontuário eletrônico,
                    agenda por profissional, controle de exames e gestão de equipe em um único sistema
                    acessível pelo navegador — sem instalação, sem servidor local.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>sistema-gratuito-para-clinicas" class="btn-outline">Saber mais</a>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">O que está incluído:</span>
                    <span class="funciona-chip">Prontuário eletrônico</span>
                    <span class="funciona-chip">Agenda multi-profissional</span>
                    <span class="funciona-chip">Controle de exames</span>
                    <span class="funciona-chip">Gestão de equipe</span>
                    <span class="funciona-chip">Upload de arquivos</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Dashboard — Sua Clínica</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Paciente</label>
                        <input class="fm-input" type="text" placeholder="Buscar paciente..." readonly>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">Profissional</label>
                            <select class="fm-select" disabled><option>Dr. Marcos Ferreira</option></select>
                        </div>
                        <div>
                            <label class="fm-label">Especialidade</label>
                            <select class="fm-select" disabled><option>Clínica Médica</option></select>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Queixa Principal</label>
                        <textarea class="fm-textarea" rows="3" placeholder="Registre a queixa e contexto do atendimento..." readonly></textarea>
                    </div>
                    <button class="fm-btn" disabled>Abrir prontuário →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">O Sistema em ação</div>
            <h2>Prontuário eletrônico integrado ao fluxo clínico</h2>
            <p>Queixa, avaliação, hipóteses diagnósticas, conduta e solicitação de exames — tudo registrado por consulta na timeline do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário Eletrônico · Clínica Médica · 05/06/2026</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa Principal</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Dor lombar há 3 semanas. Piora ao sentar por longos períodos. Nega irradiação. Trabalha em escritório 8h/dia.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação / Hipóteses</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">PA</div>
                                <div class="pmock-field-input">126/80</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Peso</div>
                                <div class="pmock-field-input">78kg</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">CID</div>
                                <div class="pmock-field-input">M54.5</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Anti-inflamatório 7 dias. Encaminhar fisioterapia. Solicitar RX lombar em 2 incidências. Retorno em 4 semanas.</div>
                        </div>
                    </div>
                    <div class="pmock-act">
                        <button class="btn-save">Salvar rascunho</button>
                        <button class="btn-finish">Finalizar consulta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="specialties">
    <div class="wrap">
        <div class="spec-header">
            <div class="spec-eyebrow">Multi-especialidade</div>
            <h2 style="color:var(--white);">Um sistema para todas as especialidades da clínica</h2>
            <p>Médicos, psicólogos, fisioterapeutas, nutricionistas e mais — todos em um único ambiente.</p>
        </div>
        <div class="spec-grid">
            <div class="spec-card">
                <div class="spec-icon">🩺</div>
                <div class="spec-name">Clínica Médica</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">🧠</div>
                <div class="spec-name">Psicologia</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">🦷</div>
                <div class="spec-name">Odontologia</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">🏃</div>
                <div class="spec-name">Fisioterapia</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">🥗</div>
                <div class="spec-name">Nutrição</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">👶</div>
                <div class="spec-name">Pediatria</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">👁️</div>
                <div class="spec-name">Oftalmologia</div>
            </div>
            <div class="spec-card">
                <div class="spec-icon">💊</div>
                <div class="spec-name">Psiquiatria</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que sua clínica precisa em um sistema</h2>
        <p class="section-sub">Prontuário, agenda, exames e gestão — integrados para eliminar retrabalho e papelada.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico</h3>
                <p>Registre anamnese, avaliação clínica, hipóteses diagnósticas e conduta por consulta. Timeline completa do paciente com histórico de todos os atendimentos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda por Profissional</h3>
                <p>Agende consultas para cada profissional da clínica. Filtre por médico, data e status. Cancele e remarque diretamente. Visão geral da ocupação em tempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧪</div>
                <h3>Controle de Exames</h3>
                <p>Solicite e acompanhe exames dentro do prontuário. Checklist de exames pendentes por paciente. Resultados vinculados ao atendimento correspondente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Gestão de Equipe</h3>
                <p>Cadastre médicos, colaboradores e recepcionistas. Cada perfil acessa apenas o que é relevante — sem exposição desnecessária de dados de pacientes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Upload de Arquivos</h3>
                <p>Anexe laudos, exames de imagem e documentos ao prontuário. Organizados por atendimento e acessíveis de qualquer dispositivo com internet.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios e Gestão</h3>
                <p>Acompanhe produtividade por profissional e período. Dados para gestão clínica e tomada de decisão sobre a operação da clínica.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Comece agora</div>
        <h2>Três passos para organizar sua clínica</h2>
        <p class="section-sub">Da criação da conta ao primeiro atendimento registrado — tudo no mesmo dia.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie sua conta grátis</h3>
                <p>Preencha o nome da clínica e e-mail. Sem cartão de crédito. Acesso imediato ao sistema completo por 30 dias.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre equipe e pacientes</h3>
                <p>Adicione profissionais, colaboradores e pacientes. Cada um com o perfil correto de acesso às informações da clínica.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use e decida depois</h3>
                <p>Agenda, prontuário e exames funcionando desde o primeiro dia. Assine apenas se gostar, a partir de R$ 79/mês.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para clínicas</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema funciona para clínicas com vários profissionais de saúde?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O UTecnologia Saúde é multi-profissional por design. O Plano Clínica suporta até 5 profissionais de saúde e 10 colaboradores. O Plano Pro suporta até 20 profissionais. Cada um com sua própria agenda e acesso aos pacientes do seu vínculo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema funciona para diferentes especialidades médicas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O sistema suporta clínica médica, psicologia, odontologia, fisioterapia, nutrição, pediatria, oftalmologia e outras especialidades. O prontuário é adaptado ao fluxo de cada especialidade com campos e labels correspondentes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Precisa de instalação ou servidor próprio?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. O UTecnologia Saúde é 100% online (SaaS). Acesse pelo navegador de qualquer computador, tablet ou celular. Sem instalação, sem servidor local e sem manutenção de infraestrutura.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Como funciona a hierarquia de acesso dos usuários?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O sistema tem perfis de acesso por nível: Estabelecimento (gestor da clínica), Prestador (médico/profissional), Colaborador (recepcionista/auxiliar) e Paciente. Cada perfil acessa apenas os dados relevantes ao seu papel. Isso garante que colaboradores vejam a agenda mas não o prontuário completo, por exemplo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Qual o custo após o trial gratuito?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O Plano Solo custa R$ 79/mês (1 profissional + 2 colaboradores), o Plano Clínica R$ 199/mês (5 profissionais + 10 colaboradores) e o Plano Pro R$ 399/mês (20 profissionais + 50 colaboradores). Todos os planos têm pacientes ilimitados e podem ser cancelados a qualquer momento.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Os dados de pacientes ficam seguros na nuvem?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O UTecnologia Saúde usa arquitetura multi-tenant com dados completamente isolados por clínica. Cada clínica tem seu próprio ambiente — os dados dos seus pacientes não são acessíveis por outros usuários ou outras clínicas na plataforma.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Organize sua clínica com o UTecnologia Saúde</h2>
            <p class="cta-sub">30 dias para testar o sistema completo — prontuário, agenda, exames e equipe.<br>Sem cartão de crédito. Cancele quando quiser.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar grátis agora →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>">Início</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
                <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
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
  "url": "https://utecnologia.com.br/sistema-para-clinicas",
  "description": "Sistema para clínicas com prontuário eletrônico, agenda e gestão de equipe. Multi-especialidade, 100% online.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema funciona para clínicas com vários profissionais de saúde?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O Plano Clínica suporta até 5 profissionais e 10 colaboradores. O Plano Pro suporta até 20 profissionais."}},
    {"@type": "Question", "name": "O sistema funciona para diferentes especialidades médicas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Suporta clínica médica, psicologia, odontologia, fisioterapia, nutrição, pediatria, oftalmologia e outras especialidades."}},
    {"@type": "Question", "name": "Qual o custo após o trial gratuito?", "acceptedAnswer": {"@type": "Answer", "text": "O Plano Solo custa R$ 79/mês, o Plano Clínica R$ 199/mês e o Plano Pro R$ 399/mês. Todos com pacientes ilimitados e cancelamento a qualquer momento."}}
  ]
}
</script>
</body>
</html>
