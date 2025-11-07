-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 06-Nov-2025 às 09:50
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
-- Estrutura da tabela `comentario`
--

DROP TABLE IF EXISTS `comentario`;
CREATE TABLE IF NOT EXISTS `comentario` (
  `id_comentario` int NOT NULL AUTO_INCREMENT,
  `id_publicacao_fk` int NOT NULL,
  `id_usuario_fk` int DEFAULT NULL,
  `texto_comentario` text NOT NULL,
  `data_comentario` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comentario`),
  KEY `id_publicacao_fk` (`id_publicacao_fk`),
  KEY `id_usuario_fk` (`id_usuario_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura da tabela `curtida`
--

DROP TABLE IF EXISTS `curtida`;
CREATE TABLE IF NOT EXISTS `curtida` (
  `id_usuario_fk` int NOT NULL,
  `id_publicacao_fk` int NOT NULL,
  `data_curtida` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario_fk`,`id_publicacao_fk`),
  KEY `id_publicacao_fk` (`id_publicacao_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura da tabela `denuncia`
--

DROP TABLE IF EXISTS `denuncia`;
CREATE TABLE IF NOT EXISTS `denuncia` (
  `id_denuncia` int NOT NULL AUTO_INCREMENT,
  `id_publicacao_fk` int NOT NULL,
  `id_usuario_fk` int DEFAULT NULL,
  `motivo` text NOT NULL,
  `data_denuncia` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pendente','analisada') DEFAULT 'pendente',
  `analisado_por_admin` varchar(255) DEFAULT NULL,
  `data_analise` datetime DEFAULT NULL,
  `observacao_admin` text,
  PRIMARY KEY (`id_denuncia`),
  KEY `id_publicacao_fk` (`id_publicacao_fk`),
  KEY `id_usuario_fk` (`id_usuario_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb3;

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
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha`, `preferencias`, `data_cadastro`, `deleted_at`, `verificado`, `codigo_verificacao`, `codigo_expira_em`, `token_recuperacao`, `token_expira_em`, `foto_perfil`) VALUES
(57, 'Edupadawan', 'eduardo.2023318418@aluno.iffar.edu.br', '$2y$10$oG/AMvlqZj5gGHDTRun3ye95IkLZdhDttIjioBIxT23K.Swj3mzvC', 'Eu sou viado', '2025-10-29 13:39:19', NULL, 1, NULL, NULL, NULL, NULL, 'meu_perfil/fotos_perfil/2244ac5a4af50bfe5e8799898b2fc4ed.jpg'),
(78, 'THOMAS SILVEIRA BRACCINI', 'thomas.silveira.braccini@gmail.com', '$2y$10$cc3dgZmuBtLWQgJ9jSVEuePEEz3Oq3w5FovZdo.3aY/aYi7lMY4S6', 'sexo', '2025-11-05 21:29:24', NULL, 1, NULL, NULL, NULL, NULL, '');

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`id_publicacao_fk`) REFERENCES `publicacao` (`id_publicacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `curtida`
--
ALTER TABLE `curtida`
  ADD CONSTRAINT `curtida_ibfk_1` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `curtida_ibfk_2` FOREIGN KEY (`id_publicacao_fk`) REFERENCES `publicacao` (`id_publicacao`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `denuncia`
--
ALTER TABLE `denuncia`
  ADD CONSTRAINT `denuncia_ibfk_1` FOREIGN KEY (`id_publicacao_fk`) REFERENCES `publicacao` (`id_publicacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `denuncia_ibfk_2` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `publicacao`
--
ALTER TABLE `publicacao`
  ADD CONSTRAINT `publicacao_ibfk_1` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
