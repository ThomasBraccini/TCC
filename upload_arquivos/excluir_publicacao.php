<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$id_publicacao = $_GET["id"] ?? 0;
if ($id_publicacao <= 0) {
    header("Location: ../feed.php?error=ID inválido!");
    exit;
}
$id_usuario = $_SESSION['user_id'];
$sql = "SELECT * FROM publicacao WHERE id_publicacao = '$id_publicacao'";
$result = mysqli_query($conexao, $sql);
$publicacao = mysqli_fetch_assoc($result);
if (!$publicacao) {
    header("Location: ../feed.php?error=Publicação não encontrada!");
    exit;
}
if ($publicacao['id_usuario_fk'] != $id_usuario) {
    header("Location: ../feed.php?error=Você não tem permissão para excluir esta publicação!");
    exit;
}
$caminho_arquivo = "../uploads/" . $publicacao["caminho_arquivo"];
if (file_exists($caminho_arquivo)) {
    if (!unlink($caminho_arquivo)) {
        header("Location: ../feed.php?error=Erro ao excluir o arquivo!");
        exit;
    }
}
// Exclui do banco
$sql = "DELETE FROM publicacao WHERE id_publicacao = '$id_publicacao'";
$result = mysqli_query($conexao, $sql);
if ($result) {
    header("Location: ../feed.php?success=1");
} else {
    header("Location: ../feed.php?error=Erro ao excluir do banco!");
}
exit;
?>