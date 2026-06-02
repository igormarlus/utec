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
		echo '<li><a href="'.base_url().'adm/dev/migrar_fase2_prontuario_especialidades">Fase 2 — Campos extras por especialidade (tabela + inserts)</a></li>';
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

			// Salva o token no próprio admin logado para o link funcionar ao testar
			if($this->db->field_exists('senha_token', 'usuarios') && $this->db->field_exists('senha_token_expires', 'usuarios')){
				$this->db->where('id', (int)$this->session->userdata('id'));
				$this->db->update('usuarios', [
					'senha_token'         => $token,
					'senha_token_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
				]);
			}

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

		$this->ensure_column('usuarios', 'senha_token', "VARCHAR(64) NULL DEFAULT NULL", $logs);
		$this->ensure_column('usuarios', 'senha_token_expires', "DATETIME NULL DEFAULT NULL", $logs);

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

	function migrar_fase2_prontuario_especialidades(){
		$logs = [];

		// 1. Coluna campos_extras em agendamentos
		$this->ensure_column('agendamentos', 'campos_extras', "TEXT NULL DEFAULT NULL", $logs);

		// 2. Tabela de configuração de campos por especialidade
		$sql_create = "CREATE TABLE IF NOT EXISTS `especialidades_campos_config` (
			`id`                INT AUTO_INCREMENT PRIMARY KEY,
			`especialidade_id`  INT NOT NULL,
			`campo_chave`       VARCHAR(80) NOT NULL,
			`campo_label`       VARCHAR(150) NOT NULL,
			`campo_tipo`        VARCHAR(30) NOT NULL DEFAULT 'textarea',
			`campo_opcoes`      TEXT DEFAULT NULL,
			`campo_placeholder` VARCHAR(255) DEFAULT NULL,
			`ordem`             INT NOT NULL DEFAULT 0,
			`obrigatorio`       TINYINT NOT NULL DEFAULT 0,
			`status`            TINYINT NOT NULL DEFAULT 1,
			`dt_cadastro`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY `uk_esp_campo` (`especialidade_id`, `campo_chave`),
			KEY `idx_esp_campos_esp` (`especialidade_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_create, $logs, 'tabela `especialidades_campos_config`');

		// 3. Inserts por especialidade (INSERT IGNORE — idempotente)
		$campos = [

			// ── Fisioterapia (id=10) ─────────────────────────────────────────
			[10, 'eva_dor',          'EVA — Escala de Dor (0–10)',           'number',   null,
				'0 = sem dor, 10 = dor máxima',                                          10, 0],
			[10, 'regiao_corporal',  'Região Corporal Tratada',              'select',
				'["Coluna Cervical","Coluna Lombar","Coluna Torácica","Ombro Direito","Ombro Esquerdo","Cotovelo D.","Cotovelo E.","Punho/Mão D.","Punho/Mão E.","Quadril D.","Quadril E.","Joelho D.","Joelho E.","Tornozelo/Pé D.","Tornozelo/Pé E.","Bilateral","MMII","MMSS","Outro"]',
				'Selecione a região',                                                     20, 0],
			[10, 'fase_tratamento',  'Fase do Tratamento',                   'select',
				'["Aguda","Subaguda","Crônica","Manutenção","Alta"]',
				null,                                                                     30, 0],

			// ── Odontologia (id=28) ──────────────────────────────────────────
			[28, 'dentes_tratados',      'Dente(s) Tratado(s) — Numeração FDI', 'text',  null,
				'Ex: 36, 47, 11',                                                         10, 0],
			[28, 'procedimento_odonto',  'Procedimento Realizado',              'select',
				'["Restauração","Extração","Endodontia (Canal)","Profilaxia/Limpeza","Prótese Parcial","Prótese Total","Ortodontia","Implante","Clareamento","Cirurgia Oral","Gengivoplastia","Outro"]',
				null,                                                                     20, 0],
			[28, 'anestesia_odonto',     'Anestesia Utilizada',                 'select',
				'["Não utilizada","Articaína 4%","Lidocaína 2%","Mepivacaína","Prilocaína","Outro"]',
				null,                                                                     30, 0],
			[28, 'material_odonto',      'Material Utilizado',                  'text',  null,
				'Ex: Compósito A2, Cimento de Ionômero de Vidro...',                      40, 0],

			// ── Psicologia (id=36) ───────────────────────────────────────────
			[36, 'num_sessao',        'Nº da Sessão',   'number', null,
				'Número sequencial da sessão com este paciente',                          10, 0],
			[36, 'modalidade_psico',  'Modalidade',     'select',
				'["Individual","Casal","Família","Grupo","Online — Individual","Online — Casal","Online — Grupo"]',
				null,                                                                     20, 0],
			[36, 'cid_referencia',    'CID de Referência (opcional)', 'text', null,
				'Ex: F41.1, F32.0',                                                       30, 0],

			// ── Psiquiatria (id=37) ──────────────────────────────────────────
			[37, 'cid_psiq',          'CID-10 / Hipótese Diagnóstica',    'text',     null,
				'Ex: F32.1, F20.0, F41.0',                                                10, 0],
			[37, 'mse_exame',         'Exame do Estado Mental (MSE)',     'textarea',  null,
				'Humor, afeto, pensamento, sensopercepção, orientação, memória...',       20, 0],
			[37, 'medicacao_ajuste',  'Medicação / Ajuste Terapêutico',   'textarea',  null,
				'Nome, dose, via, frequência. Ex: Escitalopram 20mg 1x/dia',             30, 0],

			// ── Nutrição (id=27) ─────────────────────────────────────────────
			[27, 'peso_kg_nutri',       'Peso (kg)',    'number', null, 'Ex: 72.5', 10, 0],
			[27, 'altura_cm_nutri',     'Altura (cm)',  'number', null, 'Ex: 170',  20, 0],
			[27, 'objetivo_nutri',      'Objetivo',     'select',
				'["Emagrecimento","Ganho de massa","Manutenção","Saúde geral","Tratamento patológico","Gestação/Lactação","Outro"]',
				null,                                                                     30, 0],
			[27, 'imc_calc',            'IMC',          'text',   null,
				'Calculado ou informado manualmente',                                     40, 0],

			// ── Pediatria (id=33) ────────────────────────────────────────────
			[33, 'peso_ped',         'Peso (kg)',                    'number', null, 'Ex: 12.3',   10, 0],
			[33, 'altura_ped',       'Altura/Comprimento (cm)',      'number', null, 'Ex: 95',     20, 0],
			[33, 'responsavel_ped',  'Nome do Responsável',          'text',   null,
				'Nome do pai, mãe ou responsável legal',                                  30, 0],
			[33, 'parentesco_ped',   'Grau de Parentesco',           'select',
				'["Mãe","Pai","Avó/Avô","Tio/Tia","Irmão/Irmã","Guardião","Outro"]',
				null,                                                                     40, 0],

			// ── Cardiologia (id=3) ───────────────────────────────────────────
			[3, 'pa_sistolica',  'PA Sistólica (mmHg)',  'number', null, 'Ex: 120',  10, 0],
			[3, 'pa_diastolica', 'PA Diastólica (mmHg)', 'number', null, 'Ex: 80',   20, 0],
			[3, 'fc_bpm',        'FC (bpm)',              'number', null, 'Ex: 72',   30, 0],
			[3, 'peso_card',     'Peso (kg)',             'number', null, 'Ex: 75.0', 40, 0],

			// ── Ginecologia e Obstetrícia (id=14) ────────────────────────────
			[14, 'dum_gine',            'DUM (Última Menstruação)', 'text',   null,
				'DD/MM/AAAA',                                                             10, 0],
			[14, 'ig_semanas',          'IG (semanas)',             'number', null,
				'Se gestante, informe a idade gestacional',                               20, 0],
			[14, 'tipo_consulta_gine',  'Tipo de Consulta',         'select',
				'["Ginecológica","Pré-natal","Retorno","Urgência","Procedimento"]',
				null,                                                                     30, 0],

			// ── Fonoaudiologia (id=11) ───────────────────────────────────────
			[11, 'area_fono',        'Área de Atuação',  'select',
				'["Linguagem","Motricidade Orofacial","Deglutição","Voz","Audição","Outro"]',
				null,                                                                     10, 0],
			[11, 'num_sessao_fono',  'Nº da Sessão',     'number', null,
				'Número sequencial da sessão',                                            20, 0],
		];

		$inserted = 0; $skipped = 0;
		foreach($campos as $c){
			$esp_id  = (int)$c[0];
			$chave   = $this->db->escape($c[1]);
			$label   = $this->db->escape($c[2]);
			$tipo    = $this->db->escape($c[3]);
			$opcoes  = ($c[4] !== null) ? $this->db->escape($c[4]) : 'NULL';
			$ph      = ($c[5] !== null) ? $this->db->escape($c[5]) : 'NULL';
			$ordem   = (int)$c[6];
			$obrig   = (int)$c[7];

			$qr = $this->db->query(
				"SELECT id FROM especialidades_campos_config WHERE especialidade_id = $esp_id AND campo_chave = $chave LIMIT 1"
			);
			if($qr->num_rows() > 0){
				$skipped++;
				continue;
			}
			$this->db->query(
				"INSERT INTO especialidades_campos_config (especialidade_id, campo_chave, campo_label, campo_tipo, campo_opcoes, campo_placeholder, ordem, obrigatorio)
				VALUES ($esp_id, $chave, $label, $tipo, $opcoes, $ph, $ordem, $obrig)"
			);
			$inserted++;
		}
		$logs[] = "OK: campos extras — $inserted inseridos, $skipped já existiam";

		echo '<h2>Migração Fase 2 — Campos Extras por Especialidade</h2><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
		echo '<p>Especialidades configuradas: Fisioterapia, Odontologia, Psicologia, Psiquiatria, Nutrição, Pediatria, Cardiologia, Ginecologia, Fonoaudiologia.</p>';
		echo '<p>Coluna <code>agendamentos.campos_extras</code> (TEXT/JSON) criada para armazenar os valores.</p>';
		echo '<p><a href="'.base_url().'adm/dev">Voltar ao Dev</a></p>';
	}

	// -----------------------------------------------------------------
	// Seeder: 3 artigos do Sprint 1 de SEO/GEO — inseridos como rascunhos
	// Rota: adm/dev/semear_artigos_sprint1
	// -----------------------------------------------------------------
	public function semear_artigos_sprint1()
	{
		$usuario = $this->session->userdata('nivel');
		if ((int)$usuario !== 1) { show_404(); return; }

		$artigos = $this->_artigos_sprint1();
		$inseridos = 0;
		$pulados   = 0;
		$logs      = [];

		foreach ($artigos as $art) {
			$existe = $this->db->where('slug', $art['slug'])->count_all_results('blog_posts');
			if ($existe > 0) {
				$logs[]  = 'PULADO: slug "' . $art['slug'] . '" já existe';
				$pulados++;
				continue;
			}
			$art['criado_em']   = date('Y-m-d H:i:s');
			$art['publicado']   = 0;
			$art['publicado_em'] = null;
			$this->db->insert('blog_posts', $art);
			$id = $this->db->insert_id();
			$logs[] = 'OK: artigo "' . $art['titulo'] . '" inserido (id=' . $id . ', rascunho)';
			$inseridos++;
		}

		echo '<h2>Seeder — Artigos Sprint 1 SEO</h2><ul>';
		foreach ($logs as $log) {
			echo '<li>' . htmlspecialchars($log) . '</li>';
		}
		echo '</ul>';
		echo '<p>' . $inseridos . ' artigo(s) inserido(s) como rascunho. ' . $pulados . ' pulado(s).</p>';
		echo '<p>Acesse <a href="' . base_url() . 'adm/blog">adm/blog</a> para revisar e publicar.</p>';
		echo '<p><a href="' . base_url() . 'adm/dev">Voltar ao Dev</a></p>';
	}

	private function _artigos_sprint1()
	{
		$base = base_url();

		// ------------------------------------------------------------------
		// Artigo 1
		// ------------------------------------------------------------------
		$art1_conteudo = <<<HTML
<p>Escolher o melhor software para clínicas não é uma decisão de uma só variável. Volume de pacientes, número de profissionais, especialidade, orçamento e modelo de operação do consultório afetam qual sistema vai realmente funcionar no dia a dia.</p>
<p>Este guia traz 7 critérios práticos para avaliar qualquer software para clínicas antes de contratar — e evitar a armadilha de pagar por recursos que a clínica não usa, ou migrar para um sistema que não atende o fluxo real de atendimento.</p>

<h2>1. O sistema cobre o fluxo real da sua clínica?</h2>
<p>O primeiro filtro é simples: o software resolve o que a sua clínica faz no dia a dia? Uma clínica odontológica precisa de prontuário odontológico e controle de retornos. Uma clínica multiprofissional precisa de agenda separada por profissional. Um consultório solo precisa de algo simples, sem burocracia de onboarding corporativo.</p>
<p>Antes de avaliar recursos avançados, verifique se o básico está coberto para o seu tipo de clínica.</p>

<h2>2. Prontuário eletrônico incluso ou separado?</h2>
<p>Muitos sistemas de agenda não incluem prontuário eletrônico — ou cobram à parte. Em uma clínica de saúde, agenda e prontuário precisam estar integrados: ao abrir o agendamento, o prontuário do paciente deve estar disponível imediatamente, com o histórico de consultas anteriores.</p>
<p>Verifique se o software para clínicas que você está avaliando inclui o <a href="{BASE}sistema-prontuario-eletronico">prontuário eletrônico</a> no mesmo plano da agenda, sem custo adicional.</p>

<h2>3. Quantos profissionais o plano suporta?</h2>
<p>Softwares para clínicas geralmente cobram por número de profissionais. Compare o custo real por profissional, não o preço de entrada. Um sistema com plano "individual" de R$ 79/mês pode ser mais barato que um "all-in-one" de R$ 500/mês se você tem apenas 1 médico.</p>
<p>Para consultórios solo, o <a href="{BASE}sistema-para-consultorio-medico">plano para consultório médico</a> tende a ser bem mais acessível do que planos voltados para clínicas com equipe.</p>

<h2>4. O sistema é 100% online ou precisa de instalação?</h2>
<p>Sistemas que precisam de instalação local exigem manutenção técnica, atualizações manuais e backup próprio. Sistemas online (SaaS) funcionam pelo navegador, atualizam automaticamente e mantêm os dados na nuvem com backup automático.</p>
<p>Para clínicas pequenas e consultórios que não têm equipe de TI, um software para clínicas online elimina esse problema inteiramente.</p>

<h2>5. A recepcionista pode usar sem acessar os prontuários?</h2>
<p>Controle de acesso por perfil é fundamental. A recepcionista precisa gerenciar a agenda e o cadastro de pacientes — mas não deve ter acesso aos prontuários clínicos. Verifique se o sistema permite essa separação de permissões de forma simples e clara.</p>

<h2>6. Quanto tempo leva para migrar da planilha?</h2>
<p>Um bom software para clínicas deve ter onboarding rápido. Se o sistema exige semanas de implantação e treinamento para uma clínica pequena, isso é um sinal de que ele foi projetado para hospitais ou grandes redes — não para o seu perfil.</p>
<p>Teste grátis com trial real (não apenas demo) é o padrão mínimo aceitável para avaliar isso.</p>

<h2>7. O suporte é acessível quando você precisa?</h2>
<p>Em um consultório ou clínica pequena, o sistema para de funcionar e você não tem TI interno para resolver. Verifique se o suporte responde de forma humana, em quanto tempo e por quais canais.</p>

<h2>Resumo: como escolher o melhor software para clínicas</h2>
<ul>
<li>Verifique se cobre o fluxo real da sua especialidade</li>
<li>Confirme que prontuário e agenda estão integrados no mesmo plano</li>
<li>Compare o custo real por profissional, não o preço de entrada</li>
<li>Prefira sistema online a software instalado em servidor local</li>
<li>Exija separação de permissões entre médico e recepcionista</li>
<li>Teste com trial real antes de assinar qualquer contrato</li>
<li>Avalie o suporte antes de contratar — não depois</li>
</ul>

<p>Para explorar opções por especialidade, veja o <a href="{BASE}sistema-para-clinicas">comparativo de sistemas por tipo de clínica</a>.</p>
HTML;

		// ------------------------------------------------------------------
		// Artigo 2
		// ------------------------------------------------------------------
		$art2_conteudo = <<<HTML
<p>Escolher um software para clínica odontológica envolve critérios diferentes dos de uma clínica médica geral. A rotina de um consultório odontológico tem especificidades que nem todo sistema de gestão clínica resolve bem: retornos frequentes, histórico de procedimentos por dente, múltiplas sessões para o mesmo tratamento e agenda densa.</p>
<p>Este guia traz os critérios práticos para avaliar um software odontológico antes de contratar — e o que evitar para não errar na escolha.</p>

<h2>O que um software para clínica odontológica precisa ter</h2>

<h3>1. Agenda com controle de retornos</h3>
<p>A agenda de uma clínica odontológica é mais densa que a de outras especialidades. Retornos pós-procedimento, avaliações de rotina e tratamentos em múltiplas sessões precisam ser organizados sem conflito de horário. Verifique se o sistema permite remarcar e cancelar com visibilidade do histórico do paciente.</p>

<h3>2. Prontuário odontológico com histórico de procedimentos</h3>
<p>O prontuário para dentistas precisa registrar o que foi feito em cada sessão: procedimento realizado, dente(s) tratado(s), material utilizado, anestesia e orientações ao paciente. Um software odontológico que usa apenas campos de texto livre pode funcionar no início, mas se torna difícil de consultar à medida que o volume de pacientes cresce.</p>

<h3>3. Controle de pacientes com histórico de atendimentos</h3>
<p>Em odontologia, o histórico completo do paciente é consultado com frequência. Você precisa ver rapidamente quantas sessões foram realizadas, quais procedimentos foram concluídos e o que está pendente. O software para consultório odontológico deve tornar esse acesso imediato.</p>

<h3>4. Recepcionista pode agendar sem ver o prontuário</h3>
<p>Em uma clínica odontológica com recepcionista, a separação de acesso é importante: ela gerencia a agenda, você acessa o prontuário clínico. Confirme se o sistema permite essa divisão de permissões sem complicação.</p>

<h3>5. Acesso online, sem instalar no computador da clínica</h3>
<p>Um programa para clínica odontológica que requer instalação local cria dependência de manutenção técnica. Um sistema online permite acesso de qualquer computador ou celular — útil quando você atende em mais de um local ou quer acessar o prontuário fora do consultório.</p>

<h3>6. Preço justo para o porte da clínica</h3>
<p>Softwares odontológicos variam muito em preço. Para uma clínica com 1 dentista, planos entre R$ 79 e R$ 150/mês costumam cobrir bem a operação. Para clínicas com 2 a 5 dentistas, planos de R$ 199 a R$ 400/mês são mais adequados. Não pague por recursos de rede hospitalar se você tem um consultório.</p>

<h2>Quando vale migrar da planilha para um software odontológico?</h2>
<p>A migração faz sentido quando:</p>
<ul>
<li>Você tem mais de 40–50 pacientes ativos e o histórico está ficando difícil de consultar</li>
<li>A recepcionista gerencia a agenda separada do seu prontuário</li>
<li>Você quer controle de procedimentos por sessão sem depender de memória ou anotação em papel</li>
<li>Você tem mais de um dentista na clínica e precisa de agendas separadas</li>
</ul>

<h2>Perguntas frequentes sobre software para odontologia</h2>

<p><strong>Qual a diferença entre software para dentista autônomo e software para clínica odontológica?</strong><br>
Para dentista solo, um plano individual com 1 profissional e 1–2 colaboradores é suficiente. Para uma clínica com 2 a 5 dentistas, o plano precisa suportar múltiplos profissionais com agendas separadas e permissões por perfil.</p>

<p><strong>Sistema odontológico precisa ser específico para odontologia?</strong><br>
Não necessariamente. Um <a href="{BASE}software-para-clinicas-odontologicas">software para clínicas odontológicas</a> de uso geral pode atender bem desde que tenha prontuário flexível, controle de procedimentos por sessão e agenda com histórico de retornos.</p>

<p>Para mais opções comparativas, veja também o <a href="{BASE}sistema-para-dentistas">sistema para dentistas autônomos</a>.</p>
HTML;

		// ------------------------------------------------------------------
		// Artigo 3
		// ------------------------------------------------------------------
		$art3_conteudo = <<<HTML
<p>A busca por "software médico" reúne dois perfis muito diferentes: o médico autônomo que quer organizar seu consultório, e a clínica com múltiplos profissionais que precisa de gestão de equipe. Escolher o programa errado para o seu perfil significa pagar por recursos que não usa — ou não ter os recursos que precisa.</p>
<p>Este guia ajuda a identificar qual software médico faz sentido para o seu caso.</p>

<h2>Software médico para consultório: o perfil do médico autônomo</h2>
<p>O médico autônomo que atende no consultório precisa de um programa para consultório médico com foco em três coisas:</p>
<ul>
<li><strong>Agenda organizada</strong> — para ele e sua recepcionista agendarem consultas sem conflito</li>
<li><strong>Prontuário eletrônico</strong> — para registrar anamnese, evolução e conduta de cada consulta</li>
<li><strong>Controle de exames</strong> — para solicitar, receber resultados e manter o histórico do paciente</li>
</ul>
<p>Para esse perfil, um <a href="{BASE}sistema-para-consultorio-medico">sistema para consultório médico</a> com 1 profissional e 2 colaboradores (recepcionista) é suficiente. Plano a partir de R$ 79/mês, sem necessidade de recursos de gestão corporativa.</p>

<h2>Software médico para clínica: o perfil da clínica com equipe</h2>
<p>Uma clínica com 2 ou mais médicos tem necessidades adicionais que um plano de consultório não cobre:</p>
<ul>
<li>Agenda separada por médico, sem conflito de visualização</li>
<li>Controle hierárquico de equipe (médico, recepcionista, administrador)</li>
<li>Relatórios de produção por profissional</li>
<li>Escalabilidade: adicionar médicos sem migrar de sistema</li>
</ul>
<p>Para esse perfil, o <a href="{BASE}sistema-para-clinica-medica">sistema para clínica médica</a> com planos Clínica (até 5 médicos) ou Pro (até 20 médicos) é o caminho certo.</p>

<h2>Comparativo rápido: consultório vs clínica médica</h2>
<table>
<thead><tr><th>Característica</th><th>Consultório (1 médico)</th><th>Clínica médica (2+ médicos)</th></tr></thead>
<tbody>
<tr><td>Número de médicos</td><td>1</td><td>2 a 20</td></tr>
<tr><td>Colaboradores</td><td>Até 2</td><td>Até 50</td></tr>
<tr><td>Agenda por profissional</td><td>Não (agenda única)</td><td>Sim</td></tr>
<tr><td>Prontuário eletrônico</td><td>Sim</td><td>Sim</td></tr>
<tr><td>Relatórios de produção</td><td>Não</td><td>Sim</td></tr>
<tr><td>Preço mensal</td><td>R$ 79</td><td>R$ 199 a R$ 399</td></tr>
</tbody>
</table>

<h2>O que avaliar em qualquer software médico antes de contratar</h2>
<ol>
<li><strong>É online ou precisa de instalação?</strong> Prefira software médico online — sem manutenção técnica, backup automático e acesso de qualquer dispositivo.</li>
<li><strong>Prontuário e agenda estão integrados?</strong> Sem integração, você perde o histórico do paciente ao abrir o agendamento.</li>
<li><strong>A separação de acesso entre médico e recepcionista funciona?</strong> A recepcionista não deve ver prontuários clínicos.</li>
<li><strong>Tem trial real para testar?</strong> Demo com dados fictícios não representa como o sistema vai funcionar na sua rotina.</li>
<li><strong>O suporte resolve problemas de verdade?</strong> Avalie antes de contratar, não depois.</li>
</ol>

<h2>Qual o melhor software médico para consultório pequeno?</h2>
<p>Para médico autônomo com consultório pequeno (até 50 pacientes ativos, 1 recepcionista), as prioridades são: facilidade de uso, prontuário eletrônico incluso no plano, acesso online sem instalação e suporte acessível. Sistemas hospitalares ou plataformas voltadas para grandes redes são excessivos e caros para esse perfil.</p>
<p>Veja também: <a href="{BASE}software-para-medicos">software para médicos</a> — comparativo de funcionalidades por porte de consultório.</p>
HTML;

		// Substitui placeholder de base_url nos conteúdos
		$art1_conteudo = str_replace('{BASE}', $base, $art1_conteudo);
		$art2_conteudo = str_replace('{BASE}', $base, $art2_conteudo);
		$art3_conteudo = str_replace('{BASE}', $base, $art3_conteudo);

		return [
			[
				'titulo'         => 'Melhor software para clínicas: como avaliar em 2026',
				'slug'           => 'melhor-software-para-clinicas-como-avaliar',
				'resumo'         => '7 critérios práticos para escolher o melhor software para clínicas em 2026 — prontuário, agenda, custo por profissional e o que realmente importa antes de contratar.',
				'conteudo'       => $art1_conteudo,
				'meta_titulo'    => 'Melhor Software para Clínicas: 7 Critérios para Avaliar em 2026 | UTecnologia Saúde',
				'meta_descricao' => 'Como escolher o melhor software para clínicas em 2026: 7 critérios práticos — prontuário integrado, custo por profissional, acesso online e o que evitar na hora de contratar.',
				'autor'          => 'UTecnologia Saúde',
				'tempo_leitura'  => 7,
				'id_categoria'   => null,
			],
			[
				'titulo'         => 'Software para clínica odontológica: como escolher sem errar',
				'slug'           => 'software-para-clinica-odontologica-como-escolher',
				'resumo'         => 'Guia prático para escolher um software para clínica odontológica: agenda com retornos, prontuário odontológico, gestão de consultório e quando vale migrar da planilha.',
				'conteudo'       => $art2_conteudo,
				'meta_titulo'    => 'Software para Clínica Odontológica: Como Escolher Sem Errar | UTecnologia Saúde',
				'meta_descricao' => 'Como escolher um software para clínica odontológica: critérios práticos para agenda com retornos, prontuário de procedimentos, gestão de equipe e preço por porte de consultório.',
				'autor'          => 'UTecnologia Saúde',
				'tempo_leitura'  => 6,
				'id_categoria'   => null,
			],
			[
				'titulo'         => 'Software médico: como escolher para consultório ou clínica',
				'slug'           => 'software-medico-como-escolher-para-consultorio-ou-clinica',
				'resumo'         => 'Software médico para consultório ou clínica: o que avaliar, quais recursos importam e como escolher entre programa para consultório médico e sistema para clínica com equipe.',
				'conteudo'       => $art3_conteudo,
				'meta_titulo'    => 'Software Médico: Como Escolher para Consultório ou Clínica | UTecnologia Saúde',
				'meta_descricao' => 'Como escolher o software médico certo: diferenças entre programa para consultório médico e sistema para clínica médica com equipe, critérios de avaliação e comparativo de planos.',
				'autor'          => 'UTecnologia Saúde',
				'tempo_leitura'  => 6,
				'id_categoria'   => null,
			],
		];
	}
}
