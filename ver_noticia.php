<?php
session_start();
require_once "conexao.php";
if (!isset($_GET['id']) or !is_numeric($_GET['id'])) {
    header("Location: noticias.php");
    exit;
}
$id_noticia = $_GET['id'];
// Busca a notícia no banco de dados
$sql = "SELECT id_noticia, titulo, subtitulo, corpo, autor, caminho_midia, data_publicacao 
        FROM noticias 
        WHERE id_noticia = $id_noticia 
        AND ativo = 1"; // Só mostra se estiver ativa
$resultado = mysqli_query($conexao, $sql);
// Se não encontrou a notícia, volta para a lista
if (mysqli_num_rows($resultado) == 0) {
    header("Location: noticias.php");
    exit;
}
// Pega os dados da notícia como um array associativo
$noticia = mysqli_fetch_assoc($resultado);
// Formata a data para o padrão brasileiro (ex: 12/12/2025)
$data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
// Verifica se tem imagem e se o arquivo realmente existe no servidor
$tem_imagem = !empty($noticia['caminho_midia']) &&  file_exists("../uploads/noticias/" . $noticia['caminho_midia']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $noticia['titulo'] ?> • NAC Portal</title>
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
        .noticia-titulo {
            font-size: 3.5rem !important;     /* Título bem grande */
            font-weight: 700;
            color: #333;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .noticia-subtitulo {
            font-size: 2rem !important;       /* Subtítulo médio */
            font-weight: 400;
            font-style: italic;
            color: #555;
            margin-top: 0;
            margin-bottom: 40px;
        }
        .noticia-conteudo {
            font-size: 1.1rem;                /* Texto do corpo normal e legível */
            line-height: 1.8;
            color: #444;
            text-align: justify;
        }
        .noticia-imagem {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>
    <main class="noticia-container">
        <h1 class="noticia-titulo center-align"><?= $noticia['titulo'] ?></h1>
        <?php if (!empty($noticia['subtitulo'])): ?>
            <h2 class="noticia-subtitulo center-align"><?= $noticia['subtitulo'] ?></h2>
        <?php endif; ?>
        <div class="noticia-data center-align grey-text text-darken-1" style="margin: 30px 0;">
            Publicado em: <?= $data_formatada ?>
            <?php if (!empty($noticia['autor'])): ?>
                <span style="margin-left: 20px;">
                    Por: <?= $noticia['autor'] ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="noticia-conteudo">
            <?= nl2br($noticia['corpo']) ?>
        </div>
    </main>
    <?php include_once "footer.php"; ?>
    <script src="../js/materialize.min.js"></script>
</body>
</html>
</html>