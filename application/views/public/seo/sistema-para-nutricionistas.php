<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-676174906"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-676174906');
</script>

    <meta charset="UTF-8">
    <title>Sistema para Nutricionistas | Prontuário Nutricional Online | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para nutricionistas com prontuário nutricional online, anamnese alimentar, agenda de consultas e gestão de pacientes. 100% online. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-nutricionistas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-nutricionistas">
    <meta property="og:title" content="Sistema para Nutricionistas | Prontuário Nutricional Online — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário nutricional online com anamnese alimentar, avaliação antropométrica e plano alimentar. Sistema para nutricionistas. Teste 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Nutricionistas | Prontuário Nutricional — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário nutricional com anamnese alimentar, IMC e plano alimentar. Sistema para nutricionistas. 30 dias grátis.">
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
    .hero-text{font-size:17px;color:var(--muted);line-height:1.75;margin-bottom:18px;}
    .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;margin-top:28px;}
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
    .fm-grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px;}
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
    .prontuario-stage::before{content:'Prontuário — Nutrição';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    /* prontuario-fields preserved */
    .prontuario-fields{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:32px;max-width:820px;margin:0 auto;}
    .fields-title{font-size:16px;font-weight:700;margin-bottom:20px;font-family:var(--ff-display);color:var(--ink);}
    .fields-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
    .field-item{display:flex;align-items:flex-start;gap:10px;font-size:14px;color:var(--muted);}
    .field-item::before{content:"🥗";flex-shrink:0;}
    /* use-cases preserved */
    .use-cases{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;}
    .use-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;}
    .use-card.highlight{border-color:var(--teal);background:var(--teal-lt);}
    .use-card h3{font-family:var(--ff-display);font-size:20px;font-weight:700;margin-bottom:10px;color:var(--ink);}
    .use-card p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:14px;}
    .use-chip{display:inline-block;background:var(--teal-lt);color:var(--teal);border:1px solid var(--teal-md);font-size:13px;font-weight:700;padding:6px 14px;border-radius:999px;}
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
    @media(max-width:900px){.hero-inner,.use-cases{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.fields-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2,.fm-grid3{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário eletrônico</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema para Nutricionistas</div>
                <h1><em>Prontuário nutricional</em> online e sistema de gestão para nutricionistas</h1>
                <p class="hero-text">
                    O UTecnologia Saúde reúne prontuário nutricional, anamnese alimentar, avaliação antropométrica
                    e agenda de consultas em uma plataforma 100% online — acessível de qualquer dispositivo,
                    sem instalação.
                </p>
                <p class="hero-text">
                    Para nutricionistas autônomos e clínicas de nutrição que querem organizar o atendimento,
                    registrar evoluções e manter o histórico completo de cada paciente em um único lugar.
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
                    <span class="funciona-label">O sistema inclui:</span>
                    <span class="funciona-chip">Prontuário nutricional completo</span>
                    <span class="funciona-chip">Anamnese alimentar</span>
                    <span class="funciona-chip">Avaliação antropométrica e IMC</span>
                    <span class="funciona-chip">Agenda de consultas</span>
                    <span class="funciona-chip">Laudos e exames</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Consulta Nutricional — Registro</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Queixa / Anamnese Alimentar</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Hábitos alimentares, restrições, histórico..." readonly></textarea>
                    </div>
                    <div class="fm-grid3">
                        <div>
                            <label class="fm-label">Peso (kg)</label>
                            <input class="fm-input" type="text" value="72.5" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Altura (cm)</label>
                            <input class="fm-input" type="text" value="168" readonly>
                        </div>
                        <div>
                            <label class="fm-label">IMC</label>
                            <input class="fm-input" type="text" value="25.7" readonly>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Objetivo</label>
                        <select class="fm-select" disabled><option>Emagrecimento</option></select>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Conduta / Plano Alimentar</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Avaliação nutricional e orientações..." readonly></textarea>
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
            <h2>Como o prontuário nutricional aparece na plataforma</h2>
            <p>Anamnese alimentar, avaliação antropométrica, IMC, objetivo e conduta nutricional em um histórico organizado por paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário — Nutrição · Fernanda Lima · 3ª Consulta</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa / Anamnese Alimentar</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Busca emagrecimento. Rotina de trabalho intensa, refeições rápidas. Preferência por carboidratos. Sem restrições alimentares. Refere sensação de fome noturna frequente.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Medidas Antropométricas</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Peso</div>
                                <div class="pmock-field-input">72,5 kg</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Altura</div>
                                <div class="pmock-field-input">168 cm</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">IMC</div>
                                <div class="pmock-field-input">25,7</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Objetivo</div>
                        <div class="pmock-row">
                            <div class="pmock-field-select">Emagrecimento</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação Nutricional / Condutas</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Déficit calórico moderado de 300 kcal/dia. Fracionamento em 5 refeições. Inclusão de proteína nas refeições intermediárias para controle da saciedade noturna. Meta: 1 kg/mês.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Evolução / Plano Alimentar</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Paciente aderiu bem ao plano da consulta anterior. Perdeu 0,8 kg. Manter plano atual com ajuste no jantar. Retorno em 30 dias.</div>
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

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Prontuário Nutricional</div>
        <h2>O que registrar no prontuário nutricional do paciente</h2>
        <p class="section-sub">O CFN exige prontuário atualizado por todos os nutricionistas. O UTecnologia Saúde oferece campos adaptados para o atendimento nutricional em consultório ou clínica.</p>
        <div class="prontuario-fields">
            <div class="fields-title">Campos disponíveis no prontuário nutricional:</div>
            <div class="fields-grid">
                <div class="field-item">Queixa principal e motivo da consulta</div>
                <div class="field-item">Anamnese alimentar (hábitos, preferências, restrições)</div>
                <div class="field-item">Peso, altura, IMC e circunferências</div>
                <div class="field-item">Histórico clínico e comorbidades</div>
                <div class="field-item">Avaliação nutricional e diagnóstico</div>
                <div class="field-item">Conduta: plano alimentar e orientações</div>
                <div class="field-item">Exames laboratoriais e laudos anexados</div>
                <div class="field-item">Evolução por consulta e retorno</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Para quem serve</div>
        <h2>Um sistema de nutrição pensado para a rotina real do consultório</h2>
        <p class="section-sub">Do nutricionista autônomo à clínica multiprofissional, o sistema organiza a operação sem complicar o atendimento.</p>
        <div class="use-cases">
            <div class="use-card highlight">
                <h3>Nutricionista autônomo</h3>
                <p>Ideal para quem atende em consultório próprio ou por hora em clínica. Prontuário nutricional, agenda e histórico de pacientes em um único sistema simples e acessível.</p>
                <span class="use-chip">Plano Solo a partir de R$ 79/mês</span>
            </div>
            <div class="use-card">
                <h3>Clínica de nutrição ou multiprofissional</h3>
                <p>Para clínicas com vários nutricionistas ou com equipe (médicos, psicólogos, fisioterapeutas), cada profissional gerencia seus pacientes com controle de acesso por perfil.</p>
                <span class="use-chip">Plano Clínica e Plano Pro</span>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que o nutricionista precisa no dia a dia</h2>
        <p class="section-sub">Uma plataforma que acompanha o fluxo real do atendimento, da primeira consulta ao acompanhamento de longo prazo.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário nutricional online</h3>
                <p>Registre anamnese alimentar, avaliação nutricional, conduta e evolução do paciente em um histórico cronológico único e acessível.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de consultas e retornos</h3>
                <p>Organize consultas iniciais, retornos e encaixes. Visualize a agenda por dia ou semana e controle os horários disponíveis com facilidade.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Exames laboratoriais e anexos</h3>
                <p>Solicite exames, registre resultados e armazene laudos de hemograma, bioquímica e outros exames diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3>Ficha completa do paciente</h3>
                <p>Dados cadastrais, histórico clínico, antecedentes familiares e informações de contato organizados em um perfil completo e de fácil acesso.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Clínica multiprofissional</h3>
                <p>Para clínicas com médicos, nutricionistas e outros profissionais: cada um com seu acesso, seus pacientes e seus prontuários, na mesma plataforma.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% online, sem instalação</h3>
                <p>Acesse do consultório, de casa ou do celular. Sem servidor local, sem backup manual, sem dependência de sistema operacional.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema e prontuário nutricional</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema é adequado para nutricionistas autônomos?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Solo foi criado para profissionais autônomos: 1 nutricionista, até 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia agenda e prontuários sem pagar por recursos de clínica grande.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O prontuário nutricional tem campos específicos para nutrição?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O prontuário inclui campos para anamnese alimentar, avaliação antropométrica (peso, altura, IMC, circunferências), histórico clínico, conduta nutricional e evolução por consulta. Laudos de exames laboratoriais também podem ser anexados diretamente ao prontuário.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso usar para clínica com vários nutricionistas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 nutricionistas + 10 colaboradores. O plano Pro suporta até 20 profissionais. Cada nutricionista gerencia seus próprios pacientes, com isolamento de dados entre profissionais da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema funciona para clínica multiprofissional com médico e nutricionista?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. A plataforma suporta equipes com diferentes especialidades no mesmo ambiente. Médicos, nutricionistas e outros profissionais podem trabalhar com seus próprios prontuários, agendas e pacientes dentro da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema atende a exigência de prontuário do CFN?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O UTecnologia Saúde oferece um prontuário nutricional online estruturado para registrar o histórico completo do paciente — anamnese, avaliação, conduta e evolução — atendendo as boas práticas de documentação exigidas pelo Conselho Federal de Nutricionistas. Para necessidades específicas de conformidade, recomendamos verificar com o seu conselho regional.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Funciona no celular durante a consulta?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O sistema é 100% online e responsivo. Você pode acessar prontuários, registrar evoluções e consultar o histórico do paciente pelo celular, tablet ou computador, em qualquer lugar com internet.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Organize seus atendimentos nutricionais</h2>
            <p class="cta-sub">Prontuário nutricional online, agenda e gestão de pacientes por 30 dias grátis.<br>Sem cartão de crédito. Sem instalação. Começa em minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
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
  "url": "https://utecnologia.com.br/sistema-para-nutricionistas",
  "description": "Sistema para nutricionistas com prontuário nutricional online, anamnese alimentar, avaliação antropométrica e agenda de consultas.",
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
    {"@type": "ListItem", "position": 3, "name": "Sistema para Nutricionistas", "item": "https://utecnologia.com.br/sistema-para-nutricionistas"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema é adequado para nutricionistas autônomos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo é ideal para nutricionistas autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês."}},
    {"@type": "Question", "name": "O prontuário nutricional tem campos específicos para nutrição?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O prontuário inclui campos para anamnese alimentar, avaliação antropométrica (peso, altura, IMC, circunferências), histórico clínico, conduta nutricional e evolução por consulta. Laudos de exames laboratoriais também podem ser anexados."}},
    {"@type": "Question", "name": "Posso usar para clínica com vários nutricionistas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Clínica suporta até 5 profissionais + 10 colaboradores. O plano Pro suporta até 20 profissionais. Cada nutricionista gerencia seus próprios pacientes com isolamento de dados."}},
    {"@type": "Question", "name": "O sistema atende a exigência de prontuário do CFN?", "acceptedAnswer": {"@type": "Answer", "text": "O UTecnologia Saúde oferece prontuário nutricional estruturado para registrar anamnese, avaliação, conduta e evolução — atendendo as boas práticas de documentação exigidas pelo CFN. Para necessidades específicas, recomendamos verificar com o conselho regional."}},
    {"@type": "Question", "name": "Funciona no celular durante a consulta?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema é 100% online e responsivo. Você acessa prontuários e registra evoluções pelo celular, tablet ou computador, de qualquer lugar com internet."}}
  ]
}
</script>
</body>
</html>
