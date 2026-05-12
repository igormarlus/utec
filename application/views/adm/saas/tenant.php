<!DOCTYPE html>
<html>
  <head>
    <title>Tenant | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
      .info-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); padding:18px 20px; }
      .info-label { color:#64748b; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
      .info-value { color:#0f172a; font-size:20px; font-weight:700; margin-top:8px; }
      .panel-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); margin-bottom:24px; }
      .panel-card-header { padding:18px 20px 0; }
      .panel-card-body { padding:18px 20px 20px; }
      .status-pill { display:inline-block; border-radius:999px; font-size:11px; font-weight:700; padding:5px 10px; text-transform:uppercase; }
      .status-active { background:#dcfce7; color:#166534; }
      .status-trial { background:#dbeafe; color:#1d4ed8; }
      .status-past-due { background:#fef3c7; color:#92400e; }
      .status-paused { background:#ede9fe; color:#5b21b6; }
      .status-pending { background:#e2e8f0; color:#475569; }
      .status-canceled { background:#fee2e2; color:#b91c1c; }
      .onboarding-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); margin-bottom:24px; padding:20px; }
      .onboarding-progress { width:100%; height:12px; border-radius:999px; background:#e5edf5; overflow:hidden; margin-top:14px; }
      .onboarding-fill { height:100%; background:linear-gradient(90deg,#0ea5e9,#22c55e); }
      .onboarding-grid { display:grid; gap:10px; margin-top:18px; }
      .onboarding-item { display:grid; grid-template-columns:40px 1fr; gap:12px; align-items:flex-start; border:1px solid #e2e8f0; border-radius:14px; padding:14px; background:#f8fafc; }
      .onboarding-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px; }
      .onboarding-item.done .onboarding-icon { background:#dcfce7; color:#166534; }
      .onboarding-item.pending .onboarding-icon { background:#fff7ed; color:#c2410c; }
      .onboarding-title { font-size:16px; font-weight:700; color:#0f172a; }
      .onboarding-copy { color:#64748b; font-size:13px; margin-top:4px; }
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
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/saas">Operacao SaaS</a></li>
            <li class="breadcrumb-item"><span><?=$tenant->tenant_nome?></span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <h6 class="element-header"><?=$tenant->tenant_nome?></h6>
                <p style="color:#64748b">Tenant provisionado para operacao SaaS, com equipe, assinatura e historico de cobranca.</p>
              </div>

              <? if(isset($flash_ok) && $flash_ok){ ?><div class="alert alert-success"><?=$flash_ok?></div><? } ?>
              <? if(isset($flash_error) && $flash_error){ ?><div class="alert alert-danger"><?=$flash_error?></div><? } ?>
              <? if(isset($mercadopago_ready) && !$mercadopago_ready){ ?><div class="alert alert-warning">O arquivo <strong>application/config/mercadopago.php</strong> ainda nao existe neste servidor. Checkout e sincronizacao Mercado Pago ficam indisponiveis ate ele ser publicado.</div><? } ?>

              <div class="info-grid">
                <div class="info-card"><div class="info-label">Responsavel</div><div class="info-value"><?=$tenant->owner_nome ? $tenant->owner_nome : 'Nao definido'?></div></div>
                <div class="info-card"><div class="info-label">Status</div><div class="info-value"><?=$tenant->status == 1 ? 'Ativo' : 'Inativo'?></div></div>
                <div class="info-card"><div class="info-label">Contato</div><div class="info-value"><?=$tenant->contato_email ? $tenant->contato_email : 'Sem e-mail'?></div></div>
                <div class="info-card"><div class="info-label">Vencimento</div><div class="info-value"><?=$tenant->expires_at ? date('d/m/Y', strtotime($tenant->expires_at)) : 'Nao definido'?></div></div>
              </div>

              <? if(isset($onboarding) && isset($onboarding['items'])){ ?>
                <div class="onboarding-wrap">
                  <div class="d-flex justify-content-between align-items-center" style="gap:12px;flex-wrap:wrap;">
                    <div>
                      <h6 class="element-header" style="margin-bottom:6px;">Jornada de ativacao</h6>
                      <p style="margin:0;color:#64748b;">Acompanhe o quanto este tenant ja esta pronto para sair da contratacao e entrar em uso real.</p>
                    </div>
                    <div class="status-pill status-active"><?=$onboarding['completed']?> de <?=$onboarding['total']?> etapas</div>
                  </div>
                  <div class="onboarding-progress">
                    <div class="onboarding-fill" style="width: <?=$onboarding['progress']?>%;"></div>
                  </div>
                  <div class="onboarding-grid">
                    <? foreach($onboarding['items'] as $item){ ?>
                      <div class="onboarding-item <?=$item['done'] ? 'done' : 'pending'?>">
                        <div class="onboarding-icon"><?=$item['done'] ? '✓' : '!'?></div>
                        <div>
                          <div class="onboarding-title"><?=$item['title']?></div>
                          <div class="onboarding-copy"><?=$item['description']?></div>
                        </div>
                      </div>
                    <? } ?>
                  </div>
                </div>
              <? } ?>

              <div class="panel-card">
                <div class="panel-card-header"><h6 class="element-header" style="margin-bottom:0;">Assinaturas</h6></div>
                <div class="panel-card-body">
                  <div class="table-responsive">
                    <table class="table table-lightborder">
                      <thead>
                        <tr>
                          <th>Plano</th>
                          <th>Status</th>
                          <th>Ciclo</th>
                          <th>Valor</th>
                          <th>Proxima cobranca</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <? if($assinaturas->num_rows() > 0){ foreach($assinaturas->result() as $assinatura){ ?>
                          <? $status_class = 'status-pending'; if($assinatura->status === 'active'){ $status_class = 'status-active'; } elseif($assinatura->status === 'trial'){ $status_class = 'status-trial'; } elseif($assinatura->status === 'past_due'){ $status_class = 'status-past-due'; } elseif($assinatura->status === 'paused'){ $status_class = 'status-paused'; } elseif($assinatura->status === 'canceled'){ $status_class = 'status-canceled'; } ?>
                          <tr>
                            <td><?=$assinatura->plano_nome ? $assinatura->plano_nome : 'Plano removido'?></td>
                            <td><span class="status-pill <?=$status_class?>"><?=$assinatura->status?></span></td>
                            <td><?=$assinatura->billing_cycle?> / <?=$assinatura->billing_interval_count?></td>
                            <td>R$ <?=number_format((float)$assinatura->valor, 2, ',', '.')?></td>
                            <td><?=$assinatura->next_billing_at ? date('d/m/Y', strtotime($assinatura->next_billing_at)) : 'Nao definido'?></td>
                            <td>
                              <? if((int)$viewer->nivel === 1 && isset($mercadopago_ready) && $mercadopago_ready){ ?>
                                <a href="<?=base_url()?>adm/saas/sincronizar/<?=$assinatura->id?>" class="btn btn-sm btn-outline-info">Sincronizar MP</a>
                              <? } ?>
                              <? if(isset($mercadopago_ready) && $mercadopago_ready){ ?>
                                <a href="<?=base_url()?>adm/saas/pagamento/<?=$assinatura->id?>" class="btn btn-sm btn-outline-primary">Pagar via PIX ou cartao</a>
                              <? } ?>
                              <? if(isset($assinatura->checkout_url) && trim((string)$assinatura->checkout_url) !== ''){ ?>
                                <a href="<?=$assinatura->checkout_url?>" target="_blank" class="btn btn-sm btn-outline-secondary">Abrir link MP</a>
                              <? } ?>
                            </td>
                          </tr>
                        <? } } else { ?>
                          <tr><td colspan="6">Nenhuma assinatura encontrada.</td></tr>
                        <? } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-7">
                  <div class="panel-card">
                    <div class="panel-card-header"><h6 class="element-header" style="margin-bottom:0;">Equipe vinculada</h6></div>
                    <div class="panel-card-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Nome</th>
                              <th>Nivel</th>
                              <th>Papel</th>
                              <th>Onboarding</th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($usuarios->num_rows() > 0){ foreach($usuarios->result() as $usuario){ ?>
                              <tr>
                                <td><?=$usuario->nome?><br><small><?=$usuario->email?></small></td>
                                <td><?=$usuario->nivel?></td>
                                <td><?=$usuario->tenant_role ? $usuario->tenant_role : 'member'?></td>
                                <td><?=$usuario->onboarding_status ? $usuario->onboarding_status : 'pendente'?></td>
                              </tr>
                            <? } } else { ?>
                              <tr><td colspan="4">Nenhum usuario vinculado a este tenant.</td></tr>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-5">
                  <div class="panel-card">
                    <div class="panel-card-header"><h6 class="element-header" style="margin-bottom:0;">Ciclos de cobranca</h6></div>
                    <div class="panel-card-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Referencia</th>
                              <th>Status</th>
                              <th>Valor</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($ciclos->num_rows() > 0){ foreach($ciclos->result() as $ciclo){ ?>
                              <tr>
                                <td><?=$ciclo->reference_label ? $ciclo->reference_label : date('m/Y', strtotime($ciclo->created_at))?></td>
                                <td><?=$ciclo->status?></td>
                                <td>R$ <?=number_format((float)$ciclo->amount_due, 2, ',', '.')?></td>
                                <td>
                                  <? if((int)$viewer->nivel === 1 && !in_array($ciclo->status, ['paid', 'canceled'])){ ?>
                                    <a href="<?=base_url()?>adm/saas/registrar_pagamento/<?=$ciclo->id?>?back=<?=urlencode(base_url().'adm/saas/tenant/'.$tenant->id)?>" class="btn btn-sm btn-outline-success">Registrar pagamento</a>
                                  <? } ?>
                                </td>
                              </tr>
                            <? } } else { ?>
                              <tr><td colspan="4">Nenhum ciclo registrado ainda.</td></tr>
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
      </div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
