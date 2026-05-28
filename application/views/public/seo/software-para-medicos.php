<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Software para Médicos — Prontuário e Agenda para Consultórios | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software para médicos com prontuário eletrônico completo, agenda de consultas e gestão de pacientes. Para consultórios e clínicas. Online, sem instalação. Trial grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/software-para-medicos">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/software-para-medicos">
    <meta property="og:title" content="Software para Médicos — UTecnologia Saúde">
    <meta property="og:description" content="Software médico com prontuário eletrônico e agenda de consultas. Para consultórios e clínicas. Trial grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Software para Médicos — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico e agenda para médicos. Consultórios e clínicas. Trial grátis 30 dias.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #0369a1; --primary-dark: #075985;
            --accent: #22c55e; --border: #e2e8f0;
            --paper: #f8fafc; --panel: #ffffff;
            --radius: 16px; --shadow: 0 4px 24px rgba(15,23,42,.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: var(--ink); background: var(--paper); line-height: 1.6; }
        a { color: var(--primary-dark); }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

        .topnav { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 0; }
        .topnav .wrap { display: flex; justify-content: space-between; align-items: center; }
        .brand { font-size: 17px; font-weight: 800; color: var(--ink); text-decoration: none; }
        .brand span { color: var(--primary); }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--muted); text-decoration: none; }
        .btn-nav { background: var(--primary); color: #fff !important; padding: 8px 18px; border-radius: 999px; font-weight: 700 !important; font-size: 13px !important; }

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #e0f2fe 0%, #f8fafc 60%); }
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
        .badge-icon { width: 28px; height: 28px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }

        .personas { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .persona-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .persona-card.highlight { border-color: var(--primary); background: #f0f9ff; }
        .persona-icon { font-size: 32px; margin-bottom: 14px; }
        .persona-card h3 { font-size: 18px; font-weight: 800; margin-bottom: 10px; }
        .persona-card p { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 14px; }
        .persona-plan { font-size: 13px; font-weight: 700; color: var(--primary); background: #e0f2fe; padding: 6px 14px; border-radius: 999px; display: inline-block; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .step { text-align: center; }
        .step-num { width: 48px; height: 48px; background: var(--primary); color: #fff; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; margin: 0 auto 16px; }
        .step h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: 14px; color: var(--muted); }

        .faq-list { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        .faq-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; }
        .faq-q { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .faq-a { font-size: 14px; color: var(--muted); line-height: 1.7; }

        .cta-box { background: var(--primary); border-radius: 24px; padding: 56px 40px; text-align: center; }
        .cta-box h2 { color: #fff; font-size: 30px; margin-bottom: 12px; }
        .cta-box p { color: rgba(255,255,255,.85); font-size: 16px; margin-bottom: 28px; }
        .btn-white { display: inline-block; background: #fff; color: var(--primary-dark); padding: 14px 32px; border-radius: 999px; font-weight: 800; font-size: 15px; text-decoration: none; }

        .footer { background: var(--ink); color: rgba(255,255,255,.6); padding: 32px 0; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-links a { color: rgba(255,255,255,.6); text-decoration: none; font-size: 13px; margin-left: 20px; }
        .footer-brand { font-size: 14px; font-weight: 700; color: #fff; }

        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .features-grid, .personas { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid, .personas { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>sistema-para-consultorio-medico">Consultório</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
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
                    O UTecnologia Saúde é o software médico que organiza sua agenda de consultas,
                    o prontuário eletrônico de cada paciente e a gestão da equipe em uma plataforma única,
                    100% online. Para médico autônomo ou clínica com vários profissionais.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O software médico inclui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário eletrônico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda de consultas e retornos</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Solicitação e registro de exames</div>
                    <div class="badge-item"><div class="badge-icon">📊</div> Relatórios de atendimento</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> Acesso online de qualquer lugar</div>
                    <div class="badge-item"><div class="badge-icon">💰</div> A partir de R$ 79/mês</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Para qual perfil de médico</div>
        <h2>O software certo para cada tipo de consultório médico</h2>
        <p class="section-sub">Do médico autônomo à clínica com equipe — existe um plano para cada situação.</p>
        <div class="personas">
            <div class="persona-card highlight">
                <div class="persona-icon">👨‍⚕️</div>
                <h3>Médico autônomo</h3>
                <p>Você atende sozinho ou com uma secretária. Precisa de prontuário eletrônico organizado, agenda de consultas e acesso ao histórico de cada paciente. Sem pagar por recursos de uma clínica grande que você não vai usar.</p>
                <span class="persona-plan">Plano Solo — R$ 79/mês</span>
            </div>
            <div class="persona-card">
                <div class="persona-icon">🏥</div>
                <h3>Clínica com equipe médica</h3>
                <p>Sua clínica tem 2 ou mais médicos, cada um com sua agenda e seus pacientes. Você precisa de uma visão centralizada da operação, controle de acesso por profissional e relatórios de atendimento.</p>
                <span class="persona-plan">Plano Clínica — R$ 199/mês</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>O que o software médico faz pelo seu consultório</h2>
        <p class="section-sub">Funcionalidades pensadas para o fluxo real de atendimento médico — não para uma lista de marketing.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico</h3>
                <p>Anamnese, queixa principal, histórico, evolução clínica, hipóteses diagnósticas (CID), conduta e prescrição. Registro completo de cada consulta, acessível a qualquer momento.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de Consultas</h3>
                <p>Visualize e gerencie toda a agenda por profissional. Agende consultas, retornos e procedimentos. Cancele e remarque com um clique. Filtre por data, status e médico.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Documentos</h3>
                <p>Solicite exames, registre resultados e armazene laudos, imagens e qualquer arquivo diretamente no prontuário do paciente. Histórico completo de exames por atendimento.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Histórico Completo</h3>
                <p>Todo o histórico do paciente em um único lugar: consultas anteriores, exames, diagnósticos e evolução. Disponível antes de cada atendimento, sem precisar procurar em pastas físicas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios de Atendimento</h3>
                <p>Veja atendimentos por período, profissional e tipo. Dados para entender o volume de consultas e o desempenho do consultório ao longo do tempo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Acesso de Qualquer Lugar</h3>
                <p>Consulte o prontuário do paciente no consultório, em casa ou no celular entre um atendimento e outro. Nenhuma dependência de um computador específico.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Como começar</div>
        <h2>Do cadastro ao primeiro prontuário em minutos</h2>
        <p class="section-sub">Sem equipe técnica, sem instalação, sem contrato burocrático.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie sua conta</h3>
                <p>Preencha nome do consultório e e-mail. Acesso imediato. Sem cartão de crédito, sem compromisso.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Configure e cadastre pacientes</h3>
                <p>Adicione sua equipe, configure a agenda e comece a cadastrar os pacientes para os primeiros atendimentos.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias grátis</h3>
                <p>Prontuários, agenda e relatórios disponíveis de imediato. Depois do trial, escolha o plano ideal para o seu consultório.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o software médico</h2>
        <p class="section-sub" style="margin-bottom: 40px;">O que os médicos perguntam antes de adotar um sistema.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O software funciona para qualquer especialidade médica?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde foi projetado para qualquer especialidade que utilize prontuário, agenda e controle de pacientes: clínico geral, cardiologista, dermatologista, neurologista, ortopedista, oftalmologista, ginecologista, pediatra e outras especialidades. O prontuário é flexível para adaptar os campos à necessidade de cada especialidade.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso instalar o software no computador do consultório?</div>
                <div class="faq-a">Não. O UTecnologia Saúde é um software médico 100% online (SaaS). Você acessa pelo navegador — Chrome, Firefox, Safari — de qualquer computador, tablet ou celular. Sem instalação, sem atualização manual, sem servidor local. Funciona em Windows, Mac, Linux ou qualquer dispositivo com internet.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como funciona o prontuário eletrônico para médicos?</div>
                <div class="faq-a">Cada consulta gera um registro no prontuário do paciente com data, médico responsável e campos estruturados: queixa principal, anamnese, hipóteses diagnósticas (com CID), conduta, prescrição e observações. Os registros ficam armazenados cronologicamente e o médico acessa o histórico completo antes de qualquer atendimento.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">É seguro armazenar prontuários de pacientes em um software online?</div>
                <div class="faq-a">Sim. Cada consultório ou clínica opera em um ambiente isolado no sistema. Os prontuários dos seus pacientes não são compartilhados com outras clínicas. O acesso é protegido por login e senha, com controle de perfil — cada usuário vê apenas o que é relevante à sua função.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O software para médicos tem um plano para consultório de um único profissional?</div>
                <div class="faq-a">Sim. O Plano Solo foi criado para médicos autônomos com consultório próprio: 1 profissional de saúde + 2 colaboradores (secretária, auxiliar), com pacientes ilimitados e acesso completo a prontuário, agenda e relatórios. O custo é R$ 79/mês após o trial de 30 dias.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso usar o software no celular durante a visita a pacientes?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde é responsivo e funciona bem em smartphones. Você pode consultar o prontuário de um paciente, registrar uma evolução ou verificar a agenda diretamente pelo celular, mesmo fora do consultório.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Comece a usar o software médico agora</h2>
            <p>Trial gratuito de 30 dias. Sem cartão de crédito. Sem contrato.<br>Do cadastro ao primeiro prontuário em menos de 10 minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Ativar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
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
  "@type": "WebPage",
  "name": "Software para Médicos — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/software-para-medicos",
  "description": "Software para médicos com prontuário eletrônico completo, agenda de consultas e gestão de pacientes. Para consultórios e clínicas. 100% online.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Software para Clínicas", "item": "https://utecnologia.com.br/software-para-clinicas"},
      {"@type": "ListItem", "position": 3, "name": "Software para Médicos", "item": "https://utecnologia.com.br/software-para-medicos"}
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
      "name": "O software funciona para qualquer especialidade médica?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. Funciona para clínico geral e especialistas: cardiologista, dermatologista, neurologista, ortopedista, oftalmologista, ginecologista, pediatra e outros. O prontuário é flexível para adaptar os campos a cada especialidade."}
    },
    {
      "@type": "Question",
      "name": "Preciso instalar o software no computador do consultório?",
      "acceptedAnswer": {"@type": "Answer", "text": "Não. O UTecnologia Saúde é 100% online. Você acessa pelo navegador de qualquer computador, tablet ou celular. Sem instalação, sem servidor local."}
    },
    {
      "@type": "Question",
      "name": "O software para médicos tem um plano para consultório individual?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O Plano Solo é para médicos autônomos: 1 profissional + 2 colaboradores, pacientes ilimitados, prontuário + agenda + relatórios por R$ 79/mês após o trial de 30 dias."}
    },
    {
      "@type": "Question",
      "name": "Posso usar o software no celular durante a visita a pacientes?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O UTecnologia Saúde é responsivo e funciona em smartphones. Você pode consultar prontuários, registrar evoluções ou verificar a agenda pelo celular."}
    }
  ]
}
</script>
</body>
</html>
