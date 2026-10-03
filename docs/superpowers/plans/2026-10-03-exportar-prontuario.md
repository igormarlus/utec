# Exportar Prontuário (PDF, CSV, XLSX) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permitir que níveis 1–3 exportem o prontuário de um paciente (com período opcional) em PDF, CSV e XLSX, com registro de auditoria.

**Architecture:** Funções puras em `prontuario_export_helper.php` (rótulos, CSV, linhas tabulares, período, nome de arquivo); gerador XLSX próprio em `libraries/Xlsx_simples.php`; coleta de dados em `Prontuario_export_model`; endpoint `Atendimento::exportar_prontuario()` que valida acesso, registra auditoria e despacha para o formato. Botão na tela do prontuário.

**Tech Stack:** PHP 7.2 (produção PHP 7), CodeIgniter 3.1.10, MySQL/MariaDB, mPDF v6 (`application/libraries/M_pdf.php`), ZipArchive.

Spec: `docs/superpowers/specs/2026-10-03-exportar-prontuario-design.md`

## Global Constraints
- PHP: rodar todos os testes e `php -l` com `C:\PHP\PHP7.2\php.exe` (mPDF v6 não roda em PHP 8). Nada de sintaxe PHP 8 (sem `match`, `?->`, named args, `str_contains`, arrow fn com tipos union).
- CI3 apenas: `$this->db`, `$this->input->get()`, `$this->load->*`. Não usar `$_GET`/`$_POST`. Não tocar `system/`.
- Quem exporta: nível ∈ {1,2,3} **e** `padrao_model->can_access_usuario($id_paciente)`. Nível 4 → 403.
- Formatos: `pdf`, `csv`, `xlsx`. CSV = separador `;`, UTF-8 com BOM, `\r\n`. XLSX só se `class_exists('ZipArchive')`.
- Nome do arquivo: `prontuario-{id}-{AAAAMMDD}.{ext}` — nunca o nome do paciente.
- Auditoria em `prontuario_exportacoes`, guardada por `table_exists` (sem tabela, exporta mesmo assim). `ip_hash = hash('sha256', $ip.$app_key)` com `$app_key = getenv('APP_SECRET') ?: (getenv('MERCADOPAGO_WEBHOOK_SECRET') ?: 'utec-ai-salt')` (mesmo salt de `Padrao_model::track_ai_referral`).
- Testes no padrão do projeto: script PHP simples em `tests/`, `exit(1)` na falha, sem PHPUnit. Rodar com `C:\PHP\PHP7.2\php.exe tests\<arquivo>.php` (sucesso = exit 0, sem saída em STDERR).
- Commits terminam com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.

## File Structure
| Arquivo | Ação | Responsabilidade |
|---|---|---|
| `application/helpers/prontuario_export_helper.php` | Criar | Funções puras: rótulos, status, período, extras, linhas tabulares, CSV, nome de arquivo |
| `tests/prontuario_export_helper_test.php` | Criar | Testes das funções puras |
| `application/views/adm/usuarios/new/prontuario.php` | Modificar | Usar `utec_pront_rotulos()`; botão Exportar |
| `application/libraries/Xlsx_simples.php` | Criar | Gerador XLSX mínimo (inlineStr, cabeçalho negrito) |
| `tests/prontuario_export_xlsx_test.php` | Criar | Valida zip/partes/XML |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_prontuario_exportacoes()` |
| `application/models/Prontuario_export_model.php` | Criar | `coletar()` + `registrar_exportacao()` |
| `application/controllers/adm/Atendimento.php` | Modificar | `exportar_prontuario()` |
| `application/views/adm/usuarios/prontuario_pdf.php` | Criar | HTML do PDF |
| `tests/prontuario_export_source_test.php` | Criar | Teste estático de fiação (rota, guardas, migração, view) |
| `application/libraries/Manual_conteudo.php` | Modificar | Tópico "Exportar prontuário" |
| `CLAUDE.md` | Modificar | Documentar feature, migração, tabela |

---

### Task 1: Funções puras do helper

**Files:**
- Create: `application/helpers/prontuario_export_helper.php`
- Test: `tests/prontuario_export_helper_test.php`

**Interfaces:**
- Produces:
  - `utec_pront_rotulos($esp_id): array` — chaves `atendimento_inicial, avaliacao, reavaliacao, ph_inicial, ph_avaliacao, ph_reav`
  - `utec_pront_status_texto($status): string`
  - `utec_pront_status_exame_texto($status): string`
  - `utec_pront_data_br($ymd): string` (`'2026-10-03'` → `'03/10/2026'`, inválido → `''`)
  - `utec_pront_normalizar_periodo($de, $ate): array` → `array($de_ou_vazio, $ate_ou_vazio)`
  - `utec_pront_montar_extras($raw_json, array $labels_por_chave): array` → lista de `array($rotulo, $valor)`
  - `utec_pront_extras_texto(array $extras): string`
  - `utec_pront_linhas_tabulares(array $dados): array` → `array('Atendimentos' => linhas, 'Exames' => linhas)`
  - `utec_pront_celula_segura($v): string`
  - `utec_pront_csv(array $abas): string`
  - `utec_pront_nome_arquivo($id, $ext, $ymd): string`
- Formato de `$dados` (produzido pelo model na Task 4):
  - `$dados['atendimentos'][]` = `array('data'=>'Y-m-d','hora'=>'HH:MM','status_texto'=>..,'profissional'=>..,'especialidade'=>..,'atendimento_inicial'=>..,'avaliacao'=>..,'reavaliacao'=>..,'extras'=>array(array(rotulo,valor)),'rotulos'=>array(...))`
  - `$dados['exames'][]` = `array('data'=>'Y-m-d','exame'=>..,'status_texto'=>..,'profissional'=>..,'obs'=>..)`

- [ ] **Step 1: Write the failing test**

`tests/prontuario_export_helper_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/prontuario_export_helper.php';

