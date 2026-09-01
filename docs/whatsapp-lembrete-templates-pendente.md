# Pendencia: templates dedicados do lembrete por WhatsApp

Contexto: o cron de lembrete de consulta (ver
`docs/superpowers/specs/2026-09-01-whatsapp-lembrete-consulta-cron-design.md`) foi
entregue reutilizando o template atual `confirmacao_consulta` para os dois disparos
(paciente e profissional), so para validar o fluxo de ponta a ponta. Esta e a lista
do que falta para trocar pelos templates definitivos.

## 1. Criar e aprovar 2 templates na Meta Business Manager

- **`lembrete_consulta`** (paciente): mesma estrutura do `confirmacao_consulta`
  (header de imagem + 5 parametros de corpo: nome, tipo, data, hora, profissional +
  2 botoes quick-reply). Texto do corpo em tom de lembrete, nao de "consulta
  agendada". Botoes continuam confirmar / cancelar com os payloads
  `confirmar_agendamento:{id}` / `cancelar_agendamento:{id}`.
- **`lembrete_consulta_profissional`** (profissional): template informativo, **sem
  botoes**. Parametros sugeridos: nome do paciente, tipo, data, hora. Sem fluxo de
  resposta (o profissional nao confirma/cancela pelo WhatsApp).

Prazo de aprovacao da Meta: normalmente 1 a 2 dias uteis.

## 2. Colunas novas em `whatsapp_config`

Adicionar via metodo idempotente em `adm/dev/Dev.php` (mesmo estilo de
`migrar_lembrete_whatsapp`):

| Coluna | Tipo | Default |
|--------|------|---------|
| `template_lembrete_paciente_name` | VARCHAR(120) | `'lembrete_consulta'` |
| `template_lembrete_paciente_lang` | VARCHAR(20) | `'pt_BR'` |
| `template_lembrete_profissional_name` | VARCHAR(120) | `'lembrete_consulta_profissional'` |
| `template_lembrete_profissional_lang` | VARCHAR(20) | `'pt_BR'` |

## 3. Tela `adm/whatsapp`

Adicionar os 4 campos no formulario (controller `adm/Whatsapp.php`, view
correspondente e `Whatsapp_model::salvar_configuracao()` no bloco `filtrar_colunas`).
Texto de apoio: "template ja aprovado na Meta".

## 4. Ajustar `Whatsapp_agendamento::notificar_lembrete()`

- Selecionar o nome/idioma do template conforme `$tipo`:
  - `lembrete_paciente` -> `template_lembrete_paciente_name` / `_lang`
  - `lembrete_profissional` -> `template_lembrete_profissional_name` / `_lang`
- Para `lembrete_profissional`, montar componentes sem o bloco de botoes (criar
  `utec_whatsapp_componentes_template_profissional()` ou um parametro no builder
  atual).
- Fallback: se o campo novo estiver vazio, continuar usando `template_name` /
  `template_lang` (comportamento do MVP), para nao quebrar em bases nao migradas.

## 5. Testes

- Atualizar `tests/whatsapp_lembrete_test.php` para cobrir a escolha de template por
  tipo e o builder de componentes sem botoes.

## 6. Opcional / futuro

- Multiplas janelas de lembrete (D-1 e manha do dia) — a landing e os artigos ja
  publicados mencionam "vespera e manha do dia". Hoje o cron faz so a janela de ~6h.
- Se as janelas forem implementadas, `tipo_notificacao` pode virar
  `lembrete_paciente_d1`, `lembrete_paciente_6h` etc., ou ganhar uma coluna
  `janela` separada.
