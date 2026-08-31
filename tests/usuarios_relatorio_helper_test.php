<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/usuarios_relatorio_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . " esperado [" . $expected . "] mas recebeu [" . $actual . "]\n");
        exit(1);
    }
}

$prestador = array(
    'nivel' => 3,
    'profissao' => 'Clinico geral',
    'especialidade_nome' => 'Odontologia',
);
assertSameValue('Odontologia', utec_relatorio_resolve_atividade($prestador), 'atividade prestador');

$colaborador = array(
    'nivel' => 4,
    'profissao' => 'Atendente',
    'especialidade_nome' => 'Psicologia',
);
assertSameValue('Atendente', utec_relatorio_resolve_atividade($colaborador), 'atividade colaborador');

$semDados = array(
    'nivel' => 2,
    'profissao' => '',
    'especialidade_nome' => '',
);
assertSameValue('Nao informado', utec_relatorio_resolve_atividade($semDados), 'atividade vazia');

$trial = array(
    'tenant_status' => 1,
    'subscription_status' => 'trial',
);
assertSameValue('Trial', utec_relatorio_resolve_plano_status($trial), 'status trial');

$paid = array(
    'tenant_status' => 1,
    'subscription_status' => 'authorized',
);
assertSameValue('Pago', utec_relatorio_resolve_plano_status($paid), 'status pago');

$blocked = array(
    'tenant_status' => 0,
    'subscription_status' => 'authorized',
);
assertSameValue('Bloqueado', utec_relatorio_resolve_plano_status($blocked), 'status bloqueado');

$none = array(
    'tenant_status' => null,
    'subscription_status' => '',
);
assertSameValue('', utec_relatorio_resolve_plano_status($none), 'status vazio');

$semPlano = array(
    'tenant_status' => 1,
    'subscription_status' => 'pending',
);
assertSameValue('Sem plano', utec_relatorio_resolve_plano_status($semPlano), 'status sem plano');

assertSameValue('0', utec_relatorio_formatar_numero(0), 'contador zero');
assertSameValue('12', utec_relatorio_formatar_numero(12), 'contador inteiro');
assertSameValue(false, utec_relatorio_mostra_plano_por_nivel(4), 'atendente sem plano');
assertSameValue(true, utec_relatorio_mostra_plano_por_nivel(3), 'profissional com plano');
assertSameValue(true, utec_relatorio_tem_foto_usuario(array('img' => 'foto.jpg')), 'usuario com foto');
assertSameValue(false, utec_relatorio_tem_foto_usuario(array('img' => '')), 'usuario sem foto');
assertSameValue('P', utec_relatorio_inicial_usuario(array('nome' => 'Paciente Teste')), 'inicial paciente');
assertSameValue('?', utec_relatorio_inicial_usuario(array('nome' => '')), 'inicial vazia');

echo "OK\n";
