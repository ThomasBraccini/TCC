<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/TCC/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/TCC/css/style_todos.css"/>
</head>
<body>

<nav class="teal z-depth-1">
    <div class="nav-wrapper container">
        <a href="/TCC/feed.php" class="brand-logo left hide-on-mobile">NAC Portal</a>
        <a href="/TCC/feed.php" class="brand-logo left show-on-mobile hide-on-med-and-up" style="font-size: 1rem;">NAC</a>

        <ul class="right">
            <li><a href="/TCC/meu_perfil/meu_perfil.php" class="tooltipped" data-position="bottom" data-tooltip="Meu Perfil">Meu Perfil</a></li>
            <li><a href="/TCC/upload_arquivos/publicar_arte.php" class="tooltipped" data-position="bottom" data-tooltip="Publicar Arte">Publicar Arte</a></li>
            <li><a href="/TCC/noticias.php" class="tooltipped" data-position="bottom" data-tooltip="Notícias">Notícias</a></li>
            <li><a href="/TCC/logout.php" class="tooltipped" data-position="bottom" data-tooltip="Sair">Sair</a></li>
        </ul>
    </div>
</nav>

<script type="text/javascript" src="/TCC/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Tooltip.init(document.querySelectorAll('.tooltipped'), { position: 'bottom' });
    });
</script>
</body>
</html>