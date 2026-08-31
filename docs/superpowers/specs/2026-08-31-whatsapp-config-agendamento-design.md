# Configuracao de WhatsApp para Disparo no Agendamento — Design

**Data:** 2026-08-31
**Status:** Aprovado para planejamento
**Escopo:** Adicionar configuracao administravel do WhatsApp no painel admin, criar tela restrita ao nivel 1 e integrar o disparo opcional de template aprovado nos pontos de criacao de agendamento
**Stack:** PHP 7 + CodeIgniter 3 + MySQL + Bootstrap 4 + jQuery

## 1. Objetivo

O sistema ja tem um design aprovado para confirmacao de agendamento via WhatsApp, mas a configuracao ainda nao pode depender de valores fixos em codigo. O objetivo desta entrega e:

- criar uma configuracao editavel pelo admin nivel 1
- permitir troca de numero/remetente e credenciais sem editar arquivos
- identificar os pontos que criam agendamento e adicionar um checkbox, marcado por padrao, para disparar a mensagem aprovada
- manter o agendamento funcionando mesmo quando a configuracao do WhatsApp estiver incompleta ou inativa

## 2. Fora de escopo

- Automacao de lembretes recorrentes
- Multi-configuracao por tenant nesta primeira entrega
- Editor de templates no admin
- Cadastro de varios numeros ativos ao mesmo tempo
- Implementacao de analytics ou dashboard de campanhas

## 3. Decisoes de design

| Aspecto | Decisao |
|---|---|
| Fonte de verdade da configuracao | Tabela propria no banco |
| Acesso | Apenas admin `nivel == 1` |
| Tela de configuracao | Nova rota admin dedicada, ex: `adm/whatsapp` |
| Integracao com agendamento | Checkbox visivel e marcado por padrao nos formularios de criacao |
| Falha de configuracao | Nao impede salvar agendamento |
| Falha de envio | Nao impede salvar agendamento; registrar erro |
| Menu admin | Nova aba/entrada apenas para admin nivel 1 |

## 4. Tabela nova

Tabela proposta: `whatsapp_config`

Modelo de uma linha ativa por vez, com possibilidade futura de historico ou troca de status.

Campos:

- `id`
- `nome_conexao`
- `phone_number_id`
- `waba_id`
- `access_token`
- `app_secret`
- `verify_token`
- `template_name`
- `template_lang`
- `numero_remetente`
- `status`
- `created_at`
- `updated_at`

Regra:

- usar sempre a configuracao `status = 1` mais recente
- se nao houver nenhuma configuracao ativa, o envio fica indisponivel

## 5. SQL inicial

O SQL deve ser entregue de forma direta para injecao manual no banco e tambem pode ser convertido depois em migracao idempotente no `Dev.php`.

```sql
CREATE TABLE IF NOT EXISTS `whatsapp_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome_conexao` VARCHAR(120) NOT NULL DEFAULT 'Configuracao principal',
  `phone_number_id` VARCHAR(80) NOT NULL DEFAULT '',
  `waba_id` VARCHAR(80) NOT NULL DEFAULT '',
  `access_token` TEXT NULL,
  `app_secret` VARCHAR(255) NOT NULL DEFAULT '',
  `verify_token` VARCHAR(255) NOT NULL DEFAULT '',
  `template_name` VARCHAR(120) NOT NULL DEFAULT 'confirmacao_consulta',
  `template_lang` VARCHAR(20) NOT NULL DEFAULT 'pt_BR',
  `numero_remetente` VARCHAR(30) NOT NULL DEFAULT '',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_whatsapp_config_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 6. Tela admin de configuracao

Nova tela para admin nivel 1:

- rota: `adm/whatsapp`
- formulario com os campos da tabela
- acao de salvar atualiza a configuracao ativa
- possibilidade simples de ativar/desativar
- texto de apoio informando que o template deve estar aprovado na Meta

Campos visiveis:

- nome da conexao
- numero remetente
- phone number id
- waba id
- access token
- app secret
- verify token
- template name
- template lang
- status ativo/inativo

## 7. Menu admin

Adicionar uma nova entrada no menu lateral apenas para admin `nivel == 1`.

Rotulo sugerido:

- `WhatsApp`

Subitens sugeridos:

- `Configuracoes`
- opcionalmente depois: `Logs de disparo`

## 8. Pontos de agendamento a alterar

Locais confirmados que criam agendamento hoje:

1. `application/views/adm/atendimento/atendimento.php`
   Fluxo de novo agendamento a partir do paciente/prontuario

2. `application/views/adm/calendario/index.php`
   Modal/formulario rapido de agendamento no calendario

Backends correspondentes:

1. `application/controllers/adm/Atendimento.php`
2. `application/controllers/adm/Calendario.php`

## 9. Comportamento do checkbox

Checkbox:

- label: `Enviar confirmacao pelo WhatsApp`
- marcado por padrao
- visivel para niveis 1 a 4

Quando configuracao estiver ausente ou inativa:

- manter o checkbox visivel
- desabilitar ou apresentar aviso claro na tela
- salvar o agendamento normalmente

Post esperado:

- `enviar_whatsapp_confirmacao = 1` por padrao quando marcado

## 10. Regras de disparo

Fluxo no salvar:

1. salvar o agendamento primeiro
2. verificar se o checkbox veio marcado
3. carregar configuracao ativa do WhatsApp
4. validar se a configuracao esta utilizavel
5. se estiver valida, disparar o template aprovado
6. registrar sucesso ou erro em log proprio

O disparo nao pode bloquear o cadastro do agendamento.

## 11. Persistencia de log

Seguir o design anterior com tabela de log de notificacoes:

- `whatsapp_notificacoes`

Mesmo que a primeira entrega nao implemente todo o webhook, o log do disparo ja deve existir para auditoria.

Campos minimos esperados para log:

- `id`
- `id_agendamento`
- `tenant_id`
- `telefone_destino`
- `wamid`
- `status_envio`
- `erro_detalhe`
- `status_confirmacao`
- `criado_em`
- `respondido_em`

## 12. Riscos e mitigacoes

| Risco | Mitigacao |
|---|---|
| Credenciais sensiveis ficarem expostas no admin | Restringir rota ao nivel 1 e mascarar campos sensiveis quando fizer sentido |
| Falha na API bloquear agenda | Salvar agendamento antes do disparo e tratar erro separadamente |
| Configuracao parcial gerar comportamento confuso | Validar campos obrigatorios antes do envio |
| Dois pontos de agendamento divergirem | Centralizar a regra no backend e reaproveitar validacao |

## 13. Criterios de sucesso

- Existe uma nova tela admin de configuracao do WhatsApp acessivel apenas por `nivel == 1`
- O admin consegue trocar numero e credenciais sem editar codigo
- Os dois pontos atuais de criacao de agendamento exibem checkbox marcado por padrao
- O disparo ocorre somente quando o checkbox estiver marcado e a configuracao estiver valida
- O agendamento continua sendo salvo mesmo que o disparo falhe
- O sistema registra sucesso ou erro do disparo
