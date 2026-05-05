<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/bootstrap.min.css" media="screen">
<title>UTecnologia Saude | Acesso</title>
<style>
body {
    margin: 0;
    min-height: 100vh;
    font-family: "Inter", sans-serif;
    background:
        radial-gradient(circle at top left, rgba(34, 197, 94, 0.18), transparent 35%),
        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.22), transparent 30%),
        linear-gradient(135deg, #eff6ff 0%, #f8fafc 45%, #ecfeff 100%);
    color: #0f172a;
}
.login-shell {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}
.login-card {
    width: 100%;
    max-width: 920px;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(320px, 420px);
    background: rgba(255,255,255,0.96);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
}
.login-brand {
    padding: 48px;
    background: linear-gradient(160deg, #0f766e 0%, #0ea5e9 55%, #22c55e 100%);
    color: #fff;
}
.login-brand img {
    width: 160px;
    margin-bottom: 24px;
}
.login-brand h1 {
    font-size: 34px;
    line-height: 1.15;
    margin: 0 0 16px;
}
.login-brand p {
    font-size: 15px;
    line-height: 1.6;
    margin: 0 0 24px;
    color: rgba(255,255,255,0.88);
}
.login-brand ul {
    padding-left: 18px;
    margin: 0;
    color: rgba(255,255,255,0.9);
}
.login-brand li {
    margin-bottom: 8px;
}
.login-form {
    padding: 48px 40px;
}
.login-form h2 {
    font-size: 28px;
    margin: 0 0 10px;
}
.login-form p {
    margin: 0 0 28px;
    color: #475569;
}
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #0f172a;
}
.form-group input {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 14px;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.form-group input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
}
.btn-login {
    width: 100%;
    border: 0;
    border-radius: 14px;
    padding: 14px 18px;
    background: linear-gradient(90deg, #0ea5e9 0%, #22c55e 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
}
.login-note {
    margin-top: 16px;
    font-size: 12px;
    color: #64748b;
}
@media (max-width: 860px) {
    .login-card {
        grid-template-columns: 1fr;
    }
    .login-brand,
    .login-form {
        padding: 32px 24px;
    }
}
</style>
</head>
<body>
  <div class="login-shell">
    <div class="login-card">
      <div class="login-brand">
        <img src="<?php echo base_url();?>img/logo-w.png" alt="UTecnologia Saude">
        <h1>Gestao clinica com mais clareza e controle.</h1>
        <p>Centralize pacientes, agenda, prontuarios e operacao da clinica em um ambiente mais organizado e pronto para crescer.</p>
        <ul>
          <li>Agenda e atendimentos em um unico fluxo</li>
          <li>Prontuario e documentos do paciente organizados</li>
          <li>Base pronta para planos, assinaturas e relatorios</li>
        </ul>
      </div>
      <div class="login-form">
        <h2>Acessar plataforma</h2>
        <p>Entre com suas credenciais para continuar na UTecnologia Saude.</p>
        <form action="<?=base_url()?>admin/logar" method="post">
          <div class="form-group">
            <label for="login">Usuario</label>
            <input id="login" type="text" name="login" placeholder="Digite seu usuario">
          </div>
          <div class="form-group">
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha">
          </div>
          <button type="submit" class="btn-login">Entrar</button>
        </form>
        <div class="login-note">
          Ambiente restrito para administradores, recepcao, profissionais e clinicas cadastradas.
        </div>
      </div>
    </div>
  </div>
</body>
</html>
