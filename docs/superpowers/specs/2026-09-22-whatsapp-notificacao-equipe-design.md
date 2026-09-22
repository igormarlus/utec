# Notificação WhatsApp à Equipe (Confirmação/Cancelamento)

## Objetivo

Avisar por WhatsApp o profissional e quem cadastrou o agendamento (o "atendente") assim
que o paciente confirma ou cancela a consulta pelo botão do template de agendamento.
Hoje, essa resposta só gera um aviso interno (sino no topo); este trabalho fecha o
gap registrado em `docs/superpowers/specs/2026-08-31-respostas-whatsapp-notificacoes-design.md`
("Não será enviado WhatsApp ao profissional ou atendente nesta etapa. Esse disparo
depende de template interno aprovado em trabalho posterior.").

## Escopo

- Dois templates novos na Meta, já submetidos para aprovação:
  - `agendamento_confirmado_equipe`
  - `agendamento_cancelado_equipe`
- Novo método público `Whatsapp_agendamento::notificar_equipe($contexto, $acao)`.
- Chamada nova em `Webhooks::processar_resposta_agendamento()`, logo após os avisos
  internos e antes da resposta de texto ao paciente.
- Flag de ativação em `application/config/whatsapp.php`, **ligado por padrão**.
- Reaproveita a coluna `whatsapp_notificacoes.tipo_notificacao` (já existe desde o
  lembrete) com dois valores novos: `equipe_confirmado`, `equipe_cancelado`. Nenhuma
  migração de banco nesta entrega.

### Fora de escopo

- Botões ou fluxo de resposta pelo profissional/atendente — os templates são
  informativos, sem botão. Confirmar/cancelar continua sendo ação exclusiva do
  paciente.
- Colunas configuráveis de nome/idioma de template em `whatsapp_config` — os nomes
  são fixos no código (não são personalizáveis por tenant nesta entrega).
- Múltiplos destinatários além de profissional e quem cadastrou (ex.: toda a equipe
  de uma clínica).

## Templates

Ambos: categoria Utility, idioma `pt_BR`, sem cabeçalho, sem rodapé, sem botão. Corpo
com 3 variáveis (paciente, profissional, data+hora combinada em um único parâmetro).
Estado: **submetidos à Meta em 2026-09-22, aguardando aprovação** (normalmente 1–2
dias úteis).

**`agendamento_confirmado_equipe`:**
```
📅 Boas notícias! O paciente {{1}} confirmou a presença na consulta agendada com {{2}} em {{3}}. A agenda já está atualizada no sistema — digite *menu* para conferir os horários do dia pelo WhatsApp.
```

**`agendamento_cancelado_equipe`:**
```
❌ Atenção: o paciente {{1}} cancelou a consulta que estava marcada com {{2}} em {{3}}. O horário já está livre para um novo agendamento — digite *menu* para conferir sua agenda pelo WhatsApp.
```

`{{1}}` = `paciente_nome`, `{{2}}` = `prestador_nome`, `{{3}}` = data e hora formatadas
juntas (ex.: `25/09/2026 às 14:30`, via `utec_whatsapp_formatar_data_br()` +
`utec_whatsapp_formatar_hora_br()`). A instrução "digite *menu*" reaproveita o
chatbot conversacional já em produção (`Whatsapp_chatbot.php`): qualquer texto que
não seja um comando reconhecido dos perfis `profissional`/`atendente` cai no menu
padrão, então a instrução funciona sem precisar de uma palavra-chave dedicada.

## Configuração (flag de ativação)

`application/config/whatsapp.php`:

```php
$whatsapp_env_notificar_equipe = getenv('WHATSAPP_NOTIFICAR_EQUIPE');
// Notificacao WhatsApp a profissional/atendente apos confirmar/cancelar. Liga por
// padrao; desligar so setando WHATSAPP_NOTIFICAR_EQUIPE=0 no ambiente (por exemplo,
// enquanto os templates ainda nao estiverem aprovados na Meta, para reduzir ruido
// de log de falha).
$config['notificar_equipe_ativo'] = !($whatsapp_env_notificar_equipe === '0' || $whatsapp_env_notificar_equipe === 'false');
```

Padrão ligado (decisão do usuário): o código já sai pronto para funcionar assim que
a Meta aprovar os templates, sem precisar de novo deploy. Enquanto os templates não
estiverem aprovados, a Cloud API rejeita o envio (nome de template desconhecido) —
o erro é apenas logado, sem impacto no restante do fluxo (ver "Erros e segurança").

## Destinatários e dedup

`notificar_equipe($contexto, $acao)` recebe o mesmo `$contexto` já usado pelos avisos
internos (`id_agendamento`, `id_user`, `id_prestador`, ...) e busca o restante dos
dados via `buscar_contexto_agendamento($id_agendamento)`, que passa a incluir
`cad.telefone AS cadastrado_por_telefone` (campo novo nessa consulta; hoje só trazia
`cadastrado_por_nome`).

Os destinatários são resolvidos com a mesma função já usada pelos avisos internos —
`utec_notificacoes_destinatarios_agendamento($id_user, $id_prestador)` — que devolve
os IDs únicos de quem cadastrou e do profissional (um só ID quando são a mesma
pessoa). Para cada ID da lista, o nome/telefone vem do contexto: se o ID bate com
`id_prestador`, usa `prestador_nome`/`prestador_telefone`; senão, usa
`cadastrado_por_nome`/`cadastrado_por_telefone`.

## Fluxo

Em `Webhooks::processar_resposta_agendamento()`, logo após o bloco de avisos internos
(linha ~159) e antes da resposta de texto ao paciente:

```php
// Notificacao WhatsApp para profissional e atendente. Falha aqui nao afeta o
// paciente nem os avisos internos ja registrados.
$this->load->library('whatsapp_agendamento');
$envioEquipe = $this->whatsapp_agendamento->notificar_equipe($contexto, $acao);
log_message('info', '[whatsapp_webhook] Notificacao a equipe: enviados='.(int)$envioEquipe['enviados'].' falhas='.(int)$envioEquipe['falhas'].' agendamento='.$idAgendamento);
```

Dentro de `notificar_equipe()`:

1. Se `notificar_equipe_ativo` for falso ou a config WhatsApp não estiver ativa
   (`utec_whatsapp_config_ativa`), retorna `['enviados' => 0, 'falhas' => 0, 'detalhes' => []]` sem tentar nada.
2. Resolve o nome do template pelo `$acao` via `utec_whatsapp_template_equipe_nome()`;
   ação inválida também é no-op.
3. Busca o contexto completo do agendamento e a lista de destinatários (seção
   acima).
4. Para cada destinatário: normaliza o telefone; se inválido, registra log e loga
   `whatsapp_notificacoes` com erro, segue para o próximo. Senão, valida a cota do
   tenant (`validar_quota_tenant($agendamento, $telefone, $tipo)`, com
   `$tipo = 'equipe_confirmado'`/`'equipe_cancelado'`); se estourada, registra o
   limite e segue. Senão, monta o payload (`utec_whatsapp_componentes_equipe_template()`,
   sem cabeçalho/botão) usando o nome do template resolvido no passo 2, envia e
   grava o log com `tipo_notificacao` correspondente.
5. Devolve o resumo agregado (`enviados`, `falhas`, `detalhes` por destinatário).

`montar_payload()` (hoje fixo em `$config->template_name`) passa a aceitar o nome do
template como parâmetro, com o valor atual (`$config->template_name`) como default —
sem quebrar as chamadas existentes (`notificar_agendamento`, `notificar_lembrete`).

## Cota trial

Cada envio de equipe conta contra o limite de 3 disparos do trial/free
(`utec_whatsapp_politica_limite`), no mesmo padrão do lembrete. Tenant com
assinatura ativa continua ilimitado. Consequência aceita: um único clique do
paciente pode consumir até 2 unidades de cota de uma vez (profissional + atendente,
quando forem pessoas diferentes).

## Erros e segurança

- Nenhuma falha aqui desfaz a confirmação/cancelamento já persistido, os avisos
  internos ou a resposta de texto ao paciente — mesma filosofia de isolamento já
  usada nos dois blocos vizinhos do webhook.
- Template ainda não aprovado pela Meta: a Cloud API retorna erro, registrado em
  log (`error`) e em `whatsapp_notificacoes` (`status_envio = 'erro'`); o restante do
  fluxo segue normalmente.
- `log_message()` usa apenas os níveis existentes no CI3 (`info`/`error`), nunca
  `warning`.
- Telefone inválido ou cota estourada: mesmo tratamento das demais notificações
  WhatsApp do módulo (log + registro do motivo, sem exceção lançada).

## Verificação

- `tests/whatsapp_notificar_equipe_test.php` (puro, sem banco):
  - `utec_whatsapp_template_equipe_nome('confirmar')` → `'agendamento_confirmado_equipe'`;
    `'cancelar'` → `'agendamento_cancelado_equipe'`; outro valor → `''`.
  - `utec_whatsapp_componentes_equipe_template($contexto)` monta só o componente
    `body` com 3 parâmetros (paciente, profissional, data+hora combinada); sem
    `header` nem `buttons` no array retornado.
- Asserções de fonte (grep no estilo de `tests/whatsapp_lembrete_source_test.php`):
  - `Webhooks.php` chama `notificar_equipe`.
  - `config/whatsapp.php` define `notificar_equipe_ativo` com o default ligado
    (ausência de `=== '0'`/`=== 'false'` como única forma de desligar).
  - `Whatsapp_agendamento.php` passa o tipo `equipe_confirmado`/`equipe_cancelado`
    para `validar_quota_tenant`.
- `php -l` em todos os arquivos tocados.
- Smoke manual (após aprovação da Meta): confirmar e cancelar um agendamento de
  teste pelo botão, conferir 2 linhas novas em `whatsapp_notificacoes`
  (`tipo_notificacao = equipe_confirmado` / `equipe_cancelado`) e a chegada das
  mensagens nos dois números de teste.

## Passo de publicação

1. Enviar por FTP os 4 arquivos tocados: `application/config/whatsapp.php`,
   `application/helpers/whatsapp_agendamento_helper.php`,
   `application/libraries/Whatsapp_agendamento.php`, `application/controllers/Webhooks.php`.
2. Nenhuma migração de banco necessária.
3. Quando a Meta aprovar os dois templates, nenhuma ação adicional é necessária —
   o flag já sai ligado. Se quiser desligar temporariamente por qualquer motivo,
   definir `WHATSAPP_NOTIFICAR_EQUIPE=0` no cPanel.
