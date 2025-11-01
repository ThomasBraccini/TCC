<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$sql_usuario = "SELECT 
                    nome, 
                    email, 
                    preferencias
                FROM usuario 
                WHERE id_usuario = {$_SESSION['user_id']} 
                AND deleted_at IS NULL";
$dados_usuario = mysqli_query($conexao, $sql_usuario);
if (!$dados_usuario || mysqli_num_rows($dados_usuario) === 0) {
    session_destroy();
    header("Location: ../index.php?error=Usuário não encontrado.");
    exit;
}
$usuario = mysqli_fetch_assoc($dados_usuario);
$preferencias = $usuario['preferencias'];
$sql_publicacoes = "SELECT 
                        id_publicacao, 
                        titulo, 
                        caminho_arquivo, 
                        tipo_arquivo,
                        descricao,
                    DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_pub_fmt
                    FROM publicacao 
                    WHERE id_usuario_fk = {$_SESSION['user_id']} 
                    AND deleted_at IS NULL
                    ORDER BY data_publicacao DESC";
$dados_publicacoes = mysqli_query($conexao, $sql_publicacoes);
$publicacoes = [];
while ($publicacao = mysqli_fetch_assoc($dados_publicacoes)) {
    $publicacoes[] = $publicacao;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Meu Perfil - NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
<?php include_once "../header.php"; ?>
<main class="container">
    <!-- PERFIL -->
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s12 m3 center">
                            <div class="avatar circle teal" style="width: 100px; height: 100px; line-height: 100px; font-size: 3rem; margin: 0 auto;">
                            </div>
                        </div>
                        <div class="col s12 m9">
                            <h4><?= $usuario['nome'] ?></h4>
                            <p class="grey-text text-darken-1"><?= $usuario['email'] ?></p>
                            <?php if (!empty($preferencias)): ?>
                                <p><?= nl2br($preferencias) ?></p>
                            <?php endif; ?>
                            <a href="../editar_perfil.php" class="btn waves-effect waves-light teal">Editar Perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABAS -->
    <div class="row">
        <div class="col s12">
            <ul class="tabs">
                <li class="tab col s6"><a class="active" href="#minhas">Minhas Publicações</a></li>
                <li class="tab col s6"><a href="#curtidos">Vídeos Curtidos</a></li>
            </ul>
        </div>
    </div>
    <!-- MINHAS PUBLICAÇÕES-->
    <div id="minhas" class="col s12">
        <?php if (empty($publicacoes)): ?>
            <div class="card-panel center no-content">
                <i class="material-icons large grey-text">image_search</i>
                <p>Você ainda não publicou nenhuma obra.</p>
                <a href="../upload_arquivos/publicar_arte.php" class="btn waves-effect waves-light teal">Publicar sua primeira arte</a>
            </div>
        <?php else: ?>
            <div class="row" style="margin: 0 -0.75rem;">
                <?php foreach ($publicacoes as $publicacao): ?>
                    <?php 
                    $caminho = "../uploads/" . $publicacao['caminho_arquivo'];
                    $titulo  = $publicacao['titulo'];
                    $colClass = 'feed-col col s12 m6 l4';
                    ?>
                    <div class="<?= $colClass ?>" style="padding: 0.75rem;">
                        <div class="card feed-card">
                            <!-- MÍDIA (EXATAMENTE IGUAL AO FEED) -->
                            <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho ?>" class="feed-img materialboxed" alt="<?= $titulo ?>">
                                </div>
                            <?php elseif ($publicacao['tipo_arquivo'] == 'video'): ?>
                                <div class="feed-media-container">
                                    <video class="feed-video" controls preload="metadata" 
                                        poster="../uploads/thumbnail_<?= pathinfo($publicacao['caminho_arquivo'], PATHINFO_FILENAME) ?>.jpg">
                                        <source src="<?= $caminho ?>" type="video/mp4">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                </div>
                            <?php elseif ($publicacao['tipo_arquivo'] == 'audio'): ?>
                                <div class="feed-audio-container">
                                    <i class="material-icons audio-icon">audiotrack</i>
                                    <audio class="feed-audio" controls>
                                        <source src="<?= $caminho ?>" type="audio/mpeg">
                                        <source src="<?= $caminho ?>" type="audio/wav">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                </div>
                            <?php endif; ?>
                            <!-- CONTEÚDO (IGUAL AO FEED) -->
                            <div class="card-content-feed">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="autor-chip"><?= $usuario['nome'] ?></span>
                                    <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                        <?= $publicacao['data_pub_fmt'] ?>
                                    </span>
                                </div>
                                <h3 class="card-title-feed"><?= $titulo ?></h3>
                                <?php if (!empty($publicacao['descricao'])): ?>
                                    <p class="feed-description"><?= nl2br($publicacao['descricao']) ?></p>
                                <?php endif; ?>
                                <!-- BOTÃO EXCLUIR -->
                                <div class="delete-btn">
                                    <a href="#modal-delete-<?= $publicacao['id_publicacao'] ?>" 
                                    class="modal-trigger btn-small red waves-effect waves-light">
                                        <i class="material-icons left">delete</i> Excluir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL EXCLUIR -->
                    <div id="modal-delete-<?= $publicacao['id_publicacao'] ?>" class="modal">
                        <div class="modal-content">
                            <h5>Excluir Publicação?</h5>
                            <p>Você está prestes a <strong>excluir permanentemente</strong>:</p>
                            <p class="truncate"><strong>"<?= $titulo ?>"</strong></p>
                            <p><small>Esta ação não pode ser desfeita.</small></p>
                        </div>
                        <div class="modal-footer">
                            <a href="#!" class="modal-close waves-effect btn-flat">Cancelar</a>
                            <a href="../upload_arquivos/excluir_publicacao.php?id=<?= $publicacao['id_publicacao'] ?>" 
                            class="btn red waves-effect waves-light">
                                <i class="material-icons left">delete</i> Confirmar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- VÍDEOS CURTIDOS -->
    <div id="curtidos" class="col s12">
        <div class="card-panel center no-content">
            <p>Em breve: vídeos que você curtiu.</p>
        </div>
    </div>
</main>
<script type="text/javascript" src="../js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Tabs.init(document.querySelectorAll('.tabs'));
        M.Materialbox.init(document.querySelectorAll('.materialboxed'));
        M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });
    });
</script>
</body>
</html>