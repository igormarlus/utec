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

// --- Revisao final: regressoes especificas ---
assertSource(strpos($lib, "config->load('whatsapp'") !== false, 'O construtor deve carregar a config whatsapp (senao notificar_equipe_ativo le NULL/off).');
assertSource(strpos($lib, "'nao_aplicavel'") === false, 'notificar_equipe nao deve gravar status_confirmacao=nao_aplicavel (valor nunca escrito por outro caminho, risco de ENUM invalido).');
assertSource(strpos($lib, "field_exists('tipo_notificacao', 'whatsapp_notificacoes')") !== false, 'notificar_equipe deve ser no-op quando a coluna tipo_notificacao nao existir (instalacao sem a migracao do lembrete).');

$webhook = file_get_contents(__DIR__ . '/../application/controllers/Webhooks.php');

// --- Task 4: integracao no webhook ---
assertSource(strpos($webhook, 'notificar_equipe(') !== false, 'Webhooks.php deve chamar notificar_equipe apos os avisos internos.');
assertSource(strpos($webhook, "log_message('warning'") === false, 'Webhooks.php nao deve usar log_message(warning).');

// --- Revisao final: paciente responde antes da equipe ---
$posResponder = strpos($webhook, 'responder_interacao(');
$posNotificarEquipe = strpos($webhook, 'notificar_equipe(');
assertSource($posResponder !== false && $posNotificarEquipe !== false && $posResponder < $posNotificarEquipe, 'Webhooks.php deve responder ao paciente (responder_interacao) antes de notificar a equipe (notificar_equipe).');

$model = file_get_contents(__DIR__ . '/../application/models/Whatsapp_model.php');
$atendimento = file_get_contents(__DIR__ . '/../application/controllers/adm/Atendimento.php');

// --- Revisao final: Task 2 continua excluindo as linhas de equipe das consultas de "ultima resposta" ---
$exclusaoEquipe = "NOT IN ('equipe_confirmado', 'equipe_cancelado')";
assertSource(strpos($model, $exclusaoEquipe) !== false, 'Whatsapp_model.php deve excluir equipe_confirmado/equipe_cancelado das consultas de ultima resposta.');
assertSource(strpos($atendimento, $exclusaoEquipe) !== false, 'adm/Atendimento.php deve excluir equipe_confirmado/equipe_cancelado da consulta de status do WhatsApp na agenda.');

echo "OK\n";
