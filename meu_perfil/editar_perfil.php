<?php
session_start(); 
require_once "../conexao.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$id = $_SESSION['user_id'];
$sql = "SELECT nome, preferencias, foto_perfil 
        FROM usuario 
        WHERE id_usuario = $id AND deleted_at IS NULL";
$resultado = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($resultado);
if ($usuario == null) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nome'])) {
        $nome = trim($_POST['nome']);
    } else {
        $nome = '';
    }
    if (isset($_POST['preferencias'])) {
        $preferencias = trim($_POST['preferencias']);
    } else {
        $preferencias = '';
    }
    $foto_perfil = $usuario['foto_perfil'];
    if (isset($_FILES['foto_perfil'])) {
        if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $pasta = "../meu_perfil/fotos_perfil/";
            $nomeArquivo = md5(time() . uniqid());
            $extensao = strtolower(pathinfo($_FILES["foto_perfil"]["name"], PATHINFO_EXTENSION));
            $tiposPermitidos = array("jpg", "jpeg", "png");
            if (!in_array($extensao, $tiposPermitidos)) {
                header("Location: editar_perfil.php?error=Apenas JPG, JPEG ou PNG");
                exit;
            }
            $tamanhoMaximo = 5 * 1024 * 1024;
            if ($_FILES["foto_perfil"]['size'] > $tamanhoMaximo) {
                header("Location: editar_perfil.php?error=Imagem muito grande (máx 5MB)");
                exit;
            }
            $nomeFinal = $nomeArquivo . "." . $extensao;
            $caminhoFinal = $pasta . $nomeFinal;
            if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $caminhoFinal)) {
                if ($foto_perfil != '') {
                    if (file_exists("../" . $foto_perfil)) {
                        unlink("../" . $foto_perfil);
                    }
                }
                $foto_perfil = "meu_perfil/fotos_perfil/" . $nomeFinal;
            } else {
                header("Location: editar_perfil.php?error=Erro no upload");
                exit;
            }
        }
    }
    $sql_update = "
        UPDATE usuario 
        SET nome = '$nome',
            preferencias = '$preferencias',
            foto_perfil = '$foto_perfil'
        WHERE id_usuario = $id
    ";
    $resultado_update = mysqli_query($conexao, $sql_update);
    if ($resultado_update) {
        $_SESSION['user_nome'] = $nome;
        header("Location: editar_perfil.php?success=Perfil atualizado com sucesso");
    } else {
        header("Location: editar_perfil.php?error=Erro ao salvar");
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
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css?v=<?php echo time(); ?>" />
</head>
<body>
    <h1 class="title-nac">Editar Perfil</h1>
    <?php if (isset($_GET['error'])) { ?>
        <div class="container">
            <div class="card-panel red lighten-4 red-text text-darken-2 center-align" style="max-width: 500px; margin: 20px auto;">
                <?php echo $_GET['error']; ?>
            </div>
        </div>
    <?php } ?>
    <div class="upload-container">
        <div class="upload-card">
            <h2 class="upload-title">Atualizar Perfil</h2>
            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="input-field col s12">
                        <input type="text" name="nome" id="nome" required maxlength="150" class="validate"
                            value="<?php echo $usuario['nome']; ?>">
                        <label for="nome">Nome de usuário</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea name="preferencias" id="preferencias" class="materialize-textarea validate"><?php
                            echo $usuario['preferencias'];
                        ?></textarea>
                        <label for="preferencias">Bio</label>
                    </div>
                    <div class="col s12 center-align" style="margin: 2rem 0;">
                        <p style="font-weight: 500; margin-bottom: 1rem;">Foto atual</p>
                        <?php
                        if ($usuario['foto_perfil'] != '') {
                            $foto_path = "../" . $usuario['foto_perfil'];
                        } else {
                            $foto_path = '';
                        }
                        if ($foto_path != '' && file_exists($foto_path)) {
                        ?>
                            <img src="<?php echo $foto_path; ?>" alt="Foto de perfil"
                                class="circle responsive-img"
                                style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #00897b; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <?php
                        } else {
                        ?>
                            <div class="circle teal"
                                style="width: 140px; height: 140px; line-height: 140px; font-size: 3.5rem; color: white; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <?php
                                if ($usuario['nome'] != '') {
                                    echo strtoupper(substr($usuario['nome'], 0, 2));
                                } else {
                                    echo 'U';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="file-field input-field col s12">
                        <div class="btn teal darken-1">
                            <span>Nova Foto</span>
                            <input type="file" name="foto_perfil" accept="image/*">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Escolha uma nova foto">
                        </div>
                    </div>
                    <div class="col s12 center-align" style="margin-top: 3rem;">
                        <button type="submit" class="btn waves-effect waves-light btn-upload" style="margin-right: 1.5rem; min-width: 220px;">
                            SALVAR ALTERAÇÕES
                        </button>
                        <a href="meu_perfil.php" class="btn waves-effect waves-light btn-cancel" style="min-width: 180px;">
                            CANCELAR
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="modalPerfilSucesso" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text" style="font-size: 5rem;">check_circle</i>
            <h5>Perfil atualizado!</h5>
            <p style="margin-top: 1rem;">Suas alterações foram salvas com sucesso.</p>
        </div>
        <div class="modal-footer">
            <a href="meu_perfil.php" class="modal-close waves-effect waves-light btn teal">
                Ir para meu perfil
            </a>
        </div>
    </div>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.AutoInit();
            M.textareaAutoResize(document.getElementById('preferencias'));
            <?php if (isset($_GET['success'])) { ?>
                M.Modal.getInstance(document.getElementById('modalPerfilSucesso')).open();
            <?php } ?>
        });
    </script>
</body>
</html>
