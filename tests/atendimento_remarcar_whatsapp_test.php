<?php

$controller = file_get_contents(__DIR__ . '/../application/controllers/adm/Atendimento.php');

function assertRemarcarController($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$inicio = strpos($controller, 'function remarcar_agenda()');
assertRemarcarController($inicio !== false, 'Controller deve ter a funcao remarcar_agenda().');

$fimBusca = strpos($controller, "\nfunction ", $inicio + 1);
$corpo = $fimBusca !== false ? substr($controller, $inicio, $fimBusca - $inicio) : substr($controller, $inicio);

assertRemarcarController(
    strpos($corpo, 'notificar_agendamento(') !== false,
    'remarcar_agenda() deve disparar a notificacao WhatsApp apos remarcar (regressao: reagendamento pelo calendario/agenda nao avisava o paciente).'
);

echo "OK\n";
