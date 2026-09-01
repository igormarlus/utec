# Lembrete de Consulta por WhatsApp via Cron — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Um endpoint publico horario, chamado pelo cron do cPanel, que envia por WhatsApp um lembrete unico ao paciente e ao profissional cerca de 6 horas antes da consulta, reaproveitando conexao, webhook e log ja existentes.

**Architecture:** Novo controller raiz `Cron.php` autenticado por token na querystring. Ele carrega os agendamentos elegiveis via `Whatsapp_model::get_agendamentos_para_lembrete()` (janela "faltam ate 7h", `status = 0`, sem lembrete previo do mesmo tipo, e — so para o paciente — sem confirmacao registrada) e dispara cada um por `Whatsapp_agendamento::notificar_lembrete($id, $tipo)`. O disparo reusa o template atual de confirmacao, a checagem de cota trial e o `registrar_log()`, agora carimbado com uma coluna nova `whatsapp_notificacoes.tipo_notificacao`. Nenhuma mudanca no webhook: os botoes do lembrete carregam o mesmo payload `confirmar_agendamento:{id}` / `cancelar_agendamento:{id}`.

**Tech Stack:** PHP 7, CodeIgniter 3.1.10, MySQL/InnoDB, Meta WhatsApp Cloud API, testes PHP diretos em `tests/`, PowerShell, git.

**Spec:** `docs/superpowers/specs/2026-09-01-whatsapp-lembrete-consulta-cron-design.md`
**Pendencia relacionada:** `docs/whatsapp-lembrete-templates-pendente.md`

## Global Constraints

- CodeIgniter 3.1.10. Nao migrar de framework. APIs permitidas: `$this->db`, `$this->input`, `$this->load`, `$this->config`, `$this->session`, `$this->output`, `log_message()`.
- Nao modificar `system/`.
- Nunca usar `log_message('warning', ...)` — esse nivel nao existe no CI3. Usar `debug`, `info` ou `error`.
- Nao usar `$_POST`/`$_GET` direto quando houver equivalente CI. Excecao ja aceita no projeto: `$_GET['hub_*']` e `$_SERVER['HTTP_X_HUB_SIGNATURE_256']` no `Webhooks.php`. Para o token do cron usar `$this->input->get('token')`.
- Helpers puros no padrao do arquivo: cada funcao embrulhada em `if (!function_exists('...')) { ... }`.
- Testes no padrao de `tests/`: `define('BASEPATH', __DIR__);`, `require` do arquivo alvo, funcao `assertSameValue`/`assert*` local, `echo "OK\n";` no fim, `exit(1)` na falha. Rodar com `php tests\<arquivo>.php`.
- Migracoes: metodo novo em `application/controllers/adm/Dev.php`, protegido por `if($this->session->userdata('nivel') != 1){ show_error('Acesso negado.', 403); return; }`, idempotente (checar `field_exists` / `SHOW INDEX` antes de `ALTER`).
- Config sensivel: arquivo em `application/config/`, valor lido de `getenv()` com fallback hardcoded, no padrao de `application/config/mercadopago.php`.
- Validacao sem suite automatizada de runtime: `php -l <arquivo>` apos cada mudanca em `.php` — esperado `No syntax errors detected in <arquivo>`.
- Enum de `tipo_notificacao`: exatamente `confirmacao`, `lembrete_paciente`, `lembrete_profissional`.
- `agendamentos.status`: `0` pendente, `1` em atendimento, `2` finalizado, `3` cancelado. Lembrete so para `status = 0`.
- Janela: consulta com `TIMESTAMP(data_agenda, hora_agenda)` entre agora e agora + 7h.

**Branch:** `feat/seo-whatsapp-confirmacao-consulta` (atual; o design doc ja foi commitado nela).

---

## Mapa de arquivos

| Arquivo | Acao | Responsabilidade |
|---------|------|-----------------|
| `application/helpers/whatsapp_agendamento_helper.php` | Modificar | 3 funcoes puras: tipos validos de lembrete e intervalo da janela |
| `application/controllers/adm/Dev.php` | Modificar | `migrar_lembrete_whatsapp()` — coluna + indice em `whatsapp_notificacoes` |
| `application/models/Whatsapp_model.php` | Modificar | `registrar_log()` grava `tipo_notificacao`; novo `get_agendamentos_para_lembrete()` |
| `application/libraries/Whatsapp_agendamento.php` | Modificar | `notificar_lembrete($id, $tipo)`; contexto passa a trazer `prestador_telefone` |
| `application/config/whatsapp.php` | Criar | Token do cron (`getenv('WHATSAPP_CRON_TOKEN')` + fallback) |
| `application/controllers/Cron.php` | Criar | Endpoint `lembrete_whatsapp()` — valida token, itera, responde JSON |
| `application/config/routes.php` | Modificar | Rota `cron/lembrete-whatsapp` |
| `tests/whatsapp_lembrete_test.php` | Criar | Testes das funcoes puras |
| `tests/whatsapp_lembrete_source_test.php` | Criar | Asserts de fonte para Dev, Model, Library, Controller, rota e config |
| `CLAUDE.md` | Modificar | Registrar o cron como implementado (secao 10.3.1 e roadmap 15.3) |

---

## Task 1: Funcoes puras de lembrete

**Files:**
- Create: `tests/whatsapp_lembrete_test.php`
- Modify: `application/helpers/whatsapp_agendamento_helper.php`

