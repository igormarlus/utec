<?php

$bridge = file_get_contents(__DIR__ . '/../webhooks/whatsapp/index.php');
$expected = "chdir(dirname(__DIR__, 2));";

if (strpos($bridge, $expected) === false) {
    fwrite(STDERR, 'Bridge deve trocar para a raiz antes de carregar o CodeIgniter.' . PHP_EOL);
    exit(1);
}

echo "OK\n";
