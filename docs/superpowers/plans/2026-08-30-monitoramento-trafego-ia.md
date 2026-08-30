# Monitoramento de Tráfego de IA — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Capturar acessos ao site vindos de assistentes de IA, atribuir conversões (trial, assinatura, WhatsApp, contato) a essas origens e exibir tudo num dashboard administrativo com cards, gráficos e tabelas.

**Architecture:** Sem hooks e sem `MY_Controller` — a captura é um método novo em `Padrao_model` (`track_ai_referral()`) chamado ao lado de `indexador()` nos construtores de `Home.php` e `Blog.php`. Duas tabelas novas (`ai_referrals` first-touch + `ai_conversions` funil) criadas por um método idempotente em `adm/Dev.php`. Dashboard em controller novo `adm/Marketing` (nível 1), espelhando `adm/Leads`, com view no tema Adminto e Chart.js 2.9.3 já vendorizado.

**Tech Stack:** PHP 7, CodeIgniter 3.1.10, MySQL/MariaDB (`utf8mb4`), Bootstrap 4 / tema Adminto, Chart.js 2.9.3 (`bower_components/chart.js/dist/Chart.bundle.min.js`), PowerShell + `php -l` para validação (projeto não tem PHPUnit).

**Spec:** `docs/superpowers/specs/2026-08-30-monitoramento-trafego-ia-design.md`

---

## Mapa de arquivos

| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/config/ai_sources.php` | Criar | Lista configurável de fontes de IA (domínios + UTMs) + `ai_medium_flags` |
| `application/models/Padrao_model.php` | Modificar | `detect_ai_source()`, `ai_sources_list()`, `track_ai_referral()`, `_ai_set_cookie()`, `mark_ai_conversion()` (antes de `} // fecha class`, linha ~740) |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_monitoramento_ia()`, `purgar_monitoramento_ia()`, `testar_detector_ia()` + 3 links no `index()` |
| `application/controllers/Home.php` | Modificar | `track_ai_referral()` no construtor; `track_evento()`; `mark_ai_conversion()` em trial/assinatura/pagamentos |
| `application/controllers/Blog.php` | Modificar | `track_ai_referral()` no construtor |
| `application/models/adm/Marketing_model.php` | Criar | Queries agregadas: summary, by_source, landing_pages, conversion_by_source, timeline |
| `application/controllers/adm/Marketing.php` | Criar | Dashboard (nível 1): `index()`, `trafego_ia()`, `api($rel)` |
| `application/views/adm/marketing/trafego_ia.php` | Criar | View: cards + filtro de período + 2 gráficos Chart.js + 2 tabelas |
| `includes/adm/menu.php` | Modificar | Nova seção "Marketing" → "Trafego de IA" (só `$menu_can_admin`), após o bloco "Administracao" (linha ~57) |
| `application/config/routes.php` | Modificar | Rotas `adm/marketing*`, `adm/marketing/api/(:any)`, `e/track` |
| `application/views/index-front.php` | Modificar | Beacon JS antes de `</body>` (linha ~1373) |
| `application/views/public/contato.php` | Modificar | Mesmo beacon JS antes de `</body>` (linha ~177) |
| `CLAUDE.md` | Modificar | Seções 6.2, 8, 13 — registrar controller/rotas/view novos |

### Convenções observadas no projeto (seguir à risca)

- `Padrao_model` usa `<?` short-open e métodos procedurais sem palavra-chave de visibilidade (`function foo(){`).
- Migrações em `Dev.php` usam os helpers privados `run_sql($sql, $logs, $label)`, `ensure_column(...)`, `table_exists(...)` e imprimem um `<ul>` de log com `htmlspecialchars`.
- Controllers admin novos: `$this->load->model('adm/Usuarios_model'); $this->Usuarios_model->verSession();` + checagem `nivel != 1 → redirect('adm/atendimento')` (padrão de `adm/Leads.php`).
- Views admin incluem `includes/adm/search.php`, `includes/adm/menu.php`, `includes/adm/top.php` (caminhos relativos ao `index.php` da raiz) e usam `<body class="menu-position-side menu-side-left full-screen with-content-panel">`.
- `save_queries` fica como está. Não tocar em `system/`. Não migrar framework.

---

## Task 1: Config de fontes de IA

**Files:**
- Create: `application/config/ai_sources.php`

- [ ] **Step 1: Criar o arquivo de config**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -----------------------------------------------------------------------
| FONTES DE TRÁFEGO DE IA — usado por Padrao_model::detect_ai_source()
| -----------------------------------------------------------------------
| 'domains' casam contra o HTTP_REFERER (strpos, lowercase).
| 'utm'     casam contra utm_source (igualdade exata, lowercase).
|
| NÃO incluir buscadores (google.com, bing.com, x.com) em 'domains':
| tráfego de busca tradicional não é IA. Eles só contam via UTM explícita.
*/

$config['ai_sources'] = array(
    'chatgpt'    => array('domains' => array('chatgpt.com', 'openai.com'),         'utm' => array('chatgpt.com', 'chatgpt')),
    'gemini'     => array('domains' => array('gemini.google.com'),                 'utm' => array('gemini', 'gemini.google.com')),
    'claude'     => array('domains' => array('claude.ai'),                         'utm' => array('claude', 'claude.ai')),
    'perplexity' => array('domains' => array('perplexity.ai'),                     'utm' => array('perplexity', 'perplexity.ai')),
    'copilot'    => array('domains' => array('copilot.microsoft.com'),             'utm' => array('copilot')),
    'deepseek'   => array('domains' => array('chat.deepseek.com', 'deepseek.com'), 'utm' => array('deepseek')),
    'grok'       => array('domains' => array('grok.com'),                          'utm' => array('grok')),
);

/* utm_medium que sempre conta como IA (fonte 'outros' se nenhuma fonte específica casar) */
$config['ai_medium_flags'] = array('ai-assistant', 'ai_assistant', 'ai');
```

- [ ] **Step 2: Validar sintaxe**

Run:
```powershell
php -l application/config/ai_sources.php
```
Expected: `No syntax errors detected in application/config/ai_sources.php`

- [ ] **Step 3: Commit**

```bash
git add application/config/ai_sources.php
git commit -m "feat: config de fontes de trafego de IA"
```

---

## Task 2: Detector de IA + harness de teste

O detector é uma função pura (`referrer` + `utm[]` → resultado). O "teste" é uma rota de dev que roda os casos mínimos do documento e imprime PASS/FAIL — o projeto não tem PHPUnit.

**Files:**
- Modify: `application/models/Padrao_model.php` (inserir antes de `} // fecha class`, linha ~740)
- Modify: `application/controllers/adm/Dev.php` (novo método + link no `index()`)

- [ ] **Step 1: Escrever o teste que falha — `testar_detector_ia()` em `Dev.php`**

Inserir este método logo após `function index(){ ... }` (após a linha ~118):

