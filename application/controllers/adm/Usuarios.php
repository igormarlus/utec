<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		#$this->padrao_model->indexador();
		$this->usuarios_model->verSession();

		if(!$this->session->userdata('id')){
			redirect('');
		}

   } // fecha fn USER

function Index(){
	#echo "teste";
	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");
	$dados['nivel'] = 1;
	#$this->load->view('adm/usuarios/lista', $dados);
	$this->load->view('adm/usuarios/new/lista', $dados);

}

function novo(){
	//echo "teste";
	#if ($this->session->userdata('nivel') > 2) {
		$this->padrao_model->indexador();
		$dados['niveis'] = $this->db->get('usuarios_niveis');
		#$dados['setores'] = $this->padrao_model->get_by_matriz('id_setor','0','usuarios_setores');
		#$dados['unidades'] = $this->db->get('unidades');
		$this->load->view('adm/usuarios/novo' , $dados);
		
	#} else {
	#	echo "Você não tem permissão para acessar esta página";	
	#}
}



function cadastro($nivel){
	
	#$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id)->row();
	#$this->load->view('adm/usuarios/edicao', $dados);	
	$this->load->model('padrao_model');
	#$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id)->row();

	#$dados["exames"] = $this->db->query("SELECT * FROM exames ORDER BY nome asc ");
	#$dados["consultas"] = $this->db->query("SELECT * FROM consultas ORDER BY nome asc");
	#$dados["procedimentos"] = $this->db->query("SELECT * FROM procedimentos ORDER BY nome asc");

	$dados['nivel'] = $nivel;
	
	#$this->load->view('adm/usuarios/edicao', $dados);	
	$this->load->view('adm/usuarios/new/cadastro', $dados);

}

function cadastrar() {

	

	#return false;
	
	$dd = array(
		'id_user' => $this->session->userdata('id'),
		'nome' => $this->input->post('nome'),
		'nivel' => $this->input->post('nivel'),
		'telefone' => $this->input->post('telefone'),
		'profissao' => $this->input->post('profissao'),
		'rg' => $this->input->post('identidade'),
		'cpf' => $this->input->post('cpf'),
		'email' => $this->input->post('email'),
		'endereco' => $this->input->post('endereco'),
		'numero' => $this->input->post('numero'),
		'bairro' => $this->input->post('bairro'),
		'cidade' => $this->input->post('cidade'),
		'uf' => $this->input->post('uf'),
		'complemento' => $this->input->post('complemento'),
		'cep' => $this->input->post('cep'),
		'redes_sociais' => $this->input->post('redes_sociais')
	);
	if($this->input->post('nivel') == '3'){
		$dd['especialidade'] = $this->input->post('especialidade');
		$dd['classe'] = $this->input->post('classe');
	}

	if($this->input->post('nivel') < 5){
		$dd['login'] = $this->input->post('login');
		$senha_nova  = $this->input->post('senha');
		if($senha_nova){
			$dd['senha'] = password_hash($senha_nova, PASSWORD_DEFAULT);
		}
	}

	 

#	if($_POST['id_setor'] == '3'){
#		$dd['afiliacoes'] = $_POST['afiliacoes'];
		$dd['dt_nascimento'] = $_POST['dt_nascimento'];
#	}

	if (isset($_POST['imagem'])) {
		$dd['img'] = $_POST['imagem'];
	}
	#print_r($_FILES);
	#return false;
	if(isset($_FILES)){
		if(isset($_FILES['userfile']['name'])){
			$video = $this->do_upload();
			#echo $video;
			#return false;
			#if(empty($video['error'])){
			#	echo "Ops! Ocorreu algum erro!! Por favor Informe o esse erro ao suporte<a href='".base_url()."adm/usuarios/edicao/".$this->session->userdata('id')."'>. Voltar</a>";
			#}else{
				$dd['video'] = $video;	
			#}
			
			#return false;
		}
	}

	
	if ($this->db->insert('usuarios', $dd)) {
		redirect('adm/usuarios/edicao/'.$this->db->insert_id());
	} else {
		echo "Falha ao alterar usuario!";	
	}
	
}

function cadastrar____(){
	$this->padrao_model->indexador();
	//echo "teste";
	if ($_POST) {
		if ($this->usuarios_model->cadastrar()) {
			$this->Index();
		} else {
			echo "falha no cadastro";
		}
	}

}

function rel($nivel=3){
		$nivel = (int)$nivel;
		$this->load->model('padrao_model');
		$dados['nivel'] = $nivel;

		#if($this->session->userdata('nivel') == 7 && ($nivel == 5 || $nivel == 2)) {
		#	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel AND id_user = ".$this->session->userdata('id')." ");
		#}else{
			$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");
		#}

		/*

		if($this->session->userdata('nivel') == 7 || $this->session->userdata('nivel') == 1){
			$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE id_prestador = ".$this->session->userdata('id')." AND nivel = $nivel ");
		}


		if($this->session->userdata('id') == 39 || $this->session->userdata('id') == 284 || $this->session->userdata('id') == 352 ) {
			$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");
		}
		
		*/

		$this->load->view('adm/usuarios/new/lista', $dados);

		if($nivel == 7){
			#$this->load->view('adm/usuarios/lista_instalador', $dados);
		}else{
			#$this->load->view('adm/usuarios/lista', $dados);	
		}
		

	}



