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

assertSameValue('active', utec_whatsapp_normalizar_status_assinatura(' ACTIVE '), 'Status de assinatura deve ser normalizado.');

$politicaTrial = utec_whatsapp_politica_limite('trial', 2);
assertSameValue(true, $politicaTrial['allowed'], 'Trial com 2 envios ainda deve poder enviar.');
assertSameValue('quota_available', $politicaTrial['reason'], 'Trial abaixo do limite deve retornar quota disponivel.');
assertSameValue(3, $politicaTrial['limit'], 'Trial deve ter limite de 3 envios.');

$politicaTrialBloqueado = utec_whatsapp_politica_limite('trial', 3);
assertSameValue(false, $politicaTrialBloqueado['allowed'], 'Trial com 3 envios deve bloquear o 4o.');
assertSameValue('quota_reached', $politicaTrialBloqueado['reason'], 'Trial bloqueado deve retornar motivo de quota.');

$politicaStatusNaoAtivo = utec_whatsapp_politica_limite('past_due', 3);
assertSameValue(false, $politicaStatusNaoAtivo['allowed'], 'Status nao ativo com 3 envios deve bloquear.');

$politicaActive = utec_whatsapp_politica_limite('active', 999);
assertSameValue(true, $politicaActive['allowed'], 'Assinatura ativa deve ter envio ilimitado.');
assertSameValue(0, $politicaActive['limit'], 'Assinatura ativa deve retornar limite zero como ilimitado.');

$politicaSemAssinatura = utec_whatsapp_politica_limite('', 3);
assertSameValue(false, $politicaSemAssinatura['allowed'], 'Tenant sem assinatura com 3 envios deve bloquear.');

$resumoEnviado = utec_whatsapp_resumo_envio(['sent' => true, 'reason' => 'sent', 'wamid' => 'wamid.123']);
assertSameValue('success', $resumoEnviado['type'], 'Envio com sucesso deve gerar alerta success.');
assertSameValue('Solicitacao WhatsApp aceita pela Meta. A entrega sera atualizada pelo webhook. ID Meta: wamid.123', $resumoEnviado['message'], 'Mensagem de sucesso deve deixar claro que a entrega ainda sera confirmada.');

$resumoErro = utec_whatsapp_resumo_envio(['sent' => false, 'reason' => 'api_error', 'error' => 'Template não encontrado']);
assertSameValue('danger', $resumoErro['type'], 'Falha da API deve gerar alerta danger.');

$resumoConfig = utec_whatsapp_resumo_envio(['sent' => false, 'reason' => 'config_unavailable']);
assertSameValue('warning', $resumoConfig['type'], 'Configuração indisponível deve gerar alerta warning.');

$resumoQuota = utec_whatsapp_resumo_envio(['sent' => false, 'reason' => 'quota_reached']);
assertSameValue('warning', $resumoQuota['type'], 'Quota atingida deve gerar alerta warning.');

$componentes = utec_whatsapp_componentes_template([
    'id' => 77,
    'paciente_nome' => 'Maria',
    'tipo' => 'Consulta',
    'data_agenda' => '2026-09-01',
    'hora_agenda' => '14:30:00',
    'prestador_nome' => 'Dra. Ana',
], [
    'header_image_url' => 'https://utecnologia.com.br/img/logo-w.png',
]);
assertSameValue(4, count($componentes), 'Template com imagem e quick replies deve montar header, body e 2 botoes.');
assertSameValue('image', $componentes[0]['parameters'][0]['type'], 'Primeiro componente deve ser o header de imagem.');
assertSameValue('confirmar_agendamento:77', $componentes[2]['parameters'][0]['payload'], 'Payload do botao confirmar deve incluir o agendamento.');
assertSameValue('cancelar_agendamento:77', $componentes[3]['parameters'][0]['payload'], 'Payload do botao cancelar deve incluir o agendamento.');

echo "OK\n";
