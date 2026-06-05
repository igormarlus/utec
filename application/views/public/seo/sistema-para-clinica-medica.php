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
    .hero-cta{display:flex;gap:12px;flex-wrap:wrap;}
    .btn-primary{display:inline-block;background:var(--teal);color:var(--white);padding:14px 28px;border-radius:999px;font-weight:700;font-size:15px;}
    .btn-primary:hover{background:#006d8c;color:var(--white);}
    .btn-outline{display:inline-block;border:2px solid var(--border);color:var(--muted);padding:13px 24px;border-radius:999px;font-weight:600;font-size:14px;}
    .trust-line{font-size:12px;color:var(--subtle);display:flex;gap:16px;flex-wrap:wrap;margin-top:16px;}
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
    .fm-textarea{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);resize:none;font-family:var(--ff-body);}
    .fm-btn{display:block;width:100%;background:var(--teal);color:var(--white);border:none;padding:11px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--ff-body);text-align:center;margin-top:4px;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário — Clínica Médica';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    /* pront-grid preserved */
    .pront-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;}
    .pront-list{display:flex;flex-direction:column;gap:16px;margin-top:24px;}
    .pront-item{display:flex;gap:14px;align-items:flex-start;}
    .pront-dot{width:8px;height:8px;background:var(--teal);border-radius:50%;margin-top:6px;flex-shrink:0;}
    .pront-item h4{font-size:14px;font-weight:700;margin-bottom:4px;color:var(--ink);}
    .pront-item p{font-size:13px;color:var(--muted);}
    .plans-wrap{background:var(--teal-lt);border-radius:24px;padding:28px;}
    .plans-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--teal);margin-bottom:18px;}
    .plan-cards{display:flex;flex-direction:column;gap:14px;}
    .plan-card{background:var(--white);border-radius:12px;padding:16px;border:1.5px solid var(--border);}
    .plan-card.featured{border-color:var(--teal);box-shadow:0 0 0 3px rgba(0,127,163,.12);}
    .plan-name{font-size:13px;font-weight:700;color:var(--ink);margin-bottom:4px;}
    .plan-price{font-size:24px;font-weight:800;color:var(--teal);}
    .plan-price span{font-size:13px;font-weight:500;color:var(--subtle);}
    .plan-desc{font-size:13px;color:var(--muted);margin-top:4px;}
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
    @media(max-width:900px){.hero-inner,.pront-grid{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}h1{font-size:34px;}h2{font-size:28px;}}
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
                <div class="trust-line">
                    <span>Sem cartão de crédito</span>
                    <span>100% online</span>
                    <span>A partir de R$ 79/mês</span>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Ideal para:</span>
                    <span class="funciona-chip">Médicos clínicos gerais</span>
                    <span class="funciona-chip">Clínicas com múltiplos médicos</span>
                    <span class="funciona-chip">Especialistas autônomos</span>
                    <span class="funciona-chip">Consultórios com prontuário digital</span>
                    <span class="funciona-chip">Clínicas que solicitam exames</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Consulta Médica — Registro</span>
                </div>
                <div class="card-body">
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">PA Sistólica (mmHg)</label>
                            <input class="fm-input" type="text" value="130" readonly>
                        </div>
                        <div>
                            <label class="fm-label">PA Diastólica (mmHg)</label>
                            <input class="fm-input" type="text" value="85" readonly>
                        </div>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">FC (bpm)</label>
                            <input class="fm-input" type="text" value="78" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Peso (kg)</label>
                            <input class="fm-input" type="text" value="82" readonly>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Hipótese Diagnóstica / CID</label>
                        <input class="fm-input" type="text" placeholder="Ex: HAS — I10, Diabetes Tipo 2 — E11" readonly>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Evolução / Conduta</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Queixa, evolução clínica e conduta adotada..." readonly></textarea>
                    </div>
                    <button class="fm-btn" disabled>Salvar consulta →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Visualização do Sistema</div>
            <h2>Como o prontuário médico aparece na plataforma</h2>
            <p>PA, FC, peso, hipótese diagnóstica e conduta — cada consulta registrada na timeline do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário — Clínica Médica · Roberto Souza · 05/06/2026</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa Principal</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Cefaleia há 3 dias, tontura ao se levantar. Refere não ter tomado medicação anti-hipertensiva regularmente na última semana.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Sinais Vitais</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">PA (mmHg)</div>
                                <div class="pmock-field-input">160/100</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">FC (bpm)</div>
                                <div class="pmock-field-input">88</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Peso (kg)</div>
                                <div class="pmock-field-input">82</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Hipótese Diagnóstica / CID</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">HAS descompensada — I10. Crise hipertensiva — I11.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta / Prescrição</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Reforço de adesão ao tratamento. Ajuste de Losartana 50mg → 100mg/dia. Solicito ECG e perfil lipídico. Retorno em 30 dias ou antes se sintomas.</div>
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

