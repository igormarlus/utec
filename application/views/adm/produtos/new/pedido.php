<!DOCTYPE html>
<html>
  <head>
    <title>Detalhes da Assinatura | UTEC</title>
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
                <h6 class="element-header">Detalhes da contratacao</h6>
                <? $pedido_item = $pedido->num_rows() ? $pedido->row() : null; ?>
                <? if($pedido_item){ ?>
                  <p><strong>Hash da assinatura:</strong> <?=$id_pedido?></p>
                  <p><strong>Valor total:</strong> R$ <?=number_format((float)$pedido_item->total, 2, ',', '.')?></p>
                  <div class="table-responsive">
                    <table class="table table-lightborder">
                      <thead>
                        <tr>
                          <th>Plano</th>
                          <th>Quantidade</th>
                          <th>Valor unitario</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <? foreach($carrinhos->result() as $item){ ?>
                          <? $plano = $this->padrao_model->get_by_id($item->id_produto, 'produtos'); ?>
                          <? $plano_nome = $plano->num_rows() ? $plano->row()->modelo : 'Plano removido'; ?>
                          <tr>
                            <td><?=$plano_nome?></td>
                            <td><?=$item->qtd?></td>
                            <td>R$ <?=number_format((float)$item->valor, 2, ',', '.')?></td>
                            <td>R$ <?=number_format((float)$item->total, 2, ',', '.')?></td>
                          </tr>
                        <? } ?>
                      </tbody>
                    </table>
                  </div>
                <? } else { ?>
                  <p>Nenhuma contratacao encontrada.</p>
                <? } ?>
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
