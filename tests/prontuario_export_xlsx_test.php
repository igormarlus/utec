<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Xlsx_simples.php';

function falha($label) { fwrite(STDERR, $label . PHP_EOL); exit(1); }

if (!Xlsx_simples::disponivel()) { falha('ZipArchive indisponivel neste PHP — habilite extension=zip para rodar o teste'); }

$x = new Xlsx_simples();
$x->adicionar_aba('Atendimentos', array(
    array('Data', 'Texto'),
    array('01/10/2026', 'A & B <c> "d"', 'Paciente 😀 ok'),
    array('02/10/2026', "linha1\nlinha2 \x01 controle"),
));
$x->adicionar_aba('Exames/[x]:?', array(array('Exame'), array('Raio-X')));
$bin = $x->gerar();
if (!is_string($bin) || strlen($bin) < 100) { falha('gerar() nao retornou bytes'); }
if (substr($bin, 0, 2) !== 'PK') { falha('nao e um zip'); }

$tmp = tempnam(sys_get_temp_dir(), 'xt');
file_put_contents($tmp, $bin);
$zip = new ZipArchive();
if ($zip->open($tmp) !== true) { falha('zip nao abre'); }
$partes = array('[Content_Types].xml', '_rels/.rels', 'xl/workbook.xml', 'xl/_rels/workbook.xml.rels',
    'xl/styles.xml', 'xl/worksheets/sheet1.xml', 'xl/worksheets/sheet2.xml');
foreach ($partes as $p) {
    $xml = $zip->getFromName($p);
    if ($xml === false) { falha('parte ausente: ' . $p); }
    libxml_use_internal_errors(true);
    if (simplexml_load_string($xml) === false) { falha('XML invalido: ' . $p); }
}
$s1 = $zip->getFromName('xl/worksheets/sheet1.xml');
if (strpos($s1, 'A &amp; B &lt;c&gt; &quot;d&quot;') === false) { falha('texto nao escapado'); }
if (strpos($s1, "😀") === false) { falha('emoji removido'); }
if (strpos($s1, "\x01") !== false) { falha('caractere de controle nao removido'); }
if (strpos($s1, 'r="B3"') === false) { falha('referencia de celula B3 ausente'); }
if (strpos($s1, 's="1"') === false) { falha('cabecalho sem estilo negrito'); }
$wb = $zip->getFromName('xl/workbook.xml');
if (strpos($wb, 'name="Exames"') === false && strpos($wb, 'name="Examesx"') === false) { falha('nome de aba nao sanitizado: ' . $wb); }
$zip->close();
unlink($tmp);

echo "OK\n";
