# Notificação WhatsApp à Equipe (Confirmação/Cancelamento) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Quando o paciente confirma ou cancela a consulta pelo botão do WhatsApp, avisar também o profissional e quem cadastrou o agendamento (deduplicado) por WhatsApp, usando os dois templates já aprovados na Meta (`agendamento_confirmado_equipe`, `agendamento_cancelado_equipe`).

**Architecture:** Novo método `Whatsapp_agendamento::notificar_equipe()` chamado de `Webhooks::processar_resposta_agendamento()` logo após os avisos internos já existentes. Reaproveita `whatsapp_notificacoes.tipo_notificacao` (sem migração nova) e a cota trial. Corrige de passagem as 3 consultas "última linha por agendamento" para não confundir o status de equipe com o status real de resposta do paciente.

**Tech Stack:** PHP 7, CodeIgniter 3.1.10, MySQL/MariaDB, Meta WhatsApp Cloud API.

**Spec:** `docs/superpowers/specs/2026-09-22-whatsapp-notificacao-equipe-design.md`

## Global Constraints

- Não modificar `system/` (core CI3). Não migrar para CI4 ou outro framework.
- `log_message()` só aceita `debug`, `info`, `error` neste projeto — nunca `warning`.
- Nenhuma migração de banco nesta entrega — reaproveita `whatsapp_notificacoes.tipo_notificacao`, coluna já existente.
- Falha em `notificar_equipe()` nunca desfaz a confirmação/cancelamento já persistido, os avisos internos, nem bloqueia a resposta de texto ao paciente.
- Textos enviados ao WhatsApp e mensagens de log seguem o padrão do projeto: sem acentos (ver `utec_whatsapp_texto_resposta_agendamento()` como referência).
- Templates já aprovados na Meta em 2026-09-22: `agendamento_confirmado_equipe`, `agendamento_cancelado_equipe` — 3 variáveis de corpo cada (paciente, profissional, data+hora combinada), sem cabeçalho, sem botão.
- Flag `notificar_equipe_ativo` sai **ligado por padrão** (decisão do usuário) — só desliga com `WHATSAPP_NOTIFICAR_EQUIPE=0`.

---

## Mapa de arquivos

| Arquivo | Ação | Responsabilidade |
|---------|------|-------------------|
| `application/helpers/whatsapp_agendamento_helper.php` | Modificar | `utec_whatsapp_template_equipe_nome()`, `utec_whatsapp_componentes_equipe_template()` |
| `tests/whatsapp_notificar_equipe_test.php` | Criar | Testes puros das 2 funções acima |
| `application/models/Whatsapp_model.php` | Modificar | Corrige `get_notificacao_por_agendamento()`, `listar_agendamentos_chatbot()`, `obter_agendamento_chatbot()` para excluir `tipo_notificacao IN ('equipe_confirmado','equipe_cancelado')` da "última linha" |
| `application/controllers/adm/Atendimento.php` | Modificar | Mesma correção na subquery de `whatsapp_status` da agenda |
| `application/config/whatsapp.php` | Modificar | Flag `notificar_equipe_ativo` |
| `application/libraries/Whatsapp_agendamento.php` | Modificar | `buscar_contexto_agendamento()` ganha `cadastrado_por_telefone`; `montar_payload()` aceita nome de template e componentes; novo método `notificar_equipe()` |
| `application/controllers/Webhooks.php` | Modificar | Chama `notificar_equipe()` em `processar_resposta_agendamento()` |
| `tests/whatsapp_notificar_equipe_source_test.php` | Criar | Asserções de fonte (model, biblioteca, webhook, config) |

**Convenções de teste deste projeto:** arquivos PHP diretos em `tests/`, sem framework. Rodar com `php tests/<arquivo>.php`. Dois estilos: puro (`define('BASEPATH', __DIR__)` + `require` do arquivo alvo + `assertSameValue`) para funções sem I/O, e "source assertion" (`file_get_contents` + `strpos`) para código que depende de banco/HTTP, seguindo exatamente `tests/whatsapp_lembrete_test.php` e `tests/whatsapp_lembrete_source_test.php` como referência.

---

## Task 1: Funções puras — seleção de template e montagem do corpo

**Files:**
- Modify: `application/helpers/whatsapp_agendamento_helper.php`
- Create: `tests/whatsapp_notificar_equipe_test.php`

**Interfaces:**
- Produces: `utec_whatsapp_template_equipe_nome(string $acao): string` — `'confirmar'` → `'agendamento_confirmado_equipe'`, `'cancelar'` → `'agendamento_cancelado_equipe'`, qualquer outro valor → `''`.
- Produces: `utec_whatsapp_componentes_equipe_template($contexto): array` — array de componentes Meta com só o bloco `body` (3 parâmetros: `paciente_nome`, `prestador_nome`, data+hora combinada), sem `header` nem `button`.

