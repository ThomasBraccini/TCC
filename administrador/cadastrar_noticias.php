<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id']) or !isset($_SESSION['is_admin']) or $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}
$mensagem = "";
$classeMensagem = "";
$sucesso = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inicializa variáveis com valor vazio para evitar erros
    $titulo    = '';
    $subtitulo = '';
    $autor     = '';
    $conteudo  = '';
    // Atribui valores do POST apenas se existirem 
    if (isset($_POST['titulo'])) {
        $titulo = $_POST['titulo'];
    }
    if (isset($_POST['subtitulo'])) {
        $subtitulo = $_POST['subtitulo'];
    }
    if (isset($_POST['autor'])) {
        $autor = $_POST['autor'];
    }
    if (isset($_POST['conteudo'])) {
        $conteudo = $_POST['conteudo'];
    }
    // Validação básica: título e conteúdo são obrigatórios
    if (empty($titulo) || empty($conteudo)) {
        $mensagem = "Preencha o título e o conteúdo!";
        $classeMensagem = "red";
    } else {
        $nomeImagem = null;
        // Processa upload da imagem apenas se um arquivo foi enviado
        if (!empty($_FILES['imagem_capa']['name'])) {
            // Extrai extensão em minúsculas
            $extensao = strtolower(pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION));
            // Gera nome único para evitar sobrescrita
            $nomeImagem = uniqid('noticia_') . '.' . $extensao;
            // Define caminho final no servidor
            $destino = "../uploads/noticias/" . $nomeImagem;
            // Move o arquivo temporário para a pasta definitiva
            move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $destino);
        }
        $sql = "INSERT INTO noticias 
                (titulo, subtitulo, autor, corpo, caminho_midia, data_publicacao, ativo) 
                VALUES 
                ('$titulo', '$subtitulo', '$autor', '$conteudo', '$nomeImagem', NOW(), 1)";
        if (mysqli_query($conexao, $sql)) {
            $sucesso = true;         // Usado para abrir modal de sucesso
            $_POST = [];             // Limpa os campos do formulário após sucesso
        } else {
            // Exibe erro detalhado do MySQL
            $mensagem = "Erro ao salvar: " . mysqli_error($conexao);
            $classeMensagem = "red";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Notícia • Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link rel="stylesheet" href="../css/style_todos.css">
</head>
<body class="grey lighten-4">
    <?php include_once "header.php"; ?>
    <!-- MODAL SUCESSO -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text">check_circle</i>
            <h5>Notícia cadastrada com sucesso!</h5>
        </div>
        <div class="modal-footer">
            <a href="cadastrar_noticias.php" class="modal-close btn green">OK</a>
        </div>
    </div>
    <!-- MODAL CONFIRMAÇÃO -->
    <div id="modalConfirmarCadastro" class="modal">
        <div class="modal-content center">
            <h5>Confirmar publicação</h5>
            <p>Tem certeza que deseja cadastrar esta notícia?</p>
        </div>
        <div class="modal-footer">
            <a class="modal-close btn grey">Cancelar</a>
            <button id="btnConfirmarEnvio" class="btn teal">Cadastrar</button>
        </div>
    </div>
    <main class="container">
        <div class="row">
            <div class="col s12">
                <div class="card white">
                    <div class="card-content">
                        <h4 class="teal-text text-darken-2 center" style="margin-bottom:30px;">
                            Cadastrar Notícia
                        </h4>
                        <form id="formNoticia" method="POST" enctype="multipart/form-data">
                            <!--Título -->
                            <div class="input-field col s12">
                                <input name="titulo" type="text" required placeholder="Título da Notícia"
                                    value="<?php
                                            if (isset($_POST['titulo'])) {
                                                echo $_POST['titulo'];
                                            }
                                        ?>">
                            </div>
                            <!--Subtítulo -->
                            <div class="input-field col s12">
                                <input name="subtitulo" type="text" placeholder="Subtítulo (opcional)"
                                    value="<?php
                                            if (isset($_POST['subtitulo'])) {
                                                echo $_POST['subtitulo'];
                                            }
                                        ?>">
                            </div>
                            <!-- Autor -->
                            <div class="input-field col s12">
                                <input name="autor" type="text" placeholder="Autor (opcional)"
                                    value="<?php
                                            if (isset($_POST['autor'])) {
                                                echo $_POST['autor'];
                                            }
                                        ?>">
                            </div>
                            <!-- Conteúdo -->
                            <div class="input-field col s12">
                                <textarea name="conteudo" class="materialize-textarea" required placeholder="Conteúdo da notícia"
                                    style="min-height:300px;"><?php
                                        if (isset($_POST['conteudo'])) {
                                            echo $_POST['conteudo'];
                                        }
                                    ?></textarea>
                            </div>
                            <!-- Imagem de Capa -->
                            <div class="file-field input-field col s12">
                                <div class="btn teal lighten-1">
                                    <span>Imagem de Capa</span>
                                    <input type="file" name="imagem_capa" accept="image/*">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Imagem opcional">
                                </div>
                            </div>
                            <div class="row center" style="margin-top:40px;">
                                <div class="col s12 m6">
                                    <a href="index.php" class="btn-large grey">Voltar</a>
                                </div>
                                <div class="col s12 m6">
                                    <button type="button" id="btnAbrirConfirmacao" class="btn-large teal">Salvar Notícia</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../js/materialize.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa todos os modais do Materialize existentes na página
        var modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
        <?php if ($sucesso): ?>
            // Abre automaticamente o modal de sucesso após o cadastro
            var instance = M.Modal.getInstance(document.getElementById('modalSucesso'));
            instance.open();
        <?php endif; ?>
        // Abre o modal de confirmação ao clicar em "Salvar Notícia"
        document.getElementById('btnAbrirConfirmacao').addEventListener('click', function() {
            M.Modal.getInstance(document.getElementById('modalConfirmarCadastro')).open();
        });
        // Envia o formulário após o usuário confirmar a ação
        document.getElementById('btnConfirmarEnvio').addEventListener('click', function() {
            document.getElementById('formNoticia').submit();
        });
    });
    </script>
    <?php include_once "footer.php"; ?>
</body>
</html>