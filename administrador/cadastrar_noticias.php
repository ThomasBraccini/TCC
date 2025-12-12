<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}
$mensagem = "";
$classeMensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo     = $_POST['titulo'];
    $subtitulo  = $_POST['subtitulo'] ?? '';  // Novo campo
    $autor      = $_POST['autor'] ?? '';      // Novo campo
    $conteudo   = $_POST['conteudo'];
    $id_autor   = $_SESSION['user_id'];       // Pode usar como referência se quiser

    if (empty($titulo) || empty($conteudo)) {
        $mensagem = "Preencha o título e o conteúdo!";
        $classeMensagem = "red";
    } else {
        $nomeImagem = null;
        if (!empty($_FILES['imagem_capa']['name'])) {
            $extensao = strtolower(pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION));
            $nomeImagem = uniqid('noticia_') . '.' . $extensao;
            $destino = "../uploads/noticias/" . $nomeImagem;
            move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $destino);
        }

        // INSERT atualizado com subtitulo e autor (campos existentes na tabela)
        $sql = "INSERT INTO noticias 
                (titulo, subtitulo, autor, corpo, caminho_midia, data_publicacao, ativo) 
                VALUES 
                ('$titulo', '$subtitulo', '$autor', '$conteudo', '$nomeImagem', NOW(), 1)";

        if (mysqli_query($conexao, $sql)) {
            $mensagem = "Notícia publicada com sucesso!";
            $classeMensagem = "green";
            $sucesso = true;
            // Limpar campos após sucesso
            $_POST = [];
        } else {
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

                    <?php if (!empty($mensagem)): ?>
                        <div class="card-panel <?= $classeMensagem ?> lighten-5 <?= $classeMensagem ?>-text text-darken-4" style="border-radius:10px;">
                            <i class="material-icons left">
                                <?= ($classeMensagem == 'green') ? 'check_circle' : 'info' ?>
                            </i>
                            <?= $mensagem ?>
                        </div>
                    <?php endif; ?>

                    <form id="formNoticia" method="POST" enctype="multipart/form-data">
                        <div class="input-field col s12">
                            <input name="titulo" type="text" required placeholder="Título da Notícia"
                                value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
                        </div>

                        <!-- Campo novo: Subtítulo -->
                        <div class="input-field col s12">
                            <input name="subtitulo" type="text" placeholder="Subtítulo (opcional)"
                                value="<?= isset($_POST['subtitulo']) ? htmlspecialchars($_POST['subtitulo']) : '' ?>">
                        </div>

                        <!-- Campo novo: Autor -->
                        <div class="input-field col s12">
                            <input name="autor" type="text" placeholder="Autor (opcional)"
                                value="<?= isset($_POST['autor']) ? htmlspecialchars($_POST['autor']) : '' ?>">
                        </div>

                        <div class="input-field col s12">
                            <textarea name="conteudo" class="materialize-textarea" required placeholder="Conteúdo da notícia"
                                style="min-height:300px;"><?= isset($_POST['conteudo']) ? htmlspecialchars($_POST['conteudo']) : '' ?></textarea>
                        </div>

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
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);

    <?php if ($sucesso): ?>
        var instance = M.Modal.getInstance(document.getElementById('modalSucesso'));
        instance.open();
    <?php endif; ?>

    document.getElementById('btnAbrirConfirmacao').addEventListener('click', function() {
        M.Modal.getInstance(document.getElementById('modalConfirmarCadastro')).open();
    });

    document.getElementById('btnConfirmarEnvio').addEventListener('click', function() {
        document.getElementById('formNoticia').submit();
    });
});
</script>
</body>
</html>