<section class="section">
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

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="pront-grid">
            <div>
                <div class="section-label" style="text-align:left;">Prontuário eletrônico</div>
                <h2 style="text-align:left;">Tudo que o médico precisa registrar em cada consulta</h2>
                <p style="color:var(--muted);font-size:16px;margin-top:12px;">O prontuário do UTecnologia Saúde foi estruturado para o fluxo real de uma consulta médica, do registro inicial ao histórico de retornos.</p>
                <div class="pront-list">
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Anamnese estruturada</h4><p>Queixa principal, história da doença atual, antecedentes pessoais e familiares.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Exame físico e sinais vitais</h4><p>PA, FC, temperatura, peso, altura, SpO2 e achados relevantes.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Hipótese diagnóstica e CID</h4><p>Registro das hipóteses com codificação CID-10 para controle e relatórios.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Conduta e prescrição</h4><p>Documentação da conduta adotada, prescrições e orientações ao paciente.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Timeline de atendimentos</h4><p>Toda a história de consultas do paciente em ordem cronológica e acessível.</p></div></div>
                </div>
            </div>
            <div class="plans-wrap">
                <div class="plans-label">Planos disponíveis</div>
                <div class="plan-cards">
                    <div class="plan-card">
                        <div class="plan-name">Solo</div>
                        <div class="plan-price">R$ 79<span>/mês</span></div>
                        <div class="plan-desc">1 médico · 2 colaboradores</div>
                    </div>
                    <div class="plan-card featured">
                        <div class="plan-name">Clínica</div>
                        <div class="plan-price">R$ 199<span>/mês</span></div>
                        <div class="plan-desc">Até 5 médicos · 10 colaboradores</div>
                    </div>
                    <div class="plan-card">
                        <div class="plan-name">Pro</div>
                        <div class="plan-price">R$ 399<span>/mês</span></div>
                        <div class="plan-desc">Até 20 médicos · 50 colaboradores</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--teal-lt);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para clínica médica</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O prontuário segue os padrões do CFM?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O sistema oferece campos estruturados para anamnese, evolução clínica, hipóteses diagnósticas, CID e conduta — alinhados às boas práticas de documentação médica. Para necessidades específicas de conformidade regulatória, recomendamos validar com seu conselho profissional.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso ter uma clínica com vários médicos?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O sistema suporta múltiplos profissionais na mesma clínica. Os planos Clínica e Pro suportam de 5 a 20 médicos, cada um com sua própria agenda e acesso ao prontuário dos seus pacientes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema funciona para clínico geral e especialistas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O prontuário é flexível o suficiente para clínico geral, cardiologistas, dermatologistas, endocrinologistas, ginecologistas, neurologistas e outras especialidades médicas que necessitam de registro de consultas e histórico de pacientes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Como a recepcionista acessa o sistema?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Você cadastra a recepcionista como colaboradora (nível 4). Ela tem acesso à agenda e ao cadastro de pacientes, mas não ao prontuário clínico — que fica restrito ao médico responsável.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Experimente o sistema para clínica médica</h2>
            <p class="cta-sub">30 dias grátis para você e sua equipe. Sem cartão de crédito.<br>Do cadastro ao primeiro prontuário registrado em minutos.</p>
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
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário eletrônico</a>
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
  "url": "https://utecnologia.com.br/sistema-para-clinica-medica",
  "description": "Sistema para clínica médica com prontuário eletrônico completo, agenda e controle de exames.",
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
    {"@type": "ListItem", "position": 3, "name": "Sistema para Clínica Médica", "item": "https://utecnologia.com.br/sistema-para-clinica-medica"}
  ]
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
