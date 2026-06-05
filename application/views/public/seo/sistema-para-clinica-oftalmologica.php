<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuário para Oftalmologista | Sistema para Clínica Oftalmológica | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia, pressão intraocular e laudos de exames. Sistema online para clínica oftalmológica. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinica-oftalmologica">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinica-oftalmologica">
    <meta property="og:title" content="Prontuário para Oftalmologista | Sistema para Clínica Oftalmológica — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário para oftalmologista: acuidade visual, refração, biomicroscopia, pressão intraocular e laudos. Sistema online. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Prontuário para Oftalmologista | UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia e laudos. Sistema online para clínica. Trial grátis 30 dias.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=Outfit:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=Outfit:wght@300;400;500;600;700&display=swap"></noscript>

    <style>
        :root {
            --navy:    #0a2540;
            --teal:    #007fa3;
            --teal-lt: #e0f4f8;
            --teal-md: #b3dfe9;
            --accent:  #00b4d8;
            --green:   #10b981;
            --ink:     #0a2540;
            --muted:   #4a6080;
            --subtle:  #8fa3b8;
            --border:  #dce7ef;
            --paper:   #f5f8fb;
            --white:   #ffffff;
            --radius:  14px;
            --shadow:  0 4px 32px rgba(10,37,64,.10);
            --shadow-lg: 0 12px 48px rgba(10,37,64,.16);
            --ff-display: 'Fraunces', Georgia, serif;
            --ff-body:    'Outfit', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--ff-body); color: var(--ink); background: var(--white); line-height: 1.65; -webkit-font-smoothing: antialiased; }
        a { color: var(--teal); text-decoration: none; }
        img { max-width: 100%; }

        /* ── TOPNAV ── */
        .topnav { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.92); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); padding: 13px 0; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 24px; }
        .topnav-inner { display: flex; justify-content: space-between; align-items: center; }
        .brand { display: flex; align-items: center; gap: 10px; font-family: var(--ff-display); font-size: 17px; font-weight: 700; color: var(--navy); text-decoration: none; }
        .brand-dot { width: 8px; height: 8px; background: var(--accent); border-radius: 50%; display: inline-block; }
        .nav-links { display: flex; gap: 28px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--muted); }
        .nav-links a:hover { color: var(--navy); }
        .btn-nav { background: var(--navy); color: #fff !important; padding: 9px 20px; border-radius: 999px; font-weight: 600; font-size: 13px; transition: background .2s; }
        .btn-nav:hover { background: var(--teal); }

        /* ── HERO ── */
        .hero { padding: 80px 0 72px; background: linear-gradient(155deg, #e8f4fa 0%, var(--white) 55%); overflow: hidden; position: relative; }
        .hero::before { content: ''; position: absolute; top: -120px; right: -80px; width: 560px; height: 560px; border-radius: 50%; border: 1px solid rgba(0,180,216,.12); pointer-events: none; }
        .hero::after  { content: ''; position: absolute; top: -60px; right: -30px; width: 360px; height: 360px; border-radius: 50%; border: 1px solid rgba(0,180,216,.10); pointer-events: none; }
        .hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; position: relative; z-index: 1; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; letter-spacing: .13em; text-transform: uppercase; color: var(--teal); margin-bottom: 18px; }
        .eyebrow-dot { width: 6px; height: 6px; background: var(--accent); border-radius: 50%; }
        h1 { font-family: var(--ff-display); font-size: 46px; font-weight: 800; line-height: 1.08; color: var(--navy); margin-bottom: 22px; }
        h1 em { font-style: italic; color: var(--teal); }
        .hero-text { font-size: 17px; color: var(--muted); line-height: 1.75; margin-bottom: 36px; }
        .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; background: var(--navy); color: #fff; padding: 15px 30px; border-radius: 999px; font-weight: 700; font-size: 15px; transition: all .2s; }
        .btn-primary:hover { background: var(--teal); color: #fff; transform: translateY(-1px); }
        .btn-ghost { display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--border); color: var(--muted); padding: 14px 24px; border-radius: 999px; font-weight: 600; font-size: 14px; transition: all .2s; }
        .btn-ghost:hover { border-color: var(--teal); color: var(--teal); }
        .trust-line { display: flex; align-items: center; gap: 16px; margin-top: 28px; }
        .trust-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--muted); font-weight: 500; }
        .trust-chip svg { width: 15px; height: 15px; color: var(--green); }

        /* ── MINI FORM CARD (hero right) ── */
        .hero-card { background: var(--white); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow-lg); padding: 24px; position: relative; }
        .hero-card-topbar { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
        .topbar-dots { display: flex; gap: 5px; }
        .topbar-dot { width: 10px; height: 10px; border-radius: 50%; }
        .topbar-dot.red { background: #ff6b6b; }
        .topbar-dot.yel { background: #ffd43b; }
        .topbar-dot.grn { background: #51cf66; }
        .topbar-label { font-size: 12px; font-weight: 600; color: var(--muted); letter-spacing: .08em; text-transform: uppercase; margin-left: 4px; }
        .topbar-badge { margin-left: auto; background: var(--teal-lt); color: var(--teal); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: .06em; }
        .fm-group { margin-bottom: 12px; }
        .fm-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
        .fm-input { width: 100%; background: var(--paper); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; font-size: 13px; color: var(--muted); font-family: var(--ff-body); }
        .fm-input.active { border-color: var(--accent); background: #fff; color: var(--navy); }
        .fm-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .fm-grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
        .fm-sep { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--subtle); padding: 8px 0 6px; border-bottom: 1px solid var(--border); margin-bottom: 10px; }
        .fm-select { width: 100%; background: var(--paper); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; font-size: 13px; color: var(--navy); font-family: var(--ff-body); cursor: default; display: flex; justify-content: space-between; align-items: center; }
        .fm-select-arrow { color: var(--subtle); font-size: 11px; }
        .fm-textarea { width: 100%; background: var(--paper); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; font-size: 12px; color: var(--muted); font-family: var(--ff-body); resize: none; min-height: 54px; }
        .fm-actions { display: flex; gap: 8px; margin-top: 14px; }
        .fm-btn { flex: 1; text-align: center; padding: 9px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; font-family: var(--ff-body); cursor: default; }
        .fm-btn.ghost { background: var(--paper); border: 1px solid var(--border); color: var(--muted); }
        .fm-btn.solid { background: var(--navy); color: #fff; }

        /* ── SECTION SHELL ── */
        .section { padding: 88px 0; }
        .section-alt { background: var(--paper); }
        .section-label { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--teal); margin-bottom: 14px; }
        h2 { font-family: var(--ff-display); font-size: 38px; font-weight: 700; line-height: 1.15; color: var(--navy); margin-bottom: 16px; }
        h2 em { font-style: italic; color: var(--teal); }
        .section-sub { font-size: 17px; color: var(--muted); line-height: 1.7; max-width: 600px; margin-bottom: 56px; }

        /* ── FULL PRONTUÁRIO MOCK ── */
        .prontuario-stage { position: relative; }
        .prontuario-stage::before { content: 'Prontuário — Oftalmologia'; position: absolute; top: -14px; left: 32px; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--teal); background: var(--paper); padding: 0 8px; }
        .prontuario-mock { background: var(--white); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }
        .pmock-topbar { background: var(--navy); padding: 14px 20px; display: flex; align-items: center; gap: 10px; }
        .pmock-dots { display: flex; gap: 5px; }
        .pmock-dot { width: 10px; height: 10px; border-radius: 50%; }
        .pmock-dot.r { background: rgba(255,255,255,.25); }
        .pmock-dot.y { background: rgba(255,255,255,.4); }
        .pmock-dot.g { background: rgba(255,255,255,.6); }
        .pmock-title { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.7); margin-left: 6px; }
        .pmock-badge { margin-left: auto; background: rgba(0,180,216,.3); color: var(--accent); font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 999px; }
        .pmock-body { padding: 28px; }
        .pmock-group { margin-bottom: 24px; }
        .pmock-group-label { font-size: 10.5px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--teal); padding-bottom: 8px; border-bottom: 1.5px solid var(--teal-md); margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }
        .pmock-group-label span { width: 4px; height: 14px; background: var(--accent); border-radius: 2px; flex-shrink: 0; }
        .pmock-row { display: grid; gap: 12px; margin-bottom: 12px; }
        .pmock-row.col2 { grid-template-columns: 1fr 1fr; }
        .pmock-row.col3 { grid-template-columns: 1fr 1fr 1fr; }
        .pmock-row.col1 { grid-template-columns: 1fr; }
        .pmock-field { }
        .pmock-field-label { font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 5px; letter-spacing: .03em; }
        .pmock-field-input { background: var(--paper); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; font-size: 12.5px; color: var(--subtle); font-family: var(--ff-body); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pmock-field-input.filled { color: var(--navy); background: #f0fafd; border-color: var(--teal-md); }
        .pmock-field-input.textarea { min-height: 60px; white-space: normal; color: var(--subtle); line-height: 1.5; font-size: 12px; }
        .pmock-field-select { background: var(--paper); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; font-size: 12.5px; color: var(--navy); font-family: var(--ff-body); display: flex; justify-content: space-between; align-items: center; }
        .pmock-field-select.placeholder { color: var(--subtle); }
        .pmock-act { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .pmock-act-btn { padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; font-family: var(--ff-body); cursor: default; }
        .pmock-act-btn.ghost { background: var(--paper); border: 1px solid var(--border); color: var(--muted); }
        .pmock-act-btn.primary { background: var(--navy); color: #fff; }
        .pmock-act-btn.success { background: var(--green); color: #fff; }

        /* ── RECEITA BLOCK ── */
        .receita-highlight { background: linear-gradient(135deg, #e0f4f8 0%, #f0fafd 100%); border: 1.5px solid var(--teal-md); border-radius: 16px; padding: 24px; margin-top: 8px; }
        .receita-highlight .pmock-group-label { color: #0369a1; border-bottom-color: var(--teal-md); }
        .receita-highlight .pmock-group-label span { background: #0369a1; }

        /* ── FEATURES ── */
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; transition: box-shadow .2s, transform .2s; }
        .feature-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
        .feature-icon { width: 44px; height: 44px; background: var(--teal-lt); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; }
        .feature-card h3 { font-family: var(--ff-display); font-size: 17px; font-weight: 600; margin-bottom: 10px; color: var(--navy); }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.7; }

        /* ── STEPS ── */
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; }
        .step { text-align: center; }
        .step-num { width: 52px; height: 52px; background: var(--navy); color: #fff; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-family: var(--ff-display); font-size: 22px; font-weight: 800; margin: 0 auto 18px; }
        .step h3 { font-family: var(--ff-display); font-size: 18px; font-weight: 600; margin-bottom: 10px; color: var(--navy); }
        .step p { font-size: 14px; color: var(--muted); line-height: 1.7; }
        .steps-connector { display: grid; grid-template-columns: 1fr 32px 1fr 32px 1fr; align-items: center; gap: 0; }
        .step-arrow { text-align: center; color: var(--teal-md); font-size: 24px; margin-top: -32px; }

        /* ── FAQ ── */
        .faq-list { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
        .faq-item { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .faq-q { font-size: 15px; font-weight: 600; color: var(--navy); padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; cursor: pointer; }
        .faq-q:hover { color: var(--teal); }
        .faq-chevron { font-size: 18px; color: var(--subtle); transition: transform .25s; flex-shrink: 0; }
        .faq-a { font-size: 14px; color: var(--muted); line-height: 1.8; padding: 0 24px 20px; display: none; }
        .faq-item.open .faq-a { display: block; }
        .faq-item.open .faq-chevron { transform: rotate(180deg); }

        /* ── CTA ── */
        .cta-wrap { background: var(--navy); border-radius: 24px; padding: 64px 48px; text-align: center; position: relative; overflow: hidden; }
        .cta-wrap::before { content: ''; position: absolute; top: -80px; left: 50%; transform: translateX(-50%); width: 480px; height: 480px; border-radius: 50%; background: radial-gradient(circle, rgba(0,180,216,.15) 0%, transparent 70%); pointer-events: none; }
        .cta-wrap h2 { color: #fff; font-size: 34px; margin-bottom: 14px; position: relative; }
        .cta-wrap p { color: rgba(255,255,255,.75); font-size: 16px; margin-bottom: 32px; position: relative; }
        .btn-white { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: var(--navy); padding: 15px 36px; border-radius: 999px; font-weight: 800; font-size: 15px; transition: all .2s; position: relative; }
        .btn-white:hover { background: var(--teal-lt); color: var(--teal); transform: translateY(-1px); }
        .cta-sub { margin-top: 18px; font-size: 13px; color: rgba(255,255,255,.5); position: relative; }

        /* ── FOOTER ── */
        footer { background: #06172b; padding: 32px 0; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-brand { font-family: var(--ff-display); font-size: 16px; font-weight: 700; color: #fff; }
        .footer-links { display: flex; gap: 24px; flex-wrap: wrap; }
        .footer-links a { color: rgba(255,255,255,.5); font-size: 13px; }
        .footer-links a:hover { color: rgba(255,255,255,.85); }

        /* ── RESPONSIVE ── */
        @media (max-width: 920px) {
            h1 { font-size: 34px; }
            h2 { font-size: 30px; }
            .hero-inner { grid-template-columns: 1fr; gap: 48px; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; gap: 32px; }
            .pmock-row.col3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .hero { padding: 56px 0 48px; }
            .section { padding: 64px 0; }
            h1 { font-size: 28px; }
            .features-grid { grid-template-columns: 1fr; }
            .pmock-row.col2, .pmock-row.col3 { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .cta-wrap { padding: 40px 24px; }
            .pmock-act { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="topnav">
    <div class="wrap">
        <div class="topnav-inner">
            <a class="brand" href="<?=base_url()?>">
                <img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:40px;width:auto;display:block">
            </a>
            <div class="nav-links">
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Ver tudo</a>
                <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow"><span class="eyebrow-dot"></span>Sistema para Oftalmologia</div>
                <h1>Prontuário oftalmológico que <em>reconhece sua rotina</em></h1>
                <p class="hero-text">
                    Campos específicos para AV, PIO, refração, biomicroscopia, fundo de olho
                    e prescrição de óculos — tudo integrado à agenda e ao histórico do paciente.
                    Sem texto livre onde cabe campo estruturado.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-ghost">Ver planos</a>
                </div>
                <div class="trust-line">
                    <div class="trust-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Sem cartão de crédito
                    </div>
                    <div class="trust-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        100% online
                    </div>
                    <div class="trust-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        A partir de R$ 79/mês
                    </div>
                </div>
            </div>

            <!-- Mini form preview -->
            <div class="hero-card">
                <div class="hero-card-topbar">
                    <div class="topbar-dots">
                        <div class="topbar-dot red"></div>
                        <div class="topbar-dot yel"></div>
                        <div class="topbar-dot grn"></div>
                    </div>
                    <span class="topbar-label">Atendimento</span>
                    <span class="topbar-badge">Oftalmologia</span>
                </div>

                <div class="fm-group">
                    <div class="fm-label">Tipo de Consulta</div>
                    <div class="fm-select"><span>Primeira Consulta</span><span class="fm-select-arrow">▾</span></div>
                </div>

                <div class="fm-sep">Acuidade Visual</div>
                <div class="fm-grid2">
                    <div class="fm-group">
                        <div class="fm-label">AV s/ Correção — OD</div>
                        <div class="fm-input active">20/200</div>
                    </div>
                    <div class="fm-group">
                        <div class="fm-label">AV s/ Correção — OE</div>
                        <div class="fm-input active">20/100</div>
                    </div>
                    <div class="fm-group">
                        <div class="fm-label">AV c/ Correção — OD</div>
                        <div class="fm-input">Ex: 20/20, 1.0</div>
                    </div>
                    <div class="fm-group">
                        <div class="fm-label">AV c/ Correção — OE</div>
                        <div class="fm-input">Ex: 20/20, 1.0</div>
                    </div>
                </div>

                <div class="fm-sep">Pressão Intraocular</div>
                <div class="fm-grid2">
                    <div class="fm-group">
                        <div class="fm-label">PIO — OD (mmHg)</div>
                        <div class="fm-input active">14</div>
                    </div>
                    <div class="fm-group">
                        <div class="fm-label">PIO — OE (mmHg)</div>
                        <div class="fm-input active">16</div>
                    </div>
                </div>

                <div class="fm-actions">
                    <div class="fm-btn ghost">Salvar sem encerrar</div>
                    <div class="fm-btn solid">Finalizar atendimento</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRONTUÁRIO MOCK COMPLETO -->
<section class="section section-alt">
    <div class="wrap">
        <div class="section-label"><span class="eyebrow-dot"></span>Prontuário Completo</div>
        <h2>Cada campo <em>no seu lugar certo</em></h2>
        <p class="section-sub">
            Veja exatamente o que aparece no sistema quando um oftalmologista abre um atendimento.
            Não é texto livre — são campos estruturados que refletem o exame clínico real.
        </p>

        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots">
                        <div class="pmock-dot r"></div>
                        <div class="pmock-dot y"></div>
                        <div class="pmock-dot g"></div>
                    </div>
                    <span class="pmock-title">Registro do atendimento em andamento &mdash; 05/06/2026 às 14:00h</span>
                    <span class="pmock-badge">OFTALMOLOGIA</span>
                </div>
                <div class="pmock-body">

                    <!-- Campos padrão adaptados -->
                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Queixa / Motivo da Consulta</div>
                        <div class="pmock-row col1">
                            <div class="pmock-field">
                                <div class="pmock-field-input textarea">Queixa principal, tempo de evolução, antecedentes oculares e sistêmicos relevantes.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de consulta -->
                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Tipo de Consulta</div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Tipo de Consulta</div>
                                <div class="pmock-field-select placeholder">— selecione — <span style="color:var(--subtle)">▾</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- AV -->
                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Acuidade Visual</div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">AV s/ Correção — OD</div>
                                <div class="pmock-field-input">Ex: 20/200, 0.5, CD 1m</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">AV s/ Correção — OE</div>
                                <div class="pmock-field-input">Ex: 20/200, 0.5, CD 1m</div>
                            </div>
                        </div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">AV c/ Correção — OD</div>
                                <div class="pmock-field-input">Ex: 20/20, 1.0</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">AV c/ Correção — OE</div>
                                <div class="pmock-field-input">Ex: 20/20, 1.0</div>
                            </div>
                        </div>
                    </div>

                    <!-- PIO + Refração -->
                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Pressão Intraocular</div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">PIO — OD (mmHg)</div>
                                <div class="pmock-field-input">Ex: 14</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">PIO — OE (mmHg)</div>
                                <div class="pmock-field-input">Ex: 16</div>
                            </div>
                        </div>
                    </div>

                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Refração</div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Refração OD</div>
                                <div class="pmock-field-input">Ex: Esf. -2,00 / Cil. -0,50 x 180°</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Refração OE</div>
                                <div class="pmock-field-input">Ex: Esf. -1,75 / Cil. -0,75 x 175°</div>
                            </div>
                        </div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Adição (ADD)</div>
                                <div class="pmock-field-input">Ex: +2,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Exame clínico -->
                    <div class="pmock-group">
                        <div class="pmock-group-label"><span></span>Exame Ocular / Achados</div>
                        <div class="pmock-row col1">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Biomicroscopia</div>
                                <div class="pmock-field-input textarea">Achados na lâmpada de fenda (córnea, cristalino, câmara anterior...)</div>
                            </div>
                        </div>
                        <div class="pmock-row col1">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Fundoscopia / Fundo de Olho</div>
                                <div class="pmock-field-input textarea">Disco óptico, mácula, vasos, periferia</div>
                            </div>
                        </div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">CID-10</div>
                                <div class="pmock-field-input">Ex: H52.1, H40.0, H26.9</div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescrição de óculos — highlight -->
                    <div class="receita-highlight">
                        <div class="pmock-group-label"><span></span>Prescrição de Óculos</div>
                        <div class="pmock-row col2" style="margin-bottom:12px">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Tipo de Lente Prescrita</div>
                                <div class="pmock-field-select placeholder">— selecione — <span style="color:var(--subtle)">▾</span></div>
                            </div>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:var(--muted);letter-spacing:.07em;text-transform:uppercase;margin-bottom:8px;">Olho Direito</div>
                        <div class="pmock-row col3" style="margin-bottom:12px">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Esférico</div>
                                <div class="pmock-field-input">Ex: -2,00</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Cilíndrico</div>
                                <div class="pmock-field-input">Ex: -0,50</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Eixo</div>
                                <div class="pmock-field-input">Ex: 180</div>
                            </div>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:var(--muted);letter-spacing:.07em;text-transform:uppercase;margin-bottom:8px;">Olho Esquerdo</div>
                        <div class="pmock-row col3" style="margin-bottom:12px">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Esférico</div>
                                <div class="pmock-field-input">Ex: -1,75</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Cilíndrico</div>
                                <div class="pmock-field-input">Ex: -0,75</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Eixo</div>
                                <div class="pmock-field-input">Ex: 175</div>
                            </div>
                        </div>
                        <div class="pmock-row col2">
                            <div class="pmock-field">
                                <div class="pmock-field-label">Adição (ADD)</div>
                                <div class="pmock-field-input">Ex: +2,00</div>
                            </div>
                            <div class="pmock-field">
                                <div class="pmock-field-label">Observações da Receita</div>
                                <div class="pmock-field-input">Ex: antirreflexo obrigatório, uso diurno</div>
                            </div>
                        </div>
                    </div>

                    <!-- Conduta -->
                    <div class="pmock-group" style="margin-top:24px">
                        <div class="pmock-group-label"><span></span>Conduta / Prescrição / Retorno</div>
                        <div class="pmock-row col1">
                            <div class="pmock-field">
                                <div class="pmock-field-input textarea">Conduta adotada, prescrição de óculos/lentes, medicação ocular, orientações e retorno.</div>
                            </div>
                        </div>
                    </div>

                    <div class="pmock-act">
                        <div class="pmock-act-btn ghost">Salvar sem encerrar</div>
                        <div class="pmock-act-btn primary">Marcar como em atendimento</div>
                        <div class="pmock-act-btn success">Finalizar atendimento</div>
                    </div>
                </div>
            </div>
        </div>

        <p style="text-align:center;font-size:13px;color:var(--subtle);margin-top:20px;">
            Interface real do sistema — campos estruturados específicos para oftalmologia, armazenados no prontuário do paciente.
        </p>
    </div>
</section>

<!-- FEATURES -->
<section class="section">
    <div class="wrap">
        <div class="section-label"><span class="eyebrow-dot"></span>Funcionalidades</div>
        <h2>Tudo que a clínica oftalmológica <em>precisa no dia a dia</em></h2>
        <p class="section-sub">Uma plataforma que acompanha o fluxo real do atendimento, da chegada do paciente ao retorno.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda por Oftalmologista</h3>
                <p>Gerencie a agenda de cada médico da clínica. Visualize por dia, semana ou profissional. Cancele e remarque consultas diretamente no sistema.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Laudos Anexados</h3>
                <p>Armazene OCT, retinografias, campos visuais, laudos de tomografia e outros arquivos diretamente no prontuário — vinculados ao atendimento correspondente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe Completa</h3>
                <p>Cadastre oftalmologistas, ortoptistas e colaboradores administrativos. Cada profissional acessa apenas o que é relevante ao seu papel na clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Histórico Clínico Completo</h3>
                <p>Todos os atendimentos ficam registrados em linha do tempo. O médico consulta a AV, PIO e refração das consultas anteriores antes de cada novo exame.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Dados Isolados por Clínica</h3>
                <p>Cada clínica opera em ambiente isolado. Os prontuários dos seus pacientes são exclusivos da sua clínica. Acesso sempre por login e perfil de usuário.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% Online, Qualquer Dispositivo</h3>
                <p>Acesse do consultório, de casa ou do celular. Sem instalação, sem backup manual, sem servidor local. Basta um navegador atualizado.</p>
            </div>
        </div>
    </div>
</section>

<!-- STEPS -->
<section class="section section-alt">
    <div class="wrap">
        <div class="section-label"><span class="eyebrow-dot"></span>Como começar</div>
        <h2 style="text-align:center;">Do cadastro ao primeiro atendimento <em>em 3 passos</em></h2>
        <p class="section-sub" style="margin-left:auto;margin-right:auto;text-align:center;">Sem burocracia. Sem instalação. Sem equipe técnica necessária.</p>
        <div class="steps-grid" style="max-width:760px;margin:0 auto;">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie a conta da clínica</h3>
                <p>Preencha o nome da clínica e e-mail. Acesso imediato, sem cartão de crédito e sem compromisso.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre médicos e pacientes</h3>
                <p>Adicione os oftalmologistas da equipe como especialidade Oftalmologia e comece a cadastrar pacientes e a agenda.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias grátis</h3>
                <p>Prontuários, campos específicos, agendamentos e exames disponíveis imediatamente. Depois, escolha o plano.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section">
    <div class="wrap">
        <div class="section-label"><span class="eyebrow-dot"></span>Perguntas frequentes</div>
        <h2 style="text-align:center;">Dúvidas sobre o sistema <em>para oftalmologia</em></h2>
        <p class="section-sub" style="margin:0 auto 48px;text-align:center;">O que os oftalmologistas perguntam antes de adotar o sistema.</p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema tem campos específicos para oftalmologia?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    Sim. O prontuário para oftalmologista inclui campos estruturados para acuidade visual sem e com correção (OD e OE), pressão intraocular, refração, biomicroscopia, fundoscopia, CID-10 e um bloco completo de prescrição de óculos com tipo de lente, esférico, cilíndrico e eixo para cada olho. Todos os campos aparecem automaticamente quando o prestador é cadastrado com especialidade Oftalmologia.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O bloco de prescrição de óculos gera receita em PDF?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    Os dados da prescrição são armazenados no prontuário do paciente (tipo de lente, esférico/cilíndrico/eixo OD e OE, adição e observações). A geração de receita em PDF é uma funcionalidade que está no roadmap do produto. Por enquanto, o sistema armazena e exibe o histórico completo de prescrições de cada consulta.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso armazenar OCT, retinografias e campos visuais no sistema?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    Sim. O sistema permite anexar arquivos de qualquer tipo — imagens, PDFs, laudos de OCT, campos visuais, retinografias, angiografias — diretamente ao prontuário do paciente, vinculados ao atendimento correspondente. Os arquivos ficam acessíveis em todas as consultas futuras.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    É possível gerenciar a agenda de vários oftalmologistas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    Sim. O sistema suporta múltiplos profissionais. Cada oftalmologista tem sua própria agenda e seus pacientes. O Plano Clínica comporta até 5 médicos + colaboradores por R$ 199/mês. O Plano Pro suporta até 20 profissionais simultaneamente.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O histórico de AV e PIO fica disponível entre consultas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    Sim. Todo o histórico clínico — incluindo os valores de acuidade visual e pressão intraocular de cada atendimento — fica registrado na linha do tempo do prontuário. O médico consulta a evolução completa do paciente antes de cada novo exame, de qualquer dispositivo.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Qual o custo para uma clínica com 2 ou 3 oftalmologistas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">
                    O Plano Clínica, por R$ 199/mês, suporta até 5 profissionais de saúde + 10 colaboradores. Para 1 oftalmologista com consultório próprio, o Plano Solo sai por R$ 79/mês. Você pode testar gratuitamente por 30 dias antes de escolher o plano — sem cartão de crédito.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Organize sua clínica oftalmológica agora</h2>
            <p>Crie sua conta e experimente todos os recursos por 30 dias completamente grátis.<br>Sem cartão de crédito. Sem contrato. Sem instalação.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
            <div class="cta-sub">Acesso imediato · Dados seguros · Cancele quando quiser</div>
        </div>
    </div>
</section>

<footer>
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>politica-de-privacidade">Privacidade</a>
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
  "url": "https://utecnologia.com.br/sistema-para-clinica-oftalmologica",
  "description": "Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia, pressão intraocular e laudos. Sistema online para clínica oftalmológica.",
  "offers": { "@type": "Offer", "price": "79", "priceCurrency": "BRL" }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
    {"@type": "ListItem", "position": 3, "name": "Sistema para Clínica Oftalmológica", "item": "https://utecnologia.com.br/sistema-para-clinica-oftalmologica"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "O sistema tem campos específicos para oftalmologia?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O prontuário para oftalmologista inclui campos para acuidade visual (OD e OE), pressão intraocular, refração, biomicroscopia, fundoscopia, CID-10 e um bloco completo de prescrição de óculos. Todos os campos aparecem automaticamente quando o prestador é cadastrado com especialidade Oftalmologia."}
    },
    {
      "@type": "Question",
      "name": "É possível gerenciar a agenda de vários oftalmologistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema suporta múltiplos profissionais. O Plano Clínica comporta até 5 médicos + colaboradores por R$ 199/mês. O Plano Pro suporta até 20 profissionais."}
    },
    {
      "@type": "Question",
      "name": "Posso armazenar tomografias e campos visuais no sistema?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema permite anexar arquivos de qualquer tipo — OCT, campos visuais, retinografias, angiografias — diretamente ao prontuário do paciente, vinculados ao atendimento correspondente."}
    },
    {
      "@type": "Question",
      "name": "O histórico de AV e PIO fica disponível entre consultas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. Todo o histórico clínico fica registrado na linha do tempo do prontuário. O médico consulta a evolução completa antes de cada novo exame, de qualquer dispositivo."}
    },
    {
      "@type": "Question",
      "name": "Qual o custo para uma clínica com 2 ou 3 oftalmologistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "O Plano Clínica, por R$ 199/mês, suporta até 5 profissionais + 10 colaboradores. Para 1 oftalmologista, o Plano Solo sai por R$ 79/mês. Trial grátis de 30 dias, sem cartão de crédito."}
    }
  ]
}
</script>

</body>
</html>
