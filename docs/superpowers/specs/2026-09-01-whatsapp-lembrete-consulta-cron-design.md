# Lembrete de Consulta por WhatsApp via Cron

## Objetivo

Enviar, uma vez por agendamento, um lembrete pelo WhatsApp para o paciente e para
o profissional cerca de 6 horas antes do horario marcado. O disparo e feito por um
endpoint publico chamado pelo cron do cPanel a cada hora. Reaproveita a conexao,
o webhook e o log ja existentes do fluxo de confirmacao no agendamento (ver
`docs/superpowers/specs/2026-08-25-whatsapp-confirmacao-agendamento-design.md` e
`2026-08-31-respostas-whatsapp-notificacoes-design.md`).

## Escopo

- Novo controller raiz `application/controllers/Cron.php`, metodo `lembrete_whatsapp()`.
- Rota `cron/lembrete-whatsapp` em `application/config/routes.php`.
- Autenticacao por token secreto na querystring (`?token=...`), sem sessao.
- Nova funcao `Whatsapp_agendamento::notificar_lembrete($id_agendamento, $tipo)`.
- Nova coluna `whatsapp_notificacoes.tipo_notificacao` para idempotencia por tipo de disparo.
- Migracao idempotente em `Dev.php`: `adm/dev/migrar_lembrete_whatsapp` (nivel 1).
- Novo arquivo `application/config/whatsapp.php` com o token do cron (env var + fallback).
- Testes puros em `tests/whatsapp_lembrete_test.php`.

### Fora de escopo nesta entrega (anotado em `docs/whatsapp-lembrete-templates-pendente.md`)

- Templates dedicados de lembrete na Meta (texto generico para o paciente e
  template informativo sem botoes para o profissional).
- Colunas `template_lembrete_paciente_name`/`_lang` e
  `template_lembrete_profissional_name`/`_lang` em `whatsapp_config` e os campos
  correspondentes na tela `adm/whatsapp`.
- Multiplas janelas de lembrete (D-1 + manha do dia). Esta entrega usa apenas a
  janela unica de ~6 horas antes.

## Template usado no MVP

Enquanto os templates dedicados nao sao aprovados, os dois lembretes (paciente e
profissional) reutilizam o template atual de confirmacao: `whatsapp_config.template_name`
e `template_lang`, com os mesmos componentes de `utec_whatsapp_componentes_template()`
(header de imagem, 5 parametros de corpo, 2 botoes quick-reply
`confirmar_agendamento:{id}` / `cancelar_agendamento:{id}`).

Consequencia aceita no MVP: o profissional recebe a mesma mensagem com botoes. Se ele
clicar, o webhook trata como resposta do agendamento igual a do paciente. A troca
para um template informativo sem botoes esta na pendencia registrada.

## Dados

### `whatsapp_notificacoes` (ALTER)

- Nova coluna `tipo_notificacao` VARCHAR(30) NOT NULL DEFAULT `'confirmacao'`.
- Valores: `confirmacao` (disparo no agendamento, comportamento atual),
  `lembrete_paciente`, `lembrete_profissional`.
- Novo indice `(id_agendamento, tipo_notificacao)` para a checagem de idempotencia.
- Linhas antigas assumem `confirmacao` pelo default, sem backfill.

O `Whatsapp_model::registrar_log()` passa a gravar `tipo_notificacao` (via
`filtrar_colunas`, entao continua funcionando se a coluna ainda nao existir).
`get_notificacao_por_wamid()` continua sendo a via primaria do webhook: como o
WAMID e unico por mensagem, a resolucao da resposta nao depende do tipo.

### Token do cron

`application/config/whatsapp.php` (novo), no padrao de `config/mercadopago.php`:

```php
$config['cron_token'] = getenv('WHATSAPP_CRON_TOKEN') ?: 'TROCAR_ESTE_TOKEN_LONGO';
```

Carregado com `$this->config->load('whatsapp', TRUE)` no controller do cron.

## Regra de elegibilidade

O cron roda de hora em hora. Em vez de uma janela fixa `[+6h, +7h)`, a selecao usa
"faltam entre 0 e 7 horas para a consulta". Assim, se um run atrasar ou falhar, o
agendamento continua elegivel no run seguinte, e a idempotencia por
`tipo_notificacao` evita duplicidade.

### Lembrete do paciente (`lembrete_paciente`)

Elegivel quando, para o `agendamento`:

1. `status = 0` (pendente; `3` = cancelado fica de fora).
2. `TIMESTAMP(data_agenda, hora_agenda)` entre `NOW()` e `NOW() + INTERVAL 7 HOUR`.
3. Nao existe linha em `whatsapp_notificacoes` com
   `id_agendamento = a.id AND tipo_notificacao = 'lembrete_paciente'`.
4. Nao existe linha em `whatsapp_notificacoes` com
   `id_agendamento = a.id AND status_confirmacao = 'confirmado'` (qualquer tipo).
   Ou seja: se o paciente ja confirmou no disparo do agendamento, nao recebe o lembrete.
5. Paciente com telefone normalizavel (>= 12 digitos apos prefixo 55).

### Lembrete do profissional (`lembrete_profissional`)

Regras 1, 2 e 3 iguais (com `tipo_notificacao = 'lembrete_profissional'`).
Nao depende da confirmacao do paciente (regra 4 nao se aplica): e um aviso de agenda.
Exige `agendamentos.id_prestador > 0` e `usuarios.telefone` do prestador normalizavel.

## Fluxo do cron

