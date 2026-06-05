# Calendário Dinâmico — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Criar a view `adm/calendario` com calendário FullCalendar mensal/semanal/diário, eventos coloridos por profissional, painel lateral com agendamentos do dia clicado, modal de criação e modal de ações rápidas — tudo respeitando o escopo de acesso por nível de usuário.

**Architecture:** Controller CI3 `Calendario.php` com endpoint JSON `eventos()` consumido pelo FullCalendar via AJAX. View única com CSS inline e JS embutido. Reutiliza `Padrao_model::get_scope_user_ids()` e `get_visible_prestador_ids()` para controle de acesso, e os endpoints de status/remarcar/cancelar já existentes em `Atendimento.php`.

**Tech Stack:** PHP 7 + CodeIgniter 3.1.10, FullCalendar 3.x (bower_components), Bootstrap 4 Modal, Select2, jQuery — todos já disponíveis localmente.

---

## Arquivos Envolvidos

| Ação | Arquivo |
|------|---------|
| **Criar** | `application/controllers/adm/Calendario.php` |
| **Criar** | `application/views/adm/calendario/index.php` |
| **Modificar** | `includes/adm/menu.php` |
| **Modificar** | `includes/adm/top.php` |

---

## Task 1: Controller — Skeleton + `Index()`

**Files:**
- Create: `application/controllers/adm/Calendario.php`

- [ ] **1.1 Criar o controller com construtor padrão**

Crie `application/controllers/adm/Calendario.php` com o conteúdo abaixo. Segue exatamente o mesmo padrão do `Atendimento.php`:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        $this->load->model('adm/usuarios_model');
        $this->load->model('padrao_model');
        $this->usuarios_model->verSession();
        $this->load->model('FbApi_model', 'fbapi_model');
        $this->padrao_model->indexador();
    }

    function Index()
    {
        $dd_user = $this->padrao_model->get_usuario_logado();

        // Pacientes não têm acesso ao calendário
        if ((int)$dd_user->nivel === 5) {
            show_error('Acesso negado.', 403);
            return;
        }

        $visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
        $visible_prestador_sql = $this->padrao_model->ids_to_sql_in($visible_prestador_ids);

        if ((int)$dd_user->nivel === 1) {
            $prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC");
        } else {
            $prestadores = $this->db->query("SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN (" . $visible_prestador_sql . ") ORDER BY nome ASC");
        }

        $dados['dd']         = $dd_user;
        $dados['nivel']      = (int)$dd_user->nivel;
        $dados['prestadores'] = $prestadores;

        $this->load->view('adm/calendario/index', $dados);
    }
}
```

- [ ] **1.2 Verificar que a rota carrega sem erro**

No browser, logado como admin (nível 1), acesse `http://localhost/utec/adm/calendario`.
Deve carregar sem erro 404 ou 500 (pode ser página em branco por enquanto — a view ainda não existe, mas o controller deve inicializar sem exceção; se der 404 de view, está certo).

---

## Task 2: View — Estrutura HTML/CSS

**Files:**
- Create: `application/views/adm/calendario/index.php`

- [ ] **2.1 Criar o diretório e a view com estrutura base**

Crie a pasta `application/views/adm/calendario/` e o arquivo `index.php` com o conteúdo abaixo.
Esta view segue exatamente o padrão de `application/views/adm/usuarios/new/atendimentos.php` (head, body, includes, scripts).

