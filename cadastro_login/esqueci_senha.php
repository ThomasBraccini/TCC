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
    <!-- TÍTULO -->
    <title>Esqueci a Senha - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2">
                <?= $_GET['error'] ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Recuperar Senha</h2>
                <p class="center-align" style="margin-bottom: 1.5rem; color: #555; font-size: 0.95rem;">
                    Digite seu e-mail para receber um link de recuperação.
                </p>
            <form action="processa_esqueci_senha.php" method="POST">
                <div class="row">
                    <!-- EMAIL -->
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate"
                            placeholder="seu.email@aluno.iffar.edu.br">
                        <label for="email">E-mail</label>
                    </div>
                    <!-- BOTÃO -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Enviar Link de Recuperação
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
    <!-- MODAL SUCESSO -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text">check_circle</i>
            <h5>Link enviado com sucesso</h5>
            <p>
                Se o e-mail informado estiver cadastrado, você receberá um link
                para redefinir sua senha em alguns instantes.
            </p>
        </div>
        <div class="modal-footer">
            <a href="esqueci_senha.php" class="modal-close btn teal">
                Ok
            </a>
        </div>
    </div>
    <!-- JS -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modais = document.querySelectorAll('.modal');
            M.Modal.init(modais);
            // Se veio ?success=1, abre o modal automaticamente
            const params = new URLSearchParams(window.location.search);
            if (params.get('success') === '1') {
                var modal = M.Modal.getInstance(
                    document.getElementById('modalSucesso')
                );
                modal.open();
            }
        });
    </script>
</body>
</html>