function assertSameValue($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, $label . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

// --- rótulos (iguais ao switch original da view)
$padrao = utec_pront_rotulos(0);
assertSameValue('Atendimento Inicial', $padrao['atendimento_inicial'], 'rotulo padrao inicial');
assertSameValue('Reavaliação', $padrao['reavaliacao'], 'rotulo padrao reav');
assertSameValue('Registre avaliação clínica, hipóteses e condutas adotadas.', $padrao['ph_avaliacao'], 'placeholder padrao');
$fisio = utec_pront_rotulos(10);
assertSameValue('Queixa / Avaliação Postural', $fisio['atendimento_inicial'], 'fisio inicial');
$psico = utec_pront_rotulos('36');
assertSameValue('Evolução da Sessão', $psico['avaliacao'], 'psico aceita string');
assertSameValue('Atendimento Inicial', utec_pront_rotulos(999)['atendimento_inicial'], 'esp desconhecida = padrao');

// --- status
assertSameValue('Pendente', utec_pront_status_texto(0), 'status 0');
assertSameValue('Em atendimento', utec_pront_status_texto('1'), 'status 1');
assertSameValue('Finalizado', utec_pront_status_texto(2), 'status 2');
assertSameValue('Cancelado', utec_pront_status_texto(3), 'status 3');
assertSameValue('Pendente', utec_pront_status_exame_texto(0), 'exame 0');
assertSameValue('Solicitado', utec_pront_status_exame_texto('1'), 'exame 1');
assertSameValue('Entregue', utec_pront_status_exame_texto(2), 'exame 2');

// --- datas e período
assertSameValue('03/10/2026', utec_pront_data_br('2026-10-03'), 'data br');
assertSameValue('03/10/2026', utec_pront_data_br('2026-10-03 10:00:00'), 'data br com hora');
assertSameValue('', utec_pront_data_br('lixo'), 'data br invalida');
assertSameValue(array('2026-01-01', '2026-02-01'), utec_pront_normalizar_periodo('2026-01-01', '2026-02-01'), 'periodo ok');
assertSameValue(array('2026-01-01', '2026-02-01'), utec_pront_normalizar_periodo('2026-02-01', '2026-01-01'), 'periodo invertido');
assertSameValue(array('', '2026-02-01'), utec_pront_normalizar_periodo('01/01/2026', '2026-02-01'), 'de invalido vira vazio');
assertSameValue(array('', ''), utec_pront_normalizar_periodo(null, ''), 'periodo vazio');
assertSameValue(array('', ''), utec_pront_normalizar_periodo('2026-02-31', ''), 'data inexistente');

// --- extras
$labels = array('eva' => 'Escala EVA', 'regiao' => 'Região');
assertSameValue(array(array('Escala EVA', '7'), array('Região', 'Lombar'), array('outro', 'x')),
    utec_pront_montar_extras('{"eva":7,"regiao":"Lombar","vazio":"","outro":"x"}', $labels), 'extras com e sem config');
assertSameValue(array(array('Região', 'Lombar, Joelho')),
    utec_pront_montar_extras('{"regiao":["Lombar","Joelho"]}', $labels), 'extras array');
assertSameValue(array(), utec_pront_montar_extras('', $labels), 'extras vazio');
assertSameValue(array(), utec_pront_montar_extras('{quebrado', $labels), 'extras json invalido');
assertSameValue('Escala EVA: 7 | Região: Lombar',
    utec_pront_extras_texto(array(array('Escala EVA', '7'), array('Região', 'Lombar'))), 'extras texto');

// --- linhas tabulares
$dados = array(
    'atendimentos' => array(array(
        'data' => '2026-10-01', 'hora' => '09:30', 'status_texto' => 'Finalizado',
        'profissional' => 'Dra. Ana', 'especialidade' => 'Fisioterapia',
        'atendimento_inicial' => 'Dor lombar', 'avaliacao' => 'RPG', 'reavaliacao' => 'Retorno 7d',
        'extras' => array(array('Escala EVA', '7')), 'rotulos' => utec_pront_rotulos(10),
    )),
    'exames' => array(array(
        'data' => '2026-10-01', 'exame' => 'Raio-X', 'status_texto' => 'Solicitado',
        'profissional' => 'Dra. Ana', 'obs' => 'Coluna',
    )),
);
$abas = utec_pront_linhas_tabulares($dados);
assertSameValue(array('Atendimentos', 'Exames'), array_keys($abas), 'abas');
assertSameValue(array('Data', 'Hora', 'Status', 'Profissional', 'Especialidade', 'Atendimento inicial', 'Avaliação', 'Reavaliação', 'Campos extras'),
    $abas['Atendimentos'][0], 'cabecalho atendimentos');
assertSameValue(array('01/10/2026', '09:30', 'Finalizado', 'Dra. Ana', 'Fisioterapia', 'Dor lombar', 'RPG', 'Retorno 7d', 'Escala EVA: 7'),
    $abas['Atendimentos'][1], 'linha atendimento');
assertSameValue(array('Data', 'Exame', 'Status', 'Profissional', 'Observação'), $abas['Exames'][0], 'cabecalho exames');
assertSameValue(array('01/10/2026', 'Raio-X', 'Solicitado', 'Dra. Ana', 'Coluna'), $abas['Exames'][1], 'linha exame');
$vazio = utec_pront_linhas_tabulares(array('atendimentos' => array(), 'exames' => array()));
assertSameValue(1, count($vazio['Atendimentos']), 'so cabecalho quando vazio');

// --- célula segura
assertSameValue("'=SOMA(A1)", utec_pront_celula_segura('=SOMA(A1)'), 'formula =');
assertSameValue("'+55 11", utec_pront_celula_segura('+55 11'), 'formula +');
assertSameValue("'-1", utec_pront_celula_segura('-1'), 'formula -');
assertSameValue("'@x", utec_pront_celula_segura('@x'), 'formula @');
assertSameValue('texto', utec_pront_celula_segura('texto'), 'texto normal');
assertSameValue('', utec_pront_celula_segura(null), 'null');

// --- CSV
$csv = utec_pront_csv(array(
    'A' => array(array('Nome', 'Obs'), array('Ana; Silva', "linha1\nlinha2"), array('Diz "oi"', '=1+1')),
    'B' => array(array('X')),
));
$bom = "\xEF\xBB\xBF";
assertSameValue($bom, substr($csv, 0, 3), 'csv bom');
$esperado = $bom
    . "Nome;Obs\r\n"
    . "\"Ana; Silva\";\"linha1\nlinha2\"\r\n"
    . "\"Diz \"\"oi\"\"\";'=1+1\r\n"
    . "\r\n"
    . "X\r\n";
assertSameValue($esperado, $csv, 'csv completo');

// --- nome de arquivo
assertSameValue('prontuario-42-20261003.pdf', utec_pront_nome_arquivo('42', 'pdf', '2026-10-03'), 'nome arquivo');

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_helper_test.php`
Expected: FAIL — `failed to open stream` for `prontuario_export_helper.php`.

- [ ] **Step 3: Write the implementation**

`application/helpers/prontuario_export_helper.php` — o switch de rótulos é cópia literal de `application/views/adm/usuarios/new/prontuario.php` linhas 452–542:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras da exportação de prontuário (sem CI, sem banco) — testes em tests/prontuario_export_*.

if(!function_exists('utec_pront_rotulos')){
	function utec_pront_rotulos($esp_id){
		$lbl = array(
			'atendimento_inicial' => 'Atendimento Inicial',
			'avaliacao'           => 'Avaliação',
			'reavaliacao'         => 'Reavaliação',
			'ph_inicial'  => 'Descreva a queixa principal, contexto e primeiros registros.',
			'ph_avaliacao'=> 'Registre avaliação clínica, hipóteses e condutas adotadas.',
			'ph_reav'     => 'Registre evolução, retorno ou observações complementares.',
		);
		switch((int)$esp_id){
			case 10: // Fisioterapia
				$lbl['atendimento_inicial'] = 'Queixa / Avaliação Postural';
				$lbl['avaliacao']           = 'Evolução da Sessão / Técnicas Aplicadas';
				$lbl['reavaliacao']         = 'Resposta ao Tratamento / Próxima Sessão';
				$lbl['ph_inicial']   = 'Queixa principal, intensidade de dor, limitações funcionais e achados posturais.';
				$lbl['ph_avaliacao'] = 'Técnicas aplicadas (RPG, PNF, eletroterapia, hidroterapia...), exercícios realizados.';
				$lbl['ph_reav']      = 'Resposta do paciente, evolução do quadro, plano e objetivos para a próxima sessão.';
				break;
			case 36: // Psicologia
				$lbl['atendimento_inicial'] = 'Demanda Apresentada';
				$lbl['avaliacao']           = 'Evolução da Sessão';
				$lbl['reavaliacao']         = 'Observações / Encaminhamentos';
				$lbl['ph_inicial']   = 'Demanda e contexto trazidos pelo paciente nesta sessão.';
				$lbl['ph_avaliacao'] = 'Registro clínico da sessão e intervenções realizadas.';
				$lbl['ph_reav']      = 'Observações, encaminhamentos ou pontos para a próxima sessão.';
				break;
			case 28: // Odontologia
				$lbl['atendimento_inicial'] = 'Queixa / Motivo da Consulta';
				$lbl['avaliacao']           = 'Procedimento(s) Realizado(s)';
				$lbl['reavaliacao']         = 'Prescrição / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, dente(s) envolvido(s), histórico relevante.';
				$lbl['ph_avaliacao'] = 'Procedimento realizado, dente(s) — numeração FDI, anestesia e material utilizado.';
				$lbl['ph_reav']      = 'Medicação prescrita, orientações pós-operatórias, data de retorno.';
				break;
			case 37: // Psiquiatria
				$lbl['atendimento_inicial'] = 'Queixa Principal / Estado Mental';
				$lbl['avaliacao']           = 'Avaliação / Hipótese Diagnóstica';
				$lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
				$lbl['ph_inicial']   = 'Queixa principal, humor, sono, apetite, pensamento e comportamento.';
				$lbl['ph_avaliacao'] = 'Hipótese diagnóstica (CID), exame do estado mental, raciocínio clínico.';
				$lbl['ph_reav']      = 'Conduta adotada, ajuste de medicação, orientações, retorno.';
				break;
			case 27: // Nutrição
				$lbl['atendimento_inicial'] = 'Queixa / Anamnese Alimentar';
				$lbl['avaliacao']           = 'Avaliação Nutricional / Condutas';
				$lbl['reavaliacao']         = 'Evolução / Plano Alimentar';
				$lbl['ph_inicial']   = 'Queixa principal, hábitos alimentares, intolerâncias, histórico de saúde.';
				$lbl['ph_avaliacao'] = 'Avaliação antropométrica, diagnóstico nutricional, condutas adotadas.';
				$lbl['ph_reav']      = 'Evolução do quadro, ajustes no plano alimentar, metas para o próximo retorno.';
				break;
			case 33: // Pediatria
				$lbl['atendimento_inicial'] = 'Queixa / Dados do Responsável';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Retorno';
				$lbl['ph_inicial']   = 'Queixa relatada pelo responsável, histórico de saúde e desenvolvimento da criança.';
				$lbl['ph_avaliacao'] = 'Exame físico, curva de crescimento, hipóteses diagnósticas.';
				$lbl['ph_reav']      = 'Conduta, prescrição, orientações ao responsável, data de retorno.';
				break;
			case 14: // Ginecologia e Obstetrícia
				$lbl['atendimento_inicial'] = 'Queixa / Anamnese Ginecológica';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, ciclo menstrual, DUM, histórico obstétrico.';
				$lbl['ph_avaliacao'] = 'Exame físico, hipóteses diagnósticas, exames solicitados.';
				$lbl['ph_reav']      = 'Conduta, prescrição, orientações, retorno.';
				break;
			case 11: // Fonoaudiologia
				$lbl['atendimento_inicial'] = 'Queixa / Avaliação Fonoaudiológica';
				$lbl['avaliacao']           = 'Evolução da Sessão / Técnicas';
				$lbl['reavaliacao']         = 'Resposta / Próxima Sessão';
				$lbl['ph_inicial']   = 'Queixa principal, histórico de linguagem, deglutição ou voz.';
				$lbl['ph_avaliacao'] = 'Técnicas aplicadas, exercícios realizados, progresso observado.';
				$lbl['ph_reav']      = 'Resposta do paciente, orientações, plano para próxima sessão.';
				break;
			case 3: // Cardiologia
				$lbl['atendimento_inicial'] = 'Queixa Cardiovascular';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
				$lbl['ph_inicial']   = 'Queixa principal (dor torácica, dispneia, palpitações...), PA, FC.';
				$lbl['ph_avaliacao'] = 'Ausculta, hipóteses, ECG, exames solicitados.';
				$lbl['ph_reav']      = 'Conduta, ajuste de medicação, exames de retorno.';
				break;
			case 29: // Oftalmologia
				$lbl['atendimento_inicial'] = 'Queixa / Motivo da Consulta';
				$lbl['avaliacao']           = 'Exame Ocular / Achados';
				$lbl['reavaliacao']         = 'Conduta / Prescrição / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, tempo de evolução, antecedentes oculares e sistêmicos relevantes.';
				$lbl['ph_avaliacao'] = 'Acuidade visual, biomicroscopia, fundoscopia, PIO e demais achados.';
				$lbl['ph_reav']      = 'Conduta adotada, prescrição de óculos/lentes, medicação ocular, orientações e retorno.';
				break;
		}
		return $lbl;
	}
}

if(!function_exists('utec_pront_status_texto')){
	function utec_pront_status_texto($status){
		$mapa = array(1 => 'Em atendimento', 2 => 'Finalizado', 3 => 'Cancelado');
		$s = (int)$status;
		return isset($mapa[$s]) ? $mapa[$s] : 'Pendente';
	}
}

if(!function_exists('utec_pront_status_exame_texto')){
	function utec_pront_status_exame_texto($status){
		$mapa = array(1 => 'Solicitado', 2 => 'Entregue');
		$s = (int)$status;
		return isset($mapa[$s]) ? $mapa[$s] : 'Pendente';
	}
}

if(!function_exists('utec_pront_data_valida')){
	function utec_pront_data_valida($ymd){
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string)$ymd, $m)){ return false; }
		return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
	}
}

if(!function_exists('utec_pront_data_br')){
	function utec_pront_data_br($ymd){
		$d = substr((string)$ymd, 0, 10);
		if(!utec_pront_data_valida($d)){ return ''; }
		return substr($d, 8, 2).'/'.substr($d, 5, 2).'/'.substr($d, 0, 4);
	}
}

if(!function_exists('utec_pront_normalizar_periodo')){
	function utec_pront_normalizar_periodo($de, $ate){
		$de  = utec_pront_data_valida($de) ? (string)$de : '';
		$ate = utec_pront_data_valida($ate) ? (string)$ate : '';
		if($de !== '' && $ate !== '' && $de > $ate){
			$tmp = $de; $de = $ate; $ate = $tmp;
		}
		return array($de, $ate);
	}
}

if(!function_exists('utec_pront_montar_extras')){
	function utec_pront_montar_extras($raw_json, array $labels_por_chave){
		if(!is_string($raw_json) || trim($raw_json) === ''){ return array(); }
		$dec = json_decode($raw_json, true);
		if(!is_array($dec)){ return array(); }
		$saida = array();
		foreach($dec as $chave => $valor){
			if(is_array($valor)){
				$valor = implode(', ', array_map('strval', $valor));
			}
			$valor = trim((string)$valor);
			if($valor === ''){ continue; }
			$rotulo = isset($labels_por_chave[$chave]) ? $labels_por_chave[$chave] : (string)$chave;
			$saida[] = array($rotulo, $valor);
		}
		return $saida;
	}
}

if(!function_exists('utec_pront_extras_texto')){
	function utec_pront_extras_texto(array $extras){
		$partes = array();
		foreach($extras as $par){
			$partes[] = $par[0].': '.$par[1];
		}
		return implode(' | ', $partes);
	}
}

if(!function_exists('utec_pront_linhas_tabulares')){
	function utec_pront_linhas_tabulares(array $dados){
		$atend = array(array('Data', 'Hora', 'Status', 'Profissional', 'Especialidade', 'Atendimento inicial', 'Avaliação', 'Reavaliação', 'Campos extras'));
		$lista = isset($dados['atendimentos']) ? $dados['atendimentos'] : array();
		foreach($lista as $a){
			$atend[] = array(
				utec_pront_data_br($a['data']),
				(string)$a['hora'],
				(string)$a['status_texto'],
				(string)$a['profissional'],
				(string)$a['especialidade'],
				(string)$a['atendimento_inicial'],
				(string)$a['avaliacao'],
				(string)$a['reavaliacao'],
				utec_pront_extras_texto(isset($a['extras']) ? $a['extras'] : array()),
			);
		}
		$exames = array(array('Data', 'Exame', 'Status', 'Profissional', 'Observação'));
		$lista_ex = isset($dados['exames']) ? $dados['exames'] : array();
		foreach($lista_ex as $e){
			$exames[] = array(
				utec_pront_data_br($e['data']),
				(string)$e['exame'],
				(string)$e['status_texto'],
				(string)$e['profissional'],
				(string)$e['obs'],
			);
		}
		return array('Atendimentos' => $atend, 'Exames' => $exames);
	}
}

if(!function_exists('utec_pront_celula_segura')){
	function utec_pront_celula_segura($v){
		$v = (string)$v;
		if($v !== '' && strpos("=+-@\t\r", $v[0]) !== false){
			return "'".$v;
		}
		return $v;
	}
}

if(!function_exists('utec_pront_csv')){
	function utec_pront_csv(array $abas){
		$blocos = array();
		foreach($abas as $linhas){
			$txt = '';
			foreach($linhas as $linha){
				$celulas = array();
				foreach($linha as $c){
					$c = utec_pront_celula_segura($c);
					if(strpbrk($c, ";\"\r\n") !== false){
						$c = '"'.str_replace('"', '""', $c).'"';
					}
					$celulas[] = $c;
				}
				$txt .= implode(';', $celulas)."\r\n";
			}
			$blocos[] = $txt;
		}
		return "\xEF\xBB\xBF".implode("\r\n", $blocos);
	}
}

if(!function_exists('utec_pront_nome_arquivo')){
	function utec_pront_nome_arquivo($id, $ext, $ymd){
		return 'prontuario-'.(int)$id.'-'.str_replace('-', '', substr((string)$ymd, 0, 10)).'.'.$ext;
	}
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_helper_test.php`
Expected: `OK`, exit 0.

- [ ] **Step 5: Commit**

```bash
git add application/helpers/prontuario_export_helper.php tests/prontuario_export_helper_test.php
git commit -m "feat(prontuario): helper puro da exportacao (rotulos, csv, linhas, periodo)"
```

---

### Task 2: View do prontuário usa o helper de rótulos

**Files:**
- Modify: `application/views/adm/usuarios/new/prontuario.php:450-543`
- Test: `tests/prontuario_export_source_test.php` (criar)

**Interfaces:**
- Consumes: `utec_pront_rotulos($esp_id)` (Task 1).

- [ ] **Step 1: Write the failing test**

`tests/prontuario_export_source_test.php`:
```php
<?php
function assertContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $label . ' — trecho ausente: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function assertNotContains($needle, $haystack, $label) {
    if (strpos($haystack, $needle) !== false) {
        fwrite(STDERR, $label . ' — trecho nao deveria existir: ' . $needle . PHP_EOL);
        exit(1);
    }
}
function lerArquivo($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!is_file($path)) {
        fwrite(STDERR, 'Arquivo ausente: ' . $rel . PHP_EOL);
        exit(1);
    }
    return file_get_contents($path);
}

// View do prontuário usa o helper (tela e exportação não divergem)
$view = lerArquivo('application/views/adm/usuarios/new/prontuario.php');
assertContains("load->helper('prontuario_export')", $view, 'view carrega helper');
assertContains('$lbl = utec_pront_rotulos(', $view, 'view usa rotulos do helper');
assertNotContains("case 10: // Fisioterapia", $view, 'switch removido da view');

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php`
Expected: FAIL — `view carrega helper — trecho ausente`.

- [ ] **Step 3: Substituir o bloco de rótulos na view**

Em `application/views/adm/usuarios/new/prontuario.php`, substituir todo o bloco que começa em
`// ── Labels dinâmicos por especialidade (Fase 1) ─────────────────` e termina em
`// ── fim labels ───────────────────────────────────────────────────` (inclusive o `$lbl = [...]`, a linha `$esp = ...` e o `switch` inteiro) por:
```php
              // ── Labels dinâmicos por especialidade (Fase 1) — fonte única em prontuario_export_helper ──
              if(!function_exists('utec_pront_rotulos')){ $this->load->helper('prontuario_export'); }
              $lbl = utec_pront_rotulos(isset($prestador_esp_id) ? (int)$prestador_esp_id : 0);
              // ── fim labels ───────────────────────────────────────────────────
```
(Manter o `<?php` de abertura e o `?>` de fechamento do bloco como estão. `$this->load` funciona dentro de views CI3.)

- [ ] **Step 4: Run tests and lint**

Run:
```
C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php
C:\PHP\PHP7.2\php.exe -l application\views\adm\usuarios\new\prontuario.php
C:\PHP\PHP7.2\php.exe tests\prontuario_export_helper_test.php
```
Expected: `OK`, `No syntax errors detected`, `OK`.

Verificação manual rápida: abrir `http://localhost/utec/adm/atendimento/prontuario/{id_paciente}/{id_agenda}` com um prestador de Fisioterapia — rótulo "Queixa / Avaliação Postural" continua aparecendo.

- [ ] **Step 5: Commit**

```bash
git add application/views/adm/usuarios/new/prontuario.php tests/prontuario_export_source_test.php
git commit -m "refactor(prontuario): rotulos por especialidade vindos do helper compartilhado"
```

---

### Task 3: Gerador XLSX (`Xlsx_simples`)

**Files:**
- Create: `application/libraries/Xlsx_simples.php`
- Test: `tests/prontuario_export_xlsx_test.php`

**Interfaces:**
- Produces:
  - `Xlsx_simples::disponivel(): bool` (estático)
  - `$x->adicionar_aba($nome, array $linhas): void` — primeira linha = cabeçalho (negrito)
  - `$x->gerar(): string|false` — bytes do .xlsx; `false` em falha
  - Carregamento no CI: `$this->load->library('xlsx_simples')` → `$this->xlsx_simples`

- [ ] **Step 1: Write the failing test**

`tests/prontuario_export_xlsx_test.php`:
```php
<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Xlsx_simples.php';

function falha($label) { fwrite(STDERR, $label . PHP_EOL); exit(1); }

if (!Xlsx_simples::disponivel()) { falha('ZipArchive indisponivel neste PHP — habilite extension=zip para rodar o teste'); }

$x = new Xlsx_simples();
$x->adicionar_aba('Atendimentos', array(
    array('Data', 'Texto'),
    array('01/10/2026', 'A & B <c> "d"'),
    array('02/10/2026', "linha1\nlinha2 \x01 controle"),
));
$x->adicionar_aba('Exames/[x]:?', array(array('Exame'), array('Raio-X')));
$bin = $x->gerar();
if (!is_string($bin) || strlen($bin) < 100) { falha('gerar() nao retornou bytes'); }
if (substr($bin, 0, 2) !== 'PK') { falha('nao e um zip'); }

$tmp = tempnam(sys_get_temp_dir(), 'xt');
file_put_contents($tmp, $bin);
$zip = new ZipArchive();
if ($zip->open($tmp) !== true) { falha('zip nao abre'); }
$partes = array('[Content_Types].xml', '_rels/.rels', 'xl/workbook.xml', 'xl/_rels/workbook.xml.rels',
    'xl/styles.xml', 'xl/worksheets/sheet1.xml', 'xl/worksheets/sheet2.xml');
foreach ($partes as $p) {
    $xml = $zip->getFromName($p);
    if ($xml === false) { falha('parte ausente: ' . $p); }
    libxml_use_internal_errors(true);
    if (simplexml_load_string($xml) === false) { falha('XML invalido: ' . $p); }
}
$s1 = $zip->getFromName('xl/worksheets/sheet1.xml');
if (strpos($s1, 'A &amp; B &lt;c&gt; &quot;d&quot;') === false) { falha('texto nao escapado'); }
if (strpos($s1, "\x01") !== false) { falha('caractere de controle nao removido'); }
if (strpos($s1, 'r="B3"') === false) { falha('referencia de celula B3 ausente'); }
if (strpos($s1, 's="1"') === false) { falha('cabecalho sem estilo negrito'); }
$wb = $zip->getFromName('xl/workbook.xml');
if (strpos($wb, 'name="Exames"') === false && strpos($wb, 'name="Examesx"') === false) { falha('nome de aba nao sanitizado: ' . $wb); }
$zip->close();
unlink($tmp);

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_xlsx_test.php`
Expected: FAIL — `failed to open stream` para `Xlsx_simples.php`. (Se falhar com "ZipArchive indisponivel", habilitar `extension=zip` no `php.ini` do `C:\PHP\PHP7.2` e repetir.)

- [ ] **Step 3: Write the implementation**

`application/libraries/Xlsx_simples.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Gerador XLSX mínimo (OOXML) sem dependências: só texto (inlineStr), cabeçalho em negrito.
// Usado pela exportação de prontuário. Requer ZipArchive.
class Xlsx_simples {

	private $abas = array();

	public static function disponivel(){
		return class_exists('ZipArchive');
	}

	public function adicionar_aba($nome, array $linhas){
		$this->abas[] = array('nome' => $this->nome_aba($nome, count($this->abas) + 1), 'linhas' => $linhas);
	}

	public function gerar(){
		if(!self::disponivel() || empty($this->abas)){ return false; }
		$tmp = tempnam(sys_get_temp_dir(), 'xlsx');
		if($tmp === false){ return false; }
		$zip = new ZipArchive();
		if($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true){
			@unlink($tmp);
			return false;
		}
		$zip->addFromString('[Content_Types].xml', $this->content_types());
		$zip->addFromString('_rels/.rels', $this->rels_raiz());
		$zip->addFromString('xl/workbook.xml', $this->workbook());
		$zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbook_rels());
		$zip->addFromString('xl/styles.xml', $this->styles());
		foreach($this->abas as $i => $aba){
			$zip->addFromString('xl/worksheets/sheet'.($i + 1).'.xml', $this->sheet($aba['linhas']));
		}
		$zip->close();
		$bin = file_get_contents($tmp);
		@unlink($tmp);
		return $bin === false ? false : $bin;
	}

	public static function xml($v){
		$v = (string)$v;
		$limpo = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $v);
		if($limpo === null){
			// UTF-8 inválido: remove bytes de controle no modo byte
			$limpo = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v);
		}
		return htmlspecialchars($limpo, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}

	public static function coluna($indice){
		$letras = '';
		$n = (int)$indice + 1;
		while($n > 0){
			$resto = ($n - 1) % 26;
			$letras = chr(65 + $resto).$letras;
			$n = (int)(($n - $resto - 1) / 26);
		}
		return $letras;
	}

	private function nome_aba($nome, $n){
		$nome = preg_replace('/[\\\\\/\?\*\[\]:]/', '', (string)$nome);
		$nome = function_exists('mb_substr') ? mb_substr($nome, 0, 31, 'UTF-8') : substr($nome, 0, 31);
		return $nome !== '' ? $nome : 'Aba'.$n;
	}

	private function sheet(array $linhas){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
		$r = 0;
		foreach($linhas as $linha){
			$r++;
			$xml .= '<row r="'.$r.'">';
			$c = 0;
			foreach($linha as $valor){
				$ref = self::coluna($c).$r;
				$estilo = $r === 1 ? ' s="1"' : '';
				$xml .= '<c r="'.$ref.'" t="inlineStr"'.$estilo.'><is><t xml:space="preserve">'.self::xml($valor).'</t></is></c>';
				$c++;
			}
			$xml .= '</row>';
		}
		return $xml.'</sheetData></worksheet>';
	}

	private function content_types(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			.'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			.'<Default Extension="xml" ContentType="application/xml"/>'
			.'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			.'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
		foreach($this->abas as $i => $aba){
			$xml .= '<Override PartName="/xl/worksheets/sheet'.($i + 1).'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
		}
		return $xml.'</Types>';
	}

	private function rels_raiz(){
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			.'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			.'</Relationships>';
	}

	private function workbook(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
		foreach($this->abas as $i => $aba){
			$xml .= '<sheet name="'.self::xml($aba['nome']).'" sheetId="'.($i + 1).'" r:id="rId'.($i + 1).'"/>';
		}
		return $xml.'</sheets></workbook>';
	}

	private function workbook_rels(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
		$total = count($this->abas);
		foreach($this->abas as $i => $aba){
			$xml .= '<Relationship Id="rId'.($i + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.($i + 1).'.xml"/>';
		}
		$xml .= '<Relationship Id="rId'.($total + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
		return $xml.'</Relationships>';
	}

	private function styles(){
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			.'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
			.'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
			.'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
			.'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
			.'<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
			.'</styleSheet>';
	}
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_xlsx_test.php`
Expected: `OK`. Opcional: salvar o `$bin` num arquivo e abrir no Excel/LibreOffice — 2 abas, cabeçalho em negrito, sem aviso de reparo.

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Xlsx_simples.php tests/prontuario_export_xlsx_test.php
git commit -m "feat(prontuario): gerador XLSX minimo sem dependencias"
```

---

### Task 4: Migração de auditoria + model de coleta

**Files:**
- Modify: `application/controllers/adm/Dev.php` (novo método logo após `migrar_horarios_atendimento()`, ~linha 459)
- Create: `application/models/Prontuario_export_model.php`
- Modify: `tests/prontuario_export_source_test.php` (acrescentar asserts antes do `echo "OK\n";`)

**Interfaces:**
- Consumes: helpers da Task 1; `Padrao_model::get_scope_user_ids($u)`, `ids_to_sql_in($ids)`; métodos privados existentes de `Dev.php`: `run_sql($sql, &$logs, $rotulo)`.
- Produces:
  - `Prontuario_export_model::coletar($id_paciente, $de, $ate, $usuario): array|null` — chaves `paciente` (objeto: `id, nome, telefone, email, dt_cadastro`), `atendimentos`, `exames`, `arquivos` (lista de `array('data','nome','descricao','tipo')`), `meta` (`array('de','ate','gerado_por','gerado_em')`). `null` se o paciente não existe.
  - `Prontuario_export_model::registrar_exportacao($id_usuario, $id_paciente, $formato, $de, $ate, $ip): bool`

- [ ] **Step 1: Write the failing test (acrescentar ao source test)**

Inserir antes de `echo "OK\n";` em `tests/prontuario_export_source_test.php`:
```php
// Migração
$dev = lerArquivo('application/controllers/adm/Dev.php');
assertContains('function migrar_prontuario_exportacoes()', $dev, 'migracao existe');
assertContains('CREATE TABLE IF NOT EXISTS `prontuario_exportacoes`', $dev, 'tabela auditoria');

// Model
$model = lerArquivo('application/models/Prontuario_export_model.php');
assertContains('function coletar(', $model, 'coletar existe');
assertContains('function registrar_exportacao(', $model, 'registrar existe');
assertContains("table_exists('prontuario_exportacoes')", $model, 'auditoria guardada por table_exists');
assertContains('get_scope_user_ids', $model, 'model aplica escopo dos agendamentos');
assertContains('utec_pront_rotulos(', $model, 'model usa rotulos do helper');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php`
Expected: FAIL — `migracao existe — trecho ausente`.

- [ ] **Step 3a: Migração em `Dev.php`**

Inserir após o fechamento de `migrar_horarios_atendimento()`:
```php
	function migrar_prontuario_exportacoes(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$this->run_sql("CREATE TABLE IF NOT EXISTS `prontuario_exportacoes` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`id_usuario` INT NOT NULL,
			`id_paciente` INT NOT NULL,
			`tenant_id` INT NULL DEFAULT NULL,
			`formato` VARCHAR(8) NOT NULL,
			`periodo_de` DATE NULL DEFAULT NULL,
			`periodo_ate` DATE NULL DEFAULT NULL,
			`ip_hash` CHAR(64) NULL DEFAULT NULL,
			`criado_em` DATETIME NOT NULL,
			INDEX `idx_pront_exp_paciente` (`id_paciente`),
			INDEX `idx_pront_exp_usuario` (`id_usuario`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $logs, 'tabela `prontuario_exportacoes` verificada');

		echo '<h3>Migração: auditoria de exportação de prontuário</h3><ul>';
		foreach($logs as $log){
			echo '<li>'.htmlspecialchars($log).'</li>';
		}
		echo '</ul>';
	}
```

- [ ] **Step 3b: Model**

`application/models/Prontuario_export_model.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Coleta os dados do prontuário para exportação (PDF/CSV/XLSX) e registra a auditoria.
// Sem controle de acesso por design: o controller (Atendimento::exportar_prontuario) valida nível e escopo.
class Prontuario_export_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->helper('prontuario_export');
		$this->load->model('padrao_model');
	}

	public function coletar($id_paciente, $de, $ate, $usuario){
		$id_paciente = (int)$id_paciente;
		$qr_pac = $this->db->query("SELECT * FROM usuarios WHERE id = ? LIMIT 1", array($id_paciente));
		if($qr_pac->num_rows() === 0){ return null; }
		$p = $qr_pac->row();
		$paciente = (object)array(
			'id' => (int)$p->id,
			'nome' => isset($p->nome) ? (string)$p->nome : '',
			'telefone' => isset($p->telefone) ? (string)$p->telefone : '',
			'email' => isset($p->email) ? (string)$p->email : '',
			'dt_cadastro' => isset($p->dt_cadastro) ? (string)$p->dt_cadastro : '',
		);

		// Mesmo filtro de escopo de Atendimento::prontuario()
		$where = array('a.id_paciente = '.$id_paciente);
		if((int)$usuario->nivel !== 1){
			$scope_sql = $this->padrao_model->ids_to_sql_in($this->padrao_model->get_scope_user_ids($usuario));
			$where[] = '(a.id_user IN ('.$scope_sql.') OR a.id_paciente IN ('.$scope_sql.') OR a.id_prestador IN ('.$scope_sql.'))';
		}
		$binds = array();
		if($de !== ''){ $where[] = 'a.data_agenda >= ?'; $binds[] = $de; }
		if($ate !== ''){ $where[] = 'a.data_agenda <= ?'; $binds[] = $ate; }

		$tem_esp = $this->db->table_exists('usuarios_especialidades');
		$sel_esp = $tem_esp ? ', ue.nome AS especialidade_nome' : ", '' AS especialidade_nome";
		$join_esp = $tem_esp ? ' LEFT JOIN usuarios_especialidades ue ON ue.id = pr.especialidade' : '';

		$rows = $this->db->query(
			"SELECT a.*, pr.nome AS prestador_nome, pr.especialidade AS prestador_esp_id".$sel_esp."
			FROM agendamentos a
			LEFT JOIN usuarios pr ON pr.id = a.id_prestador".$join_esp."
			WHERE ".implode(' AND ', $where)."
			ORDER BY a.data_agenda ASC, a.hora_agenda ASC, a.id ASC",
			$binds
		)->result();

		$labels_por_esp = $this->labels_campos_extras($rows);

		$atendimentos = array();
		$ids_atend = array();
		foreach($rows as $r){
			$esp = (int)$r->prestador_esp_id;
			$ids_atend[] = (int)$r->id;
			$atendimentos[] = array(
				'id' => (int)$r->id,
				'data' => (string)$r->data_agenda,
				'hora' => substr((string)$r->hora_agenda, 0, 5),
				'status_texto' => utec_pront_status_texto($r->status),
				'profissional' => (string)$r->prestador_nome,
				'especialidade' => (string)$r->especialidade_nome,
				'atendimento_inicial' => isset($r->atendimento_inicial) ? (string)$r->atendimento_inicial : '',
				'avaliacao' => isset($r->avaliacao) ? (string)$r->avaliacao : '',
				'reavaliacao' => isset($r->reavaliacao) ? (string)$r->reavaliacao : '',
				'extras' => utec_pront_montar_extras(
					isset($r->campos_extras) ? (string)$r->campos_extras : '',
					isset($labels_por_esp[$esp]) ? $labels_por_esp[$esp] : array()
				),
				'rotulos' => utec_pront_rotulos($esp),
			);
		}

		return array(
			'paciente' => $paciente,
			'atendimentos' => $atendimentos,
			'exames' => $this->coletar_exames($id_paciente, $ids_atend),
			'arquivos' => $this->coletar_arquivos($id_paciente, $de, $ate),
			'meta' => array(
				'de' => $de,
				'ate' => $ate,
				'gerado_por' => isset($usuario->nome) ? (string)$usuario->nome : '',
				'gerado_em' => date('d/m/Y H:i'),
			),
		);
	}

	public function registrar_exportacao($id_usuario, $id_paciente, $formato, $de, $ate, $ip){
		if(!$this->db->table_exists('prontuario_exportacoes')){ return false; }
		$app_key = getenv('APP_SECRET') ?: (getenv('MERCADOPAGO_WEBHOOK_SECRET') ?: 'utec-ai-salt');
		$tenant_id = null;
		if($this->db->field_exists('tenant_id', 'usuarios')){
			$qr = $this->db->query("SELECT tenant_id FROM usuarios WHERE id = ? LIMIT 1", array((int)$id_usuario));
			if($qr->num_rows() > 0 && (int)$qr->row()->tenant_id > 0){ $tenant_id = (int)$qr->row()->tenant_id; }
		}
		return (bool)$this->db->insert('prontuario_exportacoes', array(
			'id_usuario' => (int)$id_usuario,
			'id_paciente' => (int)$id_paciente,
			'tenant_id' => $tenant_id,
			'formato' => (string)$formato,
			'periodo_de' => $de !== '' ? $de : null,
			'periodo_ate' => $ate !== '' ? $ate : null,
			'ip_hash' => $ip !== '' ? hash('sha256', $ip.$app_key) : null,
			'criado_em' => date('Y-m-d H:i:s'),
		));
	}

	private function labels_campos_extras(array $rows){
		$esp_ids = array();
		foreach($rows as $r){
			if((int)$r->prestador_esp_id > 0){ $esp_ids[(int)$r->prestador_esp_id] = true; }
		}
		if(empty($esp_ids) || !$this->db->table_exists('especialidades_campos_config')){ return array(); }
		$cfg = $this->db->query(
			"SELECT especialidade_id, campo_chave, campo_label FROM especialidades_campos_config
			WHERE especialidade_id IN (".implode(',', array_keys($esp_ids)).")"
		)->result();
		$mapa = array();
		foreach($cfg as $c){
			$mapa[(int)$c->especialidade_id][(string)$c->campo_chave] = (string)$c->campo_label;
		}
		return $mapa;
	}

	private function coletar_exames($id_paciente, array $ids_atend){
		if(empty($ids_atend) || !$this->db->table_exists('usuarios_exames_atendimento')){ return array(); }
		$rows = $this->db->query(
			"SELECT uea.status, ue.obs AS obs, ag.data_agenda, ex.nome AS exame_nome, pr.nome AS prestador_nome
			FROM usuarios_exames_atendimento uea
			LEFT JOIN usuarios_exames ue ON ue.id = uea.id_exame_atendimento
			LEFT JOIN agendamentos ag ON ag.id = uea.id_atendimento
			LEFT JOIN exames ex ON ex.id = uea.id_exame
			LEFT JOIN usuarios pr ON pr.id = ag.id_prestador
			WHERE uea.id_user = ".(int)$id_paciente."
			  AND uea.id_atendimento IN (".implode(',', array_map('intval', $ids_atend)).")
			ORDER BY ag.data_agenda ASC, uea.id ASC"
		)->result();
		$saida = array();
		foreach($rows as $r){
			$saida[] = array(
				'data' => (string)$r->data_agenda,
				'exame' => (string)$r->exame_nome,
				'status_texto' => utec_pront_status_exame_texto($r->status),
				'profissional' => (string)$r->prestador_nome,
				'obs' => (string)$r->obs,
			);
		}
		return $saida;
	}

	private function coletar_arquivos($id_paciente, $de, $ate){
		if(!$this->db->table_exists('pacientes_arquivos')){ return array(); }
		$where = array('id_paciente = '.(int)$id_paciente);
		$binds = array();
		if($de !== ''){ $where[] = 'DATE(dt_cadastro) >= ?'; $binds[] = $de; }
		if($ate !== ''){ $where[] = 'DATE(dt_cadastro) <= ?'; $binds[] = $ate; }
		$rows = $this->db->query(
			"SELECT nome_original, descricao, tipo, dt_cadastro FROM pacientes_arquivos
			WHERE ".implode(' AND ', $where)." ORDER BY dt_cadastro ASC, id ASC",
			$binds
		)->result();
		$saida = array();
		foreach($rows as $r){
			$saida[] = array(
				'data' => substr((string)$r->dt_cadastro, 0, 10),
				'nome' => (string)$r->nome_original,
				'descricao' => (string)$r->descricao,
				'tipo' => (string)$r->tipo,
			);
		}
		return $saida;
	}
}
```

- [ ] **Step 4: Run tests and lint**

Run:
```
C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php
C:\PHP\PHP7.2\php.exe -l application\models\Prontuario_export_model.php
C:\PHP\PHP7.2\php.exe -l application\controllers\adm\Dev.php
```
Expected: `OK` + 2× `No syntax errors detected`.

Rodar a migração local logado como nível 1: `http://localhost/utec/adm/dev/migrar_prontuario_exportacoes` → "tabela `prontuario_exportacoes` verificada". Rodar 2× (idempotente).

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Dev.php application/models/Prontuario_export_model.php tests/prontuario_export_source_test.php
git commit -m "feat(prontuario): migracao de auditoria e model de coleta da exportacao"
```

---

### Task 5: Endpoint `exportar_prontuario` + view do PDF

**Files:**
- Modify: `application/controllers/adm/Atendimento.php` (novo método logo após `prontuario()`, ~linha 430)
- Create: `application/views/adm/usuarios/prontuario_pdf.php`
- Modify: `tests/prontuario_export_source_test.php`

**Interfaces:**
- Consumes: Task 1 (`utec_pront_normalizar_periodo`, `utec_pront_linhas_tabulares`, `utec_pront_csv`, `utec_pront_nome_arquivo`, `utec_pront_data_br`), Task 3 (`Xlsx_simples`), Task 4 (`coletar`, `registrar_exportacao`), `Padrao_model::get_usuario_logado()`, `can_access_usuario($id)`, `application/libraries/M_pdf.php`, CI `download` helper (`force_download($nome, $dados, TRUE)`).
- Produces: URL `adm/atendimento/exportar_prontuario/{id}/{pdf|csv|xlsx}?de=&ate=` (usada na Task 6).

- [ ] **Step 1: Write the failing test**

Acrescentar antes de `echo "OK\n";`:
```php
// Controller
$ctl = lerArquivo('application/controllers/adm/Atendimento.php');
assertContains('function exportar_prontuario(', $ctl, 'endpoint existe');
assertContains("array(1, 2, 3)", $ctl, 'restrito a niveis 1-3');
assertContains('can_access_usuario($id_paciente)', $ctl, 'checa escopo do paciente');
assertContains('registrar_exportacao(', $ctl, 'grava auditoria');
assertContains('Xlsx_simples::disponivel()', $ctl, 'xlsx guardado por ZipArchive');
assertContains("error_reporting(0)", $ctl, 'suprime notices do mPDF');

