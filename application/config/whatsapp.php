<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$whatsapp_env_cron_token = getenv('WHATSAPP_CRON_TOKEN');

// Trocar o fallback por um token longo e aleatorio antes do deploy,
// ou definir a env var WHATSAPP_CRON_TOKEN no ambiente.
$config['cron_token'] = $whatsapp_env_cron_token ? $whatsapp_env_cron_token : 'TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY';
