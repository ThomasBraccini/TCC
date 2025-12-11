<?php
session_start();
require_once "../conexao.php";
// Proteção: só admin entra
if (!isset($_SESSION['user_id']) or !isset($_SESSION['is_admin']) or $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}
$total_denuncias = 0;
$sql = "SELECT COUNT(*) AS total_pendente FROM denuncia WHERE status = 'pendente'";
$resultado = mysqli_query($conexao, $sql);
if ($resultado) {
    $linha = mysqli_fetch_assoc($resultado);    
    $total_denuncias = $linha['total_pendente'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin • NAC Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
    <style>
        body { background: #f5f7fa; }
        .admin-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px !important;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .admin-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        .card-title-admin {
            font-weight: 700 !important;
            font-size: 1.6rem !important;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        .badge-admin {
            font-size: 1.1rem !important;
            padding: 10px 20px !important;
            border-radius: 30px !important;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 50px; margin-bottom: 100px;">
        <h3 class="center teal-text text-darken-2" style="font-weight: 300;">
            <i class="material-icons large">admin_panel_settings</i><br>
            Painel do Administrador
        </h3>
        <p class="center grey-text text-darken-2" style="font-size:1.2rem; margin-bottom:40px;">
        </p>
        <div class="row">
            <!-- CADASTRAR NOTÍCIA -->
            <div class="col s12 m6">
                <a href="cadastrar_noticia.php" style="text-decoration:none; display:block;">
                    <div class="card admin-card z-depth-5">
                        <div class="card-image waves-effect waves-block waves-light" style="background: linear-gradient(135deg, #00695c, #009688); padding: 40px 20px;">
                            <h4 class="white-text center card-title-admin">
                                <i class="material-icons large">post_add</i><br>
                                Cadastrar Notícia
                            </h4>
                        </div>
                        <div class="card-content center" style="padding: 40px 20px;">
                            <p class="grey-text text-darken-3" style="font-size:1.2rem; line-height:1.8;">
                                Publicar novas notícias culturais, shows, exposições e eventos
                            </p>
                            <div class="center" style="margin-top:30px;">
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- REVISAR DENÚNCIAS -->
            <div class="col s12 m6">
                <a href="denunciar.php" style="text-decoration:none; display:block;">
                    <div class="card admin-card z-depth-5">
                        <div class="card-image waves-effect waves-block waves-light" style="background: linear-gradient(135deg, #b71c1c, #e53935); padding: 40px 20px; position:relative;">
                            <h4 class="white-text center card-title-admin">
                                <i class="material-icons large">flag</i><br>
                                Denúncias
                            </h4>
                        </div>
                        <div class="card-content center" style="padding: 40px 20px;">
                            <p class="grey-text text-darken-3" style="font-size:1.2rem; line-height:1.8;">
                                Revisar denúncias recebidas e decidir se remove ou mantém a publicação
                            </p>
                            <div class="center" style="margin-top:30px;">
                                <?php if ($total_denuncias == 0): ?>
                                    <span class="badge-admin green white-text">
                                        <i class="material-icons left">check_circle</i> Tudo em dia!
                                    </span>
                                <?php else: ?>
                                    <span class="badge-admin red white-text">
                                        <i class="material-icons left">warning</i>
                                        <?= $total_denuncias ?> aguardando análise
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="center" style="margin-top:80px;">
            <a href="../index.php" class="btn-large grey darken-3 waves-effect waves-light" style="border-radius:30px; padding:0 50px; height:56px; font-size:1.1rem;">
                Sair
            </a>
        </div>
    </div>
</body>
</html>