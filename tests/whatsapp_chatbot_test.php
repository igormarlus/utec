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

$evento = utec_whatsapp_extrair_evento_webhook([
    'entry' => [[
        'changes' => [[
            'value' => ['messages' => [[
                'id' => 'wamid.inbound.1',
                'from' => '5581988887777',
                'type' => 'text',
                'text' => ['body' => 'Oi'],
            ]]],
        ]],
    ]],
]);
assertSameValue('text', $evento['message_type'], 'Deve reconhecer texto.');
assertSameValue('Oi', $evento['text'], 'Deve preservar o texto.');
assertSameValue('wamid.inbound.1', $evento['message_id'], 'Deve preservar id recebido.');
assertSameValue('5581988887777', $evento['from'], 'Deve preservar remetente.');

$lista = utec_whatsapp_payload_lista('5581988887777', 'Menu', 'Escolha uma opcao.', 'Ver opcoes', [[
    'title' => 'Atendimento',
    'rows' => [['id' => 'chat:paciente:proximas', 'title' => 'Proximas consultas', 'description' => 'Veja seus horarios']],
]]);
assertSameValue('interactive', $lista['type'], 'Menu deve ser interativo.');
assertSameValue('list', $lista['interactive']['type'], 'Menu deve ser lista.');

$botoes = utec_whatsapp_payload_botoes('5581988887777', 'Consulta', 'Escolha uma opcao.', [
    ['id' => 'confirmar_agendamento:77', 'title' => 'Confirmar'],
    ['id' => 'cancelar_agendamento:77', 'title' => 'Cancelar'],
]);
assertSameValue('cancelar_agendamento:77', $botoes['interactive']['action']['buttons'][1]['reply']['id'], 'Botao deve manter payload legado.');

$legado = utec_whatsapp_extrair_evento_webhook([
    'entry' => [[
        'changes' => [[
            'value' => ['messages' => [[
                'interactive' => ['button_reply' => ['id' => 'cancelar_agendamento:492']],
            ]]],
        ]],
    ]],
]);
assertSameValue('cancelar', $legado['action'], 'Payload legado de cancelamento deve manter a acao.');
assertSameValue(492, $legado['id_agendamento'], 'Payload legado de cancelamento deve manter o agendamento.');

echo "OK\n";
