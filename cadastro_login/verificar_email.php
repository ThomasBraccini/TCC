<?php
session_start();
if (!isset($_SESSION['email_verificacao'])) {
    header("Location: registro.php");
    exit;
}
$email = $_SESSION['email_verificacao'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Verificar E-mail - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <!-- TÍTULO NAC -->
    <h1 class="title-nac">NAC Portal</h1>
    <!-- MENSAGENS DE ERRO/SUCESSO -->
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2">
                <?= $_GET['error'] ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="container">
            <div class="card-panel green lighten-4 green-text text-darken-1">
                <?= $_GET['success'] ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- CONTAINER DO FORMULÁRIO -->
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Verificação de E-mail</h2>
            <p  style="margin-bottom: 1.5rem; color: #555; font-size: 0.95rem;">
                Copie e cole o código de 6 dígitos enviado para seu e-mail.
            </p>
            <p style="margin-bottom: 1rem; color: #00695c; font-weight: 500;">
                E-mail sendo verificado: <strong><?= $email ?></strong>
            </p>
            <!-- FORMULÁRIO -->
            <form action="processa_verificacao.php" method="POST">
                <div class="row">
                    <!-- CÓDIGO DE 6 DÍGITOS -->
                    <div class="input-field col s12">
                        <input type="text" name="codigo" id="codigo" required maxlength="6" 
                            pattern="[0-9]{6}" class="validate" placeholder="123456"
                            style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
                        <label for="codigo">Digite o código de 6 dígitos</label>
                    </div>
                    <!-- BOTÃO -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Verificar E-mail
                        </button>
                    </div>
                </div>
            </form>
            <!-- LINK VOLTAR -->
            <div class="login-links">
                <a href="../index.php">Voltar para login</a>
            </div>
        </div>
    </div>
    <!-- JS -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
</body>
</html>