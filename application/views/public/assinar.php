<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Assinar UTecnologia Saude</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ink: #172033;
            --muted: #667085;
            --line: #d0d8e4;
            --paper: #f6f8fb;
            --panel: #ffffff;
            --primary: #0f766e;
            --primary-strong: #115e59;
            --accent: #f97316;
            --soft: #ecfeff;
            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --ok-bg: #ecfdf3;
            --ok-text: #166534;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(249,115,22,.12), transparent 28%),
                radial-gradient(circle at top right, rgba(15,118,110,.15), transparent 25%),
                linear-gradient(180deg, #f8fafc 0%, #eef4f7 100%);
        }
        a { color: inherit; text-decoration: none; }
        .shell { max-width: 1180px; margin: 0 auto; padding: 28px 18px 56px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 28px; }
        .brand { font-size: 15px; letter-spacing: .08em; text-transform: uppercase; color: var(--primary-strong); font-weight: 700; }
        .back-link { font-size: 14px; color: var(--muted); }
        .hero { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr); gap: 24px; align-items: start; }
        .hero-copy {
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(208,216,228,.9);
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 24px 60px rgba(23,32,51,.07);
        }
        .eyebrow { font-size: 12px; letter-spacing: .18em; text-transform: uppercase; color: var(--accent); font-weight: 700; }
        h1 { font-size: 48px; line-height: 1.02; margin: 14px 0 14px; }
        .lead { font-size: 18px; line-height: 1.7; color: #42526b; max-width: 720px; }
        .hero-points { display: grid; gap: 10px; margin-top: 24px; }
        .hero-point {
            background: linear-gradient(90deg, rgba(15,118,110,.08), rgba(249,115,22,.05));
            border: 1px solid rgba(15,118,110,.12);
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 15px;
        }
        .signup-card, .plans-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(23,32,51,.08);
        }
        .signup-card { padding: 24px; }
        .card-title { font-size: 28px; margin: 0 0 6px; }
        .card-subtitle { margin: 0 0 18px; font-size: 15px; color: var(--muted); line-height: 1.6; }
        .alert { border-radius: 16px; padding: 14px 16px; font-size: 14px; margin-bottom: 16px; }
        .alert-error { background: var(--danger-bg); color: var(--danger-text); border: 1px solid #fecaca; }
        .alert-ok { background: var(--ok-bg); color: var(--ok-text); border: 1px solid #bbf7d0; }
        .alert-warn { background: #fff7ed; color: #9a3412; border: 1px solid #fdba74; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .field { display: grid; gap: 6px; }
        .field-wide { grid-column: 1 / -1; }
        label { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        input, select, textarea {
            width: 100%;
            border: 1px solid #c9d3df;
            border-radius: 14px;
            padding: 12px 13px;
            font: inherit;
            color: var(--ink);
            background: #fff;
        }
        textarea { min-height: 92px; resize: vertical; }
        .form-note { font-size: 13px; color: var(--muted); line-height: 1.6; margin-top: 14px; }
        .submit-row { display: flex; align-items: center; gap: 12px; margin-top: 18px; flex-wrap: wrap; }
        .btn-submit {
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            padding: 13px 22px;
            cursor: pointer;
            box-shadow: 0 18px 36px rgba(15,118,110,.18);
        }
        .plans-card { padding: 24px; margin-top: 24px; }
        .plans-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .plan {
            border: 1px solid #dbe2ea;
            border-radius: 22px;
            padding: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbfd 100%);
            position: relative;
        }
        .plan-featured {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7ed 100%);
            border-color: rgba(249,115,22,.35);
            box-shadow: 0 18px 34px rgba(249,115,22,.12);
            transform: translateY(-6px);
        }
        .plan-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
        }
        .plan-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid rgba(67,56,202,.14);
            white-space: nowrap;
        }
        .plan h3 { margin: 0 0 8px; font-size: 24px; }
        .plan-price { font-size: 34px; line-height: 1; margin: 8px 0 10px; }
        .plan-meta { font-size: 13px; color: var(--muted); line-height: 1.6; }
        .plan-copy { font-size: 14px; line-height: 1.7; color: #43526a; margin-top: 12px; min-height: 72px; }
        .plan-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
        .plan-badge {
            background: var(--soft);
            border: 1px solid rgba(15,118,110,.15);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            color: var(--primary-strong);
            font-weight: 700;
        }
        .plan-list {
            list-style: none;
            margin: 16px 0 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }
        .plan-list li {
            font-size: 14px;
            color: #334155;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(208,216,228,.7);
            border-radius: 12px;
            padding: 10px 12px;
        }
        .plan-action {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            border-radius: 999px;
            border: 1px solid rgba(15,118,110,.18);
            background: #fff;
            color: var(--primary-strong);
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .compare-card {
            margin-top: 24px;
            background: rgba(255,255,255,.84);
            border: 1px solid rgba(208,216,228,.92);
            border-radius: 24px;
            padding: 22px;
        }
        .compare-title {
            font-size: 22px;
            margin: 0 0 8px;
        }
        .compare-copy {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
            margin: 0 0 16px;
        }
        .compare-grid {
            display: grid;
            grid-template-columns: 1.15fr repeat(3, minmax(0, 1fr));
            gap: 0;
            border: 1px solid #dbe2ea;
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
        }
        .compare-cell {
            padding: 14px 12px;
            border-right: 1px solid #e5ecf3;
            border-bottom: 1px solid #e5ecf3;
            font-size: 14px;
            line-height: 1.5;
        }
        .compare-cell:last-child {
            border-right: 0;
        }
        .compare-head {
            background: #f8fafc;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .compare-row-title {
            font-weight: 700;
            color: #1e293b;
            background: #fcfdff;
        }
        .compare-highlight {
            background: #fff7ed;
        }
        @media (max-width: 980px) {
            .hero { grid-template-columns: 1fr; }
            .plans-grid { grid-template-columns: 1fr; }
            .compare-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 680px) {
            h1 { font-size: 36px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="topbar">
            <a class="brand" href="<?=base_url()?>">UTecnologia Saude</a>
            <a class="back-link" href="<?=base_url()?>">Voltar para a pagina inicial</a>
        </div>

        <div class="hero">
            <div class="hero-copy">
                <div class="eyebrow">Onboarding comercial</div>
                <h1>Comece a operar sua clinica no SaaS sem depender do cadastro interno.</h1>
                <p class="lead">
                    Esta area inicia a assinatura publica da sua clinica ou consultorio. Voce escolhe o plano,
                    cria o acesso principal da operacao e segue para o checkout do Mercado Pago.
                </p>
                <div class="hero-points">
                    <div class="hero-point">Seu cadastro cria automaticamente o tenant, o usuario owner e a assinatura inicial.</div>
                    <div class="hero-point">Depois do pagamento, a operacao ja nasce pronta para evoluir para um fluxo comercial real.</div>
                    <div class="hero-point">Enquanto o autosservico amadurece, esse onboarding ja elimina o provisionamento totalmente manual.</div>
                </div>
            </div>

            <div class="signup-card">
                <h2 class="card-title">Criar assinatura</h2>
                <p class="card-subtitle">Preencha os dados da operacao e escolha o plano que melhor combina com sua clinica.</p>

                <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
                <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>
                <? if(!$mercadopago_ready){ ?><div class="alert alert-warn">O cadastro publico ja pode criar o tenant, mas o checkout automatico do Mercado Pago ainda nao esta publicado neste servidor.</div><? } ?>

                <form method="post" action="<?=base_url()?>assinar/enviar">
                    <div class="form-grid">
                        <div class="field">
                            <label>Nome do responsavel</label>
                            <input type="text" name="nome_responsavel" required>
                        </div>
                        <div class="field">
                            <label>Nome da clinica</label>
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
                            <label>Plano</label>
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
                            <label>Observacoes comerciais</label>
                            <textarea name="observacoes" placeholder="Opcional: detalhes sobre a operacao, porte da clinica ou contexto do onboarding."></textarea>
                        </div>
                    </div>

                    <div class="submit-row">
                        <button class="btn-submit" type="submit">Criar tenant e seguir para a contratacao</button>
                    </div>

                    <div class="form-note">
                        O acesso principal sera criado com este e-mail. Se o checkout estiver habilitado no servidor,
                        voce sera redirecionado automaticamente para o Mercado Pago.
                    </div>
                </form>
            </div>
        </div>

        <div class="plans-card">
            <h2 class="card-title">Planos publicados</h2>
            <p class="card-subtitle">Os planos abaixo sao puxados diretamente do catalogo comercial do sistema e ja refletem os limites operacionais do seu tenant.</p>
            <div class="plans-grid">
                <? if(count($planos)){ foreach($planos as $idx => $plano){ ?>
                    <? $is_featured = stripos((string)$plano->modelo, 'Essencial') !== false || $idx === 1; ?>
                    <div class="plan <?=$is_featured ? 'plan-featured' : ''?>">
                        <div class="plan-top">
                            <h3><?=$plano->modelo?></h3>
                            <span class="plan-label"><?=$is_featured ? 'Mais equilibrado' : ((int)$plano->max_profissionais <= 1 ? 'Entrada' : 'Escala')?></span>
                        </div>
                        <div class="plan-price">R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?></div>
                        <div class="plan-meta">
                            Recorrencia: <?=$plano->billing_interval?> / <?=max(1, (int)$plano->billing_interval_count)?><br>
                            Trial: <?=max(0, (int)$plano->trial_days)?> dia(s)<br>
                            Setup: R$ <?=number_format((float)$plano->setup_fee, 2, ',', '.')?>
                        </div>
                        <div class="plan-copy">
                            <?=trim(strip_tags((string)$plano->especificacoes)) !== '' ? nl2br(htmlspecialchars(trim(strip_tags((string)$plano->especificacoes)))) : 'Plano pronto para contratacao online, com configuracao comercial herdada do catalogo SaaS.'?>
                        </div>
                        <div class="plan-badges">
                            <span class="plan-badge"><?=max(0, (int)$plano->max_profissionais)?> profissionais</span>
                            <span class="plan-badge"><?=max(0, (int)$plano->max_colaboradores)?> colaboradores</span>
                            <span class="plan-badge"><?=max(0, (int)$plano->max_pacientes)?> pacientes</span>
                        </div>
                        <ul class="plan-list">
                            <li>Agenda, pacientes, prontuario e historico clinico em uma unica operacao.</li>
                            <li>Tenant criado com owner principal, assinatura e ciclo inicial prontos para cobranca.</li>
                            <li>Base preparada para evoluir com WhatsApp, portal do paciente e onboarding guiado.</li>
                        </ul>
                        <button type="button" class="plan-action" data-plan-id="<?=$plano->id?>">Escolher este plano</button>
                    </div>
                <? } } else { ?>
                    <div class="alert alert-warn">Nenhum plano SaaS publicado para contratacao publica neste momento.</div>
                <? } ?>
            </div>

            <? if(count($planos) >= 3){ ?>
                <div class="compare-card">
                    <h3 class="compare-title">Qual plano faz mais sentido para cada momento?</h3>
                    <p class="compare-copy">Esta comparacao nao tenta inventar modulos novos. Ela organiza comercialmente a mesma base clinica do sistema de acordo com porte, equipe e volume operacional.</p>
                    <div class="compare-grid">
                        <div class="compare-cell compare-head">Cenario</div>
                        <? foreach(array_slice($planos, 0, 3) as $head_plan){ ?>
                            <? $head_featured = stripos((string)$head_plan->modelo, 'Essencial') !== false; ?>
                            <div class="compare-cell compare-head <?=$head_featured ? 'compare-highlight' : ''?>"><?=$head_plan->modelo?></div>
                        <? } ?>

                        <div class="compare-cell compare-row-title">Perfil ideal</div>
                        <div class="compare-cell">Profissional autonomo ou consultorio enxuto.</div>
                        <div class="compare-cell compare-highlight">Clinica pequena estruturando recepcao, equipe e recorrencia.</div>
                        <div class="compare-cell">Clinica em crescimento com maior volume e mais agenda compartilhada.</div>

                        <div class="compare-cell compare-row-title">Equipe</div>
                        <div class="compare-cell">Operacao compacta, com poucos acessos simultaneos.</div>
                        <div class="compare-cell compare-highlight">Time equilibrado para recepcao, atendimento e gestao.</div>
                        <div class="compare-cell">Equipe multiprofissional com mais colaboradores e fluxo continuo.</div>

                        <div class="compare-cell compare-row-title">Objetivo comercial</div>
                        <div class="compare-cell">Baixa barreira de entrada para validar o sistema.</div>
                        <div class="compare-cell compare-highlight">Plano principal para vender previsibilidade e estrutura.</div>
                        <div class="compare-cell">Upgrade natural para quem cresce sem trocar de plataforma.</div>
                    </div>
                </div>
            <? } ?>
        </div>
    </div>
    <script>
        (function () {
            var planButtons = document.querySelectorAll('[data-plan-id]');
            var planSelect = document.querySelector('select[name="plano_id"]');
            if (!planButtons.length || !planSelect) {
                return;
            }
            planButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    planSelect.value = button.getAttribute('data-plan-id');
                    planSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    planSelect.focus();
                });
            });
        })();
    </script>
</body>
</html>
