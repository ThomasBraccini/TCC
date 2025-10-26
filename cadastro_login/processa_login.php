<?php
session_start();
require_once "../conexao.php";
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header("Location: ../index.php?erros=Preencha email e senha..");
    exit;
}

$query = "SELECT id_usuario, nome, senha, verificado FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $nome = $registro['nome'];
    $senhaHash = $registro['senha'];
    $verificado = $registro['verificado'];
    $usuario_encontrado = true;
} else {
    $usuario_encontrado = false;
}
mysqli_free_result($resultado);

if (! $usuario_encontrado) {
    header("Location: ../index.php?error=E-mail ou senha incorretos.");
    exit;
}
if ($verificado == 0) {
    header("Location: ../index.php?error=Você precisa verificar seu e-mail antes de fazer login.");
    exit;
}
if (password_verify($senha, $senhaHash)) {
    // Login OK
    $_SESSION['user_id'] = $id_usuario;
    $_SESSION['email'] = $email;
    $_SESSION['nome'] = $nome;
    header("Location: ../feed.php");    exit;
    } else {
        header("Location: ../index.php?error=E-mail ou senha incorretos.");
        exit;
    }
?>