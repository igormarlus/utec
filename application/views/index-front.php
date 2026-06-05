<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WSW6C4F4K8"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-WSW6C4F4K8');
    </script>
    <meta charset="UTF-8">
    <title>UTecnologia Saúde — Gestão clínica para clínicas e profissionais de saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prontuário eletrônico, agenda inteligente e gestão de pacientes. Experimente 30 dias grátis, sem cartão de crédito e sem compromisso.">
    <link rel="canonical" href="https://utecnologia.com.br/">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/">
    <meta property="og:title" content="UTecnologia Saúde — Gestão clínica para clínicas e profissionais de saúde">
    <meta property="og:description" content="Prontuário eletrônico, agenda inteligente e gestão de pacientes. Experimente 30 dias grátis, sem cartão de crédito e sem compromisso.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="UTecnologia Saúde — Gestão clínica para clínicas e profissionais de saúde">
    <meta name="twitter:description" content="Prontuário eletrônico, agenda inteligente e gestão de pacientes. Experimente 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>

    <?php if(!empty($fb_pixel_id)): ?>
    <!-- Meta Pixel — base code -->
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?=htmlspecialchars((string)$fb_pixel_id)?>');
    fbq('track', 'PageView', {}, {eventID: '<?=htmlspecialchars((string)$fb_pv_event_id)?>'});
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?=htmlspecialchars((string)$fb_pixel_id)?>&ev=PageView&noscript=1"/></noscript>
    <?php endif; ?>

    <style>
        /* ── TOKENS ── */
        :root {
            --brand-blue:   #0ea5e9;
            --brand-green:  #22c55e;
            --seg-clinica:  #2563eb;
            --seg-clinica-light: #eff6ff;
            --seg-clinica-border: #bfdbfe;
            --seg-prof:     #7c3aed;
            --seg-prof-light: #f5f3ff;
            --seg-prof-border: #ddd6fe;
            --text:         #0f172a;
            --muted:        #475569;
            --subtle:       #94a3b8;
            --surface:      #ffffff;
            --paper:        #f8fafc;
            --border:       #e2e8f0;
            --radius-sm:    10px;
            --radius-md:    16px;
            --radius-lg:    24px;
            --shadow-sm:    0 1px 4px rgba(15,23,42,.06);
            --shadow-md:    0 8px 24px rgba(15,23,42,.09);
            --shadow-lg:    0 20px 50px rgba(15,23,42,.12);
        }

        /* ── RESET ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: "Inter", system-ui, sans-serif; color: var(--text); background: #fff; line-height: 1.5; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }
        ul { list-style: none; }

        /* ── LAYOUT ── */
        .container { max-width: 1120px; margin: 0 auto; padding: 0 20px; }

        /* ── HEADER ── */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
        }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            height: 64px;
        }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-image {
            display: block;
            height: 44px;
            width: auto;
        }
        .logo-name { font-weight: 700; font-size: 16px; line-height: 1.15; }
        .logo-tagline { font-size: 11px; color: var(--subtle); }
        /* Nav com dropdown */
        .header-nav { display: flex; gap: 2px; font-size: 14px; align-items: center; }
        .nav-item { position: relative; }
        .nav-link {
            display: flex; align-items: center; gap: 4px;
            color: var(--muted); padding: 8px 12px; border-radius: 8px;
            transition: all .15s; white-space: nowrap; text-decoration: none;
            font-weight: 500;
        }
        .nav-link:hover { color: var(--text); background: var(--paper); }
        .nav-link .chevron { transition: transform .2s; flex-shrink: 0; }
        .nav-item:hover .chevron { transform: rotate(180deg); }

        /* Dropdown panel */
        .nav-dropdown {
            position: absolute; top: calc(100% + 6px); left: 0;
            background: #fff; border: 1px solid var(--border);
            border-radius: 14px; box-shadow: 0 12px 40px rgba(15,23,42,.13);
            min-width: 230px; padding: 8px;
            opacity: 0; visibility: hidden;
            transform: translateY(-6px);
            transition: opacity .18s, transform .18s, visibility .18s;
            z-index: 200;
        }
        .nav-item:hover .nav-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-dropdown a {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 9px 10px; border-radius: 8px;
            color: var(--text); font-size: 13px;
            transition: background .12s; text-decoration: none;
        }
        .nav-dropdown a:hover { background: var(--paper); }
        .nav-dropdown a:hover .dd-label { color: #0284c7; }
        .dd-icon { font-size: 15px; flex-shrink: 0; margin-top: 1px; line-height: 1; }
        .dd-text { display: flex; flex-direction: column; gap: 1px; }
        .dd-label { font-weight: 600; font-size: 13px; color: var(--text); }
        .dd-desc { font-size: 11px; color: var(--muted); line-height: 1.3; }
        .nav-divider { height: 1px; background: var(--border); margin: 4px 2px; }

        /* Dropdown com 2 colunas para especialidades */
        .nav-dropdown.two-col { min-width: 420px; display: flex; flex-wrap: wrap; }
        .nav-dropdown.two-col a { width: 50%; }

        .header-actions { display: flex; align-items: center; gap: 10px; }
        .btn-ghost {
            padding: 7px 14px; border-radius: 999px;
            font-size: 13px; font-weight: 500;
            border: 1px solid var(--border);
            background: transparent; color: var(--text);
            cursor: pointer; transition: background .15s, border-color .15s;
        }
        .btn-ghost:hover { background: var(--paper); border-color: #cbd5e1; }
        .btn-wa-header {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 999px;
            font-size: 13px; font-weight: 600;
            background: #25d366; color: #fff;
            transition: filter .15s;
        }
        .btn-wa-header:hover { filter: brightness(1.06); }
        @media(max-width:900px) {
            .header-nav { display: none; }
            .btn-wa-header span { display: none; }
        }

        /* ── HERO ── */
        .hero {
            padding: 72px 20px 80px;
            background: linear-gradient(160deg, #f0f9ff 0%, #f0fdf4 55%, #faf5ff 100%);
            text-align: center;
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,.8); border: 1px solid var(--border);
            border-radius: 999px; padding: 5px 14px;
            font-size: 12px; font-weight: 600; color: var(--muted);
            letter-spacing: .02em; margin-bottom: 24px;
        }
        .hero-eyebrow .dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.3)} }
        .hero h1 {
            font-size: clamp(32px, 5vw, 56px);
            font-weight: 800; line-height: 1.1;
            letter-spacing: -.02em; margin-bottom: 20px;
        }
        .hero h1 .gradient {
            background: linear-gradient(90deg, #0ea5e9 20%, #22c55e 80%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-sub {
            font-size: clamp(16px, 2.2vw, 19px);
            color: var(--muted); max-width: 620px; margin: 0 auto 32px;
            line-height: 1.65;
        }
        .trust-row {
            display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;
            margin-bottom: 52px;
        }
        .trust-badge {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.9); border: 1px solid var(--border);
            border-radius: 999px; padding: 6px 13px;
            font-size: 12px; font-weight: 500; color: var(--muted);
        }
        .trust-badge .check { color: #22c55e; font-weight: 700; }

        /* ── SEGMENT CARDS ── */
        .segment-split {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 20px; max-width: 860px; margin: 0 auto;
        }
        @media(max-width:680px) { .segment-split { grid-template-columns: 1fr; } }

        .segment-card {
            background: #fff; border-radius: var(--radius-lg);
            padding: 32px 28px; text-align: left;
            box-shadow: var(--shadow-md);
            border: 2px solid transparent;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            display: flex; flex-direction: column;
        }
        .segment-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
        .segment-card.clinica { border-color: var(--seg-clinica-border); }
        .segment-card.profissional { border-color: var(--seg-prof-border); }

        .seg-tag {
            display: inline-flex; align-items: center; gap: 6px;
            border-radius: 999px; padding: 4px 12px;
            font-size: 11px; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; margin-bottom: 16px;
        }
        .clinica .seg-tag { background: var(--seg-clinica-light); color: var(--seg-clinica); }
        .profissional .seg-tag { background: var(--seg-prof-light); color: var(--seg-prof); }

        .segment-card h3 { font-size: 22px; font-weight: 800; margin-bottom: 8px; }
        .segment-card .seg-desc { font-size: 14px; color: var(--muted); margin-bottom: 20px; line-height: 1.6; }

        .seg-features { display: flex; flex-direction: column; gap: 9px; margin-bottom: 28px; flex: 1; }
        .seg-feature {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 14px; color: #334155;
        }
        .seg-feature .ico { font-size: 15px; flex-shrink: 0; margin-top: 1px; }

        .btn-seg {
            display: block; text-align: center;
            padding: 13px 20px; border-radius: 999px;
            font-size: 15px; font-weight: 700;
            transition: filter .15s, transform .1s, box-shadow .15s;
        }
        .btn-seg:hover { filter: brightness(1.07); transform: translateY(-1px); }
        .clinica .btn-seg {
            background: linear-gradient(90deg, #2563eb, #0ea5e9);
            color: #fff; box-shadow: 0 6px 18px rgba(37,99,235,.3);
        }
        .profissional .btn-seg {
            background: linear-gradient(90deg, #7c3aed, #a78bfa);
            color: #fff; box-shadow: 0 6px 18px rgba(124,58,237,.3);
        }
        .btn-seg-note { font-size: 12px; text-align: center; color: var(--subtle); margin-top: 10px; }

        /* ── DIVIDER ── */
        .section-divider { height: 1px; background: var(--border); margin: 0 20px; }

        /* ── SECTION HEADER ── */
        .section-label {
            font-size: 12px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: var(--brand-blue);
            margin-bottom: 10px;
        }
        .section-title {
            font-size: clamp(26px, 3.5vw, 38px);
            font-weight: 800; letter-spacing: -.02em; line-height: 1.15;
            margin-bottom: 14px;
        }
        .section-sub { font-size: 16px; color: var(--muted); max-width: 560px; line-height: 1.65; }

        /* ── FEATURES ── */
        .features-section { padding: 88px 20px; background: #fff; }
        .features-header { margin-bottom: 52px; }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        @media(max-width:900px) { .features-grid { grid-template-columns: 1fr 1fr; } }
        @media(max-width:560px) { .features-grid { grid-template-columns: 1fr; } }

        .feature-card {
            padding: 28px 24px; border-radius: var(--radius-md);
            border: 1px solid var(--border); background: var(--paper);
            transition: box-shadow .2s, transform .2s;
        }
        .feature-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 16px;
        }
        .feature-card h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.65; }

        /* ── HOW IT WORKS ── */
        .how-section { padding: 88px 20px; background: var(--paper); }
        .how-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 32px; margin-top: 52px; position: relative;
        }
        @media(max-width:700px) { .how-grid { grid-template-columns: 1fr; } .how-grid::before { display: none; } }
        .how-step { text-align: center; }
        .step-number {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #fff; font-size: 22px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            position: relative; z-index: 1;
        }
        .how-step h4 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
        .how-step p { font-size: 14px; color: var(--muted); line-height: 1.65; max-width: 260px; margin: 0 auto; }
        .how-grid::before {
            content: ''; position: absolute;
            top: 26px; left: calc(100% / 6); right: calc(100% / 6);
            height: 2px;
            background: linear-gradient(90deg, var(--brand-blue), var(--brand-green));
            z-index: 0;
        }

        /* ── SPECIALTIES ── */
        .spec-section { padding: 80px 20px; background: #fff; }
        .spec-grid {
            display: flex; flex-wrap: wrap; gap: 10px;
            margin-top: 36px;
        }
        .spec-pill {
            padding: 8px 16px; border-radius: 999px;
            border: 1px solid var(--border); background: var(--paper);
            font-size: 13px; color: var(--muted);
            transition: background .15s, border-color .15s, color .15s;
        }
        .spec-pill:hover {
            background: var(--seg-clinica-light);
            border-color: var(--seg-clinica-border);
            color: var(--seg-clinica);
        }

        /* ── SOCIAL PROOF ── */
        .social-section { padding: 88px 20px; background: var(--paper); }
        .social-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 24px; margin-top: 52px;
        }
        @media(max-width:900px) { .social-grid { grid-template-columns: 1fr 1fr; } }
        @media(max-width:560px) { .social-grid { grid-template-columns: 1fr; } }
        .testimonial {
            background: #fff; border-radius: var(--radius-md);
            border: 1px solid var(--border); padding: 28px 24px;
            display: flex; flex-direction: column;
            transition: box-shadow .2s, transform .2s;
        }
        .testimonial:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .t-stars { color: #f59e0b; font-size: 15px; letter-spacing: 2px; margin-bottom: 16px; }
        .t-quote { font-size: 15px; color: #334155; line-height: 1.7; flex: 1; margin-bottom: 20px; }
        .t-author { display: flex; align-items: center; gap: 12px; }
        .t-avatar {
            width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; color: #fff;
        }
        .t-name { font-size: 14px; font-weight: 700; color: var(--text); }
        .t-role { font-size: 12px; color: var(--subtle); margin-top: 2px; }

        /* ── DUAL CTA BANNER ── */
        .cta-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f2a1f 100%);
            color: #fff; text-align: center;
        }
        .cta-section .section-label { color: #7dd3fc; }
        .cta-section .section-title { color: #fff; margin: 0 auto 14px; }
        .cta-section .section-sub { color: #94a3b8; margin: 0 auto 48px; }
        .cta-cards {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 20px; max-width: 780px; margin: 0 auto;
        }
        @media(max-width:600px) { .cta-cards { grid-template-columns: 1fr; } }
        .cta-card {
            border-radius: var(--radius-lg); padding: 32px 24px;
            border: 1px solid rgba(255,255,255,.1); text-align: left;
        }
        .cta-card.clinica-cta { background: rgba(37,99,235,.15); border-color: rgba(37,99,235,.3); }
        .cta-card.prof-cta { background: rgba(124,58,237,.15); border-color: rgba(124,58,237,.3); }
        .cta-card-emoji { font-size: 28px; margin-bottom: 14px; }
        .cta-card h3 { font-size: 20px; font-weight: 800; margin-bottom: 8px; color: #fff; }
        .cta-card p { font-size: 14px; color: #94a3b8; margin-bottom: 24px; line-height: 1.6; }
        .btn-cta-clinica {
            display: inline-block; padding: 12px 22px; border-radius: 999px;
            background: linear-gradient(90deg, #2563eb, #0ea5e9);
            color: #fff; font-size: 14px; font-weight: 700;
            box-shadow: 0 6px 18px rgba(37,99,235,.4);
            transition: filter .15s, transform .1s;
        }
        .btn-cta-prof {
            display: inline-block; padding: 12px 22px; border-radius: 999px;
            background: linear-gradient(90deg, #7c3aed, #a78bfa);
            color: #fff; font-size: 14px; font-weight: 700;
            box-shadow: 0 6px 18px rgba(124,58,237,.4);
            transition: filter .15s, transform .1s;
        }
        .btn-cta-clinica:hover, .btn-cta-prof:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .cta-note { margin-top: 28px; font-size: 12px; color: #64748b; }

        /* ── CONTACT ── */
        .contact-section { padding: 80px 20px; background: var(--paper); }
        .contact-grid {
            display: grid; grid-template-columns: 1fr 1fr 1fr;
            gap: 20px; margin-top: 48px;
        }
        @media(max-width:780px) { .contact-grid { grid-template-columns: 1fr; } }
        .contact-card {
            background: #fff; border-radius: var(--radius-md);
            padding: 28px 24px; border: 1px solid var(--border);
        }
        .contact-card-icon { font-size: 28px; margin-bottom: 14px; }
        .contact-card h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .contact-card p, .contact-card a { font-size: 14px; color: var(--muted); line-height: 1.7; }
        .contact-card a { color: var(--brand-blue); font-weight: 600; }
        .contact-card a:hover { text-decoration: underline; }
        .btn-wa-contact {
            display: inline-flex; align-items: center; gap: 8px;
            margin-top: 14px; padding: 10px 20px; border-radius: 999px;
            background: #25d366; color: #fff;
            font-size: 14px; font-weight: 700;
            transition: filter .15s;
        }
        .btn-wa-contact:hover { filter: brightness(1.08); }

        /* ── LOGIN SECTION ── */
        .login-section { padding: 80px 20px; background: #fff; }
        .login-inner {
            max-width: 480px; margin: 0 auto;
            text-align: center;
        }
        .login-inner .section-title { font-size: 28px; }
        .login-form-wrap {
            margin-top: 32px; background: var(--paper);
            border-radius: var(--radius-lg); padding: 32px 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            text-align: left;
        }
        .login-form-wrap form { display: flex; flex-direction: column; gap: 16px; }
        .form-field { display: flex; flex-direction: column; gap: 6px; }
        .form-field label { font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .form-field input {
            padding: 11px 14px; border-radius: var(--radius-sm);
            border: 1px solid var(--border); font-size: 14px;
            font-family: inherit; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-field input:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(14,165,233,.12);
        }
        .login-row { display: flex; justify-content: space-between; align-items: center; font-size: 12px; }
        .remember { display: flex; align-items: center; gap: 6px; color: var(--muted); }
        .forgot a { color: var(--brand-blue); font-weight: 600; }
        .btn-login {
            width: 100%; padding: 12px; border: none; border-radius: 999px;
            background: linear-gradient(90deg, var(--brand-blue), var(--brand-green));
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 14px rgba(14,165,233,.35);
            transition: filter .15s, transform .1s;
        }
        .btn-login:hover { filter: brightness(1.07); transform: translateY(-1px); }
        .login-footer-note { text-align: center; font-size: 12px; color: var(--subtle); margin-top: 4px; }
        .login-footer-note a { color: var(--brand-green); font-weight: 600; }

        /* ── FOOTER ── */
        .site-footer {
            background: #0f172a; color: #94a3b8;
            padding: 56px 20px 0;
        }
        .footer-logo-image {
            display: block;
            height: 38px;
            width: auto;
        }
        .footer-name { font-size: 15px; font-weight: 700; color: #e2e8f0; }
        .footer-grid {
            max-width: 1120px; margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid #1e293b;
        }
        .footer-tagline { font-size: 13px; color: #475569; margin: 10px 0 20px; line-height: 1.65; max-width: 260px; }
        .footer-wa { display: inline-flex; align-items: center; gap: 6px; background: #25d366; color: #fff; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; }
        .footer-col-title { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #475569; margin-bottom: 14px; }
        .footer-col a { display: block; font-size: 13px; color: #64748b; transition: color .15s; margin-bottom: 9px; }
        .footer-col a:hover { color: #94a3b8; }
        .footer-bottom {
            max-width: 1120px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
            padding: 20px 0;
            font-size: 12px; color: #334155;
        }
        .footer-bottom a { color: #475569; margin-left: 16px; }
        .footer-bottom a:hover { color: #64748b; }
        @media(max-width: 900px) { .footer-grid { grid-template-columns: 1fr 1fr 1fr; } }
        @media(max-width: 560px) { .footer-grid { grid-template-columns: 1fr 1fr; } .footer-bottom { flex-direction: column; align-items: flex-start; } }

        /* ── FLOATING WHATSAPP ── */
        .wa-float {
            position: fixed; bottom: 24px; right: 24px; z-index: 200;
            width: 56px; height: 56px; border-radius: 50%;
            background: #25d366;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(37,211,102,.45);
            transition: transform .2s, box-shadow .2s;
        }
        .wa-float:hover { transform: scale(1.08); box-shadow: 0 8px 24px rgba(37,211,102,.5); }
        .wa-float svg { width: 28px; height: 28px; fill: #fff; }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 300;
            background: rgba(15,23,42,.55); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff; border-radius: var(--radius-lg);
            padding: 36px 32px; width: 100%; max-width: 420px;
            margin: 16px; box-shadow: var(--shadow-lg);
            position: relative;
        }
        .modal-close {
            position: absolute; top: 14px; right: 16px;
            background: none; border: none; cursor: pointer;
            font-size: 22px; color: var(--subtle); line-height: 1;
        }
        .modal-close:hover { color: var(--text); }
        .modal-box h3 { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
        .modal-box .modal-sub { font-size: 14px; color: var(--muted); margin-bottom: 24px; }

        /* ── PRODUCT IMG INSIDE HERO ── */
        .hero-product-wrap {
            margin: 0 auto 52px;
            max-width: 980px;
        }
        .hero-product-wrap img {
            width: 100%;
            border-radius: var(--radius-lg);
            box-shadow: 0 24px 64px rgba(15,23,42,.18);
            border: 1px solid rgba(226,232,240,.7);
            display: block;
        }

        /* ── UTILITY ── */
        .text-center { text-align: center; }
        .mt-4 { margin-top: 16px; }

        /* ── PLANS ── */
        .plans-section { padding: 72px 20px; background: var(--paper); }
        .plans-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-top: 40px;
        }
        @media(max-width: 900px) { .plans-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 560px) { .plans-grid { grid-template-columns: 1fr; } }
        .plan-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 24px 20px;
            display: flex; flex-direction: column; gap: 8px;
        }
        .plan-card--custom { border-style: dashed; }
        .plan-name { font-size: 18px; font-weight: 800; margin: 0; color: var(--text); }
        .plan-desc { font-size: 14px; color: var(--muted); line-height: 1.6; margin: 0; flex: 1; }
        .plan-price { font-size: 12px; color: var(--subtle); margin: 4px 0 0; }
        .btn-plan {
            display: block; text-align: center;
            padding: 10px 16px; border-radius: 999px;
            border: 1.5px solid var(--brand-blue); color: var(--brand-blue);
            font-size: 14px; font-weight: 600; text-decoration: none;
            transition: background .15s, color .15s; margin-top: 8px;
        }
        .btn-plan:hover { background: var(--brand-blue); color: #fff; }
        .btn-plan--wa { border-color: #25d366; color: #25d366; }
        .btn-plan--wa:hover { background: #25d366; color: #fff; }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════
     FLOATING WHATSAPP
═══════════════════════════════════════════════════════ -->
<a class="wa-float" href="https://wa.me/5581983276882?text=Ol%C3%A1%2C%20gostaria%20de%20conhecer%20o%20UTecnologia%20Sa%C3%BAde" target="_blank" rel="noopener" title="Fale pelo WhatsApp">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
    </svg>
</a>

<!-- ═══════════════════════════════════════════════════════
     LOGIN MODAL
═══════════════════════════════════════════════════════ -->
<div id="login-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-label="Entrar no sistema">
    <div class="modal-box">
        <button class="modal-close" onclick="fecharModal()" aria-label="Fechar">&times;</button>
        <h3>Entrar no sistema</h3>
        <p class="modal-sub">Acesse sua conta para gerenciar pacientes, agenda e prontuários.</p>
        <form action="<?=base_url()?>admin/logar" method="post" style="display:flex;flex-direction:column;gap:14px;">
            <div class="form-field">
                <label for="m-login">E-mail ou usuário</label>
                <input type="text" id="m-login" name="login" placeholder="seuemail@clinica.com.br" required autocomplete="username">
            </div>
            <div class="form-field">
                <label for="m-senha">Senha</label>
                <input type="password" id="m-senha" name="senha" placeholder="Sua senha" required autocomplete="current-password">
            </div>
            <div class="login-row">
                <label class="remember">
                    <input type="checkbox" name="lembrar" value="1" style="width:14px;height:14px;">
                    Lembrar-me
                </label>
                <div class="forgot"><a href="#">Esqueci minha senha</a></div>
            </div>
            <button type="submit" class="btn-login">Entrar no sistema</button>
            <p class="login-footer-note">Ainda não tem conta? <a href="<?=base_url()?>experimentar">Experimente 30 dias grátis</a></p>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     HEADER
═══════════════════════════════════════════════════════ -->
<header class="site-header">
    <div class="container header-inner">
        <a href="<?=base_url()?>" class="logo">
            <img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" class="logo-image">
            <div>
                <div class="logo-name">UTecnologia Saúde</div>
                <div class="logo-tagline">Gestão clínica inteligente</div>
            </div>
        </a>
        <nav class="header-nav">

            <!-- Sistema -->
            <div class="nav-item">
                <a class="nav-link" href="<?=base_url()?>sistema-para-clinicas">
                    Sistema
                    <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6,9 12,15 18,9"/></svg>
                </a>
                <div class="nav-dropdown">
                    <a href="<?=base_url()?>sistema-para-clinicas">
                        <span class="dd-icon">🏥</span>
                        <span class="dd-text"><span class="dd-label">Para clínicas</span><span class="dd-desc">Gestão completa para equipes</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-prontuario-eletronico">
                        <span class="dd-icon">📋</span>
                        <span class="dd-text"><span class="dd-label">Prontuário eletrônico</span><span class="dd-desc">Histórico clínico digital</span></span>
                    </a>
                    <a href="<?=base_url()?>software-para-clinicas">
                        <span class="dd-icon">💻</span>
                        <span class="dd-text"><span class="dd-label">Software para clínicas</span><span class="dd-desc">SaaS 100% online, sem instalação</span></span>
                    </a>
                    <div class="nav-divider"></div>
                    <a href="<?=base_url()?>sistema-gratuito-para-clinicas">
                        <span class="dd-icon">🎁</span>
                        <span class="dd-text"><span class="dd-label">30 dias grátis</span><span class="dd-desc">Teste sem cartão de crédito</span></span>
                    </a>
                </div>
            </div>

            <!-- Especialidades -->
            <div class="nav-item">
                <a class="nav-link" href="#especialidades">
                    Especialidades
                    <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6,9 12,15 18,9"/></svg>
                </a>
                <div class="nav-dropdown two-col">
                    <a href="<?=base_url()?>sistema-para-clinica-medica">
                        <span class="dd-icon">👨‍⚕️</span><span class="dd-text"><span class="dd-label">Clínica médica</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-psicologos">
                        <span class="dd-icon">🧠</span><span class="dd-text"><span class="dd-label">Psicólogos</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-dentistas">
                        <span class="dd-icon">🦷</span><span class="dd-text"><span class="dd-label">Dentistas</span></span>
                    </a>
                    <a href="<?=base_url()?>software-para-clinicas-odontologicas">
                        <span class="dd-icon">🪥</span><span class="dd-text"><span class="dd-label">Clínicas odontológicas</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-clinica-de-fisioterapia">
                        <span class="dd-icon">🏃</span><span class="dd-text"><span class="dd-label">Fisioterapia</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-clinica-oftalmologica">
                        <span class="dd-icon">👁️</span><span class="dd-text"><span class="dd-label">Oftalmologia</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-consultorio-medico">
                        <span class="dd-icon">🩺</span><span class="dd-text"><span class="dd-label">Consultório médico</span></span>
                    </a>
                    <a href="<?=base_url()?>software-para-medicos">
                        <span class="dd-icon">💊</span><span class="dd-text"><span class="dd-label">Médicos autônomos</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-nutricionistas">
                        <span class="dd-icon">🥗</span><span class="dd-text"><span class="dd-label">Nutricionistas</span></span>
                    </a>
                    <a href="<?=base_url()?>sistema-para-clinicas">
                        <span class="dd-icon">➕</span><span class="dd-text"><span class="dd-label">Ver todas</span></span>
                    </a>
                </div>
            </div>

            <!-- Blog -->
            <div class="nav-item">
                <a class="nav-link" href="<?=base_url()?>blog">Blog</a>
            </div>

            <!-- Empresa -->
            <div class="nav-item">
                <a class="nav-link" href="<?=base_url()?>sobre">
                    Empresa
                    <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6,9 12,15 18,9"/></svg>
                </a>
                <div class="nav-dropdown">
                    <a href="<?=base_url()?>sobre">
                        <span class="dd-icon">🏢</span>
                        <span class="dd-text"><span class="dd-label">Sobre nós</span><span class="dd-desc">Quem somos e nossa missão</span></span>
                    </a>
                    <a href="<?=base_url()?>contato">
                        <span class="dd-icon">💬</span>
                        <span class="dd-text"><span class="dd-label">Contato</span><span class="dd-desc">WhatsApp e e-mail</span></span>
                    </a>
                    <a href="<?=base_url()?>politica-de-privacidade">
                        <span class="dd-icon">🔒</span>
                        <span class="dd-text"><span class="dd-label">Privacidade</span><span class="dd-desc">Política LGPD</span></span>
                    </a>
                </div>
            </div>

        </nav>
        <div class="header-actions">
            <button class="btn-ghost" onclick="abrirModal()">Já tenho conta</button>
            <a class="btn-wa-header" href="https://wa.me/5581983276882?text=Ol%C3%A1%2C%20gostaria%20de%20conhecer%20o%20UTecnologia%20Sa%C3%BAde" target="_blank" rel="noopener">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                <span>WhatsApp</span>
            </a>
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ -->
<section class="hero">
    <div class="container">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            30 dias grátis · Sem cartão de crédito · Acesso imediato
        </div>

        <h1>
            Gestão clínica completa para<br>
            <span class="gradient">clínicas e profissionais de saúde</span>
        </h1>

        <p class="hero-sub">
            Prontuário eletrônico, agenda inteligente, controle de exames e atendimentos —
            tudo integrado em um sistema simples e acessível de qualquer dispositivo.
        </p>

        <div class="trust-row">
            <span class="trust-badge"><span class="check">✓</span> 30 dias grátis</span>
            <span class="trust-badge"><span class="check">✓</span> Sem cartão de crédito</span>
            <span class="trust-badge"><span class="check">✓</span> Acesso imediato</span>
            <span class="trust-badge"><span class="check">✓</span> Cancele quando quiser</span>
            <span class="trust-badge"><span class="check">✓</span> Dados seguros e privados</span>
        </div>

        <!-- PRODUTO EM AÇÃO -->
        <div class="hero-product-wrap">
            <img
                src="<?=base_url()?>imagens/utec-dash3.png"
                alt="UTecnologia Saúde — agenda, prontuário e atendimentos em múltiplos dispositivos"
                loading="eager"
            >
        </div>

        <!-- ESCOLHA DE SEGMENTO -->
        <div class="segment-split">

            <!-- CLÍNICA -->
            <div class="segment-card clinica">
                <span class="seg-tag">🏥 Para Clínicas</span>
                <h3>Minha clínica tem mais de um profissional</h3>
                <p class="seg-desc">
                    Gerencie toda a equipe em um único sistema — médicos, recepcionistas, colaboradores —
                    com agenda compartilhada, prontuários centralizados e controle de acesso por perfil.
                </p>
                <ul class="seg-features">
                    <li class="seg-feature"><span class="ico">📅</span>Agenda por profissional e sala</li>
                    <li class="seg-feature"><span class="ico">👥</span>Gestão de equipe com permissões</li>
                    <li class="seg-feature"><span class="ico">📋</span>Prontuários e histórico centralizado</li>
                    <li class="seg-feature"><span class="ico">📊</span>Relatórios gerenciais e clínicos</li>
                    <li class="seg-feature"><span class="ico">🔒</span>Controle de acesso por função</li>
                </ul>
                <a href="<?=base_url()?>experimentar?tipo=clinica" class="btn-seg"
                   onclick="if(typeof fbq!=='undefined')fbq('track','Lead',{content_category:'clinica',content_name:'CTA Landing Clinica'})">
                    Quero para minha clínica →
                </a>
                <p class="btn-seg-note">Experimente 30 dias grátis, sem compromisso</p>
            </div>

            <!-- PROFISSIONAL -->
            <div class="segment-card profissional">
                <span class="seg-tag">👨‍⚕️ Para Profissionais</span>
                <h3>Trabalho sozinho no meu consultório</h3>
                <p class="seg-desc">
                    Perfeito para fisioterapeutas, psicólogos, nutricionistas, fonoaudiólogos e qualquer
                    profissional de saúde que atende de forma independente ou em consultório próprio.
                </p>
                <ul class="seg-features">
                    <li class="seg-feature"><span class="ico">📝</span>Prontuário eletrônico pessoal</li>
                    <li class="seg-feature"><span class="ico">📅</span>Agenda de consultas simplificada</li>
                    <li class="seg-feature"><span class="ico">🗂️</span>Histórico completo de cada paciente</li>
                    <li class="seg-feature"><span class="ico">📎</span>Anexo de exames e documentos</li>
                    <li class="seg-feature"><span class="ico">📱</span>Acesso em qualquer dispositivo</li>
                </ul>
                <a href="<?=base_url()?>experimentar?tipo=profissional" class="btn-seg"
                   onclick="if(typeof fbq!=='undefined')fbq('track','Lead',{content_category:'profissional',content_name:'CTA Landing Profissional'})">
                    Quero para meu consultório →
                </a>
                <p class="btn-seg-note">Experimente 30 dias grátis, sem compromisso</p>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     FUNCIONALIDADES
═══════════════════════════════════════════════════════ -->
<section id="funcionalidades" class="features-section">
    <div class="container">
        <div class="features-header">
            <p class="section-label">O que está incluído</p>
            <h2 class="section-title">Tudo que você precisa para<br>organizar sua prática clínica</h2>
            <p class="section-sub">Um sistema pensado para a rotina real de profissionais de saúde — sem complexidade desnecessária.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background:#eff6ff;">📋</div>
                <h4>Prontuário Eletrônico Completo</h4>
                <p>Registre anamnese, evolução clínica, hipóteses diagnósticas, prescrições e anotações. Histórico completo do paciente em poucos cliques.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#f0fdf4;">📅</div>
                <h4>Agenda Inteligente</h4>
                <p>Controle de consultas por profissional, com visão diária, semanal e mensal. Cancele e remarque diretamente na agenda sem retrabalho.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#fefce8;">🔬</div>
                <h4>Exames e Documentos</h4>
                <p>Organize laudos, exames laboratoriais, imagens e documentos de cada paciente. Acesse o histórico completo em qualquer atendimento.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#fdf4ff;">👥</div>
                <h4>Cadastro Completo de Pacientes</h4>
                <p>Dados cadastrais, contatos, convênios, responsáveis e observações gerais. Base de pacientes organizada e fácil de pesquisar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#fff7ed;">📊</div>
                <h4>Relatórios Clínicos</h4>
                <p>Gere relatórios de atendimentos por profissional, período e especialidade. Exporte para PDF e tenha visão gerencial da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:#f0f9ff;">🔒</div>
                <h4>Segurança e Controle de Acesso</h4>
                <p>Perfis individuais para cada membro da equipe com permissões distintas. Dados dos pacientes protegidos e acessíveis só por quem deve.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     COMO FUNCIONA
═══════════════════════════════════════════════════════ -->
<section id="como-funciona" class="how-section">
    <div class="container text-center">
        <p class="section-label">Simples e rápido</p>
        <h2 class="section-title">Comece a atender em 3 passos</h2>
        <p class="section-sub" style="margin:0 auto;">Do cadastro ao primeiro atendimento em menos de 10 minutos.</p>

        <div class="how-grid">
            <div class="how-step">
                <div class="step-number">1</div>
                <h4>Crie sua conta</h4>
                <p>Preencha nome, e-mail e escolha seu plano. Sem cartão de crédito exigido. O acesso é liberado na hora.</p>
            </div>
            <div class="how-step">
                <div class="step-number">2</div>
                <h4>Configure seu perfil</h4>
                <p>Informe sua especialidade, horários de atendimento e, se tiver equipe, adicione os colaboradores com suas funções.</p>
            </div>
            <div class="how-step">
                <div class="step-number">3</div>
                <h4>Comece a atender</h4>
                <p>Cadastre seus pacientes, abra a agenda e registre os prontuários. Tudo pronto para o dia a dia clínico.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     ESPECIALIDADES
═══════════════════════════════════════════════════════ -->
<section id="especialidades" class="spec-section">
    <div class="container">
        <p class="section-label">Abrangência</p>
        <h2 class="section-title">Para qualquer especialidade clínica</h2>
        <p class="section-sub">O sistema atende toda especialidade que trabalha com prontuário, agenda e atendimento de pacientes.</p>

        <div class="spec-grid">
            <span class="spec-pill">Medicina Geral</span>
            <span class="spec-pill">Psicologia</span>
            <span class="spec-pill">Fisioterapia</span>
            <span class="spec-pill">Nutrição</span>
            <span class="spec-pill">Fonoaudiologia</span>
            <span class="spec-pill">Odontologia</span>
            <span class="spec-pill">Pediatria</span>
            <span class="spec-pill">Ginecologia e Obstetrícia</span>
            <span class="spec-pill">Cardiologia</span>
            <span class="spec-pill">Dermatologia</span>
            <span class="spec-pill">Ortopedia</span>
            <span class="spec-pill">Neurologia</span>
            <span class="spec-pill">Endocrinologia</span>
            <span class="spec-pill">Oftalmologia</span>
            <span class="spec-pill">Terapia Ocupacional</span>
            <span class="spec-pill">Medicina do Trabalho</span>
            <span class="spec-pill">Psiquiatria</span>
            <span class="spec-pill">Urologia</span>
            <span class="spec-pill">Reumatologia</span>
            <span class="spec-pill">Acupuntura</span>
            <span class="spec-pill">Homeopatia</span>
            <span class="spec-pill">Medicina Integrativa</span>
            <span class="spec-pill">E muito mais...</span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     PROVAS SOCIAIS
═══════════════════════════════════════════════════════ -->
<section class="social-section">
    <div class="container">
        <p class="section-label">Quem já usa</p>
        <h2 class="section-title">Profissionais de saúde que<br>transformaram sua rotina clínica</h2>
        <p class="section-sub">Clínicas e profissionais que saíram das planilhas e passaram a atender com mais organização e eficiência.</p>

        <div class="social-grid">

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Antes eu controlava minha agenda no WhatsApp e o prontuário em papel. Com o sistema, consigo ver todos os meus pacientes da semana em um clique e registrar a evolução diretamente no atendimento. Economizo pelo menos uma hora por dia."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);">A</div>
                    <div>
                        <div class="t-name">Dra. Ana Beatriz Mendes</div>
                        <div class="t-role">Psicóloga Clínica · Recife, PE</div>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Nossa clínica tem três fisioterapeutas e eu precisava de algo que centralizasse os agendamentos. O controle de acesso por perfil foi o que mais me convenceu — cada profissional vê apenas os seus pacientes. Simples e seguro."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#2563eb,#0ea5e9);">R</div>
                    <div>
                        <div class="t-name">Rodrigo Cavalcante</div>
                        <div class="t-role">Gestor · Clínica Movimento Saúde · Caruaru, PE</div>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Trabalho sozinha no consultório e precisava de algo leve, sem complicação. Em menos de 15 minutos já tinha a agenda configurada e meu primeiro paciente cadastrado. O histórico clínico é muito claro — consigo rever qualquer consulta anterior em segundos."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#0f766e,#22c55e);">C</div>
                    <div>
                        <div class="t-name">Camila Ferreira</div>
                        <div class="t-role">Nutricionista · Consultório próprio · Olinda, PE</div>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Minha recepcionista agenda pelo sistema e eu acesso o prontuário pelo celular antes de entrar na sala. O relatório mensal de atendimentos que antes levava horas para montar agora gero em um botão."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#ea580c,#fbbf24);">M</div>
                    <div>
                        <div class="t-name">Dr. Marcelo Alencar</div>
                        <div class="t-role">Oftalmologista · Clínica Visão Plena · Maceió, AL</div>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Atendo crianças com TEA e preciso de registro detalhado de cada sessão. A timeline de prontuário me dá uma visão perfeita da evolução de cada paciente. Os pais ficam impressionados quando mostro o histórico organizado nas revisões."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#7c3aed,#ec4899);">P</div>
                    <div>
                        <div class="t-name">Patrícia Lima</div>
                        <div class="t-role">Fonoaudióloga · João Pessoa, PB</div>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="t-stars">★★★★★</div>
                <p class="t-quote">"Migrei de outro sistema que era caro e complicado demais para uma clínica pequena. Aqui pago um preço justo, o suporte responde rápido e o sistema faz exatamente o que eu preciso: agenda + prontuário + exames integrados."</p>
                <div class="t-author">
                    <div class="t-avatar" style="background:linear-gradient(135deg,#0369a1,#0ea5e9);">F</div>
                    <div>
                        <div class="t-name">Fernando Sousa</div>
                        <div class="t-role">Ortopedista · Clínica OrthoVida · Natal, RN</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CTA FINAL
═══════════════════════════════════════════════════════ -->
<section class="cta-section">
    <div class="container">
        <p class="section-label">Comece hoje</p>
        <h2 class="section-title" style="max-width:600px;margin:0 auto 14px;">
            30 dias para descobrir que gestão clínica pode ser simples
        </h2>
        <p class="section-sub">
            Sem burocracia, sem contrato. Crie sua conta agora e veja o sistema funcionando para você.
        </p>

        <div class="cta-cards">
            <div class="cta-card clinica-cta">
                <div class="cta-card-emoji">🏥</div>
                <h3>Para Clínicas</h3>
                <p>Agenda compartilhada, gestão de equipe, relatórios e prontuários centralizados para múltiplos profissionais.</p>
                <a href="<?=base_url()?>experimentar?tipo=clinica" class="btn-cta-clinica">Experimentar para minha clínica →</a>
            </div>
            <div class="cta-card prof-cta">
                <div class="cta-card-emoji">👨‍⚕️</div>
                <h3>Para Profissionais</h3>
                <p>Prontuário, agenda e histórico de pacientes para fisioterapeutas, psicólogos, nutricionistas e outros profissionais independentes.</p>
                <a href="<?=base_url()?>experimentar?tipo=profissional" class="btn-cta-prof">Experimentar para meu consultório →</a>
            </div>
        </div>
        <p class="cta-note">Sem cartão de crédito · Acesso imediato · Cancele quando quiser</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CONTATO
═══════════════════════════════════════════════════════ -->
<section id="contato" class="contact-section">
    <div class="container">
        <p class="section-label">Estamos aqui</p>
        <h2 class="section-title">Tem dúvidas? Fale com a gente</h2>
        <p class="section-sub">Nossa equipe atende clínicas e profissionais de saúde em todo o Brasil.</p>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-card-icon">💬</div>
                <h4>WhatsApp</h4>
                <p>Atendimento rápido para dúvidas sobre o sistema, planos e suporte técnico.</p>
                <a class="btn-wa-contact" href="https://wa.me/5581983276882?text=Ol%C3%A1%2C%20gostaria%20de%20conhecer%20o%20UTecnologia%20Sa%C3%BAde" target="_blank" rel="noopener">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    Chamar no WhatsApp
                </a>
            </div>
            <div class="contact-card">
                <div class="contact-card-icon">✉️</div>
                <h4>E-mail</h4>
                <p>Para questões detalhadas, envie um e-mail e responderemos em até 1 dia útil.</p>
                <p style="margin-top:12px;"><a href="mailto:suporte@utecnologia.com.br">suporte@utecnologia.com.br</a></p>
            </div>
            <div class="contact-card">
                <div class="contact-card-icon">🌎</div>
                <h4>100% Online</h4>
                <p>Sistema acessível pelo navegador, de qualquer computador, tablet ou celular. Atendemos todo o Brasil.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     LOGIN SECTION (clientes existentes)
═══════════════════════════════════════════════════════ -->
<section id="login" class="login-section">
    <div class="container">
        <div class="login-inner">
            <p class="section-label text-center">Acesso ao sistema</p>
            <h2 class="section-title text-center">Já tem conta?</h2>
            <p style="color:var(--muted);font-size:15px;margin-top:8px;">Entre com seu e-mail e senha para acessar sua clínica ou consultório.</p>

            <div class="login-form-wrap">
                <form action="<?=base_url()?>admin/logar" method="post">
                    <div class="form-field">
                        <label for="login-email">E-mail ou usuário</label>
                        <input type="text" id="login-email" name="login" placeholder="seuemail@clinica.com.br" required autocomplete="username">
                    </div>
                    <div class="form-field">
                        <label for="login-senha">Senha</label>
                        <input type="password" id="login-senha" name="senha" placeholder="Sua senha" required autocomplete="current-password">
                    </div>
                    <div class="login-row">
                        <label class="remember">
                            <input type="checkbox" name="lembrar" value="1" style="width:14px;height:14px;">
                            Lembrar-me
                        </label>
                        <div class="forgot"><a href="#">Esqueci minha senha</a></div>
                    </div>
                    <button type="submit" class="btn-login">Entrar no sistema</button>
                    <p class="login-footer-note">
                        Ainda não tem conta?
                        <a href="<?=base_url()?>experimentar">Experimente 30 dias grátis</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════ -->
<footer class="site-footer">
    <div class="footer-grid">

        <!-- Marca -->
        <div>
            <div style="display:flex;align-items:center;gap:10px;">
                <img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" class="footer-logo-image">
                <span class="footer-name">UTecnologia Saúde</span>
            </div>
            <p class="footer-tagline">Sistema SaaS de gestão clínica para médicos, psicólogos, dentistas, fisioterapeutas e outros profissionais de saúde.</p>
            <a class="footer-wa" href="https://wa.me/5581983276882" target="_blank" rel="noopener">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                WhatsApp
            </a>
        </div>

        <!-- Especialidades -->
        <div class="footer-col">
            <div class="footer-col-title">Especialidades</div>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
            <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
            <a href="<?=base_url()?>software-para-clinicas-odontologicas">Clínicas Odontológicas</a>
            <a href="<?=base_url()?>sistema-para-clinica-de-fisioterapia">Fisioterapia</a>
            <a href="<?=base_url()?>sistema-para-clinica-oftalmologica">Oftalmologia</a>
            <a href="<?=base_url()?>sistema-para-nutricionistas">Nutricionistas</a>
            <a href="<?=base_url()?>sistema-para-consultorio-medico">Consultório Médico</a>
        </div>

        <!-- Recursos -->
        <div class="footer-col">
            <div class="footer-col-title">Recursos</div>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário Eletrônico</a>
            <a href="<?=base_url()?>software-para-clinicas">Software para Clínicas</a>
            <a href="<?=base_url()?>software-para-medicos">Software para Médicos</a>
            <a href="<?=base_url()?>sistema-gratuito-para-clinicas">Trial Gratuito</a>
            <a href="<?=base_url()?>assinar">Ver Planos</a>
        </div>

        <!-- Comparativos -->
        <div class="footer-col">
            <div class="footer-col-title">Comparativos</div>
            <a href="<?=base_url()?>alternativa-feegow">Alternativa ao Feegow</a>
            <a href="<?=base_url()?>alternativa-odontoclinic">Alternativa ao Odontoclinic</a>
            <a href="<?=base_url()?>sistema-para-clinicas">Ver todos os sistemas</a>
        </div>

        <!-- Empresa -->
        <div class="footer-col">
            <div class="footer-col-title">Empresa</div>
            <a href="<?=base_url()?>sobre">Sobre nós</a>
            <a href="<?=base_url()?>contato">Contato</a>
            <a href="<?=base_url()?>politica-de-privacidade">Política de Privacidade</a>
            <a href="<?=base_url()?>experimentar">Experimentar grátis</a>
        </div>

    </div>
    <div class="footer-bottom">
        <span>© <span id="ano"></span> UTecnologia Saúde — utecnologia.com.br</span>
        <div>
            <a href="<?=base_url()?>sobre">Sobre</a>
            <a href="<?=base_url()?>politica-de-privacidade">Privacidade</a>
            <a href="<?=base_url()?>contato">Contato</a>
        </div>
    </div>
</footer>

<script>
    document.getElementById('ano').textContent = new Date().getFullYear();

    function abrirModal() {
        document.getElementById('login-modal').classList.add('open');
        document.getElementById('m-login').focus();
    }
    function fecharModal() {
        document.getElementById('login-modal').classList.remove('open');
    }
    document.getElementById('login-modal').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fecharModal();
    });
</script>

<!-- Schema JSON-LD — SoftwareApplication -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "url": "https://utecnologia.com.br/",
  "description": "Sistema SaaS de gestão clínica com prontuário eletrônico, agenda inteligente e gestão de pacientes para clínicas e profissionais de saúde.",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "inLanguage": "pt-BR",
  "isAccessibleForFree": false,
  "offers": [
    {
      "@type": "Offer",
      "name": "Plano Solo",
      "price": "79.00",
      "priceCurrency": "BRL",
      "description": "1 profissional, 2 colaboradores, pacientes ilimitados"
    },
    {
      "@type": "Offer",
      "name": "Plano Clínica",
      "price": "199.00",
      "priceCurrency": "BRL",
      "description": "Até 5 profissionais, 10 colaboradores, pacientes ilimitados"
    },
    {
      "@type": "Offer",
      "name": "Plano Pro",
      "price": "399.00",
      "priceCurrency": "BRL",
      "description": "Até 20 profissionais, 50 colaboradores, pacientes ilimitados"
    }
  ],
  "featureList": [
    "Prontuário eletrônico",
    "Agenda inteligente",
    "Gestão de pacientes",
    "Exames e documentos integrados",
    "Relatórios clínicos",
    "Multi-tenant por clínica"
  ]
}
</script>

<!-- Schema JSON-LD — Organization -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "UTecnologia Saúde",
  "url": "https://utecnologia.com.br",
  "logo": "https://utecnologia.com.br/imagens/logo-utec.png",
  "description": "Sistema de gestão clínica SaaS para clínicas médicas e profissionais de saúde.",
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer support",
    "availableLanguage": "Portuguese"
  },
  "sameAs": []
}
</script>

<!-- Schema JSON-LD — WebSite com SearchAction -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "UTecnologia Saúde",
  "url": "https://utecnologia.com.br"
}
</script>
</body>
</html>
