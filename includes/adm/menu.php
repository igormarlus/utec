<?
$ci_menu = get_instance();
$ci_menu->load->model('padrao_model');

$menu_user = $ci_menu->padrao_model->get_usuario_logado();
$menu_user_id = $this->session->userdata('id');
$menu_user_name = $this->session->userdata('nome');
$menu_level = (int)$this->session->userdata('nivel');
$menu_tenant = method_exists($ci_menu->padrao_model, 'get_logged_tenant') ? $ci_menu->padrao_model->get_logged_tenant() : null;
$menu_user_has_saas = method_exists($ci_menu->padrao_model, 'usuario_tem_saas') ? $ci_menu->padrao_model->usuario_tem_saas($menu_user) : false;
$menu_level_name = method_exists($ci_menu->padrao_model, 'get_nivel_nome') ? $ci_menu->padrao_model->get_nivel_nome($menu_level) : 'Usuario';
$menu_dashboard_url = ($menu_level === 1 || $menu_level === 5) ? base_url().'adm/usuarios/dash' : base_url().'adm/atendimento';
$menu_logo_path = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '\\/').DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'logo-w.png' : '';
$menu_logo_available = $menu_logo_path && file_exists($menu_logo_path);
$menu_can_admin = ($menu_level === 1);
$menu_can_team = in_array($menu_level, [1,2,3]);
$menu_can_reports = in_array($menu_level, [1,2,3,4]);
$menu_can_commercial = in_array($menu_level, [1,2,3]) && $menu_user_has_saas;
$menu_can_saas = in_array($menu_level, [1,2,3]) && $menu_user_has_saas;
$menu_is_patient = ($menu_level === 5);
$menu_is_collaborator = ($menu_level === 4);

$menu_sections = [];

if($menu_can_admin){
	$menu_sections[] = [
		'title' => 'Administracao',
		'items' => [
			[
				'label' => 'Usuarios',
				'icon' => 'os-icon-package',
				'url' => base_url().'adm/usuarios',
				'children' => [
					['label' => 'Todos os usuarios', 'url' => base_url().'adm/usuarios'],
				],
			],
			[
				'label' => 'Blog',
				'icon' => 'os-icon-edit-32',
				'url' => base_url().'adm/blog',
				'children' => [
					['label' => 'Artigos',     'url' => base_url().'adm/blog'],
					['label' => 'Novo artigo', 'url' => base_url().'adm/blog/novo'],
				],
			],
			[
				'label' => 'Especialidades',
				'icon' => 'os-icon-star',
				'url' => base_url().'adm/especialidades',
				'children' => [
					['label' => 'Campos por especialidade', 'url' => base_url().'adm/especialidades'],
					['label' => 'Novo campo',               'url' => base_url().'adm/especialidades/form'],
				],
			],
		],
	];
}

$menu_operacao_items = [];
$menu_operacao_items[] = [
	'label' => 'Visao geral',
	'icon' => 'os-icon-bar-chart-stats-up',
	'url' => base_url().'adm/usuarios/dash',
	'children' => [
		['label' => 'Painel principal', 'url' => base_url().'adm/usuarios/dash'],
	],
];

if(!$menu_is_patient){
	$menu_operacao_items[] = [
		'label' => 'Agenda',
		'icon' => 'os-icon-clipboard',
		'url' => base_url().'adm/atendimento',
		'children' => [
			['label' => 'Atendimentos', 'url' => base_url().'adm/atendimento'],
		],
	];

	$menu_operacao_items[] = [
		'label' => 'Pacientes',
		'icon' => 'os-icon-user-male-circle2',
		'url' => base_url().'adm/usuarios/rel/5',
		'children' => [
			['label' => 'Lista de pacientes', 'url' => base_url().'adm/usuarios/rel/5'],
			['label' => 'Novo paciente', 'url' => base_url().'adm/usuarios/cadastro/5'],
		],
	];
}

if($menu_can_team){
	$menu_team_children = [];
	if($menu_level === 1){
		foreach($this->db->query("SELECT * FROM usuarios_niveis ORDER BY nome asc")->result() as $menu_role_item){
			$menu_team_children[] = [
				'label' => $menu_role_item->nome,
				'url' => base_url().'adm/usuarios/rel/'.$menu_role_item->nivel,
			];
		}
	}else{
		if($menu_level === 2){
			$menu_team_children[] = ['label' => 'Prestadores', 'url' => base_url().'adm/usuarios/rel/3'];
			$menu_team_children[] = ['label' => 'Colaboradores', 'url' => base_url().'adm/usuarios/rel/4'];
		}
		if($menu_level === 3){
			$menu_team_children[] = ['label' => 'Colaboradores', 'url' => base_url().'adm/usuarios/rel/4'];
		}
		$menu_team_children[] = ['label' => 'Pacientes', 'url' => base_url().'adm/usuarios/rel/5'];
	}

	$menu_operacao_items[] = [
		'label' => 'Equipe',
		'icon' => 'os-icon-users',
		'url' => base_url().'adm/usuarios/rel/'.($menu_level === 3 ? '4' : '3'),
		'children' => $menu_team_children,
	];
}

