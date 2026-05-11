<!DOCTYPE html>
<html>
  <head>
    <title>Editar Plano | UTEC</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
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
            <li class="breadcrumb-item"><a href="<?=base_url()?>adm/produtos">Planos</a></li>
            <li class="breadcrumb-item"><span>Editar</span></li>
          </ul>
          <div class="content-i">
            <div class="content-box">
              <div class="element-box">
                <h6 class="element-header">Editar plano</h6>
                <form method="post" action="<?=base_url()?>adm/produtos/editar">
                  <input type="hidden" name="id" value="<?=$produto->id?>">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Nome do plano</label>
                      <input type="text" name="modelo" class="form-control" value="<?=htmlspecialchars($produto->modelo)?>" required>
                    </div>
                    <div class="col-md-3">
                      <label>Tipo de plano</label>
                      <select name="id_categoria" class="form-control">
                        <? foreach($categorias->result() as $categoria){ ?>
                          <option value="<?=$categoria->id?>" <?=$produto->id_categoria == $categoria->id ? 'selected="selected"' : ''?>><?=$categoria->nome?></option>
                        <? } ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label>Valor base</label>
                      <input type="text" name="preco" class="form-control" value="<?=number_format((float)$produto->preco, 2, ',', '.')?>">
                    </div>
                    <div class="col-md-2">
                      <label>Valor de venda</label>
                      <input type="text" name="preco_venda" class="form-control" value="<?=number_format((float)$produto->preco_venda, 2, ',', '.')?>">
                    </div>
                    <div class="col-md-1">
                      <label>Status</label>
                      <select name="status" class="form-control">
                        <option value="1" <?=$produto->status == 1 ? 'selected="selected"' : ''?>>Ativo</option>
                        <option value="0" <?=$produto->status == 0 ? 'selected="selected"' : ''?>>Inativo</option>
                      </select>
                    </div>
                  </div>
                  <div class="row" style="margin-top:14px">
                    <div class="col-md-2">
                      <label>Limite/quantidade</label>
                      <input type="text" name="qtd" class="form-control" value="<?=$produto->qtd?>">
                    </div>
                    <div class="col-md-2">
                      <label>Codigo</label>
                      <input type="text" name="codigo" class="form-control" value="<?=$produto->codigo?>">
                    </div>
                    <div class="col-md-8">
                      <label>Descricao comercial</label>
                      <textarea name="especificacoes" class="form-control" rows="5"><?=htmlspecialchars($produto->especificacoes)?></textarea>
                    </div>
                  </div>
                  <div class="row" style="margin-top:14px">
                    <div class="col-md-2">
                      <label>Codigo do plano</label>
                      <input type="text" name="plan_code" class="form-control" value="<?=isset($produto->plan_code) ? htmlspecialchars($produto->plan_code) : ''?>">
                    </div>
                    <div class="col-md-2">
                      <label>Ciclo</label>
                      <select name="billing_interval" class="form-control">
                        <option value="monthly" <?=isset($produto->billing_interval) && $produto->billing_interval === 'monthly' ? 'selected="selected"' : ''?>>Mensal</option>
                        <option value="quarterly" <?=isset($produto->billing_interval) && $produto->billing_interval === 'quarterly' ? 'selected="selected"' : ''?>>Trimestral</option>
                        <option value="semiannual" <?=isset($produto->billing_interval) && $produto->billing_interval === 'semiannual' ? 'selected="selected"' : ''?>>Semestral</option>
                        <option value="yearly" <?=isset($produto->billing_interval) && $produto->billing_interval === 'yearly' ? 'selected="selected"' : ''?>>Anual</option>
                      </select>
                    </div>
                    <div class="col-md-1">
                      <label>Intervalo</label>
                      <input type="number" min="1" name="billing_interval_count" class="form-control" value="<?=isset($produto->billing_interval_count) ? (int)$produto->billing_interval_count : 1?>">
                    </div>
                    <div class="col-md-1">
                      <label>Trial</label>
                      <input type="number" min="0" name="trial_days" class="form-control" value="<?=isset($produto->trial_days) ? (int)$produto->trial_days : 0?>">
                    </div>
                    <div class="col-md-2">
                      <label>Taxa setup</label>
                      <input type="text" name="setup_fee" class="form-control" value="<?=isset($produto->setup_fee) ? number_format((float)$produto->setup_fee, 2, ',', '.') : '0,00'?>">
                    </div>
                    <div class="col-md-1">
                      <label>Prof.</label>
                      <input type="number" min="0" name="max_profissionais" class="form-control" value="<?=isset($produto->max_profissionais) ? (int)$produto->max_profissionais : 0?>">
                    </div>
                    <div class="col-md-1">
                      <label>Colab.</label>
                      <input type="number" min="0" name="max_colaboradores" class="form-control" value="<?=isset($produto->max_colaboradores) ? (int)$produto->max_colaboradores : 0?>">
                    </div>
                    <div class="col-md-1">
                      <label>Pac.</label>
                      <input type="number" min="0" name="max_pacientes" class="form-control" value="<?=isset($produto->max_pacientes) ? (int)$produto->max_pacientes : 0?>">
                    </div>
                    <div class="col-md-1">
                      <label>Publico</label>
                      <select name="saas_publicado" class="form-control">
                        <option value="1" <?=!isset($produto->saas_publicado) || (int)$produto->saas_publicado === 1 ? 'selected="selected"' : ''?>>Sim</option>
                        <option value="0" <?=isset($produto->saas_publicado) && (int)$produto->saas_publicado === 0 ? 'selected="selected"' : ''?>>Nao</option>
                      </select>
                    </div>
                  </div>
                  <div style="margin-top:16px">
                    <button type="submit" class="btn btn-primary">Salvar plano</button>
                    <a href="<?=base_url()?>adm/produtos" class="btn btn-secondary">Voltar</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
