<!DOCTYPE html>
<html>
<head>
  <title>Rótulos de pacientes</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .rt-shell { max-width: 960px; }
    .rt-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .rt-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
    .rt-linha { display:flex; align-items:center; gap:12px; padding:10px 0; border-top:1px solid #eef2f7; flex-wrap:wrap; }
    .rt-linha:first-of-type { border-top:0; }
    .rt-linha.is-off { opacity:.5; }
    .rt-cores { display:flex; gap:8px; flex-wrap:wrap; }
    .rt-cor input { position:absolute; opacity:0; }
    .rt-cor span { display:inline-block; width:26px; height:26px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 0 1px #cbd5e1; cursor:pointer; }
    .rt-cor input:checked + span { box-shadow:0 0 0 2px #0f172a; }
    .rt-cor input:focus-visible + span { outline:2px solid #2563eb; outline-offset:2px; }
    .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:1px 10px; font-size:12px; font-weight:700; background:#fff; margin:2px 4px 2px 0; }
    .ut-rotulo-alerta { background:#fef2f2; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="rt-shell">
            <h4 style="font-weight:800;color:#0f172a;">Rótulos de pacientes</h4>
            <p class="rt-sub">Etiquetas para organizar pacientes e destacar alertas. Rótulos marcados como <strong>alerta</strong> aparecem em destaque no prontuário e na agenda.</p>

            <? if($flash_ok){ ?><div class="alert alert-success"><?=htmlspecialchars($flash_ok)?></div><? } ?>
            <? if($flash_erro){ ?><div class="alert alert-danger"><?=htmlspecialchars($flash_erro)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas de rótulos ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_rotulos_pacientes</code>.</div><? } ?>

            <? if($eh_admin){ ?>
            <div class="rt-panel">
              <form method="get" action="<?=base_url('adm/rotulos')?>" class="form-inline" style="gap:8px;">
                <label for="rt-conta" class="mr-2">ID de um usuário da clínica</label>
                <input type="number" min="1" id="rt-conta" name="conta" class="form-control form-control-sm mr-2" value="<?=$conta > 0 ? (int)$conta : ''?>">
                <button type="submit" class="btn btn-sm btn-primary">Abrir catálogo</button>
              </form>
              <small class="text-muted">O catálogo é da clínica (estabelecimento ou profissional autônomo) à qual o usuário pertence.<? if($conta > 0){ ?> Conta atual: #<?=(int)$conta?>.<? } ?></small>
            </div>
            <? } ?>

            <? if($schema_ok && $conta > 0){ ?>
            <div class="rt-panel">
              <h5 style="font-weight:800;">Novo rótulo</h5>
              <form method="post" action="<?=base_url('adm/rotulos/salvar')?>">
                <input type="hidden" name="conta" value="<?=(int)$conta?>">
                <div class="form-group">
                  <label for="rt-nome">Nome</label>
                  <input type="text" id="rt-nome" name="nome" maxlength="40" required class="form-control" placeholder="Ex.: VIP, Convênio X, Alérgico a dipirona">
                </div>
                <div class="form-group">
                  <span class="d-block mb-1">Cor</span>
                  <div class="rt-cores" role="radiogroup" aria-label="Cor do rótulo">
                    <? foreach($paleta as $chave => $hex){ ?>
                      <label class="rt-cor" title="<?=htmlspecialchars($chave)?>"><input type="radio" name="cor" value="<?=htmlspecialchars($chave)?>" aria-label="<?=htmlspecialchars($chave)?>" <?=$chave === 'azul' ? 'checked' : ''?>><span style="background:<?=$hex?>"></span></label>
                    <? } ?>
                  </div>
                </div>
                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="rt-alerta" name="alerta" value="1">
                  <label class="form-check-label" for="rt-alerta">É um alerta (aparece em destaque no prontuário e na agenda)</label>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar rótulo</button>
              </form>
            </div>

            <div class="rt-panel">
              <h5 style="font-weight:800;">Rótulos da clínica</h5>
              <? if(empty($rotulos)){ ?><p class="rt-sub">Nenhum rótulo cadastrado.</p><? } ?>
              <? foreach($rotulos as $r){ $ativo = (int)$r->status === 1; ?>
                <div class="rt-linha<?=$ativo ? '' : ' is-off'?>">
                  <div style="min-width:180px;"><?=utec_rotulos_chips_html(array($r))?></div>
                  <details style="flex:1;min-width:240px;">
                    <summary class="btn btn-sm btn-light">Editar</summary>
                    <form method="post" action="<?=base_url('adm/rotulos/salvar')?>" class="mt-2">
                      <input type="hidden" name="conta" value="<?=(int)$conta?>">
                      <input type="hidden" name="id" value="<?=(int)$r->id?>">
                      <input type="text" name="nome" maxlength="40" required class="form-control form-control-sm mb-2" value="<?=htmlspecialchars($r->nome, ENT_QUOTES, 'UTF-8')?>" aria-label="Nome do rótulo">
                      <div class="rt-cores mb-2">
                        <? foreach($paleta as $chave => $hex){ ?>
                          <label class="rt-cor" title="<?=htmlspecialchars($chave)?>"><input type="radio" name="cor" value="<?=htmlspecialchars($chave)?>" aria-label="<?=htmlspecialchars($chave)?>" <?=$chave === $r->cor ? 'checked' : ''?>><span style="background:<?=$hex?>"></span></label>
                        <? } ?>
                      </div>
                      <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="rt-alerta-<?=(int)$r->id?>" name="alerta" value="1" <?=(int)$r->alerta === 1 ? 'checked' : ''?>>
                        <label class="form-check-label" for="rt-alerta-<?=(int)$r->id?>">Alerta</label>
                      </div>
                      <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                    </form>
                  </details>
                  <form method="post" action="<?=base_url('adm/rotulos/status/'.(int)$r->id)?>">
                    <input type="hidden" name="conta" value="<?=(int)$conta?>">
                    <button type="submit" class="btn btn-sm <?=$ativo ? 'btn-outline-secondary' : 'btn-outline-success'?>"><?=$ativo ? 'Desativar' : 'Ativar'?></button>
                  </form>
                </div>
              <? } ?>
            </div>
            <? } ?>
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
