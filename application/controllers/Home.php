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
		$this->padrao_model->track_ai_referral();
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
			'senha'             => (string)$this->input->post('senha'),
			'senha_confirmacao' => (string)$this->input->post('senha_confirmacao'),
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
				'usr'   => true,
			]);
		}

		// E-mail de boas-vindas com login + a senha escolhida (lembrete para a 2ª visita)
		$this->load->library('email_acesso');
		$this->email_acesso->boas_vindas([
			'nome'        => $result['nome_responsavel'],
			'login'       => $result['login'],
			'senha'       => $result['senha'],
			'tenant_nome' => $result['tenant_nome'],
			'trial_fim'   => !empty($result['trial_ends_at']) ? date('d/m/Y', strtotime($result['trial_ends_at'])) : '',
			'plano_nome'  => $result['plano_nome'],
			'origem'      => 'trial',
		]);

		// Atribuicao de trafego de IA — trial criado
		$this->padrao_model->mark_ai_conversion('trial', null, 'trial_'.(int)$result['subscription_id'], 'plano:'.(string)$this->input->post('plano_id'));

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

		$this->load->library('email_acesso');
		$this->email_acesso->boas_vindas([
			'nome'        => $result['nome_responsavel'],
			'login'       => $result['login'],
			'senha'       => $result['senha'],
			'tenant_nome' => $result['tenant_nome'],
			'trial_fim'   => !empty($result['trial_ends_at']) ? date('d/m/Y', strtotime($result['trial_ends_at'])) : '',
			'plano_nome'  => $result['plano_nome'],
			'origem'      => 'assinatura',
		]);

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

		// Atribuicao de trafego de IA — assinatura iniciada (sem valor: intencao, nao receita)
		$this->padrao_model->mark_ai_conversion('assinatura', null, 'sub_'.(int)$result['subscription_id'], 'etapa:cadastro');

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
				$this->padrao_model->mark_ai_conversion('assinatura', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'pay_'.(isset($payment['id']) ? $payment['id'] : (int)$detail['subscription']->id), 'via:cartao');
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
				$this->padrao_model->mark_ai_conversion('assinatura', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'pay_'.(isset($payment['id']) ? $payment['id'] : (int)$detail['subscription']->id), 'via:sync');
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
			$this->session->set_flashdata('operational_trial_error', 'Link de definição de senha inválido ou expirado. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}
		$user = $this->db->query(
			"SELECT id, nome, email FROM usuarios
			 WHERE senha_token = ".$this->db->escape($token)."
			 AND senha_token_expires > NOW()
			 LIMIT 1"
		)->row();
		if(!$user){
			$this->session->set_flashdata('operational_trial_error', 'Este link já foi usado ou expirou. Peça um novo em "Esqueci minha senha".');
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
		$this->load->helper('acesso');
		$token  = preg_replace('/[^a-f0-9]/i', '', (string)$this->input->post('token'));
		$nova   = (string)$this->input->post('nova_senha');
		$conf   = (string)$this->input->post('confirmar_senha');

		if(strlen($token) !== 64){
			$this->session->set_flashdata('operational_trial_error', 'Link inválido. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}
		$validacao = utec_acesso_validar_senha($nova, $conf);
		if(!$validacao['ok']){
			$this->session->set_flashdata('definir_senha_error', $validacao['msg']);
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
			$this->session->set_flashdata('operational_trial_error', 'Link expirado. Peça um novo em "Esqueci minha senha".');
			redirect('admin');
			return;
		}

		$this->db->where('id', $user->id);
		$this->db->update('usuarios', [
			'senha'               => password_hash($nova, PASSWORD_DEFAULT),
			'senha_token'         => null,
			'senha_token_expires' => null,
		]);

		// Sempre entra como o dono do token (outra sessão aberta no navegador é substituída)
		$this->session->sess_regenerate(true);
		$this->session->set_userdata([
			'usr'   => true,
			'id'    => $user->id,
			'nome'  => $user->nome,
			'nivel' => $user->nivel,
			'login' => $user->login,
		]);

		$this->session->set_flashdata('operational_trial_ok', 'Senha definida com sucesso! Bem-vindo(a) ao sistema.');
		$nivel = (int)$user->nivel;
		redirect(($nivel >= 2 && $nivel <= 4) ? 'adm/atendimento' : 'adm/usuarios');
	}

	// ── ESQUECI MINHA SENHA ─────────────────────────────────────────────

	public function esqueci_senha()
	{
		$dados['flash_ok']    = $this->session->flashdata('esqueci_ok');
		$dados['flash_error'] = $this->session->flashdata('esqueci_error');
		$this->load->view('public/esqueci-senha', $dados);
	}

	public function enviar_redefinicao()
	{
		$this->load->helper('acesso');
		$esqueci_msg_generica = 'Se houver uma conta com esses dados, enviamos um link para o e-mail cadastrado. O link vale por 1 hora.';
		$identificacao = trim((string)$this->input->post('identificacao'));

		if($identificacao === ''){
			$this->session->set_flashdata('esqueci_error', 'Informe seu e-mail ou usuário.');
			redirect('acesso/esqueci');
			return;
		}

		if($this->db->field_exists('senha_token', 'usuarios') && $this->db->field_exists('senha_token_expires', 'usuarios')){
			$usuarios = $this->db->query(
				"SELECT id, nome, login, email,
				        TIMESTAMPDIFF(SECOND, NOW(), senha_token_expires) AS segundos_restantes
				   FROM usuarios
				  WHERE nivel BETWEEN 1 AND 4
				    AND (login = ? OR LOWER(email) = ?)
				  LIMIT 5",
				[$identificacao, strtolower($identificacao)]
			)->result();

			if(count($usuarios)){
				$this->load->library('email_acesso');
			}
			foreach($usuarios as $u){
				if(!utec_acesso_email_valido($u->email)){ continue; }
				if(!utec_acesso_pode_reenviar($u->segundos_restantes, 60, 2)){ continue; }
				$token = utec_acesso_gerar_token();
				$this->db->set('senha_token', $token);
				$this->db->set('senha_token_expires', 'DATE_ADD(NOW(), INTERVAL 60 MINUTE)', false);
				$this->db->where('id', (int)$u->id);
				$this->db->update('usuarios');
				$this->email_acesso->redefinicao([
					'email' => trim((string)$u->email),
					'nome'  => $u->nome,
					'login' => $u->login,
					'token' => $token,
				]);
			}
		}else{
			log_message('error', 'esqueci_senha: colunas senha_token/senha_token_expires ausentes em usuarios');
		}

		$this->session->set_flashdata('esqueci_ok', $esqueci_msg_generica);
		redirect('acesso/esqueci');
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

	public function seo_alternativa_shosp()
	{
		$this->load->view('public/seo/alternativa-shosp');
	}

	public function seo_alternativa_clinica_nuvens()
	{
		$this->load->view('public/seo/alternativa-clinica-nas-nuvens');
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

	public function seo_sistema_ginecologia()
	{
		$this->load->view('public/seo/sistema-para-ginecologia');
	}

	public function seo_sistema_pediatria()
	{
		$this->load->view('public/seo/sistema-para-pediatria');
	}

	public function seo_sistema_psiquiatria()
	{
		$this->load->view('public/seo/sistema-para-psiquiatria');
	}

	public function seo_sistema_fonoaudiologia()
	{
		$this->load->view('public/seo/sistema-para-fonoaudiologia');
	}

	public function seo_sistema_medicina_trabalho()
	{
		$this->load->view('public/seo/sistema-para-medicina-do-trabalho');
	}

	public function seo_confirmacao_whatsapp()
	{
		$this->load->view('public/seo/confirmacao-de-consulta-por-whatsapp');
	}

	public function seo_casos_de_uso()
	{
		$this->load->view('public/seo/casos-de-uso');
	}

	public function seo_chatbot_para_clinicas()
	{
		$this->load->view('public/seo/chatbot-para-clinicas');
	}

	public function sobre()
	{
		$this->load->view('public/sobre');
	}

	public function contato()
	{
		$this->load->view('public/contato');
	}

	public function track_evento()
	{
		$tipo = strtolower(trim((string)$this->input->get('t')));
		$permitidos = array('whatsapp', 'contato');
		if(!in_array($tipo, $permitidos, true)){
			$this->output->set_status_header(204);
			return;
		}

		// Rate-limit simples: 1 evento do mesmo tipo por sessao a cada 60s.
		$flagkey = 'ai_evt_'.$tipo;
		$last = (int)$this->session->userdata($flagkey);
		if($last > 0 && (time() - $last) < 60){
			$this->output->set_status_header(204);
			return;
		}
		$this->session->set_userdata($flagkey, time());

		$origem = preg_replace('/[^a-z0-9_\-\/]/i', '', (string)$this->input->get('o'));
		$this->padrao_model->mark_ai_conversion($tipo, null, null, $origem !== '' ? 'origem:'.mb_substr($origem, 0, 80) : null);

		$this->output->set_status_header(204);
	}

	public function politica_privacidade()
	{
		$this->load->view('public/politica-de-privacidade');
	}
}
