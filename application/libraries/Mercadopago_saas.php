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
