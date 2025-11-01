<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php";
$mensagem = '';
if (isset($_GET['success'])) {
    $mensagem = '<div class="card-panel green lighten-4 green-text text-darken-1">' . $_GET['success'] . '</div>';
}
if (isset($_GET['error'])) {
    $mensagem = '<div class="card-panel red lighten-4 red-text text-darken-2">' . $_GET['error'] . '</div>';
}
$sql = "SELECT publicacao.titulo,publicacao.caminho_arquivo,publicacao.tipo_arquivo,
            publicacao.data_publicacao,
            publicacao.id_publicacao,
            publicacao.id_usuario_fk,
            publicacao.descricao,
            usuario.nome AS nome_usuario
        FROM publicacao
        JOIN usuario ON publicacao.id_usuario_fk = usuario.id_usuario
        WHERE publicacao.deleted_at IS NULL
        ORDER BY publicacao.data_publicacao DESC";
$resultado = mysqli_query($conexao, $sql);
$publicacoes = [];
if ($resultado) {
    while ($vetor = mysqli_fetch_assoc($resultado)) {
        $publicacoes[] = $vetor;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>NAC Portal - Feed</title>
    <!-- Material Icons + CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
</head>
<body>
<?php include_once "header.php"; ?>
<main class="container">
    <!-- Mensagens -->
    <?php if ($mensagem): ?>
        <div class="row"><div class="col s12"><?php echo $mensagem; ?></div></div>
    <?php endif; ?>
    <?php if (empty($publicacoes)): ?>
        <div class="card-panel center no-content">
            <i class="material-icons large grey-text">image_search</i>
            <p>Nenhuma publicação disponível no momento.</p>
        </div>
    <?php else: ?>
        <div class="row" style="margin: 0 -0.75rem;">
            <?php foreach ($publicacoes as $index => $publicacao): ?>
                <?php 
                // Força quebra de linha a cada 3 itens (desktop)
                    $colClass = 'feed-col col s12 m6 l4';                
                ?>
                <div class="<?php echo $colClass; ?>" style="padding: 0.75rem;">
                    <div class="card feed-card">
                        <!-- MÍDIA: IMAGEM OU VÍDEO -->
                        <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                            <div class="feed-media-container">
                                <img src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>" class="feed-img materialboxed" alt="<?php echo $publicacao['titulo']; ?>">
                            </div>
                        <?php elseif ($publicacao['tipo_arquivo'] == 'video'): ?>
                            <div class="feed-media-container">
                                <video class="feed-video" controls preload="metadata" poster="uploads/thumbnail_<?php echo pathinfo($publicacao['caminho_arquivo'], PATHINFO_FILENAME); ?>.jpg">
                                    <source src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>" type="video/mp4">
                                    Seu navegador não suporta vídeo.
                                </video>
                            </div>
                        <?php elseif ($publicacao['tipo_arquivo'] == 'audio'): ?>
                            <div class="feed-audio-container">
                                <i class="material-icons audio-icon">audiotrack</i>
                                <audio class="feed-audio" controls>
                                    <source src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>" type="audio/mpeg">
                                    <source src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>" type="audio/wav">
                                    Seu navegador não suporta áudio.
                                </audio>
                            </div>
                        <?php endif; ?>
                        <!-- CONTEÚDO -->
                        <div class="card-content-feed">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="autor-chip">
                                    <?php echo $publicacao['nome_usuario']; ?>
                                </span>
                                <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                    <?php echo date('d/m/Y', strtotime($publicacao['data_publicacao'])); ?>
                                </span>
                            </div>
                            <h3 class="card-title-feed"><?php echo $publicacao['titulo']; ?></h3>
                            <p class="feed-description">
                                <?php echo $publicacao['descricao']; ?>
                            </p>
                            <!-- BOTÃO EXCLUIR (agora sempre com o container para reservar espaço) -->
                            <div class="delete-btn">
                                <?php if ($publicacao['id_usuario_fk'] == $_SESSION['user_id']): ?>
                                    <a href="#modal-delete-<?php echo $publicacao['id_publicacao']; ?>" class="modal-trigger btn-small red waves-effect waves-light">
                                        <i class="material-icons left">delete</i> Excluir
                                    </a>
                                <?php else: ?>
                                    <!-- placeholder invisível para manter o card alinhado -->
                                    <div class="delete-placeholder">placeholder</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL EXCLUIR -->
                <?php if ($publicacao['id_usuario_fk'] == $_SESSION['user_id']): ?>
                    <div id="modal-delete-<?php echo $publicacao['id_publicacao']; ?>" class="modal">
                        <div>
                            <h5>Excluir Publicação?</h5>
                            <p>Você está prestes a <strong>excluir permanentemente</strong>:</p>
                            <p class="truncate"><strong>"<?php echo $publicacao['titulo']; ?>"</strong></p>
                            <p><small>Esta ação não pode ser desfeita.</small></p>
                        </div>
                        <div class="modal-footer">
                            <a href="#!" class="modal-close waves-effect btn-flat">Cancelar</a>
                            <a href="upload_arquivos/excluir_publicacao.php?id=<?php echo $publicacao['id_publicacao']; ?>" class="btn red waves-effect waves-light">
                                <i class="material-icons left">delete</i> Confirmar
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<!-- Materialize JS -->
<script type="text/javascript" src="js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });
        document.querySelectorAll('video').forEach(video => {
            video.addEventListener('play', function() {
                const icon = this.parentElement.querySelector('.play-icon');
                if (icon) icon.remove();
            });
        });
    });
</script>
</body>
</html>