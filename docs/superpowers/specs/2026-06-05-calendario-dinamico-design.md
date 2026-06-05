# Calendário Dinâmico — Design Spec

**Data:** 2026-06-05
**Projeto:** UTecnologia Saúde
**Rota:** `adm/calendario`
**Status:** Aprovado para implementação

---

## 1. Visão Geral

Nova view de calendário dinâmico para visualização e gerenciamento de agendamentos clínicos. Complementa a "Agenda clínica" existente (`adm/atendimento`), que é uma lista tabular filtrada por dia. O calendário oferece visão temporal mensal/semanal/diária com criação e edição de agendamentos diretamente pelo clique.

**Acessível por:** níveis 1, 2, 3 e 4 (não pacientes).
**Atalhos:** menu lateral (seção "Operação Clínica") + top bar.

---

## 2. Arquivos Envolvidos

| Tipo | Caminho |
|------|---------|
| Controller | `application/controllers/adm/Calendario.php` |
| View | `application/views/adm/calendario/index.php` |
| Menu | `includes/adm/menu.php` (adicionar item) |
| Top bar | `includes/adm/top.php` (adicionar atalho) |
| FullCalendar JS | `bower_components/fullcalendar/dist/fullcalendar.min.js` (já disponível) |
| FullCalendar CSS | `bower_components/fullcalendar/dist/fullcalendar.min.css` (já disponível) |
| Locale PT-BR | `bower_components/fullcalendar/dist/locale/pt-br.js` (já disponível) |

---

## 3. Controller — `Calendario.php`

### 3.1 Construtor

Idêntico ao padrão dos controllers `adm/`: carrega `session`, helpers `form`/`url`, models `adm/usuarios_model`, `padrao_model`, chama `verSession()`.

### 3.2 Actions

#### `Index()` — GET

Renderiza a view. Passa para a view:

- `$dados['dd']` — usuário logado
- `$dados['nivel']` — nível do usuário
- `$dados['prestadores']` — lista de prestadores visíveis no escopo (para o filtro lateral e para o modal de criação)
- `$dados['scope_sql']` — IDs do escopo (usados via JS apenas para saber se o usuário tem filtro de profissional)

Não carrega agendamentos aqui — o FullCalendar faz fetch via AJAX.

#### `eventos()` — GET, retorna JSON

Endpoint AJAX chamado pelo FullCalendar com `?start=YYYY-MM-DD&end=YYYY-MM-DD`.

Aplica o mesmo escopo de `Atendimento::Index()`:
- Usa `get_scope_user_ids()` e `get_visible_prestador_ids()`
- Query em `agendamentos` com JOIN em `usuarios` (paciente) e `usuarios` (prestador)
- Filtro por `data_agenda BETWEEN :start AND :end`
- Suporte a filtro opcional `?id_prestador=X` (validado contra escopo)

Retorna array JSON no formato FullCalendar:
```json
[
  {
    "id": 42,
    "title": "Fábio Ramos",
    "start": "2026-06-05T14:00:00",
    "end": "2026-06-05T15:00:00",
    "extendedProps": {
      "paciente_id": 15,
      "paciente_nome": "Fábio Ramos",
      "prestador_id": 3,
      "prestador_nome": "Dr. Silva",
      "tipo": "consulta",
      "status": 0,
      "hora": "14:00",
      "data": "2026-06-05"
    },
    "backgroundColor": "#dbeafe",
    "borderColor": "#93c5fd",
    "textColor": "#1e40af"
  }
]
```

#### `salvar_agendamento()` — POST

Cria novo agendamento. Valida:
- `id_paciente` pertence ao escopo
- `id_prestador` pertence ao escopo
- `data_agenda` formato `Y-m-d`
- `hora_agenda` formato `H:i`
- `tipo` em lista permitida

INSERT em `agendamentos` com `status = 0`, `id_user` = ID do prestador selecionado.

Retorna JSON `{"success": true, "id": 99}` ou `{"success": false, "error": "mensagem"}`.

---

## 4. View — `index.php`

### 4.1 Estrutura HTML

Segue o padrão de `atendimentos.php`:
- Inclui `includes/adm/search.php`, `includes/adm/menu.php`, `includes/adm/top.php`
- Breadcrumb: Painel → Calendário
- CSS próprio em `<style>` inline na view

### 4.2 Layout

```
┌─ page-header (gradient verde escuro) ─────────────────────────────┐
│  📅 Calendário   [Mês][Semana][Dia]           + Novo agendamento   │
└────────────────────────────────────────────────────────────────────┘
┌─ cal-wrapper (grid 2 colunas: 1fr / 300px) ────────────────────────┐
│  ┌─ cal-panel ──────────────┐  ┌─ side-panel ───────────────────┐  │
│  │  Nav: ‹ Junho 2026 ›    │  │  [Filtro profissionais]        │  │
│  │  Dom Seg Ter Qua ...    │  │  (apenas níveis 1 e 2)         │  │
│  │  ┌──┬──┬──┬──┬──┬──┬──┐ │  │                               │  │
│  │  │  │  │  │  │  │ ev│  │ │  │  Card: Qui 5 jun — Hoje       │  │
│  │  │  │ev│  │  │ev│ ev│  │ │  │  Pendentes: 2 | Em curso: 1   │  │
│  │  │  │  │  │  │  │   │  │ │  │  ─────────────────────────── │  │
│  │  └──┴──┴──┴──┴──┴──┴──┘ │  │  14:00 Fábio R. [pendente]   │  │
│  │  [Legenda profissionais] │  │  15:30 Gabi L.  [em atend.]  │  │
│  └──────────────────────────┘  │  + Agendar neste dia          │  │
│                                └───────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────┘
```

