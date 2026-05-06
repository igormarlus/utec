<!DOCTYPE html>
<html>
  <head>
    <title>Relatorios Clinicos</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="relatorios clinicos utec saude" name="keywords">
    <meta content="Relatorios clinicos com indicadores de atendimentos, pacientes e exames." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .report-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        height: 100%;
        padding: 18px 20px;
      }
      .report-label {
        color: #64748b;
        font-size: 12px;
        letter-spacing: .08em;
        margin-bottom: 8px;
        text-transform: uppercase;
      }
      .report-value {
        color: #0f172a;
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
      }
      .report-note {
        color: #475569;
        font-size: 13px;
        margin-top: 8px;
      }
      .report-panel {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
      }
      .report-panel-header {
        padding: 18px 20px 0;
      }
      .report-panel-body {
        padding: 18px 20px 20px;
      }
      .report-table td, .report-table th {
        vertical-align: middle;
      }
      .report-filter-box {
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
      }
      .status-badge {
        display: inline-block;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
      }
      .status-pendente { background: #fee2e2; color: #b91c1c; }
      .status-atendimento { background: #dcfce7; color: #166534; }
      .status-finalizado { background: #fef3c7; color: #92400e; }
      .status-solicitado { background: #dbeafe; color: #1d4ed8; }
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
              <span>Relatorios clinicos</span>
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
                  Relatorios clinicos
                </h6>
                <div class="element-box report-panel report-filter-box">
                  <form method="get" action="<?=base_url()?>adm/usuarios/relatorios_clinicos">
                    <div class="row">
                      <div class="col-md-3">
                        <label>Data inicial</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?=$filtros['data_inicio']?>">
                      </div>
                      <div class="col-md-3">
                        <label>Data final</label>
                        <input type="date" name="data_fim" class="form-control" value="<?=$filtros['data_fim']?>">
                      </div>
                      <div class="col-md-4">
                        <label>Profissional</label>
                        <select name="id_prestador" class="form-control">
                          <option value="0">Todos os profissionais</option>
                          <? foreach($prestadores->result() as $prestador){ ?>
                            <option value="<?=$prestador->id?>" <?=$filtros['id_prestador'] == $prestador->id ? 'selected="selected"' : ''?>><?=$prestador->nome?></option>
                          <? } ?>
                        </select>
                      </div>
                      <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Atendimentos</div>
                    <div class="report-value"><?=$metricas_relatorio['atendimentos']?></div>
                    <div class="report-note">no periodo selecionado</div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Finalizados</div>
                    <div class="report-value"><?=$metricas_relatorio['finalizados']?></div>
                    <div class="report-note">consultas concluidas</div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Pendentes</div>
                    <div class="report-value"><?=$metricas_relatorio['pendentes']?></div>
                    <div class="report-note">atendimentos em aberto</div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Pacientes ativos</div>
                    <div class="report-value"><?=$metricas_relatorio['pacientes_ativos']?></div>
                    <div class="report-note">com movimentacao</div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Exames</div>
                    <div class="report-value"><?=$metricas_relatorio['exames_solicitados']?></div>
                    <div class="report-note">solicitados no periodo</div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                  <div class="report-card">
                    <div class="report-label">Exames pendentes</div>
                    <div class="report-value"><?=$metricas_relatorio['exames_pendentes']?></div>
                    <div class="report-note">aguardando retorno</div>
                  </div>
                </div>
              </div>

              <div class="report-panel">
                <div class="report-panel-header">
                  <h6 class="element-header" style="margin-bottom:0">Resumo por profissional</h6>
                </div>
                <div class="report-panel-body">
                  <div class="table-responsive">
                    <table class="table table-lightborder report-table">
                      <thead>
                        <tr>
                          <th>Profissional</th>
                          <th>Atendimentos</th>
                          <th>Finalizados</th>
                          <th>Pacientes atendidos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <? if($resumo_profissionais->num_rows() > 0){ foreach($resumo_profissionais->result() as $linha){ ?>
                          <tr>
                            <td><?=$linha->prestador_nome ? $linha->prestador_nome : 'Nao informado'?></td>
                            <td><?=$linha->total_atendimentos?></td>
                            <td><?=$linha->total_finalizados?></td>
                            <td><?=$linha->total_pacientes?></td>
                          </tr>
                        <? } } else { ?>
                          <tr>
                            <td colspan="4">Nenhum dado encontrado para os filtros selecionados.</td>
                          </tr>
                        <? } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-7">
                  <div class="report-panel">
                    <div class="report-panel-header">
                      <h6 class="element-header" style="margin-bottom:0">Atendimentos recentes</h6>
                    </div>
                    <div class="report-panel-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder report-table">
                          <thead>
                            <tr>
                              <th>Paciente</th>
                              <th>Profissional</th>
                              <th>Data</th>
                              <th>Status</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($atendimentos_recentes->num_rows() > 0){ foreach($atendimentos_recentes->result() as $agenda){ ?>
                              <tr>
                                <td><?=$agenda->paciente_nome?></td>
                                <td><?=$agenda->prestador_nome ? $agenda->prestador_nome : 'Nao informado'?></td>
                                <td><?=$this->padrao_model->converte_data($agenda->data_agenda)?> as <?=substr($agenda->hora_agenda,0,5)?>h</td>
                                <td>
                                  <? if((int)$agenda->status === 2){ ?>
                                    <span class="status-badge status-finalizado">Finalizado</span>
                                  <? } elseif((int)$agenda->status === 1){ ?>
                                    <span class="status-badge status-atendimento">Em atendimento</span>
                                  <? } else { ?>
                                    <span class="status-badge status-pendente">Pendente</span>
                                  <? } ?>
                                </td>
                                <td>
                                  <a href="<?=base_url('adm/usuarios/prontuario/'.$agenda->id_paciente.'/'.$agenda->id)?>" class="btn btn-sm btn-outline-primary">Prontuario</a>
                                </td>
                              </tr>
                            <? } } else { ?>
                              <tr>
                                <td colspan="5">Nenhum atendimento encontrado.</td>
                              </tr>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5">
                  <div class="report-panel">
                    <div class="report-panel-header">
                      <h6 class="element-header" style="margin-bottom:0">Exames pendentes</h6>
                    </div>
                    <div class="report-panel-body">
                      <div class="table-responsive">
                        <table class="table table-lightborder report-table">
                          <thead>
                            <tr>
                              <th>Paciente</th>
                              <th>Exame</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <? if($exames_pendentes_lista->num_rows() > 0){ foreach($exames_pendentes_lista->result() as $exame){ ?>
                              <tr>
                                <td>
                                  <?=$exame->paciente_nome?><br>
                                  <small><?=$this->padrao_model->converte_data($exame->data_agenda)?> as <?=substr($exame->hora_agenda,0,5)?>h</small>
                                </td>
                                <td><?=$exame->exame_nome ? $exame->exame_nome : 'Nao identificado'?></td>
                                <td>
                                  <? if((string)$exame->status === '1'){ ?>
                                    <span class="status-badge status-solicitado">Solicitado</span>
                                  <? } else { ?>
                                    <span class="status-badge status-pendente">Pendente</span>
                                  <? } ?>
                                </td>
                              </tr>
                            <? } } else { ?>
                              <tr>
                                <td colspan="3">Nenhum exame pendente neste periodo.</td>
                              </tr>
                            <? } ?>
                          </tbody>
                        </table>
                      </div>
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
    <script src="<?=base_url()?>bower_components/select2/dist/js/select2.full.min.js"></script>
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
