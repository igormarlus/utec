<!DOCTYPE html>
<html>
  <head>
    <title>Assinaturas | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .summary-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); margin:18px 0 22px; }
      .summary-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); padding:18px 20px; }
      .summary-label { color:#64748b; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
      .summary-value { color:#0f172a; font-size:28px; font-weight:700; margin-top:6px; }
      .filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); margin-bottom:20px; padding:18px 20px; }
      .status-pill { border-radius:999px; display:inline-block; font-size:11px; font-weight:700; padding:5px 10px; text-transform:uppercase; }
      .status-pendente { background:#fee2e2; color:#b91c1c; }
      .status-analise { background:#dbeafe; color:#1d4ed8; }
      .status-ativa { background:#dcfce7; color:#166534; }
      .muted { color:#64748b; font-size:12px; }
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
              <div class="summary-grid">
                <div class="summary-card">
                  <div class="summary-label">Assinaturas</div>
                  <div class="summary-value"><?=$resumo_assinaturas['total']?></div>
                </div>
                <div class="summary-card">
                  <div class="summary-label">Ativas</div>
                  <div class="summary-value"><?=$resumo_assinaturas['ativas']?></div>
                </div>
                <div class="summary-card">
                  <div class="summary-label">Em analise</div>
                  <div class="summary-value"><?=$resumo_assinaturas['analise']?></div>
                </div>
                <div class="summary-card">
                  <div class="summary-label">Pendentes</div>
                  <div class="summary-value"><?=$resumo_assinaturas['pendentes']?></div>
                </div>
                <div class="summary-card">
                  <div class="summary-label">Valor total</div>
                  <div class="summary-value">R$ <?=number_format((float)$resumo_assinaturas['total_valor'], 2, ',', '.')?></div>
                </div>
              </div>
              <div class="filter-card">
                <form method="get" action="<?=base_url()?>adm/produtos/rel_pedidos">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Buscar</label>
                      <input type="text" name="busca" class="form-control" value="<?=htmlspecialchars($filtros['busca'])?>" placeholder="Cliente, responsavel ou hash da assinatura">
                    </div>
                    <div class="col-md-2">
                      <label>Status</label>
                      <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="0" <?=$filtros['status'] === '0' ? 'selected="selected"' : ''?>>Pendente</option>
                        <option value="1" <?=$filtros['status'] === '1' ? 'selected="selected"' : ''?>>Em analise</option>
                        <option value="2" <?=$filtros['status'] === '2' ? 'selected="selected"' : ''?>>Ativa</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label>Data inicial</label>
                      <input type="date" name="data_ini" class="form-control" value="<?=htmlspecialchars($filtros['data_ini'])?>">
                    </div>
                    <div class="col-md-2">
                      <label>Data final</label>
                      <input type="date" name="data_fim" class="form-control" value="<?=htmlspecialchars($filtros['data_fim'])?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                      <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="element-box">
                <div class="table-responsive">
                  <table class="table table-lightborder">
                    <thead>
                      <tr>
                        <th>Assinatura</th>
                        <th>Cliente</th>
                        <th>Responsavel</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <? if($pedidos_finalizados->num_rows() > 0){ foreach($pedidos_finalizados->result() as $pedido){ ?>
                        <tr>
                          <td>
                            <strong>#<?=$pedido->id?></strong><br>
                            <span class="muted"><?=$pedido->id_pedido?></span>
                          </td>
                          <td>
                            <?=$pedido->cliente_nome ? $pedido->cliente_nome : 'Nao identificado'?>
                          </td>
                          <td><?=$pedido->responsavel_nome ? $pedido->responsavel_nome : 'Nao identificado'?></td>
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
                          <td><a href="<?=base_url()?>adm/produtos/pedido/<?=$pedido->id_pedido?>" class="btn btn-sm btn-outline-primary">Detalhes</a></td>
                        </tr>
                      <? } } else { ?>
                        <tr><td colspan="7">Nenhuma assinatura encontrada para os filtros informados.</td></tr>
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
