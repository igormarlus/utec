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
            --primary-dark:#0d5f58;
            --primary-light:#e6f7f5;
            --accent:#f97316;
            --ok-bg:#ecfdf3;
            --ok-text:#166534;
            --error-bg:#fef2f2;
            --error-text:#991b1b;
            --radius-sm:10px;
            --radius-md:14px;
            --radius-lg:22px;
            --radius-xl:28px;
            --shadow-sm:0 2px 8px rgba(23,32,51,.06);
            --shadow-md:0 8px 24px rgba(23,32,51,.08);
            --shadow-lg:0 24px 60px rgba(23,32,51,.10);
            --transition:all .18s ease;
        }
        * { box-sizing:border-box; }
        body {
            margin:0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                radial-gradient(circle at top right, rgba(249,115,22,.08), transparent 26%),
                linear-gradient(180deg,#f8fafc 0%,#eef4f7 100%);
            min-height:100vh;
        }
        .wrap { max-width:1120px; margin:0 auto; padding:32px 20px 64px; }

        /* Topbar */
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:32px; flex-wrap:wrap; }
        .brand { font-size:14px; letter-spacing:.16em; text-transform:uppercase; color:#115e59; font-weight:700; }
        .back {
            display:inline-flex; align-items:center; gap:6px;
            color:var(--muted); text-decoration:none; font-size:14px; font-weight:500;
            padding:6px 14px; border-radius:999px; border:1px solid var(--line);
            background:#fff; transition:var(--transition);
        }
        .back:hover { color:var(--primary); border-color:var(--primary); background:var(--primary-light); }

        /* Hero layout */
        .hero { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(340px,.85fr); gap:28px; align-items:start; }
        .hero-left { display:flex; flex-direction:column; gap:22px; }
        .panel-img { overflow:hidden; padding:0; border-radius:var(--radius-xl); box-shadow:var(--shadow-lg); }
        .panel-img img { width:100%; display:block; border-radius:var(--radius-xl); }

        /* Benefits panel */
        .benefits-panel {
            background:rgba(255,255,255,.90);
            backdrop-filter:blur(8px);
            border:1px solid rgba(208,216,228,.7);
            border-radius:var(--radius-xl);
            padding:26px 28px;
            box-shadow:var(--shadow-md);
        }
        .benefits-title { font-size:17px; font-weight:700; color:#115e59; margin:0 0 18px; line-height:1.4; }
        .benefits-list { display:grid; gap:14px; }
        .benefit-item { display:flex; align-items:flex-start; gap:13px; font-size:14px; color:#334155; line-height:1.6; }
        .benefit-item .b-ico {
            font-size:18px; flex-shrink:0; margin-top:1px;
            width:36px; height:36px; border-radius:10px;
            background:var(--primary-light); display:flex; align-items:center; justify-content:center;
        }
        .benefit-item strong { display:block; font-size:13.5px; font-weight:700; color:#172033; margin-bottom:2px; }
        .benefits-note { margin-top:18px; font-size:13px; color:#667085; line-height:1.65; border-top:1px solid #e8edf3; padding-top:16px; }

        /* Form panel */
        .panel {
            background:rgba(255,255,255,.96);
            backdrop-filter:blur(12px);
            border:1px solid rgba(208,216,228,.8);
            border-radius:var(--radius-xl);
            padding:30px 28px;
            box-shadow:var(--shadow-lg);
        }
        .eyebrow {
            display:inline-flex; align-items:center; gap:6px;
            font-size:11.5px; letter-spacing:.16em; text-transform:uppercase;
            color:var(--accent); font-weight:700;
            background:rgba(249,115,22,.08); border-radius:999px;
            padding:4px 12px; margin-bottom:2px;
        }
        h1 { font-size:46px; line-height:1.04; margin:14px 0 14px; }
        .lead { font-size:17px; line-height:1.75; color:#46566e; }
        .points { display:grid; gap:10px; margin-top:22px; }
        .point { border:1px solid rgba(15,118,110,.14); background:linear-gradient(90deg, rgba(15,118,110,.08), rgba(249,115,22,.06)); border-radius:16px; padding:14px 16px; font-size:15px; }

        /* Alerts */
        .alert { border-radius:var(--radius-md); padding:14px 16px; font-size:14px; margin-bottom:18px; display:flex; align-items:flex-start; gap:10px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok-text); border:1px solid #bbf7d0; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }

        /* Card header */
        .card-title { font-size:26px; font-weight:800; margin:0 0 6px; letter-spacing:-.02em; }
        .card-subtitle { color:var(--muted); font-size:14.5px; line-height:1.65; margin:0 0 22px; }

        /* Form fields */
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        .field { display:grid; gap:6px; }
        .field-wide { grid-column:1 / -1; }
        label {
            font-size:11.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
            color:#55677d; display:flex; align-items:center; gap:4px;
        }
        .field-hint { font-size:12px; color:var(--muted); margin-top:2px; }

        input, select, textarea {
            width:100%;
            border:1.5px solid #d1dae5;
            border-radius:var(--radius-md);
            padding:11px 14px;
            font:inherit;
            font-size:14.5px;
            color:var(--ink);
            background:#fff;
            transition:var(--transition);
            outline:none;
            -webkit-appearance:none;
            appearance:none;
        }
        input::placeholder, textarea::placeholder { color:#9aabb8; }
        input:hover, select:hover, textarea:hover { border-color:#a8bcc9; }
        input:focus, select:focus, textarea:focus {
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(15,118,110,.12);
            background:#fafffe;
        }
        select {
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23667085' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat:no-repeat;
            background-position:right 14px center;
            padding-right:36px;
        }
        textarea { min-height:96px; resize:vertical; }

        /* Tipo cards (radio-like) */
        .tipo-cards { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
        .tipo-card { position:relative; }
        .tipo-card input[type="radio"] { position:absolute; opacity:0; width:0; height:0; }
        .tipo-card-label {
            display:flex; flex-direction:column; align-items:center; gap:6px;
            border:1.5px solid #d1dae5; border-radius:var(--radius-lg);
            padding:14px 10px 12px; cursor:pointer; text-align:center;
            background:#fff; transition:var(--transition); user-select:none;
        }
        .tipo-card-label:hover { border-color:var(--primary); background:var(--primary-light); }
        .tipo-card input[type="radio"]:checked + .tipo-card-label {
            border-color:var(--primary); background:var(--primary-light);
            box-shadow:0 0 0 3px rgba(15,118,110,.10);
        }
        .tipo-card-ico { font-size:22px; line-height:1; }
        .tipo-card-title { font-size:12.5px; font-weight:700; color:var(--ink); line-height:1.3; }
        .tipo-card-desc { font-size:11px; color:var(--muted); line-height:1.4; }

        /* Especialidade animated reveal */
        .field-esp-wrap {
            overflow:hidden;
            max-height:0;
            opacity:0;
            transition:max-height .28s ease, opacity .22s ease, margin-top .22s ease;
            margin-top:0;
        }
        .field-esp-wrap.visible {
            max-height:120px;
            opacity:1;
            margin-top:0;
        }

        /* Submit row */
        .submit-row { display:flex; flex-wrap:wrap; gap:12px; margin-top:20px; align-items:center; }
        .btn-submit {
            border:0; border-radius:999px;
            background:linear-gradient(90deg,var(--primary) 0%,#1a9e94 50%,var(--accent) 100%);
            background-size:200% 100%;
            color:#fff;
            padding:14px 28px;
            font-size:15px; font-weight:700;
            cursor:pointer;
            box-shadow:0 12px 32px rgba(15,118,110,.22);
            transition:background-position .3s ease, box-shadow .2s ease, transform .15s ease;
            letter-spacing:.01em;
        }
        .btn-submit:hover {
            background-position:100% 0;
            box-shadow:0 16px 40px rgba(15,118,110,.30);
            transform:translateY(-1px);
        }
        .btn-submit:active { transform:translateY(0); box-shadow:0 8px 20px rgba(15,118,110,.20); }

        /* Plans section */
        .plans { margin-top:24px; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
        .plan {
            border:1.5px solid #dbe2ea; border-radius:var(--radius-lg);
            padding:20px 18px; background:linear-gradient(180deg,#fff 0%,#f9fbfd 100%);
            transition:var(--transition);
        }
        .plan:hover { border-color:var(--primary); box-shadow:var(--shadow-md); transform:translateY(-2px); }
        .plan h3 { margin:0 0 8px; font-size:20px; font-weight:800; }
        .plan-price { font-size:30px; line-height:1; margin:8px 0 10px; font-weight:800; color:var(--primary); }
        .plan-copy { font-size:13.5px; line-height:1.75; color:#43526a; }

        @media (max-width: 980px) {
            .hero, .plans { grid-template-columns:1fr; }
            .hero-left { order:2; }
            .panel { order:1; }
        }
        @media (max-width: 680px) {
            h1 { font-size:30px; }
            .form-grid { grid-template-columns:1fr; }
            .wrap { padding:20px 14px 44px; }
            .card-title { font-size:22px; }
            .btn-submit { width:100%; font-size:16px; padding:15px 20px; justify-content:center; }
            input, select { font-size:16px; }
            .benefits-panel { display:none; }
            .tipo-cards { grid-template-columns:1fr; gap:8px; }
            .tipo-card-label { flex-direction:row; text-align:left; gap:12px; padding:12px 14px; }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            <div class="brand"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></div>
            <a class="back" href="<?=base_url()?>">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 11L5 7l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Voltar ao site
            </a>
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
                        <div class="field field-wide">
                            <label>Tipo de operação</label>
                            <div class="tipo-cards">
                                <div class="tipo-card">
                                    <input type="radio" name="tenant_tipo" id="tipo_clinica" value="clinica"
                                        <?=(!isset($tipo_selecionado)||$tipo_selecionado==='clinica'?'checked':'')?>>
                                    <label class="tipo-card-label" for="tipo_clinica">
                                        <span class="tipo-card-ico">🏥</span>
                                        <span class="tipo-card-title">Clínica</span>
                                        <span class="tipo-card-desc">Mais de 1 profissional</span>
                                    </label>
                                </div>
                                <div class="tipo-card">
                                    <input type="radio" name="tenant_tipo" id="tipo_consultorio" value="consultorio"
                                        <?=($tipo_selecionado==='consultorio'?'checked':'')?>>
                                    <label class="tipo-card-label" for="tipo_consultorio">
                                        <span class="tipo-card-ico">🩺</span>
                                        <span class="tipo-card-title">Consultório</span>
                                        <span class="tipo-card-desc">Consultório individual</span>
                                    </label>
                                </div>
                                <div class="tipo-card">
                                    <input type="radio" name="tenant_tipo" id="tipo_profissional" value="profissional"
                                        <?=($tipo_selecionado==='profissional'?'checked':'')?>>
                                    <label class="tipo-card-label" for="tipo_profissional">
                                        <span class="tipo-card-ico">👤</span>
                                        <span class="tipo-card-title">Profissional</span>
                                        <span class="tipo-card-desc">Autônomo independente</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="field field-wide">
                            <div class="field-esp-wrap" id="campo-especialidade">
                                <label>Sua especialidade <span style="color:#e53e3e">*</span></label>
                                <?php if(!empty($especialidades)){ ?>
                                    <select name="especialidade_id" id="select-especialidade">
                                        <option value="">— Selecione sua especialidade —</option>
                                        <?php foreach($especialidades as $esp){ ?>
                                            <option value="<?=$esp->id?>"><?=htmlspecialchars($esp->nome)?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select name="especialidade_id" id="select-especialidade" disabled>
                                        <option value="">Especialidades indisponíveis no momento</option>
                                    </select>
                                    <small style="color:#e53e3e;margin-top:4px;display:block">Entre em contato para finalizar seu cadastro.</small>
                                <?php } ?>
                            </div>
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
<script>
(function(){
    var campoEsp  = document.getElementById('campo-especialidade');
    var selectEsp = document.getElementById('select-especialidade');
    var tipoInputs = document.querySelectorAll('input[name="tenant_tipo"]');
    if(!campoEsp || !tipoInputs.length) return;

    var podeValidar = selectEsp && !selectEsp.disabled && selectEsp.options.length > 1;

    function getTipoSelecionado(){
        for(var i=0; i<tipoInputs.length; i++){
            if(tipoInputs[i].checked) return tipoInputs[i].value;
        }
        return '';
    }

    function toggleEsp(){
        var tipo = getTipoSelecionado();
        var mostrar = (tipo === 'profissional' || tipo === 'consultorio');
        if(mostrar){
            campoEsp.classList.add('visible');
            if(podeValidar) selectEsp.required = true;
        } else {
            campoEsp.classList.remove('visible');
            if(selectEsp){ selectEsp.required = false; selectEsp.value = ''; }
            var msg = document.getElementById('esp-err-msg');
            if(msg) msg.remove();
            if(selectEsp) selectEsp.style.borderColor = '';
        }
    }

    tipoInputs.forEach(function(inp){ inp.addEventListener('change', toggleEsp); });
    toggleEsp();

    var form = campoEsp.closest('form');
    if(form && podeValidar){
        form.addEventListener('submit', function(e){
            var tipo = getTipoSelecionado();
            if((tipo === 'profissional' || tipo === 'consultorio') && !selectEsp.value){
                e.preventDefault();
                selectEsp.focus();
                selectEsp.style.borderColor = '#e53e3e';
                var msg = document.getElementById('esp-err-msg');
                if(!msg){
                    msg = document.createElement('small');
                    msg.id = 'esp-err-msg';
                    msg.style.cssText = 'color:#e53e3e;font-size:12px;margin-top:6px;display:block';
                    msg.textContent = 'Selecione sua especialidade antes de continuar.';
                    campoEsp.appendChild(msg);
                }
            }
        });
        selectEsp.addEventListener('change', function(){
            selectEsp.style.borderColor = '';
            var msg = document.getElementById('esp-err-msg');
            if(msg) msg.remove();
        });
    }
})();
</script>
</body>
</html>

