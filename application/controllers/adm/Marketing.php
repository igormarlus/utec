<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->model('padrao_model');
		$this->load->model('adm/Marketing_model');
		$this->load->model('adm/Usuarios_model');
		$this->Usuarios_model->verSession();

		$usuario = $this->padrao_model->get_usuario_logado();
		if(!$usuario || (int)$usuario->nivel !== 1){
			redirect('adm/atendimento');
		}
	}

	public function index(){
		$this->trafego_ia();
	}

	public function trafego_ia(){
		$filtros = $this->_filtros_from_get();
		$dados = array(
			'summary'     => $this->Marketing_model->get_summary($filtros),
			'sources'     => $this->Marketing_model->get_by_source($filtros),
			'pages'       => $this->Marketing_model->get_landing_pages($filtros),
			'conv'        => $this->Marketing_model->get_conversion_by_source($filtros),
			'timeline'    => $this->Marketing_model->get_timeline($filtros),
			'filtros_raw' => $filtros,
			'schema_ok'   => $this->db->table_exists('ai_referrals') && $this->db->table_exists('ai_conversions'),
		);
		$this->load->view('adm/marketing/trafego_ia', $dados);
	}

	public function api($rel = ''){
		$filtros = $this->_filtros_from_get();
		switch($rel){
			case 'summary':     $out = $this->Marketing_model->get_summary($filtros); break;
			case 'sources':     $out = $this->Marketing_model->get_by_source($filtros); break;
			case 'pages':       $out = $this->Marketing_model->get_landing_pages($filtros); break;
			case 'conversions': $out = $this->Marketing_model->get_conversion_by_source($filtros); break;
			case 'timeline':    $out = $this->Marketing_model->get_timeline($filtros); break;
			default:
				$this->output->set_status_header(404)->set_content_type('application/json')
					->set_output(json_encode(array('error' => 'relatorio invalido')));
				return;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	private function _filtros_from_get(){
		return array(
			'start_date'   => $this->input->get('start_date'),
			'end_date'     => $this->input->get('end_date'),
			'source'       => $this->input->get('source'),
			'landing_page' => $this->input->get('landing_page'),
			'converted'    => $this->input->get('converted'),
		);
	}
}
