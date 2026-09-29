<?php
// Testes estáticos (leitura de código-fonte), mesmo padrão de tests/whatsapp_*_source_test.php.
$raiz = __DIR__ . '/..';

function src($caminho) {
    global $raiz;
    $arquivo = $raiz . '/' . $caminho;
    if (!file_exists($arquivo)) {
        fwrite(STDERR, 'Arquivo ausente: ' . $caminho . PHP_EOL);
        exit(1);
    }
    return file_get_contents($arquivo);
}
function assertSrc($cond, $label) {
    if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); }
}
// Corpo de uma função: do início da assinatura até a próxima declaração de função
// (cobre os estilos do projeto: "\tpublic function", "\tfunction", "\tprivate function", "function" na coluna 0).
function corpoFuncao($codigo, $assinatura) {
    $ini = strpos($codigo, $assinatura);
    if ($ini === false) { return ''; }
    $fim = strlen($codigo);
    foreach (array("\n\tpublic function ", "\n\tprivate function ", "\n\tprotected function ", "\n\tfunction ", "\nfunction ") as $marca) {
        $pos = strpos($codigo, $marca, $ini + 1);
        if ($pos !== false && $pos < $fim) { $fim = $pos; }
    }
    return substr($codigo, $ini, $fim - $ini);
}

// --- Task 2: library
$lib = src('application/libraries/Email_acesso.php');
assertSrc(strpos($lib, 'class Email_acesso') !== false, 'Library Email_acesso deve existir.');
foreach (array('function boas_vindas(', 'function acesso_equipe(', 'function redefinicao(') as $m) {
    assertSrc(strpos($lib, $m) !== false, 'Email_acesso deve ter ' . $m);
}
assertSrc(strpos($lib, 'bcc(') === false, 'E-mail com senha nao pode ir com BCC (spec secao 5).');
assertSrc(strpos($lib, "->clear(") !== false, 'Email_acesso deve limpar o estado entre envios.');
assertSrc(strpos($lib, "'acesso/senha/'") !== false, 'Links de token devem usar acesso/senha/{token}.');

// --- Task 3: esqueci minha senha + login
$rotas = src('application/config/routes.php');
assertSrc(strpos($rotas, "\$route['acesso/esqueci'] = 'home/esqueci_senha';") !== false, 'Rota acesso/esqueci ausente.');
assertSrc(strpos($rotas, "\$route['acesso/esqueci/enviar'] = 'home/enviar_redefinicao';") !== false, 'Rota acesso/esqueci/enviar ausente.');

$home = src('application/controllers/Home.php');
$enviar = corpoFuncao($home, 'public function enviar_redefinicao()');
assertSrc($enviar !== '', 'Home::enviar_redefinicao() ausente.');
assertSrc(strpos($enviar, 'DATE_ADD(NOW(), INTERVAL 60 MINUTE)') !== false, 'Expiracao da redefinicao deve ser calculada no MySQL (60 min).');
assertSrc(strpos($enviar, 'TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires)') !== false, 'Throttle deve usar o relogio do MySQL.');
assertSrc(strpos($enviar, 'utec_acesso_pode_reenviar(') !== false, 'Throttle ausente.');
assertSrc(substr_count($enviar, "set_flashdata('esqueci_ok'") === 1 && strpos($enviar, 'esqueci_msg_generica') !== false, 'Resposta do esqueci deve ser unica e generica.');

$salvar = corpoFuncao($home, 'public function salvar_senha()');
assertSrc(strpos($salvar, "if(!\$this->session->userdata('id'))") === false, 'salvar_senha deve sempre gravar a sessao do usuario do token.');
assertSrc(strpos($salvar, "'usr'   => true") !== false, 'Sessao apos salvar senha deve usar usr => true.');
assertSrc(strpos($salvar, 'utec_acesso_validar_senha(') !== false, 'salvar_senha deve validar via helper.');
assertSrc(strpos(corpoFuncao($home, 'public function definir_senha('), "redirect('experimentar')") === false, 'Token invalido deve voltar para o login, nao para experimentar.');

$admin = src('application/controllers/Admin.php');
assertSrc(strpos($admin, "adm/esqueceu-senha") === false, 'Admin::esqueceuSenha nao pode carregar view inexistente.');

$um = src('application/models/adm/Usuarios_model.php');
assertSrc(strpos($um, "set_flashdata('login_error'") !== false, 'Login invalido deve mostrar mensagem.');

$login = src('application/views/adm/login.php');
assertSrc(strpos($login, 'acesso/esqueci') !== false, 'Tela /admin deve linkar Esqueci minha senha.');
assertSrc(strpos($login, "flashdata('login_error')") !== false, 'Tela /admin deve exibir login_error.');

$front = src('application/views/index-front.php');
assertSrc(strpos($front, '<a href="#">Esqueci minha senha</a>') === false, 'Links Esqueci da landing nao podem apontar para #.');
assertSrc(substr_count($front, 'acesso/esqueci') >= 2, 'Landing deve linkar acesso/esqueci no modal e na secao de login.');

$view = src('application/views/public/esqueci-senha.php');
assertSrc(strpos($view, 'acesso/esqueci/enviar') !== false && strpos($view, 'name="identificacao"') !== false, 'View esqueci-senha incompleta.');

assertSrc(strpos($um, 'LOWER(email) = ?') !== false, 'logar() deve aceitar e-mail quando o login nao existe.');
assertSrc(strpos($um, 'num_rows() === 1') !== false || strpos($um, 'count($por_email) === 1') !== false, 'Fallback por e-mail so pode entrar com exatamente 1 usuario.');

