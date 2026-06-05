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
    <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
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
      .menu-w .main-menu > li.has-sub-menu {
        position: relative;
      }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu {
        display: none !important;
        position: static !important;
        transform: none !important;
        opacity: 1 !important;
        visibility: visible !important;
        width: 100%;
        margin: 8px 0 0;
        padding: 0 0 0 18px;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
      }
      .menu-w .main-menu > li.has-sub-menu.active > .sub-menu {
        display: block !important;
      }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li {
        display: block;
        margin: 0 0 6px;
      }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li a {
        display: block;
        padding: 8px 12px;
        border-radius: 12px;
        color: #526273;
        font-size: 13px;
        line-height: 1.35;
        white-space: normal;
        background: rgba(255,255,255,.7);
      }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li a:hover {
        background: #eef4ff;
        color: #2563eb;
        text-decoration: none;
      }
      /* Onboarding checklist */
      .ob-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:22px 24px; box-shadow:0 4px 16px rgba(15,23,42,.05); margin-bottom:24px; }
      .ob-title { font-size:16px; font-weight:700; color:#0f172a; margin:0 0 2px; }
      .ob-subtitle { font-size:13px; color:#64748b; margin:0 0 12px; }
      .ob-bar-track { background:#e2e8f0; border-radius:999px; height:6px; overflow:hidden; }
      .ob-bar-fill { background:linear-gradient(90deg,#0f766e,#f97316); border-radius:999px; height:6px; transition:width .4s; }
      .ob-bar-label { font-size:12px; color:#64748b; margin-top:5px; }
      .ob-steps { display:flex; flex-direction:column; gap:10px; margin-top:16px; }
      .ob-step { display:flex; align-items:flex-start; gap:14px; padding:13px 16px; border-radius:14px; border:1px solid #e2e8f0; }
      .ob-step.ob-done { background:#f0fdf4; border-color:#bbf7d0; }
      .ob-step.ob-active { background:#eff6ff; border-color:#bfdbfe; }
      .ob-step.ob-locked { background:#f8fafc; opacity:.6; }
      .ob-num { width:30px; height:30px; border-radius:999px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; margin-top:1px; }
      .ob-done .ob-num { background:#16a34a; color:#fff; }
      .ob-active .ob-num { background:#2563eb; color:#fff; }
      .ob-locked .ob-num { background:#cbd5e1; color:#fff; }
      .ob-step-title { font-size:13px; font-weight:700; color:#0f172a; margin:0 0 2px; }
      .ob-step-desc { font-size:12px; color:#64748b; margin:0; }
      .ob-cta { display:inline-block; margin-top:8px; background:linear-gradient(90deg,#0f766e,#f97316); color:#fff !important; border-radius:999px; padding:6px 15px; font-size:12px; font-weight:700; text-decoration:none !important; }
      .ob-cta:hover { opacity:.88; }
      /* Busca instantânea de pacientes */
      .pac-search-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 4px 16px rgba(15,23,42,.05); padding:18px 20px; margin-bottom:24px; }
      .pac-search-label { font-size:12px; font-weight:700; color:#64748b; letter-spacing:.07em; text-transform:uppercase; margin-bottom:8px; }
      .pac-search-wrap { position:relative; }
      .pac-search-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
      .pac-search-input { border-radius:999px; padding:9px 18px 9px 42px; border:1.5px solid #e2e8f0; font-size:14px; width:100%; outline:none; transition:border-color .18s,box-shadow .18s; }
      .pac-search-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
      .pac-search-results { position:absolute; z-index:1050; top:calc(100% + 6px); left:0; right:0; background:#fff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 8px 32px rgba(15,23,42,.12); max-height:340px; overflow-y:auto; display:none; }
      .pac-result-item { display:flex; align-items:center; gap:14px; padding:11px 16px; cursor:pointer; border-bottom:1px solid #f1f5f9; text-decoration:none !important; color:inherit; }
      .pac-result-item:last-child { border-bottom:none; }
      .pac-result-item:hover { background:#eff6ff; }
      .pac-result-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#0f766e); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:14px; flex-shrink:0; }
      .pac-result-nome { font-size:13px; font-weight:700; color:#0f172a; }
      .pac-result-meta { font-size:11px; color:#64748b; margin-top:2px; display:flex; flex-wrap:wrap; gap:4px; }
      .pac-result-tag { display:inline-block; background:#f1f5f9; border-radius:999px; padding:1px 8px; font-size:11px; color:#475569; white-space:nowrap; }
      .pac-empty,.pac-loading { padding:16px; text-align:center; color:#94a3b8; font-size:13px; }
      .agenda-table tbody tr:hover {
        background: var(--ut-green-50) !important;
        transition: background .14s;
      }
      .agenda-table .patient-name,
      .agenda-table .patient-subtitle {
        font-family: var(--ut-font);
      }
      .agenda-table .action-group .btn {
        font-family: var(--ut-font);
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

              <?php if(!empty($onboarding['show'])):
                $ob_done = (int)$onboarding['steps_done'];
                $ob_pct  = intval($ob_done / 3 * 100);
              ?>
              <div class="ob-card">
                <p class="ob-title">Primeiros passos</p>
                <p class="ob-subtitle">Configure o essencial para começar a usar o sistema completo.</p>
                <div class="ob-bar-track"><div class="ob-bar-fill" style="width:<?=$ob_pct?>%"></div></div>
                <div class="ob-bar-label"><?=$ob_done?> de 3 etapas concluídas</div>
                <div class="ob-steps">

                  <div class="ob-step <?=!empty($onboarding['has_prestador'])?'ob-done':'ob-active'?>">
                    <div class="ob-num"><?=!empty($onboarding['has_prestador'])?'✓':'1'?></div>
                    <div>
                      <p class="ob-step-title">Profissional cadastrado</p>
                      <?php if(!empty($onboarding['has_prestador'])): ?>
                      <p class="ob-step-desc">Profissional disponível — pronto para agendamentos.</p>
                      <?php else: ?>
                      <p class="ob-step-desc">Cadastre o profissional que vai atender os pacientes.</p>
                      <a href="<?=base_url()?>adm/atendimento/cadastro/3" class="ob-cta">Cadastrar profissional →</a>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="ob-step <?=!empty($onboarding['has_paciente'])?'ob-done':(!empty($onboarding['has_prestador'])?'ob-active':'ob-locked')?>">
                    <div class="ob-num"><?=!empty($onboarding['has_paciente'])?'✓':'2'?></div>
                    <div>
                      <p class="ob-step-title">Primeiro paciente cadastrado</p>
                      <?php if(!empty($onboarding['has_paciente'])): ?>
                      <p class="ob-step-desc">Paciente cadastrado — pronto para agendar.</p>
                      <?php elseif(!empty($onboarding['has_prestador'])): ?>
                      <p class="ob-step-desc">Cadastre o primeiro paciente para criar agendamentos.</p>
                      <a href="<?=base_url()?>adm/atendimento/cadastro/5" class="ob-cta">Cadastrar paciente →</a>
                      <?php else: ?>
                      <p class="ob-step-desc">Disponível após cadastrar um profissional.</p>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="ob-step <?=!empty($onboarding['has_agendamento'])?'ob-done':(!empty($onboarding['has_paciente'])?'ob-active':'ob-locked')?>">
                    <div class="ob-num"><?=!empty($onboarding['has_agendamento'])?'✓':'3'?></div>
                    <div>
                      <p class="ob-step-title">Criar o primeiro agendamento</p>
                      <?php if(!empty($onboarding['has_agendamento'])): ?>
                      <p class="ob-step-desc">Tudo pronto! Você já tem agendamentos registrados no sistema.</p>
                      <?php elseif(!empty($onboarding['has_paciente'])): ?>
                      <p class="ob-step-desc">Abra o prontuário de um paciente e crie o primeiro agendamento.</p>
                      <a href="<?=base_url()?>adm/usuarios/rel/5" class="ob-cta">Ver pacientes →</a>
                      <?php else: ?>
                      <p class="ob-step-desc">Disponível após cadastrar o primeiro paciente.</p>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>
              </div>
              <?php endif; ?>

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

              <div class="ut-stats-desktop" style="display:flex;gap:10px;margin-bottom:24px;padding:14px 20px;background:linear-gradient(135deg,var(--ut-green-900) 0%,var(--ut-green-800) 100%);border-radius:18px;">
                <div class="ut-stat-chip total" style="flex:1;">
                  <span class="stat-value"><?=$metricas_agenda['total']?></span>
                  <span class="stat-label">Total</span>
                </div>
                <div class="ut-stat-chip pendentes" style="flex:1;">
                  <span class="stat-value"><?=$metricas_agenda['pendentes']?></span>
                  <span class="stat-label">Pendentes</span>
                </div>
                <div class="ut-stat-chip em-curso" style="flex:1;">
                  <span class="stat-value"><?=$metricas_agenda['em_atendimento']?></span>
                  <span class="stat-label">Em curso</span>
                </div>
                <div class="ut-stat-chip feitos" style="flex:1;">
                  <span class="stat-value"><?=$metricas_agenda['finalizados']?></span>
                  <span class="stat-label">Feitos</span>
                </div>
              </div>

              <div class="pac-search-card">
                <p class="pac-search-label">Buscar paciente</p>
                <div class="pac-search-wrap">
                  <svg class="pac-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  <input type="text" id="pac-search" class="pac-search-input" placeholder="Digite o nome do paciente para ver prontuário ou marcar consulta..." autocomplete="off">
                  <div id="pac-search-results" class="pac-search-results"></div>
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
                            <td><?php
                              $ut_pill_class = 'pendente';
                              if((int)$agenda->status === 1) $ut_pill_class = 'atendimento';
                              if((int)$agenda->status === 2) $ut_pill_class = 'finalizado';
                              if((int)$agenda->status === 3) $ut_pill_class = 'cancelado';
                            ?>
                            <span class="ut-status-pill <?=$ut_pill_class?>"><?=$status_nome?></span></td>
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
    <script src="<?=base_url()?>bower_components/tether/dist/js/tether.min.js"></script>
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
      $(document).on('click', '.menu-w li.has-sub-menu > a', function(e){
        var $item = $(this).closest('li');
        if($(window).width() > 991){
          e.preventDefault();
          $item.closest('ul').find('> li.active').not($item).removeClass('active');
          $item.toggleClass('active');
        }
      });
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

      /* Busca instantânea de pacientes */
      (function(){
        var $input   = $('#pac-search');
        var $results = $('#pac-search-results');
        var timer    = null;
        var BASE     = '<?=base_url()?>';

        function fmtDate(ymd) {
          if (!ymd) return '';
          var p = ymd.split('-');
          return p[2] + '/' + p[1] + '/' + p[0];
        }

        var TODAY = new Date().toISOString().split('T')[0];

        function render(items) {
          if (!items.length) {
            $results.html('<div class="pac-empty">Nenhum paciente encontrado.</div>').show();
            return;
          }
          var html = '';
          $.each(items, function(_, p) {
            var ini  = p.nome ? p.nome.trim()[0].toUpperCase() : '?';
            var tags = '';
            if (p.telefone) tags += '<span class="pac-result-tag">' + p.telefone + '</span>';
            tags += '<span class="pac-result-tag">' + p.total_ag + ' consulta' + (p.total_ag !== 1 ? 's' : '') + '</span>';
            if (p.proximo_data) {
              var prox = fmtDate(p.proximo_data);
              if (p.proximo_tipo) prox += ' · ' + p.proximo_tipo;
              tags += '<span class="pac-result-tag" style="background:#dbeafe;color:#1d4ed8">📅 próx: ' + prox + '</span>';
            }
            if (p.ultima_data && p.ultima_data < TODAY) {
              var past = fmtDate(p.ultima_data);
              if (p.ultimo_tipo) past += ' · ' + p.ultimo_tipo;
              tags += '<span class="pac-result-tag" style="background:#f1f5f9;color:#475569">🕐 últ: ' + past + '</span>';
            }
            html += '<a href="' + BASE + 'adm/usuarios/prontuario/' + p.id + '" class="pac-result-item">'
                  + '<div class="pac-result-avatar">' + ini + '</div>'
                  + '<div><div class="pac-result-nome">' + p.nome + '</div>'
                  + '<div class="pac-result-meta">' + tags + '</div></div>'
                  + '</a>';
          });
          $results.html(html).show();
        }

        $input.on('input', function(){
          clearTimeout(timer);
          var q = $.trim($(this).val());
          if (q.length < 2) { $results.hide(); return; }
          $results.html('<div class="pac-loading">Buscando...</div>').show();
          timer = setTimeout(function(){
            $.get(BASE + 'adm/atendimento/buscar_paciente', { q: q })
              .done(function(data){
                var items = typeof data === 'string' ? JSON.parse(data) : data;
                render(items);
              })
              .fail(function(){ $results.hide(); });
          }, 280);
        });

        $(document).on('click', function(e){
          if (!$(e.target).closest('.pac-search-card').length) $results.hide();
        });
      })();
    </script>
  </body>
</html>
