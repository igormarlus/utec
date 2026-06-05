-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 05/06/2026 às 14:34
-- Versão do servidor: 10.11.16-MariaDB-cll-lve
-- Versão do PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `utecnologiacom_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `especialidades_campos_config`
--

CREATE TABLE `especialidades_campos_config` (
  `id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `campo_chave` varchar(80) NOT NULL,
  `campo_label` varchar(150) NOT NULL,
  `campo_tipo` varchar(30) NOT NULL DEFAULT 'textarea',
  `campo_opcoes` text DEFAULT NULL,
  `campo_placeholder` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `obrigatorio` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `dt_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `especialidades_campos_config`
--

INSERT INTO `especialidades_campos_config` (`id`, `especialidade_id`, `campo_chave`, `campo_label`, `campo_tipo`, `campo_opcoes`, `campo_placeholder`, `ordem`, `obrigatorio`, `status`, `dt_cadastro`) VALUES
(1, 10, 'eva_dor', 'EVA — Escala de Dor (0–10)', 'number', NULL, '0 = sem dor, 10 = dor máxima', 10, 0, 1, '2026-05-29 00:05:45'),
(2, 10, 'regiao_corporal', 'Região Corporal Tratada', 'select', '[\"Coluna Cervical\",\"Coluna Lombar\",\"Coluna Torácica\",\"Ombro Direito\",\"Ombro Esquerdo\",\"Cotovelo D.\",\"Cotovelo E.\",\"Punho/Mão D.\",\"Punho/Mão E.\",\"Quadril D.\",\"Quadril E.\",\"Joelho D.\",\"Joelho E.\",\"Tornozelo/Pé D.\",\"Tornozelo/Pé E.\",\"Bilateral\",\"MMII\",\"MMSS\",\"Outro\"]', 'Selecione a região', 20, 0, 1, '2026-05-29 00:05:45'),
(3, 10, 'fase_tratamento', 'Fase do Tratamento', 'select', '[\"Aguda\",\"Subaguda\",\"Crônica\",\"Manutenção\",\"Alta\"]', NULL, 30, 0, 1, '2026-05-29 00:05:45'),
(4, 28, 'dentes_tratados', 'Dente(s) Tratado(s) — Numeração FDI', 'text', NULL, 'Ex: 36, 47, 11', 10, 0, 1, '2026-05-29 00:05:45'),
(5, 28, 'procedimento_odonto', 'Procedimento Realizado', 'select', '[\"Restauração\",\"Extração\",\"Endodontia (Canal)\",\"Profilaxia/Limpeza\",\"Prótese Parcial\",\"Prótese Total\",\"Ortodontia\",\"Implante\",\"Clareamento\",\"Cirurgia Oral\",\"Gengivoplastia\",\"Outro\"]', NULL, 20, 0, 1, '2026-05-29 00:05:45'),
(6, 28, 'anestesia_odonto', 'Anestesia Utilizada', 'select', '[\"Não utilizada\",\"Articaína 4%\",\"Lidocaína 2%\",\"Mepivacaína\",\"Prilocaína\",\"Outro\"]', NULL, 30, 0, 1, '2026-05-29 00:05:45'),
(7, 28, 'material_odonto', 'Material Utilizado', 'text', NULL, 'Ex: Compósito A2, Cimento de Ionômero de Vidro...', 40, 0, 1, '2026-05-29 00:05:45'),
(8, 36, 'num_sessao', 'Nº da Sessão', 'number', NULL, 'Número sequencial da sessão com este paciente', 10, 0, 1, '2026-05-29 00:05:45'),
(9, 36, 'modalidade_psico', 'Modalidade', 'select', '[\"Individual\",\"Casal\",\"Família\",\"Grupo\",\"Online — Individual\",\"Online — Casal\",\"Online — Grupo\"]', NULL, 20, 0, 1, '2026-05-29 00:05:45'),
(10, 36, 'cid_referencia', 'CID de Referência (opcional)', 'text', NULL, 'Ex: F41.1, F32.0', 30, 0, 1, '2026-05-29 00:05:45'),
(11, 37, 'cid_psiq', 'CID-10 / Hipótese Diagnóstica', 'text', NULL, 'Ex: F32.1, F20.0, F41.0', 10, 0, 1, '2026-05-29 00:05:45'),
(12, 37, 'mse_exame', 'Exame do Estado Mental (MSE)', 'textarea', NULL, 'Humor, afeto, pensamento, sensopercepção, orientação, memória...', 20, 0, 1, '2026-05-29 00:05:45'),
(13, 37, 'medicacao_ajuste', 'Medicação / Ajuste Terapêutico', 'textarea', NULL, 'Nome, dose, via, frequência. Ex: Escitalopram 20mg 1x/dia', 30, 0, 1, '2026-05-29 00:05:45'),
(14, 27, 'peso_kg_nutri', 'Peso (kg)', 'number', NULL, 'Ex: 72.5', 10, 0, 1, '2026-05-29 00:05:45'),
(15, 27, 'altura_cm_nutri', 'Altura (cm)', 'number', NULL, 'Ex: 170', 20, 0, 1, '2026-05-29 00:05:45'),
(16, 27, 'objetivo_nutri', 'Objetivo', 'select', '[\"Emagrecimento\",\"Ganho de massa\",\"Manutenção\",\"Saúde geral\",\"Tratamento patológico\",\"Gestação/Lactação\",\"Outro\"]', NULL, 30, 0, 1, '2026-05-29 00:05:45'),
(17, 27, 'imc_calc', 'IMC', 'text', NULL, 'Calculado ou informado manualmente', 40, 0, 1, '2026-05-29 00:05:45'),
(18, 33, 'peso_ped', 'Peso (kg)', 'number', NULL, 'Ex: 12.3', 10, 0, 1, '2026-05-29 00:05:45'),
(19, 33, 'altura_ped', 'Altura/Comprimento (cm)', 'number', NULL, 'Ex: 95', 20, 0, 1, '2026-05-29 00:05:45'),
(20, 33, 'responsavel_ped', 'Nome do Responsável', 'text', NULL, 'Nome do pai, mãe ou responsável legal', 30, 0, 1, '2026-05-29 00:05:45'),
(21, 33, 'parentesco_ped', 'Grau de Parentesco', 'select', '[\"Mãe\",\"Pai\",\"Avó/Avô\",\"Tio/Tia\",\"Irmão/Irmã\",\"Guardião\",\"Outro\"]', NULL, 40, 0, 1, '2026-05-29 00:05:45'),
(22, 3, 'pa_sistolica', 'PA Sistólica (mmHg)', 'number', NULL, 'Ex: 120', 10, 0, 1, '2026-05-29 00:05:45'),
(23, 3, 'pa_diastolica', 'PA Diastólica (mmHg)', 'number', NULL, 'Ex: 80', 20, 0, 1, '2026-05-29 00:05:45'),
(24, 3, 'fc_bpm', 'FC (bpm)', 'number', NULL, 'Ex: 72', 30, 0, 1, '2026-05-29 00:05:45'),
(25, 3, 'peso_card', 'Peso (kg)', 'number', NULL, 'Ex: 75.0', 40, 0, 1, '2026-05-29 00:05:45'),
(26, 14, 'dum_gine', 'DUM (Última Menstruação)', 'text', NULL, 'DD/MM/AAAA', 10, 0, 1, '2026-05-29 00:05:45'),
(27, 14, 'ig_semanas', 'IG (semanas)', 'number', NULL, 'Se gestante, informe a idade gestacional', 20, 0, 1, '2026-05-29 00:05:45'),
(28, 14, 'tipo_consulta_gine', 'Tipo de Consulta', 'select', '[\"Ginecológica\",\"Pré-natal\",\"Retorno\",\"Urgência\",\"Procedimento\"]', NULL, 30, 0, 1, '2026-05-29 00:05:45'),
(29, 11, 'area_fono', 'Área de Atuação', 'select', '[\"Linguagem\",\"Motricidade Orofacial\",\"Deglutição\",\"Voz\",\"Audição\",\"Outro\"]', NULL, 10, 0, 1, '2026-05-29 00:05:45'),
(30, 11, 'num_sessao_fono', 'Nº da Sessão', 'number', NULL, 'Número sequencial da sessão', 20, 0, 1, '2026-05-29 00:05:45');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `especialidades_campos_config`
--
ALTER TABLE `especialidades_campos_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_esp_campo` (`especialidade_id`,`campo_chave`),
  ADD KEY `idx_esp_campos_esp` (`especialidade_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `especialidades_campos_config`
--
ALTER TABLE `especialidades_campos_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