- [ ] **Step 1: Escrever o teste que falha**

Criar `tests/whatsapp_notificar_equipe_test.php`:

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

// --- utec_whatsapp_template_equipe_nome ---
assertSameValue('agendamento_confirmado_equipe', utec_whatsapp_template_equipe_nome('confirmar'), 'Confirmar deve mapear para o template de confirmado.');
assertSameValue('agendamento_cancelado_equipe', utec_whatsapp_template_equipe_nome('cancelar'), 'Cancelar deve mapear para o template de cancelado.');
assertSameValue('agendamento_confirmado_equipe', utec_whatsapp_template_equipe_nome('CONFIRMAR'), 'A comparacao deve ignorar caixa.');
assertSameValue('', utec_whatsapp_template_equipe_nome('outra_coisa'), 'Acao desconhecida nao deve mapear para template nenhum.');
assertSameValue('', utec_whatsapp_template_equipe_nome(''), 'String vazia nao deve mapear para template nenhum.');

// --- utec_whatsapp_componentes_equipe_template ---
$contexto = [
    'paciente_nome' => 'Maria Silva',
    'prestador_nome' => 'Dr. Joao Pereira',
    'data_agenda' => '2026-09-25',
    'hora_agenda' => '14:30:00',
];
$componentes = utec_whatsapp_componentes_equipe_template($contexto);

assertSameValue(1, count($componentes), 'So deve existir o componente body (sem header/button).');
assertSameValue('body', $componentes[0]['type'], 'O unico componente deve ser do tipo body.');
assertSameValue(3, count($componentes[0]['parameters']), 'O corpo deve ter exatamente 3 parametros.');
assertSameValue('Maria Silva', $componentes[0]['parameters'][0]['text'], 'Parametro 1 e o nome do paciente.');
assertSameValue('Dr. Joao Pereira', $componentes[0]['parameters'][1]['text'], 'Parametro 2 e o nome do profissional.');
assertSameValue('25/09/2026 as 14:30', $componentes[0]['parameters'][2]['text'], 'Parametro 3 combina data e hora formatadas em pt-BR.');

// --- valores ausentes usam fallback, sem notice/erro ---
$componentesVazio = utec_whatsapp_componentes_equipe_template([]);
assertSameValue('Paciente', $componentesVazio[0]['parameters'][0]['text'], 'Sem nome do paciente, usa o fallback Paciente.');
assertSameValue('Profissional', $componentesVazio[0]['parameters'][1]['text'], 'Sem nome do profissional, usa o fallback Profissional.');

echo "OK\n";
```

- [ ] **Step 2: Rodar o teste e confirmar que falha**

Run: `php tests/whatsapp_notificar_equipe_test.php`
Expected: erro fatal — `utec_whatsapp_template_equipe_nome()` e `utec_whatsapp_componentes_equipe_template()` ainda não existem.

- [ ] **Step 3: Implementar as duas funções**

Abrir `application/helpers/whatsapp_agendamento_helper.php`. Localizar o fim do arquivo (função `utec_whatsapp_lembrete_intervalo`, por volta da linha 807-819) e acrescentar depois dela:

```php
if (!function_exists('utec_whatsapp_template_equipe_nome')) {
    function utec_whatsapp_template_equipe_nome($acao)
    {
        $acao = strtolower(trim((string)$acao));

        if ($acao === 'confirmar') {
            return 'agendamento_confirmado_equipe';
        }
        if ($acao === 'cancelar') {
            return 'agendamento_cancelado_equipe';
        }

        return '';
    }
}

if (!function_exists('utec_whatsapp_componentes_equipe_template')) {
    function utec_whatsapp_componentes_equipe_template($contexto)
    {
        $dataBr = utec_whatsapp_formatar_data_br(utec_whatsapp_read($contexto, 'data_agenda', ''));
        $horaBr = utec_whatsapp_formatar_hora_br(utec_whatsapp_read($contexto, 'hora_agenda', ''));
        $dataHora = trim($dataBr . ' as ' . $horaBr, ' as ');
        if ($dataBr !== '' && $horaBr !== '') {
            $dataHora = $dataBr . ' as ' . $horaBr;
        }

        return [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'paciente_nome', 'Paciente'))],
                    ['type' => 'text', 'text' => trim((string)utec_whatsapp_read($contexto, 'prestador_nome', 'Profissional'))],
                    ['type' => 'text', 'text' => $dataHora],
                ],
            ],
        ];
    }
}
```

- [ ] **Step 4: Rodar o teste e confirmar que passa**

Run: `php tests/whatsapp_notificar_equipe_test.php`
Expected: `OK`

- [ ] **Step 5: Lint**

Run: `php -l application/helpers/whatsapp_agendamento_helper.php`
Expected: `No syntax errors detected in application/helpers/whatsapp_agendamento_helper.php`

- [ ] **Step 6: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php tests/whatsapp_notificar_equipe_test.php
git commit -m "feat: funcoes puras de template para notificacao whatsapp a equipe"
```

