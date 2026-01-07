<?php
session_start();
require_once "../conexao.php"; 
$email = '';
$senha = '';
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (isset($_POST['senha'])) {
    $senha = $_POST['senha'];
}
// Verifica se algum campo está vazio
if ($email === '' || $senha === '') {
    header("Location: ../index.php?erro_email=1&erro_senha=1");
    exit; 
}
// Busca o usuário no banco pelo e-mail
$query = "SELECT id_usuario, nome, senha, verificado, is_admin 
            FROM usuario 
            WHERE email = '$email'";
$resultado = mysqli_query($conexao, $query);
// Se houver erro na consulta, para tudo e mostra o erro
if (!$resultado) {
    die("Erro na consulta: " . mysqli_error($conexao));
}
// Verifica se encontrou algum usuário com esse e-mail
if (mysqli_num_rows($resultado) > 0) {
    // Pega os dados do usuário encontrado
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $nome       = $registro['nome'];
    $senhaHash  = $registro['senha'];     // Senha criptografada no banco
    $verificado = $registro['verificado'];
    // Trata o campo is_admin (pode ser NULL no banco)
    $is_admin = 0;
    if (!empty($registro['is_admin'])) {
        $is_admin = $registro['is_admin'];
    }
    $usuario_encontrado = true;
} else {
    $usuario_encontrado = false;
}
// Se o e-mail não existe, volta ao login com erro no e-mail
if (!$usuario_encontrado) {
    header("Location: ../index.php?erro_email=1");
    exit;
}
// Se a conta não foi verificada ainda, não deixa entrar
if ($verificado == 0) {
    header("Location: ../index.php?erro_email=1");
    exit;
}
// Verifica se a senha digitada confere com a senha criptografada
if (password_verify($senha, $senhaHash)) {
    // Senha correta: salva dados na sessão
    $_SESSION['user_id']  = $id_usuario;
    $_SESSION['email']    = $email;
    $_SESSION['nome']     = $nome;
    $_SESSION['is_admin'] = $is_admin;
    // Redireciona para a página certa
    if ($is_admin == 1) {
        header("Location: ../administrador/index.php"); // Admin vai para painel
    } else {
        header("Location: ../feed.php"); // Usuário comum vai para o feed
    }
    exit;
} else {
    // Senha errada: volta ao login com erro na senha
    header("Location: ../index.php?erro_senha=1");
    exit;
}
?>