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

// Aviso no sino
$notif = lerArquivo('application/models/Notificacoes_model.php');
assertContains('public function criar_aviso_lista_espera(', $notif, 'metodo de aviso');
assertContains("'lista_espera_vaga'", $notif, 'tipo do aviso');

// Library
$lib = lerArquivo('application/libraries/Lista_espera_vagas.php');
assertContains('class Lista_espera_vagas', $lib, 'classe');
assertContains('public function vaga_aberta(', $lib, 'vaga_aberta');
assertContains('public function vaga_do_agendamento(', $lib, 'vaga_do_agendamento');
assertContains('catch (Throwable $e)', $lib, 'blindada');
assertContains('utec_le_deve_avisar(', $lib, 'regra de aviso');
assertContains('->horario_ocupado(', $lib, 'checa vaga livre');
assertContains('->registrar_vaga(', $lib, 'deduplica');
assertContains("'adm/lista_espera/vaga/'", $lib, 'url do aviso');

// Pontos de disparo
$atd = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains("vaga_do_agendamento(\$id_agenda, 'agenda_cancelar')", $atd, 'cancelar na agenda');
assertContains("'agenda_remarcar'", $atd, 'remarcar na agenda');
$posAnt = strpos($atd, '$ant_remarcar = $this->db->query(');
$posUpd = strpos($atd, '$atualizado = $this->db->update(\'agendamentos\', $upd_remarcar);');
if ($posAnt === false || $posUpd === false || $posAnt > $posUpd) { fwrite(STDERR, "remarcar le a vaga antiga antes do UPDATE\n"); exit(1); }
$wh = lerArquivo('application/controllers/Webhooks.php');
assertContains("vaga_do_agendamento(\$idAgendamento, 'whatsapp_cancelar')", $wh, 'botao cancelar do WhatsApp');
$posProc = strpos($wh, "if (!\$resultado['processado'])");
$posVaga = strpos($wh, "'whatsapp_cancelar'");
if ($posProc === false || $posVaga === false || $posVaga < $posProc) { fwrite(STDERR, "webhook so dispara apos processado\n"); exit(1); }
$bot = lerArquivo('application/libraries/Whatsapp_chatbot_agenda.php');
assertContains("'chatbot_cancelar'", $bot, 'cancelar pelo chatbot');
assertContains("'chatbot_remarcar'", $bot, 'remarcar pelo chatbot');

// Controller e telas
$ctl = lerArquivo('application/controllers/adm/Lista_espera.php');
foreach (array('class Lista_espera extends CI_Controller', 'public function index(', 'public function salvar(',
    'public function remover(', 'public function vaga(', 'can_access_usuario(', 'utec_le_normalizar(', 'utec_le_ordenar(',
    '#^adm/[a-z0-9_/?=&-]*$#i') as $t) {
    assertContains($t, $ctl, 'controller ' . $t);
}
$vIndex = lerArquivo('application/views/adm/lista_espera/index.php');
foreach (array('Como funciona', 'adm/lista_espera/salvar', 'adm/lista_espera/remover/', 'adm/atendimento/buscar_paciente',
    'Qualquer profissional', 'migrar_lista_espera', 'utec_le_resumo_preferencias(', 'utec_le_dias_espera(') as $t) {
    assertContains($t, $vIndex, 'view index ' . $t);
}
$vVaga = lerArquivo('application/views/adm/lista_espera/vaga.php');
foreach (array('Vaga já preenchida', 'Compatível', 'adm/atendimento/novo/', 'lista_espera=') as $t) {
    assertContains($t, $vVaga, 'view vaga ' . $t);
}
assertContains("'Lista de espera', 'url' => base_url().'adm/lista_espera'", lerArquivo('includes/adm/menu.php'), 'menu');

echo "OK\n";
