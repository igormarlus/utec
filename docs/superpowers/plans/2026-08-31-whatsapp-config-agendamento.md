# WhatsApp Config Agendamento Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Adicionar configuracao administravel do WhatsApp no painel admin e permitir disparo opcional de template aprovado ao criar agendamentos.

**Architecture:** A configuracao ativa fica centralizada em uma tabela propria lida por um model dedicado. O disparo e o log de notificacoes ficam encapsulados em uma library para reaproveitar a mesma regra nos fluxos de `Atendimento` e `Calendario`, sem bloquear o salvamento do agendamento em caso de falha externa.

**Tech Stack:** PHP 7, CodeIgniter 3, MySQL, Bootstrap 4, jQuery

---

### Task 1: Cobrir a nova regra com testes CLI

**Files:**
- Create: `tests/whatsapp_agendamento_test.php`
- Read: `application/helpers/usuarios_relatorio_helper.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

$configAtiva = ['status' => 1, 'phone_number_id' => '123', 'access_token' => 'abc', 'template_name' => 'confirmacao', 'template_lang' => 'pt_BR'];
$configInativa = ['status' => 0, 'phone_number_id' => '123', 'access_token' => 'abc', 'template_name' => 'confirmacao', 'template_lang' => 'pt_BR'];
$configParcial = ['status' => 1, 'phone_number_id' => '', 'access_token' => 'abc', 'template_name' => 'confirmacao', 'template_lang' => 'pt_BR'];

assertSameValue(true, utec_whatsapp_config_ativa($configAtiva), 'Configuracao completa ativa deve ser utilizavel');
assertSameValue(false, utec_whatsapp_config_ativa($configInativa), 'Configuracao inativa nao pode disparar');
assertSameValue(false, utec_whatsapp_config_ativa($configParcial), 'Configuracao incompleta nao pode disparar');
assertSameValue(true, utec_whatsapp_checkbox_marcado(['enviar_whatsapp_confirmacao' => '1']), 'Checkbox marcado deve retornar true');
assertSameValue(false, utec_whatsapp_checkbox_marcado([]), 'Checkbox ausente deve retornar false');
echo "OK\n";
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php tests/whatsapp_agendamento_test.php`
Expected: FAIL com erro de include ou funcao indefinida para o helper novo

