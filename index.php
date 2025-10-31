<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: feed.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Entrar no NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Entrar no NAC Portal</h2>
            <form action="cadastro_login/processa_login.php" method="POST">
                <div class="row">
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate" placeholder="seu.email@iffarroupilha.edu.br">
                        <label for="email">E-mail</label>
                    </div>
                    <div class="input-field col s12">
                        <input type="password" name="senha" id="senha" required class="validate">
                        <label for="senha">Senha</label>
                    </div>
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Entrar
                        </button>
                    </div>
                </div>
            </form>
            <div class="login-links">
                <a href="cadastro_login/registro.php">Não tem uma conta? Cadastre-se agora</a>
                <a href="cadastro_login/esqueci_senha.php">Esqueceu a senha?</a>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.updateTextFields();
        });
    </script>
</body>
</html>