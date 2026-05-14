<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?=$manual['pdf_title']?></title>
    <style>
      body { font-family: DejaVu Sans, sans-serif; color:#1e293b; font-size:11px; line-height:1.6; }
      h1 { font-size:24px; margin:0 0 8px; color:#0f172a; }
      h2 { font-size:15px; margin:0 0 10px; color:#0f172a; }
      .hero { border:1px solid #dbe7f3; background:#f8fbff; border-radius:14px; padding:18px; margin-bottom:18px; }
      .copy { color:#475569; }
      .section { border:1px solid #e2e8f0; border-radius:14px; padding:16px; margin-bottom:14px; }
      ul { margin:0; padding-left:18px; }
      li { margin-bottom:8px; }
      .foot { margin-top:18px; font-size:10px; color:#64748b; }
    </style>
  </head>
  <body>
    <div class="hero">
      <div style="font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#2563eb;font-weight:700;">UTEC Saude</div>
      <h1><?=$manual['title']?></h1>
      <div class="copy"><?=$manual['subtitle']?></div>
    </div>

    <div class="section">
      <h2>Quem usa este perfil</h2>
      <ul><? foreach($manual['who'] as $item){ ?><li><?=$item?></li><? } ?></ul>
    </div>

    <div class="section">
      <h2>O que pode acessar</h2>
      <ul><? foreach($manual['access'] as $item){ ?><li><?=$item?></li><? } ?></ul>
    </div>

    <div class="section">
      <h2>Rotina recomendada</h2>
      <ul><? foreach($manual['day_to_day'] as $item){ ?><li><?=$item?></li><? } ?></ul>
    </div>

    <div class="section">
      <h2>Assinatura e pagamento</h2>
      <ul><? foreach($manual['payments'] as $item){ ?><li><?=$item?></li><? } ?></ul>
    </div>

    <div class="section">
      <h2>Boas praticas da operacao</h2>
      <ul><? foreach($manual['good_practices'] as $item){ ?><li><?=$item?></li><? } ?></ul>
    </div>

    <div class="foot">Documento gerado para apoio operacional da area administrativa.</div>
  </body>
</html>
