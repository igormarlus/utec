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
    strpos($controller, "load->library('whatsapp_chatbot')") !== false,
    'O controller deve carregar a biblioteca do chatbot.'
);
assertWebhookController(
    strpos($controller, 'whatsapp_chatbot->processar($evento)') !== false,
    'O controller deve encaminhar mensagens novas ao chatbot.'
);
assertWebhookController(
    preg_match('/processar_resposta_agendamento\(\$evento\);\s*continue;/', $controller) === 1,
    'Acoes legadas devem encerrar a iteracao antes do chatbot.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Resposta ao paciente enviada.') !== false,
    'O controller deve registrar o envio da resposta ao paciente.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Falha ao responder paciente.') !== false,
    'O controller deve registrar a falha ao responder o paciente.'
);
assertWebhookController(
    preg_match('/Status sem notificacao correspondente\. wamid=.*status=.*erro=/', $controller) === 1,
    'O status sem notificacao deve registrar o status e o detalhe retornados pela Meta.'
);
assertWebhookController(
    strpos($controller, '[whatsapp_webhook] Mensagem recebida.') !== false
    && strpos($controller, '[whatsapp_webhook] Chatbot processado.') !== false,
    'O controller deve registrar o encaminhamento e o resultado do chatbot sem expor o texto recebido.'
);
assertWebhookController(
    strpos($controller, 'perfil_status=') !== false,
    'O log do chatbot deve registrar o diagnostico da identificacao de perfil.'
);

echo "OK\n";
