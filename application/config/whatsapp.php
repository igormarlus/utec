<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$whatsapp_env_cron_token = getenv('WHATSAPP_CRON_TOKEN');

// Trocar o fallback por um token longo e aleatorio antes do deploy,
// ou definir a env var WHATSAPP_CRON_TOKEN no ambiente.
$config['cron_token'] = $whatsapp_env_cron_token ? $whatsapp_env_cron_token : 'TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY';

$whatsapp_env_lembrete_prof = getenv('WHATSAPP_LEMBRETE_PROFISSIONAL');
// Lane do lembrete ao profissional. Manter FALSE ate o template dedicado
// 'lembrete_consulta_profissional' (sem botoes) ser aprovado na Meta.
$config['lembrete_profissional_ativo'] = ($whatsapp_env_lembrete_prof === '1' || $whatsapp_env_lembrete_prof === 'true');
