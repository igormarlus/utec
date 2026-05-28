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

	private function SE($table, $data){
		$filtered = [];
		foreach($data as $column => $value){
			if($this->column_exists($table, $column)){
				$filtered[$column] = $value;
			}
		}
		return $filtered;
	}

	private function ensure_plan_category($nome, &$logs){
		$qr = $this->db->query("SELECT * FROM produtos_categorias WHERE nome = ".$this->db->escape($nome)." LIMIT 1");
		if($qr->num_rows()){
			$logs[] = 'OK: categoria `'.$nome.'` ja existia';
			return (int)$qr->row()->id;
		}

		$data = $this->filter_existing_columns('produtos_categorias', [
			'nome' => $nome,
			'status' => 1,
			'id_user' => (int)$this->session->userdata('id'),
		]);
		$this->db->insert('produtos_categorias', $data);
		$logs[] = 'OK: categoria `'.$nome.'` criada';
		return (int)$this->db->insert_id();
	}

	private function upsert_saas_plan($category_id, $plan, &$logs){
		$where_field = $this->column_exists('produtos', 'plan_code') ? 'plan_code' : 'codigo';
		$where_value = $this->column_exists('produtos', 'plan_code') ? $plan['plan_code'] : $plan['codigo'];
		$qr = $this->db->query("SELECT id FROM produtos WHERE ".$where_field." = ".$this->db->escape($where_value)." LIMIT 1");

		$data = $this->filter_existing_columns('produtos', [
			'modelo' => $plan['modelo'],
			'id_categoria' => $category_id,
			'preco' => $plan['preco'],
			'preco_venda' => $plan['preco_venda'],
			'qtd' => $plan['qtd'],
			'codigo' => $plan['codigo'],
			'status' => 1,
			'especificacoes' => $plan['especificacoes'],
			'plan_code' => $plan['plan_code'],
			'billing_interval' => 'monthly',
			'billing_interval_count' => 1,
			'trial_days' => 7,
			'setup_fee' => 0.00,
			'max_profissionais' => $plan['max_profissionais'],
			'max_colaboradores' => $plan['max_colaboradores'],
			'max_pacientes' => $plan['max_pacientes'],
			'saas_publicado' => $plan['saas_publicado'],
			'id_user' => (int)$this->session->userdata('id'),
		]);

		if($qr->num_rows()){
			$id = (int)$qr->row()->id;
			$this->db->where('id', $id);
			$this->db->update('produtos', $data);
			$logs[] = 'OK: plano `'.$plan['modelo'].'` atualizado';
			return $id;
		}

		$this->db->insert('produtos', $data);
		$logs[] = 'OK: plano `'.$plan['modelo'].'` criado';
		return (int)$this->db->insert_id();
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
		echo '<li><a href="'.base_url().'adm/dev/seed_planos_saas_comerciais">Criar planos SaaS sugeridos</a></li>';
		echo '<li><a href="'.base_url().'adm/dev/migrar_especialidades">Criar tabela usuarios_especialidades e normalizar campo</a></li>';
		echo '<li><a href="'.base_url().'adm/dev/testar_email_boas_vindas">Testar e-mail de boas-vindas (sem cadastrar)</a></li>';
		echo '</ul>';
	}

	function testar_email_boas_vindas(){
		$enviado = false;
		$erro    = '';
		$admin   = $this->db->get_where('usuarios', ['id' => (int)$this->session->userdata('id')])->row();
		$email_default = $admin ? $admin->email : '';

		if($this->input->post('email_destino')){
			$email_destino = trim($this->input->post('email_destino'));
			$token         = bin2hex(random_bytes(32));

			$result = [
				'tenant_nome'   => $this->input->post('tenant_nome') ?: 'Clínica Teste Demo',
				'login'         => $email_destino,
				'senha_gerada'  => 'Teste@2026',
				'token'         => $token,
				'trial_ends_at' => date('Y-m-d', strtotime('+30 days')),
			];

			try {
				$this->config->load('email');
				$this->load->library('email');
				$this->email->initialize($this->config->config);

				$nome_clinica = htmlspecialchars((string)$result['tenant_nome']);
				$login        = htmlspecialchars((string)$result['login']);
				$senha        = htmlspecialchars((string)$result['senha_gerada']);
				$link_senha   = base_url().'acesso/senha/'.$token;
				$link_sistema = base_url().'admin';
				$trial_fim    = date('d/m/Y', strtotime($result['trial_ends_at']));

				$body = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f6f8fb;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fb;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <tr><td style="background:linear-gradient(90deg,#0f766e,#f97316);padding:32px 40px;">
    <p style="margin:0;font-size:13px;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.8);font-weight:700;">UTecnologia Saúde</p>
    <h1 style="margin:10px 0 0;color:#fff;font-size:26px;font-weight:800;">Seu acesso está pronto! 🎉</h1>
  </td></tr>
  <tr><td style="padding:36px 40px;">
    <p style="font-size:16px;color:#334155;line-height:1.7;">Olá, <strong>'.htmlspecialchars($login).'</strong>!</p>
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
				$this->email->to($email_destino);
				$this->email->bcc('igor_marlus@yahoo.com.br');
				$this->email->subject('[TESTE] Seu acesso UTecnologia Saúde está pronto — '.$nome_clinica);
				$this->email->message($body);

				if($this->email->send()){
					$enviado = true;
				} else {
					$erro = $this->email->print_debugger(['headers','subject','body']);
				}
			} catch(Exception $e){
				$erro = $e->getMessage();
			}
		}

		echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
		<title>Testar E-mail de Boas-vindas</title>
		<link rel="stylesheet" href="'.base_url().'bower_components/bootstrap/dist/css/bootstrap.min.css">
		</head><body class="p-4">';
		echo '<h2>Testar E-mail de Boas-vindas</h2>';
		echo '<p class="text-muted">Envia o e-mail de boas-vindas com um token real (não cadastra nenhum usuário).</p>';

		if($enviado){
			$admin_email = htmlspecialchars($this->input->post('email_destino'));
			echo '<div class="alert alert-success">✅ E-mail enviado para <strong>'.$admin_email.'</strong> com BCC para <strong>igor_marlus@yahoo.com.br</strong>.<br>Verifique caixa de entrada e <strong>pasta de spam</strong> em ambas as contas.</div>';
			echo '<details class="mt-2"><summary style="cursor:pointer;color:#555;">Ver debug SMTP</summary><pre style="font-size:11px;background:#f8f8f8;padding:10px;margin-top:6px;">'.htmlspecialchars($this->email->print_debugger()).'</pre></details>';
		}
		if($erro){
			echo '<div class="alert alert-danger"><strong>Erro ao enviar:</strong><pre style="font-size:12px;margin-top:8px;">'.htmlspecialchars($erro).'</pre></div>';
		}

		$email_val = htmlspecialchars($this->input->post('email_destino') ?: $email_default);
		$nome_val  = htmlspecialchars($this->input->post('tenant_nome') ?: 'Clínica Teste Demo');

		echo '<form method="post" class="mt-3" style="max-width:480px;">
			<div class="form-group">
				<label>E-mail destino</label>
				<input type="email" name="email_destino" class="form-control" value="'.$email_val.'" required>
			</div>
			<div class="form-group mt-2">
				<label>Nome da clínica (fictício)</label>
				<input type="text" name="tenant_nome" class="form-control" value="'.$nome_val.'">
			</div>
			<button type="submit" class="btn btn-primary mt-3">Enviar e-mail de teste</button>
			<a href="'.base_url().'adm/dev" class="btn btn-secondary mt-3 ml-2">Voltar</a>
		</form>';
		echo '</body></html>';
	}

	function migrar_token_senha(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];

		$cols = [
			'senha_token'         => "ALTER TABLE `usuarios` ADD COLUMN `senha_token` VARCHAR(64) DEFAULT NULL",
			'senha_token_expires' => "ALTER TABLE `usuarios` ADD COLUMN `senha_token_expires` DATETIME DEFAULT NULL",
		];
		foreach($cols as $col => $sql){
			if(!$this->db->field_exists($col, 'usuarios')){
				if($this->db->query($sql)){
					$logs[] = "✅ Coluna <strong>$col</strong> adicionada em <code>usuarios</code>.";
				} else {
					$logs[] = "❌ Erro ao adicionar <strong>$col</strong>: ".$this->db->error()['message'];
				}
			} else {
				$logs[] = "⚠️ Coluna <strong>$col</strong> já existe.";
			}
		}

		echo '<h3>Migração: token de definição de senha</h3><ul>';
		foreach($logs as $l) echo "<li>$l</li>";
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

	function seed_planos_saas_comerciais(){
		$logs = [];

		if(!$this->table_exists('produtos') || !$this->table_exists('produtos_categorias')){
			echo '<p>As tabelas de produtos ainda nao existem neste ambiente.</p>';
			return;
		}

		$category_id = $this->ensure_plan_category('Assinaturas SaaS', $logs);

		$plans = [
			[
				'modelo' => 'Solo Start',
				'codigo' => 'SAAS-SOLO',
				'plan_code' => 'solo-start-mensal',
				'preco' => 99.90,
				'preco_venda' => 99.90,
				'qtd' => 1,
				'max_profissionais' => 1,
				'max_colaboradores' => 2,
				'max_pacientes' => 800,
				'saas_publicado' => 1,
				'especificacoes' => "Indicado para profissional autonomo ou consultorio enxuto.\nInclui agenda, cadastro de pacientes, prontuario eletronico, timeline clinica, anexos de exames e relatorios basicos.\nLimites: 1 profissional, 2 colaboradores e ate 800 pacientes ativos.",
			],
			[
				'modelo' => 'Clinica Essencial',
				'codigo' => 'SAAS-ESS',
				'plan_code' => 'clinica-essencial-mensal',
				'preco' => 197.00,
				'preco_venda' => 197.00,
				'qtd' => 3,
				'max_profissionais' => 3,
				'max_colaboradores' => 6,
				'max_pacientes' => 3000,
				'saas_publicado' => 1,
				'especificacoes' => "Indicado para clinicas em inicio de operacao estruturada.\nInclui tudo do Solo Start com operacao multiprofissional, mais capacidade de equipe e melhor cobertura para recepcao e atendimento.\nLimites: 3 profissionais, 6 colaboradores e ate 3.000 pacientes ativos.",
			],
			[
				'modelo' => 'Clinica Pro',
				'codigo' => 'SAAS-PRO',
				'plan_code' => 'clinica-pro-mensal',
				'preco' => 397.00,
				'preco_venda' => 397.00,
				'qtd' => 6,
				'max_profissionais' => 6,
				'max_colaboradores' => 12,
				'max_pacientes' => 12000,
				'saas_publicado' => 1,
				'especificacoes' => "Indicado para clinicas em crescimento com maior volume operacional.\nInclui toda a base clinica do sistema com capacidade ampliada para equipe, prontuarios, anexos, agenda e relatorios.\nLimites: 6 profissionais, 12 colaboradores e ate 12.000 pacientes ativos.",
			],
			[
				'modelo' => 'Enterprise',
				'codigo' => 'SAAS-ENT',
				'plan_code' => 'enterprise-mensal',
				'preco' => 797.00,
				'preco_venda' => 797.00,
				'qtd' => 12,
				'max_profissionais' => 12,
				'max_colaboradores' => 30,
				'max_pacientes' => 50000,
				'saas_publicado' => 0,
				'especificacoes' => "Plano para operacoes maiores e negociacao consultiva.\nUsa a mesma base clinica do sistema com limites expandidos e margem para customizacao comercial, onboarding assistido e condicoes especiais.\nLimites de referencia: 12 profissionais, 30 colaboradores e ate 50.000 pacientes ativos.",
			],
		];

		foreach($plans as $plan){
			$this->upsert_saas_plan($category_id, $plan, $logs);
		}

		echo '<h2>Planos SaaS sugeridos</h2><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
		echo '<p>Base sugerida a partir de referencias publicas observadas em 11/05/2026, com entrada em faixas como R$ 99,90 (Smed), R$ 79,90-R$ 99,90 por profissional (QuarkClinic) e planos de clinica em R$ 299, R$ 599 e R$ 999 (Prontivus).</p>';
		echo '<p><a href="'.base_url().'adm/produtos">Abrir catalogo de planos</a></p>';
	}

	function migrar_especialidades(){
		$logs = [];

		// 1. Criar tabela
		$sql_create = "CREATE TABLE IF NOT EXISTS `usuarios_especialidades` (
			`id`          INT AUTO_INCREMENT PRIMARY KEY,
			`nome`        VARCHAR(150) NOT NULL,
			`status`      TINYINT NOT NULL DEFAULT 1,
			`ordem`       INT NOT NULL DEFAULT 0,
			`dt_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_create, $logs, 'tabela `usuarios_especialidades`');

		// 2. Seed (idempotente por id)
		$especialidades = [
			[1,  'Acupuntura',                          10],
			[2,  'Alergologia e Imunologia',             20],
			[3,  'Cardiologia',                          30],
			[4,  'Cirurgia Cardiovascular',              40],
			[5,  'Cirurgia Geral',                       50],
			[6,  'Cirurgia Plástica',                    60],
			[7,  'Clínica Médica',                       70],
			[8,  'Dermatologia',                         80],
			[9,  'Endocrinologia e Metabologia',         90],
			[10, 'Fisioterapia',                        100],
			[11, 'Fonoaudiologia',                      110],
			[12, 'Gastroenterologia',                   120],
			[13, 'Geriatria',                           130],
			[14, 'Ginecologia e Obstetrícia',           140],
			[15, 'Hematologia',                         150],
			[16, 'Homeopatia',                          160],
			[17, 'Infectologia',                        170],
			[18, 'Medicina de Família e Comunidade',    180],
			[19, 'Medicina do Esporte',                 190],
			[20, 'Medicina do Trabalho',                200],
			[21, 'Medicina Estética',                   210],
			[22, 'Medicina Intensiva',                  220],
			[23, 'Medicina Legal',                      230],
			[24, 'Nefrologia',                          240],
			[25, 'Neurologia',                          250],
			[26, 'Neurocirurgia',                       260],
			[27, 'Nutrição',                            270],
			[28, 'Odontologia',                         280],
			[29, 'Oftalmologia',                        290],
			[30, 'Oncologia',                           300],
			[31, 'Ortopedia e Traumatologia',           310],
			[32, 'Otorrinolaringologia',                320],
			[33, 'Pediatria',                           330],
			[34, 'Pneumologia',                         340],
			[35, 'Proctologia',                         350],
			[36, 'Psicologia',                          360],
			[37, 'Psiquiatria',                         370],
			[38, 'Radiologia e Diagnóstico por Imagem', 380],
			[39, 'Reumatologia',                        390],
			[40, 'Terapia Ocupacional',                 400],
			[41, 'Urologia',                            410],
			[42, 'Vascular e Angiologia',               420],
		];

		$inserted = 0; $skipped = 0;
		foreach($especialidades as $esp){
			$exists = $this->db->query("SELECT id FROM usuarios_especialidades WHERE id = ".(int)$esp[0]." LIMIT 1")->num_rows();
			if(!$exists){
				$this->db->query(
					"INSERT INTO usuarios_especialidades (id, nome, status, ordem) VALUES (".
					(int)$esp[0].", ".$this->db->escape($esp[1]).", 1, ".(int)$esp[2].")"
				);
				$inserted++;
			} else {
				$skipped++;
			}
		}
		$logs[] = "OK: seed — $inserted especialidades inseridas, $skipped já existiam";

		// 3. Migrar valores texto conhecidos para IDs (nivel = 3)
		$migrate_sqls = [
			6  => "UPDATE usuarios SET especialidade = '6'  WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%cirurgia%'",
			28 => "UPDATE usuarios SET especialidade = '28' WHERE nivel = 3 AND especialidade IS NOT NULL AND (LOWER(especialidade) LIKE '%dentista%' OR LOWER(especialidade) LIKE '%odontolog%')",
			36 => "UPDATE usuarios SET especialidade = '36' WHERE nivel = 3 AND especialidade IS NOT NULL AND (LOWER(especialidade) LIKE '%psicolog%' OR LOWER(especialidade) LIKE '%pscicologo%')",
			10 => "UPDATE usuarios SET especialidade = '10' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%fisioterapia%'",
			3  => "UPDATE usuarios SET especialidade = '3'  WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%cardiologia%'",
			7  => "UPDATE usuarios SET especialidade = '7'  WHERE nivel = 3 AND especialidade IS NOT NULL AND (LOWER(especialidade) LIKE '%clinica medica%' OR LOWER(especialidade) LIKE '%clinica geral%')",
			8  => "UPDATE usuarios SET especialidade = '8'  WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%dermatologia%'",
			9  => "UPDATE usuarios SET especialidade = '9'  WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%endocrinolog%'",
			14 => "UPDATE usuarios SET especialidade = '14' WHERE nivel = 3 AND especialidade IS NOT NULL AND (LOWER(especialidade) LIKE '%ginecolog%' OR LOWER(especialidade) LIKE '%obstetric%')",
			25 => "UPDATE usuarios SET especialidade = '25' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%neurologia%'",
			27 => "UPDATE usuarios SET especialidade = '27' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%nutri%'",
			29 => "UPDATE usuarios SET especialidade = '29' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%oftalmolog%'",
			31 => "UPDATE usuarios SET especialidade = '31' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%ortopedia%'",
			33 => "UPDATE usuarios SET especialidade = '33' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%pediatria%'",
			37 => "UPDATE usuarios SET especialidade = '37' WHERE nivel = 3 AND especialidade IS NOT NULL AND LOWER(especialidade) LIKE '%psiquiatria%'",
		];

		// Só executa para valores que ainda não sejam numéricos
		foreach($migrate_sqls as $esp_id => $base_sql){
			$sql = $base_sql." AND especialidade NOT REGEXP '^[0-9]+$'";
			$this->db->query($sql);
			$affected = $this->db->affected_rows();
			if($affected > 0){
				$logs[] = "OK: $affected registro(s) → id $esp_id";
			}
		}

		// 4. Anular valores texto restantes não mapeados (em qualquer nivel)
		$this->run_sql(
			"UPDATE usuarios SET especialidade = NULL WHERE especialidade IS NOT NULL AND especialidade != '' AND especialidade NOT REGEXP '^[0-9]+$'",
			$logs,
			'anular valores texto não mapeados'
		);

		// 5. Alterar tipo da coluna para INT (se ainda for texto)
		$qr_col = $this->db->query(
			"SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'especialidade' LIMIT 1"
		);
		if($qr_col->num_rows() > 0){
			$current_type = strtolower($qr_col->row()->DATA_TYPE);
			if(strpos($current_type, 'int') === false){
				$this->run_sql(
					"ALTER TABLE `usuarios` MODIFY COLUMN `especialidade` INT NULL DEFAULT NULL",
					$logs,
					'ALTER usuarios.especialidade VARCHAR → INT'
				);
			} else {
				$logs[] = "OK: usuarios.especialidade já é INT — sem ALTER necessário";
			}
		}

		echo '<h2>Migração: Especialidades</h2><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
		echo '<p><a href="'.base_url().'adm/usuarios">Abrir Usuários</a></p>';
	}
}
