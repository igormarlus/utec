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

echo "OK\n";
