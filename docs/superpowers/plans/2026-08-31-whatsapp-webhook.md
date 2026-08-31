# WhatsApp Webhook Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Criar o webhook público do WhatsApp para validar a URL na Meta e processar os botões de confirmar e cancelar de mensagens de agendamento.

**Architecture:** O endpoint público ficará em `webhooks/whatsapp`, roteado para um controller dedicado `Webhooks`. O controller delega persistência ao `Whatsapp_model`, que localiza a configuração ativa, resolve a notificação por `wamid` ou `id_agendamento`, atualiza `whatsapp_notificacoes` e, no cancelamento, também atualiza `agendamentos.status = 3`. A pasta raiz `webhooks/whatsapp/` servirá para documentação e log técnico simples.

**Tech Stack:** PHP 7, CodeIgniter 3, MySQL, WhatsApp Cloud API

---

### Task 1: Cobrir o parsing básico do webhook com teste CLI

**Files:**
- Create: `tests/whatsapp_webhook_test.php`
- Modify: `application/helpers/whatsapp_agendamento_helper.php`

- [ ] **Step 1: Write the failing test**

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

$payload = [
    'entry' => [[
        'changes' => [[
            'value' => [
                'messages' => [[
                    'context' => ['id' => 'wamid.HBgMTESTE123'],
                    'interactive' => [
                        'button_reply' => [
                            'id' => 'cancelar_agendamento:492',
                            'title' => 'Cancelar',
                        ],
                    ],
                ]],
            ],
        ]],
    ]],
];

$evento = utec_whatsapp_extrair_evento_webhook($payload);
assertSameValue('cancelar', $evento['action'], 'Deve identificar acao cancelar.');
assertSameValue(492, $evento['id_agendamento'], 'Deve identificar o id do agendamento.');
assertSameValue('wamid.HBgMTESTE123', $evento['wamid'], 'Deve identificar o wamid de contexto.');
assertSameValue('cancelar_agendamento:492', $evento['payload'], 'Deve manter o payload original.');

$desconhecido = utec_whatsapp_extrair_evento_webhook(['entry' => []]);
assertSameValue('', $desconhecido['action'], 'Payload sem mensagem interativa deve voltar vazio.');

echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php tests/whatsapp_webhook_test.php`
Expected: FAIL com `Call to undefined function utec_whatsapp_extrair_evento_webhook()`

- [ ] **Step 3: Write minimal implementation**

```php
function utec_whatsapp_extrair_evento_webhook($payload)
{
    $messages = isset($payload['entry'][0]['changes'][0]['value']['messages'][0])
        ? $payload['entry'][0]['changes'][0]['value']['messages'][0]
        : [];

    $buttonId = isset($messages['interactive']['button_reply']['id'])
        ? trim((string)$messages['interactive']['button_reply']['id'])
        : '';

    $wamid = isset($messages['context']['id'])
        ? trim((string)$messages['context']['id'])
        : '';

    $action = '';
    $idAgendamento = 0;

    if (preg_match('/^(confirmar|cancelar)_agendamento:(\d+)$/', $buttonId, $matches)) {
        $action = $matches[1];
        $idAgendamento = (int)$matches[2];
    }

    return [
        'action' => $action,
        'id_agendamento' => $idAgendamento,
        'wamid' => $wamid,
        'payload' => $buttonId,
    ];
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php tests/whatsapp_webhook_test.php`
Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add tests/whatsapp_webhook_test.php application/helpers/whatsapp_agendamento_helper.php
git commit -m "test: cover whatsapp webhook payload parsing"
```

### Task 2: Expor operacoes de webhook no Whatsapp_model

**Files:**
- Modify: `application/models/Whatsapp_model.php`

- [ ] **Step 1: Add the failing expectation mentally against current model**

Run: review `application/models/Whatsapp_model.php`
Expected: o model ainda nao possui busca por `wamid` nem atualizacao de confirmacao do webhook

- [ ] **Step 2: Implement lookup and update helpers**

```php
public function get_notificacao_por_wamid($wamid)
{
    $wamid = trim((string)$wamid);
    if ($wamid === '' || !$this->db->table_exists($this->log_table)) {
        return null;
    }
    $qr = $this->db->query(
        "SELECT * FROM `{$this->log_table}` WHERE wamid = ".$this->db->escape($wamid)." ORDER BY id DESC LIMIT 1"
    );
    return $qr->num_rows() ? $qr->row() : null;
}

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

public function atualizar_confirmacao_notificacao($id, $status_confirmacao)
{
    $id = (int)$id;
    $status_confirmacao = trim((string)$status_confirmacao);
    if ($id <= 0 || $status_confirmacao === '' || !$this->db->table_exists($this->log_table)) {
        return false;
    }
    $this->db->where('id', $id);
    return $this->db->update($this->log_table, $this->filtrar_colunas($this->log_table, [
        'status_confirmacao' => $status_confirmacao,
        'respondido_em' => date('Y-m-d H:i:s'),
    ]));
}

public function cancelar_agendamento_por_webhook($id_agendamento)
{
    $id_agendamento = (int)$id_agendamento;
    if ($id_agendamento <= 0 || !$this->db->table_exists('agendamentos')) {
        return false;
    }
    $this->db->where('id', $id_agendamento);
    return $this->db->update('agendamentos', ['status' => 3]);
}
```

- [ ] **Step 3: Add a small resolver method**

```php
public function resolver_notificacao_webhook($wamid, $id_agendamento)
{
    $notificacao = $this->get_notificacao_por_wamid($wamid);
    if ($notificacao) {
        return $notificacao;
    }
    return $this->get_notificacao_por_agendamento($id_agendamento);
}
```

- [ ] **Step 4: Run verification**

Run:
- `php -l application/models/Whatsapp_model.php`

Expected: sem erros de sintaxe

- [ ] **Step 5: Commit**

```bash
git add application/models/Whatsapp_model.php
git commit -m "feat: add whatsapp webhook persistence helpers"
```

### Task 3: Criar controller publico e rota do webhook

**Files:**
- Create: `application/controllers/Webhooks.php`
- Modify: `application/config/routes.php`
- Modify: `application/models/Whatsapp_model.php`

- [ ] **Step 1: Write the behavior target**

```php
// GET /webhooks/whatsapp
// valida verify_token e responde hub.challenge
//
// POST /webhooks/whatsapp
// processa payload interativo e responde JSON simples
```

- [ ] **Step 2: Implement the route**

```php
$route['webhooks/whatsapp'] = 'webhooks/whatsapp';
```

- [ ] **Step 3: Implement the controller**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'whatsapp_agendamento'));
        $this->load->model('Whatsapp_model', 'whatsapp_model');
    }

    public function whatsapp()
    {
        if (strtoupper($this->input->method()) === 'GET') {
            return $this->validar_whatsapp();
        }

        return $this->receber_whatsapp();
    }

    protected function validar_whatsapp()
    {
        $config = $this->whatsapp_model->get_configuracao_ativa();
        $challenge = $this->input->get('hub_challenge', true);
        if ($challenge === null || $challenge === '') {
            $challenge = isset($_GET['hub_challenge']) ? $_GET['hub_challenge'] : (isset($_GET['hub.challenge']) ? $_GET['hub.challenge'] : '');
        }
        $verifyToken = isset($_GET['hub_verify_token']) ? $_GET['hub_verify_token'] : (isset($_GET['hub.verify_token']) ? $_GET['hub.verify_token'] : '');
        $storedToken = trim((string)utec_whatsapp_read($config, 'verify_token', ''));

        if ($storedToken === '' || $verifyToken !== $storedToken) {
            log_message('error', '[whatsapp_webhook] verify_token invalido ou ausente.');
            $this->output->set_status_header(403);
            echo 'forbidden';
            return;
        }

        $this->output->set_content_type('text/plain');
        echo $challenge;
    }

    protected function receber_whatsapp()
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode((string)$raw, true);
        if (!is_array($payload)) {
            log_message('error', '[whatsapp_webhook] payload invalido.');
            return $this->respond_json(['ok' => false, 'message' => 'payload invalido'], 400);
        }

        $this->registrar_payload_bruto($raw);
        $evento = utec_whatsapp_extrair_evento_webhook($payload);

        if ($evento['action'] === '') {
            log_message('debug', '[whatsapp_webhook] evento recebido sem acao tratavel.');
            return $this->respond_json(['ok' => true, 'message' => 'evento recebido']);
        }

        $notificacao = $this->whatsapp_model->resolver_notificacao_webhook($evento['wamid'], $evento['id_agendamento']);
        if (!$notificacao) {
            log_message('warning', '[whatsapp_webhook] notificacao nao localizada para o evento recebido.');
            return $this->respond_json(['ok' => true, 'message' => 'notificacao nao encontrada']);
        }

        $statusConfirmacao = $evento['action'] === 'confirmar' ? 'confirmado' : 'cancelado';
        $this->whatsapp_model->atualizar_confirmacao_notificacao((int)$notificacao->id, $statusConfirmacao);

        if ($evento['action'] === 'cancelar') {
            $this->whatsapp_model->cancelar_agendamento_por_webhook((int)$notificacao->id_agendamento);
        }

        return $this->respond_json([
            'ok' => true,
            'action' => $evento['action'],
            'id_agendamento' => (int)$notificacao->id_agendamento,
        ]);
    }

    protected function registrar_payload_bruto($raw)
    {
        $dir = FCPATH.'webhooks/whatsapp';
        if (is_dir($dir) && is_writable($dir)) {
            @file_put_contents($dir.'/ultimo-payload.json', (string)$raw);
        }
        log_message('debug', '[whatsapp_webhook] payload recebido: '.substr((string)$raw, 0, 1500));
    }

    protected function respond_json($data, $status = 200)
    {
        $this->output->set_status_header((int)$status);
        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }
}
```

- [ ] **Step 4: Run verification**

Run:
- `php -l application/controllers/Webhooks.php`
- `php -l application/config/routes.php`
- `php -l application/models/Whatsapp_model.php`

Expected: sem erros de sintaxe

- [ ] **Step 5: Commit**

```bash
git add application/controllers/Webhooks.php application/config/routes.php application/models/Whatsapp_model.php
git commit -m "feat: add public whatsapp webhook endpoint"
```

### Task 4: Criar a pasta raiz do webhook e documentacao operacional

**Files:**
- Create: `webhooks/whatsapp/README.md`
- Create: `webhooks/whatsapp/.gitkeep`

- [ ] **Step 1: Add the root folder files**

```md
# WhatsApp Webhook

