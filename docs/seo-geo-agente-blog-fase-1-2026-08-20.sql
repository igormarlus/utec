-- ============================================================
-- UTecnologia Saúde — Artigos comerciais fase 1
-- Rodada: 2026-08-20
-- Importar via phpMyAdmin: banco utecnologiacom_db
-- EXECUÇÃO ÚNICA: confirme os slugs abaixo antes de importar para evitar duplicidade.
-- Conferir categorias: SELECT id, nome FROM blog_categorias ORDER BY id
-- Conferir slugs: SELECT id, slug, publicado FROM blog_posts
--                 WHERE slug IN ('quanto-custa-um-software-para-clinica',
--                 'como-migrar-da-planilha-para-sistema-clinico',
--                 'software-gratuito-para-clinicas-trial-vs-gratuito')
-- Ajustar `id_categoria` com o resultado de `blog_categorias` antes de importar.
-- ============================================================

INSERT INTO `blog_posts`
  (`id_categoria`, `titulo`, `slug`, `resumo`, `conteudo`, `meta_titulo`, `meta_descricao`, `autor`, `tempo_leitura`, `publicado`, `criado_em`, `publicado_em`)
VALUES
(1,
 'Quanto custa um software para clínica? O que avaliar além do preço',
 'quanto-custa-um-software-para-clinica',
 'Entenda o que realmente pesa no custo de um software para clínica e por que olhar só a mensalidade pode levar a uma escolha ruim.',
 '<p>O preço mensal é apenas o ponto mais visível da decisão. Para saber quanto um software para clínica realmente custa, é preciso considerar o tempo de implantação, a adaptação da equipe, os recursos incluídos e o risco de precisar trocar de sistema depois.</p><h2>1. Mensalidade não é custo total</h2><p>Compare implantação, curva de adoção e risco de troca futura.</p><h2>2. Plano barato pode sair caro</h2><p>Se o sistema não acompanha o crescimento da clínica, a troca vem cedo.</p><h2>3. Trial reduz risco</h2><p>Um trial real permite validar agenda, prontuário e adoção antes da assinatura.</p><p>O <a href="https://utecnologia.com.br/software-para-clinicas">software para clínicas</a> da UTecnologia Saúde oferece 30 dias de teste para essa validação prática.</p>',
 'Quanto custa um software para clínica?',
 'Veja o que avaliar no custo de um software para clínica: mensalidade, implantação, adoção da equipe e risco de troca futura.',
 'UTecnologia Saúde', 5, 0, '2026-08-20 10:00:00', NULL),
(1,
 'Como migrar da planilha para um sistema clínico sem travar a rotina',
 'como-migrar-da-planilha-para-sistema-clinico',
 'Um passo a passo simples para clínicas e consultórios saírem da planilha com menos atrito e mais segurança operacional.',
 '<p>Migrar da planilha não precisa ser um projeto gigante. Com uma sequência simples, a clínica consegue organizar os dados essenciais, testar o novo fluxo e reduzir o impacto da mudança sobre a recepção e os profissionais.</p><h2>1. Comece pela agenda atual</h2><p>O primeiro ganho é operacional: parar de perder informação do dia.</p><h2>2. Cadastre primeiro os pacientes ativos</h2><p>Nem todo histórico precisa entrar no dia um.</p><h2>3. Teste com trial antes da mudança maior</h2><p>Validar a rotina reduz resistência da equipe.</p><p>Se quiser comparar esse cenário na prática, veja o <a href="https://utecnologia.com.br/sistema-para-clinicas">sistema para clínicas</a> e o trial gratuito da UTecnologia Saúde.</p>',
 'Como migrar da planilha para um sistema clínico',
 'Passo a passo para sair da planilha e começar a usar um sistema clínico sem paralisar a recepção nem perder o controle da rotina.',
 'UTecnologia Saúde', 5, 0, '2026-08-20 10:05:00', NULL),
(1,
 'Software gratuito para clínicas: o que existe, o que é trial e o que vale a pena',
 'software-gratuito-para-clinicas-trial-vs-gratuito',
 'Nem todo “gratuito” ajuda a clínica de verdade. Entenda a diferença entre software grátis, plano limitado e trial completo.',
 '<p>Muita busca por software gratuito nasce da tentativa de reduzir risco e encontrar uma solução que caiba no orçamento. Antes de escolher apenas pelo rótulo “grátis”, vale entender quais recursos estão disponíveis e se o modelo permite testar a operação real da clínica.</p><h2>1. Gratuito nem sempre significa utilizável</h2><p>Muitos planos grátis travam exatamente nos recursos que a clínica precisa.</p><h2>2. Trial completo pode ser mais útil que plano free</h2><p>Você testa a operação real antes de decidir.</p><h2>3. O que comparar</h2><p>Agenda, prontuário, equipe, suporte e limite de uso.</p><p>O <a href="https://utecnologia.com.br/sistema-gratuito-para-clinicas">trial gratuito para clínicas</a> da UTecnologia Saúde segue essa lógica de validação prática.</p>',
 'Software gratuito para clínicas: trial vs gratuito',
 'Entenda a diferença entre software grátis, plano limitado e trial completo para clínicas antes de decidir apenas pelo menor custo.',
 'UTecnologia Saúde', 5, 0, '2026-08-20 10:10:00', NULL);
