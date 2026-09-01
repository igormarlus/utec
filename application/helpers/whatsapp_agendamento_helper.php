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

if (!function_exists('utec_whatsapp_perfil_tem_plano')) {
    function utec_whatsapp_perfil_tem_plano($perfil)
    {
        return in_array(trim((string)$perfil), ['admin', 'profissional'], true);
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
