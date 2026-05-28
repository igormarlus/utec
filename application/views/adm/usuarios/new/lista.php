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
      .ul-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
      }
      .ul-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(15,23,42,.05);
        padding: 18px 18px 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: box-shadow .18s, border-color .18s;
      }
      .ul-card:hover { box-shadow: 0 6px 20px rgba(15,23,42,.10); border-color: #c7d2e8; }
      .ul-card-top { display: flex; align-items: center; gap: 14px; }
      .ul-avatar {
        width: 46px; height: 46px; border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #0f766e);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 18px; flex-shrink: 0;
        overflow: hidden;
      }
      .ul-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
      .ul-card-name { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3; }
      .ul-card-sub { font-size: 12px; color: #64748b; margin: 2px 0 0; }
      .ul-card-meta { display: flex; flex-wrap: wrap; gap: 6px; }
      .ul-meta-pill {
        display: inline-flex; align-items: center; gap: 4px;
        background: #f1f5f9; border-radius: 999px;
        padding: 3px 10px; font-size: 12px; color: #475569;
        white-space: nowrap;
      }
      .ul-meta-pill a { color: inherit; text-decoration: none; }
      .ul-meta-pill a:hover { color: #0f766e; }
      .ul-status-pill {
        display: inline-block; border-radius: 999px;
        padding: 2px 10px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
      }
      .ul-status-ativo { background: #dcfce7; color: #15803d; }
      .ul-status-inativo { background: #fee2e2; color: #b91c1c; }
      .ul-card-actions { display: flex; flex-wrap: wrap; gap: 6px; padding-top: 6px; border-top: 1px solid #f1f5f9; }
      .ul-btn {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 8px; padding: 5px 11px; font-size: 12px; font-weight: 600;
        border: 1.5px solid transparent; text-decoration: none; cursor: pointer;
        transition: background .15s, border-color .15s;
        line-height: 1.4;
      }
      .ul-btn-prontuario { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
      .ul-btn-prontuario:hover { background: #dbeafe; color: #1d4ed8; }
      .ul-btn-edit { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
      .ul-btn-edit:hover { background: #ffedd5; color: #9a3412; }
      .ul-btn-remove { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
      .ul-btn-remove:hover { background: #fee2e2; color: #991b1b; }
      .ul-btn-acessar { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
      .ul-btn-acessar:hover { background: #dcfce7; color: #166534; }
      .ul-empty {
        grid-column: 1 / -1;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
        padding: 48px 24px; text-align: center; color: #94a3b8;
      }
      .ul-empty-icon { font-size: 40px; margin-bottom: 12px; }
      .ul-empty-txt { font-size: 14px; }
      /* Menu colapsável lateral */
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

              <? $total = $usuarios->num_rows(); $nivel_nome = $this->padrao_model->get_by_matriz('nivel', $nivel, 'usuarios_niveis')->row()->nome; ?>
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

              <div class="ul-grid" id="ul-grid">
                <? if ($total === 0): ?>
                <div class="ul-empty">
                  <div class="ul-empty-icon">👤</div>
                  <div class="ul-empty-txt">Nenhum registro encontrado. <a href="<?=base_url()?>adm/usuarios/cadastro/<?=$nivel?>">Cadastrar o primeiro</a>.</div>
                </div>
                <? else: foreach ($usuarios->result() as $u):
                  $ini = $u->nome ? mb_strtoupper(mb_substr(trim($u->nome), 0, 1, 'UTF-8')) : '?';
                  $ativo = (int)$u->status === 1;
                ?>
                <div class="ul-card" data-nome="<?=mb_strtolower($u->nome, 'UTF-8')?>">
                  <div class="ul-card-top">
                    <div class="ul-avatar">
                      <? if ($u->img): ?>
                        <img src="<?=base_url()?>imagens/usuarios/min/<?=$u->img?>" alt="<?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?>">
                      <? else: ?>
                        <?=$ini?>
                      <? endif; ?>
                    </div>
                    <div>
                      <? if ($nivel == 5): ?>
                        <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$u->id?>" class="ul-card-name" style="text-decoration:none;color:#0f172a"><?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?></a>
                      <? else: ?>
                        <p class="ul-card-name"><?=htmlspecialchars($u->nome, ENT_QUOTES, 'UTF-8')?></p>
                      <? endif; ?>
                      <p class="ul-card-sub">
                        ID #<?=$u->id?>
                        <? if ($nivel < 5 && $u->login): ?> &middot; <?=htmlspecialchars($u->login, ENT_QUOTES, 'UTF-8')?><? endif; ?>
                      </p>
                    </div>
                  </div>

                  <div class="ul-card-meta">
                    <? if ($u->telefone):
                      $tel_trat = preg_replace('/[^0-9]/', '', $u->telefone);
                    ?>
                    <span class="ul-meta-pill">
                      📞 <a href="https://api.whatsapp.com/send?phone=55<?=$tel_trat?>" target="_blank"><?=htmlspecialchars($u->telefone, ENT_QUOTES, 'UTF-8')?></a>
                    </span>
                    <? endif; ?>
                    <? if ($u->dt_cadastro): ?>
                    <span class="ul-meta-pill">📅 <?=date('d/m/Y', strtotime($u->dt_cadastro))?></span>
                    <? endif; ?>
                    <span class="ul-status-pill <?=$ativo ? 'ul-status-ativo' : 'ul-status-inativo'?>">
                      <?=$ativo ? 'Ativo' : 'Inativo'?>
                    </span>
                  </div>

                  <div class="ul-card-actions">
                    <? if ($nivel == 5): ?>
                    <a href="<?=base_url()?>adm/usuarios/prontuario/<?=$u->id?>" class="ul-btn ul-btn-prontuario">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                      Prontuário
                    </a>
                    <? endif; ?>
                    <a href="<?=base_url()?>adm/usuarios/edicao/<?=$u->id?>" class="ul-btn ul-btn-edit">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      Editar
                    </a>
                    <a href="<?=base_url()?>adm/usuarios/remover/<?=$u->id?>" class="ul-btn ul-btn-remove" onclick="return confirm('Remover <?=htmlspecialchars(addslashes($u->nome), ENT_QUOTES, 'UTF-8')?>?')">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                      Remover
                    </a>
                    <? if ($this->session->userdata('nivel') == 1): ?>
                    <a href="<?=base_url()?>admin/logar_como/<?=$u->id?>" class="ul-btn ul-btn-acessar" target="_blank">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                      Acessar
                    </a>
                    <? endif; ?>
                    <a href="<?=base_url()?>adm/usuarios/set_status/<?=$u->id?>/<?=$u->status?>" class="ul-btn" style="background:#f8fafc;color:#64748b;border-color:#e2e8f0" title="<?=$ativo ? 'Desativar' : 'Ativar'?>">
                      <? if ($ativo): ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg> Desativar
                      <? else: ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Ativar
                      <? endif; ?>
                    </a>
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
        $('#ul-grid .ul-card').each(function(){
          $(this).toggle(!q || $(this).data('nome').indexOf(q) !== -1);
        });
      });
    </script>
  </body>
</html>
