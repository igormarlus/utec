# Rótulos dos Pacientes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Etiquetas coloridas por paciente (organização + alertas) com catálogo por clínica, exibidas no prontuário, na lista de pacientes (com filtro) e — só os alertas — na agenda.

**Architecture:** Funções puras em `rotulos_helper.php`; `Rotulos_model` com catálogo e vínculos (guardado por `table_exists`); controller `adm/Rotulos` para o catálogo e para aplicar rótulos a um paciente (POST + redirect). As views existentes buscam os rótulos via `get_instance()->load->model('Rotulos_model')` — os controllers `Usuarios` e `Atendimento` não mudam.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MySQL/MariaDB 10.11, Bootstrap 4 + jQuery (já carregados nas views).

Spec: `docs/superpowers/specs/2026-10-03-rotulos-pacientes-design.md`

## Global Constraints
- PHP: testes e `php -l` só com `C:/PHP/PHP7.2/php.exe` (PATH `php` é PHP 8). Sem sintaxe PHP 8.
- CI3 apenas (`$this->db`, `$this->input->post()/get()`, `$this->load->*`); nunca `$_POST`/`$_GET`; não tocar `system/`.
- "Conta" (dona do catálogo) = raiz da árvore por `usuarios.id_user`: sobe enquanto o pai existe e tem nível 2, 3 ou 4; para em nível 2; máx. 10 saltos; usuário inicial nível 1 → conta 0.
- Gerenciar catálogo: nível 1 (conta via `?conta=ID`); nível 2 (sua raiz); nível 3 só se a raiz é ele mesmo. Aplicar em paciente: níveis 1–4 + `can_access_usuario($id_paciente)`; só rótulos ativos da conta do **paciente**.
- Paleta fixa: `azul #2563eb`, `verde #16a34a`, `ambar #d97706`, `vermelho #dc2626`, `roxo #7c3aed`, `rosa #db2777`, `cinza #64748b`, `teal #0d9488`; cor inválida → `cinza`.
- Nome: trim, espaços colapsados, máx. 40 caracteres (multibyte), vazio = inválido; único por conta.
- Sugestões iniciais (só quando a conta não tem nenhum rótulo): VIP (roxo), Retorno pendente (ambar), Convênio (azul), Gestante (rosa, alerta), Alérgico (vermelho, alerta).
- Sem migração executada, todas as telas existentes funcionam como hoje (blocos de rótulo não aparecem).
- Todo texto de rótulo na saída passa por `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Flash keys: `rotulos_ok` / `rotulos_erro`.
- Testes: scripts PHP em `tests/`, `exit(1)` na falha, imprimem `OK`.
- Commits terminam com linha em branco + `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
- Preservar line endings existentes de cada arquivo (vários são CRLF).

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/rotulos_helper.php` | Criar | Funções puras (paleta, nome, raiz, sugestões, ids válidos, HTML de chips, atributo data) |
| `tests/rotulos_helper_test.php` | Criar | Testes do helper |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_rotulos_pacientes()` |
| `application/models/Rotulos_model.php` | Criar | Catálogo, sugestões, vínculos |
| `tests/rotulos_source_test.php` | Criar | Teste estático de fiação |
| `application/controllers/adm/Rotulos.php` | Criar | Catálogo (index/salvar/status) + aplicar no paciente |
| `application/views/adm/rotulos/index.php` | Criar | Tela do catálogo |
| `includes/adm/menu.php` | Modificar | Item "Rótulos" em Pacientes |
| `application/views/adm/usuarios/new/prontuario.php` | Modificar | Chips + painel de aplicar |
| `application/views/adm/usuarios/new/lista.php` | Modificar | Chips + filtro |
| `application/views/adm/usuarios/new/atendimentos.php` | Modificar | Alertas na agenda |
| `application/libraries/Manual_conteudo.php`, `CLAUDE.md` | Modificar | Documentação |

---

### Task 1: Helper puro

**Files:**
- Create: `application/helpers/rotulos_helper.php`
- Test: `tests/rotulos_helper_test.php`

**Interfaces — Produces:**
- `utec_rotulos_paleta(): array` (chave → hex)
- `utec_rotulos_cor_valida($cor): string`
- `utec_rotulos_normalizar_nome($nome): string`
- `utec_rotulos_resolver_raiz($id, callable $buscar): int` — `$buscar($id)` → `array('id'=>int,'nivel'=>int,'id_user'=>int)` ou `null`
- `utec_rotulos_sugestoes(): array` de `array(nome, cor, alerta)`
- `utec_rotulos_ids_validos(array $ids_post, array $ids_catalogo): array` de int
- `utec_rotulos_chips_html($rotulos, $so_alertas = false): string` — aceita lista de objetos ou arrays com `nome`, `cor`, `alerta`
- `utec_rotulos_data_attr($rotulos): string` → `",3,7,"` (vazio → `","`)

- [ ] **Step 1: Write the failing test** — `tests/rotulos_helper_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/rotulos_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

// --- paleta / cor
$p = utec_rotulos_paleta();
assertSameValue(8, count($p), 'paleta 8 cores');
assertSameValue('#dc2626', $p['vermelho'], 'hex vermelho');
assertSameValue('azul', utec_rotulos_cor_valida('azul'), 'cor valida');
assertSameValue('cinza', utec_rotulos_cor_valida('#ff0000'), 'cor fora da paleta');
assertSameValue('cinza', utec_rotulos_cor_valida(null), 'cor null');

// --- nome
assertSameValue('Retorno pendente', utec_rotulos_normalizar_nome("  Retorno   pendente \n"), 'nome normalizado');
assertSameValue('', utec_rotulos_normalizar_nome('   '), 'nome vazio');
assertSameValue(40, mb_strlen(utec_rotulos_normalizar_nome(str_repeat('é', 41)), 'UTF-8'), 'nome cortado em 40 (multibyte)');
assertSameValue('Alérgico', utec_rotulos_normalizar_nome('Alérgico'), 'acento preservado');

// --- raiz da árvore (árvore falsa)
$arvore = array(
    2  => array('id' => 2,  'nivel' => 2, 'id_user' => 1),   // estabelecimento
    1  => array('id' => 1,  'nivel' => 1, 'id_user' => 0),   // admin
    3  => array('id' => 3,  'nivel' => 3, 'id_user' => 2),   // prestador da clínica
    4  => array('id' => 4,  'nivel' => 4, 'id_user' => 3),   // colaborador do prestador
    50 => array('id' => 50, 'nivel' => 5, 'id_user' => 4),  // paciente
    30 => array('id' => 30, 'nivel' => 3, 'id_user' => 1),   // prestador autônomo
    51 => array('id' => 51, 'nivel' => 5, 'id_user' => 30),  // paciente do autônomo
    52 => array('id' => 52, 'nivel' => 5, 'id_user' => 999), // órfão (pai inexistente)
    60 => array('id' => 60, 'nivel' => 4, 'id_user' => 61),  // ciclo 60 <-> 61
    61 => array('id' => 61, 'nivel' => 4, 'id_user' => 60),
);
$buscar = function ($id) use ($arvore) { return isset($arvore[$id]) ? $arvore[$id] : null; };
assertSameValue(2, utec_rotulos_resolver_raiz(50, $buscar), 'paciente -> colab -> prestador -> estabelecimento');
assertSameValue(2, utec_rotulos_resolver_raiz(2, $buscar), 'estabelecimento e a propria raiz');
assertSameValue(2, utec_rotulos_resolver_raiz(4, $buscar), 'colaborador sobe ate estabelecimento');
assertSameValue(30, utec_rotulos_resolver_raiz(51, $buscar), 'paciente do autonomo');
assertSameValue(30, utec_rotulos_resolver_raiz(30, $buscar), 'autonomo e a propria raiz (pai admin ignorado)');
assertSameValue(52, utec_rotulos_resolver_raiz(52, $buscar), 'orfao fica nele mesmo');
assertTrue(in_array(utec_rotulos_resolver_raiz(60, $buscar), array(60, 61), true), 'ciclo termina');
assertSameValue(0, utec_rotulos_resolver_raiz(1, $buscar), 'admin nao tem conta');
assertSameValue(0, utec_rotulos_resolver_raiz(12345, $buscar), 'usuario inexistente');

// --- sugestões
$s = utec_rotulos_sugestoes();
assertSameValue(5, count($s), '5 sugestoes');
assertSameValue(array('Gestante', 'rosa', 1), $s[3], 'gestante alerta');

// --- ids válidos
assertSameValue(array(3, 7), utec_rotulos_ids_validos(array('3', '7', '7', '99', 'x', '-1'), array(3, 7, 8)), 'ids filtrados pelo catalogo');
assertSameValue(array(), utec_rotulos_ids_validos(array(), array(3)), 'post vazio');

// --- chips
$rot = array(
    (object)array('id' => 3, 'nome' => 'VIP', 'cor' => 'roxo', 'alerta' => 0),
    array('id' => 7, 'nome' => '<script>x</script>', 'cor' => 'vermelho', 'alerta' => '1'),
);
$html = utec_rotulos_chips_html($rot);
assertTrue(strpos($html, 'VIP') !== false, 'chip VIP');
assertTrue(strpos($html, '<script>') === false, 'escapa script');
assertTrue(strpos($html, '&lt;script&gt;') !== false, 'texto escapado presente');
assertTrue(strpos($html, 'ut-rotulo-alerta') !== false, 'classe de alerta');
assertTrue(strpos($html, '#7c3aed') !== false, 'cor roxo aplicada');
$so = utec_rotulos_chips_html($rot, true);
assertTrue(strpos($so, 'VIP') === false && strpos($so, 'ut-rotulo-alerta') !== false, 'so alertas');
assertSameValue('', utec_rotulos_chips_html(array()), 'sem rotulos = vazio');

// --- data attr
assertSameValue(',3,7,', utec_rotulos_data_attr($rot), 'data attr');
assertSameValue(',', utec_rotulos_data_attr(array()), 'data attr vazio');

echo "OK\n";
```

