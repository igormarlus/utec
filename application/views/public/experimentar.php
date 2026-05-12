<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comecar 30 dias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ink:#172033;
            --muted:#667085;
            --line:#d0d8e4;
            --panel:#ffffff;
            --paper:#f6f8fb;
            --primary:#0f766e;
            --accent:#f97316;
            --ok-bg:#ecfdf3;
            --ok-text:#166534;
            --error-bg:#fef2f2;
            --error-text:#991b1b;
        }
        * { box-sizing:border-box; }
        body {
            margin:0;
            font-family: Georgia, "Times New Roman", serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.12), transparent 26%),
                radial-gradient(circle at top right, rgba(249,115,22,.12), transparent 22%),
                linear-gradient(180deg,#f8fafc 0%,#eef4f7 100%);
        }
        .wrap { max-width:1120px; margin:0 auto; padding:30px 18px 54px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:26px; flex-wrap:wrap; }
        .brand { font-size:14px; letter-spacing:.16em; text-transform:uppercase; color:#115e59; font-weight:700; }
        .back { color:var(--muted); text-decoration:none; font-size:14px; }
        .hero { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(330px,.85fr); gap:24px; align-items:start; }
        .panel {
            background:rgba(255,255,255,.92);
            border:1px solid rgba(208,216,228,.9);
            border-radius:28px;
            padding:28px;
            box-shadow:0 24px 60px rgba(23,32,51,.08);
        }
        .eyebrow { font-size:12px; letter-spacing:.18em; text-transform:uppercase; color:var(--accent); font-weight:700; }
        h1 { font-size:46px; line-height:1.04; margin:14px 0 14px; }
        .lead { font-size:17px; line-height:1.75; color:#46566e; }
        .points { display:grid; gap:10px; margin-top:22px; }
        .point { border:1px solid rgba(15,118,110,.14); background:linear-gradient(90deg, rgba(15,118,110,.08), rgba(249,115,22,.06)); border-radius:16px; padding:14px 16px; font-size:15px; }
        .alert { border-radius:16px; padding:14px 16px; font-size:14px; margin-bottom:16px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok-text); border:1px solid #bbf7d0; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }
        .card-title { font-size:28px; margin:0 0 6px; }
        .card-subtitle { color:var(--muted); font-size:15px; line-height:1.6; margin:0 0 18px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
        .field { display:grid; gap:6px; }
        .field-wide { grid-column:1 / -1; }
        label { font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); }
        input, select, textarea {
            width:100%;
            border:1px solid #c9d3df;
            border-radius:14px;
            padding:12px 13px;
            font:inherit;
            color:var(--ink);
            background:#fff;
        }
        textarea { min-height:96px; resize:vertical; }
        .submit-row { display:flex; flex-wrap:wrap; gap:12px; margin-top:18px; align-items:center; }
        .btn-submit {
            border:0;
            border-radius:999px;
            background:linear-gradient(90deg,var(--primary),var(--accent));
            color:#fff;
            padding:13px 22px;
            font-size:15px;
            font-weight:700;
            cursor:pointer;
            box-shadow:0 18px 36px rgba(15,118,110,.18);
        }
        .plans { margin-top:24px; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
        .plan { border:1px solid #dbe2ea; border-radius:22px; padding:18px; background:linear-gradient(180deg,#fff 0%,#f9fbfd 100%); }
        .plan h3 { margin:0 0 8px; font-size:22px; }
        .plan-price { font-size:32px; line-height:1; margin:8px 0 10px; }
        .plan-copy { font-size:14px; line-height:1.7; color:#43526a; }
        @media (max-width: 980px) { .hero,.plans { grid-template-columns:1fr; } }
        @media (max-width: 680px) { h1 { font-size:34px; } .form-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div class="brand">UTecnologia Saude</div>
            <a class="back" href="<?=base_url()?>">Voltar para o site</a>
        </div>

        <div class="hero">
            <div class="panel">
                <div class="eyebrow">Onboarding operacional</div>
                <h1>Comece a usar o sistema por 30 dias sem entrar na camada de gestao SaaS.</h1>
                <p class="lead">
                    Este fluxo foi pensado para clinicas e profissionais que querem operar logo a agenda, pacientes,
                    prontuarios e atendimentos. O ambiente ja nasce pronto para uso medico, com o plano escolhido vinculado
                    e o pagamento disponivel durante o periodo de trial.
                </p>
                <div class="points">
                    <div class="point">Voce cria o acesso principal da operacao e ja pode entrar no sistema.</div>
                    <div class="point">O tenant continua existindo por baixo para isolar seus dados, mas sem exigir configuracao manual de SaaS.</div>
                    <div class="point">Durante os 30 dias, o pagamento do plano fica disponivel em uma jornada mais direta dentro do uso da ferramenta.</div>
                </div>
            </div>

            <div class="panel">
                <h2 class="card-title">Ativar 30 dias</h2>
                <p class="card-subtitle">Preencha apenas o essencial para criar a operacao e comecar a atender.</p>

                <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
                <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>

                <form method="post" action="<?=base_url()?>experimentar/enviar">
                    <div class="form-grid">
                        <div class="field">
                            <label>Nome do responsavel</label>
                            <input type="text" name="nome_responsavel" required>
                        </div>
                        <div class="field">
                            <label>Clinica ou nome profissional</label>
                            <input type="text" name="tenant_nome" required>
                        </div>
                        <div class="field">
                            <label>E-mail principal</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="field">
                            <label>Telefone</label>
                            <input type="text" name="telefone" placeholder="(00) 00000-0000">
                        </div>
                        <div class="field">
                            <label>Documento</label>
                            <input type="text" name="documento" placeholder="CPF ou CNPJ">
                        </div>
                        <div class="field">
                            <label>Tipo da operacao</label>
                            <select name="tenant_tipo">
                                <option value="clinica">Clinica</option>
                                <option value="consultorio">Consultorio</option>
                                <option value="profissional">Profissional</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Plano para trial</label>
                            <select name="plano_id" required>
                                <option value="">Selecione um plano</option>
                                <? foreach($planos as $plano){ ?>
                                    <option value="<?=$plano->id?>"><?=$plano->modelo?> - R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?> / <?=$plano->billing_interval?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Senha inicial</label>
                            <input type="password" name="senha" required minlength="6">
                        </div>
                        <div class="field field-wide">
                            <label>Observacoes</label>
                            <textarea name="observacoes" placeholder="Opcional: especialidade, porte da clinica ou contexto de uso."></textarea>
                        </div>
                    </div>

                    <div class="submit-row">
                        <button class="btn-submit" type="submit">Criar acesso e liberar 30 dias</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel" style="margin-top:24px;">
            <h2 class="card-title">Planos disponiveis no trial</h2>
            <p class="card-subtitle">Voce escolhe o plano agora, usa o sistema imediatamente e decide o pagamento dentro da operacao durante os 30 dias.</p>
            <div class="plans">
                <? foreach($planos as $plano){ ?>
                    <div class="plan">
                        <h3><?=$plano->modelo?></h3>
                        <div class="plan-price">R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?></div>
                        <div class="plan-copy">
                            <?=trim(strip_tags((string)$plano->especificacoes)) !== '' ? nl2br(htmlspecialchars(trim(strip_tags((string)$plano->especificacoes)))) : 'Plano preparado para uma entrada mais leve, com trial operacional de 30 dias e pagamento liberado durante o uso.'?>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</body>
</html>
