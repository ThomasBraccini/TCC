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
    <!-- Mensagem de erro vinda da URL (?error=...) -->
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
                <?= $_GET['error']?>
            </div>
        </div>
    <?php endif; ?>
    <!-- Mensagem de sucesso vinda da URL -->
    <?php if (isset($_GET['success'])): ?>
        <div class="container">
                <?= $_GET['success'] ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Criar Conta</h2>
            <form action="processa_registro.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Campo Nome de Usuário -->
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required class="validate" maxlength="150" 
                            placeholder="Nome de Usuário">
                        <label for="nome">Nome de Usuário:</label>
                    </div>
                    <!-- Campo E-mail -->
                    <div class="input-field col s12">
                        <input type="email" name="email" id="email" required class="validate" maxlength="255"
                            placeholder="seu.email@aluno.iffar.edu.br">
                        <label for="email">E-mail</label>
                        <span id="erro-email" style="color:red; font-size:13px;"></span> <!-- Mensagem de erro em tempo real -->
                    </div>
                    <!-- Campo Senha -->
                    <div class="input-field col s12">
                        <input type="password" name="senha" id="senha" required class="validate" minlength="8"
                            placeholder="Senha">
                        <label for="senha">Senha (mín. 8 caracteres)</label>
                        <span id="erro-senha" style="color:red; font-size:13px;"></span>
                    </div>
                    <!-- Confirmação de Senha -->
                    <div class="input-field col s12">
                        <input type="password" name="confirma_senha" id="confirma_senha" required class="validate" minlength="8"
                            placeholder="Confirmar Senha">
                        <label for="confirma_senha">Confirmar Senha</label>
                        <span id="erro-confirma" style="color:red; font-size:13px;"></span>
                    </div>
                    <!-- Campo Bio (opcional) -->
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea validate"
                                placeholder="O que você gosta..."></textarea>
                        <label for="preferencias">Bio</label>
                    </div>
                    <!-- Upload de foto de perfil -->
                    <div class="file-field input-field col s12">
                        <div class="btn btn-login">
                            <span>Foto</span>
                            <input type="file" name="foto_perfil" accept="image/*"> <!-- Aceita apenas imagens -->
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Escolha uma foto">
                        </div>
                    </div>
                    <!-- Botão de envio do formulário -->
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light btn-login">
                            Cadastrar
                        </button>
                    </div>
                </div>
            </form>
            <div class="login-links">
                <a href="../index.php">Já tem conta? Faça login</a>
            </div>
        </div>
    </div>
    <!-- Modal exibido quando o e-mail já existe -->
    <div id="modalEmailExistente" class="modal">
        <div class="modal-content">
            <h4>Atenção</h4>
            <p>O e-mail informado já está cadastrado em nosso sistema.</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn teal">Entendi</a>
        </div>
    </div>
    <!-- Modal: Código de verificação enviado -->
    <div id="modalCodigoEnviado" class="modal">
        <div class="modal-content">
            <h4>Cadastro iniciado</h4>
            <p>Código enviado! Verifique seu e-mail para concluir o cadastro.</p>
        </div>
        <div class="modal-footer">
            <a href="verificar_email.php" class="modal-close btn teal">OK</a>
        </div>
    </div>
    <!-- Inicializa modais do Materialize -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.modal');
            M.Modal.init(elems);
            <?php if (isset($_GET['email_existente'])): ?>
                M.Modal.getInstance(
                    document.getElementById('modalEmailExistente')
                ).open();
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                M.Modal.getInstance(
                    document.getElementById('modalCodigoEnviado')
                ).open();
            <?php endif; ?>
        });
    </script>
    <!-- Carrega o JavaScript do Materialize -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <!-- Validação básica antes do envio -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.updateTextFields(); // Atualiza labels dos campos
            M.textareaAutoResize(document.getElementById('preferencias'));
            const form = document.querySelector('form');
            const senha = document.getElementById('senha');
            const confirma = document.getElementById('confirma_senha');
            // Impede envio se as senhas não baterem ou forem curtas
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
    <!-- Validação em tempo real enquanto o usuário digita -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const email = document.getElementById("email");
            const senha = document.getElementById("senha");
            const confirma = document.getElementById("confirma_senha");
            const erroEmail = document.getElementById("erro-email");
            const erroSenha = document.getElementById("erro-senha");
            const erroConfirma = document.getElementById("erro-confirma");
            // Verifica formato do e-mail
            email.addEventListener("input", function() {
                const valido = email.checkValidity();
                erroEmail.textContent = valido ? "" : "E-mail inválido";
            });
            // Verifica tamanho mínimo da senha
            senha.addEventListener("input", function() {
                erroSenha.textContent = senha.value.length < 8 ? 
                    "A senha deve ter no mínimo 8 caracteres" : "";
            });
            // Compara as duas senhas
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