<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/disponibilidade_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- conversões
assertSameValue(480, utec_disp_min('08:00'), 'min 08:00');
assertSameValue(555, utec_disp_min('09:15:00'), 'min com segundos');
assertSameValue(1440, utec_disp_min('24:00'), 'min 24:00');
assertSameValue(null, utec_disp_min('8h'), 'min invalido');
assertSameValue(null, utec_disp_min('25:00'), 'min hora > 24');
assertSameValue('09:05', utec_disp_hhmm(545), 'hhmm');
assertSameValue(true, utec_disp_data_valida('2026-09-22'), 'data valida');
assertSameValue(false, utec_disp_data_valida('2026-02-31'), 'data inexistente');
assertSameValue(true, in_array(50, utec_disp_duracoes_permitidas(), true), 'duracao 50 permitida');
assertSameValue('Domingo', utec_disp_nomes_dias()[0], 'dia 0 = domingo');

// --- geração de slots
assertSameValue(array('08:00', '08:30', '09:00', '09:30'),
    utec_disp_gerar_slots(array(array('08:00', '10:00')), 30), 'slots simples');
assertSameValue(array('08:00', '08:50', '09:40', '10:30'),
    utec_disp_gerar_slots(array(array('08:00', '12:00')), 50), 'slot parcial descartado');
assertSameValue(array('08:00', '08:30', '14:00', '14:30'),
    utec_disp_gerar_slots(array(array('14:00', '15:00'), array('08:00', '09:00')), 30), 'multiplos intervalos ordenados');
assertSameValue(array(), utec_disp_gerar_slots(array(array('10:00', '09:00')), 30), 'intervalo invertido ignorado');
assertSameValue(array(), utec_disp_gerar_slots(array(array('08:00', '10:00')), 0), 'duracao zero');

// --- remoção de ocupados
$slots = array('08:00', '08:30', '09:00', '09:30');
assertSameValue(array('09:00', '09:30'),
    utec_disp_remover_ocupados($slots, array('08:15'), array(), 30), 'agendamento fora do passo remove 2 slots');
assertSameValue(array('08:00', '08:30', '09:30'),
    utec_disp_remover_ocupados($slots, array('09:00:00'), array(), 30), 'agendamento com segundos');
assertSameValue(array('08:00', '08:30'),
    utec_disp_remover_ocupados($slots, array(), array(array('09:00', '10:00')), 30), 'bloqueio parcial');
assertSameValue(array(),
    utec_disp_remover_ocupados($slots, array(), array(array('00:00', '24:00')), 30), 'bloqueio dia inteiro');

// --- recorte de bloqueios
$ferias = array(array('inicio' => '2026-09-21 18:00:00', 'fim' => '2026-09-23 10:00:00'));
assertSameValue(array(array('18:00', '24:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-21'), 'recorte dia 1');
assertSameValue(array(array('00:00', '24:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-22'), 'recorte dia do meio');
assertSameValue(array(array('00:00', '10:00')), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-23'), 'recorte ultimo dia');
assertSameValue(array(), utec_disp_recortar_bloqueios_no_dia($ferias, '2026-09-24'), 'recorte fora');

// --- normalização de bloqueio
$b = utec_disp_normalizar_bloqueio('2026-09-22', '', '', '', true);
assertSameValue(true, $b['ok'], 'dia inteiro ok');
assertSameValue('2026-09-22 00:00:00', $b['inicio'], 'dia inteiro inicio');
assertSameValue('2026-09-22 23:59:59', $b['fim'], 'dia inteiro fim');
assertSameValue(array(array('00:00', '24:00')),
    utec_disp_recortar_bloqueios_no_dia(array($b), '2026-09-22'), 'dia inteiro cobre o dia todo');
$b = utec_disp_normalizar_bloqueio('2026-09-22', '14:00', '2026-09-22', '18:00', false);
assertSameValue('2026-09-22 14:00:00', $b['inicio'], 'bloqueio parcial inicio');
assertSameValue('2026-09-22 18:00:00', $b['fim'], 'bloqueio parcial fim');
$b = utec_disp_normalizar_bloqueio('2026-09-22', '18:00', '2026-09-22', '14:00', false);
assertSameValue(false, $b['ok'], 'bloqueio invertido rejeitado');
$b = utec_disp_normalizar_bloqueio('2026-02-31', '', '', '', true);
assertSameValue(false, $b['ok'], 'data invalida rejeitada');

// --- classificação
$grade = array(array('08:00', '12:00'));
assertSameValue('fora_da_grade', utec_disp_classificar_horario('07:30', $grade, array(), array(), 30), 'antes da grade');
assertSameValue('fora_da_grade', utec_disp_classificar_horario('11:45', $grade, array(), array(), 30), 'nao cabe inteiro');
assertSameValue('ocupado', utec_disp_classificar_horario('08:00', $grade, array('08:00'), array(), 30), 'ocupado');
assertSameValue('bloqueado', utec_disp_classificar_horario('10:00', $grade, array(), array(array('09:30', '10:30')), 30), 'bloqueado');
assertSameValue('bloqueado', utec_disp_classificar_horario('13:00', $grade, array(), array(array('00:00', '24:00')), 30), 'bloqueado tem precedencia');
assertSameValue('livre', utec_disp_classificar_horario('10:00', $grade, array('08:00'), array(), 30), 'livre');
assertSameValue('livre', utec_disp_classificar_horario('10:15', $grade, array(), array(), 30), 'livre fora do passo');

// --- filtro de horário atual
assertSameValue(array('09:00'), utec_disp_filtrar_apos(array('08:00', '08:30', '09:00'), '08:30'), 'filtrar apos');

// --- validação de intervalos
assertSameValue('', utec_disp_validar_intervalos(array(array('08:00', '12:00'), array('12:00', '13:00'))), 'intervalos encostados ok');
assertSameValue(true, utec_disp_validar_intervalos(array(array('08:00', '12:00'), array('11:00', '13:00'))) !== '', 'sobreposicao rejeitada');
assertSameValue(true, utec_disp_validar_intervalos(array(array('12:00', '08:00'))) !== '', 'invertido rejeitado');
assertSameValue(true, utec_disp_validar_intervalos(array(array('8h', '9h'))) !== '', 'formato rejeitado');
assertSameValue('', utec_disp_validar_intervalos(array()), 'dia vazio ok');

echo "OK disponibilidade_helper_test\n";
