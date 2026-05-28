<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema Gratuito para Clínicas — 30 Dias Grátis | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Teste o sistema de gestão clínica gratuitamente por 30 dias. Prontuário eletrônico, agenda e gestão completa. Sem cartão de crédito. Para médicos, dentistas, psicólogos e fisioterapeutas.">
    <link rel="canonical" href="https://utecnologia.com.br/sistema-gratuito-para-clinicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/sistema-gratuito-para-clinicas">
    <meta property="og:title" content="Sistema Gratuito para Clínicas — UTecnologia Saúde">
    <meta property="og:description" content="Teste grátis por 30 dias. Prontuário, agenda e gestão. Sem cartão de crédito.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema Gratuito para Clínicas — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico e agenda. Teste 30 dias grátis, sem cartão de crédito.">
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

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #f0fdf4 0%, #f8fafc 60%); }
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

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }

        .trial-box { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 2px solid #86efac; border-radius: 24px; padding: 48px 40px; text-align: center; margin-bottom: 48px; }
        .trial-number { font-size: 96px; font-weight: 900; color: var(--primary); line-height: 1; }
        .trial-unit { font-size: 24px; font-weight: 700; color: var(--primary-dark); margin-bottom: 12px; }
        .trial-desc { font-size: 18px; color: var(--muted); margin-bottom: 8px; }
        .trial-sub { font-size: 14px; color: var(--subtle); }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .price-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; text-align: center; }
        .price-card.featured { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(22,163,74,.15); }
        .plan-name { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .plan-price { font-size: 36px; font-weight: 900; color: var(--primary); margin-bottom: 4px; }
        .plan-price sup { font-size: 18px; }
        .plan-price span { font-size: 14px; font-weight: 500; color: var(--muted); }
        .plan-features { list-style: none; text-align: left; margin: 16px 0 24px; display: flex; flex-direction: column; gap: 8px; }
        .plan-features li { font-size: 13px; color: var(--muted); display: flex; gap: 8px; align-items: flex-start; }
        .plan-features li::before { content: "✔"; color: var(--primary); font-weight: 700; flex-shrink: 0; }
        .btn-plan { display: block; background: var(--primary); color: #fff; padding: 12px; border-radius: 999px; font-weight: 700; font-size: 14px; text-decoration: none; text-align: center; }
        .btn-plan-outline { background: transparent; border: 1.5px solid var(--primary); color: var(--primary-dark); }

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
            .features-grid, .pricing-grid { grid-template-columns: 1fr 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid, .pricing-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .trial-number { font-size: 72px; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
            <a href="<?=base_url()?>assinar">Ver planos</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Sistema gratuito para clínicas</div>
                <h1>Sistema de gestão clínica <em>grátis por 30 dias</em> — sem cartão de crédito</h1>
                <p class="hero-text">
                    Prontuário eletrônico, agenda inteligente e gestão completa da sua clínica ou consultório.
                    Teste tudo sem pagar nada e sem comprometer cartão. Se gostar, escolha o plano.
                    Se não gostar, não precisa nem avisar.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Ativar meus 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos pagos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">No trial gratuito você tem acesso a</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário eletrônico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda por profissional</div>
                    <div class="badge-item"><div class="badge-icon">🔬</div> Exames e documentos</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Cadastro de equipe e pacientes</div>
                    <div class="badge-item"><div class="badge-icon">📊</div> Relatórios de atendimentos</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> Acesso de qualquer dispositivo</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="trial-box">
            <div class="trial-number">30</div>
            <div class="trial-unit">dias completamente grátis</div>
            <p class="trial-desc">Sem cartão de crédito. Sem fidelidade. Sem limite de funcionalidades durante o trial.</p>
            <p class="trial-sub">Você usa tudo. Só paga se decidir continuar.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Completo</h3>
                <p>Anamnese, evolução clínica, hipóteses diagnósticas e histórico. Funciona para médicos, psicólogos, dentistas, fisioterapeutas e terapeutas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda Inteligente</h3>
                <p>Visualize e gerencie todos os agendamentos por profissional. Cancele e remarque com um clique. Filtre por data e status.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔬</div>
                <h3>Exames e Arquivos</h3>
                <p>Solicite exames, registre resultados e armazene arquivos no prontuário. Radiografias, laudos, documentos — tudo organizado por paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Gestão de Equipe</h3>
                <p>Cadastre profissionais, colaboradores e pacientes. Cada perfil acessa apenas o que é relevante ao seu papel na clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios</h3>
                <p>Acompanhe atendimentos por período e profissional. Dados para gestão e tomada de decisão na sua clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>100% Online</h3>
                <p>Sem instalação, sem servidor local. Acesse do computador, tablet ou celular. Seus dados disponíveis de qualquer lugar.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Após o trial</div>
        <h2>Planos acessíveis para qualquer tamanho de clínica</h2>
        <p class="section-sub">Se gostar dos 30 dias gratuitos, escolha o plano que se encaixa na sua realidade.</p>
        <div class="pricing-grid">
            <div class="price-card">
                <div class="plan-name">Solo</div>
                <div class="plan-price"><sup>R$</sup>79<span>/mês</span></div>
                <ul class="plan-features">
                    <li>1 profissional de saúde</li>
                    <li>2 colaboradores</li>
                    <li>Pacientes ilimitados</li>
                    <li>Prontuário + agenda + exames</li>
                </ul>
                <a href="<?=base_url()?>experimentar" class="btn-plan btn-plan-outline">Testar grátis</a>
            </div>
            <div class="price-card featured">
                <div class="plan-name">Clínica</div>
                <div class="plan-price"><sup>R$</sup>199<span>/mês</span></div>
                <ul class="plan-features">
                    <li>Até 5 profissionais</li>
                    <li>10 colaboradores</li>
                    <li>Pacientes ilimitados</li>
                    <li>Prontuário + agenda + exames</li>
                    <li>Relatórios avançados</li>
                </ul>
                <a href="<?=base_url()?>experimentar" class="btn-plan">Testar grátis</a>
            </div>
            <div class="price-card">
                <div class="plan-name">Pro</div>
                <div class="plan-price"><sup>R$</sup>399<span>/mês</span></div>
                <ul class="plan-features">
                    <li>Até 20 profissionais</li>
                    <li>50 colaboradores</li>
                    <li>Pacientes ilimitados</li>
                    <li>Todos os recursos</li>
                </ul>
                <a href="<?=base_url()?>experimentar" class="btn-plan btn-plan-outline">Testar grátis</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre o trial gratuito</h2>
        <p class="section-sub" style="margin-bottom: 40px;">Tudo que você precisa saber antes de criar sua conta.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O sistema é realmente gratuito?</div>
                <div class="faq-a">O trial de 30 dias é completamente gratuito. Você usa todas as funcionalidades sem pagar nada e sem precisar de cartão de crédito. Após os 30 dias, o sistema continua disponível se você escolher um plano pago — a partir de R$ 79/mês. Se não continuar, não há cobrança.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Existe um sistema médico totalmente gratuito para sempre?</div>
                <div class="faq-a">Sistemas clínicos profissionais têm custo de infraestrutura e manutenção. O UTecnologia Saúde oferece o menor custo do segmento — R$ 79/mês no plano básico — com o trial de 30 dias para você avaliar sem risco. Soluções "grátis para sempre" geralmente têm limitações severas de dados, suporte ou segurança que comprometem a operação clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso de cartão de crédito para ativar o trial?</div>
                <div class="faq-a">Não. O trial de 30 dias é ativado apenas com e-mail e nome da clínica. Nenhum dado de pagamento é solicitado durante o período gratuito. Só após decidir continuar é que você escolhe o plano e informa o método de pagamento.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Quais especialidades podem usar o trial gratuito?</div>
                <div class="faq-a">O sistema funciona para qualquer profissional ou clínica que use prontuário, agenda e controle de pacientes: médicos clínicos gerais, psicólogos, dentistas, fisioterapeutas, nutricionistas, oftalmologistas, fonoaudiólogos, terapeutas e outras especialidades de saúde.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Os dados cadastrados no trial são perdidos?</div>
                <div class="faq-a">Não. Se você optar por continuar com um plano pago ao final dos 30 dias, todos os pacientes, prontuários e agendamentos cadastrados durante o trial são mantidos. Você continua de onde parou.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema funciona para consultório de um único profissional?</div>
                <div class="faq-a">Sim. O Plano Solo foi criado exatamente para consultórios com 1 profissional e até 2 colaboradores, por R$ 79/mês após o trial. É ideal para médico, psicólogo, dentista ou fisioterapeuta autônomo que quer organizar sua agenda e prontuários sem pagar por recursos que não vai usar.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Comece agora — é grátis por 30 dias</h2>
            <p>Sem cartão de crédito. Sem contrato. Sem limite de funcionalidades durante o trial.<br>Se não gostar, não há cobrança e não precisa cancelar.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Ativar meu trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>sistema-para-consultorio-medico">Consultório</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>assinar">Ver planos</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sistema Gratuito para Clínicas — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/sistema-gratuito-para-clinicas",
  "description": "Teste o sistema de gestão clínica gratuitamente por 30 dias. Prontuário eletrônico, agenda e gestão completa. Sem cartão de crédito.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema Gratuito para Clínicas", "item": "https://utecnologia.com.br/sistema-gratuito-para-clinicas"}
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
      "name": "O sistema é realmente gratuito?",
      "acceptedAnswer": {"@type": "Answer", "text": "O trial de 30 dias é completamente gratuito. Você usa todas as funcionalidades sem pagar nada e sem precisar de cartão de crédito. Após os 30 dias, o plano básico começa em R$ 79/mês."}
    },
    {
      "@type": "Question",
      "name": "Preciso de cartão de crédito para ativar o trial?",
      "acceptedAnswer": {"@type": "Answer", "text": "Não. O trial de 30 dias é ativado apenas com e-mail e nome da clínica. Nenhum dado de pagamento é solicitado durante o período gratuito."}
    },
    {
      "@type": "Question",
      "name": "Quais especialidades podem usar o trial gratuito?",
      "acceptedAnswer": {"@type": "Answer", "text": "O sistema funciona para médicos, psicólogos, dentistas, fisioterapeutas, nutricionistas, oftalmologistas, fonoaudiólogos, terapeutas e outras especialidades que usam prontuário, agenda e controle de pacientes."}
    },
    {
      "@type": "Question",
      "name": "Os dados cadastrados no trial são perdidos?",
      "acceptedAnswer": {"@type": "Answer", "text": "Não. Se você optar por continuar com um plano pago, todos os pacientes, prontuários e agendamentos cadastrados durante o trial são mantidos."}
    }
  ]
}
</script>
</body>
</html>
