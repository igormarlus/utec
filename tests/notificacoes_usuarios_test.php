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

assertSameValue([12], utec_notificacoes_destinatarios_agendamento(12, 12), 'Deve deduplicar criador e prestador iguais.');
assertSameValue([12, 25], utec_notificacoes_destinatarios_agendamento(12, 25), 'Deve manter a ordem de criador e prestador distintos.');
assertSameValue([25], utec_notificacoes_destinatarios_agendamento(0, 25), 'Deve ignorar destinatario sem ID positivo.');

echo "OK\n";
