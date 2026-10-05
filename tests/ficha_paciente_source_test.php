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
$ctl = lerArquivo('application/controllers/adm/Ficha.php');
assertContains('class Ficha extends CI_Controller', $ctl, 'controller existe');
assertContains('verSession()', $ctl, 'exige sessao');
assertContains('if($nivel < 1 || $nivel > 4)', $ctl, 'bloqueia nivel 5');
assertContains('can_access_usuario($id)', $ctl, 'escopo do paciente');
assertContains('(int)$alvo->nivel !== 5', $ctl, 'so pacientes');
assertContains('utec_ficha_normalizar($this->input->post(), $pode_saude)', $ctl, 'descarta saude sem permissao');
assertContains('array(1, 2, 3)', $ctl, 'saude so 1-3');

$vf = lerArquivo('application/views/adm/ficha/paciente.php');
assertContains('name="alergias"', $vf, 'campo alergias');
assertContains('name="responsavel_cpf"', $vf, 'campo cpf responsavel');
assertContains('name="convenio_validade"', $vf, 'campo validade');
assertContains('$pode_editar_saude', $vf, 'saude condicionada');
assertContains('htmlspecialchars', $vf, 'view escapa');

$pront = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains("load->model('Ficha_paciente_model', 'ficha_model')", $pront, 'prontuario carrega ficha');
assertContains('ficha_model->disponivel()', $pront, 'guarda sem migracao');
assertContains('class="ut-ficha-alergia"', $pront, 'alergias em destaque');
assertContains('class="ut-ficha-card"', $pront, 'card da ficha');
assertContains('adm/ficha/paciente/', $pront, 'link editar ficha');
assertContains("flashdata('ficha_ok')", $pront, 'flash da ficha');

$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Editar ficha', $manual, 'manual cobre ficha');
$claude = lerArquivo('CLAUDE.md');
assertContains('migrar_ficha_pacientes', $claude, 'CLAUDE.md documenta migracao');

echo "OK\n";
