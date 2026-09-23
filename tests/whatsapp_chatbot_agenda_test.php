<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertAgendaSame($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- antecedencia
$agora = strtotime('2026-09-22 10:00:30');
assertAgendaSame(24, utec_whatsapp_agenda_antecedencia_horas(), 'antecedencia 24h');
assertAgendaSame('2026-09-23 10:01', utec_whatsapp_agenda_minimo_datetime($agora), 'minimo arredonda para cima');
assertAgendaSame('2026-09-23 10:00', utec_whatsapp_agenda_minimo_datetime(strtotime('2026-09-22 10:00:00')), 'minimo exato');
assertAgendaSame(true, utec_whatsapp_agenda_antecedencia_ok('2026-09-23', '10:00:00', strtotime('2026-09-22 10:00:00')), 'exatamente 24h ok');
assertAgendaSame(false, utec_whatsapp_agenda_antecedencia_ok('2026-09-23', '09:59', strtotime('2026-09-22 10:00:00')), '23h59 nao ok');
assertAgendaSame(false, utec_whatsapp_agenda_antecedencia_ok('', '', $agora), 'data vazia nao ok');

// --- ids
assertAgendaSame('rem:812:d:20260925', utec_whatsapp_agenda_id_dia(812, '2026-09-25'), 'id dia');
assertAgendaSame('rem:812:p:20260925:2', utec_whatsapp_agenda_id_pagina(812, '2026-09-25', 2), 'id pagina');
assertAgendaSame('rem:812:h:202609251430', utec_whatsapp_agenda_id_hora(812, '2026-09-25', '14:30:00'), 'id hora');
assertAgendaSame('rem:812:ok:202609251430', utec_whatsapp_agenda_id_confirmar_remarcacao(812, '2026-09-25', '14:30'), 'id confirmar remarcacao');
assertAgendaSame('rem:812:dias', utec_whatsapp_agenda_id_outros_dias(812), 'id outros dias');
assertAgendaSame('can:812:sem_motivo', utec_whatsapp_agenda_id_sem_motivo(812), 'id sem motivo');
assertAgendaSame('can:812:ok', utec_whatsapp_agenda_id_confirmar_cancelamento(812), 'id confirmar cancelamento');

$p = utec_whatsapp_agenda_parse_id('rem:812:d:20260925');
assertAgendaSame(array('fluxo' => 'remarcar', 'acao' => 'dia', 'id_agendamento' => 812, 'data' => '2026-09-25', 'hora' => '', 'pagina' => 0), $p, 'parse dia');
$p = utec_whatsapp_agenda_parse_id('rem:812:p:20260925:3');
assertAgendaSame('pagina', $p['acao'], 'parse pagina acao');
assertAgendaSame(3, $p['pagina'], 'parse pagina numero');
$p = utec_whatsapp_agenda_parse_id('rem:812:h:202609251430');
assertAgendaSame(array('fluxo' => 'remarcar', 'acao' => 'hora', 'id_agendamento' => 812, 'data' => '2026-09-25', 'hora' => '14:30', 'pagina' => 0), $p, 'parse hora');
$p = utec_whatsapp_agenda_parse_id('rem:812:ok:202609251430');
assertAgendaSame('confirmar', $p['acao'], 'parse confirmar remarcacao');
assertAgendaSame('remarcar', $p['fluxo'], 'parse confirmar remarcacao fluxo');
$p = utec_whatsapp_agenda_parse_id('rem:812:dias');
assertAgendaSame('dias', $p['acao'], 'parse outros dias');
$p = utec_whatsapp_agenda_parse_id('can:812:sem_motivo');
assertAgendaSame(array('fluxo' => 'cancelar', 'acao' => 'sem_motivo', 'id_agendamento' => 812, 'data' => '', 'hora' => '', 'pagina' => 0), $p, 'parse sem motivo');
$p = utec_whatsapp_agenda_parse_id('can:812:ok');
assertAgendaSame('confirmar', $p['acao'], 'parse confirmar cancelamento');
assertAgendaSame('cancelar', $p['fluxo'], 'parse confirmar cancelamento fluxo');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:d:20260231'), 'data inexistente rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:h:202609252460'), 'hora invalida rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:0:dias'), 'id zero rejeitado');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('rem:812:p:20260925:0'), 'pagina zero rejeitada');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('chat:paciente:remarcar:812'), 'id do menu nao e da agenda');
assertAgendaSame(null, utec_whatsapp_agenda_parse_id('confirmar_agendamento:812'), 'botao do template nao e da agenda');

// --- paginacao
function horasTeste($n) { $h = array(); for ($i = 0; $i < $n; $i++) { $h[] = sprintf('%02d:%02d', 8 + intdiv($i, 2), ($i % 2) * 30); } return $h; }
assertAgendaSame(array('itens' => array(), 'tem_mais' => false), utec_whatsapp_agenda_paginar_horarios(array(), 1), 'paginacao vazia');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(9), 1);
assertAgendaSame(9, count($r['itens']), '9 itens'); assertAgendaSame(false, $r['tem_mais'], '9 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(10), 1);
assertAgendaSame(10, count($r['itens']), '10 cabem'); assertAgendaSame(false, $r['tem_mais'], '10 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(11), 1);
assertAgendaSame(9, count($r['itens']), '11 pagina 1'); assertAgendaSame(true, $r['tem_mais'], '11 tem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(11), 2);
assertAgendaSame(2, count($r['itens']), '11 pagina 2'); assertAgendaSame(false, $r['tem_mais'], '11 pagina 2 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 2);
assertAgendaSame(9, count($r['itens']), '25 pagina 2'); assertAgendaSame(true, $r['tem_mais'], '25 pagina 2 tem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 3);
assertAgendaSame(7, count($r['itens']), '25 pagina 3'); assertAgendaSame(false, $r['tem_mais'], '25 pagina 3 sem mais');
$r = utec_whatsapp_agenda_paginar_horarios(horasTeste(25), 4);
assertAgendaSame(array(), $r['itens'], '25 pagina 4 vazia');

// --- rotulos e textos
assertAgendaSame('Sex 25/09', utec_whatsapp_agenda_rotulo_dia('2026-09-25'), 'rotulo sexta');
assertAgendaSame('Dom 27/09', utec_whatsapp_agenda_rotulo_dia('2026-09-27'), 'rotulo domingo');
assertAgendaSame('Sáb 26/09', utec_whatsapp_agenda_rotulo_dia('2026-09-26'), 'rotulo sabado');
assertAgendaSame('Remarcar para Sex 25/09 às 14:30 com Dra. Ana?', utec_whatsapp_agenda_texto_confirmar_remarcacao('2026-09-25', '14:30:00', 'Dra. Ana'), 'texto confirmar remarcacao');
assertAgendaSame('Remarcar para Sex 25/09 às 14:30?', utec_whatsapp_agenda_texto_confirmar_remarcacao('2026-09-25', '14:30', ''), 'texto sem profissional');
assertAgendaSame('Consulta remarcada para Sex 25/09 às 14:30 com Dra. Ana.', utec_whatsapp_agenda_texto_remarcado('2026-09-25', '14:30', 'Dra. Ana'), 'texto remarcado');
assertAgendaSame('Cancelar a consulta de Sex 25/09 às 14:30 com Dra. Ana?', utec_whatsapp_agenda_texto_confirmar_cancelamento('2026-09-25', '14:30', 'Dra. Ana'), 'texto confirmar cancelamento');
assertAgendaSame(true, strpos(utec_whatsapp_agenda_texto_fallback('prazo'), '24 horas') !== false, 'fallback prazo');
assertAgendaSame(true, utec_whatsapp_agenda_texto_fallback('sem_grade') !== '', 'fallback sem grade');
assertAgendaSame(true, strpos(utec_whatsapp_agenda_texto_fallback('sem_vaga'), '30 dias') !== false, 'fallback sem vaga');
assertAgendaSame('', utec_whatsapp_agenda_texto_fallback('outro'), 'fallback desconhecido');

// --- avisos internos
assertAgendaSame('whatsapp_chatbot_remarcado', utec_notificacoes_tipo_chatbot_agenda('remarcar'), 'tipo remarcado');
assertAgendaSame('whatsapp_chatbot_cancelado', utec_notificacoes_tipo_chatbot_agenda('cancelar'), 'tipo cancelado');
assertAgendaSame('', utec_notificacoes_tipo_chatbot_agenda('confirmar'), 'tipo fora do escopo');
assertAgendaSame(
    'Maria remarcou a consulta de 23/09/2026 às 14:00 para 25/09/2026 às 14:30 pelo WhatsApp.',
    utec_notificacoes_mensagem_chatbot_agenda('remarcar', 'Maria', array('data_anterior' => '2026-09-23', 'hora_anterior' => '14:00:00', 'data_nova' => '2026-09-25', 'hora_nova' => '14:30')),
    'mensagem remarcado'
);
assertAgendaSame(
    'Maria cancelou a consulta de 25/09/2026 às 14:30 pelo WhatsApp. Motivo: Viagem',
    utec_notificacoes_mensagem_chatbot_agenda('cancelar', 'Maria', array('data_anterior' => '2026-09-25', 'hora_anterior' => '14:30', 'motivo' => 'Viagem')),
    'mensagem cancelado com motivo'
);
assertAgendaSame(
    'O paciente cancelou a consulta de 25/09/2026 às 14:30 pelo WhatsApp. Sem motivo informado.',
    utec_notificacoes_mensagem_chatbot_agenda('cancelar', '', array('data_anterior' => '2026-09-25', 'hora_anterior' => '14:30', 'motivo' => '')),
    'mensagem cancelado sem motivo'
);

// --- template de remarcacao para a equipe
assertAgendaSame('agendamento_remarcado_equipe', utec_whatsapp_template_equipe_nome('remarcar'), 'template remarcado');
assertAgendaSame('agendamento_cancelado_equipe', utec_whatsapp_template_equipe_nome('cancelar'), 'template cancelado inalterado');
$comp = utec_whatsapp_componentes_equipe_template(array(
    'paciente_nome' => 'Maria', 'prestador_nome' => 'Dra. Ana',
    'data_anterior' => '2026-09-23', 'hora_anterior' => '14:00:00',
    'data_agenda' => '2026-09-25', 'hora_agenda' => '14:30:00',
), 'remarcar');
$textos = array();
foreach ($comp[0]['parameters'] as $par) { $textos[] = $par['text']; }
assertAgendaSame(array('Maria', 'Dra. Ana', '23/09/2026 as 14:00', '25/09/2026 as 14:30'), $textos, 'componentes remarcado');

echo "OK whatsapp_chatbot_agenda_test\n";
