<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atendimento extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		#$this->padrao_model->indexador();
		$this->usuarios_model->verSession();

   } // fecha fn USER

function Index(){
	#echo "teste";
	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios");
	$dados['nivel'] = 1;

	$dd_user = $this->padrao_model->get_by_id($this->session->userdata('id'),'usuarios')->row();

	if($dd_user->nivel == "4"){
		$id_prestador = $dd_user->id_user;
	}

	if($dd_user->nivel == "3"){
		$id_prestador = $dd_user->id;
	}

	if($dd_user->nivel == "2"){
		$id_prestador = $dd_user->id;
	}

	$id_user = $id_prestador;

	#echo $id_user;
	#return false;
	

	$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos where id_prestador = $id_user ");
	#$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos");
	$dados["qr_agendamentos"] = $qr_agendamentos;
	$dados["dd"] = $dd_user;
	$dados["id_user"] = $id_user;

	$this->load->view('adm/usuarios/new/atendimentos', $dados);
	#$this->load->view('adm/usuarios/lista', $dados);
	#$this->load->view('adm/usuarios/new/lista', $dados);

}

function novo($id_user){
	$id_user = (int)$id_user;
	$dados["dd"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id_user ")->row();
	
	// criar regra para filtrar de acordo com o login
	$dados['prestadores'] = $this->db->query("SELECT * FROM usuarios WHERE nivel = 3"); 

	$dados["nivel"] = $dados["dd"]->nivel;

	$this->load->view('adm/atendimento/atendimento' , $dados);	
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

	#print_r($_POST);
	#return false;
	#Array ( [id_paciente] => 446 [id_prestador] => 444 [tipo] => Consulta [data_agenda] => 2025-12-08 [hora_agenda] => 10:00 ) 
	
	$dd = array(
		#'id_setor' => $_POST['id_setor'],
		'id_user' => $this->session->userdata('id'),
		'id_paciente' => $this->input->post('id_paciente'),
		'id_prestador' => $this->input->post('id_prestador'),
		'tipo' => $this->input->post('tipo'),
		'data_agenda' => $this->input->post('data_agenda'),
		'hora_agenda' => $this->input->post('hora_agenda'),
		'data_hora_agenda' => $this->input->post('data_agenda')." ".$this->input->post('hora_agenda'),
		
	);
	

	#$this->db->where('id', $_POST['id']);	
	if ($this->db->insert('agendamentos', $dd)) {
		redirect('adm/usuarios/prontuario/'.$dd['id_paciente']);
	} else {
		echo "Falha ao agendar paciente!";	
	}
	
}

function set() {

	#print_r($_POST);
	#return false;
	#Array  [id_agenda] => 3 [atendimento_inicial] => atendimentoasd asd asd [avaliacao] => avaliaçãoasd asdas dasd

	$id_agenda = $this->input->post('id_agenda');

	$qr_agenda = $this->db->query("SELECT * FROM agendamentos where id = $id_agenda ");

	if($qr_agenda->num_rows() == 0){ echo "Falha 114"; return; }

	$dd_agenda = $qr_agenda->row();

	// valida se é o prestador que salva os dados
	#if($this->session->userdata('id') != $dd_agenda->id_prestador){ echo "Falha 157"; return; }

	$dd = array(
		#'id_setor' => $_POST['id_setor'],
		'id_user_alt' => $this->session->userdata('id'),
		'atendimento_inicial' => $this->input->post('atendimento_inicial'),
		'avaliacao' => $this->input->post('avaliacao'),
		'reavaliacao' => $this->input->post('reavaliacao'),	
		'status' => 2 	
		
	);


	

	$this->db->where('id', $id_agenda);	
	if ($this->db->update('agendamentos', $dd)) {
		redirect('adm/usuarios/prontuario/'.$dd_agenda->id_paciente);
	} else {
		echo "Falha ao agendar paciente!";	
	}
	
}




function prontuario__($id_user=1){

	//echo "teste";
	if($id_user == 1){ return; }
	$this->load->model('padrao_model');
	$nivel = 5;
	$dados['nivel'] = $nivel;
	
	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");

	$dados["dd"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id_user ")->row();

	$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos where id_paciente = $id_user ");
	$dados["qr_agendamentos"] = $qr_agendamentos;


	$this->load->view('adm/usuarios/new/prontuario', $dados);

} // x fn

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

function exames($id_user=1){
	$id_user = (int)$id_user;
	if($id_user == 1){ return; }
	$this->load->model('padrao_model');
	$nivel = 5;
	$dados['nivel'] = $nivel;
	
	$dados["usuarios"] = $this->db->query("SELECT * FROM usuarios WHERE nivel = $nivel ");

	$dados["dd"] = $this->db->query("SELECT * FROM usuarios WHERE id = $id_user ")->row();

	$qr_agendamentos = $this->db->query("SELECT * FROM agendamentos where id_paciente = $id_user ORDER BY id desc");
	$dados["qr_agendamentos"] = $qr_agendamentos;

	// 
	$dados["exames"] = $this->db->query("SELECT * FROM exames WHERE id_user = '1' ");

	$dados["exames_user"] = $this->db->query("SELECT * FROM usuarios_exames_atendimento WHERE id_user = $id_user ");


	$this->load->view('adm/usuarios/new/exames', $dados);

} // x fn

	function set_exame(){
		#print_r($_POST);
		#echo "<br>";
		$dd_post = [  
			'id_user' => $this->input->post('id_user'),
			'id_agendamento' => $this->input->post('id_agendamento'), 			
			'obs' => $this->input->post('obs'),
		];

		$dd_agenda = $this->padrao_model->get_by_id($this->input->post('id_agendamento'),'agendamentos')->row();
		if($dd_agenda){
			$dd_post['data_exame'] = $dd_agenda->data_agenda;
		}

		$this->db->where($dd_post);
		$qr_ver = $this->db->get('usuarios_exames');

		if($qr_ver->num_rows() == 0){
			$this->db->insert('usuarios_exames', $dd_post);
			$id_exame_ag = $this->db->insert_id();
		}else{
			$id_exame_ag = $qr_ver->row()->id;
		}

		if(isset($_POST['exames'])){
			#echo "TEM";
			foreach($this->input->post('exames') as $ex){
				#echo "<br>";
				#echo $ex;
				$dd_exame = [
					'id_atendimento' => $dd_post['id_agendamento'],
					'id_exame_atendimento' => $id_exame_ag,
					'id_user' => $dd_post['id_user'],
					'id_exame' => $ex
				];
				$this->db->where($dd_exame);
				$qr_ver_exa_age = $this->db->get('usuarios_exames_atendimento');
				if($qr_ver_exa_age->num_rows() == 0){
					$this->db->insert('usuarios_exames_atendimento' , $dd_exame);
				}
			}
		}

		redirect('adm/atendimento/exames/'.$dd_post['id_user']);

		//'exames' => Array ( [0] => 1 [1] => 2 ) 
	}

###################### set status
function set_status_agenda($id_agenda,$status){
	$id_agenda = (int)$id_agenda;
	$status    = (int)$status;
	$refer	= $this->agent->referrer();
	
	#return false;
	#$this->padrao_model->indexador();
	$dd = $this->padrao_model->get_by_id($id_agenda,'agendamentos')->row();
	#echo $status;
	#echo "<br>";
	if($status == "1"){
		$new_status = 2;
	}
	if($status == "0"){
		$new_status = 1;
	}
	if($status == "2"){
		$new_status = 0;
	}

	$dd_status = array('status' => $new_status);
	$this->db->where('id',$id_agenda);
	$this->db->update('agendamentos',$dd_status);
	#print_r($dd_status);
	$refer = str_replace(base_url(),"",$refer);
	#echo $refer;
	redirect($refer);
	#redirect('adm/usuarios/prontuario/'.$dd->id_paciente,'refresh');

}

function set_status_exame($id_exame,$status){
	$id_exame = (int)$id_exame;
	$status   = (int)$status;
	#$this->padrao_model->indexador();
	$dd = $this->padrao_model->get_by_id($id_exame,'usuarios_exames_atendimento')->row();
	#echo $id_exame;
	#print_r($dd);
	#echo $status;
	#echo "<br>";
	if($status == "1"){
		$new_status = 2;
	}
	if($status == "0"){
		$new_status = 1;
	}
	if($status == "2"){
		$new_status = 0;
	}

	$dd_status = array('status' => $new_status);
	$this->db->where('id',$id_exame);
	$this->db->update('usuarios_exames_atendimento',$dd_status);
	#print_r($dd_status);

	redirect('adm/atendimento/exames/'.$dd->id_user,'refresh');

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
	$id = (int)$id;
	$this->db->where('id', $id);
	$this->db->delete('usuarios');
	redirect('adm/usuarios','refresh');
}

// ─── Arquivos do Paciente ─────────────────────────────────────────────────────

function upload_arquivo(){
	$id_paciente    = (int)$this->input->post('id_paciente');
	$id_agendamento = (int)$this->input->post('id_agendamento');
	$descricao      = $this->input->post('descricao');

	if($id_paciente == 0){
		echo json_encode(['ok' => false, 'msg' => 'Paciente inválido']);
		return;
	}

	$config['upload_path']   = './uploads/pacientes/';
	$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx';
	$config['max_size']      = 10240; // 10 MB
	$config['encrypt_name']  = TRUE;
	$this->load->library('upload', $config);

	if(!$this->upload->do_upload('arquivo')){
		echo json_encode(['ok' => false, 'msg' => strip_tags($this->upload->display_errors())]);
		return;
	}

	$upload = $this->upload->data();
	$ext    = strtolower($upload['file_ext']);
	$tipo   = in_array($ext, ['.jpg','.jpeg','.png','.gif']) ? 'imagem' : rtrim($ext,'.');

	$this->db->insert('pacientes_arquivos', [
		'id_paciente'    => $id_paciente,
		'id_agendamento' => $id_agendamento,
		'id_user'        => $this->session->userdata('id'),
		'arquivo'        => $upload['file_name'],
		'nome_original'  => $upload['orig_name'],
		'tipo'           => $tipo,
		'descricao'      => $descricao,
	]);

	echo json_encode(['ok' => true, 'msg' => 'Arquivo enviado com sucesso']);
}

function del_arquivo($id){
	$id = (int)$id;
	$qr = $this->padrao_model->get_by_id($id, 'pacientes_arquivos')->row();
	if(!$qr){ redirect('adm/atendimento'); }

	$id_paciente = $qr->id_paciente;
	$arquivo     = './uploads/pacientes/'.$qr->arquivo;
	if(file_exists($arquivo)){ unlink($arquivo); }

	$this->db->where('id', $id);
	$this->db->delete('pacientes_arquivos');

	redirect('adm/usuarios/prontuario/'.$id_paciente);
}


}
