# WhatsApp Chatbot Selecao de Perfil Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permitir que telefones associados a mais de um perfil escolham conscientemente o menu de paciente, profissional, atendente ou administrador.

**Architecture:** O modelo passa a resolver todos os perfis distintos de um telefone e pode selecionar um perfil solicitado, sempre usando o usuario mais recente dentro daquele perfil. A biblioteca do chatbot exibe um seletor quando houver mais de um perfil, persiste a escolha na sessao existente por 15 minutos e permite retornar ao seletor por `Trocar perfil`.

**Tech Stack:** PHP 7+, CodeIgniter 3, MySQL, WhatsApp Cloud API interactive lists, testes PHP procedurais.

---

### Task 1: Resolver perfis disponiveis por telefone

**Files:**
- Modify: `application/helpers/whatsapp_agendamento_helper.php`
- Modify: `tests/whatsapp_chatbot_test.php`

- [ ] **Step 1: Write failing tests for grouping and priority dentro do perfil**

```php
$perfis = utec_whatsapp_resolver_perfis_chatbot([
    (object)['id' => 10, 'nivel' => 5, 'tenant_id' => 2],
    (object)['id' => 15, 'nivel' => 3, 'tenant_id' => 2],
    (object)['id' => 18, 'nivel' => 5, 'tenant_id' => 3],
]);
assertSameValue(['paciente', 'profissional'], array_keys($perfis), 'Telefone compartilhado deve listar perfis distintos.');
assertSameValue(18, $perfis['paciente']['id_usuario'], 'Mesmo perfil deve usar o usuario mais recente.');
assertSameValue(15, $perfis['profissional']['id_usuario'], 'Profissional deve manter seu usuario associado.');
```

- [ ] **Step 2: Run the helper test and verify failure**

Run: `php tests\whatsapp_chatbot_test.php`

Expected: fatal error for `utec_whatsapp_resolver_perfis_chatbot`.

- [ ] **Step 3: Implement the grouping helper**

```php
function utec_whatsapp_resolver_perfis_chatbot($usuarios)
{
    $perfis = [];
    foreach (is_array($usuarios) ? $usuarios : [] as $usuario) {
        $perfil = utec_whatsapp_perfil_por_nivel(utec_whatsapp_read($usuario, 'nivel'));
        if ($perfil === '') {
            continue;
        }
        if (!isset($perfis[$perfil]) || (int)utec_whatsapp_read($usuario, 'id') > $perfis[$perfil]['id_usuario']) {
            $perfis[$perfil] = [
                'id_usuario' => (int)utec_whatsapp_read($usuario, 'id'),
                'tenant_id' => (int)utec_whatsapp_read($usuario, 'tenant_id'),
                'perfil' => $perfil,
            ];
        }
    }
    return $perfis;
}
```

- [ ] **Step 4: Run the helper test and verify success**

Run: `php tests\whatsapp_chatbot_test.php`

Expected: `OK`.

### Task 2: Expor selecao de perfil no modelo

**Files:**
- Modify: `application/models/Whatsapp_model.php`
- Modify: `tests/whatsapp_chatbot_test.php`

- [ ] **Step 1: Add a failing source assertion for profile selection**

```php
assertSameValue(
    1,
    preg_match('/resolver_perfil_chatbot\(\$telefone, \$perfil_solicitado = \'\'\)/', $codigoModeloChatbot),
    'Modelo deve aceitar um perfil solicitado pelo seletor.'
);
```

- [ ] **Step 2: Run the test and verify failure**

Run: `php tests\whatsapp_chatbot_test.php`

Expected: assertion failure because the method accepts apenas `$telefone`.

- [ ] **Step 3: Implement selected and multi-profile results**

Change `resolver_perfil_chatbot($telefone, $perfil_solicitado = '')` to call `utec_whatsapp_resolver_perfis_chatbot($usuario->result())` and return:

```php
['perfil_status' => 'encontrado', 'perfis_disponiveis' => ['paciente']]
['perfil_status' => 'selecao_necessaria', 'perfis_disponiveis' => ['paciente', 'profissional']]
['perfil_status' => 'selecionado', 'perfil' => 'profissional', 'id_usuario' => 15, 'tenant_id' => 2]
```

