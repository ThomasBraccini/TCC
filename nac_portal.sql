-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 16-Jan-2026 às 01:25
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
  `data_analise` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_denuncia`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_publicacao` (`id_publicacao`),
  KEY `idx_status` (`status`),
  KEY `idx_data` (`data_denuncia`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id_noticia`),
  KEY `id_admin` (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3;

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
  PRIMARY KEY (`id_publicacao`),
  KEY `idx_publicacao_usuario` (`id_usuario_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha`, `preferencias`, `data_cadastro`, `deleted_at`, `verificado`, `codigo_verificacao`, `codigo_expira_em`, `token_recuperacao`, `token_expira_em`, `foto_perfil`, `is_admin`) VALUES
(88, 'Administrador', 'admin@portal.com', '$2y$10$vcZkBYUYYfI90rltbN9VCefENOKNH9ibD3JilIPou.AU2BFPVRixK', NULL, '2025-12-06 16:56:28', NULL, 1, NULL, NULL, NULL, NULL, NULL, 1),
(102, 'THOMAS SILVEIRA BRACCINI', 'thomas.silveira.braccini@gmail.com', '$2y$10$jp0IZ7mb1cdQ/5.8mh2/AOpcNd9m2QOd9jNstr6Tf6Uk4CZpcv3my', 'Casa Marrom', '2026-01-15 22:24:52', NULL, 1, NULL, NULL, NULL, NULL, '', 0);

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
