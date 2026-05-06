<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$root = dirname(__DIR__);
$htmlFile = __DIR__ . DIRECTORY_SEPARATOR . 'manual-uso-sistema.html';
$pdfFile = __DIR__ . DIRECTORY_SEPARATOR . 'manual-uso-sistema.pdf';
$tempDir = __DIR__ . DIRECTORY_SEPARATOR . 'tmp-mpdf';

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

if (!defined('_MPDF_TEMP_PATH')) {
    define('_MPDF_TEMP_PATH', $tempDir . DIRECTORY_SEPARATOR);
}

require_once $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'third_party' . DIRECTORY_SEPARATOR . 'mpdf' . DIRECTORY_SEPARATOR . 'mpdf.php';

$html = file_get_contents($htmlFile);
if ($html === false) {
    fwrite(STDERR, "Nao foi possivel ler o HTML do manual.\n");
    exit(1);
}

$mpdf = new mPDF('utf-8', 'A4', 0, '', 12, 12, 12, 12, 8, 8);
$mpdf->SetTitle('Manual de Uso - UTEC Saude');
$mpdf->SetAuthor('OpenAI Codex');
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->Output($pdfFile, 'F');

echo $pdfFile . PHP_EOL;
