<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/lista_espera_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- catálogos
assertSameValue(array('' => 'Tanto faz', 'manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'), utec_le_turnos(), 'turnos');
assertSameValue(array('desistiu' => 'Desistiu', 'conseguiu_horario' => 'Conseguiu horário', 'outro' => 'Outro'), utec_le_motivos_saida(), 'motivos');
assertSameValue('Sáb', utec_le_dias_rotulos()[6], 'rotulo sabado');

// --- turno da hora (limites)
assertSameValue('manha', utec_le_turno_da_hora('00:00'), '00:00');
assertSameValue('manha', utec_le_turno_da_hora('11:59'), '11:59');
assertSameValue('tarde', utec_le_turno_da_hora('12:00'), '12:00');
assertSameValue('tarde', utec_le_turno_da_hora('17:59:00'), '17:59:00');
assertSameValue('noite', utec_le_turno_da_hora('18:00'), '18:00');
assertSameValue('', utec_le_turno_da_hora('25:00'), 'hora invalida');
assertSameValue('', utec_le_turno_da_hora(''), 'hora vazia');

// --- dias
assertSameValue('1,3,5', utec_le_dias_normalizar(array('5', '1', '3', '3')), 'dias ordenados sem repeticao');
assertSameValue('0,6', utec_le_dias_normalizar('6,0,7,x'), 'csv descarta invalidos');
assertSameValue('', utec_le_dias_normalizar(array()), 'dias vazio');
assertSameValue('', utec_le_dias_normalizar(null), 'dias null');

// --- data
assertSameValue(true, utec_le_data_valida('2026-10-15'), 'data ok');
assertSameValue(false, utec_le_data_valida('2026-02-30'), 'data inexistente');
assertSameValue(false, utec_le_data_valida('15/10/2026'), 'formato BR');

// --- normalizar
$n = utec_le_normalizar(array('id_paciente' => '45', 'id_prestador' => '0', 'turno' => 'tarde', 'dias_semana' => array('2', '4'),
    'a_partir_de' => '2026-10-15', 'observacao' => "  prefere   \n depois das 15h  "));
assertSameValue(array(), $n['erros'], 'sem erros');
assertSameValue(array('id_paciente' => 45, 'id_prestador' => null, 'turno' => 'tarde', 'dias_semana' => '2,4',
    'a_partir_de' => '2026-10-15', 'observacao' => 'prefere depois das 15h'), $n['dados'], 'dados normalizados');
$n = utec_le_normalizar(array('id_paciente' => '', 'id_prestador' => '7', 'turno' => 'madrugada', 'a_partir_de' => '31/12', 'observacao' => ''));
assertSameValue(array('Selecione o paciente.'), $n['erros'], 'paciente obrigatorio');
assertSameValue(7, $n['dados']['id_prestador'], 'prestador int');
assertSameValue('', $n['dados']['turno'], 'turno invalido vira tanto faz');
assertSameValue(null, $n['dados']['a_partir_de'], 'data invalida vira null');
assertSameValue(null, $n['dados']['observacao'], 'obs vazia vira null');
$n = utec_le_normalizar(array('id_paciente' => '1', 'observacao' => str_repeat('á', 600)));
assertSameValue(500, mb_strlen($n['dados']['observacao'], 'UTF-8'), 'obs cortada em 500');

