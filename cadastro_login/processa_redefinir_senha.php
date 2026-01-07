<?php
session_start();
require_once "../conexao.php"; 
// Pega os dados enviados pelo formulário
if (isset($_POST['token']) && isset($_POST['nova_senha']) && isset($_POST['confirmar_senha'])) {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
} else {
    // Se algum campo faltar, define como vazio
    $token = '';
    $nova_senha = '';
    $confirmar_senha = '';
}
// Validação: token não pode estar vazio
if ($token === '') {
    header("Location: esqueci_senha.php?error=Token inválido.");
    exit; 
}
// Validação: senha deve ter pelo menos 8 caracteres
if (strlen($nova_senha) < 8) {
    header("Location: redefinir_senha.php?token=$token&error=A senha deve ter no mínimo 8 caracteres.");
    exit;
}
// Validação: as duas senhas precisam ser iguais
if ($nova_senha !== $confirmar_senha) {
    header("Location: redefinir_senha.php?token=$token&error=As senhas não conferem.");
    exit;
}
// Busca o usuário pelo token de recuperação
$query = "SELECT id_usuario, token_expira_em FROM usuario WHERE token_recuperacao = '$token'";
$resultado = mysqli_query($conexao, $query);
// Verifica se encontrou um usuário com esse token
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $token_expira = $registro['token_expira_em']; // Timestamp de expiração
    $encontrou = true;
    mysqli_free_result($resultado); // Libera memória
} else {
    $encontrou = false;
}
// Se o token não existe no banco
if (!$encontrou) {
    header("Location: esqueci_senha.php?error=Link inválido ou expirado.");
    exit;
}
// Verifica se o token já expirou (compara tempo atual com o de expiração)
if (time() > $token_expira) {
    header("Location: esqueci_senha.php?error=Link expirado. Solicite um novo.");
    exit;
}
// Criptografa a nova senha de forma segura
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
// Atualiza a senha e remove o token (torna o link inválido após uso)
$update = "UPDATE usuario SET senha = '$senha_hash', token_recuperacao = NULL, token_expira_em = NULL WHERE id_usuario = $id_usuario";
$resultado = mysqli_query($conexao, $update);
// Se atualizou com sucesso
if ($resultado) {
    header("Location: redefinir_senha.php?success=1"); 
    exit;
} else {
    header("Location: redefinir_senha.php?token=$token&error=Erro ao redefinir senha. Tente novamente.");
    exit;
}
?>