<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alternativa ao Odontoclinic — Sistema para Dentistas | UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Procurando substituir o Odontoclinic? Conheça o UTecnologia Saúde: prontuário odontológico, agenda e gestão de clínica. Teste grátis por 30 dias, sem cartão.">
    <link rel="canonical" href="https://utecnologia.com.br/alternativa-odontoclinic">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/alternativa-odontoclinic">
    <meta property="og:title" content="Alternativa ao Odontoclinic — UTecnologia Saúde">
    <meta property="og:description" content="Procurando substituir o Odontoclinic? Prontuário odontológico, agenda e gestão de clínica. Teste grátis 30 dias.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Alternativa ao Odontoclinic — UTecnologia Saúde">
    <meta name="twitter:description" content="Substitua o Odontoclinic por um sistema moderno. Prontuário, agenda e gestão. Trial 30 dias grátis.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #0f172a; --muted: #475569; --subtle: #94a3b8;
            --primary: #0d9488; --primary-dark: #0f766e;
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

        .hero { padding: 80px 0 60px; background: linear-gradient(160deg, #f0fdfa 0%, #f8fafc 60%); }
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
        .badge-icon { width: 28px; height: 28px; background: #f0fdfa; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

        .section { padding: 72px 0; }
        .section-label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--primary); text-align: center; margin-bottom: 12px; }
        h2 { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 16px; }
        .section-sub { font-size: 17px; color: var(--muted); text-align: center; max-width: 580px; margin: 0 auto 48px; }

        /* COMPARISON TABLE */
        .compare-wrap { overflow-x: auto; }
        .compare-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
        .compare-table th { padding: 16px 20px; font-size: 14px; font-weight: 700; }
        .compare-table th:first-child { text-align: left; background: var(--paper); }
        .compare-table th.col-utec { background: var(--primary); color: #fff; }
        .compare-table th.col-them { background: #f1f5f9; color: var(--muted); }
        .compare-table td { padding: 14px 20px; font-size: 14px; border-top: 1px solid var(--border); }
        .compare-table td:first-child { font-weight: 600; color: var(--ink); background: var(--paper); }
        .compare-table td.col-utec { text-align: center; color: var(--primary-dark); font-weight: 600; }
        .compare-table td.col-them { text-align: center; color: var(--subtle); }
        .yes { color: #16a34a !important; }
        .no { color: #dc2626 !important; }

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

        .disclaimer { font-size: 12px; color: var(--subtle); text-align: center; max-width: 700px; margin: 24px auto 0; line-height: 1.6; }

        .footer { background: var(--ink); color: rgba(255,255,255,.6); padding: 32px 0; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-links a { color: rgba(255,255,255,.6); text-decoration: none; font-size: 13px; margin-left: 20px; }
        .footer-brand { font-size: 14px; font-weight: 700; color: #fff; }

        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            h1 { font-size: 30px; }
        }
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
        <div class="nav-links">
            <a href="<?=base_url()?>sistema-para-dentistas">Para Dentistas</a>
            <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
            <a href="<?=base_url()?>alternativa-feegow">vs Feegow</a>
            <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="wrap">
        <div class="hero-inner">
            <div>
                <div class="eyebrow">Alternativa ao Odontoclinic</div>
                <h1>Procurando uma <em>alternativa ao Odontoclinic</em>?</h1>
                <p class="hero-text">
                    Muitos dentistas e clínicas odontológicas estão migrando para sistemas mais modernos.
                    O UTecnologia Saúde oferece prontuário odontológico, agenda completa e gestão de clínica
                    em um sistema 100% online, com trial gratuito de 30 dias.
                </p>
                <div class="hero-cta">
                    <a href="<?=base_url()?>experimentar" class="btn-primary">Testar 30 dias grátis →</a>
                    <a href="<?=base_url()?>sistema-para-dentistas" class="btn-outline">Ver recursos para dentistas</a>
                </div>
            </div>
            <div class="hero-badge">
                <div class="badge-title">O que você encontra aqui</div>
                <div class="badge-list">
                    <div class="badge-item"><div class="badge-icon">🦷</div> Prontuário odontológico completo</div>
                    <div class="badge-item"><div class="badge-icon">📅</div> Agenda por dentista e unidade</div>
                    <div class="badge-item"><div class="badge-icon">📎</div> Anexo de radiografias e arquivos</div>
                    <div class="badge-item"><div class="badge-icon">👥</div> Multi-profissionais na mesma clínica</div>
                    <div class="badge-item"><div class="badge-icon">🌐</div> 100% online, sem instalação</div>
                    <div class="badge-item"><div class="badge-icon">💰</div> A partir de R$ 79/mês</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Comparação</div>
        <h2>UTecnologia Saúde vs sistemas tradicionais de odontologia</h2>
        <p class="section-sub">Veja como o UTecnologia Saúde se posiciona em relação aos sistemas que você está avaliando.</p>
        <div class="compare-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Recurso</th>
                        <th class="col-utec">UTecnologia Saúde</th>
                        <th class="col-them">Sistemas tradicionais</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Prontuário odontológico</td><td class="col-utec yes">✔ Completo</td><td class="col-them">Varia por sistema</td></tr>
                    <tr><td>Agenda por profissional</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Nem sempre multi-profissional</td></tr>
                    <tr><td>Anexo de radiografias e arquivos</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Varia</td></tr>
                    <tr><td>Acesso online (sem instalação)</td><td class="col-utec yes">✔ 100% online</td><td class="col-them">Muitos exigem instalação local</td></tr>
                    <tr><td>Trial gratuito</td><td class="col-utec yes">✔ 30 dias grátis</td><td class="col-them">Raramente oferecem</td></tr>
                    <tr><td>Preço inicial</td><td class="col-utec yes">✔ R$ 79/mês</td><td class="col-them">Em geral acima de R$ 150/mês</td></tr>
                    <tr><td>Suporte em português</td><td class="col-utec yes">✔ Sim</td><td class="col-them">Varia</td></tr>
                    <tr><td>Sem fidelidade forçada</td><td class="col-utec yes">✔ Cancela quando quiser</td><td class="col-them">Nem sempre</td></tr>
                </tbody>
            </table>
        </div>
        <p class="disclaimer">Informações baseadas em pesquisa de mercado pública. O UTecnologia Saúde não possui odontograma gráfico — ideal para clínicas que precisam de prontuário, agenda e gestão. Para odontograma interativo avançado, avalie se é indispensável no seu fluxo.</p>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Por que migrar agora</div>
        <h2>O que clínicas odontológicas precisam de um sistema moderno</h2>
        <p class="section-sub">A gestão da clínica não deve depender de software desatualizado ou caro demais para o que entrega.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Prontuário Odontológico</h3>
                <p>Registre anamnese odontológica, queixas, tratamentos realizados, hipóteses diagnósticas e evolução de cada consulta. Histórico completo por paciente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Agenda Multidenista</h3>
                <p>Agende consultas para todos os dentistas da clínica. Filtre por profissional, data e status. Cancele e remarque diretamente na agenda.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📎</div>
                <h3>Arquivos e Radiografias</h3>
                <p>Anexe radiografias, moldes digitalizados, laudos e qualquer arquivo diretamente ao prontuário do paciente. Acesse de qualquer dispositivo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipe Completa</h3>
                <p>Cadastre todos os dentistas e colaboradores da clínica. Cada profissional acessa apenas os dados relevantes ao seu trabalho.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios e Gestão</h3>
                <p>Acompanhe atendimentos por período, profissional e tipo de procedimento. Dados para tomada de decisão na gestão da clínica.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Segurança e Privacidade</h3>
                <p>Cada clínica tem seu ambiente isolado. Acesso por login e perfil de usuário. Os dados dos seus pacientes não são compartilhados.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="wrap">
        <div class="section-label">Migração simplificada</div>
        <h2>Comece a usar em 3 passos</h2>
        <p class="section-sub">Migrar de sistema não precisa ser traumático. Aqui você começa a testar sem burocracia.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Crie sua conta gratuitamente</h3>
                <p>Preencha o nome da clínica e e-mail. Sem cartão de crédito. Acesso imediato ao sistema completo.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Cadastre dentistas e pacientes</h3>
                <p>Adicione os profissionais da sua equipe e comece a cadastrar os pacientes. O sistema está pronto para usar.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Use por 30 dias e decida</h3>
                <p>Teste prontuários, agenda e relatórios com dados reais. Só assine se gostar. Planos a partir de R$ 79/mês.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre migrar do Odontoclinic</h2>
        <p class="section-sub" style="margin-bottom: 40px;">Respostas diretas para quem está avaliando a troca de sistema.</p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">O UTecnologia Saúde substitui o Odontoclinic para clínicas odontológicas?</div>
                <div class="faq-a">Para a maioria dos fluxos clínicos, sim. O UTecnologia Saúde oferece prontuário odontológico, agenda por dentista, gestão de equipe e anexo de arquivos como radiografias. O sistema não possui odontograma gráfico interativo — se esse recurso for essencial para seu fluxo, avalie se vale a diferença no custo e na modernidade da plataforma.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Preciso exportar dados do sistema anterior?</div>
                <div class="faq-a">O sistema permite o cadastro manual de pacientes e histórico. Para migrações de grande volume, nossa equipe pode orientar o processo. Durante os 30 dias de trial você tem tempo para avaliar o fluxo antes de comprometer a migração definitiva.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Funciona para clínicas com vários dentistas?</div>
                <div class="faq-a">Sim. O UTecnologia Saúde é multi-profissional por design. O Plano Clínica suporta até 5 dentistas + 10 colaboradores. O Plano Pro suporta até 20 profissionais. Cada um com sua agenda própria e acesso aos pacientes do seu vínculo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">É possível anexar radiografias no prontuário?</div>
                <div class="faq-a">Sim. O sistema permite anexar arquivos de qualquer formato (imagens, PDFs, laudos) diretamente ao prontuário do paciente, vinculados a cada atendimento. As radiografias ficam armazenadas com segurança e acessíveis de qualquer dispositivo.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Qual o custo em comparação com sistemas de odontologia?</div>
                <div class="faq-a">O Plano Solo começa em R$ 79/mês para 1 dentista e 2 colaboradores. O Plano Clínica custa R$ 199/mês para até 5 dentistas. Em geral, os sistemas especializados em odontologia cobram acima de R$ 150–300/mês por valores similares. E você testa grátis por 30 dias antes de decidir.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema precisa ser instalado no computador da clínica?</div>
                <div class="faq-a">Não. O UTecnologia Saúde é 100% online (SaaS). Acesse pelo navegador de qualquer computador, tablet ou celular. Sem instalação, sem servidor local, sem backup manual. Ideal para clínicas que querem modernizar a operação.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Teste antes de migrar — 30 dias grátis</h2>
            <p>Crie sua conta agora e avalie o UTecnologia Saúde com dados reais da sua clínica.<br>Sem cartão de crédito. Sem compromisso de permanência.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Começar trial gratuito →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
                <a href="<?=base_url()?>sistema-para-dentistas">Para Dentistas</a>
                <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
                <a href="<?=base_url()?>alternativa-feegow">vs Feegow</a>
                <a href="<?=base_url()?>sistema-prontuario-eletronico">Prontuário</a>
                <a href="<?=base_url()?>experimentar">Trial grátis</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Alternativa ao Odontoclinic — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/alternativa-odontoclinic",
  "description": "Procurando substituir o Odontoclinic? Conheça o UTecnologia Saúde: prontuário odontológico, agenda e gestão de clínica. Trial grátis 30 dias.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Sistema para Dentistas", "item": "https://utecnologia.com.br/sistema-para-dentistas"},
      {"@type": "ListItem", "position": 3, "name": "Alternativa ao Odontoclinic", "item": "https://utecnologia.com.br/alternativa-odontoclinic"}
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
      "name": "O UTecnologia Saúde substitui o Odontoclinic para clínicas odontológicas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Para a maioria dos fluxos, sim. O sistema oferece prontuário odontológico, agenda por dentista e anexo de arquivos como radiografias. Não possui odontograma gráfico interativo."}
    },
    {
      "@type": "Question",
      "name": "Funciona para clínicas com vários dentistas?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O Plano Clínica suporta até 5 dentistas + 10 colaboradores por R$ 199/mês. O Plano Pro suporta até 20 profissionais."}
    },
    {
      "@type": "Question",
      "name": "É possível anexar radiografias no prontuário?",
      "acceptedAnswer": {"@type": "Answer", "text": "Sim. O sistema permite anexar arquivos de qualquer formato diretamente ao prontuário do paciente, vinculados a cada atendimento."}
    },
    {
      "@type": "Question",
      "name": "Qual o custo em comparação com sistemas de odontologia?",
      "acceptedAnswer": {"@type": "Answer", "text": "O Plano Solo começa em R$ 79/mês para 1 dentista e 2 colaboradores. O Plano Clínica custa R$ 199/mês para até 5 dentistas. E você testa grátis por 30 dias antes de decidir."}
    }
  ]
}
</script>
</body>
</html>

