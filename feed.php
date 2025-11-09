<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once "conexao.php"; // na raiz

// Mensagens
$mostrar_modal_sucesso = isset($_GET['success']);
$mensagem_erro = isset($_GET['error']) 
    ? '<div class="card-panel red lighten-4 red-text text-darken-2">' . htmlspecialchars($_GET['error']) . '</div>' 
    : '';

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
    while ($row = mysqli_fetch_assoc($resultado)) {
        $publicacoes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>NAC Portal - Feed</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
</head>
<body>
    <?php include_once "header.php"; ?>

    <main class="container">
        <!-- ERRO -->
        <?php if ($mensagem_erro): ?>
            <div class="row"><div class="col s12"><?= $mensagem_erro ?></div></div>
        <?php endif; ?>

        <!-- MODAL SUCESSO -->
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
                    ?>
                    <div class="feed-col col s12 m6 l4" style="padding: 0.75rem;">
                        <div class="card feed-card">

                            <!-- MÍDIA -->
                            <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho_arquivo ?>" class="feed-img materialboxed" alt="<?= htmlspecialchars($publicacao['titulo']) ?>">
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
                                    
                                    <!-- BOTÃO VERDE (CORRIGIDO: caminho para pasta meu_perfil) -->
                                    <a href="meu_perfil/usuarios_perfil.php?id=<?= $publicacao['id_usuario_fk'] ?>" 
                                       class="waves-effect waves-light teal lighten-1 white-text"
                                       style="padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 0.9rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2); text-decoration: none;">
                                        <?= htmlspecialchars($publicacao['nome_usuario']) ?>
                                    </a>

                                    <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                        <?= $data_formatada ?>
                                    </span>
                                </div>

                                <h3 class="card-title-feed"><?= htmlspecialchars($publicacao['titulo']) ?></h3>
                                <p class="feed-description"><?= nl2br(htmlspecialchars($publicacao['descricao'])) ?></p>

                                <div class="delete-btn">
                                    <?php if ($is_dono): ?>
                                        <a href="#modal-delete-<?= $publicacao['id_publicacao'] ?>" 
                                           class="modal-trigger btn-small red waves-effect waves-light">
                                            <i class="material-icons left">delete</i> Excluir
                                        </a>
                                    <?php else: ?>
                                        <div class="delete-placeholder"></div>
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
                                <p class="truncate"><strong>"<?= htmlspecialchars($publicacao['titulo']) ?>"</strong></p>
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

    <!-- JS -->
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });
            M.Materialbox.init(document.querySelectorAll('.materialboxed'));

            <?php if ($mostrar_modal_sucesso): ?>
                const modalSucesso = M.Modal.getInstance(document.getElementById('modal-sucesso'));
                if (modalSucesso) modalSucesso.open();
            <?php endif; ?>
        });
    </script>
</body>
</html>