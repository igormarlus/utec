<!DOCTYPE html>
<html>
  <head>
    <title>Editar Plano | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
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
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/produtos">Planos</a></li>
            <li class="breadcrumb-item"><span>Editar</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-box">
                <h6 class="element-header">Editar plano</h6>
                <form method="post" action="<?=base_url()?>adm/produtos/editar">
                  <input type="hidden" name="id" value="<?=$produto->id?>">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Nome do plano</label>
                      <input type="text" name="modelo" class="form-control" value="<?=htmlspecialchars($produto->modelo)?>" required>
                    </div>
                    <div class="col-md-3">
                      <label>Tipo de plano</label>
                      <select name="id_categoria" class="form-control">
                        <? foreach($categorias->result() as $categoria){ ?>
                          <option value="<?=$categoria->id?>" <?=$produto->id_categoria == $categoria->id ? 'selected="selected"' : ''?>><?=$categoria->nome?></option>
                        <? } ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label>Valor base</label>
                      <input type="text" name="preco" class="form-control" value="<?=number_format((float)$produto->preco, 2, ',', '.')?>">
                    </div>
                    <div class="col-md-2">
                      <label>Valor de venda</label>
                      <input type="text" name="preco_venda" class="form-control" value="<?=number_format((float)$produto->preco_venda, 2, ',', '.')?>">
                    </div>
                    <div class="col-md-1">
                      <label>Status</label>
                      <input type="text" name="status" class="form-control" value="<?=$produto->status?>">
                    </div>
                  </div>
                  <div class="row" style="margin-top:14px">
                    <div class="col-md-2">
                      <label>Limite/quantidade</label>
                      <input type="text" name="qtd" class="form-control" value="<?=$produto->qtd?>">
                    </div>
                    <div class="col-md-2">
                      <label>Codigo</label>
                      <input type="text" name="codigo" class="form-control" value="<?=$produto->codigo?>">
                    </div>
                    <div class="col-md-8">
                      <label>Descricao comercial</label>
                      <textarea name="especificacoes" class="form-control" rows="5"><?=htmlspecialchars($produto->especificacoes)?></textarea>
                    </div>
                  </div>
                  <div style="margin-top:16px">
                    <button type="submit" class="btn btn-primary">Salvar plano</button>
                    <a href="<?=base_url()?>adm/produtos" class="btn btn-secondary">Voltar</a>
                  </div>
                </form>
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