Esta pasta documenta o endpoint publico `https://utecnologia.com.br/webhooks/whatsapp`.

Uso:
- cadastrar essa URL na Meta
- usar o mesmo valor salvo em `adm/whatsapp -> Verify Token`
- verificar `ultimo-payload.json` apenas como apoio tecnico se o servidor permitir escrita
```

- [ ] **Step 2: Run verification**

Run:
- `Get-ChildItem webhooks\\whatsapp`

Expected: listar `README.md` e `.gitkeep`

- [ ] **Step 3: Commit**

```bash
git add webhooks/whatsapp/README.md webhooks/whatsapp/.gitkeep
git commit -m "docs: add whatsapp webhook root folder"
```

### Task 5: Verificacao final e publicacao

**Files:**
- Read: `application/controllers/Webhooks.php`
- Read: `application/models/Whatsapp_model.php`
- Read: `application/helpers/whatsapp_agendamento_helper.php`
- Read: `tests/whatsapp_webhook_test.php`
- Read: `application/config/routes.php`

- [ ] **Step 1: Run full verification**

Run:
- `php tests/whatsapp_agendamento_test.php`
- `php tests/whatsapp_webhook_test.php`
- `php -l application/helpers/whatsapp_agendamento_helper.php`
- `php -l application/models/Whatsapp_model.php`
- `php -l application/controllers/Webhooks.php`
- `php -l application/config/routes.php`

Expected: testes `OK` e sem erros de sintaxe

- [ ] **Step 2: Runtime checklist**

Checklist:
- `adm/whatsapp` possui `verify_token` preenchido
- URL final para Meta: `https://utecnologia.com.br/webhooks/whatsapp`
- GET com token correto devolve `hub.challenge`
- POST com payload de `confirmar_agendamento:{id}` marca `status_confirmacao = confirmado`
- POST com payload de `cancelar_agendamento:{id}` marca `status_confirmacao = cancelado` e `agendamentos.status = 3`

- [ ] **Step 3: Publish changed files by FTP**

Arquivos previstos para upload:
- `application/helpers/whatsapp_agendamento_helper.php`
- `application/models/Whatsapp_model.php`
- `application/controllers/Webhooks.php`
- `application/config/routes.php`
- `tests/whatsapp_webhook_test.php`
- `webhooks/whatsapp/README.md`
- `webhooks/whatsapp/.gitkeep`

- [ ] **Step 4: Commit**

```bash
git add docs/superpowers/plans/2026-08-31-whatsapp-webhook.md
git commit -m "docs: add whatsapp webhook implementation plan"
```
