<?php
// SESSÃO DEVE SER A PRIMEIRA COISA
session_start();
require_once "conexao.php";

// Buscar notícias do banco
$sql = "SELECT n.id_noticia, n.titulo, n.conteudo, n.imagem_capa, 
               n.data_publicacao, n.visualizacoes, u.nome AS autor
        FROM noticia n
        JOIN usuario u ON n.id_autor = u.id_usuario
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
    <!-- CORREÇÃO: Verificar caminho correto do CSS -->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/style_todos.css"/>
    <style>
        .noticia-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .noticia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .noticia-imagem-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .noticia-imagem {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .noticia-card:hover .noticia-imagem {
            transform: scale(1.05);
        }
        .noticia-conteudo {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 200px);
        }
        .noticia-titulo {
            font-size: 1.2rem;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .noticia-resumo {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .noticia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            font-size: 0.85rem;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .noticia-data {
            display: flex;
            align-items: center;
        }
        .noticia-data i {
            margin-right: 5px;
            font-size: 1rem;
        }
        .noticia-visualizacoes {
            display: flex;
            align-items: center;
        }
        .noticia-visualizacoes i {
            margin-right: 5px;
        }
        .no-noticias {
            text-align: center;
            padding: 60px 20px;
        }
        .no-noticias i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        .page-title {
            margin: 30px 0 40px;
            text-align: center;
        }
        .page-title h2 {
            font-weight: 300;
            color: #009688;
            margin-bottom: 10px;
        }
        .page-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php 
    // VERIFICAR SE O HEADER EXISTE
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
                    $resumo = strip_tags($noticia['conteudo']);
                    $resumo = strlen($resumo) > 150 ? substr($resumo, 0, 150) . '...' : $resumo;
                    
                    // Formatar data
                    $data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
                    
                    // Verificar se tem imagem
                    $tem_imagem = !empty($noticia['imagem_capa']) && file_exists("uploads/noticias/" . $noticia['imagem_capa']);
                ?>
                    <div class="col s12 m6 l4">
                        <a href="ver_noticia.php?id=<?= $noticia['id_noticia'] ?>" class="black-text" style="text-decoration: none;">
                            <div class="card noticia-card hoverable">
                                <div class="noticia-imagem-container">
                                    <?php if ($tem_imagem): ?>
                                        <img src="uploads/noticias/<?= $noticia['imagem_capa'] ?>" 
                                             alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                                             class="noticia-imagem">
                                    <?php else: ?>
                                        <div style="background: linear-gradient(135deg, #009688, #4DB6AC); height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="material-icons white-text" style="font-size: 4rem;">newspaper</i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="noticia-conteudo">
                                    <h3 class="noticia-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h3>
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

    <!-- CORREÇÃO: Script com caminho correto -->
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de notícias carregada');
            // Remover qualquer código específico do feed que possa causar erro
        });
    </script>
</body>
</html>