```php
<!DOCTYPE html>
<html>
<head>
  <title>Calendário</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    /* ── Layout base ──────────────────────────────────── */
    .cal-page-header {
      background: linear-gradient(135deg, #052e16 0%, #14532d 100%);
      padding: 16px 24px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 16px; flex-wrap: wrap; border-radius: 0 0 0 0;
      margin-bottom: 20px;
    }
    .cal-page-header h1 {
      color: #f0fdf4; font-size: 18px; font-weight: 800;
      font-family: var(--ut-font, 'Lato', sans-serif); margin: 0;
    }
    .cal-page-header .sub {
      color: #6ee7b7; font-size: 11px; font-weight: 700;
      letter-spacing: .07em; text-transform: uppercase; display: block; margin-bottom: 2px;
    }
    .cal-view-tabs {
      display: flex; background: rgba(255,255,255,.12);
      border-radius: 10px; padding: 3px; gap: 2px;
    }
    .cal-view-tab {
      padding: 6px 14px; border-radius: 8px; font-size: 12px;
      font-weight: 700; color: #a7f3d0; cursor: pointer;
      border: none; background: transparent;
    }
    .cal-view-tab.active { background: #fff; color: #052e16; }
    .btn-cal-new {
      background: #16a34a; color: #fff; border: none;
      border-radius: 10px; padding: 9px 18px; font-size: 13px;
      font-weight: 700; cursor: pointer;
    }

    /* ── Wrapper 2 colunas ────────────────────────────── */
    .cal-wrapper {
      display: grid;
      grid-template-columns: 1fr 300px;
      gap: 20px;
      padding: 0 24px 24px;
    }

    /* ── Painel do calendário ─────────────────────────── */
    .cal-panel {
      background: #fff;
      border-radius: 18px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 20px rgba(15,23,42,.05);
      overflow: hidden;
    }
    /* Sobrescrever estilos padrão do FullCalendar para combinar com o design */
    .cal-panel .fc-toolbar {
      padding: 16px 20px;
      border-bottom: 1px solid #f1f5f9;
      margin-bottom: 0;
    }
    .cal-panel .fc-toolbar h2 {
      font-size: 16px; font-weight: 800; color: #0f172a;
    }
    .cal-panel .fc-button {
      background: #fff; border: 1px solid #e2e8f0;
      color: #475569; border-radius: 8px;
      font-size: 12px; font-weight: 700; padding: 5px 12px;
      box-shadow: none;
    }
    .cal-panel .fc-button:hover { background: #f1f5f9; }
    .cal-panel .fc-button-primary:not(:disabled).fc-button-active,
    .cal-panel .fc-button-primary:not(:disabled):active {
      background: #2563eb; border-color: #2563eb; color: #fff;
    }
    .cal-panel .fc-day-header {
      font-size: 11px; font-weight: 700; letter-spacing: .06em;
      text-transform: uppercase; color: #94a3b8;
      padding: 10px 4px; text-align: center;
    }
    .cal-panel .fc-day-number { font-size: 12px; font-weight: 700; color: #334155; }
    .cal-panel td.fc-today { background: #f0fdf4 !important; }
    .cal-panel .fc-event {
      border-radius: 4px; font-size: 10px; font-weight: 700;
      padding: 2px 5px; cursor: pointer; border: none;
    }
    .cal-panel .fc-event .fc-title { font-size: 10px; font-weight: 700; }
    .cal-panel .fc-more { font-size: 10px; color: #2563eb; font-weight: 700; }
    .cal-legend {
      padding: 10px 20px; border-top: 1px solid #f1f5f9;
      display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
    }
    .legend-label {
      font-size: 10px; font-weight: 700; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .06em;
    }
    .legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #475569; }
    .legend-chip { width: 10px; height: 10px; border-radius: 2px; }
    .legend-sep { width: 1px; height: 14px; background: #e2e8f0; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    /* ── Painel lateral ───────────────────────────────── */
    .side-panel { display: flex; flex-direction: column; gap: 14px; }

    .prof-filter-card {
      background: #fff; border-radius: 14px;
      border: 1px solid #e2e8f0; padding: 12px 14px;
    }
    .prof-filter-title {
      font-size: 10px; font-weight: 700; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px;
    }
    .prof-chip {
      display: flex; align-items: center; gap: 6px;
      padding: 5px 8px; border-radius: 8px; cursor: pointer;
      transition: background .12s; user-select: none;
    }
    .prof-chip:hover { background: #f8fafc; }
    .prof-chip-dot { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
    .prof-chip-name { font-size: 12px; font-weight: 600; color: #334155; flex: 1; }
    .prof-chip-check { font-size: 14px; }
    .prof-chip.off .prof-chip-check { color: #cbd5e1; }
    .prof-chip.off .prof-chip-name { opacity: .5; }

    .side-day-card {
      background: #fff; border-radius: 18px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 20px rgba(15,23,42,.05); overflow: hidden;
    }
    .side-day-header {
      padding: 14px 16px; border-bottom: 1px solid #f1f5f9;
      display: flex; align-items: center; justify-content: space-between;
    }
    .side-day-title { font-size: 13px; font-weight: 800; color: #0f172a; }
    .side-day-badge {
      background: #eff6ff; color: #1d4ed8;
      border-radius: 999px; padding: 2px 9px;
      font-size: 10px; font-weight: 700;
    }
    .side-stats {
      display: grid; grid-template-columns: repeat(3,1fr);
      border-bottom: 1px solid #f1f5f9;
    }
    .side-stat { padding: 10px 6px; text-align: center; }
    .side-stat + .side-stat { border-left: 1px solid #f1f5f9; }
    .side-stat-val { font-size: 20px; font-weight: 800; line-height: 1; }
    .side-stat-lbl {
      font-size: 9px; color: #94a3b8; font-weight: 700;
      text-transform: uppercase; letter-spacing: .05em; margin-top: 3px;
    }
    .side-agenda-list { padding: 6px 12px 10px; max-height: 340px; overflow-y: auto; }
    .side-ag-item {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 6px; border-bottom: 1px solid #f8fafc;
      cursor: pointer; border-radius: 8px; transition: background .12s;
    }
    .side-ag-item:last-child { border-bottom: none; }
    .side-ag-item:hover { background: #f8fafc; }
    .side-ag-bar { width: 3px; height: 36px; border-radius: 3px; flex-shrink: 0; }
    .side-ag-time { min-width: 36px; font-size: 11px; font-weight: 700; color: #475569; }
    .side-ag-avatar {
      width: 30px; height: 30px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 11px; font-weight: 800; flex-shrink: 0;
    }
    .side-ag-info { flex: 1; min-width: 0; }
    .side-ag-name { font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side-ag-meta { font-size: 10px; color: #64748b; }
    .side-ag-status { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .side-day-footer { padding: 10px 14px; border-top: 1px solid #f1f5f9; }
    .btn-day-new {
      display: block; width: 100%; padding: 9px;
      background: linear-gradient(90deg, #052e16, #16a34a);
      color: #fff; border: none; border-radius: 10px;
      font-size: 12px; font-weight: 700; cursor: pointer; text-align: center;
    }
    .side-empty { padding: 24px 16px; text-align: center; color: #94a3b8; font-size: 13px; }

    /* ── Responsivo: em telas menores oculta o side panel ─ */
    @media (max-width: 900px) {
      .cal-wrapper { grid-template-columns: 1fr; }
      .side-panel { display: none; }
    }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <?php include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <?php include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <?php include("includes/adm/top.php"); ?>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
        <li class="breadcrumb-item"><span>Calendário</span></li>
      </ul>
      <div class="content-panel-toggler">
        <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
      </div>
      <div class="content-i">
        <div class="content-box" style="padding:0;">

          <!-- HEADER DA PÁGINA -->
          <div class="cal-page-header">
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
              <div>
                <span class="sub">Operação Clínica</span>
                <h1>📅 Calendário</h1>
              </div>
              <div class="cal-view-tabs" id="cal-view-tabs">
                <button class="cal-view-tab active" data-view="month">Mês</button>
                <button class="cal-view-tab" data-view="agendaWeek">Semana</button>
                <button class="cal-view-tab" data-view="agendaDay">Dia</button>
              </div>
            </div>
            <?php if (in_array($nivel, [1, 2, 3, 4])): ?>
            <button class="btn-cal-new" id="btn-novo-agendamento">+ Novo agendamento</button>
            <?php endif; ?>
          </div>

          <!-- GRID 2 COLUNAS -->
          <div class="cal-wrapper">

            <!-- CALENDÁRIO FULLCALENDAR -->
            <div class="cal-panel">
              <div id="calendario"></div>
              <!-- Legenda profissionais + status dots -->
              <div class="cal-legend" id="cal-legend">
                <span class="legend-label">Profissionais:</span>
                <!-- preenchida via JS -->
                <div class="legend-sep"></div>
                <span class="legend-label">Status:</span>
                <div class="legend-item"><div class="legend-dot" style="background:#ef4444;"></div> Pendente</div>
                <div class="legend-item"><div class="legend-dot" style="background:#16a34a;"></div> Em atend.</div>
                <div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div> Finalizado</div>
                <div class="legend-item"><div class="legend-dot" style="background:#94a3b8;"></div> Cancelado</div>
              </div>
            </div>

            <!-- PAINEL LATERAL -->
            <div class="side-panel">

              <!-- Filtro de profissionais: apenas níveis 1 e 2 -->
              <?php if (in_array($nivel, [1, 2])): ?>
              <div class="prof-filter-card" id="prof-filter-card">
                <div class="prof-filter-title">Filtrar profissional</div>
                <?php foreach ($prestadores->result() as $prest): ?>
                <?php $cor = cal_prof_cor($prest->id); ?>
                <div class="prof-chip" data-id="<?=$prest->id?>" data-cor="<?=$cor['bg']?>" onclick="calToggleProf(this)">
                  <div class="prof-chip-dot" style="background:<?=$cor['border']?>;"></div>
                  <span class="prof-chip-name"><?=htmlspecialchars($prest->nome)?></span>
                  <span class="prof-chip-check">✓</span>
                </div>
                <?php endforeach; ?>
                <p style="margin:6px 0 0;font-size:10px;color:#94a3b8;">Clique para ocultar/mostrar</p>
              </div>
              <?php endif; ?>

              <!-- Card do dia selecionado -->
              <div class="side-day-card" id="side-day-card">
                <div class="side-day-header">
                  <span class="side-day-title" id="side-day-title">Selecione um dia</span>
                  <span class="side-day-badge" id="side-day-badge" style="display:none;">0 ag.</span>
                </div>
                <div id="side-day-content">
                  <div class="side-empty">Clique em um dia no calendário para ver os agendamentos.</div>
                </div>
              </div>

            </div><!-- /side-panel -->
          </div><!-- /cal-wrapper -->

        </div><!-- /content-box -->
      </div><!-- /content-i -->
    </div><!-- /content-w -->
  </div><!-- /layout-w -->
  <div class="display-type"></div>
</div><!-- /all-wrapper -->

<!-- ══ MODAL: CRIAR AGENDAMENTO ══════════════════════════════ -->
<div class="modal fade" id="modal-criar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:18px;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#052e16,#14532d);border:none;">
        <h5 class="modal-title" style="color:#f0fdf4;font-weight:800;">Novo Agendamento</h5>
        <button type="button" class="close" data-dismiss="modal" style="color:#6ee7b7;opacity:1;">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-criar-agendamento">
          <div class="form-group">
            <label style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;">Paciente</label>
            <select id="criar-paciente" name="id_paciente" class="form-control" style="width:100%;" required></select>
          </div>
          <div class="form-group">
            <label style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;">Profissional</label>
            <select id="criar-prestador" name="id_prestador" class="form-control" required>
              <option value="">Selecione...</option>
              <?php foreach ($prestadores->result() as $prest): ?>
              <option value="<?=$prest->id?>"><?=htmlspecialchars($prest->nome)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;">Data</label>
                <input type="date" id="criar-data" name="data_agenda" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;">Horário</label>
                <input type="time" id="criar-hora" name="hora_agenda" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;">Tipo</label>
            <select name="tipo" class="form-control" required>
              <option value="Consulta">Consulta</option>
              <option value="Retorno">Retorno</option>
              <option value="Avaliacao">Avaliação</option>
              <option value="Exame">Exame</option>
            </select>
          </div>
          <div id="criar-erro" class="alert alert-danger" style="display:none;font-size:13px;"></div>
        </form>
      </div>
      <div class="modal-footer" style="border:none;padding:12px 20px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-sm" id="btn-salvar-agendamento">Salvar agendamento</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL: AÇÕES RÁPIDAS ══════════════════════════════════ -->
<div class="modal fade" id="modal-acoes" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:18px;overflow:hidden;">
      <div class="modal-header" style="border:none;padding:18px 20px 10px;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;">
          <div id="acao-avatar" style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:800;flex-shrink:0;">?</div>
          <div style="flex:1;min-width:0;">
            <div id="acao-nome" style="font-size:15px;font-weight:800;color:#0f172a;">Paciente</div>
            <div id="acao-meta" style="font-size:12px;color:#64748b;margin-top:2px;">–</div>
          </div>
          <span id="acao-status-badge" class="badge" style="font-size:11px;padding:5px 10px;border-radius:999px;">Pendente</span>
        </div>
        <button type="button" class="close" data-dismiss="modal" style="margin-left:8px;">&times;</button>
      </div>
      <div class="modal-body" style="padding-top:0;">
        <!-- sub-form de remarcação (oculto por padrão) -->
        <div id="remarcar-sub" style="display:none;background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;">
          <form id="form-remarcar" method="post" action="<?=base_url()?>adm/atendimento/remarcar_agenda">
            <input type="hidden" name="id_agenda" id="remarcar-id">
            <div class="row">
              <div class="col-6">
                <label style="font-size:11px;font-weight:700;color:#475569;">Nova data</label>
                <input type="date" name="data_agenda" id="remarcar-data" class="form-control form-control-sm" required>
              </div>
              <div class="col-6">
                <label style="font-size:11px;font-weight:700;color:#475569;">Novo horário</label>
                <input type="time" name="hora_agenda" id="remarcar-hora" class="form-control form-control-sm" required>
              </div>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px;">
              <button type="submit" class="btn btn-primary btn-sm">Salvar remarcação</button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('remarcar-sub').style.display='none'">Fechar</button>
            </div>
          </form>
        </div>
        <!-- botões de ação -->
        <div style="display:flex;flex-direction:column;gap:8px;">
          <a id="acao-prontuario" href="#" class="btn btn-primary btn-block" style="border-radius:10px;">📋 Abrir Prontuário</a>
          <a id="acao-status" href="#" class="btn btn-success btn-block" style="border-radius:10px;">▶ Iniciar</a>
          <button type="button" class="btn btn-outline-primary btn-block" style="border-radius:10px;" onclick="calOpenRemarcar()">📅 Remarcar</button>
          <a id="acao-cancelar" href="#" class="btn btn-outline-danger btn-block" style="border-radius:10px;" onclick="return confirm('Cancelar este agendamento?')">✕ Cancelar</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══ SCRIPTS ════════════════════════════════════════════════ -->
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/tether/dist/js/tether.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/modal.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/tooltip.js"></script>
<script src="<?=base_url()?>bower_components/moment/moment.min.js"></script>
<script src="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?=base_url()?>bower_components/fullcalendar/dist/locale/pt-br.js"></script>
<script src="<?=base_url()?>js/demo_customizer.js?version=4.5.0"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
<script>
/* ── Configuração ──────────────────────────────────── */
var BASE = '<?=base_url()?>';
var NIVEL = <?=(int)$nivel?>;

/* Paleta de 8 cores por id do profissional */
var PALETA = [
  {bg:'#dbeafe',border:'#93c5fd',text:'#1e40af',dot:'#3b82f6'},
  {bg:'#fce7f3',border:'#f9a8d4',text:'#9d174d',dot:'#ec4899'},
  {bg:'#fef3c7',border:'#fde68a',text:'#92400e',dot:'#f59e0b'},
  {bg:'#ede9fe',border:'#c4b5fd',text:'#5b21b6',dot:'#8b5cf6'},
  {bg:'#dcfce7',border:'#86efac',text:'#166534',dot:'#22c55e'},
  {bg:'#ffedd5',border:'#fdba74',text:'#9a3412',dot:'#f97316'},
  {bg:'#cffafe',border:'#67e8f9',text:'#164e63',dot:'#06b6d4'},
  {bg:'#f1f5f9',border:'#cbd5e1',text:'#334155',dot:'#94a3b8'},
];
function profCor(id){ return PALETA[parseInt(id) % 8]; }

var STATUS_COR = {0:'#ef4444', 1:'#16a34a', 2:'#f59e0b', 3:'#94a3b8'};
var STATUS_NOME = {0:'Pendente', 1:'Em atendimento', 2:'Finalizado', 3:'Cancelado'};
var STATUS_BADGE_CLS = {0:'badge-danger', 1:'badge-success', 2:'badge-warning', 3:'badge-secondary'};

/* Prestadores disponíveis (para legenda e filtro) */
var PRESTADORES = <?php
  $prest_js = [];
  foreach ($prestadores->result() as $p) {
    $prest_js[] = ['id' => (int)$p->id, 'nome' => htmlspecialchars($p->nome, ENT_QUOTES)];
  }
  echo json_encode($prest_js);
?>;

/* IDs ocultos pelo filtro lateral */
var hiddenPrestadores = {};

/* ── Legenda dinâmica ────────────────────────────── */
(function buildLegend() {
  var legend = document.getElementById('cal-legend');
  if (!legend || !PRESTADORES.length) return;
  var frag = '';
  PRESTADORES.forEach(function(p) {
    var cor = profCor(p.id);
    frag += '<div class="legend-item"><div class="legend-chip" style="background:' + cor.bg + ';border:1.5px solid ' + cor.border + ';"></div> ' + p.nome + '</div>';
  });
  // insere antes do primeiro .legend-sep
  var sep = legend.querySelector('.legend-sep');
  var tmp = document.createElement('div');
  tmp.innerHTML = frag;
  while (tmp.firstChild) { legend.insertBefore(tmp.firstChild, sep); }
})();

/* ── FullCalendar ────────────────────────────────── */
$(function() {
  $('#calendario').fullCalendar({
    locale: 'pt-br',
    defaultView: 'month',
    header: {
      left: 'prev,next today',
      center: 'title',
      right: ''  // view switching feito pelos nossos botões
    },
    eventLimit: 3,
    events: function(start, end, timezone, callback) {
      $.get(BASE + 'adm/calendario/eventos', {
        start: start.format('YYYY-MM-DD'),
        end:   end.format('YYYY-MM-DD')
      }).done(function(data) {
        var eventos = typeof data === 'string' ? JSON.parse(data) : data;
        // filtra profissionais ocultos
        eventos = eventos.filter(function(ev) {
          return !hiddenPrestadores[ev.extendedProps.prestador_id];
        });
        callback(eventos);
      }).fail(function() { callback([]); });
    },
    eventRender: function(event, element) {
      // Adiciona ponto de status no canto direito
      var status = event.extendedProps ? event.extendedProps.status : 0;
      var cor = STATUS_COR[status] || '#94a3b8';
      element.find('.fc-title').after(
        '<span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:' + cor + ';margin-left:4px;vertical-align:middle;flex-shrink:0;"></span>'
      );
    },
    dayClick: function(date, jsEvent, view) {
      // Atualiza painel lateral com o dia clicado
      calLoadDia(date.format('YYYY-MM-DD'), date.format('DD/MM/YYYY'));
      // Pré-preenche data no modal de criação
      document.getElementById('criar-data').value = date.format('YYYY-MM-DD');
    },
    eventClick: function(calEvent) {
      calAbrirAcoes(calEvent);
    }
  });

  // Botões de view
  $('#cal-view-tabs .cal-view-tab').on('click', function() {
    $('#cal-view-tabs .cal-view-tab').removeClass('active');
    $(this).addClass('active');
    $('#calendario').fullCalendar('changeView', $(this).data('view'));
  });

  // Botão "Novo agendamento" no header
  $('#btn-novo-agendamento, .btn-day-new-trigger').on('click', function() {
    $('#modal-criar').modal('show');
  });

  // Select2 para busca de paciente no modal de criação
  $('#criar-paciente').select2({
    dropdownParent: $('#modal-criar'),
    placeholder: 'Digite o nome do paciente...',
    minimumInputLength: 2,
    ajax: {
      url: BASE + 'adm/atendimento/buscar_paciente',
      dataType: 'json',
      delay: 280,
      data: function(params) { return { q: params.term }; },
      processResults: function(data) {
        return { results: data.map(function(p) { return { id: p.id, text: p.nome }; }) };
      }
    }
  });

  // Salvar novo agendamento via AJAX
  $('#btn-salvar-agendamento').on('click', function() {
    var form = $('#form-criar-agendamento');
    $('#criar-erro').hide();
    $.post(BASE + 'adm/calendario/salvar_agendamento', form.serialize())
      .done(function(res) {
        var r = typeof res === 'string' ? JSON.parse(res) : res;
        if (r.success) {
          $('#modal-criar').modal('hide');
          $('#calendario').fullCalendar('refetchEvents');
          var data = document.getElementById('criar-data').value;
          if (data) calLoadDia(data, data.split('-').reverse().join('/'));
        } else {
          $('#criar-erro').text(r.error || 'Erro ao salvar.').show();
        }
      })
      .fail(function() { $('#criar-erro').text('Erro de comunicação com o servidor.').show(); });
  });
});

/* ── Painel lateral: carregar dia ───────────────── */
function calLoadDia(dateYmd, dateFmt) {
  $.get(BASE + 'adm/calendario/eventos', { start: dateYmd, end: dateYmd })
    .done(function(data) {
      var eventos = typeof data === 'string' ? JSON.parse(data) : data;
      var total = eventos.length;
      var pendentes = eventos.filter(function(e){ return e.extendedProps.status === 0; }).length;
      var em_curso  = eventos.filter(function(e){ return e.extendedProps.status === 1; }).length;
      var feitos    = eventos.filter(function(e){ return e.extendedProps.status === 2; }).length;

      document.getElementById('side-day-title').textContent = dateFmt;
      var badge = document.getElementById('side-day-badge');
      badge.textContent = total + ' ag.';
      badge.style.display = total ? '' : 'none';

      if (!total) {
        document.getElementById('side-day-content').innerHTML =
          '<div class="side-empty">Nenhum agendamento neste dia.<br><button class="btn btn-sm btn-success mt-2 btn-day-new-trigger" onclick="document.getElementById(\'criar-data\').value=\'' + dateYmd + '\';$(\'#modal-criar\').modal(\'show\')">+ Agendar</button></div>';
        return;
      }

      var html = '<div class="side-stats">'
        + '<div class="side-stat"><div class="side-stat-val" style="color:#ef4444;">' + pendentes + '</div><div class="side-stat-lbl">Pendentes</div></div>'
        + '<div class="side-stat"><div class="side-stat-val" style="color:#16a34a;">' + em_curso + '</div><div class="side-stat-lbl">Em curso</div></div>'
        + '<div class="side-stat"><div class="side-stat-val" style="color:#f59e0b;">' + feitos + '</div><div class="side-stat-lbl">Feitos</div></div>'
        + '</div><div class="side-agenda-list">';

      eventos.sort(function(a,b){ return a.start < b.start ? -1 : 1; });
      eventos.forEach(function(ev) {
        var ep = ev.extendedProps;
        var cor = profCor(ep.prestador_id);
        var ini = ep.paciente_nome ? ep.paciente_nome.trim().charAt(0).toUpperCase() : '?';
        var hora = ep.hora ? ep.hora : ev.start.substring(11,16);
        html += '<div class="side-ag-item" onclick="calAbrirAcoesDireto(' + JSON.stringify(ev).replace(/'/g,"&#39;") + ')">'
          + '<div class="side-ag-bar" style="background:' + cor.border + ';"></div>'
          + '<div class="side-ag-time">' + hora + '</div>'
          + '<div class="side-ag-avatar" style="background:' + cor.dot + ';">' + ini + '</div>'
          + '<div class="side-ag-info">'
          +   '<div class="side-ag-name">' + ep.paciente_nome + '</div>'
          +   '<div class="side-ag-meta">' + (ep.prestador_nome || '') + (ep.tipo ? ' · ' + ep.tipo : '') + '</div>'
          + '</div>'
          + '<div class="side-ag-status" style="background:' + STATUS_COR[ep.status] + ';"></div>'
          + '</div>';
      });

      html += '</div><div class="side-day-footer"><button class="btn-day-new" onclick="document.getElementById(\'criar-data\').value=\'' + dateYmd + '\';$(\'#modal-criar\').modal(\'show\')">+ Agendar neste dia</button></div>';
      document.getElementById('side-day-content').innerHTML = html;
    });
}

/* ── Modal de ações rápidas ─────────────────────── */
var _acoesEvento = null;

function calAbrirAcoes(calEvent) {
  calAbrirAcoesDireto({
    id: calEvent.id,
    extendedProps: calEvent.extendedProps,
    start: calEvent.start ? calEvent.start.format('YYYY-MM-DDTHH:mm:ss') : ''
  });
}

function calAbrirAcoesDireto(ev) {
  _acoesEvento = ev;
  var ep = ev.extendedProps;
  var ini = ep.paciente_nome ? ep.paciente_nome.trim().charAt(0).toUpperCase() : '?';
  var cor = profCor(ep.prestador_id);
  var status = parseInt(ep.status);
  var hora = ep.hora || (ev.start ? ev.start.substring(11,16) : '');
  var data = ep.data || (ev.start ? ev.start.substring(0,10).split('-').reverse().join('/') : '');

  document.getElementById('acao-avatar').textContent = ini;
  document.getElementById('acao-avatar').style.background = cor.dot;
  document.getElementById('acao-nome').textContent = ep.paciente_nome;
  document.getElementById('acao-meta').textContent = hora + (data ? ' · ' + data : '') + (ep.prestador_nome ? ' · ' + ep.prestador_nome : '') + (ep.tipo ? ' · ' + ep.tipo : '');

  var badge = document.getElementById('acao-status-badge');
  badge.textContent = STATUS_NOME[status] || 'Pendente';
  badge.className = 'badge ' + (STATUS_BADGE_CLS[status] || 'badge-secondary');

  document.getElementById('acao-prontuario').href = BASE + 'adm/usuarios/prontuario/' + ep.paciente_id + '/' + ev.id;

  var btnStatus = document.getElementById('acao-status');
  if (status === 0) {
    btnStatus.textContent = '▶ Iniciar'; btnStatus.className = 'btn btn-success btn-block'; btnStatus.style.borderRadius = '10px';
  } else if (status === 1) {
    btnStatus.textContent = '✓ Finalizar'; btnStatus.className = 'btn btn-warning btn-block'; btnStatus.style.borderRadius = '10px';
  } else {
    btnStatus.textContent = '↺ Reabrir'; btnStatus.className = 'btn btn-outline-secondary btn-block'; btnStatus.style.borderRadius = '10px';
  }
  btnStatus.href = BASE + 'adm/atendimento/set_status_agenda/' + ev.id + '/' + status;

  var btnCancel = document.getElementById('acao-cancelar');
  if (status === 3) { btnCancel.style.display = 'none'; } else { btnCancel.style.display = ''; }
  btnCancel.href = BASE + 'adm/atendimento/cancelar_agenda/' + ev.id;

  document.getElementById('remarcar-sub').style.display = 'none';
  $('#modal-acoes').modal('show');
}

function calOpenRemarcar() {
  if (!_acoesEvento) return;
  var ep = _acoesEvento.extendedProps;
  document.getElementById('remarcar-id').value = _acoesEvento.id;
  document.getElementById('remarcar-data').value = ep.data || (_acoesEvento.start ? _acoesEvento.start.substring(0,10) : '');
  document.getElementById('remarcar-hora').value = ep.hora || (_acoesEvento.start ? _acoesEvento.start.substring(11,16) : '');
  document.getElementById('remarcar-sub').style.display = '';
}

/* ── Filtro de profissionais (nível 1/2) ────────── */
function calToggleProf(chip) {
  var id = chip.getAttribute('data-id');
  if (hiddenPrestadores[id]) {
    delete hiddenPrestadores[id];
    chip.classList.remove('off');
    chip.querySelector('.prof-chip-check').style.color = '';
  } else {
    hiddenPrestadores[id] = true;
    chip.classList.add('off');
    chip.querySelector('.prof-chip-check').style.color = '#cbd5e1';
  }
  $('#calendario').fullCalendar('refetchEvents');
}

/* menu mobile (padrão do projeto) */
(function utMenuBind() {
  if (typeof $ !== 'undefined') {
    $(document).off('click.utmenu').on('click.utmenu', '.mobile-menu-trigger', function () {
      $('.menu-mobile .menu-and-user').slideToggle(200, 'swing');
    });
  } else { setTimeout(utMenuBind, 80); }
})();
</script>
</body>
</html>
```