// View PDF
$pdf = lerArquivo('application/views/adm/usuarios/prontuario_pdf.php');
assertContains('htmlspecialchars', $pdf, 'pdf escapa conteudo');
assertContains('dejavusans', $pdf, 'pdf usa fonte com cache commitado');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php`
Expected: FAIL — `endpoint existe — trecho ausente`.

- [ ] **Step 3a: Controller**

Inserir em `Atendimento.php` logo após o `} // x fn` que fecha `prontuario()`:
```php
function exportar_prontuario($id_paciente=0, $formato=''){
	$id_paciente = (int)$id_paciente;
	$formato = strtolower((string)$formato);
	if($id_paciente <= 1 || !in_array($formato, array('pdf', 'csv', 'xlsx'), true)){
		show_404(); return;
	}
	$dd_user = $this->padrao_model->get_usuario_logado();
	if(!$dd_user || !in_array((int)$dd_user->nivel, array(1, 2, 3), true)){
		show_error('Acesso negado à exportação do prontuário.', 403); return;
	}
	if(!$this->padrao_model->can_access_usuario($id_paciente)){
		show_error('Acesso negado ao prontuario selecionado.', 403); return;
	}
	$this->load->helper(array('prontuario_export', 'download'));
	$this->load->library('xlsx_simples');
	if($formato === 'xlsx' && !Xlsx_simples::disponivel()){
		show_404(); return;
	}

	list($de, $ate) = utec_pront_normalizar_periodo($this->input->get('de', true), $this->input->get('ate', true));
	$this->load->model('Prontuario_export_model', 'prontuario_export_model');
	$dados = $this->prontuario_export_model->coletar($id_paciente, $de, $ate, $dd_user);
	if(!$dados){ show_404(); return; }

	$this->prontuario_export_model->registrar_exportacao(
		(int)$dd_user->id, $id_paciente, $formato, $de, $ate, (string)$this->input->ip_address()
	);
	$nome = utec_pront_nome_arquivo($id_paciente, $formato, date('Y-m-d'));

	if($formato === 'csv'){
		force_download($nome, utec_pront_csv(utec_pront_linhas_tabulares($dados)), TRUE);
		return;
	}

	if($formato === 'xlsx'){
		foreach(utec_pront_linhas_tabulares($dados) as $aba => $linhas){
			$this->xlsx_simples->adicionar_aba($aba, $linhas);
		}
		$bin = $this->xlsx_simples->gerar();
		if($bin === false){
			log_message('error', 'exportar_prontuario: falha ao gerar XLSX do paciente '.$id_paciente);
			show_error('Não foi possível gerar o arquivo. Tente novamente.', 500); return;
		}
		force_download($nome, $bin, TRUE);
		return;
	}

	// PDF — mesmo cuidado de Usuarios::manual_pdf(): o mPDF v6 emite notices que corrompem o PDF
	$html = $this->load->view('adm/usuarios/prontuario_pdf', array('exp' => $dados), true);
	$nivel_erro_anterior = error_reporting();
	error_reporting(0);
	$this->load->library('m_pdf');
	$mpdf = $this->m_pdf->pdf;
	$mpdf->SetTitle('Prontuario - '.$dados['paciente']->nome);
	$mpdf->SetAuthor('UTec Saude');
	$mpdf->SetHTMLHeader('<div style="text-align:right;font-size:8pt;color:#64748b;border-bottom:0.5pt solid #e2e8f0;padding-bottom:4px;">Prontuário — '.htmlspecialchars($dados['paciente']->nome).'</div>');
	$mpdf->SetHTMLFooter('<div style="text-align:center;font-size:8pt;color:#94a3b8;border-top:0.5pt solid #e2e8f0;padding-top:4px;">Documento confidencial &middot; gerado por '.htmlspecialchars($dados['meta']['gerado_por']).' em '.$dados['meta']['gerado_em'].' &middot; página {PAGENO} de {nb}</div>');
	$mpdf->WriteHTML($html);
	$mpdf->Output($nome, 'D');
	error_reporting($nivel_erro_anterior);
}
```

