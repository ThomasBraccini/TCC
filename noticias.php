<?php
session_start();
require_once "conexao.php";

// Buscar notícias do banco - CORRIGIDO para estrutura da tabela
$sql = "SELECT n.id_noticia, n.titulo, n.subtitulo, n.corpo, n.caminho_midia, 
               n.data_publicacao, n.visualizacoes, n.autor, n.categoria, n.tags
        FROM noticias n
        WHERE n.ativo = 1
        ORDER BY n.data_publicacao DESC";

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
    <?php 
    if (file_exists("header.php")) {
        include_once "header.php"; 
    } else {
        echo "<nav class='teal'><div class='nav-wrapper'><a href='#' class='brand-logo'>NAC Portal</a></div></nav>";
    }
    ?>

    <main class="container">
        <div class="page-title">
            <h2><i class="material-icons left">newspaper</i> Notícias Culturais</h2>
            <p>Fique por dentro das últimas novidades, eventos e destaques do mundo cultural</p>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="card-panel no-noticias">
                <i class="material-icons">newspaper</i>
                <h5>Nenhuma notícia publicada ainda</h5>
                <p>Em breve teremos novidades para você!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($noticias as $noticia): 
                    // Criar resumo do conteúdo
                    $resumo = strip_tags($noticia['corpo']);
                    $resumo = strlen($resumo) > 150 ? substr($resumo, 0, 150) . '...' : $resumo;
                    
                    // Formatar data
                    $data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
                    
                    // Verificar se tem imagem
                    $tem_imagem = !empty($noticia['caminho_midia']) && file_exists("uploads/noticias/" . $noticia['caminho_midia']);
                ?>
                    <div class="col s12 m6 l4">
                        <a href="ver_noticia.php?id=<?= $noticia['id_noticia'] ?>" class="black-text" style="text-decoration: none;">
                            <div class="card noticia-card hoverable">
                                <div class="noticia-imagem-container">
                                    <?php if ($tem_imagem): ?>
                                        <img src="uploads/noticias/<?= $noticia['caminho_midia'] ?>" 
                                             alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                                             class="noticia-imagem">
                                    <?php else: ?>
                                        <div style="background: linear-gradient(135deg, #009688, #4DB6AC); height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="material-icons white-text" style="font-size: 4rem;">newspaper</i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="noticia-conteudo">
                                    <div class="noticia-info">
                                        <?php if (!empty($noticia['categoria'])): ?>
                                            <span class="noticia-categoria"><?= htmlspecialchars($noticia['categoria']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="noticia-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                                    
                                    <?php if (!empty($noticia['subtitulo'])): ?>
                                        <p class="noticia-subtitulo"><?= htmlspecialchars($noticia['subtitulo']) ?></p>
                                    <?php endif; ?>
                                    
                                    <p class="noticia-resumo"><?= htmlspecialchars($resumo) ?></p>
                                    
                                    <div class="noticia-meta">
                                        <div class="noticia-data">
                                            <i class="material-icons tiny">calendar_today</i>
                                            <?= $data_formatada ?>
                                        </div>
                                        <div class="noticia-visualizacoes">
                                            <i class="material-icons tiny">visibility</i>
                                            <?= $noticia['visualizacoes'] ?>
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

    <?php 
    if (file_exists("footer.php")) {
        include_once "footer.php"; 
    }
    ?>

    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de notícias carregada');
        });
    </script>
</body>
</html>