### 4.3 Cores por Profissional

Paleta de 8 cores geradas por `id % 8` do prestador. Passada para o JS via variável PHP embutida:

```php
$paleta = [
  ['bg'=>'#dbeafe','border'=>'#93c5fd','text'=>'#1e40af'],  // azul
  ['bg'=>'#fce7f3','border'=>'#f9a8d4','text'=>'#9d174d'],  // rosa
  ['bg'=>'#fef3c7','border'=>'#fde68a','text'=>'#92400e'],  // amarelo
  ['bg'=>'#ede9fe','border'=>'#c4b5fd','text'=>'#5b21b6'],  // roxo
  ['bg'=>'#dcfce7','border'=>'#86efac','text'=>'#166534'],  // verde
  ['bg'=>'#ffedd5','border'=>'#fdba74','text'=>'#9a3412'],  // laranja
  ['bg'=>'#cffafe','border'=>'#67e8f9','text'=>'#164e63'],  // ciano
  ['bg'=>'#f1f5f9','border'=>'#cbd5e1','text'=>'#334155'],  // cinza
];
```

### 4.4 Indicador de Status

Ponto colorido no canto do evento via `eventDidMount` do FullCalendar:
- 0 = pendente → `#ef4444`
- 1 = em atendimento → `#16a34a`
- 2 = finalizado → `#f59e0b`
- 3 = cancelado → `#94a3b8`

### 4.5 Filtro de Profissionais (painel lateral)

Renderizado via PHP para níveis 1 e 2. Lista de checkboxes visuais (dot colorido + nome + contagem).
Ao marcar/desmarcar, refiltra os eventos localmente no FullCalendar via `getEventSource().remove()` + refetch com parâmetro `id_prestador`.

### 4.6 Interação — Clique num slot vazio

Evento FullCalendar `dateClick`:
- Preenche data/hora no **modal de criação** e abre via Bootstrap Modal
- Modal tem: paciente (Select2 via `adm/atendimento/buscar_paciente`), profissional (select do escopo), data, hora, tipo
- Submit via AJAX para `adm/calendario/salvar_agendamento`
- Em caso de sucesso: fecha modal + refetch do calendário

### 4.7 Interação — Clique num evento

Evento FullCalendar `eventClick`:
- Abre **modal de ações rápidas** com:
  - Avatar + nome do paciente + profissional + data/hora + tipo
  - Badge de status atual (pendente / em atendimento / finalizado / cancelado)
  - Botão "Prontuário" → `adm/usuarios/prontuario/{id_paciente}/{id_agenda}`
  - Botão "Iniciar/Finalizar/Reabrir" → GET `adm/atendimento/set_status_agenda/{id}/{status}` + reload
  - Botão "Remarcar" → expande sub-form inline no modal (nova data + hora) → POST `adm/atendimento/remarcar_agenda`
  - Botão "Cancelar" → confirm JS → GET `adm/atendimento/cancelar_agenda/{id}` + reload

---

## 5. Controle de Acesso por Nível

| Nível | Vê no calendário | Cria agendamento | Filtro profissional |
|-------|-----------------|-----------------|---------------------|
| 1 — Admin | Todos | Sim | Select livre |
| 2 — Estabelecimento | Escopo completo | Sim | Checkboxes visuais |
| 3 — Prestador | Só seus agendamentos | Sim (como prestador) | Não (só ele) |
| 4 — Colaborador | Escopo do grupo | Sim | Não |
| 5 — Paciente | **Bloqueado** (`show_error 403`) | — | — |

A lógica de escopo reutiliza `get_scope_user_ids()` e `get_visible_prestador_ids()` já existentes em `Padrao_model`.

---

## 6. Menu e Top Bar

### menu.php

Na seção "Operação clínica", para `!$menu_is_patient`, adicionar **após** o item "Agenda":

```php
$menu_operacao_items[] = [
  'label'    => 'Calendário',
  'icon'     => 'os-icon-calendar',
  'url'      => base_url().'adm/calendario',
  'children' => [
    ['label' => 'Calendário de agendamentos', 'url' => base_url().'adm/calendario'],
  ],
];
```

### top.php

Para níveis 2, 3 e 4, adicionar atalho após "Agenda":

```php
$top_shortcuts[] = ['label' => 'Calendário', 'url' => base_url().'adm/calendario'];
```

---

## 7. Rotas

Nenhuma rota especial necessária — CI3 auto-descobre `adm/Calendario` → `adm/calendario` e `adm/calendario/eventos`.

---

## 8. Dependências JavaScript

Já disponíveis em `bower_components/`:
- `fullcalendar/dist/fullcalendar.min.css`
- `fullcalendar/dist/fullcalendar.min.js`
- `fullcalendar/dist/locale/pt-br.js`
- `jquery/dist/jquery.min.js`
- `bootstrap/js/dist/modal.js`
- `select2/dist/js/select2.full.min.js`

---

## 9. Fora de Escopo

- View de timeline com colunas por profissional (opção C — backlog futuro)
- Drag-and-drop de eventos para remarcar
- Notificações push de novos agendamentos
- Integração com Google Calendar
