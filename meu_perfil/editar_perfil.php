<?php
session_start(); 
require_once "../conexao.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$id = $_SESSION['user_id'];
// Busca os dados atuais do usuário no banco
$sql = "SELECT nome, preferencias, foto_perfil 
        FROM usuario 
        WHERE id_usuario = $id AND deleted_at IS NULL";
$resultado = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($resultado);
// Se não encontrar o usuário, desloga e volta pro login
if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
// Processa o formulário quando enviado
if (isset($_POST['nome']) && isset($_POST['preferencias'])) {
        $nome = $_POST['nome'];
        $preferencias = $_POST['preferencias'];
    $foto_perfil = $usuario['foto_perfil'];
    // Processa upload de nova foto
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
        $pasta = "../meu_perfil/fotos_perfil/";
        $nomeArquivo = md5(time());
        $nomeCompleto = $_FILES["foto_perfil"]["name"];
        $nomeSeparado = explode('.', $nomeCompleto);
        $ultimaPosicao = count($nomeSeparado) - 1;
        $extensao = strtolower($nomeSeparado[$ultimaPosicao]);
        // Tipos de imagem permitidos
        $tiposPermitidos = ["jpg", "png", "jpeg"];
        if (!in_array($extensao, $tiposPermitidos)) {
            header("Location: editar_perfil.php?error=Apenas JPG ou PNG são permitidos");
            exit;
        }
        // Limite de tamanho: 5MB
        $tamanhoMaximo = 5 * 1024 * 1024;
        if ($_FILES["foto_perfil"]['size'] > $tamanhoMaximo) {
            header("Location: editar_perfil.php?error=Imagem muito grande (máx 5MB)");
            exit;
        }
        $nomeFinal = $nomeArquivo . "." . $extensao;
        $caminhoFinal = $pasta . $nomeFinal;
        // Move o arquivo temporário para a pasta final
        if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $caminhoFinal)) {
            // Remove a foto antiga, se existir
            if ($foto_perfil && file_exists("../" . $foto_perfil)) {
                @unlink("../" . $foto_perfil);
            }
            // Atualiza o caminho da nova foto
            $foto_perfil = "meu_perfil/fotos_perfil/" . $nomeFinal;
        } else {
            header("Location: editar_perfil.php?error=Erro ao fazer upload da imagem");
            exit;
        }
    }
    // Atualiza os dados no banco de dados
    $sql_update = "UPDATE usuario 
                    SET nome = '$nome', 
                        preferencias = '$preferencias', 
                        foto_perfil = '$foto_perfil' 
                    WHERE id_usuario = $id";
    if (mysqli_query($conexao, $sql_update)) {
        // Atualiza o nome na sessão para refletir imediatamente
        $_SESSION['user_nome'] = $nome;
        header("Location: editar_perfil.php?success=Perfil atualizado com sucesso");
    } else {
        header("Location: editar_perfil.php?error=Erro ao salvar no banco");
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
    <div class="upload-container">
        <div class="upload-card">
            <h2 class="upload-title">Atualizar Perfil</h2>
            <!-- Formulário para editar nome, bio e foto -->
            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Campo Nome -->
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required maxlength="150" class="validate"
                            value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>">
                        <label for="nome">Nome de usuário:</label>
                    </div>
                    <!-- Campo Bio -->
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea">
                            <?= htmlspecialchars($usuario['preferencias'], ENT_QUOTES) ?>
                        </textarea>
                        <label for="preferencias">Bio:</label>
                    </div>
                    <!-- Exibe foto atual do usuário -->
                    <div class="col s12">
                        <p><strong>Foto atual:</strong></p>
                        <?php 
                            // Define o caminho da foto
                            if (!empty($usuario['foto_perfil'])) {
                                $caminho_foto = "../" . $usuario['foto_perfil'];
                            } else {
                                $caminho_foto = '';
                            }
                            // Verifica se a foto existe no servidor
                            if ($caminho_foto && file_exists($caminho_foto)) { 
                        ?>
                            <img src="<?= $caminho_foto ?>" alt="Foto atual" 
                                class="circle" 
                                style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #009688;">
                        <?php 
                            } else { 
                        ?>
                            <div class="circle teal" 
                                style="width: 120px; height: 120px; line-height: 120px; font-size: 2.5rem; color: white; text-align: center;">
                                <?= strtoupper(substr($usuario['nome'], 0, 2)) // Mostra as duas primeiras letras do nome ?>
                            </div>
                            <p><em>Nenhuma foto</em></p>
                        <?php 
                        } 
                        ?>
                    </div>
                    <!-- Campo para enviar nova foto -->
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
                    <!-- Botões Salvar e Cancelar -->
                    <div class="col s12" style="margin-top: 2rem; text-align: center;">
                        <button type="submit" class="btn waves-effect waves-light btn-upload">
                            Salvar Alterações
                        </button>
                        <a href="meu_perfil.php" class="btn waves-effect waves-light btn-cancel">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal que aparece quando o perfil é atualizado com sucesso -->
    <div id="modalPerfilSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 4rem;">check_circle</i>
            <h5>Perfil atualizado com sucesso!</h5>
            <p>Suas alterações foram salvas.</p>
        </div>
        <div class="modal-footer">
            <a href="meu_perfil.php" class="modal-close waves-effect waves-green btn-flat">
                Ir para meu perfil
            </a>
        </div>
    </div>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <!-- Inicializa modais e abre o de sucesso automaticamente -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.modal');
            M.Modal.init(elems, { dismissible: true });
            // Se veio com ?success na URL, abre o modal de confirmação
            <?php if (isset($_GET['success'])): ?>
                (function() {
                    var el = document.getElementById('modalPerfilSucesso');
                    if (el) {
                        var inst = M.Modal.getInstance(el);
                        inst.open();
                    }
                })();
            <?php endif; ?>
        });
    </script>
</body>
</html>