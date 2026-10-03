<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Rótulos de pacientes: catálogo por conta (raiz da árvore) + vínculos paciente↔rótulo.
// Sem controle de acesso por design — o controller adm/Rotulos (e as views) impõem nível e escopo.
class Rotulos_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('rotulos');
	}

	public function disponivel(){
		return $this->db->table_exists('pacientes_rotulos') && $this->db->table_exists('pacientes_rotulos_vinculos');
	}

	public function conta_raiz($id_usuario){
		$db = $this->db;
		return utec_rotulos_resolver_raiz((int)$id_usuario, function($id) use ($db){
			$row = $db->query("SELECT id, nivel, id_user FROM usuarios WHERE id = ? LIMIT 1", array((int)$id))->row_array();
			return $row ? $row : null;
		});
	}

	public function catalogo($id_conta, $so_ativos = true){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return array(); }
		return $this->db->query(
			"SELECT * FROM pacientes_rotulos WHERE id_conta = ?".($so_ativos ? " AND status = 1" : "")." ORDER BY ordem ASC, nome ASC",
			array((int)$id_conta)
		)->result();
	}

	public function garantir_sugestoes($id_conta, $id_usuario){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return; }
		$total = (int)$this->db->query("SELECT COUNT(*) AS total FROM pacientes_rotulos WHERE id_conta = ?", array((int)$id_conta))->row()->total;
		if($total > 0){ return; }
		$ordem = 0;
		foreach(utec_rotulos_sugestoes() as $s){
			$ordem += 10;
			$this->db->query(
				"INSERT IGNORE INTO pacientes_rotulos (id_conta, nome, cor, alerta, ordem, status, criado_por, criado_em) VALUES (?, ?, ?, ?, ?, 1, ?, ?)",
				array((int)$id_conta, $s[0], $s[1], (int)$s[2], $ordem, (int)$id_usuario, date('Y-m-d H:i:s'))
			);
		}
	}

	public function salvar_rotulo($id_conta, $id_rotulo, $nome, $cor, $alerta, $id_usuario){
		if(!$this->disponivel()){ return array('ok' => false, 'erro' => 'As tabelas de rótulos ainda não foram criadas.'); }
		$id_conta = (int)$id_conta;
		$id_rotulo = (int)$id_rotulo;
		if($id_conta <= 0){ return array('ok' => false, 'erro' => 'Conta inválida.'); }
		$nome = utec_rotulos_normalizar_nome($nome);
		if($nome === ''){ return array('ok' => false, 'erro' => 'Informe o nome do rótulo.'); }
		$dup = $this->db->query("SELECT id FROM pacientes_rotulos WHERE id_conta = ? AND nome = ? AND id <> ? LIMIT 1", array($id_conta, $nome, $id_rotulo));
		if($dup->num_rows() > 0){ return array('ok' => false, 'erro' => 'Já existe um rótulo com esse nome.'); }
		$dados = array('nome' => $nome, 'cor' => utec_rotulos_cor_valida($cor), 'alerta' => $alerta ? 1 : 0);
		if($id_rotulo > 0){
			$existe = $this->db->query("SELECT id FROM pacientes_rotulos WHERE id = ? AND id_conta = ? LIMIT 1", array($id_rotulo, $id_conta));
			if($existe->num_rows() === 0){ return array('ok' => false, 'erro' => 'Rótulo não encontrado.'); }
			$this->db->where('id', $id_rotulo)->where('id_conta', $id_conta)->update('pacientes_rotulos', $dados);
			return array('ok' => true, 'erro' => '');
		}
		$max = $this->db->query("SELECT COALESCE(MAX(ordem), 0) AS m FROM pacientes_rotulos WHERE id_conta = ?", array($id_conta))->row();
		$dados['id_conta'] = $id_conta;
		$dados['ordem'] = (int)$max->m + 10;
		$dados['status'] = 1;
		$dados['criado_por'] = (int)$id_usuario;
		$dados['criado_em'] = date('Y-m-d H:i:s');
		$ok = $this->db->insert('pacientes_rotulos', $dados);
		return array('ok' => (bool)$ok, 'erro' => $ok ? '' : 'Não foi possível salvar o rótulo.');
	}

	public function alternar_status($id_conta, $id_rotulo){
		if(!$this->disponivel()){ return false; }
		$this->db->query("UPDATE pacientes_rotulos SET status = 1 - status WHERE id = ? AND id_conta = ?", array((int)$id_rotulo, (int)$id_conta));
		return $this->db->affected_rows() > 0;
	}

	public function rotulos_do_paciente($id_paciente, $so_ativos = true){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query(
			"SELECT r.* FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE v.id_paciente = ?".($so_ativos ? " AND r.status = 1" : "")."
			ORDER BY r.alerta DESC, r.ordem ASC, r.nome ASC",
			array((int)$id_paciente)
		)->result();
	}

	public function rotulos_de_pacientes(array $ids){
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids), function($v){ return $v > 0; })));
		if(empty($ids) || !$this->disponivel()){ return array(); }
		$rows = $this->db->query(
			"SELECT v.id_paciente, r.* FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE r.status = 1 AND v.id_paciente IN (".implode(',', $ids).")
			ORDER BY r.alerta DESC, r.ordem ASC, r.nome ASC"
		)->result();
		$mapa = array();
		foreach($rows as $r){
			$mapa[(int)$r->id_paciente][] = $r;
		}
		return $mapa;
	}

	public function definir_rotulos_paciente($id_paciente, array $ids_rotulo, $id_conta, $id_usuario){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return false; }
		$agora = date('Y-m-d H:i:s');
		$this->db->trans_start();
		$this->db->query(
			"DELETE v FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE v.id_paciente = ? AND r.id_conta = ? AND r.status = 1",
			array((int)$id_paciente, (int)$id_conta)
		);
		foreach($ids_rotulo as $id_rotulo){
			$this->db->query(
				"INSERT IGNORE INTO pacientes_rotulos_vinculos (id_paciente, id_rotulo, aplicado_por, aplicado_em) VALUES (?, ?, ?, ?)",
				array((int)$id_paciente, (int)$id_rotulo, (int)$id_usuario, $agora)
			);
		}
		$this->db->trans_complete();
		return (bool)$this->db->trans_status();
	}
}
