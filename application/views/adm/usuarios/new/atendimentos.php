<!DOCTYPE html>
<html>
  <head>
    <title>Agenda Clinica</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="agenda clinica utec saude" name="keywords">
    <meta content="Agenda clinica com filtros, status operacionais e atalhos de atendimento." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .agenda-stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        height: 100%;
        padding: 18px 20px;
      }
      .agenda-stat-label {
        color: #64748b;
        font-size: 12px;
        letter-spacing: .08em;
        margin-bottom: 8px;
        text-transform: uppercase;
      }
      .agenda-stat-value {
        color: #0f172a;
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
      }
      .agenda-filter-card,
      .agenda-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
      }
      .agenda-filter-card { padding: 20px; margin-bottom: 24px; }
      .agenda-panel-header { padding: 18px 20px 0; }
      .agenda-panel-body { padding: 18px 20px 20px; }
      .status-pill {
        align-items: center;
        border-radius: 999px;
        display: inline-flex;
        font-size: 11px;
        font-weight: 700;
        gap: 6px;
        justify-content: center;
        letter-spacing: .04em;
        line-height: 1.2;
        min-height: 30px;
        padding: 6px 12px;
        text-transform: uppercase;
        white-space: nowrap;
      }
      .status-pendente { background: #fee2e2; color: #b91c1c; }
      .status-atendimento { background: #dcfce7; color: #166534; }
      .status-finalizado { background: #fef3c7; color: #92400e; }
      .status-cancelado { background: #e2e8f0; color: #475569; }
      .agenda-table {
        min-width: 980px;
      }
      .agenda-table td,
      .agenda-table th {
        vertical-align: middle;
      }
      .agenda-table th:nth-child(1),
      .agenda-table td:nth-child(1) {
        min-width: 92px;
      }
      .agenda-table th:nth-child(2),
      .agenda-table td:nth-child(2) {
        min-width: 220px;
      }
      .agenda-table th:nth-child(4),
      .agenda-table td:nth-child(4) {
        min-width: 130px;
      }
      .agenda-table th:nth-child(5),
      .agenda-table td:nth-child(5) {
        min-width: 130px;
      }
      .agenda-table th:nth-child(6),
      .agenda-table td:nth-child(6) {
        min-width: 110px;
      }
      .agenda-table th:nth-child(7),
      .agenda-table td:nth-child(7) {
        min-width: 240px;
      }
      .patient-cell {
        align-items: center;
        display: flex;
        gap: 12px;
      }
      .patient-avatar {
        align-items: center;
        background: #e2e8f0;
        border-radius: 999px;
        color: #334155;
        display: inline-flex;
        font-weight: 700;
        height: 42px;
        justify-content: center;
        overflow: hidden;
        width: 42px;
      }
      .patient-avatar img {
        height: 100%;
        object-fit: cover;
        width: 100%;
      }
      .patient-name {
        color: #0f172a;
        font-weight: 700;
      }
      .patient-subtitle {
        color: #64748b;
        font-size: 12px;
      }
      .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
      }
      .empty-state {
        color: #64748b;
        padding: 26px 12px;
        text-align: center;
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
              <span>Agenda clinica</span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <div class="element-actions">
                  <a href="<?=base_url()?>adm/usuarios/rel/5" class="btn btn-outline-primary btn-sm">Ver pacientes</a>
                </div>
                <h6 class="element-header">Agenda clinica</h6>
                <p style="color:#64748b">Acompanhe os atendimentos do dia, altere status rapidamente e abra o prontuario sem sair do fluxo.</p>
              </div>

              <div class="agenda-filter-card">
                <form method="get" action="<?=base_url()?>adm/atendimento">
                  <div class="row">
                    <div class="col-md-3">
                      <label>Data</label>
                      <input type="date" name="data_agenda" class="form-control" value="<?=$filtros['data_agenda']?>">
                    </div>
                    <div class="col-md-3">
                      <label>Status</label>
                      <select name="status" class="form-control">
                        <option value="" <?=$filtros['status'] === '' ? 'selected="selected"' : ''?>>Todos</option>
                        <option value="0" <?=$filtros['status'] === '0' ? 'selected="selected"' : ''?>>Pendentes</option>
                        <option value="1" <?=$filtros['status'] === '1' ? 'selected="selected"' : ''?>>Em atendimento</option>
                        <option value="2" <?=$filtros['status'] === '2' ? 'selected="selected"' : ''?>>Finalizados</option>
                        <option value="3" <?=$filtros['status'] === '3' ? 'selected="selected"' : ''?>>Cancelados</option>
                      </select>
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

              <div class="row">
                <div class="col-lg-3 col-md-6">
                  <div class="agenda-stat-card">
                    <div class="agenda-stat-label">Atendimentos</div>
                    <div class="agenda-stat-value"><?=$metricas_agenda['total']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">para a selecao atual</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="agenda-stat-card">
                    <div class="agenda-stat-label">Pendentes</div>
                    <div class="agenda-stat-value"><?=$metricas_agenda['pendentes']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">aguardando inicio</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="agenda-stat-card">
                    <div class="agenda-stat-label">Em atendimento</div>
                    <div class="agenda-stat-value"><?=$metricas_agenda['em_atendimento']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">atendimentos ativos</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="agenda-stat-card">
                    <div class="agenda-stat-label">Finalizados</div>
                    <div class="agenda-stat-value"><?=$metricas_agenda['finalizados']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">encerrados no dia</div>
                  </div>
                </div>
              </div>

              <div class="agenda-panel" style="margin-top:24px">
                <div class="agenda-panel-header">
                  <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px">
                    <div>
                      <h6 class="element-header" style="margin-bottom:4px">Lista operacional da agenda</h6>
                      <p style="margin:0;color:#64748b">Use os atalhos para iniciar, concluir ou abrir o prontuario do paciente.</p>
                    </div>
                    <a href="<?=base_url()?>adm/usuarios/rel/5" class="btn btn-success btn-sm">Novo agendamento via paciente</a>
                  </div>
                </div>
                <div class="agenda-panel-body">
                  <div id="remarcacao-box" class="agenda-filter-card" style="display:none;margin-bottom:20px;padding:16px">
                    <form method="post" action="<?=base_url()?>adm/atendimento/remarcar_agenda">
                      <input type="hidden" name="id_agenda" id="remarcar-id-agenda">
                      <div class="row">
                        <div class="col-md-4">
                          <label>Nova data</label>
                          <input type="date" name="data_agenda" id="remarcar-data" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                          <label>Novo horario</label>
                          <input type="time" name="hora_agenda" id="remarcar-hora" class="form-control" required>
                        </div>
                        <div class="col-md-5 d-flex align-items-end" style="gap:8px">
                          <button type="submit" class="btn btn-primary">Salvar remarcacao</button>
                          <button type="button" class="btn btn-secondary" id="cancelar-remarcacao">Fechar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-lightborder agenda-table">
                      <thead>
                        <tr>
                          <th>Horario</th>
                          <th>Paciente</th>
                          <th>Tipo</th>
                          <th>Profissional</th>
                          <th>Status</th>
                          <th>Contato</th>
                          <th>Acoes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <? if($qr_agendamentos->num_rows() > 0){ foreach($qr_agendamentos->result() as $agenda){ ?>
                          <? $status_class = 'status-pendente'; $status_nome = 'Pendente'; ?>
                          <? if((int)$agenda->status === 1){ $status_class = 'status-atendimento'; $status_nome = 'Em atendimento'; } ?>
                          <? if((int)$agenda->status === 2){ $status_class = 'status-finalizado'; $status_nome = 'Finalizado'; } ?>
                          <? if((int)$agenda->status === 3){ $status_class = 'status-cancelado'; $status_nome = 'Cancelado'; } ?>
                          <? $iniciais = strtoupper(substr(trim($agenda->paciente_nome), 0, 1)); ?>
                          <tr>
                            <td>
                              <strong><?=substr($agenda->hora_agenda,0,5)?>h</strong><br>
                              <small><?=$this->padrao_model->converte_data($agenda->data_agenda)?></small>
                            </td>
                            <td>
                              <div class="patient-cell">
                                <div class="patient-avatar">
                                  <? if($agenda->paciente_img != ""){ ?>
                                    <img src="<?=base_url()?>imagens/usuarios/min/<?=$agenda->paciente_img?>" alt="<?=$agenda->paciente_nome?>">
                                  <? } else { ?>
                                    <?=$iniciais ? $iniciais : 'P'?>
                                  <? } ?>
                                </div>
                                <div>
                                  <div class="patient-name"><?=$agenda->paciente_nome?></div>
                                  <div class="patient-subtitle">Agendamento #<?=$agenda->id?></div>
                                </div>
                              </div>
                            </td>
                            <td><?=ucfirst($agenda->tipo)?></td>
                            <td><?=$agenda->prestador_nome ? $agenda->prestador_nome : 'Nao informado'?></td>
                            <td><span class="status-pill <?=$status_class?>"><?=$status_nome?></span></td>
                            <td>
                              <? $tel = str_replace(["-"," ","+","(",")"], "", $agenda->paciente_telefone); ?>
                              <? if($agenda->paciente_telefone){ ?>
                                <a href="https://api.whatsapp.com/send?phone=55<?=$tel?>" target="_blank"><?=$agenda->paciente_telefone?></a>
                              <? } else { ?>
                                <span class="patient-subtitle">Nao informado</span>
                              <? } ?>
                            </td>
                            <td>
                              <div class="action-group">
                                <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$agenda->id_paciente?>/<?=$agenda->id?>" class="btn btn-sm btn-primary">Prontuario</a>
                                <a href="<?=base_url()?>adm/atendimento/set_status_agenda/<?=$agenda->id?>/<?=$agenda->status?>" class="btn btn-sm btn-outline-secondary">
                                  <?=$agenda->status == 0 ? 'Iniciar' : ($agenda->status == 1 ? 'Finalizar' : 'Reabrir')?>
                                </a>
                                <button
                                  type="button"
                                  class="btn btn-sm btn-outline-primary btn-remarcar"
                                  data-id="<?=$agenda->id?>"
                                  data-data="<?=$agenda->data_agenda?>"
                                  data-hora="<?=substr($agenda->hora_agenda,0,5)?>">
                                  Remarcar
                                </button>
                                <? if((int)$agenda->status !== 3){ ?>
                                  <a href="<?=base_url()?>adm/atendimento/cancelar_agenda/<?=$agenda->id?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancelar este agendamento?')">Cancelar</a>
                                <? } ?>
                              </div>
                            </td>
                          </tr>
                        <? } } else { ?>
                          <tr>
                            <td colspan="7" class="empty-state">Nenhum agendamento encontrado para os filtros selecionados.</td>
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
      <div class="display-type"></div>
    </div>
    <script src="<?=base_url()?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url()?>bower_components/popper.js/dist/umd/popper.min.js"></script>
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
    <script>
      $(document).on('click', '.btn-remarcar', function(){
        $('#remarcar-id-agenda').val($(this).data('id'));
        $('#remarcar-data').val($(this).data('data'));
        $('#remarcar-hora').val($(this).data('hora'));
        $('#remarcacao-box').show();
        $('html, body').animate({ scrollTop: $('#remarcacao-box').offset().top - 90 }, 250);
      });
      $('#cancelar-remarcacao').on('click', function(){
        $('#remarcacao-box').hide();
      });
    </script>
  </body>
</html>
