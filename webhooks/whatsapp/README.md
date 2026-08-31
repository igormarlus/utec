# WhatsApp Webhook

Endpoint publico para cadastrar na Meta:

`https://utecnologia.com.br/webhooks/whatsapp`

Configuracao necessaria em `adm/whatsapp`:

- `Verify Token`: deve ser igual ao informado na Meta.
- `App Secret`: segredo do aplicativo Meta usado para validar cada POST.

O webhook processa:

- status de envio (`sent`, `delivered`, `read` e `failed`) pelo `wamid`;
- botoes `Confirmar` e `Cancelar` do agendamento.

O campo `messages` deve estar inscrito no painel Webhooks do aplicativo Meta e o WABA deve estar associado ao aplicativo.
