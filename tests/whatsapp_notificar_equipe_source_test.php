<?php

function assertSource($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$config = file_get_contents(__DIR__ . '/../application/config/whatsapp.php');
$lib = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_agendamento.php');

// --- Task 3: config e biblioteca ---
assertSource(strpos($config, "getenv('WHATSAPP_NOTIFICAR_EQUIPE')") !== false, 'config/whatsapp.php deve ler WHATSAPP_NOTIFICAR_EQUIPE.');
assertSource(strpos($config, "\$config['notificar_equipe_ativo'] = !(") !== false, 'O flag notificar_equipe_ativo deve sair ligado por padrao (negacao do desligamento explicito).');

assertSource(strpos($lib, 'function notificar_equipe(') !== false, 'A biblioteca deve expor notificar_equipe().');
assertSource(strpos($lib, "notificar_equipe_ativo'") !== false, 'notificar_equipe deve checar o flag de ativacao.');
assertSource(strpos($lib, 'utec_whatsapp_template_equipe_nome(') !== false, 'notificar_equipe deve resolver o nome do template pela acao.');
assertSource(strpos($lib, 'cad.telefone AS cadastrado_por_telefone') !== false, 'O contexto deve trazer o telefone de quem cadastrou.');
assertSource(strpos($lib, 'utec_notificacoes_destinatarios_agendamento(') !== false, 'notificar_equipe deve reaproveitar a resolucao de destinatarios dos avisos internos.');
assertSource(strpos($lib, "'equipe_cancelado'") !== false && strpos($lib, "'equipe_confirmado'") !== false, 'notificar_equipe deve gravar os dois tipos de notificacao de equipe.');
assertSource(strpos($lib, "validar_quota_tenant(\$agendamento, \$telefone, \$tipoNotificacao)") !== false, 'notificar_equipe deve respeitar a cota do tenant.');
assertSource(strpos($lib, "log_message('warning'") === false, 'A biblioteca nao deve usar log_message(warning).');

echo "OK\n";
