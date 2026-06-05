<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Prontuário Eletrônico para Clínicas — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de prontuário eletrônico completo para clínicas e consultórios. Anamnese, evolução clínica, exames e histórico do paciente em um registro digital seguro e acessível. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-prontuario-eletronico">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-prontuario-eletronico">
    <meta property="og:title" content="Sistema de Prontuário Eletrônico — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário eletrônico completo para clínicas e consultórios. Anamnese, evolução, exames e histórico do paciente. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema de Prontuário Eletrônico — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico para clínicas e consultórios. 30 dias grátis.">
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
    .prontuario-stage::before{content:'Prontuário Eletrônico Digital';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .feature-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:28px;transition:transform .2s;}
    .feature-card:hover{transform:translateY(-3px);}
    .feature-icon{width:44px;height:44px;background:var(--teal-lt);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px;}
    .feature-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .feature-card p{font-size:14px;color:var(--muted);line-height:1.65;}
    /* vs-table preserved */
    .vs-table{background:var(--white);border-radius:20px;border:1px solid var(--border);overflow:hidden;box-shadow:var(--shadow);}
    .vs-header{display:grid;grid-template-columns:1fr 1fr 1fr;background:var(--paper);padding:16px 24px;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--subtle);}
    .vs-header .col-utec{color:var(--teal);}
    .vs-row{display:grid;grid-template-columns:1fr 1fr 1fr;padding:14px 24px;border-top:1px solid var(--border);font-size:14px;align-items:center;}
    .vs-row:nth-child(even){background:var(--paper);}
    .check{color:var(--green);font-weight:700;}
    .cross{color:#ef4444;font-weight:700;}
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
    @media(max-width:900px){.hero-inner{grid-template-columns:1fr;}.features-grid{grid-template-columns:1fr 1fr;}.vs-header,.vs-row{grid-template-columns:1fr 1fr;}.vs-header .col-papel,.vs-row .col-papel{display:none;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.features-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Prontuário Eletrônico</div>
                <h1><em>Sistema de prontuário eletrônico</em> para clínicas e consultórios</h1>
                <p class="hero-text">
                    Substitua o papel e as planilhas por um prontuário eletrônico estruturado, acessível de qualquer dispositivo,
                    com histórico completo de cada paciente organizado e seguro.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>sistema-para-clinicas" class="btn-outline">Ver especialidades</a>
                </div>
                <div class="funciona-strip">
                    <span class="funciona-label">Campos do prontuário:</span>
                    <span class="funciona-chip">Anamnese estruturada</span>
                    <span class="funciona-chip">Evolução por consulta</span>
                    <span class="funciona-chip">Hipótese diagnóstica</span>
                    <span class="funciona-chip">Conduta e prescrição</span>
                    <span class="funciona-chip">Exames integrados</span>
                </div>
            </div>
            <div class="hero-card">
                <div class="topbar-dots">
                    <span></span><span></span><span></span>
                    <span class="card-title-bar">Prontuário Eletrônico</span>
                </div>
                <div class="card-body">
                    <div class="fm-group">
                        <label class="fm-label">Queixa Principal / Anamnese</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Queixa principal e contexto clínico..." readonly></textarea>
                    </div>
                    <div class="fm-grid2">
                        <div>
                            <label class="fm-label">Hipótese / CID</label>
                            <input class="fm-input" type="text" placeholder="Ex: HAS — I10" readonly>
                        </div>
                        <div>
                            <label class="fm-label">Evolução</label>
                            <input class="fm-input" type="text" placeholder="Retorno / evolução" readonly>
                        </div>
                    </div>
                    <div class="fm-group">
                        <label class="fm-label">Conduta / Prescrição</label>
                        <textarea class="fm-textarea" rows="2" placeholder="Conduta clínica, prescrição, orientações..." readonly></textarea>
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
            <div class="pront-label">O prontuário em ação</div>
            <h2>Registro digital de cada atendimento</h2>
            <p>Anamnese, avaliação clínica, diagnóstico, conduta e exames — todos registrados na timeline cronológica do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário Eletrônico · João Silva · Consulta 04/06/2026</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa Principal / Anamnese</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Dor precordial em aperto há 2 dias. Irradiação para MSE. Nega sudorese. HAS em uso de Losartana. HF de IAM paterno.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação / Hipótese Diagnóstica</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">PA</div>
                                <div class="pmock-field-input">148/92 mmHg</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">FC</div>
                                <div class="pmock-field-input">88 bpm</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">CID</div>
                                <div class="pmock-field-input">I20 / R07.4</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta / Prescrição</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Solicitar ECG + Troponina + CK-MB. AAS 100mg/dia. Encaminhar cardiologista. Orientação para SE se piora.</div>
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

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que o prontuário eletrônico precisa ter</h2>
        <p class="section-sub">Uma plataforma pensada para o fluxo real de atendimento clínico.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Anamnese e Histórico</h3>
                <p>Registre queixa principal, histórico da doença atual, antecedentes pessoais e familiares na primeira consulta. Disponível em todas as consultas seguintes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Evolução por Atendimento</h3>
                <p>Cada consulta registra uma evolução clínica separada, mantendo uma timeline cronológica completa da trajetória do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames Integrados</h3>
                <p>Solicite exames durante o atendimento, registre resultados e armazene laudos diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Documentos e Arquivos</h3>
                <p>Anexe PDFs, imagens e outros arquivos clínicos ao prontuário. Tudo associado ao histórico do paciente e acessível quando necessário.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Acesso por Perfil</h3>
                <p>O prontuário é acessível apenas ao profissional responsável. Recepcionistas e colaboradores administrativos não visualizam os registros clínicos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Acesso Online Seguro</h3>
                <p>Acesse o prontuário de qualquer dispositivo com internet. Sem instalação local, sem risco de perda de dados por falha de hardware.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--white);">
    <div class="wrap">
        <div class="section-label">Prontuário digital vs papel</div>
        <h2>Por que migrar para o prontuário eletrônico?</h2>
        <p class="section-sub" style="margin-bottom:40px;">Compare o prontuário em papel com o sistema digital.</p>
        <div class="vs-table">
            <div class="vs-header">
                <div>Critério</div>
                <div class="col-utec">UTecnologia Saúde</div>
                <div class="col-papel">Prontuário em papel</div>
            </div>
            <div class="vs-row"><div>Acessível de qualquer lugar</div><div class="check">✓ Sim</div><div class="col-papel cross">✗ Não</div></div>
            <div class="vs-row"><div>Busca rápida por paciente</div><div class="check">✓ Imediata</div><div class="col-papel cross">✗ Manual</div></div>
            <div class="vs-row"><div>Risco de perda ou deterioração</div><div class="check">✓ Zero</div><div class="col-papel cross">✗ Alto</div></div>
            <div class="vs-row"><div>Legibilidade garantida</div><div class="check">✓ Sempre</div><div class="col-papel cross">✗ Caligrafia</div></div>
            <div class="vs-row"><div>Histórico cronológico automático</div><div class="check">✓ Sim</div><div class="col-papel cross">✗ Manual</div></div>
            <div class="vs-row"><div>Exames vinculados ao registro</div><div class="check">✓ Integrado</div><div class="col-papel cross">✗ Separado</div></div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--teal-lt);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre prontuário eletrônico</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O prontuário eletrônico tem validade legal no Brasil?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O prontuário eletrônico tem validade legal no Brasil. A Resolução CFM 1.821/2007 regulamenta o uso de prontuário eletrônico do paciente para médicos. Para demais profissionais, cada conselho profissional pode ter regulamentações específicas, recomendamos consultar o respectivo CFP, CFF, COFFITO, etc.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso migrar meu histórico de prontuários em papel para o sistema?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Você pode cadastrar os pacientes e criar o prontuário digital a partir da próxima consulta, inserindo um resumo do histórico anterior. Também é possível anexar documentos escaneados do prontuário antigo diretamente no perfil do paciente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Os dados ficam seguros caso eu cancele minha conta?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Recomendamos exportar ou fazer backup dos registros importantes antes de encerrar a conta. O sistema permite acessar todos os registros durante o período ativo da assinatura.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso ter o prontuário de pacientes de vários profissionais na mesma clínica?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O sistema é multi-profissional. Cada médico, psicólogo, dentista ou terapeuta gerencia os prontuários dos seus próprios pacientes, com controle hierárquico de acesso.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Modernize o prontuário da sua clínica</h2>
            <p class="cta-sub">30 dias grátis para experimentar o prontuário eletrônico completo.<br>Sem cartão de crédito. Sem instalação.</p>
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
                <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
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
  "url": "https://utecnologia.com.br/sistema-prontuario-eletronico",
  "description": "Sistema de prontuário eletrônico completo para clínicas e consultórios.",
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
    {"@type": "ListItem", "position": 3, "name": "Prontuário Eletrônico", "item": "https://utecnologia.com.br/sistema-prontuario-eletronico"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O prontuário eletrônico tem validade legal no Brasil?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. A Resolução CFM 1.821/2007 regulamenta o uso de prontuário eletrônico para médicos no Brasil. Demais profissionais devem consultar o respectivo conselho de classe."}},
    {"@type": "Question", "name": "Posso migrar meu histórico de prontuários em papel?", "acceptedAnswer": {"@type": "Answer", "text": "Você pode cadastrar pacientes e criar o prontuário digital a partir da próxima consulta, inserindo um resumo do histórico anterior, e também anexar documentos escaneados."}},
    {"@type": "Question", "name": "Posso ter prontuários de vários profissionais na mesma clínica?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema é multi-profissional, com controle hierárquico de acesso. Cada profissional gerencia os prontuários dos seus próprios pacientes."}}
  ]
}
</script>
</body>
</html>