```php
	function testar_detector_ia(){
		$this->load->model('padrao_model');

		$casos = array(
			array('nome' => 'ChatGPT via UTM',          'ref' => '',                                   'utm' => array('utm_source' => 'chatgpt.com'),  'esp' => array(true,  'chatgpt',    'utm')),
			array('nome' => 'ChatGPT via Referer',       'ref' => 'https://chatgpt.com/',               'utm' => array(),                               'esp' => array(true,  'chatgpt',    'referer')),
			array('nome' => 'Perplexity via Referer',    'ref' => 'https://www.perplexity.ai/',         'utm' => array(),                               'esp' => array(true,  'perplexity', 'referer')),
			array('nome' => 'Google Search tradicional', 'ref' => 'https://www.google.com/search?q=x',  'utm' => array(),                               'esp' => array(false, null,         null)),
			array('nome' => 'Acesso direto',            'ref' => '',                                    'utm' => array(),                               'esp' => array(false, null,         null)),
			array('nome' => 'utm_medium ai-assistant',   'ref' => '',                                   'utm' => array('utm_medium' => 'ai-assistant'), 'esp' => array(true,  'outros',     'utm')),
			array('nome' => 'Bing (nao e IA)',           'ref' => 'https://www.bing.com/search?q=x',    'utm' => array(),                               'esp' => array(false, null,         null)),
		);

		$falhas = 0;
		echo '<h2>Teste: detector de trafego de IA</h2>';
		echo '<table border="1" cellpadding="6" style="border-collapse:collapse;font-family:monospace;">';
		echo '<tr><th>#</th><th>Caso</th><th>Esperado [is_ai, source, method]</th><th>Obtido</th><th></th></tr>';
		foreach($casos as $i => $c){
			$r = $this->padrao_model->detect_ai_source($c['ref'], $c['utm']);
			$ok = ($r['is_ai'] === $c['esp'][0] && $r['source'] === $c['esp'][1] && ($c['esp'][2] === null || $r['method'] === $c['esp'][2]));
			if(!$ok){ $falhas++; }
			echo '<tr><td>'.($i + 1).'</td><td>'.htmlspecialchars($c['nome']).'</td>'
				.'<td>'.htmlspecialchars(json_encode($c['esp'])).'</td>'
				.'<td>'.htmlspecialchars(json_encode(array($r['is_ai'], $r['source'], $r['method']))).'</td>'
				.'<td style="font-weight:bold;color:'.($ok ? 'green' : 'red').'">'.($ok ? 'PASS' : 'FAIL').'</td></tr>';
		}
		echo '</table>';
		echo '<p><strong>'.(count($casos) - $falhas).'/'.count($casos).' PASS</strong></p>';
		echo '<p><a href="'.base_url().'adm/dev">Voltar ao Dev</a></p>';
	}
```

- [ ] **Step 2: Rodar o teste e confirmar que falha**

Run: abrir `http://localhost/utec/adm/dev/testar_detector_ia` logado como admin nível 1
Expected: erro fatal `Call to undefined method Padrao_model::detect_ai_source()` (o método ainda não existe).

- [ ] **Step 3: Implementar `detect_ai_source()` e `ai_sources_list()` em `Padrao_model.php`**

Inserir antes da linha `} // fecha class` (~740):

```php
function ai_sources_list(){
	$this->config->load('ai_sources', TRUE);
	$sources = $this->config->item('ai_sources', 'ai_sources');
	$list = is_array($sources) ? array_keys($sources) : array();
	$list[] = 'outros';
	return $list;
}

function detect_ai_source($referrer, $utm){
	$this->config->load('ai_sources', TRUE);
	$sources = $this->config->item('ai_sources', 'ai_sources');
	$medium_flags = $this->config->item('ai_medium_flags', 'ai_sources');
	if(!is_array($sources)){ $sources = array(); }
	if(!is_array($medium_flags)){ $medium_flags = array(); }
	if(!is_array($utm)){ $utm = array(); }

	$utm_source = isset($utm['utm_source']) ? strtolower(trim((string)$utm['utm_source'])) : '';
	$utm_medium = isset($utm['utm_medium']) ? strtolower(trim((string)$utm['utm_medium'])) : '';
	$ref = strtolower(trim((string)$referrer));

	// 1. UTM explicita (prioridade maior)
	if($utm_source !== ''){
		foreach($sources as $slug => $conf){
			$utms = (isset($conf['utm']) && is_array($conf['utm'])) ? $conf['utm'] : array();
			if(in_array($utm_source, $utms, true)){
				return array('is_ai' => true, 'source' => $slug, 'method' => 'utm');
			}
		}
	}
	if($utm_medium !== '' && in_array($utm_medium, $medium_flags, true)){
		return array('is_ai' => true, 'source' => 'outros', 'method' => 'utm');
	}

	// 2. Referer (strpos, nao comparacao exata — subdominios e caminhos)
	if($ref !== ''){
		foreach($sources as $slug => $conf){
			$domains = (isset($conf['domains']) && is_array($conf['domains'])) ? $conf['domains'] : array();
			foreach($domains as $domain){
				if($domain !== '' && strpos($ref, $domain) !== false){
					return array('is_ai' => true, 'source' => $slug, 'method' => 'referer');
				}
			}
		}
	}

	// 3. Extensao futura para identificadores proprios de IA — vazio nesta fase.

	return array('is_ai' => false, 'source' => null, 'method' => null);
}
```

- [ ] **Step 4: Rodar o teste e confirmar 7/7 PASS**

Run: recarregar `http://localhost/utec/adm/dev/testar_detector_ia`
Expected: tabela com 7 linhas verdes e `7/7 PASS`.

- [ ] **Step 5: Adicionar links no `index()` do `Dev.php`**

Em `function index(){`, antes de `echo '</ul>';`, adicionar:

```php
		echo '<li><a href="'.base_url().'adm/dev/migrar_monitoramento_ia">Migrar monitoramento de trafego de IA (tabelas)</a></li>';
		echo '<li><a href="'.base_url().'adm/dev/testar_detector_ia">Testar detector de trafego de IA</a></li>';
		echo '<li><a href="'.base_url().'adm/dev/purgar_monitoramento_ia">Purgar monitoramento de IA (>18 meses)</a></li>';
```

- [ ] **Step 6: Validar sintaxe**