---

## Task 2: Corrigir a "última linha" para não misturar tipo de notificação

**Files:**
- Modify: `application/models/Whatsapp_model.php:151-163` (`get_notificacao_por_agendamento`)
- Modify: `application/models/Whatsapp_model.php:609-617` (`listar_agendamentos_chatbot`)
- Modify: `application/models/Whatsapp_model.php:632-639` (`obter_agendamento_chatbot`)
- Modify: `application/controllers/adm/Atendimento.php:66-80`

**Contexto do bug:** essas 4 consultas pegam a linha de `whatsapp_notificacoes` com maior `id` (ou `MAX(id)`) para um agendamento, sem filtrar por `tipo_notificacao`, para mostrar a etiqueta "Confirmado/Cancelado via WhatsApp" na agenda, no prontuário e no chatbot. A Task 4 vai gravar linhas novas com `tipo_notificacao IN ('equipe_confirmado', 'equipe_cancelado')` logo depois de cada resposta do paciente — sem essa correção, essas linhas novas viram a "última linha" e a etiqueta passa a mostrar o status da notificação de equipe (que nunca é `confirmado`/`cancelado`) em vez do status real da resposta do paciente.

**Interfaces:**
- Consumes: nenhuma função nova de tasks anteriores.
- Produces: comportamento inalterado para instalações sem a coluna `tipo_notificacao` (guarda por `field_exists`, igual ao resto do arquivo); comportamento corrigido quando a coluna existe.

- [ ] **Step 1: Corrigir `get_notificacao_por_agendamento()`**

Em `application/models/Whatsapp_model.php`, localizar:

```php
    public function get_notificacao_por_agendamento($id_agendamento)
    {
        $id_agendamento = (int)$id_agendamento;
        if ($id_agendamento <= 0 || !$this->db->table_exists($this->log_table)) {
            return null;
        }

        $qr = $this->db->query(
            "SELECT * FROM `{$this->log_table}` WHERE id_agendamento = {$id_agendamento} ORDER BY id DESC LIMIT 1"
        );

        return $qr->num_rows() ? $qr->row() : null;
    }
```

Substituir por:

```php
    public function get_notificacao_por_agendamento($id_agendamento)
    {
        $id_agendamento = (int)$id_agendamento;
        if ($id_agendamento <= 0 || !$this->db->table_exists($this->log_table)) {
            return null;
        }

        $filtroTipo = $this->db->field_exists('tipo_notificacao', $this->log_table)
            ? " AND tipo_notificacao NOT IN ('equipe_confirmado', 'equipe_cancelado')"
            : '';

        $qr = $this->db->query(
            "SELECT * FROM `{$this->log_table}` WHERE id_agendamento = {$id_agendamento}{$filtroTipo} ORDER BY id DESC LIMIT 1"
        );

        return $qr->num_rows() ? $qr->row() : null;
    }
```

- [ ] **Step 2: Corrigir `listar_agendamentos_chatbot()` e `obter_agendamento_chatbot()`**

Nas duas funções, localizar (aparece duas vezes, uma em cada função):

```php
        $statusWhatsapp = "'' AS status_whatsapp";
        if ($this->tabela_possui_campos($this->log_table, ['id', 'id_agendamento', 'status_envio', 'status_confirmacao'])) {
            $statusWhatsapp = "COALESCE((SELECT CONCAT_WS('/', wn.status_envio, wn.status_confirmacao) FROM `{$this->log_table}` wn WHERE wn.id_agendamento = a.id ORDER BY wn.id DESC LIMIT 1), '') AS status_whatsapp";
        }
```

Substituir as duas ocorrências por:

```php
        $statusWhatsapp = "'' AS status_whatsapp";
        if ($this->tabela_possui_campos($this->log_table, ['id', 'id_agendamento', 'status_envio', 'status_confirmacao'])) {
            $filtroTipoChatbot = $this->db->field_exists('tipo_notificacao', $this->log_table)
                ? " AND wn.tipo_notificacao NOT IN ('equipe_confirmado', 'equipe_cancelado')"
                : '';
            $statusWhatsapp = "COALESCE((SELECT CONCAT_WS('/', wn.status_envio, wn.status_confirmacao) FROM `{$this->log_table}` wn WHERE wn.id_agendamento = a.id{$filtroTipoChatbot} ORDER BY wn.id DESC LIMIT 1), '') AS status_whatsapp";
        }
```

- [ ] **Step 3: Corrigir a subquery da agenda em `Atendimento.php`**

