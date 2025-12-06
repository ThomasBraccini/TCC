<?php
session_start();
require_once "../conexao.php";

$email = '';
$senha = '';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (isset($_POST['senha'])) {
    $senha = $_POST['senha'];
}

if ($email === '' || $senha === '') {
    header("Location: ../index.php?erro_email=1&erro_senha=1");
    exit;
}

$query = "SELECT id_usuario, nome, senha, verificado, is_admin 
        FROM usuario 
        WHERE email = '$email'";

$resultado = mysqli_query($conexao, $query);

if (!$resultado) {
    die("Erro na consulta: " . mysqli_error($conexao));
}

if (mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario   = $registro['id_usuario'];
    $nome         = $registro['nome'];
    $senhaHash    = $registro['senha'];
    $verificado   = $registro['verificado'];

    $is_admin = 0;
    if (!empty($registro['is_admin'])) {
        $is_admin = $registro['is_admin'];
    }

    $usuario_encontrado = true;
} else {
    $usuario_encontrado = false;
}

if (!$usuario_encontrado) {
    header("Location: ../index.php?erro_email=1");
    exit;
}

if ($verificado == 0) {
    header("Location: ../index.php?erro_email=1");
    exit;
}

if (password_verify($senha, $senhaHash)) {
    $_SESSION['user_id']  = $id_usuario;
    $_SESSION['email']    = $email;
    $_SESSION['nome']     = $nome;
    $_SESSION['is_admin'] = $is_admin;

    if ($is_admin == 1) {
        header("Location: ../administrador/index.php");
    } else {
        header("Location: ../feed.php");
    }
    exit;
} else {
    header("Location: ../index.php?erro_senha=1");
    exit;
}
?>
