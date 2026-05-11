# Tenant SaaS

## O que e um tenant

No sistema, `tenant` e a conta SaaS de uma clinica, consultorio ou profissional.

Ele representa o cliente pagante dentro da plataforma e agrupa:

- dados comerciais da clinica
- usuario responsavel principal
- assinatura do plano
- ciclos de cobranca
- equipe vinculada
- status operacional de acesso

Em termos simples:

- `usuario` = uma pessoa dentro do sistema
- `tenant` = a empresa ou operacao que aluga o sistema
- `assinatura` = o contrato/plano dessa operacao

## Como funciona hoje

Hoje o fluxo nao e publico nem automatico.

O cadastro de um tenant acontece de forma interna, pelo painel administrativo:

- `https://utecnologia.com.br/adm/saas`

Dentro dessa area, um administrador provisiona manualmente a clinica.

## Fluxo atual do tenant

### 1. Escolher o responsavel base

O fluxo parte de um usuario existente no sistema, normalmente nivel `2` ou `3`.

Esse usuario vira o dono operacional do tenant:

- campo interno: `id_owner_user`
- papel no tenant: `owner`

### 2. Provisionar a clinica

Na tela `adm/saas`, o admin informa:

- nome comercial do tenant
- tipo: `clinica`, `consultorio` ou `profissional`
- plano
- ciclo de cobranca
- valor recorrente
- trial
- setup fee
- contato comercial
- gateway

Quando salva, o sistema cria:

- 1 registro em `saas_tenants`
- 1 registro em `saas_subscriptions`
- 1 ciclo inicial em `saas_subscription_cycles`
- 1 evento em `saas_billing_events`

### 3. Vincular a equipe ao tenant

Depois do provisionamento, o sistema propaga o `tenant_id` para a arvore de usuarios do responsavel principal.

Ou seja, os usuarios ligados a esse dono passam a carregar:

- `tenant_id`
- `tenant_role`
- `onboarding_status`

Na pratica, isso faz a clinica passar a existir como uma operacao separada dentro do SaaS.

### 4. Gerar a assinatura

Cada tenant pode ter uma ou mais assinaturas, mas hoje o fluxo principal trabalha com uma assinatura ativa por tenant.

A assinatura guarda:

- plano contratado
- valor
- ciclo
- proxima cobranca
- status
- integracao com gateway

Status mais comuns:

- `trial`
- `pending`
- `active`
- `past_due`
- `paused`
- `canceled`

### 5. Cobrar e acompanhar os ciclos

O tenant tambem possui ciclos de cobranca.

Cada ciclo representa um periodo faturavel, com:

- referencia
- vencimento
- valor devido
- valor pago
- status

Exemplos:

- `pending` = aguardando pagamento
- `trial` = em periodo de teste
- `paid` = pago
- `past_due` = vencido

### 6. Controlar o acesso da clinica

O status operacional do tenant depende da assinatura.

Hoje a regra pratica e:

- `active`, `trial` e `pending` mantem o tenant liberado
- `past_due`, `paused` e `canceled` podem suspender a operacao

Quando o tenant fica suspenso:

- `saas_tenants.status` vai para inativo
- usuarios vinculados recebem `onboarding_status = bloqueado`
- o sistema redireciona para `adm/saas/bloqueado`

## O que aparece na tela do tenant

Ao abrir:

- `adm/saas/tenant/{id}`

voce esta vendo 4 blocos principais:

### 1. Resumo do tenant

Mostra:

- responsavel
- status
- contato
- vencimento

### 2. Assinaturas

Mostra:

- plano
- status da assinatura
- ciclo
- valor
- proxima cobranca

Acoes disponiveis:

- `Gerar checkout MP`
- `Sincronizar MP`
- `Abrir link`

Observacao:

Esses botoes so funcionam quando o Mercado Pago estiver publicado e configurado no servidor.

### 3. Equipe vinculada

Mostra os usuarios ligados ao tenant, com:

- nome
- nivel
- papel
- onboarding

### 4. Ciclos de cobranca

Mostra:

- referencia
- status
- valor

Se o usuario for admin, tambem aparece:

- `Registrar pagamento`

## Existe link publico para a clinica se cadastrar sozinha?

Hoje, nao.

Atualmente nao existe uma rota publica como:

- `/assinar`
- `/cadastro-clinica`
- `/trial`

nem um fluxo automatico que faca tudo sozinho:

- cadastro da clinica
- criacao do usuario owner
- escolha do plano
- checkout
- ativacao automatica do tenant

O que existe hoje e:

- area administrativa interna em `adm/saas`
- provisionamento manual pelo time interno
- checkout Mercado Pago gerado depois que a assinatura ja existe

## Entao como a clinica entra hoje?

Hoje o fluxo real e este:

1. O time interno cria ou escolhe o usuario responsavel.
2. O admin acessa `adm/saas`.
3. O admin provisiona o tenant manualmente.
4. O sistema cria a assinatura e o primeiro ciclo.
5. O admin gera o checkout do Mercado Pago.
6. A clinica paga.
7. O tenant segue ativo e operacional.

## O que falta para existir um link publico de onboarding

Para existir um link publico real, ainda precisamos construir uma fase de onboarding comercial.

O fluxo ideal seria:

1. Pagina publica de planos
2. Formulario de cadastro da clinica
3. Criacao automatica do usuario owner
4. Criacao automatica do tenant
5. Criacao automatica da assinatura
6. Redirecionamento imediato para checkout Mercado Pago
7. Ativacao automatica apos webhook
8. Primeiro acesso guiado da clinica

## Recomendacao pratica

Hoje o modulo SaaS ja serve para operacao assistida, onboarding interno e cobranca inicial.

Ele ainda nao esta pronto como autosservico publico.

Se a meta for alugar sem intervencao manual, o proximo passo comercial mais importante e criar:

- landing publica de assinatura
- onboarding self-service
- criacao automatica do tenant
- ativacao automatica por webhook

## Resumo direto

- `tenant` e a conta SaaS da clinica dentro da plataforma
- hoje ele e criado manualmente pelo painel `adm/saas`
- nao existe ainda um link publico onde o usuario se cadastra e comeca sozinho
- o checkout do Mercado Pago ja pode existir depois que o tenant foi provisionado
- o proximo passo para vender em escala e criar onboarding publico automatizado
