<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('utec_whatsapp_read')) {
    function utec_whatsapp_read($source, $key, $default = '')
    {
        if (is_array($source)) {
            return isset($source[$key]) ? $source[$key] : $default;
        }
        if (is_object($source)) {
            return isset($source->$key) ? $source->$key : $default;
        }
        return $default;
    }
}

if (!function_exists('utec_whatsapp_normalizar_numero')) {
    function utec_whatsapp_normalizar_numero($numero)
    {
        return preg_replace('/\D+/', '', (string)$numero);
    }
}

if (!function_exists('utec_whatsapp_variantes_numero_chatbot')) {
    function utec_whatsapp_variantes_numero_chatbot($numero)
    {
        $numero = utec_whatsapp_normalizar_numero($numero);
        if ($numero === '') {
            return [];
        }

        $variantes = [$numero => true];
        if (substr($numero, 0, 2) !== '55' || !in_array(strlen($numero), [12, 13], true)) {
            return array_map('strval', array_keys($variantes));
        }
        $local = substr($numero, 2);

        $alternativo = '';
        if (strlen($local) === 10) {
            $alternativo = substr($local, 0, 2).'9'.substr($local, 2);
        } elseif (strlen($local) === 11 && substr($local, 2, 1) === '9') {
            $alternativo = substr($local, 0, 2).substr($local, 3);
        }

        foreach ([$local, $alternativo] as $candidato) {
            if ($candidato === '') {
                continue;
            }
            $variantes[$candidato] = true;
            $variantes['55'.$candidato] = true;
        }

        return array_map('strval', array_keys($variantes));
    }
}

if (!function_exists('utec_whatsapp_perfil_por_nivel')) {
    function utec_whatsapp_perfil_por_nivel($nivel)
    {
        switch ((int)$nivel) {
            case 5:
                return 'paciente';
            case 3:
                return 'profissional';
            case 4:
                return 'atendente';
            case 1:
            case 2:
                return 'admin';
            default:
                return '';
        }
    }
}

if (!function_exists('utec_whatsapp_resolver_perfil_chatbot_unico')) {
    function utec_whatsapp_resolver_perfil_chatbot_unico($usuarios)
    {
        if (!is_array($usuarios) || count($usuarios) !== 1) {
            return [];
        }

        $usuario = $usuarios[0];
        $perfil = utec_whatsapp_perfil_por_nivel(utec_whatsapp_read($usuario, 'nivel'));
        if ($perfil === '') {
            return [];
        }

        return [
            'id_usuario' => (int)utec_whatsapp_read($usuario, 'id'),
            'tenant_id' => (int)utec_whatsapp_read($usuario, 'tenant_id'),
            'perfil' => $perfil,
        ];
    }
}

if (!function_exists('utec_whatsapp_perfil_tem_plano')) {
    function utec_whatsapp_perfil_tem_plano($perfil)
    {
        return in_array(trim((string)$perfil), ['admin', 'profissional'], true);
    }
}

if (!function_exists('utec_whatsapp_status_chatbot')) {
    function utec_whatsapp_status_chatbot($agendamento)
    {
        $status = strtolower(trim((string)utec_whatsapp_read($agendamento, 'status', '')));
        $statusWhatsapp = strtolower(trim((string)utec_whatsapp_read($agendamento, 'status_whatsapp', '')));

        if ($status === '3' || strpos($status, 'cancel') !== false || strpos($statusWhatsapp, 'cancel') !== false) {
            return '❌ cancelado';
        }
        if (strpos($statusWhatsapp, 'confirm') !== false) {
            return '✅ confirmado';
        }

        return '⏳ pendente';
    }
}

if (!function_exists('utec_whatsapp_chatbot_url_suporte')) {
    function utec_whatsapp_chatbot_url_suporte()
    {
        return 'https://wa.me/5581983276882';
    }
}

if (!function_exists('utec_whatsapp_chatbot_texto_suporte')) {
    function utec_whatsapp_chatbot_texto_suporte()
    {
        return 'Fale com o dev.';
    }
}

if (!function_exists('utec_notificacoes_tipo_solicitacao_chatbot')) {
    function utec_notificacoes_tipo_solicitacao_chatbot($acao)
    {
        $acao = strtolower(trim((string)$acao));
        return in_array($acao, ['remarcacao', 'cancelamento'], true)
            ? 'whatsapp_chatbot_'.$acao
            : '';
    }
}

if (!function_exists('utec_whatsapp_normalizar_status_assinatura')) {
    function utec_whatsapp_normalizar_status_assinatura($status)
    {
        return strtolower(trim((string)$status));
    }
}

if (!function_exists('utec_whatsapp_politica_limite')) {
    function utec_whatsapp_politica_limite($subscription_status, $used)
    {
        $status = utec_whatsapp_normalizar_status_assinatura($subscription_status);
        $used = (int)$used;

        if ($status === 'active') {
            return ['allowed' => true, 'reason' => 'active_unlimited', 'limit' => 0, 'used' => $used];
        }

        $limit = 3;
        return [
            'allowed' => $used < $limit,
            'reason' => $used < $limit ? 'quota_available' : 'quota_reached',
            'limit' => $limit,
            'used' => $used,
        ];
    }
}

