<!DOCTYPE html>
<html lang="pt-BR">
<head>
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
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <div class="eyebrow">Trial operacional liberado</div>
            <h1>Sua clinica ja pode usar o sistema pelos proximos 30 dias.</h1>
            <p>
                O ambiente foi criado com foco em uso clinico e operacional. Voce ja pode entrar no sistema,
                cadastrar equipe, pacientes, agenda e atendimentos, enquanto o plano escolhido fica pronto para pagamento
                em um fluxo separado da gestao SaaS.
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
                    <div class="value" style="font-size:20px;"><?=$detail['owner']->email?></div>
                    <div class="copy">Use este acesso para entrar e comecar a operar.</div>
                </div>
                <div class="card">
                    <div class="label">Fim do trial</div>
                    <div class="value"><?=$detail['subscription']->trial_ends_at ? date('d/m/Y', strtotime($detail['subscription']->trial_ends_at)) : 'Nao definido'?></div>
                    <div class="copy">Pagamento do plano: R$ <?=number_format((float)$detail['subscription']->valor, 2, ',', '.')?></div>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="<?=base_url()?>admin">Entrar no sistema</a>
                <a class="btn btn-secondary" href="<?=$payment_url?>">Ver pagamento do plano</a>
                <a class="btn btn-secondary" href="<?=base_url()?>experimentar">Criar outro acesso</a>
            </div>
        </div>
    </div>
</body>
</html>
