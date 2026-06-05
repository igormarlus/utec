<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuário Psicológico Online | Sistema para Psicólogos e Clínicas de Psicologia | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prontuário psicológico online para registrar sessões, evolução terapêutica e histórico do paciente. Sistema para psicólogos e clínicas de psicologia. Sigilo garantido. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-psicologos">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-psicologos">
    <meta property="og:title" content="Prontuário Psicológico Online | Sistema para Psicólogos — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário psicológico para registrar sessões, evolução terapêutica e histórico do paciente. Agenda e gestão para psicólogos. 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Prontuário Psicológico Online | Sistema para Psicólogos — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário psicológico para sessões, evolução terapêutica e histórico do paciente. Sistema para psicólogos. 30 dias grátis.">
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
    .prontuario-stage::before{content:'Prontuário — Psicologia';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .pmock-row.col2>*{flex:1;}
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
    .privacy-box{background:linear-gradient(135deg,var(--teal-lt),var(--teal-md));border:1px solid var(--teal);border-radius:20px;padding:40px;margin-top:48px;}
    .privacy-box h3{font-family:var(--ff-display);font-size:20px;font-weight:700;margin-bottom:12px;color:var(--navy);}
    .privacy-box p{font-size:15px;color:var(--muted);line-height:1.7;}
    .pront-info-grid{max-width:800px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .pront-info-card{background:var(--teal-lt);border:1px solid var(--teal-md);border-radius:12px;padding:24px;}
    .pront-info-card strong{display:block;margin-bottom:10px;color:var(--navy);font-size:15px;font-family:var(--ff-display);}
    .pront-info-card ul{list-style:none;display:flex;flex-direction:column;gap:8px;font-size:14px;color:var(--muted);}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid,.pront-info-grid{grid-template-columns:1fr 1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid,.pront-info-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}}
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
                <div class="eyebrow">Software para Psicologia</div>
                <h1><em>Prontuário psicológico</em> online e sistema de gestão para psicólogos e clínicas de psicologia</h1>
                <p class="hero-text">
                    Registre evoluções de sessão, gerencie sua agenda de atendimentos e mantenha o histórico completo
                    de cada paciente — de forma segura, sigilosa e acessível de qualquer lugar.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
                <div class="trust-line">
                    <span>Sem cartão de crédito</span>
                    <span>Dados isolados por clínica</span>
                    <span>A partir de R$ 79/mês</span>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Funciona para:</span>
                    <span class="funciona-chip">Psicólogos autônomos</span>
                    <span class="funciona-chip">Clínicas de psicologia</span>
                    <span class="funciona-chip">Atendimento online</span>
                    <span class="funciona-chip">Terapia individual e em grupo</span>
                    <span class="funciona-chip">Clínica multiprofissional</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Registro de Sessão — Psicologia</span>
                </div>
                <div class="card-body">
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">Nº da Sessão</label>
                            <input class="fm-input" type="number" value="12" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Modalidade</label>
                            <select class="fm-select" disabled><option>Individual</option></select>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Demanda Apresentada</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Demanda trazida pelo paciente nesta sessão..." readonly></textarea>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Evolução da Sessão</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Evolução e observações clínicas..." readonly></textarea>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">CID de Referência (opcional)</label>
                        <input class="fm-input" type="text" placeholder="Ex: F41.1, F32.0" readonly>
                    </div>
                    <button class="fm-btn" disabled>Registrar sessão →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Visualização do Sistema</div>
            <h2>Como o prontuário psicológico aparece na plataforma</h2>
            <p>Cada sessão registrada com número, modalidade, evolução e CID — histórico sigiloso e organizado por paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário — Psicologia · Ana Paula · Sessão nº 12</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Demanda Apresentada</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Paciente relata intensificação da ansiedade nos últimos dias associada a conflito familiar. Traz questões sobre relacionamento com mãe.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Sessão</div>
                        <div class="pmock-row col2">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Nº da Sessão</div>
                                <div class="pmock-field-input">12</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Modalidade</div>
                                <div class="pmock-field-select">Individual</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Evolução da Sessão</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Exploração do padrão relacional com a figura materna. Paciente demonstra maior consciência sobre gatilhos de ansiedade. Intervenção com técnicas cognitivo-comportamentais de reestruturação. Boa adesão ao processo.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">CID de Referência / Encaminhamentos</div>
                        <div class="pmock-row col2">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">CID</div>
                                <div class="pmock-field-input">F41.1</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Próxima Sessão</div>
                                <div class="pmock-field-input">15/06/2026</div>
                            </div>
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

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Prontuário Psicológico</div>
        <h2>O que um prontuário psicológico precisa registrar</h2>
        <p class="section-sub">O CFP determina que cada psicólogo mantenha prontuário atualizado de seus atendimentos. O UTecnologia Saúde oferece um prontuário psicológico online, seguro e acessível de qualquer dispositivo.</p>
        <div class="pront-info-grid">
            <div class="pront-info-card">
                <strong>Por sessão</strong>
                <ul>
                    <li>• Demanda apresentada pelo paciente</li>
                    <li>• Evolução e observações clínicas</li>
                    <li>• Técnicas e abordagens utilizadas</li>
                    <li>• Encaminhamentos e próximos passos</li>
                </ul>
            </div>
            <div class="pront-info-card">
                <strong>Por paciente</strong>
                <ul>
                    <li>• Histórico clínico e anamnese inicial</li>
                    <li>• Documentos, laudos e avaliações psicológicas</li>
                    <li>• Linha do tempo do processo terapêutico</li>
                    <li>• Dados de contato e responsáveis</li>
                </ul>
            </div>
        </div>
        <p style="text-align:center;margin-top:28px;font-size:14px;">
            <a href="<?=base_url()?>blog/prontuario-eletronico-para-psicologos" style="color:var(--teal);font-weight:600;">→ Leia também: Prontuário eletrônico para psicólogos — o que saber antes de escolher um sistema</a>
        </p>
    </div>
</section>

<section class="section" style="background:var(--teal-lt);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre prontuário psicológico e o sistema para psicólogos</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema é adequado para psicólogos autônomos?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Solo foi criado exatamente para profissionais autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia sua agenda e prontuários de forma simples, sem precisar de infraestrutura de clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Os registros das sessões ficam protegidos?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O acesso ao sistema é protegido por login e senha. Os dados dos pacientes são isolados por clínica — nenhum dado de uma clínica é visível em outra. Para profissionais que trabalham em mais de um local, cada ambiente é completamente separado.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso usar para clínica de psicologia com vários terapeutas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 psicólogos e o plano Pro até 20. Cada profissional tem sua própria agenda e acessa apenas os prontuários dos seus pacientes, mantendo o sigilo entre profissionais da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema atende psicólogos que trabalham online?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Por ser 100% online, o sistema é acessível de qualquer dispositivo — ideal para quem atende de forma remota ou em múltiplos locais. Você acessa prontuários, agenda e histórico de qualquer lugar com internet.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O que é um prontuário psicológico?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O prontuário psicológico é o documento clínico que registra o histórico de atendimentos de um paciente em psicologia — incluindo anamnese inicial, evolução de cada sessão, técnicas utilizadas, encaminhamentos e conclusões do processo terapêutico. O Conselho Federal de Psicologia (CFP) determina que todo psicólogo deve manter prontuário atualizado dos seus atendimentos. O UTecnologia Saúde oferece um sistema de prontuário psicológico online, protegido por login, com dados isolados por profissional e clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O prontuário psicológico fica protegido no sistema?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O acesso é protegido por login e senha individual. Os dados de cada clínica são armazenados em ambiente isolado (multi-tenant) — nenhuma informação de um psicólogo ou clínica é visível para outros usuários da plataforma. Cada profissional acessa apenas os prontuários dos seus próprios pacientes.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Experimente o sistema para psicólogos</h2>
            <p class="cta-sub">30 dias grátis para você organizar seu consultório ou clínica.<br>Sem cartão de crédito. Sem compromisso.</p>
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
                <a href="<?=base_url()?>sistema-para-clinica-de-fisioterapia">Fisioterapia</a>
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
  "url": "https://utecnologia.com.br/sistema-para-psicologos",
  "description": "Prontuário psicológico online para registrar sessões, evolução terapêutica e histórico do paciente. Sistema para psicólogos e clínicas de psicologia.",
  "offers": {
    "@type": "Offer",
    "price": "79",
    "priceCurrency": "BRL"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
    {"@type": "ListItem", "position": 3, "name": "Sistema para Psicólogos", "item": "https://utecnologia.com.br/sistema-para-psicologos"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O que é um prontuário psicológico?", "acceptedAnswer": {"@type": "Answer", "text": "O prontuário psicológico é o documento clínico que registra o histórico de atendimentos em psicologia — anamnese inicial, evolução de cada sessão, técnicas utilizadas, encaminhamentos e conclusões do processo terapêutico. O CFP determina que todo psicólogo deve manter prontuário atualizado dos seus atendimentos."}},
    {"@type": "Question", "name": "O sistema é adequado para psicólogos autônomos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo foi criado para profissionais autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia agenda e prontuário psicológico de forma simples, sem infraestrutura de clínica."}},
    {"@type": "Question", "name": "Os registros do prontuário psicológico ficam protegidos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O acesso é protegido por login e senha individual. Os dados são isolados por clínica em ambiente multi-tenant — nenhuma informação de um psicólogo é visível para outros usuários da plataforma."}},
    {"@type": "Question", "name": "Posso usar para clínica de psicologia com vários terapeutas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Clínica suporta até 5 psicólogos e o plano Pro até 20. Cada profissional tem sua própria agenda e acessa apenas os prontuários dos seus pacientes, mantendo o sigilo entre profissionais da mesma clínica."}},
    {"@type": "Question", "name": "O sistema atende psicólogos que trabalham online?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Por ser 100% online, o sistema é acessível de qualquer dispositivo — ideal para atendimentos remotos ou em múltiplos locais. Você acessa prontuários, agenda e histórico de qualquer lugar com internet."}}
  ]
}
</script>
</body>
</html>
