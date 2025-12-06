<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
// Busca usuário
$sql_usuario = "SELECT 
                    nome, 
                    email, 
                    preferencias,
                    foto_perfil
                FROM usuario 
                WHERE id_usuario = " . $_SESSION['user_id'] . " 
                AND deleted_at IS NULL";
                
$resultado = mysqli_query($conexao, $sql_usuario);
$usuario = mysqli_fetch_assoc($resultado);
if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
$preferencias = $usuario['preferencias'];
// Busca publicações
$sql_publicacoes = "SELECT 
                        id_publicacao, 
                        titulo, 
                        caminho_arquivo, 
                        tipo_arquivo,
                        descricao,
                    DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_pub_fmt
                    FROM publicacao 
                    WHERE id_usuario_fk = " . $_SESSION['user_id'] . " 
                    AND deleted_at IS NULL
                    ORDER BY data_publicacao";
                    
$result_publicacoes = mysqli_query($conexao, $sql_publicacoes);
$publicacoes = [];
while ($publicacao = mysqli_fetch_assoc($result_publicacoes)) {
    $publicacoes[] = $publicacao;
}
// ADICIONE AQUI: BUSCA PUBLICAÇÕES SALVAS
$sql_salvos = "SELECT 
                p.id_publicacao, p.titulo, p.caminho_arquivo, p.tipo_arquivo, p.descricao,
                DATE_FORMAT(p.data_publicacao, '%d/%m/%Y') AS data_pub_fmt,
                u.nome AS nome_autor
            FROM salvos s
            JOIN publicacao p ON s.id_publicacao = p.id_publicacao
            JOIN usuario u ON p.id_usuario_fk = u.id_usuario
            WHERE s.id_usuario = " . $_SESSION['user_id'] . "
            AND p.deleted_at IS NULL
            ORDER BY s.data_salvo DESC";

$resultado_salvos = mysqli_query($conexao, $sql_salvos);
$salvos = [];
while ($salvo = mysqli_fetch_assoc($resultado_salvos)) {
    $salvos[] = $salvo;
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
    <!-- PERFIL COM FOTO -->
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <!-- FOTO DE PERFIL -->
                        <div class="col s12 m3 center">
                            <?php 
                            $caminho_banco = $usuario['foto_perfil']; 
                            $caminho_servidor = "../" . $caminho_banco;
                            $tem_foto = $caminho_banco && file_exists($caminho_servidor);
                            ?>
                            <?php if ($tem_foto): ?>
                                <img src="<?= $caminho_servidor ?>" 
                                    alt="Foto de perfil de <?= $usuario['nome'] ?>" 
                                    class="circle" 
                                    style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #009688;">
                            <?php else: ?>
                                <div class="circle teal" 
                                    style="width: 100px; height: 100px; line-height: 100px; font-size: 2.5rem; color: white; margin: 0 auto;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- INFORMAÇÕES -->
                        <div class="col s12 m9">
                            <h4><?= $usuario['nome'] ?></h4>
                            <p class="grey-text text-darken-1"><?= $usuario['email'] ?></p>
                            <?php if (!empty($preferencias)): ?>
                                <p><?= $preferencias ?></p>
                            <?php endif; ?>
                            <!-- LINK CORRIGIDO: MESMA PASTA -->
                            <a href="editar_perfil.php" class="btn waves-effect waves-light teal">Editar Perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABAS -->
        <div class="row">
            <div class="col s12 center">
                <ul class="tabs tabs-fixed-width">
                    <li class="tab col s6"><a class="active" href="#minhas">Minhas Publicações</a></li>
                    <li class="tab col s6"><a href="#salvos">Publicações Curtidas</a></li>
                </ul>
            </div>
        </div>
    <!-- MINHAS PUBLICAÇÕES -->
    <div id="minhas" class="col s12">
        <?php if (empty($publicacoes)): ?>
            <div class="card-panel center no-content">
                <i class="material-icons large grey-text">image_search</i>
                <p>Você ainda não publicou nenhuma obra.</p>
                <a href="../upload_arqu ivos/publicar_arte.php" class="btn waves-effect waves-light teal">
                    Publicar sua primeira arte
                </a>
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
                            <!-- MÍDIA -->
                            <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho ?>" 
                                        class="feed-img materialboxed" 
                                        alt="<?= $titulo ?>">
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
                            <!-- CONTEÚDO -->
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
        <!-- ADICIONE AQUI: SEÇÃO SALVOS -->
    <div id="salvos" class="col s12">
        <?php if (empty($salvos)): ?>
            <div class="card-panel center no-content">
                <i class="material-icons large grey-text">bookmark_border</i>
                <p>Você ainda não salvou nenhuma obra.</p>
                <a href="../feed.php" class="btn waves-effect waves-light teal">
                    Explorar Feed
                </a>
            </div>
        <?php else: ?>
            <div class="row" style="margin: 0 -0.75rem;">
                <?php foreach ($salvos as $publicacao): ?>
                    <?php 
                    $caminho = "../uploads/" . $publicacao['caminho_arquivo'];
                    $titulo  = $publicacao['titulo'];
                    $colClass = 'feed-col col s12 m6 l4';
                    ?>
                    <div class="<?= $colClass ?>" style="padding: 0.75rem;">
                        <div class="card feed-card">
                            <!-- MÍDIA -->
                            <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                                <div class="feed-media-container">
                                    <img src="<?= $caminho ?>" 
                                        class="feed-img materialboxed" 
                                        alt="<?= $titulo ?>">
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
                            <!-- CONTEÚDO -->
                            <div class="card-content-feed">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="autor-chip"><?= $publicacao['nome_autor'] ?></span>
                                    <span class="grey-text text-darken-1" style="font-size: 0.8rem;">
                                        <?= $publicacao['data_pub_fmt'] ?>
                                    </span>
                                </div>
                                <h3 class="card-title-feed"><?= $titulo ?></h3>
                                <?php if (!empty($publicacao['descricao'])): ?>
                                    <p class="feed-description"><?= nl2br($publicacao['descricao']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- FIM DA SEÇÃO SALVOS -->
</main>
<script type="text/javascript" src="../js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Tabs.init(document.querySelectorAll('.tabs'));
        M.Materialbox.init(document.querySelectorAll('.materialboxed'));
        M.Modal.init(document.querySelectorAll('.modal'), { opacity: 0.7 });
        // FORÇA AS ABAS VERDES DEPOIS QUE O MATERIALIZE TERMINA DE RODAR
        setTimeout(function() {
            document.querySelectorAll('.tabs .tab a').forEach(function(link) {
                link.style.color = '#00695c';
                if (link.classList.contains('active')) {
                    link.style.color = '#009688';
                }
            });
            document.querySelector('.tabs .indicator').style.backgroundColor = '#009688';
        }, 100);
    });
</script>
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