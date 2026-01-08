<?php
session_start();
if (isset($_GET['email_verificado']) && $_GET['email_verificado'] == 1) {
    $_SESSION['modal_sucesso'] = [
        'titulo' => 'E-mail Verificado!',
        'mensagem' => 'Seu e-mail foi verificado com sucesso. Agora você pode fazer login.',
        'botao' => 'Ir para Login',
        'link' => 'index.php'
    ];
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
    <style>
        #senha {
            background-repeat: no-repeat;
            background-position: right 40px center;
            background-size: 25px;
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
                    <!-- CAMPO EMAIL -->
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate" 
                            placeholder="seu.email@iffarroupilha.edu.br">
                        <label for="email">E-mail</label>
                        <?php if (isset($_GET['erro_email'])): ?>
                            <span style="color:red; font-size:14px;">E-mail incorreto.</span>
                        <?php endif; ?>
                    </div>
                    <!-- CAMPO SENHA -->
                    <div class="input-field col s12">
                        <input type="password" name="senha" id="senha" required class="validate" placeholder="Sua senha">
                        <label for="senha">Senha</label>
                        <?php if (isset($_GET['erro_senha'])): ?>
                            <span style="color:red; font-size:14px;">Senha incorreta.</span>
                        <?php endif; ?>
                    </div>
                    <!-- BOTÃO -->
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
    <!-- Modal existente (não alterado, pois você pediu sem modal agora) -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content">
            <h4 id="modalTitulo"></h4>
            <p id="modalMensagem"></p>
        </div>
        <div class="modal-footer">
            <a href="#" id="modalBotao" class="modal-close waves-effect waves-green btn-flat"></a>
        </div>
    </div>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var senha = document.getElementById('senha');
            senha.style.backgroundImage = "url('./img_senha/olho_fechado.svg')";    
            senha.onclick = function(event) {
                var larguraCampo = senha.offsetWidth;
                var posClique = event.offsetX;
                if (larguraCampo - posClique < 65) {
                    if (senha.type === 'password') {
                        senha.type = 'text';
                        senha.style.backgroundImage = "url('./img_senha/olho_aberto.svg')";
                    } else {
                        senha.type = 'password';
                        senha.style.backgroundImage = "url('./img_senha/olho_fechado.svg')";
                    }
                }
            };
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalElems = document.querySelectorAll('.modal');
            var modalInstances = M.Modal.init(modalElems);
            <?php if (isset($_SESSION['modal_sucesso'])): ?>
                var modalData = <?= json_encode($_SESSION['modal_sucesso']) ?>;
                document.getElementById('modalTitulo').textContent = modalData.titulo;
                document.getElementById('modalMensagem').textContent = modalData.mensagem;
                document.getElementById('modalBotao').textContent = modalData.botao;
                document.getElementById('modalBotao').href = modalData.link;
                var modalInstance = M.Modal.getInstance(document.getElementById('modalSucesso'));
                modalInstance.open();
                <?php unset($_SESSION['modal_sucesso']); ?>
            <?php endif; ?>
        });
    </script>
    <?php include "footer.php"; ?>
</body>
</html>
