<?php
session_start();
require_once "conexao.php"; // Ajuste o caminho se necessário

// ---------------------------------------------------------------------
// 1. Verifica login
// ---------------------------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$id_usuario = (int)$_SESSION['user_id'];

// ---------------------------------------------------------------------
// 2. Busca dados do usuário com prepared statement
// ---------------------------------------------------------------------
$sql_usuario = "SELECT 
                    nome, 
                    email, 
                    COALESCE(preferencias, '') AS preferencias,
                    DATE_FORMAT(data_cadastro, '%d/%m/%Y às %H:%i') AS data_cadastro_fmt
                FROM usuario 
                WHERE id_usuario = ? AND deleted_at IS NULL";

$stmt = $conexao->prepare($sql_usuario);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    session_destroy();
    header("Location: index.php?error=Usuário não encontrado.");
    exit;
}

$usuario = $res->fetch_assoc();
$stmt->close();

// ---------------------------------------------------------------------
// 3. Força preferencias como string vazia se for NULL
// ---------------------------------------------------------------------
$preferencias = trim($usuario['preferencias'] ?? '');
$preferencias = $preferencias !== '' ? $preferencias : 'Entusiasta da arte e tecnologia no IFFar.';

// ---------------------------------------------------------------------
// 4. Busca publicações
// ---------------------------------------------------------------------
$sql_pub = "SELECT 
                id_publicacao, titulo, caminho_arquivo, tipo_arquivo,
                DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_pub_fmt
            FROM publicacao 
            WHERE id_usuario_fk = ? AND deleted_at IS NULL
            ORDER BY data_publicacao DESC";

$stmt_pub = $conexao->prepare($sql_pub);
$stmt_pub->bind_param("i", $id_usuario);
$stmt_pub->execute();
$res_pub = $stmt_pub->get_result();

$publicacoes = [];
while ($row = $res_pub->fetch_assoc()) {
    $publicacoes[] = $row;
}
$stmt_pub->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - NAC Portal</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f5f5f5;
            color: #333;
        }
        header {
            background: #2e7d32;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        header a {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 15px;
        }
        header a:hover { text-decoration: underline; }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .profile-header {
            display: flex;
            align-items: flex-start;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .avatar {
            width: 90px;
            height: 90px;
            background: #2e7d32;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: bold;
            margin-right: 25px;
            flex-shrink: 0;
        }
        .info h1 {
            margin: 0 0 8px 0;
            font-size: 1.9rem;
            color: #1b5e20;
        }
        .info p {
            margin: 6px 0;
            color: #555;
            line-height: 1.5;
        }
        .info p.small {
            font-size: 0.85rem;
            color: #777;
            margin-top: 12px;
        }
        .btn {
            background: #2e7d32;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 12px;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .btn:hover { background: #1b5e20; }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 25px;
        }
        .tabs a {
            padding: 12px 20px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 3px solid transparent;
        }
        .tabs a.active {
            color: #2e7d32;
            border-bottom-color: #2e7d32;
            font-weight: 600;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .card-media { width: 100%; height: 160px; object-fit: cover; }
        .card-video { width: 100%; height: 160px; background: #000; }
        .card-placeholder {
            width: 100%;
            height: 160px;
            background: #c8e6c9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2e7d32;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .card-title {
            padding: 12px;
            font-weight: 600;
            text-align: center;
            background: #fafafa;
            border-top: 1px solid #eee;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .no-content { text-align: center; padding: 40px; color: #777; font-style: italic; }
    </style>
</head>
<body>

<header>
    <div>NAC Portal</div>
    <div>
        <a href="feed.php">Voltar à Galeria</a>
        <a href="logout.php">Sair</a>
    </div>
</header>

<div class="container">

    <!-- PERFIL -->
    <section class="profile-header">
        <div class="avatar">
            <?= htmlspecialchars(strtoupper(substr($usuario['nome'], 0, 2))) ?>
        </div>
        <div class="info">
            <h1><?= htmlspecialchars($usuario['nome']) ?></h1>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
            
            <!-- AQUI ESTÁ A CORREÇÃO -->
            <p>
                <?= nl2br(htmlspecialchars($preferencias)) ?>
            </p>
            
            <p class="small">Cadastrado em: <?= htmlspecialchars($usuario['data_cadastro_fmt']) ?></p>
            <a href="editar_perfil.php" class="btn">Editar Perfil</a>
        </div>
    </section>

    <!-- ABAS -->
    <nav class="tabs">
        <a href="#minhas" class="active">Minhas Publicações</a>
        <a href="#curtidos">Vídeos Curtidos</a>
    </nav>

    <!-- MINHAS PUBLICAÇÕES -->
    <section id="minhas">
        <?php if (empty($publicacoes)): ?>
            <div class="no-content">
                <p>Você ainda não publicou nenhuma obra.</p>
                <a href="upload_arquivos/publicar_arte.php" class="btn" style="margin-top:15px;">Publicar sua primeira arte</a>
            </div>
        <?php else: ?>
            <div class="gallery">
                <?php foreach ($publicacoes as $pub): ?>
                    <div class="card">
                        <?php 
                        $caminho = "uploads/" . htmlspecialchars($pub['caminho_arquivo']);
                        $titulo = htmlspecialchars($pub['titulo']);
                        ?>
                        <?php if ($pub['tipo_arquivo'] === 'imagem'): ?>
                            <img src="<?= $caminho ?>" alt="<?= $titulo ?>" class="card-media">
                        <?php elseif ($pub['tipo_arquivo'] === 'video'): ?>
                            <video class="card-video" controls poster="uploads/thumbnail_<?= pathinfo($pub['caminho_arquivo'], PATHINFO_FILENAME) ?>.jpg">
                                <source src="<?= $caminho ?>" type="video/mp4">
                            </video>
                        <?php elseif ($pub['tipo_arquivo'] === 'audio'): ?>
                            <div class="card-placeholder">ÁUDIO</div>
                        <?php else: ?>
                            <div class="card-placeholder">ARQUIVO</div>
                        <?php endif; ?>
                        <div class="card-title"><?= $titulo ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- CURTIDOS (placeholder) -->
    <section id="curtidos" style="display: none;">
        <div class="no-content">
            <p>Em breve: vídeos que você curtiu.</p>
        </div>
    </section>

</div>

<script>
    document.querySelectorAll('.tabs a').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.tabs a').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('section[id]').forEach(sec => {
                sec.style.display = 'none';
            });
            const target = this.getAttribute('href').substring(1);
            const section = document.getElementById(target);
            if (section) section.style.display = 'block';
        });
    });
</script>

</body>
</html>