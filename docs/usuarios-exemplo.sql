-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 28/05/2026 às 17:25
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
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT 0,
  `tenant_id` int(11) DEFAULT NULL,
  `tenant_role` varchar(30) DEFAULT NULL,
  `billing_customer_id` varchar(120) DEFAULT NULL,
  `onboarding_status` varchar(30) NOT NULL DEFAULT 'pendente',
  `id_setor` int(11) DEFAULT NULL,
  `id_unidade` int(11) DEFAULT NULL,
  `id_prestador` int(11) DEFAULT 0,
  `nivel` int(11) DEFAULT 0,
  `nome` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nome_empresa` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `profissao` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nacionalidade` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `cpf` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `rg` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `telefone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `especialidade` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `classe` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `cep` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `endereco` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `complemento` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ponto_referencia` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `bairro` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `cidade` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `uf` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `login` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `saas` int(1) DEFAULT 0,
  `idade` int(11) DEFAULT NULL,
  `dt_nascimento` date NOT NULL DEFAULT '0000-00-00',
  `status` int(11) DEFAULT 0,
  `exame` int(11) DEFAULT 0,
  `consulta` int(11) DEFAULT 0,
  `procedimento` int(11) DEFAULT 0,
  `verificado` int(11) DEFAULT NULL,
  `foto` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `img` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `assinatura` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `redes_sociais` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `site` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `apresentacao` text DEFAULT NULL,
  `banco` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `agencia` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `conta` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `forma_pagamento` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT '',
  `qrcode` varchar(255) DEFAULT NULL,
  `atendente` varchar(50) DEFAULT NULL,
  `responsavel_tecnico` varchar(50) DEFAULT NULL,
  `diretoria_executiva` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `dt_cadastro` timestamp NULL DEFAULT current_timestamp(),
  `senha_token` varchar(64) DEFAULT NULL,
  `senha_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_user`, `tenant_id`, `tenant_role`, `billing_customer_id`, `onboarding_status`, `id_setor`, `id_unidade`, `id_prestador`, `nivel`, `nome`, `nome_empresa`, `estado_civil`, `profissao`, `nacionalidade`, `email`, `cpf`, `rg`, `telefone`, `especialidade`, `classe`, `cep`, `endereco`, `numero`, `complemento`, `ponto_referencia`, `bairro`, `cidade`, `uf`, `login`, `senha`, `saas`, `idade`, `dt_nascimento`, `status`, `exame`, `consulta`, `procedimento`, `verificado`, `foto`, `img`, `assinatura`, `redes_sociais`, `site`, `apresentacao`, `banco`, `agencia`, `conta`, `forma_pagamento`, `qrcode`, `atendente`, `responsavel_tecnico`, `diretoria_executiva`, `latitude`, `longitude`, `dt_cadastro`, `senha_token`, `senha_token_expires`) VALUES
