# Ficha do Paciente (Entrega A) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ficha fixa por paciente (pessoal/responsável, saúde básica, convênio) editável em `adm/ficha/paciente/{id}` e exibida no prontuário com alergias em destaque.

**Architecture:** Funções puras em `ficha_paciente_helper.php` (opções, CPF, normalização, detecção de mudança na saúde, formatação); `Ficha_paciente_model` com upsert 1:1 em `pacientes_ficha` (guardado por `table_exists`); controller `adm/Ficha` (GET formulário / POST salvar); o prontuário busca a ficha via `get_instance()->load->model(...)` — controllers do prontuário não mudam.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MariaDB 10.11, Bootstrap 4 views.

Spec: `docs/superpowers/specs/2026-10-05-ficha-paciente-design.md` · Branch: `feat/ficha-paciente` (empilhada sobre `feat/rotulos-pacientes`).

## Global Constraints
- PHP: testes e `php -l` só com `C:/PHP/PHP7.2/php.exe` (PATH `php` é PHP 8). Sem sintaxe PHP 8. Não usar `isset()` sobre resultado de função (`isset(f()[$k])` é erro fatal) — usar `array_key_exists`.
- CI3 apenas; nunca `$_POST`/`$_GET`; não tocar `system/`.
- Ver ficha: níveis 1–4 + `can_access_usuario($id_paciente)` + alvo com `usuarios.nivel = 5`. Editar pessoal/convênio: 1–4. Editar saúde: 1–3; para nível 4 o servidor descarta as chaves de saúde.
- Chaves da ficha (19, nesta ordem): `nome_social, sexo, estado_civil, responsavel_nome, responsavel_parentesco, responsavel_telefone, responsavel_cpf, emergencia_nome, emergencia_parentesco, emergencia_telefone, tipo_sanguineo, alergias, medicamentos, comorbidades, obs_saude, convenio_nome, convenio_plano, convenio_carteirinha, convenio_validade`.
- Chaves de saúde: `tipo_sanguineo, alergias, medicamentos, comorbidades, obs_saude`.
- Tamanhos: `nome_social`/`responsavel_nome`/`emergencia_nome` 120; `responsavel_parentesco`/`emergencia_parentesco` 40; `convenio_nome`/`convenio_plano` 80; `convenio_carteirinha` 40; textos longos (alergias, medicamentos, comorbidades, obs_saude) 2000.
- Opções: sexo `feminino|masculino|outro|nao_informado`; estado_civil `solteiro|casado|uniao_estavel|divorciado|viuvo|nao_informado`; tipo_sanguineo `A+ A- B+ B- AB+ AB- O+ O-`. Fora da lista → `''`.
- Telefone: só dígitos, aceito com 10 ou 11 dígitos; senão `''`. CPF do responsável: 11 dígitos com DV válido; inválido → erro "CPF do responsável inválido." e nada é gravado. Validade: `AAAA-MM-DD` válida ou `''`.
- `''` é gravado como NULL; NULL lido vira `''`.
- Flash keys: `ficha_ok` / `ficha_erro`.
- Toda saída de valor da ficha escapada com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Sem migração, nada novo aparece no prontuário e nada quebra.
- Testes: scripts PHP em `tests/`, `exit(1)` na falha, imprimem `OK`.
- Commits: `git add <paths>` explícito (nunca `-u`/`-A`); mensagem termina com linha em branco + `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`. Preservar line endings de cada arquivo.

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/ficha_paciente_helper.php` | Criar | Funções puras |
| `tests/ficha_paciente_helper_test.php` | Criar | Testes do helper |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_ficha_pacientes()` |
| `application/models/Ficha_paciente_model.php` | Criar | `disponivel/obter/salvar` |
| `tests/ficha_paciente_source_test.php` | Criar | Teste estático de fiação |
| `application/controllers/adm/Ficha.php` | Criar | GET/POST da ficha |
| `application/views/adm/ficha/paciente.php` | Criar | Formulário |
| `application/views/adm/usuarios/new/prontuario.php` | Modificar | Alergias, nome social, card, botão |
| `application/libraries/Manual_conteudo.php`, `CLAUDE.md` | Modificar | Documentação |

---

### Task 1: Helper puro

**Files:** Create `application/helpers/ficha_paciente_helper.php`, `tests/ficha_paciente_helper_test.php`

**Interfaces — Produces:**
- `utec_ficha_opcoes_sexo(): array`, `utec_ficha_opcoes_estado_civil(): array`, `utec_ficha_opcoes_tipo_sanguineo(): array` (chave → rótulo)
- `utec_ficha_campos_saude(): array` (lista das 5 chaves)
- `utec_ficha_vazia(): array` (19 chaves → `''`)
- `utec_ficha_cpf_valido($cpf): bool`
- `utec_ficha_normalizar($post, $pode_editar_saude): array('dados'=>array, 'erros'=>array)`
- `utec_ficha_saude_mudou(array $antes, array $depois): bool`
- `utec_ficha_rotulo_opcao(array $opcoes, $valor): string` ("Não informado" quando vazio/desconhecido)
- `utec_ficha_telefone_fmt($digitos): string`
- `utec_ficha_data_br($ymd): string`