// --- compatibilidade (2026-10-13 é terça, w = 2)
$vaga = array('id_prestador' => 9, 'data_agenda' => '2026-10-13', 'hora_agenda' => '14:00:00');
$base = array('id_prestador' => null, 'turno' => '', 'dias_semana' => '', 'a_partir_de' => null);
assertSameValue(true, utec_le_compativel($base, $vaga), 'sem preferencias casa');
assertSameValue(true, utec_le_compativel(array_merge($base, array('id_prestador' => 9)), $vaga), 'mesmo prestador');
assertSameValue(false, utec_le_compativel(array_merge($base, array('id_prestador' => 8)), $vaga), 'outro prestador');
assertSameValue(true, utec_le_compativel(array_merge($base, array('turno' => 'tarde')), $vaga), 'turno tarde');
assertSameValue(false, utec_le_compativel(array_merge($base, array('turno' => 'manha')), $vaga), 'turno manha');
assertSameValue(true, utec_le_compativel(array_merge($base, array('dias_semana' => '1,2')), $vaga), 'terca marcada');
assertSameValue(false, utec_le_compativel(array_merge($base, array('dias_semana' => '1,3')), $vaga), 'terca nao marcada');
assertSameValue(true, utec_le_compativel(array_merge($base, array('a_partir_de' => '2026-10-13')), $vaga), 'a partir do proprio dia');
assertSameValue(false, utec_le_compativel(array_merge($base, array('a_partir_de' => '2026-10-14')), $vaga), 'a partir de depois');
assertSameValue(true, utec_le_compativel((object)array_merge($base, array('id_prestador' => '9', 'turno' => 'tarde', 'dias_semana' => '2')), (object)$vaga), 'objetos e strings');
assertSameValue(false, utec_le_compativel($base, array('id_prestador' => 9, 'data_agenda' => 'lixo', 'hora_agenda' => '14:00')), 'vaga invalida');

// --- ordenar: compatíveis primeiro, depois mais antigos
$e1 = array('id' => 1, 'criado_em' => '2026-10-01 10:00:00', 'id_prestador' => null, 'turno' => 'manha', 'dias_semana' => '', 'a_partir_de' => null);
$e2 = array('id' => 2, 'criado_em' => '2026-10-03 10:00:00', 'id_prestador' => null, 'turno' => '', 'dias_semana' => '', 'a_partir_de' => null);
$e3 = array('id' => 3, 'criado_em' => '2026-10-02 10:00:00', 'id_prestador' => 9, 'turno' => 'tarde', 'dias_semana' => '', 'a_partir_de' => null);
$ord = utec_le_ordenar(array($e1, $e2, $e3), $vaga);
assertSameValue(array(3, 2, 1), array_map(function ($i) { return $i['entrada']['id']; }, $ord), 'ordem');
assertSameValue(array(true, true, false), array_map(function ($i) { return $i['compativel']; }, $ord), 'flags');
assertSameValue(array(), utec_le_ordenar(array(), $vaga), 'lista vazia');

// --- deve avisar
assertSameValue(true, utec_le_deve_avisar($vaga, '2026-10-12 09:00:00', 2), 'futura com fila');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-13 14:00:00', 2), 'exatamente agora nao');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-13 15:00:00', 2), 'passada');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-12 09:00:00', 0), 'sem fila');
assertSameValue(false, utec_le_deve_avisar(array('data_agenda' => '2026-10-13', 'hora_agenda' => 'xx'), '2026-10-12 09:00:00', 2), 'hora invalida');

// --- dias de espera
assertSameValue(6, utec_le_dias_espera('2026-10-01 18:30:00', '2026-10-07'), 'dias de espera');
assertSameValue(0, utec_le_dias_espera('2026-10-07 08:00:00', '2026-10-07'), 'hoje');
assertSameValue(0, utec_le_dias_espera('', '2026-10-07'), 'sem data');

// --- resumo
assertSameValue('Sem preferência', utec_le_resumo_preferencias($base), 'resumo vazio');
assertSameValue('Tarde · Seg, Ter · a partir de 15/10', utec_le_resumo_preferencias(array('turno' => 'tarde', 'dias_semana' => '1,2', 'a_partir_de' => '2026-10-15')), 'resumo completo');

// --- mensagem
assertSameValue('Abriu vaga com Dr. Carlos em 13/10 às 14:00. 3 pacientes na lista de espera.', utec_le_mensagem_aviso('Dr. Carlos', '2026-10-13', '14:00:00', 3), 'mensagem plural');
assertSameValue('Abriu vaga em 13/10 às 14:00. 1 paciente na lista de espera.', utec_le_mensagem_aviso('', '2026-10-13', '14:00', 1), 'mensagem singular sem nome');

echo "OK\n";
