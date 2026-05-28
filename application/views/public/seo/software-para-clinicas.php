<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Software para Clínicas — Prontuário, Agenda e Gestão Online | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software médico para clínicas e consultórios com prontuário eletrônico, agenda por profissional e gestão completa. 100% online, sem instalação. Teste grátis 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/software-para-clinicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/software-para-clinicas">
    <meta property="og:title" content="Software para Clínicas — UTecnologia Saúde">
    <meta property="og:description" content="Software médico para clínicas: prontuário eletrônico, agenda e gestão 100% online. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Software para Clínicas — UTecnologia Saúde">
    <meta name="twitter:description" content="Software médico online. Prontuário, agenda e gestão. Trial grátis 30 dias.">
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

        .topnav { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 0; }
        .topnav .wrap { display: flex; justify-content: space-between; align-items: center; }
        .brand { font-size: 17px; font-weight: 800; color: var(--ink); text-decoration: none; }
        .brand span { color: var(--primary); }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--muted); text-decoration: none; }
        .btn-nav { background: var(--primary); color: #fff !important; padding: 8px 18px; border-radius: 999px; font-weight: 700 !important; font-size: 13px !important; }

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
        .badge-icon { width: 28px; height: 28px; background: #f0f9ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .specialties { background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 100%); color: #fff; }
        .specialties h2 { color: #fff; }
        .specialties .section-sub { color: rgba(255,255,255,.7); }
        .spec-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .spec-card { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); border-radius: var(--radius); padding: 20px; }
        .spec-icon { font-size: 24px; margin-bottom: 10px; }
        .spec-card h3 { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .spec-card p { font-size: 12px; color: rgba(255,255,255,.6); line-height: 1.5; }

        .saas-pillars { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .pillar { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; border-top: 4px solid var(--primary); }
        .pillar h3 { font-size: 18px; font-weight: 800; margin-bottom: 12px; }
        .pillar p { font-size: 14px; color: var(--muted); line-height: 1.7; }

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
            .features-grid, .saas-pillars, .spec-grid { grid-template-columns: 1fr 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid, .saas-pillars { grid-template-columns: 1fr; }
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
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
            <a href="<?=base_url()?>sistema-gratuito-para-clinicas">Trial grátis</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar agora</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Software para Clínicas</div>
                <h1><em>Software para clínicas</em> com prontuário, agenda e gestão integrados</h1>
                <p class="hero-text">
                    O UTecnologia Saúde é um software médico 100% online que centraliza
                    prontuário eletrônico, agenda de atendimentos e gestão da equipe em uma única plataforma.
                    Para médicos, dentistas, psicólogos, fisioterapeutas e terapeutas.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O software inclui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário eletrônico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda inteligente multi-profissional</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Exames e documentos do paciente</div>
                    <div class="badge-item"><div class="badge-icon">📊</div> Relatórios clínicos e de gestão</div>
                    <div class="badge-item"><div class="badge-icon">🔒</div> Acesso seguro por perfil</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> SaaS — sem instalação local</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">O que diferencia o software</div>
        <h2>Por que um software médico SaaS faz diferença na clínica</h2>
        <p class="section-sub">Software instalado no computador é passado. Software na nuvem é o que clínicas eficientes usam hoje.</p>
        <div class="saas-pillars">
            <div class="pillar">
                <h3>Acesso de qualquer lugar</h3>
                <p>Prontuário, agenda e histórico do paciente acessíveis do computador da clínica, do notebook de casa ou do celular entre um atendimento e outro. Nenhuma dependência de um único dispositivo.</p>
            </div>
            <div class="pillar">
                <h3>Sem manutenção técnica</h3>
                <p>Sem instalar atualizações, sem backup manual, sem servidor que trava. O software é mantido na nuvem e você usa sempre a versão mais recente, com segurança e disponibilidade gerenciadas automaticamente.</p>
            </div>
            <div class="pillar">
                <h3>Múltiplos profissionais simultâneos</h3>
                <p>Todos os profissionais da clínica trabalham no mesmo sistema ao mesmo tempo. Cada um com sua agenda e seus pacientes, sem conflito de dados e sem precisar estar no mesmo computador.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Módulos do software</div>
        <h2>Tudo que o software para clínicas precisa ter</h2>
        <p class="section-sub">Funcionalidades pensadas para o dia a dia clínico — nada a mais, nada a menos.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Eletrônico</h3>
                <p>Anamnese estruturada, evolução clínica, hipóteses diagnósticas e histórico completo. Cada atendimento registrado com data, profissional e observações.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda por Profissional</h3>
                <p>Visualize todos os agendamentos da clínica. Filtre por profissional, data e status. Cancele, remarque e confirme diretamente no sistema.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Arquivos</h3>
                <p>Solicite exames, registre resultados e armazene arquivos de qualquer tipo no prontuário do paciente. Vinculados ao atendimento e acessíveis a qualquer hora.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Gestão de Equipe</h3>
                <p>Estabelecimentos, prestadores, colaboradores e pacientes em hierarquia clara. Cada perfil com acesso ajustado ao seu papel na operação da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios</h3>
                <p>Atendimentos por período, por profissional e por tipo. Dados para a gestão diária e para decisões estratégicas da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>Segurança e Isolamento</h3>
                <p>Cada clínica tem seu ambiente exclusivo no sistema. Os dados dos seus pacientes não são compartilhados. Acesso sempre por login e senha.</p>
            </div>
        </div>
    </div>
</section>

<section class="section specialties">
    <div class="wrap">
        <div class="section-label" style="color:rgba(255,255,255,.7)">Especialidades atendidas</div>
        <h2>Software médico para todas as especialidades clínicas</h2>
        <p class="section-sub">O software funciona para qualquer especialidade que utilize prontuário, agenda e controle de pacientes.</p>
        <div class="spec-grid">
            <div class="spec-card"><div class="spec-icon">🩺</div><h3>Clínica Médica</h3><p>Clínico geral e especialistas</p></div>
            <div class="spec-card"><div class="spec-icon">🧠</div><h3>Psicologia</h3><p>Consultórios e clínicas de psicologia</p></div>
            <div class="spec-card"><div class="spec-icon">🦷</div><h3>Odontologia</h3><p>Dentistas e clínicas odontológicas</p></div>
            <div class="spec-card"><div class="spec-icon">🏃</div><h3>Fisioterapia</h3><p>Clínicas e consultórios de fisio</p></div>
            <div class="spec-card"><div class="spec-icon">🌿</div><h3>Nutrição</h3><p>Nutricionistas e clínicas</p></div>
            <div class="spec-card"><div class="spec-icon">👁️</div><h3>Oftalmologia</h3><p>Clínicas oftalmológicas</p></div>
            <div class="spec-card"><div class="spec-icon">🗣️</div><h3>Fonoaudiologia</h3><p>Fonoaudiólogos e clínicas de fono</p></div>
            <div class="spec-card"><div class="spec-icon">💆</div><h3>Terapias</h3><p>Terapeutas e demais especialidades</p></div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o software para clínicas</h2>
        <p class="section-sub" style="margin-bottom: 40px;">O que clínicas perguntam antes de contratar um software médico.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">Qual a diferença entre "sistema" e "software" para clínicas?</div>
                <div class="faq-a">Na prática, os termos são equivalentes no contexto clínico. "Software para clínicas" e "sistema para clínicas" se referem à mesma categoria de produto: uma plataforma digital que gerencia prontuário, agenda e pacientes. O UTecnologia Saúde é um software SaaS (na nuvem), acessível pelo navegador sem instalação local.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O software funciona sem instalar nada no computador?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde é 100% online (SaaS). Você acessa pelo navegador de qualquer computador, tablet ou celular com internet. Sem instalação, sem atualização manual, sem servidor local. A infraestrutura é toda gerenciada na nuvem.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O software atende clínicas com vários médicos ou profissionais?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde foi projetado para clínicas multi-profissionais. No Plano Clínica são até 5 profissionais + 10 colaboradores; no Plano Pro até 20 profissionais. Cada um com agenda própria e acesso aos seus pacientes.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como o software protege os dados dos pacientes?</div>
                <div class="faq-a">Cada clínica tem um ambiente isolado (tenant). Os dados dos pacientes não são compartilhados entre clínicas. O acesso é protegido por login, senha e perfil de usuário — cada profissional vê apenas o que é relevante para sua função.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">É possível testar o software antes de contratar?</div>
                <div class="faq-a">Sim. O trial de 30 dias é gratuito e sem cartão de crédito. Você usa todas as funcionalidades — prontuário, agenda, relatórios e gestão de equipe — com dados reais. Só assina se gostar.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Qual o custo do software após o trial?</div>
                <div class="faq-a">O Plano Solo custa R$ 79/mês (1 profissional, 2 colaboradores). O Plano Clínica R$ 199/mês (até 5 profissionais). O Plano Pro R$ 399/mês (até 20 profissionais). Sem taxa de adesão nos planos mensais e sem fidelidade contratual.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Teste o software por 30 dias grátis</h2>
            <p>Sem instalação. Sem cartão de crédito. Sem contrato de fidelidade.<br>Crie sua conta agora e comece a usar em minutos.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Testar software grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-clinicas">Sistema para Clínicas</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>sistema-gratuito-para-clinicas">Trial grátis</a>
                <a href="<?=base_url()?>assinar">Planos</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Software para Clínicas — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/software-para-clinicas",
  "description": "Software médico para clínicas e consultórios com prontuário eletrônico, agenda e gestão completa 100% online.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Software para Clínicas", "item": "https://utecnologia.com.br/software-para-clinicas"}
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
      "name": "Qual a diferença entre sistema e software para clínicas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Na prática, os termos são equivalentes. 'Software para clínicas' e 'sistema para clínicas' se referem à mesma categoria de produto: uma plataforma digital para gerenciar prontuário, agenda e pacientes."}
    },
    {
      "@type": "Question",
      "name": "O software funciona sem instalar nada no computador?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O UTecnologia Saúde é 100% online (SaaS). Você acessa pelo navegador de qualquer computador, tablet ou celular com internet. Sem instalação, sem atualização manual, sem servidor local."}
    },
    {
      "@type": "Question",
      "name": "É possível testar o software antes de contratar?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O trial de 30 dias é gratuito e sem cartão de crédito. Você usa todas as funcionalidades com dados reais. Só assina se gostar."}
    }
  ]
}
</script>
</body>
</html>

