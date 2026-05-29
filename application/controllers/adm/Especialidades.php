<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Especialidades extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->usuarios_model->verSession();
		if((int)$this->session->userdata('nivel') !== 1){
			show_error('Acesso restrito ao administrador.', 403);
		}
	}

	function index(){
		$dados['especialidades'] = $this->db->query(
			"SELECT * FROM usuarios_especialidades ORDER BY nome ASC"
		)->result();

		$dados['campos'] = $this->db->query(
			"SELECT ecc.*, ue.nome AS esp_nome
			FROM especialidades_campos_config ecc
			JOIN usuarios_especialidades ue ON ue.id = ecc.especialidade_id
			ORDER BY ue.nome ASC, ecc.ordem ASC, ecc.id ASC"
		)->result();

		// agrupa por especialidade_id para uso na view
		$dados['campos_por_esp'] = [];
		foreach($dados['campos'] as $c){
			$dados['campos_por_esp'][$c->especialidade_id][] = $c;
		}

		$dados['flash_ok']    = $this->session->flashdata('ok');
		$dados['flash_error'] = $this->session->flashdata('error');
		$this->load->view('adm/especialidades/index', $dados);
	}

	function form($id = 0){
		$id = (int)$id;

		$dados['especialidades'] = $this->db->query(
			"SELECT * FROM usuarios_especialidades WHERE status = 1 ORDER BY nome ASC"
		)->result();

		$dados['campo']        = null;
		$dados['opcoes_texto'] = '';
		$dados['esp_presel']   = (int)$this->input->get('esp');
		$dados['flash_error']  = $this->session->flashdata('error');

		if($id > 0){
			$dados['campo'] = $this->db->query(
				"SELECT * FROM especialidades_campos_config WHERE id = $id LIMIT 1"
			)->row();
			if(!$dados['campo']){
				redirect('adm/especialidades');
				return;
			}
			if($dados['campo']->campo_opcoes){
				$arr = json_decode($dados['campo']->campo_opcoes, true);
				if(is_array($arr)){
					$dados['opcoes_texto'] = implode("\n", $arr);
				}
			}
		}

		$this->load->view('adm/especialidades/form', $dados);
	}

	function salvar(){
		$id               = (int)$this->input->post('id');
		$especialidade_id = (int)$this->input->post('especialidade_id');
		$campo_chave      = preg_replace('/[^a-z0-9_]/', '', strtolower(trim((string)$this->input->post('campo_chave'))));
		$campo_label      = trim((string)$this->input->post('campo_label'));
		$campo_tipo       = $this->input->post('campo_tipo');
		$campo_placeholder= trim((string)$this->input->post('campo_placeholder'));
		$ordem            = (int)$this->input->post('ordem');
		$obrigatorio      = $this->input->post('obrigatorio') ? 1 : 0;
		$status           = $this->input->post('status') ? 1 : 0;

		if(!$especialidade_id || !$campo_chave || !$campo_label){
			$this->session->set_flashdata('error', 'Preencha os campos obrigatórios: especialidade, chave e label.');
			$redir = $id > 0 ? 'adm/especialidades/form/'.$id : 'adm/especialidades/form';
			redirect($redir);
			return;
		}

		// chave única por especialidade (verificação manual em insert)
		if($id === 0){
			$qr_dup = $this->db->query(
				"SELECT id FROM especialidades_campos_config WHERE especialidade_id = $especialidade_id AND campo_chave = ".$this->db->escape($campo_chave)." LIMIT 1"
			);
			if($qr_dup->num_rows() > 0){
				$this->session->set_flashdata('error', 'Já existe um campo com essa chave para esta especialidade. Use outra chave.');
				redirect('adm/especialidades/form?esp='.$especialidade_id);
				return;
			}
		}

		$campo_opcoes = null;
		if(in_array($campo_tipo, ['select', 'radio'])){
			$opcoes_raw = trim((string)$this->input->post('campo_opcoes_texto'));
			if($opcoes_raw){
				$arr = array_values(array_filter(array_map('trim', explode("\n", $opcoes_raw))));
				$campo_opcoes = !empty($arr) ? json_encode($arr, JSON_UNESCAPED_UNICODE) : null;
			}
		}

		$dd = [
			'especialidade_id'   => $especialidade_id,
			'campo_chave'        => $campo_chave,
			'campo_label'        => $campo_label,
			'campo_tipo'         => $campo_tipo,
			'campo_opcoes'       => $campo_opcoes,
			'campo_placeholder'  => $campo_placeholder ?: null,
			'ordem'              => $ordem,
			'obrigatorio'        => $obrigatorio,
			'status'             => $status,
		];

		if($id > 0){
			$this->db->where('id', $id);
			$this->db->update('especialidades_campos_config', $dd);
			$this->session->set_flashdata('ok', 'Campo atualizado com sucesso.');
		} else {
			$this->db->insert('especialidades_campos_config', $dd);
			$this->session->set_flashdata('ok', 'Campo adicionado com sucesso.');
		}

		redirect('adm/especialidades');
	}

	function toggle_status($id){
		$id = (int)$id;
		$qr = $this->db->query(
			"SELECT status FROM especialidades_campos_config WHERE id = $id LIMIT 1"
		);
		if($qr->num_rows() > 0){
			$novo = (int)$qr->row()->status === 1 ? 0 : 1;
			$this->db->where('id', $id);
			$this->db->update('especialidades_campos_config', ['status' => $novo]);
			$msg = $novo ? 'Campo ativado.' : 'Campo desativado.';
			$this->session->set_flashdata('ok', $msg);
		}
		redirect('adm/especialidades');
	}

	function deletar($id){
		$id = (int)$id;
		$this->db->where('id', $id);
		$this->db->delete('especialidades_campos_config');
		$this->session->set_flashdata('ok', 'Campo removido.');
		redirect('adm/especialidades');
	}
}
