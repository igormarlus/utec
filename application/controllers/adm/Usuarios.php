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
	$dd_user = $this->padrao_model->get_usuario_logado();
	$scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
	$scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);
	if((int)$dd_user->nivel === 1){
		$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");
	}else{
		$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE id IN (".$scope_sql.")");
	}
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
	$this->load->model('padrao_model');
	$dd_user = $this->padrao_model->get_usuario_logado();
	$nivel = $this->padrao_model->sanitize_child_level($nivel, $dd_user);

	$dados['nivel'] = $nivel;
	$dados['niveis_permitidos'] = $this->padrao_model->get_allowed_child_levels($dd_user);
	$dados['vinculo_options'] = $this->padrao_model->get_vinculo_options($nivel, $dd_user);
	$dados['vinculo_default'] = $this->padrao_model->get_vinculo_default_id($nivel, $dd_user);
	$dados['usuario_logado'] = $dd_user;
	
	#$this->load->view('adm/usuarios/edicao', $dados);	
	$this->load->view('adm/usuarios/new/cadastro', $dados);

}

function cadastrar() {
	$dd_user = $this->padrao_model->get_usuario_logado();
	$nivel = $this->padrao_model->sanitize_child_level($this->input->post('nivel'), $dd_user);
	$vinculo_id = $this->padrao_model->resolve_vinculo_id($nivel, $this->input->post('id_user'), $dd_user);
	
	$dd = array(
		'id_user' => $vinculo_id,
		'nome' => $this->input->post('nome'),
		'nivel' => $nivel,
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
	if($nivel == 3){
		$dd['especialidade'] = $this->input->post('especialidade');
		$dd['classe'] = $this->input->post('classe');
	}

	if($nivel == 5 && $this->db->field_exists('afiliacoes', 'usuarios')){
		$dd['afiliacoes'] = $this->input->post('afiliacoes');
	}

	if($nivel < 5){
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

	if($this->db->field_exists('tenant_id', 'usuarios')){
		$tenant_id = 0;
		if(isset($dd_user->tenant_id) && (int)$dd_user->tenant_id > 0){
			$tenant_id = (int)$dd_user->tenant_id;
		}elseif($vinculo_id > 0){
			$tenant_id = $this->padrao_model->get_usuario_tenant_id($vinculo_id);
		}
		if($tenant_id > 0){
			$dd['tenant_id'] = $tenant_id;
			$dd['tenant_role'] = $this->padrao_model->infer_tenant_role_by_level($nivel);
			if($this->db->field_exists('onboarding_status', 'usuarios')){
				$dd['onboarding_status'] = 'ativo';
			}
		}
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
		$dd_user = $this->padrao_model->get_usuario_logado();
		$scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
		$scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);
		if((int)$dd_user->nivel === 1){
			$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");
		}else{
			$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel AND id IN (".$scope_sql.") ");
		}

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
	if(!$this->padrao_model->can_access_usuario($id_user)){
		show_error('Acesso negado ao prontuario selecionado.', 403);
		return;
	}
	$this->load->model('padrao_model');
	$nivel = 5;
	$dados['nivel'] = $nivel;
	$dados['id_agenda'] = $id_agenda;

	if($id_agenda > 0){
		if(!$this->padrao_model->can_access_agendamento($id_agenda)){
			show_error('Acesso negado ao atendimento selecionado.', 403);
			return;
		}
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

	$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos where id_paciente = $id_user ORDER BY data_agenda DESC, hora_agenda DESC, id DESC ");
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
		$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		$dados['dd_user'] = $dd_user;
		$scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
		$scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);

		$hoje = date('Y-m-d');
		$where_scope = "";

		if((int)$dd_user->nivel !== 1){
			$where_scope = " WHERE (a.id_user IN (".$scope_sql.") OR a.id_paciente IN (".$scope_sql.") OR a.id_prestador IN (".$scope_sql.")) ";
		}

		$dados['metricas'] = [
			'agendados_hoje' => 0,
			'confirmados_hoje' => 0,
			'finalizados_hoje' => 0,
			'pacientes_ativos' => 0,
		];

		$qr_agendados_hoje = $this->db->query(
			"SELECT COUNT(a.id) AS total
			FROM agendamentos a
			".$where_scope.(empty($where_scope) ? " WHERE " : " AND ")."a.data_agenda = '".$hoje."'"
		);
		$dados['metricas']['agendados_hoje'] = (int)$qr_agendados_hoje->row()->total;

		$qr_confirmados_hoje = $this->db->query(
			"SELECT COUNT(a.id) AS total
			FROM agendamentos a
			".$where_scope.(empty($where_scope) ? " WHERE " : " AND ")."a.data_agenda = '".$hoje."' AND a.status = 1"
		);
		$dados['metricas']['confirmados_hoje'] = (int)$qr_confirmados_hoje->row()->total;

		$qr_finalizados_hoje = $this->db->query(
			"SELECT COUNT(a.id) AS total
			FROM agendamentos a
			".$where_scope.(empty($where_scope) ? " WHERE " : " AND ")."a.data_agenda = '".$hoje."' AND a.status = 2"
		);
		$dados['metricas']['finalizados_hoje'] = (int)$qr_finalizados_hoje->row()->total;

		$qr_pacientes_ativos = $this->db->query(
			"SELECT COUNT(DISTINCT a.id_paciente) AS total
			FROM agendamentos a
			".$where_scope
		);
		$dados['metricas']['pacientes_ativos'] = (int)$qr_pacientes_ativos->row()->total;

		$dados['agendamentos_hoje'] = $this->db->query(
			"SELECT a.*, p.nome AS paciente_nome, pr.nome AS prestador_nome
			FROM agendamentos a
			LEFT JOIN usuarios p ON p.id = a.id_paciente
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador
			".$where_scope.(empty($where_scope) ? " WHERE " : " AND ")."a.data_agenda = '".$hoje."'
			ORDER BY a.hora_agenda ASC, a.id DESC"
		);

		$dados['proximos_agendamentos'] = $this->db->query(
			"SELECT a.*, p.nome AS paciente_nome, pr.nome AS prestador_nome
			FROM agendamentos a
			LEFT JOIN usuarios p ON p.id = a.id_paciente
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador
			".$where_scope."
			ORDER BY a.data_agenda DESC, a.hora_agenda DESC, a.id DESC
			LIMIT 10"
		);

		$dados['pacientes_recentes'] = $this->db->query(
			"SELECT DISTINCT p.id, p.nome, p.telefone, p.email
			FROM agendamentos a
			INNER JOIN usuarios p ON p.id = a.id_paciente
			".$where_scope."
			ORDER BY p.id DESC
			LIMIT 8"
		);

		$this->load->view('adm/dash', $dados);

}

function relatorios_clinicos(){
		$this->load->model('padrao_model');

		$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();
		$dados['dd_user'] = $dd_user;
		$scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
		$scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);
		$visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);

		$data_inicio = $this->input->get('data_inicio', true);
		$data_fim = $this->input->get('data_fim', true);
		$id_prestador = (int)$this->input->get('id_prestador');

		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$data_inicio)){
			$data_inicio = date('Y-m-01');
		}
		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$data_fim)){
			$data_fim = date('Y-m-d');
		}
		if($data_inicio > $data_fim){
			$tmp = $data_inicio;
			$data_inicio = $data_fim;
			$data_fim = $tmp;
		}

		$where_agenda = ["a.data_agenda >= '".$data_inicio."'", "a.data_agenda <= '".$data_fim."'"];
		$where_exames = ["ag.data_agenda >= '".$data_inicio."'", "ag.data_agenda <= '".$data_fim."'"];

		if((int)$dd_user->nivel !== 1){
			$where_agenda[] = "(a.id_user IN (".$scope_sql.") OR a.id_paciente IN (".$scope_sql.") OR a.id_prestador IN (".$scope_sql."))";
			$where_exames[] = "(ag.id_user IN (".$scope_sql.") OR ag.id_paciente IN (".$scope_sql.") OR ag.id_prestador IN (".$scope_sql."))";
		}
		if((int)$dd_user->nivel !== 1 && $id_prestador > 0 && !in_array($id_prestador, $visible_prestador_ids)){
			$id_prestador = 0;
		}

		if($id_prestador > 0){
			$where_agenda[] = "a.id_prestador = ".$id_prestador;
			$where_exames[] = "ag.id_prestador = ".$id_prestador;
		}

		$where_agenda_sql = " WHERE ".implode(" AND ", $where_agenda)." ";
		$where_exames_sql = " WHERE ".implode(" AND ", $where_exames)." ";

		$dados['filtros'] = [
			'data_inicio' => $data_inicio,
			'data_fim' => $data_fim,
			'id_prestador' => $id_prestador,
		];

		if((int)$dd_user->nivel === 1){
			$dados['prestadores'] = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC");
		}else{
			$dados['prestadores'] = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (".$this->padrao_model->ids_to_sql_in($visible_prestador_ids).") ORDER BY nome ASC");
		}

		$dados['metricas_relatorio'] = [
			'atendimentos' => 0,
			'finalizados' => 0,
			'pendentes' => 0,
			'pacientes_ativos' => 0,
			'exames_solicitados' => 0,
			'exames_pendentes' => 0,
		];

		$dados['metricas_relatorio']['atendimentos'] = (int)$this->db->query(
			"SELECT COUNT(a.id) total FROM agendamentos a ".$where_agenda_sql
		)->row()->total;

		$dados['metricas_relatorio']['finalizados'] = (int)$this->db->query(
			"SELECT COUNT(a.id) total FROM agendamentos a ".$where_agenda_sql." AND a.status = 2"
		)->row()->total;

		$dados['metricas_relatorio']['pendentes'] = (int)$this->db->query(
			"SELECT COUNT(a.id) total FROM agendamentos a ".$where_agenda_sql." AND a.status = 0"
		)->row()->total;

		$dados['metricas_relatorio']['pacientes_ativos'] = (int)$this->db->query(
			"SELECT COUNT(DISTINCT a.id_paciente) total FROM agendamentos a ".$where_agenda_sql
		)->row()->total;

		$dados['metricas_relatorio']['exames_solicitados'] = (int)$this->db->query(
			"SELECT COUNT(uea.id) total
			FROM usuarios_exames_atendimento uea
			INNER JOIN agendamentos ag ON ag.id = uea.id_atendimento
			".$where_exames_sql
		)->row()->total;

		$dados['metricas_relatorio']['exames_pendentes'] = (int)$this->db->query(
			"SELECT COUNT(uea.id) total
			FROM usuarios_exames_atendimento uea
			INNER JOIN agendamentos ag ON ag.id = uea.id_atendimento
			".$where_exames_sql." AND uea.status IN (0,1)"
		)->row()->total;

		$dados['resumo_profissionais'] = $this->db->query(
			"SELECT pr.nome AS prestador_nome,
			        COUNT(a.id) AS total_atendimentos,
			        SUM(CASE WHEN a.status = 2 THEN 1 ELSE 0 END) AS total_finalizados,
			        COUNT(DISTINCT a.id_paciente) AS total_pacientes
			FROM agendamentos a
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador
			".$where_agenda_sql."
			GROUP BY a.id_prestador, pr.nome
			ORDER BY total_atendimentos DESC, pr.nome ASC"
		);

		$dados['atendimentos_recentes'] = $this->db->query(
			"SELECT a.*, p.nome AS paciente_nome, pr.nome AS prestador_nome
			FROM agendamentos a
			LEFT JOIN usuarios p ON p.id = a.id_paciente
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador
			".$where_agenda_sql."
			ORDER BY a.data_agenda DESC, a.hora_agenda DESC, a.id DESC
			LIMIT 20"
		);

		$dados['exames_pendentes_lista'] = $this->db->query(
			"SELECT uea.*, ex.nome AS exame_nome, p.nome AS paciente_nome, pr.nome AS prestador_nome, ag.data_agenda, ag.hora_agenda
			FROM usuarios_exames_atendimento uea
			INNER JOIN agendamentos ag ON ag.id = uea.id_atendimento
			LEFT JOIN exames ex ON ex.id = uea.id_exame
			LEFT JOIN usuarios p ON p.id = uea.id_user
			LEFT JOIN usuarios pr ON pr.id = ag.id_prestador
			".$where_exames_sql." AND uea.status IN (0,1)
			ORDER BY ag.data_agenda DESC, ag.hora_agenda DESC, uea.id DESC
			LIMIT 20"
		);

		$this->load->view('adm/relatorios/clinicos', $dados);
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
	if(!$this->padrao_model->can_access_usuario($id)){
		redirect('adm/usuarios');
	}
	$dd_user = $this->padrao_model->get_usuario_logado();
	$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id ")->row();
	if(!$dados["usuario"]){
		redirect('adm/usuarios');
	}
	$dados['niveis_permitidos'] = $this->padrao_model->get_allowed_child_levels($dd_user);
	$dados['pode_editar_regra'] = ((int)$dd_user->nivel === 1);
	$dados['vinculo_options'] = $this->padrao_model->get_vinculo_options((int)$dados["usuario"]->nivel, $dd_user);
	$dados['vinculo_label'] = $this->padrao_model->get_vinculo_label((int)$dados["usuario"]->id_user);

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
	$id_editar = (int)$this->input->post('id');
	if(!$this->padrao_model->can_access_usuario($id_editar)){
		redirect('adm/usuarios');
	}
	$dd_user = $this->padrao_model->get_usuario_logado();
	$usuario_atual = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id_editar." LIMIT 1")->row();
	if(!$usuario_atual){
		redirect('adm/usuarios');
	}
	$nivel = $this->padrao_model->sanitize_child_level($this->input->post('nivel'), $dd_user, (int)$usuario_atual->nivel);
	
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

	if((int)$dd_user->nivel === 1){
		$dd['nivel'] = $nivel;
		$dd['id_user'] = $this->padrao_model->resolve_vinculo_id($nivel, $this->input->post('id_user'), $dd_user);
	}

	if($nivel == '3'){
		$dd['especialidade'] = $this->input->post('especialidade');
		$dd['classe']        = $this->input->post('classe');
	}

	if($nivel == 5 && $this->db->field_exists('afiliacoes', 'usuarios')){
		$dd['afiliacoes'] = $this->input->post('afiliacoes');
	}

	if($nivel < 5){
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