- [ ] **Step 1: Failing test** — `tests/ficha_paciente_helper_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/ficha_paciente_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

// --- estrutura
$vazia = utec_ficha_vazia();
assertSameValue(19, count($vazia), '19 chaves');
assertSameValue('', $vazia['alergias'], 'vazia tem strings vazias');
assertSameValue(array('tipo_sanguineo', 'alergias', 'medicamentos', 'comorbidades', 'obs_saude'), utec_ficha_campos_saude(), 'campos de saude');
assertSameValue(8, count(utec_ficha_opcoes_tipo_sanguineo()), '8 tipos sanguineos');
assertSameValue('Feminino', utec_ficha_opcoes_sexo()['feminino'], 'rotulo sexo');

// --- CPF
assertTrue(utec_ficha_cpf_valido('529.982.247-25'), 'cpf valido com mascara');
assertTrue(utec_ficha_cpf_valido('52998224725'), 'cpf valido sem mascara');
assertTrue(!utec_ficha_cpf_valido('529.982.247-24'), 'dv errado');
assertTrue(!utec_ficha_cpf_valido('111.111.111-11'), 'repetido');
assertTrue(!utec_ficha_cpf_valido('123'), 'curto');

// --- normalização completa (pode editar saúde)
$post = array(
    'nome_social' => "  Ana   Maria  ",
    'sexo' => 'feminino',
    'estado_civil' => 'invalido',
    'responsavel_nome' => str_repeat('x', 130),
    'responsavel_parentesco' => 'Mãe',
    'responsavel_telefone' => '(81) 99999-0000',
    'responsavel_cpf' => '529.982.247-25',
    'emergencia_nome' => 'João',
    'emergencia_parentesco' => 'Pai',
    'emergencia_telefone' => '123',
    'tipo_sanguineo' => 'O-',
    'alergias' => "Dipirona\r\nPenicilina  ",
    'medicamentos' => str_repeat('é', 2100),
    'comorbidades' => '',
    'obs_saude' => 'ok',
    'convenio_nome' => 'Unimed',
    'convenio_plano' => 'Apartamento',
    'convenio_carteirinha' => '0001',
    'convenio_validade' => '2026-02-31',
    'campo_estranho' => 'x',
);
$r = utec_ficha_normalizar($post, true);
$d = $r['dados'];
assertSameValue(array(), $r['erros'], 'sem erros');
assertSameValue('Ana Maria', $d['nome_social'], 'espacos colapsados');
assertSameValue('feminino', $d['sexo'], 'sexo valido');
assertSameValue('', $d['estado_civil'], 'opcao invalida vira vazio');
assertSameValue(120, mb_strlen($d['responsavel_nome'], 'UTF-8'), 'corta em 120');
assertSameValue('81999990000', $d['responsavel_telefone'], 'telefone so digitos');
assertSameValue('52998224725', $d['responsavel_cpf'], 'cpf so digitos');
assertSameValue('', $d['emergencia_telefone'], 'telefone curto vira vazio');
assertSameValue('O-', $d['tipo_sanguineo'], 'tipo sanguineo');
assertSameValue("Dipirona\nPenicilina", $d['alergias'], 'quebras normalizadas e trim');
assertSameValue(2000, mb_strlen($d['medicamentos'], 'UTF-8'), 'texto longo cortado em 2000');
assertSameValue('', $d['convenio_validade'], 'data inexistente vira vazio');
assertTrue(!array_key_exists('campo_estranho', $d), 'ignora campos desconhecidos');
assertSameValue(19, count($d), '19 chaves com saude');

// --- sem permissão de saúde: chaves de saúde removidas
$r2 = utec_ficha_normalizar($post, false);
foreach (utec_ficha_campos_saude() as $k) {
    assertTrue(!array_key_exists($k, $r2['dados']), 'sem saude: ' . $k);
}
assertSameValue(14, count($r2['dados']), '14 chaves sem saude');

// --- CPF inválido gera erro
$r3 = utec_ficha_normalizar(array('responsavel_cpf' => '111.111.111-11'), true);
assertSameValue(array('CPF do responsável inválido.'), $r3['erros'], 'erro de cpf');
assertSameValue('', $r3['dados']['responsavel_cpf'], 'cpf invalido nao gravado');

// --- validade válida e post não-array
$r4 = utec_ficha_normalizar(array('convenio_validade' => '2027-12-31'), true);
assertSameValue('2027-12-31', $r4['dados']['convenio_validade'], 'validade valida');
$r5 = utec_ficha_normalizar(null, true);
assertSameValue('', $r5['dados']['nome_social'], 'post nulo');

// --- mudança na saúde
$antes = utec_ficha_vazia();
$depois = $antes; $depois['nome_social'] = 'X';
assertTrue(!utec_ficha_saude_mudou($antes, $depois), 'mudanca fora da saude nao conta');
$depois['alergias'] = 'Dipirona';
assertTrue(utec_ficha_saude_mudou($antes, $depois), 'mudanca na saude conta');
$sem_saude = $r2['dados'];
assertTrue(!utec_ficha_saude_mudou($antes, $sem_saude), 'chaves ausentes nao contam');

// --- formatação
assertSameValue('Feminino', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), 'feminino'), 'rotulo opcao');
assertSameValue('Não informado', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), ''), 'rotulo vazio');
assertSameValue('Não informado', utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), 'xyz'), 'rotulo desconhecido');
assertSameValue('(81) 99999-0000', utec_ficha_telefone_fmt('81999990000'), 'fmt celular');
assertSameValue('(81) 3333-0000', utec_ficha_telefone_fmt('8133330000'), 'fmt fixo');
assertSameValue('', utec_ficha_telefone_fmt('12'), 'fmt invalido');
assertSameValue('31/12/2027', utec_ficha_data_br('2027-12-31'), 'data br');
assertSameValue('', utec_ficha_data_br(''), 'data br vazia');

echo "OK\n";
```

- [ ] **Step 2: Run** → `C:/PHP/PHP7.2/php.exe tests/ficha_paciente_helper_test.php` falha (arquivo ausente).

