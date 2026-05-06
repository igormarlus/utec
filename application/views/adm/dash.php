<!DOCTYPE html>
<html>
  <head>
    <title>Painel Clinico</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="painel clinico utec saude" name="keywords">
    <meta content="Painel principal com metricas clinicas, pacientes e agenda." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="favicon.png" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/dropzone/dist/dropzone.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .utec-stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        height: 100%;
      }
      .utec-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        margin-bottom: 8px;
      }
      .utec-stat-value {
        font-size: 30px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
      }
      .utec-stat-note {
        font-size: 13px;
        color: #475569;
        margin-top: 8px;
      }
      .utec-panel {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
      }
      .utec-panel-header {
        padding: 18px 20px 0;
      }
      .utec-panel-body {
        padding: 18px 20px 20px;
      }
      .utec-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
      }
      .utec-badge-pendente { background: #fef3c7; color: #92400e; }
      .utec-badge-confirmado { background: #dbeafe; color: #1d4ed8; }
      .utec-badge-finalizado { background: #dcfce7; color: #166534; }
      .utec-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
      }
      .utec-list-item:last-child {
        border-bottom: 0;
      }
      .utec-list-main {
        min-width: 0;
      }
      .utec-list-title {
        font-weight: 600;
        color: #0f172a;
      }
      .utec-list-subtitle {
        font-size: 13px;
        color: #64748b;
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
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Painel</a>
            </li>
            <li class="breadcrumb-item">
              <span>Visao Clinica</span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <div class="element-actions">
                  <a href="<?=base_url()?>adm/atendimento" class="btn btn-primary btn-sm">Abrir agenda</a>
                </div>
                <h6 class="element-header">
                  Painel clinico
                </h6>
                <div class="element-box-tp">
                  Visao resumida da operacao, com foco na agenda, pacientes e acompanhamentos do dia.
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="utec-stat-card">
                    <div class="utec-stat-label">Agendados hoje</div>
                    <div class="utec-stat-value"><?=$metricas['agendados_hoje']?></div>
                    <div class="utec-stat-note">Consultas e atendimentos previstos para <?=date('d/m/Y')?></div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="utec-stat-card">
                    <div class="utec-stat-label">Confirmados hoje</div>
                    <div class="utec-stat-value"><?=$metricas['confirmados_hoje']?></div>
                    <div class="utec-stat-note">Agendamentos ja confirmados pela equipe</div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="utec-stat-card">
                    <div class="utec-stat-label">Finalizados hoje</div>
                    <div class="utec-stat-value"><?=$metricas['finalizados_hoje']?></div>
                    <div class="utec-stat-note">Atendimentos concluidos e registrados</div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="utec-stat-card">
                    <div class="utec-stat-label">Pacientes ativos</div>
                    <div class="utec-stat-value"><?=$metricas['pacientes_ativos']?></div>
                    <div class="utec-stat-note">Pacientes com historico na sua operacao</div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-8">
                  <div class="utec-panel">
                    <div class="utec-panel-header">
                      <h6 class="element-header" style="margin-bottom:0;">Agenda de hoje</h6>
                    </div>
                    <div class="utec-panel-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Horario</th>
                              <th>Paciente</th>
                              <th>Profissional</th>
                              <th>Tipo</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($agendamentos_hoje->num_rows() > 0){ ?>
                              <? foreach($agendamentos_hoje->result() as $agenda){ ?>
                                <tr>
                                  <td><?=$agenda->hora_agenda?></td>
                                  <td><?=$agenda->paciente_nome?></td>
                                  <td><?=$agenda->prestador_nome?></td>
                                  <td><?=$agenda->tipo?></td>
                                  <td>
                                    <? if($agenda->status == 0){ ?>
                                      <span class="utec-badge utec-badge-pendente">Pendente</span>
                                    <? } elseif($agenda->status == 1){ ?>
                                      <span class="utec-badge utec-badge-confirmado">Confirmado</span>
                                    <? } else { ?>
                                      <span class="utec-badge utec-badge-finalizado">Finalizado</span>
                                    <? } ?>
                                  </td>
                                </tr>
                              <? } ?>
                            <? } else { ?>
                              <tr>
                                <td colspan="5">Nenhum agendamento encontrado para hoje.</td>
                              </tr>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4">
                  <div class="utec-panel mb-4">
                    <div class="utec-panel-header">
                      <h6 class="element-header" style="margin-bottom:0;">Proximos acompanhamentos</h6>
                    </div>
                    <div class="utec-panel-body">
                      <? if($proximos_agendamentos->num_rows() > 0){ ?>
                        <? foreach($proximos_agendamentos->result() as $agenda){ ?>
                          <div class="utec-list-item">
                            <div class="utec-list-main">
                              <div class="utec-list-title"><?=$agenda->paciente_nome?></div>
                              <div class="utec-list-subtitle"><?=$this->padrao_model->converte_data($agenda->data_agenda)?> as <?=$agenda->hora_agenda?></div>
                            </div>
                            <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$agenda->id_paciente?>/<?=$agenda->id?>" class="btn btn-outline-primary btn-sm">Abrir</a>
                          </div>
                        <? } ?>
                      <? } else { ?>
                        <div class="utec-list-subtitle">Sem acompanhamentos recentes para exibir.</div>
                      <? } ?>
                    </div>
                  </div>

                  <div class="utec-panel">
                    <div class="utec-panel-header">
                      <h6 class="element-header" style="margin-bottom:0;">Pacientes recentes</h6>
                    </div>
                    <div class="utec-panel-body">
                      <? if($pacientes_recentes->num_rows() > 0){ ?>
                        <? foreach($pacientes_recentes->result() as $paciente){ ?>
                          <div class="utec-list-item">
                            <div class="utec-list-main">
                              <div class="utec-list-title"><?=$paciente->nome?></div>
                              <div class="utec-list-subtitle"><?=$paciente->telefone?><? if($paciente->email){ echo ' • '.$paciente->email; } ?></div>
                            </div>
                            <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$paciente->id?>" class="btn btn-outline-secondary btn-sm">Ver</a>
                          </div>
                        <? } ?>
                      <? } else { ?>
                        <div class="utec-list-subtitle">Nenhum paciente recente localizado.</div>
                      <? } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="display-type"></div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>bower_components/moment/moment.js"></script>
    <script src="<?=base_url()?>bower_components/chart.js/dist/Chart.min.js"></script>
    <script src="<?=base_url()?>bower_components/select2/dist/js/select2.full.min.js"></script>
    <script src="<?=base_url()?>bower_components/jquery-bar-rating/dist/jquery.barrating.min.js"></script>
    <script src="<?=base_url()?>bower_components/ckeditor/ckeditor.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap-validator/dist/validator.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="<?=base_url()?>bower_components/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <script src="<?=base_url()?>bower_components/dropzone/dist/dropzone.js"></script>
    <script src="<?=base_url()?>bower_components/editable-table/mindmup-editabletable.js"></script>
    <script src="<?=base_url()?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="<?=base_url()?>bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/tether/dist/js/tether.min.js"></script>
    <script src="<?=base_url()?>bower_components/slick-carousel/slick/slick.min.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/util.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/alert.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/button.js"></script>
    <script src="<?=base_url()?>bower_components/bootstrap/js/dist/carousel.js"></script>
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
