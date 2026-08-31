<?php
define('BASEPATH', __DIR__);
define('ENVIRONMENT', 'development');

require __DIR__ . '/../application/helpers/whatsapp_agendamento_helper.php';
require __DIR__ . '/../application/config/database.php';

$args = $argv;
array_shift($args);
$send = in_array('--send', $args, true);
$agendamentoId = 0;

foreach ($args as $arg) {
    if (strpos($arg, '--agendamento=') === 0) {
        $agendamentoId = (int)substr($arg, strlen('--agendamento='));
    }
}

$cfg = $db['default'];
$mysqli = @new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "Falha ao conectar no banco: {$mysqli->connect_error}\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$configSql = "SELECT * FROM whatsapp_config WHERE status = 1 ORDER BY id DESC LIMIT 1";
$configRes = $mysqli->query($configSql);
if (!$configRes || !$configRes->num_rows) {
    fwrite(STDERR, "Nenhuma configuracao ativa encontrada em whatsapp_config.\n");
    exit(1);
}
$config = $configRes->fetch_assoc();

$whereAgendamento = $agendamentoId > 0
    ? "a.id = {$agendamentoId}"
    : "TRIM(COALESCE(p.telefone, '')) <> ''";

$agendamentoSql = "
    SELECT a.id, a.data_agenda, a.hora_agenda, a.tipo,
           p.nome AS paciente_nome, p.telefone AS paciente_telefone,
           pr.nome AS prestador_nome
    FROM agendamentos a
    LEFT JOIN usuarios p ON p.id = a.id_paciente
    LEFT JOIN usuarios pr ON pr.id = a.id_prestador
    WHERE {$whereAgendamento}
    ORDER BY a.id DESC
    LIMIT 1
";
$agendamentoRes = $mysqli->query($agendamentoSql);
if (!$agendamentoRes || !$agendamentoRes->num_rows) {
    fwrite(STDERR, "Nenhum agendamento elegivel encontrado para teste.\n");
    exit(1);
}
$agendamento = $agendamentoRes->fetch_assoc();

$telefone = utec_whatsapp_normalizar_numero($agendamento['paciente_telefone']);
if (strlen($telefone) >= 10 && strlen($telefone) <= 11) {
    $telefone = '55'.$telefone;
}

$payload = [
    'messaging_product' => 'whatsapp',
    'to' => $telefone,
    'type' => 'template',
    'template' => [
        'name' => trim((string)$config['template_name']),
        'language' => [
            'code' => trim((string)$config['template_lang']),
        ],
        'components' => [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => trim((string)$agendamento['paciente_nome'])],
                    ['type' => 'text', 'text' => trim((string)$agendamento['tipo'])],
                    ['type' => 'text', 'text' => utec_whatsapp_formatar_data_br($agendamento['data_agenda'])],
                    ['type' => 'text', 'text' => utec_whatsapp_formatar_hora_br($agendamento['hora_agenda'])],
                    ['type' => 'text', 'text' => trim((string)$agendamento['prestador_nome'])],
                ],
            ],
        ],
    ],
];

echo "Configuracao ativa:\n";
echo json_encode([
    'id' => (int)$config['id'],
    'template_name' => $config['template_name'],
    'template_lang' => $config['template_lang'],
    'phone_number_id' => $config['phone_number_id'],
    'numero_remetente' => $config['numero_remetente'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Agendamento escolhido:\n";
echo json_encode([
    'id' => (int)$agendamento['id'],
    'paciente_nome' => $agendamento['paciente_nome'],
    'paciente_telefone' => $agendamento['paciente_telefone'],
    'tipo' => $agendamento['tipo'],
    'data_agenda' => $agendamento['data_agenda'],
    'hora_agenda' => $agendamento['hora_agenda'],
    'prestador_nome' => $agendamento['prestador_nome'],
    'telefone_normalizado' => $telefone,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if (!$send) {
    echo "Modo preview. Use --send para tentar o disparo real.\n";
    exit(0);
}

if (!function_exists('curl_init')) {
    fwrite(STDERR, "cURL indisponivel no PHP CLI.\n");
    exit(1);
}

$url = 'https://graph.facebook.com/v20.0/'.trim((string)$config['phone_number_id']).'/messages';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.trim((string)$config['access_token']),
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);

$raw = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: {$httpCode}\n";
if ($curlError) {
    echo "CURL_ERROR: {$curlError}\n";
}
echo "Resposta:\n";
echo ($raw !== false ? $raw : '') . "\n";
