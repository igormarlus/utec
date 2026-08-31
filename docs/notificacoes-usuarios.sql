CREATE TABLE IF NOT EXISTS `notificacoes_usuarios` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `id_usuario_destino` INT UNSIGNED NOT NULL,
    `id_agendamento` INT UNSIGNED NOT NULL DEFAULT 0,
    `id_whatsapp_notificacao` INT UNSIGNED NOT NULL DEFAULT 0,
    `tipo` VARCHAR(60) NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `mensagem` VARCHAR(500) NOT NULL,
    `url` VARCHAR(255) NOT NULL DEFAULT '',
    `lida` TINYINT(1) NOT NULL DEFAULT 0,
    `criado_em` DATETIME NOT NULL,
    `lida_em` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_notificacao_usuario_lida` (`id_usuario_destino`, `lida`, `criado_em`),
    KEY `idx_notificacao_agendamento` (`id_agendamento`),
    KEY `idx_notificacao_tenant` (`tenant_id`),
    UNIQUE KEY `uq_notificacao_evento_usuario` (
        `id_usuario_destino`,
        `id_whatsapp_notificacao`,
        `tipo`
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
