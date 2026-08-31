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
assertWebhookController(
    strpos($controller, 'registrar_resposta_webhook(') !== false,
    'O controller deve usar a transicao idempotente registrar_resposta_webhook().'
);
assertWebhookController(
    strpos($controller, 'criar_resposta_agendamento(') !== false,
    'O controller deve criar os avisos internos apos a transicao.'
);
assertWebhookController(
    strpos($controller, 'responder_interacao(') !== false,
    'O controller deve disparar a resposta de texto ao paciente.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Resposta ao paciente enviada.') !== false,
    'O controller deve registrar o envio da resposta ao paciente.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Falha ao responder paciente.') !== false,
    'O controller deve registrar a falha ao responder o paciente.'
);

echo "OK\n";
