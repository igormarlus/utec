<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		//$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');

		if(!$this->session->userdata('id')){
			#redirect('app','refresh');
		}

		//$this->usuarios_model->verSession();

   } // fecha fn USER

	function Index(){
		
		#echo "Maria";
		
			$dados['dd'] = "";

			if($this->session->userdata('id')){
				$dd = $this->padrao_model->get_by_id($this->session->userdata('id'),'MJ_users')->row();
				if($dd->cpf == "" || $dd->id_endereco == "0"){
					#redirect('maria/dados');
				}
				$dados['dd'] = $dd;
				
				$this->load->view('app' , $dados);
			
			}else{
				$this->load->view('login' , $dados);
			}
			//$this->load->view('maria/chamar' , $dados);
			
		
		
		
	}

	function clear_car(){
		$where = array(
			'id_user' => $this->session->userdata('id'),
			#'status' => '0'
			'status' => '1'
		);
		$this->db->where($where);
		$this->db->delete('carrinho');
		redirect('app/cardapio');

	}

	function clear_car_adm($id_user=0){
		if(!$this->session->userdata('id')){
			return false;
		}
		if($id_user == 0){
			return false;
		}
		$where = array(
			'id_user' => $id_user,
			'id_cliente' => $this->session->userdata('id'),
			#'status' => '0'
			//'status' => '1'
		);
		$this->db->where($where);
		$qr = $this->db->get('carrinho');
		echo $qr->num_rows();

		
		$this->db->where($where);
		$this->db->delete('carrinho');
		//redirect('app/cardapio');

	}

	function pedido($id_cliente=1,$whats=""){

		if($this->session->userdata('cliente')){
			$whats = $this->session->userdata('whats');
		}

		$dados['whats'] = $whats;
		
		$dados['id_cliente'] = $id_cliente;

		$qr_cliente = $this->padrao_model->get_by_id($id_cliente,'usuarios');
		if($qr_cliente->num_rows() == 0){
			redirect('','refresh');
		}else{
			$dados['categorias'] = $this->db->query("SELECT * FROM produtos_categorias WHERE id_user = $id_cliente AND raiz = 0 AND status = 1 ORDER BY ordem asc");
			$dados['produtos'] = $this->db->query("SELECT * FROM produtos WHERE id_user = $id_cliente ORDER BY nome asc");
			$dados['carrinho'] = $this->db->query("SELECT * FROM carrinho WHERE id_user = '".$this->session->userdata('id')."' AND id_cliente = $id_cliente AND status = 1 ORDER BY dt asc");
			$dados['cliente'] = $qr_cliente->row();

			$dados['whats'] = $whats;

			$this->load->view('pedido' , $dados);
		}


		
	}

	function rem($id_car){
		if(!$this->session->userdata('id')){
			redirect('app','refresh');
		}
		$where = array(
			'id_user' => $this->session->userdata('id'),
			'id' => $id_car
		);
		
		$qr_get = $this->db->query("SELECT * FROM carrinho WHERE id_user = ".$this->session->userdata('id')." AND id = ".$id_car."  ");
		#echo $qr_get->num_rows();

		$this->db->where($where);
		$this->db->delete('carrinho');
		redirect('user/pedido');

	}

	function set_pedido(){
		if(!$this->session->userdata('id')){
			#redirect('app','refresh');
		}

		

		// DADOS DA EMPRESA VIA CNPJ
		// $cnpj = $this->input->post('cnpj');
		// $this->db->where('cnpj',$cnpj);
		// $qr_cnpj = $this->db->get('MJ_users');
		// if($qr_cnpj->num_rows() > 0){
		// 	$id_comprador = $qr_cnpj->row()->id;
		// }else{
		// 	$id_comprador = $this->session->userdata('id');
		// }

		$this->db->where('id',$this->session->userdata('id'));
		$qr_user = $this->db->get('pi_whats_users');
		if($qr_user->num_rows() > 0){
			$id_comprador = $qr_user->row()->id;
			$id_cliente = $qr_user->row()->id_user;
		}else{
			echo "usuario invalido.";
			return false;
		}

		
		#echo "OK - ".$id_comprador."<br>";
		#print_r($dd_car);
		#echo $qr_ver->num_rows();
		#return false;


		$where_up = array(
			'id_cliente' => $id_cliente,
			'id_user' => $this->session->userdata('id'),
			'status' => 1
		);
		$this->db->where($where_up);
		$qr_ver = $this->db->get('carrinho' , $dd_car);

		if($qr_ver->num_rows() > 0){

			// verifica carrinho
			#$carrinho = $this->db->query("SELECT * FROM carrinho WHERE id_user = '".$this->session->userdata('id')."' AND id_cliente = $id_cliente AND status = 1 ORDER BY dt asc");
			#echo "carrinho.... (".$qr_ver->num_rows().") ";

			if($qr_ver->num_rows() > 0){
				$total = 0;
				$hash = $id_cliente."-".$id_comprador."-".date("Y-m-d H:i:s");
        		$hash_pedido = md5($hash);
				foreach($qr_ver->result() as $produto){
					$dd_produto = $this->padrao_model->get_by_id($produto->id_produto,'produtos')->row();
					$total += $dd_produto->preco_venda * $produto->qtd;  
					#echo $produto->id_produto." (".$produto->qtd.") = R$ ".$dd_produto->preco_venda." ";
					#echo "<br>";
					unset($produto->id);
					#$produto->status = 0;
					$this->db->insert('carrinho_hist' , $produto);
				}
			}
			#echo "<br><br>";
			#echo $total;
			#return false;

			$dd_pedido = array(
				'id_pedido' => $hash_pedido,
				'id_user' => $this->session->userdata('id'),
				'id_cliente' => $id_cliente,
				'total' => $total,
				'status' => 0,
				'forma_pagamento' => 'PIX',
				#'id_comprador' => $id_comprador
			);
			$this->db->insert('pedidos' , $dd_pedido);
			$id_pedido = $this->db->insert_id();

			$dd_pedido = [
				'id_pedido' => $id_pedido,
				'hash' => $hash_pedido,
				'status' => 1
			];
			$this->db->where($where_up);
			$this->db->update('carrinho' , $dd_pedido);

			$this->db->where($where_up);
			$this->db->update('carrinho_hist' , $dd_pedido);
			

			 $payload = [
		        'ok'      => true,
		        'id'      => $this->session->userdata('id'),
		        'id_user' => $id_cliente,
		        // 'from' => $from,
		        // 'valor_dep' => '7.59',
		    ];

		    return $this->output
		        ->set_status_header(200)
		        ->set_content_type('application/json', 'utf-8')
		        ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
			/*
			$dd_car = array(
				'id_pedido' => $id_pedido,
				'status' => 1
			);
			*/
			
			#print_r($where_up);
			#return false;
		
			#$this->db->where($where_up);
			#$this->db->update('carrinho' , $dd_car);

			#echo "Pedido Relizado com sucesso! <a href='".base_url()."app'> Voltar</a> ";
			#echo "FIM";
			#$this->load->view('pedido_finalizado');
		}else{
			#redirect('app');
			echo "erdirect";
		}
	}

	function check_cnpj(){
		$cnpj = $this->input->post('cnpj');
		$this->db->where('cnpj',$cnpj);
		$qr = $this->db->get('MJ_users');
		if($qr->num_rows() == 0){
			echo "CNPJ não existe";
		}else{
			echo "<p class='badge badge-success'>".$qr->row()->razao_social."</p>";
		}

	}





	// ####################################### MERCADO PAGO	
	function process_payment(){
		// TEST-7bc1e82a-cb62-4dea-b394-de2a656f5fba - Public Key
		// TEST-685172168846807-012610-c99a2afde36d8c0ac567b2613371ec49-182756904 - ACess Token
		#require_once "includes/lib/mercadopago.php";
			$this->load->model('padrao_model');


		  #$access_token = "TEST-685172168846807-012610-c99a2afde36d8c0ac567b2613371ec49-182756904";
		  $access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";

			  if(isset($_POST)){

			    if(isset($_POST['pix'])){

			      if($_POST['pix']){
			      	$id_user = $this->session->userdata('id');
					$dd_user = $this->padrao_model->get_by_id($id_user,'ai_user')->row();
					#print_r($dd_user);

			        #$valor = 10;
			        
			        $valor = str_replace("R$ ","",$_POST['valor_dep']);
					$valor = str_replace(".","",$valor);
					$valor = str_replace(",",".",$valor);

					

					$hash = md5($id_user."--3--32-".date("Y-m-d h:i:s"));


					$dd_dep = array(
						'id_user' => $id_user,
						'valor' => $valor
					);
					$this->db->where($dd_dep);
					$qr_dep = $this->db->get('depositos');
					if($qr_dep->num_rows() == 0){
						$dd_dep['hash_transacao'] = $hash;
						$this->db->insert('depositos', $dd_dep);
						$id_ext = $this->db->insert_id();
					}else{
						$id_ext = $qr_dep->row()->id;
					}
					
					#echo $id_ext;
					#echo "<br>";
					#print_r($dd_user);
					#return false;
					
					

			        #include_once 'mercadopago/lib/mercadopago/vendor/autoload.php';
			        include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';

			        //MercadoPago\SDK::setAccessToken($access_token);

			        MercadoPago\SDK::setAccessToken($access_token);
			     
		         	 $payment = new MercadoPago\Payment();
					 $payment->transaction_amount = $valor;
					 $payment->description = "Créditos ChatGPT";
					 $payment->payment_method_id = "pix";
					 #$payment->external_reference = $this->session->userdata('id').date("Y").date("m").date("d");
					 $payment->external_reference = $id_ext;
					 //$payment->payment_method_id = "bolbradesco";
					 $payment->payer = array(
					    "email" => $dd_user->email,
					    "first_name" => $dd_user->nome,
					 	#"email" => "igor@yahoo.com.br",
					    # "first_name" => "Igor",
					     "last_name" => "Loteria",
					     "identification" => array(
					         "type" => "CPF",
					         "number" => "06030689444"
					      ),
					     "address"=>  array(
					         "zip_code" => "52041230",
					         "street_name" => "Rua dr jose higino ribeiro campos",
					         "street_number" => "11",
					         "neighborhood" => "Encruzilhada",
					         "city" => "Recife",
					         "federal_unit" => "PE"
					      )
					   );

					 $payment->save();	
					 echo json_encode($payment->point_of_interaction);

			      }else{
			        echo json_encode(array(
			          'status'  => 'error',
			          'message' => 'pix required'
			        ));
			        exit;
			      }

			    }else{
			      echo json_encode(array(
			        'status'  => 'error',
			        'message' => 'pix required'
			      ));
			      exit;
			    }

			  }else{
			    echo json_encode(array(
			      'status'  => 'error',
			      'message' => 'post required'
			    ));
			    exit;
			  }




		/*
		MercadoPago\SDK::setAccessToken("ENV_ACCESS_TOKEN");

		 $payment = new MercadoPago\Payment();
		 $payment->transaction_amount = 100;
		 $payment->description = "Título do produto";
		 $payment->payment_method_id = "pix";
		 $payment->payer = array(
		     "email" => $this->input->post("email"),
		     "first_name" => $this->input->post("payerFirstName"),
		     "last_name" => $this->input->post("payerLastName"),
		     "identification" => array(
		         "type" => "CPF",
		         "number" => $this->input->post("identificationNumber")
		      ),
		     "address"=>  array(
		         "zip_code" => "06233200",
		         "street_name" => "Av. das Nações Unidas",
		         "street_number" => "3003",
		         "neighborhood" => "Bonfim",
		         "city" => "Osasco",
		         "federal_unit" => "SP"
		      )
		   );

		 $payment->save();
		 */



		#print_r($_POST);
	}

	## PARA CHATBOT WHATSAPP
	function process_payment_wa_oficial($id_cliente,$id_user=0){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
		header("Content-Type: application/json");
		#echo $id_cliente;
		 #$rawData = file_get_contents("php://input");
		 $rawData = $this->input->post();
		 $from = $this->input->post('from');
		 $pix = $this->input->post('pix');
		 #$rawData = $_POST;
		 #return $from;
		 $this->load->model('padrao_model');

		  $access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";
		  		#echo "OK NAO";
			  if(isset($rawData)){
			  	#print_r($_POST);
			  	#echo "OK";
			    if(isset($pix)){

			      if($_POST['pix']){
			      	
					#$qr_user = $this->padrao_model->get_by_matriz('telefone',$from,'pi_whats_users');
					#$dd_user = $this->padrao_model->get_by_matriz('telefone',$from,'pi_whats_users')->row();
					if($id_user == 0){
						$dd_user = $this->db->query("SELECT * FROM pi_whats_users WHERE telefone = '".$from."' ORDER BY id ASC LIMIT 1")->row();
						$id_user = $dd_user->id;
					}else{
						$dd_user = $this->db->query("SELECT * FROM pi_whats_users WHERE id = '".$id_user."'")->row();
						$id_user = $dd_user->id;
					}
					
					#echo $qr_user->num_rows();
					// CASO SEJA POR ULTIMO PEDIDO
					$qr_verifica_last_pedido = $this->db->query("SELECT * FROM pedidos WHERE id_user = ".$id_user." AND status = 0 ORDER BY id desc LIMIT 1 ");
					#echo "Total: ".$qr_verifica_last_pedido->num_rows()."(id_user = ".$id_user.")";
					#echo $dd_user->id. " ID UISER";

					// CASO SEJA ACUMULATIVO PRA PAGAR NO FIM (PRESENCIAL)
					#$qr_verifica_last_pedido = $this->db->query("SELECT * FROM pedidos WHERE id_user = ".$id_user." AND status = 0 ORDER BY id asc");

					if($qr_verifica_last_pedido->num_rows() == 0){
						return "erro no pedido";
						exit;
					}else{
						foreach($qr_verifica_last_pedido->result() as $pedido){
							$valor = $pedido->total;
							$id_ext = "RPG-".$pedido->id_pedido;
							#print_r($pedido);
						}
					}
					#return $valor;

					#echo $id_ext. " id_ext";
					#echo $valor. " valor";
					
			        #$valor = str_replace("R$ ","",$this->input->post('valor_dep'));
					#$valor = str_replace(".","",$valor);
					#$valor = str_replace(",",".",$valor);

					#$hash = md5($id_user."--3--32-".date("Y-m-d h:i:s"));


					$dd_dep = array(
						'id_cliente' => $id_cliente,
						'id_user' => $id_user,
						'valor' => $valor
					);
					$this->db->where($dd_dep);
					$qr_dep = $this->db->get('depositos');
					if($qr_dep->num_rows() == 0){
						$dd_dep['hash_transacao'] = $id_ext;
						$this->db->insert('depositos', $dd_dep);
						#$id_ext = $this->db->insert_id();
						#$id_ext = $id_ext;
					}else{
						#$id_ext = $qr_dep->row()->hash_transacao;
						#$id_ext = $qr_dep->row()->id;
					}
					
					#echo $id_ext;
					#echo "<br>";
					#print_r($dd_user);
					#return false;

					
					
					

			        #include_once 'mercadopago/lib/mercadopago/vendor/autoload.php';
			        include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';

			        //MercadoPago\SDK::setAccessToken($access_token);

			        MercadoPago\SDK::setAccessToken($access_token);
			     
		         	 $payment = new MercadoPago\Payment();
					 $payment->transaction_amount = $valor;
					 $payment->description = "Créditos WA Commerce";
					 $payment->payment_method_id = "pix";
					 #$payment->external_reference = $this->session->userdata('id').date("Y").date("m").date("d");
					 $payment->external_reference = $id_ext;
					 //$payment->payment_method_id = "bolbradesco";
					 $payment->payer = array(
					    #"email" => $dd_user->email,
					    #"email" => $dd_user->email,
					    "first_name" => $dd_user->nome,
					 	"email" => "igor@yahoo.com.br",
					    # "first_name" => "Igor",
					     "last_name" => "WA",
					     "identification" => array(
					         "type" => "CPF",
					         "number" => "06030689444"
					      ),
					     "address"=>  array(
					         "zip_code" => "52041230",
					         "street_name" => "Rua dr jose higino ribeiro campos",
					         "street_number" => "11",
					         "neighborhood" => "Encruzilhada",
					         "city" => "Recife",
					         "federal_unit" => "PE"
					      )
					   );

					 $payment->save();	
					 echo json_encode($payment->point_of_interaction);
					//  $resposta = [
					//  	'pay' => $payment->point_of_interaction,
					//  	'id_cliente' => $id_cliente,
					//  	#'data' => $rawData
					//  	'from' => $from,
					//  	'valor' => $valor,
					//  	'hash' => $hash,
					//  ];
					//  #echo $resposta;
					// echo json_encode($resposta);
				    exit;

			      }else{
			        echo json_encode(array(
			          'status'  => 'error',
			          'message' => 'pix required'
			        ));
			        exit;
			      }

			    }else{
			      echo json_encode(array(
			        'status'  => 'error',
			        'message' => 'pix required'
			      ));
			      exit;
			    }

			  }else{
			    echo json_encode(array(
			      'status'  => 'error',
			      'message' => 'post required'
			    ));
			    exit;
			  }




		/*
		MercadoPago\SDK::setAccessToken("ENV_ACCESS_TOKEN");

		 $payment = new MercadoPago\Payment();
		 $payment->transaction_amount = 100;
		 $payment->description = "Título do produto";
		 $payment->payment_method_id = "pix";
		 $payment->payer = array(
		     "email" => $this->input->post("email"),
		     "first_name" => $this->input->post("payerFirstName"),
		     "last_name" => $this->input->post("payerLastName"),
		     "identification" => array(
		         "type" => "CPF",
		         "number" => $this->input->post("identificationNumber")
		      ),
		     "address"=>  array(
		         "zip_code" => "06233200",
		         "street_name" => "Av. das Nações Unidas",
		         "street_number" => "3003",
		         "neighborhood" => "Bonfim",
		         "city" => "Osasco",
		         "federal_unit" => "SP"
		      )
		   );

		 $payment->save();
		 */



		#print_r($_POST);
	}

	## PARA CARTÃO DE CREDITO SYS MARKETING
	// Controller: User.php (exemplo)
