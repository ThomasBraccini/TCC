<?php
session_start();
require_once "../conexao.php";

// Verifica se o token existe na URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $token = '';
}

if ($token === '') {
    header("Location: esqueci_senha.php?error=Link inválido.");
    exit;
}

// Verifica se o token é válido e não expirou
$query = "SELECT id_usuario, token_expira_em FROM usuario WHERE token_recuperacao = '$token'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $token_expira = $registro['token_expira_em'];
    $encontrou = true;
    
    // Libera o resultado apenas se a query foi bem-sucedida
    mysqli_free_result($resultado);
} else {
    $encontrou = false;
}

if (!$encontrou) {
    header("Location: esqueci_senha.php?error=Link inválido ou expirado.");
    exit;
}

if (time() > $token_expira) {
    header("Location: esqueci_senha.php?error=Link expirado. Solicite um novo.");
    exit;
}

// Se chegou aqui, o token é válido
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - NAC Portal</title>
</head>
<body>
    Criar Nova Senha
    
    <?php
    if (isset($_GET['error'])) {
        echo '<p>' . $_GET['error'] . '</p>';
    }
    ?>
    
    <form action="processa_redefinir_senha.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="nova_senha">Nova Senha (mínimo 8 caracteres):</label><br>
        <input type="password" name="nova_senha" id="nova_senha" required minlength="8"><br><br>
        <label for="confirmar_senha">Confirmar Nova Senha:</label><br>
        <input type="password" name="confirmar_senha" id="confirmar_senha" required minlength="8"><br><br>
        <input type="submit" value="Redefinir Senha">
    </form>
</body>
</html>