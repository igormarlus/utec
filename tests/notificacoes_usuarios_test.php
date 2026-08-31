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

assertSameValue([12], utec_notificacoes_destinatarios_agendamento(12, 12), 'Deve deduplicar criador e prestador iguais.');
assertSameValue([12, 25], utec_notificacoes_destinatarios_agendamento(12, 25), 'Deve manter a ordem de criador e prestador distintos.');
assertSameValue([25], utec_notificacoes_destinatarios_agendamento(0, 25), 'Deve ignorar destinatario sem ID positivo.');

assertSameValue(
    'whatsapp_agendamento_confirmado',
    utec_notificacoes_tipo_resposta_agendamento('confirmar'),
    'Confirmacao deve usar o tipo de notificacao correto.'
);
assertSameValue(
    'whatsapp_agendamento_cancelado',
    utec_notificacoes_tipo_resposta_agendamento('cancelar'),
    'Cancelamento deve usar o tipo de notificacao correto.'
);

$mensagemConfirmacao = utec_notificacoes_mensagem_resposta_agendamento('Maria Silva', 'confirmar');
assertSameValue(true, strpos($mensagemConfirmacao, 'Maria Silva') !== false, 'Mensagem de confirmacao deve conter o nome do paciente.');
assertSameValue(true, strpos(strtolower($mensagemConfirmacao), 'confirm') !== false, 'Mensagem de confirmacao deve informar a acao.');

$mensagemCancelamento = utec_notificacoes_mensagem_resposta_agendamento('Maria Silva', 'cancelar');
assertSameValue(true, strpos($mensagemCancelamento, 'Maria Silva') !== false, 'Mensagem de cancelamento deve conter o nome do paciente.');
assertSameValue(true, strpos(strtolower($mensagemCancelamento), 'cancel') !== false, 'Mensagem de cancelamento deve informar a acao.');

assertSameValue('Confirmado via WhatsApp', utec_whatsapp_rotulo_confirmacao('confirmado'), 'Rotulo de confirmacao para a agenda e o prontuario.');
assertSameValue('Cancelado via WhatsApp', utec_whatsapp_rotulo_confirmacao('cancelado'), 'Rotulo de cancelamento para a agenda e o prontuario.');
assertSameValue('Sem retorno WhatsApp', utec_whatsapp_rotulo_confirmacao(''), 'Sem resposta do paciente deve exibir rotulo neutro.');

$controllerNotificacoes = file_get_contents(__DIR__ . '/../application/controllers/adm/Notificacoes.php');
assertSameValue(true, strpos($controllerNotificacoes, "verSession(") !== false, 'Notificacoes deve validar a sessao.');
assertSameValue(true, strpos($controllerNotificacoes, "abrir_para_usuario(\$id, ") !== false, 'Notificacoes deve abrir a linha pelo destinatario.');
assertSameValue(true, strpos($controllerNotificacoes, "session->userdata('id')") !== false, 'Notificacoes deve usar o usuario logado como destinatario.');
assertSameValue(true, strpos($controllerNotificacoes, 'redirect(') !== false, 'Notificacoes deve redirecionar para a URL armazenada.');

echo "OK\n";