- [ ] **Step 2: Run to verify it fails** — `C:/PHP/PHP7.2/php.exe tests/rotulos_helper_test.php` → falha ao abrir `rotulos_helper.php`.

- [ ] **Step 3: Implement** — `application/helpers/rotulos_helper.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras dos rótulos de pacientes (sem CI, sem banco) — testes em tests/rotulos_*.

if(!function_exists('utec_rotulos_paleta')){
	function utec_rotulos_paleta(){
		return array(
			'azul' => '#2563eb', 'verde' => '#16a34a', 'ambar' => '#d97706', 'vermelho' => '#dc2626',
			'roxo' => '#7c3aed', 'rosa' => '#db2777', 'cinza' => '#64748b', 'teal' => '#0d9488',
		);
	}
}

if(!function_exists('utec_rotulos_cor_valida')){
	function utec_rotulos_cor_valida($cor){
		$paleta = utec_rotulos_paleta();
		$cor = (string)$cor;
		return isset($paleta[$cor]) ? $cor : 'cinza';
	}
}

if(!function_exists('utec_rotulos_normalizar_nome')){
	function utec_rotulos_normalizar_nome($nome){
		$n = preg_replace('/\s+/u', ' ', (string)$nome);
		$n = trim((string)$n);
		if(function_exists('mb_substr')){
			$n = mb_substr($n, 0, 40, 'UTF-8');
		}else{
			$n = substr($n, 0, 40);
		}
		return trim($n);
	}
}

if(!function_exists('utec_rotulos_resolver_raiz')){
	// Sobe pela árvore id_user até o estabelecimento (nível 2) ou até não haver pai válido (2, 3 ou 4).
	function utec_rotulos_resolver_raiz($id, callable $buscar){
		$atual = call_user_func($buscar, (int)$id);
		if(!$atual || (int)$atual['nivel'] === 1){ return 0; }
		$raiz = (int)$atual['id'];
		$visitados = array($raiz => true);
		for($i = 0; $i < 10 && (int)$atual['nivel'] !== 2; $i++){
			$pai_id = (int)$atual['id_user'];
			if($pai_id <= 0 || isset($visitados[$pai_id])){ break; }
			$pai = call_user_func($buscar, $pai_id);
			if(!$pai || !in_array((int)$pai['nivel'], array(2, 3, 4), true)){ break; }
			$atual = $pai;
			$raiz = (int)$pai['id'];
			$visitados[$raiz] = true;
		}
		return $raiz;
	}
}

if(!function_exists('utec_rotulos_sugestoes')){
	function utec_rotulos_sugestoes(){
		return array(
			array('VIP', 'roxo', 0),
			array('Retorno pendente', 'ambar', 0),
			array('Convênio', 'azul', 0),
			array('Gestante', 'rosa', 1),
			array('Alérgico', 'vermelho', 1),
		);
	}
}

if(!function_exists('utec_rotulos_ids_validos')){
	function utec_rotulos_ids_validos(array $ids_post, array $ids_catalogo){
		$permitidos = array();
		foreach($ids_catalogo as $c){ $permitidos[(int)$c] = true; }
		$saida = array();
		foreach($ids_post as $v){
			if(!is_scalar($v) || !preg_match('/^\d+$/', (string)$v)){ continue; }
			$id = (int)$v;
			if($id > 0 && isset($permitidos[$id]) && !in_array($id, $saida, true)){
				$saida[] = $id;
			}
		}
		return $saida;
	}
}

if(!function_exists('utec_rotulos_chips_html')){
	function utec_rotulos_chips_html($rotulos, $so_alertas = false){
		$paleta = utec_rotulos_paleta();
		$html = '';
		foreach((array)$rotulos as $r){
			$r = (array)$r;
			$alerta = !empty($r['alerta']) && (int)$r['alerta'] === 1;
			if($so_alertas && !$alerta){ continue; }
			$hex = $paleta[utec_rotulos_cor_valida(isset($r['cor']) ? $r['cor'] : '')];
			$nome = htmlspecialchars(isset($r['nome']) ? (string)$r['nome'] : '', ENT_QUOTES, 'UTF-8');
			$classe = 'ut-rotulo'.($alerta ? ' ut-rotulo-alerta' : '');
			$html .= '<span class="'.$classe.'" style="border-color:'.$hex.';color:'.$hex.';">'.($alerta ? '&#9888; ' : '').$nome.'</span>';
		}
		return $html;
	}
}

if(!function_exists('utec_rotulos_data_attr')){
	function utec_rotulos_data_attr($rotulos){
		$ids = array();
		foreach((array)$rotulos as $r){
			$r = (array)$r;
			if(isset($r['id'])){ $ids[] = (int)$r['id']; }
		}
		return ','.(empty($ids) ? '' : implode(',', $ids).',');
	}
}
```

- [ ] **Step 4: Run** — `C:/PHP/PHP7.2/php.exe tests/rotulos_helper_test.php` → `OK`; `C:/PHP/PHP7.2/php.exe -l application/helpers/rotulos_helper.php` → sem erros.

