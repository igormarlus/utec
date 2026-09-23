# Template pendente — agendamento_remarcado_equipe

Aviso por WhatsApp ao profissional e a quem cadastrou a consulta quando o
paciente remarca sozinho pelo chatbot. Enquanto não for aprovado, a
remarcação avisa a equipe só pelo sino (aviso interno).

- **Nome:** `agendamento_remarcado_equipe`
- **Categoria:** Utilidade (Utility)
- **Idioma:** pt_BR
- **Botões:** nenhum
- **Corpo (4 variáveis):**

> O paciente {{1}} remarcou a consulta com {{2}} pelo WhatsApp.
> Antes: {{3}}
> Agora: {{4}}

- **Exemplos para a Meta:** {{1}} Maria Souza · {{2}} Dra. Ana Lima · {{3}} 23/09/2026 as 14:00 · {{4}} 25/09/2026 as 14:30

## Depois da aprovação

1. No cPanel, definir a variável de ambiente `WHATSAPP_NOTIFICAR_REMARCACAO_EQUIPE=1`.
2. Remarcar uma consulta de teste pelo chatbot e conferir o log `whatsapp_notificacoes` com `tipo_notificacao = 'equipe_remarcado'` e `status_envio = 'enviado'`.

Os parâmetros são montados em `utec_whatsapp_componentes_equipe_template($contexto, 'remarcar')`.
