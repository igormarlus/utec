<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saas_model extends CI_Model {

	function has_schema(){
		return $this->db->table_exists('saas_tenants')
			&& $this->db->table_exists('saas_subscriptions')
			&& $this->db->field_exists('tenant_id', 'usuarios');
	}

	function get_dashboard_data($viewer){
		$data = [
			'resumo' => [
				'tenants' => 0,
				'tenants_ativos' => 0,
				'assinaturas' => 0,
				'mrr' => 0.00,
			],
			'tenants' => [],
			'assinaturas' => [],
			'planos' => [],
			'usuarios_base' => [],
		];

		if(!$this->has_schema()){
			return $data;
		}

		$scope_tenant_where = $this->build_tenant_where_sql($viewer);
		$scope_subscription_where = $this->build_subscription_where_sql($viewer);
		$tenants_ativos_where = $scope_tenant_where ? $scope_tenant_where." AND t.status = 1" : " WHERE t.status = 1";
		$mrr_where = $scope_subscription_where ? $scope_subscription_where." AND s.status IN ('active','trial','past_due')" : " WHERE s.status IN ('active','trial','past_due')";

		$data['resumo']['tenants'] = (int)$this->db->query(
			"SELECT COUNT(t.id) AS total FROM saas_tenants t ".$scope_tenant_where
		)->row()->total;
		$data['resumo']['tenants_ativos'] = (int)$this->db->query(
			"SELECT COUNT(t.id) AS total FROM saas_tenants t ".$tenants_ativos_where
		)->row()->total;
		$data['resumo']['assinaturas'] = (int)$this->db->query(
			"SELECT COUNT(s.id) AS total FROM saas_subscriptions s ".$scope_subscription_where
		)->row()->total;
		$data['resumo']['mrr'] = (float)$this->db->query(
			"SELECT COALESCE(SUM(s.valor),0) AS total FROM saas_subscriptions s ".$mrr_where
		)->row()->total;

		$tenant_query = "SELECT t.*, owner.nome AS owner_nome, owner.email AS owner_email, owner.telefone AS owner_telefone
			FROM saas_tenants t
			LEFT JOIN usuarios owner ON owner.id = t.id_owner_user
			".$scope_tenant_where."
			ORDER BY t.id DESC LIMIT 100";
		$data['tenants'] = $this->db->query($tenant_query);

		$subscription_query = "SELECT s.*, t.tenant_nome, p.modelo AS plano_nome, u.nome AS responsavel_nome
			FROM saas_subscriptions s
			LEFT JOIN saas_tenants t ON t.id = s.tenant_id
			LEFT JOIN produtos p ON p.id = s.plano_id
			LEFT JOIN usuarios u ON u.id = s.id_cliente
			".$scope_subscription_where."
			ORDER BY s.id DESC LIMIT 100";
		$data['assinaturas'] = $this->db->query($subscription_query);

		$data['planos'] = $this->db->query("SELECT id, modelo, preco_venda, billing_interval, billing_interval_count, trial_days FROM produtos WHERE status = 1 ORDER BY modelo ASC");

		if((int)$viewer->nivel === 1){
			$data['usuarios_base'] = $this->db->query("SELECT id, nome, nivel, email, telefone, tenant_id FROM usuarios WHERE nivel IN (2,3) ORDER BY nome ASC LIMIT 200");
		}else{
			$scope_ids = $this->padrao_model->get_scope_user_ids($viewer);
			$scope_sql = $this->padrao_model->ids_to_sql_in($scope_ids);
			$data['usuarios_base'] = $this->db->query("SELECT id, nome, nivel, email, telefone, tenant_id FROM usuarios WHERE nivel IN (2,3) AND id IN (".$scope_sql.") ORDER BY nome ASC LIMIT 200");
		}

		return $data;
	}

	function get_public_plans(){
		if(!$this->db->table_exists('produtos')){
			return [];
		}
		$select = ['id', 'modelo', 'preco_venda', 'especificacoes'];
		if($this->db->field_exists('billing_interval', 'produtos')){
			$select[] = 'billing_interval';
		}
		if($this->db->field_exists('billing_interval_count', 'produtos')){
			$select[] = 'billing_interval_count';
		}
		if($this->db->field_exists('trial_days', 'produtos')){
			$select[] = 'trial_days';
		}
		if($this->db->field_exists('setup_fee', 'produtos')){
			$select[] = 'setup_fee';
		}
		if($this->db->field_exists('max_profissionais', 'produtos')){
			$select[] = 'max_profissionais';
		}
		if($this->db->field_exists('max_colaboradores', 'produtos')){
			$select[] = 'max_colaboradores';
		}
		if($this->db->field_exists('max_pacientes', 'produtos')){
			$select[] = 'max_pacientes';
		}
		$where = ["status = 1"];
		if($this->db->field_exists('saas_publicado', 'produtos')){
			$where[] = "saas_publicado = 1";
		}
		$qr = $this->db->query(
			"SELECT ".implode(', ', $select)."
			FROM produtos
			WHERE ".implode(" AND ", $where)."
			ORDER BY preco_venda ASC, id ASC"
		);
		return $qr->result();
	}

	function get_tenant_detail($tenant_id, $viewer){
		if(!$this->has_schema()){
			return null;
		}
		$tenant_id = (int)$tenant_id;
		if($tenant_id <= 0){
			return null;
		}

		$scope_where = $this->build_tenant_where_sql($viewer);
		$sql = "SELECT t.*, owner.nome AS owner_nome, owner.email AS owner_email, owner.telefone AS owner_telefone
			FROM saas_tenants t
			LEFT JOIN usuarios owner ON owner.id = t.id_owner_user
			WHERE t.id = ".$tenant_id;
		if((int)$viewer->nivel !== 1){
			if(isset($viewer->tenant_id) && (int)$viewer->tenant_id > 0){
				$sql .= " AND t.id = ".(int)$viewer->tenant_id;
			}else{
				$sql .= " AND t.id_owner_user = ".(int)$viewer->id;
			}
		}
		$tenant = $this->db->query($sql)->row();
		if(!$tenant){
			return null;
		}

		$assinaturas = $this->db->query(
			"SELECT s.*, p.modelo AS plano_nome
			FROM saas_subscriptions s
			LEFT JOIN produtos p ON p.id = s.plano_id
			WHERE s.tenant_id = ".$tenant_id."
			ORDER BY s.id DESC"
		);
		$ciclos = $this->db->query(
			"SELECT * FROM saas_subscription_cycles WHERE tenant_id = ".$tenant_id." ORDER BY id DESC LIMIT 50"
		);
		$usuarios = $this->db->query(
			"SELECT id, nome, nivel, email, telefone, tenant_role, onboarding_status
			FROM usuarios WHERE tenant_id = ".$tenant_id."
			ORDER BY nivel ASC, nome ASC"
		);

		return [
			'tenant' => $tenant,
			'assinaturas' => $assinaturas,
			'ciclos' => $ciclos,
			'usuarios' => $usuarios,
		];
	}

	function get_subscription_detail($subscription_id, $viewer){
		if(!$this->has_schema()){
			return null;
		}
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return null;
		}

		$sql = "SELECT s.*, t.tenant_nome, t.id_owner_user, t.status AS tenant_status, p.modelo AS plano_nome
			FROM saas_subscriptions s
			LEFT JOIN saas_tenants t ON t.id = s.tenant_id
			LEFT JOIN produtos p ON p.id = s.plano_id
			WHERE s.id = ".$subscription_id." LIMIT 1";
		$subscription = $this->db->query($sql)->row();
		if(!$subscription){
			return null;
		}

		if((int)$viewer->nivel !== 1){
			if(isset($viewer->tenant_id) && (int)$viewer->tenant_id > 0 && (int)$viewer->tenant_id !== (int)$subscription->tenant_id){
				return null;
			}
			if((!isset($viewer->tenant_id) || (int)$viewer->tenant_id <= 0) && (int)$viewer->id !== (int)$subscription->id_cliente){
				return null;
			}
		}

		$tenant = $this->db->query("SELECT * FROM saas_tenants WHERE id = ".(int)$subscription->tenant_id." LIMIT 1")->row();
		$owner = $this->db->query("SELECT * FROM usuarios WHERE id = ".(int)$subscription->id_cliente." LIMIT 1")->row();
		$plano = $this->db->query("SELECT * FROM produtos WHERE id = ".(int)$subscription->plano_id." LIMIT 1")->row();

		return [
			'subscription' => $subscription,
			'tenant' => $tenant,
			'owner' => $owner,
			'plano' => $plano,
		];
	}

	function get_subscription_detail_system($subscription_id){
		if(!$this->has_schema()){
			return null;
		}
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return null;
		}
		$subscription = $this->db->query("SELECT * FROM saas_subscriptions WHERE id = ".$subscription_id." LIMIT 1")->row();
		if(!$subscription){
			return null;
		}
		$tenant = $this->db->query("SELECT * FROM saas_tenants WHERE id = ".(int)$subscription->tenant_id." LIMIT 1")->row();
		$owner = $this->db->query("SELECT * FROM usuarios WHERE id = ".(int)$subscription->id_cliente." LIMIT 1")->row();
		$plano = $this->db->query("SELECT * FROM produtos WHERE id = ".(int)$subscription->plano_id." LIMIT 1")->row();
		return [
			'subscription' => $subscription,
			'tenant' => $tenant,
			'owner' => $owner,
			'plano' => $plano,
		];
	}

	function provision_tenant($data, $viewer){
		if(!$this->has_schema()){
			return ['ok' => false, 'msg' => 'Execute a migracao da fase 1 SaaS antes de provisionar uma clinica.'];
		}

		$owner_id = (int)$data['owner_user_id'];
		$plano_id = (int)$data['plano_id'];
		if($owner_id <= 0 || $plano_id <= 0){
			return ['ok' => false, 'msg' => 'Selecione um responsavel e um plano.'];
		}

		$owner = $this->db->query("SELECT * FROM usuarios WHERE id = ".$owner_id." LIMIT 1")->row();
		if(!$owner){
			return ['ok' => false, 'msg' => 'Usuario responsavel nao encontrado.'];
		}
		if((int)$viewer->nivel !== 1 && !$this->padrao_model->can_access_usuario($owner_id, $viewer)){
			return ['ok' => false, 'msg' => 'Voce nao pode provisionar este usuario.'];
		}

		$plano = $this->db->query("SELECT * FROM produtos WHERE id = ".$plano_id." LIMIT 1")->row();
		if(!$plano){
			return ['ok' => false, 'msg' => 'Plano nao encontrado.'];
		}

		$tenant_nome = trim($data['tenant_nome']) !== '' ? trim($data['tenant_nome']) : $owner->nome;
		$tenant_slug = $this->build_slug($tenant_nome);
		$billing_cycle = trim($data['billing_cycle']) !== '' ? trim($data['billing_cycle']) : $this->normalize_billing_cycle($plano);
		$interval_count = max(1, (int)$data['billing_interval_count']);
		$trial_days = max(0, (int)$data['trial_days']);
		$valor = $this->normalize_money($data['valor']);
		if($valor <= 0){
			$valor = (float)$plano->preco_venda;
		}
		$setup_fee = $this->normalize_money($data['setup_fee']);
		$agora = date('Y-m-d H:i:s');
		$trial_ends_at = $trial_days > 0 ? date('Y-m-d H:i:s', strtotime('+'.$trial_days.' days')) : null;
		$period_end = $this->calculate_period_end($agora, $billing_cycle, $interval_count);

		$this->db->trans_begin();

		$this->db->insert('saas_tenants', [
			'id_responsavel' => (int)$viewer->id,
			'id_owner_user' => $owner_id,
			'tenant_nome' => $tenant_nome,
			'slug' => $tenant_slug,
			'tenant_tipo' => trim($data['tenant_tipo']) !== '' ? trim($data['tenant_tipo']) : 'clinica',
			'documento' => trim($data['documento']),
			'contato_nome' => trim($data['contato_nome']) !== '' ? trim($data['contato_nome']) : $owner->nome,
			'contato_email' => trim($data['contato_email']) !== '' ? trim($data['contato_email']) : $owner->email,
			'contato_telefone' => trim($data['contato_telefone']) !== '' ? trim($data['contato_telefone']) : $owner->telefone,
			'status' => (int)$data['status'],
			'trial_ends_at' => $trial_ends_at,
			'activated_at' => $agora,
			'expires_at' => $period_end,
			'observacoes' => trim($data['observacoes']),
			'updated_at' => $agora,
		]);
		$tenant_id = (int)$this->db->insert_id();

		$subscription_status = $trial_days > 0 ? 'trial' : 'active';
		$this->db->insert('saas_subscriptions', [
			'tenant_id' => $tenant_id,
			'plano_id' => $plano_id,
			'id_cliente' => $owner_id,
			'billing_cycle' => $billing_cycle,
			'billing_interval_count' => $interval_count,
			'status' => $subscription_status,
			'valor' => $valor,
			'setup_fee' => $setup_fee,
			'trial_ends_at' => $trial_ends_at,
			'started_at' => $agora,
			'current_period_start' => $agora,
			'current_period_end' => $period_end,
			'next_billing_at' => $period_end,
			'gateway' => trim($data['gateway']),
			'gateway_reference' => trim($data['gateway_reference']),
			'observacoes' => trim($data['observacoes']),
			'updated_at' => $agora,
		]);
		$subscription_id = (int)$this->db->insert_id();

		$this->db->insert('saas_subscription_cycles', [
			'subscription_id' => $subscription_id,
			'tenant_id' => $tenant_id,
			'cycle_number' => 1,
			'reference_label' => date('m/Y'),
			'period_start' => $agora,
			'period_end' => $period_end,
			'due_at' => $period_end,
			'status' => $trial_days > 0 ? 'trial' : 'pending',
			'amount_due' => $valor + $setup_fee,
			'amount_paid' => 0,
			'created_at' => $agora,
			'updated_at' => $agora,
		]);

		$this->db->insert('saas_billing_events', [
			'subscription_id' => $subscription_id,
			'tenant_id' => $tenant_id,
			'event_type' => 'tenant_provisioned',
			'gateway' => trim($data['gateway']),
			'gateway_reference' => trim($data['gateway_reference']),
			'status' => $subscription_status,
			'amount' => $valor,
			'payload_text' => 'Tenant provisionado manualmente pelo admin em '.date('d/m/Y H:i'),
		]);

		$this->sync_tenant_to_tree($tenant_id, $owner_id);

		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			return ['ok' => false, 'msg' => 'Nao foi possivel provisionar a clinica.'];
		}

		$this->db->trans_commit();
		return ['ok' => true, 'msg' => 'Clinica provisionada com sucesso.', 'tenant_id' => $tenant_id];
	}

	function create_public_tenant_signup($data){
		if(!$this->has_schema()){
			return ['ok' => false, 'msg' => 'A estrutura SaaS ainda nao foi liberada neste ambiente.'];
		}

		$nome_responsavel = trim((string)$data['nome_responsavel']);
		$tenant_nome = trim((string)$data['tenant_nome']);
		$email = strtolower(trim((string)$data['email']));
		$telefone = trim((string)$data['telefone']);
		$documento = trim((string)$data['documento']);
		$plano_id = (int)$data['plano_id'];
		$senha = (string)$data['senha'];
		$observacoes = trim((string)$data['observacoes']);

		if($nome_responsavel === '' || $tenant_nome === '' || $email === '' || $plano_id <= 0 || $senha === ''){
			return ['ok' => false, 'msg' => 'Preencha nome do responsavel, nome da clinica, e-mail, senha e plano.'];
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return ['ok' => false, 'msg' => 'Informe um e-mail valido para continuar.'];
		}
		if(strlen($senha) < 6){
			return ['ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres.'];
		}

		$plano_where = "id = ".$plano_id." AND status = 1";
		if($this->db->field_exists('saas_publicado', 'produtos')){
			$plano_where .= " AND saas_publicado = 1";
		}
		$plano = $this->db->query("SELECT * FROM produtos WHERE ".$plano_where." LIMIT 1")->row();
		if(!$plano){
			return ['ok' => false, 'msg' => 'O plano selecionado nao esta disponivel para contratacao publica.'];
		}

		$qr_email = $this->db->query("SELECT id FROM usuarios WHERE email = ".$this->db->escape($email)." OR login = ".$this->db->escape($email)." LIMIT 1");
		if($qr_email->num_rows()){
			return ['ok' => false, 'msg' => 'Ja existe um usuario com este e-mail. Use outro e-mail ou recupere seu acesso.'];
		}

		$agora = date('Y-m-d H:i:s');
		$billing_cycle = isset($plano->billing_interval) && trim((string)$plano->billing_interval) !== '' ? trim((string)$plano->billing_interval) : 'monthly';
		$interval_count = isset($plano->billing_interval_count) ? max(1, (int)$plano->billing_interval_count) : 1;
		$trial_days = isset($plano->trial_days) ? max(0, (int)$plano->trial_days) : 0;
		$valor = isset($plano->preco_venda) ? (float)$plano->preco_venda : 0.00;
		$setup_fee = isset($plano->setup_fee) ? (float)$plano->setup_fee : 0.00;
		$trial_ends_at = $trial_days > 0 ? date('Y-m-d H:i:s', strtotime('+'.$trial_days.' days')) : null;
		$period_end = $this->calculate_period_end($agora, $billing_cycle, $interval_count);
		$tenant_slug = $this->build_unique_tenant_slug($tenant_nome);

		$this->db->trans_begin();

		$user_insert = [
			'id_user' => 0,
			'nome' => $nome_responsavel,
			'nivel' => 2,
			'telefone' => $telefone,
			'cpf' => $documento,
			'email' => $email,
			'login' => $email,
			'senha' => password_hash($senha, PASSWORD_DEFAULT),
			'status' => 1,
		];
		if($this->db->field_exists('dt_cadastro', 'usuarios')){
			$user_insert['dt_cadastro'] = $agora;
		}
		if($this->db->field_exists('onboarding_status', 'usuarios')){
			$user_insert['onboarding_status'] = 'ativo';
		}
		$this->db->insert('usuarios', $user_insert);
		$owner_id = (int)$this->db->insert_id();

		$this->db->insert('saas_tenants', [
			'id_responsavel' => $owner_id,
			'id_owner_user' => $owner_id,
			'tenant_nome' => $tenant_nome,
			'slug' => $tenant_slug,
			'tenant_tipo' => !empty($data['tenant_tipo']) ? trim((string)$data['tenant_tipo']) : 'clinica',
			'documento' => $documento,
			'contato_nome' => $nome_responsavel,
			'contato_email' => $email,
			'contato_telefone' => $telefone,
			'status' => 1,
			'trial_ends_at' => $trial_ends_at,
			'activated_at' => $agora,
			'expires_at' => $period_end,
			'observacoes' => ($observacoes !== '' ? $observacoes.' | ' : '').'Cadastro publico iniciado em '.date('d/m/Y H:i'),
			'updated_at' => $agora,
		]);
		$tenant_id = (int)$this->db->insert_id();

		$subscription_status = $trial_days > 0 ? 'trial' : 'pending';
		$this->db->insert('saas_subscriptions', [
			'tenant_id' => $tenant_id,
			'plano_id' => $plano_id,
			'id_cliente' => $owner_id,
			'billing_cycle' => $billing_cycle,
			'billing_interval_count' => $interval_count,
			'status' => $subscription_status,
			'valor' => $valor,
			'setup_fee' => $setup_fee,
			'trial_ends_at' => $trial_ends_at,
			'started_at' => $agora,
			'current_period_start' => $agora,
			'current_period_end' => $period_end,
			'next_billing_at' => $period_end,
			'gateway' => 'mercadopago',
			'observacoes' => 'Assinatura criada via onboarding publico',
			'updated_at' => $agora,
		]);
		$subscription_id = (int)$this->db->insert_id();

		$this->db->insert('saas_subscription_cycles', [
			'subscription_id' => $subscription_id,
			'tenant_id' => $tenant_id,
			'cycle_number' => 1,
			'reference_label' => date('m/Y'),
			'period_start' => $agora,
			'period_end' => $period_end,
			'due_at' => $period_end,
			'status' => $trial_days > 0 ? 'trial' : 'pending',
			'amount_due' => $valor + $setup_fee,
			'amount_paid' => 0,
			'created_at' => $agora,
			'updated_at' => $agora,
		]);

		$this->db->insert('saas_billing_events', [
			'subscription_id' => $subscription_id,
			'tenant_id' => $tenant_id,
			'event_type' => 'public_signup_created',
			'gateway' => 'mercadopago',
			'gateway_reference' => '',
			'status' => $subscription_status,
			'amount' => $valor,
			'payload_text' => 'Onboarding publico iniciado por '.$email,
		]);

		$user_update = [];
		if($this->db->field_exists('tenant_id', 'usuarios')){
			$user_update['tenant_id'] = $tenant_id;
		}
		if($this->db->field_exists('tenant_role', 'usuarios')){
			$user_update['tenant_role'] = 'owner';
		}
		if($this->db->field_exists('billing_customer_id', 'usuarios')){
			$user_update['billing_customer_id'] = $email;
		}
		if(count($user_update)){
			$this->db->where('id', $owner_id);
			$this->db->update('usuarios', $user_update);
		}

		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			return ['ok' => false, 'msg' => 'Nao foi possivel iniciar a contratacao agora.'];
		}

		$this->db->trans_commit();
		return [
			'ok' => true,
			'tenant_id' => $tenant_id,
			'subscription_id' => $subscription_id,
			'user_id' => $owner_id,
			'login' => $email,
			'tenant_nome' => $tenant_nome,
		];
	}

	function save_checkout_data($subscription_id, $payload){
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return false;
		}

		$update = [
			'gateway' => 'mercadopago',
			'gateway_subscription_id' => isset($payload['gateway_subscription_id']) ? $payload['gateway_subscription_id'] : null,
			'gateway_reference' => isset($payload['gateway_reference']) ? $payload['gateway_reference'] : null,
			'checkout_url' => isset($payload['checkout_url']) ? $payload['checkout_url'] : null,
			'checkout_type' => isset($payload['checkout_type']) ? $payload['checkout_type'] : 'preapproval',
			'status' => isset($payload['status']) ? $payload['status'] : 'pending',
			'gateway_status_detail' => isset($payload['gateway_status_detail']) ? $payload['gateway_status_detail'] : null,
			'updated_at' => date('Y-m-d H:i:s'),
		];

		$this->db->where('id', $subscription_id);
		return $this->db->update('saas_subscriptions', $update);
	}

	function sync_subscription_gateway_status($gateway_subscription_id, $gateway_status, $gateway_detail='', $raw_payload=''){
		if(!$this->has_schema()){
			return false;
		}
		$gateway_subscription_id = trim((string)$gateway_subscription_id);
		if($gateway_subscription_id === ''){
			return false;
		}

		$this->db->where('gateway_subscription_id', $gateway_subscription_id);
		$qr = $this->db->get('saas_subscriptions');
		if(!$qr->num_rows()){
			return false;
		}
		$subscription = $qr->row();
		$local_status = $this->map_subscription_status($gateway_status);
		$agora = date('Y-m-d H:i:s');

		$this->db->where('id', (int)$subscription->id);
		$this->db->update('saas_subscriptions', [
			'status' => $local_status,
			'gateway_status_detail' => $gateway_detail,
			'webhook_last_event_at' => $agora,
			'updated_at' => $agora,
		]);

		$this->db->insert('saas_billing_events', [
			'subscription_id' => (int)$subscription->id,
			'tenant_id' => (int)$subscription->tenant_id,
			'event_type' => 'mercadopago_webhook',
			'gateway' => 'mercadopago',
			'gateway_reference' => $gateway_subscription_id,
			'status' => $gateway_status,
			'amount' => (float)$subscription->valor,
			'payload_text' => $raw_payload !== '' ? $raw_payload : 'Webhook Mercado Pago',
		]);

		$this->refresh_subscription_health((int)$subscription->id);
		return true;
	}

	function sync_subscription_with_gateway($subscription_id, $gateway_status, $gateway_detail='', $raw_payload=''){
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return false;
		}
		$qr = $this->db->query("SELECT * FROM saas_subscriptions WHERE id = ".$subscription_id." LIMIT 1");
		if(!$qr->num_rows()){
			return false;
		}
		$subscription = $qr->row();
		return $this->sync_subscription_gateway_status(
			(string)$subscription->gateway_subscription_id,
			$gateway_status,
			$gateway_detail,
			$raw_payload
		);
	}

	function register_cycle_payment($cycle_id, $payment_method='manual', $amount_paid=null, $notes=''){
		$cycle_id = (int)$cycle_id;
		if($cycle_id <= 0){
			return ['ok' => false, 'msg' => 'Ciclo invalido.'];
		}
		$cycle = $this->db->query("SELECT * FROM saas_subscription_cycles WHERE id = ".$cycle_id." LIMIT 1")->row();
		if(!$cycle){
			return ['ok' => false, 'msg' => 'Ciclo nao encontrado.'];
		}
		$subscription = $this->db->query("SELECT * FROM saas_subscriptions WHERE id = ".(int)$cycle->subscription_id." LIMIT 1")->row();
		if(!$subscription){
			return ['ok' => false, 'msg' => 'Assinatura nao encontrada para este ciclo.'];
		}

		$agora = date('Y-m-d H:i:s');
		$amount_paid = $amount_paid === null ? (float)$cycle->amount_due : (float)$amount_paid;

		$this->db->trans_begin();

		$this->db->where('id', $cycle_id);
		$this->db->update('saas_subscription_cycles', [
			'status' => 'paid',
			'paid_at' => $agora,
			'amount_paid' => $amount_paid,
			'payment_method' => $payment_method,
			'updated_at' => $agora,
		]);

		$next_start = $cycle->period_end ? $cycle->period_end : $agora;
		$next_end = $this->calculate_period_end($next_start, $subscription->billing_cycle, $subscription->billing_interval_count);

		$this->db->where('id', (int)$subscription->id);
		$this->db->update('saas_subscriptions', [
			'status' => 'active',
			'current_period_start' => $next_start,
			'current_period_end' => $next_end,
			'next_billing_at' => $next_end,
			'updated_at' => $agora,
		]);

		$this->ensure_open_cycle($subscription->id, $subscription->tenant_id, $next_start, $next_end, (float)$subscription->valor);
		$this->update_tenant_operational_status((int)$subscription->tenant_id);

		$this->db->insert('saas_billing_events', [
			'subscription_id' => (int)$subscription->id,
			'tenant_id' => (int)$subscription->tenant_id,
			'cycle_id' => $cycle_id,
			'event_type' => 'manual_cycle_payment',
			'gateway' => $payment_method,
			'gateway_reference' => '',
			'status' => 'paid',
			'amount' => $amount_paid,
			'payload_text' => $notes !== '' ? $notes : 'Pagamento manual registrado via painel SaaS',
		]);

		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			return ['ok' => false, 'msg' => 'Nao foi possivel registrar o pagamento do ciclo.'];
		}

		$this->db->trans_commit();
		return ['ok' => true, 'msg' => 'Pagamento do ciclo registrado com sucesso.'];
	}

	function refresh_subscription_health($subscription_id){
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return false;
		}
		$subscription = $this->db->query("SELECT * FROM saas_subscriptions WHERE id = ".$subscription_id." LIMIT 1")->row();
		if(!$subscription){
			return false;
		}

		$agora = date('Y-m-d H:i:s');
		$new_status = $subscription->status;

		if(in_array($subscription->status, ['canceled', 'paused'])){
			$this->update_tenant_operational_status((int)$subscription->tenant_id);
			return true;
		}

		if($subscription->trial_ends_at && $subscription->status === 'trial' && strtotime($subscription->trial_ends_at) < strtotime($agora)){
			$new_status = 'pending';
		}

		$open_cycle = $this->get_open_cycle((int)$subscription->id);
		if($open_cycle && strtotime($open_cycle->due_at) < strtotime($agora) && !in_array($open_cycle->status, ['paid', 'canceled'])){
			$new_status = 'past_due';
			$this->db->where('id', (int)$open_cycle->id);
			$this->db->update('saas_subscription_cycles', [
				'status' => 'past_due',
				'updated_at' => $agora,
			]);
		}

		$this->db->where('id', (int)$subscription->id);
		$this->db->update('saas_subscriptions', [
			'status' => $new_status,
			'updated_at' => $agora,
		]);

		$this->update_tenant_operational_status((int)$subscription->tenant_id);
		return true;
	}

	function refresh_all_subscription_health(){
		if(!$this->has_schema()){
			return ['subscriptions' => 0];
		}
		$qr = $this->db->query("SELECT id FROM saas_subscriptions ORDER BY id ASC");
		$total = 0;
		foreach($qr->result() as $row){
			$this->refresh_subscription_health((int)$row->id);
			$total++;
		}
		return ['subscriptions' => $total];
	}

	function update_tenant_operational_status($tenant_id){
		$tenant_id = (int)$tenant_id;
		if($tenant_id <= 0){
			return false;
		}
		$subscription = $this->db->query("SELECT * FROM saas_subscriptions WHERE tenant_id = ".$tenant_id." ORDER BY id DESC LIMIT 1")->row();
		if(!$subscription){
			return false;
		}

		$allow = in_array($subscription->status, ['active', 'trial', 'pending']);
		$agora = date('Y-m-d H:i:s');
		$tenant_update = [
			'status' => $allow ? 1 : 0,
			'updated_at' => $agora,
		];
		if(!$allow){
			$tenant_update['suspended_at'] = $agora;
		}

		$this->db->where('id', $tenant_id);
		$this->db->update('saas_tenants', $tenant_update);

		if($this->db->field_exists('onboarding_status', 'usuarios')){
			$this->db->where('tenant_id', $tenant_id);
			$this->db->update('usuarios', [
				'onboarding_status' => $allow ? 'ativo' : 'bloqueado',
			]);
		}

		return true;
	}

	function get_open_cycle($subscription_id){
		$subscription_id = (int)$subscription_id;
		if($subscription_id <= 0){
			return null;
		}
		$qr = $this->db->query(
			"SELECT * FROM saas_subscription_cycles
			WHERE subscription_id = ".$subscription_id."
			AND status IN ('pending','trial','past_due')
			ORDER BY id ASC LIMIT 1"
		);
		return $qr->num_rows() ? $qr->row() : null;
	}

	function ensure_open_cycle($subscription_id, $tenant_id, $period_start, $period_end, $amount_due){
		$subscription_id = (int)$subscription_id;
		$tenant_id = (int)$tenant_id;
		if($subscription_id <= 0 || $tenant_id <= 0){
			return false;
		}
		$open_cycle = $this->get_open_cycle($subscription_id);
		if($open_cycle){
			return true;
		}

		$last_cycle = $this->db->query("SELECT * FROM saas_subscription_cycles WHERE subscription_id = ".$subscription_id." ORDER BY id DESC LIMIT 1")->row();
		$cycle_number = $last_cycle ? ((int)$last_cycle->cycle_number + 1) : 1;

		$this->db->insert('saas_subscription_cycles', [
			'subscription_id' => $subscription_id,
			'tenant_id' => $tenant_id,
			'cycle_number' => $cycle_number,
			'reference_label' => date('m/Y', strtotime($period_end)),
			'period_start' => $period_start,
			'period_end' => $period_end,
			'due_at' => $period_end,
			'status' => 'pending',
			'amount_due' => $amount_due,
			'amount_paid' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);

		return true;
	}

	function map_subscription_status($gateway_status){
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

	function sync_tenant_to_tree($tenant_id, $root_user_id){
		$tenant_id = (int)$tenant_id;
		$root_user_id = (int)$root_user_id;
		if($tenant_id <= 0 || $root_user_id <= 0 || !$this->db->field_exists('tenant_id', 'usuarios')){
			return;
		}

		$user_ids = $this->padrao_model->expand_user_tree_ids([$root_user_id]);
		if(!in_array($root_user_id, $user_ids)){
			$user_ids[] = $root_user_id;
		}

		foreach($user_ids as $user_id){
			$user_id = (int)$user_id;
			if($user_id <= 0){
				continue;
			}
			$usuario = $this->db->query("SELECT id, nivel FROM usuarios WHERE id = ".$user_id." LIMIT 1")->row();
			if(!$usuario){
				continue;
			}
			$this->db->where('id', $user_id);
			$this->db->update('usuarios', [
				'tenant_id' => $tenant_id,
				'tenant_role' => $this->infer_tenant_role($usuario->nivel, $root_user_id === $user_id),
				'onboarding_status' => 'ativo',
			]);
		}
	}

	function infer_tenant_role($nivel, $is_owner=false){
		$nivel = (int)$nivel;
		if($is_owner){
			return 'owner';
		}
		switch($nivel){
			case 1:
			case 2:
				return 'admin';
			case 3:
				return 'provider';
			case 4:
				return 'staff';
			case 5:
				return 'patient';
			default:
				return 'member';
		}
	}

	function normalize_money($value){
		$value = str_replace('R$ ', '', trim((string)$value));
		$value = str_replace('.', '', $value);
		$value = str_replace(',', '.', $value);
		return (float)$value;
	}

	function normalize_billing_cycle($plano){
		if(isset($plano->billing_interval) && trim($plano->billing_interval) !== ''){
			return trim($plano->billing_interval);
		}
		return 'monthly';
	}

	function calculate_period_end($start, $cycle, $interval_count){
		$interval_count = max(1, (int)$interval_count);
		switch($cycle){
			case 'weekly':
				return date('Y-m-d H:i:s', strtotime('+'.$interval_count.' week', strtotime($start)));
			case 'quarterly':
				return date('Y-m-d H:i:s', strtotime('+'.($interval_count * 3).' month', strtotime($start)));
			case 'semiannual':
				return date('Y-m-d H:i:s', strtotime('+'.($interval_count * 6).' month', strtotime($start)));
			case 'yearly':
				return date('Y-m-d H:i:s', strtotime('+'.$interval_count.' year', strtotime($start)));
			case 'monthly':
			default:
				return date('Y-m-d H:i:s', strtotime('+'.$interval_count.' month', strtotime($start)));
		}
	}

	function build_slug($text){
		$text = trim((string)$text);
		$text = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text));
		$text = preg_replace('/[^a-z0-9]+/', '-', $text);
		$text = trim($text, '-');
		return $text !== '' ? $text : 'tenant-'.date('YmdHis');
	}

	private function build_unique_tenant_slug($text){
		$base_slug = $this->build_slug($text);
		$slug = $base_slug;
		$index = 1;
		while($this->db->query("SELECT id FROM saas_tenants WHERE slug = ".$this->db->escape($slug)." LIMIT 1")->num_rows()){
			$index++;
			$slug = $base_slug.'-'.$index;
		}
		return $slug;
	}

	private function build_tenant_where_sql($viewer){
		if((int)$viewer->nivel === 1){
			return '';
		}
		if(isset($viewer->tenant_id) && (int)$viewer->tenant_id > 0){
			return " WHERE t.id = ".(int)$viewer->tenant_id." ";
		}
		return " WHERE t.id_owner_user = ".(int)$viewer->id." ";
	}

	private function build_subscription_where_sql($viewer){
		if((int)$viewer->nivel === 1){
			return '';
		}
		if(isset($viewer->tenant_id) && (int)$viewer->tenant_id > 0){
			return " WHERE s.tenant_id = ".(int)$viewer->tenant_id." ";
		}
		return " WHERE s.id_cliente = ".(int)$viewer->id." ";
	}
}
