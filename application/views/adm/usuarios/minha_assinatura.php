<!DOCTYPE html>
<html>
  <head>
    <title>Minha Assinatura | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .signature-shell { display:grid; gap:24px; }
      .hero-card {
        background: linear-gradient(135deg, rgba(15,118,110,.14), rgba(234,88,12,.1));
        border: 1px solid #d8e5ec;
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(15,23,42,.06);
        padding: 24px;
      }
      .hero-title { font-size: 34px; font-weight: 700; color: #0f172a; line-height: 1.08; margin: 0 0 10px; }
      .hero-copy { color: #475569; font-size: 16px; line-height: 1.7; max-width: 760px; }
      .hero-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:20px; }
      .summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; }
      .summary-card, .panel-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:20px; box-shadow:0 10px 24px rgba(15,23,42,.05);
      }
      .summary-card { padding:18px 20px; }
      .label { color:#64748b; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
      .value { color:#0f172a; font-size:28px; font-weight:700; margin-top:8px; line-height:1.05; }
      .copy { color:#475569; font-size:14px; line-height:1.7; margin-top:8px; }
      .panel-head { padding:18px 20px 0; }
      .panel-body { padding:18px 20px 20px; }
      .status-pill { display:inline-flex; align-items:center; border-radius:999px; padding:7px 12px; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; background:#e2e8f0; color:#475569; }
      .status-active { background:#dcfce7; color:#166534; }
      .status-trial { background:#dbeafe; color:#1d4ed8; }
      .status-past_due { background:#fef3c7; color:#92400e; }
      .status-pending { background:#e2e8f0; color:#475569; }
      .status-canceled { background:#fee2e2; color:#b91c1c; }
      .status-paused { background:#ede9fe; color:#5b21b6; }
      .timeline { display:grid; gap:12px; }
      .timeline-item { border:1px solid #e2e8f0; border-radius:16px; padding:14px 16px; background:#f8fafc; }
      .timeline-item strong { display:block; color:#0f172a; margin-bottom:6px; }
      .checklist { display:grid; gap:10px; }
      .check-item { display:grid; grid-template-columns:40px 1fr; gap:12px; align-items:flex-start; border:1px solid #e2e8f0; border-radius:14px; padding:14px; background:#f8fafc; }
      .check-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700; }
      .check-done .check-icon { background:#dcfce7; color:#166534; }
      .check-pending .check-icon { background:#fff7ed; color:#c2410c; }
      .alert-card { border-radius:16px; padding:14px 16px; font-size:14px; margin-bottom:16px; }
      .alert-ok { background:#ecfdf3; color:#166534; border:1px solid #bbf7d0; }
      .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
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
            <li class="breadcrumb-item"><span>Minha assinatura</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <? if(isset($flash_ok) && $flash_ok){ ?><div class="alert-card alert-ok"><?=$flash_ok?></div><? } ?>
              <? if(isset($flash_error) && $flash_error){ ?><div class="alert-card alert-error"><?=$flash_error?></div><? } ?>

              <div class="signature-shell">
                <div class="hero-card">
                  <div class="label" style="color:#9a3412;">Assinatura da operacao</div>
                  <h1 class="hero-title">Acompanhe o plano da sua clinica sem sair da rotina operacional.</h1>
                  <div class="hero-copy">
                    Aqui voce acompanha trial, status comercial, vencimento e pagamento do plano com uma linguagem pensada para a clinica.
                    A operacao continua pronta para agenda, pacientes, prontuarios e atendimento enquanto voce organiza a contratacao.
                  </div>
                  <div class="hero-actions">
                    <a href="<?=$payment_url?>" class="btn btn-primary">Pagar com PIX ou cartao</a>
                    <a href="<?=$status_url?>" class="btn btn-outline-secondary">Atualizar status</a>
                  </div>
                </div>

                <div class="summary-grid">
                  <div class="summary-card">
                    <div class="label">Clinica</div>
                    <div class="value"><?=$tenant->tenant_nome?></div>
                    <div class="copy"><?=$tenant->tenant_tipo ? ucfirst($tenant->tenant_tipo) : 'Operacao clinica'?></div>
                  </div>
                  <div class="summary-card">
                    <div class="label">Plano atual</div>
                    <div class="value" style="font-size:24px;"><?=$plano->modelo?></div>
                    <div class="copy">Valor recorrente: R$ <?=number_format((float)$subscription->valor, 2, ',', '.')?></div>
                  </div>
                  <div class="summary-card">
                    <div class="label">Status da assinatura</div>
                    <? $status_class = 'status-pill status-pending'; $status_key = str_replace('-', '_', (string)$subscription->status); if(in_array($subscription->status, ['active','trial','past_due','canceled','paused'])){ $status_class = 'status-pill status-'.$status_key; } ?>
                    <div style="margin-top:10px;"><span class="<?=$status_class?>"><?=$subscription->status?></span></div>
                    <div class="copy">Proxima cobranca: <?=$subscription->next_billing_at ? date('d/m/Y', strtotime($subscription->next_billing_at)) : 'Nao definida'?></div>
                  </div>
                  <div class="summary-card">
                    <div class="label">Trial e renovacao</div>
                    <div class="value" style="font-size:24px;"><?=$subscription->trial_ends_at ? date('d/m/Y', strtotime($subscription->trial_ends_at)) : 'Sem trial'?></div>
                    <div class="copy">Responsavel principal: <?=$owner->email?></div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-7">
                    <div class="panel-card">
                      <div class="panel-head"><h6 class="element-header" style="margin-bottom:0;">Resumo comercial</h6></div>
                      <div class="panel-body">
                        <div class="timeline">
                          <div class="timeline-item">
                            <strong>Plano contratado</strong>
                            <?=$plano->modelo?> com ciclo <?=$subscription->billing_cycle?> / <?=max(1, (int)$subscription->billing_interval_count)?>.
                          </div>
                          <div class="timeline-item">
                            <strong>Ciclo em aberto</strong>
                            <? if($open_cycle){ ?>
                              Referencia <?=$open_cycle->reference_label ? $open_cycle->reference_label : 'Atual'?>, vencimento <?=$open_cycle->due_at ? date('d/m/Y', strtotime($open_cycle->due_at)) : 'nao definido'?> e valor de R$ <?=number_format((float)$open_cycle->amount_due, 2, ',', '.')?>.
                            <? } else { ?>
                              Nao existe cobranca pendente neste momento.
                            <? } ?>
                          </div>
                          <div class="timeline-item">
                            <strong>Pagamento</strong>
                            Use PIX ou cartao para regularizar ou antecipar a contratacao sem sair da area da clinica.
                          </div>
                          <div class="timeline-item">
                            <strong>Uso da operacao</strong>
                            Enquanto o status estiver em `active`, `trial` ou `pending`, a clinica continua liberada para operar normalmente.
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="panel-card">
                      <div class="panel-head"><h6 class="element-header" style="margin-bottom:0;">Prontidao da clinica</h6></div>
                      <div class="panel-body">
                        <? if(isset($onboarding['items']) && count($onboarding['items'])){ ?>
                          <div class="copy" style="margin-bottom:16px;">Veja o quanto a clinica ja esta pronta para sair do cadastro e entrar em uso pleno.</div>
                          <div class="checklist">
                            <? foreach($onboarding['items'] as $item){ ?>
                              <div class="check-item <?=$item['done'] ? 'check-done' : 'check-pending'?>">
                                <div class="check-icon"><?=$item['done'] ? '✓' : '!'?></div>
                                <div>
                                  <strong style="display:block;color:#0f172a;"><?=$item['title']?></strong>
                                  <div class="copy" style="margin-top:4px;"><?=$item['description']?></div>
                                </div>
                              </div>
                            <? } ?>
                          </div>
                        <? } else { ?>
                          <div class="copy">Ainda nao existem indicadores de prontidao para esta operacao.</div>
                        <? } ?>
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