**Interfaces:**
- Produces:
  - `utec_whatsapp_lembrete_tipos(): array` — `['lembrete_paciente', 'lembrete_profissional']`
  - `utec_whatsapp_lembrete_tipo_valido($tipo): bool`
  - `utec_whatsapp_lembrete_intervalo($agora_ts, $horas = 7): array` — `['inicio' => 'Y-m-d H:i:s', 'fim' => 'Y-m-d H:i:s']`, onde `inicio` = `$agora_ts` e `fim` = `$agora_ts + $horas*3600`

- [ ] **Step 1: Escrever o teste que falha**

Create `tests/whatsapp_lembrete_test.php`:

```php
<?php

define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

assertSameValue(
    ['lembrete_paciente', 'lembrete_profissional'],
    utec_whatsapp_lembrete_tipos(),
    'Os tipos de lembrete devem ser paciente e profissional.'
);

assertSameValue(true, utec_whatsapp_lembrete_tipo_valido('lembrete_paciente'), 'lembrete_paciente e valido.');
assertSameValue(true, utec_whatsapp_lembrete_tipo_valido('lembrete_profissional'), 'lembrete_profissional e valido.');
assertSameValue(false, utec_whatsapp_lembrete_tipo_valido('confirmacao'), 'confirmacao nao e um tipo de lembrete.');
assertSameValue(false, utec_whatsapp_lembrete_tipo_valido(''), 'String vazia nao e um tipo de lembrete.');

$base = mktime(8, 0, 0, 9, 1, 2026); // 2026-09-01 08:00:00
$intervalo = utec_whatsapp_lembrete_intervalo($base);
assertSameValue('2026-09-01 08:00:00', $intervalo['inicio'], 'Inicio da janela e o agora.');
assertSameValue('2026-09-01 15:00:00', $intervalo['fim'], 'Fim da janela e agora + 7 horas.');

$intervalo6 = utec_whatsapp_lembrete_intervalo($base, 6);
assertSameValue('2026-09-01 14:00:00', $intervalo6['fim'], 'O numero de horas da janela e configuravel.');

echo "OK\n";
```

- [ ] **Step 2: Rodar o teste e ver falhar**

Run: `php tests\whatsapp_lembrete_test.php`
Expected: FAIL — `Call to undefined function utec_whatsapp_lembrete_tipos()`.

- [ ] **Step 3: Implementar as funcoes puras**

No fim de `application/helpers/whatsapp_agendamento_helper.php`, antes do fechamento do arquivo, adicionar:

```php
if (!function_exists('utec_whatsapp_lembrete_tipos')) {
    function utec_whatsapp_lembrete_tipos()
    {
        return ['lembrete_paciente', 'lembrete_profissional'];
    }
}

if (!function_exists('utec_whatsapp_lembrete_tipo_valido')) {
    function utec_whatsapp_lembrete_tipo_valido($tipo)
    {
        return in_array((string)$tipo, utec_whatsapp_lembrete_tipos(), true);
    }
}

if (!function_exists('utec_whatsapp_lembrete_intervalo')) {
    function utec_whatsapp_lembrete_intervalo($agora_ts, $horas = 7)
    {
        $agora_ts = (int)$agora_ts;
        $horas = (int)$horas;
        return [
            'inicio' => date('Y-m-d H:i:s', $agora_ts),
            'fim' => date('Y-m-d H:i:s', $agora_ts + ($horas * 3600)),
        ];
    }
}
```

- [ ] **Step 4: Rodar o teste e ver passar**

Run: `php tests\whatsapp_lembrete_test.php`
Expected: `OK`

- [ ] **Step 5: Lint**

Run: `php -l application\helpers\whatsapp_agendamento_helper.php`
Expected: `No syntax errors detected in application\helpers\whatsapp_agendamento_helper.php`

- [ ] **Step 6: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php tests/whatsapp_lembrete_test.php
git commit -m "feat: helpers puros do lembrete de consulta por whatsapp"
```

---

## Task 2: Coluna `tipo_notificacao` e log carimbado

**Files:**
- Modify: `application/controllers/adm/Dev.php`
- Modify: `application/models/Whatsapp_model.php`
- Create: `tests/whatsapp_lembrete_source_test.php`

**Interfaces:**
- Consumes: nada de tasks anteriores.
- Produces:
  - Rota de migracao `adm/dev/migrar_lembrete_whatsapp` que adiciona `whatsapp_notificacoes.tipo_notificacao` VARCHAR(30) NOT NULL DEFAULT `'confirmacao'` e o indice `idx_wn_agendamento_tipo (id_agendamento, tipo_notificacao)`.
  - `Whatsapp_model::registrar_log($data)` passa a persistir `tipo_notificacao` a partir de `$data['tipo_notificacao']` (default `'confirmacao'`), via `filtrar_colunas` (degrada se a coluna nao existir).

- [ ] **Step 1: Escrever o teste de fonte que falha**

Create `tests/whatsapp_lembrete_source_test.php`:

```php
<?php