if (!function_exists('utec_whatsapp_checkbox_marcado')) {
    function utec_whatsapp_checkbox_marcado($post)
    {
        $valor = utec_whatsapp_read($post, 'enviar_whatsapp_confirmacao', '');
        return in_array((string)$valor, ['1', 'on', 'true'], true);
    }
}

if (!function_exists('utec_whatsapp_config_ativa')) {
    function utec_whatsapp_config_ativa($config)
    {
        if (!$config) {
            return false;
        }

        $required = [
            'phone_number_id',
            'access_token',
            'template_name',
            'template_lang',
        ];

        if ((int)utec_whatsapp_read($config, 'status', 0) !== 1) {
            return false;
        }

        foreach ($required as $field) {
            if (trim((string)utec_whatsapp_read($config, $field, '')) === '') {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('utec_whatsapp_formatar_data_br')) {
    function utec_whatsapp_formatar_data_br($data)
    {
        $data = trim((string)$data);
        if ($data === '') {
            return '';
        }

        $timestamp = strtotime($data);
        if (!$timestamp) {
            return $data;
        }

        return date('d/m/Y', $timestamp);
    }
}

if (!function_exists('utec_whatsapp_formatar_hora_br')) {
    function utec_whatsapp_formatar_hora_br($hora)
    {
        $hora = trim((string)$hora);
        if ($hora === '') {
            return '';
        }

        if (preg_match('/^\d{2}:\d{2}/', $hora)) {
            return substr($hora, 0, 5);
        }

        $timestamp = strtotime($hora);
        if (!$timestamp) {
            return $hora;
        }

        return date('H:i', $timestamp);
    }
}

if (!function_exists('utec_whatsapp_resumo_envio')) {
    function utec_whatsapp_resumo_envio($resultado)
    {
        $reason = trim((string)utec_whatsapp_read($resultado, 'reason', ''));
        $error = trim((string)utec_whatsapp_read($resultado, 'error', ''));
        $wamid = trim((string)utec_whatsapp_read($resultado, 'wamid', ''));
        $sent = (bool)utec_whatsapp_read($resultado, 'sent', false);

        if ($sent) {
            $message = 'Solicitacao WhatsApp aceita pela Meta. A entrega sera atualizada pelo webhook.';
            if ($wamid !== '') {
                $message .= ' ID Meta: '.$wamid;
            }
            return ['type' => 'success', 'message' => $message];
        }

        switch ($reason) {
            case 'unchecked':
                return ['type' => 'warning', 'message' => 'Envio por WhatsApp desmarcado no agendamento.'];
            case 'config_unavailable':
                return ['type' => 'warning', 'message' => 'Configuracao do WhatsApp ausente, incompleta ou inativa.'];
            case 'invalid_phone':
                return ['type' => 'warning', 'message' => 'Paciente sem telefone valido para WhatsApp.'];
            case 'quota_reached':
                return ['type' => 'warning', 'message' => $error !== '' ? $error : 'Limite de 3 envios do plano trial/free atingido. Contrate um plano para liberar novos disparos.'];
            case 'agendamento_not_found':
                return ['type' => 'danger', 'message' => 'Nao foi possivel localizar o agendamento para disparo do WhatsApp.'];
            case 'invalid_agendamento':
                return ['type' => 'danger', 'message' => 'ID de agendamento invalido para envio do WhatsApp.'];
            case 'api_error':
                return ['type' => 'danger', 'message' => $error !== '' ? 'Falha ao enviar WhatsApp: '.$error : 'Falha ao enviar WhatsApp pela API da Meta.'];
            default:
                return ['type' => 'warning', 'message' => $error !== '' ? $error : 'O disparo do WhatsApp nao foi concluido.'];
        }
    }
}

if (!function_exists('utec_whatsapp_payload_botao')) {
    function utec_whatsapp_payload_botao($acao, $id_agendamento)
    {
        $acao = trim((string)$acao);
        $id_agendamento = (int)$id_agendamento;
        return $acao.'_agendamento:'.$id_agendamento;
    }
}

if (!function_exists('utec_whatsapp_texto_resposta_agendamento')) {
    function utec_whatsapp_texto_resposta_agendamento($acao)
    {
        $acao = strtolower(trim((string)$acao));

        if ($acao === 'confirmar') {
            return 'Recebemos sua confirmacao. Sua consulta permanece agendada. Em caso de necessidade, entre em contato com a clinica.';
        }
        if ($acao === 'cancelar') {
            return 'Recebemos sua solicitacao de cancelamento. Nossa equipe esta a disposicao para auxiliar em um novo agendamento.';
        }

        return '';
    }
}

if (!function_exists('utec_whatsapp_payload_texto')) {
    function utec_whatsapp_payload_texto($telefone, $texto)
    {
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => trim((string)$telefone),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => trim((string)$texto),
            ],
        ];
    }
}

if (!function_exists('utec_whatsapp_truncar_texto')) {
    function utec_whatsapp_truncar_texto($texto, $limite)
    {
        $texto = (string)$texto;
        $limite = (int)$limite;

        if ($texto === '' || $limite <= 0) {
            return '';
        }

        if (@preg_match('//u', $texto) !== 1) {
            return '';
        }

        if (function_exists('mb_substr')) {
            return mb_substr($texto, 0, $limite, 'UTF-8');
        }

        if (function_exists('iconv_substr')) {
            $truncado = @iconv_substr($texto, 0, $limite, 'UTF-8');
            if ($truncado !== false) {
                return $truncado;
            }
        }

        $caracteres = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY);
        return is_array($caracteres) ? implode('', array_slice($caracteres, 0, $limite)) : '';
    }
}

