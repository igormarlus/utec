# Tempo de Espera Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Registrar chegada (check-in), início e fim de cada atendimento e exibir espera/atraso/duração na agenda e médias em Relatórios clínicos.

**Architecture:** Funções puras em `tempo_atendimento_helper.php` (transições, minutos, métricas, formatação, elegibilidade do check-in, fragmento SQL de agregação). `Atendimento::set_status_agenda()` e `remarcar_agenda()` passam a gravar/zerar horários; novo `Atendimento::checkin()`. `Usuarios::relatorios_clinicos()` agrega com `TIMESTAMPDIFF`. Tudo guardado por `field_exists` — sem migração, comportamento atual.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MariaDB 10.11, Bootstrap 4 + jQuery.

Spec: `docs/superpowers/specs/2026-10-05-tempo-espera-design.md` · Branch: `feat/tempo-espera` (empilhada sobre `feat/ficha-paciente`).

## Global Constraints
- PHP: testes e `php -l` só com `C:/PHP/PHP7.2/php.exe` (PATH `php` é PHP 8). Sem sintaxe PHP 8.
- CI3 apenas; nunca `$_POST`/`$_GET`; não tocar `system/`.
- Colunas novas em `agendamentos`: `chegada_em DATETIME NULL`, `chegada_por INT NULL`, `inicio_atendimento_em DATETIME NULL`, `fim_atendimento_em DATETIME NULL`. Migração `adm/dev/migrar_tempos_atendimento`.
- Guarda de schema: `field_exists('chegada_em', 'agendamentos') && field_exists('inicio_atendimento_em', 'agendamentos')`. Sem as colunas, nada novo é gravado nem exibido.
- Transições: 0→1 grava início e zera fim; 1→2 grava fim; 2→0 zera início e fim (mantém chegada); 3→0 zera os 4 campos; demais → nada. Remarcação zera os 4 campos. Cancelamento não mexe nos horários.
- Check-in: POST `adm/atendimento/checkin/{id}`; níveis 1–4 + `can_access_agendamento`; marcar só se `status = 0`, `data_agenda = hoje` e sem chegada; `acao=desfazer` só se há chegada e não há início. `voltar` aceito só se casa `#^adm/[a-z0-9_/]*$#i`, senão `adm/atendimento`. Flash `tempo_ok` / `tempo_erro`.
- Minutos = diferença em segundos / 60, truncada em direção a zero. Espera e duração válidas em 0..480; atraso válido em −480..480; fora disso → `null` (e fora das médias).
- Formatação: `18 min`, `1 h 05 min`, `-5 min`; `null` → `''`.
- Toda saída textual escapada com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Testes: scripts PHP em `tests/`, `exit(1)` na falha, imprimem `OK`.
- Commits: `git add <paths>` explícito (nunca `-u`/`-A`); mensagem termina com linha em branco + `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`. Preservar line endings (várias views são CRLF).

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/tempo_atendimento_helper.php` | Criar | Funções puras |
| `tests/tempo_atendimento_helper_test.php` | Criar | Testes do helper |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_tempos_atendimento()` |
| `application/controllers/adm/Atendimento.php` | Modificar | helper no construtor, `set_status_agenda`, `remarcar_agenda`, `checkin`, `tem_colunas_tempo` |
| `tests/tempo_atendimento_source_test.php` | Criar | Teste estático |
| `application/controllers/adm/Usuarios.php` | Modificar | agregação em `relatorios_clinicos()` |
| `application/views/adm/relatorios/clinicos.php` | Modificar | cards + tabela |
| `application/views/adm/usuarios/new/atendimentos.php` | Modificar | resumo, botões, flash |
| `application/libraries/Manual_conteudo.php`, `CLAUDE.md` | Modificar | Docs |

---

### Task 1: Helper puro

**Files:** Create `application/helpers/tempo_atendimento_helper.php`, `tests/tempo_atendimento_helper_test.php`

**Interfaces — Produces:**
- `utec_tempo_campos_transicao($status_atual, $status_novo, $agora): array`
- `utec_tempo_campos_zerados(): array` (4 chaves → `null`)
- `utec_tempo_ts($v): ?int` (timestamp ou `null` para vazio/`0000-00-00...`/inválido)
- `utec_tempo_minutos_entre($de, $ate): ?int`
- `utec_tempo_metricas($ag): array('espera'=>?int,'atraso'=>?int,'duracao'=>?int)` (`$ag` objeto ou array com `data_agenda`, `hora_agenda`, `chegada_em`, `inicio_atendimento_em`, `fim_atendimento_em`)
- `utec_tempo_formatar($min): string`
- `utec_tempo_resumo_agenda($ag): string`
- `utec_tempo_pode_checkin($ag, $hoje): bool`, `utec_tempo_pode_desfazer_checkin($ag): bool`
- `utec_tempo_sql_agregados($alias): string` (fragmento SELECT com `espera_media, espera_n, atraso_medio, atraso_n, duracao_media, duracao_n`)

