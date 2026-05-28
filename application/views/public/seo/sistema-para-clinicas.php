<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Clínicas — Gestão Completa com Prontuário e Agenda | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para clínicas com prontuário eletrônico, agenda inteligente e gestão de pacientes. Para médicos, psicólogos, fisioterapeutas e dentistas. Teste grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinicas">
    <meta property="og:title" content="Sistema para Clínicas — UTecnologia Saúde">
    <meta property="og:description" content="Sistema para clínicas com prontuário eletrônico, agenda inteligente e gestão de pacientes. Teste grátis por 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Clínicas — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico, agenda e gestão de pacientes. Teste grátis 30 dias.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #0ea5e9; --primary-dark: #0284c7;
            --accent: #22c55e; --border: #e2e8f0;
            --paper: #f8fafc; --panel: #ffffff;
            --radius: 16px; --shadow: 0 4px 24px rgba(15,23,42,.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: var(--ink); background: var(--paper); line-height: 1.6; }
        a { color: var(--primary-dark); }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

        /* NAV */
        .topnav { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 0; }
        .topnav .wrap { display: flex; justify-content: space-between; align-items: center; }
        .brand { font-size: 17px; font-weight: 800; color: var(--ink); text-decoration: none; }
        .brand span { color: var(--primary); }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--muted); text-decoration: none; }
        .btn-nav { background: var(--primary); color: #fff !important; padding: 8px 18px; border-radius: 999px; font-weight: 700 !important; font-size: 13px !important; }

        /* HERO */
        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #f0f9ff 0%, #f8fafc 60%); }
        .hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .eyebrow { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); margin-bottom: 12px; }
        h1 { font-size: 42px; font-weight: 800; line-height: 1.12; color: var(--ink); margin-bottom: 20px; }
        h1 em { font-style: normal; color: var(--primary); }
        .hero-text { font-size: 18px; color: var(--muted); line-height: 1.7; margin-bottom: 32px; }
        .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-primary { display: inline-block; background: var(--primary); color: #fff; padding: 14px 28px; border-radius: 999px; font-weight: 700; font-size: 15px; text-decoration: none; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline { display: inline-block; border: 1.5px solid var(--border); color: var(--muted); padding: 13px 24px; border-radius: 999px; font-weight: 600; font-size: 14px; text-decoration: none; }
        .hero-badge { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow); }
        .hero-badge .badge-title { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 16px; }
        .badge-list { display: flex; flex-direction: column; gap: 10px; }
        .badge-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: var(--ink); }
        .badge-icon { width: 28px; height: 28px; background: #f0fdf4; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        /* FEATURES */
        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        /* SPECIALTIES */
        .specialties { background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 100%); color: #fff; }
        .specialties h2 { color: #fff; }
        .specialties .section-sub { color: rgba(255,255,255,.7); }
        .spec-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .spec-card { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); border-radius: var(--radius); padding: 20px; }
        .spec-icon { font-size: 24px; margin-bottom: 10px; }
        .spec-card h3 { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .spec-card p { font-size: 12px; color: rgba(255,255,255,.6); line-height: 1.5; }

        /* STEPS */
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .step { text-align: center; }
        .step-num { width: 48px; height: 48px; background: var(--primary); color: #fff; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; margin: 0 auto 16px; }
        .step h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: 14px; color: var(--muted); }

        /* FAQ */
        .faq-list { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        .faq-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; }
        .faq-q { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .faq-a { font-size: 14px; color: var(--muted); line-height: 1.7; }

        /* CTA */
        .cta-box { background: var(--primary); border-radius: 24px; padding: 56px 40px; text-align: center; }
        .cta-box h2 { color: #fff; font-size: 30px; margin-bottom: 12px; }
        .cta-box p { color: rgba(255,255,255,.85); font-size: 16px; margin-bottom: 28px; }
        .btn-white { display: inline-block; background: #fff; color: var(--primary-dark); padding: 14px 32px; border-radius: 999px; font-weight: 800; font-size: 15px; text-decoration: none; }

        /* FOOTER */
        .footer { background: var(--ink); color: rgba(255,255,255,.6); padding: 32px 0; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-links a { color: rgba(255,255,255,.6); text-decoration: none; font-size: 13px; margin-left: 20px; }
        .footer-brand { font-size: 14px; font-weight: 700; color: #fff; }

        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .spec-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .spec-grid { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>">Início</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
            <a href="<?=base_url()?>alternativa-feegow">Comparar</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema para Clínicas</div>
                <h1>O <em>sistema para clínicas</em> que organiza sua rotina clínica do começo ao fim</h1>
                <p class="hero-text">
                    Prontuário eletrônico, agenda inteligente e gestão completa de pacientes em um único sistema online.
                    Para médicos, psicólogos, dentistas, fisioterapeutas e terapeutas.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O que está incluído</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário eletrônico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda inteligente por profissional</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Exames e documentos integrados</div>
                    <div class="badge-item"><div class="badge-icon">📊</div> Relatórios e gestão</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Multi-profissionais na mesma clínica</div>
                    <div class="badge-item"><div class="badge-icon">🔒</div> Acesso seguro por perfil</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que uma clínica precisa para funcionar bem</h2>
        <p class="section-sub">Sem planilhas, sem papel, sem sistemas separados. Uma plataforma que centraliza o que importa.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico</h3>
                <p>Anamnese, evolução clínica, hipóteses diagnósticas e histórico completo do paciente em um registro organizado e acessível.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda por Profissional</h3>
                <p>Visualize e gerencie todos os agendamentos da clínica. Filtre por data, status e profissional. Cancele e remarque com um clique.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Documentos</h3>
                <p>Solicite exames, registre resultados e armazene arquivos diretamente no prontuário do paciente. Tudo rastreado e organizado.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe Hierárquica</h3>
                <p>Cadastre estabelecimentos, prestadores, colaboradores e pacientes. Cada um com acesso apenas ao que é relevante para seu papel.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios Clínicos</h3>
                <p>Acompanhe atendimentos por profissional, período e tipo. Dados para gestão e tomada de decisão rápida.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% Online</h3>
                <p>Acesse de qualquer dispositivo com internet. Sem instalação, sem servidor local, sem backup manual. Seus dados sempre disponíveis.</p>
            </div>
        </div>
    </div>
</section>

<section class="section specialties">
    <div class="wrap">
        <div class="section-label" style="color:rgba(255,255,255,.7)">Especialidades atendidas</div>
        <h2>Para qualquer clínica ou consultório de saúde</h2>
        <p class="section-sub">O sistema funciona para todas as especialidades que utilizam prontuário, agenda e controle de pacientes.</p>
        <div class="spec-grid">
            <div class="spec-card"><div class="spec-icon">🩺</div><h3>Clínica Médica</h3><p>Clínico geral e especialistas</p></div>
            <div class="spec-card"><div class="spec-icon">🧠</div><h3>Psicologia</h3><p>Consultórios e clínicas de psicologia</p></div>
            <div class="spec-card"><div class="spec-icon">🦷</div><h3>Odontologia</h3><p>Dentistas e clínicas odontológicas</p></div>
            <div class="spec-card"><div class="spec-icon">🏃</div><h3>Fisioterapia</h3><p>Clínicas e consultórios de fisio</p></div>
            <div class="spec-card"><div class="spec-icon">🌿</div><h3>Nutrição</h3><p>Nutricionistas e clínicas de nutrição</p></div>
            <div class="spec-card"><div class="spec-icon">👁️</div><h3>Oftalmologia</h3><p>Clínicas oftalmológicas</p></div>
            <div class="spec-card"><div class="spec-icon">🗣️</div><h3>Fonoaudiologia</h3><p>Fonoaudiólogos e clínicas de fono</p></div>
            <div class="spec-card"><div class="spec-icon">💆</div><h3>Outras Terapias</h3><p>Terapeutas e outras especialidades</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Como funciona</div>
        <h2>Comece a usar em 3 passos</h2>
        <p class="section-sub">Sem instalação, sem contrato. Do cadastro ao primeiro atendimento registrado em minutos.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie sua conta grátis</h3>
                <p>Preencha o formulário com o nome da sua clínica e e-mail. Nenhum cartão de crédito necessário.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre sua equipe</h3>
                <p>Adicione profissionais, colaboradores e comece a cadastrar seus pacientes no sistema.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias grátis</h3>
                <p>Agenda, prontuários e exames disponíveis imediatamente. Depois, escolha o plano que se encaixa na sua clínica.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para clínicas</h2>
        <p class="section-sub" style="margin-bottom: 40px;">Respostas para as perguntas mais comuns.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O que é um sistema para clínicas?</div>
                <div class="faq-a">Um sistema para clínicas é um software online que centraliza o gerenciamento da clínica: prontuário eletrônico dos pacientes, agenda de atendimentos, controle de exames, cadastro de profissionais e relatórios. Substitui planilhas, papel e sistemas isolados por uma plataforma integrada acessível de qualquer dispositivo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O UTecnologia Saúde funciona para qualquer especialidade médica?</div>
                <div class="faq-a">Sim. O sistema foi projetado para clínicas e consultórios que utilizam prontuário, agenda e controle de pacientes, independentemente da especialidade. Funciona para médicos clínicos gerais, psicólogos, dentistas, fisioterapeutas, nutricionistas, oftalmologistas, fonoaudiólogos, terapeutas e outras especialidades.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso ter mais de um profissional no mesmo sistema?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde é multi-profissional. Você pode cadastrar vários prestadores de saúde na mesma clínica, cada um com sua própria agenda e acesso ao prontuário dos seus pacientes. Os planos variam de 1 profissional (Solo) até 20+ (Pro e Enterprise).</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Os dados dos pacientes ficam seguros?</div>
                <div class="faq-a">Sim. Cada clínica tem seu próprio ambiente isolado (tenant). O acesso ao sistema é protegido por login e senha, e cada usuário vê apenas os dados correspondentes ao seu perfil. Os dados são armazenados com segurança e não são compartilhados entre clínicas.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso instalar algum programa?</div>
                <div class="faq-a">Não. O UTecnologia Saúde é um sistema 100% online (SaaS). Basta ter acesso à internet e um navegador — computador, tablet ou celular. Não há instalação, servidor local ou backup manual necessário.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como funciona o trial gratuito?</div>
                <div class="faq-a">Você cria sua conta gratuitamente e tem acesso completo ao sistema por 30 dias, sem precisar de cartão de crédito. Após o período de trial, você escolhe o plano que melhor se encaixa no tamanho da sua clínica.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Pronto para organizar sua clínica?</h2>
            <p>Crie sua conta agora e experimente todos os recursos por 30 dias completamente grátis.<br>Sem cartão de crédito. Sem compromisso.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>">Início</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
                <a href="<?=base_url()?>sistema-para-dentistas">Dentistas</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<!-- Schema JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sistema para Clínicas — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-para-clinicas",
  "description": "Sistema para clínicas com prontuário eletrônico, agenda inteligente e gestão de pacientes.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"}
    ]
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "O que é um sistema para clínicas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Um sistema para clínicas é um software online que centraliza prontuário eletrônico, agenda de atendimentos, controle de exames e gestão de profissionais em uma única plataforma acessível de qualquer dispositivo."}
    },
    {
      "@type": "Question",
      "name": "O UTecnologia Saúde funciona para qualquer especialidade médica?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. Funciona para médicos, psicólogos, dentistas, fisioterapeutas, nutricionistas, oftalmologistas, fonoaudiólogos, terapeutas e outras especialidades que utilizam prontuário, agenda e controle de pacientes."}
    },
    {
      "@type": "Question",
      "name": "Como funciona o trial gratuito?",
      "acceptedAnswer": {"@type": "Answer", "text": "Você cria sua conta gratuitamente e tem acesso completo ao sistema por 30 dias, sem precisar de cartão de crédito. Após o trial, escolhe o plano que melhor se encaixa na sua clínica."}
    }
  ]
}
</script>
</body>
</html>

