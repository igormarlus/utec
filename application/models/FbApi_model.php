<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FbApi_model extends CI_Model {

    private $pixel_id;
    private $access_token;
    private $api_version;
    private $test_event_code;

    function __construct() {
        parent::__construct();
        $this->config->load('facebook');
        $this->pixel_id        = (string)($this->config->item('facebook_pixel_id')     ?: '');
        $this->access_token    = (string)($this->config->item('facebook_access_token') ?: '');
        $this->api_version     = (string)($this->config->item('facebook_graph_version') ?: 'v19.0');
        $this->test_event_code = (string)($this->config->item('facebook_test_event_code') ?: '');
    }

    /**
     * Envia um evento para a Conversions API da Meta.
     *
     * @param string $event_name  Nome do evento Meta (PageView, Lead, StartTrial, etc.)
     * @param array  $options     Dados do evento:
     *   - email        (string) E-mail do usuário — será hasheado com SHA-256
     *   - phone        (string) Telefone — será normalizado e hasheado
     *   - nome         (string) Nome completo — será separado em fn/ln e hasheado
     *   - event_id     (string) ID para deduplicação browser Pixel + CAPI
     *   - source_url   (string) URL da página de origem
     *   - custom_data  (array)  Dados customizados (value, currency, content_name, etc.)
     *
     * @return array|null  ['event_id' => string, 'response' => string] ou null se sem credenciais
     */
    public function send_event($event_name, $options = []) {
        if (!$this->pixel_id || !$this->access_token) {
            return null;
        }

        $ip         = $this->_get_real_ip();
        $ua         = isset($_SERVER['HTTP_USER_AGENT']) ? (string)$_SERVER['HTTP_USER_AGENT'] : '';
        $source_url = !empty($options['source_url']) ? (string)$options['source_url'] : $this->_current_url();

        $user_data = [
            'client_ip_address' => $ip,
            'client_user_agent' => $ua,
        ];

        // PII — sempre hasheados com SHA-256, formato lowercase/normalizado
        if (!empty($options['email'])) {
            $user_data['em'] = hash('sha256', strtolower(trim((string)$options['email'])));
        }

        if (!empty($options['phone'])) {
            $phone = preg_replace('/\D/', '', (string)$options['phone']);
            if (strlen($phone) >= 10) {
                if (strlen($phone) <= 11) {
                    $phone = '55' . $phone; // adiciona DDI Brasil se não tiver
                }
                $user_data['ph'] = hash('sha256', $phone);
            }
        }

        if (!empty($options['nome'])) {
            $parts = explode(' ', trim((string)$options['nome']), 2);
            $user_data['fn'] = hash('sha256', strtolower(trim($parts[0])));
            if (!empty($parts[1])) {
                $user_data['ln'] = hash('sha256', strtolower(trim($parts[1])));
            }
        }

        // Cookies do navegador para match — lidos automaticamente da requisição
        if (!empty($options['fbp'])) {
            $user_data['fbp'] = (string)$options['fbp'];
        } elseif (!empty($_COOKIE['_fbp'])) {
            $user_data['fbp'] = (string)$_COOKIE['_fbp'];
        }

        if (!empty($options['fbc'])) {
            $user_data['fbc'] = (string)$options['fbc'];
        } elseif (!empty($_COOKIE['_fbc'])) {
            $user_data['fbc'] = (string)$_COOKIE['_fbc'];
        }

        // event_id compartilhado com o browser Pixel para deduplicação
        $event_id = !empty($options['event_id'])
            ? (string)$options['event_id']
            : ('srv_' . time() . '_' . bin2hex(openssl_random_pseudo_bytes(4)));

        $event = [
            'event_name'       => $event_name,
            'event_time'       => time(),
            'event_id'         => $event_id,
            'event_source_url' => $source_url,
            'action_source'    => 'website',
            'user_data'        => $user_data,
        ];

        if (!empty($options['custom_data']) && is_array($options['custom_data'])) {
            $event['custom_data'] = $options['custom_data'];
        }

        $payload = [
            'data'         => [$event],
            'access_token' => $this->access_token,
        ];

        if ($this->test_event_code) {
            $payload['test_event_code'] = $this->test_event_code;
        }

        $api_url = "https://graph.facebook.com/{$this->api_version}/{$this->pixel_id}/events";
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $response   = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $log_response = $curl_error ? 'CURL_ERROR: ' . $curl_error : (string)$response;

        if ($this->db->table_exists('api_conv_fb')) {
            $this->db->insert('api_conv_fb', [
                'id_user'  => (int)$this->session->userdata('id'),
                'tipo'     => $event_name,
                'ip'       => $ip,
                'response' => substr($log_response, 0, 500),
            ]);
        }

        return ['event_id' => $event_id, 'response' => $log_response];
    }

    /** Retorna o Pixel ID para uso nas views (não é segredo) */
    public function get_pixel_id() {
        return $this->pixel_id;
    }

    /** Resolve o IP real considerando proxies e Cloudflare */
    private function _get_real_ip() {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /** Monta a URL atual da requisição */
    private function _current_url() {
        if (empty($_SERVER['HTTP_HOST'])) {
            return site_url();
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
    }

} // fecha FbApi_model
