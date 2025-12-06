<?php
session_start();
include '../conexao.php';

// Verificar se está logado como admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../cadastro_login/login.php");
    exit();
}

// Primeiro, vamos verificar se a tabela denuncia existe
$tabela_existe = false;
$result = mysqli_query($conexao, "SHOW TABLES LIKE 'denuncia'");
if ($result && mysqli_num_rows($result) > 0) {
    $tabela_existe = true;
}
mysqli_free_result($result);

// Se tentar analisar denúncia mas a tabela não existe
if (isset($_POST['analisar']) && !$tabela_existe) {
    $mensagem = "A tabela de denúncias ainda não foi criada.";
}
// Se a tabela existe e foi enviado formulário
elseif (isset($_POST['analisar']) && $tabela_existe) {
    $id_denuncia = $_POST['id_denuncia'];
    $status = $_POST['status'];
    $obs = $_POST['observacao'];

    $sql = "UPDATE denuncia 
            SET status = ?, 
                analisado_por_admin = ?, 
                data_analise = NOW(), 
                observacao_admin = ? 
            WHERE id_denuncia = ?";
    
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "sisi", $status, $_SESSION['user_id'], $obs, $id_denuncia);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Buscar denúncias se a tabela existir
$denuncias = [];
if ($tabela_existe) {
    $sql = "SELECT d.*, p.titulo, u.nome as nome_denunciante 
            FROM denuncia d
            JOIN publicacao p ON d.id_publicacao_fk = p.id_publicacao
            JOIN usuario u ON d.id_usuario_fk = u.id_usuario
            WHERE d.status = 'pendente'
            ORDER BY d.data_denuncia DESC";

    $resultado = mysqli_query($conexao, $sql);
    
    if ($resultado) {
        $denuncias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
        mysqli_free_result($resultado);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <link rel="stylesheet" href="../css/materialize.css">
    <link rel="stylesheet" href="../css/style_todos.css">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .card { padding: 20px; margin-bottom: 20px; }
        h3 { color: #1a237e; }
    </style>
</head>
<body>

<div class="container">
    <h3>Painel do Administrador</h3>
    <p>Olá, <b><?=$_SESSION['nome'] ?? 'Admin'?></b> | <a href="../logout.php">Sair</a></p>

    <?php if (isset($mensagem)): ?>
        <div class="card-panel orange lighten-4">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <?php if (!$tabela_existe): ?>
        <div class="card">
            <h5>Atenção</h5>
            <p>A tabela de denúncias ainda não foi criada no banco de dados.</p>
            <p>Para criar a tabela, execute este SQL no phpMyAdmin:</p>
            <pre style="background: #f1f1f1; padding: 10px; border-radius: 5px;">
CREATE TABLE denuncia (
    id_denuncia INT PRIMARY KEY AUTO_INCREMENT,
    id_publicacao_fk INT NOT NULL,
    id_usuario_fk INT NOT NULL,
    motivo TEXT NOT NULL,
    data_denuncia DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'analisada', 'rejeitada', 'publicacao_removida') DEFAULT 'pendente',
    analisado_por_admin INT NULL,
    data_analise DATETIME NULL,
    observacao_admin TEXT NULL
);
            </pre>
        </div>
    <?php else: ?>
        <div class="card">
            <h5>Denúncias Pendentes (<?=count($denuncias)?>)</h5>

            <?php if (count($denuncias) == 0): ?>
                <p>Tudo limpo! Nenhuma denúncia pendente.</p>
            <?php else: ?>
                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Denunciante</th>
                            <th>Publicação</th>
                            <th>Motivo</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($denuncias as $d): ?>
                        <tr>
                            <td><?=date('d/m/Y H:i', strtotime($d['data_denuncia']))?></td>
                            <td><?=htmlspecialchars($d['nome_denunciante'])?></td>
                            <td><?=htmlspecialchars($d['titulo'])?></td>
                            <td><?=htmlspecialchars($d['motivo'])?></td>
                            <td>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="id_denuncia" value="<?=$d['id_denuncia']?>">
                                    <select name="status" required>
                                        <option value="analisada">Aprovada / Analisada</option>
                                        <option value="rejeitada">Rejeitada</option>
                                        <option value="publicacao_removida">Publicação Removida</option>
                                    </select><br><br>
                                    <textarea name="observacao" placeholder="Observação (opcional)" rows="2"></textarea><br><br>
                                    <button type="submit" name="analisar" class="btn blue">Analisar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>