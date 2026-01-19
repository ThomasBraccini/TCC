<?php
session_start(); 
if (isset($_SESSION['user_id'])) {
    header("Location: ../feed.php");
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Cadastrar no NAC Portal</title>
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
            <div class="card-panel red lighten-4 red-text text-darken-2 center-align" style="max-width: 480px; margin: 25px auto; padding: 20px;">
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
            <div class="card-panel green lighten-4 green-text text-darken-2 center-align" style="max-width: 480px; margin: 25px auto; padding: 20px;">
                <?php echo $_GET['success']; ?>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Criar Conta</h2>
            <form action="processa_registro.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required class="validate" maxlength="150"
                            placeholder="Nome de Usuário">
                        <label for="nome">Nome de Usuário</label>
                    </div>
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate" maxlength="255"
                            placeholder="seu.email@iffarroupilha.edu.br">
                        <label for="email">E-mail</label>
                        <span id="erro-email" style="color:red; font-size:13px;"></span>
                    </div>
                    <div class="input-field col s12">
                        <input type="password" name="senha" id="senha" required class="validate" minlength="8"
                            placeholder="Senha (mín. 8 caracteres)">
                        <label for="senha">Senha</label>
                        <span id="erro-senha" style="color:red; font-size:13px;"></span>
                    </div>
                    <div class="input-field col s12">
                        <input type="password" name="confirma_senha" id="confirma_senha" required class="validate" minlength="8"
                            placeholder="Confirme a senha">
                        <label for="confirma_senha">Confirmar Senha</label>
                        <span id="erro-confirma" style="color:red; font-size:13px;"></span>
                    </div>
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea validate"
                                placeholder="Conte um pouco sobre você..."></textarea>
                        <label for="preferencias">Bio (opcional)</label>
                    </div>
                    <div class="file-field input-field col s12">
                        <div class="btn btn-login">
                            <span>Foto de Perfil</span>
                            <input type="file" name="foto_perfil" accept="image/*">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Nenhuma foto selecionada">
                        </div>
                    </div>
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            CADASTRAR
                        </button>
                    </div>
                </div>
            </form>
            <div class="login-links">
                <a href="../index.php">Já tem conta? Faça login</a>
            </div>
        </div>
    </div>
    <div id="modalEmailExistente" class="modal">
        <div class="modal-content">
            <h4>Atenção</h4>
            <p>O e-mail informado já está cadastrado. Use outro ou faça login.</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-light btn teal">Entendi</a>
        </div>
    </div>
    <div id="modalCodigoEnviado" class="modal">
        <div class="modal-content">
            <h4>Cadastro iniciado</h4>
            <p>Código enviado! Verifique seu e-mail para concluir o cadastro.</p>
        </div>
        <div class="modal-footer">
            <a href="verificar_email.php" class="modal-close waves-effect waves-light btn teal">OK</a>
        </div>
    </div>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.AutoInit();
            M.updateTextFields();
            M.textareaAutoResize(document.getElementById('preferencias'));
            <?php
            if (isset($_GET['email_existente'])) {
            ?>
                M.Modal.getInstance(document.getElementById('modalEmailExistente')).open();
            <?php
            }
            ?>
            <?php
            if (isset($_GET['success'])) {
            ?>
                M.Modal.getInstance(document.getElementById('modalCodigoEnviado')).open();
            <?php
            }
            ?>
            var form = document.querySelector('form');
            var senha = document.getElementById('senha');
            var confirma = document.getElementById('confirma_senha');
            form.addEventListener('submit', function(e) {
                if (senha.value !== confirma.value) {
                    e.preventDefault();
                    M.toast({html: 'As senhas não coincidem!', classes: 'red rounded'});
                } else {
                    if (senha.value.length < 8) {
                        e.preventDefault();
                        M.toast({html: 'Senha deve ter no mínimo 8 caracteres!', classes: 'red rounded'});
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var email = document.getElementById("email");
            var senha = document.getElementById("senha");
            var confirma = document.getElementById("confirma_senha");
            var erroEmail = document.getElementById("erro-email");
            var erroSenha = document.getElementById("erro-senha");
            var erroConfirma = document.getElementById("erro-confirma");
            email.addEventListener("input", function() {
                if (email.checkValidity()) {
                    erroEmail.textContent = "";
                } else {
                    erroEmail.textContent = "E-mail inválido";
                }
            });
            senha.addEventListener("input", function() {
                if (senha.value.length < 8) {
                    erroSenha.textContent = "Mínimo 8 caracteres";
                } else {
                    erroSenha.textContent = "";
                }
            });
            function validarSenhas() {
                if (senha.value && confirma.value && senha.value !== confirma.value) {
                    erroConfirma.textContent = "As senhas não conferem";
                } else {
                    erroConfirma.textContent = "";
                }
            }
            senha.addEventListener("input", validarSenhas);
            confirma.addEventListener("input", validarSenhas);
        });
    </script>
</body>
</html>
