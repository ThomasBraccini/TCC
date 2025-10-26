<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php";

// Busca todas as publicações
$sql = "SELECT * FROM publicacao WHERE deleted_at IS NULL ORDER BY data_publicacao DESC";
$result = mysqli_query($conexao, $sql);
$publicacoes = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NAC Portal - Feed</title>
</head>
<body>

    <!-- CABEÇALHO -->
    <header>
        <table width="100%">
            <tr>
                <td>
                    <h2 style="margin: 0;">NAC Portal</h2>
                </td>
                <td>
                    <input type="text" placeholder="Pesquisar obras, artistas...">
                </td>
                <td>
                    <a href="perfil.php">Meu Perfil</a>
                    <a href="upload_arquivos/publicar_arte.php">Publicar Arte</a>
                    <a href="noticias.php">Notificações</a>
                    <a href="logout.php">Sair</a>
                </td>
            </tr>
        </table>
    </header>

    <!-- FEED DE PUBLICAÇÕES -->
    <div>
        <h1>Feed de Artes</h1>
        
        <?php if (empty($publicacoes)): ?>
            <p><i>Nenhuma publicação disponível no momento.</i></p>
        <?php else: ?>
            <?php foreach ($publicacoes as $publicacao): ?>
                <div>
                    <h3><?php echo $publicacao['titulo']; ?></h3>
                    <p><?php echo $publicacao['descricao']; ?></p>
                    
                    <?php if ($publicacao['tipo_arquivo'] == 'imagem'): ?>
                        <img src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>" width="400">
                    <?php elseif ($publicacao['tipo_arquivo'] == 'video'): ?>
                        <video width="400" controls>
                            <source src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>">
                        </video>
                    <?php elseif ($publicacao['tipo_arquivo'] == 'audio'): ?>
                        <audio controls>
                            <source src="uploads/<?php echo $publicacao['caminho_arquivo']; ?>">
                        </audio>
                    <?php endif; ?>
                    
                    <p><small>Publicado em: <?php echo $publicacao['data_publicacao']; ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>