- [ ] **Step 3: Write minimal implementation**

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('utec_whatsapp_read')){
    function utec_whatsapp_read($source, $key, $default = ''){
        if(is_array($source)){
            return isset($source[$key]) ? $source[$key] : $default;
        }
        if(is_object($source)){
            return isset($source->$key) ? $source->$key : $default;
        }
        return $default;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php tests/whatsapp_agendamento_test.php`
Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add tests/whatsapp_agendamento_test.php application/helpers/whatsapp_agendamento_helper.php
git commit -m "test: cover whatsapp appointment config helpers"
```

### Task 2: Centralizar configuracao, log e disparo no backend

**Files:**
- Create: `application/helpers/whatsapp_agendamento_helper.php`
- Create: `application/models/Whatsapp_model.php`
- Create: `application/libraries/Whatsapp_agendamento.php`
- Modify: `application/controllers/adm/Atendimento.php`
- Modify: `application/controllers/adm/Calendario.php`

- [ ] **Step 1: Expand helper and model contracts**

```php
// helper
function utec_whatsapp_config_ativa($config) { /* valida status e campos obrigatorios */ }
function utec_whatsapp_checkbox_marcado($post) { /* normaliza checkbox */ }
function utec_whatsapp_normalizar_numero($numero) { /* apenas digitos */ }

// model
public function get_configuracao_ativa() { /* SELECT * FROM whatsapp_config WHERE status = 1 ORDER BY id DESC LIMIT 1 */ }
public function salvar_configuracao($data) { /* upsert simples da linha ativa */ }
public function registrar_log($data) { /* insert em whatsapp_notificacoes se tabela existir */ }
```

- [ ] **Step 2: Add failing integration behavior check mentally against both controllers**

Run: review `application/controllers/adm/Atendimento.php` and `application/controllers/adm/Calendario.php`
Expected: ambos salvam agendamento sem qualquer chamada centralizada de WhatsApp

- [ ] **Step 3: Implement reusable library**

```php
public function notificar_agendamento($id_agendamento, $enviar = true) {
    if(!$enviar){ return ['sent' => false, 'reason' => 'disabled']; }
    $config = $this->CI->whatsapp_model->get_configuracao_ativa();
    if(!utec_whatsapp_config_ativa($config)){ return ['sent' => false, 'reason' => 'config_unavailable']; }
    // buscar agendamento + paciente + tenant
    // montar payload do template
    // tentar POST na Graph API
    // registrar sucesso/erro em whatsapp_notificacoes
}
```

- [ ] **Step 4: Wire controllers after successful insert**

```php
$insert_ok = $this->db->insert('agendamentos', $dd);
$agendamento_id = (int)$this->db->insert_id();
$enviar_whatsapp = utec_whatsapp_checkbox_marcado($this->input->post(NULL, true));
$this->whatsapp_agendamento->notificar_agendamento($agendamento_id, $enviar_whatsapp);
```

- [ ] **Step 5: Run verification**

Run:
- `php -l application/helpers/whatsapp_agendamento_helper.php`
- `php -l application/models/Whatsapp_model.php`
- `php -l application/libraries/Whatsapp_agendamento.php`
- `php -l application/controllers/adm/Atendimento.php`
- `php -l application/controllers/adm/Calendario.php`

Expected: sem erros de sintaxe

- [ ] **Step 6: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php application/models/Whatsapp_model.php application/libraries/Whatsapp_agendamento.php application/controllers/adm/Atendimento.php application/controllers/adm/Calendario.php
git commit -m "feat: add whatsapp scheduling backend"
```

### Task 3: Adicionar tela admin e menu restrito ao nivel 1

**Files:**
- Create: `application/controllers/adm/Whatsapp.php`
- Create: `application/views/adm/whatsapp/index.php`
- Modify: `includes/adm/menu.php`

- [ ] **Step 1: Write the controller contract**

```php
class Whatsapp extends CI_Controller {
    public function index() { /* carrega config ativa e view */ }
    public function salvar() { /* valida nivel 1, persiste e redireciona com flashdata */ }
}
```

- [ ] **Step 2: Implement form view**

```php
// campos: nome_conexao, numero_remetente, phone_number_id, waba_id, access_token,
// app_secret, verify_token, template_name, template_lang, status
// mostrar aviso sobre template aprovado na Meta
```

- [ ] **Step 3: Add admin-only menu item**

```php
[
    'label' => 'WhatsApp',
    'icon' => 'os-icon-mail-14',
    'url' => base_url().'adm/whatsapp',
    'children' => [
        ['label' => 'Configuracoes', 'url' => base_url().'adm/whatsapp'],
    ],
],
```

- [ ] **Step 4: Run verification**

Run:
- `php -l application/controllers/adm/Whatsapp.php`
- `php -l application/views/adm/whatsapp/index.php`
- `php -l includes/adm/menu.php`

Expected: sem erros de sintaxe

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Whatsapp.php application/views/adm/whatsapp/index.php includes/adm/menu.php
git commit -m "feat: add admin whatsapp settings screen"
```

### Task 4: Atualizar os dois formularios de agendamento

**Files:**
- Modify: `application/views/adm/atendimento/atendimento.php`
- Modify: `application/views/adm/calendario/index.php`

- [ ] **Step 1: Add default checked checkbox in patient scheduling form**

```php
<div class="custom-control custom-checkbox" style="margin-top:18px;">
  <input type="checkbox" class="custom-control-input" id="enviar-whatsapp-confirmacao" name="enviar_whatsapp_confirmacao" value="1" checked>
  <label class="custom-control-label" for="enviar-whatsapp-confirmacao">Enviar confirmacao pelo WhatsApp</label>
</div>
```

- [ ] **Step 2: Add matching checkbox in calendar modal**

```php
<div class="custom-control custom-checkbox" style="margin-top:10px;">
  <input type="checkbox" class="custom-control-input" id="criar-enviar-whatsapp" name="enviar_whatsapp_confirmacao" value="1" checked>
  <label class="custom-control-label" for="criar-enviar-whatsapp">Enviar confirmacao pelo WhatsApp</label>
</div>
```

- [ ] **Step 3: Surface config-unavailable hint when needed**

```php
<?php if(empty($whatsapp_disponivel)): ?>
  <div class="alert alert-warning">Configuracao do WhatsApp ainda incompleta. O agendamento sera salvo sem disparo.</div>
<?php endif; ?>
```

- [ ] **Step 4: Run verification**

Run:
- `php -l application/views/adm/atendimento/atendimento.php`
- `php -l application/views/adm/calendario/index.php`

Expected: sem erros de sintaxe

- [ ] **Step 5: Commit**

```bash
git add application/views/adm/atendimento/atendimento.php application/views/adm/calendario/index.php
git commit -m "feat: add whatsapp toggle to scheduling forms"
```

### Task 5: Verificacao final da entrega

**Files:**
- Read: `tests/whatsapp_agendamento_test.php`
- Read: `application/controllers/adm/Atendimento.php`
- Read: `application/controllers/adm/Calendario.php`
- Read: `application/controllers/adm/Whatsapp.php`

- [ ] **Step 1: Run test suite and syntax checks**

Run:
- `php tests/usuarios_relatorio_helper_test.php`
- `php tests/whatsapp_agendamento_test.php`
- `php -l application/helpers/usuarios_relatorio_helper.php`
- `php -l application/helpers/whatsapp_agendamento_helper.php`
- `php -l application/models/Whatsapp_model.php`
- `php -l application/libraries/Whatsapp_agendamento.php`
- `php -l application/controllers/adm/Usuarios.php`
- `php -l application/controllers/adm/Atendimento.php`
- `php -l application/controllers/adm/Calendario.php`
- `php -l application/controllers/adm/Whatsapp.php`
- `php -l application/views/adm/usuarios/new/lista.php`
- `php -l application/views/adm/atendimento/atendimento.php`
- `php -l application/views/adm/calendario/index.php`
- `php -l includes/adm/menu.php`
- `php -l includes/adm/paciente/menu.php`

Expected: testes `OK` e todos os arquivos sem erro de sintaxe

- [ ] **Step 2: Review runtime assumptions**

Checklist:
- `whatsapp_config` ja existe no banco
- se `whatsapp_notificacoes` ainda nao existir, library nao quebra o agendamento
- nenhum fluxo de agendamento foi bloqueado por falha externa

- [ ] **Step 3: Commit**

```bash
git add docs/superpowers/plans/2026-08-31-whatsapp-config-agendamento.md
git commit -m "docs: add whatsapp scheduling implementation plan"
```