- [ ] **2.2 Adicionar helper `cal_prof_cor()` na view**

Logo **antes** do `<!DOCTYPE html>` na view, adicione o bloco PHP com o helper de cor:

```php
<?php
function cal_prof_cor($id) {
    $paleta = [
        ['bg'=>'#dbeafe','border'=>'#93c5fd','text'=>'#1e40af'],
        ['bg'=>'#fce7f3','border'=>'#f9a8d4','text'=>'#9d174d'],
        ['bg'=>'#fef3c7','border'=>'#fde68a','text'=>'#92400e'],
        ['bg'=>'#ede9fe','border'=>'#c4b5fd','text'=>'#5b21b6'],
        ['bg'=>'#dcfce7','border'=>'#86efac','text'=>'#166534'],
        ['bg'=>'#ffedd5','border'=>'#fdba74','text'=>'#9a3412'],
        ['bg'=>'#cffafe','border'=>'#67e8f9','text'=>'#164e63'],
        ['bg'=>'#f1f5f9','border'=>'#cbd5e1','text'=>'#334155'],
    ];
    return $paleta[(int)$id % 8];
}
?>
```

- [ ] **2.3 Verificar a view carrega sem erro de PHP**

Acesse `http://localhost/utec/adm/calendario` com usuário nível 1.
Esperado: página carrega, menu lateral aparece, header verde com título "Calendário" aparece, grid vazio do calendário renderiza.

