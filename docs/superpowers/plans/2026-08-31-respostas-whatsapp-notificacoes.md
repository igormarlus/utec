# Respostas WhatsApp e Notificacoes Internas Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Responder ao paciente apos Confirmar ou Cancelar e registrar avisos internos, visiveis no topo, agenda e prontuario, para quem marcou e para o profissional.

**Architecture:** O webhook permanece a entrada autenticada. `Whatsapp_model` passa a executar a primeira transicao de resposta de forma idempotente; apenas essa primeira transicao dispara texto ao paciente e cria notificacoes internas. `Notificacoes_model` encapsula leitura, criacao e marcacao de leitura em `notificacoes_usuarios`; agenda e prontuario leem o ultimo estado de `whatsapp_notificacoes` sem alterar seus fluxos existentes.

**Tech Stack:** PHP 7 compatível, CodeIgniter 3, MySQL/InnoDB, Meta WhatsApp Cloud API e testes PHP diretos no diretorio `tests/`.

---

## Estrutura de Arquivos

- Create: `application/models/Notificacoes_model.php` - persistencia e leitura de avisos internos por usuario.
- Create: `application/controllers/adm/Notificacoes.php` - abertura segura de um aviso e marcacao como lido.
- Create: `docs/notificacoes-usuarios.sql` - versao rastreada do SQL ja aplicado em producao.
- Create: `tests/notificacoes_usuarios_test.php` - validacoes das regras puras de destinatario e textos de evento.
- Modify: `application/helpers/whatsapp_agendamento_helper.php` - funcoes puras para texto ao paciente, payload de texto e destinatarios distintos.
- Modify: `application/libraries/Whatsapp_agendamento.php` - envio de mensagem de texto comum pela Cloud API.
- Modify: `application/models/Whatsapp_model.php` - transicao idempotente da resposta e contexto do agendamento.
- Modify: `application/controllers/Webhooks.php` - coordenacao de persistencia, resposta ao paciente e avisos internos.
- Modify: `includes/adm/top.php` - contador e lista de notificacoes nao lidas do usuario logado.
- Modify: `application/controllers/adm/Atendimento.php` - selecao do ultimo retorno WhatsApp no dataset da agenda.
- Modify: `application/views/adm/usuarios/new/atendimentos.php` - indicadores de confirmado, cancelado ou sem retorno na agenda desktop e mobile.
- Modify: `application/views/adm/usuarios/new/prontuario.php` - resposta WhatsApp na linha do tempo do agendamento.
- Modify: `tests/whatsapp_webhook_test.php` - cobertura de texto e idempotencia do evento recebido.

### Task 1: Criar regras puras e testes de resposta

**Files:**
- Modify: `tests/whatsapp_webhook_test.php`
- Create: `tests/notificacoes_usuarios_test.php`
- Modify: `application/helpers/whatsapp_agendamento_helper.php`

- [ ] **Step 1: Escrever o teste que falha para os textos de resposta e payload de texto**

Adicionar ao teste de webhook:

```php
assertSameValue(
    'Recebemos sua confirmacao. Sua consulta permanece agendada. Em caso de necessidade, entre em contato com a clinica.',
    utec_whatsapp_texto_resposta_agendamento('confirmar'),
    'Confirmacao deve usar o texto padrao.'
);
assertSameValue('text', utec_whatsapp_payload_texto('5581999999999', 'Teste')['type'], 'Resposta deve usar mensagem de texto.');
```

Criar `tests/notificacoes_usuarios_test.php` para exigir que `utec_notificacoes_destinatarios_agendamento(12, 12)` devolva apenas `[12]` e que `(12, 25)` devolva `[12, 25]`.

- [ ] **Step 2: Executar os testes para confirmar falha**

Run: `php tests\whatsapp_webhook_test.php` e `php tests\notificacoes_usuarios_test.php`

Expected: falha por funcoes inexistentes.

- [ ] **Step 3: Implementar as funcoes puras no helper**

Adicionar funcoes que retornem os dois textos aprovados, montem:

```php
[
    'messaging_product' => 'whatsapp',
    'recipient_type' => 'individual',
    'to' => $telefone,
    'type' => 'text',
    'text' => ['preview_url' => false, 'body' => $texto],
]
```

e normalizem a lista de destinatarios removendo IDs invalidos e duplicados.

- [ ] **Step 4: Executar os testes verdes**

Run: `php tests\whatsapp_webhook_test.php` e `php tests\notificacoes_usuarios_test.php`

Expected: ambos retornam `OK`.

- [ ] **Step 5: Commit**

```bash
git add application/helpers/whatsapp_agendamento_helper.php tests/whatsapp_webhook_test.php tests/notificacoes_usuarios_test.php
git commit -m "feat: add whatsapp response helpers"
```

### Task 2: Persistir resposta uma unica vez e avisos internos

**Files:**
- Create: `application/models/Notificacoes_model.php`
- Create: `docs/notificacoes-usuarios.sql`
- Modify: `application/models/Whatsapp_model.php`
- Modify: `tests/notificacoes_usuarios_test.php`

