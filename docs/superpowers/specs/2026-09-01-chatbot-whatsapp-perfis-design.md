# Chatbot WhatsApp por perfis - Design

**Data:** 2026-09-01
**Status:** Proposto e aprovado para planejamento tecnico

## Objetivo

Ampliar o chatbot do WhatsApp, hoje restrito aos botoes de confirmacao e
cancelamento de consultas, para oferecer menus interativos conforme o perfil
do telefone que enviou a mensagem.

O bot deve atender pacientes, profissionais e atendentes/administradores,
preservando os fluxos de confirmacao existentes. Numeros sem cadastro recebem
uma mensagem informativa com o link publico da Utec.

## Perfis

| Perfil | Identificacao | Menu inicial |
| --- | --- | --- |
| Paciente | Telefone vinculado a um agendamento | Proximas consultas, confirmar/cancelar, remarcar, atendimento |
| Profissional | Usuario interno de nivel profissional | Agenda de hoje, agenda de amanha, pendencias, meu plano, suporte |
| Atendente/admin | Usuario interno autorizado que nao seja profissional | Agenda de hoje, pendencias, cancelamentos, meu plano quando aplicavel, suporte |
| Nao cadastrado | Telefone sem vinculo com paciente ou usuario interno | Aviso de nao cadastro e link do site |

O telefone deve ser normalizado antes de qualquer consulta, para que formatos
com ou sem DDI, DDD, espacos e pontuacao possam ser reconhecidos.

Quando houver mais de um vinculo possivel, usuarios internos prevalecem sobre
o perfil de paciente. O nivel de acesso define quais itens internos ficam
visiveis, especialmente o acesso a informacoes de plano.

## Entrada e sessao

Todas as mensagens de texto recebidas fora de um fluxo ativo abrem o menu do
perfil identificado. Respostas de botoes e listas tambem passam pelo mesmo
roteador central.

Uma sessao persistida por telefone controla a conversa e armazena:

- perfil identificado e usuario relacionado, quando houver;
- fluxo e etapa atuais;
- dados temporarios necessarios para concluir a solicitacao;
- data de criacao, ultima atividade e expiracao.

Quando uma etapa solicitar texto livre, por exemplo o motivo de cancelamento
ou uma solicitacao de remarcacao, a proxima mensagem de texto deve ser tratada
como a resposta da etapa. Ela nao reabre o menu. Sessoes expiradas voltam ao
comportamento padrao de exibir o menu.

Os payloads atuais `confirmar_agendamento:<id>` e
`cancelar_agendamento:<id>` continuam aceitos para manter a compatibilidade
com notificacoes ja enviadas.

## Menus e respostas

Os menus devem usar listas e botoes interativos da Meta quando o conteudo
caber nos limites da API. Quando houver muitos resultados, a resposta deve ser
resumida e orientada por botoes de navegacao ou mensagem de texto equivalente.

### Paciente

- **Proximas consultas:** lista de consultas futuras, com data, horario,
  profissional e status.
- **Confirmar ou cancelar:** apresenta consultas pendentes e permite iniciar
  confirmacao, cancelamento ou coleta do motivo do cancelamento.
- **Solicitar remarcacao:** seleciona a consulta e coleta uma mensagem livre
  para encaminhamento ao atendimento.
- **Falar com atendimento:** informa o canal de atendimento definido pelo
  sistema.

### Profissional

- **Agenda de hoje:** lista pacientes, horarios e status de confirmacao,
  usando `✅ confirmado`, `⏳ pendente` e `❌ cancelado`.
- **Agenda de amanha:** mesma visualizacao para o proximo dia util ou dia
  seguinte, conforme regra de agenda existente.
- **Confirmacoes pendentes:** filtra os atendimentos que ainda precisam de
  resposta do paciente.
- **Meu plano:** apresenta vencimento e informacoes permitidas do plano do
  usuario ou clinica.
- **Suporte:** envia a opcao `Fale com o dev` com link WhatsApp para
  `+55 81 98327-6882`.

### Atendente/admin

- **Agenda de hoje:** lista os atendimentos sob sua permissao, com paciente,
  horario e status.
- **Pendencias de confirmacao:** lista consultas sem confirmacao.
- **Cancelamentos do dia:** lista cancelamentos registrados no dia.
- **Meu plano:** exibido somente para niveis que hoje possuem permissao para
  consultar plano.
- **Suporte:** envia a opcao `Fale com o dev` com link WhatsApp para
  `+55 81 98327-6882`.

### Numero nao cadastrado

O bot responde que o numero nao esta cadastrado e envia o link da pagina
principal, obtido da `base_url` da aplicacao. Nenhum dado interno ou de
agendamento deve ser revelado.

## Regras e protecoes

- Validar a assinatura do webhook antes de processar qualquer evento, como ja
  ocorre no fluxo atual.
- Ignorar ou registrar de forma idempotente eventos repetidos da Meta.
- Validar que cada agendamento solicitado pertence ao perfil e ao escopo do
  usuario identificado antes de mostrar ou alterar dados.
- Limitar resultados de agenda e paginar ou resumir quando necessario.
- Registrar mudancas de status, respostas abertas e transicoes de sessao para
  auditoria.
- Oferecer retorno claro para opcoes invalidas e uma forma de voltar ao menu.

## Componentes afetados

- Webhook: extracao de mensagens de texto, botoes e listas; identificacao do
  perfil; roteamento por sessao.
- Modelo WhatsApp: persistencia de sessoes, consultas de perfil e operacoes de
  agenda e plano autorizadas.
- Biblioteca WhatsApp: composicao e envio de mensagens interativas, texto e
  links de suporte.
- Helper WhatsApp: payloads, rotulos, interpretacao de eventos e respostas
  padronizadas.
- Banco de dados: tabela de sessao de chatbot e, se necessario, indices de
  telefone e consultas de agenda.
- Testes: parser do webhook, resolucao de perfis, transicoes de sessao,
  autorizacao, compatibilidade com confirmacao/cancelamento e payloads Meta.

## Fora de escopo desta fase

- Atendimento humano bidirecional dentro da plataforma.
- Alteracao automatica de horario ou profissional pelo WhatsApp.
- Pagamentos, contratacao ou mudanca de plano pelo chatbot.
- Interpretacao por IA de linguagem natural; o texto livre sera usado apenas
  nas etapas que explicitamente o solicitarem.
