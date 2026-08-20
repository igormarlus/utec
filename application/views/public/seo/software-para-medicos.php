<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Software para Médicos — Prontuário e Agenda para Consultórios | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software médico com prontuário eletrônico, agenda de consultas e gestão para médicos e consultórios. 100% online, sem instalação. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/software-para-medicos">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/software-para-medicos">
    <meta property="og:title" content="Software para Médicos — UTecnologia Saúde">
    <meta property="og:description" content="Software médico com prontuário eletrônico e agenda para consultórios. Trial grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Software para Médicos — UTecnologia Saúde">
    <meta name="twitter:description" content="Software médico online. Prontuário, agenda e gestão. Trial grátis 30 dias.">
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
    .fm-textarea{width:100%;background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ink);font-family:var(--ff-body);resize:none;}
    .fm-btn{display:block;width:100%;background:var(--teal);color:var(--white);border:none;padding:11px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--ff-body);text-align:center;margin-top:4px;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:36px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:16px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário Médico — Software para Médicos';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .pmock-row.col3>*{flex:1;}
    .pmock-field-input{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:8px 10px;font-size:12px;color:rgba(255,255,255,.75);width:100%;}
    .pmock-act{display:flex;gap:10px;justify-content:flex-end;padding-top:4px;}
    .pmock-act button{padding:8px 18px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:default;font-family:var(--ff-body);}
    .pmock-act .btn-save{background:rgba(0,127,163,.3);color:var(--teal-md);}
    .pmock-act .btn-finish{background:var(--teal);color:var(--white);}
    .section{padding:72px 0;}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);text-align:center;margin-bottom:12px;}
    h2{font-family:var(--ff-display);font-size:36px;font-weight:700;text-align:center;color:var(--ink);margin-bottom:14px;}
    .section-sub{font-size:17px;color:var(--muted);text-align:center;max-width:560px;margin:0 auto 48px;line-height:1.65;}
    /* personas — preserved */
    .personas{display:grid;grid-template-columns:1fr 1fr;gap:28px;}
    .persona-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:32px;}
    .persona-card.highlight{border-color:var(--teal);box-shadow:0 0 0 2px rgba(0,127,163,.15);}
    .persona-title{font-family:var(--ff-display);font-size:20px;font-weight:700;color:var(--ink);margin-bottom:8px;}
    .persona-plan{display:inline-block;background:var(--teal-lt);color:var(--teal);border:1px solid var(--teal-md);border-radius:999px;font-size:13px;font-weight:700;padding:5px 14px;margin-bottom:14px;}
    .persona-card ul{list-style:none;display:flex;flex-direction:column;gap:8px;}
    .persona-card li{font-size:14px;color:var(--muted);padding-left:20px;position:relative;line-height:1.5;}
    .persona-card li::before{content:'✓';position:absolute;left:0;color:var(--green);font-weight:700;}
    /* steps-grid — preserved */
    .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;}
    .step{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;text-align:center;}
    .step-number{width:40px;height:40px;background:var(--teal);color:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;margin:0 auto 16px;}
    .step h3{font-family:var(--ff-display);font-size:16px;font-weight:600;color:var(--ink);margin-bottom:8px;}
    .step p{font-size:14px;color:var(--muted);line-height:1.65;}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.personas,.steps-grid{grid-template-columns:1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.nav-links{display:none;}h1{font-size:28px;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>sistema-para-consultorio-medico">Consultório Médico</a>
            <a href="<?=base_url()?>software-para-clinicas">Software para Clínicas</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Software para Médicos</div>
                <h1><em>Software para médicos</em> — prontuário e agenda para consultórios e clínicas</h1>
                <p class="hero-text">
                    Um sistema médico 100% online com prontuário eletrônico, agenda de consultas e retornos,
                    exames e relatórios. Para médicos autônomos e clínicas com equipe.
                    Sem instalação. Sem servidor local.
                </p>
                <p class="hero-text" style="font-size:15px;background:#ffffff;border:1px solid var(--border);border-radius:12px;padding:14px 16px;max-width:620px;">
                    <strong>Em resumo:</strong> este software médico serve melhor para consultório e clínica que querem agenda,
                    prontuário e operação online sem instalação. Se você precisa de um sistema ultraespecializado por equipamento
                    ou integração muito específica de hospital, vale validar esse ponto antes da contratação.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>sistema-para-consultorio-medico" class="btn-outline">Para consultório</a>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">O software inclui:</span>
                    <span class="funciona-chip">Prontuário eletrônico</span>
                    <span class="funciona-chip">Agenda de consultas</span>
                    <span class="funciona-chip">Solicitação de exames</span>
                    <span class="funciona-chip">Relatórios de atendimento</span>
                    <span class="funciona-chip">A partir de R$ 79/mês</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Prontuário Médico</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Queixa Principal / Anamnese</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Queixa principal e histórico clínico..." readonly></textarea>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">PA</label>
                            <input class="fm-input" type="text" placeholder="120/80 mmHg" readonly>
                        </div>
                        <div>
                            <label class="fm-label">FC</label>
                            <input class="fm-input" type="text" placeholder="72 bpm" readonly>
                        </div>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">Hipótese / CID</label>
                            <input class="fm-input" type="text" placeholder="Ex: J06.9" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Evolução</label>
                            <input class="fm-input" type="text" placeholder="Retorno / evolução" readonly>
                        </div>
                    </div>
                    <button class="fm-btn" disabled>Finalizar consulta →</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Veja o software em ação</div>
            <h2>Prontuário médico no fluxo real de atendimento</h2>
            <p>Queixa, sinais vitais, hipótese diagnóstica e conduta — tudo registrado por consulta, com histórico cronológico automático.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário · Dr. Marcelo Costa · Carlos Mendes · 05/06/2026</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa Principal / Anamnese</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Tosse seca há 8 dias, febre baixa (37,8°C) há 3 dias. Nega dispneia. Rinorreia hialina. Criança de 6 anos, vacinação em dia.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação / Sinais Vitais</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">Temp.</div>
                                <div class="pmock-field-input">37,6°C</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">FR</div>
                                <div class="pmock-field-input">22 irpm</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">CID</div>
                                <div class="pmock-field-input">J06.9</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta / Prescrição</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">IVAS viral. Dipirona 15mg/kg SN. Nasal com SF 0,9% 4x/dia. Retorno se febre &gt;48h ou piora respiratória. Orientações à mãe.</div>
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
        <div class="section-label">Para qual médico é este software?</div>
        <h2>Planos para médico autônomo e clínica com equipe</h2>
        <p class="section-sub" style="margin-bottom:40px;">O UTecnologia Saúde atende tanto o médico que trabalha só quanto a clínica com vários profissionais.</p>
        <div class="personas">
            <div class="persona-card highlight">
                <div class="persona-title">Médico autônomo</div>
                <div class="persona-plan">Plano Solo — R$ 79/mês</div>
                <ul>
                    <li>1 médico + 2 colaboradores</li>
                    <li>Pacientes ilimitados</li>
                    <li>Prontuário eletrônico completo</li>
                    <li>Agenda de consultas e retornos</li>
                    <li>Solicitação e controle de exames</li>
                    <li>Relatórios de atendimento</li>
                </ul>
            </div>
            <div class="persona-card">
                <div class="persona-title">Clínica com equipe médica</div>
                <div class="persona-plan">Plano Clínica — R$ 199/mês</div>
                <ul>
                    <li>Até 5 médicos + 10 colaboradores</li>
                    <li>Pacientes ilimitados</li>
                    <li>Agenda por profissional</li>
                    <li>Prontuário por especialidade</li>
                    <li>Relatórios por médico e clínica</li>
                    <li>Hierarquia de acesso por perfil</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--teal-lt);">
    <div class="wrap">
        <div class="section-label">Como começar</div>
        <h2>Três passos para usar o software médico</h2>
        <p class="section-sub" style="margin-bottom:40px;">Do cadastro ao primeiro atendimento em minutos, sem necessidade de treinamento técnico.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Crie sua conta</h3>
                <p>Acesse o formulário de trial e cadastre seu consultório em menos de 2 minutos. Sem cartão de crédito e sem contrato.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Configure e cadastre pacientes</h3>
                <p>Adicione seus dados, cadastre os primeiros pacientes e configure sua agenda. O sistema é intuitivo — sem manual, sem suporte técnico.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Use por 30 dias grátis</h3>
                <p>Use todas as funcionalidades com dados reais durante 30 dias. Prontuário, agenda, exames e relatórios — nada bloqueado no trial.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Decisão prática</div>
        <h2>Quando este software faz sentido para o médico</h2>
        <p class="section-sub">A página precisa responder rápido para quem serve e quando não serve.</p>
        <div class="personas">
            <div class="persona-card highlight">
                <div class="persona-title">Faz sentido se você quer</div>
                <ul>
                    <li>Prontuário, agenda e exames no mesmo fluxo</li>
                    <li>Sistema online sem instalação local</li>
                    <li>Começar com trial antes de assinar</li>
                    <li>Plano para consultório e evolução para clínica</li>
                </ul>
            </div>
            <div class="persona-card">
                <div class="persona-title">Vale validar antes se você precisa</div>
                <ul>
                    <li>Integrações muito específicas fora do fluxo atual do produto</li>
                    <li>Funcionalidades hospitalares não prometidas nesta página</li>
                    <li>Campos ultraespecializados que dependem de nicho muito restrito</li>
                    <li>Processos regulatórios que exigem conferência técnica própria</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o software médico</h2>
        <p class="section-sub" style="margin-bottom:40px;">O que os médicos perguntam antes de usar o sistema.</p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O software tem prontuário eletrônico dentro da norma do CFM?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O prontuário eletrônico do UTecnologia Saúde é estruturado para o registro clínico médico. Para conformidade total com a Resolução CFM 1.821/2007 é necessário verificar requisitos específicos de assinatura digital. Recomendamos consultar o CRM estadual sobre o seu caso específico.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso usar o software em mais de um computador ou tablet?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Como o sistema é 100% online, você acessa de qualquer dispositivo com internet — o computador do consultório, o notebook de casa, o tablet na visita hospitalar. O mesmo login funciona em todos eles, e os dados estão sempre sincronizados em tempo real.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O software tem agenda de retorno e encaixe?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. A agenda suporta consultas, retornos e encaixes. Você pode visualizar por dia, semana ou mês, filtrar por profissional e ver o status de cada agendamento (confirmado, cancelado, remarcado, realizado).</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O software funciona para médicos especialistas (cardiologia, ortopedia, dermatologia)?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O prontuário é adaptável para diferentes especialidades. Os campos de queixa, avaliação e conduta funcionam para qualquer especialidade clínica. Para médicos de especialidades como oftalmologia, os campos específicos da especialidade estão em desenvolvimento.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso solicitar exames e registrar resultados no sistema?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Durante o atendimento você pode registrar os exames solicitados. Quando os resultados chegam, são vinculados ao prontuário do paciente. É possível também fazer upload de arquivos (PDFs de laudos, imagens) diretamente no sistema.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Qual a diferença entre o Plano Solo e o Plano Clínica?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">O Plano Solo (R$ 79/mês) é para 1 médico e 2 colaboradores — ideal para consultório individual. O Plano Clínica (R$ 199/mês) inclui até 5 profissionais e 10 colaboradores, com agenda individual por médico e relatórios por profissional e por clínica.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Software médico com 30 dias grátis</h2>
            <p class="cta-sub">Prontuário, agenda e exames para o seu consultório.<br>Sem instalação. Sem cartão de crédito.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta médica grátis →</a>
            <p style="margin-top:16px;font-size:14px;color:rgba(255,255,255,.72);">
                Compare também com <a href="<?=base_url()?>sistema-para-consultorio-medico" style="color:#fff;text-decoration:underline;">consultório médico</a>,
                <a href="<?=base_url()?>sistema-para-clinica-medica" style="color:#fff;text-decoration:underline;">clínica médica</a> e
                <a href="<?=base_url()?>alternativa-shosp" style="color:#fff;text-decoration:underline;">alternativa ao Shosp</a>.
            </p>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-para-consultorio-medico">Consultório Médico</a>
                <a href="<?=base_url()?>software-para-clinicas">Software para Clínicas</a>
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
  "url": "https://utecnologia.com.br/software-para-medicos",
  "description": "Software médico com prontuário eletrônico, agenda de consultas e gestão para médicos e consultórios, 100% online.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Software para Médicos", "item": "https://utecnologia.com.br/software-para-medicos"}
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
      "name": "O software tem prontuário eletrônico dentro da norma do CFM?",
      "acceptedAnswer": {"@type": "Answer", "text": "O prontuário eletrônico é estruturado para o registro clínico médico. Para conformidade total com a Resolução CFM 1.821/2007, recomendamos verificar requisitos de assinatura digital com o CRM estadual."}
    },
    {
      "@type": "Question",
      "name": "O software funciona para médicos especialistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O prontuário é adaptável para diferentes especialidades. Os campos de queixa, avaliação e conduta funcionam para qualquer especialidade clínica."}
    },
    {
      "@type": "Question",
      "name": "Posso usar o software em mais de um computador ou tablet?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. Como o sistema é 100% online, você acessa de qualquer dispositivo com internet — computador, notebook ou tablet. O mesmo login funciona em todos."}
    },
    {
      "@type": "Question",
      "name": "Qual a diferença entre o Plano Solo e o Plano Clínica?",
      "acceptedAnswer": {"@type": "Answer", "text": "O Plano Solo (R$ 79/mês) é para 1 médico e 2 colaboradores. O Plano Clínica (R$ 199/mês) inclui até 5 profissionais e 10 colaboradores, com agenda individual e relatórios por profissional."}
    }
  ]
}
</script>
</body>
</html>