- [ ] **Step 5: Commit**
```bash
git add application/helpers/rotulos_helper.php tests/rotulos_helper_test.php
git commit -m "feat(rotulos): helper puro (paleta, nome, raiz da arvore, chips)"
```

---

### Task 2: Migração + model

**Files:**
- Modify: `application/controllers/adm/Dev.php` (novo método logo após `migrar_prontuario_exportacoes()`)
- Create: `application/models/Rotulos_model.php`
- Create: `tests/rotulos_source_test.php`

**Interfaces:**
- Consumes: Task 1 helper; `Dev::run_sql($sql, &$logs, $label)` (privado, já existe).
- Produces (`Rotulos_model`, carregado como `$this->load->model('Rotulos_model', 'rotulos_model')`):
  - `disponivel(): bool`
  - `conta_raiz($id_usuario): int`
  - `catalogo($id_conta, $so_ativos = true): array` de objetos (`id, id_conta, nome, cor, alerta, ordem, status`)
  - `garantir_sugestoes($id_conta, $id_usuario): void`
  - `salvar_rotulo($id_conta, $id_rotulo, $nome, $cor, $alerta, $id_usuario): array('ok'=>bool,'erro'=>string)`
  - `alternar_status($id_conta, $id_rotulo): bool`
  - `rotulos_do_paciente($id_paciente, $so_ativos = true): array` de objetos
  - `rotulos_de_pacientes(array $ids): array` `[id_paciente => [objetos]]` (só ativos)
  - `definir_rotulos_paciente($id_paciente, array $ids_rotulo, $id_conta, $id_usuario): bool`

- [ ] **Step 1: Write the failing test** — `tests/rotulos_source_test.php`:
```php
<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerArquivo($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) { fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL); exit(1); }
    return file_get_contents($path);
}

// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_rotulos_pacientes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_rotulos`', $dev, 'tabela catalogo');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_rotulos_vinculos`', $dev, 'tabela vinculos');
assertContains('UNIQUE KEY `uk_rotulo_conta_nome` (`id_conta`, `nome`)', $dev, 'nome unico por conta');
assertContains('PRIMARY KEY (`id_paciente`, `id_rotulo`)', $dev, 'pk composta');

// Model
$model = lerArquivo('application/models/Rotulos_model.php');
foreach (array('function disponivel(', 'function conta_raiz(', 'function catalogo(', 'function garantir_sugestoes(',
    'function salvar_rotulo(', 'function alternar_status(', 'function rotulos_do_paciente(',
    'function rotulos_de_pacientes(', 'function definir_rotulos_paciente(') as $fn) {
    assertContains($fn, $model, 'model: ' . $fn);
}
assertContains("table_exists('pacientes_rotulos_vinculos')", $model, 'model guardado por table_exists');
assertContains('trans_start', $model, 'definir em transacao');
assertContains('utec_rotulos_resolver_raiz(', $model, 'raiz via helper');

