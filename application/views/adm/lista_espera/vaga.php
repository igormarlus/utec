<? $e = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Vaga aberta</title>
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
  <p><a href="<?=base_url('adm/lista_espera')?>">&larr; Lista de espera</a></p>
  <div class="le-panel" style="border-left:6px solid <?=($ocupada || $passada) ? '#94a3b8' : '#16a34a'?>;">
    <h4 style="font-weight:800;margin:0 0 4px;">Vaga aberta</h4>
    <div style="font-size:16px;"><?=$e($vaga->prestador_nome)?> · <?=$e(date('d/m/Y', strtotime($vaga->data_agenda)))?> às <?=$e($vaga->hora_agenda)?></div>
    <? if($ocupada){ ?>
      <div class="alert alert-secondary" style="margin:12px 0 0;">Vaga já preenchida. Outro agendamento já ocupa este horário.</div>
    <? } elseif($passada){ ?>
      <div class="alert alert-secondary" style="margin:12px 0 0;">Este horário já passou.</div>
    <? } ?>
  </div>
  <div class="le-panel">
    <h5 style="font-weight:800;">Pacientes na lista</h5>
    <? if(!$itens){ ?><p class="text-muted" style="margin:0;">Ninguém aguardando para este profissional.</p><? } ?>
    <? foreach($itens as $item){ $it = $item['entrada']; $tel = preg_replace('/\D+/', '', (string)$it->paciente_telefone); ?>
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;padding:12px 0;border-top:1px solid #eef2f7;">
        <div style="flex:1;min-width:220px;">
          <strong><?=$e($it->paciente_nome)?></strong>
          <? if($item['compativel']){ ?><span class="badge badge-success" style="margin-left:6px;">Compatível</span><? } ?>
          <? if($tel !== ''){ ?> · <a href="https://api.whatsapp.com/send?phone=55<?=$e($tel)?>" target="_blank" rel="noopener"><?=$e($it->paciente_telefone)?></a><? } ?>
          <div class="le-sub" style="margin:2px 0 0;"><?=$e($it->prestador_nome ? $it->prestador_nome : 'Qualquer profissional')?> · <?=$e(utec_le_resumo_preferencias($it))?> · na lista há <?=utec_le_dias_espera($it->criado_em, date('Y-m-d'))?> dia(s)</div>
          <? if($it->observacao){ ?><div style="font-size:13px;color:#475569;"><?=$e($it->observacao)?></div><? } ?>
        </div>
        <? if(!$ocupada && !$passada){ ?>
          <a class="btn btn-sm btn-success" href="<?=base_url('adm/atendimento/novo/'.(int)$it->id_paciente.'?prestador='.(int)$vaga->id_prestador.'&data='.rawurlencode($vaga->data_agenda).'&hora='.rawurlencode($vaga->hora_agenda).'&lista_espera='.(int)$it->id)?>">Agendar</a>
        <? } ?>
      </div>
    <? } ?>
  </div>
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
</body>
</html>
