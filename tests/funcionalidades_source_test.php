<?php
function falha($m) { fwrite(STDERR, $m . PHP_EOL); exit(1); }
function ler($rel) { $p = __DIR__ . '/../' . $rel; if (!is_file($p)) { falha('ausente: ' . $rel); } return file_get_contents($p); }
function tem($agulha, $palheiro, $rotulo) { if (strpos($palheiro, $agulha) === false) { falha($rotulo . ' — ausente: ' . $agulha); } }
function naoTem($agulha, $palheiro, $rotulo) { if (strpos($palheiro, $agulha) !== false) { falha($rotulo . ' — nao deveria ter: ' . $agulha); } }

$exp = ler('application/views/public/experimentar.php');
tem("load->view('public/partials/funcionalidades'", $exp, 'experimentar usa partial');
tem('id="funcionalidades"', $exp, 'ancora funcionalidades');
naoTem('<strong>Seguro e 100% online</strong>', $exp, 'cards antigos removidos');

$ass = ler('application/views/public/assinar.php');
tem("load->view('public/partials/funcionalidades'", $ass, 'assinar usa partial');
naoTem('Tenant criado', $ass, 'sem texto tecnico');
naoTem('owner principal', $ass, 'sem texto tecnico');
tem('Tudo o que está incluído', $ass, 'secao completa no assinar');

echo "OK\n";
