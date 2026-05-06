<!DOCTYPE html>
<html>
  <head>
    <title>Editar Tipo de Plano | UTEC</title>
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
          <div class="content-i">
            <div class="content-box">
              <div class="element-box">
                <h6 class="element-header">Editar tipo de plano</h6>
                <form method="post" action="<?=base_url()?>adm/produtos/cadastrar_categoria/edit">
                  <input type="hidden" name="id" value="<?=$categoria->id?>">
                  <div class="row">
                    <div class="col-md-6">
                      <label>Nome</label>
                      <input type="text" name="nome" class="form-control" value="<?=htmlspecialchars($categoria->nome)?>" required>
                    </div>
                    <div class="col-md-2">
                      <label>Status</label>
                      <select name="status" class="form-control">
                        <option value="1" <?=$categoria->status == 1 ? 'selected="selected"' : ''?>>Ativo</option>
                        <option value="0" <?=$categoria->status == 0 ? 'selected="selected"' : ''?>>Inativo</option>
                      </select>
                    </div>
                  </div>
                  <div style="margin-top:16px">
                    <button type="submit" class="btn btn-primary">Salvar tipo</button>
                    <a href="<?=base_url()?>adm/produtos/categorias" class="btn btn-secondary">Voltar</a>
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