function assertSource($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$dev = file_get_contents(__DIR__ . '/../application/controllers/adm/Dev.php');
$model = file_get_contents(__DIR__ . '/../application/models/Whatsapp_model.php');

// --- Task 2: migracao + log ---
assertSource(strpos($dev, 'function migrar_lembrete_whatsapp(') !== false, 'Dev.php deve expor migrar_lembrete_whatsapp().');
assertSource(strpos($dev, "userdata('nivel') != 1") !== false, 'A migracao deve ser protegida por nivel 1.');
assertSource(strpos($dev, 'tipo_notificacao') !== false, 'A migracao deve adicionar a coluna tipo_notificacao.');
assertSource(strpos($dev, 'idx_wn_agendamento_tipo') !== false, 'A migracao deve criar o indice idx_wn_agendamento_tipo.');
assertSource(strpos($model, "'tipo_notificacao' =>") !== false, 'registrar_log deve gravar tipo_notificacao.');
assertSource(strpos($model, "log_message('warning'") === false, 'O model nao deve usar log_message(warning).');

echo "OK\n";
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: FAIL — `Dev.php deve expor migrar_lembrete_whatsapp().`

- [ ] **Step 3: Implementar a migracao**

Em `application/controllers/adm/Dev.php`, logo apos o metodo `migrar_token_senha()` (por volta da linha 388), adicionar:

```php
	function migrar_lembrete_whatsapp(){
		if($this->session->userdata('nivel') != 1){
			show_error('Acesso negado.', 403); return;
		}
		$logs = [];
		$tabela = 'whatsapp_notificacoes';

		if(!$this->db->table_exists($tabela)){
			$logs[] = "❌ Tabela <strong>$tabela</strong> nao existe. Crie o fluxo de confirmacao antes.";
		} else {
			if(!$this->db->field_exists('tipo_notificacao', $tabela)){
				if($this->db->query("ALTER TABLE `$tabela` ADD COLUMN `tipo_notificacao` VARCHAR(30) NOT NULL DEFAULT 'confirmacao'")){
					$logs[] = "✅ Coluna <strong>tipo_notificacao</strong> adicionada.";
				} else {
					$logs[] = "❌ Erro ao adicionar tipo_notificacao: ".$this->db->error()['message'];
				}
			} else {
				$logs[] = "⚠️ Coluna <strong>tipo_notificacao</strong> ja existe.";
			}

			$temIndice = $this->db->query("SHOW INDEX FROM `$tabela` WHERE Key_name = 'idx_wn_agendamento_tipo'")->num_rows() > 0;
			if(!$temIndice){
				if($this->db->query("ALTER TABLE `$tabela` ADD INDEX `idx_wn_agendamento_tipo` (`id_agendamento`, `tipo_notificacao`)")){
					$logs[] = "✅ Indice <strong>idx_wn_agendamento_tipo</strong> criado.";
				} else {
					$logs[] = "❌ Erro ao criar indice: ".$this->db->error()['message'];
				}
			} else {
				$logs[] = "⚠️ Indice <strong>idx_wn_agendamento_tipo</strong> ja existe.";
			}
		}

		echo '<h3>Migração: lembrete de consulta por WhatsApp</h3><ul>';
		foreach($logs as $l) echo "<li>$l</li>";
		echo '</ul>';
	}
```

- [ ] **Step 4: Fazer `registrar_log` gravar o tipo**

Em `application/models/Whatsapp_model.php`, no metodo `registrar_log()`, dentro do array passado a `filtrar_colunas($this->log_table, [ ... ])`, adicionar a chave (logo depois de `'status_confirmacao' => ...`):

```php
            'tipo_notificacao' => trim((string)utec_whatsapp_read($data, 'tipo_notificacao', 'confirmacao')),
```

- [ ] **Step 5: Rodar o teste e ver passar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: `OK`

- [ ] **Step 6: Lint**

Run:
```powershell
php -l application\controllers\adm\Dev.php
php -l application\models\Whatsapp_model.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 7: Commit**

```bash
git add application/controllers/adm/Dev.php application/models/Whatsapp_model.php tests/whatsapp_lembrete_source_test.php
git commit -m "feat: coluna tipo_notificacao e migracao do lembrete whatsapp"
```

---

## Task 3: Query de elegibilidade no `Whatsapp_model`

**Files:**
- Modify: `application/models/Whatsapp_model.php`
- Modify: `tests/whatsapp_lembrete_source_test.php`

**Interfaces:**
- Consumes: coluna `whatsapp_notificacoes.tipo_notificacao` (Task 2).
- Produces:
  - `Whatsapp_model::get_agendamentos_para_lembrete($tipo, $inicio, $fim): array` — array de objetos com a propriedade `id` (int do agendamento). Retorna `[]` se `$tipo` invalido, se as tabelas nao existirem ou se `tipo_notificacao` ainda nao existir na coluna.
  - Filtros por tipo:
    - `lembrete_paciente`: `a.status = 0`, `TIMESTAMP(a.data_agenda, a.hora_agenda)` em `[$inicio, $fim]`, sem linha `wn.tipo_notificacao = 'lembrete_paciente'` para o agendamento, e sem linha `wn.status_confirmacao = 'confirmado'` para o agendamento.
    - `lembrete_profissional`: iguais, com `tipo_notificacao = 'lembrete_profissional'`, sem a regra de `confirmado`, e `a.id_prestador > 0`.

- [ ] **Step 1: Acrescentar os asserts de fonte que falham**

Em `tests/whatsapp_lembrete_source_test.php`, antes de `echo "OK\n";`, adicionar:

```php
// --- Task 3: query de elegibilidade ---
assertSource(strpos($model, 'function get_agendamentos_para_lembrete(') !== false, 'O model deve expor get_agendamentos_para_lembrete().');
assertSource(strpos($model, 'utec_whatsapp_lembrete_tipo_valido(') !== false, 'A query deve validar o tipo de lembrete.');
assertSource(strpos($model, 'NOT EXISTS') !== false, 'A query deve usar NOT EXISTS para a idempotencia.');
assertSource(strpos($model, "TIMESTAMP(a.data_agenda, a.hora_agenda)") !== false, 'A query deve comparar a data e hora do agendamento como TIMESTAMP.');
assertSource(strpos($model, "a.status = 0") !== false, 'A query deve exigir agendamento pendente (status 0).');
assertSource(strpos($model, "'lembrete_profissional'") !== false, 'A query deve tratar o tipo lembrete_profissional.');
assertSource(strpos($model, "a.id_prestador > 0") !== false, 'O lembrete do profissional exige prestador vinculado.');
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: FAIL — `O model deve expor get_agendamentos_para_lembrete().`

- [ ] **Step 3: Implementar a query**

Em `application/models/Whatsapp_model.php`, adicionar o metodo abaixo (por exemplo logo apos `get_notificacao_por_agendamento()`):

```php
    public function get_agendamentos_para_lembrete($tipo, $inicio, $fim)
    {
        $tipo = (string)$tipo;
        if (!utec_whatsapp_lembrete_tipo_valido($tipo)
            || !$this->db->table_exists('agendamentos')
            || !$this->db->table_exists($this->log_table)
            || !$this->db->field_exists('tipo_notificacao', $this->log_table)) {
            return [];
        }

        $inicio = $this->db->escape((string)$inicio);
        $fim = $this->db->escape((string)$fim);
        $tipoEscapado = $this->db->escape($tipo);

        $condicaoConfirmado = '';
        $condicaoPrestador = '';
        if ($tipo === 'lembrete_paciente') {
            $condicaoConfirmado =
                " AND NOT EXISTS (SELECT 1 FROM `{$this->log_table}` wc"
                . " WHERE wc.id_agendamento = a.id AND wc.status_confirmacao = 'confirmado')";
        } else {
            $condicaoPrestador = ' AND a.id_prestador > 0';
        }

        $sql =
            "SELECT a.id\n"
            . "FROM `agendamentos` a\n"
            . "WHERE a.status = 0\n"
            . "  AND TIMESTAMP(a.data_agenda, a.hora_agenda) >= {$inicio}\n"
            . "  AND TIMESTAMP(a.data_agenda, a.hora_agenda) <= {$fim}\n"
            . "  AND NOT EXISTS (SELECT 1 FROM `{$this->log_table}` wn"
            . " WHERE wn.id_agendamento = a.id AND wn.tipo_notificacao = {$tipoEscapado})\n"
            . $condicaoConfirmado
            . $condicaoPrestador
            . "\nORDER BY a.data_agenda ASC, a.hora_agenda ASC, a.id ASC";

        $qr = $this->db->query($sql);
        return $qr ? $qr->result() : [];
    }
```

- [ ] **Step 4: Rodar o teste e ver passar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: `OK`

- [ ] **Step 5: Lint**

Run: `php -l application\models\Whatsapp_model.php`
Expected: `No syntax errors detected in application\models\Whatsapp_model.php`

- [ ] **Step 6: Commit**

```bash
git add application/models/Whatsapp_model.php tests/whatsapp_lembrete_source_test.php
git commit -m "feat: selecao de agendamentos elegiveis para lembrete whatsapp"
```

---

## Task 4: `notificar_lembrete()` na biblioteca

**Files:**
- Modify: `application/libraries/Whatsapp_agendamento.php`
- Modify: `tests/whatsapp_lembrete_source_test.php`

**Interfaces:**
- Consumes:
  - `utec_whatsapp_lembrete_tipo_valido($tipo)` (Task 1).
  - `Whatsapp_model::registrar_log()` com suporte a `tipo_notificacao` (Task 2).
- Produces:
  - `Whatsapp_agendamento::notificar_lembrete($id_agendamento, $tipo): array` — retorno no mesmo formato de `notificar_agendamento()`: `['sent' => bool, 'reason' => string, 'wamid' => string, 'error' => string]`. Para `lembrete_profissional` usa `prestador_telefone`; para `lembrete_paciente` usa `paciente_telefone`. Reusa `montar_payload()` (template atual da config), `validar_quota_tenant()` e `registrar_log()` carimbando `tipo_notificacao => $tipo`.
  - `buscar_contexto_agendamento()` passa a selecionar `pr.telefone AS prestador_telefone`.

- [ ] **Step 1: Acrescentar os asserts de fonte que falham**

Em `tests/whatsapp_lembrete_source_test.php`, adicionar antes de `echo "OK\n";`:

```php
// --- Task 4: notificar_lembrete na biblioteca ---
$lib = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_agendamento.php');
assertSource(strpos($lib, 'function notificar_lembrete(') !== false, 'A biblioteca deve expor notificar_lembrete().');
assertSource(strpos($lib, 'utec_whatsapp_lembrete_tipo_valido(') !== false, 'notificar_lembrete deve validar o tipo recebido.');
assertSource(strpos($lib, 'pr.telefone AS prestador_telefone') !== false, 'O contexto deve trazer o telefone do prestador.');
assertSource(strpos($lib, "'tipo_notificacao' => \$tipo") !== false, 'O log do lembrete deve carimbar o tipo.');
assertSource(strpos($lib, 'validar_quota_tenant(') !== false, 'O lembrete deve respeitar a cota do tenant.');
assertSource(strpos($lib, "log_message('warning'") === false, 'A biblioteca nao deve usar log_message(warning).');
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: FAIL — `A biblioteca deve expor notificar_lembrete().`

- [ ] **Step 3: Trazer o telefone do prestador no contexto**

Em `application/libraries/Whatsapp_agendamento.php`, no metodo `buscar_contexto_agendamento()`, na lista de colunas do `SELECT`, trocar a linha:

```php
                    pr.nome AS prestador_nome,
```

por:

```php
                    pr.nome AS prestador_nome, pr.telefone AS prestador_telefone,
```

- [ ] **Step 4: Implementar `notificar_lembrete()`**

Em `application/libraries/Whatsapp_agendamento.php`, adicionar o metodo abaixo logo apos `notificar_agendamento()`:

```php
    public function notificar_lembrete($id_agendamento, $tipo)
    {
        $id_agendamento = (int)$id_agendamento;
        $tipo = (string)$tipo;

        if ($id_agendamento <= 0 || !utec_whatsapp_lembrete_tipo_valido($tipo)) {
            $result = ['sent' => false, 'reason' => 'invalid_input', 'wamid' => '', 'error' => 'Agendamento ou tipo de lembrete invalido.'];
            log_message('error', '[whatsapp_lembrete] '.$result['error'].' agendamento='.$id_agendamento.' tipo='.$tipo);
            return $result;
        }

        $config = $this->CI->whatsapp_model->get_configuracao_ativa();
        if (!utec_whatsapp_config_ativa($config)) {
            $result = ['sent' => false, 'reason' => 'config_unavailable', 'wamid' => '', 'error' => 'Configuracao do WhatsApp ausente, incompleta ou inativa.'];
            log_message('error', '[whatsapp_lembrete] '.$result['error']);
            return $result;
        }

        $agendamento = $this->buscar_contexto_agendamento($id_agendamento);
        if (!$agendamento) {
            $result = ['sent' => false, 'reason' => 'agendamento_not_found', 'wamid' => '', 'error' => 'Agendamento nao encontrado.'];
            log_message('error', '[whatsapp_lembrete] '.$result['error'].' agendamento='.$id_agendamento);
            return $result;
        }

        $bruto = $tipo === 'lembrete_profissional'
            ? (isset($agendamento->prestador_telefone) ? $agendamento->prestador_telefone : '')
            : (isset($agendamento->paciente_telefone) ? $agendamento->paciente_telefone : '');
        $telefone = $this->normalizar_destino($bruto);
        if ($telefone === '') {
            $this->CI->whatsapp_model->registrar_log([
                'id_agendamento' => $id_agendamento,
                'tenant_id' => (int)$agendamento->tenant_id,
                'status_envio' => 'erro',
                'erro_detalhe' => 'Destino sem telefone valido para o lembrete.',
                'status_confirmacao' => 'nao_enviado',
                'tipo_notificacao' => $tipo,
            ]);
            $result = ['sent' => false, 'reason' => 'invalid_phone', 'wamid' => '', 'error' => 'Destino sem telefone valido.'];
            log_message('error', '[whatsapp_lembrete] '.$result['error'].' agendamento='.$id_agendamento.' tipo='.$tipo);
            return $result;
        }

        $quota = $this->validar_quota_tenant($agendamento, $telefone);
        if (!$quota['ok']) {
            $result = ['sent' => false, 'reason' => 'quota_reached', 'wamid' => '', 'error' => $quota['message']];
            log_message('error', '[whatsapp_lembrete] '.$result['error'].' agendamento='.$id_agendamento);
            return $result;
        }

        $payload = $this->montar_payload($config, $agendamento, $telefone);
        $response = $this->enviar_payload($config, $payload);

        $this->CI->whatsapp_model->registrar_log([
            'id_agendamento' => $id_agendamento,
            'tenant_id' => (int)$agendamento->tenant_id,
            'telefone_destino' => $telefone,
            'wamid' => $response['ok'] ? $response['wamid'] : '',
            'status_envio' => $response['ok'] ? 'enviado' : 'erro',
            'erro_detalhe' => $response['ok'] ? '' : $response['error'],
            'status_confirmacao' => $response['ok'] ? 'pendente' : 'nao_enviado',
            'tipo_notificacao' => $tipo,
        ]);

        $result = [
            'sent' => $response['ok'],
            'reason' => $response['ok'] ? 'sent' : 'api_error',
            'wamid' => $response['ok'] ? $response['wamid'] : '',
            'error' => $response['ok'] ? '' : $response['error'],
        ];
        log_message($response['ok'] ? 'info' : 'error', '[whatsapp_lembrete] '.($response['ok'] ? 'enviado' : 'falha').' agendamento='.$id_agendamento.' tipo='.$tipo.($response['ok'] ? '' : ' erro='.$result['error']));
        return $result;
    }
```

- [ ] **Step 5: Rodar o teste e ver passar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: `OK`

- [ ] **Step 6: Lint**

Run: `php -l application\libraries\Whatsapp_agendamento.php`
Expected: `No syntax errors detected in application\libraries\Whatsapp_agendamento.php`

- [ ] **Step 7: Rodar o teste do fluxo de agendamento (nao pode ter quebrado)**

Run: `php tests\whatsapp_agendamento_test.php`
Expected: `OK`

- [ ] **Step 8: Commit**

```bash
git add application/libraries/Whatsapp_agendamento.php tests/whatsapp_lembrete_source_test.php
git commit -m "feat: notificar_lembrete reaproveitando template e cota do agendamento"
```

---

## Task 5: Config do token, controller `Cron.php` e rota

**Files:**
- Create: `application/config/whatsapp.php`
- Create: `application/controllers/Cron.php`
- Modify: `application/config/routes.php`
- Modify: `tests/whatsapp_lembrete_source_test.php`

**Interfaces:**
- Consumes:
  - `utec_whatsapp_lembrete_intervalo($agora_ts)` (Task 1)
  - `Whatsapp_model::get_agendamentos_para_lembrete($tipo, $inicio, $fim)` (Task 3)
  - `Whatsapp_agendamento::notificar_lembrete($id, $tipo)` (Task 4)
  - `utec_whatsapp_config_ativa($config)` (helper existente)
- Produces:
  - Config `whatsapp.cron_token` — `getenv('WHATSAPP_CRON_TOKEN')` com fallback hardcoded.
  - Endpoint `GET /cron/lembrete-whatsapp?token=...` -> `Cron::lembrete_whatsapp()`. Token invalido/vazio: HTTP 403 `forbidden`. Config inativa: JSON `{ok:true, motivo:"config_inativa", ...zeros}`. Sucesso: JSON com `elegiveis_paciente`, `enviados_paciente`, `falhas_paciente` e os `_profissional`. Sempre 200 quando o token confere.

- [ ] **Step 1: Acrescentar os asserts de fonte que falham**

Em `tests/whatsapp_lembrete_source_test.php`, adicionar antes de `echo "OK\n";`:

```php
// --- Task 5: config, controller e rota ---
$cron = file_get_contents(__DIR__ . '/../application/controllers/Cron.php');
$routes = file_get_contents(__DIR__ . '/../application/config/routes.php');
$cfg = file_get_contents(__DIR__ . '/../application/config/whatsapp.php');

assertSource(strpos($cfg, "getenv('WHATSAPP_CRON_TOKEN')") !== false, 'A config deve ler o token de uma env var.');
assertSource(strpos($cfg, "\$config['cron_token']") !== false, 'A config deve definir cron_token.');

assertSource(strpos($cron, 'function lembrete_whatsapp(') !== false, 'O Cron deve expor lembrete_whatsapp().');
assertSource(strpos($cron, 'hash_equals(') !== false, 'O Cron deve comparar o token com hash_equals.');
assertSource(strpos($cron, 'set_status_header(403)') !== false, 'Token invalido deve responder 403.');
assertSource(strpos($cron, "\$this->input->get('token')") !== false, 'O token deve vir de input->get.');
assertSource(strpos($cron, "config->load('whatsapp'") !== false, 'O Cron deve carregar a config whatsapp.');
assertSource(strpos($cron, 'utec_whatsapp_config_ativa(') !== false, 'O Cron deve checar a config ativa antes de iterar.');
assertSource(strpos($cron, 'utec_whatsapp_lembrete_intervalo(') !== false, 'O Cron deve calcular a janela pelo helper.');
assertSource(strpos($cron, 'get_agendamentos_para_lembrete(') !== false, 'O Cron deve buscar os elegiveis pelo model.');
assertSource(strpos($cron, 'notificar_lembrete(') !== false, 'O Cron deve disparar via notificar_lembrete.');
assertSource(strpos($cron, "'lembrete_paciente'") !== false && strpos($cron, "'lembrete_profissional'") !== false, 'O Cron deve iterar os dois tipos.');
assertSource(strpos($cron, "log_message('warning'") === false, 'O Cron nao deve usar log_message(warning).');

assertSource(strpos($routes, "\$route['cron/lembrete-whatsapp']") !== false, 'routes.php deve ter a rota do cron.');
```

- [ ] **Step 2: Rodar e ver falhar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: FAIL — abre em `file_get_contents(...Cron.php)` (arquivo inexistente) ou no primeiro assert da config.

> Se o `file_get_contents` emitir warning e abortar o teste antes dos asserts, tudo bem: o passo seguinte cria os arquivos. O teste so precisa ficar verde no Step 6.

- [ ] **Step 3: Criar `application/config/whatsapp.php`**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$whatsapp_env_cron_token = getenv('WHATSAPP_CRON_TOKEN');

// Trocar o fallback por um token longo e aleatorio antes do deploy,
// ou definir a env var WHATSAPP_CRON_TOKEN no ambiente.
$config['cron_token'] = $whatsapp_env_cron_token ? $whatsapp_env_cron_token : 'TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY';
```

- [ ] **Step 4: Criar `application/controllers/Cron.php`**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp_agendamento');
        $this->load->model('Whatsapp_model', 'whatsapp_model');
        $this->config->load('whatsapp', TRUE);
    }

    public function lembrete_whatsapp()
    {
        $tokenEsperado = trim((string)$this->config->item('cron_token', 'whatsapp'));
        $tokenRecebido = trim((string)$this->input->get('token'));

        if ($tokenEsperado === '' || $tokenEsperado === 'TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY' || !hash_equals($tokenEsperado, $tokenRecebido)) {
            log_message('error', '[cron_lembrete_whatsapp] Token invalido ou nao configurado.');
            $this->output->set_status_header(403);
            echo 'forbidden';
            return;
        }

        $config = $this->whatsapp_model->get_configuracao_ativa();
        if (!utec_whatsapp_config_ativa($config)) {
            $this->responder_json([
                'ok' => true,
                'motivo' => 'config_inativa',
                'elegiveis_paciente' => 0, 'enviados_paciente' => 0, 'falhas_paciente' => 0,
                'elegiveis_profissional' => 0, 'enviados_profissional' => 0, 'falhas_profissional' => 0,
            ]);
            return;
        }

        $this->load->library('whatsapp_agendamento');
        $intervalo = utec_whatsapp_lembrete_intervalo(time());

        $resumo = ['ok' => true];
        foreach (['lembrete_paciente' => 'paciente', 'lembrete_profissional' => 'profissional'] as $tipo => $sufixo) {
            $ids = $this->whatsapp_model->get_agendamentos_para_lembrete($tipo, $intervalo['inicio'], $intervalo['fim']);
            $enviados = 0;
            $falhas = 0;
            foreach ($ids as $row) {
                $envio = $this->whatsapp_agendamento->notificar_lembrete((int)$row->id, $tipo);
                if (!empty($envio['sent'])) {
                    $enviados++;
                } else {
                    $falhas++;
                }
            }
            $resumo['elegiveis_'.$sufixo] = count($ids);
            $resumo['enviados_'.$sufixo] = $enviados;
            $resumo['falhas_'.$sufixo] = $falhas;
        }

        log_message('info', '[cron_lembrete_whatsapp] '.json_encode($resumo));
        $this->responder_json($resumo);
    }

    protected function responder_json($data, $status = 200)
    {
        $this->output->set_status_header((int)$status);
        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }
}
```

- [ ] **Step 5: Adicionar a rota**

Em `application/config/routes.php`, localizar a linha:

```php
$route['webhooks/whatsapp'] = 'webhooks/whatsapp';
```

e inserir logo abaixo:

```php
$route['cron/lembrete-whatsapp'] = 'cron/lembrete_whatsapp';
```

- [ ] **Step 6: Rodar o teste e ver passar**

Run: `php tests\whatsapp_lembrete_source_test.php`
Expected: `OK`

- [ ] **Step 7: Lint**

Run:
```powershell
php -l application\config\whatsapp.php
php -l application\controllers\Cron.php
php -l application\config\routes.php
```
Expected: `No syntax errors detected` nos tres.

- [ ] **Step 8: Commit**

```bash
git add application/config/whatsapp.php application/controllers/Cron.php application/config/routes.php tests/whatsapp_lembrete_source_test.php
git commit -m "feat: endpoint de cron para lembrete de consulta por whatsapp"
```

---

## Task 6: Verificacao integrada, smoke test e docs

**Files:**
- Modify: `CLAUDE.md`
- Verify: todos os arquivos das Tasks 1 a 5.

- [ ] **Step 1: Rodar toda a suite WhatsApp**

Run:
```powershell
php tests\whatsapp_lembrete_test.php
php tests\whatsapp_lembrete_source_test.php
php tests\whatsapp_agendamento_test.php
php tests\whatsapp_webhook_test.php
php tests\whatsapp_webhook_controller_test.php
php tests\notificacoes_usuarios_test.php
```
Expected: `OK` em todos.

- [ ] **Step 2: Lint em varredura**

Run:
```powershell
php -l application\helpers\whatsapp_agendamento_helper.php
php -l application\controllers\adm\Dev.php
php -l application\models\Whatsapp_model.php
php -l application\libraries\Whatsapp_agendamento.php
php -l application\config\whatsapp.php
php -l application\controllers\Cron.php
php -l application\config\routes.php
git diff --check
```
Expected: `No syntax errors detected` em cada `.php`; `git diff --check` sem saida.

- [ ] **Step 3: Smoke test manual local**

1. Aplicar a migracao: abrir `http://localhost/utec/adm/dev/migrar_lembrete_whatsapp` logado como nivel 1. Conferir os `✅` de coluna e indice; reabrir e conferir que vira `⚠️ ja existe` (idempotente).
2. No banco local, garantir uma linha `whatsapp_config` com `status = 1` e credenciais de teste (pode ser as ja usadas no fluxo de agendamento).
3. Criar um agendamento com `status = 0`, paciente com telefone valido, prestador com telefone valido, e `data_agenda`/`hora_agenda` para daqui a ~6h30.
4. Abrir `http://localhost/utec/cron/lembrete-whatsapp?token=TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY` — deve responder **403** (fallback e recusado de proposito).
5. Editar `application/config/whatsapp.php`, trocar o fallback por `teste-local-123`, e abrir `http://localhost/utec/cron/lembrete-whatsapp?token=teste-local-123`.
6. Conferir o JSON: `elegiveis_paciente` e `elegiveis_profissional` >= 1. Conferir em `whatsapp_notificacoes` duas linhas novas para o agendamento, com `tipo_notificacao` = `lembrete_paciente` e `lembrete_profissional`.
7. Abrir a mesma URL de novo: o JSON deve vir com `elegiveis_* = 0` (idempotencia) e nenhuma linha nova em `whatsapp_notificacoes`.
8. Reverter o fallback de `application/config/whatsapp.php` para `TROCAR_ESTE_TOKEN_LONGO_ANTES_DO_DEPLOY` antes de commitar.

