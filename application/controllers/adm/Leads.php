<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Padrao_model');
        $this->load->model('adm/Leads_model');
        $this->load->model('adm/Usuarios_model');
        $this->Usuarios_model->verSession();

        $usuario = $this->Padrao_model->get_usuario_logado();
        // Apenas nível 1 (admin) acessa
        if ($usuario->nivel != 1) {
            redirect('adm/atendimento');
        }
    }

    public function index() {
        $usuario  = $this->Padrao_model->get_usuario_logado();
        $filtros  = [
            'status'       => $this->input->get('status'),
            'especialidade'=> $this->input->get('especialidade'),
            'cidade'       => $this->input->get('cidade'),
            'tipo'         => $this->input->get('tipo'),
            'busca'        => $this->input->get('busca'),
        ];

        $por_pagina = 30;
        $pagina     = max(1, (int)$this->input->get('pagina'));
        $offset     = ($pagina - 1) * $por_pagina;

        $leads  = $this->Leads_model->get_leads($filtros, $por_pagina, $offset);
        $total  = $this->Leads_model->count_leads($filtros);
        $stats  = $this->Leads_model->get_stats();
        $especialidades = $this->Leads_model->get_especialidades();
        $cidades        = $this->Leads_model->get_cidades();

        $data = [
            'usuario'       => $usuario,
            'leads'         => $leads,
            'total'         => $total,
            'por_pagina'    => $por_pagina,
            'pagina'        => $pagina,
            'total_paginas' => ceil($total / $por_pagina),
            'filtros'       => $filtros,
            'stats'         => $stats,
            'especialidades'=> $especialidades,
            'cidades'       => $cidades,
        ];

        $this->load->view('adm/leads/index.php', $data);
    }

    public function atualizar_status() {
        if ($this->input->post('id')) {
            $id          = (int)$this->input->post('id');
            $status      = $this->input->post('status');
            $observacoes = $this->input->post('observacoes');

            $status_validos = ['pendente', 'contatado', 'contatado_form', 'interessado', 'descartado', 'sem_form'];
            if (!in_array($status, $status_validos)) {
                echo json_encode(['ok' => false, 'msg' => 'Status inválido']);
                return;
            }

            $this->Leads_model->atualizar_status($id, $status, $observacoes);
            echo json_encode(['ok' => true]);
        }
    }
}