- [ ] **Step 3b: View do PDF**

`application/views/adm/usuarios/prontuario_pdf.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$e = function($v){ return nl2br(htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8')); };
$pac = $exp['paciente'];
$meta = $exp['meta'];
$periodo = 'Histórico completo';
if($meta['de'] !== '' || $meta['ate'] !== ''){
	$periodo = ($meta['de'] !== '' ? utec_pront_data_br($meta['de']) : 'início').' a '.($meta['ate'] !== '' ? utec_pront_data_br($meta['ate']) : 'hoje');
}
?>
<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10pt; line-height:1.45; }
  h1 { font-size:18pt; color:#0f4c81; margin:0 0 4px 0; }
  h2 { font-size:12.5pt; color:#0f4c81; border-bottom:1pt solid #cbd5e1; padding-bottom:3px; margin:18px 0 8px 0; }
  .muted { color:#64748b; font-size:9pt; }
  table.dados { width:100%; border-collapse:collapse; margin-top:6px; }
  table.dados td { padding:4px 6px; border:0.5pt solid #e2e8f0; vertical-align:top; }
  table.dados td.k { width:28%; background:#f1f5f9; font-weight:bold; }
  .atend { border:0.5pt solid #cbd5e1; padding:8px 10px; margin-bottom:10px; }
  .atend-top { font-weight:bold; color:#0f4c81; }
  .campo { margin-top:6px; }
  .campo-l { font-size:8.5pt; color:#475569; font-weight:bold; text-transform:uppercase; }
  table.lista { width:100%; border-collapse:collapse; }
  table.lista th { background:#0f4c81; color:#fff; font-size:8.5pt; text-align:left; padding:4px 6px; }
  table.lista td { border-bottom:0.5pt solid #e2e8f0; padding:4px 6px; font-size:9pt; }
</style>

<h1>Prontuário do paciente</h1>
<div class="muted">Período: <?=$e($periodo)?> &middot; gerado em <?=$e($meta['gerado_em'])?> por <?=$e($meta['gerado_por'])?></div>

<h2>Dados do paciente</h2>
<table class="dados">
  <tr><td class="k">Nome</td><td><?=$e($pac->nome)?></td></tr>
  <tr><td class="k">Telefone</td><td><?=$e($pac->telefone !== '' ? $pac->telefone : 'Não informado')?></td></tr>
  <tr><td class="k">E-mail</td><td><?=$e($pac->email !== '' ? $pac->email : 'Não informado')?></td></tr>
  <tr><td class="k">Cadastro</td><td><?=$e($pac->dt_cadastro !== '' ? utec_pront_data_br($pac->dt_cadastro) : 'Não informado')?></td></tr>
</table>

<h2>Atendimentos (<?=count($exp['atendimentos'])?>)</h2>
<?php if(empty($exp['atendimentos'])){ ?>
  <p class="muted">Nenhum atendimento no período.</p>
<?php } ?>
<?php foreach($exp['atendimentos'] as $a){ $r = $a['rotulos']; ?>
  <div class="atend">
    <div class="atend-top"><?=$e(utec_pront_data_br($a['data']))?> às <?=$e($a['hora'])?>h &middot; <?=$e($a['status_texto'])?></div>
    <div class="muted"><?=$e($a['profissional'] !== '' ? $a['profissional'] : 'Profissional não informado')?><?php if($a['especialidade'] !== ''){ ?> &middot; <?=$e($a['especialidade'])?><?php } ?></div>
    <?php foreach(array('atendimento_inicial', 'avaliacao', 'reavaliacao') as $campo){ if(trim($a[$campo]) === ''){ continue; } ?>
      <div class="campo"><div class="campo-l"><?=$e($r[$campo])?></div><?=$e($a[$campo])?></div>
    <?php } ?>
    <?php foreach($a['extras'] as $par){ ?>
      <div class="campo"><div class="campo-l"><?=$e($par[0])?></div><?=$e($par[1])?></div>
    <?php } ?>
  </div>
<?php } ?>

<h2>Exames (<?=count($exp['exames'])?>)</h2>
<?php if(empty($exp['exames'])){ ?>
  <p class="muted">Nenhum exame no período.</p>
<?php } else { ?>
  <table class="lista">
    <tr><th>Data</th><th>Exame</th><th>Status</th><th>Profissional</th><th>Observação</th></tr>
    <?php foreach($exp['exames'] as $x){ ?>
      <tr><td><?=$e(utec_pront_data_br($x['data']))?></td><td><?=$e($x['exame'])?></td><td><?=$e($x['status_texto'])?></td><td><?=$e($x['profissional'])?></td><td><?=$e($x['obs'])?></td></tr>
    <?php } ?>
  </table>
<?php } ?>

<h2>Arquivos anexados (<?=count($exp['arquivos'])?>)</h2>
<?php if(empty($exp['arquivos'])){ ?>
  <p class="muted">Nenhum arquivo no período.</p>
<?php } else { ?>
  <table class="lista">
    <tr><th>Data</th><th>Arquivo</th><th>Descrição</th><th>Tipo</th></tr>
    <?php foreach($exp['arquivos'] as $f){ ?>
      <tr><td><?=$e(utec_pront_data_br($f['data']))?></td><td><?=$e($f['nome'])?></td><td><?=$e($f['descricao'])?></td><td><?=$e($f['tipo'])?></td></tr>
    <?php } ?>
  </table>
  <p class="muted">Os arquivos não são incluídos neste documento; ficam disponíveis no prontuário do sistema.</p>
<?php } ?>
```