if (!function_exists('utec_whatsapp_texto_scalar')) {
    function utec_whatsapp_texto_scalar($valor)
    {
        if (!is_scalar($valor) && $valor !== null) {
            return '';
        }

        return trim((string)$valor);
    }
}

if (!function_exists('utec_whatsapp_payload_lista')) {
    function utec_whatsapp_payload_lista($telefone, $titulo, $corpo, $texto_botao, $secoes)
    {
        $sections = [];
        $secoes = is_array($secoes) ? $secoes : [];
        $secoes = array_slice($secoes, 0, 10);
        $titulo = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar($titulo), 60);
        $corpo = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar($corpo), 1024);
        $texto_botao = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar($texto_botao), 20);
        if ($corpo === '' || $texto_botao === '') {
            return [];
        }

        $totalLinhas = 0;

        foreach ($secoes as $secao) {
            if (count($sections) >= 10 || $totalLinhas >= 10) {
                break;
            }

            $rows = [];
            $itens = utec_whatsapp_read($secao, 'rows', []);
            $itens = is_array($itens) ? $itens : [];
            foreach ($itens as $item) {
                if ($totalLinhas >= 10) {
                    break;
                }

                $id = utec_whatsapp_texto_scalar(utec_whatsapp_read($item, 'id', ''));
                $tituloItem = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar(utec_whatsapp_read($item, 'title', '')), 24);
                if ($id === '' || strlen($id) > 200 || $tituloItem === '') {
                    continue;
                }

                $row = ['id' => $id, 'title' => $tituloItem];
                $descricao = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar(utec_whatsapp_read($item, 'description', '')), 72);
                if ($descricao !== '') {
                    $row['description'] = $descricao;
                }
                $rows[] = $row;
                $totalLinhas++;
            }

            if (empty($rows)) {
                continue;
            }

            $section = ['rows' => $rows];
            $tituloSecao = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar(utec_whatsapp_read($secao, 'title', '')), 24);
            if ($tituloSecao !== '') {
                $section['title'] = $tituloSecao;
            }
            $sections[] = $section;
        }

        if (empty($sections)) {
            return [];
        }

        $interactive = [
            'type' => 'list',
            'body' => ['text' => $corpo],
            'action' => [
                'button' => $texto_botao,
                'sections' => $sections,
            ],
        ];
        if ($titulo !== '') {
            $interactive['header'] = ['type' => 'text', 'text' => $titulo];
        }

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => trim((string)$telefone),
            'type' => 'interactive',
            'interactive' => $interactive,
        ];
    }
}

if (!function_exists('utec_whatsapp_payload_botoes')) {
    function utec_whatsapp_payload_botoes($telefone, $titulo, $corpo, $botoes)
    {
        $buttons = [];
        $botoes = is_array($botoes) ? $botoes : [];
        $titulo = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar($titulo), 60);
        $corpo = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar($corpo), 1024);
        if ($corpo === '') {
            return [];
        }

        foreach ($botoes as $botao) {
            $id = utec_whatsapp_texto_scalar(utec_whatsapp_read($botao, 'id', ''));
            $tituloBotao = utec_whatsapp_truncar_texto(utec_whatsapp_texto_scalar(utec_whatsapp_read($botao, 'title', '')), 20);
            if ($id === '' || strlen($id) > 256 || $tituloBotao === '') {
                continue;
            }

            $buttons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $id,
                    'title' => utec_whatsapp_truncar_texto($tituloBotao, 20),
                ],
            ];
            if (count($buttons) === 3) {
                break;
            }
        }

        if (empty($buttons)) {
            return [];
        }

        $interactive = [
            'type' => 'button',
            'body' => ['text' => $corpo],
            'action' => ['buttons' => $buttons],
        ];
        if ($titulo !== '') {
            $interactive['header'] = ['type' => 'text', 'text' => $titulo];
        }

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => trim((string)$telefone),
            'type' => 'interactive',
            'interactive' => $interactive,
        ];
    }
}

