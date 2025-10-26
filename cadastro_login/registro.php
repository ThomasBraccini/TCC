<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Realizar Cadastro - NAC Portal</title>
</head>
<body>
    <h2>Realizar Cadastro</h2>

    <form action="processa_registro.php" method="POST">
        <label for="nome">Nome completo:</label><br>
        <input type="text" name="nome" id="nome" required maxlength="150"><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required maxlength="255"><br><br>

        <label for="senha">Senha (mínimo 8 caracteres):</label><br>
        <input type="password" name="senha" id="senha" required minlength="8"><br><br>

        <label for="confirm_senha">Confirmar senha:</label><br>
        <input type="password" name="confirma_senha" id="confirm_senha" required minlength="8"><br><br>

        <label for="preferencias">Preferências (opcional):</label><br>
        <textarea name="preferencias" id="preferencias" rows="4" cols="40"></textarea><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <p><a href="../index.php">Voltar para login</a></p>
    </body>
</html>