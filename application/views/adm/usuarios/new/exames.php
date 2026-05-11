<!DOCTYPE html>
<html>
  <head>
    <title>Checklist de Exames</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="exames clinicos utec saude" name="keywords">
    <meta content="Checklist operacional de exames com solicitacao e acompanhamento por atendimento." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <style>
      .exam-stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        height: 100%;
        padding: 18px 20px;
      }
      .exam-stat-label {
        color: #64748b;
        font-size: 12px;
        letter-spacing: .08em;
        margin-bottom: 8px;
        text-transform: uppercase;
      }
      .exam-stat-value {
        color: #0f172a;
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
      }
      .exam-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
      }
      .exam-panel-header {
        padding: 18px 20px 0;
      }
      .exam-panel-body {
        padding: 18px 20px 20px;
      }
      .exam-option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
      }
      .exam-option {
        align-items: flex-start;
        background: #f8fbff;
        border: 1px solid #dde7f3;
        border-radius: 14px;
        display: flex;
        gap: 10px;
        padding: 14px;
      }
      .status-pill {
        border-radius: 999px;
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 5px 10px;
        text-transform: uppercase;
      }
      .status-pendente { background: #fee2e2; color: #b91c1c; }
      .status-solicitado { background: #dbeafe; color: #1d4ed8; }
      .status-entregue { background: #dcfce7; color: #166534; }
      .exam-table td, .exam-table th {
        vertical-align: middle;
      }
      .empty-state {
        color: #64748b;
        padding: 24px 12px;
        text-align: center;
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
              <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$dd->id?>">Prontuario</a>
            </li>
            <li class="breadcrumb-item">
              <span>Checklist de exames</span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="element-wrapper">
                <div class="element-actions">
                  <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$dd->id?>" class="btn btn-outline-primary btn-sm">Voltar ao prontuario</a>
                </div>
                <h6 class="element-header">Checklist operacional de exames</h6>
                <p style="color:#64748b">Solicite exames por atendimento e acompanhe o que ainda esta pendente, solicitado ou entregue.</p>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-6">
                  <div class="exam-stat-card">
                    <div class="exam-stat-label">Total</div>
                    <div class="exam-stat-value"><?=$metricas_exames['total']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">itens no checklist</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="exam-stat-card">
                    <div class="exam-stat-label">Pendentes</div>
                    <div class="exam-stat-value"><?=$metricas_exames['pendentes']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">ainda nao solicitados</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="exam-stat-card">
                    <div class="exam-stat-label">Solicitados</div>
                    <div class="exam-stat-value"><?=$metricas_exames['solicitados']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">aguardando retorno</div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="exam-stat-card">
                    <div class="exam-stat-label">Entregues</div>
                    <div class="exam-stat-value"><?=$metricas_exames['entregues']?></div>
                    <div style="color:#475569;font-size:13px;margin-top:8px">ja anexados/recebidos</div>
                  </div>
                </div>
              </div>

              <div class="exam-panel" style="margin-top:24px">
                <div class="exam-panel-header">
                  <h6 class="element-header" style="margin-bottom:4px">Nova solicitacao</h6>
                  <p style="margin:0;color:#64748b">Associe os exames ao atendimento correto para manter o historico clinico consistente.</p>
                </div>
                <div class="exam-panel-body">
                  <form id="form" name="form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/set_exame">
                    <input type="hidden" name="id_user" value="<?=$dd->id?>">
                    <div class="row">
                      <div class="col-md-6">
                        <label>Atendimento de referencia</label>
                        <select name="id_agendamento" class="form-control" required>
                          <? if($qr_agendamentos->num_rows() > 0){ foreach ($qr_agendamentos->result() as $agenda) { ?>
                            <option value="<?=$agenda->id?>"><?=$this->padrao_model->converte_data($agenda->data_agenda)?> as <?=substr($agenda->hora_agenda,0,5)?>h · <?=ucfirst($agenda->tipo)?></option>
                          <? } } ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label>Observacoes</label>
                        <input type="text" name="obs" class="form-control" placeholder="Ex: jejum, urgencia, preparo ou retorno previsto">
                      </div>
                    </div>
                    <div style="margin-top:16px">
                      <label>Selecione os exames</label>
                      <div class="exam-option-grid">
                        <? if($exames->num_rows() > 0){ foreach ($exames->result() as $exame) { ?>
                          <label class="exam-option" for="exame<?=$exame->id?>">
                            <input type="checkbox" name="exames[]" value="<?=$exame->id?>" id="exame<?=$exame->id?>">
                            <span><?=$exame->nome?></span>
                          </label>
                        <? } } else { ?>
                          <div class="empty-state">Nenhum exame cadastrado para selecao.</div>
                        <? } ?>
                      </div>
                    </div>
                    <div style="margin-top:18px">
                      <button class="btn btn-primary" type="submit">Adicionar ao checklist</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="exam-panel">
                <div class="exam-panel-header">
                  <h6 class="element-header" style="margin-bottom:4px">Checklist por atendimento</h6>
                  <p style="margin:0;color:#64748b">Atualize o status dos exames a medida que forem solicitados ou entregues.</p>
                </div>
                <div class="exam-panel-body">
                  <div class="table-responsive">
                    <table class="table table-lightborder exam-table">
                      <thead>
                        <tr>
                          <th>Exame</th>
                          <th>Atendimento</th>
                          <th>Profissional</th>
                          <th>Status</th>
                          <th>Observacoes</th>
                          <th>Acoes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <? if($exames_user->num_rows() > 0){ foreach ($exames_user->result() as $ex) { ?>
                          <? $status_class = 'status-pendente'; $status_nome = 'Pendente'; ?>
                          <? if((string)$ex->status === '1'){ $status_class = 'status-solicitado'; $status_nome = 'Solicitado'; } ?>
                          <? if((string)$ex->status === '2'){ $status_class = 'status-entregue'; $status_nome = 'Entregue'; } ?>
                          <tr>
                            <td>
                              <strong><?=$ex->exame_nome ? $ex->exame_nome : 'Exame nao identificado'?></strong><br>
                              <small>Paciente: <?=$dd->nome?></small>
                            </td>
                            <td>
                              <?=$this->padrao_model->converte_data($ex->data_agenda)?> as <?=substr($ex->hora_agenda,0,5)?>h<br>
                              <small>Atendimento #<?=$ex->id_atendimento?></small>
                            </td>
                            <td><?=$ex->prestador_nome ? $ex->prestador_nome : 'Nao informado'?></td>
                            <td><span class="status-pill <?=$status_class?>"><?=$status_nome?></span></td>
                            <?php $observacoes_exame = isset($ex->obs) ? trim((string)$ex->obs) : ''; ?>
                            <td><?=$observacoes_exame !== '' ? nl2br(htmlspecialchars($observacoes_exame)) : '<span style="color:#94a3b8">Sem observacoes</span>'?></td>
                            <td>
                              <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <a href="<?=base_url()?>adm/atendimento/set_status_exame/<?=$ex->id?>/<?=$ex->status?>" class="btn btn-sm btn-outline-primary">
                                  <?=$ex->status == '0' ? 'Solicitar' : ($ex->status == '1' ? 'Marcar entregue' : 'Reabrir')?>
                                </a>
                                <a href="<?=base_url()?>adm/atendimento/prontuario/<?=$dd->id?>/<?=$ex->id_atendimento?>" class="btn btn-sm btn-outline-secondary">Atendimento</a>
                              </div>
                            </td>
                          </tr>
                        <? } } else { ?>
                          <tr>
                            <td colspan="6" class="empty-state">Nenhum exame foi adicionado ao checklist deste paciente.</td>
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
  </body>
</html>
