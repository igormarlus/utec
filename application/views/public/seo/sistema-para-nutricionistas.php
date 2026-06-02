<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema para Nutricionistas | Prontuário Nutricional Online | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema para nutricionistas com prontuário nutricional online, anamnese alimentar, agenda de consultas e gestão de pacientes. 100% online. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-nutricionistas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-nutricionistas">
    <meta property="og:title" content="Sistema para Nutricionistas | Prontuário Nutricional Online — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário nutricional online com anamnese alimentar, avaliação antropométrica e plano alimentar. Sistema para nutricionistas. Teste 30 dias grátis.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema para Nutricionistas | Prontuário Nutricional — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário nutricional com anamnese alimentar, IMC e plano alimentar. Sistema para nutricionistas. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #16a34a; --primary-dark: #15803d;
            --accent: #22c55e; --border: #e2e8f0;
            --paper: #f0fdf4; --panel: #ffffff;
            --radius: 16px; --shadow: 0 4px 24px rgba(15,23,42,.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: var(--ink); background: var(--paper); line-height: 1.6; }
        a { color: var(--primary-dark); }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

        .topnav { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 0; }
        .topnav .wrap { display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--muted); text-decoration: none; }
        .btn-nav { background: var(--primary); color: #fff !important; padding: 8px 18px; border-radius: 999px; font-weight: 700 !important; font-size: 13px !important; }

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #dcfce7 0%, #f0fdf4 60%); }
        .hero-inner { display: grid; grid-template-columns: 1.1fr .9fr; gap: 56px; align-items: center; }
        .eyebrow { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); margin-bottom: 12px; }
        h1 { font-size: 42px; font-weight: 800; line-height: 1.12; margin-bottom: 20px; }
        h1 em { font-style: normal; color: var(--primary); }
        .hero-text { font-size: 18px; color: var(--muted); line-height: 1.7; margin-bottom: 18px; }
        .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; }
        .btn-primary { display: inline-block; background: var(--primary); color: #fff; padding: 14px 28px; border-radius: 999px; font-weight: 700; font-size: 15px; text-decoration: none; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline { display: inline-block; border: 1.5px solid var(--border); color: var(--muted); padding: 13px 24px; border-radius: 999px; font-weight: 600; font-size: 14px; text-decoration: none; }

        .hero-badge { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow); }
        .badge-title { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 16px; }
        .badge-list { display: flex; flex-direction: column; gap: 10px; }
        .badge-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: var(--ink); }
        .badge-icon { width: 28px; height: 28px; background: #dcfce7; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 720px; margin: 0 auto 48px; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .prontuario-fields { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 32px; max-width: 820px; margin: 0 auto; }
        .fields-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; }
        .fields-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .field-item { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--muted); }
        .field-item::before { content: "🥗"; flex-shrink: 0; }

        .use-cases { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .use-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .use-card.highlight { border-color: var(--primary); background: #f0fdf4; }
        .use-card h3 { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
        .use-card p { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 14px; }
        .use-chip { display: inline-block; background: #dcfce7; color: var(--primary-dark); font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 999px; }

        .faq-list { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        .faq-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; }
        .faq-q { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .faq-a { font-size: 14px; color: var(--muted); line-height: 1.7; }

        .cta-box { background: var(--primary); border-radius: 24px; padding: 56px 40px; text-align: center; }
        .cta-box h2 { color: #fff; font-size: 30px; margin-bottom: 12px; }
        .cta-box p { color: rgba(255,255,255,.88); font-size: 16px; margin-bottom: 28px; }
        .btn-white { display: inline-block; background: #fff; color: var(--primary-dark); padding: 14px 32px; border-radius: 999px; font-weight: 800; font-size: 15px; text-decoration: none; }

        .footer { background: var(--ink); color: rgba(255,255,255,.6); padding: 32px 0; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-links a { color: rgba(255,255,255,.7); text-decoration: none; font-size: 13px; margin-left: 20px; }
        .footer-brand { font-size: 14px; font-weight: 700; color: #fff; }

        @media (max-width: 900px) {
            .hero-inner, .features-grid, .use-cases { grid-template-columns: 1fr; }
            .fields-grid { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) { .nav-links { display: none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário eletrônico</a>
            <a href="<?=base_url()?>blog">Blog</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema para Nutricionistas</div>
                <h1><em>Prontuário nutricional</em> online e sistema de gestão para nutricionistas</h1>
                <p class="hero-text">
                    O UTecnologia Saúde reúne prontuário nutricional, anamnese alimentar, avaliação antropométrica
                    e agenda de consultas em uma plataforma 100% online — acessível de qualquer dispositivo,
                    sem instalação.
                </p>
                <p class="hero-text">
                    Para nutricionistas autônomos e clínicas de nutrição que querem organizar o atendimento,
                    registrar evoluções e manter o histórico completo de cada paciente em um único lugar.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O sistema inclui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🥗</div> Prontuário nutricional completo</div>
                    <div class="badge-item"><div class="badge-icon">📋</div> Anamnese alimentar estruturada</div>
                    <div class="badge-item"><div class="badge-icon">📏</div> Avaliação antropométrica e IMC</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda de consultas e retornos</div>
                    <div class="badge-item"><div class="badge-icon">📎</div> Anexo de laudos e exames laboratoriais</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> 100% online, sem instalação</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Prontuário Nutricional</div>
        <h2>O que registrar no prontuário nutricional do paciente</h2>
        <p class="section-sub">O CFN exige prontuário atualizado por todos os nutricionistas. O UTecnologia Saúde oferece campos adaptados para o atendimento nutricional em consultório ou clínica.</p>
        <div class="prontuario-fields">
            <div class="fields-title">Campos disponíveis no prontuário nutricional:</div>
            <div class="fields-grid">
                <div class="field-item">Queixa principal e motivo da consulta</div>
                <div class="field-item">Anamnese alimentar (hábitos, preferências, restrições)</div>
                <div class="field-item">Peso, altura, IMC e circunferências</div>
                <div class="field-item">Histórico clínico e comorbidades</div>
                <div class="field-item">Avaliação nutricional e diagnóstico</div>
                <div class="field-item">Conduta: plano alimentar e orientações</div>
                <div class="field-item">Exames laboratoriais e laudos anexados</div>
                <div class="field-item">Evolução por consulta e retorno</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Para quem serve</div>
        <h2>Um sistema de nutrição pensado para a rotina real do consultório</h2>
        <p class="section-sub">Do nutricionista autônomo à clínica multiprofissional, o sistema organiza a operação sem complicar o atendimento.</p>
        <div class="use-cases">
            <div class="use-card highlight">
                <h3>Nutricionista autônomo</h3>
                <p>Ideal para quem atende em consultório próprio ou por hora em clínica. Prontuário nutricional, agenda e histórico de pacientes em um único sistema simples e acessível.</p>
                <span class="use-chip">Plano Solo a partir de R$ 79/mês</span>
            </div>
            <div class="use-card">
                <h3>Clínica de nutrição ou multiprofissional</h3>
                <p>Para clínicas com vários nutricionistas ou com equipe (médicos, psicólogos, fisioterapeutas), cada profissional gerencia seus pacientes com controle de acesso por perfil.</p>
                <span class="use-chip">Plano Clínica e Plano Pro</span>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que o nutricionista precisa no dia a dia</h2>
        <p class="section-sub">Uma plataforma que acompanha o fluxo real do atendimento, da primeira consulta ao acompanhamento de longo prazo.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário nutricional online</h3>
                <p>Registre anamnese alimentar, avaliação nutricional, conduta e evolução do paciente em um histórico cronológico único e acessível.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda de consultas e retornos</h3>
                <p>Organize consultas iniciais, retornos e encaixes. Visualize a agenda por dia ou semana e controle os horários disponíveis com facilidade.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Exames laboratoriais e anexos</h3>
                <p>Solicite exames, registre resultados e armazene laudos de hemograma, bioquímica e outros exames diretamente no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3>Ficha completa do paciente</h3>
                <p>Dados cadastrais, histórico clínico, antecedentes familiares e informações de contato organizados em um perfil completo e de fácil acesso.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Clínica multiprofissional</h3>
                <p>Para clínicas com médicos, nutricionistas e outros profissionais: cada um com seu acesso, seus pacientes e seus prontuários, na mesma plataforma.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% online, sem instalação</h3>
                <p>Acesse do consultório, de casa ou do celular. Sem servidor local, sem backup manual, sem dependência de sistema operacional.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema e prontuário nutricional</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é adequado para nutricionistas autônomos?</div>
                <div class="faq-a">Sim. O plano Solo foi criado para profissionais autônomos: 1 nutricionista, até 2 colaboradores e pacientes ilimitados por R$ 79/mês. Você gerencia agenda e prontuários sem pagar por recursos de clínica grande.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O prontuário nutricional tem campos específicos para nutrição?</div>
                <div class="faq-a">Sim. O prontuário inclui campos para anamnese alimentar, avaliação antropométrica (peso, altura, IMC, circunferências), histórico clínico, conduta nutricional e evolução por consulta. Laudos de exames laboratoriais também podem ser anexados diretamente ao prontuário.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso usar para clínica com vários nutricionistas?</div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 nutricionistas + 10 colaboradores. O plano Pro suporta até 20 profissionais. Cada nutricionista gerencia seus próprios pacientes, com isolamento de dados entre profissionais da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema funciona para clínica multiprofissional com médico e nutricionista?</div>
                <div class="faq-a">Sim. A plataforma suporta equipes com diferentes especialidades no mesmo ambiente. Médicos, nutricionistas e outros profissionais podem trabalhar com seus próprios prontuários, agendas e pacientes dentro da mesma clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema atende a exigência de prontuário do CFN?</div>
                <div class="faq-a">O UTecnologia Saúde oferece um prontuário nutricional online estruturado para registrar o histórico completo do paciente — anamnese, avaliação, conduta e evolução — atendendo as boas práticas de documentação exigidas pelo Conselho Federal de Nutricionistas. Para necessidades específicas de conformidade, recomendamos verificar com o seu conselho regional.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Funciona no celular durante a consulta?</div>
                <div class="faq-a">Sim. O sistema é 100% online e responsivo. Você pode acessar prontuários, registrar evoluções e consultar o histórico do paciente pelo celular, tablet ou computador, em qualquer lugar com internet.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Organize seus atendimentos nutricionais</h2>
            <p>Prontuário nutricional online, agenda e gestão de pacientes por 30 dias grátis.<br>Sem cartão de crédito. Sem instalação. Começa em minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-clinicas">Todas as especialidades</a>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-para-psicologos">Psicólogos</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário eletrônico</a>
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
  "url": "https://utecnologia.com.br/sistema-para-nutricionistas",
  "description": "Sistema para nutricionistas com prontuário nutricional online, anamnese alimentar, avaliação antropométrica e agenda de consultas.",
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
    {"@type": "ListItem", "position": 3, "name": "Sistema para Nutricionistas", "item": "https://utecnologia.com.br/sistema-para-nutricionistas"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "O sistema é adequado para nutricionistas autônomos?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Solo é ideal para nutricionistas autônomos: 1 profissional, 2 colaboradores e pacientes ilimitados por R$ 79/mês."}},
    {"@type": "Question", "name": "O prontuário nutricional tem campos específicos para nutrição?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O prontuário inclui campos para anamnese alimentar, avaliação antropométrica (peso, altura, IMC, circunferências), histórico clínico, conduta nutricional e evolução por consulta. Laudos de exames laboratoriais também podem ser anexados."}},
    {"@type": "Question", "name": "Posso usar para clínica com vários nutricionistas?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O plano Clínica suporta até 5 profissionais + 10 colaboradores. O plano Pro suporta até 20 profissionais. Cada nutricionista gerencia seus próprios pacientes com isolamento de dados."}},
    {"@type": "Question", "name": "O sistema atende a exigência de prontuário do CFN?", "acceptedAnswer": {"@type": "Answer", "text": "O UTecnologia Saúde oferece prontuário nutricional estruturado para registrar anamnese, avaliação, conduta e evolução — atendendo as boas práticas de documentação exigidas pelo CFN. Para necessidades específicas, recomendamos verificar com o conselho regional."}},
    {"@type": "Question", "name": "Funciona no celular durante a consulta?", "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema é 100% online e responsivo. Você acessa prontuários e registra evoluções pelo celular, tablet ou computador, de qualquer lugar com internet."}}
  ]
}
</script>
</body>
</html>
