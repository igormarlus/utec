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
      .ut-rotulo { display:inline-block; border:1px solid; border-radius:999px; padding:1px 10px; font-size:12px; font-weight:700; background:#fff; margin:2px 4px 2px 0; }
      .ut-rotulo-alerta { background:#fef2f2; }
      .ut-rotulos-linha { margin-top:8px; }
      .ut-ficha-alergia { margin-top:8px; display:inline-block; background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:8px; padding:4px 10px; font-weight:700; font-size:13px; }
      .ut-ficha-card { background:#fff; border:1px solid #dbe3ef; border-radius:16px; padding:18px 20px; margin:0 0 20px; }
      .ut-ficha-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:18px; }
      .ut-ficha-grid h6 { font-weight:800; color:#0f172a; margin-bottom:8px; }
      .ut-ficha-grid dt { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; margin-top:6px; }
      .ut-ficha-grid dd { margin:0; color:#0f172a; }
      .ut-ficha-vazio { color:#94a3b8; }
      @media (max-width: 767.98px){ .ut-ficha-grid { grid-template-columns:1fr; } }
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
              <?php $whatsapp_status = $this->session->flashdata('whatsapp_status'); ?>
              <?php if($whatsapp_status && isset($whatsapp_status['message'])){ ?>
                <div class="alert alert-<?=htmlspecialchars($whatsapp_status['type'])?>" style="margin-bottom:20px;"><?=htmlspecialchars($whatsapp_status['message'])?></div>
              <?php } ?>
              <?php
              $ut_rot_ok = false; $ut_rot_paciente = array(); $ut_rot_catalogo = array(); $ut_rot_marcados = array(); $ut_rot_gerencia = false;
              $ut_ci =& get_instance();
              $ut_ci->load->model('Rotulos_model', 'rotulos_model');
              if($ut_ci->rotulos_model->disponivel()){
                $ut_rot_ok = true;
                $ut_rot_conta = $ut_ci->rotulos_model->conta_raiz((int)$paciente->id);
                $ut_rot_catalogo = $ut_ci->rotulos_model->catalogo($ut_rot_conta, true);
                if(empty($ut_rot_catalogo) && $ut_rot_conta > 0){
                  $ut_ci->rotulos_model->garantir_sugestoes($ut_rot_conta, (int)$this->session->userdata('id'));
                  $ut_rot_catalogo = $ut_ci->rotulos_model->catalogo($ut_rot_conta, true);
                }
                $ut_rot_paciente = $ut_ci->rotulos_model->rotulos_do_paciente((int)$paciente->id, true);
                foreach($ut_rot_paciente as $ut_r){ $ut_rot_marcados[(int)$ut_r->id] = true; }
                $ut_nivel_logado = (int)$this->session->userdata('nivel');
                $ut_id_logado = (int)$this->session->userdata('id');
                $ut_rot_gerencia = $ut_nivel_logado === 1 || $ut_nivel_logado === 2
                  || ($ut_nivel_logado === 3 && $ut_ci->rotulos_model->conta_raiz($ut_id_logado) === $ut_id_logado);
              }
              $ut_rot_flash_ok = $this->session->flashdata('rotulos_ok');
              $ut_rot_flash_erro = $this->session->flashdata('rotulos_erro');
              ?>
              <?php if($ut_rot_flash_ok){ ?><div class="alert alert-success" style="margin-bottom:20px;"><?=htmlspecialchars($ut_rot_flash_ok)?></div><?php } ?>
              <?php if($ut_rot_flash_erro){ ?><div class="alert alert-danger" style="margin-bottom:20px;"><?=htmlspecialchars($ut_rot_flash_erro)?></div><?php } ?>
              <?php
              $ut_ficha_ok = false; $ut_ficha = array();
              $ut_ci_ficha =& get_instance();
              $ut_ci_ficha->load->model('Ficha_paciente_model', 'ficha_model');
              if($ut_ci_ficha->ficha_model->disponivel() && (int)$paciente->nivel === 5 && in_array((int)$this->session->userdata('nivel'), array(1, 2, 3, 4), true)){
                $ut_ficha_ok = true;
                $ut_ficha = $ut_ci_ficha->ficha_model->obter((int)$paciente->id);
              }
              $ut_ficha_flash_ok = $this->session->flashdata('ficha_ok');
              ?>
              <?php if($ut_ficha_flash_ok){ ?><div class="alert alert-success" style="margin-bottom:20px;"><?=htmlspecialchars($ut_ficha_flash_ok)?></div><?php } ?>
              <?php
              $ut_le_lista = array();
              $ut_le_ok = false;
              if((int)$paciente->nivel === 5 && in_array((int)$this->session->userdata('nivel'), array(1, 2, 3, 4), true)){
                $ut_ci_le =& get_instance();
                $ut_ci_le->load->model('Lista_espera_model', 'lista_espera_model');
                if($ut_ci_le->lista_espera_model->disponivel()){
                  $ut_le_lista = $ut_ci_le->lista_espera_model->aguardando_do_paciente((int)$paciente->id);
                }
                $ut_le_ok = $ut_ci_le->lista_espera_model->disponivel();
              }
              ?>
              <div class="row">
                <div class="col-sm-12">
                  <div class="patient-summary-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:16px">
                      <div>
                        <div class="section-heading" style="margin-bottom:6px"><?=$paciente->nome?></div>
                        <?php if($ut_ficha_ok && $ut_ficha['nome_social'] !== ''){ ?><div style="margin:-4px 0 6px;color:#5f708c;font-size:13px;">Nome social: <strong><?=htmlspecialchars($ut_ficha['nome_social'], ENT_QUOTES, 'UTF-8')?></strong></div><?php } ?>
                        <p style="margin:0;color:#5f708c">Prontuário com histórico de atendimentos, evolução clínica e arquivos do paciente.</p>
                        <?php if($ut_rot_ok && !empty($ut_rot_paciente)){ ?>
                          <div class="ut-rotulos-linha" aria-label="Rótulos do paciente"><?=utec_rotulos_chips_html($ut_rot_paciente)?></div>
                        <?php } ?>
                        <?php if($ut_ficha_ok && trim($ut_ficha['alergias']) !== ''){
                          $ut_alg = str_replace("\n", ' · ', $ut_ficha['alergias']);
                          if(function_exists('mb_strlen') && mb_strlen($ut_alg, 'UTF-8') > 160){ $ut_alg = mb_substr($ut_alg, 0, 160, 'UTF-8').'…'; }
                        ?>
                          <div class="ut-ficha-alergia" role="note">&#9888; Alergias: <?=htmlspecialchars($ut_alg, ENT_QUOTES, 'UTF-8')?></div>
                        <?php } ?>
                        <?php foreach($ut_le_lista as $ut_le){ ?>
                          <div style="margin-top:6px;font-size:13px;color:#0f766e;font-weight:700;">&#9203; Na lista de espera desde <?=htmlspecialchars(date('d/m', strtotime($ut_le->criado_em)), ENT_QUOTES, 'UTF-8')?> · <?=htmlspecialchars($ut_le->prestador_nome ? $ut_le->prestador_nome : 'Qualquer profissional', ENT_QUOTES, 'UTF-8')?> · <a href="<?=base_url('adm/lista_espera')?>">ver lista</a></div>
                        <?php } ?>
                      </div>
                      <div class="timeline-actions" style="margin-top:0">
                        <a href="<?=base_url()?>adm/atendimento" class="btn btn-secondary">Voltar</a>
                        <a href="<?=base_url()?>adm/atendimento/novo/<?=$paciente->id?>" class="btn btn-success">Novo agendamento</a>
                        <?php if(!empty($ut_le_ok)){ ?><a href="<?=base_url('adm/lista_espera?paciente='.(int)$paciente->id)?>" class="btn btn-outline-secondary">Lista de espera</a><?php } ?>
                        <?php if(in_array((int)$this->session->userdata('nivel'), array(1, 2, 3), true)){
                          $url_exp = base_url('adm/atendimento/exportar_prontuario/'.(int)$paciente->id.'/');
                          $formatos_exp = array('pdf' => 'PDF', 'csv' => 'CSV');
                          if(class_exists('ZipArchive')){ $formatos_exp['xlsx'] = 'Excel (XLSX)'; }
                        ?>
                        <details class="pront-export" style="display:inline-block;position:relative;">
                          <summary class="btn btn-outline-primary" style="list-style:none;cursor:pointer;">Exportar</summary>
                          <div style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:250px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
                            <div style="font-size:12px;color:#5f708c;margin-bottom:6px;">Período (opcional)</div>
                            <div class="d-flex" style="gap:6px;margin-bottom:10px;">
                              <input type="date" class="form-control form-control-sm pront-exp-de" aria-label="Data inicial">
                              <input type="date" class="form-control form-control-sm pront-exp-ate" aria-label="Data final">
                            </div>
                            <?php foreach($formatos_exp as $fmt => $rotulo_fmt){ ?>
                              <a class="btn btn-sm btn-light btn-block text-left pront-exp-link" data-base="<?=htmlspecialchars($url_exp.$fmt)?>" href="<?=htmlspecialchars($url_exp.$fmt)?>"><?=$rotulo_fmt?></a>
                            <?php } ?>
                            <div style="font-size:11px;color:#8a99b3;margin-top:8px;">Documento confidencial. A exportação fica registrada.</div>
                          </div>
                        </details>
                        <script>
                          (function(){
                            var box = document.currentScript.previousElementSibling;
                            function atualizar(){
                              var de = box.querySelector('.pront-exp-de').value;
                              var ate = box.querySelector('.pront-exp-ate').value;
                              var qs = [];
                              if(de){ qs.push('de=' + encodeURIComponent(de)); }
                              if(ate){ qs.push('ate=' + encodeURIComponent(ate)); }
                              box.querySelectorAll('.pront-exp-link').forEach(function(a){
                                a.href = a.getAttribute('data-base') + (qs.length ? '?' + qs.join('&') : '');
                              });
                            }
                            box.querySelectorAll('input[type=date]').forEach(function(i){ i.addEventListener('change', atualizar); });
                          })();
                        </script>
                        <?php } ?>
                        <?php if($ut_rot_ok && in_array((int)$this->session->userdata('nivel'), array(1, 2, 3, 4), true) && (!empty($ut_rot_catalogo) || $ut_rot_gerencia)){ ?>
                        <details class="pront-rotulos" style="display:inline-block;position:relative;">
                          <summary class="btn btn-outline-secondary" style="list-style:none;cursor:pointer;">Rótulos</summary>
                          <div style="position:absolute;right:0;z-index:20;background:#fff;border:1px solid #dbe3ef;border-radius:8px;padding:12px;min-width:250px;box-shadow:0 8px 24px rgba(15,76,129,.12);">
                            <form method="post" action="<?=base_url('adm/rotulos/paciente/'.(int)$paciente->id)?>">
                              <input type="hidden" name="voltar" value="<?=htmlspecialchars($this->uri->uri_string(), ENT_QUOTES, 'UTF-8')?>">
                              <?php foreach($ut_rot_catalogo as $ut_r){ ?>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="rotulos[]" value="<?=(int)$ut_r->id?>" id="ut-rot-<?=(int)$ut_r->id?>" <?=isset($ut_rot_marcados[(int)$ut_r->id]) ? 'checked' : ''?>>
                                  <label class="form-check-label" for="ut-rot-<?=(int)$ut_r->id?>"><?=utec_rotulos_chips_html(array($ut_r))?></label>
                                </div>
                              <?php } ?>
                              <button type="submit" class="btn btn-sm btn-primary btn-block" style="margin-top:8px;">Salvar rótulos</button>
                            </form>
                            <?php if($ut_rot_gerencia){ ?>
                              <a href="<?=base_url('adm/rotulos'.((int)$this->session->userdata('nivel') === 1 ? '?conta='.(int)$ut_rot_conta : ''))?>" style="display:block;font-size:12px;margin-top:8px;">Gerenciar rótulos</a>
                            <?php } ?>
                          </div>
                        </details>
                        <?php } ?>
                        <?php if($ut_ficha_ok){ ?><a href="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>" class="btn btn-outline-secondary">Editar ficha</a><?php } ?>
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

              <?php if($ut_ficha_ok){
                $ut_fv = function($valor){ return $valor !== '' ? nl2br(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')) : '<span class="ut-ficha-vazio">Não informado</span>'; };
                $ut_fj = function(array $partes){ $partes = array_values(array_filter($partes, function($p){ return $p !== ''; })); return implode(' · ', $partes); };
              ?>
              <div class="ut-ficha-card">
                <div class="d-flex justify-content-between align-items-center" style="margin-bottom:12px;gap:12px;">
                  <div class="section-heading" style="font-size:18px;margin:0;">Ficha do paciente</div>
                  <a href="<?=base_url('adm/ficha/paciente/'.(int)$paciente->id)?>" class="btn btn-sm btn-outline-secondary">Editar ficha</a>
                </div>
                <div class="ut-ficha-grid">
                  <div>
                    <h6>Pessoal / responsável</h6>
                    <dl>
                      <dt>Sexo</dt><dd><?=htmlspecialchars(utec_ficha_rotulo_opcao(utec_ficha_opcoes_sexo(), $ut_ficha['sexo']), ENT_QUOTES, 'UTF-8')?></dd>
                      <dt>Estado civil</dt><dd><?=htmlspecialchars(utec_ficha_rotulo_opcao(utec_ficha_opcoes_estado_civil(), $ut_ficha['estado_civil']), ENT_QUOTES, 'UTF-8')?></dd>
                      <dt>Responsável</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['responsavel_nome'], $ut_ficha['responsavel_parentesco'], utec_ficha_telefone_fmt($ut_ficha['responsavel_telefone']))))?></dd>
                      <dt>Emergência</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['emergencia_nome'], $ut_ficha['emergencia_parentesco'], utec_ficha_telefone_fmt($ut_ficha['emergencia_telefone']))))?></dd>
                    </dl>
                  </div>
                  <div>
                    <h6>Saúde</h6>
                    <dl>
                      <dt>Tipo sanguíneo</dt><dd><?=$ut_fv($ut_ficha['tipo_sanguineo'])?></dd>
                      <dt>Alergias</dt><dd><?=$ut_fv($ut_ficha['alergias'])?></dd>
                      <dt>Medicamentos</dt><dd><?=$ut_fv($ut_ficha['medicamentos'])?></dd>
                      <dt>Comorbidades</dt><dd><?=$ut_fv($ut_ficha['comorbidades'])?></dd>
                      <?php if($ut_ficha['obs_saude'] !== ''){ ?><dt>Observações</dt><dd><?=$ut_fv($ut_ficha['obs_saude'])?></dd><?php } ?>
                    </dl>
                  </div>
                  <div>
                    <h6>Convênio</h6>
                    <dl>
                      <dt>Convênio</dt><dd><?=$ut_fv($ut_fj(array($ut_ficha['convenio_nome'], $ut_ficha['convenio_plano'])))?></dd>
                      <dt>Carteirinha</dt><dd><?=$ut_fv($ut_ficha['convenio_carteirinha'])?></dd>
                      <dt>Validade</dt><dd><?=$ut_fv(utec_ficha_data_br($ut_ficha['convenio_validade']))?></dd>
                    </dl>
                  </div>
                </div>
              </div>
              <?php } ?>
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
              // ── Labels dinâmicos por especialidade (Fase 1) — fonte única em prontuario_export_helper ──
              if(!function_exists('utec_pront_rotulos')){ $this->load->helper('prontuario_export'); }
              $lbl = utec_pront_rotulos(isset($prestador_esp_id) ? (int)$prestador_esp_id : 0);
              // ── fim labels ───────────────────────────────────────────────────
              ?>

              <? if($id_agenda > 0){ ?>
              <div class="current-appointment-card">
                <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:12px;margin-bottom:18px">
                  <div>
                    <div class="section-heading" style="font-size:22px;margin-bottom:4px">Registro do atendimento em andamento</div>
                    <p style="margin:0;color:#5f708c"><?=$this->padrao_model->converte_data($dd_agenda->data_agenda)?> as <?=substr($dd_agenda->hora_agenda,0,5)?>h</p>
                    <?php $wa_retorno = isset($whatsapp_retorno) ? $whatsapp_retorno : null; ?>
                    <?php if($wa_retorno && isset($wa_retorno->status_confirmacao) && function_exists('utec_whatsapp_rotulo_confirmacao')){
                      $wa_st = (string)$wa_retorno->status_confirmacao;
                      if($wa_st === 'confirmado'){ $wa_line_css = 'color:#16874b;'; }
                      elseif($wa_st === 'cancelado'){ $wa_line_css = 'color:#b91c1c;'; }
                      else { $wa_st = ''; $wa_line_css = 'color:#94a3b8;'; }
                    ?>
                      <p style="margin:4px 0 0;font-size:13px;font-weight:700;<?=$wa_line_css?>">
                        <?=htmlspecialchars(utec_whatsapp_rotulo_confirmacao($wa_st))?><?php
                          if($wa_st !== '' && !empty($wa_retorno->respondido_em)){
                            echo ' &middot; '.date('d/m/Y H:i', strtotime($wa_retorno->respondido_em));
                          }
                        ?>
                      </p>
                    <?php } ?>
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
                  <?php if(!empty($campos_config)){ ?>
                  <hr style="margin:20px 0;border-color:#e6edf7;">
                  <div class="row">
                    <?php foreach($campos_config as $campo){
                      $ce_val  = isset($campos_extras_vals[$campo->campo_chave]) ? $campos_extras_vals[$campo->campo_chave] : '';
                      $ce_opts = $campo->campo_opcoes ? json_decode($campo->campo_opcoes, true) : [];
                      $ce_col  = ($campo->campo_tipo === 'textarea') ? 'col-sm-12' : 'col-sm-6';
                    ?>
                    <div class="<?=$ce_col?>">
                      <div class="form-group">
                        <label class="mws-form-label"><?=htmlspecialchars($campo->campo_label)?></label>
                        <?php if($campo->campo_tipo === 'select' && !empty($ce_opts)){ ?>
                          <select name="campos_extras[<?=htmlspecialchars($campo->campo_chave)?>]" class="form-control form-control-sm">
                            <option value="">— selecione —</option>
                            <?php foreach($ce_opts as $ce_op){ ?>
                              <option value="<?=htmlspecialchars($ce_op)?>" <?=($ce_val === $ce_op ? 'selected' : '')?>><?=htmlspecialchars($ce_op)?></option>
                            <?php } ?>
                          </select>
                        <?php } elseif($campo->campo_tipo === 'textarea'){ ?>
                          <textarea name="campos_extras[<?=htmlspecialchars($campo->campo_chave)?>]" class="form-control form-control-sm" rows="3" placeholder="<?=htmlspecialchars((string)$campo->campo_placeholder)?>"><?=htmlspecialchars($ce_val)?></textarea>
                        <?php } else { ?>
                          <input type="<?=htmlspecialchars($campo->campo_tipo)?>" name="campos_extras[<?=htmlspecialchars($campo->campo_chave)?>]" class="form-control form-control-sm" placeholder="<?=htmlspecialchars((string)$campo->campo_placeholder)?>" value="<?=htmlspecialchars($ce_val)?>">
                        <?php } ?>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                  <?php } ?>
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

                              <?php
                              $tl_extras = [];
                              if(!empty($agenda->campos_extras)){
                                $tl_dec = json_decode($agenda->campos_extras, true);
                                if(is_array($tl_dec)) $tl_extras = $tl_dec;
                              }
                              if(!empty($tl_extras) && !empty($campos_config)){
                                foreach($campos_config as $tl_cfg){
                                  $tl_v = isset($tl_extras[$tl_cfg->campo_chave]) ? $tl_extras[$tl_cfg->campo_chave] : '';
                                  if($tl_v === '') continue;
                                ?>
                                <div class="timeline-section">
                                  <h6><?=htmlspecialchars($tl_cfg->campo_label)?></h6>
                                  <p><?=nl2br(htmlspecialchars($tl_v))?></p>
                                </div>
                                <?php } } ?>

                              <?php if(trim((string)$agenda->atendimento_inicial) === '' && trim((string)$agenda->avaliacao) === '' && trim((string)$agenda->reavaliacao) === '' && empty($tl_extras)){ ?>
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