- [ ] **Step 3: Implement** — `application/helpers/ficha_paciente_helper.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras da ficha do paciente (sem CI, sem banco) — testes em tests/ficha_paciente_*.

if(!function_exists('utec_ficha_opcoes_sexo')){
	function utec_ficha_opcoes_sexo(){
		return array('feminino' => 'Feminino', 'masculino' => 'Masculino', 'outro' => 'Outro', 'nao_informado' => 'Não informado');
	}
}

if(!function_exists('utec_ficha_opcoes_estado_civil')){
	function utec_ficha_opcoes_estado_civil(){
		return array(
			'solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'uniao_estavel' => 'União estável',
			'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'nao_informado' => 'Não informado',
		);
	}
}

if(!function_exists('utec_ficha_opcoes_tipo_sanguineo')){
	function utec_ficha_opcoes_tipo_sanguineo(){
		$tipos = array('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-');
		return array_combine($tipos, $tipos);
	}
}

if(!function_exists('utec_ficha_campos_saude')){
	function utec_ficha_campos_saude(){
		return array('tipo_sanguineo', 'alergias', 'medicamentos', 'comorbidades', 'obs_saude');
	}
}

if(!function_exists('utec_ficha_vazia')){
	function utec_ficha_vazia(){
		$chaves = array(
			'nome_social', 'sexo', 'estado_civil', 'responsavel_nome', 'responsavel_parentesco', 'responsavel_telefone',
			'responsavel_cpf', 'emergencia_nome', 'emergencia_parentesco', 'emergencia_telefone', 'tipo_sanguineo',
			'alergias', 'medicamentos', 'comorbidades', 'obs_saude', 'convenio_nome', 'convenio_plano',
			'convenio_carteirinha', 'convenio_validade',
		);
		return array_fill_keys($chaves, '');
	}
}

if(!function_exists('utec_ficha_cpf_valido')){
	function utec_ficha_cpf_valido($cpf){
		$d = preg_replace('/\D/', '', (string)$cpf);
		if(strlen($d) !== 11 || preg_match('/^(\d)\1{10}$/', $d)){ return false; }
		for($t = 9; $t < 11; $t++){
			$soma = 0;
			for($i = 0; $i < $t; $i++){ $soma += (int)$d[$i] * (($t + 1) - $i); }
			$dv = ((10 * $soma) % 11) % 10;
			if((int)$d[$t] !== $dv){ return false; }
		}
		return true;
	}
}

if(!function_exists('utec_ficha_texto_curto')){
	function utec_ficha_texto_curto($v, $max){
		$v = trim((string)preg_replace('/\s+/u', ' ', (string)$v));
		return function_exists('mb_substr') ? trim(mb_substr($v, 0, $max, 'UTF-8')) : trim(substr($v, 0, $max));
	}
}

if(!function_exists('utec_ficha_texto_longo')){
	function utec_ficha_texto_longo($v){
		$v = trim(str_replace(array("\r\n", "\r"), "\n", (string)$v));
		return function_exists('mb_substr') ? trim(mb_substr($v, 0, 2000, 'UTF-8')) : trim(substr($v, 0, 2000));
	}
}

if(!function_exists('utec_ficha_telefone')){
	function utec_ficha_telefone($v){
		$d = preg_replace('/\D/', '', (string)$v);
		return (strlen($d) === 10 || strlen($d) === 11) ? $d : '';
	}
}

if(!function_exists('utec_ficha_normalizar')){
	function utec_ficha_normalizar($post, $pode_editar_saude){
		$post = is_array($post) ? $post : array();
		$g = function($k) use ($post){ return (array_key_exists($k, $post) && is_scalar($post[$k])) ? (string)$post[$k] : ''; };
		$dados = array();
		$erros = array();

		$curtos = array(
			'nome_social' => 120, 'responsavel_nome' => 120, 'responsavel_parentesco' => 40,
			'emergencia_nome' => 120, 'emergencia_parentesco' => 40,
			'convenio_nome' => 80, 'convenio_plano' => 80, 'convenio_carteirinha' => 40,
		);
		foreach($curtos as $k => $max){ $dados[$k] = utec_ficha_texto_curto($g($k), $max); }
		foreach(array('responsavel_telefone', 'emergencia_telefone') as $k){ $dados[$k] = utec_ficha_telefone($g($k)); }

		$dados['sexo'] = array_key_exists($g('sexo'), utec_ficha_opcoes_sexo()) ? $g('sexo') : '';
		$dados['estado_civil'] = array_key_exists($g('estado_civil'), utec_ficha_opcoes_estado_civil()) ? $g('estado_civil') : '';

		$cpf = preg_replace('/\D/', '', $g('responsavel_cpf'));
		if($cpf === ''){
			$dados['responsavel_cpf'] = '';
		}elseif(utec_ficha_cpf_valido($cpf)){
			$dados['responsavel_cpf'] = $cpf;
		}else{
			$dados['responsavel_cpf'] = '';
			$erros[] = 'CPF do responsável inválido.';
		}

		$val = $g('convenio_validade');
		$dados['convenio_validade'] = (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $val, $m) && checkdate((int)$m[2], (int)$m[3], (int)$m[1])) ? $val : '';

		if($pode_editar_saude){
			$dados['tipo_sanguineo'] = array_key_exists($g('tipo_sanguineo'), utec_ficha_opcoes_tipo_sanguineo()) ? $g('tipo_sanguineo') : '';
			foreach(array('alergias', 'medicamentos', 'comorbidades', 'obs_saude') as $k){
				$dados[$k] = utec_ficha_texto_longo($g($k));
			}
		}

		// ordem canônica das chaves
		$ordenado = array();
		foreach(array_keys(utec_ficha_vazia()) as $k){
			if(array_key_exists($k, $dados)){ $ordenado[$k] = $dados[$k]; }
		}
		return array('dados' => $ordenado, 'erros' => $erros);
	}
}

if(!function_exists('utec_ficha_saude_mudou')){
	function utec_ficha_saude_mudou(array $antes, array $depois){
		foreach(utec_ficha_campos_saude() as $k){
			if(!array_key_exists($k, $depois)){ continue; }
			$a = array_key_exists($k, $antes) ? (string)$antes[$k] : '';
			if($a !== (string)$depois[$k]){ return true; }
		}
		return false;
	}
}

if(!function_exists('utec_ficha_rotulo_opcao')){
	function utec_ficha_rotulo_opcao(array $opcoes, $valor){
		$valor = (string)$valor;
		return ($valor !== '' && array_key_exists($valor, $opcoes)) ? $opcoes[$valor] : 'Não informado';
	}
}

if(!function_exists('utec_ficha_telefone_fmt')){
	function utec_ficha_telefone_fmt($digitos){
		$d = preg_replace('/\D/', '', (string)$digitos);
		if(strlen($d) === 11){ return '('.substr($d, 0, 2).') '.substr($d, 2, 5).'-'.substr($d, 7); }
		if(strlen($d) === 10){ return '('.substr($d, 0, 2).') '.substr($d, 2, 4).'-'.substr($d, 6); }
		return '';
	}
}

if(!function_exists('utec_ficha_data_br')){
	function utec_ficha_data_br($ymd){
		$v = substr((string)$ymd, 0, 10);
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])){ return ''; }
		return $m[3].'/'.$m[2].'/'.$m[1];
	}
}
```

