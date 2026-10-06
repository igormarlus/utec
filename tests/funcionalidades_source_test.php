<?php
if (!defined('BASEPATH')) { define('BASEPATH', __DIR__); }
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

$home = ler('application/views/index-front.php');
tem("Funcionalidades_conteudo::por_ids(array('prontuario', 'agenda', 'chatbot', 'ficha_paciente', 'tempo_espera', 'rotulos', 'relatorios', 'equipe'))", $home, 'home usa catalogo');
tem('feature-card--whatsapp', $home, 'card do whatsapp mantido');
tem('experimentar#funcionalidades', $home, 'link ver todas');
naoTem('Exporte para PDF e tenha visão gerencial', $home, 'card antigo de relatorios removido');

$landings = array(
    'sistema-prontuario-eletronico' => array('Consigo exportar o prontuário do paciente?', 'O sistema avisa quando o paciente tem alergia?'),
    'sistema-para-clinicas' => array('Dá para medir o tempo de espera dos pacientes?'),
    'software-para-clinicas' => array('Consigo marcar pacientes com alertas, como alergia ou gestação?'),
    'sistema-para-consultorio-medico' => array('O paciente consegue remarcar sozinho?'),
    'casos-de-uso' => array(),
);
foreach ($landings as $slug => $perguntas) {
    $html = ler('application/views/public/seo/' . $slug . '.php');
    tem("load->view('public/partials/funcionalidades'", $html, $slug . ' usa partial');
    tem('Funcionalidades relacionadas', $html, $slug . ' bloco');
    foreach ($perguntas as $q) {
        if (substr_count($html, $q) < 2) { falha($slug . ' — pergunta deve estar no HTML e no JSON-LD: ' . $q); }
    }
    if (preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m)) {
        foreach ($m[1] as $json) { if (json_decode($json) === null) { falha($slug . ' — JSON-LD invalido'); } }
    }
}

require_once __DIR__ . '/../application/libraries/Funcionalidades_conteudo.php';
$llms = ler('llms.txt');
tem('## Funcionalidades', $llms, 'llms secao');
foreach (Funcionalidades_conteudo::itens() as $i) { tem($i['titulo'], $llms, 'llms item ' . $i['id']); }
tem('Funcionalidades_conteudo', ler('CLAUDE.md'), 'CLAUDE.md cita o catalogo');

echo "OK\n";
