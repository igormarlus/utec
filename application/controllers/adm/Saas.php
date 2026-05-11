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
		$this->load->library('mercadopago_saas');
		$viewer = $this->padrao_model->get_usuario_logado();
		$detail = $this->saas_model->get_subscription_detail($subscription_id, $viewer);
		if(!$detail){
			show_error('Assinatura nao encontrada ou indisponivel para este usuario.', 404);
			return;
		}
		if(!$detail['owner'] || trim((string)$detail['owner']->email) === ''){
			$this->session->set_flashdata('saas_error', 'O responsavel da assinatura precisa ter e-mail para gerar o checkout do Mercado Pago.');
			redirect('adm/saas/tenant/'.$detail['subscription']->tenant_id);
			return;
		}

		try {
			$preapproval = $this->mercadopago_saas->create_preapproval(
				$detail['subscription'],
				$detail['tenant'],
				$detail['owner'],
				$detail['plano']
			);
		} catch (Exception $e) {
			$this->session->set_flashdata('saas_error', 'Falha ao gerar checkout do Mercado Pago: '.$e->getMessage());
			redirect('adm/saas/tenant/'.$detail['subscription']->tenant_id);
			return;
		}

		$this->saas_model->save_checkout_data((int)$detail['subscription']->id, [
			'gateway_subscription_id' => isset($preapproval->id) ? $preapproval->id : null,
			'gateway_reference' => isset($preapproval->external_reference) ? $preapproval->external_reference : null,
			'checkout_url' => isset($preapproval->init_point) ? $preapproval->init_point : null,
			'checkout_type' => 'preapproval',
			'status' => $this->saas_model->map_subscription_status(isset($preapproval->status) ? $preapproval->status : 'pending'),
			'gateway_status_detail' => isset($preapproval->status) ? $preapproval->status : 'pending',
		]);

		if(isset($preapproval->init_point) && trim((string)$preapproval->init_point) !== ''){
			redirect($preapproval->init_point);
			return;
		}

		$this->session->set_flashdata('saas_error', 'Checkout criado, mas o Mercado Pago nao retornou uma URL de redirecionamento.');
		redirect('adm/saas/tenant/'.$detail['subscription']->tenant_id);
	}

	function webhook_mercadopago(){
		$this->load->library('mercadopago_saas');
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);
		$type = isset($_GET['type']) ? $_GET['type'] : (isset($payload['type']) ? $payload['type'] : '');
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
		if(!$detail['subscription']->gateway_subscription_id){
			$this->session->set_flashdata('saas_error', 'A assinatura ainda nao possui identificador do Mercado Pago.');
			redirect('adm/saas/tenant/'.$detail['subscription']->tenant_id);
			return;
		}

		try {
			$preapproval = $this->mercadopago_saas->get_preapproval($detail['subscription']->gateway_subscription_id);
			$status = isset($preapproval->status) ? $preapproval->status : 'pending';
			$this->saas_model->sync_subscription_gateway_status(
				(string)$detail['subscription']->gateway_subscription_id,
				$status,
				'sync_manual',
				json_encode($preapproval)
			);
			$this->session->set_flashdata('saas_ok', 'Status da assinatura sincronizado com o Mercado Pago.');
		} catch (Exception $e) {
			$this->session->set_flashdata('saas_error', 'Falha ao sincronizar assinatura: '.$e->getMessage());
		}

		redirect('adm/saas/tenant/'.$detail['subscription']->tenant_id);
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
}
