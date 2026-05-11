<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dev extends CI_Controller {

	private function table_exists($table){
		return $this->db->table_exists($table);
	}

	private function column_exists($table, $column){
		return $this->db->field_exists($column, $table);
	}

	private function run_sql($sql, &$logs, $label){
		if($this->db->query($sql)){
			$logs[] = 'OK: '.$label;
			return true;
		}
		$error = $this->db->error();
		$logs[] = 'ERRO: '.$label.' - '.$error['message'];
		return false;
	}

	private function ensure_column($table, $column, $definition, &$logs){
		if($this->column_exists($table, $column)){
			$logs[] = 'OK: coluna `'.$table.'.'.$column.'` ja existia';
			return true;
		}
		return $this->run_sql("ALTER TABLE `".$table."` ADD COLUMN `".$column."` ".$definition, $logs, 'coluna `'.$table.'.'.$column.'` criada');
	}

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('adm/usuarios_model');
		$this->usuarios_model->verSession();
		if($this->session->userdata('nivel') != 1){
			exit('Acesso negado.');
		}
	}

	function index(){
		echo '<h2>Dev Controller</h2><ul>';
		echo '<li><a href="'.base_url().'adm/dev/criar_tabela_arquivos_paciente">Criar tabela pacientes_arquivos</a></li>';
		echo '<li><a href="'.base_url().'adm/dev/migrar_fase1_saas">Migrar fase 1 SaaS</a></li>';
		echo '</ul>';
	}

	function criar_tabela_arquivos_paciente(){
		$sql = "CREATE TABLE IF NOT EXISTS `pacientes_arquivos` (
			`id`             INT AUTO_INCREMENT PRIMARY KEY,
			`id_paciente`    INT NOT NULL,
			`id_agendamento` INT DEFAULT 0,
			`id_user`        INT NOT NULL,
			`arquivo`        VARCHAR(255) NOT NULL,
			`nome_original`  VARCHAR(255) NOT NULL,
			`tipo`           VARCHAR(10)  NOT NULL,
			`descricao`      VARCHAR(255) DEFAULT '',
			`dt_cadastro`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		if($this->db->query($sql)){
			echo '✅ Tabela <strong>pacientes_arquivos</strong> criada com sucesso (ou já existia).';
		} else {
			echo '❌ Erro ao criar tabela: '.$this->db->error()['message'];
		}
	}

	function migrar_fase1_saas(){
		$logs = [];

		$sql_tenants = "CREATE TABLE IF NOT EXISTS `saas_tenants` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_responsavel` INT NOT NULL DEFAULT 0,
			`id_owner_user` INT NOT NULL DEFAULT 0,
			`tenant_nome` VARCHAR(150) NOT NULL,
			`slug` VARCHAR(160) DEFAULT NULL,
			`tenant_tipo` VARCHAR(40) DEFAULT 'clinica',
			`documento` VARCHAR(30) DEFAULT NULL,
			`contato_nome` VARCHAR(150) DEFAULT NULL,
			`contato_email` VARCHAR(150) DEFAULT NULL,
			`contato_telefone` VARCHAR(30) DEFAULT NULL,
			`status` TINYINT NOT NULL DEFAULT 1,
			`trial_ends_at` DATETIME DEFAULT NULL,
			`activated_at` DATETIME DEFAULT NULL,
			`expires_at` DATETIME DEFAULT NULL,
			`suspended_at` DATETIME DEFAULT NULL,
			`canceled_at` DATETIME DEFAULT NULL,
			`observacoes` TEXT DEFAULT NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` DATETIME NULL DEFAULT NULL,
			KEY `idx_saas_tenants_owner` (`id_owner_user`),
			KEY `idx_saas_tenants_status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_tenants, $logs, 'tabela `saas_tenants`');

		$sql_subscriptions = "CREATE TABLE IF NOT EXISTS `saas_subscriptions` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`tenant_id` INT NOT NULL,
			`plano_id` INT NOT NULL DEFAULT 0,
			`pedido_id` INT NOT NULL DEFAULT 0,
			`id_cliente` INT NOT NULL DEFAULT 0,
			`billing_cycle` VARCHAR(30) NOT NULL DEFAULT 'monthly',
			`billing_interval_count` INT NOT NULL DEFAULT 1,
			`status` VARCHAR(30) NOT NULL DEFAULT 'trial',
			`valor` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			`setup_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			`trial_ends_at` DATETIME DEFAULT NULL,
			`started_at` DATETIME DEFAULT NULL,
			`current_period_start` DATETIME DEFAULT NULL,
			`current_period_end` DATETIME DEFAULT NULL,
			`next_billing_at` DATETIME DEFAULT NULL,
			`canceled_at` DATETIME DEFAULT NULL,
			`gateway` VARCHAR(40) DEFAULT NULL,
			`gateway_customer_id` VARCHAR(120) DEFAULT NULL,
			`gateway_subscription_id` VARCHAR(120) DEFAULT NULL,
			`gateway_reference` VARCHAR(120) DEFAULT NULL,
			`observacoes` TEXT DEFAULT NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` DATETIME NULL DEFAULT NULL,
			KEY `idx_saas_subscriptions_tenant` (`tenant_id`),
			KEY `idx_saas_subscriptions_status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_subscriptions, $logs, 'tabela `saas_subscriptions`');

		$sql_cycles = "CREATE TABLE IF NOT EXISTS `saas_subscription_cycles` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`subscription_id` INT NOT NULL,
			`tenant_id` INT NOT NULL,
			`cycle_number` INT NOT NULL DEFAULT 1,
			`reference_label` VARCHAR(80) DEFAULT NULL,
			`period_start` DATETIME DEFAULT NULL,
			`period_end` DATETIME DEFAULT NULL,
			`due_at` DATETIME DEFAULT NULL,
			`paid_at` DATETIME DEFAULT NULL,
			`status` VARCHAR(30) NOT NULL DEFAULT 'pending',
			`amount_due` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			`amount_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			`payment_method` VARCHAR(30) DEFAULT NULL,
			`gateway_payment_id` VARCHAR(120) DEFAULT NULL,
			`pedido_id` INT NOT NULL DEFAULT 0,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` DATETIME NULL DEFAULT NULL,
			KEY `idx_saas_cycles_subscription` (`subscription_id`),
			KEY `idx_saas_cycles_status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_cycles, $logs, 'tabela `saas_subscription_cycles`');

		$sql_events = "CREATE TABLE IF NOT EXISTS `saas_billing_events` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`subscription_id` INT NOT NULL DEFAULT 0,
			`tenant_id` INT NOT NULL DEFAULT 0,
			`cycle_id` INT NOT NULL DEFAULT 0,
			`event_type` VARCHAR(50) NOT NULL,
			`gateway` VARCHAR(40) DEFAULT NULL,
			`gateway_reference` VARCHAR(120) DEFAULT NULL,
			`status` VARCHAR(30) DEFAULT NULL,
			`amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			`payload_text` MEDIUMTEXT DEFAULT NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			KEY `idx_saas_events_subscription` (`subscription_id`),
			KEY `idx_saas_events_tenant` (`tenant_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_events, $logs, 'tabela `saas_billing_events`');

		$this->ensure_column('usuarios', 'tenant_id', "INT NULL DEFAULT NULL AFTER `id_user`", $logs);
		$this->ensure_column('usuarios', 'tenant_role', "VARCHAR(30) NULL DEFAULT NULL AFTER `tenant_id`", $logs);
		$this->ensure_column('usuarios', 'billing_customer_id', "VARCHAR(120) NULL DEFAULT NULL AFTER `tenant_role`", $logs);
		$this->ensure_column('usuarios', 'onboarding_status', "VARCHAR(30) NOT NULL DEFAULT 'pendente' AFTER `billing_customer_id`", $logs);

		$this->ensure_column('produtos', 'plan_code', "VARCHAR(80) NULL DEFAULT NULL AFTER `codigo`", $logs);
		$this->ensure_column('produtos', 'billing_interval', "VARCHAR(30) NOT NULL DEFAULT 'monthly' AFTER `plan_code`", $logs);
		$this->ensure_column('produtos', 'billing_interval_count', "INT NOT NULL DEFAULT 1 AFTER `billing_interval`", $logs);
		$this->ensure_column('produtos', 'trial_days', "INT NOT NULL DEFAULT 0 AFTER `billing_interval_count`", $logs);
		$this->ensure_column('produtos', 'setup_fee', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `trial_days`", $logs);
		$this->ensure_column('produtos', 'max_profissionais', "INT NOT NULL DEFAULT 0 AFTER `setup_fee`", $logs);
		$this->ensure_column('produtos', 'max_colaboradores', "INT NOT NULL DEFAULT 0 AFTER `max_profissionais`", $logs);
		$this->ensure_column('produtos', 'max_pacientes', "INT NOT NULL DEFAULT 0 AFTER `max_colaboradores`", $logs);
		$this->ensure_column('produtos', 'saas_publicado', "TINYINT NOT NULL DEFAULT 1 AFTER `max_pacientes`", $logs);

		$this->ensure_column('pedidos', 'tenant_id', "INT NULL DEFAULT NULL", $logs);
		$this->ensure_column('pedidos', 'subscription_id', "INT NULL DEFAULT NULL", $logs);
		$this->ensure_column('pedidos', 'billing_reference', "VARCHAR(120) NULL DEFAULT NULL", $logs);
		$this->ensure_column('pedidos', 'gateway_payment_id', "VARCHAR(120) NULL DEFAULT NULL", $logs);
		$this->ensure_column('pedidos', 'paid_at', "DATETIME NULL DEFAULT NULL", $logs);

		$this->ensure_column('carrinho_hist', 'tenant_id', "INT NULL DEFAULT NULL", $logs);
		$this->ensure_column('carrinho_hist', 'subscription_id', "INT NULL DEFAULT NULL", $logs);
		$this->ensure_column('carrinho_hist', 'cycle_id', "INT NULL DEFAULT NULL", $logs);

		$this->ensure_column('saas_subscriptions', 'checkout_url', "TEXT NULL DEFAULT NULL", $logs);
		$this->ensure_column('saas_subscriptions', 'checkout_type', "VARCHAR(30) NULL DEFAULT NULL", $logs);
		$this->ensure_column('saas_subscriptions', 'gateway_status_detail', "VARCHAR(120) NULL DEFAULT NULL", $logs);
		$this->ensure_column('saas_subscriptions', 'webhook_last_event_at', "DATETIME NULL DEFAULT NULL", $logs);

		echo '<h2>Migracao Fase 1 SaaS</h2><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
		echo '<p><a href="'.base_url().'adm/saas">Abrir modulo SaaS</a></p>';
	}
}
