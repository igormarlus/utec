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
assertContains("preg_match('#^adm/[a-z0-9_/]*$#iD', \$voltar)", $ctl, 'voltar restrito');

assertContains('utec_tempo_campos_transicao((int)$dd_agenda->status, $status_destino', $ctl, 'formulario do prontuario grava horarios');
assertContains('if(!isset($new_status)){ show_404(); return; }', $ctl, 'status invalido 404');
assertContains('if((int)$dd->status !== $status){', $ctl, 'link desatualizado nao sobrescreve');
assertContains('$id_agenda = (int)$this->input->post(\'id_agenda\');', $ctl, 'id do formulario como inteiro');

$usr = lerArquivo('application/controllers/adm/Usuarios.php');
assertContains("a.status <> 3", $usr, 'cancelados fora das medias');
assertContains("utec_tempo_sql_agregados('a')", $usr, 'relatorio usa agregacao');
assertContains("\$dados['tempos']", $usr, 'relatorio passa tempos');
$rel = lerArquivo('application/views/adm/relatorios/clinicos.php');
assertContains('Espera média', $rel, 'card espera');
assertContains('Tempos por profissional', $rel, 'tabela por profissional');

$ag = lerArquivo('application/views/adm/usuarios/new/atendimentos.php');
assertContains('utec_tempo_resumo_agenda($agenda)', $ag, 'resumo na agenda');
assertContains('adm/atendimento/checkin/', $ag, 'form de check-in');
assertContains('value="desfazer"', $ag, 'desfazer chegada');
assertContains("flashdata('tempo_ok')", $ag, 'flash de tempo');
assertContains("field_exists('chegada_em', 'agendamentos')", $ag, 'guarda sem migracao');
if (substr_count($ag, 'utec_tempo_resumo_agenda($agenda)') !== 4) { fwrite(STDERR, "resumo deve aparecer 4x\n"); exit(1); }

$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Chegou', $manual, 'manual cobre check-in');
$claude = lerArquivo('CLAUDE.md');
assertContains('migrar_tempos_atendimento', $claude, 'CLAUDE.md documenta migracao');

echo "OK\n";