(1, 1, NULL, NULL, NULL, 'pendente', 1, 1, 0, 1, 'Adm', 'Admin', NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', 'adm', '$2y$10$xItfCIbVLQNdqPC74yUhzeqOgTAZxxOk2RKqXV2h71NZEzdVjH356', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, 'avatar3.jpg', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 15:17:41', NULL, NULL),
(443, 1, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 2, 'Clínica 1', NULL, NULL, '', NULL, 'asdasd@asd.com', '065045604560465', '654654564', '81987987987', NULL, NULL, '52041230', 'Rua tal', 654, 'casa', NULL, 'Encruzilhada', 'Recife', 'PE', 'clinica1', '$2y$10$y5axDihgsqAL5J0uZ76t7.maGSSPFAf8fm3HNsJ4QXG0KNKVvAwiW', 0, NULL, '2025-05-01', 0, 0, 0, 0, NULL, NULL, 'plan3.png', NULL, 'asdasd@asd.com', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 16:31:22', NULL, NULL),
(444, 443, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 3, 'Dra Tatiane Lima', NULL, NULL, '', NULL, 'tete@asdasd.com', '', '', '', 'Cirurgia plástica', 'CR 22554', '', '', 0, '', NULL, '', '', '', 'tatiane', '$2y$10$UC42iI57zg5DfoL5Bsog0e04k8trAUrAQ5pph2NBn8Ttmc4hLBQK2', 0, NULL, '1987-08-02', 0, 0, 0, 0, NULL, NULL, 'avatar5.jpg', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 16:50:16', NULL, NULL),
(445, 444, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 4, 'Maria Atendente', NULL, NULL, '', NULL, 'emailASdas@asd.com', '65487986548679', '', '81987987897897', NULL, NULL, '', '', 0, '', NULL, '', '', '', 'atendentetati', '$2y$10$CVzn9GpvnzIdXSmTh3Fd7.R/eVe/nc1Cy9Sat.MK5ToPfi7p59L9.', 0, NULL, '1997-11-02', 0, 0, 0, 0, NULL, NULL, 'avatar7.jpg', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 16:53:47', NULL, NULL),
(446, 445, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 5, 'Paciente 1', NULL, NULL, '', NULL, 'emasilpacian@asd.com', '654654654564', '897987897', '81987987897', NULL, NULL, '52041230', 'Rua Dr. José Higino R. Campos', 11, 'casa', NULL, 'Encruzilhada', 'Recife', 'PE', NULL, '$2y$10$nQkWql.rVmNxgElTuJ/WnuZtuh/8XFaJ0nSQK8PqTGy8PotYdNiRG', 0, NULL, '1975-05-01', 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'asd@asd.com', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 16:55:13', NULL, NULL),
(447, 1, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 5, 'Paciente 2', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', NULL, NULL, 0, NULL, '1999-05-25', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08 20:14:32', NULL, NULL),
(448, 445, NULL, NULL, NULL, 'pendente', NULL, NULL, 0, 5, 'Udo', NULL, NULL, '', NULL, '', '', '', '8197897987897', NULL, NULL, '', '', 0, '', NULL, '', '', '', NULL, NULL, 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-13 19:43:02', NULL, NULL),
(450, 1, 2, 'owner', 'igor_marlus@yahoooo.com.br', 'ativo', NULL, NULL, 0, 2, 'Marlus', NULL, NULL, NULL, NULL, 'igor_marlus@yahoo.com.br', '06030689444', NULL, '81983276882', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'igor_marlus@yahoo.com.br', '$2y$10$X9gqetRF094LdARh7KTxleo5M2DGf1fiW9RkBkF4FTEXSw1Bfpg0W', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 01:41:30', NULL, NULL),
(451, 450, 2, 'patient', NULL, 'ativo', NULL, NULL, 0, 5, 'Mario', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', NULL, NULL, 0, NULL, '1998-05-01', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-11 22:57:31', NULL, NULL),
(452, 450, 2, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'Dr. M', NULL, NULL, '', NULL, '', '', '', '', 'Dentista', 'CRM 123', '', '', 0, '', NULL, '', '', '', '', NULL, 0, NULL, '1980-05-05', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-11 23:14:10', NULL, NULL),
(453, 1, 3, 'owner', 'igor.chapolin@gmail.com', 'ativo', NULL, NULL, 0, 2, 'Marlus Barros', NULL, NULL, NULL, NULL, 'igor.chapolin@gmail.com', '06030689444', NULL, '81983276882', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'igor.chapolin@gmail.com', '$2y$10$wtMR329QmQpBsDP/no7A2.ie18m1hVFEL39PoAGr2jAEulCnAeVsK', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 22:56:08', NULL, NULL),
(454, 1, 4, 'owner', 'teste@teste.com', 'ativo', NULL, NULL, 0, 2, 'Clinica teste', NULL, NULL, NULL, NULL, 'teste@teste.com', '7869876876', NULL, '81987897', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'teste@teste.com', '$2y$10$xZ7eEBVGBIWAUb.Rdo3vS.SMnXG.QGORTxRghsWyYKPbAXHwuq.d6', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-14 01:19:53', NULL, NULL),
(455, 454, 4, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'Medico 1', NULL, NULL, '', NULL, '', '', '', '', '', '', '', '', 0, '', NULL, '', '', '', 'medico1', '$2y$10$8aCP3TyYMzEXdtTsX3Xo3OzTQZsI2FHX5oZZnTfD8c5TyzowwKzVS', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-13 22:22:14', NULL, NULL),
(456, 454, 4, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'Medico 2', NULL, NULL, '', NULL, '', '', '', '', '', '', '', '', 0, '', NULL, '', '', '', 'medico2', '$2y$10$h/KDzJX.aXZj7jB/wr99qOYJRWyz/UQ4uDVqR0XdFsabT.o3bHbL.', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-13 22:22:43', NULL, NULL),
(457, 454, 4, 'staff', NULL, 'ativo', NULL, NULL, 0, 4, 'Secretaria 1', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', 'secretaria1', '$2y$10$QYhKtlOL56gPYcZqSpeSAeJ9aZkT.yzJhIlzOPYmM6iRv5.FozUq2', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-13 22:23:14', NULL, NULL),
(458, 454, 4, 'patient', NULL, 'ativo', NULL, NULL, 0, 5, 'Paciente 1', NULL, NULL, '', NULL, '', '064560456045', '654564', '81987987897', NULL, NULL, '52041230', 'Rua tal', 234, 'Apt 45', NULL, 'Boa viagem', 'Recife', 'PE', NULL, NULL, 0, NULL, '1999-05-01', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-13 22:52:49', NULL, NULL),
(459, 1, 5, 'owner', 'psci@asd.com', 'ativo', NULL, NULL, 0, 2, 'Pscicologo 1', NULL, NULL, NULL, NULL, 'psci@asd.com', '650456456456', NULL, '81987897897987', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'psci@asd.com', '$2y$10$D8Nfa6HxHumthmBgj.TXD.yasEGBxHxyltRBIBjC3XzRcDJANSCKS', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-21 20:54:16', NULL, NULL),
(460, 459, 5, 'patient', NULL, 'ativo', NULL, NULL, 0, 5, 'Moura 1', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', NULL, NULL, 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-21 20:56:01', NULL, NULL),
(461, 459, 5, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'Psicologo 1', NULL, NULL, '', NULL, '', '', '', '', 'Pscicologo', '', '', '', 0, '', NULL, '', '', '', 'psci1', '$2y$10$oCpKmXT2qiLbfQD4rtAlQ.36DeG6h8VayJR0bHf2KG0w7U6Cyr9F2', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-21 20:57:30', NULL, NULL),
(462, 0, 6, 'owner', 'igormarlus@hconnect.com.br', 'ativo', NULL, NULL, 0, 2, 'Clinica teste', NULL, NULL, NULL, NULL, 'igormarlus@hconnect.com.br', '', NULL, '81987987897', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'igormarlus@hconnect.com.br', '$2y$10$QYhKtlOL56gPYcZqSpeSAeJ9aZkT.yzJhIlzOPYmM6iRv5.FozUq2', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-23 15:34:04', '5b6d02e4db2253194a46178382209bd873abab5490a9e4d499f9bb133f7d8530', '2026-05-30 12:34:04'),
(463, 462, 6, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'pROF 1', NULL, NULL, '', NULL, '', '', '', '', '', '', '', '', 0, '', NULL, '', '', '', '', NULL, 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-23 18:46:55', NULL, NULL),
(464, 462, 6, 'patient', NULL, 'ativo', NULL, NULL, 0, 5, 'Luiz Silva', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', NULL, NULL, 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-23 18:47:16', NULL, NULL),
(465, 0, 7, 'owner', 'thiago@utecnologia.com.br', 'ativo', NULL, NULL, 0, 2, 'Thiago Duque', NULL, NULL, NULL, NULL, 'thiago@utecnologia.com.br', '', NULL, '81998120446', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'thiago@utecnologia.com.br', '$2y$10$3vqzR/e6qgfgRYfYisNmuuWey5AOEs4wi9zgkSuHdtdpzth2dnEUK', 0, NULL, '0000-00-00', 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-28 19:33:17', 'a908cf1e84bdf41ead5e886f6766b622f16503e3b564a7c5331e70375d3b868e', '2026-06-04 16:33:17'),
(466, 465, 7, 'provider', NULL, 'ativo', NULL, NULL, 0, 3, 'Thiago Duque', NULL, NULL, '', NULL, '', '', '', '', 'Fisioterapia', '', '', '', 0, '', NULL, '', '', '', 'thiagoduque', '$2y$10$Il0aczh/wPBtdeqpwh6dnOKD4Yvqk0l/s/piPOlC/npplTx26GocC', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-28 19:34:52', NULL, NULL),
(467, 465, 7, 'patient', NULL, 'ativo', NULL, NULL, 0, 5, 'Igor Marlus', NULL, NULL, '', NULL, 'igor_marlus@yahoo.com.br', '06030689444', '6654654', '81983276882', NULL, NULL, '52041230', 'RUa Tenente P. 50', 0, '', NULL, 'Caixa d´agua', 'Olinda', 'PE', NULL, NULL, 0, NULL, '1987-05-01', 0, 0, 0, 0, NULL, NULL, 'Igor.jpg', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-28 19:51:32', NULL, NULL),
(468, 465, 7, 'staff', NULL, 'ativo', NULL, NULL, 0, 4, 'Thulio Duque', NULL, NULL, '', NULL, '', '', '', '', NULL, NULL, '', '', 0, '', NULL, '', '', '', 'thulioduque', '$2y$10$mX9X2YgT5w4HLlmOcBW.leNCMExfaietxV39Q722gx0T/H5W9yTOm', 0, NULL, '0000-00-00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-28 20:15:46', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=469;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
