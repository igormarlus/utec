<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->usuarios_model->verSession();
        $this->load->model('FbApi_model', 'fbapi_model');
        $this->padrao_model->indexador();
    }

    function Index()
    {
        $dd_user = $this->padrao_model->get_usuario_logado();

        // Pacientes não têm acesso ao calendário
        if ((int)$dd_user->nivel === 5) {
            show_error('Acesso negado.', 403);
            return;
        }

        $visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
        $visible_prestador_sql = $this->padrao_model->ids_to_sql_in($visible_prestador_ids);

        if ((int)$dd_user->nivel === 1) {
            $prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC");
        } else {
            $prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (" . $visible_prestador_sql . ") ORDER BY nome ASC");
        }

        $dados['dd']          = $dd_user;
        $dados['nivel']       = (int)$dd_user->nivel;
        $dados['prestadores'] = $prestadores;

        $this->load->view('adm/calendario/index', $dados);
    }
}