if($menu_is_collaborator){
	$menu_operacao_items[] = [
		'label' => 'Minha rotina',
		'icon' => 'os-icon-check-circle',
		'url' => base_url().'adm/atendimento',
		'children' => [
			['label' => 'Agenda do dia', 'url' => base_url().'adm/atendimento'],
			['label' => 'Pacientes vinculados', 'url' => base_url().'adm/usuarios/rel/5'],
		],
	];
}

if($menu_is_patient){
	$menu_operacao_items = [
		[
			'label' => 'Meu cadastro',
			'icon' => 'os-icon-user-male-circle2',
			'url' => base_url().'adm/usuarios/edicao/'.$menu_user_id,
			'children' => [
				['label' => 'Editar meus dados', 'url' => base_url().'adm/usuarios/edicao/'.$menu_user_id],
			],
		],
	];
}

$menu_sections[] = [
	'title' => $menu_is_patient ? 'Minha area' : 'Operacao clinica',
	'items' => $menu_operacao_items,
];

if($menu_can_commercial){
	$menu_commercial_items = [
		[
			'label' => 'Planos',
			'icon' => 'os-icon-layers',
			'url' => base_url().'adm/produtos',
			'children' => $menu_can_admin ? [
				['label' => 'Catalogo de planos', 'url' => base_url().'adm/produtos'],
				['label' => 'Tipos de plano', 'url' => base_url().'adm/produtos/categorias'],
			] : [
				['label' => 'Catalogo de planos', 'url' => base_url().'adm/produtos'],
			],
		],
		[
			'label' => 'Assinaturas',
			'icon' => 'os-icon-shopping-bag',
			'url' => base_url().'adm/produtos/rel_pedidos',
			'children' => [
				['label' => 'Contratacoes', 'url' => base_url().'adm/produtos/rel_pedidos'],
			],
		],
	];

	if($menu_level !== 1 && $menu_tenant){
		$menu_commercial_items[] = [
			'label' => 'Minha assinatura',
			'icon' => 'os-icon-wallet-loaded',
			'url' => base_url().'adm/usuarios/assinatura',
			'children' => [
				['label' => 'Resumo da assinatura', 'url' => base_url().'adm/usuarios/assinatura'],
			],
		];
	}

	if($menu_can_saas && $menu_can_admin){
		$menu_commercial_items[] = [
			'label' => 'Operacao SaaS',
			'icon' => 'os-icon-home-10',
			'url' => base_url().'adm/saas',
			'children' => [
				['label' => 'Dashboard SaaS', 'url' => base_url().'adm/saas'],
			],
		];
	}

	$menu_sections[] = [
		'title' => 'Comercial',
		'items' => $menu_commercial_items,
	];
}

if($menu_can_reports){
	$menu_sections[] = [
		'title' => 'Gestao',
		'items' => [
			[
				'label' => 'Relatorios',
				'icon' => 'os-icon-folder',
				'url' => base_url().'adm/usuarios/relatorios_clinicos',
				'children' => [
					['label' => 'Relatorios clinicos', 'url' => base_url().'adm/usuarios/relatorios_clinicos'],
				],
			],
		],
	];
}

$menu_sections[] = [
	'title' => 'Minha conta',
	'items' => [
		[
			'label' => 'Perfil',
			'icon' => 'os-icon-user-male-circle2',
			'url' => base_url().'adm/usuarios/edicao/'.$menu_user_id,
			'children' => [
				['label' => 'Editar cadastro', 'url' => base_url().'adm/usuarios/edicao/'.$menu_user_id],
				['label' => 'Sair', 'url' => base_url().'adm/usuarios/logout'],
			],
		],
	],
];
?>
<link href="<?=base_url()?>css/utec-redesign.css" rel="stylesheet">
<style>
  .adm-menu-brand {
    align-items: center;
    display: flex;
    gap: 10px;
  }
  .adm-menu-brand-image {
    display: block;
    height: 42px;
    max-width: 180px;
    object-fit: contain;
    width: auto;
  }
  .adm-menu-brand-fallback {
    align-items: center;
    display: flex;
    gap: 10px;
  }
  .adm-menu-brand-fallback .logo-element {
    flex-shrink: 0;
  }
  @media (max-width: 1199.98px) {
    .adm-menu-brand-image {
      height: 38px;
      max-width: 160px;
    }
  }
