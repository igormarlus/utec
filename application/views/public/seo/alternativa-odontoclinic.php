<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alternativa ao Odontoclinic — Sistema para Dentistas | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Procurando substituir o Odontoclinic? Conheça o UTecnologia Saúde: prontuário odontológico, agenda e gestão de clínica. Teste grátis por 30 dias, sem cartão.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-odontoclinic">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/alternativa-odontoclinic">
    <meta property="og:title" content="Alternativa ao Odontoclinic — UTecnologia Saúde">
    <meta property="og:description" content="Procurando substituir o Odontoclinic? Prontuário odontológico, agenda e gestão de clínica. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Alternativa ao Odontoclinic — UTecnologia Saúde">
    <meta name="twitter:description" content="Substitua o Odontoclinic por um sistema moderno. Prontuário, agenda e gestão. Trial 30 dias grátis.">
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
    .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
    .btn-primary{display:inline-block;background:var(--teal);color:var(--white);padding:14px 28px;border-radius:999px;font-weight:700;font-size:15px;}
    .btn-primary:hover{background:#006d8c;color:var(--white);}
    .btn-outline{display:inline-block;border:2px solid var(--border);color:var(--muted);padding:13px 24px;border-radius:999px;font-weight:600;font-size:14px;}
    .funciona-strip{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;}
    .funciona-chip{font-size:12px;font-weight:600;color:var(--teal);background:var(--teal-lt);border:1px solid var(--teal-md);padding:5px 12px;border-radius:999px;}
    .funciona-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--subtle);align-self:center;}
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
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário — Odontologia';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    /* compare table */
    .compare-wrap{overflow-x:auto;}
    .compare-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .compare-table th{padding:16px 20px;font-size:14px;font-weight:700;}
    .compare-table th:first-child{text-align:left;background:var(--paper);}
    .compare-table th.col-utec{background:var(--teal);color:var(--white);}
    .compare-table th.col-them{background:var(--paper);color:var(--subtle);}
    .compare-table td{padding:14px 20px;font-size:14px;border-top:1px solid var(--border);}
    .compare-table td:first-child{font-weight:600;color:var(--ink);background:var(--paper);}
    .compare-table td.col-utec{text-align:center;color:var(--teal);font-weight:600;}
    .compare-table td.col-them{text-align:center;color:var(--subtle);}
    .yes{color:#16a34a!important;}
    .no{color:#dc2626!important;}
    .disclaimer{font-size:12px;color:var(--subtle);text-align:center;max-width:700px;margin:24px auto 0;line-height:1.6;}
    .disclaimer a{color:var(--teal);}
    /* features */
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .feature-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;transition:transform .2s;}
    .feature-card:hover{transform:translateY(-3px);}
    .feature-icon{width:44px;height:44px;background:var(--teal-lt);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;}
    .feature-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .feature-card p{font-size:14px;color:var(--muted);line-height:1.65;}
    /* steps */
    .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;}
    .step{text-align:center;}
    .step-num{width:48px;height:48px;background:var(--teal);color:var(--white);border-radius:999px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;margin:0 auto 16px;}
    .step h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .step p{font-size:14px;color:var(--muted);}
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:10px;}
    .faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .faq-q{font-size:15px;font-weight:600;color:var(--ink);padding:18px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;}
    .faq-q:hover{color:var(--teal);}
    .faq-chevron{color:var(--subtle);font-size:18px;transition:transform .25s;flex-shrink:0;}
    .faq-item.open .faq-chevron{transform:rotate(180deg);}
    .faq-a{font-size:14px;color:var(--muted);line-height:1.7;padding:0 24px;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s;}
    .faq-item.open .faq-a{max-height:500px;padding:0 24px 18px;}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.steps-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.fm-grid2{grid-template-columns:1fr;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-dentistas">Para Dentistas</a>
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>alternativa-feegow">vs Feegow</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Alternativa ao Odontoclinic</div>
                <h1>Procurando uma <em>alternativa ao Odontoclinic</em>?</h1>
                <p class="hero-text">
                    Muitos dentistas e clínicas odontológicas estão migrando para sistemas mais modernos.
                    O UTecnologia Saúde oferece prontuário odontológico, agenda completa e gestão de clínica
                    em um sistema 100% online, com trial gratuito de 30 dias.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>software-para-clinicas-odontologicas" class="btn-outline">Ver software odontológico</a>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">O que você encontra aqui:</span>
                    <span class="funciona-chip">Prontuário odontológico</span>
                    <span class="funciona-chip">Agenda por dentista</span>
                    <span class="funciona-chip">Radiografias e arquivos</span>
                    <span class="funciona-chip">Multi-profissionais</span>
                    <span class="funciona-chip">A partir de R$ 79/mês</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Atendimento — Odontologia</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Dente(s) Tratado(s) — FDI</label>
                        <input class="fm-input" type="text" placeholder="Ex: 36, 47" readonly>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">Procedimento</label>
                            <select class="fm-select" disabled><option>Canal Endodôntico</option></select>
                        </div>
                        <div>
                            <label class="fm-label">Anestesia</label>
                            <select class="fm-select" disabled><option>Articaína 4%</option></select>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Material Utilizado</label>
                        <input class="fm-input" type="text" placeholder="Ex: Cimento AH Plus, Guta-percha" readonly>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Prescrição / Retorno</label>
                        <input class="fm-input" type="text" placeholder="Amoxicilina 500mg. Retorno em 7 dias." readonly>
                    </div>
                    <button class="fm-btn" disabled>Salvar atendimento →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Visualização do Sistema</div>
            <h2>O prontuário odontológico na plataforma</h2>
            <p>Dente (FDI), procedimento, anestesia, material e prescrição — cada atendimento registrado no histórico do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário — Odontologia · Paulo Oliveira · 05/06/2026</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa / Motivo</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Dor intensa no dente 36 há 5 dias. Dor espontânea noturna e ao frio. Diagnóstico: pulpite irreversível.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Dente + Procedimento + Anestesia</div>
                        <div class="pmock-row col2">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Dente(s)</div>
                                <div class="pmock-field-input">36</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Procedimento</div>
                                <div class="pmock-field-select">Canal Endodôntico</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Material + Prescrição</div>
                        <div class="pmock-row col2">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Material</div>
                                <div class="pmock-field-input">ProTaper + AH Plus</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Prescrição</div>
                                <div class="pmock-field-input">Amoxicilina 500mg 8/8h</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-act">
                        <button class="btn-save">Salvar rascunho</button>
                        <button class="btn-finish">Finalizar atendimento</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Comparação</div>
        <h2>UTecnologia Saúde vs sistemas tradicionais de odontologia</h2>
        <p class="section-sub">Veja como o UTecnologia Saúde se posiciona em relação aos sistemas que você está avaliando.</p>
        <div class="compare-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Recurso</th>
                        <th class="col-utec">UTecnologia Saúde</th>
                        <th class="col-them">Sistemas tradicionais</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Prontuário odontológico</td><td class="col-utec yes">✔ Completo</td><td class="col-them">Varia por sistema</td></tr>
                    <tr><td>Agenda por profissional</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Nem sempre multi-profissional</td></tr>
                    <tr><td>Anexo de radiografias e arquivos</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Varia</td></tr>
                    <tr><td>Acesso online (sem instalação)</td><td class="col-utec yes">✔ 100% online</td><td class="col-them">Muitos exigem instalação local</td></tr>
                    <tr><td>Trial gratuito</td><td class="col-utec yes">✔ 30 dias grátis</td><td class="col-them">Raramente oferecem</td></tr>
                    <tr><td>Preço inicial</td><td class="col-utec yes">✔ R$ 79/mês</td><td class="col-them">Em geral acima de R$ 150/mês</td></tr>
                    <tr><td>Suporte em português</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Varia</td></tr>
                    <tr><td>Sem fidelidade forçada</td><td class="col-utec yes">✔ Cancela quando quiser</td><td class="col-them">Nem sempre</td></tr>
                </tbody>
            </table>
        </div>
        <p class="disclaimer">Informações baseadas em pesquisa de mercado pública. O UTecnologia Saúde não possui odontograma gráfico — ideal para clínicas que precisam de prontuário, agenda e gestão. Para odontograma interativo avançado, avalie se é indispensável no seu fluxo.</p>
        <p class="disclaimer">Se você chegou por buscas mais amplas, veja também nossa página dedicada de <a href="<?=base_url()?>software-para-clinicas-odontologicas">software para clínicas odontológicas</a>.</p>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Por que migrar agora</div>
        <h2>O que clínicas odontológicas precisam de um sistema moderno</h2>
        <p class="section-sub">A gestão da clínica não deve depender de software desatualizado ou caro demais para o que entrega.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Odontológico</h3>
                <p>Registre anamnese odontológica, queixas, tratamentos realizados, hipóteses diagnósticas e evolução de cada consulta. Histórico completo por paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda Multi-Dentista</h3>
                <p>Agende consultas para todos os dentistas da clínica. Filtre por profissional, data e status. Cancele e remarque diretamente na agenda.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Arquivos e Radiografias</h3>
                <p>Anexe radiografias, moldes digitalizados, laudos e qualquer arquivo diretamente ao prontuário do paciente. Acesse de qualquer dispositivo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe Completa</h3>
                <p>Cadastre todos os dentistas e colaboradores da clínica. Cada profissional acessa apenas os dados relevantes ao seu trabalho.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios e Gestão</h3>
                <p>Acompanhe atendimentos por período, profissional e tipo de procedimento. Dados para tomada de decisão na gestão da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Segurança e Privacidade</h3>
                <p>Cada clínica tem seu ambiente isolado. Acesso por login e perfil de usuário. Os dados dos seus pacientes não são compartilhados.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Migração simplificada</div>
        <h2>Comece a usar em 3 passos</h2>
        <p class="section-sub">Migrar de sistema não precisa ser traumático. Aqui você começa a testar sem burocracia.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie sua conta gratuitamente</h3>
                <p>Preencha o nome da clínica e e-mail. Sem cartão de crédito. Acesso imediato ao sistema completo.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre dentistas e pacientes</h3>
                <p>Adicione os profissionais da sua equipe e comece a cadastrar os pacientes. O sistema está pronto para usar.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias e decida</h3>
                <p>Teste prontuários, agenda e relatórios com dados reais. Só assine se gostar. Planos a partir de R$ 79/mês.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre migrar do Odontoclinic</h2>
        <p class="section-sub" style="margin-bottom:40px;">Respostas diretas para quem está avaliando a troca de sistema.</p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O UTecnologia Saúde substitui o Odontoclinic para clínicas odontológicas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Para a maioria dos fluxos clínicos, sim. O UTecnologia Saúde oferece prontuário odontológico, agenda por dentista, gestão de equipe e anexo de arquivos como radiografias. O sistema não possui odontograma gráfico interativo — se esse recurso for essencial para seu fluxo, avalie se vale a diferença no custo e na modernidade da plataforma.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Preciso exportar dados do sistema anterior?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O sistema permite o cadastro manual de pacientes e histórico. Para migrações de grande volume, nossa equipe pode orientar o processo. Durante os 30 dias de trial você tem tempo para avaliar o fluxo antes de comprometer a migração definitiva.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Funciona para clínicas com vários dentistas?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O UTecnologia Saúde é multi-profissional por design. O Plano Clínica suporta até 5 dentistas + 10 colaboradores. O Plano Pro suporta até 20 profissionais. Cada um com sua agenda própria e acesso aos pacientes do seu vínculo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    É possível anexar radiografias no prontuário?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O sistema permite anexar arquivos de qualquer formato (imagens, PDFs, laudos) diretamente ao prontuário do paciente, vinculados a cada atendimento. As radiografias ficam armazenadas com segurança e acessíveis de qualquer dispositivo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Qual o custo em comparação com sistemas de odontologia?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O Plano Solo começa em R$ 79/mês para 1 dentista e 2 colaboradores. O Plano Clínica custa R$ 199/mês para até 5 dentistas. Em geral, os sistemas especializados em odontologia cobram acima de R$ 150–300/mês por valores similares. E você testa grátis por 30 dias antes de decidir.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema precisa ser instalado no computador da clínica?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. O UTecnologia Saúde é 100% online (SaaS). Acesse pelo navegador de qualquer computador, tablet ou celular. Sem instalação, sem servidor local, sem backup manual. Ideal para clínicas que querem modernizar a operação.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Teste antes de migrar — 30 dias grátis</h2>
            <p class="cta-sub">Crie sua conta agora e avalie o UTecnologia Saúde com dados reais da sua clínica.<br>Sem cartão de crédito. Sem compromisso de permanência.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-dentistas">Para Dentistas</a>
                <a href="<?=base_url()?>software-para-clinicas-odontologicas">Clínicas Odontológicas</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>alternativa-feegow">vs Feegow</a>
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
  "url": "https://utecnologia.com.br/alternativa-odontoclinic",
  "description": "Procurando substituir o Odontoclinic? Conheça o UTecnologia Saúde: prontuário odontológico, agenda e gestão de clínica. Trial grátis 30 dias.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Dentistas", "item": "https://utecnologia.com.br/sistema-para-dentistas"},
    {"@type": "ListItem", "position": 3, "name": "Alternativa ao Odontoclinic", "item": "https://utecnologia.com.br/alternativa-odontoclinic"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O UTecnologia Saúde substitui o Odontoclinic para clínicas odontológicas?", "acceptedAnswer": {"@type": "Answer", "text": "Para a maioria dos fluxos, sim. O sistema oferece prontuário odontológico, agenda por dentista e anexo de arquivos como radiografias. Não possui odontograma gráfico interativo."}},
    {"@type": "Question", "name": "Funciona para clínicas com vários dentistas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O Plano Clínica suporta até 5 dentistas + 10 colaboradores por R$ 199/mês. O Plano Pro suporta até 20 profissionais."}},
    {"@type": "Question", "name": "É possível anexar radiografias no prontuário?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema permite anexar arquivos de qualquer formato diretamente ao prontuário do paciente, vinculados a cada atendimento."}},
    {"@type": "Question", "name": "Qual o custo em comparação com sistemas de odontologia?", "acceptedAnswer": {"@type": "Answer", "text": "O Plano Solo começa em R$ 79/mês para 1 dentista e 2 colaboradores. O Plano Clínica custa R$ 199/mês para até 5 dentistas. E você testa grátis por 30 dias antes de decidir."}}
  ]
}
</script>
</body>
</html>