---

## Task 3: Controller — Endpoint `eventos()`

**Files:**
- Modify: `application/controllers/adm/Calendario.php`

- [ ] **3.1 Adicionar o método `eventos()` no controller**

Adicione após o método `Index()` em `Calendario.php`:

```php
function eventos()
{
    $dd_user = $this->padrao_model->get_usuario_logado();
    if ((int)$dd_user->nivel === 5) {
        echo json_encode([]); return;
    }

    $start = $this->input->get('start', true);
    $end   = $this->input->get('end',   true);

    // Valida formato de data
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$start)) { echo json_encode([]); return; }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$end))   { echo json_encode([]); return; }

    $scope_ids             = $this->padrao_model->get_scope_user_ids($dd_user);
    $scope_sql             = $this->padrao_model->ids_to_sql_in($scope_ids);
    $visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
    $visible_prestador_sql = $this->padrao_model->ids_to_sql_in($visible_prestador_ids);

    $where = ["a.data_agenda BETWEEN '" . $start . "' AND '" . $end . "'"];

    if ((int)$dd_user->nivel !== 1) {
        $where[] = "(a.id_user IN (" . $scope_sql . ") OR a.id_paciente IN (" . $scope_sql . ") OR a.id_prestador IN (" . $scope_sql . "))";
    }

    // Filtro opcional por profissional (validado contra escopo)
    $id_prestador = (int)$this->input->get('id_prestador');
    if ($id_prestador > 0 && ((int)$dd_user->nivel === 1 || in_array($id_prestador, $visible_prestador_ids))) {
        $where[] = "a.id_prestador = " . $id_prestador;
    }

    $where_sql = 'WHERE ' . implode(' AND ', $where);

    $qr = $this->db->query(
        "SELECT a.id, a.id_paciente, a.id_prestador, a.data_agenda, a.hora_agenda, a.tipo, a.status,
                p.nome AS paciente_nome,
                pr.nome AS prestador_nome
         FROM agendamentos a
         LEFT JOIN usuarios p  ON p.id  = a.id_paciente
         LEFT JOIN usuarios pr ON pr.id = a.id_prestador
         " . $where_sql . "
         ORDER BY a.data_agenda ASC, a.hora_agenda ASC"
    );

    $paleta = [
        ['bg'=>'#dbeafe','border'=>'#93c5fd','text'=>'#1e40af'],
        ['bg'=>'#fce7f3','border'=>'#f9a8d4','text'=>'#9d174d'],
        ['bg'=>'#fef3c7','border'=>'#fde68a','text'=>'#92400e'],
        ['bg'=>'#ede9fe','border'=>'#c4b5fd','text'=>'#5b21b6'],
        ['bg'=>'#dcfce7','border'=>'#86efac','text'=>'#166534'],
        ['bg'=>'#ffedd5','border'=>'#fdba74','text'=>'#9a3412'],
        ['bg'=>'#cffafe','border'=>'#67e8f9','text'=>'#164e63'],
        ['bg'=>'#f1f5f9','border'=>'#cbd5e1','text'=>'#334155'],
    ];

    $eventos = [];
    foreach ($qr->result() as $ag) {
        $cor = $paleta[(int)$ag->id_prestador % 8];
        $hora = substr($ag->hora_agenda, 0, 5);
        $eventos[] = [
            'id'              => (int)$ag->id,
            'title'           => $ag->paciente_nome ?: 'Paciente',
            'start'           => $ag->data_agenda . 'T' . $hora . ':00',
            'end'             => $ag->data_agenda . 'T' . $hora . ':00',
            'backgroundColor' => $cor['bg'],
            'borderColor'     => $cor['border'],
            'textColor'       => $cor['text'],
            'extendedProps'   => [
                'paciente_id'   => (int)$ag->id_paciente,
                'paciente_nome' => $ag->paciente_nome,
                'prestador_id'  => (int)$ag->id_prestador,
                'prestador_nome'=> $ag->prestador_nome,
                'tipo'          => $ag->tipo,
                'status'        => (int)$ag->status,
                'hora'          => $hora,
                'data'          => $ag->data_agenda,
            ],
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($eventos);
}
```