if (!function_exists('utec_notificacoes_destinatarios_agendamento')) {
    function utec_notificacoes_destinatarios_agendamento($id_criador, $id_prestador)
    {
        $destinatarios = [];

        foreach ([(int)$id_criador, (int)$id_prestador] as $idUsuario) {
            if ($idUsuario > 0 && !in_array($idUsuario, $destinatarios, true)) {
                $destinatarios[] = $idUsuario;
            }
        }

        return $destinatarios;
    }
}

if (!function_exists('utec_notificacoes_tipo_resposta_agendamento')) {
    function utec_notificacoes_tipo_resposta_agendamento($acao)
    {
        $acao = strtolower(trim((string)$acao));

        if ($acao === 'confirmar') {
            return 'whatsapp_agendamento_confirmado';
        }

        if ($acao === 'cancelar') {
            return 'whatsapp_agendamento_cancelado';
        }

        return '';
    }
}

if (!function_exists('utec_notificacoes_mensagem_resposta_agendamento')) {
    function utec_notificacoes_mensagem_resposta_agendamento($paciente_nome, $acao)
    {
        $paciente_nome = trim((string)$paciente_nome);
        $paciente_nome = $paciente_nome !== '' ? $paciente_nome : 'O paciente';
        $acao = strtolower(trim((string)$acao));

        if ($acao === 'confirmar') {
            return $paciente_nome . ' confirmou o agendamento pelo WhatsApp.';
        }

        if ($acao === 'cancelar') {
            return $paciente_nome . ' cancelou o agendamento pelo WhatsApp.';
        }

        return '';
    }
}

if (!function_exists('utec_whatsapp_rotulo_confirmacao')) {
    function utec_whatsapp_rotulo_confirmacao($status)
    {
        $status = strtolower(trim((string)$status));

        if ($status === 'confirmado') {
            return 'Confirmado via WhatsApp';
        }

        if ($status === 'cancelado') {
            return 'Cancelado via WhatsApp';
        }

        return 'Sem retorno WhatsApp';
    }
}

if (!function_exists('utec_whatsapp_extrair_evento_webhook')) {
    function utec_whatsapp_extrair_evento_webhook($payload)
    {
        $eventos = utec_whatsapp_extrair_eventos_webhook($payload);
        return !empty($eventos) ? $eventos[0] : utec_whatsapp_evento_webhook_vazio();
    }
}

if (!function_exists('utec_whatsapp_extrair_eventos_webhook')) {
    function utec_whatsapp_extrair_eventos_webhook($payload)
    {
        $eventos = [];
        $entries = isset($payload['entry']) && is_array($payload['entry']) ? $payload['entry'] : [];

        foreach ($entries as $entry) {
            $changes = isset($entry['changes']) && is_array($entry['changes']) ? $entry['changes'] : [];
            foreach ($changes as $change) {
                $value = isset($change['value']) && is_array($change['value']) ? $change['value'] : [];
                $statuses = isset($value['statuses']) && is_array($value['statuses']) ? $value['statuses'] : [];
                foreach ($statuses as $status) {
                    $evento = utec_whatsapp_evento_webhook_vazio();
                    $evento['wamid'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($status, 'id', ''));
                    $evento['delivery_status'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($status, 'status', ''));
                    $evento['event_at'] = utec_whatsapp_data_evento_webhook(utec_whatsapp_read($status, 'timestamp', ''));
                    $evento['error_detail'] = utec_whatsapp_detalhe_erro_webhook(utec_whatsapp_read($status, 'errors', []));
                    $eventos[] = $evento;
                }

                $messages = isset($value['messages']) && is_array($value['messages']) ? $value['messages'] : [];
                foreach ($messages as $mensagem) {
                    $interactive = utec_whatsapp_read($mensagem, 'interactive', []);
                    $interactive = is_array($interactive) ? $interactive : [];
                    $listReply = utec_whatsapp_read($interactive, 'list_reply', []);
                    $listReply = is_array($listReply) ? $listReply : [];
                    $buttonReply = utec_whatsapp_read($interactive, 'button_reply', []);
                    $buttonReply = is_array($buttonReply) ? $buttonReply : [];
                    $button = utec_whatsapp_read($mensagem, 'button', []);
                    $button = is_array($button) ? $button : [];
                    $context = utec_whatsapp_read($mensagem, 'context', []);
                    $context = is_array($context) ? $context : [];
                    $text = utec_whatsapp_read($mensagem, 'text', []);
                    $text = is_array($text) ? $text : [];

                    $buttonId = utec_whatsapp_texto_scalar(utec_whatsapp_read($listReply, 'id', ''));
                    if ($buttonId === '') {
                        $buttonId = utec_whatsapp_texto_scalar(utec_whatsapp_read($buttonReply, 'id', ''));
                    }
                    if ($buttonId === '') {
                        $buttonId = utec_whatsapp_texto_scalar(utec_whatsapp_read($button, 'payload', ''));
                    }
                    $evento = utec_whatsapp_evento_webhook_vazio();
                    $evento['payload'] = $buttonId;
                    $evento['wamid'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($context, 'id', ''));
                    $evento['message_id'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($mensagem, 'id', ''));
                    $evento['from'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($mensagem, 'from', ''));
                    $evento['message_type'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($mensagem, 'type', ''));
                    $evento['text'] = utec_whatsapp_texto_scalar(utec_whatsapp_read($text, 'body', ''));
                    $evento['event_at'] = utec_whatsapp_data_evento_webhook(utec_whatsapp_read($mensagem, 'timestamp', ''));

                    if (preg_match('/^(confirmar|cancelar)_agendamento:(\d+)$/', $buttonId, $matches)) {
                        $evento['action'] = $matches[1];
                        $evento['id_agendamento'] = (int)$matches[2];
                    }

                    $eventos[] = $evento;
                }
            }
        }

        return $eventos;
    }
}

