<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {

	private function get_scope_sql(){
		$dd_user = $this->padrao_model->get_usuario_logado();
		$scope_ids = $this->padrao_model->get_scope_user_ids($dd_user);
		if((int)$dd_user->nivel === 1){
			return ['user' => $dd_user, 'sql' => ''];
		}
		return ['user' => $dd_user, 'sql' => $this->padrao_model->ids_to_sql_in($scope_ids)];
	}
	
	function __construct()
		   {
				parent::__construct();
				$this->load->library('session');
				$this->load->helper(array('form','url'));
				$this->load->model('adm/usuarios_model');
				$this->load->model('adm/produtos_model');
				$this->load->model('padrao_model');
				$this->padrao_model->indexador();
				$this->usuarios_model->verSession();
		   } // fecha fn USER
	
	function Index(){
		$scope = $this->get_scope_sql();
		$id = $this->session->userdata('id');
		$status = $this->input->get('status');
		$id_categoria = (int)$this->input->get('id_categoria');
		$busca = trim((string)$this->input->get('busca'));
		$where = $scope['sql'] !== '' ? " WHERE id_user IN (".$scope['sql'].") " : "";
		$where_produtos = [];
		if($scope['sql'] !== ''){
			$where_produtos[] = "p.id_user IN (".$scope['sql'].")";
		}
		if($status !== null && $status !== ''){
			$where_produtos[] = "p.status = ".(int)$status;
		}
		if($id_categoria > 0){
			$where_produtos[] = "p.id_categoria = ".$id_categoria;
		}
		if($busca !== ''){
			$busca_sql = $this->db->escape_like_str($busca);
			$where_produtos[] = "(p.modelo LIKE '%".$busca_sql."%' OR p.codigo LIKE '%".$busca_sql."%' OR p.especificacoes LIKE '%".$busca_sql."%')";
		}
		$where_produtos_sql = count($where_produtos) ? " WHERE ".implode(" AND ", $where_produtos)." " : "";
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias ".$where." ORDER BY nome asc ");
		$dados["produtos"] = $this->db->query("SELECT p.*, pc.nome, u.nome as responsavel_nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												LEFT JOIN usuarios u ON u.id = p.id_user
												".$where_produtos_sql."
												ORDER BY p.id desc");
		$where_cards = $scope['sql'] !== '' ? " WHERE id_user IN (".$scope['sql'].") " : "";
		$dados['resumo_planos'] = [
			'total' => (int)$this->db->query("SELECT COUNT(id) as total FROM produtos ".($where_cards ?: ""))->row()->total,
			'ativos' => (int)$this->db->query("SELECT COUNT(id) as total FROM produtos ".($where_cards ? $where_cards." AND status = 1" : " WHERE status = 1"))->row()->total,
			'inativos' => (int)$this->db->query("SELECT COUNT(id) as total FROM produtos ".($where_cards ? $where_cards." AND status = 0" : " WHERE status = 0"))->row()->total,
			'tipos' => (int)$this->db->query("SELECT COUNT(id) as total FROM produtos_categorias ".($where ?: ""))->row()->total,
		];
		$dados['filtros'] = ['status' => $status, 'id_categoria' => $id_categoria, 'busca' => $busca];

		$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id)->row();
		#$this->load->view('adm/produtos/lista', $dados);
		$this->load->view('adm/produtos/new/produtos', $dados);
		
		//$this->listar();
	}
	
	
	function novo(){
		redirect('adm/produtos');

	}
	
	function upImgPortfolio(){
	//echo "teste";
		##################### X IMAGENS #########################
		
		//upload codeigniter lib
		$config['upload_path'] = './imagens/produtos/';
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
			$conf['source_image'] = './imagens/produtos/'.$data['file_name'];
			$conf['new_image'] = './imagens/produtos/min/'.$data['file_name'];
			$conf['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf['height'] = 60;
			$conf['width'] = 80;
			$this->image_lib->initialize($conf); 
			$this->image_lib->resize();
			
			
			
			$conf3['image_library'] = 'gd2';
			$conf3['source_image'] = './imagens/produtos/'.$data['file_name'];
			$conf3['new_image'] = './imagens/produtos/por/'.$data['file_name'];
			$conf3['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf3['height'] = 375;
			$conf3['width'] = 500;
			$this->image_lib->initialize($conf3); 
			$this->image_lib->resize();
			
			$conf2['image_library'] = 'gd2';
			$conf2['source_image'] = './imagens/produtos/'.$data['file_name'];
			$conf2['new_image'] = './imagens/produtos/des/'.$data['file_name'];
			$conf2['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf2['height'] = 120;
			$conf2['width'] = 160;
			$this->image_lib->initialize($conf2); 
			$this->image_lib->resize();
			
			echo "<img src='".base_url()."imagens/produtos/min/".$data['file_name']."'>
					<input name='imagem' type='hidden' value='".$data['file_name']."'>";
		}


	}
	
	function cadastrar($tipo='cad') {		
		$this->produtos_model->cadastrar($tipo);
		redirect('adm/produtos' , 'refresh');		
	}

	function upImgPost($n=0){
	//echo "teste";
		##################### X IMAGENS #########################
		if($n == 0){
			$photoimg = "photoimg";
		}else{
			$photoimg = "photoimg".$n;			
		}
		
		//upload codeigniter lib
		$config['upload_path'] = './imagens/produtos/';
		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  	$this->load->library('upload', $config);

		
		if (!$this->upload->do_upload($photoimg)) {
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
			$conf['source_image'] = './imagens/produtos/'.$data['file_name'];
			$conf['new_image'] = './imagens/produtos/min/'.$data['file_name'];
			$conf['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf['height'] = 72;
			$conf['width'] = 120;
			$this->image_lib->initialize($conf); 
			$this->image_lib->resize();
			
			$conf2['image_library'] = 'gd2';
			$conf2['source_image'] = './imagens/produtos/'.$data['file_name'];
			$conf2['new_image'] = './imagens/produtos/des/'.$data['file_name'];
			$conf2['maintain_ratio'] = FALSE;
			//$conf['master_dim'] = 'height';
			$conf2['height'] = 210;
			$conf2['width'] = 300;
			$this->image_lib->initialize($conf2); 
			$this->image_lib->resize();

			if($n == 0){
				echo "<img src='".base_url()."imagens/produtos/min/".$data['file_name']."'>
					<input name='imagem' type='hidden' value='".$data['file_name']."'>";
			}else{
				echo "<img src='".base_url()."imagens/produtos/min/".$data['file_name']."'>
					<input name='imagem".$n."' type='hidden' value='".$data['file_name']."'>";
				#$photoimg = "photoimg".$n;			
			}
			
			
		}


	}
	
	
	function edicao($id){
		$scope = $this->get_scope_sql();
		$produto = $this->db->query("SELECT 
										p.*,
										pc.nome
										FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id = ?", $id)->row();
		

		$dados["produto"] = $produto;
		$where = $scope['sql'] !== '' ? " WHERE id_user IN (".$scope['sql'].") " : "";
		$where_produtos = $scope['sql'] !== '' ? " WHERE p.id_user IN (".$scope['sql'].") " : "";
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias ".$where." ");

		$dados["produtos"] = $this->db->query("SELECT p.*, pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												".$where_produtos."
												ORDER BY p.id desc");

		#echo $produto->id_user." != ".$this->session->userdata('id');
		#return false;
		if(!$produto || !$this->padrao_model->can_access_usuario((int)$produto->id_user)){
			redirect('adm/produtos','refresh');
		}
		#$dados["fornecedores"] = $this->db->query("SELECT * FROM parceiros");
		#$dados["atendimento"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id <> ?", $produto->id);
		#$this->load->view('adm/produtos/edicao', $dados);
		$this->load->view('adm/produtos/new/edicao', $dados);
		#echo $id." 333333";
		#return false;
		#redirect('adm/produtos');
	}
	
	
	public function editar() {   
		   
		if ($this->produtos_model->editar()) {
			redirect('adm/produtos' , 'refresh');
		} else {
			echo "Falha ao editar produto!";	
		}

	   
	}


	function categorias(){
		$scope = $this->get_scope_sql();
		$id = $this->session->userdata('id');
		$where = $scope['sql'] !== '' ? " WHERE id_user IN (".$scope['sql'].") " : "";
		$where_produtos = $scope['sql'] !== '' ? " WHERE p.id_user IN (".$scope['sql'].") " : "";
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias ".$where." ");
		$dados["produtos"] = $this->db->query("SELECT p.*, pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												".$where_produtos."
												ORDER BY p.id desc");
		$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id)->row();
		#$this->load->view('adm/produtos/lista', $dados);
		$this->load->view('adm/produtos/new/categorias', $dados);
		
		//$this->listar();
	}

	//cadastrar_categoria
	function cadastrar_categoria($tipo='cad') {		
		$this->produtos_model->cadastrar_categoria($tipo);
		redirect('adm/produtos/categorias' , 'refresh');		
	}


	function edicao_cat($id){
		$id      = (int)$id;
		$scope = $this->get_scope_sql();
		$qr_categoria = $this->db->query("SELECT * FROM produtos_categorias WHERE id = $id");

		#echo $qr_categoria->num_rows();
		

		$categoria = $qr_categoria->row();
		#print_r($categoria);
		$dados["categoria"] = $categoria;
		

		$where = $scope['sql'] !== '' ? " WHERE id_user IN (".$scope['sql'].") " : "";
		$where_produtos = $scope['sql'] !== '' ? " WHERE p.id_user IN (".$scope['sql'].") " : "";
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias ".$where." ");

		$dados["produtos"] = $this->db->query("SELECT p.*, pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												".$where_produtos."
												ORDER BY p.id desc");

		#echo $produto->id_user." != ".$this->session->userdata('id');
		#return false;
		if(!$categoria || !$this->padrao_model->can_access_usuario((int)$categoria->id_user)){
			redirect('adm/produtos/categorias','refresh');
		}
		#$dados["fornecedores"] = $this->db->query("SELECT * FROM parceiros");
		#$dados["atendimento"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id <> ?", $produto->id);
		#$this->load->view('adm/produtos/edicao', $dados);
		$this->load->view('adm/produtos/new/edicao_cat', $dados);
		#echo $id." 333333";
		#return false;
		#redirect('adm/produtos');
	}




	function rel_pedidos(){
		$scope = $this->get_scope_sql();
		$status = $this->input->get('status');
		$data_ini = trim((string)$this->input->get('data_ini'));
		$data_fim = trim((string)$this->input->get('data_fim'));
		$busca = trim((string)$this->input->get('busca'));
		$where = [];
		if($scope['sql'] !== ''){
			$where[] = "p.id_cliente IN (".$scope['sql'].")";
		}
		if($status !== null && $status !== ''){
			$where[] = "p.status = ".(int)$status;
		}
		if($data_ini !== ''){
			$where[] = "DATE(p.dt) >= ".$this->db->escape($data_ini);
		}
		if($data_fim !== ''){
			$where[] = "DATE(p.dt) <= ".$this->db->escape($data_fim);
		}
		if($busca !== ''){
			$busca_sql = $this->db->escape_like_str($busca);
			$where[] = "(p.id_pedido LIKE '%".$busca_sql."%' OR uc.nome LIKE '%".$busca_sql."%' OR ur.nome LIKE '%".$busca_sql."%')";
		}
		$where_sql = count($where) ? " WHERE ".implode(" AND ", $where)." " : "";
		$sql_base = "FROM pedidos p
			LEFT JOIN usuarios uc ON uc.id = p.id_user
			LEFT JOIN usuarios ur ON ur.id = p.id_cliente";
		$dados['pedidos_finalizados'] = $this->db->query("SELECT p.*, uc.nome as cliente_nome, ur.nome as responsavel_nome ".$sql_base." ".$where_sql." ORDER BY p.id desc LIMIT 100");
		$dados['resumo_assinaturas'] = [
			'total' => (int)$this->db->query("SELECT COUNT(p.id) as total ".$sql_base." ".$where_sql)->row()->total,
			'ativas' => (int)$this->db->query("SELECT COUNT(p.id) as total ".$sql_base." ".(count($where) ? $where_sql." AND p.status = 2" : " WHERE p.status = 2"))->row()->total,
			'analise' => (int)$this->db->query("SELECT COUNT(p.id) as total ".$sql_base." ".(count($where) ? $where_sql." AND p.status = 1" : " WHERE p.status = 1"))->row()->total,
			'pendentes' => (int)$this->db->query("SELECT COUNT(p.id) as total ".$sql_base." ".(count($where) ? $where_sql." AND p.status = 0" : " WHERE p.status = 0"))->row()->total,
			'total_valor' => (float)$this->db->query("SELECT COALESCE(SUM(p.total),0) as total ".$sql_base." ".$where_sql)->row()->total,
		];
		$dados['filtros'] = ['status' => $status, 'data_ini' => $data_ini, 'data_fim' => $data_fim, 'busca' => $busca];
		$dados['dd_user'] = $scope['user'];
		$this->load->view('adm/produtos/new/rel_pedidos', $dados);
	}

	function pedido($id_pedido){
		$id_pedido = trim($id_pedido);
		$dados['id_pedido'] = $id_pedido;
		$qr = $this->db->query("SELECT * FROM pedidos WHERE id_pedido = ? LIMIT 1", array($id_pedido));
		if(!$qr->num_rows()){
			redirect('adm/produtos/rel_pedidos');
		}
		$pedido_item = $qr->row();
		if(!$this->padrao_model->can_access_usuario((int)$pedido_item->id_cliente)){
			redirect('adm/produtos/rel_pedidos');
		}
		$dados['pedido'] = $qr;
		$dados['carrinhos'] = $this->db->query("SELECT * FROM carrinho_hist WHERE id_pedido = ? ORDER BY dt asc, id asc", array($id_pedido));
		$dados['comprador'] = $this->padrao_model->get_by_id($pedido_item->id_user,'usuarios')->row();
		$this->load->view('adm/produtos/new/pedido' , $dados);
	}
	

	
	
	function galeria(){
		/*
		$produto = $this->db->query("SELECT p.id, p.modelo, p.img_portfolio, p.caracteristicas, p.especificacoes, p.id_categoria, pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id = ?", $id)->row();
		$dados["produto"] = $produto;
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id <> ?", $produto->id_categoria);
		*/
		$this->db->order_by('id','desc');
        $dados['anexos'] =  $this->db->get('ensaios_fotos');
		$this->load->view('adm/galeria' , $dados);
	}
	
	
	
	
	function del_anexo($id_anexo){
		$dd_anexo = $this->padrao_model->get_by_id($id_anexo,'ensaios_fotos')->row();
		$id_modelo = $dd_anexo->id_modelo;
		unlink("files/".$dd_anexo->anexo);
		$this->db->where('id',$id_anexo);
		$this->db->delete('ensaios_fotos');
		redirect("adm/produtos/ensaios/".$id_modelo , 'refresh');
	}
	
	function del_video($id_anexo){
		$dd_anexo = $this->padrao_model->get_by_id($id_anexo,'ensaios_videos')->row();
		$id_modelo = $dd_anexo->id_modelo;
		unlink("files/".$dd_anexo->anexo);
		$this->db->where('id',$id_anexo);
		$this->db->delete('ensaios_videos');
		redirect("adm/produtos/ensaios/".$id_modelo);
	}

	function remover_cat($id_categoria){
		$dd_categoria = $this->padrao_model->get_by_id($id_categoria,'produtos_categorias')->row();
		if(!$dd_categoria || !$this->padrao_model->can_access_usuario((int)$dd_categoria->id_user)){
			redirect("adm/produtos/categorias/");
		}
		$this->db->where('id',$id_categoria);
		$this->db->delete('produtos_categorias');
		redirect("adm/produtos/categorias/");

	}
	
	
	
	
	function remover($id){
		$dd_produto = $this->padrao_model->get_by_id($id,'produtos')->row();
		if(!$dd_produto || !$this->padrao_model->can_access_usuario((int)$dd_produto->id_user)){
			redirect('adm/produtos' , 'refresh');
		}
	   	$this->produtos_model->remover($id);
		redirect('adm/produtos' , 'refresh');
	
	}	
	
	
			
	function setStatus($id, $status){
		$dd_produto = $this->padrao_model->get_by_id($id,'produtos')->row();
		if(!$dd_produto || !$this->padrao_model->can_access_usuario((int)$dd_produto->id_user)){
			redirect('adm/produtos' , 'refresh');
		}
		$dd = array('status' => $status);
		$this->db->where('id', $id);
		$this->db->update('produtos' , $dd);

		redirect('adm/produtos' , 'refresh');
	}

	function setStatus_cat($id, $status){
		$dd_categoria = $this->padrao_model->get_by_id($id,'produtos_categorias')->row();
		if(!$dd_categoria || !$this->padrao_model->can_access_usuario((int)$dd_categoria->id_user)){
			redirect('adm/produtos/categorias' , 'refresh');
		}
		$dd = array('status' => $status);
		$this->db->where('id', $id);
		$this->db->update('produtos_categorias' , $dd);

		redirect('adm/produtos/categorias' , 'refresh');
	}
}
