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
    <title>Casos de Uso: como clínicas usam WhatsApp, agenda e prontuário | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cenários de uso do UTecnologia Saúde em clínicas e consultórios: confirmação e lembrete por WhatsApp, remarcação automática, agenda do dia pelo chatbot e rotina da recepção. Cenários ilustrativos.">
    <link rel="canonical" href="https://utecnologia.com.br/casos-de-uso">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/casos-de-uso">
    <meta property="og:title" content="Casos de uso — UTecnologia Saúde">
    <meta property="og:description" content="Como clínicas e consultórios usam WhatsApp, agenda e prontuário no dia a dia. Cenários ilustrativos, teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Casos de uso — UTecnologia Saúde">
    <meta name="twitter:description" content="Cenários de uso: confirmação, remarcação automática e agenda pelo WhatsApp.">
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
    .chatbot-section{padding:84px 0;background:linear-gradient(135deg,#e0f4f8 0%,#f5f8fb 58%,#fff 100%);border-top:1px solid var(--teal-md);border-bottom:1px solid var(--border);}
    .chatbot-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(360px,.95fr);gap:48px;align-items:center;}
    .chatbot-copy h2{max-width:580px;line-height:1.12;}
    .chatbot-copy h2 em{color:var(--teal);font-style:italic;}
    .chatbot-copy > p:not(.section-label):not(.chatbot-note){font-size:17px;color:var(--muted);line-height:1.75;margin:0 0 24px;}
    .chatbot-profiles{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:28px 0;}
    .chatbot-profile-card{background:rgba(255,255,255,.86);border:1px solid var(--border);border-radius:12px;padding:16px 14px;box-shadow:0 8px 22px rgba(10,37,64,.06);}
    .chatbot-profile-card strong{display:block;font-size:13px;color:var(--navy);margin-bottom:6px;}
    .chatbot-profile-card span{display:block;font-size:12px;color:var(--muted);line-height:1.5;}
    .chatbot-note{font-size:13px!important;color:var(--muted)!important;border-left:3px solid var(--green);padding:10px 0 10px 14px;margin:20px 0 0!important;}
    .chatbot-flow{background:var(--white);border:1px solid var(--border);border-radius:20px;padding:14px;box-shadow:var(--shadow-lg);}
    .chatbot-flow img{width:100%;height:auto;display:block;border-radius:10px;}
    .chatbot-links{grid-column:1 / -1;display:flex;gap:18px;flex-wrap:wrap;padding-top:2px;}
    .chatbot-links a{font-size:14px;font-weight:700;color:var(--teal);}
    .chatbot-links a:hover{text-decoration:underline;}
    .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:0 auto;}
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:10px;}
    .faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .faq-q{appearance:none;background:none;border:0;width:100%;font:inherit;font-size:15px;font-weight:600;color:var(--ink);padding:18px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;text-align:left;}
    .faq-q:hover{color:var(--teal);}
    .faq-q:focus-visible{outline:3px solid rgba(0,127,163,.35);outline-offset:-3px;}
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
    @media(max-width:900px){.hero-inner,.chatbot-grid{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.steps-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}.chatbot-copy h2{text-align:left;}.chatbot-flow{max-width:680px;}}
    @media(max-width:600px){.features-grid,.chatbot-profiles{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}.chatbot-section{padding:60px 0;}.chatbot-links{gap:12px;flex-direction:column;}}
        .caso{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:32px;margin-bottom:24px;}
    .caso-tag{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--teal);background:var(--teal-lt);border-radius:999px;padding:4px 12px;margin-bottom:12px;}
    .caso h2{font-family:var(--ff-display);font-size:26px;line-height:1.2;margin-bottom:10px;text-align:left;}
    .caso > p{font-size:15px;color:var(--muted);line-height:1.75;margin-bottom:14px;}
    .caso-cols{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:8px;}
    .caso h3{font-size:14px;color:var(--navy);margin:0 0 8px;}
    .caso ul,.caso ol{padding-left:20px;font-size:14px;color:var(--muted);line-height:1.7;}
    .aviso-ilustrativo{max-width:760px;margin:0 auto 40px;background:#fff8e6;border:1px solid #f0d9a0;border-radius:12px;padding:14px 18px;font-size:14px;color:#6b5514;line-height:1.6;}
    @media(max-width:700px){.caso-cols{grid-template-columns:1fr;}.caso{padding:22px;}}
</style>
</head>

<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">WhatsApp</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap" style="max-width:820px;text-align:center;">
        <p class="eyebrow">Casos de uso</p>
        <h1>Como clínicas e consultórios usam o <em>UTecnologia Saúde</em></h1>
        <p class="hero-text">Quatro situações do dia a dia da recepção e do profissional, com as ferramentas do sistema que resolvem cada uma. Sem promessa de resultado: só o que o sistema faz hoje e o que continua com a equipe.</p>
        <div class="hero-cta" style="justify-content:center;">
            <a class="btn-primary" href="<?=base_url()?>experimentar">Testar grátis por 30 dias</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap" style="max-width:920px;">
        <p class="aviso-ilustrativo"><strong>Cenários ilustrativos.</strong> Os casos abaixo não são depoimentos nem representam clientes reais; descrevem como as ferramentas são usadas em situações comuns. Casos reais, com autorização dos clientes, serão publicados quando existirem.</p>

        <article class="caso">
            <span class="caso-tag">Clínica de fisioterapia</span>
            <h2>Confirmação e lembrete automáticos</h2>
            <p><strong>Situação:</strong> Uma clínica de fisioterapia com três profissionais e uma recepcionista, com sessões em série e agenda cheia. Cada horário vago por falta é uma sessão perdida.</p>
            <div class="caso-cols">
                <div>
                    <h3>Ferramentas do sistema usadas</h3>
                    <ul><li>Confirmação no agendamento (mensagem com botões Confirmar e Cancelar)</li><li>Lembrete automático poucas horas antes, só para quem ainda não respondeu</li><li>Etiqueta "Confirmado/Cancelado via WhatsApp" na agenda</li><li>Aviso no sino para a recepção e o profissional</li></ul>
                </div>
                <div>
                    <h3>Como acontece</h3>
                    <ol><li>A recepção agenda a sessão com a opção de WhatsApp marcada.</li><li>O paciente recebe a mensagem na hora e toca em Confirmar ou Cancelar.</li><li>Se não responde, recebe um lembrete antes do horário.</li><li>A agenda mostra o status de cada sessão sem ninguém ligar.</li></ol>
                </div>
            </div>
            <h3 style="margin-top:16px;">O que continua com a equipe</h3>
            <ul><li>Ligar para quem não respondeu e decidir o que fazer com o horário.</li><li>Registrar o atendimento no prontuário.</li></ul>
            <p style="margin:14px 0 0;"><a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp" style="font-weight:700;">Ver confirmação e lembrete por WhatsApp →</a></p>
        </article>

        <article class="caso">
            <span class="caso-tag">Consultório com recepção pequena</span>
            <h2>Paciente remarca e cancela sozinho</h2>
            <p><strong>Situação:</strong> Um consultório médico com uma recepcionista que divide o tempo entre atender o telefone e receber pacientes. Boa parte das ligações é para remarcar ou cancelar.</p>
            <div class="caso-cols">
                <div>
                    <h3>Ferramentas do sistema usadas</h3>
                    <ul><li>Chatbot no WhatsApp: menu "Remarcar" e "Cancelar"</li><li>Horários de atendimento do profissional cadastrados no sistema</li><li>Regra de 24 horas de antecedência</li><li>Avisos à equipe e ao profissional quando o paciente remarca ou cancela</li></ul>
                </div>
                <div>
                    <h3>Como acontece</h3>
                    <ol><li>O paciente abre o menu do chatbot no WhatsApp com o telefone cadastrado.</li><li>Escolhe a consulta e vê os dias e horários livres do mesmo profissional.</li><li>Escolhe, confirma e a agenda é atualizada na hora.</li><li>A equipe recebe o aviso; o horário liberado fica disponível.</li></ol>
                </div>
            </div>
            <h3 style="margin-top:16px;">O que continua com a equipe</h3>
            <ul><li>Pedidos com menos de 24 horas, ou de profissional sem horários cadastrados: o paciente informa o motivo e a equipe decide.</li><li>Marcar uma consulta nova do zero continua sendo feito pela recepção.</li></ul>
            <p style="margin:14px 0 0;"><a href="<?=base_url()?>blog/paciente-remarcar-consulta-pelo-whatsapp" style="font-weight:700;">Ler como o paciente remarca pelo WhatsApp →</a></p>
        </article>

        <article class="caso">
            <span class="caso-tag">Profissional autônomo</span>
            <h2>Agenda do dia no WhatsApp</h2>
            <p><strong>Situação:</strong> Um profissional que atende sozinho, entre um paciente e outro, sem computador por perto, e quer saber quem é o próximo e quem ainda não confirmou.</p>
            <div class="caso-cols">
                <div>
                    <h3>Ferramentas do sistema usadas</h3>
                    <ul><li>Chatbot por perfil (profissional)</li><li>Opções: agenda de hoje, agenda de amanhã e pendências</li><li>Aviso quando um paciente cancela</li></ul>
                </div>
                <div>
                    <h3>Como acontece</h3>
                    <ol><li>O profissional envia uma mensagem para o número da clínica.</li><li>O sistema reconhece o telefone cadastrado e mostra o menu do perfil.</li><li>Ele escolhe "Agenda de hoje" e recebe os horários com o status de cada consulta.</li></ol>
                </div>
            </div>
            <h3 style="margin-top:16px;">O que continua com a equipe</h3>
            <ul><li>Alterações na agenda e o registro do prontuário continuam sendo feitos no sistema.</li></ul>
            <p style="margin:14px 0 0;"><a href="<?=base_url()?>blog/agenda-do-dia-pelo-whatsapp-para-medicos-e-clinicas" style="font-weight:700;">Ler como consultar a agenda pelo WhatsApp →</a></p>
        </article>

        <article class="caso">
            <span class="caso-tag">Clínica odontológica</span>
            <h2>Recepção acompanha pendências e cancelamentos</h2>
            <p><strong>Situação:</strong> Uma clínica odontológica com vários dentistas, em que a recepção precisa saber rápido quais consultas ainda estão sem confirmação e o que foi cancelado, para reencaixar pacientes.</p>
            <div class="caso-cols">
                <div>
                    <h3>Ferramentas do sistema usadas</h3>
                    <ul><li>Perfil atendente no chatbot (agenda, amanhã e pendências)</li><li>Perfil administrador (cancelamentos)</li><li>Etiquetas de WhatsApp na agenda</li><li>Confirmação e lembrete automáticos</li></ul>
                </div>
                <div>
                    <h3>Como acontece</h3>
                    <ol><li>Os pacientes confirmam ou cancelam pelos botões da mensagem.</li><li>A recepção consulta as pendências de amanhã pelo WhatsApp e prioriza os contatos.</li><li>Quando alguém cancela, o administrador vê na lista de cancelamentos e a agenda libera o horário.</li></ol>
                </div>
            </div>
            <h3 style="margin-top:16px;">O que continua com a equipe</h3>
            <ul><li>Escolher quem reencaixar e negociar o novo horário.</li><li>Mensagens fora do menu: continuam com o atendimento humano.</li></ul>
            <p style="margin:14px 0 0;"><a href="<?=base_url()?>blog/mensagem-de-confirmacao-de-consulta-odontologica" style="font-weight:700;">Ver modelos de mensagem para consultório odontológico →</a></p>
        </article>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades relacionadas</div>
        <?php $this->load->view('public/partials/funcionalidades', array('func_formato' => 'grade', 'func_ids' => array('whatsapp_confirmacao', 'chatbot', 'tempo_espera', 'rotulos', 'exportar_prontuario'), 'func_agrupar' => false, 'func_titulo' => '')); ?>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <p class="section-label">Perguntas frequentes</p>
        <h2 style="text-align:center;margin-bottom:28px;">Sobre os casos de uso</h2>
        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-0">
                    Esses casos são de clientes reais?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-0" hidden>Não. São cenários ilustrativos que mostram como as ferramentas funcionam em situações comuns de clínicas. Não representam clientes nem trazem resultados medidos. Quando tivermos casos reais autorizados pelos clientes, eles serão publicados separadamente.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-1">
                    As ferramentas descritas já existem no sistema?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-1" hidden>Sim. Confirmação e lembrete por WhatsApp, remarcação e cancelamento pelo chatbot (com 24 horas ou mais de antecedência), agenda de hoje e de amanhã por perfil e avisos à equipe estão em produção.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-2">
                    Preciso configurar algo para começar?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-2" hidden>É preciso cadastrar o telefone dos pacientes e da equipe, conectar o WhatsApp oficial da Meta (uma vez) e, para a remarcação automática, cadastrar os horários de atendimento de cada profissional.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Teste no seu próprio cenário</h2>
            <p>30 dias grátis, sem cartão. Veja a confirmação, a remarcação e o chatbot funcionando com a sua agenda.</p>
            <a class="btn-primary" href="<?=base_url()?>experimentar">Começar o teste grátis</a>
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
                <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">WhatsApp</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script>
document.querySelectorAll('.faq-q').forEach(function(button) {
    button.addEventListener('click', function() {
        var item = button.closest('.faq-item');
        var expanded = !item.classList.contains('open');
        var answer = document.getElementById(button.getAttribute('aria-controls'));
        item.classList.toggle('open', expanded);
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        answer.hidden = !expanded;
    });
});
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Casos de uso do UTecnologia Saúde",
  "url": "https://utecnologia.com.br/casos-de-uso",
  "description": "Cenários ilustrativos de uso do UTecnologia Saúde em clínicas e consultórios.",
  "isPartOf": {
    "@type": "WebSite",
    "name": "UTecnologia Saúde",
    "url": "https://utecnologia.com.br/"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Início",
      "item": "https://utecnologia.com.br/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Casos de uso",
      "item": "https://utecnologia.com.br/casos-de-uso"
    }
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
      "name": "Esses casos são de clientes reais?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Não. São cenários ilustrativos que mostram como as ferramentas funcionam em situações comuns de clínicas. Não representam clientes nem trazem resultados medidos. Quando tivermos casos reais autorizados pelos clientes, eles serão publicados separadamente."
      }
    },
    {
      "@type": "Question",
      "name": "As ferramentas descritas já existem no sistema?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. Confirmação e lembrete por WhatsApp, remarcação e cancelamento pelo chatbot (com 24 horas ou mais de antecedência), agenda de hoje e de amanhã por perfil e avisos à equipe estão em produção."
      }
    },
    {
      "@type": "Question",
      "name": "Preciso configurar algo para começar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "É preciso cadastrar o telefone dos pacientes e da equipe, conectar o WhatsApp oficial da Meta (uma vez) e, para a remarcação automática, cadastrar os horários de atendimento de cada profissional."
      }
    }
  ]
}
</script>
</body>
</html>