- [ ] **Step 1: Failing test** — `tests/tempo_atendimento_helper_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/tempo_atendimento_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
function assertTrue($cond, $label) { if (!$cond) { fwrite(STDERR, $label . PHP_EOL); exit(1); } }

$agora = '2026-10-05 14:30:00';
$zero = array('chegada_em' => null, 'chegada_por' => null, 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);

// --- transições
assertSameValue(array('inicio_atendimento_em' => $agora, 'fim_atendimento_em' => null), utec_tempo_campos_transicao(0, 1, $agora), '0->1');
assertSameValue(array('fim_atendimento_em' => $agora), utec_tempo_campos_transicao('1', 2, $agora), '1->2');
assertSameValue(array('inicio_atendimento_em' => null, 'fim_atendimento_em' => null), utec_tempo_campos_transicao(2, 0, $agora), '2->0');
assertSameValue($zero, utec_tempo_campos_transicao(3, 0, $agora), '3->0');
assertSameValue(array(), utec_tempo_campos_transicao(0, 3, $agora), 'cancelar nao mexe');
assertSameValue(array(), utec_tempo_campos_transicao(1, 0, $agora), 'transicao invalida');
assertSameValue($zero, utec_tempo_campos_zerados(), 'zerados');

// --- timestamps e minutos
assertSameValue(null, utec_tempo_ts(''), 'ts vazio');
assertSameValue(null, utec_tempo_ts('0000-00-00 00:00:00'), 'ts zero');
assertSameValue(null, utec_tempo_ts('lixo'), 'ts invalido');
assertSameValue(18, utec_tempo_minutos_entre('2026-10-05 14:00:00', '2026-10-05 14:18:59'), 'trunca 18m59s');
assertSameValue(-1, utec_tempo_minutos_entre('2026-10-05 14:01:30', '2026-10-05 14:00:00'), 'negativo trunca para zero');
assertSameValue(null, utec_tempo_minutos_entre(null, '2026-10-05 14:00:00'), 'ausente');

// --- métricas
$ag = array('data_agenda' => '2026-10-05', 'hora_agenda' => '14:00:00', 'chegada_em' => '2026-10-05 13:50:00',
    'inicio_atendimento_em' => '2026-10-05 14:08:00', 'fim_atendimento_em' => '2026-10-05 14:40:00');
assertSameValue(array('espera' => 18, 'atraso' => 8, 'duracao' => 32), utec_tempo_metricas($ag), 'metricas completas');
assertSameValue(array('espera' => 18, 'atraso' => 8, 'duracao' => 32), utec_tempo_metricas((object)$ag), 'aceita objeto');
$adiantado = $ag; $adiantado['inicio_atendimento_em'] = '2026-10-05 13:55:00'; $adiantado['chegada_em'] = '2026-10-05 13:40:00';
assertSameValue(-5, utec_tempo_metricas($adiantado)['atraso'], 'atraso negativo');
$sem = array('data_agenda' => '2026-10-05', 'hora_agenda' => '14:00', 'chegada_em' => '', 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);
assertSameValue(array('espera' => null, 'atraso' => null, 'duracao' => null), utec_tempo_metricas($sem), 'sem horarios');
$esquecido = $ag; $esquecido['fim_atendimento_em'] = '2026-10-06 09:00:00';
assertSameValue(null, utec_tempo_metricas($esquecido)['duracao'], 'duracao > 480 ignorada');
$invertido = $ag; $invertido['chegada_em'] = '2026-10-05 14:20:00';
assertSameValue(null, utec_tempo_metricas($invertido)['espera'], 'espera negativa ignorada');
$sem_hora = $ag; $sem_hora['hora_agenda'] = '';
assertSameValue(null, utec_tempo_metricas($sem_hora)['atraso'], 'sem hora marcada');

// --- formatação e resumo
assertSameValue('18 min', utec_tempo_formatar(18), 'fmt min');
assertSameValue('1 h 05 min', utec_tempo_formatar(65), 'fmt hora');
assertSameValue('-5 min', utec_tempo_formatar(-5), 'fmt negativo');
assertSameValue('0 min', utec_tempo_formatar(0), 'fmt zero');
assertSameValue('', utec_tempo_formatar(null), 'fmt null');
assertSameValue('Chegou 13:50 · esperou 18 min · consulta 32 min', utec_tempo_resumo_agenda($ag), 'resumo completo');
$so_chegada = $sem; $so_chegada['chegada_em'] = '2026-10-05 13:50:00';
assertSameValue('Chegou 13:50', utec_tempo_resumo_agenda($so_chegada), 'resumo so chegada');
assertSameValue('', utec_tempo_resumo_agenda($sem), 'resumo vazio');

// --- elegibilidade
$pend = array('status' => '0', 'data_agenda' => '2026-10-05', 'chegada_em' => null, 'inicio_atendimento_em' => null);
assertTrue(utec_tempo_pode_checkin($pend, '2026-10-05'), 'pode checkin');
assertTrue(!utec_tempo_pode_checkin($pend, '2026-10-06'), 'outro dia');
$p2 = $pend; $p2['status'] = '1';
assertTrue(!utec_tempo_pode_checkin($p2, '2026-10-05'), 'ja em atendimento');
$p3 = $pend; $p3['chegada_em'] = '2026-10-05 13:50:00';
assertTrue(!utec_tempo_pode_checkin($p3, '2026-10-05'), 'ja chegou');
assertTrue(utec_tempo_pode_desfazer_checkin($p3), 'pode desfazer');
$p4 = $p3; $p4['inicio_atendimento_em'] = '2026-10-05 14:00:00';
assertTrue(!utec_tempo_pode_desfazer_checkin($p4), 'nao desfaz apos inicio');
assertTrue(!utec_tempo_pode_desfazer_checkin($pend), 'nao desfaz sem chegada');

// --- SQL
$sql = utec_tempo_sql_agregados('a');
assertTrue(strpos($sql, 'TIMESTAMPDIFF(MINUTE, a.chegada_em, a.inicio_atendimento_em)') !== false, 'sql espera');
assertTrue(strpos($sql, "CONCAT(a.data_agenda, ' ', a.hora_agenda)") !== false, 'sql atraso');
assertTrue(strpos($sql, 'BETWEEN -480 AND 480') !== false, 'sql limite atraso');
foreach (array('espera_media', 'espera_n', 'atraso_medio', 'atraso_n', 'duracao_media', 'duracao_n') as $c) {
    assertTrue(strpos($sql, 'AS ' . $c) !== false, 'sql coluna ' . $c);
}
assertTrue(strpos(utec_tempo_sql_agregados('a; DROP'), 'DROP') === false, 'alias sanitizado');

echo "OK\n";
```
- [ ] **Step 2: Run** → `C:/PHP/PHP7.2/php.exe tests/tempo_atendimento_helper_test.php` falha (arquivo ausente).
- [ ] **Step 3: Implement** — `application/helpers/tempo_atendimento_helper.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras do tempo de atendimento (check-in, espera, atraso, duração) — testes em tests/tempo_atendimento_*.

if(!function_exists('utec_tempo_campos_zerados')){
	function utec_tempo_campos_zerados(){
		return array('chegada_em' => null, 'chegada_por' => null, 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);
	}
}

if(!function_exists('utec_tempo_campos_transicao')){
	function utec_tempo_campos_transicao($status_atual, $status_novo, $agora){
		$a = (int)$status_atual;
		$n = (int)$status_novo;
		if($a === 0 && $n === 1){ return array('inicio_atendimento_em' => $agora, 'fim_atendimento_em' => null); }
		if($a === 1 && $n === 2){ return array('fim_atendimento_em' => $agora); }
		if($a === 2 && $n === 0){ return array('inicio_atendimento_em' => null, 'fim_atendimento_em' => null); }
		if($a === 3 && $n === 0){ return utec_tempo_campos_zerados(); }
		return array();
	}
}

if(!function_exists('utec_tempo_ts')){
	function utec_tempo_ts($v){
		$v = trim((string)$v);
		if($v === '' || strpos($v, '0000-00-00') === 0){ return null; }
		$t = strtotime($v);
		return $t === false ? null : $t;
	}
}

if(!function_exists('utec_tempo_minutos_entre')){
	function utec_tempo_minutos_entre($de, $ate){
		$a = utec_tempo_ts($de);
		$b = utec_tempo_ts($ate);
		if($a === null || $b === null){ return null; }
		return (int)(($b - $a) / 60);
	}
}

if(!function_exists('utec_tempo_metricas')){
	function utec_tempo_metricas($ag){
		$ag = (array)$ag;
		$g = function($k) use ($ag){ return (array_key_exists($k, $ag) && $ag[$k] !== null) ? (string)$ag[$k] : ''; };
		$marcado = ($g('data_agenda') !== '' && $g('hora_agenda') !== '') ? substr($g('data_agenda'), 0, 10).' '.$g('hora_agenda') : '';
		$espera = utec_tempo_minutos_entre($g('chegada_em'), $g('inicio_atendimento_em'));
		if($espera !== null && ($espera < 0 || $espera > 480)){ $espera = null; }
		$atraso = utec_tempo_minutos_entre($marcado, $g('inicio_atendimento_em'));
		if($atraso !== null && abs($atraso) > 480){ $atraso = null; }
		$duracao = utec_tempo_minutos_entre($g('inicio_atendimento_em'), $g('fim_atendimento_em'));
		if($duracao !== null && ($duracao < 0 || $duracao > 480)){ $duracao = null; }
		return array('espera' => $espera, 'atraso' => $atraso, 'duracao' => $duracao);
	}
}

if(!function_exists('utec_tempo_formatar')){
	function utec_tempo_formatar($min){
		if($min === null || $min === ''){ return ''; }
		$m = (int)$min;
		$abs = abs($m);
		$txt = $abs >= 60 ? (int)floor($abs / 60).' h '.str_pad((string)($abs % 60), 2, '0', STR_PAD_LEFT).' min' : $abs.' min';
		return ($m < 0 ? '-' : '').$txt;
	}
}

if(!function_exists('utec_tempo_resumo_agenda')){
	function utec_tempo_resumo_agenda($ag){
		$arr = (array)$ag;
		$partes = array();
		$chegada = utec_tempo_ts(array_key_exists('chegada_em', $arr) ? $arr['chegada_em'] : '');
		if($chegada !== null){ $partes[] = 'Chegou '.date('H:i', $chegada); }
		$m = utec_tempo_metricas($arr);
		if($m['espera'] !== null){ $partes[] = 'esperou '.utec_tempo_formatar($m['espera']); }
		if($m['duracao'] !== null){ $partes[] = 'consulta '.utec_tempo_formatar($m['duracao']); }
		return implode(' · ', $partes);
	}
}

if(!function_exists('utec_tempo_pode_checkin')){
	function utec_tempo_pode_checkin($ag, $hoje){
		$ag = (array)$ag;
		if(!array_key_exists('status', $ag) || (int)$ag['status'] !== 0){ return false; }
		if(!array_key_exists('data_agenda', $ag) || substr((string)$ag['data_agenda'], 0, 10) !== (string)$hoje){ return false; }
		return utec_tempo_ts(array_key_exists('chegada_em', $ag) ? $ag['chegada_em'] : '') === null;
	}
}

if(!function_exists('utec_tempo_pode_desfazer_checkin')){
	function utec_tempo_pode_desfazer_checkin($ag){
		$ag = (array)$ag;
		$chegou = utec_tempo_ts(array_key_exists('chegada_em', $ag) ? $ag['chegada_em'] : '') !== null;
		$iniciou = utec_tempo_ts(array_key_exists('inicio_atendimento_em', $ag) ? $ag['inicio_atendimento_em'] : '') !== null;
		return $chegou && !$iniciou;
	}
}

if(!function_exists('utec_tempo_sql_agregados')){
	function utec_tempo_sql_agregados($alias){
		$a = preg_replace('/[^a-z_]/i', '', (string)$alias);
		if($a === ''){ $a = 'a'; }
		$esp = 'TIMESTAMPDIFF(MINUTE, '.$a.'.chegada_em, '.$a.'.inicio_atendimento_em)';
		$atr = "TIMESTAMPDIFF(MINUTE, CONCAT(".$a.".data_agenda, ' ', ".$a.".hora_agenda), ".$a.".inicio_atendimento_em)";
		$dur = 'TIMESTAMPDIFF(MINUTE, '.$a.'.inicio_atendimento_em, '.$a.'.fim_atendimento_em)';
		return 'AVG(CASE WHEN '.$esp.' BETWEEN 0 AND 480 THEN '.$esp.' END) AS espera_media, '
			.'COUNT(CASE WHEN '.$esp.' BETWEEN 0 AND 480 THEN 1 END) AS espera_n, '
			.'AVG(CASE WHEN '.$atr.' BETWEEN -480 AND 480 THEN '.$atr.' END) AS atraso_medio, '
			.'COUNT(CASE WHEN '.$atr.' BETWEEN -480 AND 480 THEN 1 END) AS atraso_n, '
			.'AVG(CASE WHEN '.$dur.' BETWEEN 0 AND 480 THEN '.$dur.' END) AS duracao_media, '
			.'COUNT(CASE WHEN '.$dur.' BETWEEN 0 AND 480 THEN 1 END) AS duracao_n';
	}
}
```
Nota: a string `' · '` usa o caractere U+00B7 (o arquivo é UTF-8).
- [ ] **Step 4: Run** → `OK`; `php -l` no helper.
- [ ] **Step 5: Commit** — `git add application/helpers/tempo_atendimento_helper.php tests/tempo_atendimento_helper_test.php` · `feat(tempo): helper puro (transicoes, metricas, check-in, sql)`

