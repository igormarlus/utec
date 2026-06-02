<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <?php
  $seo_titulo    = htmlspecialchars($post->meta_titulo ?: $post->titulo);
  $seo_desc      = htmlspecialchars($post->meta_descricao ?: $post->resumo);
  $post_url      = 'https://utecnologia.com.br/blog/' . htmlspecialchars($post->slug);
  $pub_iso       = date('c', strtotime($post->publicado_em));
  $upd_iso       = $post->atualizado_em ? date('c', strtotime($post->atualizado_em)) : $pub_iso;
  ?>
  <title><?=$seo_titulo?> — UTecnologia Saúde</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?=$seo_desc?>">
  <link rel="canonical" href="<?=$post_url?>">
  <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
  <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?=$post_url?>">
  <meta property="og:title" content="<?=$seo_titulo?>">
  <meta property="og:description" content="<?=$seo_desc?>">
  <meta property="og:image" content="<?=$post->imagem_capa ? 'https://utecnologia.com.br/' . htmlspecialchars($post->imagem_capa) : 'https://utecnologia.com.br/imagens/og-cover.png'?>">
  <meta property="og:site_name" content="UTecnologia Saúde">
  <meta property="og:locale" content="pt_BR">
  <meta property="article:published_time" content="<?=$pub_iso?>">
  <meta property="article:modified_time" content="<?=$upd_iso?>">
  <meta property="article:author" content="<?=htmlspecialchars($post->autor)?>">
  <?php if ($post->categoria_nome): ?>
  <meta property="article:section" content="<?=htmlspecialchars($post->categoria_nome)?>">
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
  <style>
    :root{--ink:#0f172a;--muted:#475569;--subtle:#94a3b8;--primary:#0ea5e9;--primary-dark:#0284c7;--border:#e2e8f0;--paper:#f8fafc;--radius:16px;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;color:var(--ink);background:#fff;line-height:1.6;}
    a{color:var(--primary-dark);}
    .wrap{max-width:1100px;margin:0 auto;padding:0 20px;}
    .wrap-narrow{max-width:720px;margin:0 auto;padding:0 20px;}

    /* Nav */
    .topnav{background:#fff;border-bottom:1px solid var(--border);padding:14px 0;}
    .topnav .wrap{display:flex;justify-content:space-between;align-items:center;}
    .brand{font-size:17px;font-weight:800;color:var(--ink);text-decoration:none;}
    .brand span{color:var(--primary);}
    .nav-links{display:flex;gap:24px;align-items:center;}
    .nav-links a{font-size:14px;font-weight:500;color:var(--muted);text-decoration:none;}
    .btn-nav{background:var(--primary);color:#fff!important;padding:8px 18px;border-radius:999px;font-weight:700!important;font-size:13px!important;}

    /* Breadcrumb */
    .breadcrumb-bar{padding:14px 0;border-bottom:1px solid var(--border);background:#fff;}
    .breadcrumb-list{display:flex;gap:6px;align-items:center;font-size:13px;color:var(--subtle);list-style:none;}
    .breadcrumb-list a{color:var(--subtle);text-decoration:none;}
    .breadcrumb-list a:hover{color:var(--primary-dark);}
    .breadcrumb-list li:not(:last-child)::after{content:'/';margin-left:6px;}

    /* Hero do artigo */
    .article-hero{padding:48px 0 36px;background:linear-gradient(160deg,#f0f9ff 0%,#fff 70%);}
    .article-cat{display:inline-block;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--primary);margin-bottom:14px;}
    .article-title{font-size:36px;font-weight:800;line-height:1.2;margin-bottom:16px;}
    .article-meta{display:flex;flex-wrap:wrap;gap:16px;align-items:center;font-size:14px;color:var(--subtle);}
    .article-meta span{display:flex;align-items:center;gap:5px;}

    /* Layout */
    .article-layout{display:grid;grid-template-columns:1fr 300px;gap:48px;padding:48px 0 64px;max-width:1060px;margin:0 auto;}

    /* Conteúdo */
    .article-body{min-width:0;}
    .article-body h2{font-size:22px;font-weight:700;margin:36px 0 14px;padding-top:36px;border-top:1px solid var(--border);}
    .article-body h2:first-child{border-top:none;padding-top:0;margin-top:0;}
    .article-body h3{font-size:17px;font-weight:700;margin:24px 0 10px;color:var(--ink);}
    .article-body p{font-size:16px;color:var(--muted);line-height:1.85;margin-bottom:18px;}
    .article-body ul,.article-body ol{margin:0 0 18px 22px;display:flex;flex-direction:column;gap:8px;}
    .article-body li{font-size:16px;color:var(--muted);line-height:1.7;}
    .article-body strong{color:var(--ink);}
    .article-body a{color:var(--primary-dark);}
    .article-body blockquote{border-left:4px solid var(--primary);padding:14px 20px;margin:24px 0;background:#f0f9ff;border-radius:0 8px 8px 0;font-style:italic;color:var(--muted);}
    .article-cta{background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius);padding:28px 32px;margin:40px 0;text-align:center;}
    .article-cta h3{font-size:18px;font-weight:700;margin-bottom:8px;color:var(--ink);}
    .article-cta p{font-size:14px;color:var(--muted);margin-bottom:16px;}
    .btn-primary{display:inline-block;background:var(--primary);color:#fff;padding:12px 28px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;}

    /* Sidebar */
    .article-sidebar{}
    .sidebar-card{background:var(--paper);border:1px solid var(--border);border-radius:var(--radius);padding:22px;margin-bottom:24px;}
    .sidebar-title{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--subtle);margin-bottom:14px;}
    .toc-list{list-style:none;display:flex;flex-direction:column;gap:8px;}
    .toc-list a{font-size:14px;color:var(--muted);text-decoration:none;line-height:1.4;}
    .toc-list a:hover{color:var(--primary-dark);}
    .recent-post{display:flex;flex-direction:column;gap:4px;padding:10px 0;border-bottom:1px solid var(--border);}
    .recent-post:last-child{border-bottom:none;padding-bottom:0;}
    .recent-post a{font-size:14px;font-weight:600;color:var(--ink);text-decoration:none;line-height:1.35;}
    .recent-post a:hover{color:var(--primary-dark);}
    .recent-post span{font-size:12px;color:var(--subtle);}

    /* Footer */
    .footer{background:var(--ink);color:rgba(255,255,255,.6);padding:28px 0;}
    .footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
    .footer-inner a{color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;margin-left:16px;}
    .footer-brand{font-size:14px;font-weight:700;color:#fff;}

    @media(max-width:900px){
      .article-layout{grid-template-columns:1fr;}
      .article-sidebar{display:none;}
      .article-title{font-size:26px;}
    }
    @media(max-width:600px){.nav-links{display:none;}}
  </style>
</head>
<body>

<nav class="topnav">
  <div class="wrap">
    <a class="brand" href="<?=base_url()?>"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></a>
    <div class="nav-links">
      <a href="<?=base_url()?>sistema-para-clinicas">Clínicas</a>
      <a href="<?=base_url()?>blog">Blog</a>
      <a href="<?=base_url()?>contato">Contato</a>
      <a href="<?=base_url()?>experimentar" class="btn-nav">Testar grátis</a>
    </div>
  </div>
</nav>

<nav class="breadcrumb-bar" aria-label="Navegação estrutural">
  <div class="wrap">
    <ol class="breadcrumb-list">
      <li><a href="<?=base_url()?>">Início</a></li>
      <li><a href="<?=base_url()?>blog">Blog</a></li>
      <?php if ($post->categoria_nome): ?>
      <li><?=htmlspecialchars($post->categoria_nome)?></li>
      <?php endif; ?>
      <li><?=htmlspecialchars(mb_substr($post->titulo, 0, 50)) . (mb_strlen($post->titulo) > 50 ? '...' : '')?></li>
    </ol>
  </div>
</nav>

<header class="article-hero">
  <div class="wrap-narrow">
    <?php if ($post->categoria_nome): ?>
    <a class="article-cat" href="<?=base_url()?>blog"><?=htmlspecialchars($post->categoria_nome)?></a>
    <?php endif; ?>
    <h1 class="article-title"><?=htmlspecialchars($post->titulo)?></h1>
    <div class="article-meta">
      <span>✍️ <?=htmlspecialchars($post->autor)?></span>
      <span>📅 <?=date('d \d\e F \d\e Y', strtotime($post->publicado_em))?></span>
      <span>⏱ <?=$post->tempo_leitura?> min de leitura</span>
      <?php if ($post->atualizado_em && $post->atualizado_em !== $post->publicado_em): ?>
      <span>🔄 Atualizado em <?=date('d/m/Y', strtotime($post->atualizado_em))?></span>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="wrap">
  <div class="article-layout">

    <article class="article-body" id="article-content">
      <?= $post->conteudo ?>

      <div class="article-cta">
        <h3>Experimente o UTecnologia Saúde gratuitamente</h3>
        <p>Sistema completo de prontuário eletrônico, agenda e gestão de clínica. 30 dias grátis, sem cartão de crédito.</p>
        <a href="<?=base_url()?>experimentar" class="btn-primary">Testar grátis por 30 dias →</a>
      </div>
    </article>

    <aside class="article-sidebar">
      <div class="sidebar-card">
        <div class="sidebar-title">Neste artigo</div>
        <ul class="toc-list" id="toc"></ul>
      </div>

      <?php if (!empty($recentes)): ?>
      <div class="sidebar-card">
        <div class="sidebar-title">Artigos recentes</div>
        <?php foreach ($recentes as $r): ?>
        <div class="recent-post">
          <a href="<?=base_url()?>blog/<?=htmlspecialchars($r->slug)?>"><?=htmlspecialchars($r->titulo)?></a>
          <span><?=date('d/m/Y', strtotime($r->publicado_em))?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="sidebar-card" style="background:#f0f9ff;border-color:#bae6fd;text-align:center;">
        <div class="sidebar-title">Sistema gratuito</div>
        <p style="font-size:13px;color:#0369a1;margin-bottom:14px;">Prontuário eletrônico completo para sua clínica.</p>
        <a href="<?=base_url()?>experimentar" class="btn-primary" style="font-size:13px;">Testar 30 dias grátis</a>
      </div>
    </aside>

  </div>
</div>

<footer class="footer">
  <div class="wrap">
    <div class="footer-inner">
      <div class="footer-brand">UTecnologia Saúde</div>
      <div>
        <a href="<?=base_url()?>blog">Blog</a>
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
  "@type": "BlogPosting",
  "mainEntityOfPage": {"@type": "WebPage", "@id": "<?=$post_url?>"},
  "headline": "<?=addslashes(htmlspecialchars_decode($seo_titulo))?>",
  "description": "<?=addslashes(htmlspecialchars_decode($seo_desc))?>",
  "image": "<?=$post->imagem_capa ? 'https://utecnologia.com.br/' . $post->imagem_capa : 'https://utecnologia.com.br/imagens/og-cover.png'?>",
  "author": {"@type": "Organization", "name": "<?=addslashes(htmlspecialchars_decode(htmlspecialchars($post->autor)))?>"},
  "publisher": {
    "@type": "Organization",
    "name": "UTecnologia Saúde",
    "logo": {"@type": "ImageObject", "url": "https://utecnologia.com.br/imagens/logo.png"}
  },
  "datePublished": "<?=$pub_iso?>",
  "dateModified": "<?=$upd_iso?>",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Início",  "item": "https://utecnologia.com.br/"},
      {"@type": "ListItem", "position": 2, "name": "Blog",    "item": "https://utecnologia.com.br/blog"},
      {"@type": "ListItem", "position": 3, "name": "<?=addslashes(htmlspecialchars_decode(htmlspecialchars($post->titulo)))?>", "item": "<?=$post_url?>"}
    ]
  }
}
</script>

<script>
/* Gerar sumário automaticamente dos H2 do artigo */
(function(){
  var headers = document.querySelectorAll('#article-content h2');
  var toc = document.getElementById('toc');
  if (!toc || headers.length < 2) {
    var card = toc ? toc.closest('.sidebar-card') : null;
    if (card) card.style.display = 'none';
    return;
  }
  headers.forEach(function(h, i){
    var id = 'sec-' + i;
    h.id = id;
    var li = document.createElement('li');
    var a  = document.createElement('a');
    a.href = '#' + id;
    a.textContent = h.textContent;
    li.appendChild(a);
    toc.appendChild(li);
  });
})();
</script>
</body>
</html>

