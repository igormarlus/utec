<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Assinatura iniciada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ink: #132238;
            --muted: #5f6f86;
            --line: #d3dce7;
            --panel: #fff;
            --bg: linear-gradient(180deg, #f8fafc 0%, #edf4f8 100%);
            --primary: #0f766e;
            --accent: #ea580c;
            --ok-bg: #ecfdf3;
            --ok-text: #166534;
            --warn-bg: #fff7ed;
            --warn-text: #9a3412;
            --error-bg: #fef2f2;
            --error-text: #991b1b;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; color: var(--ink); background: var(--bg); }
        .wrap { max-width: 900px; margin: 0 auto; padding: 40px 18px 60px; }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 24px 60px rgba(19,34,56,.08);
        }
        .eyebrow { font-size: 12px; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; color: var(--accent); }
        h1 { font-size: 44px; line-height: 1.04; margin: 12px 0 14px; }
        p { font-size: 17px; line-height: 1.7; color: #415167; }
        .alert { border-radius: 16px; padding: 14px 16px; font-size: 14px; margin: 0 0 16px; }
        .alert-ok { background: var(--ok-bg); color: var(--ok-text); border: 1px solid #bbf7d0; }
        .alert-error { background: var(--error-bg); color: var(--error-text); border: 1px solid #fecaca; }
        .alert-warn { background: var(--warn-bg); color: var(--warn-text); border: 1px solid #fdba74; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 24px; }
        .card { border: 1px solid var(--line); border-radius: 20px; padding: 18px; background: #fbfdff; }
        .label { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .value { font-size: 24px; font-weight: 700; margin-top: 8px; }
        .copy { font-size: 15px; line-height: 1.7; color: #42526b; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 26px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 13px 18px;
            font-size: 15px;
            font-weight: 700;
        }
        .btn-primary { background: linear-gradient(90deg, var(--primary), var(--accent)); color: #fff; }
        .btn-secondary { background: #fff; border: 1px solid var(--line); color: var(--ink); }
        .onboarding {
            margin-top: 28px;
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 22px;
            background: #f9fbfd;
        }
        .onboarding-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .onboarding-title {
            font-size: 28px;
            margin: 0;
        }
        .progress-pill {
            border-radius: 999px;
            background: #ecfeff;
            color: #0f766e;
            border: 1px solid rgba(15,118,110,.16);
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
        }
        .progress-bar {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: #e5eef6;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }
        .checklist {
            display: grid;
            gap: 12px;
        }
        .check-item {
            display: grid;
            grid-template-columns: 44px 1fr;
            gap: 14px;
            align-items: start;
            border-radius: 18px;
            padding: 14px 16px;
            border: 1px solid #dde6ef;
            background: #fff;
        }
        .check-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
        }
        .check-done .check-icon {
            background: #ecfdf3;
            color: #15803d;
        }
        .check-pending .check-icon {
            background: #fff7ed;
            color: #c2410c;
        }
        .check-name {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .check-copy {
            font-size: 14px;
            line-height: 1.7;
            color: #55657b;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        .metric {
            background: #fff;
            border: 1px solid #dde6ef;
            border-radius: 18px;
            padding: 14px;
        }
        .metric .label {
            font-size: 11px;
        }
        .metric .value {
            font-size: 22px;
        }
        @media (max-width: 720px) {
            h1 { font-size: 34px; }
            .grid { grid-template-columns: 1fr; }
            .metrics-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <div class="eyebrow">Assinatura iniciada</div>
            <h1>Seu tenant foi criado e a contratacao ja pode seguir.</h1>
            <p>
                Esta pagina confirma que a operacao SaaS foi provisionada. A partir daqui voce pode concluir o checkout,
                acessar o sistema com o e-mail principal e acompanhar a evolucao comercial da assinatura.
            </p>

            <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
            <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>
            <? if(isset($detail['subscription']->checkout_url) && trim((string)$detail['subscription']->checkout_url) === ''){ ?>
                <div class="alert alert-warn">O checkout automatico ainda nao foi aberto para esta assinatura. A operacao foi criada, mas a finalizacao comercial pode depender de acao manual.</div>
            <? } ?>

            <div class="grid">
                <div class="card">
                    <div class="label">Clinica</div>
                    <div class="value"><?=$detail['tenant']->tenant_nome?></div>
                    <div class="copy">Slug: <?=$detail['tenant']->slug?></div>
                </div>
                <div class="card">
                    <div class="label">Plano</div>
                    <div class="value"><?=$detail['plano']->modelo?></div>
                    <div class="copy">Status atual: <?=$detail['subscription']->status?></div>
                </div>
                <div class="card">
                    <div class="label">Login principal</div>
                    <div class="value" style="font-size:20px;"><?=$detail['owner']->email?></div>
                    <div class="copy">Este e-mail foi criado como acesso owner do tenant.</div>
                </div>
                <div class="card">
                    <div class="label">Proxima cobranca</div>
                    <div class="value"><?=($detail['subscription']->next_billing_at ? date('d/m/Y', strtotime($detail['subscription']->next_billing_at)) : 'Nao definida')?></div>
                    <div class="copy">Valor recorrente: R$ <?=number_format((float)$detail['subscription']->valor, 2, ',', '.')?></div>
                </div>
            </div>

            <? if(isset($onboarding) && isset($onboarding['items'])){ ?>
                <div class="onboarding">
                    <div class="onboarding-head">
                        <div>
                            <h2 class="onboarding-title">Primeiros passos da clinica</h2>
                            <p class="copy" style="margin:6px 0 0;">Este checklist mostra o quanto a operacao ja esta pronta para sair do cadastro e entrar em uso real.</p>
                        </div>
                        <div class="progress-pill"><?=$onboarding['completed']?> de <?=$onboarding['total']?> etapas concluidas</div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?=$onboarding['progress']?>%;"></div>
                    </div>
                    <div class="checklist">
                        <? foreach($onboarding['items'] as $item){ ?>
                            <div class="check-item <?=$item['done'] ? 'check-done' : 'check-pending'?>">
                                <div class="check-icon"><?=$item['done'] ? '✓' : '!'?></div>
                                <div>
                                    <div class="check-name"><?=$item['title']?></div>
                                    <div class="check-copy"><?=$item['description']?></div>
                                </div>
                            </div>
                        <? } ?>
                    </div>

                    <? if(isset($onboarding['metrics'])){ ?>
                        <div class="metrics-grid">
                            <div class="metric">
                                <div class="label">Usuarios</div>
                                <div class="value"><?=$onboarding['metrics']['usuarios']?></div>
                            </div>
                            <div class="metric">
                                <div class="label">Profissionais</div>
                                <div class="value"><?=$onboarding['metrics']['profissionais']?></div>
                            </div>
                            <div class="metric">
                                <div class="label">Colaboradores</div>
                                <div class="value"><?=$onboarding['metrics']['colaboradores']?></div>
                            </div>
                            <div class="metric">
                                <div class="label">Pacientes</div>
                                <div class="value"><?=$onboarding['metrics']['pacientes']?></div>
                            </div>
                            <div class="metric">
                                <div class="label">Agendamentos</div>
                                <div class="value"><?=$onboarding['metrics']['agendamentos']?></div>
                            </div>
                        </div>
                    <? } ?>
                </div>
            <? } ?>

            <div class="actions">
                <? if(isset($detail['subscription']->checkout_url) && trim((string)$detail['subscription']->checkout_url) !== ''){ ?>
                    <a class="btn btn-primary" href="<?=$detail['subscription']->checkout_url?>" target="_blank">Abrir checkout Mercado Pago</a>
                <? } ?>
                <a class="btn btn-secondary" href="<?=base_url()?>admin">Ir para o login do sistema</a>
                <a class="btn btn-secondary" href="<?=base_url()?>assinar/sucesso?subscription=<?=(int)$detail['subscription']->id?>">Atualizar jornada</a>
                <a class="btn btn-secondary" href="<?=base_url()?>assinar">Criar outra assinatura</a>
            </div>
        </div>
    </div>
</body>
</html>
