<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$e = function($v){ return nl2br(htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8')); };
$pac = $exp['paciente'];
$meta = $exp['meta'];
$periodo = 'Histórico completo';
if($meta['de'] !== '' || $meta['ate'] !== ''){
	$periodo = ($meta['de'] !== '' ? utec_pront_data_br($meta['de']) : 'início').' a '.($meta['ate'] !== '' ? utec_pront_data_br($meta['ate']) : 'hoje');
}
?>
<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10pt; line-height:1.45; }
  h1 { font-size:18pt; color:#0f4c81; margin:0 0 4px 0; }
  h2 { font-size:12.5pt; color:#0f4c81; border-bottom:1pt solid #cbd5e1; padding-bottom:3px; margin:18px 0 8px 0; }
  .muted { color:#64748b; font-size:9pt; }
  table.dados { width:100%; border-collapse:collapse; margin-top:6px; }
  table.dados td { padding:4px 6px; border:0.5pt solid #e2e8f0; vertical-align:top; }
  table.dados td.k { width:28%; background:#f1f5f9; font-weight:bold; }
  .atend { border:0.5pt solid #cbd5e1; padding:8px 10px; margin-bottom:10px; }
  .atend-top { font-weight:bold; color:#0f4c81; }
  .campo { margin-top:6px; }
  .campo-l { font-size:8.5pt; color:#475569; font-weight:bold; text-transform:uppercase; }
  table.lista { width:100%; border-collapse:collapse; }
  table.lista th { background:#0f4c81; color:#fff; font-size:8.5pt; text-align:left; padding:4px 6px; }
  table.lista td { border-bottom:0.5pt solid #e2e8f0; padding:4px 6px; font-size:9pt; }
</style>

<h1>Prontuário do paciente</h1>
<div class="muted">Período: <?=$e($periodo)?> &middot; gerado em <?=$e($meta['gerado_em'])?> por <?=$e($meta['gerado_por'])?></div>

<h2>Dados do paciente</h2>
<table class="dados">
  <tr><td class="k">Nome</td><td><?=$e($pac->nome)?></td></tr>
  <tr><td class="k">Telefone</td><td><?=$e($pac->telefone !== '' ? $pac->telefone : 'Não informado')?></td></tr>
  <tr><td class="k">E-mail</td><td><?=$e($pac->email !== '' ? $pac->email : 'Não informado')?></td></tr>
  <tr><td class="k">Cadastro</td><td><?=$e($pac->dt_cadastro !== '' ? utec_pront_data_br($pac->dt_cadastro) : 'Não informado')?></td></tr>
</table>

<h2>Atendimentos (<?=count($exp['atendimentos'])?>)</h2>
<?php if(empty($exp['atendimentos'])){ ?>
  <p class="muted">Nenhum atendimento no período.</p>
<?php } ?>
<?php foreach($exp['atendimentos'] as $a){ $r = $a['rotulos']; ?>
  <div class="atend">
    <div class="atend-top"><?=$e(utec_pront_data_br($a['data']))?> às <?=$e($a['hora'])?>h &middot; <?=$e($a['status_texto'])?></div>
    <div class="muted"><?=$e($a['profissional'] !== '' ? $a['profissional'] : 'Profissional não informado')?><?php if($a['especialidade'] !== ''){ ?> &middot; <?=$e($a['especialidade'])?><?php } ?></div>
    <?php foreach(array('atendimento_inicial', 'avaliacao', 'reavaliacao') as $campo){ if(trim($a[$campo]) === ''){ continue; } ?>
      <div class="campo"><div class="campo-l"><?=$e($r[$campo])?></div><?=$e($a[$campo])?></div>
    <?php } ?>
    <?php foreach($a['extras'] as $par){ ?>
      <div class="campo"><div class="campo-l"><?=$e($par[0])?></div><?=$e($par[1])?></div>
    <?php } ?>
  </div>
<?php } ?>

<h2>Exames (<?=count($exp['exames'])?>)</h2>
<?php if(empty($exp['exames'])){ ?>
  <p class="muted">Nenhum exame no período.</p>
<?php } else { ?>
  <table class="lista">
    <tr><th>Data</th><th>Exame</th><th>Status</th><th>Profissional</th><th>Observação</th></tr>
    <?php foreach($exp['exames'] as $x){ ?>
      <tr><td><?=$e(utec_pront_data_br($x['data']))?></td><td><?=$e($x['exame'])?></td><td><?=$e($x['status_texto'])?></td><td><?=$e($x['profissional'])?></td><td><?=$e($x['obs'])?></td></tr>
    <?php } ?>
  </table>
<?php } ?>

<h2>Arquivos anexados (<?=count($exp['arquivos'])?>)</h2>
<?php if(empty($exp['arquivos'])){ ?>
  <p class="muted">Nenhum arquivo no período.</p>
<?php } else { ?>
  <table class="lista">
    <tr><th>Data</th><th>Arquivo</th><th>Descrição</th><th>Tipo</th></tr>
    <?php foreach($exp['arquivos'] as $f){ ?>
      <tr><td><?=$e(utec_pront_data_br($f['data']))?></td><td><?=$e($f['nome'])?></td><td><?=$e($f['descricao'])?></td><td><?=$e($f['tipo'])?></td></tr>
    <?php } ?>
  </table>
  <p class="muted">Os arquivos não são incluídos neste documento; ficam disponíveis no prontuário do sistema.</p>
<?php } ?>