- [ ] **Step 4: Atualizar `CLAUDE.md`**

Em `CLAUDE.md`, secao **10.3.1**, ao fim da lista de bullets, adicionar:

```markdown
- **Lembrete automatico (cron):** `GET /cron/lembrete-whatsapp?token=...` (`Cron::lembrete_whatsapp()`), agendado de hora em hora no cPanel, dispara um lembrete unico ao paciente e ao profissional quando faltam ate 7h para a consulta (`status = 0`), pulando quem ja recebeu o mesmo tipo e — para o paciente — quem ja confirmou. Token em `application/config/whatsapp.php` (`WHATSAPP_CRON_TOKEN`). Log em `whatsapp_notificacoes` com `tipo_notificacao` (`confirmacao` | `lembrete_paciente` | `lembrete_profissional`). MVP reusa o template de confirmacao; templates dedicados: `docs/whatsapp-lembrete-templates-pendente.md`. Migracao: `adm/dev/migrar_lembrete_whatsapp`.
```

Na secao **15.3 Backlog**, trocar a linha:

```markdown
- [ ] Lembretes automáticos de consulta via WhatsApp (D-1 / mesmo dia) — a confirmação no ato do agendamento já existe (10.3.1)
```

por:

```markdown
- [x] Lembrete automático de consulta via WhatsApp (cron horário, ~6h antes) — ver 10.3.1. Falta: templates dedicados e janelas D-1/manhã (`docs/whatsapp-lembrete-templates-pendente.md`)
```

