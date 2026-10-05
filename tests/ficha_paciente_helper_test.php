<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/ficha_paciente_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

// --- estrutura
$vazia = utec_ficha_vazia();
assertSameValue(19, count($vazia), '19 chaves');
assertSameValue('', $vazia['alergias'], 'vazia tem strings vazias');
assertSameValue(array('tipo_sanguineo', 'alergias', 'medicamentos', 'comorbidades', 'obs_saude'), utec_ficha_campos_saude(), 'campos de saude');
assertSameValue(8, count(utec_ficha_opcoes_tipo_sanguineo()), '8 tipos sanguineos');
assertSameValue('Feminino', utec_ficha_opcoes_sexo()['feminino'], 'rotulo sexo');

// --- CPF
assertTrue(utec_ficha_cpf_valido('529.982.247-25'), 'cpf valido com mascara');
assertTrue(utec_ficha_cpf_valido('52998224725'), 'cpf valido sem mascara');
assertTrue(!utec_ficha_cpf_valido('529.982.247-24'), 'dv errado');
assertTrue(!utec_ficha_cpf_valido('111.111.111-11'), 'repetido');
assertTrue(!utec_ficha_cpf_valido('123'), 'curto');

// --- normalização completa (pode editar saúde)
$post = array(
    'nome_social' => "  Ana   Maria  ",
    'sexo' => 'feminino',
    'estado_civil' => 'invalido',
    'responsavel_nome' => str_repeat('x', 130),
    'responsavel_parentesco' => 'Mãe',
    'responsavel_telefone' => '(81) 99999-0000',
    'responsavel_cpf' => '529.982.247-25',
    'emergencia_nome' => 'João',
    'emergencia_parentesco' => 'Pai',
    'emergencia_telefone' => '',
    'tipo_sanguineo' => 'O-',
    'alergias' => "Dipirona\r\nPenicilina  ",
    'medicamentos' => str_repeat('é', 2100),
    'comorbidades' => '',
    'obs_saude' => 'ok',
    'convenio_nome' => 'Unimed',
    'convenio_plano' => 'Apartamento',
    'convenio_carteirinha' => '0001',
    'convenio_validade' => '2026-02-31',
    'campo_estranho' => 'x',
);
$r = utec_ficha_normalizar($post, true);
$d = $r['dados'];
assertSameValue(array(), $r['erros'], 'sem erros');
assertSameValue('Ana Maria', $d['nome_social'], 'espacos colapsados');
assertSameValue('feminino', $d['sexo'], 'sexo valido');
assertSameValue('', $d['estado_civil'], 'opcao invalida vira vazio');
assertSameValue(120, mb_strlen($d['responsavel_nome'], 'UTF-8'), 'corta em 120');
assertSameValue('81999990000', $d['responsavel_telefone'], 'telefone so digitos');
assertSameValue('52998224725', $d['responsavel_cpf'], 'cpf so digitos');
assertSameValue('', $d['emergencia_telefone'], 'telefone vazio permanece vazio');
assertSameValue('O-', $d['tipo_sanguineo'], 'tipo sanguineo');
assertSameValue("Dipirona\nPenicilina", $d['alergias'], 'quebras normalizadas e trim');
assertSameValue(2000, mb_strlen($d['medicamentos'], 'UTF-8'), 'texto longo cortado em 2000');
assertSameValue('', $d['convenio_validade'], 'data inexistente vira vazio');
assertTrue(!array_key_exists('campo_estranho', $d), 'ignora campos desconhecidos');
assertSameValue(19, count($d), '19 chaves com saude');

// --- sem permissão de saúde: chaves de saúde removidas
$r2 = utec_ficha_normalizar($post, false);
foreach (utec_ficha_campos_saude() as $k) {
    assertTrue(!array_key_exists($k, $r2['dados']), 'sem saude: ' . $k);
}
assertSameValue(14, count($r2['dados']), '14 chaves sem saude');

// --- CPF inválido gera erro
$r3 = utec_ficha_normalizar(array('responsavel_cpf' => '111.111.111-11'), true);
assertSameValue(array('CPF do responsável inválido.'), $r3['erros'], 'erro de cpf');
assertSameValue('', $r3['dados']['responsavel_cpf'], 'cpf invalido nao gravado');

// --- validade válida e post não-array
$r4 = utec_ficha_normalizar(array('convenio_validade' => '2027-12-31'), true);
assertSameValue('2027-12-31', $r4['dados']['convenio_validade'], 'validade valida');
$r5 = utec_ficha_normalizar(null, true);
assertSameValue('', $r5['dados']['nome_social'], 'post nulo');

// --- mudança na saúde
$antes = utec_ficha_vazia();
$depois = $antes; $depois['nome_social'] = 'X';
assertTrue(!utec_ficha_saude_mudou($antes, $depois), 'mudanca fora da saude nao conta');
$depois['alergias'] = 'Dipirona';
assertTrue(utec_ficha_saude_mudou($antes, $depois), 'mudanca na saude conta');
$sem_saude = $r2['dados'];
assertTrue(!utec_ficha_saude_mudou($antes, $sem_saude), 'chaves ausentes nao contam');

// --- formatação
assertSameValue('Feminino', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), 'feminino'), 'rotulo opcao');
assertSameValue('Não informado', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), ''), 'rotulo vazio');
assertSameValue('Não informado', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), 'xyz'), 'rotulo desconhecido');
assertSameValue('(81) 99999-0000', utec_ficha_telefone_fmt('81999990000'), 'fmt celular');
assertSameValue('(81) 3333-0000', utec_ficha_telefone_fmt('8133330000'), 'fmt fixo');
assertSameValue('', utec_ficha_telefone_fmt('12'), 'fmt invalido');
assertSameValue('31/12/2027', utec_ficha_data_br('2027-12-31'), 'data br');
assertSameValue('', utec_ficha_data_br(''), 'data br vazia');


// --- telefone invalido gera erro (nao apaga em silencio)
$r4 = utec_ficha_normalizar(array('emergencia_telefone' => '123'), true);
assertSameValue(array('Telefone do contato de emergência inválido.'), $r4['erros'], 'erro telefone emergencia');
assertSameValue('', $r4['dados']['emergencia_telefone'], 'telefone invalido nao gravado');
$r5 = utec_ficha_normalizar(array('responsavel_telefone' => '9999'), true);
assertSameValue(array('Telefone do responsável inválido.'), $r5['erros'], 'erro telefone responsavel');
$r6 = utec_ficha_normalizar(array('responsavel_telefone' => '', 'emergencia_telefone' => '  '), true);
assertSameValue(array(), $r6['erros'], 'telefone vazio sem erro');

echo "OK\n";
