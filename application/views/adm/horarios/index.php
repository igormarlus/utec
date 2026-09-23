<!DOCTYPE html>
<html>
<head>
  <title>Horários de atendimento</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .hr-shell { max-width: 1120px; }
    .hr-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .hr-panel h5 { font-weight:800; color:#0f172a; margin-bottom:4px; }
    .hr-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
    .hr-dia { display:flex; gap:14px; align-items:flex-start; padding:12px 0; border-top:1px solid #eef2f7; flex-wrap:wrap; }
    .hr-dia:first-of-type { border-top:0; }
    .hr-dia-nome { width:130px; font-weight:700; color:#0f172a; padding-top:6px; }
    .hr-intervalos { display:flex; flex-direction:column; gap:8px; flex:1; min-width:240px; }
    .hr-intervalo { display:flex; gap:8px; align-items:center; }
    .hr-intervalo input { max-width:130px; }
    .hr-dia.is-off .hr-intervalos { opacity:.4; pointer-events:none; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="hr-shell">
            <h4 style="font-weight:800;color:#0f172a;">Horários de atendimento</h4>
            <p class="hr-sub">Defina quando o profissional atende e quanto dura cada consulta. A agenda usa essas informações para sugerir horários livres.</p>

            <? if($flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div><? } ?>
            <? if($flash_error){ ?><div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas de horários ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_horarios_atendimento</code>.</div><? } ?>

            <? if(empty($prestadores)){ ?>
              <div class="alert alert-info">Nenhum profissional disponível para configurar.</div>
            <? } else { ?>

            <? if(count($prestadores) > 1){ ?>
            <form method="get" action="<?=base_url()?>adm/horarios" class="hr-panel" style="padding:16px 22px;">
              <label style="font-weight:700;">Profissional</label>
              <select name="id_prestador" class="form-control" onchange="this.form.submit()" style="max-width:420px;">
                <? foreach($prestadores as $p){ ?>
                  <option value="<?=(int)$p->id?>" <?=(int)$p->id === (int)$id_prestador ? 'selected' : ''?>><?=htmlspecialchars($p->nome)?></option>
                <? } ?>
              </select>
            </form>
            <? } ?>

            <? if(!$pode_editar){ ?><div class="alert alert-info">Você pode consultar estes horários, mas apenas o profissional ou o estabelecimento podem alterá-los.</div><? } ?>
            <? $dis = $pode_editar ? '' : 'disabled'; ?>

            <form method="post" action="<?=base_url()?>adm/horarios/salvar" class="hr-panel" id="form-grade">
              <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
              <h5>Duração da consulta</h5>
              <p class="hr-sub">Tempo que cada agendamento ocupa na agenda deste profissional.</p>
              <select name="duracao" class="form-control" style="max-width:200px;" <?=$dis?>>
                <? foreach($duracoes as $min){ ?>
                  <option value="<?=$min?>" <?=$config && (int)$config['duracao'] === (int)$min ? 'selected' : ''?>><?=$min?> minutos</option>
                <? } ?>
              </select>

              <h5 style="margin-top:24px;">Grade semanal</h5>
              <p class="hr-sub">Marque os dias de atendimento e informe um ou mais intervalos (ex.: 08:00–12:00 e 14:00–18:00).</p>
              <? for($d = 0; $d <= 6; $d++){
                   $ivs = $config ? $config['grade'][$d] : array();
                   $atende = !empty($ivs);
                   if(!$atende){ $ivs = array(array('', '')); } ?>
                <div class="hr-dia <?=$atende ? '' : 'is-off'?>" data-dia="<?=$d?>">
                  <div class="hr-dia-nome">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input hr-atende" id="atende-<?=$d?>" name="dias[<?=$d?>][atende]" value="1" <?=$atende ? 'checked' : ''?> <?=$dis?>>
                      <label class="custom-control-label" for="atende-<?=$d?>"><?=htmlspecialchars($nomes_dias[$d])?></label>
                    </div>
                  </div>
                  <div class="hr-intervalos">
                    <? foreach($ivs as $iv){ ?>
                      <div class="hr-intervalo">
                        <input type="time" class="form-control form-control-sm" name="dias[<?=$d?>][ini][]" value="<?=htmlspecialchars($iv[0])?>" <?=$dis?>>
                        <span>até</span>
                        <input type="time" class="form-control form-control-sm" name="dias[<?=$d?>][fim][]" value="<?=htmlspecialchars($iv[1])?>" <?=$dis?>>
                        <? if($pode_editar){ ?><button type="button" class="btn btn-sm btn-link text-danger hr-remover" title="Remover intervalo">✕</button><? } ?>
                      </div>
                    <? } ?>
                    <? if($pode_editar){ ?><div><button type="button" class="btn btn-sm btn-outline-secondary hr-adicionar">+ intervalo</button></div><? } ?>
                  </div>
                </div>
              <? } ?>

              <? if($pode_editar){ ?>
              <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;">
                <button type="submit" class="btn btn-primary">Salvar horários</button>
                <button type="button" class="btn btn-outline-secondary" id="hr-copiar-segunda">Copiar segunda para seg–sex</button>
              </div>
              <? } ?>
            </form>

            <div class="hr-panel">
              <h5>Bloqueios</h5>
              <p class="hr-sub">Férias, feriados, congressos ou qualquer período em que o profissional não atende.</p>
              <? if(empty($bloqueios)){ ?>
                <p class="text-muted" style="font-size:13px;">Nenhum bloqueio futuro.</p>
              <? } else { ?>
                <table class="table table-sm">
                  <thead><tr><th>Início</th><th>Fim</th><th>Motivo</th><th></th></tr></thead>
                  <tbody>
                  <? foreach($bloqueios as $b){ ?>
                    <tr>
                      <td><?=date('d/m/Y H:i', strtotime($b->inicio))?></td>
                      <td><?=date('d/m/Y H:i', strtotime($b->fim))?></td>
                      <td><?=htmlspecialchars((string)$b->motivo)?></td>
                      <td class="text-right">
                        <? if($pode_editar){ ?>
                        <form method="post" action="<?=base_url()?>adm/horarios/desbloquear/<?=(int)$b->id?>" style="display:inline" onsubmit="return confirm('Remover este bloqueio?')">
                          <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                        <? } ?>
                      </td>
                    </tr>
                  <? } ?>
                  </tbody>
                </table>
              <? } ?>

              <? if($pode_editar){ ?>
              <form method="post" action="<?=base_url()?>adm/horarios/bloquear" id="form-bloqueio" style="margin-top:12px;">
                <input type="hidden" name="id_prestador" value="<?=(int)$id_prestador?>">
                <div class="row">
                  <div class="col-md-3"><label>Data início</label><input type="date" name="data_inicio" class="form-control" required></div>
                  <div class="col-md-2 hr-hora"><label>Hora início</label><input type="time" name="hora_inicio" class="form-control"></div>
                  <div class="col-md-3"><label>Data fim</label><input type="date" name="data_fim" class="form-control"></div>
                  <div class="col-md-2 hr-hora"><label>Hora fim</label><input type="time" name="hora_fim" class="form-control"></div>
                  <div class="col-md-2 d-flex align-items-end">
                    <div class="custom-control custom-checkbox" style="margin-bottom:8px;">
                      <input type="checkbox" class="custom-control-input" id="dia-inteiro" name="dia_inteiro" value="1">
                      <label class="custom-control-label" for="dia-inteiro">Dia inteiro</label>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:10px;">
                  <div class="col-md-8"><label>Motivo (opcional)</label><input type="text" name="motivo" maxlength="150" class="form-control" placeholder="Ex.: férias, congresso"></div>
                  <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-primary">Adicionar bloqueio</button></div>
                </div>
                <small class="text-muted">Deixe a data fim vazia para bloquear só a data de início. Com horas e datas diferentes, o bloqueio vale do início ao fim de forma contínua.</small>
              </form>
              <? } ?>
            </div>
            <? } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
<script>
$(function(){
  function linhaIntervalo(dia, ini, fim){
    return $('<div class="hr-intervalo">'
      + '<input type="time" class="form-control form-control-sm" name="dias['+dia+'][ini][]">'
      + '<span>até</span>'
      + '<input type="time" class="form-control form-control-sm" name="dias['+dia+'][fim][]">'
      + '<button type="button" class="btn btn-sm btn-link text-danger hr-remover" title="Remover intervalo">✕</button>'
      + '</div>').find('input').eq(0).val(ini || '').end().eq(1).val(fim || '').end().end();
  }
  $(document).on('change', '.hr-atende', function(){
    $(this).closest('.hr-dia').toggleClass('is-off', !this.checked);
  });
  $(document).on('click', '.hr-adicionar', function(){
    var $dia = $(this).closest('.hr-dia');
    $(this).parent().before(linhaIntervalo($dia.data('dia')));
  });
  $(document).on('click', '.hr-remover', function(){
    var $ivs = $(this).closest('.hr-intervalos');
    if($ivs.find('.hr-intervalo').length > 1){ $(this).closest('.hr-intervalo').remove(); }
    else { $(this).closest('.hr-intervalo').find('input').val(''); }
  });
  $('#hr-copiar-segunda').on('click', function(){
    var $seg = $('.hr-dia[data-dia="1"]');
    var pares = $seg.find('.hr-intervalo').map(function(){
      var $i = $(this).find('input');
      return [[$i.eq(0).val(), $i.eq(1).val()]];
    }).get();
    var atende = $seg.find('.hr-atende').prop('checked');
    for(var d = 2; d <= 5; d++){
      var $dia = $('.hr-dia[data-dia="'+d+'"]');
      $dia.find('.hr-atende').prop('checked', atende).trigger('change');
      $dia.find('.hr-intervalo').remove();
      var $add = $dia.find('.hr-adicionar').parent();
      $.each(pares, function(_, p){ $add.before(linhaIntervalo(d, p[0], p[1])); });
    }
  });
  $('#dia-inteiro').on('change', function(){
    $('#form-bloqueio .hr-hora').toggle(!this.checked);
  });
});
</script>
</body>
</html>
