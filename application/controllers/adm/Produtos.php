<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {

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
		$id = $this->session->userdata('id');
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id_user = '".$this->session->userdata('id')."' ORDER BY nome asc ");
		$dados["produtos"] = $this->db->query("SELECT p.id, p.codigo,p.qtd,p.destaque, p.adicional, p.status, p.preco, p.preco_venda,p.id_fornecedor,p.atividade, p.modelo, p.img_portfolio, p.id_categoria,p.especificacoes, p.dt,pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id_user  = '".$id."'
												ORDER BY p.id desc");

		$dados["usuario"] = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id)->row();
		#$this->load->view('adm/produtos/lista', $dados);
		$this->load->view('adm/produtos/new/produtos', $dados);
		
		//$this->listar();
	}
	
	
	function novo(){
		//echo "teste";
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias ORDER BY nome asc ");
		$dados["fornecedores"] = $this->db->query("SELECT * FROM parceiros");
		

		$this->load->view('adm/produtos/novo', $dados);

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
		$id_user = $this->session->userdata('id');
		$produto = $this->db->query("SELECT 
										p.id,
										p.id_user, 
										p.id_categoria,
										p.modelo, 
										p.preco, 
										p.preco_venda, 
										p.id_fornecedor,
										p.codigo,
										p.qtd,
										p.destaque,
										p.status,
										p.ordem,
										p.codigo_barras,

										p.keywords,
										
										p.img_portfolio, 
										p.img_portfolio2, 
										p.img_portfolio3, 
										p.img_portfolio4, 
										p.img_portfolio5, 
										p.link_pgt, 
										p.pix_pgt, 
										p.transferencia_pgt, 
										p.especificacoes, 
										p.adicional,
										
										
										pc.nome 										
										
										FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id = ?", $id)->row();
		

		$dados["produto"] = $produto;
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id_user = '".$id_user."' ");

		$dados["produtos"] = $this->db->query("SELECT p.id, p.codigo,p.qtd,p.destaque, p.preco, p.preco_venda,p.id_fornecedor,p.atividade, p.modelo, p.img_portfolio, p.id_categoria,p.especificacoes, p.dt,pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id_user  = '".$id_user."'
												ORDER BY p.id desc");

		#echo $produto->id_user." != ".$this->session->userdata('id');
		#return false;
		if($produto->id_user != $this->session->userdata('id')){
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
		$id = $this->session->userdata('id');
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id_user = '".$this->session->userdata('id')."' ");

		$id = $this->session->userdata('id');
		$dados["produtos"] = $this->db->query("SELECT p.id, p.codigo,p.qtd,p.destaque, p.status, p.preco, p.preco_venda,p.id_fornecedor,p.atividade, p.modelo, p.img_portfolio, p.id_categoria,p.especificacoes, p.dt,pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id_user  = '".$id."'
												ORDER BY p.id desc");


		$dados["produtos"] = $this->db->query("SELECT p.id, p.codigo,p.qtd,p.destaque, p.status, p.preco, p.preco_venda,p.id_fornecedor,p.atividade, p.modelo, p.img_portfolio, p.id_categoria,p.especificacoes, p.dt,pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id_user  = '".$id."'
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
		$id_user = $this->session->userdata('id');
		$qr_categoria = $this->db->query("SELECT * FROM produtos_categorias WHERE id = $id");

		#echo $qr_categoria->num_rows();
		

		$categoria = $qr_categoria->row();
		#print_r($categoria);
		$dados["categoria"] = $categoria;
		


		

		$dados["produto"] = $produto;
		$dados["categorias"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id_user = '".$id_user."' ");

		$dados["produtos"] = $this->db->query("SELECT p.id, p.codigo,p.qtd,p.destaque, p.preco, p.preco_venda,p.id_fornecedor,p.atividade, p.modelo, p.img_portfolio, p.id_categoria,p.especificacoes, p.dt,pc.nome FROM produtos p 
												INNER JOIN produtos_categorias pc ON pc.id = p.id_categoria
												WHERE p.id_user  = '".$id_user."'
												ORDER BY p.id desc");

		#echo $produto->id_user." != ".$this->session->userdata('id');
		#return false;
		if($categoria->id_user != $this->session->userdata('id')){
			redirect('adm/categorias','refresh');
		}
		#$dados["fornecedores"] = $this->db->query("SELECT * FROM parceiros");
		#$dados["atendimento"] = $this->db->query("SELECT * FROM produtos_categorias WHERE id <> ?", $produto->id);
		#$this->load->view('adm/produtos/edicao', $dados);
		$this->load->view('adm/produtos/new/edicao_cat', $dados);
		#echo $id." 333333";
		#return false;
		#redirect('adm/produtos');
	}




	function pedido($id_pedido){
		$this->db->where('id',$id_pedido);
		$qr = $this->db->get('pedidos');
		$dados['pedido'] = $qr;
		$dados['id_pedido'] = $id_pedido;
		$dados['carrinho'] = $this->db->query("SELECT * FROM carrinho WHERE id_pedido = '".$id_pedido."' AND status = 1 ORDER BY dt asc");
		$dados['comprador'] = $this->padrao_model->get_by_id($qr->row()->id_comprador,'MJ_users')->row();
		$this->load->view('adm/produtos/pedido' , $dados);
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
		$this->db->where('id',$id_categoria);
		$this->db->delete('produtos_categorias');
		redirect("adm/produtos/categorias/");

	}
	
	
	
	
	function remover($id){
			
	   	$this->produtos_model->remover($id);
		redirect('adm/produtos' , 'refresh');
	
	}	
	
	
			
	function setStatus($id, $status){
		$dd = array('status' => $status);
		$this->db->where('id', $id);
		$this->db->update('produtos' , $dd);

		redirect('adm/produtos' , 'refresh');
	}

	function setStatus_cat($id, $status){
		$dd = array('status' => $status);
		$this->db->where('id', $id);
		$this->db->update('produtos_categorias' , $dd);

		redirect('adm/produtos/categorias' , 'refresh');
	}
}
