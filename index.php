<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: feed.php"); // CORRETO - home.php está na raiz
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - NAC Portal</title>
</head>
<body>
    Login:
    <form action="cadastro_login/processa_login.php" method="POST">
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>
        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>
        <input type="submit" value="Entrar">
    </form>
        <a href="cadastro_login/esqueci_senha.php">Esqueci minha senha</a><br>
        <a href="cadastro_login/registro.php">Criar Conta</a>
    </body>
</html>