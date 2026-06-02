<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuário para Oftalmologista | Sistema para Clínica Oftalmológica | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia, pressão intraocular e laudos de exames. Sistema online para clínica oftalmológica. Teste 30 dias grátis.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-para-clinica-oftalmologica">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-para-clinica-oftalmologica">
    <meta property="og:title" content="Prontuário para Oftalmologista | Sistema para Clínica Oftalmológica — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário para oftalmologista: acuidade visual, refração, biomicroscopia, pressão intraocular e laudos. Sistema online. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Prontuário para Oftalmologista | UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia e laudos. Sistema online para clínica. Trial grátis 30 dias.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #4f46e5; --primary-dark: #4338ca;
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

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #eef2ff 0%, #f8fafc 60%); }
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
        .badge-icon { width: 28px; height: 28px; background: #eef2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .prontuario-fields { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 32px; }
        .fields-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; }
        .fields-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .field-item { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--muted); }
        .field-item::before { content: "👁️"; flex-shrink: 0; }

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
            .features-grid, .fields-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid, .fields-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
            <a href="<?=base_url()?>sistema-para-clinicas">Ver tudo</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema para Oftalmologia</div>
                <h1>Sistema para <em>clínica oftalmológica</em> com prontuário e agenda integrados</h1>
                <p class="hero-text">
                    O UTecnologia Saúde organiza o prontuário de cada paciente, a agenda dos oftalmologistas
                    e a gestão da equipe clínica em uma plataforma 100% online. Simples de usar,
                    acessível de qualquer dispositivo, sem instalação.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O sistema inclui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">👁️</div> Prontuário oftalmológico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda por médico</div>
                    <div class="badge-item"><div class="badge-icon">📎</div> Anexo de laudos e exames</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Multi-profissionais por clínica</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> 100% online, sem instalação</div>
                    <div class="badge-item"><div class="badge-icon">💰</div> A partir de R$ 79/mês</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Prontuário para Oftalmologista</div>
        <h2>O que registrar no prontuário para oftalmologista</h2>
        <p class="section-sub">Um prontuário para oftalmologista precisa ir além de texto livre — cada campo deve refletir o exame clínico real da consulta oftalmológica.</p>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="prontuario-fields">
                <div class="fields-title">Campos disponíveis no prontuário oftalmológico:</div>
                <div class="fields-grid">
                    <div class="field-item">Queixa principal e histórico</div>
                    <div class="field-item">Acuidade visual (AO, OD, OE)</div>
                    <div class="field-item">Refração e prescrição óptica</div>
                    <div class="field-item">Biomicroscopia / exame à lâmpada de fenda</div>
                    <div class="field-item">Fundo de olho e retinografia</div>
                    <div class="field-item">Pressão intraocular</div>
                    <div class="field-item">Hipóteses diagnósticas (CID)</div>
                    <div class="field-item">Conduta e prescrição médica</div>
                    <div class="field-item">Retorno e evolução do tratamento</div>
                    <div class="field-item">Anexos: laudos, tomografias, campos visuais</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Funcionalidades</div>
        <h2>Tudo que a clínica oftalmológica precisa no dia a dia</h2>
        <p class="section-sub">Uma plataforma que acompanha o fluxo real do atendimento, da chegada do paciente ao retorno.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda por Oftalmologista</h3>
                <p>Gerencie a agenda de cada médico da clínica. Visualize por dia, semana ou profissional. Cancele e remarque consultas diretamente no sistema.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Laudos</h3>
                <p>Solicite exames, registre resultados e armazene laudos, campos visuais, tomografias e outros documentos no prontuário do paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe Multi-profissional</h3>
                <p>Cadastre todos os oftalmologistas, ortoptistas e colaboradores. Cada um com acesso ao que é relevante ao seu papel na clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios de Atendimento</h3>
                <p>Acompanhe atendimentos por período, por profissional e por tipo. Visibilidade para a gestão da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Dados dos Pacientes Seguros</h3>
                <p>Cada clínica opera em ambiente isolado. Os prontuários dos seus pacientes são exclusivos da sua clínica. Acesso sempre por login e perfil.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% Online</h3>
                <p>Acesse o sistema do consultório, de casa ou do celular. Sem instalação, sem backup manual, sem servidor local para gerenciar.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Como começar</div>
        <h2>Do cadastro ao primeiro atendimento em 3 passos</h2>
        <p class="section-sub">Sem burocracia. Sem instalação. Sem equipe técnica necessária.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie a conta da clínica</h3>
                <p>Preencha o nome da clínica e e-mail. Acesso imediato, sem cartão de crédito e sem compromisso.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre médicos e pacientes</h3>
                <p>Adicione os oftalmologistas da equipe e comece a cadastrar os pacientes e configurar a agenda.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias grátis</h3>
                <p>Prontuários, agendamentos e exames disponíveis imediatamente. Depois, escolha o plano ideal.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o sistema para oftalmologia</h2>
        <p class="section-sub" style="margin-bottom: 40px;">O que os oftalmologistas perguntam antes de adotar o sistema.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema tem campos específicos para oftalmologia?</div>
                <div class="faq-a">Sim. O prontuário pode ser configurado com campos específicos para a especialidade: acuidade visual, refração, biomicroscopia, fundo de olho, pressão intraocular, prescrição óptica e outros campos relevantes ao exame oftalmológico. Além disso, laudos e exames de imagem podem ser anexados diretamente ao prontuário.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">É possível gerenciar a agenda de vários oftalmologistas?</div>
                <div class="faq-a">Sim. O sistema suporta múltiplos profissionais. Cada oftalmologista tem sua própria agenda e seus pacientes. O Plano Clínica comporta até 5 médicos + colaboradores. O Plano Pro suporta até 20 profissionais simultaneamente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso armazenar tomografias e campos visuais no sistema?</div>
                <div class="faq-a">Sim. O sistema permite anexar arquivos de qualquer tipo — imagens, PDFs, laudos de tomografias de coerência óptica (OCT), campos visuais, retinografias — diretamente ao prontuário do paciente, vinculados ao atendimento correspondente.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema funciona em computadores e tablets da clínica?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde é 100% online e responsivo. Funciona em qualquer computador com navegador, tablet ou celular. Não há instalação nem dependência de sistema operacional específico.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Qual o custo para uma clínica com 2 ou 3 oftalmologistas?</div>
                <div class="faq-a">O Plano Clínica, por R$ 199/mês, suporta até 5 profissionais de saúde + 10 colaboradores. Para 1 oftalmologista com consultório próprio, o Plano Solo sai por R$ 79/mês. Você pode testar gratuitamente por 30 dias antes de escolher o plano.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O histórico de pacientes fica disponível entre consultas?</div>
                <div class="faq-a">Sim. Todo o histórico clínico — prontuários, exames, arquivos e evolução — fica armazenado e acessível a qualquer momento. O médico consulta o histórico completo do paciente antes de cada atendimento, de qualquer dispositivo.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Organize sua clínica oftalmológica agora</h2>
            <p>Crie sua conta e experimente todos os recursos por 30 dias completamente grátis.<br>Sem cartão de crédito. Sem contrato.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-clinica-medica">Clínica Médica</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
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
  "url": "https://utecnologia.com.br/sistema-para-clinica-oftalmologica",
  "description": "Prontuário para oftalmologista com acuidade visual, refração, biomicroscopia, pressão intraocular e laudos. Sistema online para clínica oftalmológica.",
  "offers": {
    "@type": "Offer",
    "price": "79",
    "priceCurrency": "BRL"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
    {"@type": "ListItem", "position": 2, "name": "Sistema para Clínicas", "item": "https://utecnologia.com.br/sistema-para-clinicas"},
    {"@type": "ListItem", "position": 3, "name": "Sistema para Clínica Oftalmológica", "item": "https://utecnologia.com.br/sistema-para-clinica-oftalmologica"}
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
      "name": "O sistema tem campos específicos para oftalmologia?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O prontuário para oftalmologista inclui campos para acuidade visual, refração, biomicroscopia, fundo de olho, pressão intraocular e prescrição óptica. Laudos e exames de imagem também podem ser anexados diretamente ao prontuário."}
    },
    {
      "@type": "Question",
      "name": "É possível gerenciar a agenda de vários oftalmologistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema suporta múltiplos profissionais. O Plano Clínica comporta até 5 médicos + colaboradores por R$ 199/mês. O Plano Pro suporta até 20 profissionais."}
    },
    {
      "@type": "Question",
      "name": "Posso armazenar tomografias e campos visuais no sistema?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema permite anexar arquivos de qualquer tipo — imagens, PDFs, laudos de OCT, campos visuais, retinografias — diretamente ao prontuário do paciente, vinculados ao atendimento correspondente."}
    },
    {
      "@type": "Question",
      "name": "O sistema funciona em computadores e tablets da clínica?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O UTecnologia Saúde é 100% online e responsivo. Funciona em qualquer computador com navegador, tablet ou celular. Não há instalação nem dependência de sistema operacional específico."}
    },
    {
      "@type": "Question",
      "name": "Qual o custo para uma clínica com 2 ou 3 oftalmologistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "O Plano Clínica, por R$ 199/mês, suporta até 5 profissionais de saúde + 10 colaboradores. Para 1 oftalmologista com consultório próprio, o Plano Solo sai por R$ 79/mês. Você pode testar gratuitamente por 30 dias antes de escolher o plano."}
    },
    {
      "@type": "Question",
      "name": "O histórico de pacientes fica disponível entre consultas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. Todo o histórico clínico — prontuários, exames, arquivos e evolução — fica armazenado e acessível a qualquer momento. O médico consulta o histórico completo do paciente antes de cada atendimento, de qualquer dispositivo."}
    }
  ]
}
</script>
</body>
</html>

