<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/rotulos_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

// --- paleta / cor
$p = utec_rotulos_paleta();
assertSameValue(8, count($p), 'paleta 8 cores');
assertSameValue('#dc2626', $p['vermelho'], 'hex vermelho');
assertSameValue('azul', utec_rotulos_cor_valida('azul'), 'cor valida');
assertSameValue('cinza', utec_rotulos_cor_valida('#ff0000'), 'cor fora da paleta');
assertSameValue('cinza', utec_rotulos_cor_valida(null), 'cor null');

// --- nome
assertSameValue('Retorno pendente', utec_rotulos_normalizar_nome("  Retorno   pendente \n"), 'nome normalizado');
assertSameValue('', utec_rotulos_normalizar_nome('   '), 'nome vazio');
assertSameValue(40, mb_strlen(utec_rotulos_normalizar_nome(str_repeat('é', 41)), 'UTF-8'), 'nome cortado em 40 (multibyte)');
assertSameValue('Alérgico', utec_rotulos_normalizar_nome('Alérgico'), 'acento preservado');

// --- raiz da árvore (árvore falsa)
$arvore = array(
    2  => array('id' => 2,  'nivel' => 2, 'id_user' => 1),   // estabelecimento
    1  => array('id' => 1,  'nivel' => 1, 'id_user' => 0),   // admin
    3  => array('id' => 3,  'nivel' => 3, 'id_user' => 2),   // prestador da clínica
    4  => array('id' => 4,  'nivel' => 4, 'id_user' => 3),   // colaborador do prestador
    50 => array('id' => 50, 'nivel' => 5, 'id_user' => 4),  // paciente
    30 => array('id' => 30, 'nivel' => 3, 'id_user' => 1),   // prestador autônomo
    51 => array('id' => 51, 'nivel' => 5, 'id_user' => 30),  // paciente do autônomo
    52 => array('id' => 52, 'nivel' => 5, 'id_user' => 999), // órfão (pai inexistente)
    60 => array('id' => 60, 'nivel' => 4, 'id_user' => 61),  // ciclo 60 <-> 61
    61 => array('id' => 61, 'nivel' => 4, 'id_user' => 60),
);
$buscar = function ($id) use ($arvore) { return isset($arvore[$id]) ? $arvore[$id] : null; };
assertSameValue(2, utec_rotulos_resolver_raiz(50, $buscar), 'paciente -> colab -> prestador -> estabelecimento');
assertSameValue(2, utec_rotulos_resolver_raiz(2, $buscar), 'estabelecimento e a propria raiz');
assertSameValue(2, utec_rotulos_resolver_raiz(4, $buscar), 'colaborador sobe ate estabelecimento');
assertSameValue(30, utec_rotulos_resolver_raiz(51, $buscar), 'paciente do autonomo');
assertSameValue(30, utec_rotulos_resolver_raiz(30, $buscar), 'autonomo e a propria raiz (pai admin ignorado)');
assertSameValue(52, utec_rotulos_resolver_raiz(52, $buscar), 'orfao fica nele mesmo');
assertTrue(in_array(utec_rotulos_resolver_raiz(60, $buscar), array(60, 61), true), 'ciclo termina');
assertSameValue(0, utec_rotulos_resolver_raiz(1, $buscar), 'admin nao tem conta');
assertSameValue(0, utec_rotulos_resolver_raiz(12345, $buscar), 'usuario inexistente');

// --- sugestões
$s = utec_rotulos_sugestoes();
assertSameValue(5, count($s), '5 sugestoes');
assertSameValue(array('Gestante', 'rosa', 1), $s[3], 'gestante alerta');

// --- ids válidos
assertSameValue(array(3, 7), utec_rotulos_ids_validos(array('3', '7', '7', '99', 'x', '-1'), array(3, 7, 8)), 'ids filtrados pelo catalogo');
assertSameValue(array(), utec_rotulos_ids_validos(array(), array(3)), 'post vazio');

// --- chips
$rot = array(
    (object)array('id' => 3, 'nome' => 'VIP', 'cor' => 'roxo', 'alerta' => 0),
    array('id' => 7, 'nome' => '<script>x</script>', 'cor' => 'vermelho', 'alerta' => '1'),
);
$html = utec_rotulos_chips_html($rot);
assertTrue(strpos($html, 'VIP') !== false, 'chip VIP');
assertTrue(strpos($html, '<script>') === false, 'escapa script');
assertTrue(strpos($html, '&lt;script&gt;') !== false, 'texto escapado presente');
assertTrue(strpos($html, 'ut-rotulo-alerta') !== false, 'classe de alerta');
assertTrue(strpos($html, '#7c3aed') !== false, 'cor roxo aplicada');
$so = utec_rotulos_chips_html($rot, true);
assertTrue(strpos($so, 'VIP') === false && strpos($so, 'ut-rotulo-alerta') !== false, 'so alertas');
assertSameValue('', utec_rotulos_chips_html(array()), 'sem rotulos = vazio');

// --- data attr
assertSameValue(',3,7,', utec_rotulos_data_attr($rot), 'data attr');
assertSameValue(',', utec_rotulos_data_attr(array()), 'data attr vazio');

echo "OK\n";
