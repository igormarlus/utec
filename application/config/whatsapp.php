<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$whatsapp_env_cron_token = getenv('WHATSAPP_CRON_TOKEN');

// Fallback usado quando a env var WHATSAPP_CRON_TOKEN nao esta definida no ambiente.
$config['cron_token'] = $whatsapp_env_cron_token ? $whatsapp_env_cron_token : 'notwa10230901marlusti';

$whatsapp_env_lembrete_prof = getenv('WHATSAPP_LEMBRETE_PROFISSIONAL');
// Lane do lembrete ao profissional. Manter FALSE ate o template dedicado
// 'lembrete_consulta_profissional' (sem botoes) ser aprovado na Meta.
$config['lembrete_profissional_ativo'] = ($whatsapp_env_lembrete_prof === '1' || $whatsapp_env_lembrete_prof === 'true');

$whatsapp_env_notificar_equipe = getenv('WHATSAPP_NOTIFICAR_EQUIPE');
// Notificacao WhatsApp a profissional/atendente apos o paciente confirmar ou
// cancelar. Liga por padrao; desligar so com WHATSAPP_NOTIFICAR_EQUIPE=0 (ou
// =false) no ambiente.
$config['notificar_equipe_ativo'] = !($whatsapp_env_notificar_equipe === '0' || $whatsapp_env_notificar_equipe === 'false');

$whatsapp_env_remarcacao_equipe = getenv('WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE');
// Aviso WhatsApp a profissional/atendente quando o paciente remarca pelo chatbot.
// Manter FALSE ate o template 'agendamento_remarcado_equipe' ser aprovado na Meta
// (ver docs/whatsapp-remarcacao-template-pendente.md). Ligar com =1 no ambiente.
$config['notificar_remarcacao_equipe_ativo'] = ($whatsapp_env_remarcacao_equipe === '1' || $whatsapp_env_remarcacao_equipe === 'true');
