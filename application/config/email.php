<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| SMTP UTecnologia Saúde
| Host: mail.utecnologia.com.br  (cPanel padrão — ajuste se necessário)
| Porta 465 SSL ou 587 TLS
*/
$config['protocol']   = 'smtp';
$config['smtp_host']  = getenv('SMTP_HOST')  ?: 'mail.utecnologia.com.br';
$config['smtp_port']  = (int)(getenv('SMTP_PORT') ?: 465);
$config['smtp_crypto']= getenv('SMTP_CRYPTO') ?: 'ssl';
$config['smtp_user']  = getenv('SMTP_USER')  ?: 'suporte@utecnologia.com.br';
$config['smtp_pass']  = getenv('SMTP_PASS')  ?: 'N2009Lab';
$config['smtp_timeout'] = 10;

$config['mailtype']   = 'html';
$config['charset']    = 'utf-8';
$config['newline']    = "\r\n";
$config['wordwrap']   = FALSE;
$config['wrapchars']  = 998;
$config['validate']   = TRUE;
