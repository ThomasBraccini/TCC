<?php
session_start();
require_once "../conexao.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../feed.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$id_publicacao = $_GET['id'];
$redirect = '../feed.php';
if (isset($_GET['from']) && $_GET['from'] != '') {
    $redirect = $_GET['from'];
}

// Força como número para evitar erros no SQL
$id_usuario = (int)$id_usuario;
$id_publicacao = (int)$id_publicacao;

// Verifica se já está salvo
$sql_check = "SELECT 1 FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao";
$resultado = mysqli_query($conexao, $sql_check);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $delete_query = mysqli_query($conexao, "DELETE FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao");
    $acao = 'removido';
} else {
    $insert_query = mysqli_query($conexao, "INSERT INTO salvos (id_usuario, id_publicacao) VALUES ($id_usuario, $id_publicacao)");
    $acao = 'salvo';
}

$mensagem = $acao;

// Monta o redirect corretamente
if (strpos($redirect, '?') === false) {
    $redirect .= "?msg=$mensagem";
} else {
    $redirect .= "&msg=$mensagem";
}

header("Location: $redirect");
exit;