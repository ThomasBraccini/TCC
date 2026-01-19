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
    <?php
    $tempo = time();
    ?>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css?v=<?php echo $tempo; ?>" />
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <?php
    if (isset($_GET['error'])) {
    ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2 center-align" style="max-width: 500px; margin: 20px auto;">
                <?php echo $_GET['error']; ?>
            </div>
        </div>
    <?php
    }
    ?>
    <?php
    if (isset($_GET['success'])) {
    ?>
        <div class="container">
            <div class="card-panel green lighten-4 green-text text-darken-1 center-align" style="max-width: 500px; margin: 20px auto;">
                <?php echo $_GET['success']; ?>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Verificação de E-mail</h2>
            <p style="margin-bottom: 1.5rem; color: #555; font-size: 0.95rem; text-align: center;">
                Copie e cole o código de 6 dígitos enviado para seu e-mail.
            </p>
            <p style="margin-bottom: 1.5rem; color: #00695c; font-weight: 500; text-align: center;">
                E-mail sendo verificado: <strong><?php echo $email; ?></strong>
            </p>
            <form action="processa_verificacao.php" method="POST">
                <div class="row">
                <div class="input-field col s12">
                    <input type="text" name="codigo" id="codigo" required maxlength="6"
                        pattern="[0-9]{6}" class="validate" placeholder="123456"
                        style="text-align: center; 
                                font-size: 1.6rem;            
                                letter-spacing: 1.5rem;       
                                font-weight: 400;           
                                padding: 0 3rem;              
                                font-family: 'Roboto', sans-serif; 
                                box-sizing: border-box;">
                    <label for="codigo">Código de 6 dígitos</label>
                </div>
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login" style="width: 100%; height: 52px;">
                            VERIFICAR E-MAIL
                        </button>
                    </div>
                </div>
            </form>
            <div class="login-links" style="margin-top: 2rem;">
                <a href="../index.php">Voltar para login</a>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.AutoInit();
        });
    </script>
</body>
</html>