<? $primeiro = true; foreach($manual['capitulos'] as $capitulo){ ?>
<div class="manual-pdf-capitulo"<?=$primeiro ? '' : ' style="page-break-before:always;"'?>>
  <h2><?=$capitulo['titulo']?></h2>
  <p class="manual-pdf-resumo"><?=$capitulo['resumo']?></p>
  <? if($capitulo['print']){ ?>
  <img class="manual-pdf-print" src="<?=FCPATH.'imagens/manual/'.$capitulo['print']?>">
  <? } ?>
  <ul class="manual-pdf-lista">
    <? foreach($capitulo['topicos'] as $topico){ ?>
    <li><?=$topico?></li>
    <? } ?>
  </ul>
</div>
<? $primeiro = false; } ?>
