<?
class Produtos_model extends CI_Model{
	
		function _construct()
	{
		// Call the Model constructor
		parent::_construct();
	}
	
	function getProdutosCategoria($categoria){

		$qr = $this->db->query("SELECT * FROM produtos WHERE categoria = ".$categoria);		
		return $qr;	
	}
		
	function getCategorias(){

		$qr = $this->db->query("SELECT n.categoria, (	SELECT COUNT( * ) 
													FROM noticias
													WHERE categoria = n.categoria
													) as qtd
													FROM noticias n
													GROUP BY n.categoria");
		
		return $qr;	
	}
	
	
	function cadastrar($tipo='cad'){
		
		// perfil produto
		if(isset($_POST['destaque'])){
			$dd['destaque'] = $_POST['destaque'];
		}
		
		$valor = str_replace("R$ ","",$_POST['preco']);
		$valor = str_replace(".","",$valor);
		$valor = str_replace(",",".",$valor);
		
		$valor_venda = str_replace("R$ ","",$_POST['preco_venda']);
		$valor_venda = str_replace(".","",$valor_venda);
		$valor_venda = str_replace(",",".",$valor_venda);
		
		
		
		#echo $valor;
		#return false;
		
		$dd = array(
			'id_user' => $this->session->userdata('id'),
			'modelo' => $this->input->post('modelo'),
			'id_categoria' => $this->input->post('id_categoria'),
			
			'preco' => $valor,
			'preco_venda' => $valor_venda,
			
			'qtd' => $this->input->post('qtd'),
			
			'id_fornecedor' => $this->input->post('id_fornecedor'),
			
			'codigo' => $this->input->post('codigo'),
			'codigo_barras' => $this->input->post('codigo_barras'),

			'status' => $this->input->post('status'),
			
			#'idade' => $this->input->post('idade'),
			#'peso' => $this->input->post('peso'),
			#'altura' => $this->input->post('altura'),
			#'tipo_fisico' => $this->input->post('tipo_fisico'),

			#'cidade' => $this->input->post('cidade'),
			#'telefones' => $this->input->post('telefones'),
			#'email' => $this->input->post('email'),
			
			#'facebook' => $this->input->post('facebook'),
			#'outros' => $this->input->post('outros'),
			#'atividade' => $this->input->post('atividade'),
			#'caracteristicas' => $this->input->post('caracteristicas'),
			
			'especificacoes' => $this->input->post('especificacoes')
		);

		#print_r($dd);
		#return false;
		
		//adicional
		if(isset($_POST['adicional'])){
			$dd['adicional'] = $_POST['adicional'];
		}else{
			$dd['adicional'] = 0;
		}
		
		// destaque
		if(isset($_POST['destaque'])){
			$dd['destaque'] = $_POST['destaque'];
		}else{
			$dd['destaque'] = 0;
		}
		
		
		
		if (isset($_POST['imagem'])) {
			$dd['img_portfolio'] = $_POST['imagem'];
		}

		if (isset($_POST['imagem2'])) {
			$dd['img_portfolio2'] = $_POST['imagem2'];
		}

		if (isset($_POST['imagem3'])) {
			$dd['img_portfolio3'] = $_POST['imagem3'];
		}

		if (isset($_POST['imagem4'])) {
			$dd['img_portfolio4'] = $_POST['imagem4'];
		}

		if (isset($_POST['imagem5'])) {
			$dd['img_portfolio5'] = $_POST['imagem5'];
		}



		

		if($tipo == 'cad'){
			$this->db->insert('produtos', $dd);
			#$id_produto = $this->db->insert_id();
			#$dd_atendimento['id_produto'] = $id_produto;
			#$this->db->insert('produtos_atendimento', $dd_atendimento);
		}
		if($tipo == 'edit'){
			if (isset($_POST['ordem'])) {
				$dd['ordem'] = $_POST['ordem'];
			}

			// link_pgt
			if (isset($_POST['link_pgt'])) {
				$dd['link_pgt'] = $_POST['link_pgt'];
			}
			// link_pgt
			if (isset($_POST['pix_pgt'])) {
				$dd['pix_pgt'] = $_POST['pix_pgt'];
			}
			// link_pgt
			if (isset($_POST['transferencia_pgt'])) {
				$dd['transferencia_pgt'] = $_POST['transferencia_pgt'];
			}

			$dd['keywords'] = $this->input->post('keywords');

			$where_user = array('id' =>  $this->input->post('id') , 'id_user' => $this->session->userdata('id'));
			$this->db->where($where_user);
			$this->db->update('produtos', $dd);
			
		}
		
		#print_r($_POST);
		#return false;
		
	}


