<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Blog — Gestão Clínica e Prontuário Eletrônico | UTecnologia Saúde</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Artigos sobre gestão clínica, prontuário eletrônico, agenda médica e tecnologia para clínicas e consultórios no Brasil.">
  <link rel="canonical" href="https://utecnologia.com.br/blog">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://utecnologia.com.br/blog">
  <meta property="og:title" content="Blog — Gestão Clínica | UTecnologia Saúde">
  <meta property="og:description" content="Artigos sobre gestão clínica, prontuário eletrônico e tecnologia para profissionais de saúde.">
  <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
  <meta property="og:site_name" content="UTecnologia Saúde">
  <meta property="og:locale" content="pt_BR">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
  <style>
    :root{--ink:#0f172a;--muted:#475569;--subtle:#94a3b8;--primary:#0ea5e9;--primary-dark:#0284c7;--border:#e2e8f0;--paper:#f8fafc;--radius:16px;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;color:var(--ink);background:var(--paper);line-height:1.6;}
    a{color:var(--primary-dark);text-decoration:none;}
    .wrap{max-width:1100px;margin:0 auto;padding:0 20px;}
    .topnav{background:#fff;border-bottom:1px solid var(--border);padding:14px 0;}
    .topnav .wrap{display:flex;justify-content:space-between;align-items:center;}
    .brand{font-size:17px;font-weight:800;color:var(--ink);}
    .brand span{color:var(--primary);}
    .nav-links{display:flex;gap:24px;align-items:center;}
    .nav-links a{font-size:14px;font-weight:500;color:var(--muted);}
    .btn-nav{background:var(--primary);color:#fff!important;padding:8px 18px;border-radius:999px;font-weight:700!important;font-size:13px!important;}
    .page-hero{padding:56px 0 40px;background:linear-gradient(160deg,#f0f9ff 0%,#f8fafc 60%);border-bottom:1px solid var(--border);}
    .eyebrow{font-size:12px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--primary);margin-bottom:10px;}
    h1{font-size:36px;font-weight:800;line-height:1.15;margin-bottom:12px;}
    .lead{font-size:17px;color:var(--muted);line-height:1.75;}
    .blog-section{padding:56px 0;}
    .posts-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;}
    .post-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s;}
    .post-card:hover{box-shadow:0 8px 32px rgba(15,23,42,.1);}
    .card-img{width:100%;height:180px;object-fit:cover;background:linear-gradient(135deg,#e0f2fe,#bae6fd);}
    .card-img-placeholder{width:100%;height:180px;background:linear-gradient(135deg,#e0f2fe,#bae6fd);display:flex;align-items:center;justify-content:center;font-size:40px;}
    .card-body{padding:22px;flex:1;display:flex;flex-direction:column;}
    .card-cat{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--primary);margin-bottom:8px;}
    .card-title{font-size:17px;font-weight:700;color:var(--ink);line-height:1.35;margin-bottom:10px;}
    .card-title a{color:inherit;}
    .card-title a:hover{color:var(--primary-dark);}
    .card-excerpt{font-size:14px;color:var(--muted);line-height:1.65;flex:1;margin-bottom:16px;}
    .card-meta{display:flex;align-items:center;gap:12px;font-size:12px;color:var(--subtle);}
    .card-meta span{display:flex;align-items:center;gap:4px;}
    .empty-state{text-align:center;padding:64px 0;color:var(--muted);}
    .cta-strip{background:var(--primary);color:#fff;padding:48px 0;text-align:center;margin-top:0;}
    .cta-strip h2{font-size:24px;font-weight:800;margin-bottom:10px;}
    .cta-strip p{font-size:16px;opacity:.9;margin-bottom:24px;}
    .btn-cta{display:inline-block;background:#fff;color:var(--primary-dark);padding:14px 32px;border-radius:999px;font-weight:700;font-size:15px;}
    .footer{background:var(--ink);color:rgba(255,255,255,.6);padding:28px 0;}
    .footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
    .footer-inner a{color:rgba(255,255,255,.6);font-size:13px;margin-left:16px;}
    .footer-brand{font-size:14px;font-weight:700;color:#fff;}
    @media(max-width:900px){.posts-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.posts-grid{grid-template-columns:1fr;}.nav-links{display:none;}h1{font-size:26px;}}
  </style>
</head>
<body>

<nav class="topnav">
  <div class="wrap">
    <a class="brand" href="<?=base_url()?>">UTecnologia <span>Saúde</span></a>
    <div class="nav-links">
      <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
      <a href="<?=base_url()?>sobre">Sobre</a>
      <a href="<?=base_url()?>contato">Contato</a>
      <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
    </div>
  </div>
</nav>

<section class="page-hero">
  <div class="wrap">
    <div class="eyebrow">Blog</div>
    <h1>Gestão Clínica e Tecnologia em Saúde</h1>
    <p class="lead">Artigos práticos para médicos, psicólogos, dentistas e gestores de clínicas.</p>
  </div>
</section>

<section class="blog-section">
  <div class="wrap">
    <?php if (empty($posts)): ?>
    <div class="empty-state">
      <p style="font-size:18px;font-weight:600;">Em breve, novos artigos por aqui.</p>
    </div>
    <?php else: ?>
    <div class="posts-grid">
      <?php foreach ($posts as $p): ?>
      <article class="post-card">
        <?php if ($p->imagem_capa): ?>
          <img class="card-img" src="<?=base_url(htmlspecialchars($p->imagem_capa))?>" alt="<?=htmlspecialchars($p->titulo)?>">
        <?php else: ?>
          <div class="card-img-placeholder">📋</div>
        <?php endif; ?>
        <div class="card-body">
          <?php if ($p->categoria_nome): ?>
            <div class="card-cat"><?=htmlspecialchars($p->categoria_nome)?></div>
          <?php endif; ?>
          <h2 class="card-title">
            <a href="<?=base_url()?>blog/<?=htmlspecialchars($p->slug)?>"><?=htmlspecialchars($p->titulo)?></a>
          </h2>
          <?php if ($p->resumo): ?>
            <p class="card-excerpt"><?=htmlspecialchars(mb_substr($p->resumo, 0, 120)) . (mb_strlen($p->resumo) > 120 ? '...' : '')?></p>
          <?php endif; ?>
          <div class="card-meta">
            <span>📅 <?=date('d/m/Y', strtotime($p->publicado_em))?></span>
            <span>⏱ <?=$p->tempo_leitura?> min de leitura</span>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="cta-strip">
  <div class="wrap">
    <h2>Experimente o UTecnologia Saúde</h2>
    <p>Sistema de gestão clínica completo. 30 dias grátis, sem cartão de crédito.</p>
    <a href="<?=base_url()?>experimentar" class="btn-cta">Testar grátis →</a>
  </div>
</section>

<footer class="footer">
  <div class="wrap">
    <div class="footer-inner">
      <div class="footer-brand">UTecnologia Saúde</div>
      <div>
        <a href="<?=base_url()?>sobre">Sobre</a>
        <a href="<?=base_url()?>contato">Contato</a>
        <a href="<?=base_url()?>politica-de-privacidade">Privacidade</a>
        <a href="<?=base_url()?>experimentar">Trial grátis</a>
      </div>
    </div>
  </div>
</footer>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "url": "https://utecnologia.com.br/blog",
  "name": "Blog UTecnologia Saúde",
  "description": "Artigos sobre gestão clínica, prontuário eletrônico e tecnologia para profissionais de saúde.",
  "publisher": {
    "@type": "Organization",
    "name": "UTecnologia Saúde",
    "url": "https://utecnologia.com.br"
  }
}
</script>
</body>
</html>
