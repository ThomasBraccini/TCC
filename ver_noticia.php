<?php
session_start();
require_once "conexao.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: noticias.php");
    exit;
}

$id_noticia = intval($_GET['id']);

// Buscar notícia - CORRIGIDO para estrutura da tabela
$sql = "SELECT n.* FROM noticias n WHERE n.id_noticia = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_noticia);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$noticia = mysqli_fetch_assoc($resultado);

if (!$noticia) {
    header("Location: noticias.php");
    exit;
}

// Atualizar visualizações
mysqli_query($conexao, "UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id_noticia = $id_noticia");

$data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
$tem_imagem = !empty($noticia['caminho_midia']) && file_exists("../uploads/noticias/" . $noticia['caminho_midia']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> • NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" />
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css" />
    <style>
        .noticia-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 15px;
        }
        .noticia-titulo {
            font-size: 2.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .noticia-subtitulo {
            font-size: 1.3rem;
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
        }
        .noticia-data {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .noticia-imagem {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 5px;
            margin: 15px 0;
        }
        .noticia-conteudo {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #333;
            margin-top: 20px;
            white-space: pre-line;
        }
        .btn-voltar {
            margin-bottom: 20px;
        }
        .noticia-info {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>

    <main class="noticia-container">
        <!-- Botão Voltar -->
        <a href="noticias.php" class="btn btn-voltar teal waves-effect">
            <i class="material-icons left">arrow_back</i> Voltar
        </a>

        <!-- Título -->
        <h1 class="noticia-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h1>
        
        <!-- Subtítulo -->
        <?php if (!empty($noticia['subtitulo'])): ?>
            <div class="noticia-subtitulo"><?= htmlspecialchars($noticia['subtitulo']) ?></div>
        <?php endif; ?>
        
        <!-- Informações -->
        <div class="noticia-data">
            <i class="material-icons tiny">calendar_today</i>
            Publicado em: <?= $data_formatada ?>
            
            <?php if (!empty($noticia['autor'])): ?>
                <span style="margin-left: 20px;">
                    <i class="material-icons tiny">person</i>
                    Autor: <?= htmlspecialchars($noticia['autor']) ?>
                </span>
            <?php endif; ?>
            
            <?php if (!empty($noticia['categoria'])): ?>
                <span style="margin-left: 20px;">
                    <i class="material-icons tiny">category</i>
                    Categoria: <?= htmlspecialchars($noticia['categoria']) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Imagem -->
        <?php if ($tem_imagem): ?>
            <img src="../uploads/noticias/<?= $noticia['caminho_midia'] ?>" 
                 alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                 class="noticia-imagem responsive-img">
            <?php if (!empty($noticia['creditos_midia'])): ?>
                <p class="grey-text" style="font-size: 0.8rem; text-align: right; margin-top: -10px;">
                    <i>Créditos: <?= htmlspecialchars($noticia['creditos_midia']) ?></i>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Conteúdo -->
        <div class="noticia-conteudo">
            <?= nl2br(htmlspecialchars($noticia['corpo'])) ?>
        </div>
        
        <!-- Tags -->
        <?php if (!empty($noticia['tags'])): ?>
            <div class="noticia-tags" style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee;">
                <i class="material-icons tiny">local_offer</i>
                <strong>Tags:</strong>
                <?php
                $tags = explode(',', $noticia['tags']);
                foreach ($tags as $tag):
                    $tag = trim($tag);
                    if (!empty($tag)):
                ?>
                    <span class="teal lighten-5 teal-text" style="padding: 2px 8px; border-radius: 10px; margin-right: 5px; font-size: 0.9rem;">
                        <?= htmlspecialchars($tag) ?>
                    </span>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Visualizações -->
        <div class="noticia-info">
            <div class="visualizacoes">
                <i class="material-icons tiny">visibility</i>
                Visualizações: <?= $noticia['visualizacoes'] ?>
            </div>
        </div>
    </main>

    <?php include_once "footer.php"; ?>
    
    <script src="../js/materialize.min.js"></script>
</body>
</html>