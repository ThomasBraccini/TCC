<?php
session_start(); 
require_once "../conexao.php";
$id_alvo = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_alvo <= 0) {
    header("Location: ../feed.php");
    exit;
}
if (isset($_SESSION['user_id']) && $id_alvo == $_SESSION['user_id']) {
    header("Location: meu_perfil.php");
    exit;
}
$sql_usuario = "SELECT nome, email, preferencias, foto_perfil 
                FROM usuario 
                WHERE id_usuario = $id_alvo 
                AND deleted_at IS NULL";
$resultado = mysqli_query($conexao, $sql_usuario);
$usuario = mysqli_fetch_assoc($resultado);
if (!$usuario) {
    die('Usuário não encontrado.');
}
// BUSCA PUBLICAÇÕES DO USUÁRIO
$sql_publicacoes = "
    SELECT id_publicacao, titulo, caminho_arquivo, tipo_arquivo, descricao, 
        DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_fmt
    FROM publicacao 
    WHERE id_usuario_fk = $id_alvo 
    AND deleted_at IS NULL 
    ORDER BY data_publicacao DESC";
$result_publicacao = mysqli_query($conexao, $sql_publicacoes);
// Armazena todas as publicações em um array
$publicacoes = [];
while ($registro = mysqli_fetch_assoc($result_publicacao)) {
    $publicacoes[] = $registro;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $usuario['nome'] ?> - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
<?php include_once "../header.php"; ?>
<main class="container">
    <!-- CARD DE PERFIL DO USUÁRIO -->
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <div class="row valign-wrapper">
                        <!-- FOTO DE PERFIL -->
                        <div class="col s12 m3 center">
                            <?php 
                            // Define foto do usuário ou avatar padrão
                            $foto = $usuario['foto_perfil'];
                            $caminho = $foto ? "../" . $foto : "../imagens/avatar_padrao.png";
                            if ($foto && !file_exists($caminho)) $caminho = "../imagens/avatar_padrao.png";
                            ?>
                            <img src="<?= $caminho ?>" 
                                alt="Foto de <?= $usuario['nome'] ?>"
                                class="circle responsive-img"
                                style="width: 140px; height: 140px; object-fit: cover; border: 4px solid #009688;">
                        </div>
                        <!-- INFORMAÇÕES DO USUÁRIO -->
                        <div class="col s12 m9">
                            <h4><?= $usuario['nome'] ?></h4>
                            <p class="grey-text text-darken-1"><?= $usuario['email'] ?></p>
                            <!-- Preferências artísticas -->
                            <?php if (!empty($usuario['preferencias'])): ?>
                                <p><?= nl2br($usuario['preferencias']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABA DE PUBLICAÇÕES -->
    <div class="row">
        <div class="col s12 center">
            <ul class="tabs tabs-fixed-width">
                <li class="tab col s6">
                    <a class="active" href="#minhas">Publicações</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- LISTA DE PUBLICAÇÕES -->
    <div id="minhas" class="col s12">
        <!-- Caso não tenha publicações -->
        <?php if (empty($publicacoes)): ?>
            <div class="card-panel center teal lighten-5">
                <h5><?= $usuario['nome'] ?> ainda não publicou nada.</h5>
            </div>
        <?php else: ?>
            <div class="row" style="margin: 0 -0.75rem;">
                <!-- Loop das publicações -->
                <?php foreach ($publicacoes as $pub): 
                    $caminho = "../uploads/" . $pub['caminho_arquivo'];
                ?>
                    <div class="col s12 m6 l4" style="padding: 0.75rem;">
                        <div class="card feed-card hoverable">
                            <!-- MÍDIA DA PUBLICAÇÃO -->
                            <?php if ($pub['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho ?>" class="feed-img materialboxed" alt="<?= $pub['titulo'] ?>">
                                </div>
                            <?php elseif ($pub['tipo_arquivo'] == 'video'): ?>
                                <div class="feed-media-container">
                                    <video class="feed-video" controls preload="metadata">
                                        <source src="<?= $caminho ?>" type="video/mp4">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                </div>
                            <?php elseif ($pub['tipo_arquivo'] == 'audio'): ?>
                                <div class="feed-audio-container">
                                    <audio controls class="feed-audio">
                                        <source src="<?= $caminho ?>">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                </div>
                            <?php endif; ?>
                            <!-- CONTEÚDO DA PUBLICAÇÃO -->
                            <div class="card-content-feed">
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="autor-chip"><?= $usuario['nome'] ?></span>
                                    <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                        <?= $pub['data_fmt'] ?>
                                    </span>
                                </div>
                                <h3 class="card-title-feed"><?= $pub['titulo'] ?></h3>
                                <!-- Descrição da publicação -->
                                <?php if (!empty($pub['descricao'])): ?>
                                    <p class="feed-description"><?= nl2br($pub['descricao']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<!-- JS do Materialize -->
<script src="../js/materialize.min.js"></script>
<script>
    // Inicializa componentes do Materialize
    document.addEventListener('DOMContentLoaded', function() {
        M.Tabs.init(document.querySelectorAll('.tabs'));
        M.Materialbox.init(document.querySelectorAll('.materialboxed'));
    });
</script>
<?php include_once "../footer.php"; // Rodapé ?>
</body>
</html>
