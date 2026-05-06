<!DOCTYPE html>
<html>
  <head>
    <title>Tipos de Plano | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .page-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); margin-bottom:24px; }
      .page-card-header { padding:18px 22px 0; }
      .page-card-body { padding:18px 22px 22px; }
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
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/produtos">Planos</a></li>
            <li class="breadcrumb-item"><span>Tipos</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="page-card">
                <div class="page-card-header"><h6 class="element-header" style="margin-bottom:0">Novo tipo de plano</h6></div>
                <div class="page-card-body">
                  <form method="post" action="<?=base_url()?>adm/produtos/cadastrar_categoria">
                    <div class="row">
                      <div class="col-md-6">
                        <label>Nome do tipo</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Mensal, Clinica, Premium" required>
                      </div>
                      <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                          <option value="1">Ativo</option>
                          <option value="0">Inativo</option>
                        </select>
                      </div>
                      <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="page-card">
                <div class="page-card-header"><h6 class="element-header" style="margin-bottom:0">Tipos cadastrados</h6></div>
                <div class="page-card-body">
                  <div class="table-responsive">
                    <table class="table table-lightborder">
                      <thead>
                        <tr>
                          <th>Nome</th>
                          <th>Status</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <? if($categorias->num_rows() > 0){ foreach($categorias->result() as $categoria){ ?>
                          <tr>
                            <td><?=$categoria->nome?></td>
                            <td><?=$categoria->status == 1 ? 'Ativo' : 'Inativo'?></td>
                            <td><a href="<?=base_url()?>adm/produtos/edicao_cat/<?=$categoria->id?>" class="btn btn-sm btn-outline-primary">Editar</a></td>
                          </tr>
                        <? } } else { ?>
                          <tr><td colspan="3">Nenhum tipo cadastrado.</td></tr>
                        <? } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
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
