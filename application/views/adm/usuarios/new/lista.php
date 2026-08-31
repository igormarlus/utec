<!DOCTYPE html>
<html>
  <head>
    <title>Lista de Usuarios</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="lista de usuarios utec saude" name="keywords">
    <meta content="Lista e gestao de usuarios da operacao clinica." name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=base_url()?>bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
    <link href="<?=base_url()?>css/clicklinica-main.css" rel="stylesheet">
    <link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
    <style>
      .ul-header-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(15,23,42,.05);
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
      }
      .ul-header-title,
      .ul-report-name,
      .ul-search-input,
      .ul-inline-label,
      .ul-report-head span {
        font-family: var(--ut-font) !important;
      }
      .ul-header-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
      .ul-header-sub { font-size: 13px; color: #64748b; margin: 2px 0 0; }
      .ul-search-wrap {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(15,23,42,.04);
        padding: 14px 20px;
        margin-bottom: 20px;
        position: relative;
      }
      .ul-search-icon { position: absolute; left: 34px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
      .ul-search-input {
        border-radius: 999px;
        padding: 8px 16px 8px 40px;
        border: 1.5px solid #e2e8f0;
        font-size: 14px;
        width: 100%;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        background: #f8fafc;
      }
      .ul-search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.08); background: #fff; }
      .ul-report-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }
      .ul-report-head {
        display: none;
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1.5fr) minmax(0, 1fr) minmax(0, 1.55fr) minmax(0, 1.15fr) minmax(0, .9fr) minmax(0, .75fr);
        gap: 16px;
        padding: 0 18px 6px;
      }
      .ul-report-head span {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
      }
      .ul-report-row {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(15,23,42,.05);
        padding: 16px 18px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
        align-items: start;
      }
      .ul-report-row:hover { box-shadow: 0 8px 24px rgba(15,23,42,.08); border-color: #cbd5e1; }
      .ul-col { min-width: 0; color: #0f172a; font-size: 13px; }
      .ul-col,
      .ul-report-name,
      .ul-report-sub,
      .ul-contact,
      .ul-chip {
        overflow-wrap: anywhere;
      }
      .ul-inline-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-bottom: 6px;
      }
      .ul-col-full { grid-column: 1 / -1; }
      .ul-col-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-start;
      }
      .ul-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 600;
        border: 1.5px solid transparent;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s;
        line-height: 1.3;
        white-space: nowrap;
      }
      .ul-btn svg { width: 12px; height: 12px; }
      .ul-btn-prontuario { background: var(--ut-green-50); color: var(--ut-green-900); border-color: var(--ut-green-border); }
      .ul-btn-prontuario:hover { background: #dcfce7; color: var(--ut-green-800); }
      .ul-btn-edit { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
      .ul-btn-edit:hover { background: #ffedd5; color: #9a3412; }
      .ul-btn-remove { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
      .ul-btn-remove:hover { background: #fee2e2; color: #991b1b; }
      .ul-btn-acessar { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
      .ul-btn-acessar:hover { background: #dcfce7; color: #166534; }
      .ul-btn-status { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }
      .ul-btn-status:hover { background: #f1f5f9; color: #334155; }
      .ul-btn-whats { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
      .ul-btn-whats:hover { background: #d1fae5; color: #065f46; }
      .ul-user-block { display: flex; align-items: center; gap: 14px; min-width: 0; }
      .ul-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--ut-green-900), var(--ut-green-600));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
        overflow: hidden;
      }
      .ul-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
      .ul-avatar svg { width: 24px; height: 24px; fill: currentColor; }
      .ul-report-name { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.3; color: #0f172a; }
      .ul-report-name a { color: inherit; text-decoration: none; }
      .ul-report-name a:hover { color: #0f766e; }
      .ul-report-sub { font-size: 12px; color: #64748b; margin-top: 2px; }
      .ul-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 9px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 11px;
        line-height: 1.2;
      }
      .ul-chip-plan { background: #eef6ff; border-color: #bfdbfe; color: #1d4ed8; }
      .ul-chip-muted { background: #f8fafc; border-color: #e2e8f0; color: #94a3b8; }
      .ul-status-pill {
        display: inline-block;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
      }
      .ul-status-ativo { background: #dcfce7; color: #15803d; }
      .ul-status-inativo { background: #fee2e2; color: #b91c1c; }
      .ul-empty {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 48px 24px;
        text-align: center;
        color: #94a3b8;
      }
      .ul-empty-icon { font-size: 34px; margin-bottom: 12px; }
      .ul-empty-txt { font-size: 14px; }
      .ul-link { color: #0f766e; text-decoration: none; }
      .ul-link:hover { color: #115e59; text-decoration: underline; }
      .ul-contact a { color: #0f766e; text-decoration: none; }
      .ul-contact a:hover { text-decoration: underline; }
      .ul-kpi-stack { display: flex; flex-wrap: wrap; gap: 6px; }
      .menu-w .main-menu > li.has-sub-menu { position: relative; }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu {
        display: none !important; position: static !important;
        transform: none !important; opacity: 1 !important;
        visibility: visible !important; width: 100%;
        margin: 8px 0 0; padding: 0 0 0 18px;
        background: transparent !important; border: 0 !important; box-shadow: none !important;
      }
      .menu-w .main-menu > li.has-sub-menu.active > .sub-menu { display: block !important; }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li { display: block; margin: 0 0 6px; }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li a {
        display: block; padding: 8px 12px; border-radius: 12px;
        color: #526273; font-size: 13px; line-height: 1.35;
        white-space: normal; background: rgba(255,255,255,.7);
      }
      .menu-w .main-menu > li.has-sub-menu > .sub-menu li a:hover { background: #eef4ff; color: #2563eb; text-decoration: none; }
      @media (min-width: 1200px) {
        .ul-report-head { display: grid; }
        .ul-report-row {
          grid-template-columns: minmax(0, 1.45fr) minmax(0, 1.5fr) minmax(0, 1fr) minmax(0, 1.55fr) minmax(0, 1.15fr) minmax(0, .9fr) minmax(0, .75fr);
          align-items: center;
        }
        .ul-col { align-self: center; }
        .ul-inline-label { display: none; }
        .ul-col-full { grid-column: auto; }
        .ul-col-actions {
          align-self: start;
        }
        .ul-btn {
          width: 100%;
        }
      }
      @media (max-width: 991.98px) {
        .ul-report-row { grid-template-columns: 1fr; }
      }
      @media (max-width: 767.98px) {
        .ul-header-card,
        .ul-search-wrap,
        .ul-report-row { border-radius: 14px; }
        .ul-report-row { padding: 14px; gap: 12px; }
        .ul-btn { flex: 1 1 calc(50% - 8px); min-height: 38px; }
        .ul-user-block { align-items: flex-start; }
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
              <a href="<?=base_url()?>adm/atendimento">Agenda clínica</a>
            </li>
            <li class="breadcrumb-item">
              <span><?=$this->padrao_model->get_by_matriz('nivel', $nivel, 'usuarios_niveis')->row()->nome?></span>
            </li>
          </ul>
          <div class="content-panel-toggler">
            <i class="os-icon os-icon-grid-squares-22"></i><span>Menu</span>
          </div>
          <div class="content-i">
            <div class="content-box">
              <?php
                $usuarios_lista = is_array($usuarios) ? $usuarios : $usuarios->result();
                $total = count($usuarios_lista);
                $nivel_nome = $this->padrao_model->get_by_matriz('nivel', $nivel, 'usuarios_niveis')->row()->nome;
              ?>
              <div class="ul-header-card">
                <div>
                  <p class="ul-header-title"><?=$nivel_nome?></p>
                  <p class="ul-header-sub"><?=$total?> registro<?=$total !== 1 ? 's' : ''?> encontrado<?=$total !== 1 ? 's' : ''?> no seu escopo</p>
                </div>
                <a href="<?=base_url()?>adm/usuarios/cadastro/<?=$nivel?>" class="btn btn-success btn-sm">+ Novo registro</a>
              </div>

              <? if ($total > 0): ?>
              <div class="ul-search-wrap">
                <svg class="ul-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="ul-filter" class="ul-search-input" placeholder="Filtrar pelo nome..." autocomplete="off">
              </div>
              <? endif; ?>

              <div class="ul-report-list" id="ul-grid">
                <? if ($total === 0): ?>
                <div class="ul-empty">
                  <div class="ul-empty-icon">Relatorio vazio</div>
                  <div class="ul-empty-txt">Nenhum registro encontrado. <a class="ul-link" href="<?=base_url()?>adm/usuarios/cadastro/<?=$nivel?>">Cadastrar o primeiro</a>.</div>
                </div>
                <? else: ?>
                <div class="ul-report-head">
                  <span>Acoes</span>
                  <span>Usuario</span>
                  <span>Atividade</span>
                  <span>Indicadores</span>
                  <span>Contato</span>
                  <span>Cadastro</span>
                  <span>Status</span>
                </div>
                <? foreach ($usuarios_lista as $u):
                  $ativo = (int)$u->status === 1;
                  $atividade = utec_relatorio_resolve_atividade($u);
                  $plano = utec_relatorio_resolve_plano_status($u);
                  $telefone = trim((string)$u->telefone);
                  $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                  $mostrar_plano = ($plano !== '' && utec_relatorio_mostra_plano_por_nivel((int)$u->nivel));
                  $cadastro = !empty($u->dt_cadastro) ? date('d/m/Y', strtotime($u->dt_cadastro)) : 'Nao informado';
                  $ultima_atividade = !empty($u->ultima_atividade) ? date('d/m/Y', strtotime($u->ultima_atividade)) : '';
                ?>
                <div class="ul-report-row" data-nome="<?=mb_strtolower($u->nome, 'UTF-8')?>">
                  <div class="ul-col ul-col-actions ul-col-full">
                    <? if ((int)$nivel === 5): ?>
                    <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$u->id?>" class="ul-btn ul-btn-prontuario">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                      Prontuario
                    </a>
                    <? endif; ?>
                    <a href="<?=base_url()?>adm/usuarios/edicao/<?=$u->id?>" class="ul-btn ul-btn-edit">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      Editar
                    </a>
                    <? if ($telefone_limpo): ?>
                    <a href="https://api.whatsapp.com/send?phone=55<?=$telefone_limpo?>" target="_blank" class="ul-btn ul-btn-whats">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-1.9 5.4A8.5 8.5 0 0 1 7.5 19L3 20l1.1-4.3A8.5 8.5 0 1 1 21 11.5z"/></svg>
                      WhatsApp
                    </a>
                    <? endif; ?>
                    <a href="<?=base_url()?>adm/usuarios/remover/<?=$u->id?>" class="ul-btn ul-btn-remove" onclick="return confirm('Remover <?=htmlspecialchars(addslashes($u->nome), ENT_QUOTES, 'UTF-8')?>?')">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                      Remover
                    </a>
                    <? if ($this->session->userdata('nivel') == 1): ?>
                    <a href="<?=base_url()?>admin/logar_como/<?=$u->id?>" class="ul-btn ul-btn-acessar" target="_blank">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                      Acessar
                    </a>
                    <? endif; ?>
                    <a href="<?=base_url()?>adm/usuarios/set_status/<?=$u->id?>/<?=$u->status?>" class="ul-btn ul-btn-status" title="<?=$ativo ? 'Desativar' : 'Ativar'?>">
                      <? if ($ativo): ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                      Desativar
                      <? else: ?>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                      Ativar
                      <? endif; ?>
                    </a>
                  </div>

                  <div class="ul-col">
                    <span class="ul-inline-label">Usuario</span>
                    <div class="ul-user-block">
                      <div class="ul-avatar">
                        <? if ($u->img): ?>
                          <img src="<?=base_url()?>imagens/usuarios/min/<?=$u->img?>" alt="<?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?>">
                        <? else: ?>
                          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
                        <? endif; ?>
                      </div>
                      <div style="min-width:0;">
                        <p class="ul-report-name">
                          <? if ((int)$nivel === 5): ?>
                            <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$u->id?>"><?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?></a>
                          <? else: ?>
                            <?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?>
                          <? endif; ?>
                        </p>
                        <div class="ul-report-sub">
                          ID #<?=$u->id?>
                          <? if ((int)$u->nivel < 5 && !empty($u->login)): ?> · <?=htmlspecialchars($u->login, ENT_QUOTES, 'UTF-8')?><? endif; ?>
                        </div>
                        <? if (!empty($u->classe)): ?>
                        <div class="ul-report-sub"><?=htmlspecialchars($u->classe, ENT_QUOTES, 'UTF-8')?></div>
                        <? endif; ?>
                      </div>
                    </div>
                  </div>

                  <div class="ul-col">
                    <span class="ul-inline-label">Atividade</span>
                    <div><?=htmlspecialchars($atividade, ENT_QUOTES, 'UTF-8')?></div>
                    <? if (!empty($u->tenant_role)): ?>
                    <div class="ul-report-sub"><?=htmlspecialchars($u->tenant_role, ENT_QUOTES, 'UTF-8')?></div>
                    <? endif; ?>
                  </div>

                  <div class="ul-col">
                    <span class="ul-inline-label">Indicadores</span>
                    <div class="ul-kpi-stack">
                      <span class="ul-chip"><?=utec_relatorio_formatar_numero($u->total_agendamentos_gerados)?> geradas</span>
                      <span class="ul-chip"><?=utec_relatorio_formatar_numero($u->total_agendamentos_vinculados)?> vinculadas</span>
                      <span class="ul-chip"><?=utec_relatorio_formatar_numero($u->total_pacientes)?> pacientes</span>
                      <? if ($mostrar_plano): ?>
                      <span class="ul-chip ul-chip-plan"><?=$plano?></span>
                      <? endif; ?>
                      <? if ($ultima_atividade): ?>
                      <span class="ul-chip ul-chip-muted">Ultima: <?=$ultima_atividade?></span>
                      <? endif; ?>
                    </div>
                  </div>

                  <div class="ul-col ul-contact">
                    <span class="ul-inline-label">Contato</span>
                    <? if ($telefone_limpo): ?>
                    <div><a href="https://api.whatsapp.com/send?phone=55<?=$telefone_limpo?>" target="_blank"><?=htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8')?></a></div>
                    <? else: ?>
                    <div class="ul-report-sub">Telefone nao informado</div>
                    <? endif; ?>
                    <? if (!empty($u->email)): ?>
                    <div><?=htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8')?></div>
                    <? endif; ?>
                  </div>

                  <div class="ul-col">
                    <span class="ul-inline-label">Cadastro</span>
                    <div><?=$cadastro?></div>
                    <? if (!empty($u->id_user)): ?>
                    <div class="ul-report-sub">Vinculo #<?=htmlspecialchars((string)$u->id_user, ENT_QUOTES, 'UTF-8')?></div>
                    <? endif; ?>
                  </div>

                  <div class="ul-col">
                    <span class="ul-inline-label">Status</span>
                    <span class="ul-status-pill <?=$ativo ? 'ul-status-ativo' : 'ul-status-inativo'?>">
                      <?=$ativo ? 'Ativo' : 'Inativo'?>
                    </span>
                  </div>
                </div>
                <? endforeach; endif; ?>
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

      $('#ul-filter').on('input', function(){
        var q = $.trim($(this).val()).toLowerCase();
        $('#ul-grid .ul-report-row').each(function(){
          $(this).toggle(!q || $(this).data('nome').indexOf(q) !== -1);
        });
      });
    </script>
  </body>
</html>
