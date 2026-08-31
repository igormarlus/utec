<?php

$controller = file_get_contents(__DIR__ . '/../application/controllers/Webhooks.php');

function assertWebhookController($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

assertWebhookController(
    strpos($controller, "log_message('warning'") === false,
    'O controller nao deve usar o nivel warning, que nao existe no CodeIgniter 3.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Confirmacao atualizada.') !== false,
    'O controller deve registrar a confirmacao atualizada.'
);

echo "OK\n";
