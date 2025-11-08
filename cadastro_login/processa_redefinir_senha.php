<?php
session_start();
require_once "../conexao.php";
if (isset($_POST['token']) && isset($_POST['nova_senha']) && isset($_POST['confirmar_senha'])) {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
} else {
    $token = '';
    $nova_senha = '';
    $confirmar_senha = '';
}
// Validações básicas
if ($token === '') {
    header("Location: esqueci_senha.php?error=Token inválido.");
    exit;
}

if (strlen($nova_senha) < 8) {
    header("Location: redefinir_senha.php?token=$token&error=A senha deve ter no mínimo 8 caracteres.");
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    header("Location: redefinir_senha.php?token=$token&error=As senhas não conferem.");
    exit;
}
// Verifica se o token ainda é válido
$query = "SELECT id_usuario, token_expira_em FROM usuario WHERE token_recuperacao = '$token'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $token_expira = $registro['token_expira_em'];
    $encontrou = true;
    
    mysqli_free_result($resultado);
} else {
    $encontrou = false;
}
if (!$encontrou) {
    header("Location: esqueci_senha.php?error=Link inválido ou expirado.");
    exit;
}

if (time() > $token_expira) {
    header("Location: esqueci_senha.php?error=Link expirado. Solicite um novo.");
    exit;
}
// Atualiza a senha e limpa o token
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
$update = "UPDATE usuario SET senha = '$senha_hash', token_recuperacao = NULL, token_expira_em = NULL WHERE id_usuario = $id_usuario";
$resultado = mysqli_query($conexao, $update);
if ($resultado) {
    header("Location: redefinir_senha.php?success=1");
    exit;
} else {
    header("Location: redefinir_senha.php?token=$token&error=Erro ao redefinir senha. Tente novamente.");
    exit;
}
?>