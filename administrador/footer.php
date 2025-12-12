<!DOCTYPE html>
<html>
<head>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .content-wrapper {
            flex: 1;
        }
        
        footer {
            margin-top: 0 !important;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
    </div>
    <footer class="teal darken-2" 
        style="padding: 14px 0; border-radius: 10px 10px 0 0; margin-top: 35px;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="color: white; font-size: 1rem; font-weight: bold;">
                Núcleo de Arte e Cultura
            </div>
            <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
            <a href="/TCC/administrador/index.php" class="white-text">Inicio</a>
            <a href="/TCC/administrador/cadastrar_noticias.php" class="white-text">Cadastrar Notícia</a>
            <a href="/TCC/administrador/denunciar.php" class="white-text">Denúncias</a></li>
            <a href="/TCC/administrador/listagem_noticias.php" class="white-text">Notícias</a>
            <a href="/TCC/logout.php" class="white-text">Sair</a>            </div>
            <div style="color: white; font-size: 0.95rem;">
                © <?= date('Y') ?> • IFFAR
            </div>
        </div>
    </footer>
</body>
</html>