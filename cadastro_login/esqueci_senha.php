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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Esqueci a Senha - NAC Portal</title>
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css?v=<?= time() ?>" /> <!-- cache buster temporário -->
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2 center-align" style="max-width: 480px; margin: 20px auto;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Recuperar Senha</h2>
            
            <p class="center-align" style="margin: 0 0 2.5rem 0; color: #555; font-size: 1rem; line-height: 1.4;">
                Digite seu e-mail institucional para receber um link de recuperação.
            </p>
            <form action="processa_esqueci_senha.php" method="POST">
                <div class="row">
                    <!-- CAMPO EMAIL -->
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate"
                            placeholder="seu.email@iffarroupilha.edu.br">
                        <label for="email">E-mail</label>
                    </div>
                    <!-- BOTÃO -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            ENVIAR LINK DE RECUPERAÇÃO
                        </button>
                    </div>
                </div>
            </form>
            <!-- LINK VOLTAR -->
            <div class="login-links">
                <a href="../index.php">Voltar para o login</a>
            </div>
        </div>
    </div>
    <!-- MODAL SUCESSO -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text">check_circle</i>
            <h5>Link enviado com sucesso</h5>
            <p style="margin: 1.5rem 0;">
                Se o e-mail informado estiver cadastrado, você receberá um link para redefinir sua senha em alguns instantes.
            </p>
        </div>
        <div class="modal-footer">
            <a href="esqueci_senha.php" class="modal-close waves-effect waves-light btn teal">
                Ok
            </a>
        </div>
    </div>
    <!-- Scripts -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            M.AutoInit();  // Inicializa todos os componentes Materialize
            // Abre modal de sucesso automaticamente se ?success=1
            const params = new URLSearchParams(window.location.search);
            if (params.get('success') === '1') {
                const modal = M.Modal.getInstance(document.getElementById('modalSucesso'));
                modal.open();
            }
        });
    </script>
</boy>
</html>