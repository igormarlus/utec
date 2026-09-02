-- Reexecutavel: cria as tabelas, completa esquemas legados e remove o procedimento temporario ao final.
-- Todas as tabelas usam InnoDB e utf8mb4; a chave do telefone garante uma sessao por numero.

CREATE TABLE IF NOT EXISTS `whatsapp_chatbot_sessoes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `telefone` VARCHAR(25) NOT NULL,
    `perfil` VARCHAR(20) NOT NULL DEFAULT '',
    `id_usuario` INT UNSIGNED NOT NULL DEFAULT 0,
    `tenant_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `fluxo` VARCHAR(60) NOT NULL DEFAULT '',
    `etapa` VARCHAR(60) NOT NULL DEFAULT '',
    `dados_json` TEXT NULL,
    `origem_em` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
    `origem_evento` VARCHAR(255) NOT NULL DEFAULT '',
    `atividade_em` DATETIME NOT NULL,
    `expira_em` DATETIME NOT NULL,
    `criado_em` DATETIME NOT NULL,
    `atualizado_em` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_whatsapp_chatbot_sessao_telefone` (`telefone`),
    KEY `idx_whatsapp_chatbot_sessao_expira` (`expira_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `whatsapp_chatbot_eventos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `message_id` VARCHAR(255) NOT NULL,
    `telefone` VARCHAR(25) NOT NULL,
    `id_whatsapp_chatbot_sessao` INT UNSIGNED NOT NULL DEFAULT 0,
    `id_usuario` INT UNSIGNED NOT NULL DEFAULT 0,
    `id_agendamento` INT UNSIGNED NOT NULL DEFAULT 0,
    `tipo` VARCHAR(60) NOT NULL DEFAULT 'mensagem',
    `entrada` TEXT NULL,
    `resultado` TEXT NULL,
    `processamento_status` VARCHAR(20) NOT NULL DEFAULT 'pendente',
    `processamento_token` VARCHAR(64) NOT NULL DEFAULT '',
    `processando_em` DATETIME NULL DEFAULT NULL,
    `finalizado_em` DATETIME NULL DEFAULT NULL,
    `criado_em` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_whatsapp_chatbot_evento_message` (`message_id`),
    KEY `idx_whatsapp_chatbot_evento_telefone_data` (`telefone`, `criado_em`),
    KEY `idx_whatsapp_chatbot_evento_agendamento` (`id_agendamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
DROP PROCEDURE IF EXISTS `instalar_whatsapp_chatbot_sessoes`$$
CREATE PROCEDURE `instalar_whatsapp_chatbot_sessoes`()
BEGIN
    DECLARE chatbot_existe INT DEFAULT 0;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_sessoes' AND COLUMN_NAME = 'origem_em';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_sessoes` ADD COLUMN `origem_em` DATETIME NOT NULL DEFAULT ''1970-01-01 00:00:00'' AFTER `dados_json`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_sessoes' AND COLUMN_NAME = 'origem_evento';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_sessoes` ADD COLUMN `origem_evento` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `origem_em`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_sessoes' AND INDEX_NAME = 'idx_whatsapp_chatbot_sessao_expira';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_sessoes` ADD KEY `idx_whatsapp_chatbot_sessao_expira` (`expira_em`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND COLUMN_NAME = 'processamento_status';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD COLUMN `processamento_status` VARCHAR(20) NOT NULL DEFAULT ''pendente'' AFTER `resultado`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND COLUMN_NAME = 'processamento_token';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD COLUMN `processamento_token` VARCHAR(64) NOT NULL DEFAULT '''' AFTER `processamento_status`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND COLUMN_NAME = 'processando_em';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD COLUMN `processando_em` DATETIME NULL DEFAULT NULL AFTER `processamento_token`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND COLUMN_NAME = 'finalizado_em';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD COLUMN `finalizado_em` DATETIME NULL DEFAULT NULL AFTER `processando_em`';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND INDEX_NAME = 'uq_whatsapp_chatbot_evento_message';
    IF chatbot_existe = 0 THEN
        DELETE evento_novo FROM `whatsapp_chatbot_eventos` evento_novo
        INNER JOIN `whatsapp_chatbot_eventos` evento_antigo
            ON evento_antigo.`message_id` = evento_novo.`message_id`
            AND evento_antigo.`id` < evento_novo.`id`;

        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD UNIQUE KEY `uq_whatsapp_chatbot_evento_message` (`message_id`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND INDEX_NAME = 'idx_whatsapp_chatbot_evento_telefone_data';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD KEY `idx_whatsapp_chatbot_evento_telefone_data` (`telefone`, `criado_em`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_chatbot_eventos' AND INDEX_NAME = 'idx_whatsapp_chatbot_evento_agendamento';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `whatsapp_chatbot_eventos` ADD KEY `idx_whatsapp_chatbot_evento_agendamento` (`id_agendamento`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notificacoes_usuarios' AND COLUMN_NAME = 'id_whatsapp_chatbot_evento';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `notificacoes_usuarios` ADD COLUMN `id_whatsapp_chatbot_evento` INT UNSIGNED NULL DEFAULT NULL';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notificacoes_usuarios' AND INDEX_NAME = 'idx_notificacao_chatbot_evento';
    IF chatbot_existe = 0 THEN
        SET @chatbot_sql = 'ALTER TABLE `notificacoes_usuarios` ADD KEY `idx_notificacao_chatbot_evento` (`id_whatsapp_chatbot_evento`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;

    SELECT COUNT(*) INTO chatbot_existe FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notificacoes_usuarios' AND INDEX_NAME = 'uq_notificacao_chatbot_evento_usuario';
    IF chatbot_existe = 0 THEN
        DELETE notificacao_nova FROM `notificacoes_usuarios` notificacao_nova
        INNER JOIN `notificacoes_usuarios` notificacao_antiga
            ON notificacao_antiga.`id_usuario_destino` = notificacao_nova.`id_usuario_destino`
            AND notificacao_antiga.`id_whatsapp_chatbot_evento` = notificacao_nova.`id_whatsapp_chatbot_evento`
            AND notificacao_antiga.`tipo` = notificacao_nova.`tipo`
            AND notificacao_antiga.`id` < notificacao_nova.`id`
        WHERE notificacao_nova.`id_whatsapp_chatbot_evento` IS NOT NULL;
        SET @chatbot_sql = 'ALTER TABLE `notificacoes_usuarios` ADD UNIQUE KEY `uq_notificacao_chatbot_evento_usuario` (`id_usuario_destino`, `id_whatsapp_chatbot_evento`, `tipo`)';
        PREPARE chatbot_stmt FROM @chatbot_sql; EXECUTE chatbot_stmt; DEALLOCATE PREPARE chatbot_stmt;
    END IF;
END$$
CALL `instalar_whatsapp_chatbot_sessoes`()$$
DROP PROCEDURE `instalar_whatsapp_chatbot_sessoes`$$
DELIMITER ;
