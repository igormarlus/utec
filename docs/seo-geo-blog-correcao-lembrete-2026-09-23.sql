-- Correção de precisão — artigo "mensagem-de-lembrete-de-consulta-quando-enviar"
-- O lembrete real é único, poucas horas antes da consulta (não há janelas configuráveis).
UPDATE `blog_posts`
SET `conteudo` = REPLACE(`conteudo`, 'dispara o lembrete nas janelas que você definir', 'dispara um lembrete único, poucas horas antes da consulta')
WHERE `slug` = 'mensagem-de-lembrete-de-consulta-quando-enviar';