Only accept `$perfil_solicitado` when it is a key returned by the grouped profiles. Preserve `numero_nao_encontrado`, `telefone_invalido` and `schema_usuarios_invalido` for their existing cases.

- [ ] **Step 4: Run the model helper test and lint**

Run: `php tests\whatsapp_chatbot_test.php`

Run: `php -l application\models\Whatsapp_model.php`

Expected: `OK` and no syntax errors.

### Task 3: Add the WhatsApp profile selector and session handling

**Files:**
- Modify: `application/libraries/Whatsapp_chatbot.php`
- Modify: `tests/whatsapp_chatbot_library_test.php`

- [ ] **Step 1: Write failing chatbot behavior tests**

Create a fake model profile response with `perfil_status => 'selecao_necessaria'` and assert the outbound payload includes:

```php
assertChatbotSame('chat:perfil:paciente', $envio->payloads[0]['payload']['interactive']['action']['sections'][0]['rows'][0]['id'], 'Seletor deve conter paciente.');
assertChatbotSame('chat:perfil:profissional', $envio->payloads[0]['payload']['interactive']['action']['sections'][0]['rows'][1]['id'], 'Seletor deve conter profissional.');
```

Then process `chat:perfil:profissional` and assert that `salvar_sessao_chatbot()` recebeu `fluxo => 'perfil'`, `etapa => 'selecionado'`, and that somente comandos de profissional aparecem no menu.

- [ ] **Step 2: Run the library test and verify failure**

Run: `php tests\whatsapp_chatbot_library_test.php`

Expected: selector assertions fail because the chatbot currently returns the message de nao cadastrado para perfis ambiguos.

- [ ] **Step 3: Implement selector, selection and profile switch**

Add methods with these responsibilities:

```php
protected function extrair_perfil_selecionado($evento)
protected function responder_seletor_perfil($telefone, $perfis)
protected function perfil_da_sessao($telefone, $sessao)
protected function selecionar_perfil($telefone, $perfil, $evento)
```

Use interactive IDs in the format `chat:perfil:<perfil>`. Save the selected result with the existing session method using `fluxo = 'perfil'` and `etapa = 'selecionado'`. Add `trocar_perfil` to permitted commands; it clears the session and responds with the selector. Preserve the existing motivo session behavior as higher priority than profile selection.

- [ ] **Step 4: Run chatbot behavior test and lint**

Run: `php tests\whatsapp_chatbot_library_test.php`

Run: `php -l application\libraries\Whatsapp_chatbot.php`

Expected: `OK` and no syntax errors.

### Task 4: Log the resolved selection and run regression tests

**Files:**
- Modify: `application/controllers/Webhooks.php`
- Modify: `tests/whatsapp_webhook_controller_test.php`

- [ ] **Step 1: Write failing log assertion**

```php
assertWebhookController(
    strpos($controller, 'perfil_escolhido=') !== false,
    'O webhook deve registrar o perfil escolhido sem registrar telefone ou texto.'
);
```

- [ ] **Step 2: Run the controller test and verify failure**

Run: `php tests\whatsapp_webhook_controller_test.php`

Expected: assertion failure because the log ainda nao contem `perfil_escolhido`.

- [ ] **Step 3: Add selected-profile field to the existing log**

Append this data to `[whatsapp_webhook] Chatbot processado`:

```php
.' perfil_escolhido='.(string)utec_whatsapp_read($resultado, 'perfil', '')
```

Ensure the library result carries `perfil` only after a single profile is resolved or an explicit selection is validated.

- [ ] **Step 4: Run all chatbot and webhook regressions**

Run: `php tests\whatsapp_agendamento_test.php`

Run: `php tests\whatsapp_chatbot_test.php`

Run: `php tests\whatsapp_chatbot_library_test.php`

Run: `php tests\whatsapp_webhook_test.php`

Run: `php tests\whatsapp_webhook_controller_test.php`

Expected: every command prints `OK`.

- [ ] **Step 5: Run syntax validation**

Run: `php -l application\helpers\whatsapp_agendamento_helper.php`

Run: `php -l application\models\Whatsapp_model.php`

Run: `php -l application\libraries\Whatsapp_chatbot.php`

Run: `php -l application\controllers\Webhooks.php`

Expected: no syntax errors in all files.