</style>
<div class="menu-mobile menu-activated-on-click color-scheme-dark">
  <div class="menu-and-user">
    <ul class="main-menu">
      <? foreach($menu_sections as $menu_section){ ?>
        <? foreach($menu_section['items'] as $menu_item){ ?>
          <li class="has-sub-menu">
            <a href="<?=$menu_item['url']?>">
              <div class="icon-w"><div class="os-icon <?=$menu_item['icon']?>"></div></div>
              <span><?=$menu_item['label']?></span>
            </a>
            <? if(isset($menu_item['children']) && count($menu_item['children'])){ ?>
              <ul class="sub-menu">
                <? foreach($menu_item['children'] as $menu_child){ ?>
                  <li><a href="<?=$menu_child['url']?>"><?=$menu_child['label']?></a></li>
                <? } ?>
              </ul>
            <? } ?>
          </li>
        <? } ?>
      <? } ?>
    </ul>
  </div>
</div>
<div class="menu-w color-scheme-light color-style-transparent menu-position-side menu-side-left menu-layout-compact sub-menu-style-over sub-menu-color-bright selected-menu-color-light menu-activated-on-hover menu-has-selected-link">
  <div class="logo-w">
    <a class="logo" href="<?=$menu_dashboard_url?>" style="display:flex;align-items:center;gap:12px;padding:6px 0;text-decoration:none;">
      <div class="ut-agenda-brand-badge" style="flex-shrink:0;">
        <img src="<?=base_url()?>img/logo-w.png" alt="UT"
             onerror="this.parentNode.innerHTML='<span style=&quot;color:#fff;font-family:var(--ut-font);font-size:13px;font-weight:900;&quot;>UT</span>'">
      </div>
      <div>
        <div style="color:#6ee7b7;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;font-family:var(--ut-font);">UTecnologia</div>
        <div style="color:#f0fdf4;font-size:16px;font-weight:800;line-height:1.1;font-family:var(--ut-font);">Saude</div>
      </div>
    </a>
  </div>
  <div class="logged-user-w avatar-inline">
    <div class="logged-user-i">
      <div class="avatar-w"></div>
      <div class="logged-user-info-w">
        <div class="logged-user-name">
          <?=$menu_user_name?>
        </div>
        <div class="logged-user-role">
          <?=$menu_level_name?>
          <? if($menu_tenant && isset($menu_tenant->tenant_nome) && trim((string)$menu_tenant->tenant_nome) !== ''){ ?>
            <span style="display:block;font-size:11px;color:#94a3b8;margin-top:2px;"><?=$menu_tenant->tenant_nome?></span>
          <? } ?>
        </div>
      </div>
      <div class="logged-user-toggler-arrow">
        <div class="os-icon os-icon-chevron-down"></div>
      </div>
      <div class="logged-user-menu color-style-bright">
        <div class="logged-user-avatar-info">
          <div class="logged-user-info-w">
            <div class="logged-user-name">
              <?=$menu_user_name?>
            </div>
            <div class="logged-user-role">
              <?=$menu_level_name?>
            </div>
          </div>
        </div>
        <div class="bg-icon">
          <i class="os-icon os-icon-wallet-loaded"></i>
        </div>
        <ul>
          <li>
            <a href="<?=base_url()?>adm/usuarios/edicao/<?=$menu_user_id?>"><i class="os-icon os-icon-user-male-circle2"></i><span>Meus dados</span></a>
          </li>
          <li>
            <a href="<?=base_url()?>adm/usuarios/logout"><i class="os-icon os-icon-signs-11"></i><span>Sair</span></a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <ul class="main-menu">
    <? foreach($menu_sections as $menu_section){ ?>
      <li class="sub-header">
        <span><?=$menu_section['title']?></span>
      </li>
      <? foreach($menu_section['items'] as $menu_item){ ?>
        <li class="has-sub-menu">
          <a href="<?=$menu_item['url']?>">
            <div class="icon-w"><div class="os-icon <?=$menu_item['icon']?>"></div></div>
            <span><?=$menu_item['label']?></span>
          </a>
          <? if(isset($menu_item['children']) && count($menu_item['children'])){ ?>
            <ul class="sub-menu">
              <? foreach($menu_item['children'] as $menu_child){ ?>
                <li><a href="<?=$menu_child['url']?>"><?=$menu_child['label']?></a></li>
              <? } ?>
            </ul>
          <? } ?>
        </li>
      <? } ?>
    <? } ?>
  </ul>
</div>
