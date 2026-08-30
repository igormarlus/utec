<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -----------------------------------------------------------------------
| FONTES DE TRÁFEGO DE IA — usado por Padrao_model::detect_ai_source()
| -----------------------------------------------------------------------
| 'domains' casam contra o HTTP_REFERER (strpos, lowercase).
| 'utm'     casam contra utm_source (igualdade exata, lowercase).
|
| NÃO incluir buscadores (google.com, bing.com, x.com) em 'domains':
| tráfego de busca tradicional não é IA. Eles só contam via UTM explícita.
*/

$config['ai_sources'] = array(
    'chatgpt'    => array('domains' => array('chatgpt.com', 'openai.com'),         'utm' => array('chatgpt.com', 'chatgpt')),
    'gemini'     => array('domains' => array('gemini.google.com'),                 'utm' => array('gemini', 'gemini.google.com')),
    'claude'     => array('domains' => array('claude.ai'),                         'utm' => array('claude', 'claude.ai')),
    'perplexity' => array('domains' => array('perplexity.ai'),                     'utm' => array('perplexity', 'perplexity.ai')),
    'copilot'    => array('domains' => array('copilot.microsoft.com'),             'utm' => array('copilot')),
    'deepseek'   => array('domains' => array('chat.deepseek.com', 'deepseek.com'), 'utm' => array('deepseek')),
    'grok'       => array('domains' => array('grok.com'),                          'utm' => array('grok')),
);

/* utm_medium que sempre conta como IA (fonte 'outros' se nenhuma fonte específica casar) */
$config['ai_medium_flags'] = array('ai-assistant', 'ai_assistant', 'ai');
