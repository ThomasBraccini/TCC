<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'seu_email@gmail.com';
    $mail->Password   = 'sua_senha_de_app';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('teste@nacportal.com', 'Teste');
    $mail->addAddress('seu_email@gmail.com', 'Você');
    $mail->Subject = 'Teste PHPMailer';
    $mail->Body    = 'Se recebeu, PHPMailer funciona!';
    $mail->send();
    echo 'E-mail enviado com PHPMailer!';
} catch (Exception $e) {
    echo "Erro: {$mail->ErrorInfo}";
}
?>