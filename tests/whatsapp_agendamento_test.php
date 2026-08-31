<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$configAtiva = [
    'status' => 1,
    'phone_number_id' => '123456',
    'access_token' => 'token',
    'template_name' => 'confirmacao_consulta',
    'template_lang' => 'pt_BR',
];

$configInativa = $configAtiva;
$configInativa['status'] = 0;

$configParcial = $configAtiva;
$configParcial['phone_number_id'] = '';

assertSameValue(true, utec_whatsapp_config_ativa($configAtiva), 'Configuracao ativa e completa deve estar disponivel.');
assertSameValue(false, utec_whatsapp_config_ativa($configInativa), 'Configuracao inativa nao deve disparar.');
assertSameValue(false, utec_whatsapp_config_ativa($configParcial), 'Configuracao incompleta nao deve disparar.');

assertSameValue(true, utec_whatsapp_checkbox_marcado(['enviar_whatsapp_confirmacao' => '1']), 'Checkbox marcado deve retornar true.');
assertSameValue(false, utec_whatsapp_checkbox_marcado([]), 'Checkbox ausente deve retornar false.');

assertSameValue('5581999999999', utec_whatsapp_normalizar_numero('+55 (81) 99999-9999'), 'Numero deve ficar apenas com digitos.');
assertSameValue('', utec_whatsapp_normalizar_numero(''), 'Numero vazio deve continuar vazio.');

$resumoEnviado = utec_whatsapp_resumo_envio(['sent' => true, 'reason' => 'sent', 'wamid' => 'wamid.123']);
assertSameValue('success', $resumoEnviado['type'], 'Envio com sucesso deve gerar alerta success.');

$resumoErro = utec_whatsapp_resumo_envio(['sent' => false, 'reason' => 'api_error', 'error' => 'Template não encontrado']);
assertSameValue('danger', $resumoErro['type'], 'Falha da API deve gerar alerta danger.');

$resumoConfig = utec_whatsapp_resumo_envio(['sent' => false, 'reason' => 'config_unavailable']);
assertSameValue('warning', $resumoConfig['type'], 'Configuração indisponível deve gerar alerta warning.');

echo "OK\n";
