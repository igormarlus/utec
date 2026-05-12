<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model('adm/saas_model');
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
		$dados['planos'] = $this->saas_model->get_public_plans();
		$dados['flash_ok'] = $this->session->flashdata('operational_trial_ok');
		$dados['flash_error'] = $this->session->flashdata('operational_trial_error');
		$this->load->view('public/experimentar', $dados);
	}

	public function iniciar_experiencia()
	{
		$result = $this->saas_model->create_operational_trial_signup($this->input->post());
		if(!$result['ok']){
			$this->session->set_flashdata('operational_trial_error', $result['msg']);
			redirect('experimentar');
			return;
		}

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
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
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
		$detail = $this->saas_model->get_subscription_detail_system($subscription_id);
		if(!$detail){
			redirect('assinar');
			return;
		}
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$this->session->set_flashdata('public_signup_ok', 'Nao existe ciclo pendente para gerar PIX nesta assinatura.');
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
			$this->session->set_flashdata('public_signup_ok', 'PIX gerado com sucesso. Use o QR Code ou copie o codigo Pix para concluir o pagamento.');
		} catch (Exception $e) {
			$this->session->set_flashdata('public_signup_error', 'Nao foi possivel gerar o PIX agora: '.$e->getMessage());
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
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
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

	
	
}
