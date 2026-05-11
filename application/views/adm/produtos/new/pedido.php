<!DOCTYPE html>
<html>
  <head>
    <title>Detalhes da Assinatura | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin:18px 0 20px; }
      .info-card { background:#f8fbff; border:1px solid #dbe4f0; border-radius:16px; padding:16px 18px; }
      .info-label { color:#64748b; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
      .info-value { color:#0f172a; font-size:18px; font-weight:700; margin-top:6px; }
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
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/produtos/rel_pedidos">Assinaturas</a></li>
            <li class="breadcrumb-item"><span>Detalhes</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-box">
                <h6 class="element-header">Detalhes da contratacao</h6>
                <? $pedido_item = $pedido->num_rows() ? $pedido->row() : null; ?>
                <? if($pedido_item){ ?>
                  <? $cliente = $this->padrao_model->get_by_id($pedido_item->id_user, 'usuarios'); ?>
                  <? $responsavel = $this->padrao_model->get_by_id($pedido_item->id_cliente, 'usuarios'); ?>
                  <div class="info-grid">
                    <div class="info-card">
                      <div class="info-label">Hash da assinatura</div>
                      <div class="info-value"><?=$id_pedido?></div>
                    </div>
                    <div class="info-card">
                      <div class="info-label">Valor total</div>
                      <div class="info-value">R$ <?=number_format((float)$pedido_item->total, 2, ',', '.')?></div>
                    </div>
                    <div class="info-card">
                      <div class="info-label">Cliente</div>
                      <div class="info-value"><?=$cliente->num_rows() ? $cliente->row()->nome : 'Nao identificado'?></div>
                    </div>
                    <div class="info-card">
                      <div class="info-label">Responsavel</div>
                      <div class="info-value"><?=$responsavel->num_rows() ? $responsavel->row()->nome : 'Nao identificado'?></div>
                    </div>
                    <div class="info-card">
                      <div class="info-label">Status</div>
                      <div class="info-value">
                        <? if((int)$pedido_item->status === 2){ ?>
                          <span class="status-pill status-ativa">Ativa</span>
                        <? } elseif((int)$pedido_item->status === 1){ ?>
                          <span class="status-pill status-analise">Em analise</span>
                        <? } else { ?>
                          <span class="status-pill status-pendente">Pendente</span>
                        <? } ?>
                      </div>
                    </div>
                    <div class="info-card">
                      <div class="info-label">Data da contratacao</div>
                      <div class="info-value"><?=$pedido_item->dt ? substr($pedido_item->dt,0,16) : 'Nao informada'?></div>
                    </div>
                  </div>
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