	// cadastrar_categoria
	function cadastrar_categoria($tipo='cad'){
		
		// perfil produto
		if(isset($_POST['destaque'])){
			$dd['destaque'] = $_POST['destaque'];
		}
		
		
		
		#echo $valor;
		#return false;
		
		$dd = array(
			'id_user' => $this->session->userdata('id'),
			'nome' => $this->input->post('nome'),
			
			'status' => $this->input->post('status'),
			
		);
		
		
		
		if (isset($_POST['imagem'])) {
			$dd['img_portfolio'] = $_POST['imagem'];
		}
		if($tipo == 'cad'){
			$this->db->insert('produtos_categorias', $dd);
			#$id_produto = $this->db->insert_id();
			#$dd_atendimento['id_produto'] = $id_produto;
			#$this->db->insert('produtos_atendimento', $dd_atendimento);
		}
		if($tipo == 'edit'){
			$where_user = array('id' =>  $this->input->post('id') , 'id_user' => $this->session->userdata('id'));
			#print_r($where_user);

			#echo "<br>";
			#print_r($_POST);
			$this->db->where($where_user);
			$this->db->update('produtos_categorias', $dd);
			
		}
		
		#print_r($_POST);
		#return false;
		
	}
	
	
	function editar(){
		$valor = str_replace("R$ ","",$_POST['preco']);
		$valor = str_replace(".","",$valor);
		$valor = str_replace(",",".",$valor);
		
		
		$valor_venda = str_replace("R$ ","",$_POST['preco_venda']);
		$valor_venda = str_replace(".","",$valor_venda);
		$valor_venda = str_replace(",",".",$valor_venda);
		
		$dd = array(

			'id_user' => $this->session->userdata('id'),
			'modelo' => $this->input->post('modelo'),
			'id_categoria' => $this->input->post('id_categoria'),			
			'preco' => $valor,
			'preco_venda' => $valor_venda,			
			'qtd' => $this->input->post('qtd'),
			'id_fornecedor' => $this->input->post('id_fornecedor'),			
			'codigo' => $this->input->post('codigo'),						
			'especificacoes' => $this->input->post('especificacoes')

		);
		
		if (isset($_POST['imagem'])) {
			$dd['img_portfolio'] = $_POST['imagem'];
		}
		
		$this->db->where('id', $this->input->post('id'));

		if ($this->db->update('produtos', $dd)) {
			return true;
		} else {
			return false;	
		}

	}
	
	function remover($id) {
		$where = array('id' => $id , 'id_user' => $this->session->userdata('id'));
		$this->db->where($where);
		$this->db->delete('produtos');	
		
	}
	
	// renderizar valores boleanos
	function get_resposta($resposta){
		switch($resposta){
			case '0':
			echo "Não";
			break;
			
			case '1':
			echo "Sim";
			break;
			
			case '2':
			echo "A combinar";
			break;
			
			
		}
	}

	// VALIDA A NAVEGAÇÃO

########### FUNÇÃO PARA DATAS #######################
	// converter para data dd/mm/aaaa
	function converte_data($data){
		
		if (strstr($data, "/")) {//verifica se tem a barra /
		
		  $d = explode ("/", $data);//tira a barra
		  $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
		  return $invert_data;
		
		} elseif(strstr($data, "-")) {
		
		  $d = explode ("-", $data);
		  $invert_data = "$d[2]/$d[1]/$d[0]";
		  return $invert_data;
		
		} else {
		
		  return "Data invalida";
		
		}
	
	}

	
} // fecha class
?>
