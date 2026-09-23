<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horarios extends CI_Controller {

    private $usuario;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url', 'disponibilidade'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->load->model('Disponibilidade_model', 'disponibilidade_model');
        $this->usuarios_model->verSession();

        $this->usuario = $this->padrao_model->get_usuario_logado();
        $nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
        if ($nivel < 1 || $nivel > 4) {
            show_error('Acesso restrito.', 403);
        }
    }

    private function nivel()
    {
        return (int)$this->usuario->nivel;
    }

    private function prestadores_visiveis()
    {
        if ($this->nivel() === 3) {
            return $this->db->query("SELECT id, nome FROM usuarios WHERE id = ".(int)$this->usuario->id." LIMIT 1")->result();
        }
        if ($this->nivel() === 1) {
            return $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC")->result();
        }
        $ids = $this->padrao_model->get_visible_prestador_ids($this->usuario);
        if (empty($ids)) {
            return array();
        }
        return $this->db->query(
            "SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (".$this->padrao_model->ids_to_sql_in($ids).") ORDER BY nome ASC"
        )->result();
    }

    private function ids_visiveis()
    {
        $ids = array();
        foreach ($this->prestadores_visiveis() as $p) {
            $ids[] = (int)$p->id;
        }
        return $ids;
    }

    private function pode_ver($id_prestador)
    {
        $id_prestador = (int)$id_prestador;
        return $id_prestador > 0 && in_array($id_prestador, $this->ids_visiveis(), true);
    }

    private function pode_editar($id_prestador)
    {
        $id_prestador = (int)$id_prestador;
        switch ($this->nivel()) {
            case 1:
            case 2:
                return $this->pode_ver($id_prestador);
            case 3:
                return $id_prestador === (int)$this->usuario->id;
            default:
                return false;
        }
    }

    private function voltar($id_prestador)
    {
        redirect('adm/horarios?id_prestador='.(int)$id_prestador);
    }

    public function index()
    {
        $prestadores = $this->prestadores_visiveis();
        $id_prestador = (int)$this->input->get('id_prestador');
        if (!$this->pode_ver($id_prestador)) {
            $id_prestador = !empty($prestadores) ? (int)$prestadores[0]->id : 0;
        }

        $dados['prestadores'] = $prestadores;
        $dados['id_prestador'] = $id_prestador;
        $dados['schema_ok'] = $this->disponibilidade_model->schema_ok();
        $dados['config'] = $id_prestador > 0 ? $this->disponibilidade_model->get_config($id_prestador) : null;
        $dados['bloqueios'] = $id_prestador > 0 ? $this->disponibilidade_model->listar_bloqueios_futuros($id_prestador) : array();
        $dados['pode_editar'] = $id_prestador > 0 && $this->pode_editar($id_prestador);
        $dados['duracoes'] = utec_disp_duracoes_permitidas();
        $dados['nomes_dias'] = utec_disp_nomes_dias();
        $dados['flash_ok'] = $this->session->flashdata('ok');
        $dados['flash_error'] = $this->session->flashdata('error');
        $this->load->view('adm/horarios/index', $dados);
    }

    public function salvar()
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if (!$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para editar os horários deste profissional.', 403);
            return;
        }
        $duracao = (int)$this->input->post('duracao');
        if (!in_array($duracao, utec_disp_duracoes_permitidas(), true)) {
            $this->session->set_flashdata('error', 'Duração de consulta inválida.');
            $this->voltar($id_prestador);
            return;
        }

        $dias = $this->input->post('dias');
        $dias = is_array($dias) ? $dias : array();
        $nomes = utec_disp_nomes_dias();
        $grade = array();
        for ($d = 0; $d <= 6; $d++) {
            $grade[$d] = array();
            if (empty($dias[$d]['atende'])) {
                continue;
            }
            $inis = isset($dias[$d]['ini']) ? (array)$dias[$d]['ini'] : array();
            $fins = isset($dias[$d]['fim']) ? (array)$dias[$d]['fim'] : array();
            foreach ($inis as $k => $ini) {
                $ini = trim((string)$ini);
                $fim = isset($fins[$k]) ? trim((string)$fins[$k]) : '';
                if ($ini === '' && $fim === '') {
                    continue;
                }
                $grade[$d][] = array($ini, $fim);
            }
            $erro = utec_disp_validar_intervalos($grade[$d]);
            if ($erro !== '') {
                $this->session->set_flashdata('error', $nomes[$d].': '.$erro);
                $this->voltar($id_prestador);
                return;
            }
        }

        if ($this->disponibilidade_model->salvar_config($id_prestador, $duracao, $grade)) {
            $this->session->set_flashdata('ok', 'Horários de atendimento salvos.');
        } else {
            $this->session->set_flashdata('error', 'Não foi possível salvar. Verifique se a migração de horários foi executada.');
        }
        $this->voltar($id_prestador);
    }

    public function bloquear()
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if (!$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para bloquear horários deste profissional.', 403);
            return;
        }
        $b = utec_disp_normalizar_bloqueio(
            (string)$this->input->post('data_inicio', true),
            (string)$this->input->post('hora_inicio', true),
            (string)$this->input->post('data_fim', true),
            (string)$this->input->post('hora_fim', true),
            (bool)$this->input->post('dia_inteiro')
        );
        if (!$b['ok']) {
            $this->session->set_flashdata('error', $b['erro']);
        } elseif ($this->disponibilidade_model->adicionar_bloqueio(
            $id_prestador, $b['inicio'], $b['fim'], (string)$this->input->post('motivo', true), (int)$this->usuario->id
        )) {
            $this->session->set_flashdata('ok', 'Bloqueio cadastrado.');
        } else {
            $this->session->set_flashdata('error', 'Não foi possível cadastrar o bloqueio.');
        }
        $this->voltar($id_prestador);
    }

    public function desbloquear($id_bloqueio = 0)
    {
        $id_prestador = (int)$this->input->post('id_prestador');
        if ($this->input->method() !== 'post' || !$this->pode_editar($id_prestador)) {
            show_error('Sem permissão para remover este bloqueio.', 403);
            return;
        }
        if ($this->disponibilidade_model->remover_bloqueio((int)$id_bloqueio, $id_prestador)) {
            $this->session->set_flashdata('ok', 'Bloqueio removido.');
        } else {
            $this->session->set_flashdata('error', 'Bloqueio não encontrado.');
        }
        $this->voltar($id_prestador);
    }

    public function livres()
    {
        $id_prestador = (int)$this->input->get('id_prestador');
        $this->output->set_content_type('application/json');
        if (!$this->pode_ver($id_prestador)) {
            $this->output->set_status_header(403)->set_output(json_encode(array('erro' => 'forbidden')));
            return;
        }
        $data = (string)$this->input->get('data', true);
        $ignorar = (int)$this->input->get('ignorar');
        $hora = trim((string)$this->input->get('hora', true));

        $res = $this->disponibilidade_model->horarios_livres($id_prestador, $data, $ignorar);
        if ($hora !== '') {
            $verif = $this->disponibilidade_model->verificar_horario($id_prestador, $data, $hora, $ignorar);
            $res['situacao'] = $verif['situacao'];
        }
        $this->output->set_output(json_encode($res));
    }
}
