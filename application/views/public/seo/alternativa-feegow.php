<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alternativa ao Feegow — Sistema para Clínicas com Mais Recursos | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Procurando uma alternativa ao Feegow? Conheça o UTecnologia Saúde — sistema SaaS para clínicas com prontuário eletrônico, agenda inteligente e planos acessíveis. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-feegow">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/alternativa-feegow">
    <meta property="og:title" content="Alternativa ao Feegow — UTecnologia Saúde">
    <meta property="og:description" content="Alternativa ao Feegow com prontuário, agenda e gestão clínica. Planos a partir de R$ 79/mês. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Alternativa ao Feegow — UTecnologia Saúde">
    <meta name="twitter:description" content="Alternativa ao Feegow com prontuário, agenda e gestão clínica. Teste grátis 30 dias.">
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
    /* single-column hero */
    .hero{padding:80px 0 72px;background:linear-gradient(145deg,var(--teal-lt) 0%,var(--paper) 55%);text-align:center;}
    .eyebrow{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);margin-bottom:14px;}
    h1{font-family:var(--ff-display);font-size:48px;font-weight:700;line-height:1.1;color:var(--ink);margin-bottom:20px;max-width:760px;margin-left:auto;margin-right:auto;}
    h1 em{font-style:italic;color:var(--teal);}
    .hero-text{font-size:18px;color:var(--muted);line-height:1.75;margin-bottom:32px;max-width:640px;margin-left:auto;margin-right:auto;}
    .hero-cta{display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-bottom:16px;}
    .btn-primary{display:inline-block;background:var(--teal);color:var(--white);padding:14px 28px;border-radius:999px;font-weight:700;font-size:15px;}
    .btn-primary:hover{background:#006d8c;color:var(--white);}
    .btn-outline{display:inline-block;border:2px solid var(--border);color:var(--muted);padding:13px 24px;border-radius:999px;font-weight:600;font-size:14px;}
    .trust-line{font-size:12px;color:var(--subtle);display:flex;gap:16px;flex-wrap:wrap;justify-content:center;}
    .trust-line span::before{content:'✓ ';color:var(--green);font-weight:700;}
    .prontuario-section{padding:80px 0;background:var(--navy);position:relative;overflow:hidden;}
    .prontuario-section::before{content:'';position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,127,163,.3) 0%,transparent 70%);pointer-events:none;}
    .pront-header{text-align:center;margin-bottom:44px;}
    .pront-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal-md);margin-bottom:12px;}
    .pront-header h2{font-family:var(--ff-display);font-size:32px;font-weight:700;color:var(--white);margin-bottom:12px;}
    .pront-header p{font-size:15px;color:rgba(255,255,255,.65);max-width:540px;margin:0 auto;}
    .prontuario-stage{position:relative;max-width:820px;margin:0 auto;}
    .prontuario-stage::before{content:'Prontuário Eletrônico — UTecnologia Saúde';position:absolute;top:-12px;left:24px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--teal-md);background:var(--navy);padding:0 8px;z-index:10;}
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
    .pmock-row.col2>*,.pmock-row.col3>*{flex:1;}
    .pmock-field-input{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:6px;padding:8px 10px;font-size:12px;color:rgba(255,255,255,.75);width:100%;}
    .pmock-act{display:flex;gap:10px;justify-content:flex-end;padding-top:4px;}
    .pmock-act button{padding:8px 18px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:default;font-family:var(--ff-body);}
    .pmock-act .btn-save{background:rgba(0,127,163,.3);color:var(--teal-md);}
    .pmock-act .btn-finish{background:var(--teal);color:var(--white);}
    .section{padding:72px 0;}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);text-align:center;margin-bottom:12px;}
    h2{font-family:var(--ff-display);font-size:36px;font-weight:700;text-align:center;color:var(--ink);margin-bottom:14px;}
    .section-sub{font-size:17px;color:var(--muted);text-align:center;max-width:560px;margin:0 auto 48px;line-height:1.65;}
    /* compare table preserved */
    .compare-wrap{overflow-x:auto;}
    .compare-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:20px;overflow:hidden;border:1px solid var(--border);}
    .compare-table th{padding:16px 20px;font-size:13px;font-weight:700;text-align:left;background:var(--paper);}
    .compare-table th.col-utec{background:var(--teal);color:var(--white);}
    .compare-table td{padding:14px 20px;font-size:14px;border-top:1px solid var(--border);}
    .compare-table tr:nth-child(even) td{background:var(--paper);}
    .check{color:var(--green);font-weight:700;}
    .cross{color:#ef4444;font-weight:700;}
    .partial{color:#f59e0b;font-weight:700;}
    .badge-utec{display:inline-block;background:var(--teal-lt);color:var(--teal);font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;}
    .disclaimer{font-size:11px;color:var(--subtle);text-align:center;margin-top:16px;}
    /* reasons grid preserved */
    .reasons-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .reason-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:24px;transition:transform .2s;}
    .reason-card:hover{transform:translateY(-3px);}
    .reason-icon{font-size:28px;margin-bottom:12px;}
    .reason-card h3{font-family:var(--ff-display);font-size:16px;font-weight:600;margin-bottom:8px;color:var(--ink);}
    .reason-card p{font-size:14px;color:var(--muted);line-height:1.6;}
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
    @media(max-width:900px){.reasons-grid{grid-template-columns:1fr 1fr;}h1{font-size:34px;}h2{font-size:28px;}}
    @media(max-width:600px){.reasons-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:28px;}.cta-wrap{padding:40px 24px;}}
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:44px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
            <a href="<?=base_url()?>assinar">Ver planos</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="eyebrow">Comparativo de Sistemas</div>
        <h1>Procurando uma <em>alternativa ao Feegow</em>?</h1>
        <p class="hero-text">
            O UTecnologia Saúde é um sistema SaaS para clínicas médicas com prontuário eletrônico,
            agenda inteligente e gestão de equipe — com planos acessíveis para consultórios e clínicas de todos os tamanhos.
        </p>
        <div class="hero-cta">
            <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
            <a href="<?=base_url()?>sistema-para-clinicas" class="btn-outline">Ver funcionalidades</a>
        </div>
        <div class="trust-line">
            <span>Sem cartão de crédito</span>
            <span>Planos a partir de R$ 79/mês</span>
            <span>Prontuário + agenda + exames</span>
        </div>
    </div>
