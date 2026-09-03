# Chatbot WhatsApp por perfis Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (- [ ]) syntax for tracking.

**Goal:** Criar menus WhatsApp por perfil, com sessao persistida, sem alterar o comportamento dos botoes de confirmacao e cancelamento ja enviados.

**Architecture:** Webhooks continua autenticando a Meta e delega novas mensagens para Whatsapp_chatbot. A biblioteca resolve perfil e sessao no Whatsapp_model, reutiliza o escopo de Padrao_model e envia respostas por Whatsapp_agendamento. Duas tabelas guardam sessao e auditoria/idempotencia; o SQL fica em docs porque este projeto nao possui migrations automaticas.

**Tech Stack:** PHP 7, CodeIgniter 3, MySQL/InnoDB, Meta WhatsApp Cloud API e testes PHP diretos.

---

## Estrutura de arquivos

- Criar docs/whatsapp-chatbot-sessoes.sql: sessoes, eventos recebidos e alteracao das notificacoes internas.
- Criar application/libraries/Whatsapp_chatbot.php: roteador de perfil, menu, texto pendente e respostas.
- Modificar application/helpers/whatsapp_agendamento_helper.php: parser de texto/lista, payloads Meta e funcoes puras.
- Modificar application/models/Whatsapp_model.php: perfil por telefone, sessao, idempotencia, agenda e plano autorizados.
- Modificar application/libraries/Whatsapp_agendamento.php: envio generico dos payloads do chatbot.
- Modificar application/models/Notificacoes_model.php: remarcacao e cancelamento com motivo.
- Modificar application/controllers/Webhooks.php: delegacao sem duplicar botoes legados.
- Criar tests/whatsapp_chatbot_test.php e modificar os testes atuais do webhook.
- Atualizar docs/whatsapp-confirmacao-agendamento.md e webhooks/whatsapp/README.md.

### Task 1: Cobrir o formato de mensagem recebido e enviado

**Files:**
- Create: tests/whatsapp_chatbot_test.php
- Modify: tests/whatsapp_webhook_test.php
- Modify: application/helpers/whatsapp_agendamento_helper.php

- [ ] **Step 1: Escrever os testes que falham**

Criar tests/whatsapp_chatbot_test.php, usando o mesmo assertSameValue() de tests/whatsapp_webhook_test.php, com estes casos:

~~~php
$evento = utec_whatsapp_extrair_evento_webhook([
    'entry' => [[
        'changes' => [[
            'value' => ['messages' => [[
                'id' => 'wamid.inbound.1',
                'from' => '5581988887777',
                'type' => 'text',
                'text' => ['body' => 'Oi'],
            ]]],
        ]],
    ]],
]);
assertSameValue('text', $evento['message_type'], 'Deve reconhecer texto.');
assertSameValue('Oi', $evento['text'], 'Deve preservar o texto.');
assertSameValue('wamid.inbound.1', $evento['message_id'], 'Deve preservar id recebido.');
assertSameValue('5581988887777', $evento['from'], 'Deve preservar remetente.');

$lista = utec_whatsapp_payload_lista('5581988887777', 'Menu', 'Escolha uma opcao.', 'Ver opcoes', [[
    'title' => 'Atendimento',
    'rows' => [['id' => 'chat:paciente:proximas', 'title' => 'Proximas consultas', 'description' => 'Veja seus horarios']],
]]);
assertSameValue('interactive', $lista['type'], 'Menu deve ser interativo.');
assertSameValue('list', $lista['interactive']['type'], 'Menu deve ser lista.');

$botoes = utec_whatsapp_payload_botoes('5581988887777', 'Consulta', 'Escolha uma opcao.', [
    ['id' => 'confirmar_agendamento:77', 'title' => 'Confirmar'],
    ['id' => 'cancelar_agendamento:77', 'title' => 'Cancelar'],
]);
assertSameValue('cancelar_agendamento:77', $botoes['interactive']['action']['buttons'][1]['reply']['id'], 'Botao deve manter payload legado.');
~~~

Adicionar tambem a regressao de que cancelar_agendamento:492 ainda gera action cancelar e id_agendamento 492.

