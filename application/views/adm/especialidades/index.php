<!DOCTYPE html>
<html>
<head>
  <title>Campos por Especialidade</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <style>
    .esp-section { margin-bottom: 32px; }
    .esp-header {
      align-items: center;
      background: #f4f8ff;
      border: 1px solid #d7e3f7;
      border-radius: 12px 12px 0 0;
      display: flex;
      gap: 12px;
      justify-content: space-between;
      padding: 14px 18px;
    }
    .esp-header h6 { color: #183153; font-size: 15px; font-weight: 700; margin: 0; }
    .esp-table { border: 1px solid #d7e3f7; border-top: none; border-radius: 0 0 12px 12px; overflow: hidden; }
    .esp-table table { margin-bottom: 0; }
    .esp-table thead th { background: #fff; border-bottom: 2px solid #e8eef8; color: #5a6a85; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .badge-tipo { border-radius: 6px; font-size: 11px; font-weight: 600; padding: 3px 8px; }
    .badge-tipo-text     { background: #e8f4ff; color: #1a6fc4; }
    .badge-tipo-textarea { background: #e8fff0; color: #15804d; }
    .badge-tipo-number   { background: #fff4e6; color: #b86d00; }
    .badge-tipo-select   { background: #f4e8ff; color: #7c3aed; }
    .badge-tipo-radio    { background: #ffe8f0; color: #be185d; }
    .filter-bar { align-items: center; display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .empty-state { background: #f9fbff; border: 1px dashed #c8d8f0; border-radius: 12px; color: #7d8faa; padding: 20px; text-align: center; }
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
        <li class="breadcrumb-item"><span>Campos por Especialidade</span></li>
      </ul>
      <div class="content-i">
        <div class="content-box">
          <div class="element-wrapper">
            <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;margin-bottom:20px">
              <div>
                <h6 class="element-header" style="margin-bottom:4px">Campos extras por especialidade</h6>
                <p style="margin:0;color:#5f708c;font-size:13px">Defina quais campos adicionais aparecem no prontuário de atendimento conforme a especialidade do prestador.</p>
              </div>
              <a href="<?=base_url()?>adm/especialidades/form" class="btn btn-primary">+ Novo campo</a>
            </div>

            <?php if($flash_ok){ ?>
              <div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div>
            <?php } ?>
            <?php if($flash_error){ ?>
              <div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div>
            <?php } ?>

            <!-- Filtro por especialidade -->
            <div class="filter-bar">
              <label style="font-size:13px;color:#5a6a85;margin:0">Filtrar por especialidade:</label>
              <select id="filtro-esp" class="form-control" style="max-width:280px">
                <option value="">Todas</option>
                <?php foreach($especialidades as $esp){ if(isset($campos_por_esp[$esp->id])){ ?>
                  <option value="esp-<?=$esp->id?>"><?=htmlspecialchars($esp->nome)?></option>
                <?php } } ?>
              </select>
            </div>

            <?php
            $tipo_map = [
              'text'     => ['badge-tipo-text',     'Texto'],
              'textarea' => ['badge-tipo-textarea',  'Texto longo'],
              'number'   => ['badge-tipo-number',    'Número'],
              'select'   => ['badge-tipo-select',    'Seleção'],
              'radio'    => ['badge-tipo-radio',     'Opção única'],
            ];
            ?>

            <?php foreach($especialidades as $esp){
              if(!isset($campos_por_esp[$esp->id])){ continue; }
              $esp_campos = $campos_por_esp[$esp->id];
            ?>
            <div class="esp-section" id="esp-<?=$esp->id?>">
              <div class="esp-header">
                <h6><?=htmlspecialchars($esp->nome)?> <small style="color:#8fa3bf;font-weight:400">(<?=count($esp_campos)?> campo<?=count($esp_campos)!==1?'s':''?>)</small></h6>
                <a href="<?=base_url()?>adm/especialidades/form?esp=<?=$esp->id?>" class="btn btn-sm btn-outline-primary">+ Campo</a>
              </div>
              <div class="esp-table">
                <table class="table table-sm table-hover" style="background:#fff">
                  <thead>
                    <tr>
                      <th style="width:140px">Chave</th>
                      <th>Label</th>
                      <th style="width:120px">Tipo</th>
                      <th style="width:60px;text-align:center">Ordem</th>
                      <th style="width:80px;text-align:center">Obrig.</th>
                      <th style="width:80px;text-align:center">Status</th>
                      <th style="width:120px;text-align:right">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($esp_campos as $c){ ?>
                    <tr style="<?=$c->status ? '' : 'opacity:.5'?>">
                      <td><code style="font-size:12px;color:#5a6a85"><?=htmlspecialchars($c->campo_chave)?></code></td>
                      <td>
                        <?=htmlspecialchars($c->campo_label)?>
                        <?php if($c->campo_placeholder){ ?>
                          <small style="color:#9bb1cc;display:block"><?=htmlspecialchars($c->campo_placeholder)?></small>
                        <?php } ?>
                        <?php if($c->campo_tipo === 'select' && $c->campo_opcoes){
                          $opts = json_decode($c->campo_opcoes, true);
                          if(is_array($opts)){ ?>
                          <small style="color:#aaa"><?=implode(' · ', array_map('htmlspecialchars', array_slice($opts, 0, 4))).(count($opts)>4?' +'.( count($opts)-4).'':'') ?></small>
                        <?php } } ?>
                      </td>
                      <td>
                        <?php $tipo_info = isset($tipo_map[$c->campo_tipo]) ? $tipo_map[$c->campo_tipo] : ['badge-tipo-text','?']; ?>
                        <span class="badge-tipo <?=$tipo_info[0]?>"><?=$tipo_info[1]?></span>
                      </td>
                      <td style="text-align:center"><?=$c->ordem?></td>
                      <td style="text-align:center"><?=$c->obrigatorio ? '<span style="color:#e53e3e">Sim</span>' : '<span style="color:#aaa">Não</span>'?></td>
                      <td style="text-align:center">
                        <a href="<?=base_url()?>adm/especialidades/toggle_status/<?=$c->id?>" title="<?=$c->status?'Desativar':'Ativar'?>" style="text-decoration:none">
                          <?php if($c->status){ ?>
                            <span style="color:#16874b;font-weight:700">Ativo</span>
                          <?php } else { ?>
                            <span style="color:#888">Inativo</span>
                          <?php } ?>
                        </a>
                      </td>
                      <td style="text-align:right;white-space:nowrap">
                        <a href="<?=base_url()?>adm/especialidades/form/<?=$c->id?>" class="btn btn-sm btn-outline-secondary" style="margin-right:4px">Editar</a>
                        <a href="<?=base_url()?>adm/especialidades/deletar/<?=$c->id?>" class="btn btn-sm btn-outline-danger btn-del" data-confirm="Remover o campo &quot;<?=htmlspecialchars($c->campo_label)?>&quot;?">Del</a>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <?php } ?>

            <?php if(empty($campos_por_esp)){ ?>
            <div class="empty-state">
              <p style="margin:0">Nenhum campo configurado ainda. <a href="<?=base_url()?>adm/especialidades/form">Adicione o primeiro campo.</a></p>
            </div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
<script>
$(function(){
  // Filtro por especialidade
  $('#filtro-esp').on('change', function(){
    var val = $(this).val();
    if(!val){
      $('.esp-section').show();
    } else {
      $('.esp-section').hide();
      $('#'+val).show();
    }
  });

  // Confirmação antes de deletar
  $('.btn-del').on('click', function(e){
    var msg = $(this).data('confirm') || 'Confirma a remoção?';
    if(!confirm(msg)){ e.preventDefault(); }
  });
});
</script>
</body>
</html>
