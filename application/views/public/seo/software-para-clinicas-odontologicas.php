<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Software para Clínicas Odontológicas | Gestão Completa para Dentistas | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software para clínicas odontológicas com prontuário odontológico online, agenda para dentistas, gestão de pacientes e equipe. Sistema 100% online para consultórios e clínicas. Teste grátis por 30 dias.">
    <link rel="canonical" href="https://utecnologia.com.br/software-para-clinicas-odontologicas">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/software-para-clinicas-odontologicas">
    <meta property="og:title" content="Software para Clínicas Odontológicas | UTecnologia Saúde">
    <meta property="og:description" content="Software odontológico para dentistas e clínicas: prontuário, agenda, radiografias e gestão de equipe. Teste grátis por 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Software para Clínicas Odontológicas | UTecnologia Saúde">
    <meta name="twitter:description" content="Sistema para consultório odontológico com prontuário, agenda e gestão. 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #0f766e; --primary-dark: #115e59;
            --accent: #22c55e; --border: #dbe4ea;
            --paper: #f7fbfb; --panel: #ffffff;
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

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #ecfeff 0%, #f7fbfb 60%); }
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
        .badge-icon { width: 28px; height: 28px; background: #ecfeff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section.alt { background: #fff; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 720px; margin: 0 auto 48px; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .feature-icon { font-size: 28px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }

        .use-cases { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .use-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
        .use-card.highlight { border-color: var(--primary); background: #f0fdfa; }
        .use-card h3 { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
        .use-card p { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 14px; }
        .use-chip { display: inline-block; background: #ccfbf1; color: var(--primary-dark); font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 999px; }

        .proof-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .proof-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
        .proof-card p { font-size: 15px; color: var(--ink); line-height: 1.7; margin-bottom: 12px; }
        .proof-meta { font-size: 13px; color: var(--muted); font-weight: 600; }

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
            .hero-inner, .features-grid, .proof-grid, .use-cases { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-dentistas">Sistema para dentistas</a>
            <a href="<?=base_url()?>alternativa-odontoclinic">Alternativa ao Odontoclinic</a>
            <a href="<?=base_url()?>software-para-clinicas">Software para clínicas</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Software para Clínicas Odontológicas</div>
                <h1><em>Software para clínicas odontológicas</em> com agenda, prontuário e gestão completa</h1>
                <p class="hero-text">
                    O UTecnologia Saúde é um software para clínicas odontológicas que ajuda dentistas,
                    secretárias e gestores a organizar pacientes, agenda, prontuários, radiografias
                    e o atendimento diário em uma única plataforma.
                </p>
                <p class="hero-text">
                    Se você procura um sistema para consultório odontológico, um programa para clínica odontológica
                    ou um software para dentista autônomo, aqui você encontra uma solução online, simples de usar
                    e pronta para começar em minutos.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>assinar" class="btn-outline">Ver planos</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O software odontológico inclui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🦷</div> Gestão odontológica para clínica ou consultório</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda para dentistas e retornos</div>
                    <div class="badge-item"><div class="badge-icon">📋</div> Prontuário odontológico online</div>
                    <div class="badge-item"><div class="badge-icon">📎</div> Radiografias, laudos e anexos por paciente</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Acesso por dentista, recepcionista e equipe</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> 100% online, sem instalação local</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="wrap">
        <div class="section-label">Para quem serve</div>
        <h2>Um sistema odontológico pensado para a rotina real da clínica</h2>
        <p class="section-sub">Do dentista autônomo à clínica com vários profissionais, o sistema organiza a operação sem complicar o atendimento.</p>
        <div class="use-cases">
            <div class="use-card highlight">
                <h3>Consultório odontológico de um único profissional</h3>
                <p>Ideal para quem precisa de agenda, cadastro de pacientes, prontuário odontológico digital e apoio da recepcionista sem pagar por uma estrutura excessiva.</p>
                <span class="use-chip">Plano Solo a partir de R$ 79/mês</span>
            </div>
            <div class="use-card">
                <h3>Clínica odontológica com vários dentistas</h3>
                <p>Perfeito para operações com mais de um profissional, cada um com sua agenda e seus pacientes, mas com visão centralizada para a equipe administrativa.</p>
                <span class="use-chip">Plano Clínica e Plano Pro</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Recursos principais</div>
        <h2>O que esse software para clínicas odontológicas resolve</h2>
        <p class="section-sub">Mais do que marcar consultas, a plataforma ajuda a padronizar o atendimento e manter a clínica organizada.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário odontológico online</h3>
                <p>Registre anamnese, queixas, procedimentos realizados, condutas e evolução clínica do paciente em um histórico único e acessível.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda para dentistas</h3>
                <p>Organize consultas, retornos e encaixes por profissional. Filtre por data, status e dentista da equipe em poucos cliques.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Radiografias e anexos</h3>
                <p>Anexe radiografias, PDFs, laudos e imagens diretamente no prontuário do paciente para consulta rápida no momento do atendimento.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe com permissões</h3>
                <p>Recepcionistas podem cuidar da agenda e do cadastro, enquanto os dentistas acessam o prontuário clínico com controle por perfil.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏥</div>
                <h3>Gestão odontológica centralizada</h3>
                <p>Acompanhe atendimentos por período e profissional para organizar melhor o fluxo da clínica e reduzir gargalos operacionais.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Software odontológico na nuvem</h3>
                <p>Use no computador, tablet ou celular sem instalação. O acesso é online e a clínica não depende de servidor local.</p>
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="wrap">
        <div class="section-label">Prova social</div>
        <h2>O tipo de resultado que clínicas pequenas buscam</h2>
        <p class="section-sub">Estes depoimentos representam os cenários mais comuns que atendemos hoje em consultórios e clínicas odontológicas.</p>
        <div class="proof-grid">
            <div class="proof-card">
                <p>"Depois que centralizamos agenda e prontuários no UTecnologia, a recepção ficou muito mais rápida e os retornos deixaram de se perder."</p>
                <div class="proof-meta">Clínica odontológica de bairro · Equipe com 2 dentistas</div>
            </div>
            <div class="proof-card">
                <p>"Eu precisava de um software para dentista autônomo que fosse simples. Hoje consigo ver o histórico do paciente e anexar exames sem depender de papel."</p>
                <div class="proof-meta">Dentista autônomo · Consultório próprio</div>
            </div>
            <div class="proof-card">
                <p>"O maior ganho foi separar o acesso da recepcionista e dos profissionais. A agenda continua fluindo e os prontuários ficam protegidos."</p>
                <div class="proof-meta">Clínica com recepção e equipe de apoio</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre software para clínicas odontológicas</h2>
        <p class="section-sub">Perguntas que normalmente aparecem quando a clínica está pesquisando um novo sistema odontológico.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">Qual o melhor software para clínicas odontológicas?</div>
                <div class="faq-a">O melhor software é o que combina prontuário odontológico, agenda para dentistas, gestão de equipe e facilidade de uso. O UTecnologia Saúde foi pensado para clínicas que querem organizar a operação sem depender de instalação local ou processos complexos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Como organizar um consultório odontológico com mais eficiência?</div>
                <div class="faq-a">Centralizar agenda, cadastro de pacientes, prontuários e anexos em um único sistema reduz retrabalho e falhas de comunicação. Com o UTecnologia Saúde, a recepção e os dentistas trabalham na mesma base de informações.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema possui prontuário odontológico?</div>
                <div class="faq-a">Sim. O sistema permite manter um prontuário odontológico online com histórico de atendimentos, procedimentos, observações clínicas e arquivos anexos como radiografias e laudos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Funciona em celular e tablet?</div>
                <div class="faq-a">Sim. Como o software é 100% online, ele pode ser acessado pelo navegador em celular, tablet ou computador, sem instalação manual.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Serve para clínica com vários dentistas?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde atende desde consultório de um dentista até clínica odontológica com vários profissionais, cada um com sua agenda e seus atendimentos.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema tem odontograma gráfico?</div>
                <div class="faq-a">Hoje o foco do UTecnologia Saúde está em prontuário clínico, agenda, anexos e gestão. O odontograma gráfico interativo não faz parte da plataforma neste momento.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Teste o software odontológico na sua rotina real</h2>
            <p>Crie sua conta e use por 30 dias para avaliar agenda, prontuário odontológico e organização da equipe.<br>Sem cartão de crédito. Sem instalação.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar teste grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div class="footer-links">
                <a href="<?=base_url()?>sistema-para-dentistas">Sistema para Dentistas</a>
                <a href="<?=base_url()?>alternativa-odontoclinic">Alternativa ao Odontoclinic</a>
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
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Web",
  "url": "https://utecnologia.com.br/software-para-clinicas-odontologicas",
  "description": "Software para clínicas odontológicas com prontuário odontológico online, agenda para dentistas, gestão de pacientes e equipe.",
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
    {"@type": "ListItem", "position": 2, "name": "Sistema para Dentistas", "item": "https://utecnologia.com.br/sistema-para-dentistas"},
    {"@type": "ListItem", "position": 3, "name": "Software para Clínicas Odontológicas", "item": "https://utecnologia.com.br/software-para-clinicas-odontologicas"}
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
      "name": "Qual o melhor software para clínicas odontológicas?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "O melhor software é o que combina prontuário odontológico, agenda para dentistas, gestão de equipe e facilidade de uso. O UTecnologia Saúde reúne esses recursos em uma plataforma online."
      }
    },
    {
      "@type": "Question",
      "name": "Como organizar um consultório odontológico com mais eficiência?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Centralizar agenda, cadastro de pacientes, prontuários e anexos em um único sistema reduz retrabalho e falhas de comunicação. Com o UTecnologia Saúde, a recepção e os dentistas trabalham na mesma base de informações."
      }
    },
    {
      "@type": "Question",
      "name": "O sistema possui prontuário odontológico?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. O sistema permite manter um prontuário odontológico online com histórico de atendimentos, procedimentos, observações clínicas e arquivos anexos."
      }
    },
    {
      "@type": "Question",
      "name": "Funciona em celular e tablet?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. Como o software é 100% online, ele pode ser acessado pelo navegador em celular, tablet ou computador, sem instalação manual."
      }
    },
    {
      "@type": "Question",
      "name": "Serve para clínica com vários dentistas?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. O UTecnologia Saúde atende desde consultório de um dentista até clínica odontológica com vários profissionais, cada um com sua agenda e seus atendimentos."
      }
    }
  ]
}
</script>
</body>
</html>
