<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Clínica de Fisioterapia — Prontuário e Agenda de Sessões | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínica de fisioterapia com prontuário de evolução, agenda de sessões e controle de pacientes. Para fisioterapeutas autônomos e clínicas de fisio. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
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
    .brand{font-family:var(--ff-display);font-size:18px;font-weight:700;color:var(--navy);}
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
    .trust-line{font-size:12px;color:var(--subtle);display:flex;gap:16px;flex-wrap:wrap;}
    .trust-line span::before{content:'✓ ';color:var(--green);font-weight:700;}
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
    .fm-textarea{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);resize:none;font-family:var(--ff-body);}
    .fm-btn{display:block;width:100%;background:var(--teal);color:var(--white);border:none;padding:11px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--ff-body);text-align:center;margin-top:4px;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário — Fisioterapia';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .pmock-row.col2>*,.pmock-row.col3>*{flex:1;}
    .pmock-field-input{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:8px 10px;font-size:12px;color:rgba(255,255,255,.75);width:100%;}
    .pmock-field-select{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:8px 10px;font-size:12px;color:rgba(255,255,255,.75);width:100%;}
    .pmock-act{display:flex;gap:10px;justify-content:flex-end;padding-top:4px;}
    .pmock-act button{padding:8px 18px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:default;font-family:var(--ff-body);}
    .pmock-act .btn-save{background:rgba(0,127,163,.3);color:var(--teal-md);}
    .pmock-act .btn-finish{background:var(--teal);color:var(--white);}
    .section{padding:72px 0;}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);text-align:center;margin-bottom:12px;}
    h2{font-family:var(--ff-display);font-size:36px;font-weight:700;text-align:center;color:var(--ink);margin-bottom:14px;}
    .section-sub{font-size:17px;color:var(--muted);text-align:center;max-width:560px;margin:0 auto 48px;line-height:1.65;}
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .feature-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;transition:transform .2s,box-shadow .2s;}
    .feature-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);}
    .feature-icon{width:44px;height:44px;background:var(--teal-lt);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;}
    .feature-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .feature-card p{font-size:14px;color:var(--muted);line-height:1.65;}
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
    .funciona-strip{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;}
    .funciona-chip{font-size:12px;font-weight:600;color:var(--teal);background:var(--teal-lt);border:1px solid var(--teal-md);padding:5px 12px;border-radius:999px;}
    .funciona-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--subtle);align-self:center;}
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
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
                <div class="trust-line">
                    <span>Sem cartão de crédito</span>
                    <span>100% online</span>
                    <span>A partir de R$ 79/mês</span>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Funciona para:</span>
                    <span class="funciona-chip">Fisioterapeutas autônomos</span>
                    <span class="funciona-chip">Clínicas de fisio</span>
                    <span class="funciona-chip">RPG e Pilates</span>
                    <span class="funciona-chip">Ortopedia</span>
                    <span class="funciona-chip">Sessões recorrentes</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Registro de Sessão — Fisioterapia</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Queixa / Avaliação Postural</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Queixa principal e achados posturais..." readonly></textarea>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">EVA — Dor (0–10)</label>
                            <input class="fm-input" type="number" value="6" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Fase do Tratamento</label>
                            <select class="fm-select" disabled><option>Subaguda</option></select>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Região Corporal Tratada</label>
                        <select class="fm-select" disabled><option>Coluna Lombar</option></select>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Evolução da Sessão / Técnicas Aplicadas</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Técnicas aplicadas e resposta do paciente..." readonly></textarea>
                    </div>
                    <button class="fm-btn" disabled>Salvar sessão →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Visualização do Sistema</div>
            <h2>Como o prontuário de fisioterapia aparece na plataforma</h2>
            <p>Cada sessão registrada com EVA, região corporal, técnica e evolução — tudo organizado no histórico do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário — Fisioterapia · João Silva · Sessão nº 8</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa / Avaliação Postural</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Lombalgia crônica com irradiação para MIE. Postura hiperlordótica. Encurtamento de isquiotibiais bilaterais.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação da Sessão</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">EVA — Dor</div>
                                <div class="pmock-field-input">6 / 10</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Região Corporal</div>
                                <div class="pmock-field-select">Coluna Lombar</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Fase</div>
                                <div class="pmock-field-select">Subaguda</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Evolução da Sessão / Técnicas Aplicadas</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Mobilização articular L4-L5. TENS lombar 20 min. Alongamento de isquiotibiais e paravertebrais. Paciente relata melhora parcial da dor após a sessão (EVA 4).</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta / Próxima Sessão</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Incluir exercícios de estabilização lombar. Retorno em 3 dias.</div>
                        </div>
                    </div>
                    <div class="pmock-act">
                        <button class="btn-save">Salvar rascunho</button>
                        <button class="btn-finish">Finalizar sessão</button>
                    </div>
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
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema é adequado para fisioterapeuta autônomo?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Solo é ideal para fisioterapeutas autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia sua agenda e as evoluções de sessão de forma simples.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso registrar as sessões de tratamento ao longo do tempo?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Cada sessão registra uma evolução no prontuário do paciente. A timeline cronológica mostra a progressão completa do tratamento, sessão por sessão, desde a avaliação inicial até a alta.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Funciona para clínicas que atendem convênios?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O sistema gerencia o lado clínico (prontuário, agenda, evoluções). Para faturamento e gestão de convênios, recomendamos verificar a compatibilidade com os processos específicos do seu convênio.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Organize sua clínica de fisioterapia</h2>
            <p class="cta-sub">30 dias grátis para testar prontuário, agenda de sessões e gestão de pacientes.<br>Sem cartão de crédito. Comece em minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
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
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia",
  "description": "Sistema para clínica de fisioterapia com prontuário de evolução, agenda de sessões e controle de pacientes.",
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
    {"@type": "ListItem", "position": 3, "name": "Fisioterapia", "item": "https://utecnologia.com.br/sistema-para-clinica-de-fisioterapia"}
  ]
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
