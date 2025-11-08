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
    <title>Redefinir Senha - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <!-- Exibe erros (se houver) -->
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2">
                <?= htmlspecialchars($_GET['error'], ENT_QUOTES) ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Redefinir Senha</h2>
            <p class="center-align" style="margin-bottom: 1.5rem; color: #555; font-size: 0.95rem;">
                Digite sua nova senha.
            </p>
            <form action="processa_redefinir_senha.php" method="POST">
                <div class="row">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES) ?>">
                    <div class="input-field col s12">
                        <input type="password" name="nova_senha" id="nova_senha" required class="validate"
                            placeholder="Nova senha (mínimo 8 caracteres)">
                        <label for="nova_senha">Nova Senha</label>
                    </div>
                    <div class="input-field col s12">
                        <input type="password" name="confirmar_senha" id="confirmar_senha" required class="validate"
                            placeholder="Confirme sua nova senha">
                        <label for="confirmar_senha">Confirmar Senha</label>
                    </div>
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Redefinir Senha
                        </button>
                    </div>
                </div>
            </form>
            <div class="login-links">
                <a href="../index.php">Voltar para login</a>
            </div>
        </div>
    </div>
    <!-- === Modal de sucesso (adicionado) === -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 4rem;">check_circle</i>
            <h5>Senha alterada com sucesso!</h5>
            <p>Sua senha foi atualizada. Agora você pode fazer login com a nova senha.</p>
        </div>
        <div class="modal-footer">
            <a href="../index.php" class="modal-close waves-effect waves-green btn-flat">Ir para Login</a>
        </div>
    </div>
    <!-- JS -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa explicitamente o modal
            var elem = document.getElementById('modalSucesso');
            if (elem) {
                M.Modal.init(elem, { dismissible: false });
            }
            // Abre o modal somente se success=1 estiver presente
            <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                // Pega a instância e abre
                var instance = M.Modal.getInstance(document.getElementById('modalSucesso'));
                if (instance) {
                    instance.open();
                } else {
                    // fallback: inicializa e abre
                    var el = document.getElementById('modalSucesso');
                    var inst = M.Modal.init(el, { dismissible: false });
                    if (inst) inst.open();
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