- [ ] **Step 4: Run** → `OK`; `php -l` no helper.
- [ ] **Step 5: Commit** — `git add application/helpers/ficha_paciente_helper.php tests/ficha_paciente_helper_test.php` · `feat(ficha): helper puro (opcoes, cpf, normalizacao, formatacao)`

---

### Task 2: Migração + model

**Files:** Modify `application/controllers/adm/Dev.php` (novo método após `migrar_rotulos_pacientes()`); Create `application/models/Ficha_paciente_model.php`, `tests/ficha_paciente_source_test.php`

**Interfaces:**
- Consumes: Task 1 (`utec_ficha_vazia`); `Dev::run_sql($sql, &$logs, $label)`.
- Produces (`$this->load->model('Ficha_paciente_model', 'ficha_model')`): `disponivel(): bool`; `obter($id_paciente): array` (19 chaves + `saude_atualizado_por`, `saude_atualizado_em`, `saude_atualizado_por_nome`, todas string); `salvar($id_paciente, array $dados, $id_usuario, $saude_mudou): bool`.

- [ ] **Step 1: Failing test** — `tests/ficha_paciente_source_test.php`:
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

$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_ficha_pacientes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `pacientes_ficha`', $dev, 'tabela ficha');
assertContains('`id_paciente` INT NOT NULL PRIMARY KEY', $dev, 'pk id_paciente');
assertContains('`saude_atualizado_em` DATETIME NULL', $dev, 'auditoria de saude');

$model = lerArquivo('application/models/Ficha_paciente_model.php');
assertContains('function disponivel(', $model, 'disponivel');
assertContains('function obter(', $model, 'obter');
assertContains('function salvar(', $model, 'salvar');
assertContains("table_exists('pacientes_ficha')", $model, 'guarda table_exists');
assertContains('ON DUPLICATE KEY UPDATE', $model, 'upsert');
assertContains('array_keys(utec_ficha_vazia())', $model, 'colunas por whitelist');

echo "OK\n";
```
- [ ] **Step 2: Run** → FAIL `migracao existe`.

- [ ] **Step 3a: Migração** — em `Dev.php`, logo após o fechamento de `migrar_rotulos_pacientes()` (tab de indentação):
```php
	function migrar_ficha_pacientes(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->run_sql("CREATE TABLE IF NOT EXISTS `pacientes_ficha` (
			`id_paciente` INT NOT NULL PRIMARY KEY,
			`nome_social` VARCHAR(120) NULL,
			`sexo` VARCHAR(20) NULL,
			`estado_civil` VARCHAR(20) NULL,
			`responsavel_nome` VARCHAR(120) NULL,
			`responsavel_parentesco` VARCHAR(40) NULL,
			`responsavel_telefone` VARCHAR(20) NULL,
			`responsavel_cpf` VARCHAR(14) NULL,
			`emergencia_nome` VARCHAR(120) NULL,
			`emergencia_parentesco` VARCHAR(40) NULL,
			`emergencia_telefone` VARCHAR(20) NULL,
			`tipo_sanguineo` VARCHAR(3) NULL,
			`alergias` TEXT NULL,
			`medicamentos` TEXT NULL,
			`comorbidades` TEXT NULL,
			`obs_saude` TEXT NULL,
			`saude_atualizado_por` INT NULL,
			`saude_atualizado_em` DATETIME NULL,
			`convenio_nome` VARCHAR(80) NULL,
			`convenio_plano` VARCHAR(80) NULL,
			`convenio_carteirinha` VARCHAR(40) NULL,
			`convenio_validade` DATE NULL,
			`atualizado_por` INT NULL,
			`atualizado_em` DATETIME NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `pacientes_ficha` verificada');

		echo '<h3>Migração: ficha do paciente</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 3b: Model** — `application/models/Ficha_paciente_model.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ficha fixa do paciente (1:1 com usuarios nível 5). Sem controle de acesso por design —
// o controller adm/Ficha e a view do prontuário impõem nível e escopo.
class Ficha_paciente_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('ficha_paciente');
	}

	public function disponivel(){
		return $this->db->table_exists('pacientes_ficha');
	}

	public function obter($id_paciente){
		$ficha = utec_ficha_vazia();
		$ficha['saude_atualizado_por'] = '';
		$ficha['saude_atualizado_em'] = '';
		$ficha['saude_atualizado_por_nome'] = '';
		if(!$this->disponivel()){ return $ficha; }
		$row = $this->db->query(
			"SELECT f.*, u.nome AS saude_atualizado_por_nome FROM pacientes_ficha f
			LEFT JOIN usuarios u ON u.id = f.saude_atualizado_por
			WHERE f.id_paciente = ? LIMIT 1",
			array((int)$id_paciente)
		)->row_array();
		if($row){
			foreach($row as $k => $v){
				if(array_key_exists($k, $ficha)){ $ficha[$k] = ($v === null) ? '' : (string)$v; }
			}
		}
		return $ficha;
	}

	public function salvar($id_paciente, array $dados, $id_usuario, $saude_mudou){
		if(!$this->disponivel() || (int)$id_paciente <= 0){ return false; }
		$permitidas = array_keys(utec_ficha_vazia());
		$linha = array();
		foreach($dados as $k => $v){
			if(in_array($k, $permitidas, true)){ $linha[$k] = ((string)$v === '') ? null : (string)$v; }
		}
		$agora = date('Y-m-d H:i:s');
		$linha['atualizado_por'] = (int)$id_usuario;
		$linha['atualizado_em'] = $agora;
		if($saude_mudou){
			$linha['saude_atualizado_por'] = (int)$id_usuario;
			$linha['saude_atualizado_em'] = $agora;
		}
		$cols = array_keys($linha);
		$sql = "INSERT INTO pacientes_ficha (`id_paciente`, `".implode('`, `', $cols)."`) VALUES (?".str_repeat(', ?', count($cols)).")"
			." ON DUPLICATE KEY UPDATE ".implode(', ', array_map(function($c){ return "`".$c."` = VALUES(`".$c."`)"; }, $cols));
		return (bool)$this->db->query($sql, array_merge(array((int)$id_paciente), array_values($linha)));
	}
}
```

- [ ] **Step 4: Run** → source test `OK`; helper test `OK`; `php -l` em `Dev.php` e no model.
- [ ] **Step 5: Commit** — `git add application/controllers/adm/Dev.php application/models/Ficha_paciente_model.php tests/ficha_paciente_source_test.php` · `feat(ficha): migracao e model da ficha do paciente`

---

### Task 3: Controller `adm/Ficha` + formulário

**Files:** Create `application/controllers/adm/Ficha.php`, `application/views/adm/ficha/paciente.php` (pasta nova); Modify `tests/ficha_paciente_source_test.php`

**Interfaces:**
- Consumes: Task 1 (`utec_ficha_normalizar`, `utec_ficha_saude_mudou`, `utec_ficha_opcoes_*`, `utec_ficha_data_br`), Task 2 model.
- Produces: rota `adm/ficha/paciente/{id}` (GET/POST). POST campos = as 19 chaves. Flash `ficha_ok` (sucesso, redirect `adm/atendimento/prontuario/{id}`) / `ficha_erro` (redirect de volta ao formulário).

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$ctl = lerArquivo('application/controllers/adm/Ficha.php');
assertContains('class Ficha extends CI_Controller', $ctl, 'controller existe');
assertContains('verSession()', $ctl, 'exige sessao');
assertContains('if($nivel < 1 || $nivel > 4)', $ctl, 'bloqueia nivel 5');
assertContains('can_access_usuario($id)', $ctl, 'escopo do paciente');
assertContains('(int)$alvo->nivel !== 5', $ctl, 'so pacientes');
assertContains('utec_ficha_normalizar($this->input->post(), $pode_saude)', $ctl, 'descarta saude sem permissao');
assertContains('array(1, 2, 3)', $ctl, 'saude so 1-3');

$vf = lerArquivo('application/views/adm/ficha/paciente.php');
assertContains('name="alergias"', $vf, 'campo alergias');
assertContains('name="responsavel_cpf"', $vf, 'campo cpf responsavel');
assertContains('name="convenio_validade"', $vf, 'campo validade');
assertContains('$pode_editar_saude', $vf, 'saude condicionada');
assertContains('htmlspecialchars', $vf, 'view escapa');
```
- [ ] **Step 2: Run** → FAIL `controller existe`.

