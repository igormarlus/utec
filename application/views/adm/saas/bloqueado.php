<!DOCTYPE html>
<html>
  <head>
    <title>Acesso Bloqueado | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  </head>
  <body style="background:#f8fafc;font-family:Lato,sans-serif;">
    <div style="max-width:760px;margin:60px auto;padding:0 20px;">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:22px;box-shadow:0 12px 28px rgba(15,23,42,.07);padding:32px;">
        <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#b91c1c;">Operacao SaaS</div>
        <h1 style="margin:10px 0 12px;color:#0f172a;font-size:34px;">Acesso temporariamente bloqueado</h1>
        <p style="color:#475569;font-size:16px;line-height:1.6;">
          O tenant vinculado a este acesso esta com a operacao suspensa no momento. Isso normalmente acontece quando a assinatura esta pausada, cancelada ou com cobranca em atraso.
        </p>

        <? if(isset($tenant) && $tenant){ ?>
          <div style="margin-top:22px;padding:18px 20px;border-radius:16px;background:#fff7ed;border:1px solid #fdba74;">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#9a3412;">Tenant</div>
            <div style="margin-top:6px;font-size:22px;font-weight:700;color:#7c2d12;"><?=$tenant->tenant_nome?></div>
            <div style="margin-top:6px;color:#9a3412;">Status operacional: <?=$tenant->status == 1 ? 'Ativo' : 'Suspenso'?></div>
          </div>
        <? } ?>

        <div style="margin-top:24px;color:#64748b;">
          Se voce for o responsavel comercial da clinica, regularize a assinatura na area SaaS ou fale com o administrador da plataforma.
        </div>

        <div style="margin-top:24px;">
          <a href="<?=base_url()?>admin" class="btn btn-primary">Voltar ao login</a>
        </div>
      </div>
    </div>
  </body>
</html>