- [ ] **3.2 Verificar o endpoint JSON**

No browser, acesse:
`http://localhost/utec/adm/calendario/eventos?start=2026-06-01&end=2026-06-30`

Esperado: resposta JSON com array de eventos (pode ser `[]` se não houver agendamentos no mês).
Se houver agendamentos, cada item deve ter `id`, `title`, `start`, `backgroundColor`, `extendedProps`.

- [ ] **3.3 Verificar que os eventos aparecem no calendário**

Acesse `http://localhost/utec/adm/calendario`.
Se houver agendamentos no banco no mês atual, as pílulas coloridas devem aparecer nas células do calendário.
Cada pílula deve ter cor diferente por profissional e um ponto colorido de status.

---

## Task 4: Controller — `salvar_agendamento()`

**Files:**
- Modify: `application/controllers/adm/Calendario.php`

- [ ] **4.1 Adicionar o método `salvar_agendamento()` no controller**

Adicione após o método `eventos()`:

```php
function salvar_agendamento()
{
    $dd_user = $this->padrao_model->get_usuario_logado();

    // Apenas níveis 1–4 podem criar agendamentos
    if ((int)$dd_user->nivel === 5) {
        echo json_encode(['success' => false, 'error' => 'Acesso negado.']); return;
    }

    $id_paciente  = (int)$this->input->post('id_paciente');
    $id_prestador = (int)$this->input->post('id_prestador');
    $data_agenda  = $this->input->post('data_agenda',  true);
    $hora_agenda  = $this->input->post('hora_agenda',  true);
    $tipo         = $this->input->post('tipo',         true);

    // Validações
    if (!$id_paciente) {
        echo json_encode(['success' => false, 'error' => 'Selecione um paciente.']); return;
    }
    if (!$id_prestador) {
        echo json_encode(['success' => false, 'error' => 'Selecione um profissional.']); return;
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$data_agenda)) {
        echo json_encode(['success' => false, 'error' => 'Data inválida.']); return;
    }
    if (!preg_match('/^\d{2}:\d{2}$/', (string)$hora_agenda)) {
        echo json_encode(['success' => false, 'error' => 'Horário inválido.']); return;
    }
    $tipos_validos = ['Consulta', 'Retorno', 'Avaliacao', 'Exame'];
    if (!in_array($tipo, $tipos_validos)) {
        echo json_encode(['success' => false, 'error' => 'Tipo inválido.']); return;
    }

    // Verificar acesso ao paciente
    if (!$this->padrao_model->can_access_usuario($id_paciente)) {
        echo json_encode(['success' => false, 'error' => 'Acesso negado ao paciente.']); return;
    }

    // Verificar acesso ao prestador
    $visible_prestador_ids = $this->padrao_model->get_visible_prestador_ids($dd_user);
    if ((int)$dd_user->nivel !== 1 && !in_array($id_prestador, $visible_prestador_ids)) {
        echo json_encode(['success' => false, 'error' => 'Acesso negado ao profissional.']); return;
    }

    $dd = [
        'id_user'          => (int)$this->session->userdata('id'),
        'id_paciente'      => $id_paciente,
        'id_prestador'     => $id_prestador,
        'tipo'             => $tipo,
        'data_agenda'      => $data_agenda,
        'hora_agenda'      => $hora_agenda,
        'data_hora_agenda' => $data_agenda . ' ' . $hora_agenda,
        'status'           => 0,
    ];

    if ($this->db->insert('agendamentos', $dd)) {
        echo json_encode(['success' => true, 'id' => (int)$this->db->insert_id()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao salvar agendamento.']);
    }
}
```

