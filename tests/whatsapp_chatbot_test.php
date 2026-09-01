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
assertSameValue('Ver opcoes', $lista['interactive']['action']['button'], 'Menu deve manter o rotulo da acao.');
assertSameValue('Atendimento', $lista['interactive']['action']['sections'][0]['title'], 'Menu deve manter a secao.');
assertSameValue('chat:paciente:proximas', $lista['interactive']['action']['sections'][0]['rows'][0]['id'], 'Menu deve manter o id da linha.');
assertSameValue('Proximas consultas', $lista['interactive']['action']['sections'][0]['rows'][0]['title'], 'Menu deve manter o titulo da linha.');

$botoes = utec_whatsapp_payload_botoes('5581988887777', 'Consulta', 'Escolha uma opcao.', [
    ['id' => 'confirmar_agendamento:77', 'title' => 'Confirmar'],
    ['id' => 'cancelar_agendamento:77', 'title' => 'Cancelar'],
]);
assertSameValue('cancelar_agendamento:77', $botoes['interactive']['action']['buttons'][1]['reply']['id'], 'Botao deve manter payload legado.');
assertSameValue('confirmar_agendamento:77', $botoes['interactive']['action']['buttons'][0]['reply']['id'], 'Primeiro botao deve manter payload de confirmacao.');

$listaInvalida = utec_whatsapp_payload_lista('5581988887777', 'Menu', 'Escolha uma opcao.', 'Ver opcoes', [[
    'title' => 'Atendimento',
    'rows' => [['id' => '', 'title' => 'Sem id'], ['id' => 'chat:sem-titulo', 'title' => '']],
]]);
assertSameValue([], $listaInvalida, 'Lista sem linhas validas deve permitir fallback para texto simples.');

$botoesInvalidos = utec_whatsapp_payload_botoes('5581988887777', 'Consulta', 'Escolha uma opcao.', [
    ['id' => '', 'title' => 'Sem id'],
    ['id' => 'chat:sem-titulo', 'title' => ''],
]);
assertSameValue([], $botoesInvalidos, 'Botoes sem itens validos devem permitir fallback para texto simples.');

$caractereUtf8 = "\xC3\xA1";
assertSameValue(str_repeat($caractereUtf8, 60), utec_whatsapp_truncar_texto(str_repeat($caractereUtf8, 61), 60), 'Truncamento deve preservar caracteres UTF-8 completos.');

$listaUtf8 = utec_whatsapp_payload_lista(
    '5581988887777',
    str_repeat($caractereUtf8, 61),
    str_repeat($caractereUtf8, 1025),
    str_repeat($caractereUtf8, 21),
    [['rows' => [['id' => 'chat:utf8', 'title' => 'UTF-8']]]]
);
assertSameValue(str_repeat($caractereUtf8, 60), $listaUtf8['interactive']['header']['text'], 'Titulo da lista deve respeitar UTF-8.');
assertSameValue(str_repeat($caractereUtf8, 1024), $listaUtf8['interactive']['body']['text'], 'Corpo da lista deve respeitar UTF-8.');
assertSameValue(str_repeat($caractereUtf8, 20), $listaUtf8['interactive']['action']['button'], 'Rotulo da lista deve respeitar UTF-8.');

$botoesUtf8 = utec_whatsapp_payload_botoes('5581988887777', 'Consulta', 'Escolha uma opcao.', [[
    'id' => 'chat:utf8',
    'title' => str_repeat($caractereUtf8, 21),
]]);
assertSameValue(str_repeat($caractereUtf8, 20), $botoesUtf8['interactive']['action']['buttons'][0]['reply']['title'], 'Rotulo de botao deve respeitar UTF-8.');

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
