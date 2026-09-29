<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Esqueci minha senha — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?=base_url('apple-touch-icon.png')?>">
    <style>
        :root {
            --ink:#172033; --muted:#667085; --line:#d0d8e4;
            --primary:#0f766e; --accent:#f97316;
            --ok-bg:#ecfdf3; --ok-text:#166534;
            --error-bg:#fef2f2; --error-text:#991b1b;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:system-ui,sans-serif; color:var(--ink);
            background:radial-gradient(circle at top left,rgba(15,118,110,.12),transparent 30%),
                        linear-gradient(180deg,#f8fafc,#eef4f7);
            min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
        }
        .card {
            background:#fff; border:1px solid var(--line); border-radius:24px;
            padding:40px 36px; width:100%; max-width:440px;
            box-shadow:0 20px 56px rgba(23,32,51,.1);
        }
        .logo { margin-bottom:28px; }
        h1 { font-size:26px; font-weight:800; margin-bottom:8px; }
        .sub { font-size:15px; color:var(--muted); line-height:1.6; margin-bottom:28px; }
        .alert { border-radius:12px; padding:12px 16px; font-size:14px; line-height:1.5; margin-bottom:20px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok-text); border:1px solid #bbf7d0; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }
        .field { display:grid; gap:6px; margin-bottom:16px; }
        label { font-size:12px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--muted); }
        input[type=text] {
            width:100%; border:1px solid #c9d3df; border-radius:12px;
            padding:12px 14px; font:inherit; color:var(--ink); background:#fff;
        }
        input[type=text]:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(15,118,110,.12); }
        .btn {
            width:100%; border:0; border-radius:999px;
            background:linear-gradient(90deg,var(--primary),var(--accent));
            color:#fff; padding:14px; font-size:15px; font-weight:700;
            cursor:pointer; box-shadow:0 12px 28px rgba(15,118,110,.2); margin-top:8px;
        }
        .note { font-size:13px; color:var(--muted); text-align:center; margin-top:20px; line-height:1.6; }
        .note a { color:var(--primary); font-weight:600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saúde" style="height:46px;width:auto;display:block"></div>

        <h1>Esqueci minha senha</h1>
        <p class="sub">Informe o e-mail ou o usuário que você usa para entrar. Enviaremos um link para você criar uma nova senha.</p>

        <?php if($flash_ok): ?>
        <div class="alert alert-ok"><?=htmlspecialchars((string)$flash_ok)?></div>
        <?php endif; ?>
        <?php if($flash_error): ?>
        <div class="alert alert-error"><?=htmlspecialchars((string)$flash_error)?></div>
        <?php endif; ?>

        <form method="post" action="<?=base_url()?>acesso/esqueci/enviar">
            <div class="field">
                <label for="identificacao">E-mail ou usuário</label>
                <input type="text" id="identificacao" name="identificacao" placeholder="seuemail@clinica.com.br" required autocomplete="username">
            </div>
            <button type="submit" class="btn">Enviar link →</button>
        </form>

        <p class="note">Não chegou? Confira a caixa de spam ou fale com a gente no <a href="https://wa.me/5581983276882">WhatsApp</a>.<br><a href="<?=base_url()?>admin">Voltar para o login</a></p>
    </div>
</body>
</html>