echo "OK\n";
```

- [ ] **Step 2: Run** → FAIL `migracao existe — trecho ausente`.

- [ ] **Step 3a: Migração** — inserir em `Dev.php` após o fechamento de `migrar_prontuario_exportacoes()` (mesmo estilo/indentação com tab):
```php
	function migrar_rotulos_pacientes(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->run_sql("CREATE TABLE IF NOT EXISTS `pacientes_rotulos` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_conta` INT NOT NULL,
			`nome` VARCHAR(40) NOT NULL,
			`cor` VARCHAR(20) NOT NULL DEFAULT 'cinza',
			`alerta` TINYINT NOT NULL DEFAULT 0,
			`ordem` INT NOT NULL DEFAULT 0,
			`status` TINYINT NOT NULL DEFAULT 1,
			`criado_por` INT NULL DEFAULT NULL,
			`criado_em` DATETIME NOT NULL,
			UNIQUE KEY `uk_rotulo_conta_nome` (`id_conta`, `nome`),
			INDEX `idx_rotulo_conta_status` (`id_conta`, `status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `pacientes_rotulos` verificada');
		$this->run_sql("CREATE TABLE IF NOT EXISTS `pacientes_rotulos_vinculos` (
			`id_paciente` INT NOT NULL,
			`id_rotulo` INT NOT NULL,
			`aplicado_por` INT NULL DEFAULT NULL,
			`aplicado_em` DATETIME NOT NULL,
			PRIMARY KEY (`id_paciente`, `id_rotulo`),
			INDEX `idx_vinculo_rotulo` (`id_rotulo`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `pacientes_rotulos_vinculos` verificada');

		echo '<h3>Migração: rótulos de pacientes</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 3b: Model** — `application/models/Rotulos_model.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Rótulos de pacientes: catálogo por conta (raiz da árvore) + vínculos paciente↔rótulo.
// Sem controle de acesso por design — o controller adm/Rotulos (e as views) impõem nível e escopo.
class Rotulos_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('rotulos');
	}

	public function disponivel(){
		return $this->db->table_exists('pacientes_rotulos') && $this->db->table_exists('pacientes_rotulos_vinculos');
	}

	public function conta_raiz($id_usuario){
		$db = $this->db;
		return utec_rotulos_resolver_raiz((int)$id_usuario, function($id) use ($db){
			$row = $db->query("SELECT id, nivel, id_user FROM usuarios WHERE id = ? LIMIT 1", array((int)$id))->row_array();
			return $row ? $row : null;
		});
	}

	public function catalogo($id_conta, $so_ativos = true){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return array(); }
		return $this->db->query(
			"SELECT * FROM pacientes_rotulos WHERE id_conta = ?".($so_ativos ? " AND status = 1" : "")." ORDER BY ordem ASC, nome ASC",
			array((int)$id_conta)
		)->result();
	}

	public function garantir_sugestoes($id_conta, $id_usuario){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return; }
		$total = (int)$this->db->query("SELECT COUNT(*) AS total FROM pacientes_rotulos WHERE id_conta = ?", array((int)$id_conta))->row()->total;
		if($total > 0){ return; }
		$ordem = 0;
		foreach(utec_rotulos_sugestoes() as $s){
			$ordem += 10;
			$this->db->query(
				"INSERT IGNORE INTO pacientes_rotulos (id_conta, nome, cor, alerta, ordem, status, criado_por, criado_em) VALUES (?, ?, ?, ?, ?, 1, ?, ?)",
				array((int)$id_conta, $s[0], $s[1], (int)$s[2], $ordem, (int)$id_usuario, date('Y-m-d H:i:s'))
			);
		}
	}

	public function salvar_rotulo($id_conta, $id_rotulo, $nome, $cor, $alerta, $id_usuario){
		if(!$this->disponivel()){ return array('ok' => false, 'erro' => 'As tabelas de rótulos ainda não foram criadas.'); }
		$id_conta = (int)$id_conta;
		$id_rotulo = (int)$id_rotulo;
		if($id_conta <= 0){ return array('ok' => false, 'erro' => 'Conta inválida.'); }
		$nome = utec_rotulos_normalizar_nome($nome);
		if($nome === ''){ return array('ok' => false, 'erro' => 'Informe o nome do rótulo.'); }
		$dup = $this->db->query("SELECT id FROM pacientes_rotulos WHERE id_conta = ? AND nome = ? AND id <> ? LIMIT 1", array($id_conta, $nome, $id_rotulo));
		if($dup->num_rows() > 0){ return array('ok' => false, 'erro' => 'Já existe um rótulo com esse nome.'); }
		$dados = array('nome' => $nome, 'cor' => utec_rotulos_cor_valida($cor), 'alerta' => $alerta ? 1 : 0);
		if($id_rotulo > 0){
			$existe = $this->db->query("SELECT id FROM pacientes_rotulos WHERE id = ? AND id_conta = ? LIMIT 1", array($id_rotulo, $id_conta));
			if($existe->num_rows() === 0){ return array('ok' => false, 'erro' => 'Rótulo não encontrado.'); }
			$this->db->where('id', $id_rotulo)->where('id_conta', $id_conta)->update('pacientes_rotulos', $dados);
			return array('ok' => true, 'erro' => '');
		}
		$max = $this->db->query("SELECT COALESCE(MAX(ordem), 0) AS m FROM pacientes_rotulos WHERE id_conta = ?", array($id_conta))->row();
		$dados['id_conta'] = $id_conta;
		$dados['ordem'] = (int)$max->m + 10;
		$dados['status'] = 1;
		$dados['criado_por'] = (int)$id_usuario;
		$dados['criado_em'] = date('Y-m-d H:i:s');
		$ok = $this->db->insert('pacientes_rotulos', $dados);
		return array('ok' => (bool)$ok, 'erro' => $ok ? '' : 'Não foi possível salvar o rótulo.');
	}

	public function alternar_status($id_conta, $id_rotulo){
		if(!$this->disponivel()){ return false; }
		$this->db->query("UPDATE pacientes_rotulos SET status = 1 - status WHERE id = ? AND id_conta = ?", array((int)$id_rotulo, (int)$id_conta));
		return $this->db->affected_rows() > 0;
	}

	public function rotulos_do_paciente($id_paciente, $so_ativos = true){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query(
			"SELECT r.* FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE v.id_paciente = ?".($so_ativos ? " AND r.status = 1" : "")."
			ORDER BY r.alerta DESC, r.ordem ASC, r.nome ASC",
			array((int)$id_paciente)
		)->result();
	}

	public function rotulos_de_pacientes(array $ids){
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids), function($v){ return $v > 0; })));
		if(empty($ids) || !$this->disponivel()){ return array(); }
		$rows = $this->db->query(
			"SELECT v.id_paciente, r.* FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE r.status = 1 AND v.id_paciente IN (".implode(',', $ids).")
			ORDER BY r.alerta DESC, r.ordem ASC, r.nome ASC"
		)->result();
		$mapa = array();
		foreach($rows as $r){
			$mapa[(int)$r->id_paciente][] = $r;
		}
		return $mapa;
	}

	public function definir_rotulos_paciente($id_paciente, array $ids_rotulo, $id_conta, $id_usuario){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return false; }
		$agora = date('Y-m-d H:i:s');
		$this->db->trans_start();
		$this->db->query(
			"DELETE v FROM pacientes_rotulos_vinculos v
			INNER JOIN pacientes_rotulos r ON r.id = v.id_rotulo
			WHERE v.id_paciente = ? AND r.id_conta = ?",
			array((int)$id_paciente, (int)$id_conta)
		);
		foreach($ids_rotulo as $id_rotulo){
			$this->db->query(
				"INSERT IGNORE INTO pacientes_rotulos_vinculos (id_paciente, id_rotulo, aplicado_por, aplicado_em) VALUES (?, ?, ?, ?)",
				array((int)$id_paciente, (int)$id_rotulo, (int)$id_usuario, $agora)
			);
		}
		$this->db->trans_complete();
		return (bool)$this->db->trans_status();
	}
}
```

- [ ] **Step 4: Run** — source test → `OK`; `php -l` no model e no `Dev.php`; `tests/rotulos_helper_test.php` → `OK`.

- [ ] **Step 5: Commit**
```bash
git add application/controllers/adm/Dev.php application/models/Rotulos_model.php tests/rotulos_source_test.php
git commit -m "feat(rotulos): migracao e model de catalogo e vinculos"
```

---

### Task 3: Controller `adm/Rotulos` + tela do catálogo + menu

**Files:**
- Create: `application/controllers/adm/Rotulos.php`
- Create: `application/views/adm/rotulos/index.php`
- Modify: `includes/adm/menu.php` (item "Pacientes", ~linha 121-129)
- Modify: `tests/rotulos_source_test.php`

**Interfaces:**
- Consumes: Task 1 (`utec_rotulos_paleta`, `utec_rotulos_chips_html`, `utec_rotulos_ids_validos`), Task 2 model.
- Produces: rotas `adm/rotulos` (GET), `adm/rotulos/salvar` (POST: `id`, `nome`, `cor`, `alerta`, `conta`), `adm/rotulos/status/{id}` (POST: `conta`), `adm/rotulos/paciente/{id}` (POST: `rotulos[]`, `voltar`). Flash `rotulos_ok` / `rotulos_erro`.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";` em `tests/rotulos_source_test.php`:
```php
// Controller
$ctl = lerArquivo('application/controllers/adm/Rotulos.php');
assertContains('class Rotulos extends CI_Controller', $ctl, 'controller existe');
assertContains('verSession()', $ctl, 'exige sessao');
assertContains('if($nivel < 1 || $nivel > 4)', $ctl, 'bloqueia nivel 5');
assertContains('function conta_gerenciada()', $ctl, 'regra de quem gerencia');
assertContains('can_access_usuario($id_paciente)', $ctl, 'aplica so no escopo');
assertContains('utec_rotulos_ids_validos(', $ctl, 'filtra ids pelo catalogo do paciente');
assertContains("input->method() !== 'post'", $ctl, 'escritas so via POST');
assertContains("preg_match('#^adm/[a-z0-9_/]+$#i'", $ctl, 'redirect de volta restrito ao admin');

// View catálogo
$vcat = lerArquivo('application/views/adm/rotulos/index.php');
assertContains('adm/rotulos/salvar', $vcat, 'form salvar');
assertContains('adm/rotulos/status/', $vcat, 'form status');
assertContains('htmlspecialchars', $vcat, 'view escapa');

// Menu
$menu = lerArquivo('includes/adm/menu.php');
assertContains("base_url().'adm/rotulos'", $menu, 'item de menu');
```

- [ ] **Step 2: Run** → FAIL `controller existe`.

