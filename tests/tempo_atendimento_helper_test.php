<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/tempo_atendimento_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

$agora = '2026-10-05 14:30:00';
$zero = array('chegada_em' => null, 'chegada_por' => null, 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);

// --- transições
assertSameValue(array('inicio_atendimento_em' => $agora, 'fim_atendimento_em' => null), utec_tempo_campos_transicao(0, 1, $agora), '0->1');
assertSameValue(array('fim_atendimento_em' => $agora), utec_tempo_campos_transicao('1', 2, $agora), '1->2');
assertSameValue(array('inicio_atendimento_em' => null, 'fim_atendimento_em' => null), utec_tempo_campos_transicao(2, 0, $agora), '2->0');
assertSameValue($zero, utec_tempo_campos_transicao(3, 0, $agora), '3->0');
assertSameValue(array(), utec_tempo_campos_transicao(0, 3, $agora), 'cancelar nao mexe');
assertSameValue(array(), utec_tempo_campos_transicao(1, 0, $agora), 'transicao invalida');
assertSameValue($zero, utec_tempo_campos_zerados(), 'zerados');

// --- timestamps e minutos
assertSameValue(null, utec_tempo_ts(''), 'ts vazio');
assertSameValue(null, utec_tempo_ts('0000-00-00 00:00:00'), 'ts zero');
assertSameValue(null, utec_tempo_ts('lixo'), 'ts invalido');
assertSameValue(18, utec_tempo_minutos_entre('2026-10-05 14:00:00', '2026-10-05 14:18:59'), 'trunca 18m59s');
assertSameValue(-1, utec_tempo_minutos_entre('2026-10-05 14:01:30', '2026-10-05 14:00:00'), 'negativo trunca para zero');
assertSameValue(null, utec_tempo_minutos_entre(null, '2026-10-05 14:00:00'), 'ausente');

// --- métricas
$ag = array('data_agenda' => '2026-10-05', 'hora_agenda' => '14:00:00', 'chegada_em' => '2026-10-05 13:50:00',
    'inicio_atendimento_em' => '2026-10-05 14:08:00', 'fim_atendimento_em' => '2026-10-05 14:40:00');
assertSameValue(array('espera' => 18, 'atraso' => 8, 'duracao' => 32), utec_tempo_metricas($ag), 'metricas completas');
assertSameValue(array('espera' => 18, 'atraso' => 8, 'duracao' => 32), utec_tempo_metricas((object)$ag), 'aceita objeto');
$adiantado = $ag; $adiantado['inicio_atendimento_em'] = '2026-10-05 13:55:00'; $adiantado['chegada_em'] = '2026-10-05 13:40:00';
assertSameValue(-5, utec_tempo_metricas($adiantado)['atraso'], 'atraso negativo');
$sem = array('data_agenda' => '2026-10-05', 'hora_agenda' => '14:00', 'chegada_em' => '', 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);
assertSameValue(array('espera' => null, 'atraso' => null, 'duracao' => null), utec_tempo_metricas($sem), 'sem horarios');
$esquecido = $ag; $esquecido['fim_atendimento_em'] = '2026-10-06 09:00:00';
assertSameValue(null, utec_tempo_metricas($esquecido)['duracao'], 'duracao > 480 ignorada');
$invertido = $ag; $invertido['chegada_em'] = '2026-10-05 14:20:00';
assertSameValue(null, utec_tempo_metricas($invertido)['espera'], 'espera negativa ignorada');
$sem_hora = $ag; $sem_hora['hora_agenda'] = '';
assertSameValue(null, utec_tempo_metricas($sem_hora)['atraso'], 'sem hora marcada');

// --- formatação e resumo
assertSameValue('18 min', utec_tempo_formatar(18), 'fmt min');
assertSameValue('1 h 05 min', utec_tempo_formatar(65), 'fmt hora');
assertSameValue('-5 min', utec_tempo_formatar(-5), 'fmt negativo');
assertSameValue('0 min', utec_tempo_formatar(0), 'fmt zero');
assertSameValue('', utec_tempo_formatar(null), 'fmt null');
assertSameValue('Chegou 13:50 · esperou 18 min · consulta 32 min', utec_tempo_resumo_agenda($ag), 'resumo completo');
$so_chegada = $sem; $so_chegada['chegada_em'] = '2026-10-05 13:50:00';
assertSameValue('Chegou 13:50', utec_tempo_resumo_agenda($so_chegada), 'resumo so chegada');
assertSameValue('', utec_tempo_resumo_agenda($sem), 'resumo vazio');

// --- elegibilidade
$pend = array('status' => '0', 'data_agenda' => '2026-10-05', 'chegada_em' => null, 'inicio_atendimento_em' => null);
assertTrue(utec_tempo_pode_checkin($pend, '2026-10-05'), 'pode checkin');
assertTrue(!utec_tempo_pode_checkin($pend, '2026-10-06'), 'outro dia');
$p2 = $pend; $p2['status'] = '1';
assertTrue(!utec_tempo_pode_checkin($p2, '2026-10-05'), 'ja em atendimento');
$p3 = $pend; $p3['chegada_em'] = '2026-10-05 13:50:00';
assertTrue(!utec_tempo_pode_checkin($p3, '2026-10-05'), 'ja chegou');
assertTrue(utec_tempo_pode_desfazer_checkin($p3), 'pode desfazer');
$p4 = $p3; $p4['inicio_atendimento_em'] = '2026-10-05 14:00:00';
assertTrue(!utec_tempo_pode_desfazer_checkin($p4), 'nao desfaz apos inicio');
assertTrue(!utec_tempo_pode_desfazer_checkin($pend), 'nao desfaz sem chegada');

// --- SQL
$sql = utec_tempo_sql_agregados('a');
assertTrue(strpos($sql, 'TIMESTAMPDIFF(MINUTE, a.chegada_em, a.inicio_atendimento_em)') !== false, 'sql espera');
assertTrue(strpos($sql, "CONCAT(a.data_agenda, ' ', a.hora_agenda)") !== false, 'sql atraso');
assertTrue(strpos($sql, 'BETWEEN -480 AND 480') !== false, 'sql limite atraso');
foreach (array('espera_media', 'espera_n', 'atraso_medio', 'atraso_n', 'duracao_media', 'duracao_n') as $c) {
    assertTrue(strpos($sql, 'AS ' . $c) !== false, 'sql coluna ' . $c);
}
assertTrue(strpos(utec_tempo_sql_agregados('a; DROP'), 'DROP') === false, 'alias sanitizado');

echo "OK\n";