Run:
```powershell
php -l application/models/Padrao_model.php
php -l application/controllers/adm/Dev.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 7: Commit**

```bash
git add application/models/Padrao_model.php application/controllers/adm/Dev.php
git commit -m "feat: detector de trafego de IA + harness de teste em Dev"
```

---

## Task 3: Migração das tabelas `ai_referrals` e `ai_conversions`

**Files:**
- Modify: `application/controllers/adm/Dev.php` (2 métodos novos)

- [ ] **Step 1: Implementar `migrar_monitoramento_ia()` em `Dev.php`**

Adicionar após `testar_detector_ia()`:

```php
	function migrar_monitoramento_ia(){
		$logs = array();

		if($this->input->get('desfazer') == '1'){
			$this->run_sql("DROP TABLE IF EXISTS `ai_conversions`", $logs, 'DROP `ai_conversions`');
			$this->run_sql("DROP TABLE IF EXISTS `ai_referrals`", $logs, 'DROP `ai_referrals`');
			echo '<h2>Monitoramento de IA — desfazer</h2><ul>';
			foreach($logs as $log){ echo '<li>'.htmlspecialchars($log).'</li>'; }
			echo '</ul><p><a href="'.base_url().'adm/dev">Voltar ao Dev</a></p>';
			return;
		}

		$sql_referrals = "CREATE TABLE IF NOT EXISTS `ai_referrals` (
			`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`session_id` VARCHAR(100) NULL,
			`ai_source` VARCHAR(50) NULL,
			`detection_method` VARCHAR(20) NULL,
			`landing_page` VARCHAR(500) NULL,
			`request_uri` VARCHAR(500) NULL,
			`referrer` VARCHAR(500) NULL,
			`utm_source` VARCHAR(255) NULL,
			`utm_medium` VARCHAR(255) NULL,
			`utm_campaign` VARCHAR(255) NULL,
			`utm_content` VARCHAR(255) NULL,
			`utm_term` VARCHAR(255) NULL,
			`user_agent` VARCHAR(400) NULL,
			`ip_hash` VARCHAR(64) NULL,
			`id_user` INT NULL,
			`converted` TINYINT(1) NOT NULL DEFAULT 0,
			`conversion_type` VARCHAR(50) NULL,
			`conversion_value` DECIMAL(12,2) NULL,
			`converted_at` DATETIME NULL,
			`created_at` DATETIME NOT NULL,
			KEY `idx_air_source` (`ai_source`),
			KEY `idx_air_created` (`created_at`),
			KEY `idx_air_session` (`session_id`),
			KEY `idx_air_converted` (`converted`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_referrals, $logs, 'tabela `ai_referrals`');

		$sql_conversions = "CREATE TABLE IF NOT EXISTS `ai_conversions` (
			`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`ai_referral_id` BIGINT UNSIGNED NOT NULL,
			`session_id` VARCHAR(100) NULL,
			`ai_source` VARCHAR(50) NULL,
			`conversion_type` VARCHAR(50) NOT NULL,
			`conversion_value` DECIMAL(12,2) NULL,
			`reference_id` VARCHAR(100) NULL,
			`meta` VARCHAR(500) NULL,
			`created_at` DATETIME NOT NULL,
			KEY `idx_aic_referral` (`ai_referral_id`),
			KEY `idx_aic_source` (`ai_source`),
			KEY `idx_aic_type` (`conversion_type`),
			KEY `idx_aic_created` (`created_at`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		$this->run_sql($sql_conversions, $logs, 'tabela `ai_conversions`');

		// Politica de retencao sugerida: remover registros > 18 meses.
		// Rodar manualmente quando necessario: adm/dev/purgar_monitoramento_ia

		echo '<h2>Migracao — Monitoramento de Trafego de IA</h2><ul>';
		foreach($logs as $log){ echo '<li>'.htmlspecialchars($log).'</li>'; }
		echo '</ul>';
		echo '<p><a href="'.base_url().'adm/marketing/trafego_ia">Abrir dashboard de Trafego de IA</a> &middot; ';
		echo '<a href="'.base_url().'adm/dev/migrar_monitoramento_ia?desfazer=1" onclick="return confirm(\'Remover as tabelas ai_referrals e ai_conversions?\')">desfazer</a></p>';
	}

	function purgar_monitoramento_ia(){
		$meses = (int)$this->input->get('meses');
		if($meses <= 0){ $meses = 18; }
		$logs = array();
		if($this->db->table_exists('ai_conversions')){
			$this->run_sql("DELETE FROM `ai_conversions` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL ".$meses." MONTH)", $logs, 'purge ai_conversions > '.$meses.' meses');
		}
		if($this->db->table_exists('ai_referrals')){
			$this->run_sql("DELETE FROM `ai_referrals` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL ".$meses." MONTH)", $logs, 'purge ai_referrals > '.$meses.' meses');
		}
		echo '<h2>Purga — Monitoramento de IA</h2><ul>';
		foreach($logs as $log){ echo '<li>'.htmlspecialchars($log).'</li>'; }
		echo '</ul><p><a href="'.base_url().'adm/dev">Voltar ao Dev</a></p>';
	}
```

- [ ] **Step 2: Validar sintaxe**

Run:
```powershell
php -l application/controllers/adm/Dev.php
```
Expected: `No syntax errors detected in application/controllers/adm/Dev.php`

- [ ] **Step 3: Rodar a migração**

Run: abrir `http://localhost/utec/adm/dev/migrar_monitoramento_ia` logado como admin nível 1
Expected: lista com `OK: tabela ai_referrals` e `OK: tabela ai_conversions`.

- [ ] **Step 4: Conferir as tabelas no banco**

Run:
```powershell
php -r "$m=new mysqli('localhost','root','','utecnologiacom_db'); $r=$m->query('SHOW TABLES LIKE \"ai_%\"'); while($x=$r->fetch_row()) echo $x[0].PHP_EOL;"
```
Expected:
```text
ai_conversions
ai_referrals
```
(Se as credenciais locais forem outras, conferir via phpMyAdmin: as duas tabelas devem existir.)

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Dev.php
git commit -m "feat: migracao das tabelas ai_referrals e ai_conversions"
```

---

## Task 4: Tracker de referral e função de conversão em `Padrao_model`

**Files:**
- Modify: `application/models/Padrao_model.php` (3 métodos novos, antes de `} // fecha class`)
- Modify: `application/controllers/Home.php` (linha 13, construtor)
- Modify: `application/controllers/Blog.php` (linha 10, construtor)

- [ ] **Step 1: Implementar `track_ai_referral()`, `_ai_set_cookie()` e `mark_ai_conversion()` em `Padrao_model.php`**

Inserir antes de `} // fecha class` (depois de `detect_ai_source()`):

```php
function _ai_set_cookie($id, $source){
	$this->load->helper('cookie');
	set_cookie(array(
		'name'   => 'utec_air',
		'value'  => (int)$id.'|'.preg_replace('/[^a-z0-9_\-]/i', '', (string)$source),
		'expire' => 60 * 60 * 24 * 90,
		'path'   => '/',
	));
}

function track_ai_referral(){
	if(!$this->db->table_exists('ai_referrals')){
		return;
	}
	$this->load->helper(array('url', 'cookie'));

	// Ja atribuido nesta sessao? garante cookie e sai.
	$existing_id = (int)$this->session->userdata('ai_referral_id');
	if($existing_id > 0){
		$this->_ai_set_cookie($existing_id, (string)$this->session->userdata('ai_source'));
		return;
	}

	$utm = array(
		'utm_source'   => $this->input->get('utm_source'),
		'utm_medium'   => $this->input->get('utm_medium'),
		'utm_campaign' => $this->input->get('utm_campaign'),
		'utm_content'  => $this->input->get('utm_content'),
		'utm_term'     => $this->input->get('utm_term'),
	);
	$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

	$r = $this->detect_ai_source($referrer, $utm);
	if(empty($r['is_ai'])){
		return;
	}

	$session_id = (string)$this->session->session_id;

	// Ja existe linha para esta sessao? recupera e sai (nunca 2x por sessao).
	$prev = $this->db->query("SELECT id, ai_source FROM ai_referrals WHERE session_id = ? ORDER BY id DESC LIMIT 1", array($session_id));
	if($prev->num_rows()){
		$row = $prev->row();
		$this->session->set_userdata(array('ai_referral_id' => (int)$row->id, 'ai_source' => $row->ai_source));
		$this->_ai_set_cookie((int)$row->id, $row->ai_source);
		return;
	}

	$cut = function($v, $len){
		$v = (string)$v;
		return $v === '' ? null : mb_substr($v, 0, $len);
	};
	$app_key = getenv('APP_SECRET') ?: (getenv('MERCADOPAGO_WEBHOOK_SECRET') ?: 'utec-ai-salt');
	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	$qs = (string)$this->input->server('QUERY_STRING');

	$data = array(
		'session_id'       => $cut($session_id, 100),
		'ai_source'        => $r['source'],
		'detection_method' => $r['method'],
		'landing_page'     => $cut(current_url().($qs !== '' ? '?'.$qs : ''), 500),
		'request_uri'      => $cut($this->input->server('REQUEST_URI'), 500),
		'referrer'         => $cut($referrer, 500),
		'utm_source'       => $cut($utm['utm_source'], 255),
		'utm_medium'       => $cut($utm['utm_medium'], 255),
		'utm_campaign'     => $cut($utm['utm_campaign'], 255),
		'utm_content'      => $cut($utm['utm_content'], 255),
		'utm_term'         => $cut($utm['utm_term'], 255),
		'user_agent'       => $cut($this->input->user_agent(), 400),
		'ip_hash'          => $ip !== '' ? hash('sha256', $ip.$app_key) : null,
		'id_user'          => ((int)$this->session->userdata('id')) ?: null,
		'converted'        => 0,
		'created_at'       => date('Y-m-d H:i:s'),
	);

	try {
		$this->db->insert('ai_referrals', $data);
		$id = (int)$this->db->insert_id();
		if($id > 0){
			$this->session->set_userdata(array('ai_referral_id' => $id, 'ai_source' => $r['source']));
			$this->_ai_set_cookie($id, $r['source']);
		}
	} catch (Exception $e) {
		log_message('error', 'track_ai_referral: '.$e->getMessage());
	}
}

function mark_ai_conversion($type, $value = null, $reference_id = null, $meta = null){
	if(!$this->db->table_exists('ai_conversions')){
		return;
	}

	$ref_id = (int)$this->session->userdata('ai_referral_id');
	$ai_source = (string)$this->session->userdata('ai_source');

	// Re-hidrata do cookie se a sessao perdeu o vinculo (conversao cross-session).
	if($ref_id <= 0){
		$this->load->helper('cookie');
		$cookie = (string)get_cookie('utec_air');
		if($cookie !== '' && strpos($cookie, '|') !== false && $this->db->table_exists('ai_referrals')){
			list($cid, $csrc) = explode('|', $cookie, 2);
			$cid = (int)$cid;
			if($cid > 0){
				$chk = $this->db->query("SELECT id, ai_source FROM ai_referrals WHERE id = ? LIMIT 1", array($cid));
				if($chk->num_rows()){
					$ref_id = $cid;
					$ai_source = $chk->row()->ai_source;
					$this->session->set_userdata(array('ai_referral_id' => $ref_id, 'ai_source' => $ai_source));
				}
			}
		}
	}

	if($ref_id <= 0){
		return; // visita nao atribuida a IA — nada a fazer
	}

	try {
		// Dedup por reference_id (evita dupla contagem em retentativas de pagamento).
		if($reference_id !== null && $reference_id !== ''){
			$dup = $this->db->query("SELECT id FROM ai_conversions WHERE reference_id = ? LIMIT 1", array((string)$reference_id));
			if($dup->num_rows()){
				return;
			}
		}

		$val = ($value === null || $value === '') ? null : (float)$value;

		$this->db->insert('ai_conversions', array(
			'ai_referral_id'   => $ref_id,
			'session_id'       => mb_substr((string)$this->session->session_id, 0, 100),
			'ai_source'        => $ai_source !== '' ? $ai_source : null,
			'conversion_type'  => mb_substr((string)$type, 0, 50),
			'conversion_value' => $val,
			'reference_id'     => $reference_id !== null ? mb_substr((string)$reference_id, 0, 100) : null,
			'meta'             => $meta !== null ? mb_substr((string)$meta, 0, 500) : null,
			'created_at'       => date('Y-m-d H:i:s'),
		));

		// First-touch em ai_referrals — so na 1a conversao, nao sobrescreve.
		$cur = $this->db->query("SELECT converted FROM ai_referrals WHERE id = ? LIMIT 1", array($ref_id));
		if($cur->num_rows() && (int)$cur->row()->converted === 0){
			$this->db->where('id', $ref_id);
			$this->db->update('ai_referrals', array(
				'converted'        => 1,
				'conversion_type'  => mb_substr((string)$type, 0, 50),
				'conversion_value' => $val,
				'converted_at'     => date('Y-m-d H:i:s'),
			));
		}
	} catch (Exception $e) {
		log_message('error', 'mark_ai_conversion: '.$e->getMessage());
	}
}
```

