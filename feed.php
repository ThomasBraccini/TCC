<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once "conexao.php";
// Mostrar mensagens de sucesso/erro
if (isset($_GET['success'])) {
    echo '<div">';
    echo $_GET['success'];
    echo '</div>';
}

if (isset($_GET['error'])) {
    echo '<div>';
    echo $_GET['error'];
    echo '</div>';
}

// Busca todas as publicações com nome do usuário
$sql = "SELECT p.*, u.nome as nome_usuario FROM publicacao p INNER JOIN usuario u ON p.id_usuario_fk = u.id_usuario WHERE p.deleted_at IS NULL ORDER BY p.data_publicacao DESC";
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
        <table width="150%">
            <tr>
                <td>
                    <h2>NAC Portal</h2>
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
                    <!-- Nome do usuário que publicou -->
                    <div">
                        Publicado por:<strong><?php echo $publicacao['nome_usuario']; ?>
                    </div>
                    
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
                    
                    <?php if ($publicacao['id_usuario_fk'] == $_SESSION['user_id']): ?>
                        <a href="upload_arquivos/excluir_publicacao.php?id=<?php echo $publicacao['id_publicacao']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta publicação?')"style="color: red; text-decoration: none;">
                            [Excluir]
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>