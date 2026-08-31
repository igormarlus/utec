# WhatsApp Trial Limit Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Limitar a 3 os envios de confirmacao de agendamento por WhatsApp para cada tenant sem assinatura ativa, mantendo envio ilimitado para tenants com assinatura `active`.

**Architecture:** O controle de quota fica centralizado na `Whatsapp_agendamento`, que resolve o `tenant_id` do agendamento, consulta a assinatura principal no SaaS e conta os envios aceitos pela Meta em `whatsapp_notificacoes`. O log existente continua sendo a fonte de verdade de auditoria, incluindo quando o envio e bloqueado por limite comercial.

**Tech Stack:** PHP 7, CodeIgniter 3, MySQL, WhatsApp Cloud API

---

### Task 1: Cobrir a politica de limite com testes CLI

**Files:**
- Modify: `tests/whatsapp_agendamento_test.php`
- Modify: `application/helpers/whatsapp_agendamento_helper.php`

- [ ] **Step 1: Write the failing test**

```php
$politicaTrial = utec_whatsapp_politica_limite('trial', 2);
assertSameValue(true, $politicaTrial['allowed'], 'Trial com 2 envios ainda deve poder enviar.');

$politicaTrialBloqueado = utec_whatsapp_politica_limite('trial', 3);
assertSameValue(false, $politicaTrialBloqueado['allowed'], 'Trial com 3 envios deve bloquear o 4o.');
assertSameValue('quota_reached', $politicaTrialBloqueado['reason'], 'Trial bloqueado deve retornar motivo de quota.');

$politicaActive = utec_whatsapp_politica_limite('active', 999);
assertSameValue(true, $politicaActive['allowed'], 'Assinatura ativa deve ter envio ilimitado.');
assertSameValue(0, $politicaActive['limit'], 'Assinatura ativa deve retornar limite zero como ilimitado.');

$politicaSemAssinatura = utec_whatsapp_politica_limite('', 3);
assertSameValue(false, $politicaSemAssinatura['allowed'], 'Tenant sem assinatura com 3 envios deve bloquear.');
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php tests/whatsapp_agendamento_test.php`
Expected: FAIL com `Call to undefined function utec_whatsapp_politica_limite()`

- [ ] **Step 3: Write minimal implementation**

```php
function utec_whatsapp_normalizar_status_assinatura($status) {
    return strtolower(trim((string)$status));
}

function utec_whatsapp_politica_limite($subscription_status, $used) {
    $status = utec_whatsapp_normalizar_status_assinatura($subscription_status);
    if ($status === 'active') {
        return ['allowed' => true, 'reason' => 'active_unlimited', 'limit' => 0, 'used' => (int)$used];
    }
    $limit = 3;
    return [
        'allowed' => ((int)$used < $limit),
        'reason' => ((int)$used < $limit) ? 'quota_available' : 'quota_reached',
        'limit' => $limit,
        'used' => (int)$used,
    ];
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php tests/whatsapp_agendamento_test.php`
Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add tests/whatsapp_agendamento_test.php application/helpers/whatsapp_agendamento_helper.php
git commit -m "test: cover whatsapp trial quota policy"
```

### Task 2: Expor consumo por tenant no model

**Files:**
- Modify: `application/models/Whatsapp_model.php`
- Read: `application/models/adm/Saas_model.php`

- [ ] **Step 1: Add the failing expectation mentally against current model**

Run: review `application/models/Whatsapp_model.php`
Expected: o model ainda nao possui metodos para contar envios por tenant nem para resumir a assinatura

- [ ] **Step 2: Implement tenant usage queries**

```php
public function contar_envios_enviados_por_tenant($tenant_id)
{
    $tenant_id = (int)$tenant_id;
    if ($tenant_id <= 0 || !$this->db->table_exists($this->log_table)) {
        return 0;
    }
    $qr = $this->db->query(
        "SELECT COUNT(id) AS total
         FROM {$this->log_table}
         WHERE tenant_id = {$tenant_id}
           AND status_envio = 'enviado'"
    );
    return $qr->num_rows() ? (int)$qr->row()->total : 0;
}

public function registrar_limite_atingido($tenant_id, $id_agendamento, $telefone_destino, $mensagem)
{
    return $this->registrar_log([
        'tenant_id' => (int)$tenant_id,
        'id_agendamento' => (int)$id_agendamento,
        'telefone_destino' => (string)$telefone_destino,
        'status_envio' => 'limite',
        'erro_detalhe' => $mensagem,
        'status_confirmacao' => 'nao_enviado',
    ]);
}
```

- [ ] **Step 3: Add subscription resolution helper in model or library contract**

```php
public function resumir_consumo_tenant($tenant_id, $subscription_status)
{
    $used = $this->contar_envios_enviados_por_tenant($tenant_id);
    return utec_whatsapp_politica_limite($subscription_status, $used);
}
```

- [ ] **Step 4: Run verification**

Run:
- `php -l application/models/Whatsapp_model.php`

Expected: sem erros de sintaxe

- [ ] **Step 5: Commit**

```bash
git add application/models/Whatsapp_model.php
git commit -m "feat: add whatsapp tenant usage queries"
```

### Task 3: Aplicar a politica comercial na library central

**Files:**
- Modify: `application/libraries/Whatsapp_agendamento.php`
- Modify: `application/helpers/whatsapp_agendamento_helper.php`
- Read: `application/models/adm/Saas_model.php`

- [ ] **Step 1: Write the behavior target**

```php
// Dentro de notificar_agendamento():
// 1. buscar contexto do agendamento
// 2. resolver status da assinatura principal do tenant
// 3. contar envios aceitos pela Meta
// 4. bloquear quando used >= 3 para tenants nao active
```

- [ ] **Step 2: Implement subscription-aware quota methods**

```php
protected function get_subscription_status_by_tenant($tenant_id)
{
    $tenant_id = (int)$tenant_id;
    if ($tenant_id <= 0) {
        return '';
    }
    $this->CI->load->model('adm/Saas_model', 'saas_model');
    $subscription = $this->CI->saas_model->get_tenant_primary_subscription($tenant_id);
    return $subscription && isset($subscription->status) ? strtolower(trim((string)$subscription->status)) : '';
}

