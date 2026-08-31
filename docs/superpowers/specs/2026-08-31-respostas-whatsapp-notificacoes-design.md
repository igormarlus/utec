# Respostas WhatsApp e Notificacoes de Agendamento

## Objetivo

Depois de o paciente clicar em Confirmar ou Cancelar no template de agendamento, registrar a resposta sem bloquear o fluxo clinico, responder o paciente por texto e avisar internamente quem criou a marcacao e o profissional responsavel.

## Escopo

- Confirmar mantem `agendamentos.status` inalterado e atualiza `whatsapp_notificacoes.status_confirmacao` para `confirmado`.
- Cancelar atualiza `agendamentos.status` para `3` e `whatsapp_notificacoes.status_confirmacao` para `cancelado` na mesma transacao.
- O paciente recebe um texto comum pelo WhatsApp apos o clique, dentro da janela de atendimento de 24 horas aberta pela propria interacao.
- Quem marcou (`agendamentos.id_user`) e o profissional (`agendamentos.id_prestador`) recebem uma notificacao interna cada. Quando forem a mesma pessoa, somente uma notificacao sera criada.
- O sistema nao cria cancelamento, confirmacao ou pendencia por ausencia de clique. Agendamentos sem resposta continuam operacionais.
- Agenda, prontuario e relatorio operacional exibem a resposta quando existir; ausencia de resposta aparece como pendente ou sem retorno, sem impedir acoes.
- Nao sera enviado WhatsApp ao profissional ou atendente nesta etapa. Esse disparo depende de template interno aprovado em trabalho posterior.

## Dados

`whatsapp_notificacoes` continua sendo a fonte de verdade da resposta do paciente:

- `status_confirmacao`: `pendente`, `confirmado` ou `cancelado`.
- `respondido_em`: momento do clique.
- `telefone_destino`: telefone usado para a resposta automatica.

A tabela criada `notificacoes_usuarios` guarda o aviso interno por destinatario:

- `id_usuario_destino`, `id_agendamento`, `id_whatsapp_notificacao` e `tenant_id` definem destinatario e contexto.
- `tipo` sera `whatsapp_agendamento_confirmado` ou `whatsapp_agendamento_cancelado`.
- `lida` e `lida_em` permitem contador no topo e leitura individual.
- A chave unica por destinatario, notificacao WhatsApp e tipo torna o processamento idempotente em reenvios da Meta.

## Fluxo do Webhook

1. A Meta envia o clique autenticado ao endpoint `webhooks/whatsapp`.
2. O parser identifica os formatos `interactive.button_reply.id` e `button.payload`.
3. O `Whatsapp_model` localiza a notificacao pelo WAMID e verifica se a resposta ainda esta pendente.
4. A primeira transicao grava o status e, no cancelamento, altera o agendamento na mesma transacao. Reenvios posteriores nao geram nova resposta nem novos avisos.
5. Apos a persistencia, uma biblioteca WhatsApp envia um texto comum ao telefone do paciente. Falha nesse envio e registrada em log, mas nao desfaz a resposta recebida ou o cancelamento.
6. O model cria os avisos internos para os IDs distintos de criador e profissional.

Textos iniciais:

- Confirmar: `Recebemos sua confirmacao. Sua consulta permanece agendada. Em caso de necessidade, entre em contato com a clinica.`
- Cancelar: `Recebemos sua solicitacao de cancelamento. Nossa equipe esta a disposicao para auxiliar em um novo agendamento.`

## Exibicao no Sistema

- O sino em `includes/adm/top.php` consulta as notificacoes nao lidas do usuario logado e mostra titulo, resumo e link para o agendamento. Abrir o item o marca como lido antes do redirecionamento.
- A agenda em `application/views/adm/usuarios/new/atendimentos.php` exibe uma etiqueta de resposta WhatsApp e a data do retorno na linha do agendamento.
- O prontuario em `application/views/adm/usuarios/new/prontuario.php` mostra a resposta recebida na linha do tempo do respectivo agendamento.
- Os filtros e acoes existentes continuam funcionando mesmo sem registro em `whatsapp_notificacoes`.

## Falhas e Seguranca

- O webhook continua validando `X-Hub-Signature-256` com o App Secret salvo na configuracao ativa.
- Falhas de envio da resposta ao paciente sao registradas em `application/logs`; nao revertem dados clinicos.
- Todos os avisos internos usam o usuario destinatario como criterio de leitura, sem expor eventos de outros profissionais.
- O controller que abre uma notificacao valida o destinatario contra a sessao antes de marca-la como lida.

## Verificacao

- Testar parser de ambos os formatos de botao.
- Testar primeira confirmacao, primeiro cancelamento e reenvio do mesmo webhook.
- Testar criador igual ao profissional e criador diferente do profissional.
- Testar falha de resposta ao paciente sem alterar o status gravado.
- Testar contador, leitura e visibilidade de notificacoes por usuario.
- Testar indicadores de confirmado, cancelado e sem resposta na agenda e prontuario.
