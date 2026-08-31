<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('whatsapp_agendamento'));
        $this->load->model('Whatsapp_model', 'whatsapp_model');
    }

    public function whatsapp()
    {
        if (strtoupper($this->input->method()) === 'GET') {
            $this->validar_whatsapp();
            return;
        }

        $this->receber_whatsapp();
    }

    protected function validar_whatsapp()
    {
        $config = $this->whatsapp_model->get_configuracao_ativa();
        $mode = isset($_GET['hub_mode']) ? (string)$_GET['hub_mode'] : '';
        $challenge = isset($_GET['hub_challenge']) ? (string)$_GET['hub_challenge'] : '';
        $verifyToken = isset($_GET['hub_verify_token']) ? (string)$_GET['hub_verify_token'] : '';
        $storedToken = trim((string)utec_whatsapp_read($config, 'verify_token', ''));

        if ($mode !== 'subscribe' || $challenge === '' || $storedToken === '' || !hash_equals($storedToken, $verifyToken)) {
            log_message('error', '[whatsapp_webhook] Validacao da Meta recusada: token ou challenge invalido.');
            $this->output->set_status_header(403);
            echo 'forbidden';
            return;
        }

        $this->output->set_content_type('text/plain');
        echo $challenge;
    }

    protected function receber_whatsapp()
    {
        $config = $this->whatsapp_model->get_configuracao_ativa();
        $raw = (string)file_get_contents('php://input');

        if (!$this->assinatura_valida($raw, $config)) {
            log_message('error', '[whatsapp_webhook] Assinatura POST invalida ou App Secret ausente.');
            $this->responder_json(['ok' => false], 403);
            return;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            log_message('error', '[whatsapp_webhook] Payload JSON invalido.');
            $this->responder_json(['ok' => false, 'message' => 'payload_invalido'], 400);
            return;
        }

        $eventos = utec_whatsapp_extrair_eventos_webhook($payload);
        foreach ($eventos as $evento) {
            if ($evento['delivery_status'] !== '') {
                $this->processar_status_entrega($evento);
            }
            if ($evento['action'] !== '') {
                $this->processar_resposta_agendamento($evento);
            }
        }

        $this->responder_json(['ok' => true]);
    }

    protected function assinatura_valida($raw, $config)
    {
        $secret = trim((string)utec_whatsapp_read($config, 'app_secret', ''));
        $signature = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])
            ? trim((string)$_SERVER['HTTP_X_HUB_SIGNATURE_256'])
            : '';

        if ($secret === '' || strpos($signature, 'sha256=') !== 0) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $raw, $secret);
        return hash_equals($expected, $signature);
    }

    protected function processar_status_entrega($evento)
    {
        $notificacao = $this->whatsapp_model->get_notificacao_por_wamid($evento['wamid']);
        if (!$notificacao) {
            log_message('warning', '[whatsapp_webhook] Status sem notificacao correspondente. wamid='.$evento['wamid']);
            return;
        }

        $atualizado = $this->whatsapp_model->atualizar_status_envio_notificacao(
            (int)$notificacao->id,
            $evento['delivery_status'],
            $evento['error_detail'],
            $evento['event_at']
        );
        if (!$atualizado) {
            log_message('error', '[whatsapp_webhook] Nao foi possivel atualizar status. id='.(int)$notificacao->id);
        }
    }

    protected function processar_resposta_agendamento($evento)
    {
        $notificacao = $this->whatsapp_model->resolver_notificacao_webhook($evento['wamid'], $evento['id_agendamento']);
        if (!$notificacao) {
            log_message('warning', '[whatsapp_webhook] Resposta sem notificacao correspondente. wamid='.$evento['wamid']);
            return;
        }

        if ($evento['action'] === 'cancelar') {
            if (!$this->whatsapp_model->cancelar_notificacao_e_agendamento((int)$notificacao->id, (int)$notificacao->id_agendamento)) {
                log_message('error', '[whatsapp_webhook] Nao foi possivel processar cancelamento. id='.(int)$notificacao->id);
            }
            return;
        }

        if (!$this->whatsapp_model->atualizar_confirmacao_notificacao((int)$notificacao->id, 'confirmado')) {
            log_message('error', '[whatsapp_webhook] Nao foi possivel atualizar confirmacao. id='.(int)$notificacao->id);
        }
    }

    protected function responder_json($data, $status = 200)
    {
        $this->output->set_status_header((int)$status);
        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }
}