Na secao **13**, na tabela de rotas do `Dev.php`, adicionar a linha:

```markdown
| `adm/dev/migrar_lembrete_whatsapp` | Adiciona `whatsapp_notificacoes.tipo_notificacao` + índice (idempotente) |
```

- [ ] **Step 5: Lint e commit**

Run: `php -l` nao se aplica a `.md`. Conferir `git status`.

```bash
git add CLAUDE.md
git commit -m "docs: registra o cron de lembrete de consulta por whatsapp"
```

- [ ] **Step 6: Resumo de publicacao (nao executar agora)**

Deixar registrado para o deploy (ja consta no spec, secao "Passo de publicacao"):
1. `adm/dev/migrar_lembrete_whatsapp` em producao.
2. Definir `WHATSAPP_CRON_TOKEN` no ambiente ou ajustar o fallback em `application/config/whatsapp.php`.
3. FTP: subir **primeiro** `application/helpers/whatsapp_agendamento_helper.php` (Model e Library chamam `utec_whatsapp_lembrete_tipo_valido()` — se o helper chegar depois, o primeiro cron/save quebra). Depois: `application/controllers/Cron.php`, `application/config/whatsapp.php`, `application/config/routes.php`, `application/libraries/Whatsapp_agendamento.php`, `application/models/Whatsapp_model.php`, `application/controllers/adm/Dev.php`.
4. Cron do cPanel, de hora em hora: `wget -q -O /dev/null "https://utecnologia.com.br/cron/lembrete-whatsapp?token=SEU_TOKEN"`.
5. Com o cron no ar, liberar a Task 5 do plano `docs/superpowers/plans/2026-08-31-seo-geo-whatsapp-confirmacao-consulta.md` (menu, rodape, sitemaps).
6. Depois: `docs/whatsapp-lembrete-templates-pendente.md` para os templates definitivos.

