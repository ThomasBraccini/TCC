<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin • NAC Portal</title>
    <!-- Materialize CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/TCC/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/TCC/css/style_todos.css"/>
    <style>
        nav { height: 64px; line-height: 64px; }
        nav .brand-logo { line-height: 64px; font-weight: 500; }
    </style>
</head>
<body>

<!-- NAVBAR EXCLUSIVA PARA ADMINISTRADOR -->
<nav class="teal z-depth-0">
    <div class="nav-wrapper container">
        <a href="/TCC/administrador/index.php" class="brand-logo white-text" style="font-size: 1.8rem;">
        </a>

        <!-- MENU DIREITA - SOMENTE OPÇÕES DO ADMIN -->
        <ul class="right hide-on-med-and-down">
            <li><a href="/TCC/administrador/index.php" class="white-text">Inicio</a></li>
            <li><a href="/TCC/administrador/cadastrar_noticias.php" class="white-text">Cadastrar Notícia</a></li>
            <li><a href="/TCC/administrador/denunciar.php" class="white-text">Denúncias</a></li>
            <li><a href="/TCC/administrador/listagem_noticias.php" class="white-text">Notícias</a></li>
            <li><a href="/TCC/logout.php" >Sair</a></li>
        </ul>

        <!-- Botão menu mobile -->
        <a href="#" data-target="mobile-nav-admin" class="sidenav-trigger right white-text">
            <i class="material-icons">menu</i>
        </a>
    </div>
</nav>

<!-- Sidenav Mobile - Apenas opções admin -->
<ul class="sidenav" id="mobile-nav-admin">
    <li><a href="/TCC/administrador/index.php">Painel Admin</a></li>
    <li><a href="/TCC/administrador/cadastrar_noticias.php">Cadastrar Notícia</a></li>
    <li><a href="/TCC/administrador/denunciar.php">Denúncias</a></li>
    <li class="divider"></li>
    <li><a href="/TCC/logout.php" class="red-text text-darken-1">Sair</a></li>
</ul>

<!-- Scripts -->
<script src="/TCC/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa sidenav mobile
        M.Sidenav.init(document.querySelectorAll('.sidenav'));
    });
</script>