# Confirmação de Agendamento via WhatsApp (API oficial Meta) — Design

**Data:** 2026-08-25
**Status:** Aprovado para planejamento

## 1. Objetivo

Ao criar um agendamento, o usuário (nível 1–4) pode marcar a opção **"Enviar confirmação pelo WhatsApp"**. O sistema dispara um template aprovado da Meta (WhatsApp Business Platform / Cloud API) para o paciente, com botões **Confirmar** e **Cancelar**. A resposta do paciente chega via webhook e atualiza o agendamento automaticamente.

Usuários sem plano SaaS ativo (`saas_tenants.status = 1`) podem usar o recurso **gratuitamente até 3 vezes**; depois disso, o envio fica bloqueado até assinarem um plano vigente.

## 2. Fora de escopo

- Lembretes automáticos recorrentes (ex: X horas antes da consulta) — este design cobre apenas o disparo no momento da criação do agendamento.
- Reenvio manual de confirmação para agendamentos já criados sem o checkbox marcado (pode ser adicionado depois, mesma infraestrutura).
- Outros tipos de mensagem via WhatsApp (lembretes de exame, cobrança, etc.) — apenas confirmação de presença.
- Onboarding self-service de templates adicionais — o template inicial é único e fixo (`confirmacao_consulta`).

## 3. Arquitetura

```
[Calendário/Atendimento] → checkbox "Enviar confirmação via WhatsApp"
        ↓ salvar_agendamento()
Padrao_model::whatsapp_confirmacao_allowed($usuario)  → libera ou bloqueia (gratuito/plano)
        ↓ se liberado
Whatsapp_meta::enviar_template(agendamento)  → Graph API (Meta Cloud API)
        ↓ grava log em whatsapp_notificacoes (com wamid da mensagem)
[Paciente recebe mensagem, clica Confirmar/Cancelar]
        ↓
webhooks/whatsapp (novo controller adm/Whatsapp.php) → valida assinatura → localiza agendamento pelo wamid
        ↓
Atualiza whatsapp_notificacoes.status_confirmacao + agendamentos.status (se Cancelar → status=3)
```

Segue o mesmo padrão arquitetural já usado para Mercado Pago (`Mercadopago_saas.php` + rota `webhooks/mercadopago` em `routes.php`): biblioteca dedicada, config centralizada com fallback de env vars, webhook como rota CI3 (nunca um arquivo PHP solto fora do framework).

## 4. Banco de dados

Nova migração idempotente em `application/controllers/adm/Dev.php` (ex: `migrar_whatsapp_confirmacao`), seguindo o padrão de `ensure_column()` já usado nas migrações existentes.

### 4.1 Coluna nova em `usuarios`

- `whatsapp_confirmacoes_gratis_usadas` INT NOT NULL DEFAULT 0 — contador das 3 confirmações gratuitas, gravado no usuário "raiz" da árvore (ver seção 5).

### 4.2 Tabela nova `whatsapp_notificacoes`

| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | |
| `id_agendamento` | INT | FK lógica para `agendamentos.id` |
| `id_usuario_raiz` | INT | usuário dono do contador de gratuidade (ver seção 5) |
| `tenant_id` | INT NULL | tenant no momento do envio, se houver |
| `wamid` | VARCHAR(100) | id da mensagem retornado pela Graph API — chave para casar a resposta do webhook |
| `telefone_destino` | VARCHAR(20) | telefone normalizado enviado |
| `status_envio` | ENUM('enviado','erro') | resultado da chamada à Graph API |
| `erro_detalhe` | TEXT NULL | mensagem de erro da API, se houver |
| `status_confirmacao` | ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente' | resultado do clique do paciente |
| `criado_em` | DATETIME | |
| `respondido_em` | DATETIME NULL | preenchido pelo webhook |

Índice em `wamid` (busca do webhook) e em `id_agendamento`.

## 5. Resolução do "usuário raiz" (dono do contador gratuito)

Nem todo estabelecimento/prestador tem `tenant_id` preenchido (provisionamento SaaS é manual — CLAUDE.md seção 2.4/16). Para que o recurso funcione mesmo sem tenant provisionado, o contador de gratuidade vive em `usuarios`, não em `saas_tenants`.

Algoritmo (`Padrao_model::get_whatsapp_root_user($usuario)`):
1. Parte do usuário logado que está criando o agendamento.
2. Sobe pela cadeia `id_user` (mesma lógica de escopo já usada em `get_scope_user_ids`) até encontrar um usuário de nível 2 (Estabelecimento), ou um nível 3 sem `id_user` pai (prestador independente).
3. Esse é o "usuário raiz" — nele que o contador é lido/incrementado, e ele que o gate de plano (`tenant_id`) consulta.

## 6. Gate de envio

`Padrao_model::whatsapp_confirmacao_allowed($usuario_raiz)` retorna `['permitido' => bool, 'motivo' => string]`:

1. Resolve o `tenant_id` do usuário raiz. Se existir tenant e `saas_tenants.status = 1` → **permitido**, sem consumir contador (plano vigente = uso ilimitado).
2. Senão, se `whatsapp_confirmacoes_gratis_usadas < 3` → **permitido**; ao confirmar o envio com sucesso, incrementa o contador em 1.
3. Senão → **bloqueado**, motivo `"limite_gratuito_atingido"`.

### 6.1 UX no formulário de agendamento

- Checkbox "Enviar confirmação pelo WhatsApp" visível para níveis 1–4.
- Se bloqueado por limite: checkbox aparece **desabilitado**, com texto auxiliar "Você usou suas 3 confirmações gratuitas — assine um plano para continuar" e link para `adm/saas`.
- O estado de habilitado/desabilitado é calculado no carregamento da tela (Calendário/Atendimento), consultando `whatsapp_confirmacao_allowed()` para o usuário logado.