- [ ] **Step 4: Run tests, lint e teste manual**

Run:
```
C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php
C:\PHP\PHP7.2\php.exe -l application\controllers\adm\Atendimento.php
C:\PHP\PHP7.2\php.exe -l application\views\adm\usuarios\prontuario_pdf.php
```
Expected: `OK` + 2× `No syntax errors detected`.

Manual (local, logado como prestador nível 3 com paciente que tenha atendimentos):
- `http://localhost/utec/adm/atendimento/exportar_prontuario/{id}/pdf` → baixa `prontuario-{id}-AAAAMMDD.pdf`, acentos corretos, rótulos da especialidade.
- `.../csv` → abre no Excel com acentos, colunas separadas.
- `.../xlsx` → abre no Excel/LibreOffice sem aviso de reparo, 2 abas.
- `.../pdf?de=2026-01-01&ate=2026-01-31` → só atendimentos do período.
- `.../doc` → 404. Logado como nível 4 → 403.
- `SELECT * FROM prontuario_exportacoes ORDER BY id DESC LIMIT 5` → uma linha por download.

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Atendimento.php application/views/adm/usuarios/prontuario_pdf.php tests/prontuario_export_source_test.php
git commit -m "feat(prontuario): endpoint de exportacao em PDF, CSV e XLSX com auditoria"
```

---

### Task 6: Botão "Exportar" na tela + manual + CLAUDE.md

**Files:**
- Modify: `application/views/adm/usuarios/new/prontuario.php:400-403` (bloco `timeline-actions` do cabeçalho do paciente)
- Modify: `application/libraries/Manual_conteudo.php` (capítulo `prontuario`, ~linha 158-176)
- Modify: `CLAUDE.md` (seções 6.2, 13, 15.1)
- Modify: `tests/prontuario_export_source_test.php`

**Interfaces:**
- Consumes: URL da Task 5; `Xlsx_simples::disponivel()` (Task 3).

- [ ] **Step 1: Write the failing test**

Acrescentar antes de `echo "OK\n";`:
```php
// Botão na tela
assertContains('exportar_prontuario/', $view = lerArquivo('application/views/adm/usuarios/new/prontuario.php'), 'botao exportar na view');
assertContains("in_array((int)\$this->session->userdata('nivel'), array(1, 2, 3), true)", $view, 'botao so para niveis 1-3');
assertContains("class_exists('ZipArchive')", $view, 'xlsx oculto sem ZipArchive');

