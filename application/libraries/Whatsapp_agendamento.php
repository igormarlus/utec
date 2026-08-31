<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_agendamento {

    protected $CI;
    protected $api_version = 'v20.0';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('whatsapp_agendamento');
        $this->CI->load->model('Whatsapp_model', 'whatsapp_model');
        $this->CI->load->model('padrao_model');
    }

    public function is_disponivel()
    {
        return utec_whatsapp_config_ativa($this->CI->whatsapp_model->get_configuracao_ativa());
    }

    public function notificar_agendamento($id_agendamento, $enviar = true)
    {
        $id_agendamento = (int)$id_agendamento;
        if ($id_agendamento <= 0) {
            return ['sent' => false, 'reason' => 'invalid_agendamento'];
        }

        if (!$enviar) {
            return ['sent' => false, 'reason' => 'unchecked'];
        }

        $config = $this->CI->whatsapp_model->get_configuracao_ativa();
        if (!utec_whatsapp_config_ativa($config)) {
            $this->CI->whatsapp_model->registrar_log([
                'id_agendamento' => $id_agendamento,
                'status_envio' => 'ignorado',
                'erro_detalhe' => 'Configuracao do WhatsApp ausente, incompleta ou inativa.',
                'status_confirmacao' => 'nao_enviado',
            ]);
            return ['sent' => false, 'reason' => 'config_unavailable'];
        }

        $agendamento = $this->buscar_contexto_agendamento($id_agendamento);
        if (!$agendamento) {
            return ['sent' => false, 'reason' => 'agendamento_not_found'];
        }

        $telefone = $this->normalizar_destino(isset($agendamento->paciente_telefone) ? $agendamento->paciente_telefone : '');
        if ($telefone === '') {
            $this->CI->whatsapp_model->registrar_log([
                'id_agendamento' => $id_agendamento,
                'tenant_id' => (int)$agendamento->tenant_id,
                'status_envio' => 'erro',
                'erro_detalhe' => 'Paciente sem telefone valido para WhatsApp.',
                'status_confirmacao' => 'nao_enviado',
            ]);
            return ['sent' => false, 'reason' => 'invalid_phone'];
        }

        $payload = $this->montar_payload($config, $agendamento, $telefone);
        $response = $this->enviar_payload($config, $payload);

        $wamid = '';
        $erro = '';
        $status_envio = 'erro';

        if ($response['ok']) {
            $wamid = $response['wamid'];
            $status_envio = 'enviado';
        } else {
            $erro = $response['error'];
        }

        $this->CI->whatsapp_model->registrar_log([
            'id_agendamento' => $id_agendamento,
            'tenant_id' => (int)$agendamento->tenant_id,
            'telefone_destino' => $telefone,
            'wamid' => $wamid,
            'status_envio' => $status_envio,
            'erro_detalhe' => $erro,
            'status_confirmacao' => $response['ok'] ? 'pendente' : 'nao_enviado',
        ]);

        return [
            'sent' => $response['ok'],
            'reason' => $response['ok'] ? 'sent' : 'api_error',
            'wamid' => $wamid,
            'error' => $erro,
        ];
    }

    protected function buscar_contexto_agendamento($id_agendamento)
    {
        $tenantSelect = $this->CI->db->field_exists('tenant_id', 'usuarios')
            ? "COALESCE(p.tenant_id, pr.tenant_id, cad.tenant_id, 0) AS tenant_id"
            : "0 AS tenant_id";

        $qr = $this->CI->db->query(
            "SELECT a.id, a.data_agenda, a.hora_agenda, a.tipo,
                    p.nome AS paciente_nome, p.telefone AS paciente_telefone,
                    pr.nome AS prestador_nome,
                    cad.nome AS cadastrado_por_nome,
                    {$tenantSelect}
             FROM agendamentos a
             LEFT JOIN usuarios p ON p.id = a.id_paciente
             LEFT JOIN usuarios pr ON pr.id = a.id_prestador
             LEFT JOIN usuarios cad ON cad.id = a.id_user
             WHERE a.id = ".(int)$id_agendamento."
             LIMIT 1"
        );

        return $qr->num_rows() ? $qr->row() : null;
    }

    protected function normalizar_destino($telefone)
    {
        $telefone = utec_whatsapp_normalizar_numero($telefone);
        if ($telefone === '') {
            return '';
        }
        if (strlen($telefone) >= 10 && strlen($telefone) <= 11) {
            $telefone = '55'.$telefone;
        }
        return strlen($telefone) >= 12 ? $telefone : '';
    }

    protected function montar_payload($config, $agendamento, $telefone)
    {
        return [
            'messaging_product' => 'whatsapp',
            'to' => $telefone,
            'type' => 'template',
            'template' => [
                'name' => trim((string)$config->template_name),
                'language' => [
                    'code' => trim((string)$config->template_lang),
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => trim((string)$agendamento->paciente_nome)],
                            ['type' => 'text', 'text' => trim((string)$agendamento->tipo)],
                            ['type' => 'text', 'text' => utec_whatsapp_formatar_data_br($agendamento->data_agenda)],
                            ['type' => 'text', 'text' => utec_whatsapp_formatar_hora_br($agendamento->hora_agenda)],
                            ['type' => 'text', 'text' => trim((string)$agendamento->prestador_nome)],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function enviar_payload($config, $payload)
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'cURL indisponivel no servidor.', 'wamid' => ''];
        }

        $url = 'https://graph.facebook.com/'.$this->api_version.'/'.trim((string)$config->phone_number_id).'/messages';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.trim((string)$config->access_token),
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            return ['ok' => false, 'error' => 'CURL_ERROR: '.$curlError, 'wamid' => ''];
        }

        $decoded = json_decode((string)$raw, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($decoded['messages'][0]['id'])) {
            return ['ok' => true, 'error' => '', 'wamid' => (string)$decoded['messages'][0]['id']];
        }

        $error = isset($decoded['error']['message']) ? (string)$decoded['error']['message'] : ('HTTP '.$httpCode.' - resposta invalida da API.');
        return ['ok' => false, 'error' => $error, 'wamid' => ''];
    }
}
