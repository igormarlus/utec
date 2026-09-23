<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerArquivo($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) {
        fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL);
        exit(1);
    }
    return file_get_contents($path);
}

// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_horarios_atendimento()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `prestador_horarios`', $dev, 'tabela horarios');
assertContains('CREATE TABLE IF NOT EXISTS `prestador_bloqueios`', $dev, 'tabela bloqueios');
assertContains("'duracao_atendimento_min'", $dev, 'coluna duracao');

// Model
$model = lerArquivo('application/models/Disponibilidade_model.php');
foreach (array('function schema_ok(', 'function get_config(', 'function salvar_config(',
    'function listar_bloqueios_futuros(', 'function adicionar_bloqueio(', 'function remover_bloqueio(',
    'function horarios_livres(', 'function verificar_horario(', 'function proximos_livres(') as $fn) {
    assertContains($fn, $model, 'model: ' . $fn);
}
assertContains('status IN (0,1,2)', $model, 'model considera status ocupantes');
assertContains('trans_start()', $model, 'salvar_config em transacao');
assertContains("load->helper('disponibilidade')", $model, 'model carrega helper');

echo "OK disponibilidade_source_test\n";
