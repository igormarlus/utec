<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerArquivo($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) { fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL); exit(1); }
    return file_get_contents($path);
}

$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_tempos_atendimento()', $dev, 'migracao existe');
foreach (array("'chegada_em', \"DATETIME NULL\"", "'chegada_por', \"INT NULL\"", "'inicio_atendimento_em', \"DATETIME NULL\"", "'fim_atendimento_em', \"DATETIME NULL\"") as $c) {
    assertContains($c, $dev, 'coluna ' . $c);
}

$ctl = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains("'tempo_atendimento'", $ctl, 'helper carregado no construtor');
assertContains('private function tem_colunas_tempo()', $ctl, 'guarda de schema');
assertContains('utec_tempo_campos_transicao((int)$status, $new_status', $ctl, 'set_status grava horarios');
assertContains('utec_tempo_campos_zerados()', $ctl, 'remarcar zera horarios');
assertContains('function checkin(', $ctl, 'endpoint checkin');
assertContains("input->method() !== 'post'", $ctl, 'checkin so POST');
assertContains('can_access_agendamento($id_agenda)', $ctl, 'checkin respeita escopo');
assertContains("preg_match('#^adm/[a-z0-9_/]*$#i', \$voltar)", $ctl, 'voltar restrito');

echo "OK\n";