if (!function_exists('utec_whatsapp_evento_webhook_vazio')) {
    function utec_whatsapp_evento_webhook_vazio()
    {
        return [
            'action' => '',
            'id_agendamento' => 0,
            'wamid' => '',
            'payload' => '',
            'message_id' => '',
            'from' => '',
            'message_type' => '',
            'text' => '',
            'delivery_status' => '',
            'error_detail' => '',
            'event_at' => null,
        ];
    }
}

if (!function_exists('utec_whatsapp_data_evento_webhook')) {
    function utec_whatsapp_data_evento_webhook($timestamp)
    {
        $timestamp = utec_whatsapp_texto_scalar($timestamp);
        return ctype_digit($timestamp) ? gmdate('Y-m-d H:i:s', (int)$timestamp) : null;
    }
}

if (!function_exists('utec_whatsapp_detalhe_erro_webhook')) {
    function utec_whatsapp_detalhe_erro_webhook($errors)
    {
        $error = isset($errors[0]) && is_array($errors[0]) ? $errors[0] : [];
        if (empty($error)) {
            return '';
        }

        $parts = [];
        $code = utec_whatsapp_texto_scalar(utec_whatsapp_read($error, 'code', ''));
        $title = utec_whatsapp_texto_scalar(utec_whatsapp_read($error, 'title', ''));
        $message = utec_whatsapp_texto_scalar(utec_whatsapp_read($error, 'message', ''));
        $details = utec_whatsapp_texto_scalar(utec_whatsapp_read(utec_whatsapp_read($error, 'error_data', []), 'details', ''));
        if ($code !== '' || $title !== '') {
            $parts[] = trim($code.($code !== '' && $title !== '' ? ': ' : '').$title);
        }
        if ($message !== '') {
            $parts[] = $message;
        }
        if ($details !== '') {
            $parts[] = $details;
        }

        return implode(' - ', $parts);
    }
}

if (!function_exists('utec_whatsapp_status_envio_meta')) {
    function utec_whatsapp_status_envio_meta($status)
    {
        $status = strtolower(trim((string)$status));
        $map = [
            'sent' => 'enviado',
            'delivered' => 'entregue',
            'read' => 'lido',
            'failed' => 'erro',
            'deleted' => 'apagado',
        ];

        return isset($map[$status]) ? $map[$status] : '';
    }
}

if (!function_exists('utec_whatsapp_envio_consume_quota')) {
    function utec_whatsapp_envio_consume_quota($status_envio, $wamid)
    {
        $status_envio = trim((string)$status_envio);
        $wamid = trim((string)$wamid);
        return $wamid !== '' && in_array($status_envio, ['enviado', 'entregue', 'lido', 'erro', 'apagado'], true);
    }
}

if (!function_exists('utec_whatsapp_header_image_url')) {
    function utec_whatsapp_header_image_url($config = null)
    {
        $configUrl = trim((string)utec_whatsapp_read($config, 'header_image_url', ''));
        if ($configUrl !== '') {
            return $configUrl;
        }

        if (function_exists('base_url')) {
            return rtrim((string)base_url(), '/').'/img/logo-w.png';
        }

        return 'https://utecnologia.com.br/img/logo-w.png';
    }
}

if (!function_exists('utec_whatsapp_componentes_template')) {
    function utec_whatsapp_componentes_template($agendamento, $config = null)
    {
        $agendamentoId = (int)utec_whatsapp_read($agendamento, 'id', 0);
        $componentes = [];
        $headerImageUrl = utec_whatsapp_header_image_url($config);

        if ($headerImageUrl !== '') {
            $componentes[] = [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'image',
                        'image' => [
                            'link' => $headerImageUrl,
                        ],
                    ],
                ],
            ];
        }

        $componentes[] = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($agendamento, 'paciente_nome', 'Paciente'))],
                ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($agendamento, 'tipo', 'Consulta'))],
                ['type' => 'text', 'text' => utec_whatsapp_formatar_data_br(utec_whatsapp_read($agendamento, 'data_agenda', ''))],
                ['type' => 'text', 'text' => utec_whatsapp_formatar_hora_br(utec_whatsapp_read($agendamento, 'hora_agenda', ''))],
                ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($agendamento, 'prestador_nome', 'Profissional'))],
            ],
        ];

        $componentes[] = [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => '0',
            'parameters' => [
                [
                    'type' => 'payload',
                    'payload' => utec_whatsapp_payload_botao('confirmar', $agendamentoId),
                ],
            ],
        ];

        $componentes[] = [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => '1',
            'parameters' => [
                [
                    'type' => 'payload',
                    'payload' => utec_whatsapp_payload_botao('cancelar', $agendamentoId),
                ],
            ],
        ];

        return $componentes;
    }
}

