<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../conexao.php";

// Detecta sucesso (ex: processa_publicacao.php redireciona para esta página com ?success=1)
$mostrar_sucesso = isset($_GET['success']) && $_GET['success'] == '1';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Publicar Arte - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <h1 class="title-nac">Publicar Nova Obra</h1>
    <div class="upload-container">
        <div class="upload-card">
            <h2 class="upload-title">Compartilhe sua Arte</h2>
            <form action="processa_publicacao.php" method="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- TÍTULO -->
                    <div class="input-field col s12">
                        <input type="text" name="titulo" id="titulo" required maxlength="255" class="validate"
                        placeholder="Título da Obra">
                    </div>
                    <!-- DESCRIÇÃO -->
                    <div class="input-field col s12">
                        <input type="text" name="descricao" id="descricao" required maxlength="500" class="validate"
                        placeholder="Descrição da Obra">
                    </div>
                    <!-- ARQUIVO -->
                    <div class="file-field input-field col s12">
                        <div class="btn teal darken-1">
                            <span>Escolher arquivo</span>
                            <input type="file" name="arquivo" required 
                                accept="image/*,video/mp4,video/webm,audio/mpeg,audio/wav">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" 
                                placeholder="Nenhum arquivo selecionado">
                        </div>
                    </div>
                    <!-- BOTÕES -->
                    <div class="col s12" style="margin-top: 2rem; text-align: center;">
                        <button type="submit" class="btn waves-effect waves-light btn-upload">
                            Publicar Obra
                        </button>
                        <a href="../meu_perfil/meu_perfil.php" class="btn waves-effect waves-light btn-cancel" style="margin-top: 2rem; text-align: center;">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL DE SUCESSO -->
    <?php if ($mostrar_sucesso): ?>
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 4rem;">check_circle</i>
            <h5>Publicação realizada com sucesso!</h5>
            <p>Sua obra foi publicada e está disponível no feed.</p>
        </div>
        <div class="modal-footer">
            <a href="../feed.php" class="modal-close waves-effect waves-green btn-flat">Ir para o Feed</a>
        </div>
    </div>
    <?php endif; ?>
    <script src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa componentes do Materialize
            M.AutoInit();
            <?php if ($mostrar_sucesso): ?>
                // Inicializa e abre o modal de sucesso de forma robusta
                var elem = document.getElementById('modalSucesso');
                if (elem) {
                    var instance = M.Modal.init(elem, { dismissible: false });
                    instance.open();
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
