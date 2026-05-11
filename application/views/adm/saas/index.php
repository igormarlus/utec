<!DOCTYPE html>
<html>
  <head>
    <title>Operacao SaaS | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .saas-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.05); }
      .saas-card-header { padding:18px 20px 0; }
      .saas-card-body { padding:18px 20px 20px; }
      .saas-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:24px; }
      .saas-stat-label { color:#64748b; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
      .saas-stat-value { color:#0f172a; font-size:28px; font-weight:700; margin-top:8px; }
      .status-pill { display:inline-block; border-radius:999px; font-size:11px; font-weight:700; padding:5px 10px; text-transform:uppercase; }
      .status-active { background:#dcfce7; color:#166534; }
      .status-trial { background:#dbeafe; color:#1d4ed8; }
      .status-past-due { background:#fef3c7; color:#92400e; }
      .status-canceled, .status-inactive { background:#fee2e2; color:#b91c1c; }
      .status-pending { background:#e2e8f0; color:#475569; }
      .muted-note { color:#64748b; font-size:13px; }
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
            <li class="breadcrumb-item"><span>Operacao SaaS</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <h6 class="element-header">Operacao SaaS</h6>
                <p class="muted-note">Tenant, assinatura, ciclo de cobranca e provisionamento inicial das clinicas.</p>
              </div>

              <? if($schema_ok && (int)$viewer->nivel === 1){ ?>
                <div style="margin-bottom:18px;">
                  <a href="<?=base_url()?>adm/saas/rotina_cobranca" class="btn btn-outline-primary">Rodar rotina de cobranca</a>
                </div>
              <? } ?>

              <? if(!$schema_ok){ ?>
                <div class="alert alert-warning">
                  A estrutura SaaS ainda nao foi criada neste banco. Execute <strong>adm/dev/migrar_fase1_saas</strong> para liberar o modulo.
                </div>
              <? } ?>
              <? if($schema_ok){ ?>
                <div class="alert alert-info">
                  Webhook Mercado Pago configuravel em: <strong><?=base_url()?>webhooks/mercadopago</strong>
                </div>
              <? } ?>
              <? if($schema_ok && isset($mercadopago_ready) && !$mercadopago_ready){ ?>
                <div class="alert alert-warning">
                  O arquivo <strong>application/config/mercadopago.php</strong> ainda nao existe neste servidor. O painel SaaS funciona normalmente, mas checkout e sincronizacao com Mercado Pago ficam indisponiveis ate o arquivo ser publicado.
                </div>
              <? } ?>

              <? if($flash_ok){ ?><div class="alert alert-success"><?=$flash_ok?></div><? } ?>
              <? if($flash_error){ ?><div class="alert alert-danger"><?=$flash_error?></div><? } ?>

              <div class="saas-grid">
                <div class="saas-card"><div class="saas-card-body"><div class="saas-stat-label">Tenants</div><div class="saas-stat-value"><?=$resumo['tenants']?></div></div></div>
                <div class="saas-card"><div class="saas-card-body"><div class="saas-stat-label">Tenants ativos</div><div class="saas-stat-value"><?=$resumo['tenants_ativos']?></div></div></div>
                <div class="saas-card"><div class="saas-card-body"><div class="saas-stat-label">Assinaturas</div><div class="saas-stat-value"><?=$resumo['assinaturas']?></div></div></div>
                <div class="saas-card"><div class="saas-card-body"><div class="saas-stat-label">MRR estimado</div><div class="saas-stat-value">R$ <?=number_format((float)$resumo['mrr'], 2, ',', '.')?></div></div></div>
              </div>

              <? if($schema_ok && (int)$viewer->nivel === 1){ ?>
                <div class="saas-card" style="margin-bottom:24px;">
                  <div class="saas-card-header"><h6 class="element-header" style="margin-bottom:0;">Provisionar clinica</h6></div>
                  <div class="saas-card-body">
                    <form method="post" action="<?=base_url()?>adm/saas/provisionar">
                      <div class="row">
                        <div class="col-md-4">
                          <label>Responsavel base</label>
                          <select name="owner_user_id" class="form-control" required>
                            <option value="">Selecione</option>
                            <? foreach($usuarios_base->result() as $usuario){ ?>
                              <option value="<?=$usuario->id?>"><?=$usuario->nome?> (nivel <?=$usuario->nivel?><?=$usuario->tenant_id ? ' - ja vinculado' : ''?>)</option>
                            <? } ?>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label>Nome comercial do tenant</label>
                          <input type="text" name="tenant_nome" class="form-control" placeholder="Ex: Clinica Essencial">
                        </div>
                        <div class="col-md-2">
                          <label>Tipo</label>
                          <select name="tenant_tipo" class="form-control">
                            <option value="clinica">Clinica</option>
                            <option value="consultorio">Consultorio</option>
                            <option value="profissional">Profissional</option>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label>Status</label>
                          <select name="status" class="form-control">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                          </select>
                        </div>
                      </div>
                      <div class="row" style="margin-top:14px;">
                        <div class="col-md-4">
                          <label>Plano</label>
                          <select name="plano_id" class="form-control" required>
                            <option value="">Selecione</option>
                            <? foreach($planos->result() as $plano){ ?>
                              <option value="<?=$plano->id?>"><?=$plano->modelo?> - R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?> / <?=$plano->billing_interval?></option>
                            <? } ?>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label>Ciclo</label>
                          <select name="billing_cycle" class="form-control">
                            <option value="monthly">Mensal</option>
                            <option value="quarterly">Trimestral</option>
                            <option value="semiannual">Semestral</option>
                            <option value="yearly">Anual</option>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label>Intervalo</label>
                          <input type="number" min="1" name="billing_interval_count" class="form-control" value="1">
                        </div>
                        <div class="col-md-2">
                          <label>Trial (dias)</label>
                          <input type="number" min="0" name="trial_days" class="form-control" value="0">
                        </div>
                        <div class="col-md-2">
                          <label>Valor recorrente</label>
                          <input type="text" name="valor" class="form-control" placeholder="0,00">
                        </div>
                      </div>
                      <div class="row" style="margin-top:14px;">
                        <div class="col-md-2">
                          <label>Setup</label>
                          <input type="text" name="setup_fee" class="form-control" placeholder="0,00">
                        </div>
                        <div class="col-md-2">
                          <label>Gateway</label>
                          <input type="text" name="gateway" class="form-control" placeholder="Manual, PIX...">
                        </div>
                        <div class="col-md-3">
                          <label>Referencia gateway</label>
                          <input type="text" name="gateway_reference" class="form-control" placeholder="ID externo">
                        </div>
                        <div class="col-md-2">
                          <label>Documento</label>
                          <input type="text" name="documento" class="form-control" placeholder="CPF/CNPJ">
                        </div>
                        <div class="col-md-3">
                          <label>Contato comercial</label>
                          <input type="text" name="contato_nome" class="form-control" placeholder="Nome do contato">
                        </div>
                      </div>
                      <div class="row" style="margin-top:14px;">
                        <div class="col-md-4">
                          <label>E-mail do contato</label>
                          <input type="email" name="contato_email" class="form-control">
                        </div>
                        <div class="col-md-4">
                          <label>Telefone do contato</label>
                          <input type="text" name="contato_telefone" class="form-control">
                        </div>
                        <div class="col-md-4">
                          <label>Observacoes</label>
                          <input type="text" name="observacoes" class="form-control" placeholder="Anote combinados comerciais">
                        </div>
                      </div>
                      <div style="margin-top:16px;">
                        <button type="submit" class="btn btn-primary">Provisionar tenant</button>
                      </div>
                    </form>
                  </div>
                </div>
              <? } ?>

              <div class="row">
                <div class="col-lg-6">
                  <div class="saas-card" style="margin-bottom:24px;">
                    <div class="saas-card-header"><h6 class="element-header" style="margin-bottom:0;">Tenants</h6></div>
                    <div class="saas-card-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Tenant</th>
                              <th>Responsavel</th>
                              <th>Status</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($tenants && $tenants->num_rows() > 0){ foreach($tenants->result() as $tenant){ ?>
                              <tr>
                                <td>
                                  <strong><?=$tenant->tenant_nome?></strong><br>
                                  <small><?=$tenant->tenant_tipo?></small>
                                </td>
                                <td><?=$tenant->owner_nome ? $tenant->owner_nome : 'Nao definido'?></td>
                                <td><span class="status-pill <?=$tenant->status == 1 ? 'status-active' : 'status-inactive'?>"><?=$tenant->status == 1 ? 'Ativo' : 'Inativo'?></span></td>
                                <td><a href="<?=base_url()?>adm/saas/tenant/<?=$tenant->id?>" class="btn btn-sm btn-outline-primary">Abrir</a></td>
                              </tr>
                            <? } } else { ?>
                              <tr><td colspan="4">Nenhum tenant provisionado ainda.</td></tr>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="saas-card" style="margin-bottom:24px;">
                    <div class="saas-card-header"><h6 class="element-header" style="margin-bottom:0;">Assinaturas</h6></div>
                    <div class="saas-card-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Tenant</th>
                              <th>Plano</th>
                              <th>Status</th>
                              <th>Valor</th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($assinaturas && $assinaturas->num_rows() > 0){ foreach($assinaturas->result() as $assinatura){ ?>
                              <? $status_class = 'status-pending'; if($assinatura->status === 'active'){ $status_class = 'status-active'; } elseif($assinatura->status === 'trial'){ $status_class = 'status-trial'; } elseif($assinatura->status === 'past_due'){ $status_class = 'status-past-due'; } elseif($assinatura->status === 'canceled'){ $status_class = 'status-canceled'; } ?>
                              <tr>
                                <td><?=$assinatura->tenant_nome ? $assinatura->tenant_nome : 'Nao definido'?></td>
                                <td><?=$assinatura->plano_nome ? $assinatura->plano_nome : 'Plano removido'?></td>
                                <td><span class="status-pill <?=$status_class?>"><?=$assinatura->status?></span></td>
                                <td>R$ <?=number_format((float)$assinatura->valor, 2, ',', '.')?></td>
                              </tr>
                            <? } } else { ?>
                              <tr><td colspan="4">Nenhuma assinatura cadastrada.</td></tr>
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