if (!function_exists('utec_whatsapp_lembrete_tipos')) {
    function utec_whatsapp_lembrete_tipos()
    {
        return ['lembrete_paciente', 'lembrete_profissional'];
    }
}

if (!function_exists('utec_whatsapp_lembrete_tipo_valido')) {
    function utec_whatsapp_lembrete_tipo_valido($tipo)
    {
        return in_array((string)$tipo, utec_whatsapp_lembrete_tipos(), true);
    }
}

if (!function_exists('utec_whatsapp_lembrete_intervalo')) {
    function utec_whatsapp_lembrete_intervalo($agora_ts, $horas = 7)
    {
        $agora_ts = (int)$agora_ts;
        $horas = (int)$horas;
        return [
            'inicio' => date('Y-m-d H:i:s', $agora_ts),
            'fim' => date('Y-m-d H:i:s', $agora_ts + ($horas * 3600)),
        ];
    }
}

if (!function_exists('utec_whatsapp_template_equipe_nome')) {
    function utec_whatsapp_template_equipe_nome($acao)
    {
        $acao = strtolower(trim((string)$acao));

        if ($acao === 'confirmar') {
            return 'agendamento_confirmado_equipe';
        }
        if ($acao === 'cancelar') {
            return 'agendamento_cancelado_equipe';
        }
        if ($acao === 'remarcar') {
            return 'agendamento_remarcado_equipe';
        }

        return '';
    }
}

if (!function_exists('utec_whatsapp_componentes_equipe_template')) {
    function utec_whatsapp_componentes_equipe_template($contexto, $acao = '')
    {
        $acao = strtolower(trim((string)$acao));

        // Temporario: agendamento_confirmado_equipe continua aprovado na Meta com a
        // versao antiga de 5 variaveis (paciente, tipo, data, hora, profissional).
        // Remover este branch quando o template for editado/reaprovado com a mesma
        // estrutura de 3 variaveis do agendamento_cancelado_equipe.
        if ($acao === 'confirmar') {
            return [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'paciente_nome', 'Paciente'))],
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'tipo', 'Consulta'))],
                        ['type' => 'text', 'text' => utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_agenda', ''))],
                        ['type' => 'text', 'text' => utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_agenda', ''))],
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'prestador_nome', 'Profissional'))],
                    ],
                ],
            ];
        }

        if ($acao === 'remarcar') {
            $anterior = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_anterior', ''))
                . ' as ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_anterior', ''));
            $nova = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_agenda', ''))
                . ' as ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_agenda', ''));
            return [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'paciente_nome', 'Paciente'))],
                        ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'prestador_nome', 'Profissional'))],
                        ['type' => 'text', 'text' => $anterior],
                        ['type' => 'text', 'text' => $nova],
                    ],
                ],
            ];
        }

        $dataBr = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_agenda', ''));
        $horaBr = utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_agenda', ''));
        $dataHora = trim($dataBr . ' as ' . $horaBr, ' as ');
        if ($dataBr !== '' && $horaBr !== '') {
            $dataHora = $dataBr . ' as ' . $horaBr;
        }

        return [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'paciente_nome', 'Paciente'))],
                    ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'prestador_nome', 'Profissional'))],
                    ['type' => 'text', 'text' => $dataHora],
                ],
            ],
        ];
    }
}

/*
 * Remarcacao / cancelamento automaticos pelo chatbot (perfil paciente).
 * Funcoes puras: ids dos cliques, regra de antecedencia, paginacao e textos.
 */

if (!function_exists('utec_whatsapp_agenda_antecedencia_horas')) {
    function utec_whatsapp_agenda_antecedencia_horas()
    {
        return 24;
    }
}

if (!function_exists('utec_whatsapp_agenda_minimo_datetime')) {
    function utec_whatsapp_agenda_minimo_datetime($agora_ts)
    {
        $ts = (int)$agora_ts + utec_whatsapp_agenda_antecedencia_horas() * 3600;
        $ts = (int)(ceil($ts / 60) * 60);
        return date('Y-m-d H:i', $ts);
    }
}

