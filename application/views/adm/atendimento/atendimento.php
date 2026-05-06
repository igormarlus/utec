<!DOCTYPE html>
<html>
  <head>
    <title>Novo Agendamento</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="novo atendimento clinico utec saude" name="keywords">
    <meta content="Agendamento de consulta ou exame com contexto do paciente." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .booking-summary {
        background: linear-gradient(135deg, #f4f8ff 0%, #ffffff 100%);
        border: 1px solid #d7e3f7;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(40, 72, 120, 0.08);
        margin-bottom: 24px;
        padding: 24px;
      }
      .booking-form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        padding: 24px;
      }
      .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-top: 18px;
      }
      .summary-item {
        background: #fff;
        border: 1px solid #e6edf7;
        border-radius: 14px;
        padding: 14px 16px;
      }
      .summary-label {
        color: #7d8aa5;
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        margin-bottom: 4px;
        text-transform: uppercase;
      }
    </style>
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper with-side-panel solid-bg-all">
      <? include("includes/adm/search.php"); ?>
      <div class="layout-w">
        <? include("includes/adm/paciente/menu.php"); ?>
        <div class="content-w">
          <? include("includes/adm/top.php"); ?>
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Painel</a>
            </li>
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/atendimento">Agenda</a>
            </li>
            <li class="breadcrumb-item">
              <span>Novo agendamento</span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="booking-summary">
                <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:16px">
                  <div>
                    <h6 class="element-header" style="margin-bottom:6px">Novo agendamento para <?=$dd->nome?></h6>
                    <p style="margin:0;color:#5f708c">Defina profissional, tipo de atendimento, data e horario para manter o fluxo clinico organizado.</p>
                  </div>
                  <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$dd->id?>" class="btn btn-outline-primary">Abrir prontuario</a>
                    <a href="<?=base_url()?>adm/atendimento" class="btn btn-secondary">Voltar para agenda</a>
                  </div>
                </div>
                <div class="summary-grid">
                  <div class="summary-item">
                    <span class="summary-label">Paciente</span>
                    <strong><?=$dd->nome?></strong>
                  </div>
                  <div class="summary-item">
                    <span class="summary-label">Telefone</span>
                    <strong><?=$dd->telefone ? $dd->telefone : 'Nao informado'?></strong>
                  </div>
                  <div class="summary-item">
                    <span class="summary-label">E-mail</span>
                    <strong><?=$dd->email ? $dd->email : 'Nao informado'?></strong>
                  </div>
                </div>
              </div>

              <div class="booking-form-card">
                <h6 class="element-header">Dados do agendamento</h6>
                <form id="form" name="form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/cadastrar">
                  <input type="hidden" value="<?=$dd->id?>" name="id_paciente">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Profissional</label>
                      <select name="id_prestador" class="form-control" required>
                        <? foreach($prestadores->result() as $prest){ ?>
                          <option value="<?=$prest->id?>" <?=$prestador_padrao == $prest->id ? 'selected="selected"' : ''?>><?=$prest->nome?></option>
                        <? } ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Tipo</label>
                      <select name="tipo" class="form-control" required>
                        <option value="Consulta">Consulta</option>
                        <option value="Exame">Exame</option>
                        <option value="Retorno">Retorno</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Data</label>
                      <input type="date" name="data_agenda" class="form-control" value="<?=date('Y-m-d')?>" required>
                    </div>
                    <div class="col-md-2">
                      <label>Horario</label>
                      <input type="time" name="hora_agenda" class="form-control" required>
                    </div>
                  </div>
                  <div style="margin-top:18px">
                    <button class="btn btn-primary" type="submit">Confirmar agendamento</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="display-type"></div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/slick-carousel/slick/slick.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/alert.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/button.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/collapse.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/dropdown.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/modal.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tab.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/tooltip.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/popover.js"></script>
    <script src="<?=base_url()?>js/demo_customizer.js?version=4.5.0"></script>
    <script src="<?=base_url()?>js/main.js?version=4.5.0"></script>
  </body>
</html>
