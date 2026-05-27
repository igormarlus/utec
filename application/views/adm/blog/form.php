<!DOCTYPE html>
<html>
<head>
  <title><?=$post ? 'Editar artigo' : 'Novo artigo'?> — Blog UTecnologia</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <style>
    .seo-counter { font-size:11px; font-weight:600; }
    .seo-counter.ok   { color:#22c55e; }
    .seo-counter.warn { color:#f59e0b; }
    .seo-counter.over { color:#ef4444; }
    .seo-preview-box  { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-top:8px; }
    .seo-preview-title { color:#1a0dab; font-size:18px; font-weight:400; margin:0; }
    .seo-preview-url   { color:#006621; font-size:13px; margin:2px 0; }
    .seo-preview-desc  { color:#545454; font-size:13px; margin:0; }
    .content-editor    { min-height:420px; font-family:monospace; font-size:13px; }
    .tab-pane-label    { font-size:12px; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:6px; }
    .field-hint        { font-size:12px; color:#94a3b8; margin-top:3px; }
    .status-badge-pub  { background:#dcfce7; color:#166534; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
    .status-badge-ras  { background:#f1f5f9; color:#475569; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
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
        <li class="breadcrumb-item"><a href="<?=base_url()?>adm/blog">Blog</a></li>
        <li class="breadcrumb-item"><span><?=$post ? 'Editar' : 'Novo artigo'?></span></li>
      </ul>

      <div class="content-i">
        <div class="content-box">

          <?php if ($salvo): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Artigo salvo com sucesso!
            <?php if ($post && $post->publicado): ?>
              <a href="<?=base_url()?>blog/<?=htmlspecialchars($post->slug)?>" target="_blank">Ver no site →</a>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <?php
          $action = $post
            ? base_url('adm/blog/atualizar/' . $post->id)
            : base_url('adm/blog/salvar');
          ?>
          <form method="POST" action="<?=$action?>" id="form-post">

            <div class="row">
              <!-- Coluna principal -->
              <div class="col-lg-8">

                <div class="element-wrapper">
                  <h6 class="element-header">
                    <?=$post ? 'Editar artigo' : 'Novo artigo'?>
                    <?php if ($post): ?>
                      <span class="ml-2 <?=$post->publicado ? 'status-badge-pub' : 'status-badge-ras'?>">
                        <?=$post->publicado ? 'Publicado' : 'Rascunho'?>
                      </span>
                    <?php endif; ?>
                  </h6>

                  <div class="element-box">

                    <!-- Título -->
                    <div class="form-group">
                      <label>Título do artigo <span class="text-danger">*</span></label>
                      <input type="text" name="titulo" id="titulo" class="form-control form-control-lg"
                             value="<?=htmlspecialchars($post->titulo ?? '')?>"
                             placeholder="Ex: Como organizar prontuários eletrônicos em clínicas"
                             required maxlength="200">
                      <div class="field-hint">Este será o H1 da página. Inclua a palavra-chave principal.</div>
                    </div>

                    <!-- Slug -->
                    <div class="form-group">
                      <label>Slug (URL)</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="font-size:12px"><?=base_url()?>blog/</span>
                        </div>
                        <input type="text" name="slug" id="slug" class="form-control"
                               value="<?=htmlspecialchars($post->slug ?? '')?>"
                               placeholder="como-organizar-prontuarios-eletronicos">
                      </div>
                      <div class="field-hint">Deixe em branco para gerar automaticamente do título. Use apenas letras minúsculas, números e hífens.</div>
                    </div>

                    <!-- Resumo -->
                    <div class="form-group">
                      <label>Resumo / Introdução</label>
                      <textarea name="resumo" id="resumo" class="form-control" rows="3"
                                maxlength="300"
                                placeholder="1–2 frases que descrevem o artigo. Usado no card de listagem e como meta description padrão."><?=htmlspecialchars($post->resumo ?? '')?></textarea>
                      <div class="d-flex justify-content-between">
                        <div class="field-hint">Máx. 165 caracteres para uso como meta description.</div>
                        <span class="seo-counter" id="cnt-resumo">0</span>
                      </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="form-group">
                      <label>Conteúdo do artigo <span class="text-danger">*</span></label>
                      <textarea name="conteudo" id="conteudo" class="form-control content-editor"
                                placeholder="Escreva o HTML do artigo aqui. Use <h2>, <h3>, <p>, <ul>, <strong> etc."><?=($post ? $post->conteudo : '')?></textarea>
                      <div class="field-hint">Aceita HTML. Estruture com &lt;h2&gt; e &lt;h3&gt; para melhor indexação. <a href="#" id="btn-preview-html">Pré-visualizar</a></div>
                    </div>

                  </div>
                </div>

              </div>

              <!-- Coluna lateral -->
              <div class="col-lg-4">

                <!-- Publicação -->
                <div class="element-box">
                  <h6 class="element-header">Publicação</h6>

                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="publicado" name="publicado"
                             value="1" <?=(!empty($post->publicado) ? 'checked' : '')?>>
                      <label class="custom-control-label" for="publicado">Publicar agora</label>
                    </div>
                    <div class="field-hint mt-1">Desmarcado = rascunho (não aparece no site).</div>
                  </div>

                  <div class="form-group">
                    <label>Categoria</label>
                    <select name="id_categoria" class="form-control select2">
                      <option value="">— Sem categoria —</option>
                      <?php foreach ($categorias as $cat): ?>
                      <option value="<?=$cat->id?>"
                        <?=(!empty($post->id_categoria) && $post->id_categoria == $cat->id ? 'selected' : '')?>>
                        <?=htmlspecialchars($cat->nome)?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Autor</label>
                    <input type="text" name="autor" class="form-control"
                           value="<?=htmlspecialchars($post->autor ?? 'UTecnologia Saúde')?>">
                  </div>

                  <div class="form-group">
                    <label>Tempo de leitura (minutos)</label>
                    <input type="number" name="tempo_leitura" class="form-control"
                           value="<?=htmlspecialchars($post->tempo_leitura ?? 5)?>" min="1" max="60">
                  </div>

                  <div class="form-group">
                    <label>Imagem de capa</label>
                    <input type="text" name="imagem_capa" class="form-control"
                           value="<?=htmlspecialchars($post->imagem_capa ?? '')?>"
                           placeholder="imagens/blog/nome-arquivo.jpg">
                    <div class="field-hint">Caminho relativo a partir da raiz do site.</div>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="os-icon os-icon-save"></i>
                    <?=$post ? 'Salvar alterações' : 'Criar artigo'?>
                  </button>

                  <?php if ($post): ?>
                  <a href="<?=base_url()?>adm/blog" class="btn btn-white btn-block mt-2">← Voltar à lista</a>
                  <?php endif; ?>
                </div>

                <!-- SEO -->
                <div class="element-box mt-3">
                  <h6 class="element-header">SEO</h6>

                  <div class="form-group">
                    <label>Título SEO <span class="seo-counter" id="cnt-meta-titulo">0</span></label>
                    <input type="text" name="meta_titulo" id="meta_titulo" class="form-control"
                           value="<?=htmlspecialchars($post->meta_titulo ?? '')?>"
                           maxlength="70"
                           placeholder="Deixe em branco para usar o título do artigo">
                    <div class="field-hint">Ideal: 50–60 caracteres.</div>
                  </div>

                  <div class="form-group">
                    <label>Meta description <span class="seo-counter" id="cnt-meta-desc">0</span></label>
                    <textarea name="meta_descricao" id="meta_descricao" class="form-control" rows="3"
                              maxlength="165"
                              placeholder="Deixe em branco para usar o Resumo"><?=htmlspecialchars($post->meta_descricao ?? '')?></textarea>
                    <div class="field-hint">Ideal: 120–155 caracteres.</div>
                  </div>

                  <!-- Preview SERP -->
                  <div class="tab-pane-label">Pré-visualização Google</div>
                  <div class="seo-preview-box">
                    <p class="seo-preview-title" id="prev-titulo"><?=htmlspecialchars($post->meta_titulo ?: ($post->titulo ?? 'Título do artigo'))?> — UTecnologia Saúde</p>
                    <p class="seo-preview-url">utecnologia.com.br › blog › <span id="prev-slug"><?=htmlspecialchars($post->slug ?? 'slug-do-artigo')?></span></p>
                    <p class="seo-preview-desc" id="prev-desc"><?=htmlspecialchars($post->meta_descricao ?: ($post->resumo ?? 'Descrição do artigo aparecerá aqui.'))?></p>
                  </div>
                </div>

              </div>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal preview HTML -->
<div class="modal fade" id="modal-preview" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pré-visualização do conteúdo</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="preview-body" style="font-family:sans-serif;line-height:1.7;"></div>
    </div>
  </div>
</div>

<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/select2/dist/js/select2.min.js"></script>
<script src="<?=base_url()?>js/main.js"></script>
<script>
$(function(){
  $('.select2').select2({ width:'100%' });

  // Gerar slug do título
  $('#titulo').on('input', function(){
    var slug = $(this).val()
      .toLowerCase()
      .normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .trim()
      .replace(/[\s-]+/g, '-');
    if (!$('#slug').data('manual')) $('#slug').val(slug);
    $('#prev-titulo').text($(this).val() + ' — UTecnologia Saúde');
  });

  $('#slug').on('input', function(){
    $(this).data('manual', true);
    $('#prev-slug').text($(this).val());
  });

  // Contadores SEO
  function updateCounter(inputId, counterId, ideal) {
    var len = $('#' + inputId).val().length;
    var el  = $('#' + counterId);
    el.text(len + ' / ' + ideal + ' ideal');
    el.removeClass('ok warn over');
    if      (len === 0)                        el.addClass('warn');
    else if (len <= ideal)                     el.addClass('ok');
    else                                       el.addClass('over');
  }

  $('#meta_titulo').on('input', function(){
    updateCounter('meta_titulo', 'cnt-meta-titulo', 60);
    $('#prev-titulo').text(($(this).val() || $('#titulo').val()) + ' — UTecnologia Saúde');
  }).trigger('input');

  $('#meta_descricao').on('input', function(){
    updateCounter('meta_descricao', 'cnt-meta-desc', 155);
    $('#prev-desc').text($(this).val() || $('#resumo').val() || 'Descrição do artigo aparecerá aqui.');
  }).trigger('input');

  $('#resumo').on('input', function(){
    updateCounter('resumo', 'cnt-resumo', 165);
    if (!$('#meta_descricao').val()) {
      $('#prev-desc').text($(this).val());
    }
  }).trigger('input');

  // Preview HTML
  $('#btn-preview-html').on('click', function(e){
    e.preventDefault();
    $('#preview-body').html($('#conteudo').val());
    $('#modal-preview').modal('show');
  });
});
</script>
</body>
</html>
