<!DOCTYPE html>
<html>
<head>
  <title>Blog — UTecnologia Saúde</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <style>
    .badge-rascunho { background:#94a3b8; color:#fff; }
    .badge-publicado { background:#22c55e; color:#fff; }
    .post-title { font-weight:600; color:#0f172a; }
    .post-slug  { font-size:12px; color:#64748b; font-family:monospace; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">

  <?php include(APPPATH . '../includes/adm/search.php'); ?>
  <div class="layout-w">
    <?php include(APPPATH . '../includes/adm/menu.php'); ?>

    <div class="content-w">
      <?php include(APPPATH . '../includes/adm/top.php'); ?>

      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
        <li class="breadcrumb-item"><span>Blog</span></li>
      </ul>

      <div class="content-i">
        <div class="content-box">

          <div class="element-wrapper">
            <div class="element-actions">
              <a href="<?=base_url()?>adm/blog/novo" class="btn btn-primary btn-sm">
                <i class="os-icon os-icon-plus"></i> Novo artigo
              </a>
            </div>
            <h6 class="element-header">Artigos do Blog</h6>

            <div class="element-box">
              <table class="table table-striped table-hover" id="tbl-blog">
                <thead>
                  <tr>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Publicado em</th>
                    <th style="width:140px">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($posts as $p): ?>
                  <tr>
                    <td>
                      <div class="post-title"><?=htmlspecialchars($p->titulo)?></div>
                      <div class="post-slug">/blog/<?=htmlspecialchars($p->slug)?></div>
                    </td>
                    <td><?=htmlspecialchars($p->categoria_nome ?: '—')?></td>
                    <td>
                      <?php if ($p->publicado): ?>
                        <span class="badge badge-publicado">Publicado</span>
                      <?php else: ?>
                        <span class="badge badge-rascunho">Rascunho</span>
                      <?php endif; ?>
                    </td>
                    <td><?=$p->publicado_em ? date('d/m/Y', strtotime($p->publicado_em)) : '—'?></td>
                    <td>
                      <a href="<?=base_url()?>adm/blog/editar/<?=$p->id?>" class="btn btn-white btn-sm" title="Editar">
                        <i class="os-icon os-icon-edit-32"></i>
                      </a>
                      <a href="<?=base_url()?>adm/blog/publicar/<?=$p->id?>"
                         class="btn btn-sm <?=$p->publicado ? 'btn-warning' : 'btn-success'?>"
                         title="<?=$p->publicado ? 'Despublicar' : 'Publicar'?>">
                        <i class="os-icon os-icon-<?=$p->publicado ? 'eye-off' : 'eye'?>"></i>
                      </a>
                      <?php if (!$p->publicado): ?>
                      <a href="<?=base_url()?>adm/blog/excluir/<?=$p->id?>"
                         class="btn btn-danger btn-sm btn-excluir" title="Excluir"
                         data-confirm="Excluir este rascunho?">
                        <i class="os-icon os-icon-trash-2"></i>
                      </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($posts)): ?>
                  <tr><td colspan="5" class="text-center text-muted py-4">Nenhum artigo ainda. <a href="<?=base_url()?>adm/blog/novo">Criar o primeiro</a></td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js"></script>
<script>
$(function(){
  $('.btn-excluir').on('click', function(e){
    var msg = $(this).data('confirm') || 'Confirmar exclusão?';
    if (!confirm(msg)) e.preventDefault();
  });
});
</script>
</body>
</html>
