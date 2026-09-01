-- Execute este arquivo uma unica vez antes de publicar o chatbot.
-- As tabelas usam InnoDB e utf8mb4; a chave do telefone garante uma sessao ativa por numero.

CREATE TABLE IF NOT EXISTS `whatsapp_chatbot_sessoes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `telefone` VARCHAR(25) NOT NULL,
    `perfil` VARCHAR(20) NOT NULL DEFAULT '',
    `id_usuario` INT UNSIGNED NOT NULL DEFAULT 0,
    `tenant_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `fluxo` VARCHAR(60) NOT NULL DEFAULT '',
    `etapa` VARCHAR(60) NOT NULL DEFAULT '',
    `dados_json` TEXT NULL,
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
    `criado_em` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_whatsapp_chatbot_evento_message` (`message_id`),
    KEY `idx_whatsapp_chatbot_evento_telefone_data` (`telefone`, `criado_em`),
    KEY `idx_whatsapp_chatbot_evento_agendamento` (`id_agendamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Execute a proxima instrucao e confirme que ela nao retorna linha antes de alterar a tabela.
SHOW COLUMNS FROM `notificacoes_usuarios` LIKE 'id_whatsapp_chatbot_evento';

-- Somente se o SHOW COLUMNS anterior nao retornar linha, execute exatamente esta alteracao.
-- NULL preserva notificacoes existentes e permite a chave unica sem conflitar valores ausentes.
ALTER TABLE `notificacoes_usuarios`
    ADD COLUMN `id_whatsapp_chatbot_evento` INT UNSIGNED NULL DEFAULT NULL;

-- Confirme a coluna e execute este SHOW INDEX. So prossiga se ele nao retornar a chave indicada.
SHOW INDEX FROM `notificacoes_usuarios` WHERE Key_name = 'idx_notificacao_chatbot_evento';

-- Somente se o SHOW INDEX anterior nao retornar linha, execute exatamente esta alteracao.
ALTER TABLE `notificacoes_usuarios`
    ADD KEY `idx_notificacao_chatbot_evento` (`id_whatsapp_chatbot_evento`);

-- Antes da chave unica, confirme que nao ha duplicidade para eventos preenchidos.
SELECT `id_usuario_destino`, `id_whatsapp_chatbot_evento`, `tipo`, COUNT(*) AS `total`
FROM `notificacoes_usuarios`
WHERE `id_whatsapp_chatbot_evento` IS NOT NULL
GROUP BY `id_usuario_destino`, `id_whatsapp_chatbot_evento`, `tipo`
HAVING COUNT(*) > 1;

-- Somente se a consulta anterior nao retornar linha, confirme a ausencia da chave e entao crie-a.
SHOW INDEX FROM `notificacoes_usuarios` WHERE Key_name = 'uq_notificacao_chatbot_evento_usuario';

-- Somente se o SHOW INDEX anterior nao retornar linha, execute exatamente esta alteracao.
ALTER TABLE `notificacoes_usuarios`
    ADD UNIQUE KEY `uq_notificacao_chatbot_evento_usuario` (`id_usuario_destino`, `id_whatsapp_chatbot_evento`, `tipo`);
