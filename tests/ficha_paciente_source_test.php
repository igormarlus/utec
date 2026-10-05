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
assertContains('function migrar_ficha_pacientes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_ficha`', $dev, 'tabela ficha');
assertContains('`id_paciente` INT NOT NULL PRIMARY KEY', $dev, 'pk id_paciente');
assertContains('`saude_atualizado_em` DATETIME NULL', $dev, 'auditoria de saude');

$model = lerArquivo('application/models/Ficha_paciente_model.php');
assertContains('function disponivel(', $model, 'disponivel');
assertContains('function obter(', $model, 'obter');
assertContains('function salvar(', $model, 'salvar');
assertContains("table_exists('pacientes_ficha')", $model, 'guarda table_exists');
assertContains('ON DUPLICATE KEY UPDATE', $model, 'upsert');
assertContains('array_keys(utec_ficha_vazia())', $model, 'colunas por whitelist');

echo "OK\n";
