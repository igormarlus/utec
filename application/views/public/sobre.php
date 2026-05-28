<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sobre a UTecnologia Saúde — Sistema de Gestão Clínica</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Conheça a UTecnologia Saúde: sistema SaaS de gestão clínica para médicos, psicólogos, dentistas e fisioterapeutas. Prontuário eletrônico e agenda online.">
    <link rel="canonical" href="https://utecnologia.com.br/sobre">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sobre">
    <meta property="og:title" content="Sobre a UTecnologia Saúde">
    <meta property="og:description" content="Sistema SaaS de gestão clínica para profissionais de saúde. Prontuário eletrônico, agenda e gestão de pacientes.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a;--muted:#475569;--subtle:#94a3b8;--primary:#0ea5e9;--primary-dark:#0284c7;--border:#e2e8f0;--paper:#f8fafc;--radius:16px;--shadow:0 4px 24px rgba(15,23,42,.08); }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;color:var(--ink);background:var(--paper);line-height:1.6;}
        a{color:var(--primary-dark);}
        .wrap{max-width:860px;margin:0 auto;padding:0 20px;}
        .wrap-wide{max-width:1100px;margin:0 auto;padding:0 20px;}

        .topnav{background:#fff;border-bottom:1px solid var(--border);padding:14px 0;}
        .topnav .wrap-wide{display:flex;justify-content:space-between;align-items:center;}
        .brand{font-size:17px;font-weight:800;color:var(--ink);text-decoration:none;}
        .brand span{color:var(--primary);}
        .nav-links{display:flex;gap:24px;align-items:center;}
        .nav-links a{font-size:14px;font-weight:500;color:var(--muted);text-decoration:none;}
        .btn-nav{background:var(--primary);color:#fff!important;padding:8px 18px;border-radius:999px;font-weight:700!important;font-size:13px!important;}

        .page-hero{padding:64px 0 48px;background:linear-gradient(160deg,#f0f9ff 0%,#f8fafc 60%);}
        .eyebrow{font-size:12px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--primary);margin-bottom:12px;}
        h1{font-size:38px;font-weight:800;line-height:1.15;margin-bottom:16px;}
        .lead{font-size:18px;color:var(--muted);line-height:1.75;max-width:680px;}

        .content-section{padding:56px 0;}
        .content-section h2{font-size:24px;font-weight:800;margin-bottom:16px;}
        .content-section p{font-size:15px;color:var(--muted);line-height:1.8;margin-bottom:16px;}
        .content-section ul{margin:0 0 16px 20px;display:flex;flex-direction:column;gap:8px;}
        .content-section li{font-size:15px;color:var(--muted);line-height:1.7;}

        .values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:32px;}
        .value-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;}
        .value-icon{font-size:28px;margin-bottom:12px;}
        .value-card h3{font-size:15px;font-weight:700;margin-bottom:8px;}
        .value-card p{font-size:13px;color:var(--muted);line-height:1.6;}

        .contact-strip{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:32px;margin:40px 0;display:flex;gap:40px;flex-wrap:wrap;align-items:center;}
        .contact-item{display:flex;flex-direction:column;gap:4px;}
        .contact-label{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--subtle);}
        .contact-value{font-size:15px;font-weight:600;color:var(--ink);}
        .contact-value a{color:var(--primary-dark);text-decoration:none;}

        .btn-primary{display:inline-block;background:var(--primary);color:#fff;padding:14px 28px;border-radius:999px;font-weight:700;font-size:15px;text-decoration:none;}

        .footer{background:var(--ink);color:rgba(255,255,255,.6);padding:28px 0;}
        .footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
        .footer-inner a{color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;margin-left:16px;}
        .footer-brand{font-size:14px;font-weight:700;color:#fff;}

        @media(max-width:700px){.values-grid{grid-template-columns:1fr;}.contact-strip{flex-direction:column;gap:20px;}.nav-links{display:none;}h1{font-size:28px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap-wide">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:34px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>contato">Contato</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="page-hero">
    <div class="wrap">
        <div class="eyebrow">Sobre a empresa</div>
        <h1>UTecnologia Saúde</h1>
        <p class="lead">Sistema SaaS de gestão clínica desenvolvido para simplificar a rotina de médicos, psicólogos, dentistas, fisioterapeutas e outros profissionais de saúde no Brasil.</p>
    </div>
</section>

<div class="content-section">
    <div class="wrap">
        <h2>O que é a UTecnologia Saúde</h2>
        <p>A UTecnologia Saúde é uma plataforma de gestão clínica 100% online, desenvolvida para clínicas e consultórios que precisam organizar prontuários eletrônicos, agendamentos e gestão de equipe sem depender de papéis, planilhas ou sistemas instalados localmente.</p>
        <p>O sistema funciona no modelo SaaS (Software as a Service): cada clínica ou consultório tem seu próprio ambiente isolado, acessível de qualquer dispositivo com internet, com dados seguros e disponíveis a qualquer hora.</p>

        <h2 style="margin-top:40px;">Missão</h2>
        <p>Tornar a gestão clínica acessível e eficiente para profissionais de saúde de todos os portes — do médico autônomo com consultório próprio à clínica com equipe de 20 profissionais — com um sistema simples, seguro e com preço justo.</p>

        <h2 style="margin-top:40px;">Para quem foi construído</h2>
        <p>O UTecnologia Saúde atende qualquer especialidade que utilize prontuário eletrônico, agenda de atendimentos e controle de pacientes:</p>
        <ul>
            <li>Médicos clínicos gerais e especialistas (cardiologistas, dermatologistas, neurologistas, ortopedistas, oftalmologistas, ginecologistas, pediatras e outros)</li>
            <li>Psicólogos e clínicas de psicologia</li>
            <li>Dentistas e clínicas odontológicas</li>
            <li>Fisioterapeutas e clínicas de fisioterapia</li>
            <li>Nutricionistas</li>
            <li>Fonoaudiólogos</li>
            <li>Terapeutas e outras especialidades de saúde</li>
        </ul>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🔒</div>
                <h3>Segurança e privacidade</h3>
                <p>Cada clínica opera em ambiente isolado. Os dados dos pacientes são exclusivos de cada clínica e não são compartilhados.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">💰</div>
                <h3>Preço acessível</h3>
                <p>Planos a partir de R$ 79/mês, pensados para o tamanho real de cada operação. Sem custos ocultos ou fidelidade forçada.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🌐</div>
                <h3>100% online</h3>
                <p>Sem instalação, sem servidor local, sem backup manual. Acesse do computador, tablet ou celular, de qualquer lugar.</p>
            </div>
        </div>
    </div>
</div>

<div style="background:#fff;padding:56px 0;border-top:1px solid var(--border);">
    <div class="wrap">
        <h2>Entre em contato</h2>
        <p>Tem dúvidas sobre o sistema, precisa de suporte ou quer conversar sobre o plano ideal para sua clínica? Fale com a gente pelos canais abaixo.</p>
        <div class="contact-strip">
            <div class="contact-item">
                <span class="contact-label">WhatsApp</span>
                <span class="contact-value"><a href="https://wa.me/5581983276882" target="_blank" rel="noopener">(81) 98327-6882</a></span>
            </div>
            <div class="contact-item">
                <span class="contact-label">E-mail</span>
                <span class="contact-value"><a href="mailto:suporte@utecnologia.com.br">suporte@utecnologia.com.br</a></span>
            </div>
            <div class="contact-item">
                <span class="contact-label">Atendimento</span>
                <span class="contact-value">Todo o Brasil</span>
            </div>
        </div>
        <a href="<?=base_url()?>experimentar" class="btn-primary">Testar grátis por 30 dias →</a>
    </div>
</div>

<footer class="footer">
    <div class="wrap-wide">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>contato">Contato</a>
                <a href="<?=base_url()?>politica-de-privacidade">Privacidade</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "url": "https://utecnologia.com.br/sobre",
  "name": "Sobre a UTecnologia Saúde",
  "description": "Sistema SaaS de gestão clínica para médicos, psicólogos, dentistas e fisioterapeutas no Brasil.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sobre", "item": "https://utecnologia.com.br/sobre"}
    ]
  }
}
</script>
</body>
</html>
