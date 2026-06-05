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
    .cal-page-header {
      background: linear-gradient(135deg, #052e16 0%, #14532d 100%);
      padding: 16px 24px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
    }
    .cal-page-header h1 { color: #f0fdf4; font-size: 18px; font-weight: 800; margin: 0; }
    .cal-page-header .sub { color: #6ee7b7; font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; display: block; margin-bottom: 2px; }
    .cal-view-tabs { display: flex; background: rgba(255,255,255,.12); border-radius: 10px; padding: 3px; gap: 2px; }
    .cal-view-tab { padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #a7f3d0; cursor: pointer; border: none; background: transparent; }
    .cal-view-tab.active { background: #fff; color: #052e16; }
    .btn-cal-new { background: #16a34a; color: #fff; border: none; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; }
    .cal-wrapper { display: grid; grid-template-columns: 1fr 300px; gap: 20px; padding: 0 24px 24px; }
    .cal-panel { background: #fff; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,.05); overflow: hidden; }
    .cal-panel .fc-toolbar { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; margin-bottom: 0; }
    .cal-panel .fc-toolbar h2 { font-size: 16px; font-weight: 800; color: #0f172a; }
    .cal-panel .fc-button { background: #fff; border: 1px solid #e2e8f0; color: #475569; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 5px 12px; box-shadow: none; }
    .cal-panel .fc-button:hover { background: #f1f5f9; }
    .cal-panel .fc-button-primary:not(:disabled).fc-button-active,
    .cal-panel .fc-button-primary:not(:disabled):active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .cal-panel .fc-day-header { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #94a3b8; padding: 10px 4px; text-align: center; }
    .cal-panel .fc-day-number { font-size: 12px; font-weight: 700; color: #334155; }
    .cal-panel td.fc-today { background: #f0fdf4 !important; }
    .cal-panel .fc-event { border-radius: 4px; font-size: 10px; font-weight: 700; padding: 2px 5px; cursor: pointer; border: none; }
    .cal-panel .fc-event .fc-title { font-size: 10px; font-weight: 700; }
    .cal-panel .fc-more { font-size: 10px; color: #2563eb; font-weight: 700; }
    .cal-legend { padding: 10px 20px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
    .legend-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
    .legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #475569; }
    .legend-chip { width: 10px; height: 10px; border-radius: 2px; }
    .legend-sep { width: 1px; height: 14px; background: #e2e8f0; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
    .side-panel { display: flex; flex-direction: column; gap: 14px; }
    .prof-filter-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 12px 14px; }
    .prof-filter-title { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
    .prof-chip { display: flex; align-items: center; gap: 6px; padding: 5px 8px; border-radius: 8px; cursor: pointer; transition: background .12s; user-select: none; }
    .prof-chip:hover { background: #f8fafc; }
    .prof-chip-dot { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
    .prof-chip-name { font-size: 12px; font-weight: 600; color: #334155; flex: 1; }
    .prof-chip-check { font-size: 14px; }
    .prof-chip.off .prof-chip-check { color: #cbd5e1; }
    .prof-chip.off .prof-chip-name { opacity: .5; }
    .side-day-card { background: #fff; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,.05); overflow: hidden; }
    .side-day-header { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .side-day-title { font-size: 13px; font-weight: 800; color: #0f172a; }
    .side-day-badge { background: #eff6ff; color: #1d4ed8; border-radius: 999px; padding: 2px 9px; font-size: 10px; font-weight: 700; }
    .side-stats { display: grid; grid-template-columns: repeat(3,1fr); border-bottom: 1px solid #f1f5f9; }
    .side-stat { padding: 10px 6px; text-align: center; }
    .side-stat + .side-stat { border-left: 1px solid #f1f5f9; }
    .side-stat-val { font-size: 20px; font-weight: 800; line-height: 1; }
    .side-stat-lbl { font-size: 9px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-top: 3px; }
    .side-agenda-list { padding: 6px 12px 10px; max-height: 340px; overflow-y: auto; }
    .side-ag-item { display: flex; align-items: center; gap: 8px; padding: 9px 6px; border-bottom: 1px solid #f8fafc; cursor: pointer; border-radius: 8px; transition: background .12s; }
    .side-ag-item:last-child { border-bottom: none; }
    .side-ag-item:hover { background: #f8fafc; }
    .side-ag-bar { width: 3px; height: 36px; border-radius: 3px; flex-shrink: 0; }
    .side-ag-time { min-width: 36px; font-size: 11px; font-weight: 700; color: #475569; }
    .side-ag-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px; font-weight: 800; flex-shrink: 0; }
    .side-ag-info { flex: 1; min-width: 0; }
    .side-ag-name { font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side-ag-meta { font-size: 10px; color: #64748b; }
    .side-ag-status { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .side-day-footer { padding: 10px 14px; border-top: 1px solid #f1f5f9; }
    .btn-day-new { display: block; width: 100%; padding: 9px; background: linear-gradient(90deg, #052e16, #16a34a); color: #fff; border: none; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; text-align: center; }
    .side-empty { padding: 24px 16px; text-align: center; color: #94a3b8; font-size: 13px; }
    @media (max-width: 900px) { .cal-wrapper { grid-template-columns: 1fr; } .side-panel { display: none; } }
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

          <div class="cal-page-header">
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
              <div>
                <span class="sub">Operação Clínica</span>
                <h1>Calendário</h1>
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

          <div class="cal-wrapper">
            <div class="cal-panel">
              <div id="calendario"></div>
              <div class="cal-legend" id="cal-legend">
                <span class="legend-label">Profissionais:</span>
                <div class="legend-sep"></div>
                <span class="legend-label">Status:</span>
                <div class="legend-item"><div class="legend-dot" style="background:#ef4444;"></div> Pendente</div>
                <div class="legend-item"><div class="legend-dot" style="background:#16a34a;"></div> Em atend.</div>
                <div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div> Finalizado</div>
                <div class="legend-item"><div class="legend-dot" style="background:#94a3b8;"></div> Cancelado</div>
              </div>
            </div>

            <div class="side-panel">
              <?php if (in_array($nivel, [1, 2])): ?>
              <div class="prof-filter-card" id="prof-filter-card">
                <div class="prof-filter-title">Filtrar profissional</div>
                <?php foreach ($prestadores->result() as $prest): ?>
                <?php $cor = cal_prof_cor($prest->id); ?>
                <div class="prof-chip" data-id="<?=(int)$prest->id?>" onclick="calToggleProf(this)">
                  <div class="prof-chip-dot" style="background:<?=htmlspecialchars($cor['border'])?>"></div>
                  <span class="prof-chip-name"><?=htmlspecialchars($prest->nome)?></span>
                  <span class="prof-chip-check">✓</span>
                </div>
                <?php endforeach; ?>
                <p style="margin:6px 0 0;font-size:10px;color:#94a3b8;">Clique para ocultar/mostrar</p>
              </div>
              <?php endif; ?>

              <div class="side-day-card" id="side-day-card">
                <div class="side-day-header">
                  <span class="side-day-title" id="side-day-title">Selecione um dia</span>
                  <span class="side-day-badge" id="side-day-badge" style="display:none;">0 ag.</span>
                </div>
                <div id="side-day-content">
                  <div class="side-empty">Clique em um dia no calendário para ver os agendamentos.</div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="display-type"></div>
</div>

<!-- MODAL: CRIAR AGENDAMENTO -->
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
              <option value="<?=(int)$prest->id?>"><?=htmlspecialchars($prest->nome)?></option>
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

<!-- MODAL: AÇÕES RÁPIDAS -->
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
var BASE  = '<?=base_url()?>';
var NIVEL = <?=(int)$nivel?>;

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

var STATUS_COR      = {0:'#ef4444', 1:'#16a34a', 2:'#f59e0b', 3:'#94a3b8'};
var STATUS_NOME     = {0:'Pendente', 1:'Em atendimento', 2:'Finalizado', 3:'Cancelado'};
var STATUS_BADGE_CLS= {0:'badge-danger', 1:'badge-success', 2:'badge-warning', 3:'badge-secondary'};

var PRESTADORES = <?php
  $prest_js = [];
  foreach ($prestadores->result() as $p) {
    $prest_js[] = ['id' => (int)$p->id, 'nome' => htmlspecialchars($p->nome, ENT_QUOTES)];
  }
  echo json_encode($prest_js);
?>;

var hiddenPrestadores = {};

function escHtml(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

(function buildLegend(){
  var legend = document.getElementById('cal-legend');
  if (!legend || !PRESTADORES.length) return;
  var sep = legend.querySelector('.legend-sep');
  PRESTADORES.forEach(function(p){
    var cor = profCor(p.id);
    var el = document.createElement('div');
    el.className = 'legend-item';
    var chip = document.createElement('div');
    chip.className = 'legend-chip';
    chip.style.cssText = 'background:'+cor.bg+';border:1.5px solid '+cor.border+';';
    el.appendChild(chip);
    el.appendChild(document.createTextNode(' '+p.nome));
    legend.insertBefore(el, sep);
  });
})();

$(function(){
  $('#calendario').fullCalendar({
    locale: 'pt-br',
    defaultView: 'month',
    header: { left: 'prev,next today', center: 'title', right: '' },
    eventLimit: 3,
    events: function(start, end, timezone, callback){
      $.get(BASE+'adm/calendario/eventos', {
        start: start.format('YYYY-MM-DD'),
        end:   end.format('YYYY-MM-DD')
      }).done(function(data){
        var eventos = typeof data === 'string' ? JSON.parse(data) : data;
        eventos = eventos.filter(function(ev){
          return !hiddenPrestadores[ev.extendedProps.prestador_id];
        });
        callback(eventos);
      }).fail(function(){ callback([]); });
    },
    eventRender: function(event, element){
      var status = event.extendedProps ? event.extendedProps.status : 0;
      var cor = STATUS_COR[status] || '#94a3b8';
      element.find('.fc-title').after(
        '<span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:'+cor+';margin-left:4px;vertical-align:middle;flex-shrink:0;"></span>'
      );
    },
    dayClick: function(date){
      calLoadDia(date.format('YYYY-MM-DD'), date.format('DD/MM/YYYY'));
      document.getElementById('criar-data').value = date.format('YYYY-MM-DD');
    },
    eventClick: function(calEvent){
      calAbrirAcoes(calEvent);
    }
  });

  $('#cal-view-tabs .cal-view-tab').on('click', function(){
    $('#cal-view-tabs .cal-view-tab').removeClass('active');
    $(this).addClass('active');
    $('#calendario').fullCalendar('changeView', $(this).data('view'));
  });

  $('#btn-novo-agendamento').on('click', function(){
    $('#modal-criar').modal('show');
  });

  $('#criar-paciente').select2({
    dropdownParent: $('#modal-criar'),
    placeholder: 'Digite o nome do paciente...',
    minimumInputLength: 2,
    ajax: {
      url: BASE+'adm/atendimento/buscar_paciente',
      dataType: 'json',
      delay: 280,
      data: function(params){ return { q: params.term }; },
      processResults: function(data){
        return { results: data.map(function(p){ return { id: p.id, text: p.nome }; }) };
      }
    }
  });

  $('#btn-salvar-agendamento').on('click', function(){
    $('#criar-erro').hide();
    $.post(BASE+'adm/calendario/salvar_agendamento', $('#form-criar-agendamento').serialize())
      .done(function(res){
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
      .fail(function(){ $('#criar-erro').text('Erro de comunicação com o servidor.').show(); });
  });
});

function calLoadDia(dateYmd, dateFmt){
  $.get(BASE+'adm/calendario/eventos', { start: dateYmd, end: dateYmd })
    .done(function(data){
      var eventos = typeof data === 'string' ? JSON.parse(data) : data;
      var total     = eventos.length;
      var pendentes = eventos.filter(function(e){ return e.extendedProps.status === 0; }).length;
      var em_curso  = eventos.filter(function(e){ return e.extendedProps.status === 1; }).length;
      var feitos    = eventos.filter(function(e){ return e.extendedProps.status === 2; }).length;

      document.getElementById('side-day-title').textContent = dateFmt;
      var badge = document.getElementById('side-day-badge');
      badge.textContent = total + ' ag.';
      badge.style.display = total ? '' : 'none';

      if (!total) {
        document.getElementById('side-day-content').innerHTML =
          '<div class="side-empty">Nenhum agendamento neste dia.<br>'
          +'<button class="btn btn-sm btn-success mt-2" onclick="document.getElementById(\'criar-data\').value=\''
          +dateYmd+'\';$(\'#modal-criar\').modal(\'show\')">+ Agendar</button></div>';
        return;
      }

      var html = '<div class="side-stats">'
        +'<div class="side-stat"><div class="side-stat-val" style="color:#ef4444;">'+pendentes+'</div><div class="side-stat-lbl">Pendentes</div></div>'
        +'<div class="side-stat"><div class="side-stat-val" style="color:#16a34a;">'+em_curso+'</div><div class="side-stat-lbl">Em curso</div></div>'
        +'<div class="side-stat"><div class="side-stat-val" style="color:#f59e0b;">'+feitos+'</div><div class="side-stat-lbl">Feitos</div></div>'
        +'</div><div class="side-agenda-list">';

      eventos.sort(function(a,b){ return a.start < b.start ? -1 : 1; });
      eventos.forEach(function(ev){
        var ep = ev.extendedProps;
        var cor = profCor(ep.prestador_id);
        var ini = ep.paciente_nome ? ep.paciente_nome.trim().charAt(0).toUpperCase() : '?';
        var hora = ep.hora || (ev.start ? ev.start.substring(11,16) : '');
        var evJson = JSON.stringify(ev).replace(/'/g,"&#39;");
        html += '<div class="side-ag-item" onclick="calAbrirAcoesDireto('+evJson+')">'
          +'<div class="side-ag-bar" style="background:'+cor.border+';"></div>'
          +'<div class="side-ag-time">'+hora+'</div>'
          +'<div class="side-ag-avatar" style="background:'+cor.dot+';">'+ini+'</div>'
          +'<div class="side-ag-info">'
          +'<div class="side-ag-name">'+escHtml(ep.paciente_nome)+'</div>'
          +'<div class="side-ag-meta">'+escHtml(ep.prestador_nome||'')+(ep.tipo?' · '+escHtml(ep.tipo):'')+'</div>'
          +'</div>'
          +'<div class="side-ag-status" style="background:'+STATUS_COR[ep.status]+';"></div>'
          +'</div>';
      });

      html += '</div><div class="side-day-footer">'
        +'<button class="btn-day-new" onclick="document.getElementById(\'criar-data\').value=\''
        +dateYmd+'\';$(\'#modal-criar\').modal(\'show\')">+ Agendar neste dia</button></div>';
      document.getElementById('side-day-content').innerHTML = html;
    });
}

var _acoesEvento = null;

function calAbrirAcoes(calEvent){
  calAbrirAcoesDireto({
    id: calEvent.id,
    extendedProps: calEvent.extendedProps,
    start: calEvent.start ? calEvent.start.format('YYYY-MM-DDTHH:mm:ss') : ''
  });
}

function calAbrirAcoesDireto(ev){
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
  document.getElementById('acao-meta').textContent = hora+(data?' · '+data:'')+(ep.prestador_nome?' · '+ep.prestador_nome:'')+(ep.tipo?' · '+ep.tipo:'');

  var badge = document.getElementById('acao-status-badge');
  badge.textContent = STATUS_NOME[status] || 'Pendente';
  badge.className = 'badge '+(STATUS_BADGE_CLS[status]||'badge-secondary');

  document.getElementById('acao-prontuario').href = BASE+'adm/usuarios/prontuario/'+ep.paciente_id+'/'+ev.id;

  var btnStatus = document.getElementById('acao-status');
  if (status === 0) {
    btnStatus.textContent = '▶ Iniciar'; btnStatus.className = 'btn btn-success btn-block'; btnStatus.style.borderRadius = '10px';
  } else if (status === 1) {
    btnStatus.textContent = '✓ Finalizar'; btnStatus.className = 'btn btn-warning btn-block'; btnStatus.style.borderRadius = '10px';
  } else {
    btnStatus.textContent = '↺ Reabrir'; btnStatus.className = 'btn btn-outline-secondary btn-block'; btnStatus.style.borderRadius = '10px';
  }
  btnStatus.href = BASE+'adm/atendimento/set_status_agenda/'+ev.id+'/'+status;

  var btnCancel = document.getElementById('acao-cancelar');
  btnCancel.style.display = (status === 3) ? 'none' : '';
  btnCancel.href = BASE+'adm/atendimento/cancelar_agenda/'+ev.id;

  document.getElementById('remarcar-sub').style.display = 'none';
  $('#modal-acoes').modal('show');
}

function calOpenRemarcar(){
  if (!_acoesEvento) return;
  var ep = _acoesEvento.extendedProps;
  document.getElementById('remarcar-id').value = _acoesEvento.id;
  document.getElementById('remarcar-data').value = ep.data || (_acoesEvento.start ? _acoesEvento.start.substring(0,10) : '');
  document.getElementById('remarcar-hora').value = ep.hora || (_acoesEvento.start ? _acoesEvento.start.substring(11,16) : '');
  document.getElementById('remarcar-sub').style.display = '';
}

function calToggleProf(chip){
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

</script>
</body>
</html>
