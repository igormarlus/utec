<!DOCTYPE html>
<html>
  <head>
    <title><?=$manual['title']?> | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .manual-shell { display:grid; gap:24px; }
      .manual-hero { background:linear-gradient(135deg,#047bf8,#20c997); border-radius:24px; padding:36px 32px; color:#fff; box-shadow:0 20px 40px rgba(4,123,248,.18); }
      .manual-hero-label { font-size:11px; letter-spacing:.14em; text-transform:uppercase; opacity:.85; font-weight:700; margin-bottom:10px; }
      .manual-title { font-size:34px; line-height:1.1; font-weight:800; margin:0 0 10px; color:#fff; }
      .manual-copy { color:rgba(255,255,255,.92); font-size:15px; line-height:1.7; max-width:640px; }
      .manual-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:20px; }
      .manual-actions .btn-primary { background:#fff; color:#047bf8; border:none; font-weight:700; }
      .manual-actions .btn-outline-secondary { border-color:rgba(255,255,255,.5); color:#fff; }

      .manual-capitulo { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 10px 24px rgba(15,23,42,.06); }
      .manual-capitulo-head { background:linear-gradient(135deg,#047bf8,#20c997); display:flex; align-items:center; gap:14px; padding:18px 22px; color:#fff; border-left:10px solid #047bf8; }
      .manual-capitulo-badge { width:34px; height:34px; min-width:34px; border-radius:50%; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; }
      .manual-capitulo-head h6 { margin:0; font-size:16px; font-weight:800; color:#fff; }
      .manual-capitulo-body { padding:20px 22px 22px; }
      .manual-capitulo-resumo { color:#475569; margin:0 0 14px; line-height:1.7; }
      .manual-print { width:100%; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:16px; display:block; }
      .manual-checklist { list-style:none; margin:0; padding:0; color:#334155; }
      .manual-checklist li { display:flex; align-items:flex-start; gap:10px; margin-bottom:11px; line-height:1.6; }
      .manual-checklist li:last-child { margin-bottom:0; }
      .manual-check { width:18px; height:18px; min-width:18px; border-radius:50%; background:#20c997; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; margin-top:2px; }
      .manual-footer-nota { color:#94a3b8; font-size:13px; text-align:center; }
    </style>
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper with-side-panel solid-bg-all">
      <? include("includes/adm/search.php"); ?>
      <div class="layout-w">
        <? include("includes/adm/menu.php"); ?>
        <div class="content-w">
          <? include("includes/adm/top.php"); ?>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
            <li class="breadcrumb-item"><span>Manual</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="manual-shell">
                <div class="manual-hero">
                  <div class="manual-hero-label">Ajuda permanente</div>
                  <h1 class="manual-title"><?=$manual['title']?></h1>
                  <div class="manual-copy"><?=$manual['subtitle']?></div>
                  <div class="manual-actions">
                    <a href="<?=base_url()?>adm/usuarios/manual_pdf/<?=$manual['level']?>" class="btn btn-primary" target="_blank">Abrir PDF</a>
                    <a href="<?=base_url()?>adm/usuarios/dash" class="btn btn-outline-secondary">Voltar ao painel</a>
                  </div>
                </div>

                <? $numero = 0; foreach($manual['capitulos'] as $capitulo){ $numero++; ?>
                <div class="manual-capitulo">
                  <div class="manual-capitulo-head">
                    <div class="manual-capitulo-badge"><?=sprintf('%02d', $numero)?></div>
                    <h6><?=$capitulo['titulo']?></h6>
                  </div>
                  <div class="manual-capitulo-body">
                    <p class="manual-capitulo-resumo"><?=$capitulo['resumo']?></p>
                    <? if($capitulo['print']){ ?>
                    <img class="manual-print" src="<?=base_url().'imagens/manual/'.$capitulo['print']?>" alt="<?=$capitulo['titulo']?>">
                    <? } ?>
                    <ul class="manual-checklist">
                      <? foreach($capitulo['topicos'] as $topico){ ?>
                      <li><span class="manual-check">&#10003;</span><span><?=$topico?></span></li>
                      <? } ?>
                    </ul>
                  </div>
                </div>
                <? } ?>

                <div class="manual-footer-nota">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