Em `application/controllers/adm/Atendimento.php`, localizar:

```php
	$whatsapp_select = "";
	$whatsapp_join = "";
	if($this->db->table_exists('whatsapp_notificacoes')){
		$whatsapp_select = ", wr.status_confirmacao AS whatsapp_status, wr.respondido_em AS whatsapp_respondido_em";
		$whatsapp_join = "
		LEFT JOIN (
			SELECT n.id_agendamento, n.status_confirmacao, n.respondido_em
			FROM whatsapp_notificacoes n
			INNER JOIN (
				SELECT id_agendamento, MAX(id) AS max_id
				FROM whatsapp_notificacoes
				GROUP BY id_agendamento
			) nm ON nm.max_id = n.id
		) wr ON wr.id_agendamento = a.id ";
	}
```

Substituir por:

```php
	$whatsapp_select = "";
	$whatsapp_join = "";
	if($this->db->table_exists('whatsapp_notificacoes')){
		$filtro_tipo_equipe = $this->db->field_exists('tipo_notificacao', 'whatsapp_notificacoes')
			? " WHERE tipo_notificacao NOT IN ('equipe_confirmado', 'equipe_cancelado')"
			: "";
		$whatsapp_select = ", wr.status_confirmacao AS whatsapp_status, wr.respondido_em AS whatsapp_respondido_em";
		$whatsapp_join = "
		LEFT JOIN (
			SELECT n.id_agendamento, n.status_confirmacao, n.respondido_em
			FROM whatsapp_notificacoes n
			INNER JOIN (
				SELECT id_agendamento, MAX(id) AS max_id
				FROM whatsapp_notificacoes
				".$filtro_tipo_equipe."
				GROUP BY id_agendamento
			) nm ON nm.max_id = n.id
		) wr ON wr.id_agendamento = a.id ";
	}
```

- [ ] **Step 4: Lint**

Run:
```bash
php -l application/models/Whatsapp_model.php
php -l application/controllers/adm/Atendimento.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 5: Commit**

```bash
git add application/models/Whatsapp_model.php application/controllers/adm/Atendimento.php
git commit -m "fix: excluir notificacao de equipe da ultima resposta whatsapp exibida"
```

---

## Task 3: `notificar_equipe()` na biblioteca + flag de ativação

**Files:**
- Modify: `application/config/whatsapp.php`
- Modify: `application/libraries/Whatsapp_agendamento.php`
- Create: `tests/whatsapp_notificar_equipe_source_test.php`

**Interfaces:**
- Consumes: `utec_whatsapp_template_equipe_nome()`, `utec_whatsapp_componentes_equipe_template()` (Task 1); `utec_notificacoes_destinatarios_agendamento($id_criador, $id_prestador): array` (já existe em `whatsapp_agendamento_helper.php:500`).
- Produces: `Whatsapp_agendamento::notificar_equipe(array $contexto, string $acao): array` — retorna `['enviados' => int, 'falhas' => int, 'detalhes' => array]`. `$contexto` é o mesmo array devolvido por `Whatsapp_model::registrar_resposta_webhook()['contexto']` (chaves: `id_agendamento`, `id_paciente`, `paciente_nome`, `id_user`, `id_prestador`, `tenant_id`, `telefone_destino`, `id_whatsapp_notificacao`).

- [ ] **Step 1: Adicionar o flag em `application/config/whatsapp.php`**

Abrir o arquivo (conteúdo atual completo):

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$whatsapp_env_cron_token = getenv('WHATSAPP_CRON_TOKEN');

// Fallback usado quando a env var WHATSAPP_CRON_TOKEN nao esta definida no ambiente.
$config['cron_token'] = $whatsapp_env_cron_token ? $whatsapp_env_cron_token : 'notwa10230901marlusti';

$whatsapp_env_lembrete_prof = getenv('WHATSAPP_LEMBRETE_PROFISSIONAL');
// Lane do lembrete ao profissional. Manter FALSE ate o template dedicado
// 'lembrete_consulta_profissional' (sem botoes) ser aprovado na Meta.
$config['lembrete_profissional_ativo'] = ($whatsapp_env_lembrete_prof === '1' || $whatsapp_env_lembrete_prof === 'true');
```

Acrescentar ao final:

```php

$whatsapp_env_notificar_equipe = getenv('WHATSAPP_NOTIFICAR_EQUIPE');
// Notificacao WhatsApp a profissional/atendente apos o paciente confirmar ou
// cancelar. Liga por padrao; desligar so com WHATSAPP_NOTIFICAR_EQUIPE=0 (ou
// =false) no ambiente.
$config['notificar_equipe_ativo'] = !($whatsapp_env_notificar_equipe === '0' || $whatsapp_env_notificar_equipe === 'false');
```

