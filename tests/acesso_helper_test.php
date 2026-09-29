<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/acesso_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) {
    if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); }
}

// --- validar senha
assertSameValue(array('ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres.'),
    utec_acesso_validar_senha('12345', '12345'), 'senha curta');
assertSameValue(array('ok' => false, 'msg' => 'As senhas não coincidem. Tente novamente.'),
    utec_acesso_validar_senha('123456', '123457'), 'senhas divergentes');
assertSameValue(array('ok' => true, 'msg' => ''), utec_acesso_validar_senha('123456', '123456'), 'senha ok');
assertSameValue(false, utec_acesso_validar_senha(null, null)['ok'], 'senha nula');

// --- token
$t1 = utec_acesso_gerar_token();
$t2 = utec_acesso_gerar_token();
assertTrue(preg_match('/^[a-f0-9]{64}$/', $t1) === 1, 'token 64 hex');
assertTrue($t1 !== $t2, 'tokens unicos');

// --- senha aleatoria
$s = utec_acesso_senha_aleatoria(16);
assertSameValue(16, strlen($s), 'senha aleatoria tamanho');
assertTrue(preg_match('/^[A-Za-z0-9]+$/', $s) === 1, 'senha aleatoria charset');
assertSameValue(12, strlen(utec_acesso_senha_aleatoria()), 'senha aleatoria default');

// --- email valido
assertSameValue(true, utec_acesso_email_valido(' ana@clinica.com.br '), 'email com espacos');
assertSameValue(false, utec_acesso_email_valido(''), 'email vazio');
assertSameValue(false, utec_acesso_email_valido(null), 'email nulo');
assertSameValue(false, utec_acesso_email_valido('ana@'), 'email invalido');

// --- throttle (validade 60, intervalo 2)
assertSameValue(true, utec_acesso_pode_reenviar(null), 'sem token');
assertSameValue(true, utec_acesso_pode_reenviar(-10), 'token expirado');
assertSameValue(false, utec_acesso_pode_reenviar(3600), 'token recem criado');
assertSameValue(false, utec_acesso_pode_reenviar(3481), 'token criado ha 1m59s');
assertSameValue(true, utec_acesso_pode_reenviar(3480), 'token criado ha 2min');
assertSameValue(true, utec_acesso_pode_reenviar(600), 'token antigo');
assertSameValue(true, utec_acesso_pode_reenviar(6 * 86400), 'convite de 7 dias nao bloqueia');

// --- e-mail boas-vindas
$bv = utec_acesso_email_boas_vindas(array(
    'nome' => 'Ana', 'login' => 'ana@clinica.com.br', 'senha' => 'Minha<Senha>1',
    'tenant_nome' => 'Clinica <b>Sol</b>', 'trial_fim' => '28/10/2026',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
));
assertTrue(strpos($bv['assunto'], 'Clinica <b>Sol</b>') !== false, 'assunto com nome da clinica (texto puro)');
assertTrue(strpos($bv['html'], 'ana@clinica.com.br') !== false, 'boas-vindas tem login');
assertTrue(strpos($bv['html'], 'Minha&lt;Senha&gt;1') !== false, 'boas-vindas tem senha escapada');
assertTrue(strpos($bv['html'], 'Clinica &lt;b&gt;Sol&lt;/b&gt;') !== false, 'boas-vindas escapa clinica');
assertTrue(strpos($bv['html'], '28/10/2026') !== false, 'boas-vindas tem fim do trial');
assertTrue(strpos($bv['html'], 'https://x/acesso/esqueci') !== false, 'boas-vindas tem link esqueci');
$bv2 = utec_acesso_email_boas_vindas(array(
    'nome' => 'Ana', 'login' => 'a@b.co', 'senha' => 'x', 'tenant_nome' => 'T', 'trial_fim' => '',
    'link_login' => 'L', 'link_esqueci' => 'E',
));
assertTrue(strpos($bv2['html'], 'Trial ativo') === false, 'sem trial nao mostra linha de trial');

// --- e-mail equipe (com senha)
$eq = utec_acesso_email_equipe(array(
    'nome' => 'Bia', 'login' => 'bia', 'senha' => 'abc123', 'link_definir' => '',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
    'cadastrado_por' => 'Clinica Sol', 'validade_dias' => 7,
));
assertTrue(strpos($eq['html'], 'abc123') !== false, 'equipe com senha mostra senha');
assertTrue(strpos($eq['html'], 'Clinica Sol') !== false, 'equipe mostra quem cadastrou');
assertTrue(strpos($eq['html'], 'Definir minha senha') === false, 'equipe com senha sem botao definir');

// --- e-mail equipe (convite)
$cv = utec_acesso_email_equipe(array(
    'nome' => 'Bia', 'login' => 'bia', 'senha' => '', 'link_definir' => 'https://x/acesso/senha/TOKEN',
    'link_login' => 'https://x/admin', 'link_esqueci' => 'https://x/acesso/esqueci',
    'cadastrado_por' => 'Clinica Sol', 'validade_dias' => 7,
));
assertTrue(strpos($cv['html'], 'https://x/acesso/senha/TOKEN') !== false, 'convite tem link definir');
assertTrue(strpos($cv['html'], 'Definir minha senha') !== false, 'convite tem botao definir');
assertTrue(strpos($cv['html'], '7 dias') !== false, 'convite informa validade');
assertTrue(strpos($cv['html'], 'Senha:') === false, 'convite nao mostra linha de senha');

// --- e-mail redefinicao
$rd = utec_acesso_email_redefinicao(array(
    'nome' => 'Ana', 'login' => 'ana@clinica.com.br', 'link' => 'https://x/acesso/senha/TOK', 'validade_min' => 60,
));
assertTrue(strpos($rd['html'], 'https://x/acesso/senha/TOK') !== false, 'redefinicao tem link');
assertTrue(strpos($rd['html'], 'background-color:#0f766e') !== false, 'redefinicao tem fallback solido p/ Outlook');
assertTrue(substr_count($rd['html'], 'https://x/acesso/senha/TOK') >= 2, 'redefinicao mostra o link como texto');
assertTrue(strpos($rd['html'], '1 hora') !== false, 'redefinicao informa validade');
assertTrue(strpos($rd['html'], 'Senha:') === false, 'redefinicao nunca mostra senha');
assertTrue(strpos($rd['html'], 'ana@clinica.com.br') !== false, 'redefinicao mostra login');

// --- e-mail interno
$in = utec_acesso_email_novo_cadastro_interno(array(
    'tenant_nome' => 'Clinica Sol', 'login' => 'ana@clinica.com.br', 'nome' => 'Ana',
    'origem' => 'trial', 'plano_nome' => 'Solo',
));
assertTrue(strpos($in['assunto'], 'Clinica Sol') !== false, 'interno assunto');
assertTrue(strpos($in['html'], 'Solo') !== false, 'interno plano');
assertTrue(stripos($in['html'], 'senha') === false, 'interno nunca fala de senha');

echo "OK\n";
