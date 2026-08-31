# WhatsApp de Confirmacao de Agendamento

**Data:** 2026-08-31  
**Projeto:** UTec Saude  
**Status atual:** funcionando em producao com template aprovado na Meta

## 1. Objetivo

Enviar automaticamente uma mensagem de confirmacao pelo WhatsApp no momento em que um agendamento e criado no sistema, sem bloquear o salvamento do agendamento caso a API da Meta falhe.

## 2. Fluxos onde o disparo acontece

Hoje o disparo roda em dois pontos:

1. `adm/atendimento/novo/{id_paciente}`
2. `adm/calendario`

Backends responsaveis:

- `application/controllers/adm/Atendimento.php`
- `application/controllers/adm/Calendario.php`

## 3. Checkbox de envio

Nome do campo no formulario:

```text
enviar_whatsapp_confirmacao
```

Regra:

- marcado: envia `1`
- desmarcado: nao vem no `POST`

Funcao que interpreta:

- `utec_whatsapp_checkbox_marcado()`
- arquivo: `application/helpers/whatsapp_agendamento_helper.php`

## 4. Configuracao administrativa

Tela:

- `adm/whatsapp`

Arquivos:

- `application/controllers/adm/Whatsapp.php`
- `application/models/Whatsapp_model.php`
- `application/views/adm/whatsapp/index.php`

Tabela principal:

```sql
whatsapp_config
```

Campos usados hoje:

- `nome_conexao`
- `numero_remetente`
- `phone_number_id`
- `waba_id`
- `access_token`
- `app_secret`
- `verify_token`
- `template_name`
- `template_lang`
- `status`

Regra atual:

- o sistema usa a configuracao ativa mais recente
- se a configuracao estiver incompleta ou inativa, o agendamento continua sendo salvo e o WhatsApp nao e enviado

## 5. Template aprovado na Meta

Nome atual:

```text
confirmacao_consulta
```

Idioma atual:

```text
pt_BR
```

Categoria recomendada:

```text
UTILITY
```

## 6. Estrutura real do template

O template aprovado esta com estes componentes:

1. `header` com imagem
2. `body` com 5 variaveis
3. `footer` fixo
4. `2 quick reply buttons`

### 6.1 Corpo

```text
Olá, {{1}}.

Seu agendamento foi registrado com sucesso.

Tipo: {{2}}
Data: {{3}}
Horário: {{4}}
Profissional: {{5}}

Se estiver tudo certo, confirme abaixo. Caso não possa comparecer, toque em cancelar para que nossa equipe siga com o atendimento.
```

### 6.2 Variaveis

Ordem obrigatoria:

1. `{{1}}` = nome do paciente
2. `{{2}}` = tipo da consulta/agendamento
3. `{{3}}` = data
4. `{{4}}` = horario
5. `{{5}}` = profissional

### 6.3 Footer

Texto atual:

```text
Para mais informações digite: menu
```

O footer e fixo, entao nao precisa ser enviado no payload.

### 6.4 Botoes

Tipo:

```text
Quick Reply
```

Textos:

1. `Confirmar`
2. `Cancelar`

## 7. Header de imagem

O template aprovado tem `header` com imagem.  
Por isso o payload precisa enviar a imagem junto com o template.

Fallback atual no codigo:

```text
https://utecnologia.com.br/img/logo-w.png
```

Funcao responsavel:

- `utec_whatsapp_header_image_url()`
- arquivo: `application/helpers/whatsapp_agendamento_helper.php`

Se a imagem do header for alterada no futuro, o ideal e tornar essa URL configuravel na tela `adm/whatsapp`.

## 8. Payload enviado para a Meta

O envio e montado em:

- `application/libraries/Whatsapp_agendamento.php`

Componentes enviados hoje:

1. `header` com imagem
2. `body` com 5 parametros de texto
3. botao `quick_reply` indice `0`
4. botao `quick_reply` indice `1`

