<?
$ci_top = get_instance();
$ci_top->load->model('padrao_model');
$dd_usuario = $ci_top->padrao_model->get_usuario_logado();
$top_user_id = $this->session->userdata('id');
$top_level = (int)$this->session->userdata('nivel');
$top_level_name = method_exists($ci_top->padrao_model, 'get_nivel_nome') ? $ci_top->padrao_model->get_nivel_nome($top_level) : 'Usuario';
$top_tenant = method_exists($ci_top->padrao_model, 'get_logged_tenant') ? $ci_top->padrao_model->get_logged_tenant() : null;
$top_user_has_saas = method_exists($ci_top->padrao_model, 'usuario_tem_saas') ? $ci_top->padrao_model->usuario_tem_saas($dd_usuario) : false;
$top_manual_url = in_array($top_level, [2,3,4]) ? base_url().'adm/usuarios/manual/'.$top_level : '';
$top_manual_pdf_url = in_array($top_level, [2,3,4]) ? base_url().'adm/usuarios/manual_pdf/'.$top_level : '';
$top_tenant_status = ($top_tenant && isset($top_tenant->status) && (int)$top_tenant->status === 1) ? 'Ativo' : ($top_tenant ? 'Suspenso' : 'Sem tenant');
$top_logo_path = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '\\/').DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'logo-w.png' : '';
$top_logo_available = $top_logo_path && file_exists($top_logo_path);
$top_notifications = [];

$top_unread_notifications = [];
$top_unread_count = 0;
if($top_user_id){
	$ci_top->load->model('Notificacoes_model', 'notificacoes_model');
	$top_unread_notifications = $ci_top->notificacoes_model->listar_nao_lidas($top_user_id, 8);
	$top_unread_count = $ci_top->notificacoes_model->contar_nao_lidas($top_user_id);
}

if($top_tenant && isset($top_tenant->tenant_nome)){
	$top_notifications[] = [
		'title' => 'Assinatura da clinica',
		'copy' => $top_tenant->tenant_nome.' • '.$top_tenant_status,
		'url' => base_url().'adm/usuarios/assinatura',
	];
}

if(in_array($top_level, [1,2,3,4])){
	$top_notifications[] = [
		'title' => 'Agenda clinica',
		'copy' => 'Acesse os atendimentos e acompanhe a rotina do dia.',
		'url' => base_url().'adm/atendimento',
	];
}

if(in_array($top_level, [1,2,3]) && $top_user_has_saas){
	$top_notifications[] = [
		'title' => 'Operacao comercial',
		'copy' => 'Veja planos, assinaturas e evolucao do tenant.',
		'url' => base_url().'adm/produtos/rel_pedidos',
	];
}

if($top_level === 5){
	$top_notifications[] = [
		'title' => 'Minha conta',
		'copy' => 'Mantenha seus dados atualizados no cadastro.',
		'url' => base_url().'adm/usuarios/edicao/'.$top_user_id,
	];
}

