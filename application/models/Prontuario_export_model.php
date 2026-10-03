<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Coleta os dados do prontuário para exportação (PDF/CSV/XLSX) e registra a auditoria.
// Sem controle de acesso por design: o controller (Atendimento::exportar_prontuario) valida nível e escopo.
class Prontuario_export_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('prontuario_export');
		$this->load->model('padrao_model');
	}

	public function coletar($id_paciente, $de, $ate, $usuario){
		$id_paciente = (int)$id_paciente;
		$qr_pac = $this->db->query("SELECT * FROM usuarios WHERE id = ? LIMIT 1", array($id_paciente));
		if($qr_pac->num_rows() === 0){ return null; }
		$p = $qr_pac->row();
		$paciente = (object)array(
			'id' => (int)$p->id,
			'nome' => isset($p->nome) ? (string)$p->nome : '',
			'telefone' => isset($p->telefone) ? (string)$p->telefone : '',
			'email' => isset($p->email) ? (string)$p->email : '',
			'dt_cadastro' => isset($p->dt_cadastro) ? (string)$p->dt_cadastro : '',
		);

		// Mesmo filtro de escopo de Atendimento::prontuario()
		$where = array('a.id_paciente = '.$id_paciente);
		if((int)$usuario->nivel !== 1){
			$scope_sql = $this->padrao_model->ids_to_sql_in($this->padrao_model->get_scope_user_ids($usuario));
			$where[] = '(a.id_user IN ('.$scope_sql.') OR a.id_paciente IN ('.$scope_sql.') OR a.id_prestador IN ('.$scope_sql.'))';
		}
		$binds = array();
		if($de !== ''){ $where[] = 'a.data_agenda >= ?'; $binds[] = $de; }
		if($ate !== ''){ $where[] = 'a.data_agenda <= ?'; $binds[] = $ate; }

		$tem_esp = $this->db->table_exists('usuarios_especialidades');
		$sel_esp = $tem_esp ? ', ue.nome AS especialidade_nome' : ", '' AS especialidade_nome";
		$join_esp = $tem_esp ? ' LEFT JOIN usuarios_especialidades ue ON ue.id = pr.especialidade' : '';

		$rows = $this->db->query(
			"SELECT a.*, pr.nome AS prestador_nome, pr.especialidade AS prestador_esp_id".$sel_esp."
			FROM agendamentos a
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador".$join_esp."
			WHERE ".implode(' AND ', $where)."
			ORDER BY a.data_agenda ASC, a.hora_agenda ASC, a.id ASC",
			$binds
		)->result();

		$labels_por_esp = $this->labels_campos_extras($rows);

		$atendimentos = array();
		$ids_atend = array();
		foreach($rows as $r){
			$esp = (int)$r->prestador_esp_id;
			$ids_atend[] = (int)$r->id;
			$atendimentos[] = array(
				'id' => (int)$r->id,
				'data' => (string)$r->data_agenda,
				'hora' => substr((string)$r->hora_agenda, 0, 5),
				'status_texto' => utec_pront_status_texto($r->status),
				'profissional' => (string)$r->prestador_nome,
				'especialidade' => (string)$r->especialidade_nome,
				'atendimento_inicial' => isset($r->atendimento_inicial) ? (string)$r->atendimento_inicial : '',
				'avaliacao' => isset($r->avaliacao) ? (string)$r->avaliacao : '',
				'reavaliacao' => isset($r->reavaliacao) ? (string)$r->reavaliacao : '',
				'extras' => utec_pront_montar_extras(
					isset($r->campos_extras) ? (string)$r->campos_extras : '',
					isset($labels_por_esp[$esp]) ? $labels_por_esp[$esp] : array()
				),
				'rotulos' => utec_pront_rotulos($esp),
			);
		}

		return array(
			'paciente' => $paciente,
			'atendimentos' => $atendimentos,
			'exames' => $this->coletar_exames($id_paciente, $ids_atend),
			'arquivos' => $this->coletar_arquivos($id_paciente, $de, $ate),
			'meta' => array(
				'de' => $de,
				'ate' => $ate,
				'gerado_por' => isset($usuario->nome) ? (string)$usuario->nome : '',
				'gerado_em' => date('d/m/Y H:i'),
			),
		);
	}

	public function registrar_exportacao($id_usuario, $id_paciente, $formato, $de, $ate, $ip){
		if(!$this->db->table_exists('prontuario_exportacoes')){ return false; }
		$app_key = getenv('APP_SECRET') ?: (getenv('MERCADOPAGO_WEBHOOK_SECRET') ?: 'utec-ai-salt');
		$tenant_id = null;
		if($this->db->field_exists('tenant_id', 'usuarios')){
			$qr = $this->db->query("SELECT tenant_id FROM usuarios WHERE id = ? LIMIT 1", array((int)$id_usuario));
			if($qr->num_rows() > 0 && (int)$qr->row()->tenant_id > 0){ $tenant_id = (int)$qr->row()->tenant_id; }
		}
		$ok = $this->db->insert('prontuario_exportacoes', array(
			'id_usuario' => (int)$id_usuario,
			'id_paciente' => (int)$id_paciente,
			'tenant_id' => $tenant_id,
			'formato' => (string)$formato,
			'periodo_de' => $de !== '' ? $de : null,
			'periodo_ate' => $ate !== '' ? $ate : null,
			'ip_hash' => $ip !== '' ? hash('sha256', $ip.$app_key) : null,
			'criado_em' => date('Y-m-d H:i:s'),
		));
		if(!$ok){
			log_message('error', 'exportar_prontuario: falha ao registrar auditoria (usuario '.(int)$id_usuario.', formato '.$formato.')');
		}
		return (bool)$ok;
	}

	private function labels_campos_extras(array $rows){
		$esp_ids = array();
		foreach($rows as $r){
			if((int)$r->prestador_esp_id > 0){ $esp_ids[(int)$r->prestador_esp_id] = true; }
		}
		if(empty($esp_ids) || !$this->db->table_exists('especialidades_campos_config')){ return array(); }
		$cfg = $this->db->query(
			"SELECT especialidade_id, campo_chave, campo_label FROM especialidades_campos_config
			WHERE especialidade_id IN (".implode(',', array_keys($esp_ids)).")"
		)->result();
		$mapa = array();
		foreach($cfg as $c){
			$mapa[(int)$c->especialidade_id][(string)$c->campo_chave] = (string)$c->campo_label;
		}
		return $mapa;
	}

	private function coletar_exames($id_paciente, array $ids_atend){
		if(empty($ids_atend) || !$this->db->table_exists('usuarios_exames_atendimento')){ return array(); }
		$rows = $this->db->query(
			"SELECT uea.status, ue.obs AS obs, ag.data_agenda, ex.nome AS exame_nome, pr.nome AS prestador_nome
			FROM usuarios_exames_atendimento uea
			LEFT JOIN usuarios_exames ue ON ue.id = uea.id_exame_atendimento
			LEFT JOIN agendamentos ag ON ag.id = uea.id_atendimento
			LEFT JOIN exames ex ON ex.id = uea.id_exame
			LEFT JOIN usuarios pr ON pr.id = ag.id_prestador
			WHERE uea.id_user = ".(int)$id_paciente."
			  AND uea.id_atendimento IN (".implode(',', array_map('intval', $ids_atend)).")
			ORDER BY ag.data_agenda ASC, uea.id ASC"
		)->result();
		$saida = array();
		foreach($rows as $r){
			$saida[] = array(
				'data' => (string)$r->data_agenda,
				'exame' => (string)$r->exame_nome,
				'status_texto' => utec_pront_status_exame_texto($r->status),
				'profissional' => (string)$r->prestador_nome,
				'obs' => (string)$r->obs,
			);
		}
		return $saida;
	}

	private function coletar_arquivos($id_paciente, $de, $ate){
		if(!$this->db->table_exists('pacientes_arquivos')){ return array(); }
		$where = array('id_paciente = '.(int)$id_paciente);
		$binds = array();
		if($de !== ''){ $where[] = 'DATE(dt_cadastro) >= ?'; $binds[] = $de; }
		if($ate !== ''){ $where[] = 'DATE(dt_cadastro) <= ?'; $binds[] = $ate; }
		$rows = $this->db->query(
			"SELECT nome_original, descricao, tipo, dt_cadastro FROM pacientes_arquivos
			WHERE ".implode(' AND ', $where)." ORDER BY dt_cadastro ASC, id ASC",
			$binds
		)->result();
		$saida = array();
		foreach($rows as $r){
			$saida[] = array(
				'data' => substr((string)$r->dt_cadastro, 0, 10),
				'nome' => (string)$r->nome_original,
				'descricao' => (string)$r->descricao,
				'tipo' => (string)$r->tipo,
			);
		}
		return $saida;
	}
}
