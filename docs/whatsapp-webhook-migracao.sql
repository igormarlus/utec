-- Executar uma unica vez antes de publicar o webhook.
-- Permite registrar os estados assincronos recebidos da Meta.
ALTER TABLE whatsapp_notificacoes
    MODIFY COLUMN status_envio VARCHAR(20) NOT NULL DEFAULT 'pendente',
    ADD COLUMN status_atualizado_em DATETIME NULL AFTER status_envio;