- [ ] **Step 3a: Controller** — `application/controllers/adm/Ficha.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ficha extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'ficha_paciente'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Ficha_paciente_model', 'ficha_model');
		$this->usuarios_model->verSession();

		$this->usuario = $this->padrao_model->get_usuario_logado();
		$nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
		if($nivel < 1 || $nivel > 4){
			show_error('Acesso restrito.', 403);
		}
	}

	private function pode_editar_saude()
	{
		return in_array((int)$this->usuario->nivel, array(1, 2, 3), true);
	}

	public function paciente($id_paciente = 0)
	{
		$id = (int)$id_paciente;
		if($id <= 1){ show_404(); return; }
		if(!$this->padrao_model->can_access_usuario($id)){
			show_error('Acesso negado ao paciente selecionado.', 403);
			return;
		}
		$alvo = $this->db->query("SELECT id, nome, nivel FROM usuarios WHERE id = ? LIMIT 1", array($id))->row();
		if(!$alvo || (int)$alvo->nivel !== 5){ show_404(); return; }

		$pode_saude = $this->pode_editar_saude();

		if($this->input->method() === 'post'){
			if(!$this->ficha_model->disponivel()){
				$this->session->set_flashdata('ficha_erro', 'As tabelas da ficha ainda não foram criadas.');
				redirect('adm/ficha/paciente/'.$id);
				return;
			}
			$r = utec_ficha_normalizar($this->input->post(), $pode_saude);
			if(!empty($r['erros'])){
				$this->session->set_flashdata('ficha_erro', implode(' ', $r['erros']));
				redirect('adm/ficha/paciente/'.$id);
				return;
			}
			$antes = $this->ficha_model->obter($id);
			$mudou = $pode_saude && utec_ficha_saude_mudou($antes, $r['dados']);
			if($this->ficha_model->salvar($id, $r['dados'], (int)$this->usuario->id, $mudou)){
				$this->session->set_flashdata('ficha_ok', 'Ficha do paciente atualizada.');
				redirect('adm/atendimento/prontuario/'.$id);
			}else{
				$this->session->set_flashdata('ficha_erro', 'Não foi possível salvar a ficha.');
				redirect('adm/ficha/paciente/'.$id);
			}
			return;
		}

		$dados = array(
			'paciente' => $alvo,
			'ficha' => $this->ficha_model->obter($id),
			'schema_ok' => $this->ficha_model->disponivel(),
			'pode_editar_saude' => $pode_saude,
			'opcoes_sexo' => utec_ficha_opcoes_sexo(),
			'opcoes_estado_civil' => utec_ficha_opcoes_estado_civil(),
			'opcoes_tipo_sanguineo' => utec_ficha_opcoes_tipo_sanguineo(),
			'flash_erro' => $this->session->flashdata('ficha_erro'),
		);
		$this->load->view('adm/ficha/paciente', $dados);
	}
}
```