protected function validar_quota_tenant($agendamento, $telefone)
{
    $tenant_id = isset($agendamento->tenant_id) ? (int)$agendamento->tenant_id : 0;
    $status = $this->get_subscription_status_by_tenant($tenant_id);
    $policy = $this->CI->whatsapp_model->resumir_consumo_tenant($tenant_id, $status);
    if ($policy['allowed']) {
        return ['ok' => true, 'policy' => $policy];
    }
    $msg = 'Limite de 3 envios do plano trial/free atingido. Contrate um plano para liberar novos disparos.';
    $this->CI->whatsapp_model->registrar_limite_atingido($tenant_id, (int)$agendamento->id, $telefone, $msg);
    return ['ok' => false, 'policy' => $policy, 'message' => $msg];
}
```

- [ ] **Step 3: Block only the WhatsApp send, never the appointment save**

```php
$quota = $this->validar_quota_tenant($agendamento, $telefone);
if (!$quota['ok']) {
    $result = [
        'sent' => false,
        'reason' => 'quota_reached',
        'error' => $quota['message'],
    ];
    log_message('error', '[whatsapp_agendamento] '.utec_whatsapp_resumo_envio($result)['message']);
    return $result;
}
```

- [ ] **Step 4: Update user-facing summary for quota reached**

```php
case 'quota_reached':
    return [
        'type' => 'warning',
        'message' => 'Limite de 3 envios do plano trial/free atingido. Contrate um plano para liberar novos disparos.',
    ];
```

- [ ] **Step 5: Run verification**

Run:
- `php tests/whatsapp_agendamento_test.php`
- `php -l application/helpers/whatsapp_agendamento_helper.php`
- `php -l application/libraries/Whatsapp_agendamento.php`

Expected: `OK` e sem erros de sintaxe

- [ ] **Step 6: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php application/libraries/Whatsapp_agendamento.php
git commit -m "feat: enforce whatsapp trial quota by tenant"
```

### Task 4: Refletir o bloqueio nos fluxos de atendimento e calendario

**Files:**
- Read: `application/controllers/adm/Atendimento.php`
- Read: `application/controllers/adm/Calendario.php`
- Read: `application/views/adm/usuarios/new/prontuario.php`
- Read: `application/views/adm/calendario/index.php`

- [ ] **Step 1: Confirm controllers already pass through the centralized result**

Run: review `application/controllers/adm/Atendimento.php` and `application/controllers/adm/Calendario.php`
Expected: ambos ja usam `utec_whatsapp_resumo_envio()` como saida para UI

- [ ] **Step 2: Adjust copy only if needed**

```php
// Nao duplicar regra de negocio nos controllers.
// Garantir apenas que o resumo de quota_reached apareca igual aos outros motivos.
```

- [ ] **Step 3: Run verification**

Run:
- `php -l application/controllers/adm/Atendimento.php`
- `php -l application/controllers/adm/Calendario.php`
- `php -l application/views/adm/usuarios/new/prontuario.php`
- `php -l application/views/adm/calendario/index.php`

Expected: sem erros de sintaxe

- [ ] **Step 4: Commit**

```bash
git add application/controllers/adm/Atendimento.php application/controllers/adm/Calendario.php application/views/adm/usuarios/new/prontuario.php application/views/adm/calendario/index.php
git commit -m "chore: surface whatsapp quota feedback in scheduling flows"
```

### Task 5: Verificacao final da entrega

**Files:**
- Read: `application/models/Whatsapp_model.php`
- Read: `application/libraries/Whatsapp_agendamento.php`
- Read: `tests/whatsapp_agendamento_test.php`

- [ ] **Step 1: Run full verification**

Run:
- `php tests/usuarios_relatorio_helper_test.php`
- `php tests/whatsapp_agendamento_test.php`
- `php -l application/helpers/usuarios_relatorio_helper.php`
- `php -l application/helpers/whatsapp_agendamento_helper.php`
- `php -l application/models/Whatsapp_model.php`
- `php -l application/libraries/Whatsapp_agendamento.php`
- `php -l application/controllers/adm/Atendimento.php`
- `php -l application/controllers/adm/Calendario.php`
- `php -l application/views/adm/usuarios/new/prontuario.php`
- `php -l application/views/adm/calendario/index.php`

Expected: testes `OK` e todos os arquivos sem erro de sintaxe

- [ ] **Step 2: Runtime checklist**

Checklist:
- `whatsapp_notificacoes` existe no banco
- registros com `status_envio = 'enviado'` possuem `tenant_id`
- tenant `trial` bloqueia no 4o envio
- tenant `active` segue enviando apos o 3o

- [ ] **Step 3: Commit**

```bash
git add docs/superpowers/plans/2026-08-31-whatsapp-limite-trial.md
git commit -m "docs: add whatsapp trial quota plan"
```
