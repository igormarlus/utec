<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title> ** UTecnologia Saúde - Gestão de Pacientes e Clínicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UTecnologia Saúde - Sistema online para gestão de pacientes, prontuários, exames e agenda médica para clínicas e consultórios.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --azul-claro: #e0f2fe;
            --azul-medio: #38bdf8;
            --verde-claro: #dcfce7;
            --verde-medio: #22c55e;
            --texto: #0f172a;
            --cinza-claro: #f8fafc;
            --borda-card: #dbeafe;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, var(--azul-claro), var(--verde-claro));
            color: var(--texto);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        header {
            width: 100%;
            padding: 16px 24px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--azul-medio), var(--verde-medio));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 20px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .logo-text span:first-child {
            font-weight: 700;
            font-size: 18px;
        }

        .logo-text span:last-child {
            font-weight: 400;
            font-size: 12px;
            color: #64748b;
        }

        nav {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }

        nav a {
            padding: 6px 0;
            position: relative;
        }

        nav a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--azul-medio), var(--verde-medio));
            transition: width 0.2s ease-in-out;
        }

        nav a:hover::after {
            width: 100%;
        }

        .btn-header-login {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            border: 1px solid var(--azul-medio);
            background: white;
            color: #0f172a;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        }

        .btn-header-login:hover {
            background: var(--azul-claro);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.12);
            transform: translateY(-1px);
        }

        main {
            flex: 1;
        }

        .hero {
            padding: 40px 16px 30px;
        }

        .hero-inner {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.5fr);
            gap: 32px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .hero-inner {
                grid-template-columns: 1fr;
            }
        }

        .hero-text h1 {
            font-size: 32px;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .hero-text h1 span {
            background: linear-gradient(90deg, #0ea5e9, #22c55e);
            -webkit-background-clip: text;
            color: transparent;
        }

        .hero-text p {
            font-size: 15px;
            color: #475569;
            margin-bottom: 18px;
            max-width: 520px;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            color: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--azul-medio), var(--verde-medio));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.4);
            transition: transform 0.1s, box-shadow 0.2s, filter 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 13px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary span.icon {
            font-size: 16px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 18px;
            padding: 24px 20px;
            box-shadow: 0 14px 45px rgba(15, 23, 42, 0.14);
            border: 1px solid var(--borda-card);
        }

        .login-card h2 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .login-card p {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 500;
            color: #0f172a;
        }

        .form-group input {
            border-radius: 10px;
            border: 1px solid #cbd5f5;
            padding: 8px 10px;
            font-size: 13px;
            outline: none;
            transition: border 0.15s, box-shadow 0.15s, background 0.15s;
        }

        .form-group input:focus {
            border-color: var(--azul-medio);
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.2);
            background: #f9fcff;
        }

        .login-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #64748b;
        }

        .remember input[type="checkbox"] {
            width: 13px;
            height: 13px;
        }

        .forgot a {
            color: var(--azul-medio);
            font-weight: 500;
        }

        .login-card button {
            width: 100%;
        }

        .login-extra {
            margin-top: 10px;
            font-size: 11px;
            text-align: center;
            color: #64748b;
        }

        .login-extra a {
            color: var(--verde-medio);
            font-weight: 600;
        }

        .section {
            padding: 20px 16px 26px;
        }

        .section-inner {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 18px;
            padding: 20px 18px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.25);
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .section-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 18px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        @media (max-width: 900px) {
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--cinza-claro);
            border-radius: 14px;
            padding: 14px 12px;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .card h3 {
            font-size: 14px;
            margin-bottom: 6px;
        }

        .card p {
            font-size: 13px;
            color: #64748b;
        }

        .footer {
            padding: 14px 16px 18px;
            background: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
            font-size: 12px;
            margin-top: 20px;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .footer a {
            color: #a5f3fc;
        }

        .footer-small {
            color: #9ca3af;
        }
    </style>
</head>
<body>

<header>
    <div class="container header-inner">
        <div class="logo">
            <div class="logo-icon">UT</div>
            <div class="logo-text">
                <span>UTecnologia Saúde</span>
                <span>Gestão para clínicas e consultórios</span>
            </div>
        </div>
        <nav>
            <a href="#sobre">Sobre</a>
            <a href="#servicos">Funcionalidades</a>
            <a href="#contato">Contato</a>
        </nav>
        <button class="btn-header-login" onclick="document.getElementById('campo-login').scrollIntoView({behavior:'smooth'})">
            Acesso ao sistema
        </button>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container hero-inner">
            <div class="hero-text">
                <h1>
                    Sistema completo para <span>gestão de pacientes</span> e clínicas.
                </h1>
                <p>
                    O <strong>UTecnologia Saúde</strong> é um sistema online desenvolvido para a área médica,
                    permitindo o controle de pacientes, histórico clínico, prontuários, exames e agenda
                    de atendimentos em um só lugar.
                </p>

                <div class="hero-badges">
                    <div class="hero-badge">Prontuário eletrônico de pacientes (PEP)</div>
                    <div class="hero-badge">Agenda médica e lembretes</div>
                    <div class="hero-badge">Histórico de exames e evoluções clínicas</div>
                </div>

                <div class="hero-actions">
                    <button class="btn-primary" onclick="document.getElementById('contato').scrollIntoView({behavior:'smooth'})">
                        Solicitar demonstração
                    </button>
                    <button class="btn-secondary" onclick="document.getElementById('campo-login').scrollIntoView({behavior:'smooth'})">
                        <span class="icon">🔐</span>
                        Entrar no sistema
                    </button>
                </div>
            </div>

            <div id="campo-login" class="login-card">
                <h2>Login do Profissional / Clínica</h2>
                <p>Acesse seus pacientes, agendas, prontuários e relatórios de forma segura.</p>

                <!-- Ajuste a action para o seu backend (PHP, Laravel, CodeIgniter, etc.) -->
                <form action="<?=base_url()?>admin/logar" method="post">
                    <div class="form-group">
                        <label for="usuario">E-mail ou usuário</label>
                        <input type="text" id="usuario" name="login" placeholder="seuemail@clinica.com.br" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                    </div>

                    <div class="login-row">
                        <label class="remember">
                            <input type="checkbox" name="lembrar" value="1">
                            <span>Lembrar-me neste dispositivo</span>
                        </label>
                        <div class="forgot">
                            <a href="#">Esqueci minha senha</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Entrar no sistema
                    </button>

                    <div class="login-extra">
                        Sua clínica ainda não tem acesso?
                        <a href="#contato">Solicitar cadastro</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section id="sobre" class="section">
        <div class="container section-inner">
            <h2 class="section-title">Sobre o UTecnologia Saúde</h2>
            <p class="section-subtitle">
                Um sistema pensado para a rotina de clínicas, consultórios e profissionais da saúde.
            </p>

            <p style="font-size: 14px; color: #475569; margin-bottom: 12px;">
                O <strong>UTecnologia Saúde</strong> nasceu com o objetivo de organizar o dia a dia
                de profissionais da área médica, centralizando informações importantes do paciente em
                um sistema simples, moderno e acessível de qualquer lugar.
            </p>

            <p style="font-size: 14px; color: #475569;">
                Com ele, você acompanha o <strong>histórico completo do paciente</strong> (consultas,
                exames, evoluções, anotações), reduz o uso de papel, melhora a segurança dos dados e
                otimiza o atendimento, oferecendo mais tempo para cuidar de quem importa: o paciente.
            </p>
        </div>
    </section>

    <section id="servicos" class="section">
        <div class="container section-inner">
            <h2 class="section-title">Funcionalidades principais</h2>
            <p class="section-subtitle">
                Recursos que ajudam a organizar a gestão de pacientes, equipe e atendimentos.
            </p>

            <div class="grid-3">
                <div class="card">
                    <h3>Prontuário eletrônico completo</h3>
                    <p>
                        Registre o histórico clínico de cada paciente: queixas, diagnósticos,
                        prescrições, evolução, anotações importantes e anexos como exames em PDF ou imagem.
                    </p>
                </div>
                <div class="card">
                    <h3>Agenda médica e lembretes</h3>
                    <p>
                        Controle de consultas por profissional e sala, com visão diária, semanal ou mensal.
                        Possibilidade de integrar lembretes de consulta (ex.: e-mail, WhatsApp ou SMS)
                        conforme o seu fluxo.
                    </p>
                </div>
                <div class="card">
                    <h3>Exames e documentos do paciente</h3>
                    <p>
                        Organize laudos, exames laboratoriais, imagens e documentos relevantes de cada
                        paciente em um único lugar, facilitando o acesso ao histórico em novos atendimentos.
                    </p>
                </div>
                <div class="card">
                    <h3>Cadastro de pacientes e responsáveis</h3>
                    <p>
                        Dados cadastrais completos, contatos, convênios, planos, dados de responsável
                        (quando necessário) e observações gerais sobre cada paciente.
                    </p>
                </div>
                <div class="card">
                    <h3>Relatórios e visão gerencial</h3>
                    <p>
                        Geração de relatórios de atendimentos, especialidades, profissionais e períodos,
                        ajudando na tomada de decisão e na organização da gestão da clínica.
                    </p>
                </div>
                <div class="card">
                    <h3>Segurança e controle de acesso</h3>
                    <p>
                        Perfis de usuários (médicos, recepção, gestor), controle de permissões e acesso
                        protegido por senha, contribuindo para a confidencialidade dos dados dos pacientes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="contato" class="section">
        <div class="container section-inner">
            <h2 class="section-title">Fale com a UTecnologia</h2>
            <p class="section-subtitle">
                Entre em contato para conhecer melhor o sistema e solicitar uma demonstração para sua clínica.
            </p>

            <p style="font-size: 14px; color: #475569; margin-bottom: 10px;">
                Você pode adaptar este bloco para um formulário real (PHP, Laravel, etc.).
                Abaixo, um exemplo de como exibir as informações de contato da sua equipe comercial.
            </p>

            <div class="grid-3">
                <div class="card">
                    <h3>E-mail</h3>
                    <p>
                        comercial@utecnologia.com.br<br>
                        suporte@utecnologia.com.br
                    </p>
                </div>

                <div class="card">
                    <h3>WhatsApp / Telefone</h3>
                    <p>
                        (00) 00000-0000<br>
                        Atendimento para clínicas e consultórios em horário comercial.
                    </p>
                </div>

                <div class="card">
                    <h3>Atendimento em todo o Brasil</h3>
                    <p>
                        Sistema 100% online, acessível via navegador.<br>
                        Ideal para clínicas, consultórios e pequenos centros de saúde.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-inner">
        <div>
            © <span id="ano-atual"></span> UTecnologia Saúde – <span class="footer-small">utecnologia.com.br</span>. 
            Todos os direitos reservados.
        </div>
        <div class="footer-small">
            Desenvolvido por <a href="https://utecnologia.com.br" target="_blank" rel="noopener noreferrer">UTecnologia</a>
        </div>
    </div>
</footer>

<script>
    // Só para manter o ano do rodapé sempre atualizado
    document.getElementById('ano-atual').textContent = new Date().getFullYear();
</script>

</body>
</html>
