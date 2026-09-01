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

assertSameValue(
    ['lembrete_paciente', 'lembrete_profissional'],
    utec_whatsapp_lembrete_tipos(),
    'Os tipos de lembrete devem ser paciente e profissional.'
);

assertSameValue(true, utec_whatsapp_lembrete_tipo_valido('lembrete_paciente'), 'lembrete_paciente e valido.');
assertSameValue(true, utec_whatsapp_lembrete_tipo_valido('lembrete_profissional'), 'lembrete_profissional e valido.');
assertSameValue(false, utec_whatsapp_lembrete_tipo_valido('confirmacao'), 'confirmacao nao e um tipo de lembrete.');
assertSameValue(false, utec_whatsapp_lembrete_tipo_valido(''), 'String vazia nao e um tipo de lembrete.');

$base = mktime(8, 0, 0, 9, 1, 2026); // 2026-09-01 08:00:00
$intervalo = utec_whatsapp_lembrete_intervalo($base);
assertSameValue('2026-09-01 08:00:00', $intervalo['inicio'], 'Inicio da janela e o agora.');
assertSameValue('2026-09-01 15:00:00', $intervalo['fim'], 'Fim da janela e agora + 7 horas.');

$intervalo6 = utec_whatsapp_lembrete_intervalo($base, 6);
assertSameValue('2026-09-01 14:00:00', $intervalo6['fim'], 'O numero de horas da janela e configuravel.');

echo "OK\n";
