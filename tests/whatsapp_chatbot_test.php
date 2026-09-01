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

assertSameValue('paciente', utec_whatsapp_perfil_por_nivel(5), 'Nivel 5 deve ser paciente no chatbot.');
assertSameValue('profissional', utec_whatsapp_perfil_por_nivel(3), 'Nivel 3 deve ser profissional no chatbot.');
assertSameValue('atendente', utec_whatsapp_perfil_por_nivel(4), 'Nivel 4 deve ser atendente no chatbot.');
assertSameValue('admin', utec_whatsapp_perfil_por_nivel(2), 'Nivel 2 deve ser admin no chatbot.');
assertSameValue('', utec_whatsapp_perfil_por_nivel(0), 'Nivel vazio nao deve receber perfil no chatbot.');
assertSameValue('', utec_whatsapp_perfil_por_nivel(1), 'Nivel fora do contrato nao deve receber perfil no chatbot.');

assertSameValue(true, utec_whatsapp_perfil_tem_plano('admin'), 'Admin deve poder consultar plano.');
assertSameValue(true, utec_whatsapp_perfil_tem_plano('profissional'), 'Profissional deve poder consultar plano.');
assertSameValue(false, utec_whatsapp_perfil_tem_plano('atendente'), 'Atendente nao deve poder consultar plano.');
assertSameValue(false, utec_whatsapp_perfil_tem_plano('paciente'), 'Paciente nao deve poder consultar plano.');
assertSameValue(false, utec_whatsapp_perfil_tem_plano(''), 'Perfil vazio nao deve poder consultar plano.');

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
assertSameValue('', utec_whatsapp_truncar_texto(chr(195), 1), 'Truncamento com mbstring deve rejeitar UTF-8 invalido.');

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

$secoesLimite = [];
for ($indice = 0; $indice < 11; $indice++) {
    $secoesLimite[] = [
        'title' => str_repeat('S', 25),
        'rows' => [[
            'id' => 'chat:linha:'.$indice,
            'title' => str_repeat('R', 25),
            'description' => str_repeat('D', 73),
        ]],
    ];
}
$listaLimitada = utec_whatsapp_payload_lista('5581988887777', 'Titulo', 'Corpo', 'Abrir', $secoesLimite);
assertSameValue(10, count($listaLimitada['interactive']['action']['sections']), 'Lista deve limitar secoes a dez.');
$totalLinhas = 0;
foreach ($listaLimitada['interactive']['action']['sections'] as $secaoLimitada) {
    $totalLinhas += count($secaoLimitada['rows']);
}
assertSameValue(10, $totalLinhas, 'Lista deve limitar linhas totais a dez.');
assertSameValue(str_repeat('S', 24), $listaLimitada['interactive']['action']['sections'][0]['title'], 'Titulo da secao deve limitar a 24 caracteres.');
assertSameValue(str_repeat('R', 24), $listaLimitada['interactive']['action']['sections'][0]['rows'][0]['title'], 'Titulo da linha deve limitar a 24 caracteres.');
assertSameValue(str_repeat('D', 72), $listaLimitada['interactive']['action']['sections'][0]['rows'][0]['description'], 'Descricao da linha deve limitar a 72 caracteres.');

$secoesAposLimite = [];
for ($indice = 0; $indice < 10; $indice++) {
    $secoesAposLimite[] = ['rows' => [['id' => '', 'title' => 'Invalida']]];
}
$secoesAposLimite[] = ['rows' => [['id' => 'chat:fora-do-limite', 'title' => 'Ignorar']]];
assertSameValue([], utec_whatsapp_payload_lista('5581988887777', 'Titulo', 'Corpo', 'Abrir', $secoesAposLimite), 'Lista deve ignorar secoes apos o limite de dez.');

assertSameValue([], utec_whatsapp_payload_lista('5581988887777', 'Titulo', '', 'Abrir', $secoesLimite), 'Lista sem corpo deve permitir fallback.');
assertSameValue([], utec_whatsapp_payload_lista('5581988887777', 'Titulo', 'Corpo', '', $secoesLimite), 'Lista sem botao deve permitir fallback.');
assertSameValue([], utec_whatsapp_payload_lista('5581988887777', 'Titulo', 'Corpo', 'Abrir', [[
    'rows' => [['id' => str_repeat('x', 201), 'title' => 'Muito longo']],
]]), 'Lista deve rejeitar id acima de 200 caracteres.');
assertSameValue([], utec_whatsapp_payload_botoes('5581988887777', 'Titulo', '', [['id' => 'chat:ok', 'title' => 'Ok']]), 'Botoes sem corpo devem permitir fallback.');
assertSameValue([], utec_whatsapp_payload_lista('5581988887777', 'Titulo', 'Corpo', 'Abrir', [[
    'rows' => [['id' => [], 'title' => 'Invalido']],
]]), 'Lista deve rejeitar id nao escalar.');
assertSameValue([], utec_whatsapp_payload_botoes('5581988887777', 'Titulo', 'Corpo', [[
    'id' => 'chat:invalido',
    'title' => [],
]]), 'Botoes devem rejeitar titulo nao escalar.');