---

### Task 2: Migração + gravação dos horários + check-in

**Files:** Modify `application/controllers/adm/Dev.php`, `application/controllers/adm/Atendimento.php`; Create `tests/tempo_atendimento_source_test.php`

**Interfaces:**
- Consumes: Task 1 (`utec_tempo_campos_transicao`, `utec_tempo_campos_zerados`, `utec_tempo_pode_checkin`, `utec_tempo_pode_desfazer_checkin`); `Dev::ensure_column($table, $column, $definition, &$logs)`; `Padrao_model::can_access_agendamento($id)`.
- Produces: rota `adm/atendimento/checkin/{id}` (POST: `voltar`, `acao` opcional = `desfazer`); flashes `tempo_ok`/`tempo_erro`; `Atendimento::tem_colunas_tempo()` (private).

- [ ] **Step 1: Failing test** — `tests/tempo_atendimento_source_test.php`:
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
assertContains('function migrar_tempos_atendimento()', $dev, 'migracao existe');
foreach (array("'chegada_em', \"DATETIME NULL\"", "'chegada_por', \"INT NULL\"", "'inicio_atendimento_em', \"DATETIME NULL\"", "'fim_atendimento_em', \"DATETIME NULL\"") as $c) {
    assertContains($c, $dev, 'coluna ' . $c);
}