---

## Self-Review (feito na escrita)

**Cobertura do spec:**
- Endpoint `Cron.php` + rota + token de config → Task 5.
- Autenticacao por token com `hash_equals`, 403 → Task 5.
- Coluna `tipo_notificacao` + indice + migracao idempotente em `Dev.php` → Task 2.
- `registrar_log()` carimba o tipo → Task 2.
- Query de elegibilidade (janela ≤7h, `status = 0`, NOT EXISTS por tipo, NOT EXISTS confirmado so no paciente, `id_prestador > 0` no profissional) → Task 3.
- `notificar_lembrete()` reusando template atual, `validar_quota_tenant`, `montar_payload`, telefone do prestador no contexto → Task 4.
- Webhook inalterado (botoes com o mesmo payload) → nenhuma task, verificado por `php tests\whatsapp_webhook_test.php` na Task 6.
- Config inativa → JSON de zeros, sem erro → Task 5.
- Falhas parciais nao interrompem o loop, sempre 200 com token valido → Task 5.
- Testes puros (`utec_whatsapp_lembrete_intervalo`, `..._tipo_valido`) → Task 1.
- Smoke test local (criar agendamento +6h30, chamar URL, conferir linhas, repetir → sem duplicata) → Task 6.
- Passo de publicacao + gate da Task 5 do plano SEO → Task 6 Step 6.
- Fora de escopo (templates dedicados, colunas em `whatsapp_config`, tela `adm/whatsapp`, janelas D-1) → `docs/whatsapp-lembrete-templates-pendente.md`, nao entra em nenhuma task.

**Placeholder scan:** sem TBD/TODO; todo passo de codigo tem bloco completo.

**Consistencia de tipos:** `get_agendamentos_para_lembrete($tipo, $inicio, $fim)` retorna objetos com `->id`, consumidos como `(int)$row->id` na Task 5. `notificar_lembrete($id_agendamento, $tipo)` retorna `['sent','reason','wamid','error']`, consumido como `!empty($envio['sent'])` na Task 5. `utec_whatsapp_lembrete_intervalo()` retorna `['inicio','fim']`, consumido na Task 5. Nomes batem entre tasks.
