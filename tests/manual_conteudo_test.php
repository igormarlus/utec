<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Manual_conteudo.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

function assertTrue($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$mc = new Manual_conteudo();

assertSameValue(1, Manual_conteudo::VERSAO, 'VERSAO do modelo deve ser 1.');

$nivel2 = $mc->capitulos_por_nivel(2);
$nivel3 = $mc->capitulos_por_nivel(3);
$nivel4 = $mc->capitulos_por_nivel(4);
$nivelInvalido = $mc->capitulos_por_nivel(99);

assertSameValue(12, count($nivel2), 'Nivel 2 deve ver os 12 capitulos.');
assertSameValue(12, count($nivel3), 'Nivel 3 deve ver os 12 capitulos.');
assertSameValue(11, count($nivel4), 'Nivel 4 nao ve o capitulo de equipe.');
assertSameValue(0, count($nivelInvalido), 'Nivel sem capitulos cadastrados retorna lista vazia.');

$slugsNivel4 = array_column($nivel4, 'slug');
assertTrue(!in_array('equipe', $slugsNivel4, true), 'Colaborador (nivel 4) nao deve ver o capitulo equipe.');

$slugsNivel2 = array_column($nivel2, 'slug');
assertTrue(in_array('equipe', $slugsNivel2, true), 'Estabelecimento (nivel 2) deve ver o capitulo equipe.');

$assinaturaNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'assinatura-pagamento') {
        $assinaturaNivel2 = $capitulo;
    }
}
assertTrue($assinaturaNivel2 !== null, 'Capitulo assinatura-pagamento deve existir para o nivel 2.');
assertTrue(
    in_array('A tela de assinatura mostra o historico de ciclos de cobranca, pagamentos confirmados e tentativas recentes.', $assinaturaNivel2['topicos'], true),
    'Topico comum (*) deve entrar na lista do nivel 2.'
);
assertTrue(
    in_array('E o Estabelecimento quem normalmente acompanha e quita a assinatura da operacao, em `Minha assinatura`.', $assinaturaNivel2['topicos'], true),
    'Topico especifico do nivel 2 deve entrar na lista.'
);

$boasVindasNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'boas-vindas') {
        $boasVindasNivel2 = $capitulo;
    }
}
assertSameValue('boas-vindas.png', $boasVindasNivel2['print'], 'Print do capitulo boas-vindas deve resolver para o arquivo unico.');

$acessoNivel2 = null;
foreach ($nivel2 as $capitulo) {
    if ($capitulo['slug'] === 'acesso-hierarquia') {
        $acessoNivel2 = $capitulo;
    }
}
assertSameValue(null, $acessoNivel2['print'], 'Capitulo sem print cadastrado deve resolver para null.');

echo "OK\n";
