# Lista de Espera Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Lista de espera por clínica: a recepção registra pacientes; ao cancelar/remarcar uma consulta futura, o sino avisa a equipe com os compatíveis; "Agendar" abre o agendamento pré-preenchido e tira o paciente da lista.

**Architecture:** Funções puras em `lista_espera_helper.php` (turno, dias, normalização, compatibilidade, ordenação, mensagens). `Lista_espera_model` (dados, sem controle de acesso) + library `Lista_espera_vagas` (aviso de vaga, blindada por try/catch) chamada em 5 pontos após a gravação. Controller `adm/Lista_espera` (tela, salvar, remover, vaga). Encaixe pelo `Atendimento::novo/cadastrar` existentes. Tudo guardado por `table_exists`.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MariaDB 10.11, Bootstrap 4 + jQuery.

Spec: `docs/superpowers/specs/2026-10-07-lista-espera-design.md` · Doc de negócio: `docs/produto/2026-10-07-lista-espera-como-funciona.md` · Branch: `feat/lista-espera` (a partir de `main`).

## Global Constraints
- PHP: testes e `php -l` só com `C:/PHP/PHP7.2/php.exe` (PATH `php` é PHP 8). Sem sintaxe PHP 8 (sem `?->`, `match`, named args, `str_contains`).
- CI3 apenas; nunca `$_POST`/`$_GET`; não tocar `system/`.
- Tabelas: `lista_espera` e `lista_espera_vagas` (DDL na Task 2). Migração `adm/dev/migrar_lista_espera` (nível 1, idempotente).
- Guarda de schema: `Lista_espera_model::disponivel()` = as 2 tabelas existem. Sem elas: tela avisa, 5 pontos não disparam, `novo/cadastrar` ignoram `lista_espera`.
- Turno: `manha` < 12:00, `tarde` 12:00–17:59, `noite` ≥ 18:00; `''` = tanto faz. Dias: CSV 0..6 (0 = domingo), `''` = qualquer.
- Status da entrada: `aguardando` | `agendado` | `removido`. Motivos: `desistiu` | `conseguiu_horario` | `outro`.
- Origens da vaga: `agenda_cancelar` | `agenda_remarcar` | `whatsapp_cancelar` | `chatbot_cancelar` | `chatbot_remarcar`.
- Aviso: `notificacoes_usuarios.tipo = 'lista_espera_vaga'`, `id_whatsapp_notificacao = id da vaga`, título `Abriu vaga — lista de espera`, `url = 'adm/lista_espera/vaga/{id_vaga}'`.
- Destinatários: prestador da vaga + níveis 2 e 4 da árvore da conta (conta = `Rotulos_model::conta_raiz`).
- Falha na lista de espera NUNCA bloqueia cancelamento, remarcação ou agendamento: `try/catch (Throwable)` + `log_message('error', '[lista_espera] ...')`.
- `voltar` aceito só se casa `#^adm/[a-z0-9_/?=&-]*$#i`, senão `adm/lista_espera`.
- Toda saída textual escapada com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Testes: scripts PHP em `tests/`, `exit(1)` na falha, imprimem `OK`.
- Commits: `git add <paths>` explícito (nunca `-u`/`-A`); mensagem termina com linha em branco + `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`. Preservar line endings: arquivos CRLF (`Atendimento.php`, `Webhooks.php`, `menu.php`, `prontuario.php`, `atendimento/atendimento.php`, views novas copiadas de `rotulos/index.php`) continuam CRLF; `Whatsapp_chatbot_agenda.php` é LF.
- Nada sobe para produção neste plano (deploy é do `agente-dev-infra`, depois da revisão). A extensão FTP Sync pode subir arquivos sozinha: ao final de cada task, se algum arquivo novo for referenciado por arquivo existente, conferir que nada foi enviado (ver memória de deploy).

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/lista_espera_helper.php` | Criar | Funções puras |
| `tests/lista_espera_helper_test.php` | Criar | Testes do helper |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_lista_espera()` |
| `application/models/Lista_espera_model.php` | Criar | Dados (entradas, vagas, destinatários) |
| `application/models/Notificacoes_model.php` | Modificar | `criar_aviso_lista_espera()` |
| `application/libraries/Lista_espera_vagas.php` | Criar | `vaga_aberta()` / `vaga_do_agendamento()` |
| `application/controllers/adm/Atendimento.php` | Modificar | Disparo em cancelar/remarcar; encaixe em `novo`/`cadastrar` |
| `application/controllers/Webhooks.php` | Modificar | Disparo no botão Cancelar |
| `application/libraries/Whatsapp_chatbot_agenda.php` | Modificar | Disparo no cancelar/remarcar do chatbot |
| `application/controllers/adm/Lista_espera.php` | Criar | Tela, salvar, remover, vaga |
| `application/views/adm/lista_espera/index.php` | Criar | Lista + formulário + "Como funciona" |
| `application/views/adm/lista_espera/vaga.php` | Criar | Vaga aberta + compatíveis |
| `application/views/adm/atendimento/atendimento.php` | Modificar | Pré-preenchimento + hidden `id_lista_espera` |
| `application/views/adm/usuarios/new/prontuario.php` | Modificar | Selo "Na lista de espera" + botão |
| `includes/adm/menu.php` | Modificar | Item "Lista de espera" na Agenda |
| `tests/lista_espera_source_test.php` | Criar | Testes estáticos (migração, model, library, 5 pontos, encaixe, telas) |
| `application/libraries/Manual_conteudo.php` + `tests/manual_conteudo_test.php` | Modificar | Capítulo "Lista de espera" |
| `application/libraries/Funcionalidades_conteudo.php` + `tests/funcionalidades_catalogo_test.php` | Modificar | Item `lista_espera` |
| `imagens/manual/*.png` | Criar/Regravar | Prints do manual |
| `CLAUDE.md`, `docs/produto/evolucao-30-dias-google-doc.md` | Modificar | Documentação e andamento |

---

### Task 0: Branch

- [ ] **Step 1:** `git switch main && git pull --ff-only && git switch -c feat/lista-espera`

---

### Task 1: Helper puro

**Files:**
- Create: `application/helpers/lista_espera_helper.php`
- Test: `tests/lista_espera_helper_test.php`

**Interfaces:**
- Produces (usados nas tasks 3, 5, 6):
  - `utec_le_turnos(): array` · `utec_le_motivos_saida(): array` · `utec_le_dias_rotulos(): array`
  - `utec_le_turno_da_hora(string $hora): string`
  - `utec_le_dias_normalizar(array|string $dias): string`
  - `utec_le_data_valida(string $data): bool`
  - `utec_le_normalizar(array $post): array{dados: array, erros: string[]}` — chaves de `dados`: `id_paciente` int, `id_prestador` int|null, `turno`, `dias_semana`, `a_partir_de` string|null, `observacao` string|null
  - `utec_le_compativel(array|object $entrada, array|object $vaga): bool` (vaga: `id_prestador`, `data_agenda`, `hora_agenda`)
  - `utec_le_ordenar(array $entradas, array $vaga): array` de `['entrada' => original, 'compativel' => bool]`
  - `utec_le_deve_avisar(array $vaga, string $agora, int $qtd): bool`
  - `utec_le_dias_espera(string $criado_em, string $hoje): int`
  - `utec_le_resumo_preferencias(array|object $entrada): string`
  - `utec_le_mensagem_aviso(string $prestador_nome, string $data, string $hora, int $qtd): string`

- [ ] **Step 1: Write the failing test** — `tests/lista_espera_helper_test.php`:

```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/lista_espera_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- catálogos
assertSameValue(array('' => 'Tanto faz', 'manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'), utec_le_turnos(), 'turnos');
assertSameValue(array('desistiu' => 'Desistiu', 'conseguiu_horario' => 'Conseguiu horário', 'outro' => 'Outro'), utec_le_motivos_saida(), 'motivos');
assertSameValue('Sáb', utec_le_dias_rotulos()[6], 'rotulo sabado');

// --- turno da hora (limites)
assertSameValue('manha', utec_le_turno_da_hora('00:00'), '00:00');
assertSameValue('manha', utec_le_turno_da_hora('11:59'), '11:59');
assertSameValue('tarde', utec_le_turno_da_hora('12:00'), '12:00');
assertSameValue('tarde', utec_le_turno_da_hora('17:59:00'), '17:59:00');
assertSameValue('noite', utec_le_turno_da_hora('18:00'), '18:00');
assertSameValue('', utec_le_turno_da_hora('25:00'), 'hora invalida');
assertSameValue('', utec_le_turno_da_hora(''), 'hora vazia');

// --- dias
assertSameValue('1,3,5', utec_le_dias_normalizar(array('5', '1', '3', '3')), 'dias ordenados sem repeticao');
assertSameValue('0,6', utec_le_dias_normalizar('6,0,7,x'), 'csv descarta invalidos');
assertSameValue('', utec_le_dias_normalizar(array()), 'dias vazio');
assertSameValue('', utec_le_dias_normalizar(null), 'dias null');

// --- data
assertSameValue(true, utec_le_data_valida('2026-10-15'), 'data ok');
assertSameValue(false, utec_le_data_valida('2026-02-30'), 'data inexistente');
assertSameValue(false, utec_le_data_valida('15/10/2026'), 'formato BR');

// --- normalizar
$n = utec_le_normalizar(array('id_paciente' => '45', 'id_prestador' => '0', 'turno' => 'tarde', 'dias_semana' => array('2', '4'),
    'a_partir_de' => '2026-10-15', 'observacao' => "  prefere   \n depois das 15h  "));
assertSameValue(array(), $n['erros'], 'sem erros');
assertSameValue(array('id_paciente' => 45, 'id_prestador' => null, 'turno' => 'tarde', 'dias_semana' => '2,4',
    'a_partir_de' => '2026-10-15', 'observacao' => 'prefere depois das 15h'), $n['dados'], 'dados normalizados');
$n = utec_le_normalizar(array('id_paciente' => '', 'id_prestador' => '7', 'turno' => 'madrugada', 'a_partir_de' => '31/12', 'observacao' => ''));
assertSameValue(array('Selecione o paciente.'), $n['erros'], 'paciente obrigatorio');
assertSameValue(7, $n['dados']['id_prestador'], 'prestador int');
assertSameValue('', $n['dados']['turno'], 'turno invalido vira tanto faz');
assertSameValue(null, $n['dados']['a_partir_de'], 'data invalida vira null');
assertSameValue(null, $n['dados']['observacao'], 'obs vazia vira null');
$n = utec_le_normalizar(array('id_paciente' => '1', 'observacao' => str_repeat('á', 600)));
assertSameValue(500, mb_strlen($n['dados']['observacao'], 'UTF-8'), 'obs cortada em 500');

// --- compatibilidade (2026-10-13 é terça, w = 2)
$vaga = array('id_prestador' => 9, 'data_agenda' => '2026-10-13', 'hora_agenda' => '14:00:00');
$base = array('id_prestador' => null, 'turno' => '', 'dias_semana' => '', 'a_partir_de' => null);
assertSameValue(true, utec_le_compativel($base, $vaga), 'sem preferencias casa');
assertSameValue(true, utec_le_compativel(array_merge($base, array('id_prestador' => 9)), $vaga), 'mesmo prestador');
assertSameValue(false, utec_le_compativel(array_merge($base, array('id_prestador' => 8)), $vaga), 'outro prestador');
assertSameValue(true, utec_le_compativel(array_merge($base, array('turno' => 'tarde')), $vaga), 'turno tarde');
assertSameValue(false, utec_le_compativel(array_merge($base, array('turno' => 'manha')), $vaga), 'turno manha');
assertSameValue(true, utec_le_compativel(array_merge($base, array('dias_semana' => '1,2')), $vaga), 'terca marcada');
assertSameValue(false, utec_le_compativel(array_merge($base, array('dias_semana' => '1,3')), $vaga), 'terca nao marcada');
assertSameValue(true, utec_le_compativel(array_merge($base, array('a_partir_de' => '2026-10-13')), $vaga), 'a partir do proprio dia');
assertSameValue(false, utec_le_compativel(array_merge($base, array('a_partir_de' => '2026-10-14')), $vaga), 'a partir de depois');
assertSameValue(true, utec_le_compativel((object)array_merge($base, array('id_prestador' => '9', 'turno' => 'tarde', 'dias_semana' => '2')), (object)$vaga), 'objetos e strings');
assertSameValue(false, utec_le_compativel($base, array('id_prestador' => 9, 'data_agenda' => 'lixo', 'hora_agenda' => '14:00')), 'vaga invalida');

// --- ordenar: compatíveis primeiro, depois mais antigos
$e1 = array('id' => 1, 'criado_em' => '2026-10-01 10:00:00', 'id_prestador' => null, 'turno' => 'manha', 'dias_semana' => '', 'a_partir_de' => null);
$e2 = array('id' => 2, 'criado_em' => '2026-10-03 10:00:00', 'id_prestador' => null, 'turno' => '', 'dias_semana' => '', 'a_partir_de' => null);
$e3 = array('id' => 3, 'criado_em' => '2026-10-02 10:00:00', 'id_prestador' => 9, 'turno' => 'tarde', 'dias_semana' => '', 'a_partir_de' => null);
$ord = utec_le_ordenar(array($e1, $e2, $e3), $vaga);
assertSameValue(array(3, 2, 1), array_map(function ($i) { return $i['entrada']['id']; }, $ord), 'ordem');
assertSameValue(array(true, true, false), array_map(function ($i) { return $i['compativel']; }, $ord), 'flags');
assertSameValue(array(), utec_le_ordenar(array(), $vaga), 'lista vazia');

// --- deve avisar
assertSameValue(true, utec_le_deve_avisar($vaga, '2026-10-12 09:00:00', 2), 'futura com fila');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-13 14:00:00', 2), 'exatamente agora nao');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-13 15:00:00', 2), 'passada');
assertSameValue(false, utec_le_deve_avisar($vaga, '2026-10-12 09:00:00', 0), 'sem fila');
assertSameValue(false, utec_le_deve_avisar(array('data_agenda' => '2026-10-13', 'hora_agenda' => 'xx'), '2026-10-12 09:00:00', 2), 'hora invalida');

// --- dias de espera
assertSameValue(6, utec_le_dias_espera('2026-10-01 18:30:00', '2026-10-07'), 'dias de espera');
assertSameValue(0, utec_le_dias_espera('2026-10-07 08:00:00', '2026-10-07'), 'hoje');
assertSameValue(0, utec_le_dias_espera('', '2026-10-07'), 'sem data');

// --- resumo
assertSameValue('Sem preferência', utec_le_resumo_preferencias($base), 'resumo vazio');
assertSameValue('Tarde · Seg, Qua · a partir de 15/10', utec_le_resumo_preferencias(array('turno' => 'tarde', 'dias_semana' => '1,3', 'a_partir_de' => '2026-10-15')), 'resumo completo');

// --- mensagem
assertSameValue('Abriu vaga com Dr. Carlos em 13/10 às 14:00. 3 pacientes na lista de espera.', utec_le_mensagem_aviso('Dr. Carlos', '2026-10-13', '14:00:00', 3), 'mensagem plural');
assertSameValue('Abriu vaga em 13/10 às 14:00. 1 paciente na lista de espera.', utec_le_mensagem_aviso('', '2026-10-13', '14:00', 1), 'mensagem singular sem nome');

echo "OK\n";
```

