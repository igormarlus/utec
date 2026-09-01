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

echo "OK\n";