- [ ] **4.2 Verificar criação via modal**

Acesse `http://localhost/utec/adm/calendario`.
Clique em qualquer dia do calendário — o modal "Novo Agendamento" deve abrir com a data pré-preenchida.
Preencha paciente (via busca), profissional, hora e tipo. Clique "Salvar agendamento".
Esperado: modal fecha, calendário recarrega e o novo evento aparece na data clicada.

---

## Task 5: Menu lateral + Top bar

**Files:**
- Modify: `includes/adm/menu.php`
- Modify: `includes/adm/top.php`

- [ ] **5.1 Adicionar "Calendário" no menu lateral**

Em `includes/adm/menu.php`, localize o bloco que adiciona o item "Agenda" (`adm/atendimento`) dentro de `$menu_operacao_items`. Está dentro do bloco `if(!$menu_is_patient)`.

Após esse bloco, adicione o item "Calendário":

```php
// Localizar este bloco existente:
$menu_operacao_items[] = [
    'label' => 'Agenda',
    'icon' => 'os-icon-clipboard',
    'url' => base_url().'adm/atendimento',
    'children' => [
        ['label' => 'Atendimentos', 'url' => base_url().'adm/atendimento'],
    ],
];

// Adicionar logo APÓS o bloco acima:
$menu_operacao_items[] = [
    'label' => 'Calendário',
    'icon' => 'os-icon-calendar',
    'url' => base_url().'adm/calendario',
    'children' => [
        ['label' => 'Calendário de agendamentos', 'url' => base_url().'adm/calendario'],
    ],
];
```

