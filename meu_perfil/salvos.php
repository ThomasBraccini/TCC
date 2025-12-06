<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../feed.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$id_publicacao = $_GET['id'];          // deixa como string mesmo (vai vir número mesmo)
$redirect = '../feed.php';
if (isset($_GET['from']) && $_GET['from'] != '') {
    $redirect = $_GET['from'];
}
// Verifica se já está salvo
$sql_check = "SELECT 1 FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao";
$resultado = mysqli_query($conexao, $sql_check);
if (mysqli_num_rows($resultado) > 0) {
    mysqli_query($conexao, "DELETE FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao");
    $acao = 'removido';
} else {
    mysqli_query($conexao, "INSERT INTO salvos (id_usuario, id_publicacao) VALUES ($id_usuario, $id_publicacao)");
    $acao = 'salvo';
}
// Volta com mensagem
// Sempre adiciona com & no final
$redirect = $redirect . "&msg=$mensagem";
// Troca o primeiro & por ? só se ele estiver logo depois de não ter nada (ou seja, era uma URL sem parâmetros)
$redirect = str_replace('?&msg=', '?msg=', $redirect);
header("Location: $redirect");
exit;