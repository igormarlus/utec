<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$mercadopago_env_access_token = getenv('MERCADOPAGO_ACCESS_TOKEN');
$mercadopago_env_public_key = getenv('MERCADOPAGO_PUBLIC_KEY');
$mercadopago_env_webhook_secret = getenv('MERCADOPAGO_WEBHOOK_SECRET');
$mercadopago_env_base_url = getenv('APP_BASE_URL');

if(!$mercadopago_env_base_url){
	$mercadopago_env_base_url = 'https://utecnologia.com.br';
}

$config['mercadopago_access_token'] = $mercadopago_env_access_token ? $mercadopago_env_access_token : 'APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904';
$config['mercadopago_public_key'] = $mercadopago_env_public_key ? $mercadopago_env_public_key : 'APP_USR-cab577b4-78f7-46dd-9cfa-bd405a43d380';
$config['mercadopago_currency_id'] = 'BRL';
$config['mercadopago_webhook_secret'] = $mercadopago_env_webhook_secret ? $mercadopago_env_webhook_secret : '';
$config['mercadopago_back_url_success'] = rtrim($mercadopago_env_base_url, '/').'/adm/saas';
$config['mercadopago_back_url_pending'] = rtrim($mercadopago_env_base_url, '/').'/adm/saas';
$config['mercadopago_back_url_failure'] = rtrim($mercadopago_env_base_url, '/').'/adm/saas';