- [ ] **Step 1: Escrever teste de deduplicacao de destinatarios e tipos de aviso**

Adicionar ao teste de notificacoes as expectativas para os tipos `whatsapp_agendamento_confirmado` e `whatsapp_agendamento_cancelado`, e para a mensagem conter o nome do paciente e a acao.

- [ ] **Step 2: Executar para confirmar falha**

Run: `php tests\notificacoes_usuarios_test.php`

Expected: falha pelas funcoes de tipo e texto inexistentes.

- [ ] **Step 3: Criar o model e SQL rastreado**

`Notificacoes_model` deve expor:

```php
public function criar_resposta_agendamento($contexto, $acao)
public function listar_nao_lidas($id_usuario, $limite = 8)
public function contar_nao_lidas($id_usuario)
public function abrir_para_usuario($id, $id_usuario)
```

`criar_resposta_agendamento()` usa `utec_notificacoes_destinatarios_agendamento()` com `id_user` e `id_prestador`, insere uma linha por destinatario e depende da chave unica da tabela para ignorar reenvios.

`Whatsapp_model` deve substituir a atual atualizacao cega por `registrar_resposta_webhook($id_notificacao, $acao)`: carregar a linha, retornar `processado => false` se ela nao estiver pendente, e transacionar a atualizacao. Para cancelar, atualizar tambem `agendamentos.status = 3` antes do commit. O retorno traz `notificacao` e o contexto do agendamento.

`docs/notificacoes-usuarios.sql` deve conter o `CREATE TABLE` fornecido ao usuario, sem executar migration automatica.

- [ ] **Step 4: Executar teste e lint**

Run: `php tests\notificacoes_usuarios_test.php; php -l application\models\Notificacoes_model.php; php -l application\models\Whatsapp_model.php`

Expected: testes `OK` e nenhuma falha de sintaxe.

- [ ] **Step 5: Commit**

```bash
git add application/models/Notificacoes_model.php application/models/Whatsapp_model.php docs/notificacoes-usuarios.sql tests/notificacoes_usuarios_test.php
git commit -m "feat: persist whatsapp response notifications"
```

### Task 3: Enviar texto ao paciente depois da transicao

**Files:**
- Modify: `application/libraries/Whatsapp_agendamento.php`
- Modify: `application/controllers/Webhooks.php`
- Modify: `tests/whatsapp_webhook_controller_test.php`

- [ ] **Step 1: Escrever teste que exige logs de resposta enviada ou falha**

Adicionar ao teste do controller verificacoes para as mensagens:

```php
'[whatsapp_webhook] Resposta ao paciente enviada.'
'[whatsapp_webhook] Falha ao responder paciente.'
```

- [ ] **Step 2: Executar para confirmar falha**

Run: `php tests\whatsapp_webhook_controller_test.php`

Expected: falha porque os logs ainda nao existem.

- [ ] **Step 3: Implementar envio sem afetar status clinico**

Adicionar `public function responder_interacao($telefone, $acao)` em `Whatsapp_agendamento`. Ele usa a configuracao ativa, `utec_whatsapp_payload_texto()` e o mesmo cliente cURL da Cloud API. O retorno deve conter `sent`, `wamid` e `error`.

Em `Webhooks`, depois de `registrar_resposta_webhook()` retornar `processado => true`:

1. chamar `Notificacoes_model::criar_resposta_agendamento()`;
2. chamar `Whatsapp_agendamento::responder_interacao()` usando `telefone_destino` da notificacao;
3. registrar sucesso ou falha;
4. nunca fazer rollback da confirmacao/cancelamento por falha do texto.

Reenvios que retornarem `processado => false` respondem `200` sem criar aviso ou enviar texto novamente.

- [ ] **Step 4: Executar testes e lint**

Run: `php tests\whatsapp_webhook_controller_test.php; php tests\whatsapp_webhook_test.php; php -l application\libraries\Whatsapp_agendamento.php; php -l application\controllers\Webhooks.php`

Expected: todos retornam `OK` ou sintaxe valida.

- [ ] **Step 5: Commit**

```bash
git add application/libraries/Whatsapp_agendamento.php application/controllers/Webhooks.php tests/whatsapp_webhook_controller_test.php tests/whatsapp_webhook_test.php
git commit -m "feat: reply to whatsapp appointment buttons"
```

### Task 4: Exibir e marcar notificacoes internas

**Files:**
- Create: `application/controllers/adm/Notificacoes.php`
- Modify: `includes/adm/top.php`
- Modify: `tests/notificacoes_usuarios_test.php`

- [ ] **Step 1: Escrever teste de acesso por destinatario**

Adicionar expectativa textual para que o controller use `abrir_para_usuario($id, $this->session->userdata('id'))` e redirecione somente para uma URL armazenada da propria notificacao.

- [ ] **Step 2: Executar para confirmar falha**

