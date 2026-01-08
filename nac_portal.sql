-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 08-Jan-2026 às 00:30
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
-- Estrutura da tabela `denuncia`
--

DROP TABLE IF EXISTS `denuncia`;
CREATE TABLE IF NOT EXISTS `denuncia` (
  `id_denuncia` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_publicacao` int NOT NULL,
  `categoria` enum('spam','conteudo_ofensivo','desinformacao','violencia','pornografia','direitos_autorais','outros') COLLATE utf8mb4_unicode_ci DEFAULT 'outros',
  `data_denuncia` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pendente','aprovada','rejeitada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `id_admin_analisou` int DEFAULT NULL,
  `observacao_admin` text COLLATE utf8mb4_unicode_ci,
  `data_analise` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_denuncia`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_publicacao` (`id_publicacao`),
  KEY `idx_status` (`status`),
  KEY `idx_data` (`data_denuncia`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `denuncia`
--

INSERT INTO `denuncia` (`id_denuncia`, `id_usuario`, `id_publicacao`, `categoria`, `data_denuncia`, `status`, `id_admin_analisou`, `observacao_admin`, `data_analise`, `deleted_at`) VALUES
(1, 57, 46, 'pornografia', '2025-12-06 19:44:26', 'rejeitada', 88, NULL, '2025-12-06 21:09:14', NULL),
(2, 57, 46, 'outros', '2025-12-06 21:29:47', 'aprovada', 88, NULL, '2025-12-06 21:30:43', NULL),
(3, 57, 44, 'desinformacao', '2025-12-06 21:34:02', 'rejeitada', 88, NULL, '2025-12-06 21:34:47', NULL),
(4, 88, 44, 'desinformacao', '2025-12-06 21:37:17', 'rejeitada', 88, NULL, '2025-12-06 21:42:03', NULL),
(5, 88, 44, 'violencia', '2025-12-10 09:14:30', 'rejeitada', 88, NULL, '2025-12-10 16:49:56', NULL),
(6, 57, 44, 'violencia', '2025-12-10 16:51:35', 'rejeitada', 88, NULL, '2025-12-10 16:52:31', NULL),
(7, 88, 44, 'violencia', '2025-12-10 16:56:03', 'rejeitada', 88, NULL, '2025-12-10 16:56:37', NULL),
(8, 88, 44, 'violencia', '2025-12-10 16:59:15', 'rejeitada', 88, NULL, '2025-12-11 16:20:22', NULL),
(9, 57, 44, 'violencia', '2025-12-11 18:36:10', 'aprovada', 88, NULL, '2025-12-11 18:36:46', NULL),
(10, 88, 63, 'outros', '2025-12-11 18:43:48', 'aprovada', 88, NULL, '2025-12-11 18:45:06', NULL),
(11, 88, 62, 'spam', '2025-12-11 18:47:59', 'aprovada', 88, NULL, '2025-12-11 18:48:10', NULL),
(12, 79, 64, 'desinformacao', '2025-12-11 19:06:08', 'aprovada', 88, NULL, '2025-12-11 19:06:24', NULL),
(13, 57, 65, 'violencia', '2025-12-12 10:58:07', 'aprovada', 88, NULL, '2025-12-12 11:14:22', NULL),
(14, 79, 68, 'direitos_autorais', '2025-12-15 16:50:39', 'rejeitada', 88, NULL, '2026-01-05 17:04:33', NULL),
(15, 57, 69, 'violencia', '2026-01-05 21:01:18', 'aprovada', 88, NULL, '2026-01-05 21:02:26', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `noticias`
--

DROP TABLE IF EXISTS `noticias`;
CREATE TABLE IF NOT EXISTS `noticias` (
  `id_noticia` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `subtitulo` varchar(300) DEFAULT NULL,
  `corpo` text NOT NULL,
  `autor` varchar(100) NOT NULL,
  `caminho_midia` varchar(255) DEFAULT NULL,
  `id_admin` int NOT NULL,
  `data_publicacao` datetime NOT NULL,
  `ativo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_noticia`),
  KEY `id_admin` (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `publicacao`
--

INSERT INTO `publicacao` (`id_publicacao`, `id_usuario_fk`, `titulo`, `descricao`, `caminho_arquivo`, `tipo_arquivo`, `mime_type`, `tamanho_bytes`, `data_publicacao`, `deleted_at`, `curtidas_count`, `comentarios_count`) VALUES
(44, 79, 'Bruce Wayne ', 'Batman', '9b2ceeefcaed01facb3ee87f1c741469.jpg', 'imagem', 'image/jpeg', 48354, '2025-11-07 20:56:09', '2025-12-11 18:36:45', 0, 0),
(46, 79, 'Trio dos Sonhos', 'Trio lendário do truco ', 'c71aa8e8a1b5a67b0d5abb0fb365f0b4.MP4', 'video', 'video/mp4', 2367492, '2025-11-07 21:02:08', '2025-12-06 21:30:43', 0, 0),
(62, 57, 'Asa Mitaka', 'Personagem do anime de Chainsaw Man', '714edd5e5daf1bcac0ab9babede79080.jpeg', 'imagem', 'image/jpeg', 40363, '2025-11-15 17:32:27', '2025-12-11 18:48:10', 0, 0),
(63, 57, 'thomas', 'informações da minha amada juliana ', '327a61ef359eba0fab1518d4d430eda1.jpeg', 'imagem', 'image/jpeg', 3549802, '2025-12-03 14:36:21', '2025-12-11 18:45:06', 0, 0),
(64, 88, 'thomas', 'thomas', '7fa8a3dccf2334ebdd07b0e593f58369.jpg', 'imagem', 'image/jpeg', 72929, '2025-12-10 17:03:51', '2025-12-11 19:06:24', 0, 0),
(65, 79, 'thomas', 'informações da minha amada juliana ', '3dbc460dfea6d1bbfe83b76f17db2840.jpg', 'imagem', 'image/jpeg', 72929, '2025-12-12 10:57:51', '2025-12-12 11:14:22', 0, 0),
(68, 57, 'Escultura de Argila', 'Instituto federal de argila', '09c2d18b1af7f0b1ac3e335526b1287b.JPG', 'imagem', 'image/jpeg', 9860216, '2025-12-15 16:45:14', NULL, 0, 0),
(69, 79, 'Construindo Identidades', 'retrata a fachada do campus o qual auxilia os alunos a melhorar como profissional e pessoa, unindo os alunos e adolescentes através dos cursos técnicos, esportes e amizades. ', '5eaf9ff05dc5093c0d3f85f3701d842b.JPG', 'imagem', 'image/jpeg', 9916393, '2025-12-15 16:47:42', '2026-01-05 21:02:26', 0, 0),
(70, NULL, 'thomas', 'thomas', '5973198d6d84fba4c7336c302bac6ce5.jpg', 'imagem', 'image/jpeg', 72929, '2026-01-07 21:24:58', NULL, 0, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `salvos`
--

INSERT INTO `salvos` (`id_salvo`, `id_usuario`, `id_publicacao`, `data_salvo`) VALUES
(11, 79, 62, '2025-11-15 18:00:52'),
(14, 79, 46, '2025-11-15 18:11:13'),
(86, 79, 69, '2025-12-17 19:51:21'),
(91, 57, 68, '2026-01-07 21:16:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha`, `preferencias`, `data_cadastro`, `deleted_at`, `verificado`, `codigo_verificacao`, `codigo_expira_em`, `token_recuperacao`, `token_expira_em`, `foto_perfil`, `is_admin`) VALUES
(57, 'Edupadawan', 'eduardo.2023318418@aluno.iffar.edu.br', '$2y$10$oG/AMvlqZj5gGHDTRun3ye95IkLZdhDttIjioBIxT23K.Swj3mzvC', 'eu gosto de dar o cu', '2025-10-29 13:39:19', NULL, 1, NULL, NULL, NULL, NULL, 'meu_perfil/fotos_perfil/7b0b879914893aae6e8ec8d6dbc79473.jpg', 0),
(79, 'THOMAS BRACCINI', 'thomas.silveira.braccini@gmail.com', '$2y$10$OoGkzXtMpqrIhVfR.Yv6jeYJuurIq1xKNGdLMFsE6H1Xud5DuXkkS', 'Pintor e Cantor', '2025-11-07 20:55:02', NULL, 1, NULL, NULL, '809199d4f887d75dc86e6523a7861643', 1767790281, 'meu_perfil/fotos_perfil/31c8b5e4f4ea0a403eb7d3742db41f1e.jpeg', 0),
(88, 'Administrador', 'admin@portal.com', '$2y$10$vcZkBYUYYfI90rltbN9VCefENOKNH9ibD3JilIPou.AU2BFPVRixK', NULL, '2025-12-06 16:56:28', NULL, 1, NULL, NULL, NULL, NULL, NULL, 1);

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