1. `GET /cron/lembrete-whatsapp?token=XXX`.
2. Valida o token com `hash_equals`. Divergiu ou vazio: HTTP 403, corpo `forbidden`.
3. Carrega a config ativa do WhatsApp. Ausente ou inativa: responde JSON
   `{ok:true, motivo:"config_inativa", elegiveis:0}` e encerra (mesmo padrao do
   fluxo de agendamento; nao e erro).
4. Seleciona os agendamentos elegiveis para `lembrete_paciente` (query unica com os
   NOT EXISTS acima) e itera:
   - `Whatsapp_agendamento::notificar_lembrete($id, 'lembrete_paciente')`.
   - A funcao valida telefone, checa a cota trial (`validar_quota_tenant`, mesma
     regra dos 3 envios; lembretes contam), monta o payload de template com
     `template_name`/`template_lang` da config, envia pela Cloud API e grava o log
     com `tipo_notificacao = 'lembrete_paciente'` e `status_confirmacao = 'pendente'`.
5. Seleciona os elegiveis para `lembrete_profissional` e itera com
   `notificar_lembrete($id, 'lembrete_profissional')` (mesma mecanica; log com o
   proprio tipo).
6. Erros de um envio (config, telefone, cota, API) sao registrados em log e no
   proprio `whatsapp_notificacoes`, e o loop segue para o proximo agendamento.
7. Resposta final: JSON com `elegiveis_paciente`, `enviados_paciente`,
   `falhas_paciente` e os equivalentes de profissional, para inspecao no e-mail/log
   de cron do cPanel.

O endpoint sempre responde 200 quando o token e valido, mesmo com falhas parciais de
envio, para nao gerar alarme falso no cron.

## Webhook

Nenhuma mudanca. Os botoes do lembrete do paciente carregam o mesmo payload
`confirmar_agendamento:{id}` / `cancelar_agendamento:{id}` ja tratado por
`Webhooks::processar_resposta_agendamento` -> `Whatsapp_model::registrar_resposta_webhook`.
Confirmar ou cancelar pelo lembrete atualiza a agenda e gera os avisos internos
exatamente como no disparo do agendamento. A resolucao por WAMID garante que a
resposta atinja a linha certa mesmo havendo varias linhas para o mesmo agendamento.

## Idempotencia e concorrencia

- Cada envio e checado imediatamente antes do disparo (NOT EXISTS por tipo) e
  registrado logo apos. Reexecucao manual do cron na mesma hora nao reenvia.
- Sem lock explicito entre runs sobrepostos, coerente com o restante do modulo. A
  janela de corrida e pequena e o volume, baixo; no pior caso um agendamento recebe
  o lembrete duas vezes, sem efeito clinico.
- Agendamento cancelado (`status = 3`) ou ja confirmado sai da selecao.

## Falhas e seguranca

- Token invalido: 403, sem processar nada.
- Sem `curl_init` no servidor: cada envio retorna erro registrado; o loop nao quebra.
- Telefone invalido (paciente ou prestador): pula aquele envio, registra
  `status_envio = 'erro'` no log, segue.
- Cota trial atingida: registra `status_envio = 'limite'` (via
  `registrar_limite_atingido`), nao envia.
- O endpoint nao expoe dados de agendamento na resposta, apenas contagens.

## Verificacao

- `tests/whatsapp_lembrete_test.php` (PHP direto, padrao do diretorio `tests/`):
  - `utec_whatsapp_lembrete_dentro_janela($agora, $data, $hora)` — funcao pura que
    diz se a consulta esta a no maximo 7h e no futuro.
  - `utec_whatsapp_lembrete_tipo_valido($tipo)` — aceita apenas os 3 tipos.
  - Idempotencia: dado um conjunto de linhas de log ficticio, a funcao de filtro
    nao reenvia quando ja existe o tipo correspondente.
  - "Ja confirmado" remove o paciente da selecao; nao remove o profissional.
- `php -l` em todos os arquivos PHP alterados/criados.
- Smoke test local: criar agendamento com horario ~6h30 no futuro e `status = 0`,
  abrir `http://localhost/utec/cron/lembrete-whatsapp?token=<token>` (ou `curl`),
  conferir o JSON de contagens e as linhas em `whatsapp_notificacoes` com
  `tipo_notificacao` = `lembrete_paciente` / `lembrete_profissional`. Repetir a
  chamada e confirmar que nada e reenviado.

## Passo de publicacao

1. Rodar `adm/dev/migrar_lembrete_whatsapp` em producao (nivel 1).
2. Definir `WHATSAPP_CRON_TOKEN` no ambiente ou ajustar o fallback em
   `application/config/whatsapp.php` antes do deploy.
3. Enviar por FTP: `application/controllers/Cron.php`,
   `application/config/routes.php`, `application/config/whatsapp.php`,
   `application/libraries/Whatsapp_agendamento.php`,
   `application/models/Whatsapp_model.php`,
   `application/helpers/whatsapp_agendamento_helper.php`,
   `application/controllers/adm/Dev.php`.
4. Cadastrar no cPanel um cron horario:
   `wget -q -O /dev/null "https://utecnologia.com.br/cron/lembrete-whatsapp?token=SEU_TOKEN"`
5. Com o cron no ar, liberar a Task 5 do plano
   `docs/superpowers/plans/2026-08-31-seo-geo-whatsapp-confirmacao-consulta.md`
   (menu, rodape e sitemaps).
6. Depois, executar a pendencia de `docs/whatsapp-lembrete-templates-pendente.md`
   para trocar pelos templates dedicados.
