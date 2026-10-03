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
assertContains('function migrar_rotulos_pacientes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_rotulos`', $dev, 'tabela catalogo');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_rotulos_vinculos`', $dev, 'tabela vinculos');
assertContains('UNIQUE KEY `uk_rotulo_conta_nome` (`id_conta`, `nome`)', $dev, 'nome unico por conta');
assertContains('PRIMARY KEY (`id_paciente`, `id_rotulo`)', $dev, 'pk composta');

// Model
$model = lerArquivo('application/models/Rotulos_model.php');
foreach (array('function disponivel(', 'function conta_raiz(', 'function catalogo(', 'function garantir_sugestoes(',
    'function salvar_rotulo(', 'function alternar_status(', 'function rotulos_do_paciente(',
    'function rotulos_de_pacientes(', 'function definir_rotulos_paciente(') as $fn) {
    assertContains($fn, $model, 'model: ' . $fn);
}
assertContains("table_exists('pacientes_rotulos_vinculos')", $model, 'model guardado por table_exists');
assertContains('trans_start', $model, 'definir em transacao');
assertContains('utec_rotulos_resolver_raiz(', $model, 'raiz via helper');

// Controller
$ctl = lerArquivo('application/controllers/adm/Rotulos.php');
assertContains('class Rotulos extends CI_Controller', $ctl, 'controller existe');
assertContains('verSession()', $ctl, 'exige sessao');
assertContains('if($nivel < 1 || $nivel > 4)', $ctl, 'bloqueia nivel 5');
assertContains('function conta_gerenciada()', $ctl, 'regra de quem gerencia');
assertContains('can_access_usuario($id_paciente)', $ctl, 'aplica so no escopo');
assertContains('utec_rotulos_ids_validos(', $ctl, 'filtra ids pelo catalogo do paciente');
assertContains("input->method() !== 'post'", $ctl, 'escritas so via POST');
assertContains("preg_match('#^adm/[a-z0-9_/]+$#i'", $ctl, 'redirect de volta restrito ao admin');

// View catálogo
$vcat = lerArquivo('application/views/adm/rotulos/index.php');
assertContains('adm/rotulos/salvar', $vcat, 'form salvar');
assertContains('adm/rotulos/status/', $vcat, 'form status');
assertContains('htmlspecialchars', $vcat, 'view escapa');

// Menu
$menu = lerArquivo('includes/adm/menu.php');
assertContains("base_url().'adm/rotulos'", $menu, 'item de menu');

echo "OK\n";