- [ ] **Step 2: Chamar o tracker no construtor de `Home.php`**

Em `application/controllers/Home.php`, linha 13. Trocar:
```php
		$this->padrao_model->indexador();
	}
```
Por:
```php
		$this->padrao_model->indexador();
		$this->padrao_model->track_ai_referral();
	}
```

- [ ] **Step 3: Chamar o tracker no construtor de `Blog.php`**

Em `application/controllers/Blog.php`, linha 10. Trocar:
```php
        $this->padrao_model->indexador();
    }
```
Por:
```php
        $this->padrao_model->indexador();
        $this->padrao_model->track_ai_referral();
    }
```

- [ ] **Step 4: Validar sintaxe**

Run:
```powershell
php -l application/models/Padrao_model.php
php -l application/controllers/Home.php
php -l application/controllers/Blog.php
```
Expected: `No syntax errors detected` nos três.

- [ ] **Step 5: Smoke test — gravar um referral de IA**

Run: abrir `http://localhost/utec/?utm_source=chatgpt.com` no navegador, depois:
```powershell
php -r "$m=new mysqli('localhost','root','','utecnologiacom_db'); $r=$m->query('SELECT ai_source,detection_method,landing_page,converted FROM ai_referrals ORDER BY id DESC LIMIT 1'); print_r($r->fetch_assoc());"
```
Expected: uma linha com `ai_source = chatgpt`, `detection_method = utm`, `converted = 0`.

- [ ] **Step 6: Smoke test — recarregar não duplica**

Run: recarregar `http://localhost/utec/` duas vezes, depois:
```powershell
php -r "$m=new mysqli('localhost','root','','utecnologiacom_db'); $r=$m->query('SELECT COUNT(*) c FROM ai_referrals'); print_r($r->fetch_assoc());"
```
Expected: `c` continua `1` (guard de sessão + guard de `session_id` funcionando).

- [ ] **Step 7: Commit**

```bash
git add application/models/Padrao_model.php application/controllers/Home.php application/controllers/Blog.php
git commit -m "feat: captura de referral de IA e atribuicao de conversao (Padrao_model)"
```

---

## Task 5: Instrumentar conversões existentes + endpoint de beacon

**Files:**
- Modify: `application/controllers/Home.php` (5 pontos: trial, contratar, cartão, status, novo método `track_evento`)
- Modify: `application/config/routes.php` (rota `e/track`)

### Convenção de `reference_id` (dedup)

- `trial_<subscription_id>` — trial criado
- `sub_<subscription_id>` — assinatura iniciada em `contratar()` (valor `null` — é intenção, não receita)
- `pay_<payment_id>` — pagamento confirmado (valor real → conta como receita)

"Conversões IA" no dashboard vem de `ai_referrals.converted` (first-touch, sem dupla contagem). "Receita" soma só `ai_conversions.conversion_value > 0`.

- [ ] **Step 1: Conversão de trial em `iniciar_experiencia()`**

Em `application/controllers/Home.php`, logo após a linha `$this->_enviar_email_boas_vindas($result);` (~127), adicionar:

```php
		// Atribuicao de trafego de IA — trial criado
		$this->padrao_model->mark_ai_conversion('trial', null, 'trial_'.(int)$result['subscription_id'], 'plano:'.(string)$this->input->post('plano_id'));
```

- [ ] **Step 2: Conversão de assinatura iniciada em `contratar()`**

Em `application/controllers/Home.php`, logo após `$this->session->set_flashdata('fb_sub_event_id', $ev_id_sub);` (~216), adicionar:

```php
		// Atribuicao de trafego de IA — assinatura iniciada (sem valor: intencao, nao receita)
		$this->padrao_model->mark_ai_conversion('assinatura', null, 'sub_'.(int)$result['subscription_id'], 'etapa:cadastro');
```

- [ ] **Step 3: Conversão de pagamento confirmado em `assinatura_pagamento_cartao()`**

Em `application/controllers/Home.php`, dentro do `if(in_array(... ['approved', 'authorized'])){` que chama `register_cycle_payment` (~379-381), logo após a linha do `register_cycle_payment(...)`, adicionar:

```php
				$this->padrao_model->mark_ai_conversion('assinatura', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'pay_'.(isset($payment['id']) ? $payment['id'] : (int)$detail['subscription']->id), 'via:cartao');
```

- [ ] **Step 4: Conversão de pagamento confirmado em `assinatura_pagamento_status()`**

Em `application/controllers/Home.php`, dentro do `if(in_array(... ['approved', 'authorized'])){` (~440-441), logo após a linha do `register_cycle_payment(...)`, adicionar:

```php
				$this->padrao_model->mark_ai_conversion('assinatura', isset($payment['transaction_amount']) ? (float)$payment['transaction_amount'] : null, 'pay_'.(isset($payment['id']) ? $payment['id'] : (int)$detail['subscription']->id), 'via:sync');
```

- [ ] **Step 5: Novo método `track_evento()` em `Home.php`**

Adicionar como método público novo (por exemplo logo após `contato()`, ~674):

```php
	public function track_evento()
	{
		$tipo = strtolower(trim((string)$this->input->get('t')));
		$permitidos = array('whatsapp', 'contato');
		if(!in_array($tipo, $permitidos, true)){
			$this->output->set_status_header(204);
			return;
		}

		// Rate-limit simples: 1 evento do mesmo tipo por sessao a cada 60s.
		$flagkey = 'ai_evt_'.$tipo;
		$last = (int)$this->session->userdata($flagkey);
		if($last > 0 && (time() - $last) < 60){
			$this->output->set_status_header(204);
			return;
		}
		$this->session->set_userdata($flagkey, time());

		$origem = preg_replace('/[^a-z0-9_\-\/]/i', '', (string)$this->input->get('o'));
		$this->padrao_model->mark_ai_conversion($tipo, null, null, $origem !== '' ? 'origem:'.mb_substr($origem, 0, 80) : null);

		$this->output->set_status_header(204);
	}
```