Resumo da estrutura:

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "55DDDNUMERO",
  "type": "template",
  "template": {
    "name": "confirmacao_consulta",
    "language": {
      "code": "pt_BR"
    },
    "components": [
      {
        "type": "header",
        "parameters": [
          {
            "type": "image",
            "image": {
              "link": "https://utecnologia.com.br/img/logo-w.png"
            }
          }
        ]
      },
      {
        "type": "body",
        "parameters": [
          { "type": "text", "text": "nome do paciente" },
          { "type": "text", "text": "tipo da consulta" },
          { "type": "text", "text": "data" },
          { "type": "text", "text": "horario" },
          { "type": "text", "text": "profissional" }
        ]
      },
      {
        "type": "button",
        "sub_type": "quick_reply",
        "index": "0",
        "parameters": [
          { "type": "payload", "payload": "confirmar_agendamento:123" }
        ]
      },
      {
        "type": "button",
        "sub_type": "quick_reply",
        "index": "1",
        "parameters": [
          { "type": "payload", "payload": "cancelar_agendamento:123" }
        ]
      }
    ]
  }
}
```

## 9. Payload dos botoes

Os botoes nao usam as variaveis `{{1}}...{{5}}`.

Eles enviam `payload` proprio:

- confirmar: `confirmar_agendamento:{id}`
- cancelar: `cancelar_agendamento:{id}`

Funcao responsavel:

- `utec_whatsapp_payload_botao()`

## 10. Arquivos principais da feature

### Backend

- `application/controllers/adm/Atendimento.php`
- `application/controllers/adm/Calendario.php`
- `application/controllers/adm/Whatsapp.php`
- `application/models/Whatsapp_model.php`
- `application/libraries/Whatsapp_agendamento.php`
- `application/helpers/whatsapp_agendamento_helper.php`

### Views

- `application/views/adm/atendimento/atendimento.php`
- `application/views/adm/calendario/index.php`
- `application/views/adm/whatsapp/index.php`
- `application/views/adm/usuarios/new/prontuario.php`

### Menu / rota

- `includes/adm/menu.php`
- `application/config/routes.php`

## 11. Logs e retorno para diagnostico

Hoje o sistema informa o resultado do disparo:

- no prontuario: alerta apos redirecionar
- no calendario: popup simples com a mensagem
- no log do PHP: prefixo `whatsapp_agendamento`

Funcao que traduz o retorno em mensagem legivel:

- `utec_whatsapp_resumo_envio()`

Exemplos de mensagens:

- `WhatsApp enviado com sucesso.`
- `Configuracao do WhatsApp ausente, incompleta ou inativa.`
- `Paciente sem telefone valido para WhatsApp.`
- `Falha ao enviar WhatsApp: ...`

## 12. Erro importante que aconteceu neste projeto

Erro recebido:

```text
(#132012) Parameter format does not match format in the created template
```

Causa encontrada:

- o template aprovado na Meta tinha `header` com imagem e `2 quick reply buttons`
- o payload antigo enviava apenas o `body`

Correcao aplicada:

- incluir `header` com imagem
- incluir os `2` componentes de botao no payload

## 13. Tabela de log

Tabela prevista:

```sql
whatsapp_notificacoes
```

Uso:

- registrar sucesso, erro, `wamid`, telefone destino e futura confirmacao/cancelamento

Observacao:

- se a tabela ainda nao existir, o sistema nao deve quebrar o agendamento

## 14. Regras importantes para futuras alteracoes

Antes de alterar o template na Meta, sempre conferir:

1. Se existe `header`
2. Qual o tipo do `header`:
   - texto
   - imagem
   - documento
   - video
3. Quantas variaveis existem no `body`
4. A ordem exata das variaveis
5. Se existe `footer`
6. Quantos botoes existem
7. Qual o tipo dos botoes:
   - quick reply
   - call to action
8. Idioma exato aprovado
9. Nome exato do template aprovado

Se qualquer um desses pontos mudar, o payload do sistema tambem pode precisar mudar.

## 15. Melhorias futuras sugeridas

1. Tornar configuravel no admin a URL da imagem do header
2. Criar a tabela `whatsapp_notificacoes`, se ainda nao existir
3. Implementar webhook para:
   - confirmar consulta
   - cancelar consulta
4. Atualizar status do agendamento quando o paciente tocar em `Cancelar`
5. Registrar `wamid` e status de resposta do paciente
6. Criar opcao de reenvio manual da confirmacao

## 16. Teste rapido quando algo parar de funcionar

Checklist:

1. Abrir `adm/whatsapp`
2. Confirmar:
   - `template_name`
   - `template_lang`
   - `phone_number_id`
   - `access_token`
   - `status = ativo`
3. Confirmar se o paciente tem telefone valido
4. Criar novo agendamento com checkbox marcado
5. Ler a mensagem exibida na tela
6. Conferir `application/logs` procurando por:

```text
whatsapp_agendamento
```

7. Se a Meta retornar erro, comparar o template aprovado com o payload esperado nesta documentacao
