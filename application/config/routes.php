<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['locations'] = 'rpgLocations/index';
$route['experimentar'] = 'home/experimentar';
$route['experimentar/enviar'] = 'home/iniciar_experiencia';
$route['experimentar/sucesso'] = 'home/experiencia_sucesso';
$route['assinar'] = 'home/assinar';
$route['assinar/enviar'] = 'home/contratar';
$route['assinar/pagamento'] = 'home/assinatura_pagamento';
$route['assinar/pagamento/pix'] = 'home/assinatura_pagamento_pix';
$route['assinar/pagamento/cartao'] = 'home/assinatura_pagamento_cartao';
$route['assinar/pagamento/status'] = 'home/assinatura_pagamento_status';
$route['assinar/sucesso'] = 'home/assinatura_sucesso';
$route['acesso/senha/(:any)'] = 'home/definir_senha/$1';
$route['acesso/senha']        = 'home/definir_senha';
$route['acesso/salvar']       = 'home/salvar_senha';
$route['webhooks/mercadopago'] = 'adm/saas/webhook_mercadopago';

// Leads
$route['adm/leads']                        = 'adm/leads/index';
$route['adm/leads/atualizar_status']       = 'adm/leads/atualizar_status';

// SEO landing pages
$route['sistema-para-clinicas']            = 'home/seo_sistema_para_clinicas';
$route['sistema-para-clinica-medica']      = 'home/seo_sistema_clinica_medica';
$route['sistema-prontuario-eletronico']    = 'home/seo_prontuario_eletronico';
$route['sistema-para-psicologos']          = 'home/seo_sistema_psicologos';
$route['sistema-para-dentistas']           = 'home/seo_sistema_dentistas';
$route['software-para-clinicas-odontologicas'] = 'home/seo_software_clinicas_odontologicas';
$route['sistema-para-consultorio-medico']  = 'home/seo_consultorio_medico';
$route['sistema-para-clinica-de-fisioterapia'] = 'home/seo_clinica_fisioterapia';
$route['alternativa-feegow']               = 'home/seo_alternativa_feegow';
$route['alternativa-odontoclinic']         = 'home/seo_alternativa_odontoclinic';
$route['sistema-gratuito-para-clinicas']   = 'home/seo_sistema_gratuito';
$route['software-para-clinicas']           = 'home/seo_software_para_clinicas';
$route['sistema-para-clinica-oftalmologica'] = 'home/seo_clinica_oftalmologica';
$route['software-para-medicos']            = 'home/seo_software_para_medicos';
$route['sistema-para-nutricionistas']      = 'home/seo_sistema_nutricionistas';
$route['sistema-para-ginecologia']         = 'home/seo_sistema_ginecologia';
$route['sistema-para-pediatria']           = 'home/seo_sistema_pediatria';
$route['sistema-para-psiquiatria']         = 'home/seo_sistema_psiquiatria';
$route['sistema-para-fonoaudiologia']      = 'home/seo_sistema_fonoaudiologia';
$route['sistema-para-medicina-do-trabalho'] = 'home/seo_sistema_medicina_trabalho';

// Blog público
$route['blog']        = 'blog/index';
$route['blog/(:any)'] = 'blog/post/$1';

// Blog admin
$route['adm/blog']                    = 'adm/blogAdmin/index';
$route['adm/blog/novo']               = 'adm/blogAdmin/novo';
$route['adm/blog/salvar']             = 'adm/blogAdmin/salvar';
$route['adm/blog/editar/(:num)']      = 'adm/blogAdmin/editar/$1';
$route['adm/blog/atualizar/(:num)']   = 'adm/blogAdmin/atualizar/$1';
$route['adm/blog/publicar/(:num)']    = 'adm/blogAdmin/publicar/$1';
$route['adm/blog/excluir/(:num)']     = 'adm/blogAdmin/excluir/$1';

// Páginas institucionais
$route['sobre']                            = 'home/sobre';
$route['contato']                          = 'home/contato';
$route['politica-de-privacidade']          = 'home/politica_privacidade';
