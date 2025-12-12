-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 12-Dez-2025 às 16:28
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(13, 57, 65, 'violencia', '2025-12-12 10:58:07', 'aprovada', 88, NULL, '2025-12-12 11:14:22', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `noticia`
--

DROP TABLE IF EXISTS `noticia`;
CREATE TABLE IF NOT EXISTS `noticia` (
  `id_noticia` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `conteudo` longtext NOT NULL,
  `imagem_capa` varchar(500) DEFAULT NULL,
  `data_publicacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_autor` int NOT NULL,
  `visualizacoes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_noticia`),
  KEY `id_autor` (`id_autor`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `noticia`
--

INSERT INTO `noticia` (`id_noticia`, `titulo`, `conteudo`, `imagem_capa`, `data_publicacao`, `id_autor`, `visualizacoes`, `created_at`, `updated_at`) VALUES
(1, 'Thomas', '<?php\r\nsession_start();\r\nrequire_once \"../conexao.php\";\r\n\r\n// === PROTEÇÃO: SÓ ADMINISTRADOR ACESSA ===\r\nif (!isset($_SESSION[\'user_id\']) || !isset($_SESSION[\'is_admin\']) || $_SESSION[\'is_admin\'] != 1) {\r\n    header(\"Location: ../index.php\");\r\n    exit;\r\n}\r\n\r\n$mensagem = \"\";\r\n$classeMensagem = \"\";\r\n\r\n// === PROCESSAR O FORMULÁRIO QUANDO ENVIADO ===\r\nif ($_SERVER[\"REQUEST_METHOD\"] == \"POST\") {\r\n    $titulo = trim($_POST[\'titulo\']);\r\n    $conteudo = trim($_POST[\'conteudo\']);\r\n    $id_autor = $_SESSION[\'user_id\'];\r\n\r\n    // Validação básica\r\n    if (empty($titulo) || empty($conteudo)) {\r\n        $mensagem = \"Preencha o título e o conteúdo!\";\r\n        $classeMensagem = \"red\";\r\n    } else {\r\n        // Tratar upload de imagem (se houver)\r\n        $nomeImagem = null;\r\n        if (isset($_FILES[\'imagem_capa\']) && $_FILES[\'imagem_capa\'][\'error\'] == 0) {\r\n            $extensao = pathinfo($_FILES[\'imagem_capa\'][\'name\'], PATHINFO_EXTENSION);\r\n            $extensoesPermitidas = [\'jpg\', \'jpeg\', \'png\', \'gif\'];\r\n            \r\n            if (in_array(strtolower($extensao), $extensoesPermitidas)) {\r\n                $nomeImagem = uniqid(\'noticia_\') . \'.\' . $extensao;\r\n                $caminhoDestino = \"../uploads/noticias/\" . $nomeImagem;\r\n                \r\n                // Criar pasta se não existir\r\n                if (!is_dir(\'../uploads/noticias\')) {\r\n                    mkdir(\'../uploads/noticias\', 0777, true);\r\n                }\r\n                move_uploaded_file($_FILES[\'imagem_capa\'][\'tmp_name\'], $caminhoDestino);\r\n            }\r\n        }\r\n\r\n        // Inserir no banco (AGORA SEM O CAMPO \'status\')\r\n        $sql = \"INSERT INTO noticia (titulo, conteudo, imagem_capa, id_autor, data_publicacao) \r\n                VALUES (?, ?, ?, ?, NOW())\";\r\n        \r\n        $stmt = mysqli_prepare($conexao, $sql);\r\n        mysqli_stmt_bind_param($stmt, \"sssi\", $titulo, $conteudo, $nomeImagem, $id_autor);\r\n        \r\n        if (mysqli_stmt_execute($stmt)) {\r\n            $mensagem = \"Notícia publicada com sucesso!\";\r\n            $classeMensagem = \"green\";\r\n            \r\n            // Limpar os campos do formulário após sucesso\r\n            $_POST[\'titulo\'] = $_POST[\'conteudo\'] = \'\';\r\n        } else {\r\n            $mensagem = \"Erro ao salvar: \" . mysqli_error($conexao);\r\n            $classeMensagem = \"red\";\r\n        }\r\n        mysqli_stmt_close($stmt);\r\n    }\r\n}\r\n?>\r\n<!DOCTYPE html>\r\n<html lang=\"pt-br\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Cadastrar Notícia • Admin</title>\r\n    <link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">\r\n    <link type=\"text/css\" rel=\"stylesheet\" href=\"../css/materialize.min.css\" />\r\n    <link type=\"text/css\" rel=\"stylesheet\" href=\"../css/style_todos.css\" />\r\n    <style>\r\n        .card-noticia {\r\n            border-radius: 15px;\r\n            overflow: hidden;\r\n            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);\r\n            margin-top: 30px;\r\n        }\r\n        .card-noticia .card-content {\r\n            padding: 30px;\r\n        }\r\n        .input-field input[type=text]:focus, \r\n        .input-field textarea:focus {\r\n            border-bottom: 2px solid #009688 !important;\r\n            box-shadow: 0 1px 0 0 #009688 !important;\r\n        }\r\n        .btn-large {\r\n            border-radius: 30px;\r\n            padding: 0 40px;\r\n            font-weight: 600;\r\n            text-transform: none;\r\n            height: 50px;\r\n            line-height: 50px;\r\n        }\r\n    </style>\r\n</head>\r\n<body class=\"grey lighten-4\">\r\n    <?php include_once \"header.php\"; ?>\r\n\r\n    <main class=\"container\">\r\n        <div class=\"row\">\r\n            <div class=\"col s12\">\r\n                <div class=\"card card-noticia white\">\r\n                    <div class=\"card-content\">\r\n                        <h4 class=\"teal-text text-darken-2 center\" style=\"margin-bottom: 30px;\">\r\n                            <i class=\"material-icons left\">post_add</i> Cadastrar Nova Notícia\r\n                        </h4>\r\n\r\n                        <?php if (!empty($mensagem)): ?>\r\n                            <div class=\"card-panel <?= $classeMensagem ?> lighten-5 <?= $classeMensagem ?>-text text-darken-4\" style=\"border-radius: 10px;\">\r\n                                <i class=\"material-icons left\"><?= ($classeMensagem == \'green\') ? \'check_circle\' : \'info\' ?></i>\r\n                                <?= htmlspecialchars($mensagem) ?>\r\n                            </div>\r\n                        <?php endif; ?>\r\n\r\n                        <form method=\"POST\" enctype=\"multipart/form-data\" id=\"formNoticia\">\r\n                            <div class=\"row\">\r\n                                <!-- Título -->\r\n                                <div class=\"input-field col s12\">\r\n                                    <i class=\"material-icons prefix\">title</i>\r\n                                    <input id=\"titulo\" name=\"titulo\" type=\"text\" class=\"validate\" \r\n                                           value=\"<?= isset($_POST[\'titulo\']) ? htmlspecialchars($_POST[\'titulo\']) : \'\' ?>\" \r\n                                           required>\r\n                                    <label for=\"titulo\">Título da Notícia *</label>\r\n                                </div>\r\n\r\n                                <!-- Conteúdo -->\r\n                                <div class=\"input-field col s12\">\r\n                                    <i class=\"material-icons prefix\">description</i>\r\n                                    <textarea id=\"conteudo\" name=\"conteudo\" class=\"materialize-textarea validate\" \r\n                                              required><?= isset($_POST[\'conteudo\']) ? htmlspecialchars($_POST[\'conteudo\']) : \'\' ?></textarea>\r\n                                    <label for=\"conteudo\">Conteúdo *</label>\r\n                                </div>\r\n\r\n                                <!-- Upload de Imagem -->\r\n                                <div class=\"file-field input-field col s12\">\r\n                                    <div class=\"btn teal lighten-1\">\r\n                                        <span><i class=\"material-icons left\">image</i> Imagem de Capa</span>\r\n                                        <input type=\"file\" name=\"imagem_capa\" accept=\"image/*\">\r\n                                    </div>\r\n                                    <div class=\"file-path-wrapper\">\r\n                                        <input class=\"file-path validate\" type=\"text\" \r\n                                               placeholder=\"Imagem opcional para chamada da notícia (JPEG, PNG, GIF)\">\r\n                                    </div>\r\n                                </div>\r\n\r\n                                <!-- NOTA: Status removido - a notícia é sempre publicada -->\r\n                            </div>\r\n\r\n                            <!-- Botões de Ação -->\r\n                            <div class=\"row center\" style=\"margin-top: 40px;\">\r\n                                <div class=\"col s12 m6\">\r\n                                    <a href=\"index.php\" class=\"btn-large grey waves-effect waves-light\">\r\n                                        <i class=\"material-icons left\">arrow_back</i> Voltar\r\n                                    </a>\r\n                                </div>\r\n                                <div class=\"col s12 m6\">\r\n                                    <button type=\"submit\" class=\"btn-large teal waves-effect waves-light\">\r\n                                        <i class=\"material-icons left\">save</i> Publicar Notícia\r\n                                    </button>\r\n                                </div>\r\n                            </div>\r\n                        </form>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n    <script type=\"text/javascript\" src=\"../js/materialize.min.js\"></script>\r\n    <script>\r\n        document.addEventListener(\'DOMContentLoaded\', function() {\r\n            M.updateTextFields();\r\n            M.CharacterCounter.init(document.querySelectorAll(\'#titulo, #conteudo\'));\r\n            \r\n            var elems = document.querySelectorAll(\'textarea\');\r\n            M.CharacterCounter.init(elems);\r\n        });\r\n\r\n        document.getElementById(\'formNoticia\').addEventListener(\'submit\', function(e) {\r\n            var titulo = document.getElementById(\'titulo\').value.trim();\r\n            var conteudo = document.getElementById(\'conteudo\').value.trim();\r\n            \r\n            if (!titulo || !conteudo) {\r\n                e.preventDefault();\r\n                M.toast({html: \'Preencha todos os campos obrigatórios!\', classes: \'red\'});\r\n            }\r\n        });\r\n    </script>\r\n</body>\r\n</html>', 'noticia_693b416a6eb12.jpg', '2025-12-11 19:10:50', 88, 7, '2025-12-11 22:10:50', '2025-12-12 14:29:38'),
(2, 'thomas', '<?php\r\nsession_start();\r\n// Conexão está na raiz (TCC/), então precisa subir um nível: ../\r\nrequire_once \"../conexao.php\";\r\n\r\n// Buscar notícias do banco\r\n$sql = \"SELECT n.id_noticia, n.titulo, n.subtitulo, n.conteudo, n.imagem_capa, \r\n               n.data_publicacao, n.visualizacoes, u.nome AS autor\r\n        FROM noticia n\r\n        JOIN usuario u ON n.id_autor = u.id_usuario\r\n        ORDER BY n.data_publicacao DESC\";\r\n\r\n$resultado = mysqli_query($conexao, $sql);\r\n$noticias = [];\r\n\r\nif ($resultado) {\r\n    while ($registro = mysqli_fetch_assoc($resultado)) {\r\n        $noticias[] = $registro;\r\n    }\r\n}\r\n?>\r\n<!DOCTYPE html>\r\n<html lang=\"pt-br\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Notícias • NAC Portal</title>\r\n    <link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">\r\n    <!-- CSS está em ../css/ (voltar um nível para TCC/, depois entrar em css/) -->\r\n    <link type=\"text/css\" rel=\"stylesheet\" href=\"../css/materialize.min.css\" media=\"screen,projection\"/>\r\n    <link type=\"text/css\" rel=\"stylesheet\" href=\"../css/style_todos.css\"/>\r\n    <style>\r\n        .noticia-card {\r\n            border-radius: 12px;\r\n            overflow: hidden;\r\n            transition: all 0.3s ease;\r\n            height: 100%;\r\n            box-shadow: 0 4px 15px rgba(0,0,0,0.08);\r\n        }\r\n        .noticia-card:hover {\r\n            transform: translateY(-5px);\r\n            box-shadow: 0 8px 25px rgba(0,0,0,0.15);\r\n        }\r\n        .noticia-imagem-container {\r\n            height: 200px;\r\n            overflow: hidden;\r\n            position: relative;\r\n        }\r\n        .noticia-imagem {\r\n            width: 100%;\r\n            height: 100%;\r\n            object-fit: cover;\r\n            transition: transform 0.5s ease;\r\n        }\r\n        .noticia-card:hover .noticia-imagem {\r\n            transform: scale(1.05);\r\n        }\r\n        .noticia-conteudo {\r\n            padding: 20px;\r\n            display: flex;\r\n            flex-direction: column;\r\n            height: calc(100% - 200px);\r\n        }\r\n        .noticia-titulo {\r\n            font-size: 1.2rem;\r\n            font-weight: 600;\r\n            line-height: 1.4;\r\n            margin-bottom: 10px;\r\n            display: -webkit-box;\r\n            -webkit-line-clamp: 2;\r\n            -webkit-box-orient: vertical;\r\n            overflow: hidden;\r\n        }\r\n        .noticia-subtitulo {\r\n            font-size: 1rem;\r\n            color: #666;\r\n            font-style: italic;\r\n            margin-bottom: 12px;\r\n            line-height: 1.4;\r\n        }\r\n        .noticia-resumo {\r\n            color: #555;\r\n            line-height: 1.5;\r\n            flex-grow: 1;\r\n            margin-bottom: 15px;\r\n        }\r\n        .noticia-meta {\r\n            display: flex;\r\n            justify-content: space-between;\r\n            align-items: center;\r\n            margin-top: auto;\r\n            font-size: 0.85rem;\r\n            color: #888;\r\n            border-top: 1px solid #eee;\r\n            padding-top: 15px;\r\n        }\r\n        .noticia-data {\r\n            display: flex;\r\n            align-items: center;\r\n        }\r\n        .noticia-data i {\r\n            margin-right: 5px;\r\n            font-size: 1rem;\r\n        }\r\n        .noticia-visualizacoes {\r\n            display: flex;\r\n            align-items: center;\r\n        }\r\n        .noticia-visualizacoes i {\r\n            margin-right: 5px;\r\n        }\r\n        .no-noticias {\r\n            text-align: center;\r\n            padding: 60px 20px;\r\n        }\r\n        .no-noticias i {\r\n            font-size: 4rem;\r\n            color: #ccc;\r\n            margin-bottom: 20px;\r\n        }\r\n        .page-title {\r\n            margin: 30px 0 40px;\r\n            text-align: center;\r\n        }\r\n        .page-title h2 {\r\n            font-weight: 300;\r\n            color: #009688;\r\n            margin-bottom: 10px;\r\n        }\r\n        .page-title p {\r\n            color: #666;\r\n            max-width: 600px;\r\n            margin: 0 auto;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <?php \r\n    // Header está na raiz (TCC/header.php), então precisa voltar um nível: ../\r\n    if (file_exists(\"../header.php\")) {\r\n        include_once \"../header.php\"; \r\n    } else {\r\n        echo \"<nav class=\'teal\'><div class=\'nav-wrapper\'><a href=\'../feed.php\' class=\'brand-logo\'>NAC Portal</a></div></nav>\";\r\n    }\r\n    ?>\r\n\r\n    <main class=\"container\">\r\n        <div class=\"page-title\">\r\n            <h2><i class=\"material-icons left\">newspaper</i> Notícias Culturais</h2>\r\n            <p>Fique por dentro das últimas novidades, eventos e destaques do mundo cultural</p>\r\n        </div>\r\n\r\n        <?php if (empty($noticias)): ?>\r\n            <div class=\"card-panel no-noticias\">\r\n                <i class=\"material-icons\">newspaper</i>\r\n                <h5>Nenhuma notícia publicada ainda</h5>\r\n                <p>Em breve teremos novidades para você!</p>\r\n            </div>\r\n        <?php else: ?>\r\n            <div class=\"row\">\r\n                <?php foreach ($noticias as $noticia): \r\n                    // Criar resumo do conteúdo\r\n                    $resumo = strip_tags($noticia[\'conteudo\']);\r\n                    $resumo = strlen($resumo) > 120 ? substr($resumo, 0, 120) . \'...\' : $resumo;\r\n                    \r\n                    // Formatar data\r\n                    $data_formatada = date(\'d/m/Y\', strtotime($noticia[\'data_publicacao\']));\r\n                    \r\n                    // Verificar se tem imagem\r\n                    // Imagens estão em ../uploads/noticias/ (voltar um nível para TCC/, depois entrar em uploads/noticias/)\r\n                    $tem_imagem = !empty($noticia[\'imagem_capa\']) && file_exists(\"../uploads/noticias/\" . $noticia[\'imagem_capa\']);\r\n                ?>\r\n                    <div class=\"col s12 m6 l4\">\r\n                        <!-- ver_noticia.php está na MESMA pasta (noticia/) -->\r\n                        <a href=\"ver_noticia.php?id=<?= $noticia[\'id_noticia\'] ?>\" class=\"black-text\" style=\"text-decoration: none;\">\r\n                            <div class=\"card noticia-card hoverable\">\r\n                                <div class=\"noticia-imagem-container\">\r\n                                    <?php if ($tem_imagem): ?>\r\n                                        <!-- Imagem: ../uploads/noticias/ -->\r\n                                        <img src=\"../uploads/noticias/<?= $noticia[\'imagem_capa\'] ?>\" \r\n                                             alt=\"<?= htmlspecialchars($noticia[\'titulo\']) ?>\" \r\n                                             class=\"noticia-imagem\">\r\n                                    <?php else: ?>\r\n                                        <div style=\"background: linear-gradient(135deg, #009688, #4DB6AC); height: 100%; display: flex; align-items: center; justify-content: center;\">\r\n                                            <i class=\"material-icons white-text\" style=\"font-size: 4rem;\">newspaper</i>\r\n                                        </div>\r\n                                    <?php endif; ?>\r\n                                </div>\r\n                                \r\n                                <div class=\"noticia-conteudo\">\r\n                                    <h3 class=\"noticia-titulo\"><?= htmlspecialchars($noticia[\'titulo\']) ?></h3>\r\n                                    \r\n                                    <?php if (!empty($noticia[\'subtitulo\'])): ?>\r\n                                        <p class=\"noticia-subtitulo\">\"<?= htmlspecialchars($noticia[\'subtitulo\']) ?>\"</p>\r\n                                    <?php endif; ?>\r\n                                    \r\n                                    <p class=\"noticia-resumo\"><?= htmlspecialchars($resumo) ?></p>\r\n                                    \r\n                                    <div class=\"noticia-meta\">\r\n                                        <div class=\"noticia-data\">\r\n                                            <i class=\"material-icons tiny\">calendar_today</i>\r\n                                            <?= $data_formatada ?>\r\n                                        </div>\r\n                                        <div class=\"noticia-visualizacoes\">\r\n                                            <i class=\"material-icons tiny\">visibility</i>\r\n                                            <?= $noticia[\'visualizacoes\'] ?>\r\n                                        </div>\r\n                                    </div>\r\n                                    \r\n                                    <!-- Botão para ler completo -->\r\n                                    <div class=\"center\" style=\"margin-top: 15px;\">\r\n                                        <span class=\"btn teal lighten-1 waves-effect waves-light btn-small\">\r\n                                            Ler notícia completa\r\n                                        </span>\r\n                                    </div>\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    </div>\r\n                <?php endforeach; ?>\r\n            </div>\r\n        <?php endif; ?>\r\n    </main>\r\n\r\n    <?php \r\n    // Footer está na raiz (TCC/footer.php), então precisa voltar um nível: ../\r\n    if (file_exists(\"../footer.php\")) {\r\n        include_once \"../footer.php\"; \r\n    }\r\n    ?>\r\n\r\n    <!-- Scripts estão em ../js/ (voltar um nível para TCC/, depois entrar em js/) -->\r\n    <script type=\"text/javascript\" src=\"../js/materialize.min.js\"></script>\r\n    <script>\r\n        document.addEventListener(\'DOMContentLoaded\', function() {\r\n            console.log(\'Página de notícias carregada com sucesso\');\r\n            \r\n            // Inicializar tooltips se existirem\r\n            var tooltips = document.querySelectorAll(\'.tooltipped\');\r\n            if (tooltips.length > 0) {\r\n                M.Tooltip.init(tooltips);\r\n            }\r\n            \r\n            // Inicializar sidenav se existir\r\n            var sidenavs = document.querySelectorAll(\'.sidenav\');\r\n            if (sidenavs.length > 0) {\r\n                M.Sidenav.init(sidenavs);\r\n            }\r\n        });\r\n    </script>\r\n</body>\r\n</html>', 'noticia_693c20be39255.jpg', '2025-12-12 11:03:42', 88, 2, '2025-12-12 14:03:42', '2025-12-12 14:29:37');

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
  `categoria` varchar(50) NOT NULL,
  `tags` varchar(200) DEFAULT NULL,
  `autor` varchar(100) NOT NULL,
  `creditos_midia` varchar(200) DEFAULT NULL,
  `caminho_midia` varchar(255) DEFAULT NULL,
  `id_admin` int NOT NULL,
  `data_publicacao` datetime NOT NULL,
  `data_criacao` datetime NOT NULL,
  `ativo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_noticia`),
  KEY `id_admin` (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `publicacao`
--

INSERT INTO `publicacao` (`id_publicacao`, `id_usuario_fk`, `titulo`, `descricao`, `caminho_arquivo`, `tipo_arquivo`, `mime_type`, `tamanho_bytes`, `data_publicacao`, `deleted_at`, `curtidas_count`, `comentarios_count`) VALUES
(44, 79, 'Bruce Wayne ', 'Batman', '9b2ceeefcaed01facb3ee87f1c741469.jpg', 'imagem', 'image/jpeg', 48354, '2025-11-07 20:56:09', '2025-12-11 18:36:45', 0, 0),
(46, 79, 'Trio dos Sonhos', 'Trio lendário do truco ', 'c71aa8e8a1b5a67b0d5abb0fb365f0b4.MP4', 'video', 'video/mp4', 2367492, '2025-11-07 21:02:08', '2025-12-06 21:30:43', 0, 0),
(62, 57, 'Asa Mitaka', 'Personagem do anime de Chainsaw Man', '714edd5e5daf1bcac0ab9babede79080.jpeg', 'imagem', 'image/jpeg', 40363, '2025-11-15 17:32:27', '2025-12-11 18:48:10', 0, 0),
(63, 57, 'thomas', 'informações da minha amada juliana ', '327a61ef359eba0fab1518d4d430eda1.jpeg', 'imagem', 'image/jpeg', 3549802, '2025-12-03 14:36:21', '2025-12-11 18:45:06', 0, 0),
(64, 88, 'thomas', 'thomas', '7fa8a3dccf2334ebdd07b0e593f58369.jpg', 'imagem', 'image/jpeg', 72929, '2025-12-10 17:03:51', '2025-12-11 19:06:24', 0, 0),
(65, 79, 'thomas', 'informações da minha amada juliana ', '3dbc460dfea6d1bbfe83b76f17db2840.jpg', 'imagem', 'image/jpeg', 72929, '2025-12-12 10:57:51', '2025-12-12 11:14:22', 0, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `salvos`
--

INSERT INTO `salvos` (`id_salvo`, `id_usuario`, `id_publicacao`, `data_salvo`) VALUES
(11, 79, 62, '2025-11-15 18:00:52'),
(14, 79, 46, '2025-11-15 18:11:13');

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
