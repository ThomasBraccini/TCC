<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>NAC Portal</title>
    <!-- Materialize CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/TCC/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/TCC/css/style_todos.css"/>
    <style>
        nav { height: 64px; line-height: 64px; }
        nav .brand-logo { line-height: 64px; font-weight: 500; }
        .search-wrapper {
            position: absolute;
            left: 40%;
            transform: translateX(-50%);
            width: 300px;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .search-wrapper input {
            height: 40px !important;
            border-radius: 25px;
            padding-left: 45px !important;
            background: rgba(255,255,255,0.9);
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .search-wrapper i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
    </style>
</head>
<body>
<nav class="teal z-depth-0">
    <div class="nav-wrapper container" style="position: relative;">
        <a href="/TCC/feed.php" class="brand-logo white-text" style="font-size: 1.8rem;">
            NAC Portal
        </a>
        <!-- BARRA DE PESQUISA (só no feed) -->
        <?php if (basename($_SERVER['PHP_SELF']) === 'feed.php'): ?>
            <div class="search-wrapper hide-on-med-and-down">
                <i class="material-icons">search</i>
                <input type="text" id="globalSearch" placeholder="Pesquisar por título da obra...">
            </div>
            <div class="search-wrapper hide-on-large-only">
                <i class="material-icons">search</i>
                <input type="text" id="globalSearchMobile" placeholder="Título da obra..." autocomplete="off">
            </div>
        <?php endif; ?>
        <!-- MENU DIREITA -->
        <ul class="right hide-on-med-and-down">
            <li><a href="/TCC/meu_perfil/meu_perfil.php" class="white-text tooltipped" data-position="bottom" data-tooltip="Meu Perfil">Meu Perfil</a></li>
            <li><a href="/TCC/upload_arquivos/publicar_arte.php" class="white-text tooltipped" data-position="bottom" data-tooltip="Publicar Arte">Publicar Arte</a></li>
            <li><a href="/TCC/noticias.php" class="white-text tooltipped" data-position="bottom" data-tooltip="Notícias">Notícias</a></li>
            <li><a href="/TCC/logout.php" class="white-text tooltipped" data-position="bottom" data-tooltip="Sair">Sair</a></li>
        </ul>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger right white-text">
            <i class="material-icons">menu</i>
        </a>
    </div>
</nav>
<!-- Sidenav Mobile -->
<ul class="sidenav" id="mobile-nav">
    <li><a href="/TCC/meu_perfil/meu_perfil.php">Meu Perfil</a></li>
    <li><a href="/TCC/upload_arquivos/publicar_arte.php">Publicar Arte</a></li>
    <li><a href="/TCC/noticias.php">Notícias</a></li>
    <li><a href="/TCC/logout.php">Sair</a></li>
</ul>
<!-- Scripts -->
<script src="/TCC/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Sidenav.init(document.querySelectorAll('.sidenav'));
        M.Tooltip.init(document.querySelectorAll('.tooltipped'));
        <?php if (basename($_SERVER['PHP_SELF']) === 'feed.php'): ?>
            const searchDesktop = document.getElementById('globalSearch');
            const searchMobile = document.getElementById('globalSearchMobile');
            const cards = document.querySelectorAll('.feed-col');
            const filterByTitle = (query) => {
                query = query.toLowerCase().trim();
                cards.forEach(card => {
                    const titulo = (card.getAttribute('data-titulo') || '').toLowerCase();
                    card.style.display = query === '' || titulo.includes(query) ? '' : 'none';
                });
            };
            [searchDesktop, searchMobile].forEach(input => {
                if (input) {
                    input.addEventListener('input', e => filterByTitle(e.target.value));
                    input.addEventListener('keyup', e => filterByTitle(e.target.value));
                }
            });
        <?php endif; ?>
    });
</script>
</body>
</html>