- [ ] **Step 3b: View** — `application/views/adm/ficha/paciente.php` (esqueleto igual a `application/views/adm/horarios/index.php`):
```php
<!DOCTYPE html>
<html>
<head>
  <title>Ficha do paciente</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .fc-shell { max-width: 980px; }
    .fc-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .fc-panel h5 { font-weight:800; color:#0f172a; margin-bottom:12px; }
    .fc-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<?php
$e = function($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$f = $ficha;
$ro = $pode_editar_saude ? '' : ' readonly';
?>
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="fc-shell">
            <h4 style="font-weight:800;color:#0f172a;">Ficha do paciente</h4>
            <p class="fc-sub"><?=$e($paciente->nome)?> · <a href="<?=base_url('adm/atendimento/prontuario/'.(int)$paciente->id)?>">voltar ao prontuário</a></p>

            <? if($flash_erro){ ?><div class="alert alert-danger"><?=$e($flash_erro)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas da ficha ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_ficha_pacientes</code>.</div><? } ?>

            <form method="post" action="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>">
              <div class="fc-panel">
                <h5>Pessoal e responsável</h5>
                <div class="form-row">
                  <div class="form-group col-md-6"><label for="fc-nome-social">Nome social</label><input id="fc-nome-social" name="nome_social" maxlength="120" class="form-control" value="<?=$e($f['nome_social'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-sexo">Sexo</label>
                    <select id="fc-sexo" name="sexo" class="form-control"><option value="">Não informado</option>
                      <? foreach($opcoes_sexo as $k => $r){ if($k === 'nao_informado'){ continue; } ?><option value="<?=$e($k)?>" <?=$f['sexo'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                  <div class="form-group col-md-3"><label for="fc-estado-civil">Estado civil</label>
                    <select id="fc-estado-civil" name="estado_civil" class="form-control"><option value="">Não informado</option>
                      <? foreach($opcoes_estado_civil as $k => $r){ if($k === 'nao_informado'){ continue; } ?><option value="<?=$e($k)?>" <?=$f['estado_civil'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4"><label for="fc-resp-nome">Responsável legal</label><input id="fc-resp-nome" name="responsavel_nome" maxlength="120" class="form-control" value="<?=$e($f['responsavel_nome'])?>"></div>
                  <div class="form-group col-md-2"><label for="fc-resp-par">Parentesco</label><input id="fc-resp-par" name="responsavel_parentesco" maxlength="40" class="form-control" value="<?=$e($f['responsavel_parentesco'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-resp-tel">Telefone</label><input id="fc-resp-tel" name="responsavel_telefone" inputmode="tel" class="form-control" value="<?=$e(utec_ficha_telefone_fmt($f['responsavel_telefone']))?>"></div>
                  <div class="form-group col-md-3"><label for="fc-resp-cpf">CPF do responsável</label><input id="fc-resp-cpf" name="responsavel_cpf" inputmode="numeric" class="form-control" value="<?=$e($f['responsavel_cpf'])?>"></div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-5"><label for="fc-emg-nome">Contato de emergência</label><input id="fc-emg-nome" name="emergencia_nome" maxlength="120" class="form-control" value="<?=$e($f['emergencia_nome'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-emg-par">Parentesco</label><input id="fc-emg-par" name="emergencia_parentesco" maxlength="40" class="form-control" value="<?=$e($f['emergencia_parentesco'])?>"></div>
                  <div class="form-group col-md-4"><label for="fc-emg-tel">Telefone</label><input id="fc-emg-tel" name="emergencia_telefone" inputmode="tel" class="form-control" value="<?=$e(utec_ficha_telefone_fmt($f['emergencia_telefone']))?>"></div>
                </div>
              </div>

              <div class="fc-panel">
                <h5>Saúde básica</h5>
                <? if(!$pode_editar_saude){ ?><p class="fc-sub">Somente o estabelecimento e o profissional editam dados de saúde.</p><? } ?>
                <? if($f['saude_atualizado_em'] !== ''){ ?><p class="fc-sub">Última alteração de saúde: <?=$e($f['saude_atualizado_por_nome'] !== '' ? $f['saude_atualizado_por_nome'] : 'usuário #'.$f['saude_atualizado_por'])?> em <?=$e(utec_ficha_data_br($f['saude_atualizado_em']))?> <?=$e(substr($f['saude_atualizado_em'], 11, 5))?></p><? } ?>
                <div class="form-row">
                  <div class="form-group col-md-3"><label for="fc-ts">Tipo sanguíneo</label>
                    <select id="fc-ts" name="tipo_sanguineo" class="form-control" <?=$pode_editar_saude ? '' : 'disabled'?>><option value="">Não informado</option>
                      <? foreach($opcoes_tipo_sanguineo as $k => $r){ ?><option value="<?=$e($k)?>" <?=$f['tipo_sanguineo'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                </div>
                <div class="form-group"><label for="fc-alergias">Alergias</label><textarea id="fc-alergias" name="alergias" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['alergias'])?></textarea></div>
                <div class="form-group"><label for="fc-medicamentos">Medicamentos em uso</label><textarea id="fc-medicamentos" name="medicamentos" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['medicamentos'])?></textarea></div>
                <div class="form-group"><label for="fc-comorbidades">Comorbidades / doenças crônicas</label><textarea id="fc-comorbidades" name="comorbidades" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['comorbidades'])?></textarea></div>
                <div class="form-group"><label for="fc-obs-saude">Observações de saúde</label><textarea id="fc-obs-saude" name="obs_saude" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['obs_saude'])?></textarea></div>
              </div>

              <div class="fc-panel">
                <h5>Convênio</h5>
                <div class="form-row">
                  <div class="form-group col-md-4"><label for="fc-conv-nome">Convênio</label><input id="fc-conv-nome" name="convenio_nome" maxlength="80" class="form-control" value="<?=$e($f['convenio_nome'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-conv-plano">Plano</label><input id="fc-conv-plano" name="convenio_plano" maxlength="80" class="form-control" value="<?=$e($f['convenio_plano'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-conv-cart">Nº da carteirinha</label><input id="fc-conv-cart" name="convenio_carteirinha" maxlength="40" class="form-control" value="<?=$e($f['convenio_carteirinha'])?>"></div>
                  <div class="form-group col-md-2"><label for="fc-conv-val">Validade</label><input id="fc-conv-val" type="date" name="convenio_validade" class="form-control" value="<?=$e($f['convenio_validade'])?>"></div>
                </div>
              </div>

              <? if($schema_ok){ ?><button type="submit" class="btn btn-primary">Salvar ficha</button><? } ?>
              <a href="<?=base_url('adm/atendimento/prontuario/'.(int)$paciente->id)?>" class="btn btn-light">Cancelar</a>
            </form>
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

- [ ] **Step 4: Run** → source test `OK`; helper test `OK`; `php -l` no controller e na view.
- [ ] **Step 5: Commit** — `git add application/controllers/adm/Ficha.php application/views/adm/ficha/paciente.php tests/ficha_paciente_source_test.php` · `feat(ficha): tela de edicao da ficha do paciente`

---

### Task 4: Ficha no prontuário

**Files:** Modify `application/views/adm/usuarios/new/prontuario.php`, `tests/ficha_paciente_source_test.php`

**Interfaces — Consumes:** `Ficha_paciente_model::disponivel()/obter()`; helper `utec_ficha_rotulo_opcao`, `utec_ficha_opcoes_*`, `utec_ficha_telefone_fmt`, `utec_ficha_data_br` (carregados pelo model).

Âncoras atuais (linhas aproximadas na branch): primeiro `    </style>` (~325); linha do flash de erro dos rótulos `<?php if($ut_rot_flash_erro){ ?>...<?php } ?>` (~418); nome `<div class="section-heading" style="margin-bottom:6px"><?=$paciente->nome?></div>` (~424); fim do bloco dos chips de rótulos `<?php } ?>` logo após `ut-rotulos-linha` (~428); fim do bloco `<details class="pront-rotulos">…</details>` + `<?php } ?>` dentro de `.timeline-actions` (~488); `<div class="quick-metrics">` (~513). Cada âncora deve casar exatamente uma vez.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$pront = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains("load->model('Ficha_paciente_model', 'ficha_model')", $pront, 'prontuario carrega ficha');
assertContains('ficha_model->disponivel()', $pront, 'guarda sem migracao');
assertContains('class="ut-ficha-alergia"', $pront, 'alergias em destaque');
assertContains('class="ut-ficha-card"', $pront, 'card da ficha');
assertContains('adm/ficha/paciente/', $pront, 'link editar ficha');
assertContains("flashdata('ficha_ok')", $pront, 'flash da ficha');
```
- [ ] **Step 2: Run** → FAIL `prontuario carrega ficha`.

