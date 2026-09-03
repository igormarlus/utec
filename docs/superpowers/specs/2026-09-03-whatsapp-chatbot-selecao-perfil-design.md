# Selecao de Perfil no Chatbot WhatsApp

## Objetivo

Permitir que um mesmo telefone vinculado a mais de um usuario escolha o perfil de atendimento antes de receber o menu do chatbot.

## Fluxo

1. O webhook recebe uma mensagem de texto ou interacao do WhatsApp.
2. O chatbot busca todos os usuarios elegiveis associados ao telefone, considerando as variacoes brasileiras com/sem `55` e nono digito.
3. Quando existir somente um perfil, o chatbot segue diretamente para o menu desse perfil.
4. Quando houver mais de um perfil distinto, o chatbot envia uma lista interativa com o texto `Como deseja acessar?` e uma opcao para cada perfil disponivel: Paciente, Profissional, Atendente e/ou Administrador.
5. Ao selecionar uma opcao, o chatbot valida que aquele perfil pertence ao telefone e cria uma sessao de selecao por 15 minutos.
6. O chatbot responde com o menu exclusivo do perfil escolhido e mantém esse contexto por 15 minutos.
7. O menu inclui a acao `Trocar perfil`, que limpa a selecao e reexibe o seletor.

## Seguranca

- O payload da escolha contem somente o nome do perfil; o usuario e tenant sao resolvidos novamente no servidor pelo telefone.
- Se houver mais de um usuario do mesmo perfil, o sistema usa o registro mais recente (maior `id`) somente dentro do perfil explicitamente escolhido.
- A escolha nunca libera um perfil que nao esteja associado ao telefone.
- Durante a sessao, mensagens fora de um fluxo de motivo usam o perfil selecionado; a troca exige a acao explicita `Trocar perfil`.

## Persistencia

- A sessao existente `whatsapp_chatbot_sessoes` guarda o perfil, usuario e tenant selecionados e utiliza o TTL atual de 15 minutos.
- Nao sao necessarias novas tabelas ou alteracoes no SQL de sessao.

## Observabilidade

- O log do webhook informa `perfil_status=selecao_necessaria` antes da escolha.
- Apos a escolha, o resultado informa `perfil_status=selecionado` e o perfil escolhido, sem registrar telefone ou texto da mensagem.

## Testes

- Um perfil unico continua abrindo o menu diretamente.
- Paciente e profissional no mesmo telefone exibem o seletor.
- A escolha por paciente abre somente comandos de paciente.
- A escolha por profissional abre somente comandos de profissional.
- Payload de perfil que nao pertence ao telefone e rejeitado e reexibe o seletor.
- Duplicidades dentro do mesmo perfil usam o registro mais recente apenas depois da escolha explicita.