## 7. Envio da mensagem

Biblioteca `application/libraries/Whatsapp_meta.php`, espelhando `Mercadopago_saas.php`:

- `enviar_template_confirmacao($agendamento, $paciente, $prestador, $estabelecimento_nome)`:
  - Monta payload da Graph API: `POST https://graph.facebook.com/v20.0/{WHATSAPP_PHONE_NUMBER_ID}/messages`
  - Template: nome `confirmacao_consulta`, idioma `pt_BR`, variáveis `[nome_paciente, nome_profissional, nome_clinica, data_hora_formatada]`
  - Telefone normalizado a partir de `usuarios.telefone` (mesmo padrão `55DDDNUMERO` já usado no projeto)
  - Grava linha em `whatsapp_notificacoes` com o `wamid` retornado (sucesso) ou motivo de erro (falha)

Chamada é **síncrona**, dentro do fluxo de `salvar_agendamento()`. Se a chamada à Graph API falhar, o agendamento **é salvo normalmente** — a falha de envio não bloqueia o fluxo clínico, apenas fica registrada em `whatsapp_notificacoes.status_envio = 'erro'`.

## 8. Template Meta (a ser criado e submetido para aprovação)

- **Nome:** `confirmacao_consulta`
- **Categoria:** UTILITY
- **Idioma:** pt_BR
- **Corpo:** `Olá {{1}}, sua consulta com {{2}} na {{3}} está marcada para {{4}}. Você confirma sua presença?`
  - `{{1}}` nome do paciente · `{{2}}` nome do profissional · `{{3}}` nome do estabelecimento/clínica · `{{4}}` data e hora formatadas (ex: `28/08/2026 às 14:30`)
- **Botões (Quick Reply, fixos no template — não editáveis depois de aprovado):** `Confirmar` / `Cancelar`

Submissão feita pelo usuário no Meta Business Manager; aprovação pode levar minutos a ~24h.

## 9. Webhook — recepção da resposta do paciente

Novo controller `application/controllers/adm/Whatsapp.php`, rota `webhooks/whatsapp` em `application/config/routes.php` (mesmo padrão de `webhooks/mercadopago`).

### 9.1 Verificação (`GET webhooks/whatsapp`)

Responde ao desafio de verificação da Meta: confere `hub.verify_token` contra `WHATSAPP_VERIFY_TOKEN` e retorna `hub.challenge` em texto puro se bater.

### 9.2 Evento (`POST webhooks/whatsapp`)

1. Valida `X-Hub-Signature-256` (HMAC-SHA256 do corpo bruto com `WHATSAPP_APP_SECRET`). Requisição sem assinatura válida é rejeitada (HTTP 403).
2. Extrai do payload o botão clicado (`button.text` ou `button.payload`) e o `context.id` (wamid da mensagem original que o paciente respondeu).
3. Busca em `whatsapp_notificacoes` a linha com esse `wamid`.
4. Se não encontrar → loga e responde 200 (evita retries infinitos da Meta).
5. Se encontrar:
   - Atualiza `status_confirmacao` (`confirmado` ou `cancelado`) e `respondido_em`.
   - Se `cancelado` → `UPDATE agendamentos SET status = 3 WHERE id = {id_agendamento}` (mesmo valor de "cancelado" já usado em `Atendimento.php`).
   - Se `confirmado` → não altera `agendamentos.status` (fluxo clínico segue normal; a confirmação é só informativa/visível na agenda).
6. Sempre responde HTTP 200 rapidamente (requisito da Meta).

## 10. Configuração

`application/config/whatsapp.php`, seguindo o padrão de `mercadopago.php` — lê de variáveis de ambiente com fallback:

| Variável | Valor conhecido |
|---|---|
| `WHATSAPP_APP_ID` | `1605768724534528` |
| `WHATSAPP_PHONE_NUMBER_ID` | `1131045753426154` |
| `WHATSAPP_WABA_ID` | `1260790109375352` |
| `WHATSAPP_ACCESS_TOKEN` | *(a gerar — System User token permanente)* |
| `WHATSAPP_APP_SECRET` | *(a obter no App Dashboard)* |
| `WHATSAPP_VERIFY_TOKEN` | *(a definir)* |
| `WHATSAPP_TEMPLATE_NAME` | `confirmacao_consulta` |
| `WHATSAPP_TEMPLATE_LANG` | `pt_BR` |

Nenhum desses valores (especialmente token e app secret) é commitado em texto plano no repositório — mesma prática já adotada para as credenciais do Mercado Pago.

## 11. Segurança

- Assinatura HMAC do webhook validada em toda requisição `POST` (seção 9.2.1).
- Verify token na etapa de handshake `GET`.
- Token de acesso e app secret apenas via variável de ambiente.
- Telefone do paciente sempre lido de `usuarios.telefone` (nunca aceito por parâmetro externo).

## 12. Testes / verificação

- Criar agendamento com checkbox marcado, usuário raiz com 0 envios usados → mensagem enviada, `whatsapp_notificacoes` com 1 linha, contador vai para 1.
- Repetir até a 4ª tentativa sem plano ativo → checkbox aparece desabilitado antes mesmo de tentar salvar.
- Provisionar tenant com `status = 1` para o usuário raiz → checkbox liberado mesmo com contador em 3.
- Simular webhook de "Confirmar" e "Cancelar" com assinatura válida → `status_confirmacao` atualizado; no caso de cancelar, `agendamentos.status = 3`.
- Simular webhook com assinatura inválida → resposta 403, nenhuma alteração no banco.
- Simular webhook com `wamid` desconhecido → resposta 200, nenhuma alteração, sem erro fatal.
