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
    <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">

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
      .timeline-status.status-cancelado { background: #e2e8f0; color: #475569; }
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
      .section-jump {
        color: #64748b;
        font-size: 13px;
      }
      .file-card {
        height: 100%;
        padding: 14px;
        position: relative;
        text-align: center;
      }
      .file-card-preview {
        border-radius: 10px;
        margin-bottom: 8px;
        max-height: 120px;
        max-width: 100%;
        object-fit: cover;
      }
      .file-card-name {
        color: #555;
        font-size: 12px;
        margin-bottom: 4px;
        word-break: break-all;
      }
      .file-card-desc {
        color: #888;
        font-size: 11px;
        font-style: italic;
        margin-bottom: 6px;
      }
      .file-card-date {
        color: #aaa;
        font-size: 10px;
        margin-bottom: 8px;
      }
      @media (max-width: 991.98px) {
        .patient-summary-card,
        .current-appointment-card,
        .timeline-card,
        .element-box {
          border-radius: 16px;
        }
        .timeline-actions .btn {
          flex: 1 1 220px;
        }
      }
      @media (max-width: 767.98px) {
        .patient-summary-card,
        .current-appointment-card {
          padding: 18px;
        }
        .quick-metrics {
          grid-template-columns: repeat(2, minmax(0, 1fr));
          gap: 12px;
        }
        .quick-metric-card {
          padding: 16px;
        }
        .quick-metric-card strong {
          font-size: 24px;
        }
        .section-heading {
          font-size: 18px;
        }
        .timeline-list:before {
          left: 15px;
        }
        .timeline-item {
          padding-left: 40px;
        }
        .timeline-dot {
          height: 18px;
          left: 7px;
          top: 22px;
          width: 18px;
        }
        .timeline-card {
          padding: 16px;
        }
        .timeline-topbar {
          flex-direction: column;
        }
        .timeline-date {
          font-size: 16px;
        }
        .timeline-actions {
          gap: 8px;
        }
        .timeline-actions .btn {
          flex: 1 1 100%;
          width: 100%;
        }
        #form-upload-arquivo .row > div {
          margin-bottom: 12px;
        }
        #form-upload-arquivo .row > div:last-child {
          margin-bottom: 0;
        }
        .file-card {
          padding: 12px;
        }
      }
      @media (max-width: 575.98px) {
        .patient-summary-grid,
        .quick-metrics {
          grid-template-columns: 1fr;
        }
      }
      .section-heading,
      .timeline-date,
      .timeline-section h6,
      .element-header {
        font-family: var(--ut-font) !important;
      }
      .timeline-list:before {
        background: linear-gradient(180deg, var(--ut-green-border) 0%, #e2e8f0 100%);
      }
      .timeline-dot {
        border-color: var(--ut-green-600) !important;
        color: var(--ut-green-600) !important;
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

              <?php
              // ── Labels dinâmicos por especialidade (Fase 1) ─────────────────
              $lbl = [
                'atendimento_inicial' => 'Atendimento Inicial',
                'avaliacao'           => 'Avaliação',
                'reavaliacao'         => 'Reavaliação',
                'ph_inicial'  => 'Descreva a queixa principal, contexto e primeiros registros.',
                'ph_avaliacao'=> 'Registre avaliação clínica, hipóteses e condutas adotadas.',
                'ph_reav'     => 'Registre evolução, retorno ou observações complementares.',
              ];
              $esp = isset($prestador_esp_id) ? (int)$prestador_esp_id : 0;
              switch($esp){
                case 10: // Fisioterapia
                  $lbl['atendimento_inicial'] = 'Queixa / Avaliação Postural';
                  $lbl['avaliacao']           = 'Evolução da Sessão / Técnicas Aplicadas';
                  $lbl['reavaliacao']         = 'Resposta ao Tratamento / Próxima Sessão';
                  $lbl['ph_inicial']   = 'Queixa principal, intensidade de dor, limitações funcionais e achados posturais.';
                  $lbl['ph_avaliacao'] = 'Técnicas aplicadas (RPG, PNF, eletroterapia, hidroterapia...), exercícios realizados.';
                  $lbl['ph_reav']      = 'Resposta do paciente, evolução do quadro, plano e objetivos para a próxima sessão.';
                  break;
                case 36: // Psicologia
                  $lbl['atendimento_inicial'] = 'Demanda Apresentada';
                  $lbl['avaliacao']           = 'Evolução da Sessão';
                  $lbl['reavaliacao']         = 'Observações / Encaminhamentos';
                  $lbl['ph_inicial']   = 'Demanda e contexto trazidos pelo paciente nesta sessão.';
                  $lbl['ph_avaliacao'] = 'Registro clínico da sessão e intervenções realizadas.';
                  $lbl['ph_reav']      = 'Observações, encaminhamentos ou pontos para a próxima sessão.';
                  break;
                case 28: // Odontologia
                  $lbl['atendimento_inicial'] = 'Queixa / Motivo da Consulta';
                  $lbl['avaliacao']           = 'Procedimento(s) Realizado(s)';
                  $lbl['reavaliacao']         = 'Prescrição / Retorno';
                  $lbl['ph_inicial']   = 'Queixa principal, dente(s) envolvido(s), histórico relevante.';
                  $lbl['ph_avaliacao'] = 'Procedimento realizado, dente(s) — numeração FDI, anestesia e material utilizado.';
                  $lbl['ph_reav']      = 'Medicação prescrita, orientações pós-operatórias, data de retorno.';
                  break;
                case 37: // Psiquiatria
                  $lbl['atendimento_inicial'] = 'Queixa Principal / Estado Mental';
                  $lbl['avaliacao']           = 'Avaliação / Hipótese Diagnóstica';
                  $lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
                  $lbl['ph_inicial']   = 'Queixa principal, humor, sono, apetite, pensamento e comportamento.';
                  $lbl['ph_avaliacao'] = 'Hipótese diagnóstica (CID), exame do estado mental, raciocínio clínico.';
                  $lbl['ph_reav']      = 'Conduta adotada, ajuste de medicação, orientações, retorno.';
                  break;
                case 27: // Nutrição
                  $lbl['atendimento_inicial'] = 'Queixa / Anamnese Alimentar';
                  $lbl['avaliacao']           = 'Avaliação Nutricional / Condutas';
                  $lbl['reavaliacao']         = 'Evolução / Plano Alimentar';
                  $lbl['ph_inicial']   = 'Queixa principal, hábitos alimentares, intolerâncias, histórico de saúde.';
                  $lbl['ph_avaliacao'] = 'Avaliação antropométrica, diagnóstico nutricional, condutas adotadas.';
                  $lbl['ph_reav']      = 'Evolução do quadro, ajustes no plano alimentar, metas para o próximo retorno.';
                  break;
                case 33: // Pediatria
                  $lbl['atendimento_inicial'] = 'Queixa / Dados do Responsável';
                  $lbl['avaliacao']           = 'Exame Físico / Hipóteses';
                  $lbl['reavaliacao']         = 'Conduta / Retorno';
                  $lbl['ph_inicial']   = 'Queixa relatada pelo responsável, histórico de saúde e desenvolvimento da criança.';
                  $lbl['ph_avaliacao'] = 'Exame físico, curva de crescimento, hipóteses diagnósticas.';
                  $lbl['ph_reav']      = 'Conduta, prescrição, orientações ao responsável, data de retorno.';
                  break;
                case 14: // Ginecologia e Obstetrícia
                  $lbl['atendimento_inicial'] = 'Queixa / Anamnese Ginecológica';
                  $lbl['avaliacao']           = 'Exame Físico / Hipóteses';
                  $lbl['reavaliacao']         = 'Conduta / Retorno';
                  $lbl['ph_inicial']   = 'Queixa principal, ciclo menstrual, DUM, histórico obstétrico.';
                  $lbl['ph_avaliacao'] = 'Exame físico, hipóteses diagnósticas, exames solicitados.';
                  $lbl['ph_reav']      = 'Conduta, prescrição, orientações, retorno.';
                  break;
                case 11: // Fonoaudiologia
                  $lbl['atendimento_inicial'] = 'Queixa / Avaliação Fonoaudiológica';
                  $lbl['avaliacao']           = 'Evolução da Sessão / Técnicas';
                  $lbl['reavaliacao']         = 'Resposta / Próxima Sessão';
                  $lbl['ph_inicial']   = 'Queixa principal, histórico de linguagem, deglutição ou voz.';
                  $lbl['ph_avaliacao'] = 'Técnicas aplicadas, exercícios realizados, progresso observado.';
                  $lbl['ph_reav']      = 'Resposta do paciente, orientações, plano para próxima sessão.';
                  break;
                case 3: // Cardiologia
                  $lbl['atendimento_inicial'] = 'Queixa Cardiovascular';
                  $lbl['avaliacao']           = 'Exame Físico / Hipóteses';
                  $lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
                  $lbl['ph_inicial']   = 'Queixa principal (dor torácica, dispneia, palpitações...), PA, FC.';
                  $lbl['ph_avaliacao'] = 'Ausculta, hipóteses, ECG, exames solicitados.';
                  $lbl['ph_reav']      = 'Conduta, ajuste de medicação, exames de retorno.';
                  break;
              }
              // ── fim labels ───────────────────────────────────────────────────
              ?>

              <? if($id_agenda > 0){ ?>
              <div class="current-appointment-card">
                <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;margin-bottom:18px">
                  <div>
                    <div class="section-heading" style="font-size:22px;margin-bottom:4px">Registro do atendimento em andamento</div>
                    <p style="margin:0;color:#5f708c"><?=$this->padrao_model->converte_data($dd_agenda->data_agenda)?> as <?=substr($dd_agenda->hora_agenda,0,5)?>h</p>
                  </div>
                  <div>
                    <?php
                      $status_card_nome = 'Pendente';
                      $status_card_class = 'status-pendente';
                      if ((int)$dd_agenda->status === 1) {
                        $status_card_nome = 'Em atendimento';
                        $status_card_class = 'status-atendimento';
                      } elseif ((int)$dd_agenda->status === 2) {
                        $status_card_nome = 'Finalizado';
                        $status_card_class = 'status-finalizado';
                      } elseif ((int)$dd_agenda->status === 3) {
                        $status_card_nome = 'Cancelado';
                        $status_card_class = 'status-cancelado';
                      }
                    ?>
                    <?php
                      $ut_card_pill = 'pendente';
                      if((int)$dd_agenda->status === 1) $ut_card_pill = 'atendimento';
                      if((int)$dd_agenda->status === 2) $ut_card_pill = 'finalizado';
                      if((int)$dd_agenda->status === 3) $ut_card_pill = 'cancelado';
                    ?>
                    <span class="ut-status-pill <?=$ut_card_pill?>"><?=$status_card_nome?></span>
                  </div>
                </div>
                <form id="form" name="form" class="mws-form" method="post" action="<?php echo base_url() ?>index.php/adm/atendimento/set" enctype='multipart/form-data'>
                  <input type="hidden" name="id_agenda" value="<?=$id_agenda?>">
                  <div class="form-group">
                    <label class="mws-form-label"><?=$lbl['atendimento_inicial']?></label>
                    <textarea name="atendimento_inicial" class="form-control" placeholder="<?=$lbl['ph_inicial']?>"><?=$dd_agenda->atendimento_inicial?></textarea>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group bordered">
                        <label class="mws-form-label"><?=$lbl['avaliacao']?></label>
                        <textarea name="avaliacao" class="form-control" placeholder="<?=$lbl['ph_avaliacao']?>"><?=$dd_agenda->avaliacao?></textarea>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group bordered">
                        <label class="mws-form-label"><?=$lbl['reavaliacao']?></label>
                        <textarea name="reavaliacao" class="form-control" placeholder="<?=$lbl['ph_reav']?>"><?=$dd_agenda->reavaliacao?></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="ut-sticky-save ut-mobile-only">
                    <button class="btn btn-block" type="submit" name="acao_status" value="salvar"
                            style="background:var(--ut-green-900);color:#fff;font-family:var(--ut-font);font-weight:700;padding:13px;border-radius:var(--ut-radius-md);border:0;width:100%;">
                      Salvar prontuário
                    </button>
                  </div>
                  <div class="ut-action-grid" style="margin-top:18px;">
                    <button class="btn btn-outline-secondary" type="submit" name="acao_status" value="salvar">Salvar sem encerrar</button>
                    <? if((int)$dd_agenda->status !== 1){ ?>
                      <button class="btn btn-primary" type="submit" name="acao_status" value="iniciar">Marcar como em atendimento</button>
                    <? } ?>
                    <? if((int)$dd_agenda->status !== 2){ ?>
                      <button class="btn btn-success" type="submit" name="acao_status" value="finalizar">Finalizar atendimento</button>
                    <? } ?>
                    <? if((int)$dd_agenda->status === 2){ ?>
                      <button class="btn btn-warning" type="submit" name="acao_status" value="reabrir">Reabrir atendimento</button>
                    <? } ?>
                    <a href="<?=base_url()?>adm/atendimento/exames/<?=$paciente->id?>" class="btn btn-outline-primary">Solicitar exames</a>
                    <a href="#arquivos-paciente" class="btn btn-outline-secondary">Ver arquivos</a>
                  </div>
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
                          } elseif ((int)$agenda->status === 3) {
                            $status_nome = 'Cancelado';
                            $status_class = 'status-cancelado';
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
                              <?php
                                $ut_tl_pill = 'pendente';
                                if((int)$agenda->status === 1) $ut_tl_pill = 'atendimento';
                                if((int)$agenda->status === 2) $ut_tl_pill = 'finalizado';
                                if((int)$agenda->status === 3) $ut_tl_pill = 'cancelado';
                              ?>
                              <span class="ut-status-pill <?=$ut_tl_pill?>"><?=$status_nome?></span>
                            </div>

                            <div class="timeline-sections">
                              <?php if(trim((string)$agenda->atendimento_inicial) !== ''){ ?>
                              <div class="timeline-section">
                                <h6><?=$lbl['atendimento_inicial']?></h6>
                                <p><?=nl2br(htmlspecialchars($agenda->atendimento_inicial))?></p>
                              </div>
                              <?php } ?>

                              <?php if(trim((string)$agenda->avaliacao) !== ''){ ?>
                              <div class="timeline-section">
                                <h6><?=$lbl['avaliacao']?></h6>
                                <p><?=nl2br(htmlspecialchars($agenda->avaliacao))?></p>
                              </div>
                              <?php } ?>

                              <?php if(trim((string)$agenda->reavaliacao) !== ''){ ?>
                              <div class="timeline-section">
                                <h6><?=$lbl['reavaliacao']?></h6>
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
                              <a href="<?=base_url('adm/atendimento/prontuario/'.$paciente->id.'/'.$agenda->id)?>" class="btn btn-primary">
                                <?=$agenda->status == 2 ? 'Revisar registro' : ($agenda->status == 3 ? 'Reabrir contexto' : 'Abrir atendimento')?>
                              </a>
                              <a href="<?=base_url()?>adm/atendimento/exames/<?=$paciente->id?>" class="btn btn-outline-primary">Exames</a>
                              <a href="#arquivos-paciente" class="btn btn-outline-secondary">Arquivos</a>
                              <a href="<?php echo base_url().'index.php/adm/atendimento/set_status_agenda/'.$agenda->id.'/'.$agenda->status; ?>" class="btn btn-outline-secondary">
                                <?=$agenda->status == 0 ? 'Iniciar' : ($agenda->status == 1 ? 'Finalizar' : 'Reabrir')?>
                              </a>
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
                  <div class="element-box" id="arquivos-paciente">
                    <h6 class="element-header">
                      <i class="os-icon os-icon-folder" style="margin-right:6px"></i>
                      Arquivos do Paciente
                    </h6>
                    <p class="section-jump">Centralize exames, receitas, laudos e documentos para consulta rápida durante o atendimento.</p>

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
                      <div class="col-sm-6 col-md-4 col-xl-3" style="margin-bottom:16px" id="arq-<?=$arq->id?>">
                        <div class="element-box file-card">

                          <?php if($is_img): ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <img src="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>"
                                   class="file-card-preview">
                            </a>
                          <?php else: ?>
                            <a href="<?=base_url()?>uploads/pacientes/<?=$arq->arquivo?>" target="_blank">
                              <i class="os-icon <?=$icone?>" style="font-size:48px;color:#047bf8;display:block;margin-bottom:6px"></i>
                            </a>
                          <?php endif; ?>

                          <div class="file-card-name">
                            <?=htmlspecialchars($arq->nome_original)?>
                          </div>
                          <?php if($arq->descricao): ?>
                          <div class="file-card-desc">
                            <?=htmlspecialchars($arq->descricao)?>
                          </div>
                          <?php endif; ?>
                          <div class="file-card-date">
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
