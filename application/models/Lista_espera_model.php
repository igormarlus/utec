<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera: entradas por conta (raiz da árvore) e registro das vagas abertas.
// Sem controle de acesso por design — o controller adm/Lista_espera e a library Lista_espera_vagas impõem escopo.
class Lista_espera_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('lista_espera');
	}

	public function disponivel(){
		return $this->db->table_exists('lista_espera') && $this->db->table_exists('lista_espera_vagas');
	}

	public function conta_raiz($id_usuario){
		$this->load->model('Rotulos_model', 'rotulos_model');
		return (int)$this->rotulos_model->conta_raiz((int)$id_usuario);
	}

	private function select_base(){
		return "SELECT le.*, p.nome AS paciente_nome, p.telefone AS paciente_telefone, pr.nome AS prestador_nome
			FROM lista_espera le
			JOIN usuarios p ON p.id = le.id_paciente
			LEFT JOIN usuarios pr ON pr.id = le.id_prestador";
	}

	public function listar($id_conta, $status){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return array(); }
		if(!in_array($status, array('aguardando', 'agendado', 'removido'), true)){ $status = 'aguardando'; }
		$ordem = $status === 'aguardando'
			? ' ORDER BY le.criado_em ASC, le.id ASC'
			: ' ORDER BY COALESCE(le.atualizado_em, le.criado_em) DESC, le.id DESC LIMIT 200';
		return $this->db->query($this->select_base().' WHERE le.id_conta = ? AND le.status = ?'.$ordem, array((int)$id_conta, $status))->result();
	}

	public function buscar($id){
		if(!$this->disponivel() || (int)$id <= 0){ return null; }
		$row = $this->db->query($this->select_base().' WHERE le.id = ? LIMIT 1', array((int)$id))->row();
		return $row ? $row : null;
	}

	private function existe_aguardando($id_paciente, $id_prestador, $ignorar_id = 0){
		$row = $this->db->query(
			"SELECT id FROM lista_espera WHERE id_paciente = ? AND id_prestador <=> ? AND status = 'aguardando' AND id <> ? LIMIT 1",
			array((int)$id_paciente, $id_prestador === null ? null : (int)$id_prestador, (int)$ignorar_id)
		)->row();
		return (bool)$row;
	}

	public function adicionar($dados, $id_conta, $id_usuario){
		if(!$this->disponivel()){ return array('ok' => false, 'erro' => 'Lista de espera indisponível.', 'id' => 0); }
		if($this->existe_aguardando($dados['id_paciente'], $dados['id_prestador'])){
			return array('ok' => false, 'erro' => 'Paciente já está na lista de espera deste profissional.', 'id' => 0);
		}
		$ok = $this->db->insert('lista_espera', array(
			'id_conta' => (int)$id_conta,
			'id_paciente' => (int)$dados['id_paciente'],
			'id_prestador' => $dados['id_prestador'],
			'turno' => (string)$dados['turno'],
			'dias_semana' => (string)$dados['dias_semana'],
			'a_partir_de' => $dados['a_partir_de'],
			'observacao' => $dados['observacao'],
			'status' => 'aguardando',
			'criado_por' => (int)$id_usuario,
			'criado_em' => date('Y-m-d H:i:s'),
		));
		return $ok
			? array('ok' => true, 'erro' => '', 'id' => (int)$this->db->insert_id())
			: array('ok' => false, 'erro' => 'Não foi possível salvar.', 'id' => 0);
	}

	public function atualizar($id, $dados, $id_usuario){
		$atual = $this->buscar($id);
		if(!$atual || $atual->status !== 'aguardando'){ return array('ok' => false, 'erro' => 'Entrada não encontrada.'); }
		if($this->existe_aguardando($atual->id_paciente, $dados['id_prestador'], (int)$id)){
			return array('ok' => false, 'erro' => 'Paciente já está na lista de espera deste profissional.');
		}
		$this->db->where('id', (int)$id);
		$this->db->where('status', 'aguardando');
		$ok = $this->db->update('lista_espera', array(
			'id_prestador' => $dados['id_prestador'],
			'turno' => (string)$dados['turno'],
			'dias_semana' => (string)$dados['dias_semana'],
			'a_partir_de' => $dados['a_partir_de'],
			'observacao' => $dados['observacao'],
			'atualizado_por' => (int)$id_usuario,
			'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $ok ? array('ok' => true, 'erro' => '') : array('ok' => false, 'erro' => 'Não foi possível salvar.');
	}

	public function remover($id, $motivo, $id_usuario){
		if(!$this->disponivel() || !array_key_exists((string)$motivo, utec_le_motivos_saida())){ return false; }
		$this->db->where('id', (int)$id);
		$this->db->where('status', 'aguardando');
		$this->db->update('lista_espera', array(
			'status' => 'removido', 'motivo_saida' => (string)$motivo,
			'atualizado_por' => (int)$id_usuario, 'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $this->db->affected_rows() > 0;
	}

	public function marcar_agendado($id, $id_paciente, $id_agendamento, $id_usuario){
		if(!$this->disponivel() || (int)$id <= 0){ return false; }
		$this->db->where('id', (int)$id);
		$this->db->where('id_paciente', (int)$id_paciente);
		$this->db->where('status', 'aguardando');
		$this->db->update('lista_espera', array(
			'status' => 'agendado', 'id_agendamento' => (int)$id_agendamento,
			'atualizado_por' => (int)$id_usuario, 'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $this->db->affected_rows() > 0;
	}

	public function aguardando_do_paciente($id_paciente){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query($this->select_base()." WHERE le.id_paciente = ? AND le.status = 'aguardando' ORDER BY le.criado_em ASC", array((int)$id_paciente))->result();
	}

	public function contar_aguardando_para($id_conta, $id_prestador){
		if(!$this->disponivel()){ return 0; }
		$row = $this->db->query(
			"SELECT COUNT(*) AS n FROM lista_espera WHERE id_conta = ? AND status = 'aguardando' AND (id_prestador IS NULL OR id_prestador = ?)",
			array((int)$id_conta, (int)$id_prestador)
		)->row();
		return $row ? (int)$row->n : 0;
	}

	public function aguardando_para($id_conta, $id_prestador){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query(
			$this->select_base()." WHERE le.id_conta = ? AND le.status = 'aguardando' AND (le.id_prestador IS NULL OR le.id_prestador = ?) ORDER BY le.criado_em ASC, le.id ASC",
			array((int)$id_conta, (int)$id_prestador)
		)->result();
	}

	// Retorna o id da vaga nova; 0 quando o mesmo agendamento já liberou este horário (evento repetido).
	public function registrar_vaga($vaga){
		if(!$this->disponivel()){ return 0; }
		$this->db->query(
			"INSERT IGNORE INTO `lista_espera_vagas` (id_conta, id_prestador, data_agenda, hora_agenda, origem, id_agendamento_origem, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?)",
			array((int)$vaga['id_conta'], (int)$vaga['id_prestador'], (string)$vaga['data_agenda'], substr((string)$vaga['hora_agenda'], 0, 5),
				(string)$vaga['origem'], (int)$vaga['id_agendamento_origem'], date('Y-m-d H:i:s'))
		);
		return $this->db->affected_rows() > 0 ? (int)$this->db->insert_id() : 0;
	}

	public function buscar_vaga($id){
		if(!$this->disponivel() || (int)$id <= 0){ return null; }
		$row = $this->db->query(
			"SELECT v.*, pr.nome AS prestador_nome FROM lista_espera_vagas v LEFT JOIN usuarios pr ON pr.id = v.id_prestador WHERE v.id = ? LIMIT 1",
			array((int)$id)
		)->row();
		return $row ? $row : null;
	}

	public function horario_ocupado($id_prestador, $data, $hora){
		$row = $this->db->query(
			"SELECT id FROM agendamentos WHERE id_prestador = ? AND data_agenda = ? AND LEFT(hora_agenda, 5) = ? AND status IN (0,1,2) LIMIT 1",
			array((int)$id_prestador, substr((string)$data, 0, 10), substr((string)$hora, 0, 5))
		)->row();
		return (bool)$row;
	}

	// Prestador da vaga + estabelecimento (2) e colaboradores (4) da conta. Desce só por nós de equipe.
	public function destinatarios_conta($id_conta, $id_prestador){
		$ids = array();
		if((int)$id_prestador > 0){ $ids[(int)$id_prestador] = (int)$id_prestador; }
		$raiz = $this->db->query('SELECT id, nivel FROM usuarios WHERE id = ? LIMIT 1', array((int)$id_conta))->row();
		if(!$raiz){ return array_values($ids); }
		if(in_array((int)$raiz->nivel, array(2, 4), true)){ $ids[(int)$raiz->id] = (int)$raiz->id; }
		$camada = array((int)$raiz->id);
		for($i = 0; $i < 5 && $camada; $i++){
			$rows = $this->db->query('SELECT id, nivel FROM usuarios WHERE id_user IN ('.implode(',', array_map('intval', $camada)).') AND nivel IN (2,3,4)')->result();
			$camada = array();
			foreach($rows as $r){
				$id = (int)$r->id;
				if(isset($ids[$id]) && (int)$r->nivel !== 3){ continue; }
				if(in_array((int)$r->nivel, array(2, 4), true)){ $ids[$id] = $id; }
				$camada[] = $id;
			}
		}
		return array_values($ids);
	}
}
