<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';

function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function igual($e, $a, $l) { if ($e !== $a) { falha($l . ' | esperado ' . var_export($e, true) . ' obtido ' . var_export($a, true)); } }

$itens = Funcionalidades_conteudo::itens();
igual(16, count($itens), '16 itens');
igual(array('agenda' => 'Agenda', 'prontuario' => 'Prontuário', 'whatsapp' => 'WhatsApp', 'gestao' => 'Gestão'), Funcionalidades_conteudo::grupos(), 'grupos');

$ids = array(); $novos = array();
$rotas = file_get_contents(__DIR__ . '/../application/config/routes.php');
foreach ($itens as $i) {
    foreach (array('id', 'grupo', 'icone', 'titulo', 'resumo', 'descricao') as $k) {
        if (!isset($i[$k]) || trim((string)$i[$k]) === '') { falha('campo vazio ' . $k . ' em ' . (isset($i['id']) ? $i['id'] : '?')); }
    }
    if (!array_key_exists('link', $i) || !array_key_exists('novo', $i)) { falha('link/novo ausente em ' . $i['id']); }
    if (in_array($i['id'], $ids, true)) { falha('id duplicado ' . $i['id']); }
    $ids[] = $i['id'];
    if (!array_key_exists($i['grupo'], Funcionalidades_conteudo::grupos())) { falha('grupo invalido ' . $i['id']); }
    if (mb_strlen($i['resumo'], 'UTF-8') > 90) { falha('resumo > 90 em ' . $i['id']); }
    if ($i['link'] !== '' && strpos($rotas, "\$route['" . $i['link'] . "']") === false) { falha('link sem rota: ' . $i['link']); }
    if ($i['novo'] === true) { $novos[] = $i['id']; }
    foreach (array('tenant', 'owner', 'endpoint', 'migra') as $proibido) {
        if (stripos($i['titulo'] . ' ' . $i['resumo'] . ' ' . $i['descricao'], $proibido) !== false) { falha('termo tecnico "' . $proibido . '" em ' . $i['id']); }
    }
}
sort($novos);
igual(array('exportar_prontuario', 'ficha_paciente', 'rotulos', 'tempo_espera'), $novos, '4 novidades');
igual(array('agenda', 'horarios', 'tempo_espera', 'prontuario', 'prontuario_especialidade', 'ficha_paciente', 'exames', 'exportar_prontuario',
    'whatsapp_confirmacao', 'whatsapp_lembrete', 'chatbot', 'rotulos', 'avisos', 'relatorios', 'equipe', 'manual'), $ids, 'ordem dos ids');

igual(3, count(Funcionalidades_conteudo::por_grupo('whatsapp')), 'grupo whatsapp');
$sel = Funcionalidades_conteudo::por_ids(array('rotulos', 'nao_existe', 'agenda'));
igual(array('rotulos', 'agenda'), array_map(function ($i) { return $i['id']; }, $sel), 'por_ids ordem e ignora inexistente');
igual(array(), Funcionalidades_conteudo::por_ids(array()), 'por_ids vazio');

echo "OK\n";
