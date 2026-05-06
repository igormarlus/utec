<!DOCTYPE html>
<html>
  <head>
    <title>Assinaturas | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .status-pill { border-radius:999px; display:inline-block; font-size:11px; font-weight:700; padding:5px 10px; text-transform:uppercase; }
      .status-pendente { background:#fee2e2; color:#b91c1c; }
      .status-analise { background:#dbeafe; color:#1d4ed8; }
      .status-ativa { background:#dcfce7; color:#166534; }
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
            <li class="breadcrumb-item"><span>Assinaturas</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <h6 class="element-header">Contratacoes e assinaturas</h6>
                <p style="color:#64748b">Acompanhe as adesoes aos planos, status comerciais e detalhes de cada contratacao.</p>
              </div>
              <div class="element-box">
                <div class="table-responsive">
                  <table class="table table-lightborder">
                    <thead>
                      <tr>
                        <th>Assinatura</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <? if($pedidos_finalizados->num_rows() > 0){ foreach($pedidos_finalizados->result() as $pedido){ ?>
                        <tr>
                          <td>#<?=$pedido->id?></td>
                          <td>
                            <? $cliente = $this->padrao_model->get_by_id($pedido->id_comprador, 'usuarios'); ?>
                            <?=$cliente->num_rows() ? $cliente->row()->nome : 'Nao identificado'?>
                          </td>
                          <td><?=$pedido->dt ? substr($pedido->dt,0,16) : 'Nao informada'?></td>
                          <td>R$ <?=number_format((float)$pedido->total, 2, ',', '.')?></td>
                          <td>
                            <? if((int)$pedido->status === 2){ ?>
                              <span class="status-pill status-ativa">Ativa</span>
                            <? } elseif((int)$pedido->status === 1){ ?>
                              <span class="status-pill status-analise">Em analise</span>
                            <? } else { ?>
                              <span class="status-pill status-pendente">Pendente</span>
                            <? } ?>
                          </td>
                          <td><a href="<?=base_url()?>adm/usuarios/pedido/<?=$pedido->id_pedido?>" class="btn btn-sm btn-outline-primary">Detalhes</a></td>
                        </tr>
                      <? } } else { ?>
                        <tr><td colspan="6">Nenhuma assinatura registrada ainda.</td></tr>
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
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
