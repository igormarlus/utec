<?
class Usuarios_model extends CI_Model{
	
	function _construct()
	{
		// Call the Model constructor
		parent::_construct();
	}

	
	function logar(){

		$login       = trim((string)$this->input->post('login'));
		$senha_input = (string)$this->input->post('senha');
		$msg_erro    = 'Usuário ou senha inválidos.';

		if($login === '' || $senha_input === ''){
			$this->session->set_flashdata('login_error', $msg_erro);
			redirect('admin');
			return;
		}

		$this->db->where('login', $login);
		$qr_login = $this->db->get('usuarios');

		if($qr_login->num_rows() === 0){
			$this->load->helper('acesso');
			if(utec_acesso_email_valido($login)){
				$por_email = $this->db->query(
					"SELECT * FROM usuarios WHERE LOWER(email) = ? AND nivel BETWEEN 1 AND 4 LIMIT 2",
					[strtolower($login)]
				)->result();
				if(count($por_email) === 1){
					$qr_login = $this->db->query("SELECT * FROM usuarios WHERE id = ?", [(int)$por_email[0]->id]);
				}
			}
		}

		if($qr_login->num_rows() > 0){
			$dd_user   = $qr_login->row();
			$senha_ok  = false;
			$senha_db  = (string)$dd_user->senha;

			if($senha_db !== '' && password_verify($senha_input, $senha_db)){
				$senha_ok = true;
			} elseif($senha_db !== '' && $senha_db === $senha_input) {
				// migração: senha ainda em texto puro → rehasha silenciosamente
				$this->db->where('id', $dd_user->id);
				$this->db->update('usuarios', ['senha' => password_hash($senha_input, PASSWORD_DEFAULT)]);
				$senha_ok = true;
			}

			if(!$senha_ok){
				$this->session->set_flashdata('login_error', $msg_erro);
				redirect('admin');
				return;
			}

			$dd_session = array(
				'usr'   => true,
				'id'    => $dd_user->id,
				'nome'  => $dd_user->nome,
				'nivel' => $dd_user->nivel,
				'login' => $dd_user->login
			);
			$this->session->sess_regenerate(true);
			$this->session->set_userdata($dd_session);

			if($dd_user->nivel == 2 || $dd_user->nivel == 3 || $dd_user->nivel == 4){
				redirect('adm/atendimento');
			}

			redirect('adm/usuarios');

		}else{
			$this->session->set_flashdata('login_error', $msg_erro);
			redirect('admin');
		}

	}

	// VALIDA A NAVEGAÇÃO
	function verSession(){
	
		$ss = $this->session->userdata('usr');
		if(!isset($ss) || $ss != true){
			redirect('admin');
			return;
		}

		$CI =& get_instance();
		$CI->load->model('padrao_model');
		$usuario = $CI->padrao_model->get_usuario_logado();
		$current_class = $CI->router->fetch_class();
		$current_method = $CI->router->fetch_method();

		$tenant_allows_access = true;
		if($usuario && (int)$usuario->nivel !== 1){
			if(method_exists($CI->padrao_model, 'tenant_allows_access')){
				$tenant_allows_access = $CI->padrao_model->tenant_allows_access($usuario);
			}elseif(isset($usuario->tenant_id) && (int)$usuario->tenant_id > 0 && $CI->db->table_exists('saas_tenants')){
				$qr_tenant = $CI->db->query("SELECT status FROM saas_tenants WHERE id = ".(int)$usuario->tenant_id." LIMIT 1");
				if($qr_tenant->num_rows()){
					$tenant_allows_access = ((int)$qr_tenant->row()->status === 1);
				}
			}
		}

		if($usuario && (int)$usuario->nivel !== 1 && !$tenant_allows_access){
			if($current_class !== 'saas' || $current_method !== 'bloqueado'){
				redirect('adm/saas/bloqueado');
				return;
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
