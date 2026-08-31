<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$payload = [
    'entry' => [[
        'changes' => [[
            'value' => [
                'messages' => [[
                    'context' => ['id' => 'wamid.HBgMTESTE123'],
                    'interactive' => [
                        'button_reply' => [
                            'id' => 'cancelar_agendamento:492',
                            'title' => 'Cancelar',
                        ],
                    ],
                ]],
            ],
        ]],
    ]],
];

$evento = utec_whatsapp_extrair_evento_webhook($payload);
assertSameValue('cancelar', $evento['action'], 'Deve identificar acao cancelar.');
assertSameValue(492, $evento['id_agendamento'], 'Deve identificar o id do agendamento.');
assertSameValue('wamid.HBgMTESTE123', $evento['wamid'], 'Deve identificar o wamid de contexto.');
assertSameValue('cancelar_agendamento:492', $evento['payload'], 'Deve manter o payload original.');

$payloadBotaoTemplate = [
    'entry' => [[
        'changes' => [[
            'value' => [
                'messages' => [[
                    'context' => ['id' => 'wamid.HBgMTESTE789'],
                    'type' => 'button',
                    'button' => [
                        'payload' => 'confirmar_agendamento:493',
                        'text' => 'Confirmar',
                    ],
                ]],
            ],
        ]],
    ]],
];

$eventoBotaoTemplate = utec_whatsapp_extrair_evento_webhook($payloadBotaoTemplate);
assertSameValue('confirmar', $eventoBotaoTemplate['action'], 'Deve identificar a confirmacao de botao rapido de template.');
assertSameValue(493, $eventoBotaoTemplate['id_agendamento'], 'Deve identificar o agendamento do botao rapido de template.');
assertSameValue('wamid.HBgMTESTE789', $eventoBotaoTemplate['wamid'], 'Deve manter o contexto do botao rapido de template.');

$desconhecido = utec_whatsapp_extrair_evento_webhook(['entry' => []]);
assertSameValue('', $desconhecido['action'], 'Payload sem mensagem interativa deve voltar vazio.');

$payloadStatus = [
    'entry' => [[
        'changes' => [[
            'value' => [
                'statuses' => [[
                    'id' => 'wamid.HBgMTESTE123',
                    'status' => 'failed',
                    'errors' => [[
                        'code' => 131026,
                        'title' => 'Message Undeliverable',
                    ]],
                ]],
            ],
        ]],
    ]],
];

$status = utec_whatsapp_extrair_evento_webhook($payloadStatus);
assertSameValue('failed', $status['delivery_status'], 'Deve identificar o status de entrega da Meta.');
assertSameValue('wamid.HBgMTESTE123', $status['wamid'], 'Status deve identificar o wamid da mensagem enviada.');
assertSameValue('131026: Message Undeliverable', $status['error_detail'], 'Status failed deve manter o erro da Meta.');

assertSameValue('entregue', utec_whatsapp_status_envio_meta('delivered'), 'Status delivered deve ser salvo como entregue.');
assertSameValue('erro', utec_whatsapp_status_envio_meta('failed'), 'Status failed deve ser salvo como erro.');
assertSameValue(true, utec_whatsapp_envio_consume_quota('erro', 'wamid.HBgMTESTE123'), 'Falha posterior da Meta nao pode devolver quota consumida.');
assertSameValue(false, utec_whatsapp_envio_consume_quota('erro', ''), 'Erro sincrono sem wamid nao pode consumir quota.');

$lote = $payloadStatus;
$lote['entry'][0]['changes'][0]['value']['statuses'][] = [
    'id' => 'wamid.HBgMTESTE456',
    'status' => 'delivered',
    'timestamp' => '1788192000',
];
$eventos = utec_whatsapp_extrair_eventos_webhook($lote);
assertSameValue(2, count($eventos), 'Deve extrair todos os status recebidos no mesmo POST.');
assertSameValue('wamid.HBgMTESTE456', $eventos[1]['wamid'], 'Segundo status deve manter seu wamid.');
assertSameValue('2026-08-31 16:00:00', $eventos[1]['event_at'], 'Timestamp Meta deve ser convertido para data do banco.');

echo "OK\n";
