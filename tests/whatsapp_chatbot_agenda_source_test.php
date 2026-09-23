<?php
function assertFonteContem($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function assertFonteNaoContem($needle, $haystack, $label) {
    if (strpos($haystack, $needle) !== false) {
        fwrite(STDERR, $label . ' — trecho nao deveria existir: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerFonte($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) { fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL); exit(1); }
    return file_get_contents($path);
}

$model = lerFonte('application/models/Whatsapp_model.php');
assertFonteContem('function remarcar_agendamento_chatbot(', $model, 'model remarcar');
assertFonteContem('function cancelar_agendamento_chatbot(', $model, 'model cancelar');
assertFonteContem('FOR UPDATE', $model, 'model trava linhas');
assertFonteContem('verificar_horario(', $model, 'model revalida horario');
assertFonteContem("'chatbot_remarcado'", $model, 'log remarcado');
assertFonteContem("'chatbot_cancelado'", $model, 'log cancelado');
assertFonteContem('trans_begin()', $model, 'model usa transacao');
assertFonteContem('checkdate(', $model, 'model valida data real de calendario');
assertFonteNaoContem("NOT IN ('equipe_confirmado', 'equipe_cancelado')", $model, 'model exclui equipe_remarcado');
assertFonteContem("NOT IN ('equipe_confirmado', 'equipe_cancelado', 'equipe_remarcado')", $model, 'model exclusao atualizada');
assertFonteContem("status_confirmacao = 'pendente'", $model, 'model cancela confirmacoes pendentes do template antigo ao cancelar via chatbot');
assertFonteContem("'confirmacao', 'lembrete_paciente'", $model, 'model restringe o cancelamento de pendentes aos tipos de confirmacao e lembrete');

$atend = lerFonte('application/controllers/adm/Atendimento.php');
assertFonteContem("NOT IN ('equipe_confirmado', 'equipe_cancelado', 'equipe_remarcado')", $atend, 'agenda exclusao atualizada');

$notif = lerFonte('application/models/Notificacoes_model.php');
assertFonteContem('function criar_aviso_chatbot_agenda(', $notif, 'aviso interno chatbot agenda');
assertFonteContem('utec_notificacoes_mensagem_chatbot_agenda(', $notif, 'aviso usa mensagem pura');

$lib = lerFonte('application/libraries/Whatsapp_agendamento.php');
assertFonteContem("notificar_remarcacao_equipe_ativo", $lib, 'notificar_equipe respeita flag de remarcacao');
assertFonteContem("'equipe_remarcado'", $lib, 'notificar_equipe loga equipe_remarcado');

$cfg = lerFonte('application/config/whatsapp.php');
assertFonteContem("notificar_remarcacao_equipe_ativo", $cfg, 'flag na config');

echo "OK whatsapp_chatbot_agenda_source_test\n";
