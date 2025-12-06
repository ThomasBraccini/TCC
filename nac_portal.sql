-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 06-Dez-2025 às 20:31
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `nac_portal`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `publicacao`
--

DROP TABLE IF EXISTS `publicacao`;
CREATE TABLE IF NOT EXISTS `publicacao` (
  `id_publicacao` int NOT NULL AUTO_INCREMENT,
  `id_usuario_fk` int DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text,
  `caminho_arquivo` varchar(255) NOT NULL,
  `tipo_arquivo` enum('imagem','video','audio') NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `tamanho_bytes` bigint DEFAULT NULL,
  `data_publicacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `curtidas_count` int DEFAULT '0',
  `comentarios_count` int DEFAULT '0',
  PRIMARY KEY (`id_publicacao`),
  KEY `idx_publicacao_usuario` (`id_usuario_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `publicacao`
--

INSERT INTO `publicacao` (`id_publicacao`, `id_usuario_fk`, `titulo`, `descricao`, `caminho_arquivo`, `tipo_arquivo`, `mime_type`, `tamanho_bytes`, `data_publicacao`, `deleted_at`, `curtidas_count`, `comentarios_count`) VALUES
(44, 79, 'Bruce Wayne ', 'Batman', '9b2ceeefcaed01facb3ee87f1c741469.jpg', 'imagem', 'image/jpeg', 48354, '2025-11-07 20:56:09', NULL, 0, 0),
(46, 79, 'Trio dos Sonhos', 'Trio lendário do truco ', 'c71aa8e8a1b5a67b0d5abb0fb365f0b4.MP4', 'video', 'video/mp4', 2367492, '2025-11-07 21:02:08', NULL, 0, 0),
(58, 57, 'Audio', 'Audio Teste ', '657636834e87c5a002d0e628ddf9f5cc.mp3', 'audio', 'audio/mpeg', 140988, '2025-11-07 23:15:19', NULL, 0, 0),
(62, 57, 'Asa Mitaka', 'Personagem do anime de Chainsaw Man', '714edd5e5daf1bcac0ab9babede79080.jpeg', 'imagem', 'image/jpeg', 40363, '2025-11-15 17:32:27', NULL, 0, 0),
(63, 57, 'thomas', 'informações da minha amada juliana ', '327a61ef359eba0fab1518d4d430eda1.jpeg', 'imagem', 'image/jpeg', 3549802, '2025-12-03 14:36:21', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `salvos`
--

DROP TABLE IF EXISTS `salvos`;
CREATE TABLE IF NOT EXISTS `salvos` (
  `id_salvo` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_publicacao` int NOT NULL,
  `data_salvo` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_salvo`),
  UNIQUE KEY `unico_salvo` (`id_usuario`,`id_publicacao`),
  KEY `id_publicacao` (`id_publicacao`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `salvos`
--

INSERT INTO `salvos` (`id_salvo`, `id_usuario`, `id_publicacao`, `data_salvo`) VALUES
(11, 79, 62, '2025-11-15 18:00:52'),
(13, 79, 58, '2025-11-15 18:02:51'),
(14, 79, 46, '2025-11-15 18:11:13'),
(15, 57, 62, '2025-12-03 13:25:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `preferencias` text,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT '0',
  `codigo_verificacao` varchar(10) DEFAULT NULL,
  `codigo_expira_em` int DEFAULT NULL,
  `token_recuperacao` varchar(32) DEFAULT NULL,
  `token_expira_em` int DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha`, `preferencias`, `data_cadastro`, `deleted_at`, `verificado`, `codigo_verificacao`, `codigo_expira_em`, `token_recuperacao`, `token_expira_em`, `foto_perfil`, `is_admin`) VALUES
(57, 'Edupadawan', 'eduardo.2023318418@aluno.iffar.edu.br', '$2y$10$oG/AMvlqZj5gGHDTRun3ye95IkLZdhDttIjioBIxT23K.Swj3mzvC', 'Eu sou viado', '2025-10-29 13:39:19', NULL, 1, NULL, NULL, NULL, NULL, 'meu_perfil/fotos_perfil/7b0b879914893aae6e8ec8d6dbc79473.jpg', 0),
(79, 'THOMAS BRACCINI', 'thomas.silveira.braccini@gmail.com', '$2y$10$XtbdC3haBG5c/TRtIMZsuOaK9.tcJQu0Ass5EzAXUoTFZANFsZClm', 'Sou Sigma ', '2025-11-07 20:55:02', NULL, 1, NULL, NULL, NULL, NULL, 'meu_perfil/fotos_perfil/31c8b5e4f4ea0a403eb7d3742db41f1e.jpeg', 0),
(88, 'Administrador', 'admin@portal.com', '$2y$10$vcZkBYUYYfI90rltbN9VCefENOKNH9ibD3JilIPou.AU2BFPVRixK', '[]', '2025-12-06 16:56:28', NULL, 1, NULL, NULL, NULL, NULL, NULL, 1);

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `publicacao`
--
ALTER TABLE `publicacao`
  ADD CONSTRAINT `publicacao_ibfk_1` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `salvos`
--
ALTER TABLE `salvos`
  ADD CONSTRAINT `salvos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `salvos_ibfk_2` FOREIGN KEY (`id_publicacao`) REFERENCES `publicacao` (`id_publicacao`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
