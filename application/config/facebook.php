<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -----------------------------------------------------------------------
| META / FACEBOOK CONVERSIONS API
| -----------------------------------------------------------------------
| Prefira variáveis de ambiente em produção para não expor o access token.
|
|   FACEBOOK_PIXEL_ID          = ID do Pixel (público, pode ser exposto)
|   FACEBOOK_ACCESS_TOKEN      = Token da CAPI (SECRETO — não commitar)
|   FACEBOOK_TEST_EVENT_CODE   = Código de teste do Events Manager (opcional)
|   FACEBOOK_GRAPH_VERSION     = Versão da Graph API (ex: v19.0)
*/

$config['facebook_pixel_id']        = getenv('FACEBOOK_PIXEL_ID')        ?: '844919898162947';
$config['facebook_access_token']    = getenv('FACEBOOK_ACCESS_TOKEN')    ?: 'EAAdAdsxjCUIBRuDhg9C2vdebaGG69cH0rh4XOKTAlXtSX3BjO7OFnZC0ADjTtKIQen8dbLnJrdEv8gfYBtBemjXJ2vkJStuqsZAUFEkWYy3TcBthwj9XsEmZC6vGI1XvMoIkyG6Cw4dAYH5rnv6VrEOZAJlqXDbG1JMogp5V8twWcyHw3Mvlrxd5nime8gZDZD';
$config['facebook_graph_version']   = getenv('FACEBOOK_GRAPH_VERSION')   ?: 'v19.0';
$config['facebook_test_event_code'] = getenv('FACEBOOK_TEST_EVENT_CODE') ?: '';