- [ ] **Step 2: Run** → `C:/PHP/PHP7.2/php.exe tests/lista_espera_helper_test.php` falha (arquivo ausente).

- [ ] **Step 3: Implement** — `application/helpers/lista_espera_helper.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera: funções puras (sem banco). Testes em tests/lista_espera_helper_test.php.

if(!function_exists('utec_le_turnos')){
	function utec_le_turnos(){
		return array('' => 'Tanto faz', 'manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite');
	}
}

if(!function_exists('utec_le_motivos_saida')){
	function utec_le_motivos_saida(){
		return array('desistiu' => 'Desistiu', 'conseguiu_horario' => 'Conseguiu horário', 'outro' => 'Outro');
	}
}

if(!function_exists('utec_le_dias_rotulos')){
	function utec_le_dias_rotulos(){
		return array(0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb');
	}
}

if(!function_exists('utec_le_turno_da_hora')){
	function utec_le_turno_da_hora($hora){
		if(!preg_match('/^(\d{2}):(\d{2})/', (string)$hora, $m)){ return ''; }
		$h = (int)$m[1];
		if($h > 23 || (int)$m[2] > 59){ return ''; }
		if($h < 12){ return 'manha'; }
		if($h < 18){ return 'tarde'; }
		return 'noite';
	}
}

if(!function_exists('utec_le_dias_normalizar')){
	function utec_le_dias_normalizar($dias){
		if(!is_array($dias)){
			$dias = ($dias === null || $dias === '') ? array() : explode(',', (string)$dias);
		}
		$ok = array();
		foreach($dias as $d){
			$d = trim((string)$d);
			if($d !== '' && ctype_digit($d) && (int)$d <= 6){ $ok[(int)$d] = (int)$d; }
		}
		ksort($ok);
		return implode(',', $ok);
	}
}

if(!function_exists('utec_le_data_valida')){
	function utec_le_data_valida($data){
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string)$data, $m)){ return false; }
		return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
	}
}

if(!function_exists('utec_le_ler')){
	function utec_le_ler($origem, $chave, $padrao = null){
		$origem = (array)$origem;
		return array_key_exists($chave, $origem) ? $origem[$chave] : $padrao;
	}
}

if(!function_exists('utec_le_normalizar')){
	function utec_le_normalizar($post){
		$post = (array)$post;
		$erros = array();
		$id_paciente = (int)utec_le_ler($post, 'id_paciente', 0);
		if($id_paciente <= 0){ $erros[] = 'Selecione o paciente.'; }
		$id_prestador = (int)utec_le_ler($post, 'id_prestador', 0);
		$turno = (string)utec_le_ler($post, 'turno', '');
		if(!array_key_exists($turno, utec_le_turnos())){ $turno = ''; }
		$a_partir = trim((string)utec_le_ler($post, 'a_partir_de', ''));
		$obs = trim((string)preg_replace('/\s+/u', ' ', (string)utec_le_ler($post, 'observacao', '')));
		$obs = function_exists('mb_substr') ? mb_substr($obs, 0, 500, 'UTF-8') : substr($obs, 0, 500);
		return array(
			'dados' => array(
				'id_paciente' => $id_paciente,
				'id_prestador' => $id_prestador > 0 ? $id_prestador : null,
				'turno' => $turno,
				'dias_semana' => utec_le_dias_normalizar(utec_le_ler($post, 'dias_semana', array())),
				'a_partir_de' => utec_le_data_valida($a_partir) ? $a_partir : null,
				'observacao' => $obs !== '' ? $obs : null,
			),
			'erros' => $erros,
		);
	}
}

if(!function_exists('utec_le_compativel')){
	function utec_le_compativel($entrada, $vaga){
		$data = substr((string)utec_le_ler($vaga, 'data_agenda', ''), 0, 10);
		if(!utec_le_data_valida($data)){ return false; }
		$prest = (int)utec_le_ler($entrada, 'id_prestador', 0);
		if($prest > 0 && $prest !== (int)utec_le_ler($vaga, 'id_prestador', 0)){ return false; }
		$turno = (string)utec_le_ler($entrada, 'turno', '');
		if($turno !== '' && $turno !== utec_le_turno_da_hora(utec_le_ler($vaga, 'hora_agenda', ''))){ return false; }
		$dias = utec_le_dias_normalizar(utec_le_ler($entrada, 'dias_semana', ''));
		if($dias !== '' && !in_array(date('w', strtotime($data)), explode(',', $dias), true)){ return false; }
		$a_partir = substr((string)utec_le_ler($entrada, 'a_partir_de', ''), 0, 10);
		if(utec_le_data_valida($a_partir) && $a_partir > $data){ return false; }
		return true;
	}
}

if(!function_exists('utec_le_ordenar')){
	function utec_le_ordenar($entradas, $vaga){
		$lista = array();
		foreach($entradas as $e){
			$lista[] = array(
				'entrada' => $e,
				'compativel' => utec_le_compativel($e, $vaga),
				'criado_em' => (string)utec_le_ler($e, 'criado_em', ''),
				'id' => (int)utec_le_ler($e, 'id', 0),
			);
		}
		usort($lista, function($x, $y){
			if($x['compativel'] !== $y['compativel']){ return $x['compativel'] ? -1 : 1; }
			$c = strcmp($x['criado_em'], $y['criado_em']);
			return $c !== 0 ? $c : $x['id'] - $y['id'];
		});
		$saida = array();
		foreach($lista as $l){ $saida[] = array('entrada' => $l['entrada'], 'compativel' => $l['compativel']); }
		return $saida;
	}
}

if(!function_exists('utec_le_deve_avisar')){
	function utec_le_deve_avisar($vaga, $agora, $qtd_aguardando){
		$data = substr((string)utec_le_ler($vaga, 'data_agenda', ''), 0, 10);
		$hora = substr((string)utec_le_ler($vaga, 'hora_agenda', ''), 0, 5);
		if((int)$qtd_aguardando <= 0 || !utec_le_data_valida($data) || utec_le_turno_da_hora($hora) === ''){ return false; }
		return strtotime($data.' '.$hora.':00') > strtotime((string)$agora);
	}
}

if(!function_exists('utec_le_dias_espera')){
	function utec_le_dias_espera($criado_em, $hoje){
		$de = substr((string)$criado_em, 0, 10);
		$ate = substr((string)$hoje, 0, 10);
		if(!utec_le_data_valida($de) || !utec_le_data_valida($ate)){ return 0; }
		return max(0, (int)floor((strtotime($ate) - strtotime($de)) / 86400));
	}
}

if(!function_exists('utec_le_resumo_preferencias')){
	function utec_le_resumo_preferencias($entrada){
		$partes = array();
		$turnos = utec_le_turnos();
		$turno = (string)utec_le_ler($entrada, 'turno', '');
		if($turno !== '' && isset($turnos[$turno])){ $partes[] = $turnos[$turno]; }
		$dias = utec_le_dias_normalizar(utec_le_ler($entrada, 'dias_semana', ''));
		if($dias !== ''){
			$rot = utec_le_dias_rotulos();
			$nomes = array();
			foreach(explode(',', $dias) as $d){ $nomes[] = $rot[(int)$d]; }
			$partes[] = implode(', ', $nomes);
		}
		$a_partir = substr((string)utec_le_ler($entrada, 'a_partir_de', ''), 0, 10);
		if(utec_le_data_valida($a_partir)){ $partes[] = 'a partir de '.date('d/m', strtotime($a_partir)); }
		return $partes ? implode(' · ', $partes) : 'Sem preferência';
	}
}

if(!function_exists('utec_le_mensagem_aviso')){
	function utec_le_mensagem_aviso($prestador_nome, $data, $hora, $qtd){
		$quando = date('d/m', strtotime(substr((string)$data, 0, 10))).' às '.substr((string)$hora, 0, 5);
		$nome = trim((string)$prestador_nome);
		$qtd = (int)$qtd;
		$msg = 'Abriu vaga '.($nome !== '' ? 'com '.$nome.' ' : '').'em '.$quando.'. '
			.$qtd.' '.($qtd === 1 ? 'paciente' : 'pacientes').' na lista de espera.';
		return function_exists('mb_substr') ? mb_substr($msg, 0, 480, 'UTF-8') : substr($msg, 0, 480);
	}
}
```

- [ ] **Step 4: Run** → `C:/PHP/PHP7.2/php.exe tests/lista_espera_helper_test.php` imprime `OK`; `C:/PHP/PHP7.2/php.exe -l application/helpers/lista_espera_helper.php` limpo.

- [ ] **Step 5: Commit**

