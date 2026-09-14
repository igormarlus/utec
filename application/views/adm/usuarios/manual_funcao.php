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
      .manual-hero { background:linear-gradient(135deg, rgba(37,99,235,.10), rgba(15,118,110,.10)); border:1px solid #dbe7f3; border-radius:24px; padding:24px; box-shadow:0 14px 30px rgba(15,23,42,.05); }
      .manual-title { font-size:34px; line-height:1.08; color:#0f172a; font-weight:700; margin:0 0 10px; }
      .manual-copy { color:#475569; font-size:15px; line-height:1.75; max-width:840px; }
      .manual-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }
      .manual-capitulo { background:#fff; border:1px solid #e2e8f0; border-radius:20px; box-shadow:0 10px 24px rgba(15,23,42,.05); overflow:hidden; }
      .manual-capitulo-head { padding:18px 20px 0; display:flex; align-items:center; gap:10px; }
      .manual-capitulo-head i { font-size:20px; color:#2563eb; }
      .manual-capitulo-body { padding:12px 20px 20px; }
      .manual-capitulo-resumo { color:#475569; margin:6px 0 12px; }
      .manual-list { margin:0; padding-left:18px; color:#475569; }
      .manual-list li { margin-bottom:10px; line-height:1.7; }
      .manual-list li:last-child { margin-bottom:0; }
      .manual-print { width:100%; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:14px; display:block; }
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
                  <div class="label">Ajuda permanente</div>
                  <h1 class="manual-title"><?=$manual['title']?></h1>
                  <div class="manual-copy"><?=$manual['subtitle']?></div>
                  <div class="manual-actions">
                    <a href="<?=base_url()?>adm/usuarios/manual_pdf/<?=$manual['level']?>" class="btn btn-primary" target="_blank">Abrir PDF</a>
                    <a href="<?=base_url()?>adm/usuarios/dash" class="btn btn-outline-secondary">Voltar ao painel</a>
                  </div>
                </div>

                <? foreach($manual['capitulos'] as $capitulo){ ?>
                <div class="manual-capitulo">
                  <div class="manual-capitulo-head">
                    <i class="<?=$capitulo['icone']?>"></i>
                    <h6 class="element-header" style="margin-bottom:0;"><?=$capitulo['titulo']?></h6>
                  </div>
                  <div class="manual-capitulo-body">
                    <p class="manual-capitulo-resumo"><?=$capitulo['resumo']?></p>
                    <? if($capitulo['print']){ ?>
                    <img class="manual-print" src="<?=base_url().'imagens/manual/'.$capitulo['print']?>" alt="<?=$capitulo['titulo']?>">
                    <? } ?>
                    <ul class="manual-list">
                      <? foreach($capitulo['topicos'] as $topico){ ?>
                      <li><?=$topico?></li>
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
