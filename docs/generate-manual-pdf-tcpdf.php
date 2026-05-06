<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

$root = dirname(__DIR__);
$htmlFile = __DIR__ . DIRECTORY_SEPARATOR . 'manual-uso-sistema.html';
$pdfFile = __DIR__ . DIRECTORY_SEPARATOR . 'manual-uso-sistema.pdf';

require_once $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'tcpdf' . DIRECTORY_SEPARATOR . 'tcpdf.php';

$html = file_get_contents($htmlFile);
if ($html === false) {
    fwrite(STDERR, "Nao foi possivel ler o HTML do manual.\n");
    exit(1);
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('OpenAI Codex');
$pdf->SetAuthor('OpenAI Codex');
$pdf->SetTitle('Manual de Uso - UTEC Saude');
$pdf->SetMargins(12, 12, 12);
$pdf->SetAutoPageBreak(true, 12);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($pdfFile, 'F');

echo $pdfFile . PHP_EOL;