- [ ] **Step 6: Rota `e/track` em `routes.php`**

Em `application/config/routes.php`, logo após a linha `$route['webhooks/mercadopago'] = 'adm/saas/webhook_mercadopago';` (~71), adicionar:

```php
$route['e/track'] = 'home/track_evento';
```

- [ ] **Step 7: Validar sintaxe**

Run:
```powershell
php -l application/controllers/Home.php
php -l application/config/routes.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 8: Smoke test do beacon**

Run: no navegador com a sessão que veio de `?utm_source=chatgpt.com` (Task 4), acessar `http://localhost/utec/e/track?t=whatsapp&o=btn-wa-header`, depois:
```powershell
php -r "$m=new mysqli('localhost','root','','utecnologiacom_db'); $r=$m->query('SELECT conversion_type,ai_source,meta FROM ai_conversions ORDER BY id DESC LIMIT 1'); print_r($r->fetch_assoc());"
```
Expected: linha com `conversion_type = whatsapp`, `ai_source = chatgpt`, `meta = origem:btn-wa-header`. Verificar também que `ai_referrals.converted` da linha virou `1`.

- [ ] **Step 9: Commit**

```bash
git add application/controllers/Home.php application/config/routes.php
git commit -m "feat: instrumentar conversoes de IA (trial, assinatura, pagamento, beacon e/track)"
```

---

## Task 6: Beacon JS nas páginas públicas

**Files:**
- Modify: `application/views/index-front.php` (antes de `</body>`, ~1373)
- Modify: `application/views/public/contato.php` (antes de `</body>`, ~177)

- [ ] **Step 1: Adicionar o script em `index-front.php`**

Imediatamente antes de `</body>` (linha ~1373), inserir:

```html
<script>
(function () {
    var TRACK = '<?=base_url()?>e/track';
    function ping(t, o) {
        try {
            var url = TRACK + '?t=' + encodeURIComponent(t) + (o ? '&o=' + encodeURIComponent(o) : '');
            if (navigator.sendBeacon) { navigator.sendBeacon(url); }
            else { (new Image()).src = url; }
        } catch (e) {}
    }
    document.addEventListener('click', function (ev) {
        var a = ev.target.closest ? ev.target.closest('a[href*="wa.me"], a[href*="api.whatsapp.com"]') : null;
        if (a) { ping('whatsapp', (a.className || '').split(' ')[0] || 'link'); }
    }, true);
    var form = document.querySelector('form[data-track="contato"]');
    if (form) { form.addEventListener('submit', function () { ping('contato', 'form'); }); }
})();
</script>
```

- [ ] **Step 2: Adicionar o mesmo script em `public/contato.php`**

Imediatamente antes de `</body>` (linha ~177), inserir o **mesmo bloco `<script>` do Step 1**:

```html
<script>
(function () {
    var TRACK = '<?=base_url()?>e/track';
    function ping(t, o) {
        try {
            var url = TRACK + '?t=' + encodeURIComponent(t) + (o ? '&o=' + encodeURIComponent(o) : '');
            if (navigator.sendBeacon) { navigator.sendBeacon(url); }
            else { (new Image()).src = url; }
        } catch (e) {}
    }
    document.addEventListener('click', function (ev) {
        var a = ev.target.closest ? ev.target.closest('a[href*="wa.me"], a[href*="api.whatsapp.com"]') : null;
        if (a) { ping('whatsapp', (a.className || '').split(' ')[0] || 'link'); }
    }, true);
    var form = document.querySelector('form[data-track="contato"]');
    if (form) { form.addEventListener('submit', function () { ping('contato', 'form'); }); }
})();
</script>
```

- [ ] **Step 3: Validar sintaxe**

Run:
```powershell
php -l application/views/index-front.php
php -l application/views/public/contato.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 4: Smoke test no navegador**

Run: abrir `http://localhost/utec/` (DevTools → aba Network, filtro `track`), clicar no botão flutuante de WhatsApp. Confirmar request `e/track?t=whatsapp&o=wa-float` com status `204`.

- [ ] **Step 5: Commit**

```bash
git add application/views/index-front.php application/views/public/contato.php
git commit -m "feat: beacon de conversao WhatsApp/contato nas paginas publicas"
```

---

## Task 7: Model de relatórios `Marketing_model`

**Files:**
- Create: `application/models/adm/Marketing_model.php`

