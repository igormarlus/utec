<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contato — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fale com a equipe UTecnologia Saúde por WhatsApp ou e-mail. Atendimento para clínicas e profissionais de saúde em todo o Brasil.">
    <link rel="canonical" href="https://utecnologia.com.br/contato">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/contato">
    <meta property="og:title" content="Contato — UTecnologia Saúde">
    <meta property="og:description" content="Fale com a equipe por WhatsApp ou e-mail. Atendemos clínicas e profissionais de saúde em todo o Brasil.">
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
        .lead{font-size:18px;color:var(--muted);line-height:1.75;}

        .contact-section{padding:56px 0;}
        .contact-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:48px;}
        .contact-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:32px;text-align:center;}
        .contact-icon{font-size:36px;margin-bottom:16px;}
        .contact-card h2{font-size:17px;font-weight:700;margin-bottom:10px;}
        .contact-card p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:20px;}
        .btn-wa{display:inline-flex;align-items:center;gap:8px;background:#25d366;color:#fff;padding:12px 24px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;}
        .btn-email{display:inline-block;background:var(--primary);color:#fff;padding:12px 24px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;}
        .btn-trial{display:inline-block;background:var(--primary);color:#fff;padding:12px 24px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;}

        .faq-section{background:#fff;border-top:1px solid var(--border);padding:56px 0;}
        .faq-section h2{font-size:24px;font-weight:800;margin-bottom:8px;text-align:center;}
        .faq-sub{font-size:16px;color:var(--muted);text-align:center;margin-bottom:36px;}
        .faq-list{display:flex;flex-direction:column;gap:12px;}
        .faq-item{background:var(--paper);border:1px solid var(--border);border-radius:var(--radius);padding:20px 24px;}
        .faq-q{font-size:15px;font-weight:700;margin-bottom:6px;}
        .faq-a{font-size:14px;color:var(--muted);line-height:1.7;}

        .footer{background:var(--ink);color:rgba(255,255,255,.6);padding:28px 0;}
        .footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
        .footer-inner a{color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;margin-left:16px;}
        .footer-brand{font-size:14px;font-weight:700;color:#fff;}

        @media(max-width:700px){.contact-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap-wide">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>sobre">Sobre</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="page-hero">
    <div class="wrap">
        <div class="eyebrow">Fale conosco</div>
        <h1>Contato</h1>
        <p class="lead">Nossa equipe atende clínicas e profissionais de saúde em todo o Brasil. Escolha o canal que preferir.</p>
    </div>
</section>

<section class="contact-section">
    <div class="wrap">
        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">💬</div>
                <h2>WhatsApp</h2>
                <p>Atendimento rápido para dúvidas sobre o sistema, planos, suporte técnico e qualquer questão sobre sua conta.</p>
                <a class="btn-wa" href="https://wa.me/5581983276882?text=Ol%C3%A1%2C%20gostaria%20de%20conhecer%20o%20UTecnologia%20Sa%C3%BAde" target="_blank" rel="noopener">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    Chamar no WhatsApp
                </a>
            </div>
            <div class="contact-card">
                <div class="contact-icon">✉️</div>
                <h2>E-mail</h2>
                <p>Para questões detalhadas, envie um e-mail. Respondemos em até 1 dia útil com atenção total à sua situação.</p>
                <a class="btn-email" href="mailto:suporte@utecnologia.com.br">suporte@utecnologia.com.br</a>
            </div>
            <div class="contact-card">
                <div class="contact-icon">🚀</div>
                <h2>Trial gratuito</h2>
                <p>A forma mais rápida de avaliar o sistema é criar sua conta e testar por 30 dias, sem cartão de crédito.</p>
                <a class="btn-trial" href="<?=base_url()?>experimentar">Experimentar grátis →</a>
            </div>
        </div>
    </div>
</section>

<section class="faq-section">
    <div class="wrap">
        <h2>Perguntas frequentes de suporte</h2>
        <p class="faq-sub">As dúvidas mais comuns antes e durante o uso do sistema.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">Como crio minha conta para testar o sistema?</div>
                <div class="faq-a">Acesse <a href="<?=base_url()?>experimentar">utecnologia.com.br/experimentar</a>, preencha o nome da sua clínica ou consultório e o e-mail. O acesso é imediato, sem cartão de crédito e sem burocracia.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Esqueci minha senha. Como recupero?</div>
                <div class="faq-a">Entre em contato pelo WhatsApp ou e-mail informando o e-mail de acesso. Nossa equipe gera um link de redefinição de senha em minutos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso migrar dados de outro sistema?</div>
                <div class="faq-a">Sim. Entre em contato pelo WhatsApp para orientação sobre o processo de migração. Durante os 30 dias de trial você pode cadastrar seus pacientes e avaliar o fluxo antes de migrar definitivamente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como funciona o suporte técnico?</div>
                <div class="faq-a">O suporte é prestado por WhatsApp e e-mail. Para dúvidas rápidas de uso do sistema, o WhatsApp é o canal mais ágil. Para relatos de problemas técnicos ou solicitações de configuração, envie um e-mail com detalhes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Em quais regiões do Brasil o sistema está disponível?</div>
                <div class="faq-a">O UTecnologia Saúde é 100% online e atende clínicas e profissionais de saúde em todo o Brasil. Não há restrição geográfica — qualquer consultório ou clínica com acesso à internet pode usar o sistema.</div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap-wide">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sobre">Sobre</a>
                <a href="<?=base_url()?>politica-de-privacidade">Privacidade</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "url": "https://utecnologia.com.br/contato",
  "name": "Contato — UTecnologia Saúde",
  "description": "Fale com a equipe UTecnologia Saúde por WhatsApp ou e-mail.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Contato", "item": "https://utecnologia.com.br/contato"}
    ]
  }
}
</script>
<script>
(function () {
    var TRACK = '<?=base_url()?>e/track';
    function ping(t, o) {
        try {
            var url = TRACK + '?t=' + encodeURIComponent(t) + (o ? '&o=' + encodeURIComponent(o) : '');
            if (navigator.sendBeacon) { navigator.sendBeacon(url); }
            else { (new Image()).src = url; }
        } catch (e) {}
    }
    document.addEventListener('click', function (ev) {
        var a = ev.target.closest ? ev.target.closest('a[href*="wa.me"], a[href*="api.whatsapp.com"]') : null;
        if (a) { ping('whatsapp', (a.className || '').split(' ')[0] || 'link'); }
    }, true);
    var form = document.querySelector('form[data-track="contato"]');
    if (form) { form.addEventListener('submit', function () { ping('contato', 'form'); }); }
})();
</script>
</body>
</html>

