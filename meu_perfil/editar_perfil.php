<?php
session_start();
require_once "../conexao.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Busca dados atuais do usuário
$sql = "SELECT nome, preferencias, foto_perfil FROM usuario WHERE id_usuario = ? AND deleted_at IS NULL";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $preferencias = trim($_POST['preferencias'] ?? '');
    $foto_perfil = $usuario['foto_perfil']; // mantém a atual

    // Validação
    if ($nome === '') {
        $mensagem = "O nome é obrigatório.";
    } else {
        // Upload da nova foto (se enviada)
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            $pasta = "../meu_perfil/fotos_perfil/";
            $nomeArquivo = md5(time() . rand());
            $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png'];

            if (!in_array($extensao, $permitidos)) {
                $mensagem = "Apenas JPG ou PNG são permitidos.";
            } elseif ($_FILES['foto_perfil']['size'] > 5 * 1024 * 1024) {
                $mensagem = "Imagem muito grande (máx 5MB).";
            } else {
                $nomeFinal = $nomeArquivo . "." . $extensao;
                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $pasta . $nomeFinal)) {
                    // Remove foto antiga
                    if ($foto_perfil && file_exists("../" . $foto_perfil)) {
                        unlink("../" . $foto_perfil);
                    }
                    $foto_perfil = "meu_perfil/fotos_perfil/" . $nomeFinal;
                } else {
                    $mensagem = "Erro ao fazer upload da imagem.";
                }
            }
        }

        // Se não houver erro, atualiza no banco
        if ($mensagem === '') {
            $sql_update = "UPDATE usuario SET nome = ?, preferencias = ?, foto_perfil = ? WHERE id_usuario = ?";
            $stmt_update = mysqli_prepare($conexao, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "sssi", $nome, $preferencias, $foto_perfil, $_SESSION['user_id']);

            if (mysqli_stmt_execute($stmt_update)) {
                $mensagem = "Perfil atualizado com sucesso!";
                $_SESSION['user_nome'] = $nome; // opcional: atualiza na sessão
            } else {
                $mensagem = "Erro ao salvar no banco.";
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - NAC Portal</title>
</head>
<body>
    <h2>Editar Perfil</h2>

    <?php if ($mensagem): ?>
        <p style="color: <?= strpos($mensagem, 'sucesso') !== false ? 'green' : 'red' ?>; font-weight: bold;">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
        <label for="nome">Nome de usuário:</label><br>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required maxlength="150"><br><br>

        <label for="preferencias">Bio (opcional):</label><br>
        <textarea name="preferencias" id="preferencias" rows="4" cols="50"><?= htmlspecialchars($usuario['preferencias']) ?></textarea><br><br>

        <p><strong>Foto atual:</strong></p>
        <?php 
        $caminho_foto = !empty($usuario['foto_perfil']) ? "../" . $usuario['foto_perfil'] : '';
        if ($caminho_foto && file_exists($caminho_foto)): 
        ?>
            <img src="<?= $caminho_foto ?>" alt="Foto atual" width="120" height="120" style="object-fit: cover; border-radius: 50%; border: 2px solid #009688;"><br><br>
        <?php else: ?>
            <p><em>Nenhuma foto</em></p>
        <?php endif; ?>

        <label for="foto_perfil">Nova foto (opcional):</label><br>
        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*"><br><br>

        <input type="submit" value="Salvar Alterações">
    </form>

    <p><a href="meu_perfil.php">Voltar ao perfil</a></p>
</body>
</html>