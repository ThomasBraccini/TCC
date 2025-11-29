<?php
require_once "conexao.php";

$nome = "Administrador";
$email = "admin@nac.com";
$senha = "admin123"; // Mude depois!
$hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (nome, email, senha, is_admin, data_cadastro) 
        VALUES (?, ?, ?, 1, NOW())";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin criado com sucesso!<br>";
    echo "Email: admin@nac.com<br>";
    echo "Senha: admin123<br>";
    echo "<a href='index.php'>Ir para login</a>";
} else {
    echo "Erro: " . mysqli_error($conexao);
}
?>