<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Materialize CSS -->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <style>
        .nav-wrapper .input-field {
            margin: 0 10px;
        }
        .nav-wrapper .input-field input {
            height: 2.5rem;
            font-size: 0.9rem;
        }
        .nav-wrapper .brand-logo {
            font-weight: 600;
            font-size: 1.4rem;
        }
        .nav-wrapper i {
            height: 2.5rem;
            line-height: 2.5rem;
        }
        @media (max-width: 600px) {
            .hide-on-mobile { display: none !important; }
            .brand-logo { font-size: 1.1rem !important; }
            .nav-wrapper .input-field { margin: 0 5px; }
            .nav-wrapper .input-field input { width: 100px !important; }
        }
    </style>
</head>
<body>

<!-- NAVBAR COM TUDO NA PARTE DE CIMA -->
<nav class="teal z-depth-1">
    <div class="nav-wrapper container">

        <!-- LOGO -->
        <a href="feed.php" class="brand-logo left hide-on-mobile">
            NAC Portal
        </a>
        <a href="feed.php" class="brand-logo left show-on-mobile hide-on-med-and-up" style="font-size: 1rem;">
            NAC
        </a>

        <!-- MENU PRINCIPAL (sempre visível) -->
        <ul class="right">
            <!-- MEU PERFIL -->
            <li>
                <a href="perfil.php" class="tooltipped" data-position="bottom" data-tooltip="Meu Perfil">
                    Meu Perfil
                </a>
            </li>

            <!-- PUBLICAR ARTE -->
            <li>
                <a href="upload_arquivos/publicar_arte.php" class="tooltipped" data-position="bottom" data-tooltip="Publicar Arte">
                    Publicar Arte
                </a>
            </li>

            <!-- NOTIFICAÇÕES -->
            <li>
                <a href="noticias.php" class="tooltipped" data-position="bottom" data-tooltip="Notificações">
                    Notícias
                </a>
            </li>

            <!-- SAIR -->
            <li>
                <a href="logout.php" class="tooltipped" data-position="bottom" data-tooltip="Sair">
                    Sair
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Materialize JS + Inicialização -->
<script type="text/javascript" src="js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa tooltips
        M.Tooltip.init(document.querySelectorAll('.tooltipped'), {
            position: 'bottom'
        });
    });
</script>

</body>
</html>