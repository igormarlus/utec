<!DOCTYPE html>
<html>
  <head>
    <title>Planos | UTEC</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="planos assinaturas utec saude" name="keywords">
    <meta content="Catalogo de planos da plataforma com configuracao comercial." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .commerce-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        margin-bottom: 24px;
      }
      .commerce-card-header {
        padding: 18px 22px 0;
      }
      .commerce-card-body {
        padding: 18px 22px 22px;
      }
      .plan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
      }
      .plan-card {
        border: 1px solid #dbe4f0;
        border-radius: 18px;
        padding: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      }
      .plan-title {
        color: #0f172a;
        font-size: 20px;
        font-weight: 700;
      }
      .plan-price {
        color: #0f172a;
        font-size: 28px;
        font-weight: 700;
        margin: 10px 0 4px;
      }
      .plan-meta {
        color: #64748b;
        font-size: 13px;
      }
      .plan-description {
        color: #475569;
        font-size: 14px;
        margin-top: 14px;
        min-height: 70px;
      }
      .status-pill {
        border-radius: 999px;
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 10px;
        text-transform: uppercase;
      }
      .status-active { background: #dcfce7; color: #166534; }
      .status-inactive { background: #fee2e2; color: #b91c1c; }
      .form-note {
        color: #64748b;
        font-size: 13px;
      }
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
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Painel</a>
            </li>
            <li class="breadcrumb-item">
              <span>Planos</span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <div class="element-actions">
                  <a href="<?=base_url()?>adm/produtos/categorias" class="btn btn-outline-primary btn-sm">Gerenciar tipos</a>
                </div>
                <h6 class="element-header">Catalogo de planos</h6>
                <p class="form-note">Use este modulo para cadastrar os planos comercializados pela plataforma e controlar o que sera contratado pelos clientes.</p>
              </div>

              <div class="commerce-card">
                <div class="commerce-card-header">
                  <h6 class="element-header" style="margin-bottom:0">Novo plano</h6>
                </div>
                <div class="commerce-card-body">
                  <form method="post" action="<?=base_url()?>adm/produtos/cadastrar">
                    <div class="row">
                      <div class="col-md-4">
                        <label>Nome do plano</label>
                        <input type="text" name="modelo" class="form-control" placeholder="Ex: Plano Essencial" required>
                      </div>
                      <div class="col-md-3">
                        <label>Tipo de plano</label>
                        <select name="id_categoria" class="form-control" required>
                          <option value="">Selecione</option>
                          <? foreach($categorias->result() as $categoria){ ?>
                            <option value="<?=$categoria->id?>"><?=$categoria->nome?></option>
                          <? } ?>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label>Valor base</label>
                        <input type="text" name="preco" class="form-control" placeholder="0,00">
                      </div>
                      <div class="col-md-2">
                        <label>Valor de venda</label>
                        <input type="text" name="preco_venda" class="form-control" placeholder="0,00">
                      </div>
                      <div class="col-md-1">
                        <label>Status</label>
                        <select name="status" class="form-control">
                          <option value="1">Ativo</option>
                          <option value="0">Inativo</option>
                        </select>
                      </div>
                    </div>
                    <div class="row" style="margin-top:14px">
                      <div class="col-md-2">
                        <label>Limite/quantidade</label>
                        <input type="text" name="qtd" class="form-control" placeholder="0">
                      </div>
                      <div class="col-md-2">
                        <label>Codigo interno</label>
                        <input type="text" name="codigo" class="form-control" placeholder="PLN-001">
                      </div>
                      <div class="col-md-8">
                        <label>Descricao comercial</label>
                        <textarea name="especificacoes" class="form-control" rows="3" placeholder="Descreva recursos inclusos, limites e observacoes do plano."></textarea>
                      </div>
                    </div>
                    <div style="margin-top:16px">
                      <button type="submit" class="btn btn-primary">Cadastrar plano</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="plan-grid">
                <? if($produtos->num_rows() > 0){ foreach($produtos->result() as $plano){ ?>
                  <div class="plan-card">
                    <div class="d-flex justify-content-between align-items-start" style="gap:12px">
                      <div class="plan-title"><?=$plano->modelo?></div>
                      <span class="status-pill <?=$plano->status == 1 ? 'status-active' : 'status-inactive'?>">
                        <?=$plano->status == 1 ? 'Ativo' : 'Inativo'?>
                      </span>
                    </div>
                    <div class="plan-price">R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?></div>
                    <div class="plan-meta">
                      Tipo: <?=$plano->nome ? $plano->nome : 'Nao definido'?> · Codigo: <?=$plano->codigo ? $plano->codigo : 'Sem codigo'?>
                    </div>
                    <div class="plan-description">
                      <?=trim(strip_tags($plano->especificacoes)) !== '' ? nl2br(htmlspecialchars($plano->especificacoes)) : 'Sem descricao comercial cadastrada para este plano.'?>
                    </div>
                    <div class="plan-meta">Valor base: R$ <?=number_format((float)$plano->preco, 2, ',', '.')?> · Limite: <?=$plano->qtd ? $plano->qtd : 'Nao definido'?></div>
                    <div style="margin-top:16px">
                      <a href="<?=base_url()?>adm/produtos/edicao/<?=$plano->id?>" class="btn btn-sm btn-outline-primary">Editar plano</a>
                    </div>
                  </div>
                <? } } else { ?>
                  <div class="commerce-card">
                    <div class="commerce-card-body">
                      Nenhum plano cadastrado ainda. Cadastre o primeiro plano para iniciar o modulo comercial.
                    </div>
                  </div>
                <? } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="display-type"></div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/slick-carousel/slick/slick.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/alert.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/button.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/modal.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tab.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tooltip.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/popover.js"></script>
    <script src="<?=base_url()?>js/demo_customizer.js?version=4.5.0"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
