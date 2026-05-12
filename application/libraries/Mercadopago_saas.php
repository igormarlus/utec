<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mercadopago_saas {

	protected $CI;
	protected $config_loaded = false;
	protected $config_exists = false;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->config_exists = file_exists(APPPATH.'config/mercadopago.php');
		if($this->config_exists){
			$this->CI->load->config('mercadopago', true);
			$this->config_loaded = true;
			$this->boot_sdk();
		}
	}

	protected function boot_sdk()
	{
		if(!$this->config_loaded){
			return;
		}
		$autoload = FCPATH.'includes/mercadopago/lib/mercadopago/vendor/autoload.php';
		if(file_exists($autoload)){
			require_once $autoload;
		}
		$token = $this->get_access_token();
		if($token){
			MercadoPago\SDK::setAccessToken($token);
		}
	}

	public function is_available()
	{
		return $this->config_exists;
	}

	public function assert_ready()
	{
		if(!$this->config_exists){
			throw new Exception('O arquivo application/config/mercadopago.php ainda nao foi publicado no servidor.');
		}
	}

	public function get_access_token()
	{
		if(!$this->config_loaded){
			return '';
		}
		return (string)$this->CI->config->item('mercadopago_access_token', 'mercadopago');
	}

	public function get_public_key()
	{
		if(!$this->config_loaded){
			return '';
		}
		return (string)$this->CI->config->item('mercadopago_public_key', 'mercadopago');
	}

	public function get_currency_id()
	{
		if(!$this->config_loaded){
			return 'BRL';
		}
		$currency = (string)$this->CI->config->item('mercadopago_currency_id', 'mercadopago');
		return $currency !== '' ? $currency : 'BRL';
	}

	public function get_back_urls()
	{
		if(!$this->config_loaded){
			return [
				'success' => '',
				'pending' => '',
				'failure' => '',
			];
		}
		return [
			'success' => (string)$this->CI->config->item('mercadopago_back_url_success', 'mercadopago'),
			'pending' => (string)$this->CI->config->item('mercadopago_back_url_pending', 'mercadopago'),
			'failure' => (string)$this->CI->config->item('mercadopago_back_url_failure', 'mercadopago'),
		];
	}

	public function get_notification_url()
	{
		$base = function_exists('base_url') ? base_url() : '';
		return rtrim((string)$base, '/').'/webhooks/mercadopago';
	}

	public function build_preapproval($subscription, $tenant, $owner, $plano, $options=array())
	{
		$this->assert_ready();
		$preapproval = new MercadoPago\Preapproval();
		$preapproval->payer_email = $owner->email;
		$preapproval->back_url = isset($options['back_url']) && trim((string)$options['back_url']) !== '' ? trim((string)$options['back_url']) : $this->get_back_urls()['success'];
		$preapproval->reason = isset($options['reason']) && trim((string)$options['reason']) !== '' ? trim((string)$options['reason']) : $this->build_reason($tenant, $plano);
		$preapproval->external_reference = isset($options['external_reference']) && trim((string)$options['external_reference']) !== '' ? trim((string)$options['external_reference']) : 'saas-sub-'.$subscription->id;
		$preapproval->status = 'pending';
		$preapproval->auto_recurring = [
			'frequency' => max(1, (int)$subscription->billing_interval_count),
			'frequency_type' => $this->map_frequency_type($subscription->billing_cycle),
			'transaction_amount' => (float)$subscription->valor,
			'currency_id' => $this->get_currency_id(),
			'start_date' => $this->format_iso8601($subscription->current_period_start ? $subscription->current_period_start : date('Y-m-d H:i:s')),
		];
		return $preapproval;
	}

	public function create_preapproval($subscription, $tenant, $owner, $plano, $options=array())
	{
		$this->assert_ready();
		$preapproval = $this->build_preapproval($subscription, $tenant, $owner, $plano, $options);
		$result = $this->run_sdk_call(function() use ($preapproval) {
			return $preapproval->save();
		});
		if(!$result['ok'] || !$result['result']){
			throw new Exception($this->build_sdk_error_message($preapproval, $result['warnings']));
		}
		if((!isset($preapproval->id) || trim((string)$preapproval->id) === '') && (!isset($preapproval->init_point) || trim((string)$preapproval->init_point) === '')){
			throw new Exception($this->build_sdk_error_message($preapproval, $result['warnings']));
		}
		return $preapproval;
	}

	public function get_preapproval($gateway_subscription_id)
	{
		$this->assert_ready();
		$result = $this->run_sdk_call(function() use ($gateway_subscription_id) {
			return MercadoPago\Preapproval::find_by_id($gateway_subscription_id);
		});
		if(!$result['ok']){
			throw new Exception($this->build_sdk_error_message(null, $result['warnings']));
		}
		return $result['result'];
	}

	public function create_pix_payment($subscription, $tenant, $owner, $cycle, $plano, $options=array())
	{
		$this->assert_ready();
		$amount = isset($options['amount']) ? (float)$options['amount'] : (float)$cycle->amount_due;
		$payment = $this->api_request('POST', '/v1/payments', [
			'transaction_amount' => $amount,
			'description' => isset($options['description']) && trim((string)$options['description']) !== '' ? trim((string)$options['description']) : $this->build_cycle_reason($tenant, $plano, $cycle),
			'payment_method_id' => 'pix',
			'external_reference' => isset($options['external_reference']) && trim((string)$options['external_reference']) !== '' ? trim((string)$options['external_reference']) : $this->build_external_reference($subscription, $cycle, 'pix'),
			'notification_url' => $this->get_notification_url(),
			'payer' => $this->build_payer_payload($tenant, $owner),
		], [
			'X-Idempotency-Key: '.$this->generate_idempotency_key(),
		]);

		return $payment;
	}

	public function create_card_payment($subscription, $tenant, $owner, $cycle, $plano, $form_data=array())
	{
		$this->assert_ready();
		$amount = isset($form_data['transaction_amount']) ? (float)$form_data['transaction_amount'] : (float)$cycle->amount_due;
		$payer = $this->build_payer_payload($tenant, $owner, isset($form_data['payer']) && is_array($form_data['payer']) ? $form_data['payer'] : array());
		$payload = [
			'transaction_amount' => $amount,
			'token' => isset($form_data['token']) ? trim((string)$form_data['token']) : '',
			'description' => isset($form_data['description']) && trim((string)$form_data['description']) !== '' ? trim((string)$form_data['description']) : $this->build_cycle_reason($tenant, $plano, $cycle),
			'installments' => isset($form_data['installments']) ? max(1, (int)$form_data['installments']) : 1,
			'payment_method_id' => isset($form_data['payment_method_id']) ? trim((string)$form_data['payment_method_id']) : '',
			'issuer_id' => isset($form_data['issuer_id']) && trim((string)$form_data['issuer_id']) !== '' ? (int)$form_data['issuer_id'] : null,
			'external_reference' => isset($form_data['external_reference']) && trim((string)$form_data['external_reference']) !== '' ? trim((string)$form_data['external_reference']) : $this->build_external_reference($subscription, $cycle, 'card'),
			'notification_url' => $this->get_notification_url(),
			'payer' => $payer,
		];

		return $this->api_request('POST', '/v1/payments', $payload, [
			'X-Idempotency-Key: '.$this->generate_idempotency_key(),
		]);
	}

	public function get_payment($payment_id)
	{
		$this->assert_ready();
		$payment_id = trim((string)$payment_id);
		if($payment_id === ''){
			throw new Exception('Pagamento Mercado Pago nao informado.');
		}
		return $this->api_request('GET', '/v1/payments/'.$payment_id);
	}

	public function map_payment_status($gateway_status, $gateway_status_detail='')
	{
		$gateway_status = strtolower(trim((string)$gateway_status));
		$gateway_status_detail = strtolower(trim((string)$gateway_status_detail));
		switch($gateway_status){
			case 'approved':
			case 'authorized':
				return 'active';
			case 'cancelled':
			case 'cancelled_by_user':
				return 'canceled';
			case 'rejected':
				return 'past_due';
			case 'in_process':
			case 'pending':
				if(strpos($gateway_status_detail, 'waiting') !== false || strpos($gateway_status_detail, 'pending') !== false){
					return 'pending';
				}
				return 'pending';
			default:
				return $gateway_status !== '' ? $gateway_status : 'pending';
		}
	}

	public function map_gateway_status($gateway_status)
	{
		$gateway_status = strtolower(trim((string)$gateway_status));
		switch($gateway_status){
			case 'authorized':
				return 'active';
			case 'paused':
				return 'paused';
			case 'cancelled':
			case 'cancelled_by_user':
				return 'canceled';
			case 'pending':
				return 'pending';
			default:
				return $gateway_status !== '' ? $gateway_status : 'pending';
		}
	}

	protected function map_frequency_type($cycle)
	{
		switch(trim((string)$cycle)){
			case 'weekly':
				return 'days';
			case 'yearly':
				return 'months';
			case 'quarterly':
			case 'semiannual':
			case 'monthly':
			default:
				return 'months';
		}
	}

	protected function build_reason($tenant, $plano)
	{
		$tenant_nome = isset($tenant->tenant_nome) ? $tenant->tenant_nome : 'Tenant';
		$plano_nome = isset($plano->modelo) ? $plano->modelo : 'Plano';
		return 'Assinatura '.$plano_nome.' - '.$tenant_nome;
	}

	protected function build_cycle_reason($tenant, $plano, $cycle)
	{
		$base = $this->build_reason($tenant, $plano);
		$reference = isset($cycle->reference_label) && trim((string)$cycle->reference_label) !== '' ? trim((string)$cycle->reference_label) : 'ciclo';
		return $base.' - '.$reference;
	}

	protected function build_external_reference($subscription, $cycle, $method)
	{
		return 'saas-sub-'.(int)$subscription->id.'-cycle-'.(int)$cycle->id.'-'.trim((string)$method);
	}

	protected function build_payer_payload($tenant, $owner, $payer_override=array())
	{
		$email = isset($payer_override['email']) && trim((string)$payer_override['email']) !== '' ? trim((string)$payer_override['email']) : (isset($owner->email) ? trim((string)$owner->email) : '');
		$first_name = isset($payer_override['first_name']) && trim((string)$payer_override['first_name']) !== '' ? trim((string)$payer_override['first_name']) : '';
		$last_name = isset($payer_override['last_name']) && trim((string)$payer_override['last_name']) !== '' ? trim((string)$payer_override['last_name']) : '';
		$identification = isset($payer_override['identification']) && is_array($payer_override['identification']) ? $payer_override['identification'] : array();
		$document_raw = '';
		if(isset($identification['number']) && trim((string)$identification['number']) !== ''){
			$document_raw = trim((string)$identification['number']);
		}elseif(isset($tenant->documento) && trim((string)$tenant->documento) !== ''){
			$document_raw = trim((string)$tenant->documento);
		}elseif(isset($owner->cpf) && trim((string)$owner->cpf) !== ''){
			$document_raw = trim((string)$owner->cpf);
		}

		$identification_type = isset($identification['type']) && trim((string)$identification['type']) !== '' ? strtoupper(trim((string)$identification['type'])) : $this->infer_document_type($document_raw);
		$document_number = preg_replace('/\D+/', '', $document_raw);
		if($first_name === '' && isset($owner->nome)){
			$parts = preg_split('/\s+/', trim((string)$owner->nome));
			$first_name = isset($parts[0]) ? $parts[0] : '';
			if(count($parts) > 1){
				unset($parts[0]);
				$last_name = trim(implode(' ', $parts));
			}
		}

		$payer = [
			'email' => $email,
		];
		if($first_name !== ''){
			$payer['first_name'] = $first_name;
		}
		if($last_name !== ''){
			$payer['last_name'] = $last_name;
		}
		if($identification_type !== '' && $document_number !== ''){
			$payer['identification'] = [
				'type' => $identification_type,
				'number' => $document_number,
			];
		}

		return $payer;
	}

	protected function infer_document_type($document)
	{
		$digits = preg_replace('/\D+/', '', (string)$document);
		if(strlen($digits) === 14){
			return 'CNPJ';
		}
		if(strlen($digits) === 11){
			return 'CPF';
		}
		return '';
	}

	protected function generate_idempotency_key()
	{
		if(function_exists('random_bytes')){
			return bin2hex(random_bytes(16));
		}
		return md5(uniqid((string)mt_rand(), true));
	}

	protected function api_request($method, $path, $payload=null, $extra_headers=array())
	{
		$this->assert_ready();
		$token = $this->get_access_token();
		if(trim($token) === ''){
			throw new Exception('Access token do Mercado Pago nao configurado.');
		}
		$url = 'https://api.mercadopago.com'.trim((string)$path);
		$headers = [
			'Accept: application/json',
			'Authorization: Bearer '.$token,
		];
		if($payload !== null){
			$headers[] = 'Content-Type: application/json';
		}
		foreach($extra_headers as $header){
			if(trim((string)$header) !== ''){
				$headers[] = trim((string)$header);
			}
		}

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper((string)$method));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, 45);
		if($payload !== null){
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		}

		$response = curl_exec($curl);
		$curl_error = curl_error($curl);
		$http_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if($response === false){
			throw new Exception('Falha de comunicacao com o Mercado Pago: '.$curl_error);
		}

		$data = json_decode($response, true);
		if($http_code < 200 || $http_code >= 300){
			throw new Exception($this->build_api_error_message($data, $response, $http_code));
		}

		if(!is_array($data)){
			throw new Exception('Resposta invalida do Mercado Pago.');
		}

		return $data;
	}

	protected function build_api_error_message($data, $raw_response, $http_code)
	{
		$parts = [];
		if(is_array($data)){
			foreach(['message', 'error', 'cause', 'status'] as $field){
				if(isset($data[$field]) && !is_array($data[$field]) && trim((string)$data[$field]) !== ''){
					$parts[] = trim((string)$data[$field]);
				}
			}
			if(isset($data['cause']) && is_array($data['cause'])){
				foreach($data['cause'] as $cause){
					if(is_array($cause) && isset($cause['description']) && trim((string)$cause['description']) !== ''){
						$parts[] = trim((string)$cause['description']);
						break;
					}
				}
			}
		}
		if(!count($parts) && trim((string)$raw_response) !== ''){
			$parts[] = trim((string)$raw_response);
		}
		$parts = array_values(array_unique(array_filter($parts)));
		return 'Mercado Pago respondeu com erro HTTP '.$http_code.': '.(count($parts) ? implode(' | ', $parts) : 'erro nao detalhado.');
	}

	protected function format_iso8601($datetime)
	{
		$ts = strtotime($datetime);
		if(!$ts){
			$ts = time();
		}
		return date('c', $ts);
	}

	protected function run_sdk_call($callback)
	{
		$warnings = [];
		$handler = function($severity, $message, $file, $line) use (&$warnings) {
			if(stripos((string)$file, 'mercadopago') !== false){
				$warnings[] = trim((string)$message);
				return true;
			}
			return false;
		};

		$previous = set_error_handler($handler);
		try {
			$result = call_user_func($callback);
			if($previous !== null){
				restore_error_handler();
			}else{
				restore_error_handler();
			}
			return ['ok' => true, 'result' => $result, 'warnings' => $warnings];
		} catch (Exception $e) {
			if($previous !== null){
				restore_error_handler();
			}else{
				restore_error_handler();
			}
			$warnings[] = $e->getMessage();
			return ['ok' => false, 'result' => null, 'warnings' => $warnings];
		}
	}

	protected function build_sdk_error_message($entity=null, $warnings=array())
	{
		$parts = [];
		if($entity && method_exists($entity, 'Error')){
			$error = $entity->Error();
			if($error){
				if(isset($error->error) && trim((string)$error->error) !== ''){
					$parts[] = trim((string)$error->error);
				}
				if(isset($error->message) && trim((string)$error->message) !== ''){
					$parts[] = trim((string)$error->message);
				}
				if(isset($error->status) && trim((string)$error->status) !== ''){
					$parts[] = 'status '.$error->status;
				}
				if(isset($error->causes) && is_array($error->causes)){
					foreach($error->causes as $cause){
						if(isset($cause->description) && trim((string)$cause->description) !== ''){
							$parts[] = trim((string)$cause->description);
							break;
						}
					}
				}
			}
		}

		if(count($warnings)){
			foreach($warnings as $warning){
				$warning = trim((string)$warning);
				if($warning !== ''){
					$parts[] = $warning;
				}
			}
		}

		$parts = array_values(array_unique(array_filter($parts)));
		if(count($parts)){
			return 'Mercado Pago nao conseguiu criar a assinatura: '.implode(' | ', $parts);
		}

		return 'Mercado Pago nao conseguiu criar a assinatura. Revise token, e-mail do pagador, plano e URLs de retorno.';
	}
}
