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
// Descobre se o site está usando HTTPS (seguro) ou HTTP (normal)
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocolo = "https";
} else {
    $protocolo = "http";
}
// Pega o nome do site
$dominio = $_SERVER['HTTP_HOST'];
// Pega a pasta onde os arquivos do site estão 
$pasta_do_site = dirname($_SERVER['PHP_SELF']);
// Monta a URL base completa
$url_base = $protocolo . "://" . $dominio . $pasta_do_site;
// Remove barras duplicadas ou desnecessárias do final
$url_base = rtrim($url_base, '/\\');
// Envia o e-mail com o link
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thomas.silveira.braccini@gmail.com';
    $mail->Password   = 'senha de aplicativo aqui';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('noreply@nacportal.com', 'NAC Portal');
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->addAddress($email, $nome);
    $mail->isHTML(true);
    $mail->Subject = 'Recuperação de Senha - NAC Portal';
    $link = $url_base . "/redefinir_senha.php?token=$token";
    $mail->Body    = "Olá $nome,<br><br>Clique no link abaixo para redefinir sua senha:<br><br><a href='$link'>Redefinir Senha</a><br>Este link expira em 5 minutos.<br><br>Atenciosamente,<br>Equipe NAC Portal";
    $mail->AltBody = "Olá $nome,\n\nClique no link para redefinir sua senha:\n$link\n\nEste link expira em 1 hora.\n\nAtenciosamente,\nEquipe NAC Portal";
    $mail->send();
    header("Location: esqueci_senha.php?success=1");
    exit;
} catch (Exception $e) {
    header("Location: esqueci_senha.php?error=Erro ao enviar e-mail. Tente novamente.");
    exit;
}
?>