if (!function_exists('utec_whatsapp_agenda_antecedencia_ok')) {
    function utec_whatsapp_agenda_antecedencia_ok($data, $hora, $agora_ts)
    {
        $data = substr(trim((string)$data), 0, 10);
        $hora = substr(trim((string)$hora), 0, 5);
        if ($data === '' || $hora === '') {
            return false;
        }
        $ts = strtotime($data . ' ' . $hora);
        if ($ts === false) {
            return false;
        }
        return $ts - (int)$agora_ts >= utec_whatsapp_agenda_antecedencia_horas() * 3600;
    }
}

if (!function_exists('utec_whatsapp_agenda_ymd')) {
    function utec_whatsapp_agenda_ymd($data)
    {
        return str_replace('-', '', substr(trim((string)$data), 0, 10));
    }
}

if (!function_exists('utec_whatsapp_agenda_hi')) {
    function utec_whatsapp_agenda_hi($hora)
    {
        return str_replace(':', '', substr(trim((string)$hora), 0, 5));
    }
}

if (!function_exists('utec_whatsapp_agenda_id_dia')) {
    function utec_whatsapp_agenda_id_dia($id, $data)
    {
        return 'rem:' . (int)$id . ':d:' . utec_whatsapp_agenda_ymd($data);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_pagina')) {
    function utec_whatsapp_agenda_id_pagina($id, $data, $pagina)
    {
        return 'rem:' . (int)$id . ':p:' . utec_whatsapp_agenda_ymd($data) . ':' . (int)$pagina;
    }
}

if (!function_exists('utec_whatsapp_agenda_id_hora')) {
    function utec_whatsapp_agenda_id_hora($id, $data, $hora)
    {
        return 'rem:' . (int)$id . ':h:' . utec_whatsapp_agenda_ymd($data) . utec_whatsapp_agenda_hi($hora);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_confirmar_remarcacao')) {
    function utec_whatsapp_agenda_id_confirmar_remarcacao($id, $data, $hora)
    {
        return 'rem:' . (int)$id . ':ok:' . utec_whatsapp_agenda_ymd($data) . utec_whatsapp_agenda_hi($hora);
    }
}

if (!function_exists('utec_whatsapp_agenda_id_outros_dias')) {
    function utec_whatsapp_agenda_id_outros_dias($id)
    {
        return 'rem:' . (int)$id . ':dias';
    }
}

if (!function_exists('utec_whatsapp_agenda_id_sem_motivo')) {
    function utec_whatsapp_agenda_id_sem_motivo($id)
    {
        return 'can:' . (int)$id . ':sem_motivo';
    }
}

if (!function_exists('utec_whatsapp_agenda_id_confirmar_cancelamento')) {
    function utec_whatsapp_agenda_id_confirmar_cancelamento($id)
    {
        return 'can:' . (int)$id . ':ok';
    }
}

if (!function_exists('utec_whatsapp_agenda_data_de_ymd')) {
    function utec_whatsapp_agenda_data_de_ymd($ymd)
    {
        if (!preg_match('/^(\d{4})(\d{2})(\d{2})$/', (string)$ymd, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
            return '';
        }
        return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
}

if (!function_exists('utec_whatsapp_agenda_hora_de_hi')) {
    function utec_whatsapp_agenda_hora_de_hi($hi)
    {
        if (!preg_match('/^(\d{2})(\d{2})$/', (string)$hi, $m) || (int)$m[1] > 23 || (int)$m[2] > 59) {
            return '';
        }
        return $m[1] . ':' . $m[2];
    }
}

if (!function_exists('utec_whatsapp_agenda_parse_id')) {
    function utec_whatsapp_agenda_parse_id($payload)
    {
        $p = trim((string)$payload);
        $base = ['fluxo' => '', 'acao' => '', 'id_agendamento' => 0, 'data' => '', 'hora' => '', 'pagina' => 0];

        if (preg_match('/^rem:(\d+):d:(\d{8})$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'dia', 'id_agendamento' => (int)$m[1], 'data' => utec_whatsapp_agenda_data_de_ymd($m[2])]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '') ? $r : null;
        }
        if (preg_match('/^rem:(\d+):p:(\d{8}):(\d{1,2})$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'pagina', 'id_agendamento' => (int)$m[1], 'data' => utec_whatsapp_agenda_data_de_ymd($m[2]), 'pagina' => (int)$m[3]]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '' && $r['pagina'] >= 1) ? $r : null;
        }
        if (preg_match('/^rem:(\d+):(h|ok):(\d{8})(\d{4})$/', $p, $m)) {
            $r = array_merge($base, [
                'fluxo' => 'remarcar',
                'acao' => $m[2] === 'h' ? 'hora' : 'confirmar',
                'id_agendamento' => (int)$m[1],
                'data' => utec_whatsapp_agenda_data_de_ymd($m[3]),
                'hora' => utec_whatsapp_agenda_hora_de_hi($m[4]),
            ]);
            return ($r['id_agendamento'] > 0 && $r['data'] !== '' && $r['hora'] !== '') ? $r : null;
        }
        if (preg_match('/^rem:(\d+):dias$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'remarcar', 'acao' => 'dias', 'id_agendamento' => (int)$m[1]]);
            return $r['id_agendamento'] > 0 ? $r : null;
        }
        if (preg_match('/^can:(\d+):(sem_motivo|ok)$/', $p, $m)) {
            $r = array_merge($base, ['fluxo' => 'cancelar', 'acao' => $m[2] === 'ok' ? 'confirmar' : 'sem_motivo', 'id_agendamento' => (int)$m[1]]);
            return $r['id_agendamento'] > 0 ? $r : null;
        }
        return null;
    }
}

if (!function_exists('utec_whatsapp_agenda_paginar_horarios')) {
    function utec_whatsapp_agenda_paginar_horarios($horas, $pagina)
    {
        $horas = array_values((array)$horas);
        $pagina = max(1, (int)$pagina);
        $offset = 0;
        for ($p = 1; $p < $pagina; $p++) {
            if (count($horas) - $offset <= 10) {
                return ['itens' => [], 'tem_mais' => false];
            }
            $offset += 9;
        }
        if (count($horas) - $offset <= 10) {
            return ['itens' => array_slice($horas, $offset), 'tem_mais' => false];
        }
        return ['itens' => array_slice($horas, $offset, 9), 'tem_mais' => true];
    }
}

if (!function_exists('utec_whatsapp_agenda_rotulo_dia')) {
    function utec_whatsapp_agenda_rotulo_dia($data)
    {
        $ts = strtotime(substr(trim((string)$data), 0, 10));
        if ($ts === false) {
            return '';
        }
        $nomes = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        return $nomes[(int)date('w', $ts)] . ' ' . date('d/m', $ts);
    }
}

if (!function_exists('utec_whatsapp_agenda_quando')) {
    function utec_whatsapp_agenda_quando($data, $hora, $prestador)
    {
        $texto = utec_whatsapp_agenda_rotulo_dia($data) . ' às ' . substr(trim((string)$hora), 0, 5);
        $prestador = trim((string)$prestador);
        return $prestador !== '' ? $texto . ' com ' . $prestador : $texto;
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_confirmar_remarcacao')) {
    function utec_whatsapp_agenda_texto_confirmar_remarcacao($data, $hora, $prestador)
    {
        return 'Remarcar para ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '?';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_remarcado')) {
    function utec_whatsapp_agenda_texto_remarcado($data, $hora, $prestador)
    {
        return 'Consulta remarcada para ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '.';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_confirmar_cancelamento')) {
    function utec_whatsapp_agenda_texto_confirmar_cancelamento($data, $hora, $prestador)
    {
        return 'Cancelar a consulta de ' . utec_whatsapp_agenda_quando($data, $hora, $prestador) . '?';
    }
}

if (!function_exists('utec_whatsapp_agenda_texto_fallback')) {
    function utec_whatsapp_agenda_texto_fallback($motivo)
    {
        $textos = [
            'prazo' => 'Faltam menos de 24 horas para a consulta, então a equipe vai analisar seu pedido.',
            'sem_grade' => 'Este profissional ainda não tem horários disponíveis para remarcação pelo WhatsApp.',
            'sem_vaga' => 'Não há horários livres nos próximos 30 dias.',
        ];
        $motivo = trim((string)$motivo);
        return isset($textos[$motivo]) ? $textos[$motivo] : '';
    }
}

if (!function_exists('utec_notificacoes_tipo_chatbot_agenda')) {
    function utec_notificacoes_tipo_chatbot_agenda($acao)
    {
        $acao = strtolower(trim((string)$acao));
        if ($acao === 'remarcar') {
            return 'whatsapp_chatbot_remarcado';
        }
        if ($acao === 'cancelar') {
            return 'whatsapp_chatbot_cancelado';
        }
        return '';
    }
}

if (!function_exists('utec_notificacoes_mensagem_chatbot_agenda')) {
    function utec_notificacoes_mensagem_chatbot_agenda($acao, $paciente_nome, $dados)
    {
        $nome = trim((string)$paciente_nome);
        $nome = $nome !== '' ? $nome : 'O paciente';
        $anterior = utec_whatsapp_formatar_data_br(utec_whatsapp_read($dados, 'data_anterior', ''))
            . ' às ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($dados, 'hora_anterior', ''));
        if (strtolower(trim((string)$acao)) === 'remarcar') {
            $nova = utec_whatsapp_formatar_data_br(utec_whatsapp_read($dados, 'data_nova', ''))
                . ' às ' . utec_whatsapp_formatar_hora_br(utec_whatsapp_read($dados, 'hora_nova', ''));
            return $nome . ' remarcou a consulta de ' . $anterior . ' para ' . $nova . ' pelo WhatsApp.';
        }
        $motivo = trim((string)utec_whatsapp_read($dados, 'motivo', ''));
        $sufixo = $motivo !== '' ? ' Motivo: ' . utec_whatsapp_truncar_texto($motivo, 300) : ' Sem motivo informado.';
        return $nome . ' cancelou a consulta de ' . $anterior . ' pelo WhatsApp.' . $sufixo;
    }
}