$botaoId256 = str_repeat('b', 256);
$botoesId256 = utec_whatsapp_payload_botoes('5581988887777', 'Titulo', 'Corpo', [[
    'id' => $botaoId256,
    'title' => 'Valido',
]]);
assertSameValue($botaoId256, $botoesId256['interactive']['action']['buttons'][0]['reply']['id'], 'Botao deve aceitar reply.id com 256 bytes.');
assertSameValue([], utec_whatsapp_payload_botoes('5581988887777', 'Titulo', 'Corpo', [[
    'id' => str_repeat('b', 257),
    'title' => 'Invalido',
]]), 'Botao deve rejeitar reply.id acima de 256 bytes.');

$listaSemHeader = utec_whatsapp_payload_lista('5581988887777', '', 'Corpo', 'Abrir', [[
    'rows' => [['id' => 'chat:sem-header', 'title' => 'Opcao']],
]]);
assertSameValue('interactive', $listaSemHeader['type'], 'Lista valida sem titulo deve permanecer interativa.');
assertSameValue(false, isset($listaSemHeader['interactive']['header']), 'Lista sem titulo nao deve incluir header.');

$botoesSemHeader = utec_whatsapp_payload_botoes('5581988887777', '', 'Corpo', [[
    'id' => 'chat:sem-header',
    'title' => 'Opcao',
]]);
assertSameValue('interactive', $botoesSemHeader['type'], 'Botoes validos sem titulo devem permanecer interativos.');
assertSameValue(false, isset($botoesSemHeader['interactive']['header']), 'Botoes sem titulo nao devem incluir header.');

$warningsMeta = [];
set_error_handler(function ($severity, $message) use (&$warningsMeta) {
    $warningsMeta[] = $message;
    return true;
});
$eventoMalformado = utec_whatsapp_extrair_evento_webhook([
    'entry' => [[
        'changes' => [[
            'value' => ['messages' => [[
                'id' => [],
                'from' => [],
                'type' => [],
                'text' => ['body' => []],
                'context' => ['id' => []],
                'interactive' => [
                    'list_reply' => ['id' => []],
                    'button_reply' => ['id' => []],
                ],
                'button' => ['payload' => []],
            ]]],
        ]],
    ]],
]);
restore_error_handler();
assertSameValue([], $warningsMeta, 'Webhook malformado nao deve gerar warnings.');
assertSameValue('', $eventoMalformado['message_id'], 'Id recebido como array deve ser ignorado.');
assertSameValue('', $eventoMalformado['from'], 'Remetente recebido como array deve ser ignorado.');
assertSameValue('', $eventoMalformado['message_type'], 'Tipo recebido como array deve ser ignorado.');
assertSameValue('', $eventoMalformado['text'], 'Texto recebido como array deve ser ignorado.');
assertSameValue('', $eventoMalformado['wamid'], 'Contexto recebido como array deve ser ignorado.');
assertSameValue('', $eventoMalformado['payload'], 'Botoes recebidos como array devem ser ignorados.');
assertSameValue('', $eventoMalformado['action'], 'Evento malformado nao deve executar acao.');
assertSameValue(0, $eventoMalformado['id_agendamento'], 'Evento malformado nao deve apontar agendamento.');

$arquivoFallbackUnicode = tempnam(sys_get_temp_dir(), 'utec-whatsapp-');
$codigoFallbackUnicode = '<?php define("BASEPATH", 1); require '.var_export(realpath(__DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php'), true).'; echo utec_whatsapp_truncar_texto("\\xC3\\xA1\\xC3\\xA1", 1);';
file_put_contents($arquivoFallbackUnicode, $codigoFallbackUnicode);
$saidaFallbackUnicode = shell_exec(escapeshellarg(PHP_BINARY).' -n '.escapeshellarg($arquivoFallbackUnicode));
unlink($arquivoFallbackUnicode);
assertSameValue("\xC3\xA1", $saidaFallbackUnicode, 'Fallback sem mbstring deve preservar UTF-8.');

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
