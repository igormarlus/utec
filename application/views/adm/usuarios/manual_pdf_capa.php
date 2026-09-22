<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10.5pt; line-height:1.5; }
  h1 { margin:0; }

  .manual-pdf-capa { background-image: linear-gradient(160deg, #047bf8, #20c997); height: 255mm; padding: 40pt 34pt; color:#fff; }
  .manual-pdf-capa-marca { font-size:10pt; letter-spacing:3pt; text-transform:uppercase; font-weight:bold; opacity:.9; margin-bottom:130pt; }
  .manual-pdf-capa-circulo { width:34pt; height:34pt; border-radius:50%; background-color: rgba(255,255,255,.22); margin-bottom:14pt; }
  .manual-pdf-capa-titulo { font-size:30pt; font-weight:bold; line-height:1.15; margin-bottom:10pt; }
  .manual-pdf-capa-sub { font-size:12pt; opacity:.92; line-height:1.6; max-width:380pt; }
  .manual-pdf-capa-rodape { font-size:9pt; opacity:.75; margin-top:130pt; }

  table.manual-pdf-cabecalho { border-collapse:collapse; width:100%; }
  .manual-pdf-capitulo { margin-bottom:14pt; }
  .manual-pdf-lombada { background-color:#047bf8; width:10pt; }
  .manual-pdf-cabecalho-cel { background-image: linear-gradient(135deg, #047bf8, #20c997); padding:14pt 16pt; color:#fff; }
  .manual-pdf-numero { font-size:9pt; letter-spacing:2pt; text-transform:uppercase; color:rgba(255,255,255,.85); margin-bottom:4pt; }
  h2.manual-pdf-titulo { font-size:15pt; font-weight:bold; margin:0; color:#fff; }
  .manual-pdf-corpo { background-color:#fff; padding:14pt 16pt 6pt; border:0.5pt solid #e2e8f0; border-top:none; }
  .manual-pdf-resumo { color:#475569; margin:0 0 10pt; }
  .manual-pdf-print { width:100%; border:0.5pt solid #e2e8f0; margin-bottom:10pt; }
  .manual-pdf-checklist { margin:0 0 4pt; padding:0; list-style:none; }
  .manual-pdf-checklist li { margin-bottom:7pt; padding-left:2pt; }
  .manual-pdf-checklist .check { display:inline-block; width:13pt; height:13pt; line-height:13pt; text-align:center; border-radius:50%; background-color:#20c997; color:#fff; font-size:8pt; margin-right:5pt; }
</style>
<div class="manual-pdf-capa">
  <div class="manual-pdf-capa-marca">UTEC Saúde</div>
  <div class="manual-pdf-capa-circulo"></div>
  <div class="manual-pdf-capa-titulo"><?=$manual['title']?></div>
  <div class="manual-pdf-capa-sub"><?=$manual['subtitle']?></div>
  <div class="manual-pdf-capa-rodape">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
</div>
