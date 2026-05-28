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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --primary:#0ea5e9; --primary-dark:#0284c7; --accent:#22c55e; --border:#e2e8f0; --paper:#f8fafc; --radius:16px; --shadow:0 4px 24px rgba(15,23,42,.08); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter',sans-serif; color:var(--ink); background:var(--paper); line-height:1.6; }
        a { color:var(--primary-dark); }
        .wrap { max-width:1100px; margin:0 auto; padding:0 20px; }
        .topnav { background:#fff; border-bottom:1px solid var(--border); padding:14px 0; }
        .topnav .wrap { display:flex; justify-content:space-between; align-items:center; }
        .brand { font-size:17px; font-weight:800; color:var(--ink); text-decoration:none; }
        .brand span { color:var(--primary); }
        .nav-links { display:flex; gap:24px; align-items:center; }
        .nav-links a { font-size:14px; font-weight:500; color:var(--muted); text-decoration:none; }
        .btn-nav { background:var(--primary); color:#fff !important; padding:8px 18px; border-radius:999px; font-weight:700 !important; font-size:13px !important; }
        .hero { padding:80px 0 60px; background:linear-gradient(160deg,#f0f9ff 0%,#f8fafc 60%); }
        .eyebrow { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); margin-bottom:12px; }
        h1 { font-size:42px; font-weight:800; line-height:1.12; margin-bottom:20px; max-width:720px; }
        h1 em { font-style:normal; color:var(--primary); }
        .hero-text { font-size:18px; color:var(--muted); line-height:1.7; margin-bottom:32px; max-width:620px; }
        .hero-cta { display:flex; gap:12px; flex-wrap:wrap; }
        .btn-primary { display:inline-block; background:var(--primary); color:#fff; padding:14px 28px; border-radius:999px; font-weight:700; font-size:15px; text-decoration:none; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { display:inline-block; border:1.5px solid var(--border); color:var(--muted); padding:13px 24px; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none; }
        .section { padding:72px 0; }
        .section-label { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); text-align:center; margin-bottom:12px; }
        h2 { font-size:32px; font-weight:800; text-align:center; margin-bottom:16px; }
        .section-sub { font-size:17px; color:var(--muted); text-align:center; max-width:580px; margin:0 auto 48px; }

        /* COMPARISON TABLE */
        .compare-wrap { overflow-x:auto; }
        .compare-table { width:100%; border-collapse:collapse; background:#fff; border-radius:20px; overflow:hidden; border:1px solid var(--border); }
        .compare-table th { padding:16px 20px; font-size:13px; font-weight:700; text-align:left; background:#f8fafc; }
        .compare-table th.col-utec { background:#eff6ff; color:var(--primary); }
        .compare-table td { padding:14px 20px; font-size:14px; border-top:1px solid var(--border); }
        .compare-table tr:nth-child(even) td { background:#fafbfc; }
        .check { color:var(--accent); font-weight:700; }
        .cross { color:#ef4444; font-weight:700; }
        .partial { color:#f59e0b; font-weight:700; }
        .badge-utec { display:inline-block; background:#eff6ff; color:var(--primary); font-size:11px; font-weight:700; padding:3px 8px; border-radius:999px; }

        .reasons-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .reason-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:24px; }
        .reason-icon { font-size:28px; margin-bottom:12px; }
        .reason-card h3 { font-size:16px; font-weight:700; margin-bottom:8px; }
        .reason-card p { font-size:14px; color:var(--muted); line-height:1.6; }
        .faq-list { max-width:720px; margin:0 auto; display:flex; flex-direction:column; gap:12px; }
        .faq-item { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; }
        .faq-q { font-size:15px; font-weight:700; margin-bottom:8px; }
        .faq-a { font-size:14px; color:var(--muted); line-height:1.7; }
        .cta-box { background:var(--primary); border-radius:24px; padding:56px 40px; text-align:center; }
        .cta-box h2 { color:#fff; font-size:30px; margin-bottom:12px; }
        .cta-box p { color:rgba(255,255,255,.85); font-size:16px; margin-bottom:28px; }
        .btn-white { display:inline-block; background:#fff; color:var(--primary-dark); padding:14px 32px; border-radius:999px; font-weight:800; font-size:15px; text-decoration:none; }
        .footer { background:#0f172a; color:rgba(255,255,255,.6); padding:32px 0; }
        .footer-inner { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .footer-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:13px; margin-left:20px; }
        .footer-brand { font-size:14px; font-weight:700; color:#fff; }
        .disclaimer { font-size:11px; color:var(--muted); text-align:center; margin-top:16px; }
        @media(max-width:900px) { .reasons-grid { grid-template-columns:1fr 1fr; } h1 { font-size:30px; } }
        @media(max-width:600px) { .reasons-grid { grid-template-columns:1fr; } .nav-links { display:none; } }
    </style>
</head>
<body>

<nav class="topnav">
    <div class="wrap">
        <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
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
    </div>
</section>

<section class="section" style="background:#fff;">
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

<section class="section" style="background:#f0f9ff;">
    <div class="wrap">
        <div class="section-label">Perguntas frequentes</div>
        <h2>Dúvidas sobre a migração de sistema</h2>
        <p class="section-sub" style="margin-bottom:40px;"></p>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q">É difícil migrar de outro sistema para o UTecnologia Saúde?</div>
                <div class="faq-a">Não. O processo começa com a criação da sua conta gratuita. Você pode ir cadastrando os pacientes ao longo do tempo, começando pelos ativos, e reconstruindo o histórico de forma gradual conforme a necessidade clínica.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O trial de 30 dias dá acesso a todas as funcionalidades?</div>
                <div class="faq-a">Sim. Durante o trial, você tem acesso completo ao sistema — prontuário, agenda, exames, gestão de equipe e relatórios — sem precisar de cartão de crédito ou assinatura.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Posso usar o UTecnologia Saúde com minha recepcionista?</div>
                <div class="faq-a">Sim. Todos os planos incluem colaboradores. A recepcionista acessa a agenda e o cadastro de pacientes, enquanto os profissionais de saúde gerenciam os prontuários. Cada perfil vê apenas o que é relevante para sua função.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">O sistema funciona para clínicas com vários profissionais?</div>
                <div class="faq-a">Sim. O plano Clínica suporta até 5 profissionais de saúde e o plano Pro até 20, todos com suas próprias agendas e prontuários dentro da mesma clínica.</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="cta-box">
            <h2>Experimente o UTecnologia Saúde gratuitamente</h2>
            <p>30 dias para testar todos os recursos. Sem cartão de crédito.<br>Se não gostar, não cobra nada.</p>
            <a href="<?=base_url()?>experimentar" class="btn-white">Criar conta grátis →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="wrap">
        <div class="footer-inner">
            <div class="footer-brand">UTecnologia Saúde</div>
            <div>
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
  "@type": "WebPage",
  "name": "Alternativa ao Feegow — UTecnologia Saúde",
  "url": "https://utecnologia.com.br/alternativa-feegow",
  "description": "Alternativa ao Feegow com prontuário eletrônico, agenda inteligente e planos acessíveis para clínicas.",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Alternativa ao Feegow", "item": "https://utecnologia.com.br/alternativa-feegow"}
    ]
  }
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