- [ ] **Step 2: Adicionar `cadastrado_por_telefone` em `buscar_contexto_agendamento()`**

Em `application/libraries/Whatsapp_agendamento.php`, localizar (por volta da linha 316-328):

```php
        $qr = $this->CI->db->query(
            "SELECT a.id, a.data_agenda, a.hora_agenda, a.tipo,
                    p.nome AS paciente_nome, p.telefone AS paciente_telefone,
                    pr.nome AS prestador_nome, pr.telefone AS prestador_telefone,
                    cad.nome AS cadastrado_por_nome,
                    {$tenantSelect}
             FROM agendamentos a
             LEFT JOIN usuarios p ON p.id = a.id_paciente
             LEFT JOIN usuarios pr ON pr.id = a.id_prestador
             LEFT JOIN usuarios cad ON cad.id = a.id_user
             WHERE a.id = ".(int)$id_agendamento."
             LIMIT 1"
        );
```

Substituir por:

```php
        $qr = $this->CI->db->query(
            "SELECT a.id, a.data_agenda, a.hora_agenda, a.tipo,
                    p.nome AS paciente_nome, p.telefone AS paciente_telefone,
                    pr.nome AS prestador_nome, pr.telefone AS prestador_telefone,
                    cad.nome AS cadastrado_por_nome, cad.telefone AS cadastrado_por_telefone,
                    {$tenantSelect}
             FROM agendamentos a
             LEFT JOIN usuarios p ON p.id = a.id_paciente
             LEFT JOIN usuarios pr ON pr.id = a.id_prestador
             LEFT JOIN usuarios cad ON cad.id = a.id_user
             WHERE a.id = ".(int)$id_agendamento."
             LIMIT 1"
        );
```

- [ ] **Step 3: Generalizar `montar_payload()` para aceitar template e componentes**

Localizar:

```php
    protected function montar_payload($config, $agendamento, $telefone)
    {
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $telefone,
            'type' => 'template',
            'template' => [
                'name' => trim((string)$config->template_name),
                'language' => [
                    'code' => trim((string)$config->template_lang),
                ],
                'components' => utec_whatsapp_componentes_template($agendamento, $config),
            ],
        ];
    }
```

Substituir por:

```php
    protected function montar_payload($config, $agendamento, $telefone, $templateNome = null, $componentes = null)
    {
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $telefone,
            'type' => 'template',
            'template' => [
                'name' => $templateNome !== null ? trim((string)$templateNome) : trim((string)$config->template_name),
                'language' => [
                    'code' => trim((string)$config->template_lang),
                ],
                'components' => $componentes !== null ? $componentes : utec_whatsapp_componentes_template($agendamento, $config),
            ],
        ];
    }
```

Isso não muda o comportamento de `notificar_agendamento()` e `notificar_lembrete()` (continuam chamando `montar_payload($config, $agendamento, $telefone)` sem os 2 novos parâmetros, que ficam com o valor padrão `null`).

- [ ] **Step 4: Adicionar o método `notificar_equipe()`**

Logo após o método `responder_interacao()` (termina na linha ~230, antes de `enviar_chatbot()`), inserir:

