<?php
// processar_denuncia.php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['categoria']) || !isset($_POST['id_publicacao'])) {
    header("Location: index.php");
    exit;
}
$id_publicacao = $_POST['id_publicacao'];
$categoria = mysqli_real_escape_string($conexao, $_POST['categoria']);
$id_usuario = $_SESSION['user_id'];

// Verificar se já existe denúncia pendente do mesmo usuário para esta publicação
$check = mysqli_query($conexao, 
    "SELECT id_denuncia FROM denuncia 
        WHERE id_publicacao = $id_publicacao 
        AND id_usuario = $id_usuario 
        AND status = 'pendente'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['denuncia_msg'] = "Você já denunciou esta publicação. Aguarde a análise.";
} else {
    // Inserir nova denúncia
    $sql = "INSERT INTO denuncia (id_publicacao, id_usuario, categoria, status, data_denuncia) 
            VALUES ($id_publicacao, $id_usuario, '$categoria', 'pendente', NOW())";
    
    if (mysqli_query($conexao, $sql)) {
        $_SESSION['denuncia_msg'] = "Denúncia registrada com sucesso!";
    } else {
        $_SESSION['denuncia_msg'] = "Erro ao registrar denúncia: " . mysqli_error($conexao);
    }
}
// Redirecionar de volta para o feed com mensagem de sucesso
header("Location: feed.php?denuncia_ok=1");
exit;
?>