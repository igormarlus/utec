<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/prontuario_export_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- rótulos (iguais ao switch original da view)
$padrao = utec_pront_rotulos(0);
assertSameValue('Atendimento Inicial', $padrao['atendimento_inicial'], 'rotulo padrao inicial');
assertSameValue('Reavaliação', $padrao['reavaliacao'], 'rotulo padrao reav');
assertSameValue('Registre avaliação clínica, hipóteses e condutas adotadas.', $padrao['ph_avaliacao'], 'placeholder padrao');
$fisio = utec_pront_rotulos(10);
assertSameValue('Queixa / Avaliação Postural', $fisio['atendimento_inicial'], 'fisio inicial');
$psico = utec_pront_rotulos('36');
assertSameValue('Evolução da Sessão', $psico['avaliacao'], 'psico aceita string');
assertSameValue('Atendimento Inicial', utec_pront_rotulos(999)['atendimento_inicial'], 'esp desconhecida = padrao');

// --- status
assertSameValue('Pendente', utec_pront_status_texto(0), 'status 0');
assertSameValue('Em atendimento', utec_pront_status_texto('1'), 'status 1');
assertSameValue('Finalizado', utec_pront_status_texto(2), 'status 2');
assertSameValue('Cancelado', utec_pront_status_texto(3), 'status 3');
assertSameValue('Pendente', utec_pront_status_exame_texto(0), 'exame 0');
assertSameValue('Solicitado', utec_pront_status_exame_texto('1'), 'exame 1');
assertSameValue('Entregue', utec_pront_status_exame_texto(2), 'exame 2');

// --- datas e período
assertSameValue('03/10/2026', utec_pront_data_br('2026-10-03'), 'data br');
assertSameValue('03/10/2026', utec_pront_data_br('2026-10-03 10:00:00'), 'data br com hora');
assertSameValue('', utec_pront_data_br('lixo'), 'data br invalida');
assertSameValue(array('2026-01-01', '2026-02-01'), utec_pront_normalizar_periodo('2026-01-01', '2026-02-01'), 'periodo ok');
assertSameValue(array('2026-01-01', '2026-02-01'), utec_pront_normalizar_periodo('2026-02-01', '2026-01-01'), 'periodo invertido');
assertSameValue(array('', '2026-02-01'), utec_pront_normalizar_periodo('01/01/2026', '2026-02-01'), 'de invalido vira vazio');
assertSameValue(array('', ''), utec_pront_normalizar_periodo(null, ''), 'periodo vazio');
assertSameValue(array('', ''), utec_pront_normalizar_periodo('2026-02-31', ''), 'data inexistente');
assertSameValue(array('', ''), utec_pront_normalizar_periodo("2026-01-01\n", ''), 'data com quebra de linha final rejeitada');

// --- extras
$labels = array('eva' => 'Escala EVA', 'regiao' => 'Região');
assertSameValue(array(array('Escala EVA', '7'), array('Região', 'Lombar'), array('outro', 'x')),
    utec_pront_montar_extras('{"eva":7,"regiao":"Lombar","vazio":"","outro":"x"}', $labels), 'extras com e sem config');
assertSameValue(array(array('Região', 'Lombar, Joelho')),
    utec_pront_montar_extras('{"regiao":["Lombar","Joelho"]}', $labels), 'extras array');
assertSameValue(array(), utec_pront_montar_extras('', $labels), 'extras vazio');
assertSameValue(array(), utec_pront_montar_extras('{quebrado', $labels), 'extras json invalido');
assertSameValue('Escala EVA: 7 | Região: Lombar',
    utec_pront_extras_texto(array(array('Escala EVA', '7'), array('Região', 'Lombar'))), 'extras texto');

// --- linhas tabulares
$dados = array(
    'atendimentos' => array(array(
        'data' => '2026-10-01', 'hora' => '09:30', 'status_texto' => 'Finalizado',
        'profissional' => 'Dra. Ana', 'especialidade' => 'Fisioterapia',
        'atendimento_inicial' => 'Dor lombar', 'avaliacao' => 'RPG', 'reavaliacao' => 'Retorno 7d',
        'extras' => array(array('Escala EVA', '7')), 'rotulos' => utec_pront_rotulos(10),
    )),
    'exames' => array(array(
        'data' => '2026-10-01', 'exame' => 'Raio-X', 'status_texto' => 'Solicitado',
        'profissional' => 'Dra. Ana', 'obs' => 'Coluna',
    )),
);
$abas = utec_pront_linhas_tabulares($dados);
assertSameValue(array('Atendimentos', 'Exames'), array_keys($abas), 'abas');
assertSameValue(array('Data', 'Hora', 'Status', 'Profissional', 'Especialidade', 'Atendimento inicial', 'Avaliação', 'Reavaliação', 'Campos extras'),
    $abas['Atendimentos'][0], 'cabecalho atendimentos');
assertSameValue(array('01/10/2026', '09:30', 'Finalizado', 'Dra. Ana', 'Fisioterapia', 'Dor lombar', 'RPG', 'Retorno 7d', 'Escala EVA: 7'),
    $abas['Atendimentos'][1], 'linha atendimento');
assertSameValue(array('Data', 'Exame', 'Status', 'Profissional', 'Observação'), $abas['Exames'][0], 'cabecalho exames');
assertSameValue(array('01/10/2026', 'Raio-X', 'Solicitado', 'Dra. Ana', 'Coluna'), $abas['Exames'][1], 'linha exame');
$vazio = utec_pront_linhas_tabulares(array('atendimentos' => array(), 'exames' => array()));
assertSameValue(1, count($vazio['Atendimentos']), 'so cabecalho quando vazio');

// --- célula segura
assertSameValue("'=SOMA(A1)", utec_pront_celula_segura('=SOMA(A1)'), 'formula =');
assertSameValue("'+55 11", utec_pront_celula_segura('+55 11'), 'formula +');
assertSameValue("'-1", utec_pront_celula_segura('-1'), 'formula -');
assertSameValue("'@x", utec_pront_celula_segura('@x'), 'formula @');
assertSameValue('texto', utec_pront_celula_segura('texto'), 'texto normal');
assertSameValue('', utec_pront_celula_segura(null), 'null');

// --- CSV
$csv = utec_pront_csv(array(
    'A' => array(array('Nome', 'Obs'), array('Ana; Silva', "linha1\nlinha2"), array('Diz "oi"', '=1+1')),
    'B' => array(array('X')),
));
$bom = "\xEF\xBB\xBF";
assertSameValue($bom, substr($csv, 0, 3), 'csv bom');
$esperado = $bom
    . "Nome;Obs\r\n"
    . "\"Ana; Silva\";\"linha1\nlinha2\"\r\n"
    . "\"Diz \"\"oi\"\"\";'=1+1\r\n"
    . "\r\n"
    . "X\r\n";
assertSameValue($esperado, $csv, 'csv completo');

// --- nome de arquivo
assertSameValue('prontuario-42-20261003.pdf', utec_pront_nome_arquivo('42', 'pdf', '2026-10-03'), 'nome arquivo');

echo "OK\n";