```php
    public function notificar_equipe($contexto, $acao)
    {
        $resumo = ['enviados' => 0, 'falhas' => 0, 'detalhes' => []];

        if (!$this->CI->config->item('notificar_equipe_ativo', 'whatsapp')) {
            return $resumo;
        }

        $templateNome = utec_whatsapp_template_equipe_nome($acao);
        if ($templateNome === '') {
            return $resumo;
        }

        $config = $this->CI->whatsapp_model->get_configuracao_ativa();
        if (!utec_whatsapp_config_ativa($config)) {
            return $resumo;
        }

        $idAgendamento = (int)utec_whatsapp_read($contexto, 'id_agendamento', 0);
        $agendamento = $this->buscar_contexto_agendamento($idAgendamento);
        if (!$agendamento) {
            return $resumo;
        }

        $idPrestador = (int)utec_whatsapp_read($contexto, 'id_prestador', 0);
        $idCriador = (int)utec_whatsapp_read($contexto, 'id_user', 0);
        $destinatarios = utec_notificacoes_destinatarios_agendamento($idCriador, $idPrestador);
        $tipoNotificacao = $acao === 'cancelar' ? 'equipe_cancelado' : 'equipe_confirmado';
        $componentes = utec_whatsapp_componentes_equipe_template($agendamento);

        foreach ($destinatarios as $idUsuario) {
            $ehPrestador = ($idUsuario === $idPrestador);
            $papel = $ehPrestador ? 'profissional' : 'atendente';
            $telefoneBruto = $ehPrestador
                ? (isset($agendamento->prestador_telefone) ? $agendamento->prestador_telefone : '')
                : (isset($agendamento->cadastrado_por_telefone) ? $agendamento->cadastrado_por_telefone : '');
            $telefone = $this->normalizar_destino($telefoneBruto);

            if ($telefone === '') {
                $this->CI->whatsapp_model->registrar_log([
                    'id_agendamento' => $idAgendamento,
                    'tenant_id' => (int)$agendamento->tenant_id,
                    'status_envio' => 'erro',
                    'erro_detalhe' => 'Destino sem telefone valido para notificacao de equipe.',
                    'status_confirmacao' => 'nao_aplicavel',
                    'tipo_notificacao' => $tipoNotificacao,
                ]);
                $resumo['falhas']++;
                $resumo['detalhes'][] = ['papel' => $papel, 'sent' => false, 'error' => 'invalid_phone'];
                log_message('error', '[whatsapp_equipe] Telefone invalido. agendamento='.$idAgendamento.' papel='.$papel);
                continue;
            }

            $quota = $this->validar_quota_tenant($agendamento, $telefone, $tipoNotificacao);
            if (!$quota['ok']) {
                $resumo['falhas']++;
                $resumo['detalhes'][] = ['papel' => $papel, 'sent' => false, 'error' => 'quota_reached'];
                log_message('error', '[whatsapp_equipe] '.$quota['message'].' agendamento='.$idAgendamento.' papel='.$papel);
                continue;
            }

            $payload = $this->montar_payload($config, $agendamento, $telefone, $templateNome, $componentes);
            $response = $this->enviar_payload($config, $payload);

            $this->CI->whatsapp_model->registrar_log([
                'id_agendamento' => $idAgendamento,
                'tenant_id' => (int)$agendamento->tenant_id,
                'telefone_destino' => $telefone,
                'wamid' => $response['ok'] ? $response['wamid'] : '',
                'status_envio' => $response['ok'] ? 'enviado' : 'erro',
                'erro_detalhe' => $response['ok'] ? '' : $response['error'],
                'status_confirmacao' => 'nao_aplicavel',
                'tipo_notificacao' => $tipoNotificacao,
            ]);

            if ($response['ok']) {
                $resumo['enviados']++;
            } else {
                $resumo['falhas']++;
            }
            $resumo['detalhes'][] = [
                'papel' => $papel,
                'sent' => (bool)$response['ok'],
                'wamid' => $response['ok'] ? $response['wamid'] : '',
                'error' => $response['ok'] ? '' : $response['error'],
            ];
            log_message(
                $response['ok'] ? 'info' : 'error',
                '[whatsapp_equipe] '.($response['ok'] ? 'enviado' : 'falha').' agendamento='.$idAgendamento.' papel='.$papel
                    .($response['ok'] ? '' : ' erro='.$response['error'])
            );
        }

        return $resumo;
    }
```

- [ ] **Step 5: Escrever as asserções de fonte**

Criar `tests/whatsapp_notificar_equipe_source_test.php`:

```php
<?php

function assertSource($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$config = file_get_contents(__DIR__ . '/../application/config/whatsapp.php');
$lib = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_agendamento.php');

// --- Task 3: config e biblioteca ---
assertSource(strpos($config, "getenv('WHATSAPP_NOTIFICAR_EQUIPE')") !== false, 'config/whatsapp.php deve ler WHATSAPP_NOTIFICAR_EQUIPE.');
assertSource(strpos($config, "\$config['notificar_equipe_ativo'] = !(") !== false, 'O flag notificar_equipe_ativo deve sair ligado por padrao (negacao do desligamento explicito).');

assertSource(strpos($lib, 'function notificar_equipe(') !== false, 'A biblioteca deve expor notificar_equipe().');
assertSource(strpos($lib, "notificar_equipe_ativo'") !== false, 'notificar_equipe deve checar o flag de ativacao.');
assertSource(strpos($lib, 'utec_whatsapp_template_equipe_nome(') !== false, 'notificar_equipe deve resolver o nome do template pela acao.');
assertSource(strpos($lib, 'cad.telefone AS cadastrado_por_telefone') !== false, 'O contexto deve trazer o telefone de quem cadastrou.');
assertSource(strpos($lib, 'utec_notificacoes_destinatarios_agendamento(') !== false, 'notificar_equipe deve reaproveitar a resolucao de destinatarios dos avisos internos.');
assertSource(strpos($lib, "'equipe_cancelado'") !== false && strpos($lib, "'equipe_confirmado'") !== false, 'notificar_equipe deve gravar os dois tipos de notificacao de equipe.');
assertSource(strpos($lib, "validar_quota_tenant(\$agendamento, \$telefone, \$tipoNotificacao)") !== false, 'notificar_equipe deve respeitar a cota do tenant.');
assertSource(strpos($lib, "log_message('warning'") === false, 'A biblioteca nao deve usar log_message(warning).');

echo "OK\n";
```

