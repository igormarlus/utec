<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saas extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('adm/saas_model');
		$this->load->model('padrao_model');
		if($this->router->fetch_method() !== 'webhook_mercadopago'){
			$this->usuarios_model->verSession();
		}
	}

	function index(){
		$viewer = $this->padrao_model->get_usuario_logado();
		$dados = $this->saas_model->get_dashboard_data($viewer);
		$dados['schema_ok'] = $this->saas_model->has_schema();
		$dados['viewer'] = $viewer;
		$dados['flash_ok'] = $this->session->flashdata('saas_ok');
		$dados['flash_error'] = $this->session->flashdata('saas_error');
		$this->load->library('mercadopago_saas');
		$dados['mercadopago_ready'] = $this->mercadopago_saas->is_available();
		$this->load->view('adm/saas/index', $dados);
	}

	function bloqueado(){
		$viewer = $this->padrao_model->get_usuario_logado();
		$dados['viewer'] = $viewer;
		$dados['tenant'] = $this->padrao_model->get_logged_tenant();
		$this->load->view('adm/saas/bloqueado', $dados);
	}

	function provisionar(){
		$viewer = $this->padrao_model->get_usuario_logado();
		$result = $this->saas_model->provision_tenant($this->input->post(), $viewer);
		if($result['ok']){
			$this->session->set_flashdata('saas_ok', $result['msg']);
			redirect('adm/saas/tenant/'.$result['tenant_id']);
			return;
		}
		$this->session->set_flashdata('saas_error', $result['msg']);
		redirect('adm/saas');
	}

	function tenant($tenant_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_tenant_detail($tenant_id, $viewer);
		if(!$detail){
			show_error('Tenant nao encontrado ou indisponivel para este usuario.', 404);
			return;
		}
		$detail['schema_ok'] = $this->saas_model->has_schema();
		$detail['viewer'] = $viewer;
		$detail['flash_ok'] = $this->session->flashdata('saas_ok');
		$detail['flash_error'] = $this->session->flashdata('saas_error');
		$this->load->library('mercadopago_saas');
		$detail['mercadopago_ready'] = $this->mercadopago_saas->is_available();
		$this->load->view('adm/saas/tenant', $detail);
	}

	function checkout($subscription_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			show_error('Assinatura nao encontrada ou indisponivel para este usuario.', 404);
			return;
		}
		redirect('adm/saas/pagamento/'.$detail['subscription']->id);
	}

	function pagamento($subscription_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			show_error('Assinatura nao encontrada ou indisponivel para este usuario.', 404);
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
		$dados['flash_ok'] = $this->session->flashdata('saas_ok');
		$dados['flash_error'] = $this->session->flashdata('saas_error');
		$dados['status_refresh_url'] = base_url().'adm/saas/sincronizar/'.(int)$detail['subscription']->id;
		$dados['pix_submit_url'] = base_url().'adm/saas/pagamento_pix/'.(int)$detail['subscription']->id;
		$dados['card_submit_url'] = base_url().'adm/saas/pagamento_cartao/'.(int)$detail['subscription']->id;
		$dados['back_url'] = base_url().'adm/saas/tenant/'.(int)$detail['tenant']->id;
		$dados['page_mode'] = 'admin';
		$this->load->view('public/assinar-pagamento', $dados);
	}

	function webhook_mercadopago(){
		$this->load->library('mercadopago_saas');
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);
		$type = isset($_GET['type']) ? $_GET['type'] : (isset($_GET['topic']) ? $_GET['topic'] : (isset($payload['type']) ? $payload['type'] : (isset($payload['topic']) ? $payload['topic'] : '')));
		$data_id = isset($_GET['data_id']) ? $_GET['data_id'] : '';

		if($data_id === '' && isset($payload['data']['id'])){
			$data_id = $payload['data']['id'];
		}
		if($data_id === '' && isset($_GET['id'])){
			$data_id = $_GET['id'];
		}

		if($data_id === ''){
			echo json_encode(['ok' => false, 'message' => 'id ausente']);
			return;
		}

		try {
			if($type === 'payment' || strpos((string)$type, 'payment') === 0){
				$payment = $this->mercadopago_saas->get_payment($data_id);
				$this->handle_payment_notification($payment, $raw, 'mercadopago_webhook');
				echo json_encode(['ok' => true, 'status' => isset($payment['status']) ? $payment['status'] : 'pending']);
				return;
			}

			$preapproval = $this->mercadopago_saas->get_preapproval($data_id);
			$status = isset($preapproval->status) ? $preapproval->status : 'pending';
			$this->saas_model->sync_subscription_gateway_status(
				(string)$data_id,
				$status,
				$type !== '' ? $type : $status,
				$raw
			);
			echo json_encode(['ok' => true, 'status' => $status]);
			return;
		} catch (Exception $e) {
			echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
			return;
		}
	}

	function sincronizar($subscription_id){
		$this->load->library('mercadopago_saas');
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			show_error('Assinatura nao encontrada ou indisponivel para este usuario.', 404);
			return;
		}
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
		$latest_event = $open_cycle ? $this->saas_model->get_latest_cycle_payment_event((int)$open_cycle->id) : null;

		try {
			if($latest_event && trim((string)$latest_event->gateway_reference) !== ''){
				$payment = $this->mercadopago_saas->get_payment($latest_event->gateway_reference);
				$this->handle_payment_notification($payment, json_encode($payment), 'sync_manual');
				$this->session->set_flashdata('saas_ok', 'Status do pagamento sincronizado com o Mercado Pago.');
			}elseif($detail['subscription']->gateway_subscription_id){
				$preapproval = $this->mercadopago_saas->get_preapproval($detail['subscription']->gateway_subscription_id);
				$status = isset($preapproval->status) ? $preapproval->status : 'pending';
				$this->saas_model->sync_subscription_gateway_status(
					(string)$detail['subscription']->gateway_subscription_id,
					$status,
					'sync_manual',
					json_encode($preapproval)
				);
				$this->session->set_flashdata('saas_ok', 'Status da assinatura sincronizado com o Mercado Pago.');
			}else{
				$this->session->set_flashdata('saas_error', 'A assinatura ainda nao possui checkout ou pagamento Mercado Pago vinculado.');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('saas_error', 'Falha ao sincronizar assinatura: '.$e->getMessage());
		}

		redirect('adm/saas/pagamento/'.(int)$detail['subscription']->id);
	}

	function pagamento_pix($subscription_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			show_error('Assinatura nao encontrada ou indisponivel para este usuario.', 404);
			return;
		}
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$this->session->set_flashdata('saas_ok', 'Nao existe ciclo pendente para gerar PIX nesta assinatura.');
			redirect('adm/saas/pagamento/'.$detail['subscription']->id);
			return;
		}
		$this->load->library('mercadopago_saas');
		try {
			$payment = $this->mercadopago_saas->create_pix_payment($detail['subscription'], $detail['tenant'], $detail['owner'], $open_cycle, $detail['plano']);
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
			$this->session->set_flashdata('saas_ok', 'PIX gerado com sucesso para este ciclo.');
		} catch (Exception $e) {
			$this->session->set_flashdata('saas_error', 'Nao foi possivel gerar o PIX: '.$e->getMessage());
		}
		redirect('adm/saas/pagamento/'.$detail['subscription']->id);
	}

	function pagamento_cartao($subscription_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			$this->output->set_content_type('application/json')->set_status_header(404)->set_output(json_encode(['ok' => false, 'message' => 'Assinatura nao encontrada.']));
			return;
		}
		$open_cycle = $this->saas_model->get_open_cycle((int)$detail['subscription']->id);
		if(!$open_cycle){
			$this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'message' => 'Nao existe ciclo pendente para cobrar.', 'redirect_url' => base_url().'adm/saas/tenant/'.(int)$detail['tenant']->id]));
			return;
		}
		$form_data = json_decode(file_get_contents('php://input'), true);
		if(!is_array($form_data)){
			$form_data = array();
		}
		$this->load->library('mercadopago_saas');
		try {
			$payment = $this->mercadopago_saas->create_card_payment($detail['subscription'], $detail['tenant'], $detail['owner'], $open_cycle, $detail['plano'], $form_data);
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
			$this->saas_model->save_checkout_data((int)$detail['subscription']->id, [
				'gateway_reference' => isset($payment['external_reference']) ? $payment['external_reference'] : null,
				'checkout_type' => 'card',
				'status' => $this->mercadopago_saas->map_payment_status(isset($payment['status']) ? $payment['status'] : '', isset($payment['status_detail']) ? $payment['status_detail'] : ''),
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
					? base_url().'adm/saas/tenant/'.(int)$detail['tenant']->id
					: base_url().'adm/saas/pagamento/'.(int)$detail['subscription']->id,
			]));
		} catch (Exception $e) {
			$this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode([
				'ok' => false,
				'message' => $e->getMessage(),
			]));
		}
	}

	function registrar_pagamento($cycle_id){
		$viewer = $this->padrao_model->get_usuario_logado();
		if((int)$viewer->nivel !== 1){
			show_error('Apenas administradores podem registrar pagamento manual.', 403);
			return;
		}

		$result = $this->saas_model->register_cycle_payment((int)$cycle_id, 'manual', null, 'Pagamento registrado manualmente via painel');
		if($result['ok']){
			$this->session->set_flashdata('saas_ok', $result['msg']);
		}else{
			$this->session->set_flashdata('saas_error', $result['msg']);
		}
		redirect($this->input->get('back') ? $this->input->get('back') : 'adm/saas');
	}

	function rotina_cobranca(){
		$viewer = $this->padrao_model->get_usuario_logado();
		if((int)$viewer->nivel !== 1){
			show_error('Apenas administradores podem executar a rotina de cobranca.', 403);
			return;
		}
		$result = $this->saas_model->refresh_all_subscription_health();
		$this->session->set_flashdata('saas_ok', 'Rotina executada em '.$result['subscriptions'].' assinatura(s).');
		redirect('adm/saas');
	}

	protected function handle_payment_notification($payment, $raw_payload='', $event_type='mercadopago_payment_update')
	{
		if(!is_array($payment)){
			return false;
		}
		$external_reference = isset($payment['external_reference']) ? $payment['external_reference'] : '';
		$binding = $this->saas_model->resolve_cycle_by_external_reference($external_reference);
		if(!$binding){
			return false;
		}

		$this->saas_model->append_billing_event([
			'subscription_id' => (int)$binding['subscription']->id,
			'tenant_id' => (int)$binding['subscription']->tenant_id,
			'cycle_id' => (int)$binding['cycle']->id,
			'event_type' => $event_type,
			'gateway' => 'mercadopago',
			'gateway_reference' => isset($payment['id']) ? (string)$payment['id'] : '',
			'status' => isset($payment['status']) ? (string)$payment['status'] : '',
			'amount' => isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : (float)$binding['cycle']->amount_due,
			'payload_text' => $raw_payload !== '' ? $raw_payload : json_encode($payment),
		]);

		$this->saas_model->save_checkout_data((int)$binding['subscription']->id, [
			'gateway_reference' => $external_reference,
			'checkout_type' => $binding['method'],
			'status' => $this->mercadopago_saas->map_payment_status(isset($payment['status']) ? $payment['status'] : '', isset($payment['status_detail']) ? $payment['status_detail'] : ''),
			'gateway_status_detail' => isset($payment['status_detail']) ? $payment['status_detail'] : null,
		]);

		if(in_array(isset($payment['status']) ? $payment['status'] : '', ['approved', 'authorized'])){
			$this->saas_model->register_cycle_payment((int)$binding['cycle']->id, isset($payment['payment_method_id']) ? $payment['payment_method_id'] : $binding['method'], isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'Pagamento confirmado via Mercado Pago ID '.(isset($payment['id']) ? $payment['id'] : ''));
		}

		return true;
	}
}
