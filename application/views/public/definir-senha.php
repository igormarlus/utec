<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Definir minha senha — UTecnologia Saúde</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .logo { font-size:13px; letter-spacing:.15em; text-transform:uppercase; font-weight:700; color:#115e59; margin-bottom:28px; }
        h1 { font-size:26px; font-weight:800; margin-bottom:8px; }
        .sub { font-size:15px; color:var(--muted); line-height:1.6; margin-bottom:28px; }
        .alert { border-radius:12px; padding:12px 16px; font-size:14px; margin-bottom:20px; }
        .alert-error { background:var(--error-bg); color:var(--error-text); border:1px solid #fecaca; }
        .field { display:grid; gap:6px; margin-bottom:16px; }
        label { font-size:12px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--muted); }
        input[type=password] {
            width:100%; border:1px solid #c9d3df; border-radius:12px;
            padding:12px 14px; font:inherit; color:var(--ink); background:#fff;
            transition:border-color .15s, box-shadow .15s;
        }
        input[type=password]:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(15,118,110,.12); }
        .strength { font-size:12px; color:var(--muted); margin-top:4px; }
        .btn {
            width:100%; border:0; border-radius:999px;
            background:linear-gradient(90deg,var(--primary),var(--accent));
            color:#fff; padding:14px; font-size:15px; font-weight:700;
            cursor:pointer; box-shadow:0 12px 28px rgba(15,118,110,.2);
            transition:filter .15s, transform .1s; margin-top:8px;
        }
        .btn:hover { filter:brightness(1.06); transform:translateY(-1px); }
        .note { font-size:13px; color:var(--muted); text-align:center; margin-top:20px; }
        .note a { color:var(--primary); font-weight:600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">UTecnologia Saúde</div>

        <h1>Defina sua senha</h1>
        <p class="sub">Olá, <strong><?=htmlspecialchars((string)$nome)?></strong>! Crie uma senha personalizada para acessar o sistema.</p>

        <?php if($flash_error): ?>
        <div class="alert alert-error"><?=htmlspecialchars((string)$flash_error)?></div>
        <?php endif; ?>

        <form method="post" action="<?=base_url()?>acesso/salvar" id="formSenha">
            <input type="hidden" name="token" value="<?=htmlspecialchars((string)$token)?>">
            <div class="field">
                <label for="nova_senha">Nova senha</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" required minlength="6" autocomplete="new-password" oninput="verificarForca(this.value)">
                <span class="strength" id="strength-msg"></span>
            </div>
            <div class="field">
                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha" required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" class="btn">Salvar senha e entrar →</button>
        </form>

        <p class="note">Prefere entrar com a senha provisória? <a href="<?=base_url()?>admin">Ir para o login</a></p>
    </div>

    <script>
    function verificarForca(v){
        var el = document.getElementById('strength-msg');
        if(!el) return;
        if(v.length < 6){ el.textContent='Muito curta'; el.style.color='#dc2626'; }
        else if(v.length < 8){ el.textContent='Fraca — adicione mais caracteres'; el.style.color='#ea580c'; }
        else if(/[A-Z]/.test(v) && /[0-9]/.test(v)){ el.textContent='Forte ✓'; el.style.color='#16a34a'; }
        else { el.textContent='Razoável — adicione maiúsculas ou números'; el.style.color='#ca8a04'; }
    }
    document.getElementById('formSenha').addEventListener('submit', function(e){
        var a = document.getElementById('nova_senha').value;
        var b = document.getElementById('confirmar_senha').value;
        if(a !== b){ e.preventDefault(); alert('As senhas não coincidem.'); }
    });
    </script>
</body>
</html>
