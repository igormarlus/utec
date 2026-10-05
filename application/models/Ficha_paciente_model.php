<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ficha fixa do paciente (1:1 com usuarios nível 5). Sem controle de acesso por design —
// o controller adm/Ficha e a view do prontuário impõem nível e escopo.
class Ficha_paciente_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('ficha_paciente');
	}

	public function disponivel(){
		return $this->db->table_exists('pacientes_ficha');
	}

	public function obter($id_paciente){
		$ficha = utec_ficha_vazia();
		$ficha['saude_atualizado_por'] = '';
		$ficha['saude_atualizado_em'] = '';
		$ficha['saude_atualizado_por_nome'] = '';
		if(!$this->disponivel()){ return $ficha; }
		$row = $this->db->query(
			"SELECT f.*, u.nome AS saude_atualizado_por_nome FROM pacientes_ficha f
			LEFT JOIN usuarios u ON u.id = f.saude_atualizado_por
			WHERE f.id_paciente = ? LIMIT 1",
			array((int)$id_paciente)
		)->row_array();
		if($row){
			foreach($row as $k => $v){
				if(array_key_exists($k, $ficha)){ $ficha[$k] = ($v === null) ? '' : (string)$v; }
			}
		}
		return $ficha;
	}

	public function salvar($id_paciente, array $dados, $id_usuario, $saude_mudou){
		if(!$this->disponivel() || (int)$id_paciente <= 0){ return false; }
		$permitidas = array_keys(utec_ficha_vazia());
		$linha = array();
		foreach($dados as $k => $v){
			if(in_array($k, $permitidas, true)){ $linha[$k] = ((string)$v === '') ? null : (string)$v; }
		}
		$agora = date('Y-m-d H:i:s');
		$linha['atualizado_por'] = (int)$id_usuario;
		$linha['atualizado_em'] = $agora;
		if($saude_mudou){
			$linha['saude_atualizado_por'] = (int)$id_usuario;
			$linha['saude_atualizado_em'] = $agora;
		}
		$cols = array_keys($linha);
		$sql = "INSERT INTO pacientes_ficha (`id_paciente`, `".implode('`, `', $cols)."`) VALUES (?".str_repeat(', ?', count($cols)).")"
			." ON DUPLICATE KEY UPDATE ".implode(', ', array_map(function($c){ return "`".$c."` = VALUES(`".$c."`)"; }, $cols));
		return (bool)$this->db->query($sql, array_merge(array((int)$id_paciente), array_values($linha)));
	}
}
