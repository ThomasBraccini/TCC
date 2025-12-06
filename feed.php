<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php"; 
// Busca publicações
$sql = "SELECT 
            p.titulo, p.caminho_arquivo, p.tipo_arquivo, p.data_publicacao,
            p.id_publicacao, p.id_usuario_fk, p.descricao, u.nome AS nome_usuario
        FROM publicacao p
        JOIN usuario u ON p.id_usuario_fk = u.id_usuario
        WHERE p.deleted_at IS NULL
        ORDER BY p.data_publicacao DESC";

$resultado = mysqli_query($conexao, $sql);
$publicacoes = [];
if ($resultado) {
    while ($registro = mysqli_fetch_assoc($resultado)) {
        $publicacoes[] = $registro;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NAC Portal - Feed</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
</head>
<body>
    <?php include_once "header.php"; ?>
    <main class="container">

        <!-- MODAL SUCESSO (excluir) -->
        <div id="modal-sucesso" class="modal">
            <div class="modal-content center">
                <i class="material-icons large green-text" style="font-size: 4rem;">check_circle</i>
                <h5>Excluído com sucesso!</h5>
                <p>Sua publicação foi removida permanentemente.</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect btn-flat">OK</a>
            </div>
        </div>

        <!-- PUBLICAÇÕES -->
        <?php if (empty($publicacoes)): ?>
            <div class="card-panel center no-content">
                <i class="material-icons large grey-text">image_search</i>
                <p>Nenhuma publicação disponível no momento.</p>
            </div>
        <?php else: ?>
            <div class="row" style="margin: 0 -0.75rem;">
                <?php foreach ($publicacoes as $publicacao): ?>
                    <?php 
                    $caminho_arquivo = "uploads/" . $publicacao['caminho_arquivo'];
                    $thumbnail = "uploads/thumbnail_" . pathinfo($publicacao['caminho_arquivo'], PATHINFO_FILENAME) . ".jpg";
                    $data_formatada = date('d/m/Y', strtotime($publicacao['data_publicacao']));
                    $is_dono = $publicacao['id_usuario_fk'] == $_SESSION['user_id'];
                    $titulo_lower = strtolower($publicacao['titulo']);

                    // Verifica se já está salvo
                    $ja_salvo = false;
                    $check_salvo = mysqli_query($conexao, 
                        "SELECT 1 FROM salvos WHERE id_usuario = {$_SESSION['user_id']} AND id_publicacao = {$publicacao['id_publicacao']}");
                    if ($check_salvo && mysqli_num_rows($check_salvo) > 0) {
                        $ja_salvo = true;
                    }
                    ?>
                    <div class="feed-col col l4" style="padding: 0.75rem;" 
                        data-titulo="<?= $titulo_lower ?>">
                        <div class="card feed-card">

                            <!-- MÍDIA -->
                            <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho_arquivo ?>" class="feed-img materialboxed" alt="<?= $publicacao['titulo'] ?>">
                                </div>
                            <?php elseif ($publicacao['tipo_arquivo'] == 'video'): ?>
                                <div class="feed-media-container">
                                    <video class="feed-video" controls preload="metadata" poster="<?= $thumbnail ?>">
                                        <source src="<?= $caminho_arquivo ?>" type="video/mp4">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                </div>
                            <?php elseif ($publicacao['tipo_arquivo'] == 'audio'): ?>
                                <div class="feed-audio-container">
                                    <i class="material-icons audio-icon">audiotrack</i>
                                    <audio class="feed-audio" controls>
                                        <source src="<?= $caminho_arquivo ?>" type="audio/mpeg">
                                        <source src="<?= $caminho_arquivo ?>" type="audio/wav">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                </div>
                            <?php endif; ?>

                            <!-- CONTEÚDO -->
                            <div class="card-content-feed">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <a href="meu_perfil/usuarios_perfil.php?id=<?= $publicacao['id_usuario_fk'] ?>" 
                                        class="waves-effect waves-light teal lighten-1 white-text"
                                        style="padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 0.9rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2); text-decoration: none;">
                                        <?= $publicacao['nome_usuario'] ?>
                                    </a>
                                    <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                        <?= $data_formatada ?>
                                    </span>
                                </div>
                                <h3 class="card-title-feed"><?= $publicacao['titulo'] ?></h3>
                                <?php if (!empty($publicacao['descricao'])): ?>
                                    <p class="feed-description"><?= nl2br($publicacao['descricao']) ?></p>
                                <?php endif; ?>

                                <!-- BOTÃO SALVAR -->
                                <div style="margin-top: 0.8rem; text-align: right;">
                                    <a href="meu_perfil/salvos.php?id=<?= $publicacao['id_publicacao'] ?>&from=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                        class="btn-small <?= $ja_salvo ? 'teal' : 'grey lighten-1' ?> waves-effect waves-light"
                                        style="font-size: 0.8rem; padding: 0 12px;">
                                        <?= $ja_salvo ? 'Curtido' : 'Curtir' ?>
                                    </a>
                                </div>

                                <!-- BOTÃO EXCLUIR (só dono) -->
                                <div class="delete-btn" style="margin-top: 0.5rem;">
                                    <?php if ($is_dono): ?>
                                        <a href="#modal-delete-<?= $publicacao['id_publicacao'] ?>" 
                                            class="modal-trigger btn-small red waves-effect waves-light">
                                            <i class="material-icons left">delete</i> Excluir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL EXCLUIR -->
                    <?php if ($is_dono): ?>
                        <div id="modal-delete-<?= $publicacao['id_publicacao'] ?>" class="modal">
                            <div class="modal-content">
                                <h5>Excluir Publicação?</h5>
                                <p>Você está prestes a <strong>excluir permanentemente</strong>:</p>
                                <p class="truncate"><strong>"<?= $publicacao['titulo'] ?>"</strong></p>
                                <p><small>Esta ação não pode ser desfeita.</small></p>
                            </div>
                            <div class="modal-footer">
                                <a href="#!" class="modal-close waves-effect btn-flat">Cancelar</a>
                                <a href="upload_arquivos/excluir_publicacao.php?id=<?= $publicacao['id_publicacao'] ?>" 
                                    class="btn red waves-effect waves-light">
                                    <i class="material-icons left">delete</i> Confirmar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- JS (só Materialize) -->
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });
            M.Materialbox.init(document.querySelectorAll('.materialboxed'));
            <?php if (isset($_GET['success'])): ?>
                const modalSucesso = M.Modal.getInstance(document.getElementById('modal-sucesso'));
                if (modalSucesso) modalSucesso.open();
            <?php endif; ?>
        });
    </script>
</body>
</html>