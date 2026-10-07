<?php
define('BASEPATH', __DIR__);
function base_url($p = '') { return 'https://exemplo.test/' . $p; }
require __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';

function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function render($vars) {
    extract($vars);
    ob_start();
    include __DIR__ . '/../application/views/public/partials/funcionalidades.php';
    return ob_get_clean();
}
set_error_handler(function ($no, $str) { falha('notice no partial: ' . $str); });

$grade = render(array('func_formato' => 'grade', 'func_ids' => null, 'func_agrupar' => true, 'func_titulo' => 'Tudo o que está incluído'));
foreach (array('Agenda', 'Prontuário', 'WhatsApp', 'Gestão', 'Tudo o que está incluído', 'Agenda inteligente', 'Manual de ajuda') as $t) {
    if (strpos($grade, htmlspecialchars($t, ENT_QUOTES, 'UTF-8')) === false) { falha('grade sem: ' . $t); }
}
if (substr_count($grade, 'class="fx-novo"') !== 5) { falha('esperado 5 selos Novo, veio ' . substr_count($grade, 'class="fx-novo"')); }
if (strpos($grade, 'https://exemplo.test/sistema-prontuario-eletronico') === false) { falha('link saiba mais ausente'); }
if (substr_count($grade, '<style') !== 1) { falha('css deve sair uma vez'); }

$lista = render(array('func_formato' => 'lista', 'func_ids' => array('agenda', 'chatbot'), 'func_agrupar' => false, 'func_titulo' => ''));
if (strpos($lista, '<ul class="fx-lista"') === false) { falha('lista sem ul'); }
if (substr_count($lista, '<li') !== 2) { falha('lista deve ter 2 itens'); }
if (strpos($lista, '<style') !== false) { falha('css repetido na segunda chamada'); }
if (strpos($lista, '<h2') !== false) { falha('titulo vazio nao deve gerar h2'); }

echo "OK\n";
