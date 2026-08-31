<?php

$bridge = file_get_contents(__DIR__ . '/../webhooks/whatsapp/index.php');
$expected = '$root = dirname(__DIR__, 2);';

if (strpos($bridge, $expected) === false) {
    fwrite(STDERR, 'Bridge deve trocar para a raiz antes de carregar o CodeIgniter.' . PHP_EOL);
    exit(1);
}

if (strpos($bridge, "\$_SERVER['SCRIPT_NAME'] = '/index.php';") === false) {
    fwrite(STDERR, 'Bridge deve apontar SCRIPT_NAME para o front controller da raiz.' . PHP_EOL);
    exit(1);
}

echo "OK\n";
