<!DOCTYPE html>
<html>
<head>
  <title><?=$campo ? 'Editar campo' : 'Novo campo'?></title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
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
        <li class="breadcrumb-item"><a href="<?=base_url()?>adm/especialidades">Campos por especialidade</a></li>
        <li class="breadcrumb-item"><span><?=$campo ? 'Editar campo' : 'Novo campo'?></span></li>
      </ul>
      <div class="content-i">
        <div class="content-box">
          <div class="row">
            <div class="col-lg-8">
              <div class="element-wrapper">
                <h6 class="element-header"><?=$campo ? 'Editar campo' : 'Novo campo extra'?></h6>
                <div class="element-box">

                  <?php if($flash_error){ ?>
                    <div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div>
                  <?php } ?>

                  <form method="post" action="<?=base_url()?>adm/especialidades/salvar">
                    <input type="hidden" name="id" value="<?=$campo ? $campo->id : 0?>">

                    <div class="form-group">
                      <label class="mws-form-label">Especialidade <span style="color:#e53e3e">*</span></label>
                      <select name="especialidade_id" id="especialidade_id" class="form-control" required>
                        <option value="">— Selecione —</option>
                        <?php foreach($especialidades as $esp){
                          $sel_esp = ($campo && (int)$campo->especialidade_id === (int)$esp->id)
                                  || (!$campo && (int)$esp_presel === (int)$esp->id);
                        ?>
                          <option value="<?=$esp->id?>" <?=$sel_esp?'selected':''?>><?=htmlspecialchars($esp->nome)?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="mws-form-label">
                            Chave do campo <span style="color:#e53e3e">*</span>
                            <small style="color:#9bb1cc;font-weight:400"> — slug único por especialidade</small>
                          </label>
                          <input type="text" name="campo_chave" id="campo_chave" class="form-control"
                                 placeholder="ex: eva_dor, dentes_tratados"
                                 value="<?=$campo ? htmlspecialchars($campo->campo_chave) : ''?>"
                                 pattern="[a-z0-9_]+" title="Apenas letras minúsculas, números e underline"
                                 required <?=$campo ? 'readonly' : ''?>>
                          <small class="form-text text-muted">Somente a–z, 0–9 e _ (underline). Não pode ser alterada depois.</small>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="mws-form-label">Label (nome exibido) <span style="color:#e53e3e">*</span></label>
                          <input type="text" name="campo_label" class="form-control"
                                 placeholder="ex: Escala de Dor (EVA 0–10)"
                                 value="<?=$campo ? htmlspecialchars($campo->campo_label) : ''?>"
                                 required>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="mws-form-label">Tipo de campo <span style="color:#e53e3e">*</span></label>
                          <select name="campo_tipo" id="campo_tipo" class="form-control" required>
                            <?php
                            $tipos = [
                              'text'     => 'Texto curto (input)',
                              'textarea' => 'Texto longo (textarea)',
                              'number'   => 'Número',
                              'select'   => 'Lista de opções (select)',
                              'radio'    => 'Opção única (radio)',
                            ];
                            foreach($tipos as $val => $label){
                              $sel = ($campo && $campo->campo_tipo === $val) ? 'selected' : '';
                            ?>
                              <option value="<?=$val?>" <?=$sel?>><?=$label?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="mws-form-label">Placeholder <small style="color:#9bb1cc;font-weight:400">(opcional)</small></label>
                          <input type="text" name="campo_placeholder" class="form-control"
                                 placeholder="Texto de ajuda dentro do campo"
                                 value="<?=$campo ? htmlspecialchars((string)$campo->campo_placeholder) : ''?>">
                        </div>
                      </div>
                    </div>

                    <!-- Opções — exibido apenas para select/radio -->
                    <div id="bloco_opcoes" class="form-group" style="display:none">
                      <label class="mws-form-label">
                        Opções do campo
                        <small style="color:#9bb1cc;font-weight:400"> — uma opção por linha</small>
                      </label>
                      <textarea name="campo_opcoes_texto" id="campo_opcoes_texto" class="form-control" rows="7"
                                placeholder="Opção 1&#10;Opção 2&#10;Opção 3"><?=htmlspecialchars($opcoes_texto)?></textarea>
                      <small class="form-text text-muted">Digite uma opção por linha. Serão salvas como JSON.</small>
                    </div>

                    <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label class="mws-form-label">Ordem de exibição</label>
                          <input type="number" name="ordem" class="form-control"
                                 value="<?=$campo ? (int)$campo->ordem : 10?>" min="0" step="10">
                          <small class="form-text text-muted">Menor número = aparece primeiro.</small>
                        </div>
                      </div>
                      <div class="col-sm-4" style="padding-top:32px">
                        <div class="form-group">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="checkbox" name="obrigatorio" class="form-check-input"
                                     value="1" <?=($campo && $campo->obrigatorio) ? 'checked' : ''?>>
                              Campo obrigatório
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4" style="padding-top:32px">
                        <div class="form-group">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="checkbox" name="status" class="form-check-input"
                                     value="1" <?=(!$campo || $campo->status) ? 'checked' : ''?>>
                              Campo ativo
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex" style="gap:10px;margin-top:8px">
                      <button type="submit" class="btn btn-primary">Salvar campo</button>
                      <a href="<?=base_url()?>adm/especialidades" class="btn btn-outline-secondary">Cancelar</a>
                    </div>

                  </form>
                </div>
              </div>
            </div>

            <!-- Dica lateral -->
            <div class="col-lg-4">
              <div class="element-wrapper">
                <div class="element-box" style="background:#f9fbff;border:1px solid #d7e3f7">
                  <h6 style="color:#183153;font-size:13px;font-weight:700;margin-bottom:12px">Como funciona</h6>
                  <p style="font-size:13px;color:#5a6a85;margin-bottom:10px">
                    Campos configurados aqui aparecerão no prontuário de atendimento
                    quando o prestador tiver a especialidade correspondente.
                  </p>
                  <p style="font-size:13px;color:#5a6a85;margin-bottom:10px">
                    Os valores preenchidos são salvos em <code>agendamentos.campos_extras</code> como JSON,
                    usando a <strong>chave do campo</strong> como chave do objeto.
                  </p>
                  <hr style="border-color:#d7e3f7">
                  <p style="font-size:12px;color:#8fa3bf;margin-bottom:6px"><strong>Tipos disponíveis:</strong></p>
                  <ul style="font-size:12px;color:#7d8faa;padding-left:16px;margin:0">
                    <li><strong>Texto curto</strong> — input de linha única</li>
                    <li><strong>Texto longo</strong> — textarea livre</li>
                    <li><strong>Número</strong> — campo numérico (ex: EVA, PA)</li>
                    <li><strong>Seleção</strong> — dropdown com suas opções</li>
                    <li><strong>Opção única</strong> — radio buttons</li>
                  </ul>
                </div>
              </div>
            </div>

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
  $('#especialidade_id').select2({ placeholder: '— Selecione —', width: '100%' });

  function toggleOpcoes(){
    var tipo = $('#campo_tipo').val();
    if(tipo === 'select' || tipo === 'radio'){
      $('#bloco_opcoes').show();
    } else {
      $('#bloco_opcoes').hide();
    }
  }

  // Auto-slug na chave
  if(!$('#campo_chave').prop('readonly')){
    $('#campo_chave').on('input', function(){
      $(this).val($(this).val().toLowerCase().replace(/[^a-z0-9_]/g, ''));
    });
  }

  $('#campo_tipo').on('change', toggleOpcoes);
  toggleOpcoes();
});
</script>
</body>
</html>
