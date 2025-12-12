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
    <?php if (isset($_GET['success'])): ?>
        <div class="container">
            <div class="card-panel green lighten-4 green-text text-darken-1">
                <?= $_GET['success'] ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Criar Conta</h2>
            <form action="processa_registro.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- NOME -->
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required class="validate" maxlength="150" 
                            placeholder="Nome de Usuário">
                        <label for="nome">Nome de Usuário:</label>
                    </div>
                    <!-- EMAIL -->
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate" maxlength="255"
                            placeholder="seu.email@aluno.iffar.edu.br">
                        <label for="email">E-mail</label>
                        <span id="erro-email" style="color:red; font-size:13px;"></span>
                    </div>
                    <!-- SENHA -->
                    <div class="input-field col s12">
                        <input type="password" data-length="20" name="senha" id="senha" required class="validate" minlength="8"
                            placeholder="Senha">
                        <label for="senha">Senha (mín. 8 caracteres)</label>
                        <span id="erro-senha" style="color:red; font-size:13px;"></span>
                    </div>
                    <!-- CONFIRMAR SENHA -->
                    <div class="input-field col s12">
                        <input type="password" data-length="20" name="confirma_senha" id="confirma_senha" required class="validate" minlength="8"
                            placeholder="Confirmar Senha">
                        <label for="confirma_senha">Confirmar Senha</label>
                        <span id="erro-confirma" style="color:red; font-size:13px;"></span>
                    </div>
                    <!-- BIO (TEXTAREA) -->
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea validate"
                                placeholder="O que você gosta..."></textarea>
                        <label for="preferencias">Bio</label>
                    </div>
                    <!-- FOTO DE PERFIL -->
                    <div class="file-field input-field col s12">
                        <div class="btn btn-login">
                            <span>Foto</span>
                            <input type="file" name="foto_perfil" accept="image/*">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Escolha uma foto">
                        </div>
                    </div>
                    <!-- BOTÃO CADASTRAR -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Cadastrar
                        </button>
                    </div>
                </div>
            </form>
            <!-- LINKS -->
            <div class="login-links">
                <a href="../index.php">Já tem conta? Faça login</a>
            </div>
        </div>
    </div>
    <!-- JS -->
    <!-- MODAL: Email já cadastrado -->
    <div id="modalEmailExistente" class="modal">
        <div class="modal-content">
            <h4>Atenção</h4>
            <p>O e-mail informado já está cadastrado em nosso sistema.</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn teal">Entendi</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.modal');
        var instances = M.Modal.init(elems);

        <?php if (isset($_GET['email_existente'])): ?>
            var modal = document.getElementById('modalEmailExistente');
            var instance = M.Modal.getInstance(modal);
            instance.open();
        <?php endif; ?>
    });
    </script>

    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa Materialize
            M.updateTextFields();
            M.textareaAutoResize(document.getElementById('preferencias')); // Expande textarea
            // Validação no envio
            form.addEventListener('submit', function(e) {
                if (senha.value !== confirma.value) {
                    e.preventDefault();
                    M.toast({html: 'As senhas não coincidem!', classes: 'red'});
                } else if (senha.value.length < 8) {
                    e.preventDefault();
                    M.toast({html: 'A senha deve ter no mínimo 8 caracteres!', classes: 'red'});
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const email = document.getElementById("email");
            const senha = document.getElementById("senha");
            const confirma = document.getElementById("confirma_senha");

            const erroEmail = document.getElementById("erro-email");
            const erroSenha = document.getElementById("erro-senha");
            const erroConfirma = document.getElementById("erro-confirma");

            // EMAIL INVÁLIDO
            email.addEventListener("input", function() {
                const valido = email.checkValidity();
                erroEmail.textContent = valido ? "" : "E-mail inválido";
            });

            // SENHA CURTA
            senha.addEventListener("input", function() {
                erroSenha.textContent = senha.value.length < 8 ? 
                    "A senha deve ter no mínimo 8 caracteres" : "";
            });

            // SENHAS DIFERENTES
            function validarSenhas() {
                if (senha.value !== confirma.value) {
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