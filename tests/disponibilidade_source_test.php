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

// Controller
$ctrl = lerArquivo('application/controllers/adm/Horarios.php');
foreach (array('function index(', 'function salvar(', 'function bloquear(', 'function desbloquear(',
    'function livres(', 'function pode_editar(', 'function pode_ver(') as $fn) {
    assertContains($fn, $ctrl, 'controller: ' . $fn);
}
assertContains('utec_disp_validar_intervalos(', $ctrl, 'controller valida intervalos');
assertContains('utec_disp_normalizar_bloqueio(', $ctrl, 'controller normaliza bloqueio');
assertContains('get_visible_prestador_ids(', $ctrl, 'controller usa escopo de prestadores');
assertContains("set_status_header(403)", $ctrl, 'endpoint livres responde 403');

// View e menu
$view = lerArquivo('application/views/adm/horarios/index.php');
assertContains('name="duracao"', $view, 'view tem duracao');
assertContains('dias[', $view, 'view tem grade');
assertContains('adm/horarios/bloquear', $view, 'view tem form de bloqueio');
assertContains('htmlspecialchars(', $view, 'view escapa saida');
$menu = lerArquivo('includes/adm/menu.php');
assertContains("adm/horarios'", $menu, 'menu aponta para horarios');

echo "OK disponibilidade_source_test\n";