- [ ] **5.2 Adicionar atalho "Calendário" no top bar**

Em `includes/adm/top.php`, para cada nível que já tem o atalho "Agenda", adicione "Calendário" logo após. São os blocos `if($top_level === 2)`, `if($top_level === 3)` e `if($top_level === 4)`.

No bloco do nível 2 (linha ~58), após `$top_shortcuts[] = ['label' => 'Agenda', ...]`, adicione:
```php
$top_shortcuts[] = ['label' => 'Calendário', 'url' => base_url().'adm/calendario'];
```

Repita o mesmo nos blocos de nível 3 e nível 4.

- [ ] **5.3 Verificar menu e atalhos**

Acesse qualquer página do admin (ex: `adm/atendimento`) com usuário nível 2.
Esperado:
- Menu lateral deve mostrar item "Calendário" abaixo de "Agenda" na seção "Operação clínica"
- Top bar deve exibir botão "Calendário" ao lado do "Agenda"
- Clicar em ambos deve navegar para `adm/calendario`

---

## Task 6: Testes de acesso por nível

- [ ] **6.1 Testar nível 1 (Admin)**

Logue como admin nível 1. Acesse `adm/calendario`.
- Deve ver agendamentos de TODOS os profissionais
- Filtro lateral deve listar todos os prestadores do sistema
- Pode criar agendamento para qualquer paciente/prestador

