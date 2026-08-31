<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url', 'whatsapp_agendamento'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->load->model('Whatsapp_model', 'whatsapp_model');
        $this->usuarios_model->verSession();

        if ((int)$this->session->userdata('nivel') !== 1) {
            show_error('Acesso restrito ao administrador.', 403);
        }
    }

    public function index()
    {
        $dados['config'] = $this->whatsapp_model->get_configuracao_atual();
        $dados['flash_ok'] = $this->session->flashdata('ok');
        $dados['flash_error'] = $this->session->flashdata('error');
        $dados['whatsapp_disponivel'] = utec_whatsapp_config_ativa($this->whatsapp_model->get_configuracao_ativa());
        $dados['whatsapp_log_tabela'] = $this->whatsapp_model->tabela_log_existe();
        $this->load->view('adm/whatsapp/index', $dados);
    }

    public function salvar()
    {
        try {
            $this->whatsapp_model->salvar_configuracao([
                'id' => (int)$this->input->post('id'),
                'nome_conexao' => $this->input->post('nome_conexao', true),
                'numero_remetente' => $this->input->post('numero_remetente', true),
                'phone_number_id' => $this->input->post('phone_number_id', true),
                'waba_id' => $this->input->post('waba_id', true),
                'access_token' => $this->input->post('access_token', true),
                'app_secret' => $this->input->post('app_secret', true),
                'verify_token' => $this->input->post('verify_token', true),
                'template_name' => $this->input->post('template_name', true),
                'template_lang' => $this->input->post('template_lang', true),
                'status' => $this->input->post('status', true),
            ]);
            $this->session->set_flashdata('ok', 'Configuracao do WhatsApp salva com sucesso.');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Falha ao salvar configuracao: '.$e->getMessage());
        }

        redirect('adm/whatsapp');
    }
}