// Rota: user/process_payment_card_oficial/{id_cliente}/{id_user?}
public function process_payment_card_oficial($id_cliente, $id_user = 0) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header("Content-Type: application/json");

    // $rawData = file_get_contents("php://input");
    $rawData = $this->input->post();
    $from           = $this->input->post('from'); // opcional
    $token          = $this->input->post('token');
    $installments   = (int)$this->input->post('installments');
    $paymentMethod  = $this->input->post('payment_method_id');
    $issuerId       = $this->input->post('issuer_id'); // opcional
    $payerEmail     = $this->input->post('payer_email');
    $docType        = $this->input->post('payer_doc_type') ?: 'CPF';
    $docNumber      = preg_replace('/\D+/', '', (string)$this->input->post('payer_doc_number'));

    if (!$token || !$payerEmail || !$docNumber || !$paymentMethod) {
        echo json_encode(['status' => 'error', 'message' => 'Campos obrigatórios ausentes (token, payer_email, payer_doc_number, payment_method_id).']);
        return;
    }

    $this->load->model('padrao_model');

    // ATENÇÃO: use variável de ambiente em produção
    $access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";

    // === Descobrir $id_user (mesma lógica do PIX) ===
    if ($id_user == 0 && !empty($from)) {
        $dd_user = $this->db->query("SELECT * FROM pi_whats_users WHERE telefone = '".$from."' ORDER BY id ASC LIMIT 1")->row();
        if (!$dd_user) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado pelo telefone.']);
            return;
        }
        $id_user = $dd_user->id;
    } else {
        $dd_user = $this->db->query("SELECT * FROM pi_whats_users WHERE id = '".$id_user."'")->row();
        if (!$dd_user) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado pelo id.']);
            return;
        }
    }

    // === Último pedido em aberto desse usuário ===
    $qr_verifica_last_pedido = $this->db->query("SELECT * FROM pedidos WHERE id_user = ".$id_user." AND status = 0 ORDER BY id DESC LIMIT 1");
    if ($qr_verifica_last_pedido->num_rows() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Nenhum pedido em aberto para este usuário.']);
        return;
    }

    $pedido  = $qr_verifica_last_pedido->row();
    $valor   = (float)$pedido->total;
    $id_ext  = "RPG-".$pedido->id_pedido; // external_reference

    // === Registrar/garantir depósito (igual PIX) ===
    $dd_dep = [
        'id_cliente' => $id_cliente,
        'id_user'    => $id_user,
        'valor'      => $valor
    ];
    $this->db->where($dd_dep);
    $qr_dep = $this->db->get('depositos');
    if ($qr_dep->num_rows() == 0) {
        $dd_dep['hash_transacao'] = $id_ext;
        $this->db->insert('depositos', $dd_dep);
    }

    // === Mercado Pago: cartão de crédito ===
    include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';
    MercadoPago\SDK::setAccessToken($access_token);

    $payment = new MercadoPago\Payment();
    $payment->transaction_amount = $valor;
    $payment->description        = "Créditos WA Commerce";
    $payment->external_reference = $id_ext;

    // dados do cartão/token
    $payment->token              = $token;
    $payment->installments       = max(1, $installments);
    $payment->payment_method_id  = $paymentMethod;
    if (!empty($issuerId)) {
        $payment->issuer_id = (int)$issuerId;
    }

    // pagador
    $payment->payer = [
        "email" => $payerEmail,
        "first_name" => $dd_user->nome ?: "Cliente",
        "last_name"  => "WA",
        "identification" => [
            "type"   => $docType,     // "CPF" ou "CNPJ"
            "number" => $docNumber
        ],
        // endereço opcional:
        // "address" => [
        //   "zip_code" => "52041230",
        //   "street_name" => "Rua X",
        //   "street_number" => "123",
        //   "neighborhood" => "Bairro",
        //   "city" => "Recife",
        //   "federal_unit" => "PE"
        // ]
    ];

    // opcionalmente: $payment->capture = true;  // captura imediata
    // $payment->binary_mode = true; // aprovado/rejeitado (reduz "in_process")

    try {
        $payment->save();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Falha ao criar pagamento', 'exception' => $e->getMessage()]);
        return;
    }

    // Se quiser uniformizar com o retorno do PIX, responda só o essencial:
    $resp = [
        'id'                => $payment->id,
        'status'            => $payment->status,         // approved | in_process | rejected
        'status_detail'     => $payment->status_detail,  // motivo detalhado
        'transaction_amount'=> $payment->transaction_amount,
        'payment_method_id' => $payment->payment_method_id,
        'installments'      => $payment->installments,
        'external_reference'=> $payment->external_reference
    ];

    // Se houver erro de validação da API, algumas versões expõem $payment->error
    if (property_exists($payment, 'error') && !empty($payment->error)) {
        $resp['mp_error'] = $payment->error;
    }

    echo json_encode($resp);
    return;
}



	## PARA CHATBOT WHATSAPP
	function process_payment_wa($id_cliente){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
		header("Content-Type: application/json");
		#echo $id_cliente;
		 #$rawData = file_get_contents("php://input");
		 $rawData = $this->input->post();
		 $from = $this->input->post('from');
		 $pix = $this->input->post('pix');
		 #$rawData = $_POST;
		 #return $from;
		 $this->load->model('padrao_model');

		  $access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";
		  		#echo "OK NAO";
			  if(isset($rawData)){
			  	#print_r($_POST);
			  	#echo "OK";
			    if(isset($pix)){

			      if($_POST['pix']){
			      	
					$qr_user = $this->padrao_model->get_by_matriz('telefone',$from,'pi_whats_users');
					#$dd_user = $this->padrao_model->get_by_matriz('telefone',$from,'pi_whats_users')->row();
					$dd_user = $this->db->query("SELECT * FROM pi_whats_users WHERE telefone = '".$from."' ORDER BY id ASC LIMIT 1")->row();
					$id_user = $dd_user->id;
					#echo $qr_user->num_rows();
					// CASO SEJA POR ULTIMO PEDIDO
					$qr_verifica_last_pedido = $this->db->query("SELECT * FROM pedidos WHERE id_user = ".$id_user." AND status = 0 ORDER BY id desc LIMIT 1 ");
					#echo "Total: ".$qr_verifica_last_pedido->num_rows()."(id_user = ".$id_user.")";
					#echo $dd_user->id. " ID UISER";

					// CASO SEJA ACUMULATIVO PRA PAGAR NO FIM (PRESENCIAL)
					#$qr_verifica_last_pedido = $this->db->query("SELECT * FROM pedidos WHERE id_user = ".$id_user." AND status = 0 ORDER BY id asc");

					if($qr_verifica_last_pedido->num_rows() == 0){
						return "erro no pedido";
						exit;
					}else{
						foreach($qr_verifica_last_pedido->result() as $pedido){
							$valor = $pedido->total;
							$id_ext = "RPG-".$pedido->id_pedido;
							#print_r($pedido);
						}
					}
					#return $valor;

					#echo $id_ext. " id_ext";
					#echo $valor. " valor";
					
			        #$valor = str_replace("R$ ","",$this->input->post('valor_dep'));
					#$valor = str_replace(".","",$valor);
					#$valor = str_replace(",",".",$valor);

					#$hash = md5($id_user."--3--32-".date("Y-m-d h:i:s"));


					$dd_dep = array(
						'id_cliente' => $id_cliente,
						'id_user' => $id_user,
						'valor' => $valor
					);
					$this->db->where($dd_dep);
					$qr_dep = $this->db->get('depositos');
					if($qr_dep->num_rows() == 0){
						$dd_dep['hash_transacao'] = $id_ext;
						$this->db->insert('depositos', $dd_dep);
						#$id_ext = $this->db->insert_id();
						#$id_ext = $id_ext;
					}else{
						#$id_ext = $qr_dep->row()->hash_transacao;
						#$id_ext = $qr_dep->row()->id;
					}
					
					#echo $id_ext;
					#echo "<br>";
					#print_r($dd_user);
					#return false;

					
					
					

			        #include_once 'mercadopago/lib/mercadopago/vendor/autoload.php';
			        include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';

			        //MercadoPago\SDK::setAccessToken($access_token);

			        MercadoPago\SDK::setAccessToken($access_token);
			     
		         	 $payment = new MercadoPago\Payment();
					 $payment->transaction_amount = $valor;
					 $payment->description = "Créditos WA Commerce";
					 $payment->payment_method_id = "pix";
					 #$payment->external_reference = $this->session->userdata('id').date("Y").date("m").date("d");
					 $payment->external_reference = $id_ext;
					 //$payment->payment_method_id = "bolbradesco";
					 $payment->payer = array(
					    #"email" => $dd_user->email,
					    #"email" => $dd_user->email,
					    "first_name" => $dd_user->nome,
					 	"email" => "igor@yahoo.com.br",
					    # "first_name" => "Igor",
					     "last_name" => "WA",
					     "identification" => array(
					         "type" => "CPF",
					         "number" => "06030689444"
					      ),
					     "address"=>  array(
					         "zip_code" => "52041230",
					         "street_name" => "Rua dr jose higino ribeiro campos",
					         "street_number" => "11",
					         "neighborhood" => "Encruzilhada",
					         "city" => "Recife",
					         "federal_unit" => "PE"
					      )
					   );

					 $payment->save();	
					 echo json_encode($payment->point_of_interaction);
					//  $resposta = [
					//  	'pay' => $payment->point_of_interaction,
					//  	'id_cliente' => $id_cliente,
					//  	#'data' => $rawData
					//  	'from' => $from,
					//  	'valor' => $valor,
					//  	'hash' => $hash,
					//  ];
					//  #echo $resposta;
					// echo json_encode($resposta);
				    exit;

			      }else{
			        echo json_encode(array(
			          'status'  => 'error',
			          'message' => 'pix required'
			        ));
			        exit;
			      }

			    }else{
			      echo json_encode(array(
			        'status'  => 'error',
			        'message' => 'pix required'
			      ));
			      exit;
			    }

			  }else{
			    echo json_encode(array(
			      'status'  => 'error',
			      'message' => 'post required'
			    ));
			    exit;
			  }




		/*
		MercadoPago\SDK::setAccessToken("ENV_ACCESS_TOKEN");

		 $payment = new MercadoPago\Payment();
		 $payment->transaction_amount = 100;
		 $payment->description = "Título do produto";
		 $payment->payment_method_id = "pix";
		 $payment->payer = array(
		     "email" => $this->input->post("email"),
		     "first_name" => $this->input->post("payerFirstName"),
		     "last_name" => $this->input->post("payerLastName"),
		     "identification" => array(
		         "type" => "CPF",
		         "number" => $this->input->post("identificationNumber")
		      ),
		     "address"=>  array(
		         "zip_code" => "06233200",
		         "street_name" => "Av. das Nações Unidas",
		         "street_number" => "3003",
		         "neighborhood" => "Bonfim",
		         "city" => "Osasco",
		         "federal_unit" => "SP"
		      )
		   );

		 $payment->save();
		 */



		#print_r($_POST);
	}

	function verifica_pagamento($id_trans){

		include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);
     	$payment = new MercadoPago\Payment();
     	$payment = MercadoPago\Payment::find_by_id($payment_id);
		$payment->capture = true;
		$payment->update();

	}

	function busca_pagamento(){

		$access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";

        include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';
		
        MercadoPago\SDK::setAccessToken($access_token);
     	#$payment = new MercadoPago\Payment();

     	$filters = array(
            "sort" => "date_last_updated",
            "criteria" => "desc",
            "limit" => "10"
        );

     	$payment = MercadoPago\Payment::search($filters);
     	$payment->sort = "date_last_updated";     	
     	$payment->criteria = "desc";     	
		//$payment->capture = true;
		//$payment->save();
		//$payment->update();
		
		

		foreach ($payment as $key => $value) {
		    #echo $value->nome;
		    #echo $key;
		    #echo "<br><br>";
		    #echo $value->idade;
		}
		$total = count($payment);
		#echo "<br>";
		#print_r($payment[0]);
		#echo $payment["0"];
		// https://www.mercadopago.com.br/developers/pt/reference/payments/_payments_search/get // DOCUMENTACAO
		for($p=0; $p<$total; $p++){

			echo "Data criação: <strong>".$payment[$p]->date_created."</strong>";
			echo "<br>";
			echo "Data aprovação: <strong>".$payment[$p]->date_approved."</strong>";
			echo "<br>";
			echo "Tipo de pagamento: <strong> ".$payment[$p]->payment_method_id." (".$payment[$p]->operation_type.")</strong>";
			echo "<br>";
			echo "Status: <strong>".$payment[$p]->status." - ".$payment[$p]->status_detail." </strong>";
			echo "<br>";
			echo "Description: <strong>".$payment[$p]->description."</strong>";
			echo "<br>";
			echo "External_reference: <strong>".$payment[$p]->external_reference."</strong>";
			echo "<br>";
			echo "Valor: <strong>".$payment[$p]->transaction_amount."</strong>";
			echo "<br>";
			// 
			echo "<span style='color:silver'>";
			#print_r($payment[$p]);
			echo "</span>";
			echo "<br><br><hr>";

		}

		

	}

	function busca_status_pag($id_ref){

		$access_token = "APP_USR-685172168846807-012610-649caed966c451e2c65d542a6ade4edd-182756904";
        include_once 'includes/mercadopago/lib/mercadopago/vendor/autoload.php';		
        MercadoPago\SDK::setAccessToken($access_token);
     	$filters = array(
            "external_reference" => $id_ref
        );
     	$payment = MercadoPago\Payment::search($filters);
		$total = count($payment);		
		#echo $total;
		// https://www.mercadopago.com.br/developers/pt/reference/payments/_payments_search/get // DOCUMENTACAO
		#for($p=0; $p<$total; $p++){
			return  $payment[$total-1]->status;
			/*
			pending
			approved
			authorized
			in_process
			in_mediation
			rejected
			cancelled
			refunded
			charged_back
			*/
		#}

	}

	#############  VERIFICA PAGAMENTOS
	function get_deps_pend(){
		$qr_dep = $this->db->query("SELECT * FROM depositos WHERE status = 0");
		foreach ($qr_dep->result() as $dd) {
			# code...
			echo "<strong>".$dd->id."</strong> - (".$dd->status.") - ";
			$status_mp = $this->busca_status_pag($dd->id);
			echo $status_mp." ".$dd->dt_reg." ".$dd->valor;
			if($status_mp == "pending"){
				echo "[Pendente]";
			}

			if($status_mp == "approved"){
				echo "[Pago]";

				$valor_depositado = $dd->valor;

				$dd_mov = array(
	        		'id_user_mov' => 1,
	        		'id_user' => $dd->id_user,
	        		'data_hora_pagamento' => date("Y-m-d H:i:s"),
	        		'valor' => $valor_depositado,
	        		'tipo' => 'deposito',
	        		'descricao' => "Deposito via PIX CHATGPT de $valor_depositado às ".$dd->dt_reg."  ",
	        		'status' => 1,
	        		'wallet' => "pix",
	        		'hash' => $dd->hash_transacao,
	        		'realizado' => 0
	        	);

				
				## LIBERA INSERÇÃO DE SALDO
					
	        	$this->db->insert('movimento' , $dd_mov); // add saldo

				$status_ok = array('status' => 1);
				$this->db->where('id',$dd->id);
				$this->db->update('depositos', $status_ok); // remove de pendentes

				//  INSERI CRÉDITOS
				for($c=0;$c<11;$c++){
					$hash = $this->session->userdata('id')."-"."***34*324*".date("Y-d-m h:i:s").$c;
					$dd_coin = array(
						'hash' => $hash,
						'id_user_ai' => $dd->id_user,
						'valor' => 0.70,
						'status' => 0
					);
					$this->db->insert('ai_respostas_coin' , $dd_coin);
				}

				// x creditos

			} // x aprovado

			#echo $status_mp;

			echo "<br>";

		}
	}

	//  X MERCADO PAGO
	
}
