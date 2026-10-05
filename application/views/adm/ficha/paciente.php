<!DOCTYPE html>
<html>
<head>
  <title>Ficha do paciente</title>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
  <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
  <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
  <style>
    .fc-shell { max-width: 980px; }
    .fc-panel { background:#fff; border:1px solid #dbe4ee; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); padding:22px; margin-bottom:20px; }
    .fc-panel h5 { font-weight:800; color:#0f172a; margin-bottom:12px; }
    .fc-sub { color:#64748b; font-size:13px; margin-bottom:16px; }
  </style>
</head>
<body class="menu-position-side menu-side-left full-screen with-content-panel">
<?php
$e = function($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$f = $ficha;
$ro = $pode_editar_saude ? '' : ' readonly';
?>
<div class="all-wrapper with-side-panel solid-bg-all">
  <? include("includes/adm/search.php"); ?>
  <div class="layout-w">
    <? include("includes/adm/menu.php"); ?>
    <div class="content-w">
      <? include("includes/adm/top.php"); ?>
      <div class="content-i">
        <div class="content-box">
          <div class="fc-shell">
            <h4 style="font-weight:800;color:#0f172a;">Ficha do paciente</h4>
            <p class="fc-sub"><?=$e($paciente->nome)?> · <a href="<?=base_url('adm/atendimento/prontuario/'.(int)$paciente->id)?>">voltar ao prontuário</a></p>

            <? if($flash_erro){ ?><div class="alert alert-danger"><?=$e($flash_erro)?></div><? } ?>
            <? if(!$schema_ok){ ?><div class="alert alert-warning">As tabelas da ficha ainda não foram criadas. Peça ao administrador para executar <code>adm/dev/migrar_ficha_pacientes</code>.</div><? } ?>

            <form method="post" action="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>">
              <div class="fc-panel">
                <h5>Pessoal e responsável</h5>
                <div class="form-row">
                  <div class="form-group col-md-6"><label for="fc-nome-social">Nome social</label><input id="fc-nome-social" name="nome_social" maxlength="120" class="form-control" value="<?=$e($f['nome_social'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-sexo">Sexo</label>
                    <select id="fc-sexo" name="sexo" class="form-control"><option value="">Não informado</option>
                      <? foreach($opcoes_sexo as $k => $r){ if($k === 'nao_informado'){ continue; } ?><option value="<?=$e($k)?>" <?=$f['sexo'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                  <div class="form-group col-md-3"><label for="fc-estado-civil">Estado civil</label>
                    <select id="fc-estado-civil" name="estado_civil" class="form-control"><option value="">Não informado</option>
                      <? foreach($opcoes_estado_civil as $k => $r){ if($k === 'nao_informado'){ continue; } ?><option value="<?=$e($k)?>" <?=$f['estado_civil'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4"><label for="fc-resp-nome">Responsável legal</label><input id="fc-resp-nome" name="responsavel_nome" maxlength="120" class="form-control" value="<?=$e($f['responsavel_nome'])?>"></div>
                  <div class="form-group col-md-2"><label for="fc-resp-par">Parentesco</label><input id="fc-resp-par" name="responsavel_parentesco" maxlength="40" class="form-control" value="<?=$e($f['responsavel_parentesco'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-resp-tel">Telefone</label><input id="fc-resp-tel" name="responsavel_telefone" inputmode="tel" class="form-control" value="<?=$e(utec_ficha_telefone_fmt($f['responsavel_telefone']))?>"></div>
                  <div class="form-group col-md-3"><label for="fc-resp-cpf">CPF do responsável</label><input id="fc-resp-cpf" name="responsavel_cpf" inputmode="numeric" class="form-control" value="<?=$e($f['responsavel_cpf'])?>"></div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-5"><label for="fc-emg-nome">Contato de emergência</label><input id="fc-emg-nome" name="emergencia_nome" maxlength="120" class="form-control" value="<?=$e($f['emergencia_nome'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-emg-par">Parentesco</label><input id="fc-emg-par" name="emergencia_parentesco" maxlength="40" class="form-control" value="<?=$e($f['emergencia_parentesco'])?>"></div>
                  <div class="form-group col-md-4"><label for="fc-emg-tel">Telefone</label><input id="fc-emg-tel" name="emergencia_telefone" inputmode="tel" class="form-control" value="<?=$e(utec_ficha_telefone_fmt($f['emergencia_telefone']))?>"></div>
                </div>
              </div>

              <div class="fc-panel">
                <h5>Saúde básica</h5>
                <? if(!$pode_editar_saude){ ?><p class="fc-sub">Somente o estabelecimento e o profissional editam dados de saúde.</p><? } ?>
                <? if($f['saude_atualizado_em'] !== ''){ ?><p class="fc-sub">Última alteração de saúde: <?=$e($f['saude_atualizado_por_nome'] !== '' ? $f['saude_atualizado_por_nome'] : 'usuário #'.$f['saude_atualizado_por'])?> em <?=$e(utec_ficha_data_br($f['saude_atualizado_em']))?> <?=$e(substr($f['saude_atualizado_em'], 11, 5))?></p><? } ?>
                <div class="form-row">
                  <div class="form-group col-md-3"><label for="fc-ts">Tipo sanguíneo</label>
                    <select id="fc-ts" name="tipo_sanguineo" class="form-control" <?=$pode_editar_saude ? '' : 'disabled'?>><option value="">Não informado</option>
                      <? foreach($opcoes_tipo_sanguineo as $k => $r){ ?><option value="<?=$e($k)?>" <?=$f['tipo_sanguineo'] === $k ? 'selected' : ''?>><?=$e($r)?></option><? } ?>
                    </select></div>
                </div>
                <div class="form-group"><label for="fc-alergias">Alergias</label><textarea id="fc-alergias" name="alergias" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['alergias'])?></textarea></div>
                <div class="form-group"><label for="fc-medicamentos">Medicamentos em uso</label><textarea id="fc-medicamentos" name="medicamentos" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['medicamentos'])?></textarea></div>
                <div class="form-group"><label for="fc-comorbidades">Comorbidades / doenças crônicas</label><textarea id="fc-comorbidades" name="comorbidades" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['comorbidades'])?></textarea></div>
                <div class="form-group"><label for="fc-obs-saude">Observações de saúde</label><textarea id="fc-obs-saude" name="obs_saude" rows="2" maxlength="2000" class="form-control"<?=$ro?>><?=$e($f['obs_saude'])?></textarea></div>
              </div>

              <div class="fc-panel">
                <h5>Convênio</h5>
                <div class="form-row">
                  <div class="form-group col-md-4"><label for="fc-conv-nome">Convênio</label><input id="fc-conv-nome" name="convenio_nome" maxlength="80" class="form-control" value="<?=$e($f['convenio_nome'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-conv-plano">Plano</label><input id="fc-conv-plano" name="convenio_plano" maxlength="80" class="form-control" value="<?=$e($f['convenio_plano'])?>"></div>
                  <div class="form-group col-md-3"><label for="fc-conv-cart">Nº da carteirinha</label><input id="fc-conv-cart" name="convenio_carteirinha" maxlength="40" class="form-control" value="<?=$e($f['convenio_carteirinha'])?>"></div>
                  <div class="form-group col-md-2"><label for="fc-conv-val">Validade</label><input id="fc-conv-val" type="date" name="convenio_validade" class="form-control" value="<?=$e($f['convenio_validade'])?>"></div>
                </div>
              </div>

              <? if($schema_ok){ ?><button type="submit" class="btn btn-primary">Salvar ficha</button><? } ?>
              <a href="<?=base_url('adm/atendimento/prontuario/'.(int)$paciente->id)?>" class="btn btn-light">Cancelar</a>
            </form>
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
