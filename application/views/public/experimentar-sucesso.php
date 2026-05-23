<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WSW6C4F4K8"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-WSW6C4F4K8');
    </script>
    <meta charset="UTF-8">
    <title>Trial ativado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ink:#132238; --muted:#5f6f86; --line:#d3dce7; --panel:#fff; --primary:#0f766e; --accent:#ea580c;
            --ok-bg:#ecfdf3; --ok-text:#166534; --error-bg:#fef2f2; --error-text:#991b1b;
        }
        * { box-sizing:border-box; }
        body { margin:0; font-family: Georgia, "Times New Roman", serif; color:var(--ink); background:linear-gradient(180deg,#f8fafc 0%,#edf4f8 100%); }
        .wrap { max-width:920px; margin:0 auto; padding:40px 18px 60px; }
        .panel { background:var(--panel); border:1px solid var(--line); border-radius:30px; padding:30px; box-shadow:0 24px 60px rgba(19,34,56,.08); }
        .eyebrow { font-size:12px; letter-spacing:.18em; text-transform:uppercase; font-weight:700; color:var(--accent); }
        h1 { font-size:42px; line-height:1.04; margin:12px 0 14px; }
        p { font-size:17px; line-height:1.7; color:#415167; }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; margin-top:24px; }
        .card { border:1px solid var(--line); border-radius:20px; padding:18px; background:#fbfdff; }
        .label { font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); }
        .value { font-size:24px; font-weight:700; margin-top:8px; }
        .copy { font-size:15px; line-height:1.7; color:#42526b; }
        .alert { border-radius:16px; padding:14px 16px; font-size:14px; margin:0 0 16px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok-text); border:1px solid #bbf7d0; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }
        .actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:26px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; border-radius:999px; padding:13px 18px; font-size:15px; font-weight:700; text-decoration:none; }
        .btn-primary { background:linear-gradient(90deg,var(--primary),var(--accent)); color:#fff; }
        .btn-secondary { background:#fff; border:1px solid var(--line); color:var(--ink); }
        @media (max-width: 720px) { h1 { font-size:34px; } .grid { grid-template-columns:1fr; } }
    </style>

    <!-- Meta Pixel — StartTrial + CompleteRegistration (deduplica com CAPI via event_id) -->
    <?php
        $fb_pid         = '844919898162947';
        $fb_trial_id    = $this->session->flashdata('fb_trial_event_id') ?: ('trial_fx_' . time());
        $fb_reg_id      = $this->session->flashdata('fb_reg_event_id')   ?: ('reg_fx_'   . time());
        $fb_plan_value  = isset($detail['subscription']->valor) ? (float)$detail['subscription']->valor : 0;
        $fb_plan_name   = isset($detail['plano']->modelo) ? htmlspecialchars((string)$detail['plano']->modelo) : 'Trial';
    ?>
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?=$fb_pid?>');
    fbq('track', 'PageView');
    fbq('track', 'StartTrial',
        {currency: 'BRL', value: 0, predicted_ltv: <?=json_encode($fb_plan_value)?>, content_name: '<?=$fb_plan_name?>'},
        {eventID: '<?=htmlspecialchars((string)$fb_trial_id)?>'}
    );
    fbq('track', 'CompleteRegistration',
        {status: 'trial_created', content_name: '<?=$fb_plan_name?>'},
        {eventID: '<?=htmlspecialchars((string)$fb_reg_id)?>'}
    );
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?=$fb_pid?>&ev=CompleteRegistration&noscript=1"/></noscript>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <div class="eyebrow">✅ Acesso criado com sucesso!</div>
            <h1>Bem-vindo(a) ao UTecnologia Saúde!</h1>
            <p>
                Seu ambiente está pronto. Você já está logado e pode começar a usar a agenda, cadastrar pacientes
                e registrar atendimentos agora mesmo. Também enviamos suas credenciais e um link para definir sua
                senha personalizada para o e-mail cadastrado.
            </p>

            <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
            <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>

            <div class="grid">
                <div class="card">
                    <div class="label">Operacao</div>
                    <div class="value"><?=$detail['tenant']->tenant_nome?></div>
                    <div class="copy">Tipo: <?=$detail['tenant']->tenant_tipo?></div>
                </div>
                <div class="card">
                    <div class="label">Plano escolhido</div>
                    <div class="value"><?=$detail['plano']->modelo?></div>
                    <div class="copy">Status inicial: <?=$detail['subscription']->status?></div>
                </div>
                <div class="card">
                    <div class="label">Login principal</div>
                    <div class="value" style="font-size:18px;"><?=$detail['owner']->email?></div>
                    <div class="copy">Credenciais e link de acesso enviados para este e-mail.</div>
                </div>
                <div class="card">
                    <div class="label">Fim do trial</div>
                    <div class="value"><?=$detail['subscription']->trial_ends_at ? date('d/m/Y', strtotime($detail['subscription']->trial_ends_at)) : 'Nao definido'?></div>
                    <div class="copy">Pagamento do plano: R$ <?=number_format((float)$detail['subscription']->valor, 2, ',', '.')?></div>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="<?=base_url()?>adm/atendimento">Entrar no sistema agora →</a>
                <a class="btn btn-secondary" href="<?=$payment_url?>">Ver plano e pagamento</a>
            </div>
        </div>
    </div>
</body>
</html>
