<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
// Busca dados atuais do usuário
$id = $_SESSION['user_id'];
$sql = "SELECT nome, preferencias, foto_perfil 
        FROM usuario 
        WHERE id_usuario = $id AND deleted_at IS NULL";
$resultado = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($resultado);
if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
// Processamento do formulário - Opção 2: Verificar se campos existem
if (isset($_POST['nome']) && isset($_POST['preferencias'])) {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $preferencias = mysqli_real_escape_string($conexao, $_POST['preferencias']);
    $foto_perfil = $usuario['foto_perfil'];
    // Validação do nome
    if ($nome === '') {
        header("Location: editar_perfil.php?error=O nome é obrigatório");
        exit;
    }
    // Processamento da foto (se enviada)
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
        $pasta = "../meu_perfil/fotos_perfil/";
        $nomeArquivo = md5(time());
        $nomeCompleto = $_FILES["foto_perfil"]["name"];
        $nomeSeparado = explode('.', $nomeCompleto);
        $ultimaPosicao = count($nomeSeparado) - 1;
        $extensao = $nomeSeparado[$ultimaPosicao];
        $nomeArquivoExtensao = $nomeArquivo . "." . $extensao;
        $extensao = strtolower($extensao);
        
        $tiposPermitidos = ["jpg", "png", "jpeg"];
        if (!in_array($extensao, $tiposPermitidos)) {
            header("Location: editar_perfil.php?error=Apenas JPG ou PNG são permitidos");
            exit;
        }
        // Tamanho máximo 5MB
        $tamanhoMaximo = 5 * 1024 * 1024;
        if ($_FILES["foto_perfil"]['size'] > $tamanhoMaximo) {
            header("Location: editar_perfil.php?error=Imagem muito grande (máx 5MB)");
            exit;
        }
        // Move arquivo
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }
        $feitoUpload = move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $pasta . $nomeArquivoExtensao);
        if ($feitoUpload) {
            // Remove foto antiga
            if ($foto_perfil && file_exists("../" . $foto_perfil)) {
                @unlink("../" . $foto_perfil);
            }
            $foto_perfil = "meu_perfil/fotos_perfil/" . $nomeArquivoExtensao;
        } else {
            header("Location: editar_perfil.php?error=Erro ao fazer upload da imagem");
            exit;
        }
    }
    // Atualiza no banco usando mysqli_query
    $sql_update = "UPDATE usuario SET nome = '$nome', preferencias = '$preferencias', foto_perfil = '$foto_perfil' WHERE id_usuario = $id";
    if (mysqli_query($conexao, $sql_update)) {
        $_SESSION['user_nome'] = $nome;
        // redireciona com success (pode ser string ou apenas ?success=1)
        header("Location: editar_perfil.php?success=Perfil atualizado com sucesso");
    } else {
        header("Location: editar_perfil.php?error=Erro ao salvar no banco: " . mysqli_error($conexao));
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Editar Perfil - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <h1 class="title-nac">Editar Perfil</h1>
    <?php if (isset($_GET['error'])): ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2">
                <?= htmlspecialchars($_GET['error'], ENT_QUOTES) ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="upload-container">
        <div class="upload-card">
            <h2 class="upload-title">Atualizar Perfil</h2>
            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- NOME -->
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required maxlength="150" class="validate"
                            placeholder="Seu novo nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>">
                        <label for="nome">Nome de usuário:</label>
                    </div>
                    <!-- BIO -->
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea" 
                            placeholder="Sua nova bio"><?= htmlspecialchars($usuario['preferencias'], ENT_QUOTES) ?></textarea>
                        <label for="preferencias">Bio:</label>
                    </div>
                    <!-- FOTO ATUAL -->
                    <div class="col s12">
                        <p><strong>Foto atual:</strong></p>
                        <?php 
                        $caminho_foto = !empty($usuario['foto_perfil']) ? "../" . $usuario['foto_perfil'] : '';
                        if ($caminho_foto && file_exists($caminho_foto)): 
                        ?>
                            <img src="<?= $caminho_foto ?>" alt="Foto atual" 
                                class="circle" 
                                style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #009688;">
                        <?php else: ?>
                            <div class="circle teal" 
                                style="width: 120px; height: 120px; line-height: 120px; font-size: 2.5rem; color: white; text-align: center;">
                            </div>
                            <p><em>Nenhuma foto</em></p>
                        <?php endif; ?>
                    </div>
                    <!-- NOVA FOTO -->
                    <div class="file-field input-field col s12">
                        <div class="btn teal darken-1">
                            <span>Nova Foto</span>
                            <input type="file" name="foto_perfil" accept="image/*">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" 
                                placeholder="Escolha uma nova foto">
                        </div>
                    </div>
                    <!-- BOTÕES -->
                    <div class="col s12" style="margin-top: 2rem; text-align: center;">
                        <button type="submit" class="btn waves-effect waves-light btn-upload">
                            Salvar Alterações
                        </button>
                        <a href="../meu_perfil/meu_perfil.php" class="btn waves-effect waves-light btn-cancel" style="margin-top: 2rem; text-align: center;">
                        Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal de sucesso: Perfil atualizado -->
    <div id="modalPerfilSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 4rem;">check_circle</i>
            <h5>Perfil atualizado com sucesso!</h5>
            <p>Suas alterações foram salvas.</p>
        </div>
        <div class="modal-footer">
            <a href="../meu_perfil/meu_perfil.php" class="modal-close waves-effect waves-green btn-flat">Ir para meu perfil</a>
        </div>
    </div>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // inicializa todos os modais da página
            var elems = document.querySelectorAll('.modal');
            M.Modal.init(elems, { dismissible: true });
            // abre o modal de sucesso se houver ?success=... na URL
            <?php if (isset($_GET['success'])): ?>
                (function() {
                    var el = document.getElementById('modalPerfilSucesso');
                    if (el) {
                        var inst = M.Modal.getInstance(el);
                        if (!inst) inst = M.Modal.init(el, { dismissible: false });
                        inst.open();
                    }
                })();
            <?php endif; ?>
        });
    </script>
</body>
</html>
