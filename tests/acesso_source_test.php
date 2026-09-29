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

echo "OK\n";