- [ ] **Step 6: Rodar o teste de fonte**

Run: `php tests/whatsapp_notificar_equipe_source_test.php`
Expected: `OK`

- [ ] **Step 7: Lint**

Run:
```bash
php -l application/config/whatsapp.php
php -l application/libraries/Whatsapp_agendamento.php
```
Expected: `No syntax errors detected` nos dois.

- [ ] **Step 8: Commit**

```bash
git add application/config/whatsapp.php application/libraries/Whatsapp_agendamento.php tests/whatsapp_notificar_equipe_source_test.php
git commit -m "feat: notificar_equipe reaproveitando template, contexto e cota do agendamento"
```

---

## Task 4: Ligar no webhook, documentar e preparar deploy

**Files:**
- Modify: `application/controllers/Webhooks.php:125-170`
- Modify: `tests/whatsapp_notificar_equipe_source_test.php`
- Modify: `CLAUDE.md` (seção 10.3.1)

**Interfaces:**
- Consumes: `Whatsapp_agendamento::notificar_equipe(array $contexto, string $acao): array` (Task 3).

- [ ] **Step 1: Chamar `notificar_equipe()` no webhook**

Em `application/controllers/Webhooks.php`, localizar (dentro de `processar_resposta_agendamento`):

```php
        // Avisos internos para quem marcou e para o profissional. Falha aqui nao afeta a resposta ao paciente.
        $this->load->model('Notificacoes_model', 'notificacoes_model');
        if ($this->notificacoes_model->criar_resposta_agendamento($contexto, $acao) === false) {
            log_message('error', '[whatsapp_webhook] Falha ao registrar avisos internos. id='.(int)$notificacao->id);
        }

        // Resposta de texto ao paciente. Nunca faz rollback da confirmacao/cancelamento ja aplicados.
        $telefone = isset($contexto['telefone_destino']) ? $contexto['telefone_destino'] : '';
        $this->load->library('whatsapp_agendamento');
        $envio = $this->whatsapp_agendamento->responder_interacao($telefone, $acao);
        if (!empty($envio['sent'])) {
            log_message('info', '[whatsapp_webhook] Resposta ao paciente enviada. id='.(int)$notificacao->id.' wamid='.(string)$envio['wamid']);
        } else {
            log_message('error', '[whatsapp_webhook] Falha ao responder paciente. id='.(int)$notificacao->id.' erro='.(string)$envio['error']);
        }
    }
```

Substituir por:

```php
        // Avisos internos para quem marcou e para o profissional. Falha aqui nao afeta a resposta ao paciente.
        $this->load->model('Notificacoes_model', 'notificacoes_model');
        if ($this->notificacoes_model->criar_resposta_agendamento($contexto, $acao) === false) {
            log_message('error', '[whatsapp_webhook] Falha ao registrar avisos internos. id='.(int)$notificacao->id);
        }

        // Notificacao WhatsApp para profissional e atendente. Falha aqui nao afeta o
        // paciente nem os avisos internos ja registrados.
        $this->load->library('whatsapp_agendamento');
        $envioEquipe = $this->whatsapp_agendamento->notificar_equipe($contexto, $acao);
        log_message(
            'info',
            '[whatsapp_webhook] Notificacao a equipe. id='.(int)$notificacao->id
                .' enviados='.(int)$envioEquipe['enviados'].' falhas='.(int)$envioEquipe['falhas']
        );

        // Resposta de texto ao paciente. Nunca faz rollback da confirmacao/cancelamento ja aplicados.
        $telefone = isset($contexto['telefone_destino']) ? $contexto['telefone_destino'] : '';
        $envio = $this->whatsapp_agendamento->responder_interacao($telefone, $acao);
        if (!empty($envio['sent'])) {
            log_message('info', '[whatsapp_webhook] Resposta ao paciente enviada. id='.(int)$notificacao->id.' wamid='.(string)$envio['wamid']);
        } else {
            log_message('error', '[whatsapp_webhook] Falha ao responder paciente. id='.(int)$notificacao->id.' erro='.(string)$envio['error']);
        }
    }
```

Note que `$this->load->library('whatsapp_agendamento')` foi movido para antes do bloco de equipe (a biblioteca já cobre as duas chamadas seguintes; a linha antiga logo antes de `responder_interacao` foi removida por ficar duplicada).

- [ ] **Step 2: Acrescentar a asserção de fonte do webhook**

Em `tests/whatsapp_notificar_equipe_source_test.php`, adicionar ao final (antes do `echo "OK\n";`):

```php
$webhook = file_get_contents(__DIR__ . '/../application/controllers/Webhooks.php');

// --- Task 4: integracao no webhook ---
assertSource(strpos($webhook, 'notificar_equipe(') !== false, 'Webhooks.php deve chamar notificar_equipe apos os avisos internos.');
assertSource(strpos($webhook, "log_message('warning'") === false, 'Webhooks.php nao deve usar log_message(warning).');
```