```bash
git add application/helpers/lista_espera_helper.php tests/lista_espera_helper_test.php
git commit -m "feat(lista-espera): helper puro (turno, dias, compatibilidade, ordem, mensagens)

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 2: Migração e model

**Files:**
- Modify: `application/controllers/adm/Dev.php` (novo método logo após `migrar_ficha_pacientes()`)
- Create: `application/models/Lista_espera_model.php`
- Test: `tests/lista_espera_source_test.php` (criar; as tasks seguintes acrescentam blocos antes do `echo "OK\n";`)

**Interfaces:**
- Consumes: helper da Task 1; `Rotulos_model::conta_raiz($id): int`.
- Produces (tasks 3, 5, 6): `Lista_espera_model` com
  `disponivel(): bool` · `conta_raiz(int): int` · `listar(int $id_conta, string $status): object[]` · `buscar(int $id): ?object` ·
  `adicionar(array $dados, int $id_conta, int $id_usuario): array{ok,erro,id}` · `atualizar(int $id, array $dados, int $id_usuario): array{ok,erro}` ·
  `remover(int $id, string $motivo, int $id_usuario): bool` · `marcar_agendado(int $id, int $id_paciente, int $id_agendamento, int $id_usuario): bool` ·
  `aguardando_do_paciente(int $id_paciente): object[]` · `contar_aguardando_para(int $id_conta, int $id_prestador): int` ·
  `aguardando_para(int $id_conta, int $id_prestador): object[]` · `registrar_vaga(array $vaga): int` · `buscar_vaga(int $id): ?object` ·
  `horario_ocupado(int $id_prestador, string $data, string $hora): bool` · `destinatarios_conta(int $id_conta, int $id_prestador): int[]`.
  Linhas de `listar/buscar/aguardando_*` trazem `paciente_nome`, `paciente_telefone`, `prestador_nome` (NULL se qualquer).

- [ ] **Step 1: Write the failing test** — `tests/lista_espera_source_test.php`:

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
assertContains('function migrar_lista_espera()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `lista_espera`', $dev, 'tabela entradas');
assertContains('CREATE TABLE IF NOT EXISTS `lista_espera_vagas`', $dev, 'tabela vagas');
assertContains('UNIQUE KEY `uk_vaga_origem` (`id_agendamento_origem`, `data_agenda`, `hora_agenda`)', $dev, 'vaga unica por origem');

// Model
$model = lerArquivo('application/models/Lista_espera_model.php');
foreach (array('function disponivel(', 'function conta_raiz(', 'function listar(', 'function buscar(', 'function adicionar(',
    'function atualizar(', 'function remover(', 'function marcar_agendado(', 'function aguardando_do_paciente(',
    'function contar_aguardando_para(', 'function aguardando_para(', 'function registrar_vaga(', 'function buscar_vaga(',
    'function horario_ocupado(', 'function destinatarios_conta(') as $fn) {
    assertContains($fn, $model, 'model ' . $fn);
}
assertContains('INSERT IGNORE INTO `lista_espera_vagas`', $model, 'vaga deduplicada');
assertContains('status IN (0,1,2)', $model, 'ocupacao considera so ativos');
assertContains('id_prestador <=> ?', $model, 'duplicidade null-safe');

echo "OK\n";
```

- [ ] **Step 2: Run** → `C:/PHP/PHP7.2/php.exe tests/lista_espera_source_test.php` falha (`migracao existe`).

- [ ] **Step 3: Migração** — em `Dev.php`, logo após o fim de `migrar_ficha_pacientes()` (CRLF, tabs):