- [ ] **6.2 Testar nível 2 (Estabelecimento)**

Logue como usuário nível 2. Acesse `adm/calendario`.
- Deve ver apenas agendamentos do seu escopo (prestadores e pacientes vinculados a ele)
- Filtro lateral deve listar apenas os prestadores do seu escopo
- Toggle de profissional oculta/mostra eventos no calendário

- [ ] **6.3 Testar nível 3 (Prestador)**

Logue como usuário nível 3. Acesse `adm/calendario`.
- Deve ver apenas seus próprios agendamentos
- **Filtro lateral NÃO aparece** (renderizado condicionalmente para níveis 1 e 2 apenas)
- Modal de criação pré-seleciona ele mesmo como prestador (único disponível no select)

- [ ] **6.4 Testar nível 4 (Colaborador)**

Logue como usuário nível 4. Acesse `adm/calendario`.
- Deve ver agendamentos do escopo do grupo
- Filtro lateral não aparece
- Pode criar agendamentos

- [ ] **6.5 Testar nível 5 (Paciente)**

Logue como usuário nível 5. Acesse `adm/calendario` diretamente na URL.
- Deve retornar erro 403 ("Acesso negado")

- [ ] **6.6 Testar modal de ações rápidas**

Logado como nível 2, clique num evento no calendário.
- Modal deve abrir com nome do paciente, profissional, data, hora e status
- Botão "Prontuário" deve navegar para `adm/usuarios/prontuario/{id_paciente}/{id_agenda}`
- Botão "Iniciar" deve mudar status para 1 e recarregar calendário
- Botão "Remarcar" deve expandir o sub-form; após salvar, o evento deve aparecer na nova data
- Botão "Cancelar" deve pedir confirmação e remover/marcar como cancelado

- [ ] **6.7 Commit final**

```bash
git add application/controllers/adm/Calendario.php
git add application/views/adm/calendario/index.php
git add includes/adm/menu.php
git add includes/adm/top.php
git commit -m "feat: calendário dinâmico com FullCalendar, cores por profissional e controle de acesso por nível"
```

---

## Self-Review

**Cobertura do spec:**
- ✅ Layout A+B (calendário + painel lateral)
- ✅ Cores por profissional via `id % 8`
- ✅ Ponto de status em cada evento
- ✅ Views mês/semana/dia via toggle
- ✅ Controle de acesso por nível (1–5)
- ✅ Filtro lateral de profissionais (níveis 1 e 2)
- ✅ Clique em dia: painel lateral + pré-preenche modal
- ✅ Modal de criação com Select2, validação, AJAX
- ✅ Modal de ações rápidas com prontuário/status/remarcar/cancelar
- ✅ Menu lateral + top bar atalhos
- ✅ Reutiliza endpoints existentes de `Atendimento.php`

**Placeholders:** nenhum TBD ou "implementar depois".

**Consistência de tipos:**
- `extendedProps.paciente_id` — usado em `calAbrirAcoesDireto` e gerado em `eventos()` ✅
- `extendedProps.status` como int — JSON gerado com cast `(int)` ✅
- `cal_prof_cor()` em PHP e `profCor()` em JS usam a mesma paleta de 8 entradas ✅
