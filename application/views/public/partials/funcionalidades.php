<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!class_exists('Funcionalidades_conteudo')) { get_instance()->load->library('funcionalidades_conteudo'); }
$fx_formato = (isset($func_formato) && $func_formato === 'lista') ? 'lista' : 'grade';
$fx_itens = (isset($func_ids) && is_array($func_ids)) ? Funcionalidades_conteudo::por_ids($func_ids) : Funcionalidades_conteudo::itens();
$fx_agrupar = !empty($func_agrupar) && $fx_formato === 'grade';
$fx_titulo = isset($func_titulo) ? trim((string)$func_titulo) : '';
$fx_e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$fx_card = function ($i) use ($fx_e) {
    $h = '<div class="fx-card"><span class="fx-ico" aria-hidden="true">' . $fx_e($i['icone']) . '</span>'
        . '<h4 class="fx-titulo">' . $fx_e($i['titulo'])
        . ($i['novo'] ? ' <span class="fx-novo" aria-label="Novidade">Novo</span>' : '') . '</h4>'
        . '<p class="fx-resumo">' . $fx_e($i['resumo']) . '</p>';
    if ($i['link'] !== '') {
        $h .= '<a class="fx-link" href="' . $fx_e(base_url($i['link'])) . '">Saiba mais<span class="fx-sr"> sobre ' . $fx_e($i['titulo']) . '</span> →</a>';
    }
    return $h . '</div>';
};
if (empty($GLOBALS['fx_css_ok'])) { $GLOBALS['fx_css_ok'] = true; ?>
<style>
  .fx-wrap { margin: 8px 0 0; }
  .fx-wrap h2.fx-h2 { font-size: 22px; font-weight: 800; margin: 0 0 16px; }
  .fx-wrap h3.fx-grupo { font-size: 13px; letter-spacing: .06em; text-transform: uppercase; color: #64748b; margin: 18px 0 10px; }
  .fx-grade { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px; }
  .fx-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; }
  .fx-ico { font-size: 20px; display: block; margin-bottom: 6px; }
  .fx-titulo { font-size: 15px; font-weight: 700; margin: 0 0 4px; color: #0f172a; }
  .fx-novo { display: inline-block; font-size: 11px; font-weight: 800; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 999px; padding: 0 8px; vertical-align: middle; }
  .fx-resumo { font-size: 13px; line-height: 1.5; color: #475569; margin: 0; }
  .fx-link { display: inline-block; margin-top: 8px; font-size: 13px; font-weight: 700; color: #0f766e; }
  .fx-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
  .fx-lista { list-style: none; padding: 0; margin: 0; }
  .fx-lista li { margin: 0 0 8px; font-size: 14px; color: #334155; }
</style>
<?php } ?>
<div class="fx-wrap">
<?php if ($fx_titulo !== '') { ?><h2 class="fx-h2"><?= $fx_e($fx_titulo) ?></h2><?php } ?>
<?php if ($fx_formato === 'lista') { ?>
  <ul class="fx-lista">
  <?php foreach ($fx_itens as $fx_i) { ?>
    <li><span aria-hidden="true"><?= $fx_e($fx_i['icone']) ?></span> <strong><?= $fx_e($fx_i['titulo']) ?></strong> — <?= $fx_e($fx_i['resumo']) ?></li>
  <?php } ?>
  </ul>
<?php } elseif ($fx_agrupar) { ?>
  <?php foreach (Funcionalidades_conteudo::grupos() as $fx_gid => $fx_glabel) {
      $fx_doGrupo = array();
      foreach ($fx_itens as $fx_i) { if ($fx_i['grupo'] === $fx_gid) { $fx_doGrupo[] = $fx_i; } }
      if (empty($fx_doGrupo)) { continue; } ?>
    <h3 class="fx-grupo"><?= $fx_e($fx_glabel) ?></h3>
    <div class="fx-grade"><?php foreach ($fx_doGrupo as $fx_i) { echo $fx_card($fx_i); } ?></div>
  <?php } ?>
<?php } else { ?>
  <div class="fx-grade"><?php foreach ($fx_itens as $fx_i) { echo $fx_card($fx_i); } ?></div>
<?php } ?>
</div>
