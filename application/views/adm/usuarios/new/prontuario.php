<!DOCTYPE html>
<html>
  <head>
    <title>Prontuário</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="prontuario clinico utec saude" name="keywords">
    <meta content="Tamerlan Soziev" name="author">
    <meta content="Prontuario do paciente com historico, agenda e arquivos." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="favicon.png" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/dropzone/dist/dropzone.css" rel="stylesheet">

    <!-- <link href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet"> -->
    

    <link href="<?=base_url()?>bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <!--<link href="<?=base_url()?>css/main.css?version=4.5.0" rel="stylesheet">-->
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">

    <link rel="stylesheet" href="<?=base_url()?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <style>
      .patient-summary-card {
        background: linear-gradient(135deg, #f4f8ff 0%, #ffffff 100%);
        border: 1px solid #d7e3f7;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 14px 30px rgba(40, 72, 120, 0.08);
      }
      .patient-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-top: 18px;
      }
      .patient-summary-item {
        background: #fff;
        border: 1px solid #e6edf7;
        border-radius: 14px;
        padding: 14px 16px;
      }
      .patient-summary-label {
        color: #7d8aa5;
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        margin-bottom: 4px;
        text-transform: uppercase;
      }
      .quick-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
      }
      .quick-metric-card {
        background: #fff;
        border: 1px solid #e9eef6;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 10px 22px rgba(40, 72, 120, 0.06);
      }
      .quick-metric-card strong {
        color: #183153;
        display: block;
        font-size: 28px;
        line-height: 1.1;
      }
      .timeline-list {
        position: relative;
        margin-top: 10px;
      }
      .timeline-list:before {
        background: linear-gradient(180deg, #d8e3f2 0%, #eef4fb 100%);
        border-radius: 999px;
        bottom: 0;
        content: "";
        left: 19px;
        position: absolute;
        top: 0;
        width: 3px;
      }
      .timeline-item {
        padding-left: 56px;
        position: relative;
      }
      .timeline-item + .timeline-item {
        margin-top: 18px;
      }
      .timeline-dot {
        align-items: center;
        background: #fff;
        border: 3px solid #047bf8;
        border-radius: 999px;
        color: #047bf8;
        display: inline-flex;
        height: 22px;
        justify-content: center;
        left: 10px;
        position: absolute;
        top: 24px;
        width: 22px;
        z-index: 2;
      }
      .timeline-card {
        background: #fff;
        border: 1px solid #e6edf7;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(40, 72, 120, 0.08);
        padding: 22px;
      }
      .timeline-topbar {
        align-items: flex-start;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 16px;
      }
      .timeline-date {
        color: #183153;
        font-size: 18px;
        font-weight: 700;
      }
      .timeline-meta {
        color: #7d8aa5;
        font-size: 12px;
        margin-top: 4px;
      }
      .timeline-status {
        border-radius: 999px;
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 6px 10px;
        text-transform: uppercase;
      }
      .timeline-status.status-pendente { background: #fff1f0; color: #d64545; }
      .timeline-status.status-atendimento { background: #ebfff1; color: #16874b; }
      .timeline-status.status-finalizado { background: #fff6e5; color: #b97700; }
      .timeline-sections {
        display: grid;
        gap: 12px;
      }
      .timeline-section {
        background: #f8fbff;
        border: 1px solid #e4edf8;
        border-radius: 14px;
        padding: 14px 16px;
      }
      .timeline-section h6 {
        color: #183153;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        margin-bottom: 8px;
        text-transform: uppercase;
      }
      .timeline-section p {
        color: #50627c;
        margin: 0;
        white-space: pre-line;
      }
      .timeline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
      }
      .timeline-empty {
        background: #fff;
        border: 1px dashed #cdd9eb;
        border-radius: 16px;
        color: #6e7f99;
        padding: 28px;
        text-align: center;
      }
      .current-appointment-card {
        background: #ffffff;
        border: 1px solid #d8e4f4;
        border-radius: 18px;
        box-shadow: 0 14px 30px rgba(40, 72, 120, 0.08);
        margin-bottom: 24px;
        padding: 24px;
      }
      .section-heading {
        color: #183153;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 14px;
      }
    </style>
    
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper with-side-panel solid-bg-all">
      
      <? include("includes/adm/search.php"); ?>
      <div class="layout-w">
        
        
        <? #include("includes/adm/menu.php"); ?>
        <!--------------------
        END - Mobile Menu
        --------------------><!--------------------
        START - Main Menu
        -------------------->
        <? include("includes/adm/paciente/menu.php"); ?>
        
        <!--------------------
        END - Main Menu
        -------------------->
        <div class="content-w">
          <!--------------------
          START - Top Bar
          -------------------->
          
          <? include("includes/adm/top.php"); ?>
          <!--------------------
          END - Top Bar
          --------------------><!--------------------
          START - Breadcrumbs
          -------------------->
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/dash">Painel</a>
            </li>
            <li class="breadcrumb-item">
              <a href="<?=base_url()?>adm/usuarios/rel/5">Pacientes</a>
            </li>
            <li class="breadcrumb-item">
              <span>Prontuario</span>
            </li>
          </ul>
          <!--------------------
          END - Breadcrumbs
          -------------------->
          <?php
            $paciente = $dd;
            $agendamentos = $qr_agendamentos->result();
            $total_agendamentos = count($agendamentos);
            $total_finalizados = 0;
            $total_pendentes = 0;
            foreach ($agendamentos as $item_agenda) {
              if ((int)$item_agenda->status === 2) {
                $total_finalizados++;
              }
              if ((int)$item_agenda->status === 0) {
                $total_pendentes++;
              }
            }
          ?>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <div class="row">
                <div class="col-sm-12">
                  <div class="patient-summary-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:16px">
                      <div>
                        <div class="section-heading" style="margin-bottom:6px"><?=$paciente->nome?></div>
                        <p style="margin:0;color:#5f708c">Prontuário com histórico de atendimentos, evolução clínica e arquivos do paciente.</p>
                      </div>
                      <div class="timeline-actions" style="margin-top:0">
                        <a href="<?=base_url()?>adm/atendimento" class="btn btn-secondary">Voltar</a>
                        <a href="<?=base_url()?>adm/atendimento/novo/<?=$paciente->id?>" class="btn btn-success">Novo agendamento</a>
                      </div>
                    </div>
                    <div class="patient-summary-grid">
                      <div class="patient-summary-item">
                        <span class="patient-summary-label">Telefone</span>
                        <strong><?=$paciente->telefone ? $paciente->telefone : 'Nao informado'?></strong>
                      </div>
                      <div class="patient-summary-item">
                        <span class="patient-summary-label">E-mail</span>
                        <strong><?=$paciente->email ? $paciente->email : 'Nao informado'?></strong>
                      </div>
                      <div class="patient-summary-item">
                        <span class="patient-summary-label">Cadastro</span>
                        <strong><?=$paciente->dt_cadastro ? $this->padrao_model->converte_data(substr($paciente->dt_cadastro, 0, 10)) : 'Nao informado'?></strong>
                      </div>
                      <div class="patient-summary-item">
                        <span class="patient-summary-label">Perfil</span>
                        <strong><?=$this->padrao_model->get_by_matriz('nivel',$nivel,'usuarios_niveis')->row()->nome?></strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="quick-metrics">
                <div class="quick-metric-card">
                  <span class="patient-summary-label">Atendimentos</span>
                  <strong><?=$total_agendamentos?></strong>
                  <span style="color:#6f809b">registros no histórico</span>
                </div>
                <div class="quick-metric-card">
                  <span class="patient-summary-label">Finalizados</span>
                  <strong><?=$total_finalizados?></strong>
                  <span style="color:#6f809b">consultas concluídas</span>
                </div>
                <div class="quick-metric-card">
                  <span class="patient-summary-label">Pendentes</span>
                  <strong><?=$total_pendentes?></strong>
                  <span style="color:#6f809b">itens em aberto</span>
                </div>
                <div class="quick-metric-card">
                  <span class="patient-summary-label">Arquivos</span>
                  <strong><?=isset($arquivos) ? $arquivos->num_rows() : 0?></strong>
                  <span style="color:#6f809b">documentos anexados</span>
                </div>
              </div>

              <? if($id_agenda > 0){ ?>
              <div class="current-appointment-card">
                <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;margin-bottom:18px">
                  <div>
                    <div class="section-heading" style="font-size:22px;margin-bottom:4px">Registro do atendimento em andamento</div>
                    <p style="margin:0;color:#5f708c"><?=$this->padrao_model->converte_data($dd_agenda->data_agenda)?> as <?=substr($dd_agenda->hora_agenda,0,5)?>h</p>
                  </div>
                </div>
                <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/set" enctype='multipart/form-data'>
                  <input type="hidden" name="id_agenda" value="<?=$id_agenda?>">
                  <div class="form-group">
                    <label class="mws-form-label">Atendimento inicial</label>
                    <textarea name="atendimento_inicial" class="form-control" placeholder="Descreva a queixa principal, contexto e primeiros registros."><?=$dd_agenda->atendimento_inicial?></textarea>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group bordered">
                        <label class="mws-form-label">Avaliação</label>
                        <textarea name="avaliacao" class="form-control" placeholder="Registre avaliação clínica, hipóteses e condutas adotadas."><?=$dd_agenda->avaliacao?></textarea>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group bordered">
                        <label class="mws-form-label">Reavaliação</label>
                        <textarea name="reavaliacao" class="form-control" placeholder="Registre evolução, retorno ou observações complementares."><?=$dd_agenda->reavaliacao?></textarea>
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-primary" type="submit">Salvar atendimento</button>
                </form>
              </div>
            <? } ?>

              <div class="row">
                <div class="col-sm-12 col-xxxl-12">
                  <div class="element-wrapper">
                    <div class="element-box">
                      <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;margin-bottom:18px">
                        <div>
                          <div class="section-heading" style="margin-bottom:4px">Timeline do prontuário</div>
                          <p style="margin:0;color:#5f708c">Histórico clínico em ordem cronológica, com acesso rápido para edição e acompanhamento.</p>
                        </div>
                      </div>

                      <?php if($qr_agendamentos->num_rows() > 0){ ?>
                      <div class="timeline-list">
                        <?php foreach ($agendamentos as $agenda) {
                          $status_nome = 'Pendente';
                          $status_class = 'status-pendente';
                          if ((int)$agenda->status === 1) {
                            $status_nome = 'Em atendimento';
                            $status_class = 'status-atendimento';
                          } elseif ((int)$agenda->status === 2) {
                            $status_nome = 'Finalizado';
                            $status_class = 'status-finalizado';
                          }
                          $profissional = $this->padrao_model->get_by_id($agenda->id_user,'usuarios');
                          $nome_profissional = $profissional->num_rows() ? $profissional->row()->nome : 'Nao identificado';
                        ?>
                        <div class="timeline-item">
                          <span class="timeline-dot"></span>
                          <div class="timeline-card">
                            <div class="timeline-topbar">
                              <div>
                                <div class="timeline-date"><?=$this->padrao_model->converte_data($agenda->data_agenda)?> as <?=substr($agenda->hora_agenda,0,5)?>h</div>
                                <div class="timeline-meta">Agendamento #<?=$agenda->id?> • registrado por <?=$nome_profissional?></div>
                              </div>
                              <span class="timeline-status <?=$status_class?>"><?=$status_nome?></span>
                            </div>

                            <div class="timeline-sections">
                              <?php if(trim((string)$agenda->atendimento_inicial) !== ''){ ?>
                              <div class="timeline-section">
                                <h6>Atendimento inicial</h6>
                                <p><?=nl2br(htmlspecialchars($agenda->atendimento_inicial))?></p>
                              </div>
                              <?php } ?>

                              <?php if(trim((string)$agenda->avaliacao) !== ''){ ?>
                              <div class="timeline-section">
                                <h6>Avaliação</h6>
                                <p><?=nl2br(htmlspecialchars($agenda->avaliacao))?></p>
                              </div>
                              <?php } ?>

                              <?php if(trim((string)$agenda->reavaliacao) !== ''){ ?>
                              <div class="timeline-section">
                                <h6>Reavaliação</h6>
                                <p><?=nl2br(htmlspecialchars($agenda->reavaliacao))?></p>
                              </div>
                              <?php } ?>

                              <?php if(trim((string)$agenda->atendimento_inicial) === '' && trim((string)$agenda->avaliacao) === '' && trim((string)$agenda->reavaliacao) === ''){ ?>
                              <div class="timeline-section">
                                <h6>Registro clínico</h6>
                                <p>Nenhuma evolução foi preenchida para este atendimento até o momento.</p>
                              </div>
                              <?php } ?>
                            </div>

                            <div class="timeline-actions">
                              <a href="<?=base_url('adm/atendimento/prontuario/'.$paciente->id.'/'.$agenda->id)?>" class="btn btn-primary">Abrir atendimento</a>
                              <a href="<?php echo base_url().'index.php/adm/atendimento/set_status_agenda/'.$agenda->id.'/'.$agenda->status; ?>" class="btn btn-outline-secondary">Atualizar status</a>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                      <?php } else { ?>
                      <div class="timeline-empty">
                        Nenhum atendimento foi registrado ainda para este paciente.
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            <!-- ═══════════════════════════════════════════════════════
                 SEÇÃO: ARQUIVOS DO PACIENTE
            ═══════════════════════════════════════════════════════ -->
            <div class="row">
              <div class="col-sm-12">
                <div class="element-wrapper">
                  <div class="element-box">
                    <h6 class="element-header">
                      <i class="os-icon os-icon-folder" style="margin-right:6px"></i>
                      Arquivos do Paciente
                    </h6>

                    <!-- Upload form -->
                    <form id="form-upload-arquivo" method="post"
                          action="<?=base_url()?>adm/atendimento/upload_arquivo"
                          enctype="multipart/form-data">
                      <input type="hidden" name="id_paciente" value="<?=$dd->id?>">
                      <input type="hidden" name="id_agendamento" value="<?=(isset($id_agenda) ? $id_agenda : 0)?>">

                      <div class="row align-items-end">
                        <div class="col-sm-5">
                          <div class="form-group" style="margin-bottom:0">
                            <label class="mws-form-label">Arquivo <small>(jpg, png, gif, pdf, doc, xls — máx 10MB)</small></label>
                            <input type="file" name="arquivo" id="input-arquivo"
                                   class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx" required>
                          </div>
                        </div>
                        <div class="col-sm-5">
                          <div class="form-group" style="margin-bottom:0">
                            <label class="mws-form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control"
                                   placeholder="Ex: Resultado exame sangue, Receita...">
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <button type="submit" id="btn-upload" class="btn btn-primary btn-block">
                            <span id="upload-txt">Enviar</span>
                            <span id="upload-spin" style="display:none">Enviando...</span>
                          </button>
                        </div>
                      </div>
                    </form>

                    <div id="upload-msg" style="margin-top:10px;display:none"></div>

                    <hr>

                    <!-- Lista de arquivos -->
                    <?php if(isset($arquivos) && $arquivos->num_rows() > 0): ?>
                    <div class="row" id="lista-arquivos">
                      <?php foreach($arquivos->result() as $arq):
                        $is_img = in_array($arq->tipo, ['imagem','jpg','jpeg','png','gif']);
                        $icone  = $is_img ? 'os-icon-image' : ($arq->tipo == 'pdf' ? 'os-icon-file-text' : 'os-icon-database');
                      ?>
                      <div class="col-sm-4 col-md-3" style="margin-bottom:16px" id="arq-<?=$arq->id?>">
                        <div class="element-box" style="padding:12px;text-align:center;position:relative">

                          <?php if($is_img): ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <img src="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>"
                                   style="max-width:100%;max-height:120px;object-fit:cover;border-radius:4px;margin-bottom:6px">
                            </a>
                          <?php else: ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <i class="os-icon <?=$icone?>" style="font-size:48px;color:#047bf8;display:block;margin-bottom:6px"></i>
                            </a>
                          <?php endif; ?>

                          <div style="font-size:12px;color:#555;word-break:break-all;margin-bottom:4px">
                            <?=htmlspecialchars($arq->nome_original)?>
                          </div>
                          <?php if($arq->descricao): ?>
                          <div style="font-size:11px;color:#888;margin-bottom:6px;font-style:italic">
                            <?=htmlspecialchars($arq->descricao)?>
                          </div>
                          <?php endif; ?>
                          <div style="font-size:10px;color:#aaa;margin-bottom:8px">
                            <?=date('d/m/Y H:i', strtotime($arq->dt_cadastro))?>
                          </div>

                          <div class="btn-group btn-group-sm w-100">
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>"
                               target="_blank" class="btn btn-sm btn-outline-primary" title="Visualizar">
                              <i class="os-icon os-icon-eye"></i>
                            </a>
                            <a href="<?=base_url()?>adm/atendimento/del_arquivo/<?=$arq->id?>"
                               class="btn btn-sm btn-outline-danger btn-del-arq"
                               data-id="<?=$arq->id?>" title="Excluir"
                               onclick="return confirm('Excluir este arquivo?')">
                              <i class="os-icon os-icon-x"></i>
                            </a>
                          </div>

                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted" id="sem-arquivos">Nenhum arquivo enviado ainda.</p>
                    <?php endif; ?>

                  </div>
                </div>
              </div>
            </div>
            <!-- ═══ FIM ARQUIVOS ═══ -->

            <!--------------------
            END - Sidebar
            -------------------->
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

    <script>
      $(document).ready(function() {
          $('.table__').DataTable({
              "pageLength": 10,
              "order": [], // evita ordenação automática
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
              }
          });

          $('.table').DataTable({
              "paging": true,
              "lengthChange": true,
              "searching": true,
              "ordering": true,
              "order": [[0, 'desc']],
              "info": true,
              "autoWidth": false,
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
              },
          });
      });
      </script>


    <script>
    $('#form-upload-arquivo').on('submit', function(e){
      e.preventDefault();
      var $btn = $('#btn-upload');
      $btn.prop('disabled', true);
      $('#upload-txt').hide();
      $('#upload-spin').show();
      $('#upload-msg').hide();

      var fd = new FormData(this);
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(resp){
          var r = JSON.parse(resp);
          $('#upload-msg')
            .removeClass('alert-success alert-danger')
            .addClass(r.ok ? 'alert-success alert-' : 'alert alert-danger')
            .addClass('alert ' + (r.ok ? 'alert-success' : 'alert-danger'))
            .text(r.msg)
            .show();
          if(r.ok){
            $('#form-upload-arquivo')[0].reset();
            // recarrega para mostrar novo arquivo
            setTimeout(function(){ location.reload(); }, 800);
          }
        },
        error: function(){
          $('#upload-msg').addClass('alert alert-danger').text('Erro ao enviar. Tente novamente.').show();
        },
        complete: function(){
          $btn.prop('disabled', false);
          $('#upload-txt').show();
          $('#upload-spin').hide();
        }
      });
    });
    </script>
  </body>
</html>
