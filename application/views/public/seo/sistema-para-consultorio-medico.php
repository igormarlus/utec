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
    <title>Sistema para Consultório Médico — Agenda, Prontuário e Gestão Online | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para consultório médico com agenda online, prontuário eletrônico e controle de pacientes. Software médico 100% online, sem instalação. Para médicos autônomos e consultórios pequenos. Plano Solo a partir de R$ 79/mês. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-consultorio-medico">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-consultorio-medico">
    <meta property="og:title" content="Sistema para Consultório Médico — UTecnologia Saúde">
    <meta property="og:description" content="Agenda online, prontuário eletrônico e gestão de pacientes para médicos autônomos. Plano Solo a partir de R$ 79/mês. Teste 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Consultório Médico — UTecnologia Saúde">
    <meta name="twitter:description" content="Agenda e prontuário para médicos autônomos. Software médico online, sem instalação. A partir de R$ 79/mês.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --subtle:#94a3b8; --primary:#0ea5e9; --primary-dark:#0284c7; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --panel:#ffffff; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter',sans-serif; color:var(--ink); background:var(--paper); line-height:1.6; }
        a { color:var(--primary-dark); }
        .wrap { max-width:1100px; margin:0 auto; padding:0 20px; }
        .topnav { background:#fff; border-bottom:1px solid var(--border); padding:14px 0; }
        .topnav .wrap { display:flex; justify-content:space-between; align-items:center; }
        .brand { font-size:17px; font-weight:800; color:var(--ink); text-decoration:none; }
        .nav-links { display:flex; gap:24px; align-items:center; }
        .nav-links a { font-size:14px; font-weight:500; color:var(--muted); text-decoration:none; }
        .btn-nav { background:var(--primary); color:#fff !important; padding:8px 18px; border-radius:999px; font-weight:700 !important; font-size:13px !important; }
        /* Hero */
        .hero { padding:80px 0 60px; background:linear-gradient(160deg,#f0f9ff 0%,#f8fafc 60%); }
        .hero-inner { display:grid; grid-template-columns:3fr 2fr; gap:60px; align-items:center; }
        .eyebrow { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); margin-bottom:12px; }
        h1 { font-size:42px; font-weight:800; line-height:1.12; color:var(--ink); margin-bottom:20px; }
        h1 em { font-style:normal; color:var(--primary); }
        .hero-text { font-size:18px; color:var(--muted); line-height:1.7; margin-bottom:32px; }
        .hero-cta { display:flex; gap:12px; flex-wrap:wrap; }
        .btn-primary { display:inline-block; background:var(--primary); color:#fff; padding:14px 28px; border-radius:999px; font-weight:700; font-size:15px; text-decoration:none; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { display:inline-block; border:1.5px solid var(--border); color:var(--muted); padding:13px 24px; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none; }
        .plan-highlight { background:#fff; border:2px solid var(--primary); border-radius:20px; padding:28px; box-shadow:0 0 0 4px rgba(14,165,233,.08); }
        .plan-label { font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--primary); margin-bottom:6px; }
        .plan-name { font-size:22px; font-weight:800; margin-bottom:4px; }
        .plan-desc { font-size:13px; color:var(--muted); margin-bottom:16px; }
        .plan-price { font-size:36px; font-weight:800; color:var(--primary); }
        .plan-price span { font-size:14px; font-weight:500; color:var(--muted); }
        .plan-features { list-style:none; margin-top:16px; display:flex; flex-direction:column; gap:8px; }
        .plan-features li { font-size:13px; color:var(--muted); }
        .plan-features li::before { content:"✓ "; color:var(--accent); font-weight:700; }
        /* Sections */
        .section { padding:72px 0; }
        .section-label { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); text-align:center; margin-bottom:12px; }
        h2 { font-size:32px; font-weight:800; text-align:center; margin-bottom:16px; }
        .section-sub { font-size:17px; color:var(--muted); text-align:center; max-width:580px; margin:0 auto 48px; }
        /* Answer box */
        .answer-box { background:#fff; border:1px solid var(--border); border-left:4px solid var(--primary); border-radius:12px; padding:24px 28px; max-width:800px; margin:0 auto; }
        .answer-box p { font-size:15px; color:var(--muted); line-height:1.8; }
        .answer-box p + p { margin-top:12px; }
        /* For who */
        .for-who-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; max-width:800px; margin:0 auto; }
        .for-who-item { display:flex; gap:12px; align-items:flex-start; background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:18px 20px; }
        .for-who-check { width:24px; height:24px; background:#f0fdf4; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; margin-top:1px; }
        .for-who-item h4 { font-size:14px; font-weight:700; margin-bottom:4px; }
        .for-who-item p { font-size:13px; color:var(--muted); }
        .not-for-box { max-width:800px; margin:32px auto 0; background:#fff8f0; border:1px solid #fed7aa; border-radius:12px; padding:20px 24px; }
        .not-for-box p { font-size:14px; color:#92400e; }
        .not-for-box a { color:#c2410c; font-weight:600; }
        /* Features */
        .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .feature-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:28px; }
        .feature-icon { font-size:28px; margin-bottom:14px; }
        .feature-card h3 { font-size:16px; font-weight:700; margin-bottom:8px; }
        .feature-card p { font-size:14px; color:var(--muted); line-height:1.6; }
        /* Prontuário */
        .pront-grid { display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; }
        .pront-list { display:flex; flex-direction:column; gap:16px; margin-top:24px; }
        .pront-item { display:flex; gap:14px; align-items:flex-start; }
        .pront-dot { width:8px; height:8px; background:var(--primary); border-radius:50%; margin-top:6px; flex-shrink:0; }
        .pront-item h4 { font-size:14px; font-weight:700; margin-bottom:4px; }
        .pront-item p { font-size:13px; color:var(--muted); }
        /* Online block */
        .online-grid { display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; }
        .online-list { display:flex; flex-direction:column; gap:14px; margin-top:20px; }
        .online-item { display:flex; gap:12px; align-items:flex-start; }
        .online-icon { font-size:18px; flex-shrink:0; margin-top:2px; }
        .online-item h4 { font-size:14px; font-weight:700; margin-bottom:2px; }
        .online-item p { font-size:13px; color:var(--muted); }
        .online-badge { background:linear-gradient(135deg,#0ea5e9,#0284c7); border-radius:20px; padding:32px; color:#fff; }
        .online-badge h3 { font-size:22px; font-weight:800; margin-bottom:12px; }
        .online-badge p { font-size:14px; opacity:.9; line-height:1.6; }
        .online-badge ul { margin-top:16px; list-style:none; display:flex; flex-direction:column; gap:8px; }
        .online-badge ul li { font-size:13px; opacity:.9; }
        .online-badge ul li::before { content:"→ "; }
        /* Compare */
        .compare-table { width:100%; border-collapse:collapse; max-width:820px; margin:0 auto; background:#fff; border-radius:var(--radius); overflow:hidden; border:1px solid var(--border); }
        .compare-table th { padding:14px 20px; font-size:13px; font-weight:700; text-align:left; background:var(--paper); border-bottom:2px solid var(--border); }
        .compare-table th.col-feature { width:38%; }
        .compare-table th.col-cons { background:#f0f9ff; color:var(--primary-dark); }
        .compare-table th.col-clinic { background:#f8fafc; }
        .compare-table td { padding:12px 20px; font-size:13px; color:var(--muted); border-bottom:1px solid var(--border); vertical-align:top; }
        .compare-table td.col-cons { background:#f0f9ff; }
        .compare-table tr:last-child td { border-bottom:none; }
        .compare-table .yes { color:#16a34a; font-weight:700; }
        .compare-table .price-cell { font-size:16px; font-weight:800; color:var(--primary); }
        /* FAQ */
        .faq-list { max-width:720px; margin:0 auto; display:flex; flex-direction:column; gap:12px; }
        .faq-item { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; }
        .faq-q { font-size:15px; font-weight:700; margin-bottom:8px; }
        .faq-a { font-size:14px; color:var(--muted); line-height:1.7; }
        .faq-a a { color:var(--primary-dark); }
        /* CTA */
        .cta-box { background:var(--primary); border-radius:24px; padding:56px 40px; text-align:center; }
        .cta-box h2 { color:#fff; font-size:30px; margin-bottom:12px; }
        .cta-box p { color:rgba(255,255,255,.85); font-size:16px; margin-bottom:28px; }
        .btn-white { display:inline-block; background:#fff; color:var(--primary-dark); padding:14px 32px; border-radius:999px; font-weight:800; font-size:15px; text-decoration:none; }
        .footer { background:#0f172a; color:rgba(255,255,255,.6); padding:32px 0; }
        .footer-inner { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .footer-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:13px; margin-left:20px; }
        .footer-brand { font-size:14px; font-weight:700; color:#fff; }
        @media(max-width:900px) { .hero-inner,.pront-grid,.online-grid { grid-template-columns:1fr; } .features-grid { grid-template-columns:1fr 1fr; } h1 { font-size:30px; } }
        @media(max-width:600px) { .features-grid,.for-who-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>software-para-medicos">Software para Médicos</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Para médicos autônomos</div>
                <h1><em>Sistema para consultório médico</em> online, sem instalação e acessível</h1>
                <p class="hero-text">
                    Organize a agenda, mantenha o prontuário eletrônico dos seus pacientes e controle
                    os atendimentos do consultório — tudo em um programa médico online, acessível de qualquer
                    dispositivo, sem instalar nada no computador.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="plan-highlight">
                <div class="plan-label">Ideal para consultórios</div>
                <div class="plan-name">Plano Solo</div>
                <div class="plan-desc">Para médicos autônomos e consultórios pequenos</div>
                <div class="plan-price">R$ 79<span>/mês</span></div>
                <ul class="plan-features">
                    <li>1 profissional médico</li>
                    <li>2 colaboradores (recepcionista)</li>
                    <li>Pacientes ilimitados</li>
                    <li>Prontuário eletrônico completo</li>
                    <li>Agenda inteligente online</li>
                    <li>Controle de exames e documentos</li>
                    <li>100% online, sem instalação</li>
                    <li>Trial de 30 dias grátis</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:48px;padding-bottom:48px;">
    <div class="wrap">
        <div class="section-label">O que é</div>
        <h2>Sistema para consultório médico: resposta direta</h2>
        <p class="section-sub" style="margin-bottom:32px;">Para quem está avaliando e quer entender rápido o que o produto resolve.</p>
        <div class="answer-box">
            <p>O <strong>sistema para consultório médico</strong> da UTecnologia Saúde é um software médico online que substitui planilhas e cadernos no consultório. Ele centraliza a agenda de consultas, o prontuário eletrônico dos pacientes e o controle de exames solicitados em um único sistema acessível pelo navegador.</p>
            <p>É indicado para <strong>médicos autônomos</strong> que atendem em consultório próprio ou compartilhado e precisam de um programa para consultório médico simples, sem servidor local, sem instalação e sem custo de TI. O plano Solo cobre 1 médico + 2 colaboradores (recepcionista) com pacientes ilimitados por R$ 79/mês.</p>
            <p>Para clínicas com múltiplos médicos, veja o <a href="<?=base_url()?>sistema-para-clinica-medica">sistema para clínica médica</a> com suporte a equipes maiores.</p>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;padding-top:56px;padding-bottom:56px;">
    <div class="wrap">
        <div class="section-label">Para quem serve</div>
        <h2>Quando este sistema é o certo para você</h2>
        <p class="section-sub" style="margin-bottom:36px;">O sistema de consultório médico da UTecnologia é feito para médicos com essa realidade de trabalho.</p>
        <div class="for-who-grid">
            <div class="for-who-item">
                <div class="for-who-check">🩺</div>
                <div><h4>Médico autônomo com consultório próprio</h4><p>Você atende sozinho ou com uma recepcionista e quer sair da planilha sem contratar TI ou servidor.</p></div>
            </div>
            <div class="for-who-item">
                <div class="for-who-check">📋</div>
                <div><h4>Médico que quer prontuário eletrônico</h4><p>Você precisa registrar anamnese, evolução e diagnóstico de forma estruturada e acessível no retorno.</p></div>
            </div>
            <div class="for-who-item">
                <div class="for-who-check">📅</div>
                <div><h4>Consultório com recepcionista agendando</h4><p>Sua recepcionista precisa agendar e confirmar consultas sem ter acesso aos prontuários clínicos.</p></div>
            </div>
            <div class="for-who-item">
                <div class="for-who-check">🔬</div>
                <div><h4>Médico que solicita exames na consulta</h4><p>Você quer registrar os exames solicitados, receber resultados e manter o histórico no prontuário do paciente.</p></div>
            </div>
            <div class="for-who-item">
                <div class="for-who-check">💻</div>
                <div><h4>Especialista que atende em múltiplos locais</h4><p>Você atende em diferentes consultórios ou clínicas e quer acessar o prontuário de qualquer dispositivo.</p></div>
            </div>
            <div class="for-who-item">
                <div class="for-who-check">🏥</div>
                <div><h4>Médico que está crescendo e quer organizar</h4><p>Você aumentou o volume de pacientes e a planilha ou caderno já não dão conta da operação do dia a dia.</p></div>
            </div>
        </div>
        <div class="not-for-box">
            <p><strong>Não é para você se:</strong> você tem uma clínica com 2 ou mais médicos trabalhando em paralelo. Para esse cenário, o <a href="<?=base_url()?>sistema-para-clinica-medica">sistema para clínica médica</a> (planos Clínica ou Pro) tem a estrutura certa — com agenda separada por profissional, controle hierárquico de equipe e relatórios de produção por médico.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos</div>
        <h2>O que o software médico de consultório oferece</h2>
        <p class="section-sub">Tudo que um programa para consultório médico precisa ter — sem complexidade de sistemas hospitalares.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda do Consultório</h3>
                <p>Visualize todos os atendimentos do dia, semana ou mês. Filtre por status (agendado, em atendimento, concluído). Cancele e remarque sem retrabalho. Sua recepcionista agenda online enquanto você atende.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico</h3>
                <p>Registre anamnese, evolução clínica, hipótese diagnóstica e conduta em cada consulta. O histórico completo do paciente fica acessível em qualquer retorno, no computador ou no celular.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Controle de Exames</h3>
                <p>Solicite exames durante a consulta, registre resultados e armazene laudos em PDF diretamente no prontuário do paciente. Sem pastas físicas, sem perder resultado.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Recepcionista no Sistema</h3>
                <p>Cadastre até 2 colaboradores no plano Solo. A recepcionista acessa a agenda e o cadastro de pacientes para agendar e confirmar consultas, sem visualizar os registros clínicos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Arquivos do Paciente</h3>
                <p>Anexe documentos, laudos, receitas e imagens ao prontuário de cada paciente. Tudo armazenado com segurança e acessível na próxima consulta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Dados Isolados por Consultório</h3>
                <p>Os dados do seu consultório são completamente isolados dos demais usuários do sistema. Acesso protegido por login e senha com níveis de permissão separados para médico e recepcionista.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="pront-grid">
            <div>
                <div class="section-label" style="text-align:left">Prontuário eletrônico</div>
                <h2 style="text-align:left">O que o médico registra em cada consulta</h2>
                <p style="color:var(--muted);font-size:16px;margin-top:12px;">O prontuário foi estruturado para o fluxo real de uma consulta médica: do primeiro registro ao histórico de retornos.</p>
                <div class="pront-list">
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Atendimento inicial</h4><p>Queixa principal, histórico da doença atual e contexto clínico do primeiro contato com o paciente.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Avaliação clínica</h4><p>Exame físico, hipóteses diagnósticas, CID e conduta adotada — registrados de forma estruturada a cada consulta.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Evolução e retornos</h4><p>Cada retorno gera um novo registro vinculado ao histórico do paciente. A timeline completa fica disponível em qualquer consulta futura.</p></div></div>
                    <div class="pront-item"><div class="pront-dot"></div><div><h4>Exames e documentos</h4><p>Solicitações de exame, resultados recebidos e arquivos do paciente ficam vinculados ao prontuário e organizados cronologicamente.</p></div></div>
                </div>
            </div>
            <div style="background:#f0f9ff;border-radius:24px;padding:36px;">
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);margin-bottom:20px;">Quanto custa</div>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="background:#fff;border-radius:12px;padding:20px;border:2px solid var(--primary);">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--primary);margin-bottom:4px;">Recomendado para consultório</div>
                        <div style="font-size:18px;font-weight:800;margin-bottom:2px;">Plano Solo</div>
                        <div style="font-size:28px;font-weight:800;color:var(--primary);">R$ 79<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
                        <div style="font-size:13px;color:var(--muted);margin-top:8px;">1 médico · 2 colaboradores · pacientes ilimitados</div>
                    </div>
                    <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid var(--border);">
                        <div style="font-size:13px;font-weight:700;margin-bottom:2px;">Clínica — quando crescer</div>
                        <div style="font-size:22px;font-weight:800;color:var(--primary);">R$ 199<span style="font-size:13px;font-weight:500;color:var(--muted)">/mês</span></div>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">Até 5 médicos · 10 colaboradores</div>
                    </div>
                    <div style="font-size:13px;color:var(--muted);text-align:center;padding-top:4px;">Todos os planos incluem trial de 30 dias grátis.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--paper);">
    <div class="wrap">
        <div class="online-grid">
            <div>
                <div class="section-label" style="text-align:left">Software online</div>
                <h2 style="text-align:left">Sem instalação. Funciona pelo navegador.</h2>
                <p style="color:var(--muted);font-size:16px;margin-top:12px;">O sistema para consultório médico da UTecnologia é 100% online — sem instalação em computador, sem servidor local, sem licença de TI.</p>
                <div class="online-list">
                    <div class="online-item">
                        <div class="online-icon">🌐</div>
                        <div><h4>Acesso pelo navegador</h4><p>Abra no Chrome, Firefox ou Safari — no computador do consultório, no notebook em casa ou no celular entre atendimentos.</p></div>
                    </div>
                    <div class="online-item">
                        <div class="online-icon">☁️</div>
                        <div><h4>Dados na nuvem, com backup automático</h4><p>O prontuário dos seus pacientes fica armazenado com segurança. Sem risco de perda por falha no computador local.</p></div>
                    </div>
                    <div class="online-item">
                        <div class="online-icon">🔄</div>
                        <div><h4>Atualizações automáticas</h4><p>Você sempre usa a versão mais recente do sistema médico online sem precisar instalar nada ou acionar suporte técnico.</p></div>
                    </div>
                    <div class="online-item">
                        <div class="online-icon">📱</div>
                        <div><h4>Multi-dispositivo</h4><p>Inicie um atendimento no computador do consultório e acesse o histórico do paciente pelo celular em outro local.</p></div>
                    </div>
                </div>
            </div>
            <div class="online-badge">
                <h3>Por que isso importa para o consultório</h3>
                <p>Médicos autônomos não têm time de TI. Um sistema de consultório médico na nuvem elimina esse problema:</p>
                <ul>
                    <li>Sem instalar programa no computador</li>
                    <li>Sem servidor local ou HD externo para backup</li>
                    <li>Sem pagar técnico para atualizar ou manter</li>
                    <li>Sem perder dados se o computador pifar</li>
                    <li>Acesso imediato de qualquer lugar</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Consultório vs Clínica</div>
        <h2>Qual sistema é o certo para o seu caso?</h2>
        <p class="section-sub" style="margin-bottom:36px;">A diferença prática entre o sistema para consultório médico e o sistema para clínica médica.</p>
        <table class="compare-table">
            <thead>
                <tr>
                    <th class="col-feature">Característica</th>
                    <th class="col-cons">Consultório (Plano Solo)</th>
                    <th class="col-clinic">Clínica Médica (Plano Clínica / Pro)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Número de médicos</td>
                    <td class="col-cons"><strong>1 médico</strong></td>
                    <td>2 a 20 médicos</td>
                </tr>
                <tr>
                    <td>Colaboradores</td>
                    <td class="col-cons">Até 2 (ex: recepcionista)</td>
                    <td>Até 10 a 50 colaboradores</td>
                </tr>
                <tr>
                    <td>Pacientes</td>
                    <td class="col-cons">Ilimitados</td>
                    <td>Ilimitados</td>
                </tr>
                <tr>
                    <td>Agenda separada por profissional</td>
                    <td class="col-cons">— (agenda única do consultório)</td>
                    <td><span class="yes">✓</span> Cada médico tem sua agenda</td>
                </tr>
                <tr>
                    <td>Prontuário eletrônico</td>
                    <td class="col-cons"><span class="yes">✓</span> Completo</td>
                    <td><span class="yes">✓</span> Completo</td>
                </tr>
                <tr>
                    <td>Controle de exames</td>
                    <td class="col-cons"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Relatórios de produção por médico</td>
                    <td class="col-cons">—</td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Preço mensal</td>
                    <td class="col-cons"><span class="price-cell">R$ 79</span></td>
                    <td><span class="price-cell">R$ 199 / R$ 399</span></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--muted);">Precisa de estrutura de clínica? Veja o <a href="<?=base_url()?>sistema-para-clinica-medica">sistema para clínica médica</a>.</p>
    </div>
</section>

<section class="section" style="background:var(--paper);">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para consultório médico</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é adequado para médico que trabalha sozinho no consultório?</div>
                <div class="faq-a">Sim. O plano Solo foi criado exatamente para isso: 1 médico, até 2 colaboradores (recepcionista ou assistente) e pacientes ilimitados por R$ 79/mês. Você não precisa de um sistema corporativo para ter agenda e prontuário organizados.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso instalar algum programa no computador do consultório?</div>
                <div class="faq-a">Não. O sistema é 100% online — um programa médico acessível por qualquer navegador, no computador do consultório, no tablet ou no celular. Não requer instalação, servidor local ou manutenção técnica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Minha recepcionista pode agendar consultas pelo sistema?</div>
                <div class="faq-a">Sim. Você cadastra a recepcionista como colaboradora. Ela tem acesso à agenda e ao cadastro de pacientes para agendar, confirmar e cancelar consultas, sem visualizar os prontuários clínicos — que ficam restritos ao médico responsável.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema tem período gratuito para testar?</div>
                <div class="faq-a">Sim. O trial é de 30 dias grátis, sem necessidade de cartão de crédito para começar. Você configura o consultório, cadastra pacientes e usa o prontuário sem compromisso. Após o período de teste, escolhe o plano que faz sentido para o seu consultório.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Qual a diferença entre este sistema e o sistema para clínica médica?</div>
                <div class="faq-a">O sistema para consultório médico (plano Solo) foi pensado para 1 médico com até 2 colaboradores. O <a href="<?=base_url()?>sistema-para-clinica-medica">sistema para clínica médica</a> suporta de 2 a 20 médicos, com agenda separada por profissional, relatórios de produção por médico e controle hierárquico de equipe. Se você está crescendo e vai incluir mais médicos, o plano Clínica (R$ 199/mês) é a evolução natural sem perda de dados.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Vale a pena migrar da planilha para um sistema de consultório médico?</div>
                <div class="faq-a">Para a maioria dos médicos autônomos, sim — especialmente quando o volume de pacientes ultrapassa 30–40 ativos. Os ganhos práticos são: histórico de prontuário acessível em qualquer retorno, controle de exames vinculado ao paciente, agenda centralizada para a recepcionista e backup automático sem depender de HD externo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">E se eu crescer e precisar adicionar mais médicos depois?</div>
                <div class="faq-a">Basta migrar para o plano Clínica (R$ 199/mês, até 5 médicos) ou Pro (R$ 399/mês, até 20 médicos). Todos os dados do consultório são preservados na migração — pacientes, prontuários, histórico de atendimentos e exames.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Comece a organizar seu consultório hoje</h2>
            <p>30 dias grátis para testar o sistema, cadastrar pacientes e usar o prontuário.<br>Plano Solo a partir de R$ 79/mês. Sem cartão de crédito para o trial.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-clinicas" class="footer-links">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica" class="footer-links">Clínica Médica</a>
                <a href="<?=base_url()?>software-para-medicos" class="footer-links">Software para Médicos</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico" class="footer-links">Prontuário</a>
                <a href="<?=base_url()?>experimentar" class="footer-links">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "UTecnologia Saúde — Sistema para Consultório Médico",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/sistema-para-consultorio-medico",
  "description": "Sistema para consultório médico com agenda online, prontuário eletrônico e controle de pacientes. Software médico 100% online para médicos autônomos.",
  "offers": {"@type": "Offer", "price": "79", "priceCurrency": "BRL", "priceValidUntil": "2027-12-31"}
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
    {"@type": "ListItem", "position": 3, "name": "Sistema para Consultório Médico", "item": "https://utecnologia.com.br/sistema-para-consultorio-medico"}
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
      "name": "O sistema para consultório médico é adequado para médico que trabalha sozinho?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo foi criado para isso: 1 médico, até 2 colaboradores e pacientes ilimitados por R$ 79/mês. Não é preciso um sistema corporativo para ter agenda e prontuário organizados."}
    },
    {
      "@type": "Question",
      "name": "Preciso instalar algum programa no computador do consultório?",
      "acceptedAnswer": {"@type": "Answer", "text": "Não. O sistema é 100% online, acessível por qualquer navegador — sem instalação, servidor local ou manutenção técnica."}
    },
    {
      "@type": "Question",
      "name": "O sistema tem período gratuito para testar?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O trial é de 30 dias grátis, sem necessidade de cartão de crédito. Você configura o consultório, cadastra pacientes e usa o prontuário sem compromisso."}
    },
    {
      "@type": "Question",
      "name": "Qual a diferença entre sistema para consultório médico e sistema para clínica médica?",
      "acceptedAnswer": {"@type": "Answer", "text": "O sistema para consultório (plano Solo) foi pensado para 1 médico com até 2 colaboradores. O sistema para clínica médica suporta de 2 a 20 médicos, com agenda separada por profissional e relatórios de produção por médico."}
    },
    {
      "@type": "Question",
      "name": "Vale a pena migrar da planilha para um sistema de consultório médico?",
      "acceptedAnswer": {"@type": "Answer", "text": "Para a maioria dos médicos autônomos, sim — especialmente quando o volume de pacientes ultrapassa 30 a 40 ativos. Os ganhos são: prontuário acessível em qualquer retorno, controle de exames, agenda centralizada e backup automático."}
    },
    {
      "@type": "Question",
      "name": "E se eu crescer e precisar adicionar mais médicos depois?",
      "acceptedAnswer": {"@type": "Answer", "text": "Basta migrar para o plano Clínica (R$ 199/mês, até 5 médicos) ou Pro (R$ 399/mês, até 20 médicos). Todos os dados são preservados na migração."}
    }
  ]
}
</script>
</body>
</html>
