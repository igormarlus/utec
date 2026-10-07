<? $e = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Lista de espera</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .le-shell { max-width: 1100px; }
    .le-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .le-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
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
<div class="le-shell">
  <h4 style="font-weight:800;color:#0f172a;">Lista de espera</h4>
  <p class="le-sub">Pacientes que querem um horário antes do disponível. Quando uma consulta é cancelada ou remarcada, você recebe um aviso no sino com quem combina com a vaga.</p>

  <details class="le-panel" style="padding:14px 22px;">
    <summary style="font-weight:700;cursor:pointer;">Como funciona</summary>
    <ol style="margin:12px 0 0;padding-left:20px;color:#334155;">
      <li><strong>Adicione o paciente</strong> com o profissional (ou "Qualquer profissional") e, se quiser, turno, dias e a partir de quando ele pode vir.</li>
      <li><strong>Abriu uma vaga?</strong> Quando uma consulta futura é cancelada ou remarcada, o sino avisa: "Abriu vaga com ... em ...".</li>
      <li><strong>Clique no aviso</strong>: os pacientes compatíveis com a vaga aparecem primeiro. Ligue ou chame no WhatsApp.</li>
      <li><strong>Clique em Agendar</strong>: o agendamento abre preenchido. Ao salvar, o paciente sai da lista como "Agendado".</li>
    </ol>
  </details>

  <? if($flash_ok){ ?><div class="alert alert-success"><?=$e($flash_ok)?></div><? } ?>
  <? if($flash_erro){ ?><div class="alert alert-danger"><?=$e($flash_erro)?></div><? } ?>
  <? if(!$schema_ok){ ?><div class="alert alert-warning">A lista de espera ainda não foi ativada. Peça ao administrador para executar <code>adm/dev/migrar_lista_espera</code>.</div><? } ?>

  <? if($eh_admin){ ?>
  <div class="le-panel">
    <form method="get" action="<?=base_url('adm/lista_espera')?>" class="form-inline" style="gap:8px;">
      <label for="le-conta" class="mr-2">ID de um usuário da clínica</label>
      <input type="number" min="1" id="le-conta" name="conta" class="form-control form-control-sm mr-2" value="<?=$conta > 0 ? (int)$conta : ''?>">
      <button type="submit" class="btn btn-sm btn-primary">Abrir lista</button>
    </form>
  </div>
  <? } ?>

  <? if($schema_ok){
       $f = $editar ? (array)$editar : array();
       $f_dias = isset($f['dias_semana']) ? explode(',', (string)$f['dias_semana']) : array();
       $pac = $editar ? (object)array('id' => $editar->id_paciente, 'nome' => $editar->paciente_nome) : $paciente_pre;
       $voltar = 'adm/lista_espera'.($eh_admin && $conta > 0 ? '?conta='.(int)$conta : '');
  ?>
  <div class="le-panel" id="le-form">
    <h5 style="font-weight:800;"><?=$editar ? 'Editar entrada' : 'Adicionar à lista'?></h5>
    <form method="post" action="<?=base_url('adm/lista_espera/salvar')?>">
      <input type="hidden" name="voltar" value="<?=$e($voltar)?>">
      <? if($editar){ ?><input type="hidden" name="id" value="<?=(int)$editar->id?>"><? } ?>
      <div class="form-group">
        <label>Paciente *</label>
        <? if($pac){ ?>
          <input type="hidden" name="id_paciente" value="<?=(int)$pac->id?>">
          <div><strong><?=$e($pac->nome)?></strong><? if(!$editar){ ?> · <a href="<?=base_url('adm/lista_espera')?>">trocar</a><? } ?></div>
        <? } else { ?>
          <input type="hidden" name="id_paciente" id="le-id-paciente" value="">
          <input type="text" id="le-busca-paciente" class="form-control" placeholder="Digite ao menos 2 letras do nome" autocomplete="off" aria-label="Buscar paciente">
          <div id="le-resultados" class="list-group" style="display:none;position:relative;z-index:10;"></div>
        <? } ?>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="le-prest">Profissional *</label>
          <select name="id_prestador" id="le-prest" class="form-control">
            <option value="0">Qualquer profissional</option>
            <? foreach($prestadores as $p){ ?>
              <option value="<?=(int)$p->id?>" <?=isset($f['id_prestador']) && (int)$f['id_prestador'] === (int)$p->id ? 'selected' : ''?>><?=$e($p->nome)?></option>
            <? } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="le-turno">Turno</label>
          <select name="turno" id="le-turno" class="form-control">
            <? foreach(utec_le_turnos() as $k => $rot){ ?>
              <option value="<?=$e($k)?>" <?=isset($f['turno']) && $f['turno'] === $k ? 'selected' : ''?>><?=$e($rot)?></option>
            <? } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="le-apartir">A partir de</label>
          <input type="date" name="a_partir_de" id="le-apartir" class="form-control" value="<?=$e(isset($f['a_partir_de']) ? $f['a_partir_de'] : '')?>">
        </div>
      </div>
      <div class="form-group">
        <label>Dias da semana <small class="text-muted">(nenhum = qualquer dia)</small></label>
        <div>
          <? foreach(utec_le_dias_rotulos() as $d => $rot){ ?>
            <label class="mr-3"><input type="checkbox" name="dias_semana[]" value="<?=$d?>" <?=in_array((string)$d, $f_dias, true) ? 'checked' : ''?>> <?=$e($rot)?></label>
          <? } ?>
        </div>
      </div>
      <div class="form-group">
        <label for="le-obs">Observação</label>
        <textarea name="observacao" id="le-obs" class="form-control" rows="2" maxlength="500"><?=$e(isset($f['observacao']) ? $f['observacao'] : '')?></textarea>
      </div>
      <button type="submit" class="btn btn-primary"><?=$editar ? 'Salvar alterações' : 'Adicionar à lista'?></button>
      <? if($editar){ ?><a href="<?=base_url($voltar)?>" class="btn btn-link">Cancelar</a><? } ?>
    </form>
  </div>

  <? $qs_conta = $eh_admin && $conta > 0 ? '&conta='.(int)$conta : ''; ?>
  <ul class="nav nav-tabs" style="margin-bottom:12px;">
    <? foreach(array('aguardando' => 'Aguardando', 'agendado' => 'Agendados', 'removido' => 'Removidos') as $k => $rot){ ?>
      <li class="nav-item"><a class="nav-link <?=$aba === $k ? 'active' : ''?>" href="<?=base_url('adm/lista_espera?aba='.$k.$qs_conta)?>"><?=$rot?></a></li>
    <? } ?>
  </ul>
  <div class="le-panel">
    <? if(!$itens){ ?><p class="text-muted" style="margin:0;">Nenhum paciente nesta aba.</p><? } ?>
    <? $motivos = utec_le_motivos_saida(); foreach($itens as $it){ $tel = preg_replace('/\D+/', '', (string)$it->paciente_telefone); ?>
      <div class="le-linha" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start;padding:12px 0;border-top:1px solid #eef2f7;">
        <div style="flex:1;min-width:220px;">
          <a href="<?=base_url('adm/usuarios/prontuario/'.(int)$it->id_paciente)?>"><strong><?=$e($it->paciente_nome)?></strong></a>
          <? if($tel !== ''){ ?> · <a href="https://api.whatsapp.com/send?phone=55<?=$e($tel)?>" target="_blank" rel="noopener"><?=$e($it->paciente_telefone)?></a><? } ?>
          <div class="le-sub" style="margin:2px 0 0;"><?=$e($it->prestador_nome ? $it->prestador_nome : 'Qualquer profissional')?> · <?=$e(utec_le_resumo_preferencias($it))?></div>
          <? if($it->observacao){ ?><div style="font-size:13px;color:#475569;"><?=$e($it->observacao)?></div><? } ?>
        </div>
        <div style="font-size:13px;color:#64748b;min-width:120px;">
          <? if($aba === 'aguardando'){ $dias = utec_le_dias_espera($it->criado_em, date('Y-m-d')); ?>
            Na lista há <?=$dias?> <?=$dias === 1 ? 'dia' : 'dias'?>
          <? } elseif($aba === 'agendado'){ ?>
            Agendado em <?=$e(date('d/m/Y', strtotime($it->atualizado_em)))?>
          <? } else { ?>
            <?=$e(isset($motivos[$it->motivo_saida]) ? $motivos[$it->motivo_saida] : '')?> · <?=$e(date('d/m/Y', strtotime($it->atualizado_em)))?>
          <? } ?>
        </div>
        <? if($aba === 'aguardando'){ ?>
        <div style="display:flex;gap:6px;align-items:flex-start;">
          <a class="btn btn-sm btn-outline-primary" href="<?=base_url('adm/lista_espera?editar='.(int)$it->id.$qs_conta)?>#le-form">Editar</a>
          <details style="position:relative;">
            <summary class="btn btn-sm btn-outline-danger" style="list-style:none;cursor:pointer;">Remover</summary>
            <form method="post" action="<?=base_url('adm/lista_espera/remover/'.(int)$it->id)?>" style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:220px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
              <input type="hidden" name="voltar" value="<?=$e($voltar)?>">
              <label for="le-mot-<?=(int)$it->id?>" style="font-size:12px;">Motivo</label>
              <select name="motivo" id="le-mot-<?=(int)$it->id?>" class="form-control form-control-sm" required>
                <option value="">Selecione</option>
                <? foreach($motivos as $k => $rot){ ?><option value="<?=$e($k)?>"><?=$e($rot)?></option><? } ?>
              </select>
              <button type="submit" class="btn btn-sm btn-danger" style="margin-top:8px;">Remover da lista</button>
            </form>
          </details>
        </div>
        <? } ?>
      </div>
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
(function(){
  var $in = $('#le-busca-paciente'), $res = $('#le-resultados'), $id = $('#le-id-paciente'), t = null;
  if(!$in.length){ return; }
  $in.on('input', function(){
    clearTimeout(t); $id.val('');
    var q = $.trim($(this).val());
    if(q.length < 2){ $res.hide(); return; }
    t = setTimeout(function(){
      $.get('<?=base_url()?>adm/atendimento/buscar_paciente', { q: q }).done(function(data){
        var itens = typeof data === 'string' ? JSON.parse(data) : data;
        $res.empty();
        if(!itens.length){ $res.append('<div class="list-group-item text-muted">Nenhum paciente encontrado</div>'); }
        $.each(itens, function(_, p){
          $('<button type="button" class="list-group-item list-group-item-action"></button>')
            .html(p.nome).on('click', function(){ $id.val(p.id); $in.val($('<div>').html(p.nome).text()); $res.hide(); })
            .appendTo($res);
        });
        $res.show();
      });
    }, 280);
  });
  $in.closest('form').on('submit', function(e){ if(!$id.val()){ e.preventDefault(); alert('Selecione o paciente na lista de resultados.'); } });
})();
</script>
</body>
</html>