- [ ] **Step 3a: Controller** — `application/controllers/adm/Rotulos.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rotulos extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'rotulos'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Rotulos_model', 'rotulos_model');
		$this->usuarios_model->verSession();

		$this->usuario = $this->padrao_model->get_usuario_logado();
		$nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
		if($nivel < 1 || $nivel > 4){
			show_error('Acesso restrito.', 403);
		}
	}

	private function nivel()
	{
		return (int)$this->usuario->nivel;
	}

	// Conta cujo catálogo o usuário logado pode gerenciar; 0 = não gerencia.
	private function conta_gerenciada()
	{
		if($this->nivel() === 1){
			$ref = (int)$this->input->get('conta');
			if($ref <= 0){ $ref = (int)$this->input->post('conta'); }
			return $ref > 0 ? $this->rotulos_model->conta_raiz($ref) : 0;
		}
		$raiz = $this->rotulos_model->conta_raiz((int)$this->usuario->id);
		if($this->nivel() === 2){ return $raiz; }
		if($this->nivel() === 3 && $raiz === (int)$this->usuario->id){ return $raiz; }
		return 0;
	}

	private function voltar($conta)
	{
		redirect('adm/rotulos'.($this->nivel() === 1 && $conta > 0 ? '?conta='.(int)$conta : ''));
	}

	public function index()
	{
		$conta = $this->conta_gerenciada();
		if($this->nivel() !== 1 && $conta === 0){
			show_error('Os rótulos são gerenciados pelo estabelecimento ou pelo profissional autônomo.', 403);
			return;
		}
		$dados['schema_ok'] = $this->rotulos_model->disponivel();
		$dados['conta'] = $conta;
		$dados['eh_admin'] = $this->nivel() === 1;
		$dados['rotulos'] = array();
		if($dados['schema_ok'] && $conta > 0){
			$this->rotulos_model->garantir_sugestoes($conta, (int)$this->usuario->id);
			$dados['rotulos'] = $this->rotulos_model->catalogo($conta, false);
		}
		$dados['paleta'] = utec_rotulos_paleta();
		$dados['flash_ok'] = $this->session->flashdata('rotulos_ok');
		$dados['flash_erro'] = $this->session->flashdata('rotulos_erro');
		$this->load->view('adm/rotulos/index', $dados);
	}

	public function salvar()
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$conta = $this->conta_gerenciada();
		if($conta === 0){ show_error('Acesso negado.', 403); return; }
		$r = $this->rotulos_model->salvar_rotulo(
			$conta,
			(int)$this->input->post('id'),
			(string)$this->input->post('nome', true),
			(string)$this->input->post('cor', true),
			(int)$this->input->post('alerta') === 1,
			(int)$this->usuario->id
		);
		$this->session->set_flashdata($r['ok'] ? 'rotulos_ok' : 'rotulos_erro', $r['ok'] ? 'Rótulo salvo.' : $r['erro']);
		$this->voltar($conta);
	}

	public function status($id_rotulo = 0)
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$conta = $this->conta_gerenciada();
		if($conta === 0){ show_error('Acesso negado.', 403); return; }
		if($this->rotulos_model->alternar_status($conta, (int)$id_rotulo)){
			$this->session->set_flashdata('rotulos_ok', 'Status do rótulo atualizado.');
		}else{
			$this->session->set_flashdata('rotulos_erro', 'Rótulo não encontrado.');
		}
		$this->voltar($conta);
	}

	public function paciente($id_paciente = 0)
	{
		$id_paciente = (int)$id_paciente;
		if($this->input->method() !== 'post' || $id_paciente <= 1){ show_404(); return; }
		if(!$this->padrao_model->can_access_usuario($id_paciente)){
			show_error('Acesso negado ao paciente selecionado.', 403);
			return;
		}
		$conta = $this->rotulos_model->conta_raiz($id_paciente);
		$ids_catalogo = array();
		foreach($this->rotulos_model->catalogo($conta, true) as $r){
			$ids_catalogo[] = (int)$r->id;
		}
		$post = $this->input->post('rotulos');
		$ids = utec_rotulos_ids_validos(is_array($post) ? $post : array(), $ids_catalogo);
		if($this->rotulos_model->definir_rotulos_paciente($id_paciente, $ids, $conta, (int)$this->usuario->id)){
			$this->session->set_flashdata('rotulos_ok', 'Rótulos do paciente atualizados.');
		}else{
			$this->session->set_flashdata('rotulos_erro', 'Não foi possível salvar os rótulos.');
		}
		$voltar = (string)$this->input->post('voltar', true);
		if(!preg_match('#^adm/[a-z0-9_/]+$#i', $voltar)){
			$voltar = 'adm/atendimento/prontuario/'.$id_paciente;
		}
		redirect($voltar);
	}
}
```

- [ ] **Step 3b: View** — `application/views/adm/rotulos/index.php` (mesmo esqueleto de `application/views/adm/horarios/index.php`: head com Lato + `clicklinica-main.css` + `utec-redesign.css`, includes `search.php`/`menu.php`/`top.php`, scripts no fim):
```php
<!DOCTYPE html>
<html>
<head>
  <title>Rótulos de pacientes</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .rt-shell { max-width: 960px; }
    .rt-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .rt-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
    .rt-linha { display:flex; align-items:center; gap:12px; padding:10px 0; border-top:1px solid #eef2f7; flex-wrap:wrap; }
    .rt-linha:first-of-type { border-top:0; }
    .rt-linha.is-off { opacity:.5; }
    .rt-cores { display:flex; gap:8px; flex-wrap:wrap; }
    .rt-cor input { position:absolute; opacity:0; }
    .rt-cor span { display:inline-block; width:26px; height:26px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 0 1px #cbd5e1; cursor:pointer; }
    .rt-cor input:checked + span { box-shadow:0 0 0 2px #0f172a; }
    .rt-cor input:focus-visible + span { outline:2px solid #2563eb; outline-offset:2px; }
    .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:1px 10px; font-size:12px; font-weight:700; background:#fff; margin:2px 4px 2px 0; }
    .ut-rotulo-alerta { background:#fef2f2; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="rt-shell">
            <h4 style="font-weight:800;color:#0f172a;">Rótulos de pacientes</h4>
            <p class="rt-sub">Etiquetas para organizar pacientes e destacar alertas. Rótulos marcados como <strong>alerta</strong> aparecem em destaque no prontuário e na agenda.</p>

            <? if($flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div><? } ?>
            <? if($flash_erro){ ?><div class="alert alert-danger"><?=htmlspecialchars($flash_erro)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas de rótulos ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_rotulos_pacientes</code>.</div><? } ?>

            <? if($eh_admin){ ?>
            <div class="rt-panel">
              <form method="get" action="<?=base_url('adm/rotulos')?>" class="form-inline" style="gap:8px;">
                <label for="rt-conta" class="mr-2">ID de um usuário da clínica</label>
                <input type="number" min="1" id="rt-conta" name="conta" class="form-control form-control-sm mr-2" value="<?=$conta > 0 ? (int)$conta : ''?>">
                <button type="submit" class="btn btn-sm btn-primary">Abrir catálogo</button>
              </form>
              <small class="text-muted">O catálogo é da clínica (estabelecimento ou profissional autônomo) à qual o usuário pertence.<? if($conta > 0){ ?> Conta atual: #<?=(int)$conta?>.<? } ?></small>
            </div>
            <? } ?>

            <? if($schema_ok && $conta > 0){ ?>
            <div class="rt-panel">
              <h5 style="font-weight:800;">Novo rótulo</h5>
              <form method="post" action="<?=base_url('adm/rotulos/salvar')?>">
                <input type="hidden" name="conta" value="<?=(int)$conta?>">
                <div class="form-group">
                  <label for="rt-nome">Nome</label>
                  <input type="text" id="rt-nome" name="nome" maxlength="40" required class="form-control" placeholder="Ex.: VIP, Convênio X, Alérgico a dipirona">
                </div>
                <div class="form-group">
                  <span class="d-block mb-1">Cor</span>
                  <div class="rt-cores" role="radiogroup" aria-label="Cor do rótulo">
                    <? foreach($paleta as $chave => $hex){ ?>
                      <label class="rt-cor" title="<?=htmlspecialchars($chave)?>"><input type="radio" name="cor" value="<?=htmlspecialchars($chave)?>" aria-label="<?=htmlspecialchars($chave)?>" <?=$chave === 'azul' ? 'checked' : ''?>><span style="background:<?=$hex?>"></span></label>
                    <? } ?>
                  </div>
                </div>
                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="rt-alerta" name="alerta" value="1">
                  <label class="form-check-label" for="rt-alerta">É um alerta (aparece em destaque no prontuário e na agenda)</label>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar rótulo</button>
              </form>
            </div>

            <div class="rt-panel">
              <h5 style="font-weight:800;">Rótulos da clínica</h5>
              <? if(empty($rotulos)){ ?><p class="rt-sub">Nenhum rótulo cadastrado.</p><? } ?>
              <? foreach($rotulos as $r){ $ativo = (int)$r->status === 1; ?>
                <div class="rt-linha<?=$ativo ? '' : ' is-off'?>">
                  <div style="min-width:180px;"><?=utec_rotulos_chips_html(array($r))?></div>
                  <details style="flex:1;min-width:240px;">
                    <summary class="btn btn-sm btn-light">Editar</summary>
                    <form method="post" action="<?=base_url('adm/rotulos/salvar')?>" class="mt-2">
                      <input type="hidden" name="conta" value="<?=(int)$conta?>">
                      <input type="hidden" name="id" value="<?=(int)$r->id?>">
                      <input type="text" name="nome" maxlength="40" required class="form-control form-control-sm mb-2" value="<?=htmlspecialchars($r->nome, ENT_QUOTES, 'UTF-8')?>" aria-label="Nome do rótulo">
                      <div class="rt-cores mb-2">
                        <? foreach($paleta as $chave => $hex){ ?>
                          <label class="rt-cor" title="<?=htmlspecialchars($chave)?>"><input type="radio" name="cor" value="<?=htmlspecialchars($chave)?>" aria-label="<?=htmlspecialchars($chave)?>" <?=$chave === $r->cor ? 'checked' : ''?>><span style="background:<?=$hex?>"></span></label>
                        <? } ?>
                      </div>
                      <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="rt-alerta-<?=(int)$r->id?>" name="alerta" value="1" <?=(int)$r->alerta === 1 ? 'checked' : ''?>>
                        <label class="form-check-label" for="rt-alerta-<?=(int)$r->id?>">Alerta</label>
                      </div>
                      <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                    </form>
                  </details>
                  <form method="post" action="<?=base_url('adm/rotulos/status/'.(int)$r->id)?>">
                    <input type="hidden" name="conta" value="<?=(int)$conta?>">
                    <button type="submit" class="btn btn-sm <?=$ativo ? 'btn-outline-secondary' : 'btn-outline-success'?>"><?=$ativo ? 'Desativar' : 'Ativar'?></button>
                  </form>
                </div>
              <? } ?>
            </div>
            <? } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
</body>
</html>
```