$top_shortcuts = [];
if($top_level === 1){
	$top_shortcuts[] = ['label' => 'Usuarios', 'url' => base_url().'adm/usuarios'];
	if($top_user_has_saas){
		$top_shortcuts[] = ['label' => 'SaaS', 'url' => base_url().'adm/saas'];
		$top_shortcuts[] = ['label' => 'Planos', 'url' => base_url().'adm/produtos'];
	}
}
if($top_level === 2){
	$top_shortcuts[] = ['label' => 'Agenda', 'url' => base_url().'adm/atendimento'];
	$top_shortcuts[] = ['label' => 'Calendário', 'url' => base_url().'adm/calendario'];
	$top_shortcuts[] = ['label' => 'Pacientes', 'url' => base_url().'adm/usuarios/rel/5'];
	$top_shortcuts[] = ['label' => 'Equipe', 'url' => base_url().'adm/usuarios/rel/3'];
	if($top_tenant && $top_user_has_saas){ $top_shortcuts[] = ['label' => 'Minha assinatura', 'url' => base_url().'adm/usuarios/assinatura']; }
	if($top_manual_url){ $top_shortcuts[] = ['label' => 'Ajuda', 'url' => $top_manual_url]; }
	if($top_manual_pdf_url){ $top_shortcuts[] = ['label' => 'PDF', 'url' => $top_manual_pdf_url]; }
}
if($top_level === 3){
	$top_shortcuts[] = ['label' => 'Agenda', 'url' => base_url().'adm/atendimento'];
	$top_shortcuts[] = ['label' => 'Calendário', 'url' => base_url().'adm/calendario'];
	$top_shortcuts[] = ['label' => 'Pacientes', 'url' => base_url().'adm/usuarios/rel/5'];
	$top_shortcuts[] = ['label' => 'Colaboradores', 'url' => base_url().'adm/usuarios/rel/4'];
	if($top_tenant && $top_user_has_saas){ $top_shortcuts[] = ['label' => 'Minha assinatura', 'url' => base_url().'adm/usuarios/assinatura']; }
	if($top_manual_url){ $top_shortcuts[] = ['label' => 'Ajuda', 'url' => $top_manual_url]; }
	if($top_manual_pdf_url){ $top_shortcuts[] = ['label' => 'PDF', 'url' => $top_manual_pdf_url]; }
}
if($top_level === 4){
	$top_shortcuts[] = ['label' => 'Agenda', 'url' => base_url().'adm/atendimento'];
	$top_shortcuts[] = ['label' => 'Calendário', 'url' => base_url().'adm/calendario'];
	$top_shortcuts[] = ['label' => 'Pacientes', 'url' => base_url().'adm/usuarios/rel/5'];
	if($top_tenant && $top_user_has_saas){ $top_shortcuts[] = ['label' => 'Minha assinatura', 'url' => base_url().'adm/usuarios/assinatura']; }
	if($top_manual_url){ $top_shortcuts[] = ['label' => 'Ajuda', 'url' => $top_manual_url]; }
	if($top_manual_pdf_url){ $top_shortcuts[] = ['label' => 'PDF', 'url' => $top_manual_pdf_url]; }
}
if($top_level === 5){
	$top_shortcuts[] = ['label' => 'Meu cadastro', 'url' => base_url().'adm/usuarios/edicao/'.$top_user_id];
}
?>
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
<style>
  .utec-top-brand {
    align-items: center;
    color: #f0fdf4;
    display: inline-flex;
    gap: 10px;
    margin-left: 10px;
    padding: 6px 12px 6px 8px;
    text-decoration: none;
    border-radius: 12px;
    transition: background .15s;
  }
  .utec-top-brand:hover,
  .utec-top-brand:focus { color: #fff; text-decoration: none; background: rgba(34,197,94,.12); }
  .utec-top-brand-copy small {
    color: #6ee7b7; font-size: 10px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase; display: block; margin-bottom: 1px;
  }
  .utec-top-brand-copy strong {
    color: #f0fdf4; font-family: var(--ut-font); font-size: 15px; font-weight: 800; display: block;
  }
</style>
<div class="top-bar color-scheme-transparent">
  <a href="<?=base_url()?>adm/usuarios/dash" class="utec-top-brand" aria-label="UTecnologia Saude">
    <? if($top_logo_available){ ?>
      <span class="ut-agenda-brand-badge">
        <img src="<?=base_url()?>img/logo-w.png" alt="UTecnologia Saude">
      </span>
      <span class="utec-top-brand-copy">
        <small>UTecnologia</small>
        <strong>Saude</strong>
      </span>
    <? } else { ?>
      <span class="ut-agenda-brand-badge" style="background:var(--ut-green-900);">
        <span style="color:#fff;font-family:var(--ut-font);font-size:12px;font-weight:900;">UT</span>
      </span>
      <span class="utec-top-brand-copy">
        <small>UTecnologia</small>
        <strong>Saude</strong>
      </span>
    <? } ?>
  </a>
  <div class="top-menu-controls">
    <div class="element-search autosuggest-search-activator" style="min-width:260px;">
      <input placeholder="Buscar paciente, atendimento ou plano..." type="text">
    </div>

    <? if(count($top_shortcuts)){ ?>
      <div class="ut-top-shortcuts" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-right:12px;">
        <? foreach($top_shortcuts as $top_shortcut){ ?>
          <a href="<?=$top_shortcut['url']?>" class="btn btn-white btn-sm" style="border:1px solid #dbe2ea;"><?=$top_shortcut['label']?></a>
        <? } ?>
      </div>
    <? } ?>

    <div class="messages-notifications os-dropdown-trigger os-dropdown-position-left">
      <i class="os-icon os-icon-bell"></i>
      <? if($top_unread_count > 0){ ?>
        <div class="new-messages-count"><?=$top_unread_count?></div>
      <? } ?>
      <div class="os-dropdown light message-list">
        <ul>
          <? if(count($top_unread_notifications)){ ?>
            <? foreach($top_unread_notifications as $top_evento){ ?>
              <li>
                <a href="<?=base_url()?>adm/notificacoes/abrir/<?=(int)$top_evento->id?>">
                  <div class="message-content">
                    <h6 class="message-from"><?=htmlspecialchars((string)$top_evento->titulo)?></h6>
                    <h6 class="message-title"><?=htmlspecialchars((string)$top_evento->mensagem)?></h6>
                  </div>
                </a>
              </li>
            <? } ?>
          <? } else { ?>
            <li>
              <div class="message-content">
                <h6 class="message-title" style="color:#94a3b8;">Nenhum aviso novo.</h6>
              </div>
            </li>
          <? } ?>
        </ul>
      </div>
    </div>

    <div class="messages-notifications os-dropdown-trigger os-dropdown-position-left">
      <i class="os-icon os-icon-activity"></i>
      <div class="new-messages-count">
        <?=count($top_notifications)?>
      </div>
      <div class="os-dropdown light message-list">
        <ul>
          <? foreach($top_notifications as $top_notice){ ?>
            <li>
              <a href="<?=$top_notice['url']?>">
                <div class="message-content">
                  <h6 class="message-from">
                    <?=$top_notice['title']?>
                  </h6>
                  <h6 class="message-title">
                    <?=$top_notice['copy']?>
                  </h6>
                </div>
              </a>
            </li>
          <? } ?>
        </ul>
      </div>
    </div>

    <div class="logged-user-w">
      <div class="logged-user-i">
        <div class="avatar-w">
          <? if($dd_usuario && !empty($dd_usuario->img)){ ?>
            <img src="<?=base_url()?>imagens/usuarios/min/<?=$dd_usuario->img?>" alt="<?=$this->session->userdata('nome')?>" style="max-width:40px;max-height:40px">
          <? }else{ ?>
            <div style="width:40px;height:40px;border-radius:50%;background:#e2e8f0;color:#475569;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">SEM FOTO</div>
          <? } ?>
        </div>
        <div class="logged-user-menu color-style-bright">
          <div class="logged-user-avatar-info">
            <div class="avatar-w">
              <? if($dd_usuario && !empty($dd_usuario->img)){ ?>
                <img src="<?=base_url()?>imagens/usuarios/min/<?=$dd_usuario->img?>" alt="<?=$this->session->userdata('nome')?>">
              <? }else{ ?>
                <div style="width:40px;height:40px;border-radius:50%;background:#e2e8f0;color:#475569;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">SEM FOTO</div>
              <? } ?>
            </div>
            <div class="logged-user-info-w">
              <div class="logged-user-name">
                <?=$this->session->userdata('nome')?>
              </div>
              <div class="logged-user-role">
                <?=$top_level_name?><? if($dd_usuario && !empty($dd_usuario->especialidade)){ echo ' • '.$dd_usuario->especialidade; } ?>
                <? if($top_tenant && isset($top_tenant->tenant_nome)){ ?>
                  <span style="display:block;font-size:11px;color:#64748b;margin-top:2px;"><?=$top_tenant->tenant_nome?> • <?=$top_tenant_status?></span>
                <? } ?>
              </div>
            </div>
          </div>
          <div class="bg-icon">
            <i class="os-icon os-icon-wallet-loaded"></i>
          </div>
          <ul>
            <li>
              <a href="<?=base_url()?>adm/usuarios/edicao/<?=$top_user_id?>"><i class="os-icon os-icon-user-male-circle2"></i><span>Meus dados</span></a>
            </li>
            <? if(in_array($top_level, [1,2,3,4])){ ?>
              <li>
                <a href="<?=base_url()?>adm/atendimento"><i class="os-icon os-icon-clipboard"></i><span>Agenda clinica</span></a>
              </li>
            <? } ?>
            <? if(in_array($top_level, [1,2,3]) && $top_user_has_saas){ ?>
              <li>
                <a href="<?=base_url()?>adm/produtos/rel_pedidos"><i class="os-icon os-icon-coins-4"></i><span>Planos e assinaturas</span></a>
              </li>
            <? } ?>
            <? if($top_tenant && in_array($top_level, [2,3,4]) && $top_user_has_saas){ ?>
              <li>
                <a href="<?=base_url()?>adm/usuarios/assinatura"><i class="os-icon os-icon-wallet-loaded"></i><span>Minha assinatura</span></a>
              </li>
            <? } ?>
            <? if($top_manual_url){ ?>
              <li>
                <a href="<?=$top_manual_url?>"><i class="os-icon os-icon-info"></i><span>Manual da funcao</span></a>
              </li>
            <? } ?>
            <? if($top_manual_pdf_url){ ?>
              <li>
                <a href="<?=$top_manual_pdf_url?>" target="_blank"><i class="os-icon os-icon-file-text"></i><span>Manual em PDF</span></a>
              </li>
            <? } ?>
            <li>
              <a href="<?=base_url()?>adm/usuarios/logout"><i class="os-icon os-icon-signs-11"></i><span>Sair</span></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