- [ ] **Step 3: Rodar o teste de fonte e o lint**

Run:
```bash
php tests/whatsapp_notificar_equipe_source_test.php
php -l application/controllers/Webhooks.php
```
Expected: `OK` e `No syntax errors detected in application/controllers/Webhooks.php`

- [ ] **Step 4: Rodar toda a suíte de testes WhatsApp para checar regressão**

Run:
```bash
php tests/whatsapp_notificar_equipe_test.php
php tests/whatsapp_notificar_equipe_source_test.php
php tests/whatsapp_lembrete_test.php
php tests/whatsapp_lembrete_source_test.php
php tests/whatsapp_agendamento_test.php
php tests/whatsapp_webhook_test.php
php tests/whatsapp_webhook_controller_test.php
php tests/whatsapp_webhook_bridge_test.php
php tests/notificacoes_usuarios_test.php
```
Expected: `OK` em todos.

- [ ] **Step 5: Atualizar `CLAUDE.md` (seção 10.3.1)**

Localizar o bloco `- **Lembrete automático (cron):** ...` seguido de `- **Status de deploy:**` e acrescentar, logo após a linha do lembrete e antes de `- **Status de deploy:**`, um novo bullet:

```markdown
- **Notificação WhatsApp à equipe (confirmação/cancelamento):** ao paciente confirmar ou cancelar pelo botão, `Whatsapp_agendamento::notificar_equipe()` avisa por WhatsApp o profissional e quem cadastrou o agendamento (deduplicado quando é a mesma pessoa), usando os templates informativos `agendamento_confirmado_equipe` / `agendamento_cancelado_equipe` (sem botão). Chamado de `Webhooks::processar_resposta_agendamento()`, logo após os avisos internos. Ligado por padrão (`whatsapp.notificar_equipe_ativo`, env `WHATSAPP_NOTIFICAR_EQUIPE=0` desliga). Conta contra o limite de 3 disparos do trial. Log em `whatsapp_notificacoes` com `tipo_notificacao = equipe_confirmado` / `equipe_cancelado` — essas linhas são excluídas das consultas de "última resposta" (agenda, prontuário, chatbot) para não mascarar o status real do paciente.
```

E adicionar ao final da lista de "Status de deploy" (mantendo as entradas anteriores):

```markdown
  - **2026-09-22** — notificação WhatsApp à equipe: templates `agendamento_confirmado_equipe` / `agendamento_cancelado_equipe` aprovados na Meta; código implementado, pendente de envio por FTP e smoke test em produção.
```

- [ ] **Step 6: Commit**

```bash
git add application/controllers/Webhooks.php tests/whatsapp_notificar_equipe_source_test.php CLAUDE.md
git commit -m "feat: liga notificacao whatsapp a equipe no webhook de resposta"
```

---

## Self-Review

**Cobertura da spec:**
- [x] Templates aprovados documentados com as 3 variáveis — Task 1, Task 3 Step 4
- [x] Flag ligado por padrão, desliga só com `WHATSAPP_NOTIFICAR_EQUIPE=0` — Task 3 Step 1
- [x] Destinatários = profissional + quem cadastrou, deduplicados — Task 3 Step 4 (reaproveita `utec_notificacoes_destinatarios_agendamento`)
- [x] Cota trial conta os 2 envios — Task 3 Step 4 (`validar_quota_tenant`)
- [x] Isolamento de falha (não desfaz confirmação/cancelamento nem bloqueia paciente) — Task 3 Step 4, Task 4 Step 1
- [x] `tipo_notificacao` novo sem migração — reaproveita coluna existente em todas as tasks
- [x] Achado fora da spec original, mas causado por ela (bug da "última linha" do `whatsapp_notificacoes`) — Task 2, adicionada antes da Task 3 para já proteger a UI antes do primeiro envio real
- [x] Testes puros + fonte + lint — Tasks 1, 3, 4
- [x] Documentação (CLAUDE.md) — Task 4 Step 5

**Varredura de placeholder:** nenhum "TBD"/"TODO"; todo bloco de código é o conteúdo final.

**Consistência de tipos:** `notificar_equipe(array $contexto, string $acao): array` é usado com a mesma assinatura em Task 3 (definição) e Task 4 (chamada no webhook). `utec_whatsapp_template_equipe_nome()` e `utec_whatsapp_componentes_equipe_template()` (Task 1) são consumidos com os mesmos nomes em Task 3. `montar_payload()` ganha 2 parâmetros opcionais (Task 3 Step 3) sem quebrar as 2 chamadas existentes (`notificar_agendamento`, `notificar_lembrete`), que continuam passando só 3 argumentos.
