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
    <title>Sistema de Gestão Clínica Grátis por 30 Dias — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Experimente grátis por 30 dias sem cartão de crédito. Prontuário eletrônico, agenda inteligente e gestão completa para sua clínica ou consultório médico.">
    <link rel="canonical" href="https://utecnologia.com.br/experimentar">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://utecnologia.com.br/experimentar">
    <meta property="og:title" content="Experimente o Sistema Clínico Grátis por 30 Dias — UTecnologia Saúde">
    <meta property="og:description" content="Prontuário eletrônico, agenda inteligente e gestão de pacientes. Crie sua conta agora e comece a usar em minutos, sem cartão de crédito.">
    <meta property="og:image" content="https://utecnologia.com.br/imagens/og-cover.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="UTecnologia Saúde">
    <meta property="og:locale" content="pt_BR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Experimente o Sistema Clínico Grátis por 30 Dias — UTecnologia Saúde">
    <meta name="twitter:description" content="Prontuário eletrônico, agenda inteligente e gestão de pacientes. Sem cartão de crédito.">
    <meta name="twitter:image" content="https://utecnologia.com.br/imagens/og-cover.png">

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
        .hero-left { display:flex; flex-direction:column; gap:20px; }
        .panel-img { overflow:hidden; padding:0; border-radius:28px; box-shadow:0 24px 60px rgba(23,32,51,.08); }
        .panel-img img { width:100%; display:block; border-radius:28px; }
        .benefits-panel { background:rgba(255,255,255,.88); border:1px solid rgba(208,216,228,.8); border-radius:24px; padding:24px 26px; }
        .benefits-title { font-size:18px; font-weight:700; color:#115e59; margin:0 0 16px; font-family:system-ui,sans-serif; }
        .benefits-list { display:grid; gap:12px; }
        .benefit-item { display:flex; align-items:flex-start; gap:12px; font-size:14px; color:#334155; line-height:1.55; font-family:system-ui,sans-serif; }
        .benefit-item .b-ico { font-size:20px; flex-shrink:0; margin-top:1px; }
        .benefit-item strong { display:block; font-size:14px; font-weight:700; color:#172033; margin-bottom:2px; }
        .benefits-note { margin-top:16px; font-size:13px; color:#667085; font-family:system-ui,sans-serif; line-height:1.6; border-top:1px solid #e2e8f0; padding-top:14px; }
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
        @media (max-width: 980px) {
            .hero,.plans { grid-template-columns:1fr; }
            .hero-left { order:2; }
            .panel { order:1; }
        }
        @media (max-width: 680px) {
            h1 { font-size:30px; }
            .form-grid { grid-template-columns:1fr; }
            .wrap { padding:20px 14px 40px; }
            .card-title { font-size:22px; }
            .btn-submit { width:100%; font-size:16px; padding:15px 20px; }
            input, select { font-size:16px; } /* evita zoom no iOS */
            .benefits-panel { display:none; } /* esconde na mobile para reduzir scroll */
        }
    </style>

    <?php if(!empty($fb_pixel_id)): ?>
    <!-- Meta Pixel — Lead (deduplica com CAPI via event_id) -->
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?=htmlspecialchars((string)$fb_pixel_id)?>');
    fbq('track', 'PageView');
    fbq('track', 'Lead',
        {content_name: 'Trial 30 dias', content_category: '<?=htmlspecialchars((string)$tipo_selecionado)?>'},
        {eventID: '<?=htmlspecialchars((string)$fb_lead_event_id)?>'}
    );
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?=htmlspecialchars((string)$fb_pixel_id)?>&ev=Lead&noscript=1"/></noscript>
    <?php endif; ?>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div class="brand"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:34px;width:auto;display:block"></div>
            <a class="back" href="<?=base_url()?>">← Voltar ao site</a>
        </div>

        <div class="hero">
            <div class="hero-left">
                <div class="panel-img">
                    <img
                        src="<?=base_url()?>imagens/utec-dash3.png"
                        alt="UTecnologia Saúde — gestão completa da sua clínica em um só lugar"
                        loading="eager"
                    >
                </div>

                <div class="benefits-panel">
                    <h1 class="benefits-title">Por que clínicas e profissionais escolhem a UTecnologia Saúde?</h1>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <span class="b-ico">📅</span>
                            <div>
                                <strong>Agenda inteligente</strong>
                                Visualize e gerencie todos os atendimentos por profissional — filtre por data, status e especialidade sem retrabalho.
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="b-ico">📋</span>
                            <div>
                                <strong>Prontuário eletrônico completo</strong>
                                Anamnese, evolução clínica, hipóteses diagnósticas e prescrições em um histórico organizado e acessível a qualquer momento.
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="b-ico">🔬</span>
                            <div>
                                <strong>Exames e arquivos integrados</strong>
                                Solicite exames, registre resultados e armazene documentos diretamente no prontuário do paciente.
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="b-ico">📊</span>
                            <div>
                                <strong>Relatórios e gestão</strong>
                                Indicadores de atendimentos por profissional, período e especialidade para tomada de decisão rápida.
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="b-ico">🔒</span>
                            <div>
                                <strong>Seguro e 100% online</strong>
                                Dados dos pacientes protegidos, acesso por perfil e disponível em qualquer dispositivo com internet.
                            </div>
                        </div>
                    </div>
                    <p class="benefits-note">
                        Utilizado por clínicas de medicina geral, psicologia, fisioterapia, nutrição, fonoaudiologia,
                        oftalmologia e diversas outras especialidades em todo o Brasil.
                        Sistema desenvolvido por profissionais da área de tecnologia em saúde.
                    </p>
                </div>
            </div>

            <div class="panel">
                <div class="eyebrow">30 dias grátis · Sem cartão de crédito</div>
                <h2 class="card-title">Crie seu acesso agora</h2>
                <p class="card-subtitle">Preencha os dados abaixo e comece a usar a agenda, os prontuários e o atendimento em minutos.</p>

                <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
                <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>

                <form method="post" action="<?=base_url()?>experimentar/enviar">
                    <div class="form-grid">
                        <div class="field">
                            <label>Seu nome</label>
                            <input type="text" name="nome_responsavel" placeholder="Ex: Dra. Ana Silva" required>
                        </div>
                        <div class="field">
                            <label>Nome da clínica ou consultório</label>
                            <input type="text" name="tenant_nome" placeholder="Ex: Clínica Bem Estar" required>
                        </div>
                        <div class="field">
                            <label>E-mail de acesso</label>
                            <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
                        </div>
                        <div class="field">
                            <label>WhatsApp</label>
                            <input type="text" name="telefone" placeholder="(00) 00000-0000">
                        </div>
                        <div class="field">
                            <label>Tipo de operação</label>
                            <select name="tenant_tipo">
                                <option value="clinica"       <?=(!isset($tipo_selecionado)||$tipo_selecionado==='clinica'      ?'selected':'')?>  >Clínica (mais de 1 profissional)</option>
                                <option value="consultorio"  <?=($tipo_selecionado==='consultorio' ?'selected':'')?>  >Consultório individual</option>
                                <option value="profissional" <?=($tipo_selecionado==='profissional'?'selected':'')?>  >Profissional autônomo</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Plano</label>
                            <select name="plano_id" required>
                                <option value="">Escolha seu plano</option>
                                <? foreach($planos as $plano){ ?>
                                    <option value="<?=$plano->id?>"><?=$plano->modelo?> — R$ <?=number_format((float)$plano->preco_venda, 2, ',', '.')?>/<?=$plano->billing_interval?></option>
                                <? } ?>
                            </select>
                        </div>
                        <input type="hidden" name="senha" value="">
                        <input type="hidden" name="documento" value="">
                        <input type="hidden" name="observacoes" value="">
                    </div>

                    <div class="submit-row">
                        <button class="btn-submit" type="submit">Começar 30 dias grátis →</button>
                    </div>
                    <p style="font-size:12px;color:#667085;margin-top:12px;text-align:center;">Sem cartão de crédito · Acesso imediato · Você receberá as credenciais por e-mail</p>
                </form>
            </div>
        </div>

        <div class="panel" style="margin-top:24px;">
            <h2 class="card-title">Planos disponíveis no trial</h2>
            <p class="card-subtitle">Escolha o plano que melhor se encaixa no seu perfil. Você usa o sistema por 30 dias grátis e só paga depois, se quiser continuar.</p>
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