- [ ] **Step 3: Implement** — 5 inserções:

(a) CSS antes do primeiro `    </style>`:
```css
      .ut-ficha-alergia { margin-top:8px; display:inline-block; background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:8px; padding:4px 10px; font-weight:700; font-size:13px; }
      .ut-ficha-card { background:#fff; border:1px solid #dbe3ef; border-radius:16px; padding:18px 20px; margin:0 0 20px; }
      .ut-ficha-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:18px; }
      .ut-ficha-grid h6 { font-weight:800; color:#0f172a; margin-bottom:8px; }
      .ut-ficha-grid dt { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; margin-top:6px; }
      .ut-ficha-grid dd { margin:0; color:#0f172a; }
      .ut-ficha-vazio { color:#94a3b8; }
      @media (max-width: 767.98px){ .ut-ficha-grid { grid-template-columns:1fr; } }
```
(b) Logo após a linha do flash de erro dos rótulos:
```php
              <?php
              $ut_ficha_ok = false; $ut_ficha = array();
              $ut_ci_ficha =& get_instance();
              $ut_ci_ficha->load->model('Ficha_paciente_model', 'ficha_model');
              if($ut_ci_ficha->ficha_model->disponivel()){
                $ut_ficha_ok = true;
                $ut_ficha = $ut_ci_ficha->ficha_model->obter((int)$paciente->id);
              }
              $ut_ficha_flash_ok = $this->session->flashdata('ficha_ok');
              ?>
              <?php if($ut_ficha_flash_ok){ ?><div class="alert alert-success" style="margin-bottom:20px;"><?=htmlspecialchars($ut_ficha_flash_ok)?></div><?php } ?>
```
(c) Logo após a linha do nome (`section-heading` com `$paciente->nome`):
```php
                        <?php if($ut_ficha_ok && $ut_ficha['nome_social'] !== ''){ ?><div style="margin:-4px 0 6px;color:#5f708c;font-size:13px;">Nome social: <strong><?=htmlspecialchars($ut_ficha['nome_social'], ENT_QUOTES, 'UTF-8')?></strong></div><?php } ?>
```
(d) Logo após o `<?php } ?>` que fecha o bloco dos chips de rótulos (`ut-rotulos-linha`):
```php
                        <?php if($ut_ficha_ok && trim($ut_ficha['alergias']) !== ''){
                          $ut_alg = str_replace("\n", ' · ', $ut_ficha['alergias']);
                          if(function_exists('mb_strlen') && mb_strlen($ut_alg, 'UTF-8') > 160){ $ut_alg = mb_substr($ut_alg, 0, 160, 'UTF-8').'…'; }
                        ?>
                          <div class="ut-ficha-alergia" role="alert">&#9888; Alergias: <?=htmlspecialchars($ut_alg, ENT_QUOTES, 'UTF-8')?></div>
                        <?php } ?>
```
(e) Botão: logo após o `<?php } ?>` que fecha o `<details class="pront-rotulos">` (ainda dentro de `.timeline-actions`):
```php
                        <?php if($ut_ficha_ok){ ?><a href="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>" class="btn btn-outline-secondary">Editar ficha</a><?php } ?>
```
(f) Card: imediatamente antes de `<div class="quick-metrics">`:
```php
              <?php if($ut_ficha_ok){
                $ut_fv = function($valor){ return $valor !== '' ? nl2br(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')) : '<span class="ut-ficha-vazio">Não informado</span>'; };
                $ut_fj = function(array $partes){ $partes = array_values(array_filter($partes, function($p){ return $p !== ''; })); return implode(' · ', $partes); };
              ?>
              <div class="ut-ficha-card">
                <div class="d-flex justify-content-between align-items-center" style="margin-bottom:12px;gap:12px;">
                  <div class="section-heading" style="font-size:18px;margin:0;">Ficha do paciente</div>
                  <a href="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>" class="btn btn-sm btn-outline-secondary">Editar ficha</a>
                </div>
                <div class="ut-ficha-grid">
                  <div>
                    <h6>Pessoal / responsável</h6>
                    <dl>
                      <dt>Sexo</dt><dd><?=htmlspecialchars(utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), $ut_ficha['sexo']), ENT_QUOTES, 'UTF-8')?></dd>
                      <dt>Estado civil</dt><dd><?=htmlspecialchars(utec_ficha_rotulo_opcao(utec_ficha_opcoes_estado_civil(), $ut_ficha['estado_civil']), ENT_QUOTES, 'UTF-8')?></dd>
                      <dt>Responsável</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['responsavel_nome'], $ut_ficha['responsavel_parentesco'], utec_ficha_telefone_fmt($ut_ficha['responsavel_telefone']))))?></dd>
                      <dt>Emergência</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['emergencia_nome'], $ut_ficha['emergencia_parentesco'], utec_ficha_telefone_fmt($ut_ficha['emergencia_telefone']))))?></dd>
                    </dl>
                  </div>
                  <div>
                    <h6>Saúde</h6>
                    <dl>
                      <dt>Tipo sanguíneo</dt><dd><?=$ut_fv($ut_ficha['tipo_sanguineo'])?></dd>
                      <dt>Alergias</dt><dd><?=$ut_fv($ut_ficha['alergias'])?></dd>
                      <dt>Medicamentos</dt><dd><?=$ut_fv($ut_ficha['medicamentos'])?></dd>
                      <dt>Comorbidades</dt><dd><?=$ut_fv($ut_ficha['comorbidades'])?></dd>
                      <? if($ut_ficha['obs_saude'] !== ''){ ?><dt>Observações</dt><dd><?=$ut_fv($ut_ficha['obs_saude'])?></dd><? } ?>
                    </dl>
                  </div>
                  <div>
                    <h6>Convênio</h6>
                    <dl>
                      <dt>Convênio</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['convenio_nome'], $ut_ficha['convenio_plano'])))?></dd>
                      <dt>Carteirinha</dt><dd><?=$ut_fv($ut_ficha['convenio_carteirinha'])?></dd>
                      <dt>Validade</dt><dd><?=$ut_fv(utec_ficha_data_br($ut_ficha['convenio_validade']))?></dd>
                    </dl>
                  </div>
                </div>
              </div>
              <?php } ?>
```

