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
    
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .logo-if img {
            height: 60px;
            margin-right: 15px;
        }
        .logo-text {
            color: white;
            font-weight: 700;
            font-size: 1.4rem;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .title-nac {
            background: #00897b; 
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            font-size: 2.2rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 420px;
        }
        .login-title {
            font-size: 1.6rem;
            font-weight: 500;
            color: #00695c;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .input-field input {
            font-size: 1rem !important;
            padding: 0 1rem !important;
        }
        .input-field label {
            color: #555;
            font-size: 1rem;
        }
        .btn-login {
            background: #00897b;
            width: 100%;
            height: 48px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1.5rem 0;
        }
        .btn-login:hover {
            background: #00695c;
        }
        .login-links {
            text-align: center;
            margin-top: 1rem;
        }
        .login-links a {
            color: #00897b;
            font-size: 0.95rem;
            display: block;
            margin: 0.5rem 0;
        }
        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
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