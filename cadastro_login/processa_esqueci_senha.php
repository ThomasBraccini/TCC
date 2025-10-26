<?php
session_start();
require_once "../conexao.php"; 
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;

$email = $_POST['email'];
if ($email === '') {
    // CORRETO: 'esqueci_senha.php' está na mesma pasta
    header("Location: esqueci_senha.php?error=Digite seu e-mail.");
    exit;
}

// Verifica se o e-mail existe
$query = "SELECT id_usuario, nome FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $id_usuario = $registro['id_usuario'];
    $nome = $registro['nome'];
    $encontrou = true;
} else {
    $encontrou = false;
}

mysqli_free_result($resultado);
// Sempre mostra a mesma mensagem por segurança
if (!$encontrou) {
    // CORRETO: 'esqueci_senha.php' está na mesma pasta
    header("Location: esqueci_senha.php");
    exit;
}

// Gera um token único
$token = md5(rand());
$expira_em = time() + 300; // 5 minutos

// Salva o token no banco
$update = "UPDATE usuario SET token_recuperacao = '$token', token_expira_em = $expira_em WHERE id_usuario = $id_usuario";
mysqli_query($conexao, $update);

// Detecta a URL base automaticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']); // Pega o diretório do script atual (ex: /TCC/cadastro_login)
$base_url = $protocol . "://" . $host . $path;
$base_url = rtrim($base_url, '/\\'); // Remove barras no final

// Envia o e-mail com o link
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thomas.silveira.braccini@gmail.com';
    $mail->Password   = 'okau zbvu qcno nrqa';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('noreply@nacportal.com', 'NAC Portal');
    $mail->addAddress($email, $nome);
    $mail->isHTML(true);
    $mail->Subject = 'Recuperação de Senha - NAC Portal';
    $link = $base_url . "/redefinir_senha.php?token=$token";
    $mail->Body    = "Olá $nome,<br><br>Clique no link abaixo para redefinir sua senha:<br><br><a href='$link'>Redefinir Senha</a><br>Este link expira em 5 minutos.<br><br>Atenciosamente,<br>Equipe NAC Portal";
    $mail->AltBody = "Olá $nome,\n\nClique no link para redefinir sua senha:\n$link\n\nEste link expira em 1 hora.\n\nAtenciosamente,\nEquipe NAC Portal";
    $mail->send();
    header("Location: esqueci_senha.php");
    exit;
} catch (Exception $e) {
    header("Location: esqueci_senha.php?error=Erro ao enviar e-mail. Tente novamente.");
    exit;
}
?>