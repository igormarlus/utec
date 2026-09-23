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

// --- utec_whatsapp_template_equipe_nome ---
assertSameValue('agendamento_confirmado_equipe', utec_whatsapp_template_equipe_nome('confirmar'), 'Confirmar deve mapear para o template de confirmado.');
assertSameValue('agendamento_cancelado_equipe', utec_whatsapp_template_equipe_nome('cancelar'), 'Cancelar deve mapear para o template de cancelado.');
assertSameValue('agendamento_confirmado_equipe', utec_whatsapp_template_equipe_nome('CONFIRMAR'), 'A comparacao deve ignorar caixa.');
assertSameValue('', utec_whatsapp_template_equipe_nome('outra_coisa'), 'Acao desconhecida nao deve mapear para template nenhum.');
assertSameValue('', utec_whatsapp_template_equipe_nome(''), 'String vazia nao deve mapear para template nenhum.');

// --- utec_whatsapp_componentes_equipe_template ---
$contexto = [
    'paciente_nome' => 'Maria Silva',
    'prestador_nome' => 'Dr. Joao Pereira',
    'data_agenda' => '2026-09-25',
    'hora_agenda' => '14:30:00',
];
$componentes = utec_whatsapp_componentes_equipe_template($contexto);

assertSameValue(1, count($componentes), 'So deve existir o componente body (sem header/button).');
assertSameValue('body', $componentes[0]['type'], 'O unico componente deve ser do tipo body.');
assertSameValue(3, count($componentes[0]['parameters']), 'O corpo deve ter exatamente 3 parametros.');
assertSameValue('Maria Silva', $componentes[0]['parameters'][0]['text'], 'Parametro 1 e o nome do paciente.');
assertSameValue('Dr. Joao Pereira', $componentes[0]['parameters'][1]['text'], 'Parametro 2 e o nome do profissional.');
assertSameValue('25/09/2026 as 14:30', $componentes[0]['parameters'][2]['text'], 'Parametro 3 combina data e hora formatadas em pt-BR.');

// --- valores ausentes usam fallback, sem notice/erro ---
$componentesVazio = utec_whatsapp_componentes_equipe_template([]);
assertSameValue('Paciente', $componentesVazio[0]['parameters'][0]['text'], 'Sem nome do paciente, usa o fallback Paciente.');
assertSameValue('Profissional', $componentesVazio[0]['parameters'][1]['text'], 'Sem nome do profissional, usa o fallback Profissional.');

// --- confirmar usa o corpo de 5 parametros (agendamento_confirmado_equipe ainda
// aprovado na Meta com a versao antiga do template) ---
$contextoConfirmar = [
    'paciente_nome' => 'Maria Silva',
    'prestador_nome' => 'Dr. Joao Pereira',
    'tipo' => 'Consulta de retorno',
    'data_agenda' => '2026-09-25',
    'hora_agenda' => '14:30:00',
];
$componentesConfirmar = utec_whatsapp_componentes_equipe_template($contextoConfirmar, 'confirmar');
assertSameValue(1, count($componentesConfirmar), 'Confirmar tambem deve ter so o componente body.');
assertSameValue(5, count($componentesConfirmar[0]['parameters']), 'Confirmar deve ter exatamente 5 parametros (template antigo aprovado).');
assertSameValue('Maria Silva', $componentesConfirmar[0]['parameters'][0]['text'], 'Confirmar parametro 1 e o paciente.');
assertSameValue('Consulta de retorno', $componentesConfirmar[0]['parameters'][1]['text'], 'Confirmar parametro 2 e o tipo.');
assertSameValue('25/09/2026', $componentesConfirmar[0]['parameters'][2]['text'], 'Confirmar parametro 3 e a data.');
assertSameValue('14:30', $componentesConfirmar[0]['parameters'][3]['text'], 'Confirmar parametro 4 e a hora.');
assertSameValue('Dr. Joao Pereira', $componentesConfirmar[0]['parameters'][4]['text'], 'Confirmar parametro 5 e o profissional.');

// --- cancelar (explicito) continua com 3 parametros ---
$componentesCancelar = utec_whatsapp_componentes_equipe_template($contexto, 'cancelar');
assertSameValue(3, count($componentesCancelar[0]['parameters']), 'Cancelar continua com 3 parametros.');

echo "OK\n";
