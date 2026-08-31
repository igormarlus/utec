# WhatsApp Webhook Design

**Data:** 2026-08-31
**Projeto:** UTec Saude
**Objetivo:** receber callbacks da Meta para validacao do webhook e para processar os botoes `Confirmar` e `Cancelar` enviados nos templates de agendamento.

## 1. Escopo aprovado

O webhook sera exposto em uma URL publica do projeto e apoiado por um controller novo, isolado do restante da area administrativa.

Estrutura:

- pasta nova na raiz: `webhooks/whatsapp/`
- controller novo: `application/controllers/Webhooks.php`
- rota publica: `webhooks/whatsapp`
- apoio de persistencia: `application/models/Whatsapp_model.php`

## 2. URL e token de confirmacao

URL esperada para cadastrar na Meta:

```text
https://utecnologia.com.br/webhooks/whatsapp
```

Token de confirmacao:

- nao ficara fixo no codigo
- vira do campo `verify_token` da configuracao ativa em `adm/whatsapp`
- valor inicial recomendado para cadastrar na Meta:

```text
utec_whatsapp_webhook_2026
```

Se o campo estiver vazio, o endpoint deve falhar na validacao com resposta clara em log.

## 3. Comportamentos do endpoint

### 3.1 GET de validacao

Quando a Meta chamar o endpoint com `hub.mode`, `hub.verify_token` e `hub.challenge`:

- ler a configuracao ativa em `whatsapp_config`
- comparar `hub.verify_token` com `verify_token`
- se bater, responder somente o valor de `hub.challenge`
- se nao bater, responder `403`

### 3.2 POST de eventos

Quando a Meta enviar eventos:

- ler o `php://input`
- registrar o payload bruto em arquivo de apoio dentro de `webhooks/whatsapp/` apenas para diagnostico simples
- tentar extrair mensagens interativas de quick reply
- localizar o `wamid` ou o `id_agendamento` associado
- atualizar a tabela `whatsapp_notificacoes`
- atualizar o status do agendamento quando a acao for cancelamento ou confirmacao

## 4. Regras de negocio dos botoes

Os payloads ja enviados hoje pelo sistema sao:

- `confirmar_agendamento:{id}`
- `cancelar_agendamento:{id}`

Tratamento esperado:

- `confirmar_agendamento:{id}`
  - marcar `whatsapp_notificacoes.status_confirmacao = 'confirmado'`
  - preencher `respondido_em`
  - manter o agendamento ativo, sem cancelar

- `cancelar_agendamento:{id}`
  - marcar `whatsapp_notificacoes.status_confirmacao = 'cancelado'`
  - preencher `respondido_em`
  - atualizar `agendamentos.status = 3`

## 5. Apoio esperado no Whatsapp_model

O model deve ganhar metodos focados em webhook:

- buscar configuracao ativa para validacao do token
- localizar notificacao por `wamid`
- localizar notificacao por `id_agendamento`
- atualizar `status_confirmacao` e `respondido_em`
- atualizar o agendamento para cancelado
- registrar eventos tecnicos do webhook quando necessario

Nao vamos mover a regra de payload para o model; o model fica como apoio de dados.

## 6. Pasta raiz do webhook

A pasta `webhooks/whatsapp/` existira para dois fins:

- documentar rapidamente o endpoint
- armazenar um log tecnico simples de payload bruto quando necessario

Arquivos previstos:

- `webhooks/whatsapp/README.md`
- `webhooks/whatsapp/.gitkeep`
- opcionalmente um arquivo de log criado em runtime, se o servidor permitir escrita

Se o servidor nao permitir escrita na pasta, o endpoint continua funcionando e cai apenas no `log_message()`.

## 7. Formato esperado do payload

O foco da primeira entrega sera lidar com respostas de botoes interativos da Meta. A implementacao deve procurar especialmente por:

- `entry[*].changes[*].value.messages[*].interactive.button_reply.id`
- `entry[*].changes[*].value.messages[*].context.id`

Uso:

- `button_reply.id` identifica a acao escolhida
- `context.id` ajuda a casar a resposta com o `wamid` da mensagem enviada

Se a estrutura vier diferente, o endpoint nao deve quebrar. Ele deve responder `200` e registrar que recebeu um evento nao tratado.

## 8. Testes e verificacao

Verificacoes minimas:

- `php -l application/controllers/Webhooks.php`
- `php -l application/models/Whatsapp_model.php`
- teste CLI simples para parsing de payload e identificacao de acao
- teste manual do GET de validacao com query string simulada

## 9. Fora de escopo desta etapa

- reenvio automatico de mensagem
- dashboard de respostas
- cobranca por excedente de webhook
- suporte a outros tipos de template ou outros canais
- automacoes adicionais apos confirmacao

