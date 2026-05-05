<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dev extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('adm/usuarios_model');
		$this->usuarios_model->verSession();
		if($this->session->userdata('nivel') != 1){
			exit('Acesso negado.');
		}
	}

	function index(){
		echo '<h2>Dev Controller</h2><ul>';
		echo '<li><a href="'.base_url().'adm/dev/criar_tabela_arquivos_paciente">Criar tabela pacientes_arquivos</a></li>';
		echo '</ul>';
	}

	function criar_tabela_arquivos_paciente(){
		$sql = "CREATE TABLE IF NOT EXISTS `pacientes_arquivos` (
			`id`             INT AUTO_INCREMENT PRIMARY KEY,
			`id_paciente`    INT NOT NULL,
			`id_agendamento` INT DEFAULT 0,
			`id_user`        INT NOT NULL,
			`arquivo`        VARCHAR(255) NOT NULL,
			`nome_original`  VARCHAR(255) NOT NULL,
			`tipo`           VARCHAR(10)  NOT NULL,
			`descricao`      VARCHAR(255) DEFAULT '',
			`dt_cadastro`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		if($this->db->query($sql)){
			echo '✅ Tabela <strong>pacientes_arquivos</strong> criada com sucesso (ou já existia).';
		} else {
			echo '❌ Erro ao criar tabela: '.$this->db->error()['message'];
		}
	}
}