// --- Task 4: trial e assinatura
$exp = src('application/views/public/experimentar.php');
assertSrc(strpos($exp, '<input type="hidden" name="senha"') === false, 'experimentar.php nao pode mais mandar senha oculta.');
assertSrc(strpos($exp, 'name="senha"') !== false && strpos($exp, 'name="senha_confirmacao"') !== false, 'experimentar.php precisa de senha + confirmacao.');

$ass = src('application/views/public/assinar.php');
assertSrc(strpos($ass, 'name="senha_confirmacao"') !== false, 'assinar.php precisa de confirmacao de senha.');

$saas = src('application/models/adm/Saas_model.php');
$trial = corpoFuncao($saas, 'function create_operational_trial_signup(');
assertSrc(strpos($trial, 'random_int(') === false, 'Trial nao pode mais gerar senha aleatoria.');
assertSrc(strpos($trial, 'utec_acesso_validar_senha(') !== false, 'Trial deve validar a senha do usuario.');
assertSrc(strpos($trial, "'senha_gerada'") === false, 'Trial nao retorna mais senha_gerada.');
$pub = corpoFuncao($saas, 'function create_public_tenant_signup(');
assertSrc(strpos($pub, 'utec_acesso_validar_senha(') !== false, 'Assinatura deve validar senha + confirmacao.');
assertSrc(strpos($pub, "'plano_nome'") !== false, 'Assinatura deve retornar plano_nome para o e-mail.');

$home = src('application/controllers/Home.php');
assertSrc(strpos($home, '_enviar_email_boas_vindas') === false, 'E-mail de boas-vindas deve usar a library Email_acesso.');
assertSrc(strpos(corpoFuncao($home, 'public function iniciar_experiencia()'), 'email_acesso->boas_vindas(') !== false, 'Trial deve enviar boas-vindas.');
assertSrc(strpos(corpoFuncao($home, 'public function contratar()'), 'email_acesso->boas_vindas(') !== false, 'Assinatura deve enviar boas-vindas.');
assertSrc(strpos(corpoFuncao($home, 'public function iniciar_experiencia()'), "'senha_confirmacao'") !== false, 'Trial deve repassar senha_confirmacao ao model.');

// --- Task 5: usuarios criados pela equipe
$usu = src('application/controllers/adm/Usuarios.php');
$cad = corpoFuncao($usu, 'function cadastrar() {');
assertSrc($cad !== '', 'Usuarios::cadastrar() nao encontrado.');
assertSrc(strpos($cad, 'email_acesso->acesso_equipe(') !== false, 'cadastrar() deve enviar e-mail de acesso a equipe.');
assertSrc(strpos($cad, 'DATE_ADD(NOW(), INTERVAL 7 DAY)') !== false, 'Convite deve expirar em 7 dias pelo relogio do MySQL.');
assertSrc(strpos($cad, 'utec_acesso_senha_aleatoria(') !== false, 'Convite sem senha deve gravar senha aleatoria interna.');
assertSrc(strpos($cad, "'cadastro_aviso'") !== false, 'Sem e-mail e sem senha deve avisar quem cadastrou.');
assertSrc(strpos($cad, "'DATE_ADD(NOW(), INTERVAL 7 DAY)'") > strpos($cad, 'get_usuario_tenant_id('), 'set() do convite deve ficar depois das consultas intermediarias (logo antes do insert).');

$cadView = src('application/views/adm/usuarios/new/cadastro.php');
assertSrc(strpos($cadView, 'Deixe em branco para o próprio usuário criar a senha') !== false, 'Formulario deve explicar a senha em branco.');
$edView = src('application/views/adm/usuarios/new/edicao.php');
assertSrc(strpos($edView, "flashdata('cadastro_aviso')") !== false && strpos($edView, "flashdata('cadastro_ok')") !== false, 'Edicao deve exibir flashes do cadastro.');

$atendView = src('application/views/adm/usuarios/new/atendimentos.php');
assertSrc(strpos($atendView, "flashdata('cadastro_aviso')") !== false && strpos($atendView, "flashdata('cadastro_ok')") !== false, 'Agenda deve exibir os avisos do cadastro (redirect do onboarding do prestador).');
assertSrc(strpos($cad, "set_flashdata('cadastro_aviso'") > strpos($cad, "\$this->db->insert('usuarios', \$dd)"), 'Aviso de cadastro sem acesso so pode ser gravado apos o insert.');

assertSrc(strpos($cad, 'WHERE login = ?') !== false, 'cadastrar() deve checar login duplicado.');
assertSrc(strpos(src('application/libraries/Email_acesso.php'), 'catch (Throwable') !== false, 'Email_acesso::enviar deve capturar Throwable.');
assertSrc(strpos($um, 'nivel BETWEEN 1 AND 4') !== false, 'Fallback por e-mail deve restringir aos niveis 1-4.');
assertSrc(strpos(corpoFuncao(src('application/controllers/Home.php'), 'public function salvar_senha('), 'sess_regenerate(true)') !== false, 'salvar_senha deve regenerar a sessao.');
assertSrc(strpos($um, 'sess_regenerate(true)') !== false, 'logar() deve regenerar a sessao.');
assertSrc(strpos(src('application/models/adm/Saas_model.php'), 'Esqueci minha senha') !== false, 'Erro de e-mail existente deve orientar a recuperar acesso.');
echo "OK\n";