</section>

<section class="prontuario-section">
    <div class="wrap">
        <div class="pront-header">
            <div class="pront-label">Veja como é o sistema</div>
            <h2>Prontuário eletrônico integrado à agenda</h2>
            <p>Anamnese, evolução clínica, hipóteses diagnósticas e conduta — registradas por consulta na timeline do paciente.</p>
        </div>
        <div class="prontuario-stage">
            <div class="prontuario-mock">
                <div class="pmock-topbar">
                    <div class="pmock-dots"><span></span><span></span><span></span></div>
                    <span class="pmock-title">Prontuário Eletrônico · Maria Costa · Clínica Médica</span>
                </div>
                <div class="pmock-body">
                    <div class="pmock-group">
                        <div class="pmock-group-label">Queixa Principal</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Cefaleia recorrente há 2 semanas, tontura ao levantar. HAS em uso de Losartana 50mg.</div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Avaliação Clínica</div>
                        <div class="pmock-row col3">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">PA (mmHg)</div>
                                <div class="pmock-field-input">158/96</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">FC (bpm)</div>
                                <div class="pmock-field-input">84</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:4px;">HDA / CID</div>
                                <div class="pmock-field-input">HAS — I10</div>
                            </div>
                        </div>
                    </div>
                    <div class="pmock-group">
                        <div class="pmock-group-label">Conduta</div>
                        <div class="pmock-row">
                            <div class="pmock-field-input">Ajuste Losartana 50mg → 100mg/dia. Solicitar perfil lipídico e ECG. Orientações de estilo de vida. Retorno em 30 dias.</div>
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
        <div class="section-label">Comparativo</div>
        <h2>UTecnologia Saúde vs outros sistemas</h2>
        <p class="section-sub">Veja como o UTecnologia Saúde se posiciona nos critérios mais importantes para clínicas.</p>
        <div class="compare-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Funcionalidade</th>
                        <th class="col-utec">UTecnologia Saúde <span class="badge-utec">Recomendado</span></th>
                        <th>Sistemas genéricos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Prontuário eletrônico completo</td><td class="check">✓ Incluído</td><td class="partial">⚠ Varia</td></tr>
                    <tr><td>Agenda por profissional</td><td class="check">✓ Incluído</td><td class="partial">⚠ Varia</td></tr>
                    <tr><td>Multi-profissionais na mesma clínica</td><td class="check">✓ Todos os planos</td><td class="partial">⚠ Planos pagos</td></tr>
                    <tr><td>Controle de exames integrado</td><td class="check">✓ Incluído</td><td class="cross">✗ Separado</td></tr>
                    <tr><td>Hierarquia de acesso (perfis)</td><td class="check">✓ Incluído</td><td class="partial">⚠ Limitado</td></tr>
                    <tr><td>Trial gratuito sem cartão</td><td class="check">✓ 30 dias</td><td class="partial">⚠ Varia</td></tr>
                    <tr><td>Plano para consultório solo</td><td class="check">✓ R$ 79/mês</td><td class="partial">⚠ Varia</td></tr>
                    <tr><td>Suporte em português</td><td class="check">✓ Sim</td><td class="partial">⚠ Varia</td></tr>
                </tbody>
            </table>
        </div>
        <p class="disclaimer">* Informações baseadas em pesquisa de mercado pública. Características dos concorrentes podem variar. Verifique diretamente no site de cada fornecedor.</p>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Por que UTecnologia Saúde</div>
        <h2>Razões para escolher o UTecnologia Saúde</h2>
        <p class="section-sub">Uma plataforma construída para o fluxo real de clínicas médicas brasileiras.</p>
        <div class="reasons-grid">
            <div class="reason-card">
                <div class="reason-icon">💰</div>
                <h3>Planos acessíveis</h3>
                <p>Começa em R$ 79/mês para consultórios solo. Sem taxas ocultas, sem cobrança por paciente e sem limite de consultas.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">🚀</div>
                <h3>30 dias grátis</h3>
                <p>Você experimenta o sistema completo por 30 dias sem precisar inserir cartão de crédito. Só assina se gostar.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">📋</div>
                <h3>Prontuário completo</h3>
                <p>Anamnese, evolução clínica, diagnóstico, conduta e timeline do paciente — tudo integrado em um único sistema.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">👥</div>
                <h3>Multi-especialidade</h3>
                <p>Funciona para médicos, psicólogos, dentistas, fisioterapeutas, nutricionistas e outras especialidades de saúde.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">🔒</div>
                <h3>Dados isolados</h3>
                <p>Cada clínica tem seu ambiente próprio. Seus dados de pacientes não são misturados com os de outras clínicas.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">🌐</div>
                <h3>100% online</h3>
                <p>Acesse de qualquer dispositivo com internet. Sem instalação, sem servidor local, sem manutenção de software.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--teal-lt);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre a migração de sistema</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item open">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    É difícil migrar de outro sistema para o UTecnologia Saúde?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Não. O processo começa com a criação da sua conta gratuita. Você pode ir cadastrando os pacientes ao longo do tempo, começando pelos ativos, e reconstruindo o histórico de forma gradual conforme a necessidade clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O trial de 30 dias dá acesso a todas as funcionalidades?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Durante o trial, você tem acesso completo ao sistema — prontuário, agenda, exames, gestão de equipe e relatórios — sem precisar de cartão de crédito ou assinatura.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    Posso usar o UTecnologia Saúde com minha recepcionista?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. Todos os planos incluem colaboradores. A recepcionista acessa a agenda e o cadastro de pacientes, enquanto os profissionais de saúde gerenciam os prontuários. Cada perfil vê apenas o que é relevante para sua função.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
                    O sistema funciona para clínicas com vários profissionais?
                    <span class="faq-chevron">▾</span>
                </div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 profissionais de saúde e o plano Pro até 20, todos com suas próprias agendas e prontuários dentro da mesma clínica.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-wrap">
            <h2>Experimente o UTecnologia Saúde gratuitamente</h2>
            <p class="cta-sub">30 dias para testar todos os recursos. Sem cartão de crédito.<br>Se não gostar, não cobra nada.</p>
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
                <a href="<?=base_url()?>sistema-para-clinicas">Funcionalidades</a>
                <a href="<?=base_url()?>assinar">Planos</a>
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
  "url": "https://utecnologia.com.br/alternativa-feegow",
  "description": "Alternativa ao Feegow com prontuário eletrônico, agenda inteligente e planos acessíveis para clínicas.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Alternativa ao Feegow", "item": "https://utecnologia.com.br/alternativa-feegow"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "É difícil migrar de outro sistema para o UTecnologia Saúde?", "acceptedAnswer": {"@type": "Answer", "text": "Não. O processo começa com a criação da conta gratuita. Você vai cadastrando os pacientes ao longo do tempo, começando pelos ativos."}},
    {"@type": "Question", "name": "O trial de 30 dias dá acesso a todas as funcionalidades?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. Durante o trial você tem acesso completo — prontuário, agenda, exames e gestão de equipe — sem cartão de crédito."}},
    {"@type": "Question", "name": "O sistema funciona para clínicas com vários profissionais?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Clínica suporta até 5 profissionais e o Pro até 20, todos com suas próprias agendas e prontuários."}}
  ]
}
</script>
</body>
</html>
