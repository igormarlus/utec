<?
class Usuarios_model extends CI_Model{
	
	function _construct()
	{
		// Call the Model constructor
		parent::_construct();
	}

	
	function logar(){

		$login       = $this->input->post('login');
		$senha_input = $this->input->post('senha');

		$this->db->where('login', $login);
		$qr_login = $this->db->get('usuarios');

		if($qr_login->num_rows() > 0){
			$dd_user   = $qr_login->row();
			$senha_ok  = false;

			if(password_verify($senha_input, $dd_user->senha)){
				$senha_ok = true;
			} elseif($dd_user->senha === $senha_input) {
				// migração: senha ainda em texto puro → rehasha silenciosamente
				$this->db->where('id', $dd_user->id);
				$this->db->update('usuarios', ['senha' => password_hash($senha_input, PASSWORD_DEFAULT)]);
				$senha_ok = true;
			}

			if(!$senha_ok){
				redirect('admin');
			}

			$dd_session = array(
				'usr'   => true,
				'id'    => $dd_user->id,
				'nome'  => $dd_user->nome,
				'nivel' => $dd_user->nivel,
				'login' => $login
			);
			$this->session->set_userdata($dd_session);

			if($dd_user->nivel == 2 || $dd_user->nivel == 3 || $dd_user->nivel == 4){
				redirect('adm/atendimento');
			}

			redirect('adm/usuarios');

		}else{
			redirect('admin');
		}

	}

	// VALIDA A NAVEGAÇÃO
	function verSession(){
	
		$ss = $this->session->userdata('usr');
		if(isset($ss)){
			if($ss == true){
			
			}else{
				redirect('admin');
			}
		}
	}
	
	function cadastrar() {
		
		
		
		$dd = array(
					'id_unidade' => $_POST['id_unidade'],
					'id_setor' => $_POST['id_setor'],
					'nome' => $_POST['nome'],
					'login' => $_POST['login'],
					'senha' => $_POST['senha'],
					'email' => $_POST['email'],
					#'setor' => $_POST['setor'],
					'nivel' => $_POST['nivel']
		);
		
		// define vez
		if($_POST['id_setor'] == 2){
			$this->db->where(array('id_unidade' => $_POST['id_unidade'] , 'id_setor' => '2'));
			$qr_vez = $this->db->get('usuarios');
			if($qr_vez->num_rows() == 0){
				$dd['vez'] = 1;
			} else{
				$dd['vez'] = $qr_vez->row()->vez;
			}
		
		}
		
				
		if ($this->db->insert('usuarios', $dd)) {
			return true;
		} else {
			return false;	
		}	 
	
	}

	function saldo_car($id_cliente=1){
		
		$carrinho = $this->db->query("SELECT * FROM carrinho WHERE id_user = '".$this->session->userdata('id')."' AND status = 1 AND id_cliente = $id_cliente ORDER BY dt asc");

		$total = 0; 
			foreach($carrinho->result() as $car){ 
			$dd_pro = $this->padrao_model->get_by_id($car->id_produto,'produtos');
			$produto = $dd_pro->row();
			$valor = $car->qtd * $produto->preco_venda;
			$total += $valor;
		}
		echo "R$ ".number_format($total, 2, ',', '.');
	}

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
