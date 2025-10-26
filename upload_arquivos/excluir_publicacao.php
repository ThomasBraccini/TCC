<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Pega o ID da publicação a ser excluída
$id_publicacao = $_GET["id"];
$id_usuario = $_SESSION['user_id'];

// Busca a publicação para verificar se pertence ao usuário
$sql = "SELECT * FROM publicacao WHERE id_publicacao = '$id_publicacao'";
$result = mysqli_query($conexao, $sql);
$publicacao = mysqli_fetch_assoc($result);

// Verifica se a publicação existe e pertence ao usuário logado
if (!$publicacao) {
    header("Location: ../feed.php?error=Publicação não encontrada!");
    exit;
}

if ($publicacao['id_usuario_fk'] != $id_usuario) {
    header("Location: ../feed.php?error=Você não tem permissão para excluir esta publicação!");
    exit;
}

// Exclui o arquivo físico
$caminho_arquivo = "../uploads/" . $publicacao["caminho_arquivo"];
if (file_exists($caminho_arquivo)) {
    $deletou = unlink($caminho_arquivo);
    if (!$deletou) {
        header("Location: ../feed.php?error=Erro ao excluir o arquivo!");
        exit;
    }
}

// Exclui a publicação do banco de dados
$sql = "DELETE FROM publicacao WHERE id_publicacao = '$id_publicacao'";
$result = mysqli_query($conexao, $sql);

if ($result) {
    header("Location: ../feed.php?success=Publicação excluída com sucesso!");
} else {
    header("Location: ../feed.php?error=Erro ao excluir publicação do banco!");
}
exit;
?>