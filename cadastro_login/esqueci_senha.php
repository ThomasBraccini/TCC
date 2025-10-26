<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Esqueci a Senha - NAC Portal</title>
</head>
<body>
    Recuperar Senha
    <?php
    if (isset($_GET['error'])) {
        echo '<p>' . $_GET['error'] . '</p>';
    }
    ?>
    Digite seu e-mail para receber um link de recuperação:
    <form action="processa_esqueci_senha.php" method="POST">
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required>
        <input type="submit" value="Enviar Link de Recuperação">
    </form>
    <a href="../index.php"> Voltar para login</a>
</body>
</html>