<?php
session_start();
require_once "../conexao.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../feed.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$id_publicacao = (int)$_GET['id'];
$redirect = $_GET['from'] ?? '../feed.php';

// Verifica se já está salvo
$sql_check = "SELECT 1 FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao";
$result = mysqli_query($conexao, $sql_check);

if (mysqli_num_rows($result) > 0) {
    // REMOVE
    mysqli_query($conexao, "DELETE FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao");
    $acao = 'removido';
} else {
    // SALVA
    mysqli_query($conexao, "INSERT INTO salvos (id_usuario, id_publicacao) VALUES ($id_usuario, $id_publicacao)");
    $acao = 'salvo';
}

// Volta com mensagem
$redirect .= (strpos($redirect, '?') === false ? '?' : '&') . "msg=$acao";
header("Location: $redirect");
exit;