- [ ] **Step 2: Rodar teste para verificar a falha**

Run: php tests\whatsapp_chatbot_test.php

Expected: falha por Call to undefined function utec_whatsapp_payload_lista().

- [ ] **Step 3: Implementar o contrato Meta minimo**

Em utec_whatsapp_evento_webhook_vazio(), acrescentar message_id, from, message_type e text vazios. Em utec_whatsapp_extrair_eventos_webhook(), preencher esses campos de messages.id, messages.from, messages.type e messages.text.body; extrair primeiro interactive.list_reply.id, depois interactive.button_reply.id e depois button.payload. Conservar context.id em wamid e manter o regex legado:

~~~php
if (preg_match('/^(confirmar|cancelar)_agendamento:(\d+)$/', $buttonId, $matches)) {
    $evento['action'] = $matches[1];
    $evento['id_agendamento'] = (int)$matches[2];
}
~~~

Adicionar utec_whatsapp_payload_lista() e utec_whatsapp_payload_botoes(). Ambos retornam messaging_product whatsapp, recipient_type individual, to, type interactive e limitam titulo a 60, corpo a 1024, texto de botao a 20 e no maximo tres botoes. Lista usa interactive.type list, action.button e action.sections; botoes usam interactive.type button e action.buttons[].reply. Descartar itens sem id ou title; o chamador usa texto simples quando nao houver item valido.

- [ ] **Step 4: Verificar parser e sintaxe**

Run: php tests\whatsapp_chatbot_test.php; php tests\whatsapp_webhook_test.php; php -l application\helpers\whatsapp_agendamento_helper.php

Expected: ambos os testes exibem OK e o lint exibe No syntax errors detected.

- [ ] **Step 5: Commit**

~~~bash
git add application/helpers/whatsapp_agendamento_helper.php tests/whatsapp_chatbot_test.php tests/whatsapp_webhook_test.php
git commit -m "feat: parse whatsapp chatbot messages"
~~~

### Task 2: Persistir sessao, auditoria e perfil

**Files:**
- Create: docs/whatsapp-chatbot-sessoes.sql
- Modify: application/helpers/whatsapp_agendamento_helper.php
- Modify: application/models/Whatsapp_model.php
- Modify: tests/whatsapp_chatbot_test.php

- [ ] **Step 1: Escrever testes das regras de perfil**

~~~php
assertSameValue('paciente', utec_whatsapp_chatbot_perfil_por_nivel(5), 'Nivel 5 e paciente.');
assertSameValue('profissional', utec_whatsapp_chatbot_perfil_por_nivel(3), 'Nivel 3 e profissional.');
assertSameValue('atendente', utec_whatsapp_chatbot_perfil_por_nivel(4), 'Nivel 4 e atendente.');
assertSameValue('admin', utec_whatsapp_chatbot_perfil_por_nivel(2), 'Nivel 2 e admin.');
assertSameValue('', utec_whatsapp_chatbot_perfil_por_nivel(0), 'Nivel invalido nao e perfil.');
assertSameValue(true, utec_whatsapp_chatbot_perfil_tem_plano('profissional'), 'Profissional pode ver plano.');
assertSameValue(false, utec_whatsapp_chatbot_perfil_tem_plano('atendente'), 'Atendente nao pode ver plano.');
~~~

- [ ] **Step 2: Criar o SQL idempotente**

Criar docs/whatsapp-chatbot-sessoes.sql:

