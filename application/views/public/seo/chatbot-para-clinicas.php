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
    <title>Chatbot para Clínicas no WhatsApp — Integrado à Agenda | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chatbot de WhatsApp para clínicas médicas, odontológicas e consultórios: o paciente vê as próximas consultas, remarca e cancela sozinho, e a agenda atualiza na hora. API oficial da Meta. Teste grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/chatbot-para-clinicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/chatbot-para-clinicas">
    <meta property="og:title" content="Chatbot para Clínicas no WhatsApp — UTecnologia Saúde">
    <meta property="og:description" content="Paciente remarca, cancela e consulta horários pelo WhatsApp, direto na agenda da clínica. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Chatbot para Clínicas no WhatsApp — UTecnologia Saúde">
    <meta name="twitter:description" content="Chatbot de WhatsApp ligado à agenda da clínica: remarcação, cancelamento e agenda do dia por perfil. 30 dias grátis.">
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
    .chat{padding:18px;background:#e5ddd5;display:flex;flex-direction:column;gap:10px;}
    .msg{border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ink);box-shadow:0 1px 1px rgba(0,0,0,.08);max-width:88%;}
    .msg-in{background:#fff;}
    .msg-out{background:#d9fdd3;margin-left:auto;}
    .msg-opts{display:flex;flex-direction:column;gap:6px;margin-top:10px;}
    .msg-opts span{text-align:center;border:1px solid var(--teal);color:var(--teal);border-radius:8px;padding:6px 0;font-weight:700;font-size:12px;}
    .funciona-strip{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;}
    .funciona-chip{font-size:12px;font-weight:600;color:var(--teal);background:var(--teal-lt);border:1px solid var(--teal-md);padding:5px 12px;border-radius:999px;}
    .funciona-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--subtle);align-self:center;}
    .dark-section{padding:80px 0;background:var(--navy);}
    .dark-header{text-align:center;margin-bottom:44px;}
    .dark-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .dark-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .dark-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:600px;margin:0 auto;}
    .profiles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
    .profile-card{background:rgba(255,255,255,.05);border:1px solid rgba(0,127,163,.4);border-radius:14px;padding:24px;}
    .profile-card .tag{font-size:12px;font-weight:700;color:var(--teal-md);letter-spacing:.1em;margin-bottom:10px;text-transform:uppercase;}
    .profile-card h3{font-family:var(--ff-display);font-size:18px;color:#fff;margin-bottom:10px;}
    .profile-card ul{list-style:none;}
    .profile-card li{font-size:14px;color:rgba(255,255,255,.72);line-height:1.6;padding-left:18px;position:relative;margin-bottom:6px;}
    .profile-card li::before{content:'›';position:absolute;left:4px;color:var(--accent);font-weight:700;}
    .section{padding:72px 0;}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);text-align:center;margin-bottom:12px;}
    h2{font-family:var(--ff-display);font-size:36px;font-weight:700;text-align:center;color:var(--ink);margin-bottom:14px;}
    .section-sub{font-size:17px;color:var(--muted);text-align:center;max-width:600px;margin:0 auto 48px;line-height:1.65;}
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .feature-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;transition:transform .2s,box-shadow .2s;}
    .feature-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);}
    .feature-icon{width:44px;height:44px;background:var(--teal-lt);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;}
    .feature-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .feature-card p{font-size:14px;color:var(--muted);line-height:1.65;}
    .compare{max-width:820px;margin:0 auto;border-collapse:collapse;width:100%;background:var(--white);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .compare th,.compare td{padding:14px 18px;font-size:14px;text-align:left;border-bottom:1px solid var(--border);vertical-align:top;}
    .compare th{background:var(--navy);color:#fff;font-weight:600;}
    .compare td:first-child{font-weight:600;color:var(--ink);width:34%;}
    .compare td{color:var(--muted);}
    .table-wrap{overflow-x:auto;}
    .summary-box{font-size:15px;color:var(--muted);line-height:1.75;background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;max-width:720px;margin:40px auto 0;}
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:10px;}
    .faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .faq-q{appearance:none;background:none;border:0;width:100%;font:inherit;font-size:15px;font-weight:600;color:var(--ink);padding:18px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;text-align:left;}
    .faq-q:hover{color:var(--teal);}
    .faq-q:focus-visible{outline:3px solid rgba(0,127,163,.35);outline-offset:-3px;}
    .faq-chevron{color:var(--subtle);font-size:18px;transition:transform .25s;flex-shrink:0;}
    .faq-item.open .faq-chevron{transform:rotate(180deg);}
    .faq-a{font-size:14px;color:var(--muted);line-height:1.7;padding:0 24px;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s;}
    .faq-item.open .faq-a{max-height:420px;padding:0 24px 18px;}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.profiles-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.cta-wrap{padding:40px 24px;}.footer-links a{margin:0 16px 0 0;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">Confirmação por WhatsApp</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Chatbot de WhatsApp para Clínicas</div>
                <h1>Chatbot para clínicas que conversa com a <em>sua agenda</em></h1>
                <p class="hero-text">
                    O paciente manda uma mensagem no WhatsApp da clínica e o chatbot mostra as
                    próximas consultas dele. Com 24 horas ou mais de antecedência, ele remarca
                    escolhendo um horário livre do mesmo profissional, ou cancela — e a agenda
                    da clínica muda na hora, sem a recepção digitar nada.
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
                    <span class="funciona-chip">Clínica médica</span>
                    <span class="funciona-chip">Clínica odontológica</span>
                    <span class="funciona-chip">Consultório</span>
                    <span class="funciona-chip">Psicologia e Fisioterapia</span>
                </div>
            </div>
            <div class="hero-card" aria-label="Exemplo de conversa com o chatbot">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">WhatsApp · Chatbot da clínica</span>
                </div>
                <div class="chat">
                    <div class="msg msg-out">Oi, preciso mudar minha consulta</div>
                    <div class="msg msg-in">
                        👋 Olá! Escolha uma opção abaixo.
                        <div class="msg-opts">
                            <span>📅 Próximas consultas</span>
                            <span>🔄 Remarcar consulta</span>
                            <span>❌ Cancelar consulta</span>
                        </div>
                    </div>
                    <div class="msg msg-out">🔄 Remarcar consulta</div>
                    <div class="msg msg-in">
                        Dias com horário livre com a Dra. Ana:
                        <div class="msg-opts">
                            <span>Seg, 16/09</span>
                            <span>Ter, 17/09</span>
                        </div>
                    </div>
                    <div class="msg msg-out">Seg, 16/09 · 10h30</div>
                    <div class="msg msg-in">Pronto! Consulta remarcada para <strong>segunda, 16/09, às 10h30</strong>. ✅</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="dark-section">
    <div class="wrap">
        <div class="dark-header">
            <div class="dark-label">Um chatbot, três perfis</div>
            <h2>Cada pessoa vê só o que pode fazer</h2>
            <p>O chatbot reconhece o telefone cadastrado no sistema e abre o menu do perfil daquela pessoa. Ninguém precisa de senha nem de aplicativo.</p>
        </div>
        <div class="profiles-grid">
            <div class="profile-card">
                <div class="tag">Paciente</div>
                <h3>Resolve sem ligar para a clínica</h3>
                <ul>
                    <li>Vê as próximas consultas</li>
                    <li>Remarca entre os horários livres do mesmo profissional (24h ou mais antes)</li>
                    <li>Cancela, com motivo opcional</li>
                    <li>Confirma pelo botão da mensagem de confirmação ou lembrete</li>
                </ul>
            </div>
            <div class="profile-card">
                <div class="tag">Profissional e atendente</div>
                <h3>Agenda do dia no bolso</h3>
                <ul>
                    <li>Consulta a agenda de hoje e de amanhã</li>
                    <li>Vê pendências do seu perfil</li>
                    <li>Recebe aviso quando um paciente confirma, cancela ou remarca</li>
                </ul>
            </div>
            <div class="profile-card">
                <div class="tag">Administrador da clínica</div>
                <h3>Visão da operação</h3>
                <ul>
                    <li>Agenda, pendências e cancelamentos dentro das permissões</li>
                    <li>Informações do plano</li>
                    <li>Acesso ao suporte</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>Por que um chatbot ligado ao sistema da clínica</h2>
        <p class="section-sub">Um chatbot solto só responde perguntas. Aqui, cada resposta do paciente vira uma mudança real na agenda.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Horários reais, não sugestões</h3>
                <p>A remarcação usa a grade de atendimento e os bloqueios do profissional. O chatbot só oferece horário que está livre de verdade.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Checagem na hora de gravar</h3>
                <p>Se outra pessoa pegar o horário enquanto o paciente escolhe, o sistema percebe na confirmação e pede outra opção — sem encaixe duplo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>Equipe sempre avisada</h3>
                <p>Remarcação e cancelamento pelo chatbot geram aviso no sino do sistema para quem marcou a consulta e para o profissional.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3>Menu por perfil</h3>
                <p>Paciente, profissional, atendente e administrador recebem opções diferentes. Número repetido em dois cadastros é bloqueado por segurança.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <h3>Junto com confirmação e lembrete</h3>
                <p>O mesmo número envia a <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">confirmação e o lembrete de consulta</a>, com botões de confirmar e cancelar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🟢</div>
                <h3>API oficial da Meta</h3>
                <p>Funciona pela WhatsApp Cloud API, com número verificado da clínica. Não é automação de celular nem extensão de navegador.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">O que é automático e o que não é</div>
        <h2>Limites claros, sem surpresa</h2>
        <p class="section-sub">O chatbot resolve o que é objetivo. O resto continua com a sua equipe — e o paciente é avisado disso.</p>
        <div class="table-wrap">
            <table class="compare">
                <thead>
                    <tr><th>Situação</th><th>O que acontece</th></tr>
                </thead>
                <tbody>
                    <tr><td>Remarcar com 24h ou mais</td><td>Automático: o paciente escolhe dia e horário livres e confirma.</td></tr>
                    <tr><td>Cancelar com 24h ou mais</td><td>Automático: o horário volta a ficar livre na agenda.</td></tr>
                    <tr><td>Pedido com menos de 24h</td><td>O paciente informa o motivo e a equipe decide.</td></tr>
                    <tr><td>Profissional sem horários cadastrados</td><td>Vira solicitação para a equipe definir o novo horário.</td></tr>
                    <tr><td>Marcar a primeira consulta</td><td>Ainda feito pela equipe no sistema.</td></tr>
                    <tr><td>Dúvidas fora do menu</td><td>Atendimento humano, como hoje.</td></tr>
                </tbody>
            </table>
        </div>
        <p class="summary-box">
            <strong>Em resumo:</strong> o chatbot do UTecnologia Saúde é um atendimento por menus no WhatsApp,
            ligado à agenda da clínica. Ele mostra consultas, remarca e cancela quando há 24 horas ou mais
            de antecedência e dá à equipe a agenda do dia pelo celular. Não é uma inteligência artificial
            que conversa livremente: as opções são fixas, o que deixa as respostas previsíveis para o
            paciente e para a clínica.
        </p>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o chatbot para clínicas</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <button class="faq-q" type="button" aria-expanded="true" aria-controls="faq-a-ia">
                    O chatbot usa inteligência artificial?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-ia">Não. Ele funciona por menus e botões, com opções fixas por perfil. Isso evita respostas inventadas sobre horários ou orientações clínicas. Mensagens fora do menu levam o paciente de volta às opções disponíveis ou ao atendimento humano.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-marcar">
                    O paciente consegue marcar uma consulta nova pelo chatbot?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-marcar" hidden>Ainda não. Hoje o chatbot mostra, remarca e cancela consultas que já existem. A primeira marcação continua sendo feita pela equipe no sistema — e, ao marcar, a clínica pode enviar a <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">confirmação por WhatsApp</a> na hora.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-api">
                    Funciona com o WhatsApp comum ou o WhatsApp Business do celular?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-api" hidden>Não. O chatbot usa a WhatsApp Cloud API, a versão oficial da Meta para empresas, configurada uma vez na área de administração. O número usado na API não fica disponível no aplicativo comum ao mesmo tempo.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-odonto">
                    Serve para clínica odontológica?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-odonto" hidden>Sim. O fluxo é o mesmo para clínicas médicas, odontológicas, consultórios e profissionais de psicologia, fisioterapia e nutrição: ele trabalha sobre a agenda e a grade de cada profissional, não sobre a especialidade. Veja também o <a href="<?=base_url()?>sistema-para-dentistas">sistema para dentistas</a>.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-paciente">
                    Como o chatbot sabe quem é o paciente?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-paciente" hidden>Pelo telefone cadastrado no sistema, inclusive com ou sem o nono dígito. Se o mesmo número estiver em mais de um cadastro, o acesso é recusado por segurança até a clínica corrigir o cadastro. Número desconhecido não vê dados de ninguém.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-recepcao">
                    O chatbot substitui a recepção?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-recepcao" hidden>Não. Ele tira da recepção as tarefas repetitivas — remarcar, cancelar, informar o horário da próxima consulta. Casos com menos de 24 horas, dúvidas e situações que precisam de análise continuam com a equipe.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-plano">
                    Está incluído em qual plano?
                    <span class="faq-chevron">▾</span>
                </button>
                <div class="faq-a" id="faq-a-plano" hidden>O WhatsApp faz parte do sistema a partir do plano Solo (R$ 79/mês). No teste grátis de 30 dias, sem assinatura ativa, o envio de mensagens de confirmação e lembrete é limitado a 3 disparos por clínica.</div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Leia também</div>
        <h2>Guias sobre WhatsApp na clínica</h2>
        <p class="section-sub">O que dá para automatizar, o que a LGPD exige e como o paciente remarca.</p>
        <div class="features-grid">
            <a class="feature-card" href="<?=base_url()?>blog/chatbot-para-clinica-whatsapp-o-que-faz-e-o-que-nao-faz">
                <div class="feature-icon">🤖</div>
                <h3>Chatbot para clínica: o que faz e o que não faz</h3>
                <p>Expectativas realistas antes de colocar um robô no WhatsApp.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/paciente-remarcar-consulta-pelo-whatsapp">
                <div class="feature-icon">🔁</div>
                <h3>Paciente remarcando pelo WhatsApp</h3>
                <p>Como funciona a remarcação sem passar pela recepção.</p>
            </a>
            <a class="feature-card" href="<?=base_url()?>blog/enviar-mensagem-para-paciente-whatsapp-lgpd">
                <div class="feature-icon">⚖️</div>
                <h3>Mensagem para paciente e LGPD</h3>
                <p>O que observar ao falar com pacientes pelo WhatsApp.</p>
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Menos ligação, agenda sempre em dia</h2>
            <p class="cta-sub">30 dias grátis para testar o chatbot e a confirmação por WhatsApp na sua clínica.<br>Sem cartão de crédito.</p>
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
                <a href="<?=base_url()?>confirmacao-de-consulta-por-whatsapp">Confirmação por WhatsApp</a>
                <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
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
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/chatbot-para-clinicas",
  "description": "Chatbot de WhatsApp para clínicas e consultórios, ligado à agenda: o paciente vê as próximas consultas, remarca e cancela com 24 horas ou mais de antecedência, e a equipe consulta a agenda do dia por perfil.",
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
    {"@type": "ListItem", "position": 3, "name": "Chatbot para Clínicas", "item": "https://utecnologia.com.br/chatbot-para-clinicas"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O chatbot usa inteligência artificial?", "acceptedAnswer": {"@type": "Answer", "text": "Não. Ele funciona por menus e botões, com opções fixas por perfil. Isso evita respostas inventadas sobre horários ou orientações clínicas. Mensagens fora do menu levam o paciente de volta às opções disponíveis ou ao atendimento humano."}},
    {"@type": "Question", "name": "O paciente consegue marcar uma consulta nova pelo chatbot?", "acceptedAnswer": {"@type": "Answer", "text": "Ainda não. Hoje o chatbot mostra, remarca e cancela consultas que já existem. A primeira marcação continua sendo feita pela equipe no sistema — e, ao marcar, a clínica pode enviar a confirmação por WhatsApp na hora."}},
    {"@type": "Question", "name": "Funciona com o WhatsApp comum ou o WhatsApp Business do celular?", "acceptedAnswer": {"@type": "Answer", "text": "Não. O chatbot usa a WhatsApp Cloud API, a versão oficial da Meta para empresas, configurada uma vez na área de administração. O número usado na API não fica disponível no aplicativo comum ao mesmo tempo."}},
    {"@type": "Question", "name": "Serve para clínica odontológica?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O fluxo é o mesmo para clínicas médicas, odontológicas, consultórios e profissionais de psicologia, fisioterapia e nutrição: ele trabalha sobre a agenda e a grade de cada profissional, não sobre a especialidade."}},
    {"@type": "Question", "name": "Como o chatbot sabe quem é o paciente?", "acceptedAnswer": {"@type": "Answer", "text": "Pelo telefone cadastrado no sistema, inclusive com ou sem o nono dígito. Se o mesmo número estiver em mais de um cadastro, o acesso é recusado por segurança até a clínica corrigir o cadastro. Número desconhecido não vê dados de ninguém."}},
    {"@type": "Question", "name": "O chatbot substitui a recepção?", "acceptedAnswer": {"@type": "Answer", "text": "Não. Ele tira da recepção as tarefas repetitivas — remarcar, cancelar, informar o horário da próxima consulta. Casos com menos de 24 horas, dúvidas e situações que precisam de análise continuam com a equipe."}},
    {"@type": "Question", "name": "Está incluído em qual plano?", "acceptedAnswer": {"@type": "Answer", "text": "O WhatsApp faz parte do sistema a partir do plano Solo (R$ 79/mês). No teste grátis de 30 dias, sem assinatura ativa, o envio de mensagens de confirmação e lembrete é limitado a 3 disparos por clínica."}}
  ]
}
</script>
</body>
</html>