- [ ] **Step 1: Criar `application/models/adm/Marketing_model.php`**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('padrao_model');
	}

	private function normaliza_filtros($f){
		if(!is_array($f)){ $f = array(); }
		$hoje = date('Y-m-d');
		$ini  = isset($f['start_date']) ? (string)$f['start_date'] : '';
		$fim  = isset($f['end_date'])   ? (string)$f['end_date']   : '';

		$d1 = DateTime::createFromFormat('Y-m-d', $ini);
		$d2 = DateTime::createFromFormat('Y-m-d', $fim);
		if(!$d1 || $d1->format('Y-m-d') !== $ini){ $ini = date('Y-m-d', strtotime('-29 days')); }
		if(!$d2 || $d2->format('Y-m-d') !== $fim){ $fim = $hoje; }
		if($ini > $fim){ $tmp = $ini; $ini = $fim; $fim = $tmp; }

		return array(
			'start'        => $ini.' 00:00:00',
			'end'          => $fim.' 23:59:59',
			'start_date'   => $ini,
			'end_date'     => $fim,
			'source'       => isset($f['source']) ? preg_replace('/[^a-z0-9_\-]/i', '', (string)$f['source']) : '',
			'landing_page' => isset($f['landing_page']) ? (string)$f['landing_page'] : '',
			'converted'    => (isset($f['converted']) && $f['converted'] !== '') ? (int)(bool)$f['converted'] : null,
		);
	}

	private function aplica_where($f){
		$this->db->where('r.created_at >=', $f['start']);
		$this->db->where('r.created_at <=', $f['end']);
		if($f['source'] !== ''){ $this->db->where('r.ai_source', $f['source']); }
		if($f['landing_page'] !== ''){ $this->db->like('r.landing_page', $f['landing_page']); }
		if($f['converted'] !== null){ $this->db->where('r.converted', $f['converted']); }
	}

	private function count_since($ts){
		$this->db->from('ai_referrals');
		$this->db->where('created_at >=', $ts);
		return $this->db->count_all_results();
	}

	public function get_summary($filtros){
		$f = $this->normaliza_filtros($filtros);

		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$acessos = $this->db->count_all_results();

		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->where('r.converted', 1);
		$conversoes = $this->db->count_all_results();

		$this->db->select('COALESCE(SUM(c.conversion_value),0) AS receita', false);
		$this->db->from('ai_conversions c');
		$this->db->where('c.created_at >=', $f['start']);
		$this->db->where('c.created_at <=', $f['end']);
		$this->db->where('c.conversion_type', 'assinatura');
		$this->db->where('c.conversion_value >', 0);
		if($f['source'] !== ''){ $this->db->where('c.ai_source', $f['source']); }
		$receita = (float)$this->db->get()->row()->receita;

		return array(
			'acessos_hoje'    => $this->count_since(date('Y-m-d').' 00:00:00'),
			'acessos_7d'      => $this->count_since(date('Y-m-d 00:00:00', strtotime('-6 days'))),
			'acessos_30d'     => $this->count_since(date('Y-m-d 00:00:00', strtotime('-29 days'))),
			'acessos_periodo' => $acessos,
			'conversoes'      => $conversoes,
			'taxa_conversao'  => $acessos > 0 ? round($conversoes / $acessos * 100, 2) : 0,
			'receita'         => $receita,
			'periodo'         => array($f['start_date'], $f['end_date']),
		);
	}

	public function get_by_source($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('r.ai_source, COUNT(*) AS total', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.ai_source');
		$this->db->order_by('total', 'DESC');
		$rows = $this->db->get()->result();

		$mapa = array();
		foreach($rows as $row){ $mapa[$row->ai_source ? $row->ai_source : 'outros'] = (int)$row->total; }

		$saida = array();
		foreach($this->padrao_model->ai_sources_list() as $slug){
			$saida[] = array('source' => $slug, 'total' => isset($mapa[$slug]) ? $mapa[$slug] : 0);
		}
		return $saida;
	}

	public function get_landing_pages($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('r.landing_page, COUNT(*) AS acessos, SUM(r.converted) AS conversoes', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.landing_page');
		$this->db->order_by('acessos', 'DESC');
		$this->db->limit(50);
		return $this->db->get()->result();
	}

	public function get_conversion_by_source($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select("r.ai_source,
			COUNT(*) AS visitas,
			SUM(r.converted) AS conversoes,
			ROUND(SUM(r.converted) / COUNT(*) * 100, 2) AS taxa,
			COALESCE(SUM(r.conversion_value),0) AS receita", false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.ai_source');
		$this->db->order_by('visitas', 'DESC');
		return $this->db->get()->result();
	}

	public function get_timeline($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('DATE(r.created_at) AS dia, COUNT(*) AS acessos, SUM(r.converted) AS conversoes', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('dia');
		$this->db->order_by('dia', 'ASC');
		$rows = $this->db->get()->result();

		$mapa = array();
		foreach($rows as $row){ $mapa[$row->dia] = $row; }

		$saida = array();
		$cursor = strtotime($f['start_date']);
		$limite = strtotime($f['end_date']);
		while($cursor <= $limite){
			$d = date('Y-m-d', $cursor);
			$saida[] = array(
				'dia'        => $d,
				'acessos'    => isset($mapa[$d]) ? (int)$mapa[$d]->acessos : 0,
				'conversoes' => isset($mapa[$d]) ? (int)$mapa[$d]->conversoes : 0,
			);
			$cursor = strtotime('+1 day', $cursor);
		}
		return $saida;
	}
}
```

- [ ] **Step 2: Validar sintaxe**

Run:
```powershell
php -l application/models/adm/Marketing_model.php
```
Expected: `No syntax errors detected in application/models/adm/Marketing_model.php`

- [ ] **Step 3: Commit**

```bash
git add application/models/adm/Marketing_model.php
git commit -m "feat: Marketing_model com queries agregadas de trafego de IA"
```

---

## Task 8: Controller `adm/Marketing` + rotas + item de menu

**Files:**
- Create: `application/controllers/adm/Marketing.php`
- Modify: `application/config/routes.php`
- Modify: `includes/adm/menu.php`

- [ ] **Step 1: Criar `application/controllers/adm/Marketing.php`**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->model('padrao_model');
		$this->load->model('adm/Marketing_model');
		$this->load->model('adm/Usuarios_model');
		$this->Usuarios_model->verSession();

		$usuario = $this->padrao_model->get_usuario_logado();
		if(!$usuario || (int)$usuario->nivel !== 1){
			redirect('adm/atendimento');
		}
	}

	public function index(){
		$this->trafego_ia();
	}

	public function trafego_ia(){
		$filtros = $this->_filtros_from_get();
		$dados = array(
			'summary'     => $this->Marketing_model->get_summary($filtros),
			'sources'     => $this->Marketing_model->get_by_source($filtros),
			'pages'       => $this->Marketing_model->get_landing_pages($filtros),
			'conv'        => $this->Marketing_model->get_conversion_by_source($filtros),
			'timeline'    => $this->Marketing_model->get_timeline($filtros),
			'filtros_raw' => $filtros,
			'schema_ok'   => $this->db->table_exists('ai_referrals') && $this->db->table_exists('ai_conversions'),
		);
		$this->load->view('adm/marketing/trafego_ia', $dados);
	}

	public function api($rel = ''){
		$filtros = $this->_filtros_from_get();
		switch($rel){
			case 'summary':     $out = $this->Marketing_model->get_summary($filtros); break;
			case 'sources':     $out = $this->Marketing_model->get_by_source($filtros); break;
			case 'pages':       $out = $this->Marketing_model->get_landing_pages($filtros); break;
			case 'conversions': $out = $this->Marketing_model->get_conversion_by_source($filtros); break;
			case 'timeline':    $out = $this->Marketing_model->get_timeline($filtros); break;
			default:
				$this->output->set_status_header(404)->set_content_type('application/json')
					->set_output(json_encode(array('error' => 'relatorio invalido')));
				return;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	private function _filtros_from_get(){
		return array(
			'start_date'   => $this->input->get('start_date'),
			'end_date'     => $this->input->get('end_date'),
			'source'       => $this->input->get('source'),
			'landing_page' => $this->input->get('landing_page'),
			'converted'    => $this->input->get('converted'),
		);
	}
}
```

- [ ] **Step 2: Adicionar as rotas em `application/config/routes.php`**

Logo após a linha `$route['e/track'] = 'home/track_evento';` (adicionada na Task 5), inserir:

```php

// Marketing — Trafego de IA
$route['adm/marketing']            = 'adm/marketing/index';
$route['adm/marketing/trafego_ia'] = 'adm/marketing/trafego_ia';
$route['adm/marketing/api/(:any)'] = 'adm/marketing/api/$1';
```

- [ ] **Step 3: Adicionar a seção de menu em `includes/adm/menu.php`**

O bloco `if($menu_can_admin){ $menu_sections[] = ['title' => 'Administracao', ... ]; }` termina por volta da linha 57 (fecha com `\t];\n}`). Imediatamente após esse `}`, inserir:

```php

if($menu_can_admin){
	$menu_sections[] = [
		'title' => 'Marketing',
		'items' => [
			[
				'label' => 'Trafego de IA',
				'icon' => 'os-icon-signal',
				'url' => base_url().'adm/marketing/trafego_ia',
				'children' => [
					['label' => 'Visao geral', 'url' => base_url().'adm/marketing/trafego_ia'],
				],
			],
		],
	];
}
```

- [ ] **Step 4: Validar sintaxe**

Run:
```powershell
php -l application/controllers/adm/Marketing.php
php -l application/config/routes.php
php -l includes/adm/menu.php
```
Expected: `No syntax errors detected` nos três.

- [ ] **Step 5: Smoke test dos endpoints JSON**

Run: logado como admin nível 1, abrir no navegador:
- `http://localhost/utec/adm/marketing/api/summary`
- `http://localhost/utec/adm/marketing/api/timeline`

Expected: JSON válido (não HTML de erro). `summary` traz as chaves `acessos_hoje`, `conversoes`, `taxa_conversao`, `receita`, `periodo`. `timeline` traz um array de `{dia, acessos, conversoes}` com um item por dia dos últimos 30 dias.

- [ ] **Step 6: Commit**

```bash
git add application/controllers/adm/Marketing.php application/config/routes.php includes/adm/menu.php
git commit -m "feat: controller adm/Marketing, rotas e item de menu Trafego de IA"
```

---

## Task 9: View do dashboard

**Files:**
- Create: `application/views/adm/marketing/trafego_ia.php`

- [ ] **Step 1: Criar `application/views/adm/marketing/trafego_ia.php`**

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<title>Trafego de IA | UTEC</title>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
	<link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
	<style>
		.mkt-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin-bottom:24px; }
		.mkt-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px 20px; box-shadow:0 10px 24px rgba(15,23,42,.05); }
		.mkt-label { color:#64748b; font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
		.mkt-value { color:#0f172a; font-size:26px; font-weight:700; margin-top:6px; }
		.mkt-panel { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px 20px; margin-bottom:22px; }
		.mkt-panel h6 { margin:0 0 14px; font-weight:700; }
		.mkt-table { width:100%; border-collapse:collapse; font-size:13px; }
		.mkt-table th, .mkt-table td { text-align:left; padding:8px 10px; border-bottom:1px solid #eef2f7; }
		.mkt-table th { color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:.04em; }
		.mkt-filter { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-bottom:20px; }
		.mkt-filter label { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:4px; }
		.mkt-filter input, .mkt-filter select { border:1px solid #cbd5e1; border-radius:8px; padding:7px 10px; font-size:13px; }
	</style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
	<? include("includes/adm/search.php"); ?>
	<div class="layout-w">
		<? include("includes/adm/menu.php"); ?>
		<div class="content-w">
			<? include("includes/adm/top.php"); ?>
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
				<li class="breadcrumb-item"><span>Marketing</span></li>
				<li class="breadcrumb-item"><span>Trafego de IA</span></li>
			</ul>
			<div class="content-i">
				<div class="content-box">
					<div class="element-wrapper">
						<h6 class="element-header">Trafego de IA</h6>
						<p style="color:#64748b;font-size:13px;">Acessos vindos de assistentes de IA (ChatGPT, Gemini, Claude, Perplexity, Copilot, DeepSeek, Grok) e conversoes atribuidas a essas origens.</p>
					</div>

					<? if(!$schema_ok){ ?>
						<div class="alert alert-warning">
							As tabelas de monitoramento ainda nao existem. Execute
							<strong><a href="<?=base_url()?>adm/dev/migrar_monitoramento_ia"><?=base_url()?>adm/dev/migrar_monitoramento_ia</a></strong>.
						</div>
					<? } else { ?>

					<form method="get" action="<?=base_url()?>adm/marketing/trafego_ia" class="mkt-filter">
						<div>
							<label>De</label>
							<input type="date" name="start_date" value="<?=htmlspecialchars($summary['periodo'][0])?>">
						</div>
						<div>
							<label>Ate</label>
							<input type="date" name="end_date" value="<?=htmlspecialchars($summary['periodo'][1])?>">
						</div>
						<div>
							<label>Fonte</label>
							<select name="source">
								<option value="">Todas</option>
								<? foreach($sources as $s){ ?>
									<option value="<?=htmlspecialchars($s['source'])?>" <?=($filtros_raw['source'] === $s['source'] ? 'selected' : '')?>><?=htmlspecialchars(ucfirst($s['source']))?></option>
								<? } ?>
							</select>
						</div>
						<div>
							<button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
							<a href="<?=base_url()?>adm/marketing/trafego_ia" class="btn btn-link btn-sm">Limpar</a>
						</div>
					</form>

					<div class="mkt-grid">
						<div class="mkt-card"><div class="mkt-label">Acessos IA hoje</div><div class="mkt-value"><?=(int)$summary['acessos_hoje']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Acessos IA 7 dias</div><div class="mkt-value"><?=(int)$summary['acessos_7d']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Acessos IA 30 dias</div><div class="mkt-value"><?=(int)$summary['acessos_30d']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Conversoes IA (periodo)</div><div class="mkt-value"><?=(int)$summary['conversoes']?></div></div>
						<div class="mkt-card"><div class="mkt-label">Taxa de conversao IA</div><div class="mkt-value"><?=number_format((float)$summary['taxa_conversao'], 2, ',', '.')?>%</div></div>
						<div class="mkt-card"><div class="mkt-label">Receita atribuida</div><div class="mkt-value">R$ <?=number_format((float)$summary['receita'], 2, ',', '.')?></div></div>
					</div>

					<div class="mkt-panel">
						<h6>Acessos e conversoes por dia (<?=htmlspecialchars($summary['periodo'][0])?> a <?=htmlspecialchars($summary['periodo'][1])?>)</h6>
						<canvas id="chartTimeline" height="90"></canvas>
					</div>

					<div class="mkt-panel">
						<h6>Trafego por fonte de IA</h6>
						<canvas id="chartSources" height="90"></canvas>
					</div>

					<div class="mkt-panel">
						<h6>Landing pages mais acessadas</h6>
						<table class="mkt-table">
							<thead><tr><th>Pagina</th><th>Acessos</th><th>Conversoes</th></tr></thead>
							<tbody>
								<? if(empty($pages)){ ?><tr><td colspan="3" style="color:#94a3b8;">Sem dados no periodo.</td></tr><? } ?>
								<? foreach($pages as $p){ ?>
									<tr><td><?=htmlspecialchars($p->landing_page)?></td><td><?=(int)$p->acessos?></td><td><?=(int)$p->conversoes?></td></tr>
								<? } ?>
							</tbody>
						</table>
					</div>

					<div class="mkt-panel">
						<h6>Conversao por origem</h6>
						<table class="mkt-table">
							<thead><tr><th>Fonte</th><th>Visitas</th><th>Conversoes</th><th>Taxa</th><th>Receita</th></tr></thead>
							<tbody>
								<? if(empty($conv)){ ?><tr><td colspan="5" style="color:#94a3b8;">Sem dados no periodo.</td></tr><? } ?>
								<? foreach($conv as $c){ ?>
									<tr>
										<td><?=htmlspecialchars(ucfirst($c->ai_source ? $c->ai_source : 'outros'))?></td>
										<td><?=(int)$c->visitas?></td>
										<td><?=(int)$c->conversoes?></td>
										<td><?=number_format((float)$c->taxa, 2, ',', '.')?>%</td>
										<td>R$ <?=number_format((float)$c->receita, 2, ',', '.')?></td>
									</tr>
								<? } ?>
							</tbody>
						</table>
					</div>

					<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?=base_url()?>bower_components/chart.js/dist/Chart.bundle.min.js"></script>
<script>
(function () {
<? if($schema_ok){ ?>
	var timeline = <?=json_encode($timeline)?>;
	var sources  = <?=json_encode(array_values(array_filter($sources, function($s){ return $s['total'] > 0; })))?>;

	var tl = document.getElementById('chartTimeline');
	if (tl && window.Chart) {
		new Chart(tl.getContext('2d'), {
			type: 'line',
			data: {
				labels: timeline.map(function (d) { return d.dia; }),
				datasets: [
					{ label: 'Acessos',    data: timeline.map(function (d) { return d.acessos; }),    borderColor: '#0f766e', backgroundColor: 'rgba(15,118,110,.12)', fill: true, lineTension: .3 },
					{ label: 'Conversoes', data: timeline.map(function (d) { return d.conversoes; }), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,.12)', fill: true, lineTension: .3 }
				]
			},
			options: { responsive: true, maintainAspectRatio: true, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
		});
	}

	var sc = document.getElementById('chartSources');
	if (sc && window.Chart) {
		new Chart(sc.getContext('2d'), {
			type: 'bar',
			data: {
				labels: sources.map(function (s) { return s.source; }),
				datasets: [{ label: 'Acessos', data: sources.map(function (s) { return s.total; }), backgroundColor: '#0f766e' }]
			},
			options: { responsive: true, maintainAspectRatio: true, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
		});
	}
<? } ?>
})();
</script>
</body>
</html>
```

- [ ] **Step 2: Validar sintaxe**

Run:
```powershell
php -l application/views/adm/marketing/trafego_ia.php
```
Expected: `No syntax errors detected in application/views/adm/marketing/trafego_ia.php`

- [ ] **Step 3: Smoke test no navegador**

Run: logado como admin nível 1, abrir `http://localhost/utec/adm/marketing/trafego_ia`

Expected:
- Menu lateral mostra a seção **Marketing → Trafego de IA**.
- 6 cards renderizam (com os números do smoke test das Tasks 4–5: pelo menos 1 acesso `chatgpt`, 1 conversão `whatsapp`).
- Gráfico de linha (acessos/conversões por dia) e gráfico de barras (por fonte) desenham sem erro no console.
- Tabelas "Landing pages" e "Conversao por origem" preenchidas.
- Trocar o período no filtro e clicar "Aplicar" recarrega com o novo range.

- [ ] **Step 4: Commit**

```bash
git add application/views/adm/marketing/trafego_ia.php
git commit -m "feat: view do dashboard de Trafego de IA (cards, graficos, tabelas)"
```

---

## Task 10: Documentação e verificação final

**Files:**
- Modify: `CLAUDE.md`

- [ ] **Step 1: Atualizar `CLAUDE.md`**

Na seção **6.2 Admin (`application/controllers/adm/`)**, adicionar a linha na tabela:
```markdown
| `Marketing.php` | `/adm/marketing` | Dashboard de Tráfego de IA (referral de IA + conversões) — nível 1 |
```

Na seção **8.1 Estrutura** (árvore de views), adicionar sob `adm/`:
```markdown
    ├── marketing/
    │   └── trafego_ia.php            # Dashboard de Tráfego de IA (cards + Chart.js + tabelas)
```

Na seção **13 Utilitário de Dev / Migrações**, adicionar as linhas na tabela:
```markdown
| `adm/dev/migrar_monitoramento_ia` | Cria `ai_referrals` + `ai_conversions` (idempotente; `?desfazer=1` faz DROP) |
| `adm/dev/testar_detector_ia` | Roda os casos mínimos do detector de tráfego de IA (PASS/FAIL) |
| `adm/dev/purgar_monitoramento_ia` | Remove registros de IA com mais de 18 meses (`?meses=N` ajusta) |
```

Na seção **10 Integrações Externas**, adicionar um item **10.6**:
```markdown
### 10.6 Monitoramento de Tráfego de IA

- Config de fontes: `application/config/ai_sources.php`
- Captura: `Padrao_model::track_ai_referral()` (chamado em `Home` e `Blog`), grava `ai_referrals` 1x por sessão + cookie `utec_air` (90 dias)
- Conversões: `Padrao_model::mark_ai_conversion($type, $value, $reference_id, $meta)` em trial, assinatura e pagamentos; beacon `e/track?t=whatsapp|contato` para as landings
- Dashboard: `adm/marketing/trafego_ia` (nível 1)
- GEO / Brand Radar (Parte 2 do `docs/monitoramento_geo_ia.md`): fase futura, não implementada
```

- [ ] **Step 2: Validar sintaxe de todos os arquivos PHP tocados**

Run:
```powershell
php -l application/config/ai_sources.php
php -l application/config/routes.php
php -l application/models/Padrao_model.php
php -l application/models/adm/Marketing_model.php
php -l application/controllers/Home.php
php -l application/controllers/Blog.php
php -l application/controllers/adm/Dev.php
php -l application/controllers/adm/Marketing.php
php -l application/views/adm/marketing/trafego_ia.php
php -l application/views/index-front.php
php -l application/views/public/contato.php
php -l includes/adm/menu.php
```
Expected: `No syntax errors detected` em todos.

- [ ] **Step 3: Walkthrough funcional completo**

1. `http://localhost/utec/adm/dev/testar_detector_ia` → `7/7 PASS`.
2. Navegador anônimo → `http://localhost/utec/blog?utm_source=perplexity` → conferir nova linha em `ai_referrals` com `ai_source = perplexity`, `detection_method = utm`.
3. Na mesma sessão, navegar para `http://localhost/utec/precos` ou outra página pública → **não** cria nova linha (guard de sessão).
4. Na mesma sessão, `http://localhost/utec/e/track?t=whatsapp&o=teste` → nova linha em `ai_conversions` (`whatsapp`), e a linha de `ai_referrals` correspondente fica `converted = 1`.
5. `http://localhost/utec/adm/marketing/trafego_ia` → cards, gráficos e tabelas refletindo os dados acima; filtro de período funcional.
6. `http://localhost/utec/adm/dev/migrar_monitoramento_ia` re-executado → só mensagens "ja existia"/`CREATE TABLE IF NOT EXISTS` sem erro (idempotência).

- [ ] **Step 4: Revisar o diff — nada fora do escopo**

Run:
```powershell
git diff --stat main
```
Expected: apenas os arquivos do "Mapa de arquivos". Nenhuma alteração em `system/`, nem em fluxos não relacionados. As mudanças em `Home.php` são só linhas aditivas de `mark_ai_conversion()` / `track_ai_referral()` / o método `track_evento()`.

- [ ] **Step 5: Commit**

```bash
git add CLAUDE.md
git commit -m "docs: registrar monitoramento de trafego de IA no CLAUDE.md"
```

---

## Self-Review

**Spec coverage:**

| Requisito do spec | Task |
|---|---|
| Config `ai_sources.php` fora do código, sem hardcode | Task 1 |
| `ai_referrals` (first-touch, colunas de conversão inline) | Task 3 |
| `ai_conversions` (funil, dedup por `reference_id`) | Task 3 |
| Migração idempotente + reversível (`?desfazer=1`) em `Dev.php` | Task 3 |
| Política de retenção + `purgar_monitoramento_ia()` | Task 3 |
| `detect_ai_source()` — ordem UTM → Referer → aux; `strpos`; Bing/Google fora | Task 2 |
| `track_ai_referral()` — 1x por sessão, cookie 90d, `ip_hash`, defensivo | Task 4 |
| Chamada no construtor de `Home` e `Blog` (sem hook, sem MY_Controller) | Task 4 |
| `mark_ai_conversion()` — re-hidrata do cookie, first-touch, `try/catch` | Task 4 |
| Conversões: trial, assinatura, WhatsApp, contato | Task 5, 6 |
| Endpoint `e/track` com whitelist + rate-limit + `204` | Task 5 |
| Beacon `sendBeacon` nos `wa.me` de `index-front` e `contato` | Task 6 |
| `Marketing_model` — summary, sources, pages, conversions, timeline | Task 7 |
| `adm/Marketing` (nível 1), `index/trafego_ia/api` + filtros | Task 8 |
| Rotas `adm/marketing*` e `adm/marketing/api/(:any)` | Task 8 |
| Menu "Marketing → Trafego de IA" só para `$menu_can_admin` | Task 8 |
| View: 6 cards, gráfico de linha, gráfico de barras, 2 tabelas, filtro de período | Task 9 |
| Chart.js 2.9.3 vendorizado (`Chart.bundle.min.js`, sintaxe v2 `yAxes`) | Task 9 |
| LGPD: `ip_hash`, sem PII em `meta`, dashboard nível 1, retenção | Tasks 3, 4, 8 |
| Testes: `testar_detector_ia()` com os 7 casos do documento | Task 2 |
| GEO / Parte 2 fora de escopo, registrado | Task 10 (CLAUDE.md 10.6) |

**Desvios conscientes do spec (documentados aqui):**
- O filtro de período da view usa **form GET com reload** em vez de `fetch` nos endpoints `api()`. Os endpoints `api()` continuam existindo (Task 8) para uso futuro/externo. Simplicidade e robustez; zero JS de estado.
- `contratar()` grava `conversion_value = null` (intenção). A receita só entra na confirmação de pagamento (`pay_<id>`), evitando dupla contagem. Card "Conversões IA" vem de `ai_referrals.converted` (first-touch), naturalmente deduplicado.

**Placeholder scan:** sem `TBD`/`TODO`/"implementar depois". Todo passo de código traz o código completo. A "extensão futura para identificadores próprios de IA" em `detect_ai_source()` é um `return` final explícito, não um placeholder.

**Type consistency:**
- `detect_ai_source($referrer, $utm)` → `array('is_ai'=>bool, 'source'=>string|null, 'method'=>string|null)` — consumido igual em `testar_detector_ia()` (Task 2) e `track_ai_referral()` (Task 4).
- `mark_ai_conversion($type, $value=null, $reference_id=null, $meta=null)` — mesma assinatura nas 5 chamadas (Tasks 5, 6).
- `ai_sources_list()` retorna slugs + `'outros'` — usado em `Marketing_model::get_by_source()` (Task 7) e no `<select>` da view (Task 9).
- `Marketing_model` métodos retornam: `get_summary`→assoc array com `periodo`; `get_by_source`→lista de `{source,total}`; `get_landing_pages`/`get_conversion_by_source`→`result()` de objetos; `get_timeline`→lista de `{dia,acessos,conversoes}`. A view (Task 9) e o controller `api()` (Task 8) consomem exatamente esses formatos.
- Nome da tabela `ai_referrals` / `ai_conversions` e colunas idênticos entre Task 3 (CREATE), Task 4 (INSERT/UPDATE) e Task 7 (SELECT).

---

## Execution Handoff

Ver seção abaixo do plano no chat.
