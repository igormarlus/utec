<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function assertNotContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) !== false) {
        fwrite(STDERR, $label . ' — trecho nao deveria existir: ' . $needle . PHP_EOL);
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

// View do prontuário usa o helper (tela e exportação não divergem)
$view = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains("load->helper('prontuario_export')", $view, 'view carrega helper');
assertContains('$lbl = utec_pront_rotulos(', $view, 'view usa rotulos do helper');
assertNotContains("case 10: // Fisioterapia", $view, 'switch removido da view');

// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_prontuario_exportacoes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `prontuario_exportacoes`', $dev, 'tabela auditoria');

// Model
$model = lerArquivo('application/models/Prontuario_export_model.php');
assertContains('function coletar(', $model, 'coletar existe');
assertContains('function registrar_exportacao(', $model, 'registrar existe');
assertContains("table_exists('prontuario_exportacoes')", $model, 'auditoria guardada por table_exists');
assertContains('get_scope_user_ids', $model, 'model aplica escopo dos agendamentos');
assertContains('utec_pront_rotulos(', $model, 'model usa rotulos do helper');

echo "OK\n";
