<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php";
$sql = "SELECT noticias.id_noticia, 
                noticias.titulo, 
                noticias.subtitulo, 
                noticias.corpo, 
                noticias.caminho_midia, 
                noticias.data_publicacao, 
                noticias.autor 
        FROM noticias
        ORDER BY noticias.data_publicacao DESC";    
$resultado = mysqli_query($conexao, $sql);
$noticias = [];
if ($resultado) {
    while ($registro = mysqli_fetch_assoc($resultado)) {
        $noticias[] = $registro;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias • NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
</head>
<body>
    <?php include_once "header.php";?>
    <main class="container">
        <div class="page-title">
            <h2>Notícias</h2>
            <p>Fique por dentro das últimas novidades, eventos e destaques do NAC</p>
        </div>
        <?php if (empty($noticias)): ?>
            <div class="card-panel no-noticias">
                <i class="material-icons">newspaper</i>
                <h5>Nenhuma notícia publicada</h5>
                <p>Em breve teremos novidades para você!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($noticias as $noticia): 
                    // Criar resumo do conteúdo
                    $resumo = $noticia['corpo'];
                    if (strlen($resumo) > 150) {
                        $resumo = substr($resumo, 0, 150) . '...';
                    }
                    $data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
                    // Verifica se a notícia possui imagem válida
                    if (!empty($noticia['caminho_midia']) && file_exists("uploads/noticias/" . $noticia['caminho_midia'])) {
                        $tem_imagem = true;
                    } else {
                        $tem_imagem = false;
                    }
                ?>
                    <div class="col s12 m6 l4">
                        <a href="ver_noticia.php?id=<?= $noticia['id_noticia'] ?>" class="black-text" style="text-decoration: none;">
                            <div class="card noticia-card hoverable">
                                <div class="noticia-imagem-container">
                                    <?php if ($tem_imagem): ?>
                                        <img src="uploads/noticias/<?= $noticia['caminho_midia'] ?>" 
                                            alt="<?= $noticia['titulo'] ?>" 
                                            class="noticia-imagem">
                                    <?php else: ?>
                                        <div style="background: linear-gradient(135deg, #009688, #4DB6AC); height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="material-icons white-text" style="font-size: 4rem;">newspaper</i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="noticia-conteudo">
                                    <h3 class="noticia-titulo"><?= $noticia['titulo'] ?></h3>
                                    <?php if (!empty($noticia['subtitulo'])): ?>
                                        <p class="noticia-subtitulo"><?= $noticia['subtitulo'] ?></p>
                                    <?php endif; ?>
                                    <p class="noticia-resumo"><?= $resumo ?></p>
                                    <div class="noticia-meta">
                                        <div class="noticia-data">
                                            <i class="material-icons tiny">calendar_today</i>
                                            <?= $data_formatada ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php include_once "footer.php";?>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de notícias carregada');
        });
    </script>
</body>
</html>