// Manual (regra de sincronização da seção 19 do CLAUDE.md)
$manual = lerArquivo('application/libraries/Manual_conteudo.php');
assertContains('Exportar', $manual, 'manual cobre exportacao');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php`
Expected: FAIL — `botao exportar na view — trecho ausente`.

- [ ] **Step 3a: Botão na view**

Em `application/views/adm/usuarios/new/prontuario.php`, dentro de `<div class="timeline-actions" style="margin-top:0">`, logo após o link "Novo agendamento", inserir:
```php
                        <?php if(in_array((int)$this->session->userdata('nivel'), array(1, 2, 3), true)){
                          $url_exp = base_url('adm/atendimento/exportar_prontuario/'.(int)$paciente->id.'/');
                          $formatos_exp = array('pdf' => 'PDF', 'csv' => 'CSV');
                          if(class_exists('ZipArchive')){ $formatos_exp['xlsx'] = 'Excel (XLSX)'; }
                        ?>
                        <details class="pront-export" style="display:inline-block;position:relative;">
                          <summary class="btn btn-outline-primary" style="list-style:none;cursor:pointer;">Exportar</summary>
                          <div style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:250px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
                            <div style="font-size:12px;color:#5f708c;margin-bottom:6px;">Período (opcional)</div>
                            <div class="d-flex" style="gap:6px;margin-bottom:10px;">
                              <input type="date" class="form-control form-control-sm pront-exp-de" aria-label="Data inicial">
                              <input type="date" class="form-control form-control-sm pront-exp-ate" aria-label="Data final">
                            </div>
                            <?php foreach($formatos_exp as $fmt => $rotulo_fmt){ ?>
                              <a class="btn btn-sm btn-light btn-block text-left pront-exp-link" data-base="<?=htmlspecialchars($url_exp.$fmt)?>" href="<?=htmlspecialchars($url_exp.$fmt)?>"><?=$rotulo_fmt?></a>
                            <?php } ?>
                            <div style="font-size:11px;color:#8a99b3;margin-top:8px;">Documento confidencial. A exportação fica registrada.</div>
                          </div>
                        </details>
                        <script>
                          (function(){
                            var box = document.currentScript.previousElementSibling;
                            function atualizar(){
                              var de = box.querySelector('.pront-exp-de').value;
                              var ate = box.querySelector('.pront-exp-ate').value;
                              var qs = [];
                              if(de){ qs.push('de=' + encodeURIComponent(de)); }
                              if(ate){ qs.push('ate=' + encodeURIComponent(ate)); }
                              box.querySelectorAll('.pront-exp-link').forEach(function(a){
                                a.href = a.getAttribute('data-base') + (qs.length ? '?' + qs.join('&') : '');
                              });
                            }
                            box.querySelectorAll('input[type=date]').forEach(function(i){ i.addEventListener('change', atualizar); });
                          })();
                        </script>
                        <?php } ?>
