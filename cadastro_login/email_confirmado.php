<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['email_verificacao'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>E-mail Verificado - NAC Portal</title>
    </head>
    <body>
            E-mail Verificado com Sucesso!
                Seu e-mail foi verificado com sucesso. Agora você pode fazer login no sistema.
            <a href="../index.php"> Ir para Login</a>
    </body>
</html>