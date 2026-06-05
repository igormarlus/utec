<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model('adm/saas_model');
		$this->load->model('FbApi_model', 'fbapi_model');
		$this->padrao_model->indexador();
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->base();
	}


	public function base()
	{
		$dados['titulo'] = "";
		$dados['public_plans'] = $this->saas_model->get_public_plans();

		// CAPI PageView — event_id compartilhado com o browser Pixel para deduplicação
		$fb = $this->fbapi_model->send_event('PageView', [
			'source_url' => site_url(),
		]);
		$dados['fb_pixel_id']       = $this->fbapi_model->get_pixel_id();
		$dados['fb_pv_event_id']    = $fb ? $fb['event_id'] : '';

		$this->load->view('index-front' , $dados);
	}

	public function assinar()
	{
		$this->load->library('mercadopago_saas');
		$dados['planos'] = $this->saas_model->get_public_plans();
		$dados['flash_ok'] = $this->session->flashdata('public_signup_ok');
		$dados['flash_error'] = $this->session->flashdata('public_signup_error');
		$dados['mercadopago_ready'] = $this->mercadopago_saas->is_available();
		$this->load->view('public/assinar', $dados);
	}

	public function experimentar()
	{
		$tipo_raw = trim((string)$this->input->get('tipo'));
		$tipos_validos = ['clinica', 'consultorio', 'profissional'];
		$dados['tipo_selecionado'] = in_array($tipo_raw, $tipos_validos) ? $tipo_raw : 'clinica';
		$dados['planos'] = $this->saas_model->get_public_plans();
		$dados['flash_ok']    = $this->session->flashdata('operational_trial_ok');
		$dados['flash_error'] = $this->session->flashdata('operational_trial_error');
		$dados['especialidades'] = $this->db->table_exists('usuarios_especialidades')
			? $this->db->query("SELECT * FROM usuarios_especialidades WHERE status = 1 ORDER BY nome ASC")->result()
			: [];
			#print_r($dados['especialidades']);
			#return false;

		// CAPI Lead — usuário chegou à página do formulário de trial
		$fb = $this->fbapi_model->send_event('Lead', [
			'source_url'  => site_url('experimentar'),
			'custom_data' => [
				'content_name'     => 'Trial 30 dias',
				'content_category' => $dados['tipo_selecionado'],
			],
		]);
		$dados['fb_pixel_id']      = $this->fbapi_model->get_pixel_id();
		$dados['fb_lead_event_id'] = $fb ? $fb['event_id'] : '';

		$this->load->view('public/experimentar', $dados);
	}

	public function iniciar_experiencia()
	{
		$tenant_tipo = trim((string)$this->input->post('tenant_tipo'));

		$signup_data = [
			'nome_responsavel' => $this->input->post('nome_responsavel'),
			'tenant_nome'      => $this->input->post('tenant_nome'),
			'email'            => $this->input->post('email'),
			'telefone'         => $this->input->post('telefone'),
			'tenant_tipo'      => $tenant_tipo,
			'plano_id'         => $this->input->post('plano_id'),
			'documento'        => $this->input->post('documento'),
			'observacoes'      => $this->input->post('observacoes'),
			'especialidade_id' => ($tenant_tipo === 'profissional') ? (int)$this->input->post('especialidade_id') : 0,
		];

		$result = $this->saas_model->create_operational_trial_signup($signup_data);
		if(!$result['ok']){
			$this->session->set_flashdata('operational_trial_error', $result['msg']);
			redirect('experimentar');
			return;
		}

		// Auto-login: busca o usuário criado e define sessão
		$this->load->model('adm/Usuarios_model');
		$user = $this->db->get_where('usuarios', ['id' => (int)$result['user_id']])->row();
		if($user){
			$this->session->set_userdata([
				'id'    => $user->id,
				'nome'  => $user->nome,
				'nivel' => $user->nivel,
				'login' => $user->login,
				'usr'   => $user,
			]);
		}

		// Envia e-mail de boas-vindas com credenciais e link de definição de senha
		$this->_enviar_email_boas_vindas($result);

		// CAPI StartTrial + CompleteRegistration — trial criado com sucesso
		$email    = trim((string)$this->input->post('email'));
		$nome     = trim((string)$this->input->post('nome_responsavel'));
		$telefone = trim((string)$this->input->post('telefone'));
		$tipo     = trim((string)$this->input->post('tenant_tipo'));
		$valor    = isset($result['plano_valor']) ? (float)$result['plano_valor'] : 0;

		$ev_id_trial = 'trial_' . time() . '_' . bin2hex(openssl_random_pseudo_bytes(3));
		$this->fbapi_model->send_event('StartTrial', [
			'email'      => $email,
			'nome'       => $nome,
			'phone'      => $telefone,
			'event_id'   => $ev_id_trial,
			'source_url' => site_url('experimentar'),
			'custom_data' => [
				'currency'      => 'BRL',
				'value'         => 0,
				'predicted_ltv' => $valor,
				'content_name'  => 'Trial 30 dias',
				'content_category' => $tipo ?: 'clinica',
			],
		]);

		$ev_id_reg = 'reg_' . time() . '_' . bin2hex(openssl_random_pseudo_bytes(3));
		$this->fbapi_model->send_event('CompleteRegistration', [
			'email'      => $email,
			'nome'       => $nome,
			'phone'      => $telefone,
			'event_id'   => $ev_id_reg,
			'source_url' => site_url('experimentar'),
			'custom_data' => [
				'status'           => 'trial_created',
				'content_name'     => 'Trial 30 dias',
				'content_category' => $tipo ?: 'clinica',
			],
		]);

		// Passa os event_ids para a view de sucesso fazer deduplicação com o browser Pixel
		$this->session->set_flashdata('fb_trial_event_id', $ev_id_trial);
		$this->session->set_flashdata('fb_reg_event_id', $ev_id_reg);
		$this->session->set_flashdata('operational_trial_ok', 'Seu acesso de 30 dias foi criado com sucesso. Voce ja pode entrar no sistema e o pagamento do plano escolhido fica disponivel durante o periodo de trial.');
		redirect('experimentar/sucesso?subscription='.(int)$result['subscription_id']);
	}

	public function experiencia_sucesso()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			redirect('experimentar');
			return;
		}
		$dados['detail'] = $detail;
		$dados['flash_ok'] = $this->session->flashdata('operational_trial_ok');
		$dados['flash_error'] = $this->session->flashdata('operational_trial_error');
		$dados['payment_url'] = base_url().'assinar/pagamento?subscription='.(int)$detail['subscription']->id;
		$this->load->view('public/experimentar-sucesso', $dados);
	}

	public function contratar()
	{
		$result = $this->saas_model->create_public_tenant_signup($this->input->post());
		if(!$result['ok']){
			$this->session->set_flashdata('public_signup_error', $result['msg']);
			redirect('assinar');
			return;
		}

		// CAPI Subscribe — assinatura iniciada (tenant + subscription criados)
		$email    = trim((string)$this->input->post('email'));
		$nome     = trim((string)$this->input->post('nome_responsavel'));
		$telefone = trim((string)$this->input->post('telefone'));
		$valor    = isset($result['plano_valor']) ? (float)$result['plano_valor'] : 0;

		$ev_id_sub = 'sub_' . time() . '_' . bin2hex(openssl_random_pseudo_bytes(3));
		$this->fbapi_model->send_event('Subscribe', [
			'email'      => $email,
			'nome'       => $nome,
			'phone'      => $telefone,
			'event_id'   => $ev_id_sub,
			'source_url' => site_url('assinar'),
			'custom_data' => [
				'currency'     => 'BRL',
				'value'        => $valor,
				'content_name' => 'Assinatura UTecnologia Saude',
			],
		]);
		$this->session->set_flashdata('fb_sub_event_id', $ev_id_sub);

		$this->session->set_flashdata('public_signup_ok', 'Cadastro criado com sucesso. Agora escolha PIX ou cartao para concluir a contratacao.');
		redirect('assinar/pagamento?subscription='.(int)$result['subscription_id']);
	}

	public function assinatura_pagamento()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			redirect('assinar');
			return;
		}

		$this->load->library('mercadopago_saas');
		$open_cycle = $this->saas_model->ensure_checkout_cycle((int)$detail['subscription']->id);
		$latest_event = $open_cycle ? $this->saas_model->get_latest_cycle_payment_event((int)$open_cycle->id) : null;

		$dados['detail'] = $detail;
		$dados['open_cycle'] = $open_cycle;
		$dados['latest_payment_event'] = $latest_event;
		$dados['latest_payment_payload'] = $this->saas_model->extract_payment_payload($latest_event);
		$dados['mercadopago_ready'] = $this->mercadopago_saas->is_available();
		$dados['mercadopago_public_key'] = $this->mercadopago_saas->get_public_key();
		$dados['flash_ok'] = $this->session->flashdata('public_signup_ok');
		$dados['flash_error'] = $this->session->flashdata('public_signup_error');
		$dados['status_refresh_url'] = base_url().'assinar/pagamento/status?subscription='.(int)$detail['subscription']->id;
		$dados['pix_submit_url'] = base_url().'assinar/pagamento/pix?subscription='.(int)$detail['subscription']->id;
		$dados['card_submit_url'] = base_url().'assinar/pagamento/cartao?subscription='.(int)$detail['subscription']->id;
		$dados['back_url'] = base_url().'assinar/sucesso?subscription='.(int)$detail['subscription']->id;
		$dados['page_mode'] = 'public';
		$this->load->view('public/assinar-pagamento', $dados);
	}

	public function assinatura_pagamento_pix()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$is_ajax = strtolower((string)$this->input->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			if($is_ajax){
				$this->output->set_content_type('application/json')->set_status_header(404)->set_output(json_encode(['ok' => false, 'message' => 'Assinatura nao encontrada.']));
				return;
			}
			redirect('assinar');
			return;
		}
		$open_cycle = $this->saas_model->ensure_checkout_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$message = 'Nao existe ciclo pendente para gerar PIX nesta assinatura.';
			if($is_ajax){
				$this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'message' => $message]));
				return;
			}
			$this->session->set_flashdata('public_signup_ok', $message);
			redirect('assinar/pagamento?subscription='.(int)$detail['subscription']->id);
			return;
		}

		$this->load->library('mercadopago_saas');
		try {
			$payment = $this->mercadopago_saas->create_pix_payment(
				$detail['subscription'],
				$detail['tenant'],
				$detail['owner'],
				$open_cycle,
				$detail['plano']
			);
			$this->saas_model->append_billing_event([
				'subscription_id' => (int)$detail['subscription']->id,
				'tenant_id' => (int)$detail['tenant']->id,
				'cycle_id' => (int)$open_cycle->id,
				'event_type' => 'mercadopago_pix_created',
				'gateway' => 'mercadopago',
				'gateway_reference' => isset($payment['id']) ? (string)$payment['id'] : '',
				'status' => isset($payment['status']) ? (string)$payment['status'] : 'pending',
				'amount' => isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : (float)$open_cycle->amount_due,
				'payload_text' => json_encode($payment),
			]);
			$transaction_data = isset($payment['point_of_interaction']['transaction_data']) ? $payment['point_of_interaction']['transaction_data'] : array();
			$this->saas_model->save_checkout_data((int)$detail['subscription']->id, [
				'gateway_reference' => isset($payment['external_reference']) ? $payment['external_reference'] : null,
				'checkout_url' => isset($transaction_data['ticket_url']) ? $transaction_data['ticket_url'] : null,
				'checkout_type' => 'pix',
				'status' => $this->mercadopago_saas->map_payment_status(isset($payment['status']) ? $payment['status'] : 'pending', isset($payment['status_detail']) ? $payment['status_detail'] : ''),
				'gateway_status_detail' => isset($payment['status_detail']) ? $payment['status_detail'] : null,
			]);
			$message = 'PIX gerado com sucesso. Use o QR Code ou copie o codigo Pix para concluir o pagamento.';
			if($is_ajax){
				$this->output->set_content_type('application/json')->set_output(json_encode([
					'ok' => true,
					'message' => $message,
					'payment' => $payment,
					'transaction_data' => $transaction_data,
				]));
				return;
			}
			$this->session->set_flashdata('public_signup_ok', $message);
		} catch (Exception $e) {
			$message = 'Nao foi possivel gerar o PIX agora: '.$e->getMessage();
			if($is_ajax){
				$this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
					'ok' => false,
					'message' => $message,
				]));
				return;
			}
			$this->session->set_flashdata('public_signup_error', $message);
		}

		redirect('assinar/pagamento?subscription='.(int)$detail['subscription']->id);
	}

	public function assinatura_pagamento_cartao()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			$this->output->set_content_type('application/json')->set_status_header(404)->set_output(json_encode(['ok' => false, 'message' => 'Assinatura nao encontrada.']));
			return;
		}
		$open_cycle = $this->saas_model->ensure_checkout_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'message' => 'Nao existe ciclo pendente para cobrar.', 'redirect_url' => base_url().'assinar/sucesso?subscription='.(int)$detail['subscription']->id]));
			return;
		}
		$form_data = json_decode(file_get_contents('php://input'), true);
		if(!is_array($form_data)){
			$form_data = array();
		}

		$this->load->library('mercadopago_saas');
		try {
			$payment = $this->mercadopago_saas->create_card_payment(
				$detail['subscription'],
				$detail['tenant'],
				$detail['owner'],
				$open_cycle,
				$detail['plano'],
				$form_data
			);

			$this->saas_model->append_billing_event([
				'subscription_id' => (int)$detail['subscription']->id,
				'tenant_id' => (int)$detail['tenant']->id,
				'cycle_id' => (int)$open_cycle->id,
				'event_type' => 'mercadopago_card_created',
				'gateway' => 'mercadopago',
				'gateway_reference' => isset($payment['id']) ? (string)$payment['id'] : '',
				'status' => isset($payment['status']) ? (string)$payment['status'] : '',
				'amount' => isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : (float)$open_cycle->amount_due,
				'payload_text' => json_encode($payment),
			]);

			$mapped_status = $this->mercadopago_saas->map_payment_status(isset($payment['status']) ? $payment['status'] : '', isset($payment['status_detail']) ? $payment['status_detail'] : '');
			$this->saas_model->save_checkout_data((int)$detail['subscription']->id, [
				'gateway_reference' => isset($payment['external_reference']) ? $payment['external_reference'] : null,
				'checkout_type' => 'card',
				'status' => $mapped_status,
				'gateway_status_detail' => isset($payment['status_detail']) ? $payment['status_detail'] : null,
			]);

			if(in_array(isset($payment['status']) ? $payment['status'] : '', ['approved', 'authorized'])){
				$this->saas_model->register_cycle_payment((int)$open_cycle->id, 'card', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'Pagamento confirmado via cartao Mercado Pago ID '.(isset($payment['id']) ? $payment['id'] : ''));
			}

			$this->output->set_content_type('application/json')->set_output(json_encode([
				'ok' => true,
				'status' => isset($payment['status']) ? $payment['status'] : '',
				'status_detail' => isset($payment['status_detail']) ? $payment['status_detail'] : '',
				'payment_id' => isset($payment['id']) ? $payment['id'] : null,
				'message' => in_array(isset($payment['status']) ? $payment['status'] : '', ['approved', 'authorized']) ? 'Pagamento aprovado com sucesso.' : 'Pagamento enviado ao Mercado Pago. Confira o status abaixo.',
				'redirect_url' => in_array(isset($payment['status']) ? $payment['status'] : '', ['approved', 'authorized'])
					? base_url().'assinar/sucesso?subscription='.(int)$detail['subscription']->id
					: base_url().'assinar/pagamento?subscription='.(int)$detail['subscription']->id,
			]));
		} catch (Exception $e) {
			$this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
				'ok' => false,
				'message' => $e->getMessage(),
			]));
		}
	}

	public function assinatura_pagamento_status()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			redirect('assinar');
			return;
		}
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$this->session->set_flashdata('public_signup_ok', 'A assinatura nao possui ciclo pendente no momento.');
			redirect('assinar/pagamento?subscription='.(int)$detail['subscription']->id);
			return;
		}
		$event = $this->saas_model->get_latest_cycle_payment_event((int)$open_cycle->id);
		if(!$event || trim((string)$event->gateway_reference) === ''){
			$this->session->set_flashdata('public_signup_error', 'Ainda nao existe pagamento Mercado Pago vinculado a este ciclo.');
			redirect('assinar/pagamento?subscription='.(int)$detail['subscription']->id);
			return;
		}
		$this->load->library('mercadopago_saas');
		try {
			$payment = $this->mercadopago_saas->get_payment($event->gateway_reference);
			$this->saas_model->append_billing_event([
				'subscription_id' => (int)$detail['subscription']->id,
				'tenant_id' => (int)$detail['tenant']->id,
				'cycle_id' => (int)$open_cycle->id,
				'event_type' => 'mercadopago_payment_sync',
				'gateway' => 'mercadopago',
				'gateway_reference' => isset($payment['id']) ? (string)$payment['id'] : '',
				'status' => isset($payment['status']) ? (string)$payment['status'] : '',
				'amount' => isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : (float)$open_cycle->amount_due,
				'payload_text' => json_encode($payment),
			]);
			$this->saas_model->save_checkout_data((int)$detail['subscription']->id, [
				'checkout_type' => isset($payment['payment_method_id']) ? $payment['payment_method_id'] : $detail['subscription']->checkout_type,
				'status' => $this->mercadopago_saas->map_payment_status(isset($payment['status']) ? $payment['status'] : '', isset($payment['status_detail']) ? $payment['status_detail'] : ''),
				'gateway_status_detail' => isset($payment['status_detail']) ? $payment['status_detail'] : null,
			]);
			if(in_array(isset($payment['status']) ? $payment['status'] : '', ['approved', 'authorized'])){
				$this->saas_model->register_cycle_payment((int)$open_cycle->id, isset($payment['payment_method_id']) ? $payment['payment_method_id'] : 'mercadopago', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'Pagamento confirmado via sincronizacao Mercado Pago ID '.(isset($payment['id']) ? $payment['id'] : ''));
				$this->session->set_flashdata('public_signup_ok', 'Pagamento confirmado com sucesso.');
				redirect('assinar/sucesso?subscription='.(int)$detail['subscription']->id);
				return;
			}else{
				$this->session->set_flashdata('public_signup_ok', 'Status sincronizado: '.(isset($payment['status']) ? $payment['status'] : 'indefinido').'.');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('public_signup_error', 'Falha ao consultar o status no Mercado Pago: '.$e->getMessage());
		}
		redirect('assinar/pagamento?subscription='.(int)$detail['subscription']->id);
	}

	public function assinatura_sucesso()
	{
		$subscription_id = (int)$this->input->get('subscription');
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			redirect('assinar');
			return;
		}
		$dados['detail'] = $detail;
		$dados['onboarding'] = $this->saas_model->get_tenant_onboarding_summary((int)$detail['tenant']->id);
		$dados['flash_ok'] = $this->session->flashdata('public_signup_ok');
		$dados['flash_error'] = $this->session->flashdata('public_signup_error');
		$dados['payment_url'] = base_url().'assinar/pagamento?subscription='.(int)$detail['subscription']->id;
		$this->load->view('public/assinar-sucesso', $dados);
	}

	// ── DEFINIR SENHA VIA TOKEN ──────────────────────────────────────────

	public function definir_senha($token = '')
	{
		$token = preg_replace('/[^a-f0-9]/i', '', (string)$token);
		if(strlen($token) !== 64){
			$this->session->set_flashdata('operational_trial_error', 'Link de definição de senha inválido ou expirado.');
			redirect('experimentar');
			return;
		}
		$user = $this->db->query(
			"SELECT id, nome, email FROM usuarios
			 WHERE senha_token = ".$this->db->escape($token)."
			 AND senha_token_expires > NOW()
			 LIMIT 1"
		)->row();
		if(!$user){
			$this->session->set_flashdata('operational_trial_error', 'Este link de definição de senha já foi usado ou expirou. Faça login normalmente.');
			redirect('admin');
			return;
		}
		$dados['token'] = $token;
		$dados['nome']  = $user->nome;
		$dados['email'] = $user->email;
		$dados['flash_error'] = $this->session->flashdata('definir_senha_error');
		$this->load->view('public/definir-senha', $dados);
	}

	public function salvar_senha()
	{
		$token  = preg_replace('/[^a-f0-9]/i', '', (string)$this->input->post('token'));
		$nova   = (string)$this->input->post('nova_senha');
		$conf   = (string)$this->input->post('confirmar_senha');

		if(strlen($token) !== 64){
			$this->session->set_flashdata('operational_trial_error', 'Token inválido.');
			redirect('experimentar');
			return;
		}
		if(strlen($nova) < 6){
			$this->session->set_flashdata('definir_senha_error', 'A senha precisa ter pelo menos 6 caracteres.');
			redirect('acesso/senha/'.$token);
			return;
		}
		if($nova !== $conf){
			$this->session->set_flashdata('definir_senha_error', 'As senhas não coincidem. Tente novamente.');
			redirect('acesso/senha/'.$token);
			return;
		}

		$user = $this->db->query(
			"SELECT id, nome, nivel, login FROM usuarios
			 WHERE senha_token = ".$this->db->escape($token)."
			 AND senha_token_expires > NOW()
			 LIMIT 1"
		)->row();
		if(!$user){
			$this->session->set_flashdata('operational_trial_error', 'Link expirado. Faça login normalmente e redefina sua senha nas configurações.');
			redirect('admin');
			return;
		}

		$this->db->where('id', $user->id);
		$this->db->update('usuarios', [
			'senha'               => password_hash($nova, PASSWORD_DEFAULT),
			'senha_token'         => null,
			'senha_token_expires' => null,
		]);

		// Garante sessão ativa
		if(!$this->session->userdata('id')){
			$this->session->set_userdata([
				'id'    => $user->id,
				'nome'  => $user->nome,
				'nivel' => $user->nivel,
				'login' => $user->login,
				'usr'   => $user,
			]);
		}

		$this->session->set_flashdata('operational_trial_ok', 'Senha definida com sucesso! Bem-vindo(a) ao sistema.');
		redirect('adm/atendimento');
	}

	// ── SEO LANDING PAGES ───────────────────────────────────────────────

	public function seo_sistema_para_clinicas()
	{
		$this->load->view('public/seo/sistema-para-clinicas');
	}

	public function seo_sistema_clinica_medica()
	{
		$this->load->view('public/seo/sistema-para-clinica-medica');
	}

	public function seo_prontuario_eletronico()
	{
		$this->load->view('public/seo/sistema-prontuario-eletronico');
	}

	public function seo_sistema_psicologos()
	{
		$this->load->view('public/seo/sistema-para-psicologos');
	}

	public function seo_sistema_dentistas()
	{
		$this->load->view('public/seo/sistema-para-dentistas');
	}

	public function seo_software_clinicas_odontologicas()
	{
		$this->load->view('public/seo/software-para-clinicas-odontologicas');
	}

	public function seo_consultorio_medico()
	{
		$this->load->view('public/seo/sistema-para-consultorio-medico');
	}

	public function seo_clinica_fisioterapia()
	{
		$this->load->view('public/seo/sistema-para-clinica-de-fisioterapia');
	}

	public function seo_alternativa_feegow()
	{
		$this->load->view('public/seo/alternativa-feegow');
	}

	public function seo_alternativa_odontoclinic()
	{
		$this->load->view('public/seo/alternativa-odontoclinic');
	}

	public function seo_sistema_gratuito()
	{
		$this->load->view('public/seo/sistema-gratuito-para-clinicas');
	}

	public function seo_software_para_clinicas()
	{
		$this->load->view('public/seo/software-para-clinicas');
	}

	public function seo_clinica_oftalmologica()
	{
		$this->load->view('public/seo/sistema-para-clinica-oftalmologica');
	}

	public function seo_software_para_medicos()
	{
		$this->load->view('public/seo/software-para-medicos');
	}

	public function seo_sistema_nutricionistas()
	{
		$this->load->view('public/seo/sistema-para-nutricionistas');
	}

	public function sobre()
	{
		$this->load->view('public/sobre');
	}

	public function contato()
	{
		$this->load->view('public/contato');
	}

	public function politica_privacidade()
	{
		$this->load->view('public/politica-de-privacidade');
	}

	// ── E-MAIL BOAS-VINDAS ───────────────────────────────────────────────

	private function _enviar_email_boas_vindas($result)
	{
		try {
			$this->config->load('email');
			$this->load->library('email');
			$this->email->initialize($this->config->config);

			$nome_clinica  = htmlspecialchars((string)$result['tenant_nome']);
			$login         = htmlspecialchars((string)$result['login']);
			$senha         = htmlspecialchars((string)$result['senha_gerada']);
			$token         = (string)$result['token'];
			$link_senha    = base_url().'acesso/senha/'.$token;
			$link_sistema  = base_url().'admin';
			$trial_fim     = isset($result['trial_ends_at'])
				? date('d/m/Y', strtotime($result['trial_ends_at']))
				: date('d/m/Y', strtotime('+30 days'));

			$body = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f6f8fb;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fb;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <tr><td style="background:linear-gradient(90deg,#0f766e,#f97316);padding:32px 40px;">
    <p style="margin:0;font-size:13px;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.8);font-weight:700;">UTecnologia Saúde</p>
    <h1 style="margin:10px 0 0;color:#fff;font-size:26px;font-weight:800;">Seu acesso está pronto! 🎉</h1>
  </td></tr>
  <tr><td style="padding:36px 40px;">
    <p style="font-size:16px;color:#334155;line-height:1.7;">Olá, <strong>'.htmlspecialchars((string)$result['login']).'</strong>!</p>
    <p style="font-size:15px;color:#475569;line-height:1.7;">
      O ambiente <strong>'.$nome_clinica.'</strong> foi criado com sucesso.
      Você já pode entrar no sistema e começar a usar a agenda, prontuários e atendimentos pelos próximos 30 dias sem nenhum custo.
    </p>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1fdf9;border:1px solid #a7f3d0;border-radius:14px;padding:20px;margin:24px 0;">
      <tr><td>
        <p style="margin:0 0 10px;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#0f766e;">Seus dados de acesso</p>
        <p style="margin:4px 0;font-size:15px;color:#172033;"><strong>E-mail:</strong> '.$login.'</p>
        <p style="margin:4px 0;font-size:15px;color:#172033;"><strong>Senha provisória:</strong> <code style="background:#e0f2fe;padding:2px 8px;border-radius:6px;font-size:15px;">'.$senha.'</code></p>
        <p style="margin:10px 0 0;font-size:13px;color:#64748b;">Trial ativo até: <strong>'.$trial_fim.'</strong></p>
      </td></tr>
    </table>
    <p style="font-size:14px;color:#475569;line-height:1.7;">Recomendamos que você defina uma senha personalizada clicando no botão abaixo:</p>
    <p style="margin:24px 0;">
      <a href="'.$link_senha.'" style="display:inline-block;padding:14px 28px;background:linear-gradient(90deg,#0f766e,#f97316);color:#fff;font-size:15px;font-weight:700;border-radius:999px;text-decoration:none;">Definir minha senha →</a>
    </p>
    <p style="margin:16px 0;">
      <a href="'.$link_sistema.'" style="display:inline-block;padding:12px 24px;background:#fff;border:1px solid #d1d5db;color:#374151;font-size:14px;font-weight:600;border-radius:999px;text-decoration:none;">Entrar no sistema com senha provisória</a>
    </p>
    <p style="font-size:13px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:20px;margin-top:28px;">
      Dúvidas? Responda este e-mail ou acesse <a href="https://wa.me/5581983276882" style="color:#0f766e;">WhatsApp</a>.<br>
      UTecnologia Saúde — utecnologia.com.br
    </p>
  </td></tr>
</table>
</td></tr></table>
</body></html>';

			$this->email->from('suporte@utecnologia.com.br', 'UTecnologia Saúde');
			$this->email->to($result['login']);
			$this->email->bcc('igor_marlus@yahoo.com.br');
			$this->email->subject('Seu acesso UTecnologia Saúde está pronto — '.$nome_clinica);
			$this->email->message($body);
			$this->email->send();
		} catch (Exception $e) {
			log_message('error', 'Email boas-vindas falhou: '.$e->getMessage());
		}
	}



}
