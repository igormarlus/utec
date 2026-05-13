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

Hoje o sistema ja trabalha em 2 formatos:

- onboarding interno assistido em `https://utecnologia.com.br/adm/saas`
- onboarding publico inicial em `https://utecnologia.com.br/assinar`

Isso significa que o tenant pode nascer:

- manualmente, pelo painel administrativo
- automaticamente, pela pagina publica de assinatura

## Controle de acesso ao modulo SaaS

Agora o acesso interno ao modulo SaaS depende tambem do campo `saas` na tabela `usuarios`.

Regra pratica:

- usuario com `saas = 1` pode visualizar opcoes comerciais/SaaS no painel
- usuario com `saas = 0` nao visualiza menu, atalhos nem pagina interna de SaaS
- a liberacao e manual, pensada para usuarios que querem revender ou operar o produto como SaaS

Na pratica, isso isola o modulo SaaS da maioria dos usuarios e deixa a revenda habilitada apenas para perfis liberados manualmente.

## Fluxo atual do tenant

Hoje existem 2 fluxos validos para criar tenant.

## Fluxo 1: interno pelo painel SaaS

### 1. Escolher o responsavel base

O fluxo parte de um usuario existente no sistema, normalmente nivel `2` ou `3`, e com acesso SaaS liberado quando for necessario operar a revenda.

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

## Fluxo 2: publico pela pagina `assinar`

Na pagina publica:

- `https://utecnologia.com.br/assinar`

o responsavel da clinica informa:

- nome do responsavel
- nome da clinica
- e-mail principal
- telefone
- documento
- tipo de operacao
- plano
- senha inicial

Quando envia o formulario, o sistema:

- cria o usuario owner automaticamente
- cria o tenant
- cria a assinatura inicial
- cria o primeiro ciclo de cobranca
- tenta abrir o checkout do Mercado Pago

Se o Mercado Pago estiver pronto no servidor, o fluxo segue direto para o checkout.

Se o checkout ainda nao abrir, o tenant mesmo assim fica criado e a contratacao pode continuar de forma assistida.

## Vinculo da equipe com o tenant

Depois do provisionamento interno, o sistema propaga o `tenant_id` para a arvore de usuarios do responsavel principal.

Ou seja, os usuarios ligados a esse dono passam a carregar:

- `tenant_id`
- `tenant_role`
- `onboarding_status`

No fluxo publico, o owner ja nasce com essas informacoes, e os proximos cadastros podem herdar esse tenant.

Na pratica, isso faz a clinica passar a existir como uma operacao separada dentro do SaaS.

## Assinatura

Cada tenant pode ter uma ou mais assinaturas, mas hoje o fluxo principal trabalha com uma assinatura principal por tenant.

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

## Ciclos de cobranca

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

## Como os planos entram nisso

Hoje o tenant sempre depende de um plano para nascer.

Ou seja:

- sem plano, nao existe assinatura
- sem assinatura, o tenant nao entra no fluxo comercial

Os planos sao cadastrados em:

- `adm/produtos`

Eles definem:

- nome comercial
- valor
- ciclo
- trial
- setup fee
- quantidade de profissionais
- quantidade de colaboradores
- quantidade de pacientes
- se o plano aparece publicamente ou nao

## Como criar os planos

Hoje existem 2 jeitos:

### 1. Cadastro manual

Voce acessa:

- `adm/produtos`

e preenche os campos normalmente.

### 2. Seed automatico com base comercial inicial

Foi criada a rota:

- `adm/dev/seed_planos_saas_comerciais`

Ela gera uma base inicial de planos SaaS sugeridos para o produto.

Tambem existe um botao dentro de `adm/produtos`:

- `Criar planos sugeridos`

## Planos sugeridos atuais

A base inicial criada hoje segue este raciocinio:

- `Solo Start`
  Entrada para autonomo ou consultorio pequeno
- `Clinica Essencial`
  Melhor equilibrio entre preco e estrutura
- `Clinica Pro`
  Crescimento com equipe maior e mais pacientes
- `Enterprise`
  Negociacao consultiva, normalmente nao publico

Esses planos foram posicionados para ficar proximos do mercado brasileiro atual, mas respeitando o que o sistema ja entrega hoje.

## Controle operacional do tenant

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

Hoje, sim.

Atualmente existe a rota publica:

- `/assinar`

Na pratica ela ja faz:

- exibicao dos planos publicados
- captura dos dados da clinica
- criacao automatica do usuario owner
- criacao automatica do tenant
- criacao automatica da assinatura
- tentativa de redirecionamento para checkout

Importante:

- esse fluxo publico continua separado da liberacao interna por `usuarios.saas`
- o campo `saas` controla o painel comercial interno e nao impede o funcionamento da pagina publica `/assinar`

Entao o sistema ja saiu da fase de provisionamento 100% manual.

## Entao como a clinica entra hoje?

Hoje o fluxo real pode ser um destes:

1. Fluxo interno:
   Time interno acessa `adm/saas`, provisiona o tenant e gera o checkout.
2. Fluxo publico:
   A propria clinica acessa `assinar`, escolhe o plano, cria o owner e segue para a contratacao.

## O que ainda nao esta totalmente fechado no onboarding publico

Apesar de o link publico existir, ele ainda esta em fase inicial.

Ainda faltam evolucoes importantes como:

- confirmacao por e-mail
- recuperacao de senha comercial mais amigavel
- pos-pagamento guiado
- ativacao mais inteligente por webhook
- pagina publica ainda mais comercial
- primeiro acesso guiado da clinica

## O que falta para amadurecer o onboarding comercial

O link publico ja existe.

O que falta agora e amadurecer esse onboarding para um nivel comercial mais forte.

O fluxo ideal continua sendo:

1. Pagina publica de planos
2. Formulario de cadastro da clinica
3. Criacao automatica do usuario owner
4. Criacao automatica do tenant
5. Criacao automatica da assinatura
6. Redirecionamento imediato para checkout Mercado Pago
7. Ativacao automatica apos webhook
8. Primeiro acesso guiado da clinica

## Recomendacao pratica

Hoje o modulo SaaS ja serve para:

- operacao assistida
- onboarding interno
- onboarding publico inicial
- cobranca inicial

Ele ja entrou em autosservico, mas ainda nao esta totalmente maduro como onboarding comercial completo.

Se a meta for alugar sem intervencao manual, o proximo passo comercial mais importante e criar:

- pos-pagamento guiado
- ativacao automatica por webhook
- jornada inicial da clinica
- recuperacao de senha e confirmacao por e-mail

## Resumo direto

- `tenant` e a conta SaaS da clinica dentro da plataforma
- hoje ele pode ser criado manualmente em `adm/saas` ou publicamente em `assinar`
- o painel interno de SaaS so aparece para usuarios com `usuarios.saas = 1`
- o plano e a base do contrato comercial do tenant
- o sistema ja possui seed automatico para planos SaaS sugeridos
- o checkout do Mercado Pago ja pode nascer no fluxo publico ou no fluxo interno
- o proximo passo para vender em escala e amadurecer o pos-pagamento e a ativacao automatica
