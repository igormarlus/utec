<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacoes extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->load->model('Notificacoes_model', 'notificacoes_model');
        $this->usuarios_model->verSession();
    }

    public function abrir($id = 0)
    {
        $id = (int)$id;
        $id_usuario = (int)$this->session->userdata('id');
        $destino = base_url().'adm/atendimento';

        if ($id > 0 && $id_usuario > 0) {
            $notificacao = $this->notificacoes_model->abrir_para_usuario($id, $id_usuario);
            if ($notificacao && isset($notificacao->url) && trim((string)$notificacao->url) !== '') {
                $url = trim((string)$notificacao->url);
                $destino = (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)
                    ? $url
                    : base_url().ltrim($url, '/');
            }
        }

        redirect($destino);
    }
}