- [ ] **Step 3c: Menu** — em `includes/adm/menu.php`, no item `'label' => 'Pacientes'`, trocar o array `'children'` por:
```php
		'children' => array_merge([
			['label' => 'Lista de pacientes', 'url' => base_url().'adm/usuarios/rel/5'],
			['label' => 'Novo paciente', 'url' => base_url().'adm/usuarios/cadastro/5'],
		], in_array((int)$menu_level, [1, 2, 3], true) ? [
			['label' => 'Rótulos', 'url' => base_url().'adm/rotulos'],
		] : []),
```
(Antes, confirmar com `grep -n '\$menu_level' includes/adm/menu.php` que `$menu_level` está definido acima do item; se o nome for outro, usar o mesmo nome do arquivo.)

- [ ] **Step 4: Run** — source test `OK`; helper test `OK`; `php -l` em `Rotulos.php`, `views/adm/rotulos/index.php`, `includes/adm/menu.php`.

- [ ] **Step 5: Commit**
```bash
git add application/controllers/adm/Rotulos.php application/views/adm/rotulos/index.php includes/adm/menu.php tests/rotulos_source_test.php
git commit -m "feat(rotulos): catalogo por clinica e endpoint para aplicar rotulos no paciente"
```

---

### Task 4: Rótulos no prontuário

**Files:**
- Modify: `application/views/adm/usuarios/new/prontuario.php`
- Modify: `tests/rotulos_source_test.php`

**Interfaces — Consumes:** model (Task 2), helper (Task 1), `adm/rotulos/paciente/{id}` (Task 3).

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
// Prontuário
$pront = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains("load->model('Rotulos_model', 'rotulos_model')", $pront, 'prontuario carrega model');
assertContains('rotulos_model->disponivel()', $pront, 'prontuario guarda sem migracao');
assertContains('adm/rotulos/paciente/', $pront, 'form aplicar rotulos');
assertContains('name="rotulos[]"', $pront, 'checkboxes de rotulos');
assertContains("flashdata('rotulos_ok')", $pront, 'flash de rotulos');
```

- [ ] **Step 2: Run** → FAIL `prontuario carrega model`.

- [ ] **Step 3: Implement** — três inserções na view:

(a) CSS: imediatamente antes da linha `    </style>` (primeira ocorrência, ~linha 322):
```css
      .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:1px 10px; font-size:12px; font-weight:700; background:#fff; margin:2px 4px 2px 0; }
      .ut-rotulo-alerta { background:#fef2f2; }
      .ut-rotulos-linha { margin-top:8px; }
```

(b) Dados + flash: logo após o bloco `<?php if($whatsapp_status && isset($whatsapp_status['message'])){ ?> ... <?php } ?>` (antes de `<div class="row">` do resumo do paciente), inserir:
```php
              <?php
              $ut_rot_ok = false; $ut_rot_paciente = array(); $ut_rot_catalogo = array(); $ut_rot_marcados = array(); $ut_rot_gerencia = false;
              $ut_ci =& get_instance();
              $ut_ci->load->model('Rotulos_model', 'rotulos_model');
              if($ut_ci->rotulos_model->disponivel()){
                $ut_rot_ok = true;
                $ut_rot_conta = $ut_ci->rotulos_model->conta_raiz((int)$paciente->id);
                $ut_rot_catalogo = $ut_ci->rotulos_model->catalogo($ut_rot_conta, true);
                if(empty($ut_rot_catalogo) && $ut_rot_conta > 0){
                  $ut_ci->rotulos_model->garantir_sugestoes($ut_rot_conta, (int)$this->session->userdata('id'));
                  $ut_rot_catalogo = $ut_ci->rotulos_model->catalogo($ut_rot_conta, true);
                }
                $ut_rot_paciente = $ut_ci->rotulos_model->rotulos_do_paciente((int)$paciente->id, true);
                foreach($ut_rot_paciente as $ut_r){ $ut_rot_marcados[(int)$ut_r->id] = true; }
                $ut_nivel_logado = (int)$this->session->userdata('nivel');
                $ut_id_logado = (int)$this->session->userdata('id');
                $ut_rot_gerencia = $ut_nivel_logado === 1 || $ut_nivel_logado === 2
                  || ($ut_nivel_logado === 3 && $ut_ci->rotulos_model->conta_raiz($ut_id_logado) === $ut_id_logado);
              }
              $ut_rot_flash_ok = $this->session->flashdata('rotulos_ok');
              $ut_rot_flash_erro = $this->session->flashdata('rotulos_erro');
              ?>
              <?php if($ut_rot_flash_ok){ ?><div class="alert alert-success" style="margin-bottom:20px;"><?=htmlspecialchars($ut_rot_flash_ok)?></div><?php } ?>
              <?php if($ut_rot_flash_erro){ ?><div class="alert alert-danger" style="margin-bottom:20px;"><?=htmlspecialchars($ut_rot_flash_erro)?></div><?php } ?>
```

(c) Chips + painel: logo após `<p style="margin:0;color:#5f708c">Prontuário com histórico de atendimentos, evolução clínica e arquivos do paciente.</p>` inserir:
```php
                        <?php if($ut_rot_ok && !empty($ut_rot_paciente)){ ?>
                          <div class="ut-rotulos-linha" aria-label="Rótulos do paciente"><?=utec_rotulos_chips_html($ut_rot_paciente)?></div>
                        <?php } ?>
```
e dentro de `<div class="timeline-actions" style="margin-top:0">`, depois do bloco do botão Exportar (após o `<?php } ?>` que fecha o `if(in_array(... array(1, 2, 3) ...))` do Exportar e seu `<script>`), inserir:
```php
                        <?php if($ut_rot_ok && in_array((int)$this->session->userdata('nivel'), array(1, 2, 3, 4), true) && (!empty($ut_rot_catalogo) || $ut_rot_gerencia)){ ?>
                        <details class="pront-rotulos" style="display:inline-block;position:relative;">
                          <summary class="btn btn-outline-secondary" style="list-style:none;cursor:pointer;">Rótulos</summary>
                          <div style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:250px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
                            <form method="post" action="<?=base_url('adm/rotulos/paciente/'.(int)$paciente->id)?>">
                              <input type="hidden" name="voltar" value="<?=htmlspecialchars($this->uri->uri_string(), ENT_QUOTES, 'UTF-8')?>">
                              <?php foreach($ut_rot_catalogo as $ut_r){ ?>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="rotulos[]" value="<?=(int)$ut_r->id?>" id="ut-rot-<?=(int)$ut_r->id?>" <?=isset($ut_rot_marcados[(int)$ut_r->id]) ? 'checked' : ''?>>
                                  <label class="form-check-label" for="ut-rot-<?=(int)$ut_r->id?>"><?=utec_rotulos_chips_html(array($ut_r))?></label>
                                </div>
                              <?php } ?>
                              <button type="submit" class="btn btn-sm btn-primary btn-block" style="margin-top:8px;">Salvar rótulos</button>
                            </form>
                            <?php if($ut_rot_gerencia){ ?>
                              <a href="<?=base_url('adm/rotulos')?>" style="display:block;font-size:12px;margin-top:8px;">Gerenciar rótulos</a>
                            <?php } ?>
                          </div>
                        </details>
                        <?php } ?>
