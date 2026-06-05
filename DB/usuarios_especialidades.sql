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
-- Estrutura para tabela `usuarios_especialidades`
--

CREATE TABLE `usuarios_especialidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `dt_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_especialidades`
--

INSERT INTO `usuarios_especialidades` (`id`, `nome`, `status`, `ordem`, `dt_cadastro`) VALUES
(1, 'Acupuntura', 1, 10, '2026-05-28 20:40:35'),
(2, 'Alergologia e Imunologia', 1, 20, '2026-05-28 20:40:35'),
(3, 'Cardiologia', 1, 30, '2026-05-28 20:40:35'),
(4, 'Cirurgia Cardiovascular', 1, 40, '2026-05-28 20:40:35'),
(5, 'Cirurgia Geral', 1, 50, '2026-05-28 20:40:35'),
(6, 'Cirurgia Plástica', 1, 60, '2026-05-28 20:40:35'),
(7, 'Clínica Médica', 1, 70, '2026-05-28 20:40:35'),
(8, 'Dermatologia', 1, 80, '2026-05-28 20:40:35'),
(9, 'Endocrinologia e Metabologia', 1, 90, '2026-05-28 20:40:35'),
(10, 'Fisioterapia', 1, 100, '2026-05-28 20:40:35'),
(11, 'Fonoaudiologia', 1, 110, '2026-05-28 20:40:35'),
(12, 'Gastroenterologia', 1, 120, '2026-05-28 20:40:35'),
(13, 'Geriatria', 1, 130, '2026-05-28 20:40:35'),
(14, 'Ginecologia e Obstetrícia', 1, 140, '2026-05-28 20:40:35'),
(15, 'Hematologia', 1, 150, '2026-05-28 20:40:35'),
(16, 'Homeopatia', 1, 160, '2026-05-28 20:40:35'),
(17, 'Infectologia', 1, 170, '2026-05-28 20:40:35'),
(18, 'Medicina de Família e Comunidade', 1, 180, '2026-05-28 20:40:35'),
(19, 'Medicina do Esporte', 1, 190, '2026-05-28 20:40:35'),
(20, 'Medicina do Trabalho', 1, 200, '2026-05-28 20:40:35'),
(21, 'Medicina Estética', 1, 210, '2026-05-28 20:40:35'),
(22, 'Medicina Intensiva', 1, 220, '2026-05-28 20:40:35'),
(23, 'Medicina Legal', 1, 230, '2026-05-28 20:40:35'),
(24, 'Nefrologia', 1, 240, '2026-05-28 20:40:35'),
(25, 'Neurologia', 1, 250, '2026-05-28 20:40:35'),
(26, 'Neurocirurgia', 1, 260, '2026-05-28 20:40:35'),
(27, 'Nutrição', 1, 270, '2026-05-28 20:40:35'),
(28, 'Odontologia', 1, 280, '2026-05-28 20:40:35'),
(29, 'Oftalmologia', 1, 290, '2026-05-28 20:40:35'),
(30, 'Oncologia', 1, 300, '2026-05-28 20:40:35'),
(31, 'Ortopedia e Traumatologia', 1, 310, '2026-05-28 20:40:35'),
(32, 'Otorrinolaringologia', 1, 320, '2026-05-28 20:40:35'),
(33, 'Pediatria', 1, 330, '2026-05-28 20:40:35'),
(34, 'Pneumologia', 1, 340, '2026-05-28 20:40:35'),
(35, 'Proctologia', 1, 350, '2026-05-28 20:40:35'),
(36, 'Psicologia', 1, 360, '2026-05-28 20:40:35'),
(37, 'Psiquiatria', 1, 370, '2026-05-28 20:40:35'),
(38, 'Radiologia e Diagnóstico por Imagem', 1, 380, '2026-05-28 20:40:35'),
(39, 'Reumatologia', 1, 390, '2026-05-28 20:40:35'),
(40, 'Terapia Ocupacional', 1, 400, '2026-05-28 20:40:35'),
(41, 'Urologia', 1, 410, '2026-05-28 20:40:35'),
(42, 'Vascular e Angiologia', 1, 420, '2026-05-28 20:40:35');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `usuarios_especialidades`
--
ALTER TABLE `usuarios_especialidades`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios_especialidades`
--
ALTER TABLE `usuarios_especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
