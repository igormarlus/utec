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

// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_lista_espera()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `lista_espera`', $dev, 'tabela entradas');
assertContains('CREATE TABLE IF NOT EXISTS `lista_espera_vagas`', $dev, 'tabela vagas');
assertContains('UNIQUE KEY `uk_vaga_origem` (`id_agendamento_origem`, `data_agenda`, `hora_agenda`)', $dev, 'vaga unica por origem');

// Model
$model = lerArquivo('application/models/Lista_espera_model.php');
foreach (array('function disponivel(', 'function conta_raiz(', 'function listar(', 'function buscar(', 'function adicionar(',
    'function atualizar(', 'function remover(', 'function marcar_agendado(', 'function aguardando_do_paciente(',
    'function contar_aguardando_para(', 'function aguardando_para(', 'function registrar_vaga(', 'function buscar_vaga(',
    'function horario_ocupado(', 'function destinatarios_conta(') as $fn) {
    assertContains($fn, $model, 'model ' . $fn);
}
assertContains('INSERT IGNORE INTO `lista_espera_vagas`', $model, 'vaga deduplicada');
assertContains('status IN (0,1,2)', $model, 'ocupacao considera so ativos');
assertContains('id_prestador <=> ?', $model, 'duplicidade null-safe');

echo "OK\n";
