# Limite de Envios WhatsApp para Trial/Free — Design

**Data:** 2026-08-31  
**Status:** Aprovado para planejamento  
**Escopo:** Limitar o disparo de confirmacao de agendamento por WhatsApp a 3 envios por tenant para operacoes em trial/free, mantendo envio ilimitado para tenants com assinatura ativa  
**Stack:** PHP 7 + CodeIgniter 3 + MySQL + WhatsApp Cloud API

## 1. Objetivo

Hoje o sistema ja dispara a confirmacao de agendamento via WhatsApp no momento da criacao do agendamento. A proxima evolucao e controlar o consumo para tenants sem assinatura ativa.

A regra aprovada e:

- contagem por `tenant/clínica`
- tenants `trial`, `free`, sem assinatura ativa, `pending`, `past_due`, `expired` ou `canceled` podem enviar ate `3` confirmacoes
- tenants com assinatura `active` podem enviar sem limite nesta fase
- o agendamento nunca deve falhar por causa desse bloqueio; apenas o disparo do WhatsApp deve ser impedido

## 2. Decisao principal

A fonte de verdade do consumo sera a tabela:

```sql
whatsapp_notificacoes
```

Motivo:

- reaproveita a auditoria do proprio canal
- permite contar envios por `tenant_id`
- prepara o sistema para limites por plano ou creditos no futuro
- evita misturar configuracao da API com consumo operacional

## 3. Regra de negocio

### 3.1 Unidade de contagem

O limite e por `tenant_id`.

Se varios usuarios da mesma clinica agendarem, todos consomem o mesmo saldo de envios.

### 3.2 Limite atual

- `active` => ilimitado
- `trial` => 3 envios
- `free` => 3 envios
- sem assinatura => 3 envios
- `pending` => 3 envios
- `past_due` => 3 envios
- `expired` => 3 envios
- `canceled` => 3 envios

Observacao:

- `free` pode nao existir hoje como status formal na tabela de assinaturas; para esta entrega, ele representa operacoes sem assinatura ativa equivalente a plano gratuito/nao pago

### 3.3 O que consome quota

Contar apenas envios realmente aceitos pela Meta:

- `status_envio = 'enviado'`

Nao consumir quota quando:

- checkbox do WhatsApp estiver desmarcado
- configuracao estiver ausente/incompleta/inativa
- telefone do paciente for invalido
- a Meta recusar o payload antes de aceitar a mensagem
- ocorrer erro tecnico antes do aceite do envio

## 4. Ponto de bloqueio

O bloqueio deve acontecer dentro da library central:

```text
application/libraries/Whatsapp_agendamento.php
```

Fluxo:

1. salvar agendamento
2. checar checkbox
3. resolver `tenant_id` do agendamento
4. descobrir status da assinatura principal do tenant
5. calcular uso atual em `whatsapp_notificacoes`
6. se estiver acima do limite, abortar apenas o envio
7. retornar mensagem clara ao usuario

## 5. Estrutura tecnica sugerida

### 5.1 Model / consultas

Ampliar `Whatsapp_model` para expor:

- buscar total de envios por tenant
- contar apenas registros `status_envio = 'enviado'`
- opcionalmente retornar o ultimo envio e o total atual

### 5.2 Integracao com SaaS

Reaproveitar o que o projeto ja tem em:

- `application/models/Padrao_model.php`
- `application/models/adm/Saas_model.php`

Especialmente:

- `tenant_id` do usuario/agendamento
- `get_tenant_primary_subscription($tenant_id)`

### 5.3 Library de WhatsApp

Adicionar na `Whatsapp_agendamento` uma camada de politica, algo como:

- resolver tenant do agendamento
- resolver status do plano
- verificar se o tenant tem limite
- verificar uso atual
- decidir se pode ou nao enviar

## 6. Feedback ao usuario

Quando o tenant estiver bloqueado pelo limite, mostrar mensagem clara:

```text
Limite de 3 envios do plano trial/free atingido. Contrate um plano para liberar novos disparos.
```

Esse retorno deve aparecer:

- no prontuario, apos o redirect
- no calendario, no retorno do modal
- nos logs tecnicos, com prefixo `whatsapp_agendamento`

## 7. Persistencia e auditoria

Mesmo quando o bloqueio for por limite, registrar o resultado de forma auditavel.

Sugestao de status para esse caso:

```text
status_envio = 'limite'
```

E detalhe:

```text
erro_detalhe = 'Limite de 3 envios do plano trial/free atingido.'
```

Isso permite:

- diferenciar erro tecnico de bloqueio comercial
- medir quantas tentativas foram barradas por limite
- dar visibilidade futura ao comercial/produto

## 8. Casos de teste esperados

1. tenant `trial` com `0` envios
   - envia normalmente

2. tenant `trial` com `2` envios
   - envia normalmente

3. tenant `trial` com `3` envios
   - nao envia
   - agendamento continua salvo
   - retorna mensagem de limite

4. tenant `active` com `3` ou mais envios
   - continua enviando normalmente

5. tenant sem assinatura
   - usa regra de 3 envios

6. tenant com erro de config/telefone
   - nao consome quota
   - nao deve ser contado como envio

## 9. Fora de escopo desta fase

- limite diferente por plano
- compra de creditos avulsos
- dashboard comercial de consumo
- reenvio manual com politica separada
- franquia mensal com renovacao automatica
- webhook de cobranca de excedente

## 10. Evolucao futura prevista

Esta modelagem deve facilitar uma segunda fase com:

- `limite_por_plano`
- `creditos_avulsos`
- combinacao de limite incluso + excedente
- tela de consumo por tenant
- alerta de proximidade do limite

## 11. Riscos e mitigacoes

| Risco | Mitigacao |
|---|---|
| Tenant sem `tenant_id` claro no agendamento | Resolver fallback pelo paciente, prestador ou usuario que cadastrou |
| Tabela `whatsapp_notificacoes` ausente | Validar antes e informar que a governanca de limite depende do log |
| Contagem incorreta por erro tecnico | Contar apenas `status_envio = 'enviado'` |
| Mudanca futura de politica por plano | Isolar regra de limite em uma funcao/politica dedicada |

## 12. Criterios de sucesso

- o sistema impede o 4o envio para tenants trial/free
- tenants ativos continuam com envio liberado
- o agendamento continua sendo salvo mesmo quando o envio e bloqueado
- a mensagem de bloqueio aparece para o usuario
- o bloqueio fica auditavel em log
- a regra fica centralizada, sem duplicacao entre `Atendimento` e `Calendario`
