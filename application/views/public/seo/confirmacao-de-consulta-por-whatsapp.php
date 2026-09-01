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
    <title>Confirmação e Lembrete de Consulta por WhatsApp — Sistema para Clínicas | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirme e lembre consultas automaticamente pelo WhatsApp: o paciente responde com um toque e a agenda da clínica atualiza sozinha. Envio pela API oficial da Meta. Teste grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp">
    <meta property="og:title" content="Confirmação e Lembrete de Consulta por WhatsApp — UTecnologia Saúde">
    <meta property="og:description" content="O paciente confirma ou cancela a consulta pelo WhatsApp com um toque e a agenda da clínica atualiza sozinha. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Confirmação e Lembrete de Consulta por WhatsApp — UTecnologia Saúde">
    <meta name="twitter:description" content="Confirmação no agendamento e lembrete automático por WhatsApp, com resposta do paciente por botão. 30 dias grátis.">
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
    .fm-select{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);font-family:var(--ff-body);}
    .fm-btn{display:block;width:100%;background:var(--teal);color:var(--white);border:none;padding:11px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--ff-body);text-align:center;margin-top:4px;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:560px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário — Medicina do Trabalho';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:0 auto;}
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:10px;}
    .faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .faq-q{font-size:15px;font-weight:600;color:var(--ink);padding:18px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;}
    .faq-q:hover{color:var(--teal);}
    .faq-chevron{color:var(--subtle);font-size:18px;transition:transform .25s;flex-shrink:0;}
    .faq-item.open .faq-chevron{transform:rotate(180deg);}
    .faq-a{font-size:14px;color:var(--muted);line-height:1.7;padding:0 24px;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s;}
    .faq-item.open .faq-a{max-height:400px;padding:0 24px 18px;}
    .faq-a a{color:var(--teal);}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.steps-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}}
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
                <div class="eyebrow">WhatsApp para Clínicas</div>
                <h1>Confirmação e lembrete de consulta por <em>WhatsApp</em></h1>
                <p class="hero-text">
                    Assim que a consulta é marcada, o paciente recebe a confirmação no WhatsApp.
                    No dia anterior e na manhã do atendimento, o sistema envia o lembrete sozinho.
                    O paciente confirma ou cancela com um toque — e a agenda da sua clínica se
                    atualiza na hora.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
                <div class="trust-line">
                    <span>Sem cartão de crédito</span>
                    <span>API oficial da Meta</span>
                    <span>A partir de R$ 79/mês</span>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Funciona para:</span>
                    <span class="funciona-chip">Consultório individual</span>
                    <span class="funciona-chip">Clínica com recepção</span>
                    <span class="funciona-chip">Odontologia</span>
                    <span class="funciona-chip">Psicologia e Fisioterapia</span>
                    <span class="funciona-chip">Clínica com várias agendas</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">WhatsApp · Confirmação de Consulta</span>
                </div>
                <div class="card-body" style="background:#e5ddd5;">
                    <div style="background:#fff;border-radius:10px;padding:12px 14px;font-size:13px;color:var(--ink);box-shadow:0 1px 1px rgba(0,0,0,.08);margin-bottom:10px;">
                        Olá, Maria! Sua consulta com a Dra. Ana está marcada para <strong>quinta, 12/09, às 14h30</strong>. Podemos confirmar?
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <span style="flex:1;text-align:center;border:1px solid var(--teal);color:var(--teal);border-radius:8px;padding:7px 0;font-weight:700;">✓ Confirmar</span>
                            <span style="flex:1;text-align:center;border:1px solid #c04040;color:#c04040;border-radius:8px;padding:7px 0;font-weight:700;">✕ Cancelar</span>
                        </div>
                    </div>
                    <div style="background:#d9fdd3;border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ink);max-width:85%;margin-left:auto;box-shadow:0 1px 1px rgba(0,0,0,.08);margin-bottom:10px;">
                        ✓ Confirmar
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ink);box-shadow:0 1px 1px rgba(0,0,0,.08);">
                        Perfeito, sua consulta está <strong>confirmada</strong>. ✅ Até quinta!
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Como funciona</div>
            <h2>Três passos, nenhum trabalho manual</h2>
            <p>Do agendamento à baixa na agenda, o fluxo roda sozinho — a recepção só acompanha.</p>
        </div>
        <div class="steps-grid">
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 1</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">Agendou, já avisou</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">Com o checkbox de WhatsApp ligado, o paciente recebe a confirmação no momento em que a consulta entra na agenda.</p>
            </div>
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 2</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">Lembrete automático</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">Um processo roda de hora em hora e dispara o lembrete nas janelas que você definir: um dia antes e/ou na manhã da consulta.</p>
            </div>
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;">
                <div style="font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;">PASSO 3</div>
                <div style="font-family:var(--ff-display);font-size:17px;color:#fff;margin-bottom:8px;">A agenda se atualiza</div>
                <p style="font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;">O paciente toca em confirmar ou cancelar. O status muda sozinho e a recepção recebe um aviso interno.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>O que a confirmação por WhatsApp faz pela sua clínica</h2>
        <p class="section-sub">Confirmação no agendamento, lembrete automático e resposta do paciente que cai direto na agenda.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Confirmação no agendamento</h3>
                <p>Marcou a consulta com o WhatsApp ligado? O paciente recebe na hora a mensagem com os botões de confirmar e cancelar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <h3>Lembrete automático</h3>
                <p>Um processo roda de hora em hora e envia o lembrete nas janelas que você escolher: véspera e/ou manhã da consulta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👆</div>
                <h3>Resposta que cai na agenda</h3>
                <p>Quando o paciente confirma ou cancela, o status do agendamento muda sozinho. Cancelou? O horário fica livre para remarcar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>Aviso para a recepção</h3>
                <p>Cada resposta vira um aviso interno para quem marcou a consulta e para o profissional. O sino no topo mostra o que falta ler.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏷️</div>
                <h3>Etiqueta na agenda</h3>
                <p>A agenda mostra "Confirmado via WhatsApp" ou "Cancelado via WhatsApp" em cada horário, no computador e no celular.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🟢</div>
                <h3>API oficial da Meta</h3>
                <p>O envio usa a WhatsApp Cloud API, com número verificado e modelo de mensagem aprovado — não é automação de celular nem número pessoal.</p>
            </div>
        </div>
        <p class="hero-text" style="font-size:15px;background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;max-width:680px;margin:40px auto 0;">
            <strong>Em resumo:</strong> o UTecnologia Saúde confirma a consulta no momento do agendamento
            e envia lembretes automáticos por WhatsApp na véspera e no dia. O paciente responde pelos
            botões da própria mensagem e a agenda se atualiza sem ninguém digitar nada. O que ainda é
            manual: reagendar (o paciente confirma ou cancela, mas não escolhe outro horário sozinho)
            e qualquer conversa fora do modelo aprovado pela Meta.
        </p>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre a confirmação por WhatsApp</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Preciso do WhatsApp Business API para usar?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O envio é feito pela WhatsApp Cloud API, a versão oficial da Meta para empresas. A conexão é configurada uma vez na área de administração (número, token e modelo de mensagem aprovado). Não funciona com o WhatsApp comum do celular.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O paciente consegue reagendar pela mensagem?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. A mensagem tem dois botões: confirmar e cancelar. Se o paciente cancela, o horário fica livre e a recepção remarca pelo sistema ou combina um novo horário com o paciente. Escolher outro horário pelo próprio WhatsApp não faz parte do recurso.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Quantas mensagens posso enviar no teste grátis?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Durante o teste, sem uma assinatura ativa, o envio é limitado a 3 disparos por clínica — o suficiente para ver o fluxo completo funcionando. Com o plano ativo, o limite acompanha o seu volume de agendamentos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Isso é um chatbot de atendimento?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. O recurso serve para confirmar e lembrar consultas agendadas. Ele não responde dúvidas livres, não faz triagem e não conduz conversa aberta — o paciente confirma, cancela ou recebe a mensagem de texto automática.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Funciona para consultório com um profissional só?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Solo (R$ 79/mês) já inclui a confirmação e o lembrete por WhatsApp, com 1 profissional e 2 colaboradores. A recepção recebe os avisos de resposta mesmo em operação pequena.</div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Leia também</div>
        <h2>Guias práticos de confirmação e lembrete</h2>
        <p class="section-sub">Modelos de mensagem prontos e o que fazer para diminuir as faltas.</p>
        <div class="features-grid">
            <a class="feature-card" href="<?=base_url()?>blog/modelo-de-mensagem-de-confirmacao-de-consulta-whatsapp">
                <div class="feature-icon">💬</div>
                <h3>Modelos de mensagem de confirmação para WhatsApp</h3>
                <p>Textos prontos para copiar, por tipo de consulta.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/mensagem-de-lembrete-de-consulta-quando-enviar">
                <div class="feature-icon">⏰</div>
                <h3>Mensagem de lembrete: modelos e quando enviar</h3>
                <p>As melhores janelas (véspera e manhã do dia) e os erros comuns.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/confirmacao-de-consulta-manual-ou-automatica">
                <div class="feature-icon">⚖️</div>
                <h3>Confirmação manual ou automática?</h3>
                <p>Quando o volume de agendamentos justifica automatizar.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/como-reduzir-faltas-de-pacientes-no-consultorio">
                <div class="feature-icon">📉</div>
                <h3>Como reduzir faltas de pacientes</h3>
                <p>Sete práticas para a agenda não ter buraco de última hora.</p>
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Menos faltas, agenda sempre confirmada</h2>
            <p class="cta-sub">30 dias grátis para testar a confirmação e o lembrete por WhatsApp na sua clínica.<br>Sem cartão de crédito. Começa em minutos.</p>
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
                <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
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
  "url": "https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp",
  "description": "Confirmação e lembrete de consulta por WhatsApp para clínicas e consultórios, com resposta do paciente por botão e atualização automática da agenda.",
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
    {"@type": "ListItem", "position": 3, "name": "Confirmação de Consulta por WhatsApp", "item": "https://utecnologia.com.br/confirmacao-de-consulta-por-whatsapp"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Preciso do WhatsApp Business API para usar?", "acceptedAnswer": {"@type": "Answer", "text": "O envio é feito pela WhatsApp Cloud API, a versão oficial da Meta para empresas. A conexão é configurada uma vez na área de administração e não funciona com o WhatsApp comum do celular."}},
    {"@type": "Question", "name": "O paciente consegue reagendar pela mensagem?", "acceptedAnswer": {"@type": "Answer", "text": "Não. A mensagem tem dois botões: confirmar e cancelar. Se o paciente cancela, o horário fica livre e a recepção remarca pelo sistema. Escolher outro horário pelo próprio WhatsApp não faz parte do recurso."}},
    {"@type": "Question", "name": "Quantas mensagens posso enviar no teste grátis?", "acceptedAnswer": {"@type": "Answer", "text": "Durante o teste, sem assinatura ativa, o envio é limitado a 3 disparos por clínica. Com o plano ativo, o limite acompanha o volume de agendamentos."}},
    {"@type": "Question", "name": "Isso é um chatbot de atendimento?", "acceptedAnswer": {"@type": "Answer", "text": "Não. O recurso confirma e lembra consultas agendadas. Não responde dúvidas livres, não faz triagem e não conduz conversa aberta."}},
    {"@type": "Question", "name": "Funciona para consultório com um profissional só?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo (R$ 79/mês) já inclui a confirmação e o lembrete por WhatsApp, com 1 profissional e 2 colaboradores."}}
  ]
}
</script>
</body>
</html>
