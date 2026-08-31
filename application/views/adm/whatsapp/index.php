<!DOCTYPE html>
<html>
<head>
  <title>Configuracoes de WhatsApp</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .wa-shell { max-width: 1120px; }
    .wa-hero {
      background: linear-gradient(135deg, #052e16 0%, #166534 100%);
      border-radius: 18px;
      color: #f0fdf4;
      margin-bottom: 22px;
      padding: 24px;
    }
    .wa-panel {
      background: #fff;
      border: 1px solid #dbe4ee;
      border-radius: 18px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
      padding: 22px;
    }
    .wa-grid {
      display: grid;
      gap: 16px;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .wa-grid .wa-full { grid-column: 1 / -1; }
    .wa-note {
      background: #f8fafc;
      border: 1px solid #dbe4ee;
      border-radius: 14px;
      color: #475569;
      font-size: 13px;
      margin-top: 18px;
      padding: 14px 16px;
    }
    @media (max-width: 767.98px) {
      .wa-hero, .wa-panel { border-radius: 16px; padding: 18px; }
      .wa-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=base_url()?>adm/usuarios/dash">Painel</a></li>
        <li class="breadcrumb-item"><span>WhatsApp</span></li>
      </ul>
      <div class="content-i">
        <div class="content-box">
          <div class="wa-shell">
            <div class="wa-hero">
              <h6 class="element-header" style="color:#f0fdf4;margin-bottom:6px;">Configuracao de disparos por WhatsApp</h6>
              <p style="margin:0;color:rgba(240,253,244,.82);">Essa configuracao abastece o checkbox de confirmacao nos agendamentos e permite trocar numero, token e template sem editar codigo.</p>
            </div>

            <? if($flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div><? } ?>
            <? if($flash_error){ ?><div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div><? } ?>
            <? if(!$whatsapp_disponivel){ ?><div class="alert alert-warning">A configuracao atual ainda nao esta pronta para disparar mensagens. Os agendamentos seguem funcionando normalmente.</div><? } ?>
            <? if(!$whatsapp_log_tabela){ ?><div class="alert alert-warning">A tabela <code>whatsapp_notificacoes</code> ainda nao existe. O envio nao vai registrar auditoria ate essa tabela ser criada.</div><? } ?>

            <div class="wa-panel">
              <form method="post" action="<?=base_url()?>adm/whatsapp/salvar">
                <input type="hidden" name="id" value="<?=isset($config->id) ? (int)$config->id : 0?>">

                <div class="wa-grid">
                  <div>
                    <label>Nome da conexao</label>
                    <input type="text" name="nome_conexao" class="form-control" value="<?=htmlspecialchars(isset($config->nome_conexao) ? $config->nome_conexao : 'Configuracao principal')?>">
                  </div>
                  <div>
                    <label>Numero remetente</label>
                    <input type="text" name="numero_remetente" class="form-control" value="<?=htmlspecialchars(isset($config->numero_remetente) ? $config->numero_remetente : '')?>" placeholder="5581999999999">
                  </div>
                  <div>
                    <label>Phone Number ID</label>
                    <input type="text" name="phone_number_id" class="form-control" value="<?=htmlspecialchars(isset($config->phone_number_id) ? $config->phone_number_id : '')?>">
                  </div>
                  <div>
                    <label>WABA ID</label>
                    <input type="text" name="waba_id" class="form-control" value="<?=htmlspecialchars(isset($config->waba_id) ? $config->waba_id : '')?>">
                  </div>
                  <div class="wa-full">
                    <label>Access Token</label>
                    <textarea name="access_token" class="form-control" rows="3"><?=htmlspecialchars(isset($config->access_token) ? $config->access_token : '')?></textarea>
                  </div>
                  <div>
                    <label>App Secret</label>
                    <input type="text" name="app_secret" class="form-control" value="<?=htmlspecialchars(isset($config->app_secret) ? $config->app_secret : '')?>">
                  </div>
                  <div>
                    <label>Verify Token</label>
                    <input type="text" name="verify_token" class="form-control" value="<?=htmlspecialchars(isset($config->verify_token) ? $config->verify_token : '')?>">
                  </div>
                  <div>
                    <label>Template name</label>
                    <input type="text" name="template_name" class="form-control" value="<?=htmlspecialchars(isset($config->template_name) ? $config->template_name : 'confirmacao_consulta')?>">
                  </div>
                  <div>
                    <label>Template lang</label>
                    <input type="text" name="template_lang" class="form-control" value="<?=htmlspecialchars(isset($config->template_lang) ? $config->template_lang : 'pt_BR')?>">
                  </div>
                  <div class="wa-full">
                    <div class="custom-control custom-switch" style="margin-top:8px;">
                      <input type="checkbox" class="custom-control-input" id="wa-status" name="status" value="1" <?=(!isset($config->status) || (int)$config->status === 1) ? 'checked' : ''?>>
                      <label class="custom-control-label" for="wa-status">Configuracao ativa para os novos agendamentos</label>
                    </div>
                  </div>
                </div>

                <div class="wa-note">
                  O template aprovado na Meta deve aceitar 5 variaveis no corpo, nesta ordem: paciente, tipo, data, horario e profissional.
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;">
                  <button type="submit" class="btn btn-primary">Salvar configuracao</button>
                  <a href="<?=base_url()?>adm/usuarios/dash" class="btn btn-secondary">Voltar ao painel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
<script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
<script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
</body>
</html>
