<?php
session_start();
require_once "../conexao.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../feed.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$id_publicacao = $_GET['id'];

// Define para onde voltar (prioridade para o 'from', senão volta para o feed na raiz)
$redirect = '../feed.php';  // padrão seguro
if (isset($_GET['from']) && !empty($_GET['from'])) {
    // Se veio do feed.php (que está na raiz), usa o caminho relativo correto
    if ($_GET['from'] === 'feed.php') {
        $redirect = '../feed.php';
    } else {
        $redirect = $_GET['from'];
    }
}

// Verifica se já está salvo
$sql_check = "SELECT 1 FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao";
$resultado = mysqli_query($conexao, $sql_check);

if (mysqli_num_rows($resultado) > 0) {
    // Já está salvo → REMOVE (descurtir)
    mysqli_query($conexao, "DELETE FROM salvos WHERE id_usuario = $id_usuario AND id_publicacao = $id_publicacao");
} else {
    // Não está salvo → INSERE (curtir)
    mysqli_query($conexao, "INSERT INTO salvos (id_usuario, id_publicacao) VALUES ($id_usuario, $id_publicacao)");
}

// Redireciona de volta para a página correta
header("Location: $redirect");
exit;