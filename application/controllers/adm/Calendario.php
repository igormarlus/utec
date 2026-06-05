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

		if ((int)$dd_user->nivel !== 1) {
			$visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
			$visible_prestador_sql = $this->padrao_model->ids_to_sql_in($visible_prestador_ids);
			$prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (" . $visible_prestador_sql . ") ORDER BY nome ASC");
		} else {
			$prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC");
		}

		$dados['dd']          = $dd_user;
		$dados['nivel']       = (int)$dd_user->nivel;
		$dados['prestadores'] = $prestadores;

		$this->load->view('adm/calendario/index', $dados);
	}

	function eventos()
	{
		$dd_user = $this->padrao_model->get_usuario_logado();
		if ((int)$dd_user->nivel === 5) {
			header('Content-Type: application/json');
			echo json_encode([]); return;
		}

		$start = $this->input->get('start', true);
		$end   = $this->input->get('end',   true);

		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$start)) {
			header('Content-Type: application/json');
			echo json_encode([]); return;
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$end)) {
			header('Content-Type: application/json');
			echo json_encode([]); return;
		}

		$scope_ids             = $this->padrao_model->get_scope_user_ids($dd_user);
		$scope_sql             = $this->padrao_model->ids_to_sql_in($scope_ids);
		$visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);

		$where = ["a.data_agenda BETWEEN '" . $this->db->escape_str($start) . "' AND '" . $this->db->escape_str($end) . "'"];

		if ((int)$dd_user->nivel !== 1) {
			$where[] = "(a.id_user IN (" . $scope_sql . ") OR a.id_paciente IN (" . $scope_sql . ") OR a.id_prestador IN (" . $scope_sql . "))";
		}

		$id_prestador = (int)$this->input->get('id_prestador');
		if ($id_prestador > 0 && ((int)$dd_user->nivel === 1 || in_array($id_prestador, $visible_prestador_ids))) {
			$where[] = "a.id_prestador = " . $id_prestador;
		}

		$where_sql = 'WHERE ' . implode(' AND ', $where);

		$qr = $this->db->query(
			"SELECT a.id, a.id_paciente, a.id_prestador, a.data_agenda, a.hora_agenda, a.tipo, a.status,
			        p.nome  AS paciente_nome,
			        pr.nome AS prestador_nome
			 FROM agendamentos a
			 LEFT JOIN usuarios p  ON p.id  = a.id_paciente
			 LEFT JOIN usuarios pr ON pr.id = a.id_prestador
			 " . $where_sql . "
			 ORDER BY a.data_agenda ASC, a.hora_agenda ASC"
		);

		$paleta = [
			['bg'=>'#dbeafe','border'=>'#93c5fd','text'=>'#1e40af'],
			['bg'=>'#fce7f3','border'=>'#f9a8d4','text'=>'#9d174d'],
			['bg'=>'#fef3c7','border'=>'#fde68a','text'=>'#92400e'],
			['bg'=>'#ede9fe','border'=>'#c4b5fd','text'=>'#5b21b6'],
			['bg'=>'#dcfce7','border'=>'#86efac','text'=>'#166534'],
			['bg'=>'#ffedd5','border'=>'#fdba74','text'=>'#9a3412'],
			['bg'=>'#cffafe','border'=>'#67e8f9','text'=>'#164e63'],
			['bg'=>'#f1f5f9','border'=>'#cbd5e1','text'=>'#334155'],
		];

		$eventos = [];
		foreach ($qr->result() as $ag) {
			$cor  = $paleta[(int)$ag->id_prestador % 8];
			$hora = substr($ag->hora_agenda, 0, 5);
			$eventos[] = [
				'id'              => (int)$ag->id,
				'title'           => $ag->paciente_nome ?: 'Paciente',
				'start'           => $ag->data_agenda . 'T' . $hora . ':00',
				'end'             => $ag->data_agenda . 'T' . $hora . ':00',
				'backgroundColor' => $cor['bg'],
				'borderColor'     => $cor['border'],
				'textColor'       => $cor['text'],
				'extendedProps'   => [
					'paciente_id'    => (int)$ag->id_paciente,
					'paciente_nome'  => (string)$ag->paciente_nome,
					'prestador_id'   => (int)$ag->id_prestador,
					'prestador_nome' => (string)$ag->prestador_nome,
					'tipo'           => (string)$ag->tipo,
					'status'         => (int)$ag->status,
					'hora'           => $hora,
					'data'           => (string)$ag->data_agenda,
				],
			];
		}

		header('Content-Type: application/json');
		echo json_encode($eventos);
	}

	function salvar_agendamento()
	{
		$dd_user = $this->padrao_model->get_usuario_logado();

		// Apenas níveis 1–4 podem criar agendamentos
		if ((int)$dd_user->nivel === 5) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Acesso negado.']); return;
		}

		$id_paciente  = (int)$this->input->post('id_paciente');
		$id_prestador = (int)$this->input->post('id_prestador');
		$data_agenda  = $this->input->post('data_agenda',  true);
		$hora_agenda  = $this->input->post('hora_agenda',  true);
		$tipo         = $this->input->post('tipo',         true);

		// Validações
		if (!$id_paciente) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Selecione um paciente.']); return;
		}
		if (!$id_prestador) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Selecione um profissional.']); return;
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$data_agenda)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Data inválida.']); return;
		}
		if (!preg_match('/^\d{2}:\d{2}$/', (string)$hora_agenda)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Horário inválido.']); return;
		}
		$tipos_validos = ['Consulta', 'Retorno', 'Avaliacao', 'Exame'];
		if (!in_array($tipo, $tipos_validos)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Tipo inválido.']); return;
		}

		// Verificar acesso ao paciente
		if (!$this->padrao_model->can_access_usuario($id_paciente)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Acesso negado ao paciente.']); return;
		}

		// Verificar acesso ao prestador (nivel 1 pode usar qualquer prestador)
		$visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
		if ((int)$dd_user->nivel !== 1 && !in_array($id_prestador, $visible_prestador_ids)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Acesso negado ao profissional.']); return;
		}

		$dados = [
			'id_user'          => (int)$this->session->userdata('id'),
			'id_paciente'      => $id_paciente,
			'id_prestador'     => $id_prestador,
			'tipo'             => $tipo,
			'data_agenda'      => $data_agenda,
			'hora_agenda'      => $hora_agenda,
			'data_hora_agenda' => $data_agenda . ' ' . $hora_agenda,
			'status'           => 0,
		];

		header('Content-Type: application/json');
		if ($this->db->insert('agendamentos', $dados)) {
			echo json_encode(['success' => true, 'id' => (int)$this->db->insert_id()]);
		} else {
			echo json_encode(['success' => false, 'error' => 'Falha ao salvar agendamento.']);
		}
	}
}
