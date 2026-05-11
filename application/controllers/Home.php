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

	public function contratar()
	{
		$result = $this->saas_model->create_public_tenant_signup($this->input->post());
		if(!$result['ok']){
			$this->session->set_flashdata('public_signup_error', $result['msg']);
			redirect('assinar');
			return;
		}

		$this->load->library('mercadopago_saas');
		$detail = $this->saas_model->get_subscription_detail_system((int)$result['subscription_id']);
		if($detail && $this->mercadopago_saas->is_available()){
			try {
				$back_url = base_url().'assinar/sucesso?subscription='.(int)$result['subscription_id'];
				$preapproval = $this->mercadopago_saas->create_preapproval(
					$detail['subscription'],
					$detail['tenant'],
					$detail['owner'],
					$detail['plano'],
					[
						'back_url' => $back_url,
						'external_reference' => 'public-sub-'.(int)$detail['subscription']->id,
					]
				);

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
			} catch (Exception $e) {
				$this->session->set_flashdata('public_signup_error', 'Seu cadastro foi criado, mas nao conseguimos abrir o checkout agora: '.$e->getMessage());
				redirect('assinar/sucesso?subscription='.(int)$result['subscription_id']);
				return;
			}
		}

		$this->session->set_flashdata('public_signup_ok', 'Cadastro criado com sucesso. Agora finalize a ativacao comercial do seu plano.');
		redirect('assinar/sucesso?subscription='.(int)$result['subscription_id']);
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
		$this->load->view('public/assinar-sucesso', $dados);
	}

	
	
}
