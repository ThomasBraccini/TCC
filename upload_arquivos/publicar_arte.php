<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
    exit;
}
require_once "../conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Publicar Arte - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <div>
        <h2>Publicar Nova Obra de Arte</h2>
        <form action="processa_publicacao.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="titulo"><strong>Título da Obra:</strong></label><br>
                <input type="text" name="titulo" id="titulo" required maxlength="255" placeholder="Ex: Paisagem Abstrata">
            </div>
            <div>
                <label for="descricao"><strong>Descrição:</strong></label><br>
                <textarea name="descricao" id="descricao" rows="4" placeholder="Descrição"></textarea>
            </div>
            <div>
                <label for="arquivo"><strong>Arquivo da Obra (Imagem, Vídeo ou Áudio):</strong></label><br>
                <input type="file" name="arquivo" id="arquivo" required>
            </div>
            <div>
                <input type="submit" value="Publicar Obra">
                <a href="../feed.php" style="margin-left: 10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>