<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rotulos extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'rotulos'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Rotulos_model', 'rotulos_model');
		$this->usuarios_model->verSession();

		$this->usuario = $this->padrao_model->get_usuario_logado();
		$nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
		if($nivel < 1 || $nivel > 4){
			show_error('Acesso restrito.', 403);
		}
	}

	private function nivel()
	{
		return (int)$this->usuario->nivel;
	}

	// Conta cujo catálogo o usuário logado pode gerenciar; 0 = não gerencia.
	private function conta_gerenciada()
	{
		if($this->nivel() === 1){
			$ref = (int)$this->input->get('conta');
			if($ref <= 0){ $ref = (int)$this->input->post('conta'); }
			return $ref > 0 ? $this->rotulos_model->conta_raiz($ref) : 0;
		}
		$raiz = $this->rotulos_model->conta_raiz((int)$this->usuario->id);
		if($this->nivel() === 2){ return $raiz; }
		if($this->nivel() === 3 && $raiz === (int)$this->usuario->id){ return $raiz; }
		return 0;
	}

	private function voltar($conta)
	{
		redirect('adm/rotulos'.($this->nivel() === 1 && $conta > 0 ? '?conta='.(int)$conta : ''));
	}

	public function index()
	{
		$conta = $this->conta_gerenciada();
		if($this->nivel() !== 1 && $conta === 0){
			show_error('Os rótulos são gerenciados pelo estabelecimento ou pelo profissional autônomo.', 403);
			return;
		}
		$dados['schema_ok'] = $this->rotulos_model->disponivel();
		$dados['conta'] = $conta;
		$dados['eh_admin'] = $this->nivel() === 1;
		$dados['rotulos'] = array();
		if($dados['schema_ok'] && $conta > 0){
			$this->rotulos_model->garantir_sugestoes($conta, (int)$this->usuario->id);
			$dados['rotulos'] = $this->rotulos_model->catalogo($conta, false);
		}
		$dados['paleta'] = utec_rotulos_paleta();
		$dados['flash_ok'] = $this->session->flashdata('rotulos_ok');
		$dados['flash_erro'] = $this->session->flashdata('rotulos_erro');
		$this->load->view('adm/rotulos/index', $dados);
	}

	public function salvar()
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$conta = $this->conta_gerenciada();
		if($conta === 0){ show_error('Acesso negado.', 403); return; }
		$r = $this->rotulos_model->salvar_rotulo(
			$conta,
			(int)$this->input->post('id'),
			(string)$this->input->post('nome'),
			(string)$this->input->post('cor', true),
			(int)$this->input->post('alerta') === 1,
			(int)$this->usuario->id
		);
		$this->session->set_flashdata($r['ok'] ? 'rotulos_ok' : 'rotulos_erro', $r['ok'] ? 'Rótulo salvo.' : $r['erro']);
		$this->voltar($conta);
	}

	public function status($id_rotulo = 0)
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$conta = $this->conta_gerenciada();
		if($conta === 0){ show_error('Acesso negado.', 403); return; }
		if($this->rotulos_model->alternar_status($conta, (int)$id_rotulo)){
			$this->session->set_flashdata('rotulos_ok', 'Status do rótulo atualizado.');
		}else{
			$this->session->set_flashdata('rotulos_erro', 'Rótulo não encontrado.');
		}
		$this->voltar($conta);
	}

	public function paciente($id_paciente = 0)
	{
		$id_paciente = (int)$id_paciente;
		if($this->input->method() !== 'post' || $id_paciente <= 1){ show_404(); return; }
		if(!$this->padrao_model->can_access_usuario($id_paciente)){
			show_error('Acesso negado ao paciente selecionado.', 403);
			return;
		}
		$alvo = $this->db->query('SELECT nivel FROM usuarios WHERE id = ? LIMIT 1', array($id_paciente))->row();
		if(!$alvo || (int)$alvo->nivel !== 5){ show_404(); return; }
		$conta = $this->rotulos_model->conta_raiz($id_paciente);
		if($conta <= 0){
			$this->session->set_flashdata('rotulos_erro', 'Paciente sem clínica vinculada.');
			redirect('adm/atendimento/prontuario/'.$id_paciente);
			return;
		}
		$ids_catalogo = array();
		foreach($this->rotulos_model->catalogo($conta, true) as $r){
			$ids_catalogo[] = (int)$r->id;
		}
		$post = $this->input->post('rotulos');
		$ids = utec_rotulos_ids_validos(is_array($post) ? $post : array(), $ids_catalogo);
		if($this->rotulos_model->definir_rotulos_paciente($id_paciente, $ids, $conta, (int)$this->usuario->id)){
			$this->session->set_flashdata('rotulos_ok', 'Rótulos do paciente atualizados.');
		}else{
			$this->session->set_flashdata('rotulos_erro', 'Não foi possível salvar os rótulos.');
		}
		$voltar = (string)$this->input->post('voltar', true);
		if(!preg_match('#^adm/[a-z0-9_/]+$#i', $voltar)){
			$voltar = 'adm/atendimento/prontuario/'.$id_paciente;
		}
		redirect($voltar);
	}
}