Run: `php tests\notificacoes_usuarios_test.php`

Expected: falha porque o controller ainda nao existe.

- [ ] **Step 3: Implementar controller e sino do topo**

`Notificacoes::abrir($id)` deve validar sessao, buscar e marcar a linha como lida pelo destinatario, e redirecionar para `url`; se a URL estiver vazia, redirecionar para `adm/atendimento`.

No topo, manter os atalhos informativos atuais e adicionar uma lista separada de eventos nao lidos. O contador deve mostrar somente a quantidade de eventos nao lidos; cada item aponta para `adm/notificacoes/abrir/{id}`.

- [ ] **Step 4: Executar teste e lint**

Run: `php tests\notificacoes_usuarios_test.php; php -l application\controllers\adm\Notificacoes.php; php -l includes\adm\top.php`

Expected: `OK` e sintaxe valida.

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Notificacoes.php includes/adm/top.php tests/notificacoes_usuarios_test.php
git commit -m "feat: show appointment response notifications"
```

### Task 5: Mostrar resposta na agenda e prontuario

**Files:**
- Modify: `application/controllers/adm/Atendimento.php`
- Modify: `application/views/adm/usuarios/new/atendimentos.php`
- Modify: `application/views/adm/usuarios/new/prontuario.php`
- Modify: `tests/notificacoes_usuarios_test.php`

- [ ] **Step 1: Escrever teste para rotulos de exibicao**

Adicionar expectativas para `utec_whatsapp_rotulo_confirmacao('confirmado')`, `cancelado` e string vazia, retornando respectivamente `Confirmado via WhatsApp`, `Cancelado via WhatsApp` e `Sem retorno WhatsApp`.

- [ ] **Step 2: Executar para confirmar falha**

Run: `php tests\notificacoes_usuarios_test.php`

Expected: falha pela funcao de rotulo inexistente.

- [ ] **Step 3: Implementar consulta e indicadores apenas informativos**

Na consulta principal da agenda, juntar a ultima linha de `whatsapp_notificacoes` por `id_agendamento` e selecionar `status_confirmacao` e `respondido_em`.

Exibir a etiqueta na tabela desktop e no resumo dos itens mobile. No prontuario, consultar o ultimo retorno do agendamento atual e mostrar uma linha na timeline com status e horario. A tela deve continuar funcionando quando nao houver linha WhatsApp.

- [ ] **Step 4: Executar testes e lint**

Run: `php tests\notificacoes_usuarios_test.php; php -l application\controllers\adm\Atendimento.php; php -l application\views\adm\usuarios\new\atendimentos.php; php -l application\views\adm\usuarios\new\prontuario.php`

Expected: `OK` e nenhuma falha de sintaxe.

- [ ] **Step 5: Commit**

```bash
git add application/controllers/adm/Atendimento.php application/views/adm/usuarios/new/atendimentos.php application/views/adm/usuarios/new/prontuario.php tests/notificacoes_usuarios_test.php application/helpers/whatsapp_agendamento_helper.php
git commit -m "feat: show whatsapp response in appointment views"
```

### Task 6: Verificar e publicar

**Files:**
- Modify: arquivos de producao das tarefas 1 a 5.

- [ ] **Step 1: Executar toda a suite local**

Run: `php tests\whatsapp_agendamento_test.php; php tests\whatsapp_webhook_test.php; php tests\whatsapp_webhook_controller_test.php; php tests\notificacoes_usuarios_test.php`

Expected: todos retornam `OK`.

- [ ] **Step 2: Conferir alteracoes e sintaxe**

Run: `git diff --check` e `php -l` para cada arquivo PHP modificado.

Expected: sem erros de espacos ou sintaxe.

- [ ] **Step 3: Publicar somente arquivos de producao por FTP**

Enviar para `/public_html`:

```text
application/helpers/whatsapp_agendamento_helper.php
application/libraries/Whatsapp_agendamento.php
application/models/Whatsapp_model.php
application/models/Notificacoes_model.php
application/controllers/Webhooks.php
application/controllers/adm/Notificacoes.php
application/controllers/adm/Atendimento.php
application/views/adm/usuarios/new/atendimentos.php
application/views/adm/usuarios/new/prontuario.php
includes/adm/top.php
```

- [ ] **Step 4: Testar em producao**

1. Criar um agendamento com envio habilitado.
2. Confirmar no WhatsApp e verificar mensagem de retorno, linha em `whatsapp_notificacoes`, duas notificacoes internas quando os responsaveis forem distintos, contador do topo e etiqueta da agenda.
3. Criar outro agendamento, cancelar no WhatsApp e verificar `agendamentos.status = 3`, texto de retorno e avisos internos.
4. Repetir ou aguardar reenvio do webhook e verificar que nao ha segunda resposta ao paciente nem notificacoes duplicadas.

- [ ] **Step 5: Commit final**

```bash
git add application includes docs tests
git commit -m "feat: complete whatsapp appointment responses"
```
