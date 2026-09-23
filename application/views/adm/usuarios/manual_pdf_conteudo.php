<? $primeiro = true; $numero = 0; foreach($manual['capitulos'] as $capitulo){ $numero++; ?>
<div class="manual-pdf-capitulo"<?=$primeiro ? '' : ' style="page-break-before:always;"'?>>
  <tocentry content="<?=htmlspecialchars($capitulo['titulo'])?>" level="0" />
  <table class="manual-pdf-cabecalho" cellspacing="0" cellpadding="0">
    <tr>
      <td class="manual-pdf-lombada"></td>
      <td class="manual-pdf-cabecalho-cel">
        <div class="manual-pdf-numero">CAPÍTULO <?=sprintf('%02d', $numero)?></div>
        <h2 class="manual-pdf-titulo"><?=$capitulo['titulo']?></h2>
      </td>
    </tr>
  </table>
  <div class="manual-pdf-corpo">
    <p class="manual-pdf-resumo"><?=$capitulo['resumo']?></p>
    <? if($capitulo['print']){ ?>
    <img class="manual-pdf-print" src="<?=FCPATH.'imagens/manual/'.$capitulo['print']?>">
    <? } ?>
    <ul class="manual-pdf-checklist">
      <? foreach($capitulo['topicos'] as $topico){ ?>
      <li><span class="check">&#10003;</span> <?=$topico?></li>
      <? } ?>
    </ul>
  </div>
</div>
<? $primeiro = false; } ?>
