<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php";

// Mensagens de feedback
$mensagem = '';
if (isset($_GET['success'])) {
    $mensagem = '<div class="card-panel green lighten-4 green-text text-darken-1">'
        . htmlspecialchars($_GET['success']) . '</div>';
}
if (isset($_GET['error'])) {
    $mensagem = '<div class="card-panel red lighten-4 red-text text-darken-2">'
        . htmlspecialchars($_GET['error']) . '</div>';
}

// Busca publicações
$sql = "SELECT p.*, u.nome as nome_usuario 
        FROM publicacao p 
        INNER JOIN usuario u ON p.id_usuario_fk = u.id_usuario 
        WHERE p.deleted_at IS NULL 
        ORDER BY p.data_publicacao DESC";
$result = mysqli_query($conexao, $sql);
$publicacoes = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    
    <style>
        .feed-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .feed-card:hover {
            transform: translateY(-4px);
        }
        .feed-media-container {
            position: relative;
            height: 300px;
            overflow: hidden;
            background: #000;
        }
        .feed-img, .feed-video {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }
        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            color: rgba(255,255,255,0.8);
            pointer-events: none;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
            z-index: 1;
        }
        .card-content-feed {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }
        .card-title-feed {
            font-size: 1.4rem;
            font-weight: 600;
            color: #00695c;
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }
        .autor-chip {
            background: #00695c;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            height: 28px;
            line-height: 28px;
            padding: 0 10px;
            border-radius: 14px;
            display: inline-block;
        }
        .feed-description {
            flex-grow: 1;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #555;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .delete-btn {
            margin-top: auto;
            text-align: right;
        }
        .no-content {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
        /* Força 3 colunas em desktop */
        @media (min-width: 992px) {
            .feed-col {
                width: 33.333%;
                padding: 0 0.75rem;
            }
        }
    </style>
</head>
<body>

<?php include_once "header.php"; ?>

<main class="container">
    <h1 class="center teal-text text-lighten-2" style="margin: 2rem 0;">Feed de Artes</h1>

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
                $colClass = (in_array($index % 3, [0,1,2])) ? 'feed-col' : '';
                $colClass .= ' col s12 m6 l4'; // Mobile: 1 por linha, Tablet: 2, Desktop: 3
                ?>
                <div class="<?php echo $colClass; ?>" style="padding: 0.75rem;">
                    <div class="card feed-card">

                        <!-- MÍDIA: IMAGEM OU VÍDEO -->
                        <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                            <div class="feed-media-container">
                                <img src="uploads/<?php echo htmlspecialchars($publicacao['caminho_arquivo']); ?>" 
                                     class="feed-img materialboxed" 
                                     alt="<?php echo htmlspecialchars($publicacao['titulo']); ?>">
                            </div>

                        <?php elseif ($publicacao['tipo_arquivo'] == 'video'): ?>
                            <div class="feed-media-container">
                                <video class="feed-video" controls preload="metadata"
                                       poster="uploads/thumbnail_<?php echo pathinfo($publicacao['caminho_arquivo'], PATHINFO_FILENAME); ?>.jpg">
                                    <source src="uploads/<?php echo htmlspecialchars($publicacao['caminho_arquivo']); ?>" type="video/mp4">
                                    Seu navegador não suporta vídeo.
                                </video>
                                <i class="material-icons play-icon">play_circle_filled</i>
                            </div>
                        <?php endif; ?>

                        <!-- CONTEÚDO -->
                        <div class="card-content-feed">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="autor-chip">
                                    <?php echo htmlspecialchars($publicacao['nome_usuario']); ?>
                                </span>
                                <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                    <?php echo date('d/m/Y', strtotime($publicacao['data_publicacao'])); ?>
                                </span>
                            </div>

                            <h3 class="card-title-feed"><?php echo htmlspecialchars($publicacao['titulo']); ?></h3>
                            <p class="feed-description">
                                <?php echo htmlspecialchars($publicacao['descricao']); ?>
                            </p>

                            <!-- BOTÃO EXCLUIR -->
                            <?php if ($publicacao['id_usuario_fk'] == $_SESSION['user_id']): ?>
                                <div class="delete-btn">
                                    <a href="#modal-delete-<?php echo $publicacao['id_publicacao']; ?>" 
                                       class="modal-trigger btn-small red waves-effect waves-light">
                                        <i class="material-icons left">delete</i> Excluir
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- MODAL EXCLUIR -->
                <?php if ($publicacao['id_usuario_fk'] == $_SESSION['user_id']): ?>
                    <div id="modal-delete-<?php echo $publicacao['id_publicacao']; ?>" class="modal">
                        <div class="modal-content red-text text-darken-2">
                            <h5>Excluir Publicação?</h5>
                            <p>Você está prestes a <strong>excluir permanentemente</strong>:</p>
                            <p class="truncate"><strong>"<?php echo htmlspecialchars($publicacao['titulo']); ?>"</strong></p>
                            <p><small>Esta ação não pode ser desfeita.</small></p>
                        </div>
                        <div class="modal-footer">
                            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancelar</a>
                            <a href="upload_arquivos/excluir_publicacao.php?id=<?php echo $publicacao['id_publicacao']; ?>" 
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

<!-- Materialize JS -->
<script type="text/javascript" src="js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa modais
        M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });

        // Inicializa zoom em imagens
        M.Materialbox.init(document.querySelectorAll('.materialboxed'));

        // Remove ícone de play ao reproduzir
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