function prontuario($id_user=1,$id_agenda=0){
	$id_user  = (int)$id_user;
	$id_agenda = (int)$id_agenda;
	if($id_user == 1){ return; }
	$this->load->model('padrao_model');
	$nivel = 5;
	$dados['nivel'] = $nivel;
	$dados['id_agenda'] = $id_agenda;

	if($id_agenda > 0){
		$qr_agenda = $this->db->query("SELECT * FROM agendamentos WHERE id = $id_agenda ");
		if($qr_agenda->num_rows() == 0){
			echo "Falha 125";
			return;
		}else{
			$dados['dd_agenda'] = $qr_agenda->row();
		}
	}
	
	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");

	$dados["dd"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id_user ")->row();

	$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos where id_paciente = $id_user ");
	$dados["qr_agendamentos"] = $qr_agendamentos;

	$dados["arquivos"] = $this->db->query(
		"SELECT * FROM pacientes_arquivos WHERE id_paciente = $id_user ORDER BY id DESC"
	);

	$this->load->view('adm/usuarios/new/prontuario', $dados);

} // x fn

###################### set status
function set_status($id_user,$status){
	$this->padrao_model->indexador();
	#echo $status;
	#echo "<br>";
	if($status == "1"){
		$new_status = 0;
	}
	if($status == "0"){
		$new_status = 1;
	}
	if($status == "2"){
		$new_status = 0;
	}

	$dd_status = array('status' => $new_status);
	$this->db->where('id',$id_user);
	$this->db->update('usuarios',$dd_status);
	#print_r($dd_status);
	redirect('adm/usuarios','refresh');

}


function set_user_cliente(){
	$nome = $this->input->post('nome');
	$telefone = "55".$this->input->post('telefone');

	$arr = array("(",")","-"," ");
	$whats_trat = str_replace($arr, "", $telefone);

	#echo $telefone." - ".$whats_trat;
	$dd_insert = array(
		"id_user" => $this->session->userdata('id'),
		"telefone" => $whats_trat,
		"nome" => $nome
	);
	$this->db->insert('pi_whats_users' , $dd_insert);
	#print_r($_POST);
	redirect('adm/usuarios/dash','refresh');
}


function dash(){
		//echo "teste";
		$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		if($dd_user->status  == 0){
			#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
		}

		$dados['dd_user'] = $dd_user;

		if($this->session->userdata('nivel') == 1){

			$this->db->order_by('id','desc');
			$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get('carrinho');

			$this->db->where('status',1);
			$dados['carrinhos_pagos'] = $this->db->get('carrinho');

			$this->db->where('status',1);
			$dados['carrinhos_pagos'] = $this->db->get('carrinho');

			#$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->where("dt BETWEEN '".date("Y-m-d H:i:s")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
			$dados['vendas'] = $this->db->get('pedidos');


			$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE status = 1");

			
			
			$dados['pedidos_finalizados'] = $this->db->get('carrinho_hist');

			$agenda_hj = $this->db->query("SELECT * FROM carrinho WHERE dt = '".date("Y-m-d")."' ");
			$dados['agenda_hj'] = $agenda_hj;
			$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho WHERE status = 1 ");
			$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho WHERE dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

			$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho  ORDER BY id desc");
			$dados['pedidos_finalizados'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist ");
			

			$n_finalizadas = $this->db->query("SELECT * FROM carrinho WHERE status = 0 ORDER BY dt asc ");
			$dados['n_finalizadas'] = $n_finalizadas;
		}

		if($this->session->userdata('nivel') > 1){

			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->order_by('id','desc');
			$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get('carrinho');

			$this->db->where('status',1);
			$this->db->where('id_cliente',$this->session->userdata('id'));
			$dados['carrinhos_pagos'] = $this->db->get('carrinho');

			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->where("dt BETWEEN '".date("Y-m-d")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
			$dados['vendas'] = $this->db->get('pedidos');

			$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

			#$dados['pedidos_finalizados'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");
			$dados['pedidos_finalizados'] = $this->db->query("SELECT  * FROM pedidos WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");


			$dados['saldo_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje");
			$dados['saldo_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes");

			#echo $this->saldo_hoje($this->session->userdata('id'),"mes");
			#return false;

			

			#$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE id_cliente = '".$this->session->userdata('id')."'  AND status = 1");
			// pi_whats_users
			$dados['clientes'] = $this->db->query("SELECT * FROM pi_whats_users WHERE id_user = '".$this->session->userdata('id')."' ");

			$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho WHERE id_cliente = '".$this->session->userdata('id')."' AND status = 1 ");
			$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho WHERE id_cliente = '".$this->session->userdata('id')."' AND dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

			$this->db->where('status',0);
			$this->db->where('id_cliente',$this->session->userdata('id'));
			$dados['carrinhos_pendentes'] = $this->db->get('carrinho');

			$agenda_hj = $this->db->query("SELECT * FROM carrinho WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 1 ORDER BY dt desc ");
			$dados['agenda_hj'] = $agenda_hj;

			$n_finalizadas = $this->db->query("SELECT * FROM carrinho WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 0 ORDER BY dt desc ");
			$dados['n_finalizadas'] = $n_finalizadas;
		}

		$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");

		$dados["produtos"] = $this->db->query("SELECT * FROM produtos WHERE id_user = '".$this->session->userdata('id')."' ORDER BY modelo asc ");


		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$qr_pacientes = $this->db->get('pi_whats_users');
		$dados['qr_pacientes'] = $qr_pacientes;
		

		#$this->load->view('adm/dash', $dados);
		$this->load->view('adm/produtos/new/dash', $dados);

}

function rel_pedidos(){

	$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
	if($dd_user->status  == 0){
		#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
	}

	$dados['pedidos_finalizados'] = $this->db->query("SELECT  * FROM pedidos WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

	$dados['dd_user'] = $dd_user;
	$this->load->view('adm/produtos/new/rel_pedidos', $dados);

}

function set_status_pedido(){
	#print_r($_POST);
	$id_pedido = $this->input->post('id_pedido');
	$status = $this->input->post('val');
	$dd_up = array('status' => $status);

	$this->db->where('id',$id_pedido);
	$this->db->where('id_cliente',$this->session->userdata('id'));
	$this->db->update('pedidos' , $dd_up);
}

// dash_whats
function dash_whats(){
		$this->padrao_model->indexador();
		//echo "teste";
		$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		if($dd_user->status  == 0){
			#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
		}

		$dados['dd_user'] = $dd_user;

	
		if($this->session->userdata('nivel') > 1){

			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->order_by('id','desc');
			$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get('carrinho');
			$dados['clientes_carrinhos'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE id_cliente = '".$this->session->userdata('id')."' ");

			#$this->db->where('status',1);
			#$this->db->where('id_cliente',$this->session->userdata('id'));
			#$dados['carrinhos_pagos'] = $this->db->get('carrinho');

			$this->db->where('id_cliente',$this->session->userdata('id'));
			#$this->db->where("dt BETWEEN '".date("Y-m-d")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
			$dados['vendas'] = $this->db->get('pedidos');

			$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

			#$dados['pedidos_finalizados'] = $this->db->query("SELECT  DISTINCT(id_user) FROM carrinho_hist WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

			$dados['pedidos_finalizados'] = $this->db->query("SELECT  * FROM pedidos WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");


			$dados['saldo_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje");
			$dados['saldo_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes");

			$dados['saldo_carrinho_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje",1);
			$dados['saldo_carrinho_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes",1);

			#echo $this->saldo_hoje($this->session->userdata('id'),"mes");
			#return false;

			

			#$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE id_cliente = '".$this->session->userdata('id')."'  AND status = 1");
			// pi_whats_users
			$dados['clientes'] = $this->db->query("SELECT * FROM pi_whats_users WHERE id_user = '".$this->session->userdata('id')."' ");

			$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho WHERE id_cliente = '".$this->session->userdata('id')."' AND status = 1 ");
			$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho WHERE id_cliente = '".$this->session->userdata('id')."' AND dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

			$this->db->where('status',0);
			$this->db->where('id_cliente',$this->session->userdata('id'));
			$dados['carrinhos_pendentes'] = $this->db->get('carrinho');

			$agenda_hj = $this->db->query("SELECT * FROM carrinho WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 1 ORDER BY dt desc ");
			$dados['agenda_hj'] = $agenda_hj;

			$n_finalizadas = $this->db->query("SELECT * FROM carrinho WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 0 ORDER BY dt desc ");
			$dados['n_finalizadas'] = $n_finalizadas;
		}

		$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");

		$dados["produtos"] = $this->db->query("SELECT * FROM produtos WHERE id_user = '".$this->session->userdata('id')."' ORDER BY modelo asc ");


		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$qr_pacientes = $this->db->get('pi_whats_users');
		$dados['qr_pacientes'] = $qr_pacientes;
		

		#$this->load->view('adm/dash', $dados);
		$this->load->view('adm/dash_whats', $dados);

}

function get_areceber(){

	$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		if($dd_user->status  == 0){
			#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
		}

		$dados['dd_user'] = $dd_user;

		$dados['saldo_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje");
		$dados['saldo_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes");
		$dados['saldo_carrinho_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje",1);
		$dados['saldo_carrinho_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes",1);

		if($this->session->userdata('nivel') > 1){

			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->order_by('id','desc');
			$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get('carrinho');
			$dados['clientes_carrinhos'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE id_cliente = '".$this->session->userdata('id')."' ORDER BY id desc ");
			//print_r($dados['clientes_carrinhos']->result());
			$this->load->view('adm/usuarios/new/dash/pedidos_a_receber', $dados);
		}

}

// pedidos_aceitos
function get_aceitos(){

	$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		if($dd_user->status  == 0){
			#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
		}

		$dados['dd_user'] = $dd_user;

		$dados['saldo_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje");
		$dados['saldo_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes");
		$dados['saldo_carrinho_hj'] = $this->saldo_hoje($this->session->userdata('id'),"hoje",1);
		$dados['saldo_carrinho_mes'] = $this->saldo_hoje($this->session->userdata('id'),"mes",1);
	
		if($this->session->userdata('nivel') > 1){

			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->order_by('id','desc');
			$this->db->limit(10);
			//$dados['carrinhos'] = $this->db->get('carrinho');
			//$dados['clientes_carrinhos'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho WHERE id_cliente = '".$this->session->userdata('id')."' ");
			$dados['pedidos_finalizados'] = $this->db->query("SELECT  * FROM pedidos WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");
			//print_r($dados['clientes_carrinhos']->result());
			$this->load->view('adm/usuarios/new/dash/pedidos_aceitos', $dados);
		}

}

###################### WHATSWEB
// whats_web
function whats_web(){
	$this->padrao_model->indexador();
	$limit = 100;

	if($this->session->userdata('id') == 1){
		$dados['msgs'] = $this->padrao_model->get_qr('pi_whats_msgs');
		$dados['conversas'] = $this->db->query("SELECT DISTINCT contato,nome FROM pi_whats WHERE id_user <> 769 ORDER BY dt desc LIMIT $limit");
		$dados['conversas_last'] = $this->db->query("SELECT * FROM pi_whats WHERE id_user <> 769 ORDER BY dt desc LIMIT $limit");
		
		$dados['cat_produtos'] = $this->db->query("SELECT DISTINCT id as id_produto FROM produtos WHERE whats = 1  ORDER BY id asc"); // alterar id_user para id da sessao
		#########  SEQUENCIAS
		#$dados['sequencias'] = $this->db->query("SELECT * FROM pi_whats_msgs_sequencias WHERE id_user = 1 ORDER BY id asc"); // alterar id_user para id da sessao
	}else{
		$dados['msgs'] = $this->padrao_model->get_by_matriz('id_user',$this->session->userdata('id'),'pi_whats_msgs');

		$dados['conversas'] = $this->db->query("SELECT DISTINCT contato,nome,id_user FROM pi_whats WHERE id_user = ".$this->session->userdata('id')." AND my = 0 ORDER BY dt desc LIMIT $limit");
		$dados['conversas_last'] = $this->db->query("SELECT * FROM pi_whats WHERE id_user = ".$this->session->userdata('id')." AND my = 0 ORDER BY dt desc LIMIT $limit");
		$dados['cat_produtos'] = $this->db->query("SELECT DISTINCT id as id_produto FROM produtos WHERE id_user = ".$this->session->userdata('id')."  ORDER BY id asc"); // alterar id_user para id da sessao
		#echo $dados['cat_produtos']->num_rows();
		#print_r($dados['cat_produtos']->rows());
		#return false;
		#########  SEQUENCIAS
		#$dados['sequencias'] = $this->db->query("SELECT * FROM pi_whats_msgs_sequencias WHERE id_user = ".$this->session->userdata('id')." ORDER BY id asc"); // alterar id_user para id da sessao
	}


	$this->load->view('adm/usuarios/new/whats_web', $dados);
}

function get_conversa($whats,$id_cliente){
	$dados['cb'] = "";

	$chat_last = $this->db->query("SELECT * FROM pi_whats WHERE contato =  '".$whats."' AND id_user = $id_cliente ORDER BY dt asc");
	$dados['chat_last'] = $chat_last;

	$this->load->view('adm/usuarios/new/whats-web/conversa', $dados);

}

######################## X WHATSWEB




function add_car($whats="",$id_cliente=1){
		#header('Access-Control-Allow-Origin: *');
		date_default_timezone_set('America/Recife');
		#if($whats == "" ){
			$id_cliente = $this->input->post('id_cliente');
			$id_user = $this->input->post('id_user');
			$id_produto = $this->input->post('id_produto');
			$qtd = $this->input->post('qtd');
			$id_pedido = 1;

			$qr_produto = $this->padrao_model->get_by_id($id_produto,'produtos');
		#}else{

		#	$nm_produto = "Teste";
		#	$id_pedido = 1;

		#}




		#if($_POST['nm_produto']){
		$nm_produto = $qr_produto->row()->modelo;
		#}
		/*
		$this->db->where('telefone',$whats);
		$qr_user = $this->db->get('pi_whats_users');
		if($qr_user->num_rows() > 0){
			$id_user = $qr_user->row()->id;
		}else{
			$id_user = 0;
		}
		*/

		$qr_carrinho = $this->db->query("SELECT * FROM carrinho WHERE id_user = '".$id_user."' AND id_cliente = ".$id_cliente." AND (status = 1 OR status = 0)");
		$qr_qtd_carrinho = $this->db->query("SELECT DISTINCT id_pedido FROM carrinho WHERE id_user = '".$id_user."' AND id_cliente = ".$id_cliente." ORDER BY id_pedido DESC LIMIT 1 ");
		if($qr_carrinho->num_rows() == 0){
			if($qr_qtd_carrinho->num_rows() > 0){
				$new_id_ped = $qr_qtd_carrinho->row()->id_pedido + 1;
				$id_pedido = $id_user."".$new_id_ped;
			}else{
				$id_pedido = $id_user."".$qr_qtd_carrinho->num_rows();
			}
			$hash = $id_user."".$qr_qtd_carrinho->num_rows()."-".date("Y-m-d h:i:s");
		}else{
			$id_pedido = $qr_carrinho->row()->id_pedido;
			$hash = $qr_carrinho->row()->hash;

		}

		$dd_insert = array(
			'id_user' => $id_user,
			'nm_produto' => $nm_produto,
			#'id_pedido' => $id_user."1",
			'id_pedido' => $id_pedido,
			'id_produto' => $id_produto,
			'hash' => $hash,
			'status' => 0
		);
		if($_POST['id_cliente']){
			$dd_insert['id_cliente'] = $this->input->post('id_cliente');
			// pega id_produto 
			#$only_produto = explode(" - ", $nm_produto);
			#$where_pro = array(
			#	'id_user' => $dd_insert['id_cliente'],
			#	'modelo' => $only_produto[0]
			#);
			#$this->db->where($where_pro);
			#$qr_pro_id = $this->db->get("produtos");
			#if($qr_pro_id->num_rows() > 0){
				#$dd_insert['id_produto'] = $qr_pro_id->row()->id;
			#}
		} // x post id_cliente

		if(isset($_POST['qtd'])){
			$dd_insert['qtd'] = $_POST['qtd'];
			$dd_insert['status'] = 1;
		}


		$this->db->insert('carrinho' , $dd_insert);
		#print_r($dd_insert);
		echo $this->parcial($id_user,$id_cliente);
		#echo json_encode($dd_insert);

	}	


	function parcial($id_user=0,$id_cliente=1){
		#header('Access-Control-Allow-Origin: *');
		date_default_timezone_set('America/Recife');
		$this->load->model('padrao_model');

		#echo "OK";
		#return false;
		

		if($_POST['nm_produto']){
			#$nm_produto = $this->input->post('nm_produto');
		}


		if($id_user > 0){
			$dd_qtd = array('status' => 1);
			$this->db->where('id_user',$id_user);
			$this->db->where('id_cliente',$id_cliente);
			$this->db->where("status < 2");
			$this->db->where("qtd > 0");
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$this->db->update('carrinho' , $dd_qtd);
			$qr_user = $this->padrao_model->get_by_id($id_user,"pi_whats_users");
			$dados['qr_user'] = $qr_user;
		}

		$saida = "";
		$dados['saida'];
		#echo $id_user."<br>";
		#$id_userbug = $id_user;

		$where_car = array('id_user' => $id_user , 'status' => "1" , 'id_cliente' => $id_cliente);
		$this->db->where($where_car);
		$qr_carrinho = $this->db->get('carrinho');
		#$qr = "SELECT * FROM carrinho WHERE id_user = '$id_userbug' ORDER BY id desc ";
		#echo $qr;
		#echo "<br>";
		#$qr_carrinho2 = $this->db->query($qr);

		#print_r($where_car);

		#print_r($where_car);

		$dados['qr_carrinho'] = $qr_carrinho;
		#echo $qr_carrinho2->num_rows();

		#$dados['qr_carrinho'] = $qr_carrinho;
		$dados['id_user'] = $id_user;
		$dados['id_cliente'] = $id_cliente;
		

		/*
		if($qr_carrinho->num_rows() > 0){
			$saida .= "<h3>".$qr_user->row()->nome."</h3>";
			$saida .= "<h3>".$qr_user->row()->telefone."</h3>";

			$saida .= "<table class='table table-lightborder'>
				<thead>
					<tr>
						<th>Produto (EDIK)</th>
						<th>Valor</th>
						<th>QTD</th>
						<th>Subtotal</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					";
			$total = 0;
			#echo json_encode($qr_carrinho->result());
			foreach($qr_carrinho->result() as $car){
				$saida .= "<tr>";
				#$dd_pro_ex =  explode("-", $car->nm_produto);
				#$preco = str_replace("R$ ", "", $dd_pro_ex[1]);


				#echo " ---- ".$car->id_produto;
				$qr_pro_id = $this->padrao_model->get_by_id($car->id_produto,"produtos");

				if($qr_pro_id->num_rows() > 0){
					$dd_pro = $qr_pro_id->row();
					$preco = $dd_pro->preco_venda;
				}
				#echo "++++++++++ ".$dd_pro->preco_venda;



				//$total += $preco;
				$sub_total = $car->qtd * $preco;
				$total += $sub_total;
				#echo "➡️ ".$dd_pro_ex[0]."\n".$dd_pro_ex[1]." x ".$car->qtd." = *R$* ".number_format(round($sub_total,2),2,",",".")."\n  \n";
				$saida .= "<td>".$dd_pro->modelo."</td>";
				$saida .= "<td>".$preco."</td>";
				$saida .= "<td>".$car->qtd."</td>";
				$saida .= "<td>".number_format(round($sub_total,2),2,",",".")."</td>";
				$saida .= "<td><button type='button' class='btn btn-danger bt_remover' title='".$car->id."'>Remover</button></td>";
				$saida .= "</tr>";
			}
			if($id_cliente != 15){
				#$total += 3;
				#echo "\nTaxa de entrega = _R$ 3,00_\n";
			}
			

			$saida .= "<tr>
						<td>Total</td>
						
						<td></td>
						<td><select name='forma_pagamento' id='forma_pagamento'>
							<option value='PIX'>PIX</option>
							<option value='Dinheiro'>Dinheiro</option>
							<option value='Cartão'>Cartão</option>
							option
						</select></td>
						<td><h6>Total: <strong>R$ ".number_format($total,2,",",".")."</strong>  </h6></td>
						<td><button type='button' id='finalizar_pedido' class='btn btn-success'>Finalizar pedido</button></td>
					</tr>
				</tbody>
			</table>
			";
			
			echo $saida;
		}else{
			echo 0;
		}
		*/
		$this->load->view('adm/usuarios/parcial' , $dados);

		#print_r($dd_insert);

	}

	function remover_pedido($id_user,$id_pedido){
		header('Access-Control-Allow-Origin: *');
		/*
		$this->db->where('telefone',$whats);
		$qr_user = $this->db->get('pi_whats_users');
		if($qr_user->num_rows() > 0){
			$id_user = $qr_user->row()->id;
		}else{
			$id_user = 0;
		}
		*/
		$dd_arr = array(
			'id_user' => $id_user,
			'id' => $id_pedido,
			'id_cliente' => $this->session->userdata('id')
		);
		$this->db->where($dd_arr);
		$qr = $this->db->get("carrinho");
		if($qr->num_rows() > 0){
			$this->db->where($dd_arr);
			$this->db->delete("carrinho");
		}

		echo $this->parcial($id_user,$this->session->userdata('id'));

	}	 // x fn

	function refresh_car($id_user){
		$this->parcial($id_user,$this->session->userdata('id'));
	}


	###########FECHA PEDIDO
	function set_pedido($id_user,$id_cliente){
		#header('Access-Control-Allow-Origin: *');
		date_default_timezone_set('America/Recife');


		if(isset($_POST['forma_pagamento'])){
			$forma_pagamento = $this->input->post('forma_pagamento');
		}else{
			$forma_pagamento = "Indef.";
		}


		
		/*
		$this->db->where('telefone',$whats);
		$qr_user = $this->db->get('pi_whats_users');
		if($qr_user->num_rows() > 0){
			$id_user = $qr_user->row()->id;
		}else{
			$id_user = 0;
		}
		*/


		$where_ped = array('id_user' => $id_user, 'status' => 1 , 'id_cliente' => $id_cliente );
		$this->db->where($where_ped);
		$qr = $this->db->get('carrinho');
		
		// RESETA PEDIDO
		if($qr->num_rows() > 0){
			$id_pedido_format = $dd->id_user."-".$dd->id_cliente."-".date("Y-m-d h:i:s");
			$id_pedido = md5($id_pedido_format);
			foreach($qr->result() as $dd){
				unset($dd->id);
				$dd->id_pedido = $id_pedido;
				$this->db->insert('carrinho_hist' , $dd);
			}
			#echo "OK 3";
			#return false;
			$dd_insert_pedido = array(
				'id_pedido' => $id_pedido,
				'id_cliente' => $dd->id_cliente,
				'id_user' => $dd->id_user,
				'forma_pagamento' => $forma_pagamento
			);
			$this->db->insert('pedidos' , $dd_insert_pedido);
		}



		#print_r($dd_insert_pedido);

		$dd_status = array('status' => 2 , 'id_pedido' => $id_pedido);
		$where_ped = array('id_user' => $id_user, 'status' => 1 , 'id_cliente' => $id_cliente );
		$this->db->where($where_ped);
		$this->db->update('carrinho_hist' , $dd_status);


		$where_del = array('id_user' => $id_user , 'id_cliente' => $id_cliente);
		$this->db->where($where_del);
		$this->db->delete('carrinho');

		if($qr->num_rows() > 0){
			#foreach($qr->result() as $dd){
				#echo json_encode($qr->result());
			echo "<div class='alert alert-success'>Pedido concluído. <a href='".base_url()."adm/usuarios/pedido/".$id_pedido."' target='_blank'>Clique aqui</a> para ver os detalhes ou  <a href='".base_url()."adm/usuarios/dash'>Atualize a página</a> </div>";
			#}
		}



	}


function pedido($id_pedido,$tipo="1"){
		//echo "teste";
		$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		if($dd_user->status  == 0){
			#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
		}
		if($tipo == 1){
			$tabela = "carrinho_hist";
		}
		if($tipo == 2){
			$tabela = "carrinho";
		}
		$dados['dd_user'] = $dd_user;
		$dados['id_pedido'] = $id_pedido;

		if($this->session->userdata('nivel') == 1){
			$this->db->where('id_pedido',$id_pedido);
			$this->db->order_by('id','desc');
			$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get($tabela);

			$this->db->where('id_pedido',$id_pedido);
			$this->db->where('status',1);
			$dados['carrinhos_pagos'] = $this->db->get($tabela);


			$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho_hist WHERE status = 1");

			
			$this->db->where('status',0);
			$dados['carrinhos_pendentes'] = $this->db->get($tabela);

			$agenda_hj = $this->db->query("SELECT * FROM carrinho_hist WHERE dt = '".date("Y-m-d")."' ");
			$dados['agenda_hj'] = $agenda_hj;
			$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE status = 1 ");
			$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

			$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist  ORDER BY id desc");
			

			$n_finalizadas = $this->db->query("SELECT * FROM carrinho_hist WHERE status = 0 ORDER BY dt asc ");
			$dados['n_finalizadas'] = $n_finalizadas;
		}

		if($this->session->userdata('nivel') > 1){

			$this->db->where('id_cliente',$this->session->userdata('id'));
			#$this->db->where('id_pedido',$id_pedido);
			$this->db->where('hash',$id_pedido);
			$this->db->order_by('dt','asc');
			#$this->db->limit(10);
			$dados['carrinhos'] = $this->db->get($tabela);
			#echo $dados['carrinhos']->num_rows();
			#print_r($dados['carrinhos']);
			#return false;

			$this->db->where('status',1);
			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->where('id_pedido',$id_pedido);
			$dados['carrinhos_pagos'] = $this->db->get($tabela);

			$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

			$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."'  AND status = 1");

			$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."' AND status = 1 ");
			$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."' AND dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

			$this->db->where('status',0);
			$this->db->where('id_cliente',$this->session->userdata('id'));
			$this->db->where('id_pedido',$id_pedido);
			$dados['carrinhos_pendentes'] = $this->db->get($tabela);

			$agenda_hj = $this->db->query("SELECT * FROM carrinho_hist WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 1 ORDER BY dt desc ");
			$dados['agenda_hj'] = $agenda_hj;

			$n_finalizadas = $this->db->query("SELECT * FROM carrinho_hist WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 0 ORDER BY dt desc ");
			$dados['n_finalizadas'] = $n_finalizadas;
		}

		$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");

		$this->db->where('id_pedido',$id_pedido);
		$dados['pedido'] = $this->db->get('pedidos');


		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$qr_pacientes = $this->db->get('pi_whats_users');
		$dados['qr_pacientes'] = $qr_pacientes;
		#echo $dados['carrinhos']->row()->id_user;
		$this->db->where('id',$dados['carrinhos']->row()->id_user);
		$qr_cliente = $this->db->get('pi_whats_users');
		$dados['qr_cliente'] = $qr_cliente;

		## endereço
		$this->db->where('contato',$qr_cliente->row()->telefone);
		$this->db->order_by('id','desc');
		$this->db->limit(1);
		$qr_cliente_end = $this->db->get('pi_whats_servicos');
		$dados['qr_cliente_end'] = $qr_cliente_end;
		#echo $qr_cliente->num_rows();
		#return false;
		
		

		#$this->load->view('adm/dash', $dados);
		$this->load->view('adm/produtos/new/pedido', $dados);

	}	

// pedido_print
function pedido_print($id_pedido,$tipo=1){
	//echo "teste";
	$this->load->model('padrao_model');

	$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
	if($dd_user->status  == 0){
		#redirect('adm/usuarios/edicao/'.$this->session->userdata('id'),'refresh');
	}

	if($tipo == 1){
		$tabela = "carrinho_hist";
	}
	if($tipo == 2){
		$tabela = "carrinho";
	}

	$dados['dd_user'] = $dd_user;
	$dados['id_pedido'] = $id_pedido;

	if($this->session->userdata('nivel') == 1){
		$this->db->where('id_pedido',$id_pedido);
		$this->db->order_by('dt','asc');
		$this->db->limit(10);
		$dados['carrinhos'] = $this->db->get($tabela);

		$this->db->where('id_pedido',$id_pedido);
		$this->db->where('status',1);
		$dados['carrinhos_pagos'] = $this->db->get($tabela);

		#$this->db->where('id_user',$this->session->userdata('id'));
		$this->db->where("dt BETWEEN '".date("Y-m-d H:i:s")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
		$dados['vendas'] = $this->db->get('pedidos');

		


		$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho_hist WHERE status = 1");

		
		$this->db->where('status',0);
		$dados['carrinhos_pendentes'] = $this->db->get($tabela);

		$agenda_hj = $this->db->query("SELECT * FROM carrinho_hist WHERE dt = '".date("Y-m-d")."' ");
		$dados['agenda_hj'] = $agenda_hj;
		$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE status = 1 ");
		$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

		$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist  ORDER BY id desc");
		

		$n_finalizadas = $this->db->query("SELECT * FROM carrinho_hist WHERE status = 0 ORDER BY dt asc ");
		$dados['n_finalizadas'] = $n_finalizadas;
	}

	if($this->session->userdata('nivel') > 1){

		$this->db->where('id_cliente',$this->session->userdata('id'));
		$this->db->where('id_pedido',$id_pedido);
		$this->db->order_by('dt','asc');
		#$this->db->limit(10);
		$dados['carrinhos'] = $this->db->get($tabela);

		$this->db->where('status',2);
		$this->db->where('id_cliente',$this->session->userdata('id'));
		$this->db->where("dt BETWEEN '".date("Y-m-d H:i:s")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
		#$this->db->where('id_pedido',$id_pedido);
		$dados['carrinhos_pagos'] = $this->db->get($tabela);

		#$this->db->where('status',2);
		$this->db->where('id_cliente',$this->session->userdata('id'));
		$this->db->where("dt BETWEEN '".date("Y-m-d H:i:s")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
		$dados['vendas'] = $this->db->get('pedidos');

		$dados['pedidos'] = $this->db->query("SELECT  DISTINCT(id_pedido) FROM carrinho_hist WHERE id_cliente =  '".$this->session->userdata('id')."' ORDER BY id desc LIMIT 100");

		$dados['clientes'] = $this->db->query("SELECT DISTINCT id_user FROM carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."'  AND status = 1");

		$dados['saldo'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."' AND status = 1 ");
		$dados['saldo_mensal'] = $this->db->query("SELECT count(id) as total from carrinho_hist WHERE id_cliente = '".$this->session->userdata('id')."' AND dt BETWEEN '".date('y')."-".date('m')."-01' AND '".date('y')."-".date('m')."-31' AND status = 1 ");

		$this->db->where('status',0);
		$this->db->where('id_cliente',$this->session->userdata('id'));
		$this->db->where('id_pedido',$id_pedido);
		$dados['carrinhos_pendentes'] = $this->db->get($tabela);

		$agenda_hj = $this->db->query("SELECT * FROM carrinho_hist WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 1 ORDER BY dt desc ");
		$dados['agenda_hj'] = $agenda_hj;

		$n_finalizadas = $this->db->query("SELECT * FROM carrinho_hist WHERE  id_cliente = '".$this->session->userdata('id')."' AND status = 0 ORDER BY dt desc ");
		$dados['n_finalizadas'] = $n_finalizadas;
	}

	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");

	$this->db->where('id_pedido',$id_pedido);
	$dados['pedido'] = $this->db->get('pedidos');


	$this->db->order_by('id','desc');
	$this->db->limit(10);
	$qr_pacientes = $this->db->get('pi_whats_users');
	$dados['qr_pacientes'] = $qr_pacientes;
	#echo $dados['carrinhos']->row()->id_user;
	#$this->db->where('id',$dados['carrinhos']->row()->id_user);
	$this->db->where('id',$dados['carrinhos_hist']->row()->id_user);
	$qr_cliente = $this->db->get('pi_whats_users');
	$dados['qr_cliente'] = $qr_cliente;

	## endereço
	#$this->db->where('contato',$qr_cliente->row()->telefone);
	$this->db->where('id_user',$dados['pedido']->id_user);
	$this->db->order_by('id','desc');
	$qr_cliente_end = $this->db->get('pi_whats_servicos');
	$dados['qr_cliente_end'] = $qr_cliente_end;
	#echo $qr_cliente->num_rows();
	#return false;
	
	

	#$this->load->view('adm/dash', $dados);
	$this->load->view('adm/produtos/new/pedido_print', $dados);

}


function verifica_novo_pedido(){
	
	$pedidos_pendentes = $this->db->query("SELECT  * FROM pedidos WHERE id_cliente =  '".$this->session->userdata('id')."' AND status = 0 ORDER BY id desc LIMIT 100");
	echo $pedidos_pendentes->num_rows();
}

function saldo_hoje($id_cliente=1,$tempo="hoje",$tipo="2"){ // 1 a receber, 2 aceito 3 entrega 4 finalizado
		header('Access-Control-Allow-Origin: *');
		date_default_timezone_set('America/Recife');
		$this->load->model('padrao_model');

		#echo "OK";
		#return false;
		
		if($tipo == 1){
			$tabela = "carrinho";
			$where_car = array('id_cliente' => $id_cliente,'status' => 1);
		}
		if($tipo == 2){
			$tabela = "carrinho_hist";
			$where_car = array('id_cliente' => $id_cliente,'status' => 2);
		}
		

		#$where_car = array('id_cliente' => $id_cliente,'status' => 2);
		$this->db->where($where_car);
		if($tempo == "hoje"){
			$this->db->where("dt BETWEEN '".date("Y-m-d")." 00:00:01' AND '".date("Y-m-d")." 23:59:59' ");
		}
		if($tempo == "mes"){
			$this->db->where("dt BETWEEN '".date("Y-m")."-01 00:00:01' AND '".date("Y-m")."-31 23:59:59' ");
		}

		$this->db->order_by('dt','asc');
		$qr_carrinho = $this->db->get($tabela);
		if($qr_carrinho->num_rows() > 0){
			$total = 0;
			#echo json_encode($qr_carrinho->result());
			foreach($qr_carrinho->result() as $car){
				#$dd_pro_ex =  explode("-", $car->nm_produto);
				$preco = str_replace("R$ ", "", $dd_pro_ex[1]);


				#echo " ---- ".$car->id_produto;
				$qr_pro_id = $this->padrao_model->get_by_id($car->id_produto,"produtos");

				if($qr_pro_id->num_rows() > 0){
					//print_r($qr_pro_id->row());
					$dd_pro = $qr_pro_id->row();
					$preco = $dd_pro->preco_venda;
					$qr_categoria = $this->padrao_model->get_by_id($dd_pro->id_categoria,'produtos_categorias');
					if($qr_categoria->num_rows() > 0){
						$categoria = $qr_categoria->row()->nome;
						#print_r($categoria);
					}
				}else{
					$categoria = "**";
				}
				#echo "++++++++++ ".$dd_pro->preco_venda;



				//$total += $preco;
				$sub_total = $car->qtd * $preco;
				$total += $sub_total;
				
			}
			#echo $id_cliente."-------";
			if($id_cliente != 15){
				if($id_cliente == 19){
					$entrega = 5;

				}else{
					$entrega = 3;
					
				}
				#$total += $entrega;
				#echo "\nTaxa de entrega = _R$ $entrega,00_\n";
			}
			return number_format($total,2,",",".");
		}else{
			return 0;
		}

		#print_r($dd_insert);

	}




function edicao($id){
	$id = (int)$id;
	$this->load->model('padrao_model');
	$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id ")->row();

	#$dados["exames"] = $this->db->query("SELECT * FROM exames ORDER BY nome asc ");
	#$dados["consultas"] = $this->db->query("SELECT * FROM consultas ORDER BY nome asc");
	#$dados["procedimentos"] = $this->db->query("SELECT * FROM procedimentos ORDER BY nome asc");

	
	#$this->load->view('adm/usuarios/edicao', $dados);	
	$this->load->view('adm/usuarios/new/edicao', $dados);

}

function alterarSenha($msg=''){
	
	$this->db->where('id', $this->session->userdata('id'));
	$dados["usuario"] = $this->db->get('usuarios')->row();
	
	$dados["msg"] = $msg;	

	$this->load->view('adm/usuarios/alterar_senha', $dados);

}

function alterar(){
	$id          = (int)$this->session->userdata('id');
	$senha_input = $this->input->post('senha');
	$nova_senha  = $this->input->post('nova_senha');

	$qry     = $this->db->query("SELECT senha FROM usuarios WHERE id = $id");
	$usuario = $qry->row();

	$senha_ok = $usuario &&
		(password_verify($senha_input, $usuario->senha) || $usuario->senha === $senha_input);

	if($senha_ok){
		$this->db->where('id', $id);
		$this->db->update('usuarios', ['senha' => password_hash($nova_senha, PASSWORD_DEFAULT)]);
		$this->alterarSenha('Senha alterada com sucesso');
	} else {
		$this->alterarSenha('Senha Incorreta');
	}
}

function cadTopico() {
	
	$dd = array('id_raiz' => $_POST['topico_pai'],
				'titulo' => $_POST['titulo'],
				'descricao' => $_POST['descricao'],
				'status' => $_POST['status'],
	);
	
	if (isset($_POST['imagem'])) {
		$dd['img'] = $_POST['imagem'];
	}
	
	if ($this->db->insert('topicos', $dd)) {
		echo "Tópico cadastrado com sucesso!";
	} else {
		echo "Falha ao cadastrar tópico!";	
	}
	
}

function edTopico() {
	
	$dd = array('id_raiz' => $_POST['topico_pai'],
				'titulo' => $_POST['titulo'],
				'descricao' => $_POST['descricao'],
				'status' => $_POST['status'],
	);
	
	if (isset($_POST['imagem'])) {
		$dd['img'] = $_POST['imagem'];
	}
	
	$this->db->where('id', $_POST['id']);
	if ($this->db->update('topicos', $dd)) {
		echo "Tópico alterado com sucesso!";
	} else {
		echo "Falha ao cadastrar tópico!";	
	}
	
}

function cadPrato() {
	
	$dd = array('id_topico' => $_POST['topico_pai'],
				'titulo' => $_POST['titulo'],
				'descricao' => $_POST['descricao'],
				'status' => $_POST['status'],
	);
	
	if (isset($_POST['imagem'])) {
		$dd['img'] = $_POST['imagem'];
	}
	
	if ($this->db->insert('cardapio', $dd)) {
		echo "Prato cadastrado com sucesso!";
	} else {
		echo "Falha ao cadastrar prato!";	
	}
	
}

/*

function editar() {
	
	$dd = array('id' => $_POST['id'],
				'id_unidade' => $_POST['id_unidade'],
				'id_setor' => $_POST['id_setor'],
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'login' => $_POST['login'],
				#'setor' => $_POST['setor']
	);

	$this->db->where('id', $_POST['id']);	
	if ($this->db->update('usuarios', $dd)) {
		$this->Index();
	} else {
		echo "Falha ao alterar usuario!";	
	}
	
}
*/



function editar() {

	

	#return false;
	
	$dd = array(
		'nome'        => $this->input->post('nome'),
		'telefone'    => $this->input->post('telefone'),
		'profissao'   => $this->input->post('profissao'),
		'rg'          => $this->input->post('identidade'),
		'cpf'         => $this->input->post('cpf'),
		'email'       => $this->input->post('email'),
		'endereco'    => $this->input->post('endereco'),
		'numero'      => $this->input->post('numero'),
		'bairro'      => $this->input->post('bairro'),
		'cidade'      => $this->input->post('cidade'),
		'uf'          => $this->input->post('uf'),
		'complemento' => $this->input->post('complemento'),
		'cep'         => $this->input->post('cep'),
		'redes_sociais' => $this->input->post('redes_sociais')
	);

	if($this->input->post('nivel') == '3'){
		$dd['especialidade'] = $this->input->post('especialidade');
		$dd['classe']        = $this->input->post('classe');
	}

	if($this->input->post('nivel') < 5){
		$dd['login'] = $this->input->post('login');
		$senha_edit  = $this->input->post('senha');
		if($senha_edit){
			$dd['senha'] = password_hash($senha_edit, PASSWORD_DEFAULT);
		}
	}

	 

#	if($_POST['id_setor'] == '3'){
	if($this->input->post('dt_nascimento')){
		$dd['dt_nascimento'] = $this->input->post('dt_nascimento');
	}

	if($this->input->post('imagem')){
		$dd['img'] = $this->input->post('imagem');
	}

	if($_FILES && !empty($_FILES['userfile']['name'])){
		$dd['video'] = $this->do_upload();
	}

	$id_editar = (int)$this->input->post('id');
	$this->db->where('id', $id_editar);
	if($this->db->update('usuarios', $dd)){
		redirect('adm/usuarios/edicao/'.$id_editar);
	} else {
		echo "Falha ao alterar usuario!";
	}

}

public function do_upload()
    {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'mp4|mov|avi';
            $config['max_size']             = 16000;
            $config['max_width']            = 1024;
            $config['max_height']           = 768;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('userfile'))
            {
                    $error = array('error' => $this->upload->display_errors());
                    print_r($error);
                    return $error;
                    #$this->load->view('upload_form', $error);
            }
            else
            {
                    $data = array('upload_data' => $this->upload->data());
                    #print_r($data['upload_data']);
                    #echo '<br><br>';
                    return $data['upload_data']['file_name'];

                    #$this->load->view('upload_success', $data);
            }
    }



function upImgPost(){
	//echo "teste";
		##################### X IMAGENS #########################
		
		//upload codeigniter lib
		$config['upload_path'] = './imagens/usuarios/';
		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  	$this->load->library('upload', $config);

		
		if (!$this->upload->do_upload('photoimg')) {
			$status = 'error';
			$error = array('error' => $this->upload->display_errors());
			$msg = 'erro ao enviar o arquivo, tente novamente'.$error;
			echo $msg;
			print_r($error);
		} else  {
			$data = $this->upload->data();
		 
			$this->load->library('image_lib');
			
			//alterando img principal
			$conf['image_library'] = 'gd2';
			$conf['source_image'] = './imagens/usuarios/'.$data['file_name'];
			$conf['new_image'] = './imagens/usuarios/min/'.$data['file_name'];
			$conf['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf['height'] = 72;
			$conf['width'] = 120;
			$this->image_lib->initialize($conf); 
			$this->image_lib->resize();
			
			$conf2['image_library'] = 'gd2';
			$conf2['source_image'] = './imagens/usuarios/'.$data['file_name'];
			$conf2['new_image'] = './imagens/usuarios/des/'.$data['file_name'];
			$conf2['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf2['height'] = 210;
			$conf2['width'] = 300;
			$this->image_lib->initialize($conf2); 
			$this->image_lib->resize();
			
			echo "<img src='".base_url()."imagens/usuarios/min/".$data['file_name']."'>
					<input name='imagem' type='hidden' value='".$data['file_name']."'>";
		}


	}


function remover($id){
		
	$this->db->where('id', $id);
	$this->db->delete('usuarios');
	redirect('adm/usuarios','refresh');
	#$this->lista();

}

function removerPrato($id){
		
	$this->db->where('id', $id);
	$this->db->delete('cardapio');
	
	$this->pratos();

}

function logout(){
	$this->session->sess_destroy();
	#redirect('admin','refresh');
	redirect('','refresh');
}
	
}
