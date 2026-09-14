<style>
  body { font-family: dejavusans, sans-serif; color:#1e293b; font-size:10.5pt; line-height:1.5; }
  h1 { font-size:22pt; margin:0 0 8pt; color:#0f172a; }
  h2 { font-size:14pt; margin:0 0 8pt; color:#0f172a; }
  .manual-pdf-capa-marca { font-size:9pt; text-transform:uppercase; letter-spacing:2pt; color:#2563eb; font-weight:bold; margin-bottom:10pt; }
  .manual-pdf-capa-sub { color:#475569; font-size:12pt; margin-top:10pt; }
  .manual-pdf-capa-data { color:#94a3b8; font-size:9pt; margin-top:30pt; }
  .manual-pdf-capitulo { border:0.5pt solid #e2e8f0; border-radius:6pt; padding:10pt 12pt; margin-bottom:10pt; }
  .manual-pdf-resumo { color:#475569; margin:4pt 0 8pt; }
  .manual-pdf-print { width:100%; border:0.5pt solid #e2e8f0; margin-bottom:8pt; }
  .manual-pdf-lista { margin:0; padding-left:14pt; color:#475569; }
  .manual-pdf-lista li { margin-bottom:6pt; }
</style>
<div style="text-align:center; margin-top:80pt;">
  <div class="manual-pdf-capa-marca">UTEC Saude</div>
  <h1><?=$manual['title']?></h1>
  <div class="manual-pdf-capa-sub"><?=$manual['subtitle']?></div>
  <div class="manual-pdf-capa-data">Manual v<?=$manual['versao']?> &middot; gerado em <?=$manual['gerado_em']?></div>
</div>
