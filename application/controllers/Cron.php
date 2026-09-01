<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp_agendamento');
        $this->load->model('Whatsapp_model', 'whatsapp_model');
        $this->config->load('whatsapp', TRUE);
    }

    public function lembrete_whatsapp()
    {
        $tokenEsperado = trim((string)$this->config->item('cron_token', 'whatsapp'));
        $tokenRecebido = trim((string)$this->input->get('token'));

        if ($tokenEsperado === '' || $tokenEsperado === 'TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY' || !hash_equals($tokenEsperado, $tokenRecebido)) {
            if ($tokenRecebido === '') {
                log_message('info', '[cron_lembrete_whatsapp] Requisicao sem token (scanner ou warm-up).');
            } else {
                log_message('error', '[cron_lembrete_whatsapp] Token invalido ou nao configurado.');
            }
            $this->output
                ->set_status_header(403)
                ->set_content_type('text/plain')
                ->set_output('forbidden');
            return;
        }

        $config = $this->whatsapp_model->get_configuracao_ativa();
        if (!utec_whatsapp_config_ativa($config)) {
            $this->responder_json([
                'ok' => true,
                'motivo' => 'config_inativa',
                'elegiveis_paciente' => 0, 'enviados_paciente' => 0, 'falhas_paciente' => 0,
                'elegiveis_profissional' => 0, 'enviados_profissional' => 0, 'falhas_profissional' => 0,
            ]);
            return;
        }

        $this->load->library('whatsapp_agendamento');
        $intervalo = utec_whatsapp_lembrete_intervalo(time());

        $resumo = [
            'ok' => true,
            'elegiveis_paciente' => 0, 'enviados_paciente' => 0, 'falhas_paciente' => 0,
            'elegiveis_profissional' => 0, 'enviados_profissional' => 0, 'falhas_profissional' => 0,
        ];

        $tipos = ['lembrete_paciente' => 'paciente'];
        if ($this->config->item('lembrete_profissional_ativo', 'whatsapp')) {
            $tipos['lembrete_profissional'] = 'profissional';
        }

        foreach ($tipos as $tipo => $sufixo) {
            $ids = $this->whatsapp_model->get_agendamentos_para_lembrete($tipo, $intervalo['inicio'], $intervalo['fim']);
            $enviados = 0;
            $falhas = 0;
            foreach ($ids as $row) {
                $envio = $this->whatsapp_agendamento->notificar_lembrete((int)$row->id, $tipo);
                if (!empty($envio['sent'])) {
                    $enviados++;
                } else {
                    $falhas++;
                }
            }
            $resumo['elegiveis_'.$sufixo] = count($ids);
            $resumo['enviados_'.$sufixo] = $enviados;
            $resumo['falhas_'.$sufixo] = $falhas;
        }

        log_message('info', '[cron_lembrete_whatsapp] '.json_encode($resumo));
        $this->responder_json($resumo);
    }

    protected function responder_json($data, $status = 200)
    {
        $this->output
            ->set_status_header((int)$status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