~~~sql
CREATE TABLE IF NOT EXISTS whatsapp_chatbot_sessoes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  telefone VARCHAR(20) NOT NULL,
  perfil VARCHAR(20) NOT NULL DEFAULT '',
  id_usuario INT UNSIGNED NOT NULL DEFAULT 0,
  tenant_id INT UNSIGNED NOT NULL DEFAULT 0,
  fluxo VARCHAR(60) NOT NULL DEFAULT '',
  etapa VARCHAR(60) NOT NULL DEFAULT '',
  dados_json MEDIUMTEXT NULL,
  ultima_atividade_em DATETIME NOT NULL,
  expira_em DATETIME NOT NULL,
  criado_em DATETIME NOT NULL,
  atualizado_em DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_chatbot_sessao_telefone (telefone),
  KEY idx_chatbot_sessao_expira (expira_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS whatsapp_chatbot_eventos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  message_id VARCHAR(150) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  id_sessao INT UNSIGNED NOT NULL DEFAULT 0,
  id_usuario INT UNSIGNED NOT NULL DEFAULT 0,
  id_agendamento INT UNSIGNED NOT NULL DEFAULT 0,
  tipo VARCHAR(40) NOT NULL,
  entrada MEDIUMTEXT NULL,
  resultado VARCHAR(60) NOT NULL DEFAULT '',
  criado_em DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_chatbot_evento_message (message_id),
  KEY idx_chatbot_evento_telefone (telefone, criado_em),
  KEY idx_chatbot_evento_agendamento (id_agendamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
~~~

Antes dos comandos abaixo, executar `SHOW COLUMNS FROM notificacoes_usuarios LIKE 'id_whatsapp_chatbot_evento';`. Executar o bloco somente se a coluna nao existir, pois MySQL antigo nao aceita `ADD COLUMN IF NOT EXISTS`:

~~~sql
ALTER TABLE notificacoes_usuarios
  ADD COLUMN id_whatsapp_chatbot_evento INT UNSIGNED NOT NULL DEFAULT 0,
  ADD KEY idx_notificacao_chatbot_evento (id_whatsapp_chatbot_evento),
  ADD UNIQUE KEY uq_notificacao_chatbot_evento_usuario (id_usuario_destino, id_whatsapp_chatbot_evento, tipo);
~~~

- [ ] **Step 3: Implementar helpers e metodos do modelo**

Adicionar:

~~~php
function utec_whatsapp_chatbot_perfil_por_nivel($nivel) {
    switch ((int)$nivel) {
        case 5: return 'paciente';
        case 3: return 'profissional';
        case 1: case 2: return 'admin';
        case 4: return 'atendente';
        default: return '';
    }
}
function utec_whatsapp_chatbot_perfil_tem_plano($perfil) {
    return in_array((string)$perfil, ['admin', 'profissional'], true);
}
~~~

Em Whatsapp_model, declarar chatbot_session_table e chatbot_event_table e implementar:

~~~php
public function iniciar_evento_chatbot($evento)
public function finalizar_evento_chatbot($messageId, $resultado, $idAgendamento = 0)
public function obter_sessao_chatbot($telefone)
public function salvar_sessao_chatbot($telefone, $perfil, $usuario, $fluxo, $etapa, $dados, $minutos = 15)
public function limpar_sessao_chatbot($telefone)
public function resolver_perfil_chatbot($telefone)
public function listar_agendamentos_chatbot($usuario, $data, $limite = 10, $statusWhatsapp = '')
public function obter_agendamento_chatbot($idAgendamento, $usuario)
public function obter_plano_chatbot($tenantId)
~~~

iniciar_evento_chatbot() usa INSERT IGNORE por message_id; false significa reentrega e nenhuma resposta. A sessao expira em 15 minutos e serializa dados com json_encode(). resolver_perfil_chatbot() normaliza telefones de usuarios em PHP e prioriza nivel interno 1-4 sobre nivel 5; sem vinculo retorna ['perfil' => '', 'usuario' => null, 'tenant_id' => 0].

Consultas internas usam get_scope_user_ids($usuario) e ids_to_sql_in() para filtrar id_user, id_paciente ou id_prestador. Paciente filtra apenas id_paciente. Selecionar somente id, data, hora, nomes e ultimo status_confirmacao. obter_agendamento_chatbot() repete o filtro antes de qualquer alteracao. obter_plano_chatbot() retorna modelo, status e a primeira data existente entre next_billing_at, current_period_end e trial_ends_at; sem assinatura retorna null.

- [ ] **Step 4: Verificar regras, SQL e modelo**

Run: php tests\whatsapp_chatbot_test.php; php -l application\models\Whatsapp_model.php; git diff --check -- docs\whatsapp-chatbot-sessoes.sql

Expected: testes passam, lint passa e o ultimo comando nao gera saida.

- [ ] **Step 5: Commit**

~~~bash
git add docs/whatsapp-chatbot-sessoes.sql application/helpers/whatsapp_agendamento_helper.php application/models/Whatsapp_model.php tests/whatsapp_chatbot_test.php
git commit -m "feat: persist whatsapp chatbot sessions"
~~~

### Task 3: Criar o roteador e os menus aprovados

**Files:**
- Create: application/libraries/Whatsapp_chatbot.php
- Modify: application/helpers/whatsapp_agendamento_helper.php
- Modify: application/libraries/Whatsapp_agendamento.php
- Modify: application/models/Notificacoes_model.php
- Modify: tests/whatsapp_chatbot_test.php

- [ ] **Step 1: Testar comandos, status e suporte**

~~~php
assertSameValue('chat:paciente:proximas', utec_whatsapp_chatbot_comando('paciente', 'proximas'), 'Paciente deve ter proximas.');
assertSameValue('chat:profissional:agenda_hoje', utec_whatsapp_chatbot_comando('profissional', 'agenda_hoje'), 'Profissional deve ter agenda.');
assertSameValue('', utec_whatsapp_chatbot_comando('paciente', 'plano'), 'Paciente nao pode ter plano.');
assertSameValue('✅ confirmado', utec_whatsapp_chatbot_rotulo_status('confirmado'), 'Confirmado deve ter indicador.');
assertSameValue('⏳ pendente', utec_whatsapp_chatbot_rotulo_status('pendente'), 'Pendente deve ter indicador.');
assertSameValue('❌ cancelado', utec_whatsapp_chatbot_rotulo_status('cancelado'), 'Cancelado deve ter indicador.');
assertSameValue('https://wa.me/5581983276882', utec_whatsapp_chatbot_link_suporte(), 'Link do dev deve estar correto.');
~~~

- [ ] **Step 2: Executar teste e confirmar a falha**

Run: php tests\whatsapp_chatbot_test.php

Expected: falha por Call to undefined function utec_whatsapp_chatbot_comando().

- [ ] **Step 3: Implementar comandos, envio e biblioteca**

Criar utec_whatsapp_chatbot_comando() com uma matriz fechada:

~~~php
[
 'paciente' => ['proximas', 'consulta', 'cancelar', 'remarcar', 'atendimento', 'voltar'],
 'profissional' => ['agenda_hoje', 'agenda_amanha', 'pendencias', 'plano', 'suporte', 'voltar'],
 'admin' => ['agenda_hoje', 'pendencias', 'cancelamentos', 'plano', 'suporte', 'voltar'],
 'atendente' => ['agenda_hoje', 'pendencias', 'cancelamentos', 'suporte', 'voltar'],
]
~~~

Adicionar helpers que retornam, respectivamente: status confirmado/pendente/cancelado com os indicadores, https://wa.me/5581983276882 e Fale com o dev seguido do link.

Em Whatsapp_agendamento, adicionar enviar_chatbot($telefone, $payload): normalizar pelo metodo atual normalizar_destino(), validar configuracao ativa, preencher somente `$payload['to']` e chamar enviar_payload(). Retornar ['sent' => bool, 'wamid' => string, 'error' => string]. Nao aplicar quota porque a conversa foi iniciada pelo usuario.

Em Notificacoes_model, criar criar_solicitacao_chatbot($contexto, $tipo, $titulo, $mensagem), aceitando somente whatsapp_remarcacao_solicitada e whatsapp_cancelamento_com_motivo. Reutilizar destinatarios de criador/prestador e deduplicar por id_whatsapp_chatbot_evento.

Criar Whatsapp_chatbot com processar($evento) e os metodos protegidos enviar_menu, enviar_agenda, enviar_consultas_paciente, consumir_texto_pendente, enviar_plano, enviar_suporte e enviar_texto. Carregar Whatsapp_model, Notificacoes_model, Padrao_model e Whatsapp_agendamento no construtor.

processar() segue esta ordem:

1. Ignorar evento sem from ou message_id.
2. Deduplicar por iniciar_evento_chatbot().
3. Resolver perfil. Se nao cadastrado, enviar Numero nao cadastrado. Conheca a Utec em seguido de base_url(), finalizar nao_cadastrado e nao criar sessao.
4. Se sessao for aguardar_motivo_cancelamento ou aguardar_remarcacao, consumir somente texto. Exigir tres caracteres; menu ou texto invalido mantem sessao.
5. Validar chat:perfil:acao somente quando o perfil do payload for o perfil resolvido; texto livre ou comando invalido chama enviar_menu().
6. Finalizar o evento sem gravar texto de paciente em log_message().

Menus: paciente recebe Proximas consultas, Confirmar ou cancelar, Solicitar remarcacao e Falar com atendimento. Esta ultima opcao responde `Entre em contato com a clinica responsavel pelo seu atendimento.` e oferece Voltar ao menu, sem revelar telefone interno. Profissional recebe Agenda de hoje, Agenda de amanha, Confirmacoes pendentes, Meu plano e Suporte. Admin recebe Agenda de hoje, Pendencias de confirmacao, Cancelamentos do dia, Meu plano e Suporte. Atendente recebe os mesmos itens de admin sem Meu plano.

Agenda lista no maximo dez linhas no formato HH:MM - Nome - status. Nao incluir telefone, prontuario ou anotacao clinica. A acao consulta lista somente agendamentos futuros do paciente; cada item gera os comandos `chat:paciente:cancelar:<id>` e `chat:paciente:remarcar:<id>`. Validar esses dois formatos com regex, exigir que o perfil seja paciente e chamar obter_agendamento_chatbot() antes de gravar sessao. Cancelamento via menu abre sessao e pergunta Explique o motivo do cancelamento desta consulta. Remarcacao pergunta Informe o melhor dia ou horario para solicitar a remarcacao. Ao concluir, criar notificacao interna, limpar sessao e confirmar ao paciente. Plano mostra somente modelo, status e data; nunca valor, e-mail, documento ou pagamento.

- [ ] **Step 4: Verificar biblioteca e testes**

Run: php tests\whatsapp_chatbot_test.php; php -l application\libraries\Whatsapp_chatbot.php; php -l application\libraries\Whatsapp_agendamento.php; php -l application\models\Notificacoes_model.php

Expected: teste e tres lints passam.

- [ ] **Step 5: Commit**

~~~bash
git add application/helpers/whatsapp_agendamento_helper.php application/libraries/Whatsapp_chatbot.php application/libraries/Whatsapp_agendamento.php application/models/Notificacoes_model.php tests/whatsapp_chatbot_test.php
git commit -m "feat: add whatsapp chatbot profile menus"
~~~

### Task 4: Integrar ao webhook e preservar confirmacao/cancelamento

**Files:**
- Modify: application/controllers/Webhooks.php
- Modify: tests/whatsapp_webhook_controller_test.php
- Modify: tests/whatsapp_webhook_test.php

- [ ] **Step 1: Adicionar a expectativa de integracao**

Adicionar em tests/whatsapp_webhook_controller_test.php:

~~~php
assertWebhookController(strpos($controller, "load->library('whatsapp_chatbot')") !== false, 'Controller deve carregar chatbot.');
assertWebhookController(strpos($controller, 'whatsapp_chatbot->processar($evento)') !== false, 'Controller deve delegar mensagens.');
assertWebhookController(strpos($controller, "if ($evento['action'] !== '')") !== false, 'Botoes legados devem continuar separados.');
~~~

- [ ] **Step 2: Executar teste e confirmar a falha**

Run: php tests\whatsapp_webhook_controller_test.php

Expected: falha dizendo que o chatbot ainda nao foi carregado.

- [ ] **Step 3: Alterar o controller**

No construtor, adicionar:

~~~php
$this->load->library('whatsapp_chatbot');
~~~

Em receber_whatsapp(), manter processar_status_entrega() inalterado e trocar o processamento de mensagens por:

~~~php
if ($evento['action'] !== '') {
    $this->processar_resposta_agendamento($evento);
    continue;
}
if ($evento['message_id'] !== '' && $evento['from'] !== '') {
    $this->whatsapp_chatbot->processar($evento);
}
~~~

O continue impede resposta duplicada: quick replies antigos continuam exclusivos de processar_resposta_agendamento(), inclusive com transacao e notificacoes internas ja existentes. Apenas texto, lista e botoes novos entram no chatbot. Registrar eventos tecnicos com o prefixo [whatsapp_chatbot], sem corpo de texto de paciente.

- [ ] **Step 4: Rodar a regressao completa**

Run: php tests\whatsapp_webhook_controller_test.php; php tests\whatsapp_webhook_test.php; php tests\whatsapp_chatbot_test.php; php -l application\controllers\Webhooks.php

Expected: tres OK e lint sem erro.

- [ ] **Step 5: Commit**

~~~bash
git add application/controllers/Webhooks.php tests/whatsapp_webhook_controller_test.php tests/whatsapp_webhook_test.php tests/whatsapp_chatbot_test.php
git commit -m "feat: route whatsapp messages to chatbot"
~~~

### Task 5: Documentar, instalar o SQL e validar o aceite

**Files:**
- Modify: docs/whatsapp-confirmacao-agendamento.md
- Modify: webhooks/whatsapp/README.md

- [ ] **Step 1: Atualizar a documentacao**

Em docs/whatsapp-confirmacao-agendamento.md, documentar: qualquer texto fora de fluxo abre menu; fluxos de motivo/remarcacao duram 15 minutos; os quatro perfis; prioridade de usuario interno sobre paciente; resposta de nao cadastrado com https://utecnologia.com.br/; e Fale com o dev: https://wa.me/5581983276882.

Em webhooks/whatsapp/README.md, incluir: fazer backup; executar docs/whatsapp-chatbot-sessoes.sql; confirmar whatsapp_config ativo e App Secret; manter https://utecnologia.com.br/webhooks/whatsapp na Meta; enviar menu de telefone de teste.

- [ ] **Step 2: Rodar todas as verificacoes locais**

Run: php tests\whatsapp_agendamento_test.php; php tests\whatsapp_webhook_test.php; php tests\whatsapp_webhook_controller_test.php; php tests\whatsapp_chatbot_test.php; php tests\notificacoes_usuarios_test.php; php -l application\helpers\whatsapp_agendamento_helper.php; php -l application\models\Whatsapp_model.php; php -l application\models\Notificacoes_model.php; php -l application\libraries\Whatsapp_agendamento.php; php -l application\libraries\Whatsapp_chatbot.php; php -l application\controllers\Webhooks.php; git diff --check

Expected: todos os testes exibem OK, todos os lints passam e git diff --check nao gera saida.

- [ ] **Step 3: Executar aceite manual apos deploy**

1. Enviar texto livre de paciente e conferir menu e proximas consultas.
2. Selecionar cancelamento pelo menu, informar motivo e verificar auditoria/notificacao interna.
3. Tocar Cancelar em template antigo e confirmar cancelamento imediato, sem pedir motivo.
4. Testar agenda e plano com profissional e conferir escopo.
5. Testar atendente e conferir ausencia de Meu plano, inclusive para payload forjado.
6. Testar telefone nao cadastrado e conferir somente aviso/link publico.
7. Reenviar o mesmo evento da Meta e conferir uma unica linha em whatsapp_chatbot_eventos e uma unica resposta.
8. Deixar sessao expirar e enviar texto; conferir que o bot abre menu sem salvar o texto como motivo.

- [ ] **Step 4: Commit**

~~~bash
git add docs/whatsapp-confirmacao-agendamento.md webhooks/whatsapp/README.md
git commit -m "docs: document whatsapp chatbot operation"
~~~

## Revisao de cobertura

- Texto livre e etapa que exige resposta aberta: Tasks 2 e 3.
- Nao cadastrado com site: Task 3.
- Menus de paciente, profissional, atendente e admin: Task 3.
- Agenda com status: Task 3.
- Plano limitado por perfil: Tasks 2 e 3.
- Suporte Fale com o dev: Task 3.
- Compatibilidade confirmar/cancelar: Tasks 1 e 4.
- Assinatura, autorizacao e reentrega: assinatura atual permanece; Tasks 2 e 4 cobrem escopo e idempotencia.