$ctl = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains("'tempo_atendimento'", $ctl, 'helper carregado no construtor');
assertContains('private function tem_colunas_tempo()', $ctl, 'guarda de schema');
assertContains('utec_tempo_campos_transicao((int)$status, $new_status', $ctl, 'set_status grava horarios');
assertContains('utec_tempo_campos_zerados()', $ctl, 'remarcar zera horarios');
assertContains('function checkin(', $ctl, 'endpoint checkin');
assertContains("input->method() !== 'post'", $ctl, 'checkin so POST');
assertContains('can_access_agendamento($id_agenda)', $ctl, 'checkin respeita escopo');
assertContains("preg_match('#^adm/[a-z0-9_/]*$#i', \$voltar)", $ctl, 'voltar restrito');

echo "OK\n";
```
- [ ] **Step 2: Run** → FAIL `migracao existe`.

- [ ] **Step 3a: Migração** — em `Dev.php`, logo após o fechamento de `migrar_ficha_pacientes()`:
```php
	function migrar_tempos_atendimento(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->ensure_column('agendamentos', 'chegada_em', "DATETIME NULL", $logs);
		$this->ensure_column('agendamentos', 'chegada_por', "INT NULL", $logs);
		$this->ensure_column('agendamentos', 'inicio_atendimento_em', "DATETIME NULL", $logs);
		$this->ensure_column('agendamentos', 'fim_atendimento_em', "DATETIME NULL", $logs);

		echo '<h3>Migração: tempos de atendimento</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 3b: Atendimento.php**
1. Construtor: trocar `$this->load->helper(array('form','url','whatsapp_agendamento'));` por `$this->load->helper(array('form','url','whatsapp_agendamento','tempo_atendimento'));`.
2. `set_status_agenda()`: substituir
```php
	$dd_status = array('status' => $new_status);
```
por
```php
	$dd_status = array('status' => $new_status);
	if($this->tem_colunas_tempo()){
		$dd_status = array_merge($dd_status, utec_tempo_campos_transicao((int)$status, $new_status, date('Y-m-d H:i:s')));
	}
```
3. `remarcar_agenda()`: dentro de `if($atualizado){`, como primeira instrução:
```php
		if($this->tem_colunas_tempo()){
			$this->db->where('id', $id_agenda);
			$this->db->update('agendamentos', utec_tempo_campos_zerados());
		}
```
4. Novos métodos, logo após o fechamento de `remarcar_agenda()`:
```php
function checkin($id_agenda = 0){
	$id_agenda = (int)$id_agenda;
	if($this->input->method() !== 'post' || $id_agenda <= 0){ show_404(); return; }
	if(!$this->padrao_model->can_access_agendamento($id_agenda)){
		show_error('Acesso negado ao atendimento selecionado.', 403);
		return;
	}
	$nivel = (int)$this->session->userdata('nivel');
	if($nivel < 1 || $nivel > 4){ show_error('Acesso negado.', 403); return; }
	$voltar = (string)$this->input->post('voltar', true);
	if(!preg_match('#^adm/[a-z0-9_/]*$#i', $voltar)){ $voltar = 'adm/atendimento'; }
	if(!$this->tem_colunas_tempo()){
		$this->session->set_flashdata('tempo_erro', 'Execute a migração adm/dev/migrar_tempos_atendimento.');
		redirect($voltar);
		return;
	}
	$ag = $this->db->query(
		"SELECT id, status, data_agenda, chegada_em, inicio_atendimento_em FROM agendamentos WHERE id = ? LIMIT 1",
		array($id_agenda)
	)->row_array();
	if(!$ag){ show_404(); return; }

	if($this->input->post('acao') === 'desfazer'){
		if(utec_tempo_pode_desfazer_checkin($ag)){
			$this->db->where('id', $id_agenda);
			$this->db->update('agendamentos', array('chegada_em' => null, 'chegada_por' => null));
			$this->session->set_flashdata('tempo_ok', 'Chegada desfeita.');
		}else{
			$this->session->set_flashdata('tempo_erro', 'Não é possível desfazer: o atendimento já começou.');
		}
	}else{
		if(utec_tempo_pode_checkin($ag, date('Y-m-d'))){
			$this->db->where('id', $id_agenda);
			$this->db->update('agendamentos', array('chegada_em' => date('Y-m-d H:i:s'), 'chegada_por' => (int)$this->session->userdata('id')));
			$this->session->set_flashdata('tempo_ok', 'Chegada registrada.');
		}else{
			$this->session->set_flashdata('tempo_erro', 'O check-in vale só para agendamentos pendentes de hoje.');
		}
	}
	redirect($voltar);
}

private function tem_colunas_tempo(){
	return $this->db->field_exists('chegada_em', 'agendamentos') && $this->db->field_exists('inicio_atendimento_em', 'agendamentos');
}
```
- [ ] **Step 4: Run** → source test `OK`; helper test `OK`; `php -l` em `Dev.php` e `Atendimento.php`; `tests/prontuario_export_source_test.php` e `tests/atendimento_remarcar_whatsapp_test.php` (se existir) continuam `OK`.
- [ ] **Step 5: Commit** — `git add application/controllers/adm/Dev.php application/controllers/adm/Atendimento.php tests/tempo_atendimento_source_test.php` · `feat(tempo): migracao, horarios nas transicoes de status e check-in`

---

### Task 3: Relatórios clínicos

**Files:** Modify `application/controllers/adm/Usuarios.php` (`relatorios_clinicos()`, antes de `$this->load->view('adm/relatorios/clinicos', $dados);`), `application/views/adm/relatorios/clinicos.php`, `tests/tempo_atendimento_source_test.php`

**Interfaces — Consumes:** `utec_tempo_sql_agregados('a')`, `utec_tempo_formatar()`; `$where_agenda_sql` já existente no método (usa alias `a`). **Produces:** `$tempos` na view = `null` (sem colunas) ou `array('geral' => array(espera_media, espera_n, atraso_medio, atraso_n, duracao_media, duracao_n), 'por_profissional' => array de linhas com 'prestador_nome' + as mesmas chaves)`.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$usr = lerArquivo('application/controllers/adm/Usuarios.php');
assertContains("utec_tempo_sql_agregados('a')", $usr, 'relatorio usa agregacao');
assertContains("\$dados['tempos']", $usr, 'relatorio passa tempos');
$rel = lerArquivo('application/views/adm/relatorios/clinicos.php');
assertContains('Espera média', $rel, 'card espera');
assertContains('Tempos por profissional', $rel, 'tabela por profissional');
```
- [ ] **Step 2: Run** → FAIL `relatorio usa agregacao`.
- [ ] **Step 3a: Controller** — imediatamente antes de `$this->load->view('adm/relatorios/clinicos', $dados);` em `relatorios_clinicos()`:
```php
		$dados['tempos'] = null;
		if($this->db->field_exists('chegada_em', 'agendamentos') && $this->db->field_exists('inicio_atendimento_em', 'agendamentos')){
			$this->load->helper('tempo_atendimento');
			$sel_tempos = utec_tempo_sql_agregados('a');
			$dados['tempos'] = array(
				'geral' => $this->db->query("SELECT ".$sel_tempos." FROM agendamentos a ".$where_agenda_sql)->row_array(),
				'por_profissional' => $this->db->query(
					"SELECT pr.nome AS prestador_nome, ".$sel_tempos."
					FROM agendamentos a
					LEFT JOIN usuarios pr ON pr.id = a.id_prestador
					".$where_agenda_sql."
					GROUP BY a.id_prestador, pr.nome
					ORDER BY pr.nome ASC"
				)->result_array(),
			);
		}
```
- [ ] **Step 3b: View** — em `application/views/adm/relatorios/clinicos.php`, imediatamente antes do primeiro `<div class="report-panel">` (o painel "Resumo por profissional", logo após a `row` dos 6 cards):
```php
              <?php if(isset($tempos) && is_array($tempos)){
                $ut_tg = $tempos['geral'];
                $ut_tf = function($v){ return ($v === null || $v === '') ? '—' : utec_tempo_formatar((int)round((float)$v)); };
                $ut_tn = function($n){ $n = (int)$n; return $n > 0 ? $n.' atendimento'.($n === 1 ? '' : 's') : 'Sem dados no período'; };
              ?>
              <div class="row">
                <div class="col-md-4">
                  <div class="report-card">
                    <div class="report-label">Espera média</div>
                    <div class="report-value"><?=htmlspecialchars($ut_tf($ut_tg['espera_media']), ENT_QUOTES, 'UTF-8')?></div>
                    <div class="report-note">da chegada ao início · <?=htmlspecialchars($ut_tn($ut_tg['espera_n']), ENT_QUOTES, 'UTF-8')?></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="report-card">
                    <div class="report-label">Atraso médio</div>
                    <div class="report-value"><?=htmlspecialchars($ut_tf($ut_tg['atraso_medio']), ENT_QUOTES, 'UTF-8')?></div>
                    <div class="report-note">do horário marcado ao início · <?=htmlspecialchars($ut_tn($ut_tg['atraso_n']), ENT_QUOTES, 'UTF-8')?></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="report-card">
                    <div class="report-label">Duração média</div>
                    <div class="report-value"><?=htmlspecialchars($ut_tf($ut_tg['duracao_media']), ENT_QUOTES, 'UTF-8')?></div>
                    <div class="report-note">da consulta · <?=htmlspecialchars($ut_tn($ut_tg['duracao_n']), ENT_QUOTES, 'UTF-8')?></div>
                  </div>
                </div>
              </div>
              <div class="report-panel">
                <div class="report-panel-header">
                  <h6 class="element-header" style="margin-bottom:0">Tempos por profissional</h6>
                </div>
                <div class="report-panel-body">
                  <div class="table-responsive">
                    <table class="table table-lightborder report-table">
                      <thead><tr><th>Profissional</th><th>Espera média</th><th>Atraso médio</th><th>Duração média</th></tr></thead>
                      <tbody>
                        <?php if(empty($tempos['por_profissional'])){ ?>
                          <tr><td colspan="4">Sem dados no período</td></tr>
                        <?php } foreach($tempos['por_profissional'] as $ut_tp){ ?>
                          <tr>
                            <td><?=htmlspecialchars($ut_tp['prestador_nome'] !== null && $ut_tp['prestador_nome'] !== '' ? $ut_tp['prestador_nome'] : 'Sem profissional', ENT_QUOTES, 'UTF-8')?></td>
                            <td><?=htmlspecialchars($ut_tf($ut_tp['espera_media']).' ('.(int)$ut_tp['espera_n'].')', ENT_QUOTES, 'UTF-8')?></td>
                            <td><?=htmlspecialchars($ut_tf($ut_tp['atraso_medio']).' ('.(int)$ut_tp['atraso_n'].')', ENT_QUOTES, 'UTF-8')?></td>
                            <td><?=htmlspecialchars($ut_tf($ut_tp['duracao_media']).' ('.(int)$ut_tp['duracao_n'].')', ENT_QUOTES, 'UTF-8')?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php } ?>
```
- [ ] **Step 4: Run** → source + helper test `OK`; `php -l` em `Usuarios.php` e na view.
- [ ] **Step 5: Commit** — `git add application/controllers/adm/Usuarios.php application/views/adm/relatorios/clinicos.php tests/tempo_atendimento_source_test.php` · `feat(tempo): espera, atraso e duracao medios em relatorios clinicos`

---

### Task 4: Agenda (resumo, check-in e flash)

**Files:** Modify `application/views/adm/usuarios/new/atendimentos.php`, `tests/tempo_atendimento_source_test.php`

**Interfaces — Consumes:** `utec_tempo_resumo_agenda`, `utec_tempo_pode_checkin`, `utec_tempo_pode_desfazer_checkin` (helper carregado pelo construtor do `Atendimento`, que renderiza esta view; a view também carrega por segurança); rota `adm/atendimento/checkin/{id}`; flashes `tempo_ok`/`tempo_erro`.

Âncoras (arquivo CRLF; confirmar contagem antes de editar):
- primeiro `    </style>` (1×);
- o bloco PHP dos rótulos logo após `</head>` termina com `?>` antes de `  <body` (1×);
- linha do flash de cadastro: `<?php $cadastro_ok = $this->session->flashdata('cadastro_ok'); ...?>` (1×);
- linha de chips de alerta `<? if(!empty($ut_alertas_paciente[(int)$agenda->id_paciente])){ ?><div><?=utec_rotulos_chips_html($ut_alertas_paciente[(int)$agenda->id_paciente], true)?></div><? } ?>` (4× — desktop, cartão ativo, fila mobile, finalizados mobile);
- `<button` imediatamente anterior a `class="btn btn-sm btn-outline-primary btn-remarcar"` (1×, ações da tabela desktop);
- `<span class="ut-status-pill pendente" style="flex-shrink:0;">Pendente</span>` (1×, fila mobile).

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$ag = lerArquivo('application/views/adm/usuarios/new/atendimentos.php');
assertContains('utec_tempo_resumo_agenda($agenda)', $ag, 'resumo na agenda');
assertContains('adm/atendimento/checkin/', $ag, 'form de check-in');
assertContains('value="desfazer"', $ag, 'desfazer chegada');
assertContains("flashdata('tempo_ok')", $ag, 'flash de tempo');
assertContains("field_exists('chegada_em', 'agendamentos')", $ag, 'guarda sem migracao');
if (substr_count($ag, 'utec_tempo_resumo_agenda($agenda)') !== 4) { fwrite(STDERR, "resumo deve aparecer 4x\n"); exit(1); }
```
- [ ] **Step 2: Run** → FAIL `resumo na agenda`.
- [ ] **Step 3: Implement**
(a) CSS antes do primeiro `    </style>`:
```css
      .ut-tempo-resumo { font-size:12px; color:#0f766e; margin-top:2px; }
      .ut-checkin-form { display:inline; margin:0; }
```
(b) Logo após o `?>` que fecha o bloco PHP dos rótulos (antes de `  <body`):
```php
<?php
$ut_ci_tempo =& get_instance();
$ut_ci_tempo->load->helper('tempo_atendimento');
$ut_tempo_ok = $ut_ci_tempo->db->field_exists('chegada_em', 'agendamentos') && $ut_ci_tempo->db->field_exists('inicio_atendimento_em', 'agendamentos');
$ut_hoje = date('Y-m-d');
$ut_voltar_agenda = htmlspecialchars($this->uri->uri_string(), ENT_QUOTES, 'UTF-8');
?>
```
(c) Logo após a linha do flash de cadastro:
```php
              <?php $ut_tempo_flash_ok = $this->session->flashdata('tempo_ok'); $ut_tempo_flash_erro = $this->session->flashdata('tempo_erro'); ?>
              <?php if($ut_tempo_flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars((string)$ut_tempo_flash_ok)?></div><?php } ?>
              <?php if($ut_tempo_flash_erro){ ?><div class="alert alert-danger"><?=htmlspecialchars((string)$ut_tempo_flash_erro)?></div><?php } ?>
```
(d) Após **cada uma das 4** linhas de chips de alerta, uma linha (mesma indentação da linha de chips):
```php
<? if($ut_tempo_ok && ($ut_rs = utec_tempo_resumo_agenda($agenda)) !== ''){ ?><div class="ut-tempo-resumo"><?=htmlspecialchars($ut_rs, ENT_QUOTES, 'UTF-8')?></div><? } ?>
```
(e) Ações desktop — imediatamente antes do `<button` cujo atributo seguinte é `class="btn btn-sm btn-outline-primary btn-remarcar"`:
```php
                                <? if($ut_tempo_ok && utec_tempo_pode_checkin($agenda, $ut_hoje)){ ?>
                                  <form method="post" action="<?=base_url('adm/atendimento/checkin/'.(int)$agenda->id)?>" class="ut-checkin-form">
                                    <input type="hidden" name="voltar" value="<?=$ut_voltar_agenda?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Chegou</button>
                                  </form>
                                <? } elseif($ut_tempo_ok && utec_tempo_pode_desfazer_checkin($agenda)){ ?>
                                  <form method="post" action="<?=base_url('adm/atendimento/checkin/'.(int)$agenda->id)?>" class="ut-checkin-form">
                                    <input type="hidden" name="voltar" value="<?=$ut_voltar_agenda?>">
                                    <input type="hidden" name="acao" value="desfazer">
                                    <button type="submit" class="btn btn-sm btn-link">Desfazer chegada</button>
                                  </form>
                                <? } ?>
```
(f) Fila mobile — imediatamente antes de `<span class="ut-status-pill pendente" style="flex-shrink:0;">Pendente</span>`:
```php
      <? if($ut_tempo_ok && utec_tempo_pode_checkin($agenda, $ut_hoje)){ ?>
        <form method="post" action="<?=base_url('adm/atendimento/checkin/'.(int)$agenda->id)?>" class="ut-checkin-form" onclick="event.stopPropagation();">
          <input type="hidden" name="voltar" value="<?=$ut_voltar_agenda?>">
          <button type="submit" class="btn btn-sm btn-outline-success" style="margin-right:6px;">Chegou</button>
        </form>
      <? } ?>
```
- [ ] **Step 4: Run** → `tests/tempo_atendimento_source_test.php`, `tests/tempo_atendimento_helper_test.php`, `tests/rotulos_source_test.php` → `OK`; `php -l` na view.
- [ ] **Step 5: Commit** — `git add application/views/adm/usuarios/new/atendimentos.php tests/tempo_atendimento_source_test.php` · `feat(tempo): check-in e tempos na agenda`

---

### Task 5: Manual + CLAUDE.md

**Files:** Modify `application/libraries/Manual_conteudo.php`, `CLAUDE.md`, `tests/tempo_atendimento_source_test.php`

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:
```php
$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Chegou', $manual, 'manual cobre check-in');
$claude = lerArquivo('CLAUDE.md');
assertContains('migrar_tempos_atendimento', $claude, 'CLAUDE.md documenta migracao');
```
- [ ] **Step 2: Run** → FAIL `manual cobre check-in`.
- [ ] **Step 3a: Manual** — capítulo `'slug' => 'agenda'`, array `'*'`, acrescentar:
`'Quando o paciente chegar, clique em `Chegou` na agenda do dia. Ao iniciar e finalizar o atendimento, o sistema registra os horários e mostra quanto o paciente esperou e quanto durou a consulta. Marcou por engano? Use `Desfazer chegada` antes de iniciar.'`;
array `2` acrescentar: `'Em `Relatórios clínicos`, acompanha a espera média, o atraso médio em relação ao horário marcado e a duração média das consultas, no total e por profissional.'`.
Atualizar `'atualizado_em'` do capítulo para `'2026-10-05'`. Não mudar `VERSAO`.
- [ ] **Step 3b: CLAUDE.md** (apenas acréscimos, formato vizinho):
  - §4.2 **Saúde e Agenda**: `- \`agendamentos.chegada_em\` / \`chegada_por\` / \`inicio_atendimento_em\` / \`fim_atendimento_em\` — check-in e horários reais do atendimento (gravados por \`set_status_agenda\`/\`checkin\`, zerados na remarcação)`.
  - §6.2, linha de `Atendimento.php`: acrescentar ao fim da coluna Função ` + \`checkin/{id}\` (POST, níveis 1–4, check-in do dia)`.
  - §13: `| \`adm/dev/migrar_tempos_atendimento\` | Adiciona \`chegada_em\`, \`chegada_por\`, \`inicio_atendimento_em\`, \`fim_atendimento_em\` em \`agendamentos\` (idempotente) |`.
  - §15.1: `- [x] Tempo de espera: check-in na agenda, horários de início/fim, espera/atraso/duração por atendimento e médias em Relatórios clínicos (\`tempo_atendimento_helper.php\`)`.
- [ ] **Step 4: Run** → todos `tests/tempo_atendimento_*`, `tests/ficha_paciente_*`, `tests/rotulos_*`, `tests/prontuario_export_*`, `tests/disponibilidade_*` `OK`; `php -l application/libraries/Manual_conteudo.php`.
- [ ] **Step 5: Commit** — `git add application/libraries/Manual_conteudo.php CLAUDE.md tests/tempo_atendimento_source_test.php` · `docs(tempo): manual e CLAUDE.md`

---

## Deploy (depois da revisão e do ok do usuário)
1. Baixar do servidor `Dev.php`, `Atendimento.php`, `Usuarios.php`, `relatorios/clinicos.php`, `atendimentos.php`, `Manual_conteudo.php`; comparar com a branch `feat/tempo-espera` (não com `main` — rótulos e ficha ainda não estão na main).
2. Ordem: `tempo_atendimento_helper.php` → `Dev.php` → `Atendimento.php` → `Usuarios.php` → `relatorios/clinicos.php` → `Manual_conteudo.php` → `atendimentos.php` (por último). Atenção: `Atendimento.php` carrega o helper no construtor — o helper TEM que subir antes, senão toda a agenda quebra.
3. Baixar de novo e `cmp`; healthcheck (home/`admin` 200; `adm/atendimento`, `adm/usuarios/relatorios_clinicos` sem sessão 302).
4. Rodar `adm/dev/migrar_tempos_atendimento`; testar: check-in de um agendamento de hoje, desfazer, iniciar, finalizar → resumo na agenda; relatório com período de hoje mostra as médias.