```php
	function migrar_lista_espera(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->run_sql("CREATE TABLE IF NOT EXISTS `lista_espera` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_conta` INT NOT NULL,
			`id_paciente` INT NOT NULL,
			`id_prestador` INT NULL DEFAULT NULL,
			`turno` VARCHAR(10) NOT NULL DEFAULT '',
			`dias_semana` VARCHAR(20) NOT NULL DEFAULT '',
			`a_partir_de` DATE NULL DEFAULT NULL,
			`observacao` VARCHAR(500) NULL DEFAULT NULL,
			`status` VARCHAR(12) NOT NULL DEFAULT 'aguardando',
			`motivo_saida` VARCHAR(20) NULL DEFAULT NULL,
			`id_agendamento` INT NULL DEFAULT NULL,
			`criado_por` INT NULL DEFAULT NULL,
			`criado_em` DATETIME NOT NULL,
			`atualizado_por` INT NULL DEFAULT NULL,
			`atualizado_em` DATETIME NULL DEFAULT NULL,
			INDEX `idx_le_conta_status` (`id_conta`, `status`),
			INDEX `idx_le_paciente_status` (`id_paciente`, `status`),
			INDEX `idx_le_prestador_status` (`id_prestador`, `status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `lista_espera` verificada');
		$this->run_sql("CREATE TABLE IF NOT EXISTS `lista_espera_vagas` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_conta` INT NOT NULL,
			`id_prestador` INT NOT NULL,
			`data_agenda` DATE NOT NULL,
			`hora_agenda` VARCHAR(5) NOT NULL,
			`origem` VARCHAR(30) NOT NULL,
			`id_agendamento_origem` INT NOT NULL,
			`criado_em` DATETIME NOT NULL,
			UNIQUE KEY `uk_vaga_origem` (`id_agendamento_origem`, `data_agenda`, `hora_agenda`),
			INDEX `idx_vaga_conta` (`id_conta`, `criado_em`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `lista_espera_vagas` verificada');

		echo '<h3>Migração: lista de espera</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 4: Model** — `application/models/Lista_espera_model.php` (LF, tabs):

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera: entradas por conta (raiz da árvore) e registro das vagas abertas.
// Sem controle de acesso por design — o controller adm/Lista_espera e a library Lista_espera_vagas impõem escopo.
class Lista_espera_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('lista_espera');
	}

	public function disponivel(){
		return $this->db->table_exists('lista_espera') && $this->db->table_exists('lista_espera_vagas');
	}

	public function conta_raiz($id_usuario){
		$this->load->model('Rotulos_model', 'rotulos_model');
		return (int)$this->rotulos_model->conta_raiz((int)$id_usuario);
	}

	private function select_base(){
		return "SELECT le.*, p.nome AS paciente_nome, p.telefone AS paciente_telefone, pr.nome AS prestador_nome
			FROM lista_espera le
			JOIN usuarios p ON p.id = le.id_paciente
			LEFT JOIN usuarios pr ON pr.id = le.id_prestador";
	}

	public function listar($id_conta, $status){
		if(!$this->disponivel() || (int)$id_conta <= 0){ return array(); }
		if(!in_array($status, array('aguardando', 'agendado', 'removido'), true)){ $status = 'aguardando'; }
		$ordem = $status === 'aguardando'
			? ' ORDER BY le.criado_em ASC, le.id ASC'
			: ' ORDER BY COALESCE(le.atualizado_em, le.criado_em) DESC, le.id DESC LIMIT 200';
		return $this->db->query($this->select_base().' WHERE le.id_conta = ? AND le.status = ?'.$ordem, array((int)$id_conta, $status))->result();
	}

	public function buscar($id){
		if(!$this->disponivel() || (int)$id <= 0){ return null; }
		$row = $this->db->query($this->select_base().' WHERE le.id = ? LIMIT 1', array((int)$id))->row();
		return $row ? $row : null;
	}

	private function existe_aguardando($id_paciente, $id_prestador, $ignorar_id = 0){
		$row = $this->db->query(
			"SELECT id FROM lista_espera WHERE id_paciente = ? AND id_prestador <=> ? AND status = 'aguardando' AND id <> ? LIMIT 1",
			array((int)$id_paciente, $id_prestador === null ? null : (int)$id_prestador, (int)$ignorar_id)
		)->row();
		return (bool)$row;
	}

	public function adicionar($dados, $id_conta, $id_usuario){
		if(!$this->disponivel()){ return array('ok' => false, 'erro' => 'Lista de espera indisponível.', 'id' => 0); }
		if($this->existe_aguardando($dados['id_paciente'], $dados['id_prestador'])){
			return array('ok' => false, 'erro' => 'Paciente já está na lista de espera deste profissional.', 'id' => 0);
		}
		$ok = $this->db->insert('lista_espera', array(
			'id_conta' => (int)$id_conta,
			'id_paciente' => (int)$dados['id_paciente'],
			'id_prestador' => $dados['id_prestador'],
			'turno' => (string)$dados['turno'],
			'dias_semana' => (string)$dados['dias_semana'],
			'a_partir_de' => $dados['a_partir_de'],
			'observacao' => $dados['observacao'],
			'status' => 'aguardando',
			'criado_por' => (int)$id_usuario,
			'criado_em' => date('Y-m-d H:i:s'),
		));
		return $ok
			? array('ok' => true, 'erro' => '', 'id' => (int)$this->db->insert_id())
			: array('ok' => false, 'erro' => 'Não foi possível salvar.', 'id' => 0);
	}

	public function atualizar($id, $dados, $id_usuario){
		$atual = $this->buscar($id);
		if(!$atual || $atual->status !== 'aguardando'){ return array('ok' => false, 'erro' => 'Entrada não encontrada.'); }
		if($this->existe_aguardando($atual->id_paciente, $dados['id_prestador'], (int)$id)){
			return array('ok' => false, 'erro' => 'Paciente já está na lista de espera deste profissional.');
		}
		$this->db->where('id', (int)$id);
		$this->db->where('status', 'aguardando');
		$ok = $this->db->update('lista_espera', array(
			'id_prestador' => $dados['id_prestador'],
			'turno' => (string)$dados['turno'],
			'dias_semana' => (string)$dados['dias_semana'],
			'a_partir_de' => $dados['a_partir_de'],
			'observacao' => $dados['observacao'],
			'atualizado_por' => (int)$id_usuario,
			'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $ok ? array('ok' => true, 'erro' => '') : array('ok' => false, 'erro' => 'Não foi possível salvar.');
	}

	public function remover($id, $motivo, $id_usuario){
		if(!$this->disponivel() || !array_key_exists((string)$motivo, utec_le_motivos_saida())){ return false; }
		$this->db->where('id', (int)$id);
		$this->db->where('status', 'aguardando');
		$this->db->update('lista_espera', array(
			'status' => 'removido', 'motivo_saida' => (string)$motivo,
			'atualizado_por' => (int)$id_usuario, 'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $this->db->affected_rows() > 0;
	}

	public function marcar_agendado($id, $id_paciente, $id_agendamento, $id_usuario){
		if(!$this->disponivel() || (int)$id <= 0){ return false; }
		$this->db->where('id', (int)$id);
		$this->db->where('id_paciente', (int)$id_paciente);
		$this->db->where('status', 'aguardando');
		$this->db->update('lista_espera', array(
			'status' => 'agendado', 'id_agendamento' => (int)$id_agendamento,
			'atualizado_por' => (int)$id_usuario, 'atualizado_em' => date('Y-m-d H:i:s'),
		));
		return $this->db->affected_rows() > 0;
	}

	public function aguardando_do_paciente($id_paciente){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query($this->select_base()." WHERE le.id_paciente = ? AND le.status = 'aguardando' ORDER BY le.criado_em ASC", array((int)$id_paciente))->result();
	}

	public function contar_aguardando_para($id_conta, $id_prestador){
		if(!$this->disponivel()){ return 0; }
		$row = $this->db->query(
			"SELECT COUNT(*) AS n FROM lista_espera WHERE id_conta = ? AND status = 'aguardando' AND (id_prestador IS NULL OR id_prestador = ?)",
			array((int)$id_conta, (int)$id_prestador)
		)->row();
		return $row ? (int)$row->n : 0;
	}

	public function aguardando_para($id_conta, $id_prestador){
		if(!$this->disponivel()){ return array(); }
		return $this->db->query(
			$this->select_base()." WHERE le.id_conta = ? AND le.status = 'aguardando' AND (le.id_prestador IS NULL OR le.id_prestador = ?) ORDER BY le.criado_em ASC, le.id ASC",
			array((int)$id_conta, (int)$id_prestador)
		)->result();
	}

	// Retorna o id da vaga nova; 0 quando o mesmo agendamento já liberou este horário (evento repetido).
	public function registrar_vaga($vaga){
		if(!$this->disponivel()){ return 0; }
		$this->db->query(
			"INSERT IGNORE INTO `lista_espera_vagas` (id_conta, id_prestador, data_agenda, hora_agenda, origem, id_agendamento_origem, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?)",
			array((int)$vaga['id_conta'], (int)$vaga['id_prestador'], (string)$vaga['data_agenda'], substr((string)$vaga['hora_agenda'], 0, 5),
				(string)$vaga['origem'], (int)$vaga['id_agendamento_origem'], date('Y-m-d H:i:s'))
		);
		return $this->db->affected_rows() > 0 ? (int)$this->db->insert_id() : 0;
	}

	public function buscar_vaga($id){
		if(!$this->disponivel() || (int)$id <= 0){ return null; }
		$row = $this->db->query(
			"SELECT v.*, pr.nome AS prestador_nome FROM lista_espera_vagas v LEFT JOIN usuarios pr ON pr.id = v.id_prestador WHERE v.id = ? LIMIT 1",
			array((int)$id)
		)->row();
		return $row ? $row : null;
	}

	public function horario_ocupado($id_prestador, $data, $hora){
		$row = $this->db->query(
			"SELECT id FROM agendamentos WHERE id_prestador = ? AND data_agenda = ? AND LEFT(hora_agenda, 5) = ? AND status IN (0,1,2) LIMIT 1",
			array((int)$id_prestador, substr((string)$data, 0, 10), substr((string)$hora, 0, 5))
		)->row();
		return (bool)$row;
	}

	// Prestador da vaga + estabelecimento (2) e colaboradores (4) da conta. Desce só por nós de equipe.
	public function destinatarios_conta($id_conta, $id_prestador){
		$ids = array();
		if((int)$id_prestador > 0){ $ids[(int)$id_prestador] = (int)$id_prestador; }
		$raiz = $this->db->query('SELECT id, nivel FROM usuarios WHERE id = ? LIMIT 1', array((int)$id_conta))->row();
		if(!$raiz){ return array_values($ids); }
		if(in_array((int)$raiz->nivel, array(2, 4), true)){ $ids[(int)$raiz->id] = (int)$raiz->id; }
		$camada = array((int)$raiz->id);
		for($i = 0; $i < 5 && $camada; $i++){
			$rows = $this->db->query('SELECT id, nivel FROM usuarios WHERE id_user IN ('.implode(',', array_map('intval', $camada)).') AND nivel IN (2,3,4)')->result();
			$camada = array();
			foreach($rows as $r){
				$id = (int)$r->id;
				if(isset($ids[$id]) && (int)$r->nivel !== 3){ continue; }
				if(in_array((int)$r->nivel, array(2, 4), true)){ $ids[$id] = $id; }
				$camada[] = $id;
			}
		}
		return array_values($ids);
	}
}
```

- [ ] **Step 5: Run** → source test `OK`; helper test `OK`; `php -l` em `Dev.php` e no model.

- [ ] **Step 6: Commit**

```bash
git add application/controllers/adm/Dev.php application/models/Lista_espera_model.php tests/lista_espera_source_test.php
git commit -m "feat(lista-espera): migracao e model de entradas e vagas

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 3: Aviso de vaga (library + aviso no sino)

**Files:**
- Modify: `application/models/Notificacoes_model.php` (novo método antes de `listar_nao_lidas`)
- Create: `application/libraries/Lista_espera_vagas.php`
- Test: `tests/lista_espera_source_test.php` (acrescentar bloco)

**Interfaces:**
- Consumes: Task 1 (`utec_le_deve_avisar`, `utec_le_mensagem_aviso`), Task 2 (model).
- Produces (Task 4): library `Lista_espera_vagas` com
  `vaga_aberta(int $id_prestador, string $data, string $hora, string $origem, int $id_agendamento_origem): bool` e
  `vaga_do_agendamento(int $id_agendamento, string $origem): bool`. Nunca lança exceção.
  `Notificacoes_model::criar_aviso_lista_espera(int $id_vaga, array $destinatarios, string $titulo, string $mensagem, string $url, int $id_agendamento, int $tenant_id): bool`.

- [ ] **Step 1: Failing test** — acrescentar em `tests/lista_espera_source_test.php`, antes de `echo "OK\n";`:

```php
// Aviso no sino
$notif = lerArquivo('application/models/Notificacoes_model.php');
assertContains('public function criar_aviso_lista_espera(', $notif, 'metodo de aviso');
assertContains("'lista_espera_vaga'", $notif, 'tipo do aviso');

// Library
$lib = lerArquivo('application/libraries/Lista_espera_vagas.php');
assertContains('class Lista_espera_vagas', $lib, 'classe');
assertContains('public function vaga_aberta(', $lib, 'vaga_aberta');
assertContains('public function vaga_do_agendamento(', $lib, 'vaga_do_agendamento');
assertContains('catch (Throwable $e)', $lib, 'blindada');
assertContains('utec_le_deve_avisar(', $lib, 'regra de aviso');
assertContains('->horario_ocupado(', $lib, 'checa vaga livre');
assertContains('->registrar_vaga(', $lib, 'deduplica');
assertContains("'adm/lista_espera/vaga/'", $lib, 'url do aviso');
```

- [ ] **Step 2: Run** → falha (`metodo de aviso`).

- [ ] **Step 3: Notificacoes_model** — inserir antes de `public function listar_nao_lidas(` (estilo do arquivo: 4 espaços):

```php
    // Aviso de vaga aberta para a lista de espera. id_whatsapp_notificacao = id da vaga:
    // a chave única (usuario, id_whatsapp_notificacao, tipo) impede aviso repetido da mesma vaga.
    public function criar_aviso_lista_espera($id_vaga, $destinatarios, $titulo, $mensagem, $url, $id_agendamento, $tenant_id)
    {
        if ((int)$id_vaga <= 0 || !$this->tabela_possui_campos([
            'tenant_id', 'id_usuario_destino', 'id_agendamento', 'id_whatsapp_notificacao', 'tipo', 'titulo', 'mensagem', 'url', 'lida', 'criado_em'
        ])) {
            return false;
        }
        foreach ($destinatarios as $idUsuario) {
            if ((int)$idUsuario <= 0) {
                continue;
            }
            $sql = "INSERT IGNORE INTO `{$this->table}`\n"
                . '(tenant_id, id_usuario_destino, id_agendamento, id_whatsapp_notificacao, tipo, titulo, mensagem, url, lida, criado_em) VALUES ('
                . (int)$tenant_id.', '.(int)$idUsuario.', '.(int)$id_agendamento.', '.(int)$id_vaga.', '
                . $this->db->escape('lista_espera_vaga').', '.$this->db->escape((string)$titulo).', '
                . $this->db->escape((string)$mensagem).', '.$this->db->escape((string)$url).", 0, '".date('Y-m-d H:i:s')."')";
            if ($this->db->query($sql) === false) {
                return false;
            }
        }
        return true;
    }

```

- [ ] **Step 4: Library** — `application/libraries/Lista_espera_vagas.php` (LF, tabs):

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Aviso de vaga aberta para a lista de espera. Chamada após cancelamento/remarcação gravados.
// Nunca lança exceção nem bloqueia quem chamou: qualquer falha vira log [lista_espera].
class Lista_espera_vagas {

	protected $CI;

	public function __construct(){
		$this->CI =& get_instance();
	}

	public function vaga_do_agendamento($id_agendamento, $origem){
		try {
			$ag = $this->CI->db->query(
				'SELECT id, id_prestador, data_agenda, hora_agenda FROM agendamentos WHERE id = ? LIMIT 1',
				array((int)$id_agendamento)
			)->row();
			if(!$ag){ return false; }
			return $this->processar((int)$ag->id_prestador, (string)$ag->data_agenda, (string)$ag->hora_agenda, (string)$origem, (int)$ag->id);
		} catch (Throwable $e) {
			log_message('error', '[lista_espera] vaga_do_agendamento '.(int)$id_agendamento.': '.$e->getMessage());
			return false;
		}
	}

	public function vaga_aberta($id_prestador, $data, $hora, $origem, $id_agendamento_origem){
		try {
			return $this->processar((int)$id_prestador, (string)$data, (string)$hora, (string)$origem, (int)$id_agendamento_origem);
		} catch (Throwable $e) {
			log_message('error', '[lista_espera] vaga_aberta prestador='.(int)$id_prestador.': '.$e->getMessage());
			return false;
		}
	}

	protected function processar($id_prestador, $data, $hora, $origem, $id_agendamento_origem){
		$this->CI->load->model('Lista_espera_model', 'lista_espera_model');
		$m = $this->CI->lista_espera_model;
		if(!$m->disponivel() || $id_prestador <= 0){ return false; }
		$data = substr($data, 0, 10);
		$hora = substr($hora, 0, 5);
		$conta = $m->conta_raiz($id_prestador);
		if($conta <= 0){ return false; }
		$qtd = $m->contar_aguardando_para($conta, $id_prestador);
		if(!utec_le_deve_avisar(array('data_agenda' => $data, 'hora_agenda' => $hora), date('Y-m-d H:i:s'), $qtd)){ return false; }
		if($m->horario_ocupado($id_prestador, $data, $hora)){ return false; }
		$id_vaga = $m->registrar_vaga(array(
			'id_conta' => $conta, 'id_prestador' => $id_prestador, 'data_agenda' => $data, 'hora_agenda' => $hora,
			'origem' => $origem, 'id_agendamento_origem' => $id_agendamento_origem,
		));
		if($id_vaga <= 0){ return false; }
		$prest = $this->CI->db->query('SELECT * FROM usuarios WHERE id = ? LIMIT 1', array($id_prestador))->row();
		$nome = $prest ? (string)$prest->nome : '';
		$tenant = ($prest && isset($prest->tenant_id)) ? (int)$prest->tenant_id : 0;
		$this->CI->load->model('Notificacoes_model', 'notificacoes_model');
		$ok = $this->CI->notificacoes_model->criar_aviso_lista_espera(
			$id_vaga,
			$m->destinatarios_conta($conta, $id_prestador),
			'Abriu vaga — lista de espera',
			utec_le_mensagem_aviso($nome, $data, $hora, $qtd),
			'adm/lista_espera/vaga/'.$id_vaga,
			$id_agendamento_origem,
			$tenant
		);
		log_message('info', '[lista_espera] vaga '.$id_vaga.' origem='.$origem.' prestador='.$id_prestador.' fila='.$qtd.' aviso='.($ok ? 'ok' : 'falha'));
		return (bool)$ok;
	}
}
```

- [ ] **Step 5: Run** → source test `OK`; `php -l` na library e em `Notificacoes_model.php`; `C:/PHP/PHP7.2/php.exe tests/notificacoes_usuarios_test.php` continua `OK`.

- [ ] **Step 6: Commit**

```bash
git add application/models/Notificacoes_model.php application/libraries/Lista_espera_vagas.php tests/lista_espera_source_test.php
git commit -m "feat(lista-espera): aviso de vaga aberta no sino, deduplicado por vaga

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 4: Os 5 pontos de disparo

**Files:**
- Modify: `application/controllers/adm/Atendimento.php` (`cancelar_agenda`, `remarcar_agenda`) — CRLF
- Modify: `application/controllers/Webhooks.php` (`processar_resposta_agendamento`) — CRLF
- Modify: `application/libraries/Whatsapp_chatbot_agenda.php` (`confirmar_remarcacao`, `confirmar_cancelamento`) — LF
- Test: `tests/lista_espera_source_test.php` (acrescentar bloco)

**Interfaces:**
- Consumes: Task 3 (`vaga_aberta`, `vaga_do_agendamento`).

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:

```php
// Pontos de disparo
$atd = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains("vaga_do_agendamento(\$id_agenda, 'agenda_cancelar')", $atd, 'cancelar na agenda');
assertContains("'agenda_remarcar'", $atd, 'remarcar na agenda');
$posAnt = strpos($atd, '$ant_remarcar = $this->db->query(');
$posUpd = strpos($atd, '$atualizado = $this->db->update(\'agendamentos\', $upd_remarcar);');
if ($posAnt === false || $posUpd === false || $posAnt > $posUpd) { fwrite(STDERR, "remarcar le a vaga antiga antes do UPDATE\n"); exit(1); }
$wh = lerArquivo('application/controllers/Webhooks.php');
assertContains("vaga_do_agendamento(\$idAgendamento, 'whatsapp_cancelar')", $wh, 'botao cancelar do WhatsApp');
$posProc = strpos($wh, "if (!\$resultado['processado'])");
$posVaga = strpos($wh, "'whatsapp_cancelar'");
if ($posProc === false || $posVaga === false || $posVaga < $posProc) { fwrite(STDERR, "webhook so dispara apos processado\n"); exit(1); }
$bot = lerArquivo('application/libraries/Whatsapp_chatbot_agenda.php');
assertContains("'chatbot_cancelar'", $bot, 'cancelar pelo chatbot');
assertContains("'chatbot_remarcar'", $bot, 'remarcar pelo chatbot');
```

- [ ] **Step 2: Run** → falha (`cancelar na agenda`).

- [ ] **Step 3: `cancelar_agenda()`** — trocar as duas linhas do UPDATE:

```php
	$this->db->where('id', $id_agenda);
	$this->db->update('agendamentos', ['status' => 3, 'id_user_alt' => $this->session->userdata('id')]);
```
por:
```php
	$this->db->where('id', $id_agenda);
	if($this->db->update('agendamentos', ['status' => 3, 'id_user_alt' => $this->session->userdata('id')])){
		$this->load->library('Lista_espera_vagas');
		$this->lista_espera_vagas->vaga_do_agendamento($id_agenda, 'agenda_cancelar');
	}
```

- [ ] **Step 4: `remarcar_agenda()`** — logo antes de `$this->db->where('id', $id_agenda);` (a linha que precede `$upd_remarcar = [`), inserir:

```php
	$ant_remarcar = $this->db->query('SELECT id_prestador, data_agenda, hora_agenda, status FROM agendamentos WHERE id = ? LIMIT 1', array($id_agenda))->row();
```
e, dentro do `if($atualizado){`, depois das 2 linhas do WhatsApp, inserir:
```php
		// O horário antigo virou vaga: avisa a lista de espera (falha aqui não afeta a remarcação).
		if($ant_remarcar && (int)$ant_remarcar->status !== 3
			&& (substr((string)$ant_remarcar->data_agenda, 0, 10) !== $data_agenda || substr((string)$ant_remarcar->hora_agenda, 0, 5) !== $hora_agenda)){
			$this->load->library('Lista_espera_vagas');
			$this->lista_espera_vagas->vaga_aberta((int)$ant_remarcar->id_prestador, (string)$ant_remarcar->data_agenda, (string)$ant_remarcar->hora_agenda, 'agenda_remarcar', $id_agenda);
		}
```

- [ ] **Step 5: Webhook** — em `processar_resposta_agendamento()`, depois do `log_message(... 'Notificacao a equipe ...' ...);` final (última instrução do método), inserir:

```php

        // Cancelamento pelo botão libera o horário: avisa a lista de espera por último,
        // depois da resposta ao paciente e dos avisos à equipe. Falha aqui só gera log.
        if ($acao === 'cancelar') {
            $this->load->library('Lista_espera_vagas');
            $this->lista_espera_vagas->vaga_do_agendamento($idAgendamento, 'whatsapp_cancelar');
        }
```

- [ ] **Step 6: Chatbot** — em `Whatsapp_chatbot_agenda.php`:
  - `confirmar_remarcacao()`: substituir `$this->avisar_equipe('remarcar', $agendamento, $r, '', $idEvento);` por
```php
        $this->avisar_equipe('remarcar', $agendamento, $r, '', $idEvento);
        $anterior = utec_whatsapp_read($r, 'anterior', null) ?: $agendamento;
        $this->CI->load->library('Lista_espera_vagas');
        $this->CI->lista_espera_vagas->vaga_aberta(
            (int)utec_whatsapp_read($anterior, 'id_prestador', 0),
            (string)utec_whatsapp_read($anterior, 'data_agenda', ''),
            (string)utec_whatsapp_read($anterior, 'hora_agenda', ''),
            'chatbot_remarcar',
            (int)$agendamento->id
        );
```
  - `confirmar_cancelamento()`: substituir `$this->avisar_equipe('cancelar', $agendamento, $r, $motivo, $idEvento);` por
```php
        $this->avisar_equipe('cancelar', $agendamento, $r, $motivo, $idEvento);
        $this->CI->load->library('Lista_espera_vagas');
        $this->CI->lista_espera_vagas->vaga_do_agendamento((int)$agendamento->id, 'chatbot_cancelar');
```
  (Os dois pontos ficam depois do `return` de `ja_estava`, então repetição não dispara.)

- [ ] **Step 7: Run** → `tests/lista_espera_source_test.php` `OK`; continuam `OK`: `tests/atendimento_remarcar_whatsapp_test.php`, `tests/tempo_atendimento_source_test.php`, `tests/whatsapp_webhook_controller_test.php`, `tests/whatsapp_chatbot_agenda_source_test.php`, `tests/whatsapp_chatbot_agenda_library_test.php`, `tests/whatsapp_notificar_equipe_source_test.php`. `php -l` nos 3 arquivos. Se algum teste de biblioteca do chatbot usa CI falso sem `load->library`, ajustar o stub do teste para aceitar `library('Lista_espera_vagas')` e expor um objeto com `vaga_aberta`/`vaga_do_agendamento` que retornam `false` (não mudar o código de produção para agradar o teste).

- [ ] **Step 8: Commit**

```bash
git add application/controllers/adm/Atendimento.php application/controllers/Webhooks.php application/libraries/Whatsapp_chatbot_agenda.php tests/lista_espera_source_test.php
git commit -m "feat(lista-espera): avisa vaga ao cancelar ou remarcar (agenda, WhatsApp e chatbot)

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```
(Incluir no `git add` qualquer stub de teste ajustado no Step 7.)

---

### Task 5: Tela da lista de espera (controller, views e menu)

**Files:**
- Create: `application/controllers/adm/Lista_espera.php` (CRLF, tabs)
- Create: `application/views/adm/lista_espera/index.php`, `application/views/adm/lista_espera/vaga.php` (CRLF)
- Modify: `includes/adm/menu.php` (CRLF)
- Test: `tests/lista_espera_source_test.php` (acrescentar bloco)

**Interfaces:**
- Consumes: Tasks 1–2. Variáveis entregues às views:
  - `index`: `$schema_ok` bool, `$eh_admin` bool, `$conta` int, `$aba` string, `$itens` object[], `$prestadores` object[] (`id`, `nome`), `$paciente_pre` ?object (`id`, `nome`), `$editar` ?object (linha de `buscar`), `$flash_ok`, `$flash_erro`.
  - `vaga`: `$vaga` object (de `buscar_vaga`), `$ocupada` bool, `$passada` bool, `$itens` array de `['entrada' => object, 'compativel' => bool]`.
- Produces (Task 6): URL `adm/lista_espera?paciente={id}` pré-seleciona o paciente; links "Agendar" no formato `adm/atendimento/novo/{id_paciente}?prestador={id}&data=AAAA-MM-DD&hora=HH:MM&lista_espera={id}`.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:

```php
// Controller e telas
$ctl = lerArquivo('application/controllers/adm/Lista_espera.php');
foreach (array('class Lista_espera extends CI_Controller', 'public function index(', 'public function salvar(',
    'public function remover(', 'public function vaga(', 'can_access_usuario(', 'utec_le_normalizar(', 'utec_le_ordenar(',
    '#^adm/[a-z0-9_/?=&-]*$#i') as $t) {
    assertContains($t, $ctl, 'controller ' . $t);
}
$vIndex = lerArquivo('application/views/adm/lista_espera/index.php');
foreach (array('Como funciona', 'adm/lista_espera/salvar', 'adm/lista_espera/remover/', 'adm/atendimento/buscar_paciente',
    'Qualquer profissional', 'migrar_lista_espera', 'utec_le_resumo_preferencias(', 'utec_le_dias_espera(') as $t) {
    assertContains($t, $vIndex, 'view index ' . $t);
}
$vVaga = lerArquivo('application/views/adm/lista_espera/vaga.php');
foreach (array('Vaga já preenchida', 'Compatível', 'adm/atendimento/novo/', 'lista_espera=') as $t) {
    assertContains($t, $vVaga, 'view vaga ' . $t);
}
assertContains("'Lista de espera', 'url' => base_url().'adm/lista_espera'", lerArquivo('includes/adm/menu.php'), 'menu');
```

- [ ] **Step 2: Run** → falha (`Arquivo ausente: application/controllers/adm/Lista_espera.php`).

- [ ] **Step 3: Controller** — `application/controllers/adm/Lista_espera.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera da clínica: níveis 1–4, sempre dentro da própria conta (raiz da árvore).
class Lista_espera extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'lista_espera'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Lista_espera_model', 'lista_espera_model');
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

	// Conta do logado; para o admin (1), a conta de ?conta=<id de usuário da clínica>.
	private function conta_atual()
	{
		if($this->nivel() === 1){
			$ref = (int)$this->input->get('conta');
			if($ref <= 0){ $ref = (int)$this->input->post('conta'); }
			return $ref > 0 ? $this->lista_espera_model->conta_raiz($ref) : 0;
		}
		return $this->lista_espera_model->conta_raiz((int)$this->usuario->id);
	}

	private function pode_conta($conta)
	{
		return $conta > 0 && ($this->nivel() === 1 || $conta === $this->lista_espera_model->conta_raiz((int)$this->usuario->id));
	}

	private function voltar_url()
	{
		$v = (string)$this->input->post('voltar');
		return preg_match('#^adm/[a-z0-9_/?=&-]*$#i', $v) ? $v : 'adm/lista_espera';
	}

	private function prestadores()
	{
		if($this->nivel() === 1){
			return $this->db->query('SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC')->result();
		}
		$ids = $this->padrao_model->get_visible_prestador_ids($this->usuario);
		return $this->db->query('SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN ('.$this->padrao_model->ids_to_sql_in($ids).') ORDER BY nome ASC')->result();
	}

	private function paciente_valido($id_paciente)
	{
		$id_paciente = (int)$id_paciente;
		if($id_paciente <= 1 || !$this->padrao_model->can_access_usuario($id_paciente)){ return null; }
		$p = $this->db->query('SELECT id, nome, nivel FROM usuarios WHERE id = ? LIMIT 1', array($id_paciente))->row();
		return ($p && (int)$p->nivel === 5) ? $p : null;
	}

	public function index()
	{
		$aba = (string)$this->input->get('aba');
		if(!in_array($aba, array('aguardando', 'agendado', 'removido'), true)){ $aba = 'aguardando'; }
		$dados['schema_ok'] = $this->lista_espera_model->disponivel();
		$dados['eh_admin'] = $this->nivel() === 1;
		$dados['aba'] = $aba;
		$dados['paciente_pre'] = null;
		$dados['editar'] = null;
		$pre = (int)$this->input->get('paciente');
		if($pre > 0){ $dados['paciente_pre'] = $this->paciente_valido($pre); }
		$conta = $this->conta_atual();
		if($conta <= 0 && $dados['paciente_pre']){ $conta = $this->lista_espera_model->conta_raiz((int)$dados['paciente_pre']->id); }
		$dados['conta'] = $conta;
		$dados['itens'] = ($dados['schema_ok'] && $this->pode_conta($conta)) ? $this->lista_espera_model->listar($conta, $aba) : array();
		$ed = (int)$this->input->get('editar');
		if($ed > 0 && $dados['schema_ok']){
			$row = $this->lista_espera_model->buscar($ed);
			if($row && $row->status === 'aguardando' && $this->pode_conta((int)$row->id_conta) && $this->paciente_valido($row->id_paciente)){
				$dados['editar'] = $row;
			}
		}
		$dados['prestadores'] = $this->prestadores();
		$dados['flash_ok'] = $this->session->flashdata('le_ok');
		$dados['flash_erro'] = $this->session->flashdata('le_erro');
		$this->load->view('adm/lista_espera/index', $dados);
	}

	public function salvar()
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		if(!$this->lista_espera_model->disponivel()){ show_error('Lista de espera indisponível.', 503); return; }
		$id = (int)$this->input->post('id');
		$atual = $id > 0 ? $this->lista_espera_model->buscar($id) : null;
		$post = $this->input->post(NULL, true);
		if($atual){ $post['id_paciente'] = (int)$atual->id_paciente; }
		$n = utec_le_normalizar($post);
		if($n['erros']){
			$this->session->set_flashdata('le_erro', implode(' ', $n['erros']));
			redirect($this->voltar_url()); return;
		}
		$d = $n['dados'];
		if(!$this->paciente_valido($d['id_paciente'])){ show_error('Acesso negado ao paciente selecionado.', 403); return; }
		if($d['id_prestador'] !== null){
			$pr = $this->db->query('SELECT nivel FROM usuarios WHERE id = ? LIMIT 1', array($d['id_prestador']))->row();
			if(!$pr || (int)$pr->nivel !== 3 || !$this->padrao_model->can_access_usuario($d['id_prestador'])){
				$this->session->set_flashdata('le_erro', 'Profissional inválido.');
				redirect($this->voltar_url()); return;
			}
		}
		$conta = $this->lista_espera_model->conta_raiz($d['id_paciente']);
		if(!$this->pode_conta($conta)){ show_error('Acesso negado.', 403); return; }
		if($id > 0){
			if(!$atual || (int)$atual->id_conta !== $conta){ show_404(); return; }
			$r = $this->lista_espera_model->atualizar($id, $d, (int)$this->usuario->id);
		}else{
			$r = $this->lista_espera_model->adicionar($d, $conta, (int)$this->usuario->id);
		}
		$this->session->set_flashdata($r['ok'] ? 'le_ok' : 'le_erro', $r['ok'] ? 'Lista de espera atualizada.' : $r['erro']);
		redirect($this->voltar_url());
	}

	public function remover($id = 0)
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$row = $this->lista_espera_model->buscar((int)$id);
		if(!$row){ show_404(); return; }
		if(!$this->pode_conta((int)$row->id_conta) || !$this->paciente_valido($row->id_paciente)){ show_error('Acesso negado.', 403); return; }
		$ok = $this->lista_espera_model->remover((int)$id, (string)$this->input->post('motivo'), (int)$this->usuario->id);
		$this->session->set_flashdata($ok ? 'le_ok' : 'le_erro', $ok ? 'Paciente removido da lista de espera.' : 'Escolha o motivo para remover.');
		redirect($this->voltar_url());
	}

	public function vaga($id_vaga = 0)
	{
		$vaga = $this->lista_espera_model->buscar_vaga((int)$id_vaga);
		if(!$vaga){ show_404(); return; }
		if(!$this->pode_conta((int)$vaga->id_conta)){ show_error('Acesso negado.', 403); return; }
		$dados['vaga'] = $vaga;
		$dados['ocupada'] = $this->lista_espera_model->horario_ocupado((int)$vaga->id_prestador, $vaga->data_agenda, $vaga->hora_agenda);
		$dados['passada'] = strtotime($vaga->data_agenda.' '.$vaga->hora_agenda.':00') <= time();
		$dados['itens'] = utec_le_ordenar(
			$this->lista_espera_model->aguardando_para((int)$vaga->id_conta, (int)$vaga->id_prestador),
			array('id_prestador' => (int)$vaga->id_prestador, 'data_agenda' => $vaga->data_agenda, 'hora_agenda' => $vaga->hora_agenda)
		);
		$this->load->view('adm/lista_espera/vaga', $dados);
	}
}
```

- [ ] **Step 4: View `index.php`** — copiar de `application/views/adm/rotulos/index.php` o `<head>` (trocando `<title>` para `Lista de espera` e as classes `rt-*` por `le-*` com o mesmo CSS de `.rt-shell` (max-width 1100px), `.rt-panel`, `.rt-sub`), a estrutura `<body>…content-box` e o rodapé de scripts. Conteúdo do `content-box` (todo texto dinâmico com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`, aqui abreviado como `$e()`; defina no topo da view `$e = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };`):

```php
<div class="le-shell">
  <h4 style="font-weight:800;color:#0f172a;">Lista de espera</h4>
  <p class="le-sub">Pacientes que querem um horário antes do disponível. Quando uma consulta é cancelada ou remarcada, você recebe um aviso no sino com quem combina com a vaga.</p>

  <details class="le-panel" style="padding:14px 22px;">
    <summary style="font-weight:700;cursor:pointer;">Como funciona</summary>
    <ol style="margin:12px 0 0;padding-left:20px;color:#334155;">
      <li><strong>Adicione o paciente</strong> com o profissional (ou "Qualquer profissional") e, se quiser, turno, dias e a partir de quando ele pode vir.</li>
      <li><strong>Abriu uma vaga?</strong> Quando uma consulta futura é cancelada ou remarcada, o sino avisa: "Abriu vaga com ... em ...".</li>
      <li><strong>Clique no aviso</strong>: os pacientes compatíveis com a vaga aparecem primeiro. Ligue ou chame no WhatsApp.</li>
      <li><strong>Clique em Agendar</strong>: o agendamento abre preenchido. Ao salvar, o paciente sai da lista como "Agendado".</li>
    </ol>
  </details>

  <? if($flash_ok){ ?><div class="alert alert-success"><?=$e($flash_ok)?></div><? } ?>
  <? if($flash_erro){ ?><div class="alert alert-danger"><?=$e($flash_erro)?></div><? } ?>
  <? if(!$schema_ok){ ?><div class="alert alert-warning">A lista de espera ainda não foi ativada. Peça ao administrador para executar <code>adm/dev/migrar_lista_espera</code>.</div><? } ?>

  <? if($eh_admin){ ?>
  <div class="le-panel">
    <form method="get" action="<?=base_url('adm/lista_espera')?>" class="form-inline" style="gap:8px;">
      <label for="le-conta" class="mr-2">ID de um usuário da clínica</label>
      <input type="number" min="1" id="le-conta" name="conta" class="form-control form-control-sm mr-2" value="<?=$conta > 0 ? (int)$conta : ''?>">
      <button type="submit" class="btn btn-sm btn-primary">Abrir lista</button>
    </form>
  </div>
  <? } ?>

  <? if($schema_ok){
       $f = $editar ? (array)$editar : array();
       $f_dias = isset($f['dias_semana']) ? explode(',', (string)$f['dias_semana']) : array();
       $pac = $editar ? (object)array('id' => $editar->id_paciente, 'nome' => $editar->paciente_nome) : $paciente_pre;
       $voltar = 'adm/lista_espera'.($eh_admin && $conta > 0 ? '?conta='.(int)$conta : '');
  ?>
  <div class="le-panel" id="le-form">
    <h5 style="font-weight:800;"><?=$editar ? 'Editar entrada' : 'Adicionar à lista'?></h5>
    <form method="post" action="<?=base_url('adm/lista_espera/salvar')?>">
      <input type="hidden" name="voltar" value="<?=$e($voltar)?>">
      <? if($editar){ ?><input type="hidden" name="id" value="<?=(int)$editar->id?>"><? } ?>
      <div class="form-group">
        <label>Paciente *</label>
        <? if($pac){ ?>
          <input type="hidden" name="id_paciente" value="<?=(int)$pac->id?>">
          <div><strong><?=$e($pac->nome)?></strong><? if(!$editar){ ?> · <a href="<?=base_url('adm/lista_espera')?>">trocar</a><? } ?></div>
        <? } else { ?>
          <input type="hidden" name="id_paciente" id="le-id-paciente" value="">
          <input type="text" id="le-busca-paciente" class="form-control" placeholder="Digite ao menos 2 letras do nome" autocomplete="off" aria-label="Buscar paciente">
          <div id="le-resultados" class="list-group" style="display:none;position:relative;z-index:10;"></div>
        <? } ?>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="le-prest">Profissional *</label>
          <select name="id_prestador" id="le-prest" class="form-control">
            <option value="0">Qualquer profissional</option>
            <? foreach($prestadores as $p){ ?>
              <option value="<?=(int)$p->id?>" <?=isset($f['id_prestador']) && (int)$f['id_prestador'] === (int)$p->id ? 'selected' : ''?>><?=$e($p->nome)?></option>
            <? } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="le-turno">Turno</label>
          <select name="turno" id="le-turno" class="form-control">
            <? foreach(utec_le_turnos() as $k => $rot){ ?>
              <option value="<?=$e($k)?>" <?=isset($f['turno']) && $f['turno'] === $k ? 'selected' : ''?>><?=$e($rot)?></option>
            <? } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="le-apartir">A partir de</label>
          <input type="date" name="a_partir_de" id="le-apartir" class="form-control" value="<?=$e(isset($f['a_partir_de']) ? $f['a_partir_de'] : '')?>">
        </div>
      </div>
      <div class="form-group">
        <label>Dias da semana <small class="text-muted">(nenhum = qualquer dia)</small></label>
        <div>
          <? foreach(utec_le_dias_rotulos() as $d => $rot){ ?>
            <label class="mr-3"><input type="checkbox" name="dias_semana[]" value="<?=$d?>" <?=in_array((string)$d, $f_dias, true) ? 'checked' : ''?>> <?=$e($rot)?></label>
          <? } ?>
        </div>
      </div>
      <div class="form-group">
        <label for="le-obs">Observação</label>
        <textarea name="observacao" id="le-obs" class="form-control" rows="2" maxlength="500"><?=$e(isset($f['observacao']) ? $f['observacao'] : '')?></textarea>
      </div>
      <button type="submit" class="btn btn-primary"><?=$editar ? 'Salvar alterações' : 'Adicionar à lista'?></button>
      <? if($editar){ ?><a href="<?=base_url($voltar)?>" class="btn btn-link">Cancelar</a><? } ?>
    </form>
  </div>

  <? $qs_conta = $eh_admin && $conta > 0 ? '&conta='.(int)$conta : ''; ?>
  <ul class="nav nav-tabs" style="margin-bottom:12px;">
    <? foreach(array('aguardando' => 'Aguardando', 'agendado' => 'Agendados', 'removido' => 'Removidos') as $k => $rot){ ?>
      <li class="nav-item"><a class="nav-link <?=$aba === $k ? 'active' : ''?>" href="<?=base_url('adm/lista_espera?aba='.$k.$qs_conta)?>"><?=$rot?></a></li>
    <? } ?>
  </ul>
  <div class="le-panel">
    <? if(!$itens){ ?><p class="text-muted" style="margin:0;">Nenhum paciente nesta aba.</p><? } ?>
    <? $motivos = utec_le_motivos_saida(); foreach($itens as $it){ $tel = preg_replace('/\D+/', '', (string)$it->paciente_telefone); ?>
      <div class="le-linha" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start;padding:12px 0;border-top:1px solid #eef2f7;">
        <div style="flex:1;min-width:220px;">
          <a href="<?=base_url('adm/usuarios/prontuario/'.(int)$it->id_paciente)?>"><strong><?=$e($it->paciente_nome)?></strong></a>
          <? if($tel !== ''){ ?> · <a href="https://api.whatsapp.com/send?phone=55<?=$e($tel)?>" target="_blank" rel="noopener"><?=$e($it->paciente_telefone)?></a><? } ?>
          <div class="le-sub" style="margin:2px 0 0;"><?=$e($it->prestador_nome ? $it->prestador_nome : 'Qualquer profissional')?> · <?=$e(utec_le_resumo_preferencias($it))?></div>
          <? if($it->observacao){ ?><div style="font-size:13px;color:#475569;"><?=$e($it->observacao)?></div><? } ?>
        </div>
        <div style="font-size:13px;color:#64748b;min-width:120px;">
          <? if($aba === 'aguardando'){ $dias = utec_le_dias_espera($it->criado_em, date('Y-m-d')); ?>
            Na lista há <?=$dias?> <?=$dias === 1 ? 'dia' : 'dias'?>
          <? } elseif($aba === 'agendado'){ ?>
            Agendado em <?=$e(date('d/m/Y', strtotime($it->atualizado_em)))?>
          <? } else { ?>
            <?=$e(isset($motivos[$it->motivo_saida]) ? $motivos[$it->motivo_saida] : '')?> · <?=$e(date('d/m/Y', strtotime($it->atualizado_em)))?>
          <? } ?>
        </div>
        <? if($aba === 'aguardando'){ ?>
        <div style="display:flex;gap:6px;align-items:flex-start;">
          <a class="btn btn-sm btn-outline-primary" href="<?=base_url('adm/lista_espera?editar='.(int)$it->id.$qs_conta)?>#le-form">Editar</a>
          <details style="position:relative;">
            <summary class="btn btn-sm btn-outline-danger" style="list-style:none;cursor:pointer;">Remover</summary>
            <form method="post" action="<?=base_url('adm/lista_espera/remover/'.(int)$it->id)?>" style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:220px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
              <input type="hidden" name="voltar" value="<?=$e($voltar)?>">
              <label for="le-mot-<?=(int)$it->id?>" style="font-size:12px;">Motivo</label>
              <select name="motivo" id="le-mot-<?=(int)$it->id?>" class="form-control form-control-sm" required>
                <option value="">Selecione</option>
                <? foreach($motivos as $k => $rot){ ?><option value="<?=$e($k)?>"><?=$e($rot)?></option><? } ?>
              </select>
              <button type="submit" class="btn btn-sm btn-danger" style="margin-top:8px;">Remover da lista</button>
            </form>
          </details>
        </div>
        <? } ?>
      </div>
    <? } ?>
  </div>
  <? } ?>
</div>
```

Antes de `</body>`, depois dos scripts copiados, a busca de paciente (usa o endpoint já escopado `adm/atendimento/buscar_paciente`, que devolve nomes já escapados):

```html
<script>
(function(){
  var $in = $('#le-busca-paciente'), $res = $('#le-resultados'), $id = $('#le-id-paciente'), t = null;
  if(!$in.length){ return; }
  $in.on('input', function(){
    clearTimeout(t); $id.val('');
    var q = $.trim($(this).val());
    if(q.length < 2){ $res.hide(); return; }
    t = setTimeout(function(){
      $.get('<?=base_url()?>adm/atendimento/buscar_paciente', { q: q }).done(function(data){
        var itens = typeof data === 'string' ? JSON.parse(data) : data;
        $res.empty();
        if(!itens.length){ $res.append('<div class="list-group-item text-muted">Nenhum paciente encontrado</div>'); }
        $.each(itens, function(_, p){
          $('<button type="button" class="list-group-item list-group-item-action"></button>')
            .html(p.nome).on('click', function(){ $id.val(p.id); $in.val($('<div>').html(p.nome).text()); $res.hide(); })
            .appendTo($res);
        });
        $res.show();
      });
    }, 280);
  });
  $in.closest('form').on('submit', function(e){ if(!$id.val()){ e.preventDefault(); alert('Selecione o paciente na lista de resultados.'); } });
})();
</script>
```

- [ ] **Step 5: View `vaga.php`** — mesmo `<head>`/layout/rodapé de `index.php` (`<title>Vaga aberta</title>`), conteúdo:

```php
<? $e = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="le-shell">
  <p><a href="<?=base_url('adm/lista_espera')?>">&larr; Lista de espera</a></p>
  <div class="le-panel" style="border-left:6px solid <?=($ocupada || $passada) ? '#94a3b8' : '#16a34a'?>;">
    <h4 style="font-weight:800;margin:0 0 4px;">Vaga aberta</h4>
    <div style="font-size:16px;"><?=$e($vaga->prestador_nome)?> · <?=$e(date('d/m/Y', strtotime($vaga->data_agenda)))?> às <?=$e($vaga->hora_agenda)?></div>
    <? if($ocupada){ ?>
      <div class="alert alert-secondary" style="margin:12px 0 0;">Vaga já preenchida. Outro agendamento já ocupa este horário.</div>
    <? } elseif($passada){ ?>
      <div class="alert alert-secondary" style="margin:12px 0 0;">Este horário já passou.</div>
    <? } ?>
  </div>
  <div class="le-panel">
    <h5 style="font-weight:800;">Pacientes na lista</h5>
    <? if(!$itens){ ?><p class="text-muted" style="margin:0;">Ninguém aguardando para este profissional.</p><? } ?>
    <? foreach($itens as $item){ $it = $item['entrada']; $tel = preg_replace('/\D+/', '', (string)$it->paciente_telefone); ?>
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;padding:12px 0;border-top:1px solid #eef2f7;">
        <div style="flex:1;min-width:220px;">
          <strong><?=$e($it->paciente_nome)?></strong>
          <? if($item['compativel']){ ?><span class="badge badge-success" style="margin-left:6px;">Compatível</span><? } ?>
          <? if($tel !== ''){ ?> · <a href="https://api.whatsapp.com/send?phone=55<?=$e($tel)?>" target="_blank" rel="noopener"><?=$e($it->paciente_telefone)?></a><? } ?>
          <div class="le-sub" style="margin:2px 0 0;"><?=$e($it->prestador_nome ? $it->prestador_nome : 'Qualquer profissional')?> · <?=$e(utec_le_resumo_preferencias($it))?> · na lista há <?=utec_le_dias_espera($it->criado_em, date('Y-m-d'))?> dia(s)</div>
          <? if($it->observacao){ ?><div style="font-size:13px;color:#475569;"><?=$e($it->observacao)?></div><? } ?>
        </div>
        <? if(!$ocupada && !$passada){ ?>
          <a class="btn btn-sm btn-success" href="<?=base_url('adm/atendimento/novo/'.(int)$it->id_paciente.'?prestador='.(int)$vaga->id_prestador.'&data='.rawurlencode($vaga->data_agenda).'&hora='.rawurlencode($vaga->hora_agenda).'&lista_espera='.(int)$it->id)?>">Agendar</a>
        <? } ?>
      </div>
    <? } ?>
  </div>
</div>
```

- [ ] **Step 6: Menu** — em `includes/adm/menu.php`, no item `'Agenda'`, trocar
```php
			['label' => 'Atendimentos', 'url' => base_url().'adm/atendimento'],
```
por
```php
			['label' => 'Atendimentos', 'url' => base_url().'adm/atendimento'],
			['label' => 'Lista de espera', 'url' => base_url().'adm/lista_espera'],
```

- [ ] **Step 7: Run** → source test `OK`; `php -l` no controller e nas 2 views e em `menu.php`. Conferir CRLF: `file application/views/adm/lista_espera/*.php application/controllers/adm/Lista_espera.php` (deve dizer "with CRLF line terminators").

- [ ] **Step 8: Teste manual local** (WAMP, `http://localhost/utec/`): logar como nível 1, rodar `adm/dev/migrar_lista_espera`; logar como nível 2 de teste, abrir `adm/lista_espera`, adicionar um paciente com "Qualquer profissional" e outro com profissional + "Tarde"; editar; remover com motivo; conferir abas.

- [ ] **Step 9: Commit**

```bash
git add application/controllers/adm/Lista_espera.php application/views/adm/lista_espera/index.php application/views/adm/lista_espera/vaga.php includes/adm/menu.php tests/lista_espera_source_test.php
git commit -m "feat(lista-espera): tela da lista, vaga aberta com compativeis e item no menu

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 6: Encaixe pelo agendamento e selo no prontuário

**Files:**
- Modify: `application/controllers/adm/Atendimento.php` (`novo`, `cadastrar`)
- Modify: `application/views/adm/atendimento/atendimento.php` (CRLF)
- Modify: `application/views/adm/usuarios/new/prontuario.php` (CRLF)
- Test: `tests/lista_espera_source_test.php` (acrescentar bloco)

**Interfaces:**
- Consumes: Task 2 (`marcar_agendado`, `aguardando_do_paciente`, `buscar`, `disponivel`), Task 5 (formato do link "Agendar").
- Variáveis novas da view `atendimento.php`: `$pre_prestador` int, `$pre_data` string (AAAA-MM-DD ou ''), `$pre_hora` string (HH:MM ou ''), `$pre_lista_espera` int.

- [ ] **Step 1: Failing test** — acrescentar antes de `echo "OK\n";`:

```php
// Encaixe e prontuário
$atd = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains("\$dados['pre_lista_espera']", $atd, 'novo recebe lista_espera');
assertContains('->marcar_agendado(', $atd, 'cadastrar marca agendado');
$form = lerArquivo('application/views/adm/atendimento/atendimento.php');
assertContains('name="id_lista_espera"', $form, 'hidden no formulario');
assertContains('$pre_data', $form, 'data pre-preenchida');
assertContains('$pre_hora', $form, 'hora pre-preenchida');
$pront = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains('Na lista de espera desde', $pront, 'selo no prontuario');
assertContains("adm/lista_espera?paciente=", $pront, 'atalho para adicionar');
```

- [ ] **Step 2: Run** → falha (`novo recebe lista_espera`).

- [ ] **Step 3: `Atendimento::novo()`** — antes de `$this->load->view('adm/atendimento/atendimento' , $dados);`, inserir:

```php
	// Pré-preenchimento vindo da lista de espera (adm/lista_espera/vaga). Valores inválidos são ignorados.
	$pre_data = (string)$this->input->get('data');
	$pre_hora = substr((string)$this->input->get('hora'), 0, 5);
	$dados['pre_prestador'] = (int)$this->input->get('prestador');
	$dados['pre_data'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $pre_data) ? $pre_data : '';
	$dados['pre_hora'] = preg_match('/^\d{2}:\d{2}$/', $pre_hora) ? $pre_hora : '';
	$dados['pre_lista_espera'] = 0;
	$id_le = (int)$this->input->get('lista_espera');
	if($id_le > 0){
		$this->load->model('Lista_espera_model', 'lista_espera_model');
		$le = $this->lista_espera_model->buscar($id_le);
		if($le && $le->status === 'aguardando' && (int)$le->id_paciente === $id_user){
			$dados['pre_lista_espera'] = $id_le;
		}
	}
	if($dados['pre_prestador'] > 0){
		$dados['prestador_padrao'] = $dados['pre_prestador'];
	}
```

- [ ] **Step 4: `Atendimento::cadastrar()`** — logo depois de `$agendamento_id = (int)$this->db->insert_id();`, inserir:

```php
		// Encaixe pela lista de espera: tira o paciente da lista. Falha aqui não desfaz o agendamento.
		$id_lista_espera = (int)$this->input->post('id_lista_espera');
		if($id_lista_espera > 0){
			try {
				$this->load->model('Lista_espera_model', 'lista_espera_model');
				if($this->lista_espera_model->disponivel()){
					$this->lista_espera_model->marcar_agendado($id_lista_espera, $id_paciente, $agendamento_id, (int)$this->session->userdata('id'));
				}
			} catch (Throwable $e) {
				log_message('error', '[lista_espera] marcar_agendado '.$id_lista_espera.': '.$e->getMessage());
			}
		}
```

- [ ] **Step 5: View `atendimento/atendimento.php`** —
  - depois de `<input type="hidden" value="<?=$dd->id?>" name="id_paciente">` inserir:
```php
                  <?php $pre_data = isset($pre_data) ? $pre_data : ''; $pre_hora = isset($pre_hora) ? $pre_hora : ''; $pre_lista_espera = isset($pre_lista_espera) ? (int)$pre_lista_espera : 0; ?>
                  <?php if($pre_lista_espera > 0){ ?>
                    <input type="hidden" name="id_lista_espera" value="<?=$pre_lista_espera?>">
                    <div class="alert alert-info" style="margin-bottom:12px;">Encaixe pela lista de espera: ao salvar, o paciente sai da lista.</div>
                  <?php } ?>
```
  - trocar `value="<?=date('Y-m-d')?>"` do campo `data_agenda` por `value="<?=$pre_data !== '' ? htmlspecialchars($pre_data, ENT_QUOTES, 'UTF-8') : date('Y-m-d')?>"`
  - no campo `hora_agenda`, acrescentar `value="<?=htmlspecialchars($pre_hora, ENT_QUOTES, 'UTF-8')?>"` (manter `required`).
  - Se a view tiver JS que sobrescreve data/hora ao carregar (sugestão de horários livres), conferir no navegador que os valores pré-preenchidos permanecem; se forem sobrescritos, condicionar o JS a `campo vazio`.

- [ ] **Step 6: Prontuário** — logo após o bloco PHP da ficha (a linha `<?php if($ut_ficha_flash_ok){ ?>...`), inserir:
```php
              <?php
              $ut_le_lista = array();
              if((int)$paciente->nivel === 5 && in_array((int)$this->session->userdata('nivel'), array(1, 2, 3, 4), true)){
                $ut_ci_le =& get_instance();
                $ut_ci_le->load->model('Lista_espera_model', 'lista_espera_model');
                if($ut_ci_le->lista_espera_model->disponivel()){
                  $ut_le_lista = $ut_ci_le->lista_espera_model->aguardando_do_paciente((int)$paciente->id);
                }
                $ut_le_ok = $ut_ci_le->lista_espera_model->disponivel();
              }
              ?>
```
  e, dentro do card de resumo, logo depois do bloco de alergias (`<div class="ut-ficha-alergia" ...>` e seu `<?php } ?>`), inserir:
```php
                        <?php foreach($ut_le_lista as $ut_le){ ?>
                          <div style="margin-top:6px;font-size:13px;color:#0f766e;font-weight:700;">&#9203; Na lista de espera desde <?=htmlspecialchars(date('d/m', strtotime($ut_le->criado_em)), ENT_QUOTES, 'UTF-8')?> · <?=htmlspecialchars($ut_le->prestador_nome ? $ut_le->prestador_nome : 'Qualquer profissional', ENT_QUOTES, 'UTF-8')?> · <a href="<?=base_url('adm/lista_espera')?>">ver lista</a></div>
                        <?php } ?>
```
  e, em `timeline-actions`, depois do botão "Novo agendamento", inserir:
```php
                        <?php if(!empty($ut_le_ok)){ ?><a href="<?=base_url('adm/lista_espera?paciente='.(int)$paciente->id)?>" class="btn btn-outline-secondary">Lista de espera</a><?php } ?>
```

- [ ] **Step 7: Run** → source test `OK`; continuam `OK`: `tests/rotulos_source_test.php`, `tests/ficha_paciente_source_test.php`, `tests/tempo_atendimento_source_test.php`, `tests/prontuario_export_source_test.php`, `tests/atendimento_remarcar_whatsapp_test.php`. `php -l` nos 3 arquivos.

- [ ] **Step 8: Teste manual local ponta a ponta:** com 2 pacientes na lista (Task 5), cancelar na agenda uma consulta futura do profissional → sino mostra "Abriu vaga com ..." para o nível 2, o colaborador e o profissional; clicar → compatível primeiro; "Agendar" abre o formulário preenchido; salvar → paciente vai para "Agendados" e a vaga mostra "Vaga já preenchida". Remarcar outra consulta → aviso do horário antigo. Cancelar a mesma consulta de novo (reabrir e cancelar) → sem aviso duplicado para a mesma vaga. Sem pacientes na lista → nenhum aviso.

- [ ] **Step 9: Commit**

```bash
git add application/controllers/adm/Atendimento.php application/views/adm/atendimento/atendimento.php application/views/adm/usuarios/new/prontuario.php tests/lista_espera_source_test.php
git commit -m "feat(lista-espera): encaixe pelo agendamento preenchido e selo no prontuario

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

### Task 7: Manual, catálogo do site, prints e documentação

**Files:**
- Modify: `application/libraries/Manual_conteudo.php`, `tests/manual_conteudo_test.php`
- Modify: `application/libraries/Funcionalidades_conteudo.php`, `tests/funcionalidades_catalogo_test.php`
- Create/Regravar: `imagens/manual/lista-espera.png`, `agenda.png`, `prontuario.png`, `pacientes-cadastro.png`
- Modify: `CLAUDE.md`, `docs/produto/evolucao-30-dias-google-doc.md`

- [ ] **Step 1: Failing tests** —
  - `tests/manual_conteudo_test.php`: trocar `13`/`13`/`12` por `14`/`14`/`13` nas 3 asserções de contagem e acrescentar, antes do fim:
```php
$le = null;
foreach ($nivel4 as $capitulo) { if ($capitulo['slug'] === 'lista-espera') { $le = $capitulo; } }
assertTrue($le !== null, 'Colaborador (nivel 4) deve ver o capitulo lista-espera.');
assertSameValue('lista-espera.png', $le['print'], 'Print do capitulo lista-espera.');
```
  - `tests/funcionalidades_catalogo_test.php`: `16` → `17` (`'17 itens'`); novidades → `array('exportar_prontuario', 'ficha_paciente', 'lista_espera', 'rotulos', 'tempo_espera')` (`'5 novidades'`); ordem dos ids com `'lista_espera'` logo depois de `'tempo_espera'`.

- [ ] **Step 2: Run** → os 2 testes falham.

- [ ] **Step 3: Capítulo do manual** — em `Manual_conteudo::capitulos()`, logo depois do capítulo `horarios-atendimento`:
```php
            array(
                'slug' => 'lista-espera',
                'titulo' => 'Lista de espera',
                'icone' => 'os-icon-clock',
                'niveis' => array(2, 3, 4),
                'resumo' => 'Guarde quem quer um horário antes do disponível. Quando uma consulta é cancelada ou remarcada, o sistema avisa no sino quem combina com a vaga, e você encaixa o paciente em poucos cliques.',
                'topicos' => array(
                    '*' => array(
                        'Em `Agenda > Lista de espera`, clique em `Adicionar à lista`: escolha o paciente e o profissional (ou `Qualquer profissional`). Turno, dias da semana, "a partir de" e observação são opcionais — sem eles, o paciente serve para qualquer vaga.',
                        'No prontuário, o botão `Lista de espera` já abre o formulário com o paciente escolhido, e o selo "Na lista de espera desde ..." mostra quem está aguardando.',
                        'Quando uma consulta futura é cancelada ou remarcada — na agenda, pelo botão do WhatsApp ou pelo chatbot — o sino avisa: "Abriu vaga com ... em ...". Clique no aviso para ver a vaga.',
                        'Na vaga, os pacientes `Compatíveis` aparecem primeiro, e entre eles quem espera há mais tempo. Ligue ou chame no WhatsApp para confirmar.',
                        'Clique em `Agendar`: o agendamento abre preenchido com paciente, profissional, data e hora. Ao salvar, o paciente sai da lista e vai para a aba `Agendados`.',
                        'Se outra pessoa já ocupou o horário, a vaga mostra "Vaga já preenchida". Para tirar alguém da lista, use `Remover` e informe o motivo.',
                    ),
                    2 => array('Recebe os avisos de vaga de todos os profissionais da clínica.'),
                    3 => array('Recebe os avisos das vagas da sua própria agenda.'),
                    4 => array('Recebe os avisos de vaga da clínica e costuma ser quem faz o encaixe.'),
                ),
                'print' => 'lista-espera.png',
                'atualizado_em' => '2026-10-07',
            ),
```

- [ ] **Step 4: Catálogo** — em `Funcionalidades_conteudo::itens()`, logo depois do item `tempo_espera`:
```php
            array(
                'id' => 'lista_espera', 'grupo' => 'agenda', 'icone' => '📝', 'titulo' => 'Lista de espera',
                'resumo' => 'Quem quer um horário antes é avisado à equipe assim que abre uma vaga.',
                'descricao' => 'Registre quem quer ser atendido antes. Quando uma consulta é cancelada ou remarcada, a equipe recebe um aviso com os pacientes que combinam com o horário e encaixa com um clique.',
                'link' => '', 'novo' => true,
            ),
```

- [ ] **Step 5: Run** → `tests/manual_conteudo_test.php`, `tests/funcionalidades_catalogo_test.php`, `tests/funcionalidades_partial_test.php`, `tests/funcionalidades_source_test.php` `OK`; `php -l` nas 2 libraries.

- [ ] **Step 6: Prints (ambiente local, dados de teste, nunca produção)** — com o WAMP local e um nível 2 de teste com 2–3 pacientes fictícios: capturar em 1280×800 (Playwright `browser_resize` + `browser_take_screenshot`, recortado na área de conteúdo):
  - `imagens/manual/lista-espera.png` — `adm/lista_espera` com 2 pacientes aguardando e o "Como funciona" aberto;
  - `imagens/manual/agenda.png` — agenda do dia com o botão "Chegou" e uma linha "Chegou ... · esperou ...";
  - `imagens/manual/prontuario.png` — prontuário com rótulos, alergia em destaque, selo da lista de espera e botão Exportar;
  - `imagens/manual/pacientes-cadastro.png` — lista de pacientes com chips de rótulo e filtro.
  Conferir que nenhum dado real aparece. Abrir `adm/usuarios/manual/2` e `adm/usuarios/manual_pdf/2` localmente e verificar as imagens.

- [ ] **Step 7: Documentação** —
  - `CLAUDE.md`: §4.2 (tabelas `lista_espera`, `lista_espera_vagas`); §6.2 (linha `Lista_espera.php` | `/adm/lista_espera` | tela, salvar, remover, `vaga/{id}`; níveis 1–4); §7 (subseção curta `Lista_espera_model` + library `Lista_espera_vagas` chamada em 5 pontos, tipo de aviso `lista_espera_vaga`); §13 (`adm/dev/migrar_lista_espera`); §15.1 (`- [x] Lista de espera ...`); §19 (screenshots: acrescentar `lista-espera`, 8 imagens).
  - `docs/produto/evolucao-30-dias-google-doc.md`: item "Lista de espera / Encaixe" → 🟢 Pronto, aguardando publicação (data, resumo, linha no histórico).

- [ ] **Step 8: Run final** → todos `tests/lista_espera_*`, `tests/manual_conteudo_test.php`, `tests/funcionalidades_*`, `tests/rotulos_*`, `tests/ficha_paciente_*`, `tests/tempo_atendimento_*`, `tests/prontuario_export_*`, `tests/whatsapp_*_test.php`, `tests/notificacoes_usuarios_test.php`, `tests/atendimento_remarcar_whatsapp_test.php` imprimem `OK`.

- [ ] **Step 9: Commit**

```bash
git add application/libraries/Manual_conteudo.php tests/manual_conteudo_test.php application/libraries/Funcionalidades_conteudo.php tests/funcionalidades_catalogo_test.php imagens/manual/lista-espera.png imagens/manual/agenda.png imagens/manual/prontuario.png imagens/manual/pacientes-cadastro.png CLAUDE.md docs/produto/evolucao-30-dias-google-doc.md
git commit -m "docs(lista-espera): capitulo do manual, catalogo do site, prints atualizados e CLAUDE.md

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>"
```

---

## Deploy (depois da revisão final e do ok do Igor — `agente-dev-infra`)
1. Merge `feat/lista-espera` → `main`.
2. Baixar do servidor e comparar com o HEAD da branch: `Atendimento.php`, `Webhooks.php`, `Whatsapp_chatbot_agenda.php`, `Notificacoes_model.php`, `Dev.php`, `menu.php`, `prontuario.php`, `atendimento/atendimento.php`, `Manual_conteudo.php`, `Funcionalidades_conteudo.php`.
3. Subir na ordem: helper → model → `Notificacoes_model.php` → library `Lista_espera_vagas.php` → `Dev.php` → controller + views `lista_espera/` → `Atendimento.php` → `Webhooks.php` → `Whatsapp_chatbot_agenda.php` → `atendimento/atendimento.php` → `prontuario.php` → `menu.php` → `Manual_conteudo.php` + `Funcionalidades_conteudo.php` → 4 PNGs. (Os pontos de disparo só sobem depois da library; sem as tabelas eles não fazem nada.)
4. Rodar `adm/dev/migrar_lista_espera` (nível 1).
5. Healthcheck: home/`admin` 200; `adm/lista_espera` sem sessão 302; `webhooks/whatsapp` GET sem token 403; PNGs 200. Baixar de novo e `cmp`.
6. Atualizar andamento para ✅ publicado e CLAUDE.md com o status de deploy.
