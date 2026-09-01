<?php

function assertSource($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$dev = file_get_contents(__DIR__ . '/../application/controllers/adm/Dev.php');
$model = file_get_contents(__DIR__ . '/../application/models/Whatsapp_model.php');

// --- Task 2: migracao + log ---
assertSource(strpos($dev, 'function migrar_lembrete_whatsapp(') !== false, 'Dev.php deve expor migrar_lembrete_whatsapp().');
assertSource(strpos($dev, "userdata('nivel') != 1") !== false, 'A migracao deve ser protegida por nivel 1.');
assertSource(strpos($dev, 'tipo_notificacao') !== false, 'A migracao deve adicionar a coluna tipo_notificacao.');
assertSource(strpos($dev, 'idx_wn_agendamento_tipo') !== false, 'A migracao deve criar o indice idx_wn_agendamento_tipo.');
assertSource(strpos($model, "'tipo_notificacao' =>") !== false, 'registrar_log deve gravar tipo_notificacao.');
assertSource(strpos($model, "log_message('warning'") === false, 'O model nao deve usar log_message(warning).');

// --- Task 3: query de elegibilidade ---
assertSource(strpos($model, 'function get_agendamentos_para_lembrete(') !== false, 'O model deve expor get_agendamentos_para_lembrete().');
assertSource(strpos($model, 'utec_whatsapp_lembrete_tipo_valido(') !== false, 'A query deve validar o tipo de lembrete.');
assertSource(strpos($model, 'NOT EXISTS') !== false, 'A query deve usar NOT EXISTS para a idempotencia.');
assertSource(strpos($model, "TIMESTAMP(a.data_agenda, a.hora_agenda)") !== false, 'A query deve comparar a data e hora do agendamento como TIMESTAMP.');
assertSource(strpos($model, "a.status = 0") !== false, 'A query deve exigir agendamento pendente (status 0).');
assertSource(strpos($model, "'lembrete_profissional'") !== false, 'A query deve tratar o tipo lembrete_profissional.');
assertSource(strpos($model, "a.id_prestador > 0") !== false, 'O lembrete do profissional exige prestador vinculado.');
assertSource(strpos($model, "status_confirmacao = 'confirmado'") !== false, 'A query paciente deve excluir agendamentos ja confirmados.');
assertSource(strpos($model, "wr.tipo_notificacao = 'confirmacao'") !== false, 'A query paciente deve pular quem recebeu confirmacao ha pouco.');

// --- Task 4: notificar_lembrete na biblioteca ---
$lib = file_get_contents(__DIR__ . '/../application/libraries/Whatsapp_agendamento.php');
assertSource(strpos($lib, 'function notificar_lembrete(') !== false, 'A biblioteca deve expor notificar_lembrete().');
assertSource(strpos($lib, 'utec_whatsapp_lembrete_tipo_valido(') !== false, 'notificar_lembrete deve validar o tipo recebido.');
assertSource(strpos($lib, 'pr.telefone AS prestador_telefone') !== false, 'O contexto deve trazer o telefone do prestador.');
assertSource(strpos($lib, "'tipo_notificacao' => \$tipo") !== false, 'O log do lembrete deve carimbar o tipo.');
assertSource(strpos($lib, 'validar_quota_tenant(') !== false, 'O lembrete deve respeitar a cota do tenant.');
assertSource(strpos($lib, "log_message('warning'") === false, 'A biblioteca nao deve usar log_message(warning).');

// --- Task 5: config, controller e rota ---
$cron = file_get_contents(__DIR__ . '/../application/controllers/Cron.php');
$routes = file_get_contents(__DIR__ . '/../application/config/routes.php');
$cfg = file_get_contents(__DIR__ . '/../application/config/whatsapp.php');

assertSource(strpos($cfg, "getenv('WHATSAPP_CRON_TOKEN')") !== false, 'A config deve ler o token de uma env var.');
assertSource(strpos($cfg, "\$config['cron_token']") !== false, 'A config deve definir cron_token.');

assertSource(strpos($cron, 'function lembrete_whatsapp(') !== false, 'O Cron deve expor lembrete_whatsapp().');
assertSource(strpos($cron, 'hash_equals(') !== false, 'O Cron deve comparar o token com hash_equals.');
assertSource(strpos($cron, 'set_status_header(403)') !== false, 'Token invalido deve responder 403.');
assertSource(strpos($cron, "\$this->input->get('token')") !== false, 'O token deve vir de input->get.');
assertSource(strpos($cron, "config->load('whatsapp'") !== false, 'O Cron deve carregar a config whatsapp.');
assertSource(strpos($cron, 'utec_whatsapp_config_ativa(') !== false, 'O Cron deve checar a config ativa antes de iterar.');
assertSource(strpos($cron, 'utec_whatsapp_lembrete_intervalo(') !== false, 'O Cron deve calcular a janela pelo helper.');
assertSource(strpos($cron, 'get_agendamentos_para_lembrete(') !== false, 'O Cron deve buscar os elegiveis pelo model.');
assertSource(strpos($cron, 'notificar_lembrete(') !== false, 'O Cron deve disparar via notificar_lembrete.');
assertSource(strpos($cron, "'lembrete_paciente'") !== false && strpos($cron, "'lembrete_profissional'") !== false, 'O Cron deve iterar os dois tipos.');
assertSource(strpos($cron, "log_message('warning'") === false, 'O Cron nao deve usar log_message(warning).');
assertSource(strpos($cron, "lembrete_profissional_ativo") !== false, 'O cron deve gatear a lane do profissional por config.');
assertSource(strpos($cfg, "lembrete_profissional_ativo") !== false, 'A config deve definir lembrete_profissional_ativo.');

assertSource(strpos($routes, "\$route['cron/lembrete-whatsapp']") !== false, 'routes.php deve ter a rota do cron.');

echo "OK\n";