```

- [ ] **Step 4: Run** — source test `OK`; `php -l application/views/adm/usuarios/new/prontuario.php`; `tests/prontuario_export_source_test.php` continua `OK` (não pode quebrar a feature anterior).

- [ ] **Step 5: Commit**
```bash
git add application/views/adm/usuarios/new/prontuario.php tests/rotulos_source_test.php
git commit -m "feat(rotulos): chips e painel de rotulos no prontuario"
```

---

### Task 5: Lista de pacientes (chips + filtro) e alertas na agenda

**Files:**
- Modify: `application/views/adm/usuarios/new/lista.php`
- Modify: `application/views/adm/usuarios/new/atendimentos.php`
- Modify: `tests/rotulos_source_test.php`

**Interfaces — Consumes:** `Rotulos_model::disponivel()`, `rotulos_de_pacientes(array $ids)`; helper `utec_rotulos_chips_html($rotulos, $so_alertas)`, `utec_rotulos_data_attr($rotulos)`.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
// Lista
$lista = lerArquivo('application/views/adm/usuarios/new/lista.php');
assertContains('rotulos_de_pacientes(', $lista, 'lista carrega rotulos em lote');
assertContains('data-rotulos=', $lista, 'linha com data-rotulos');
assertContains('id="ul-filter-rotulo"', $lista, 'select de filtro');
assertContains('function ulFiltrar()', $lista, 'filtro combinado nome + rotulo');

// Agenda
$agenda = lerArquivo('application/views/adm/usuarios/new/atendimentos.php');
assertContains('rotulos_de_pacientes(', $agenda, 'agenda carrega rotulos em lote');
assertContains('utec_rotulos_chips_html($ut_alertas_paciente[(int)$agenda->id_paciente], true)', $agenda, 'agenda mostra so alertas');
```

- [ ] **Step 2: Run** → FAIL `lista carrega rotulos em lote`.

- [ ] **Step 3a: Lista** (`lista.php`)
1. CSS: antes do primeiro `    </style>` (~linha 244):
```css
      .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:0 8px; font-size:11px; font-weight:700; background:#fff; margin:2px 4px 0 0; }
      .ut-rotulo-alerta { background:#fef2f2; }
      .ul-rotulo-select { max-width:220px; margin-left:8px; }
```
2. Dados: imediatamente antes de `<? if ($total > 0): ?>` que envolve o `ul-search-wrap` (~linha 282), inserir:
```php
              <?php
              $ut_rot_mapa = array(); $ut_rot_opcoes = array();
              $ut_ci =& get_instance();
              $ut_ci->load->model('Rotulos_model', 'rotulos_model');
              if($ut_ci->rotulos_model->disponivel()){
                $ut_ids_pac = array();
                foreach($usuarios_lista as $ut_u){ if((int)$ut_u->nivel === 5){ $ut_ids_pac[] = (int)$ut_u->id; } }
                $ut_rot_mapa = $ut_ci->rotulos_model->rotulos_de_pacientes($ut_ids_pac);
                foreach($ut_rot_mapa as $ut_lista_r){ foreach($ut_lista_r as $ut_r){ $ut_rot_opcoes[(int)$ut_r->id] = $ut_r->nome; } }
                asort($ut_rot_opcoes);
              }
              ?>
```
3. Select: dentro de `<div class="ul-search-wrap">`, logo após o `<input type="text" id="ul-filter" ...>`:
```php
                <? if(!empty($ut_rot_opcoes)): ?>
                <select id="ul-filter-rotulo" class="form-control ul-rotulo-select" aria-label="Filtrar por rótulo">
                  <option value="">Todos os rótulos</option>
                  <? foreach($ut_rot_opcoes as $ut_id => $ut_nome): ?>
                    <option value="<?=(int)$ut_id?>"><?=htmlspecialchars($ut_nome, ENT_QUOTES, 'UTF-8')?></option>
                  <? endforeach; ?>
                </select>
                <? endif; ?>
```
4. Linha: trocar `<div class="ul-report-row" data-nome="<?=mb_strtolower($u->nome, 'UTF-8')?>">` por:
```php
                <? $ut_rot_u = isset($ut_rot_mapa[(int)$u->id]) ? $ut_rot_mapa[(int)$u->id] : array(); ?>
                <div class="ul-report-row" data-nome="<?=mb_strtolower($u->nome, 'UTF-8')?>" data-rotulos="<?=utec_rotulos_data_attr($ut_rot_u)?>">
```
5. Chips: logo após o `</p>` que fecha `<p class="ul-report-name">` (o bloco com o link para o prontuário), inserir:
```php
                        <? if(!empty($ut_rot_u)): ?><div><?=utec_rotulos_chips_html($ut_rot_u)?></div><? endif; ?>
```
6. JS: substituir o handler existente
```js
      $('#ul-filter').on('input', function(){
        var q = $.trim($(this).val()).toLowerCase();
        $('#ul-grid .ul-report-row').each(function(){
          $(this).toggle(!q || $(this).data('nome').indexOf(q) !== -1);
        });
      });
```
por:
```js
      function ulFiltrar(){
        var q = $.trim($('#ul-filter').val() || '').toLowerCase();
        var r = $('#ul-filter-rotulo').val() || '';
        $('#ul-grid .ul-report-row').each(function(){
          var okNome = !q || String($(this).data('nome')).indexOf(q) !== -1;
          var okRot = !r || String($(this).attr('data-rotulos') || '').indexOf(',' + r + ',') !== -1;
          $(this).toggle(okNome && okRot);
        });
      }
      $('#ul-filter').on('input', ulFiltrar);
      $('#ul-filter-rotulo').on('change', ulFiltrar);
```
(O helper é carregado pelo model; `utec_rotulos_*` existem sempre que o model foi carregado.)

