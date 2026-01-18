<?php
session_start(); 
// Se o usuário já estiver logado, redireciona para a página inicial
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
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css?v=<?= time() ?>" /> <!-- cache buster -->
</head>
<body>
    <h1 class="title-nac">NAC Portal</h1>
    <!-- Mensagem de erro -->
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2 center-align" style="max-width: 480px; margin: 25px auto;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Redefinir Senha</h2>
            
            <p class="center-align" style="margin: 0 0 2.5rem 0; color: #555; font-size: 1rem; line-height: 1.4;">
                Digite sua nova senha (mínimo 8 caracteres).
            </p>
            <form action="processa_redefinir_senha.php" method="POST">
                <div class="row">
                    <!-- Token oculto (obrigatório para segurança) -->
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                    <!-- Nova Senha -->
                    <div class="input-field col s12">
                        <input type="password" name="nova_senha" id="nova_senha" required class="validate"
                            placeholder="Nova senha (mín. 8 caracteres)">
                        <label for="nova_senha">Nova Senha</label>
                    </div>
                    <!-- Confirmar Senha -->
                    <div class="input-field col s12">
                        <input type="password" name="confirmar_senha" id="confirmar_senha" required class="validate"
                            placeholder="Confirme a nova senha">
                        <label for="confirmar_senha">Confirmar Senha</label>
                    </div>

                    <!-- Botão -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            REDEFINIR SENHA
                        </button>
                    </div>
                </div>
            </form>
            <!-- Link voltar -->
            <div class="login-links">
                <a href="../index.php">Voltar para o login</a>
            </div>
        </div>
    </div>
    <!-- Modal de sucesso -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 4.5rem;">check_circle</i>
            <h5>Senha alterada com sucesso!</h5>
            <p style="margin: 1.5rem 0; color: #555;">
                Sua senha foi atualizada. Agora você pode fazer login com a nova senha.
            </p>
        </div>
        <div class="modal-footer">
            <a href="../index.php" class="modal-close waves-effect waves-light btn teal">
                Ir para Login
            </a>
        </div>
    </div>
    <!-- Scripts -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.AutoInit();  // Inicializa todos os componentes (incluindo modais)
            // Abre modal de sucesso se ?success=1 na URL
            <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                const modal = M.Modal.getInstance(document.getElementById('modalSucesso'));
                if (modal) modal.open();
            <?php endif; ?>
        });
    </script>
</body>
</html>