```

- [ ] **Step 3b: Manual**

Em `application/libraries/Manual_conteudo.php`, capítulo `'slug' => 'prontuario'`:
- No array `3 => array(...)` acrescentar: `'Para entregar uma cópia ao paciente ou analisar em planilha, use o botão `Exportar` no topo do prontuário: escolha um período (opcional) e o formato - PDF, CSV ou Excel (XLSX). Cada exportação fica registrada.'`
- No array `2 => array(...)` acrescentar: `'Também pode exportar o prontuário pelo botão `Exportar` (PDF, CSV ou Excel), com período opcional. As exportações ficam registradas para auditoria.'`
- No array `4 => array(...)` acrescentar: `'A exportação do prontuário é restrita ao estabelecimento e ao profissional, por se tratar de conteúdo clínico sigiloso.'`
- Trocar `'atualizado_em' => '2026-09-07'` desse capítulo por `'atualizado_em' => '2026-10-03'`.
(Não alterar `Manual_conteudo::VERSAO` — a estrutura do array não muda.)

- [ ] **Step 3c: CLAUDE.md**

- Seção 6.2, linha de `Atendimento.php`: acrescentar "+ `exportar_prontuario/{id}/{pdf|csv|xlsx}?de=&ate=` (níveis 1–3, auditado)".
- Seção 4.2 **Saúde e Agenda**: acrescentar `prontuario_exportacoes` — auditoria de exportação (`id_usuario`, `id_paciente`, `formato`, período, `ip_hash`).
- Seção 13: linha `| adm/dev/migrar_prontuario_exportacoes | Cria prontuario_exportacoes (idempotente) |`.
- Seção 15.1: `- [x] Exportar prontuário por paciente (PDF/CSV/XLSX) com período e auditoria — helper `prontuario_export_helper.php`, `Xlsx_simples`, `Prontuario_export_model``.

- [ ] **Step 4: Run all tests and lint**

Run:
```
C:\PHP\PHP7.2\php.exe tests\prontuario_export_helper_test.php
C:\PHP\PHP7.2\php.exe tests\prontuario_export_xlsx_test.php
C:\PHP\PHP7.2\php.exe tests\prontuario_export_source_test.php
C:\PHP\PHP7.2\php.exe -l application\views\adm\usuarios\new\prontuario.php
C:\PHP\PHP7.2\php.exe -l application\libraries\Manual_conteudo.php
```
Expected: 3× `OK`, 2× `No syntax errors detected`.

Manual: nível 3 vê o botão, escolhe período, links recebem `?de=&ate=`; nível 4 não vê o botão. Tela `adm/usuarios/manual/3` mostra o tópico novo.

- [ ] **Step 5: Commit**

```bash
git add application/views/adm/usuarios/new/prontuario.php application/libraries/Manual_conteudo.php CLAUDE.md tests/prontuario_export_source_test.php
git commit -m "feat(prontuario): botao Exportar na tela, manual e documentacao"
```

---

## Deploy (agente-dev-infra, após code review)
Arquivos runtime, nesta ordem (dependências antes de quem as usa):
1. `application/helpers/prontuario_export_helper.php`
2. `application/libraries/Xlsx_simples.php`
3. `application/models/Prontuario_export_model.php`
4. `application/views/adm/usuarios/prontuario_pdf.php`
5. `application/controllers/adm/Dev.php`
6. `application/controllers/adm/Atendimento.php`
7. `application/libraries/Manual_conteudo.php`
8. `application/views/adm/usuarios/new/prontuario.php` (por último — depende do helper)

Depois: rodar `adm/dev/migrar_prontuario_exportacoes` logado como nível 1; healthcheck (home/`admin` 200, `adm/atendimento` sem sessão 302, `adm/atendimento/exportar_prontuario/2/pdf` sem sessão 302). Conferir no servidor se `ZipArchive` existe (se não, o item XLSX simplesmente não aparece).