- [ ] **Step 3b: Agenda** (`atendimentos.php`)
1. CSS: antes do primeiro `    </style>` (~linha 238):
```css
      .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:0 8px; font-size:11px; font-weight:700; background:#fff; margin:2px 4px 0 0; }
      .ut-rotulo-alerta { background:#fef2f2; }
```
2. Dados: imediatamente após o primeiro `</head>` do arquivo, inserir:
```php
<?php
$ut_alertas_paciente = array();
$ut_ci =& get_instance();
$ut_ci->load->model('Rotulos_model', 'rotulos_model');
if(isset($qr_agendamentos) && $ut_ci->rotulos_model->disponivel()){
  $ut_ids_ag = array();
  foreach($qr_agendamentos->result() as $ut_ag){ $ut_ids_ag[] = (int)$ut_ag->id_paciente; }
  $ut_alertas_paciente = $ut_ci->rotulos_model->rotulos_de_pacientes($ut_ids_ag);
}
?>
```
3. Exibição — logo após cada uma destas três linhas (desktop, cartão ativo e fila mobile):
   - `<div class="patient-name"><?=$agenda->paciente_nome?></div>`
   - `<p class="ut-active-card-name"><?=htmlspecialchars($agenda->paciente_nome)?></p>`
   - `<p class="ut-queue-name"><?=htmlspecialchars($agenda->paciente_nome)?></p>`
   inserir:
```php
<? if(!empty($ut_alertas_paciente[(int)$agenda->id_paciente])){ ?><div><?=utec_rotulos_chips_html($ut_alertas_paciente[(int)$agenda->id_paciente], true)?></div><? } ?>
```
(Se alguma dessas linhas não existir exatamente assim, localizar com `grep -n "paciente_nome" application/views/adm/usuarios/new/atendimentos.php` e inserir após o elemento que mostra o nome no mesmo bloco; reportar no relatório.)

- [ ] **Step 4: Run** — source test `OK`; `php -l` nas duas views; `tests/prontuario_export_source_test.php` e `tests/rotulos_helper_test.php` `OK`.

- [ ] **Step 5: Commit**
```bash
git add application/views/adm/usuarios/new/lista.php application/views/adm/usuarios/new/atendimentos.php tests/rotulos_source_test.php
git commit -m "feat(rotulos): chips e filtro na lista de pacientes e alertas na agenda"
```

---

### Task 6: Manual + CLAUDE.md

**Files:**
- Modify: `application/libraries/Manual_conteudo.php`, `CLAUDE.md`, `tests/rotulos_source_test.php`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Rótulos', $manual, 'manual cobre rotulos');
$claude = lerArquivo('CLAUDE.md');
assertContains('migrar_rotulos_pacientes', $claude, 'CLAUDE.md documenta migracao');
```

- [ ] **Step 2: Run** → FAIL `manual cobre rotulos`.

- [ ] **Step 3a: Manual** — capítulo `'slug' => 'pacientes-cadastro'`: no array `'*'` acrescentar
`'Use `Rótulos` no topo do prontuário para marcar o paciente com etiquetas como VIP, Convênio ou Retorno pendente. Rótulos de alerta (ex.: Gestante, Alérgico) aparecem em destaque no prontuário e na agenda do dia. Na lista de pacientes, filtre por rótulo ao lado da busca.'`;
no array `2` acrescentar `'Cria, edita e desativa os rótulos da clínica em `Pacientes > Rótulos`, escolhendo cor e se o rótulo é um alerta.'`;
criar/usar o array `3` com `'Se você atende como profissional autônomo, gerencia os próprios rótulos em `Pacientes > Rótulos`; dentro de uma clínica, apenas aplica os rótulos definidos pelo estabelecimento.'`;
no array `4` acrescentar `'Aplica e remove rótulos dos pacientes pelo prontuário; a lista de rótulos é definida pela clínica.'`.
Atualizar `'atualizado_em'` desse capítulo para `'2026-10-03'`. No capítulo `'slug' => 'agenda'`, array `'*'`, acrescentar `'Pacientes com rótulo de alerta mostram o aviso (⚠) ao lado do nome na agenda.'` e atualizar `atualizado_em` para `'2026-10-03'`. Não mudar `VERSAO`.

- [ ] **Step 3b: CLAUDE.md** (apenas acréscimos, mesmo formato das linhas vizinhas):
  - §4.2 **Saúde e Agenda**: `- \`pacientes_rotulos\` — catálogo de rótulos por conta (\`id_conta\` = raiz da árvore id_user), cor da paleta fixa, \`alerta\`` e `- \`pacientes_rotulos_vinculos\` — paciente ↔ rótulo (PK composta)`.
  - §6.2 tabela: `| \`Rotulos.php\` | \`/adm/rotulos\` | Catálogo de rótulos da clínica (níveis 1, 2 e 3 autônomo) + \`paciente/{id}\` (POST) para aplicar rótulos (níveis 1–4, escopo) |`.
  - §13 tabela: `| \`adm/dev/migrar_rotulos_pacientes\` | Cria \`pacientes_rotulos\` + \`pacientes_rotulos_vinculos\` (idempotente) |`.
  - §15.1: `- [x] Rótulos de pacientes (organização + alertas) com catálogo por clínica — prontuário, lista com filtro e alertas na agenda (\`rotulos_helper.php\`, \`Rotulos_model\`)`.

- [ ] **Step 4: Run** — os 5 testes `tests/rotulos_*` e `tests/prontuario_export_*` → `OK`; `php -l application/libraries/Manual_conteudo.php`.

- [ ] **Step 5: Commit**
```bash
git add application/libraries/Manual_conteudo.php CLAUDE.md tests/rotulos_source_test.php
git commit -m "docs(rotulos): manual e CLAUDE.md"
```

---

## Deploy (após code review e merge aprovado pelo usuário)
1. **Antes de subir**, baixar do servidor os arquivos existentes que serão sobrescritos (`Dev.php`, `menu.php`, `prontuario.php`, `lista.php`, `atendimentos.php`, `Manual_conteudo.php`) e comparar com `main` — abortar e avisar se houver diferença não explicada.
2. Ordem: `rotulos_helper.php` → `Rotulos_model.php` → `views/adm/rotulos/index.php` → `controllers/adm/Rotulos.php` → `Dev.php` → `Manual_conteudo.php` → `includes/adm/menu.php` → `lista.php` → `atendimentos.php` → `prontuario.php` (por último).
3. Pasta nova no servidor: `application/views/adm/rotulos/` — se o upload falhar com `curl: (9)`, conferir se existe um *arquivo* com esse nome no lugar da pasta.
4. Rodar `adm/dev/migrar_rotulos_pacientes` (nível 1) e healthcheck (home/`admin` 200; `adm/rotulos`, `adm/atendimento` sem sessão 302).
5. Teste online: nível 2 cria/edita rótulo; nível 4 aplica no prontuário; alerta aparece na agenda; filtro da lista funciona; nível 3 de clínica recebe 403 em `adm/rotulos`.