- [ ] **Step 4: Run** → `tests/ficha_paciente_source_test.php`, `tests/ficha_paciente_helper_test.php`, `tests/rotulos_source_test.php`, `tests/prontuario_export_source_test.php` → `OK`; `php -l` na view.
- [ ] **Step 5: Commit** — `git add application/views/adm/usuarios/new/prontuario.php tests/ficha_paciente_source_test.php` · `feat(ficha): ficha e alergias em destaque no prontuario`

---

### Task 5: Manual + CLAUDE.md

**Files:** Modify `application/libraries/Manual_conteudo.php`, `CLAUDE.md`, `tests/ficha_paciente_source_test.php`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Editar ficha', $manual, 'manual cobre ficha');
$claude = lerArquivo('CLAUDE.md');
assertContains('migrar_ficha_pacientes', $claude, 'CLAUDE.md documenta migracao');
```
- [ ] **Step 2: Run** → FAIL `manual cobre ficha`.
- [ ] **Step 3a: Manual** — capítulo `'slug' => 'pacientes-cadastro'`, array `'*'`, acrescentar:
`'No prontuário, use `Editar ficha` para registrar nome social, responsável legal, contato de emergência, convênio (plano, carteirinha e validade) e dados de saúde: tipo sanguíneo, alergias, medicamentos em uso e comorbidades.'`;
array `4` acrescentar: `'Preenche os dados pessoais e de convênio da ficha; os dados de saúde ficam visíveis, mas só o estabelecimento e o profissional podem alterá-los.'`.
Capítulo `'slug' => 'prontuario'`, array `'*'`, acrescentar: `'Alergias registradas na ficha do paciente aparecem em destaque (⚠) no topo do prontuário.'`.
Atualizar `'atualizado_em'` dos dois capítulos para `'2026-10-05'`. Não mudar `VERSAO`.
- [ ] **Step 3b: CLAUDE.md** (apenas acréscimos, formato das linhas vizinhas):
  - §4.2 **Saúde e Agenda**: `- \`pacientes_ficha\` — ficha 1:1 do paciente (pessoal/responsável, saúde básica com \`saude_atualizado_por/em\`, convênio)`.
  - §6.2: `| \`Ficha.php\` | \`/adm/ficha/paciente/{id}\` | Ficha do paciente (GET/POST): níveis 1–4 veem e editam pessoal/convênio; saúde só 1–3 |`.
  - §13: `| \`adm/dev/migrar_ficha_pacientes\` | Cria \`pacientes_ficha\` (idempotente) |`.
  - §15.1: `- [x] Ficha do paciente (pessoal/responsável, saúde básica, convênio) com alergias em destaque no prontuário (\`ficha_paciente_helper.php\`, \`Ficha_paciente_model\`). Entrega B (campos configuráveis por clínica) pendente.`
- [ ] **Step 4: Run** → todos `tests/ficha_paciente_*`, `tests/rotulos_*`, `tests/prontuario_export_*`, `tests/disponibilidade_*` `OK`; `php -l application/libraries/Manual_conteudo.php`.
- [ ] **Step 5: Commit** — `git add application/libraries/Manual_conteudo.php CLAUDE.md tests/ficha_paciente_source_test.php` · `docs(ficha): manual e CLAUDE.md`

---

## Deploy (depois da revisão e do ok do usuário)
1. Baixar do servidor `Dev.php`, `Manual_conteudo.php`, `prontuario.php` e comparar com a branch/`main`; conferir que `application/views/adm/ficha` não existe como *arquivo*.
2. Ordem: `ficha_paciente_helper.php` → `Ficha_paciente_model.php` → `views/adm/ficha/paciente.php` → `controllers/adm/Ficha.php` → `Dev.php` → `Manual_conteudo.php` → `prontuario.php` (por último).
3. Baixar de novo e `cmp`; healthcheck (home/`admin` 200; `adm/ficha/paciente/2` sem sessão 302).
4. Rodar `adm/dev/migrar_ficha_pacientes`; testar: nível 3 preenche tudo (inclusive CPF inválido → erro); nível 4 edita convênio e vê saúde só leitura; alergias aparecem no topo do prontuário.
