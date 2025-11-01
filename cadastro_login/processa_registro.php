<?php
session_start();
require_once "../conexao.php";
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;

// ---------------------------------------------------------------------
// 1. Recebe dados
// ---------------------------------------------------------------------
if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
}
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (isset($_POST['senha'])) {
    $senha = $_POST['senha'];
}
if (isset($_POST['confirma_senha'])) {
    $confirma_senha = $_POST['confirma_senha'];
}
if (isset($_POST['preferencias'])) {
    $preferencias = $_POST['preferencias']; // CAPTURA AQUI!
} else {
    $preferencias = ''; // opcional
}

// ---------------------------------------------------------------------
// 2. Validações
// ---------------------------------------------------------------------
if ($nome === '' || $email === '' || $senha === '' || $confirma_senha === '') {
    header("Location: registro.php?error=Preencha todos os campos obrigatórios.");
    exit();
}

if (strlen($senha) < 8) {
    header("Location: registro.php?error=A senha deve ter no mínimo 8 caracteres.");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: registro.php?error=E-mail inválido.");
    exit;
}

if ($senha !== $confirma_senha) {
    header("Location: registro.php?error=As senhas não conferem.");
    exit;
}

// ---------------------------------------------------------------------
// 3. Verifica e-mail duplicado
// ---------------------------------------------------------------------
$query = "SELECT id_usuario FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $registro = mysqli_fetch_assoc($resultado);
    $usuario_encontrado = true;
} else {
    $usuario_encontrado = false;
}
mysqli_free_result($resultado);

if ($usuario_encontrado) {
    $_SESSION['mensagem_erro'] = "E-mail já cadastrado.";
    header("Location: registro.php");
    exit;
}

// ---------------------------------------------------------------------
// 4. Prepara dados
// ---------------------------------------------------------------------
$codigo = rand(100000, 999999);
$expira_em = time() + 300;
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// ---------------------------------------------------------------------
// 5. INSERT CORRIGIDO: 7 colunas = 7 valores
// ---------------------------------------------------------------------
$sql = "INSERT INTO usuario 
        (nome, email, senha, preferencias, verificado, codigo_verificacao, codigo_expira_em) 
        VALUES ('$nome', '$email', '$senhaHash', '$preferencias', 0, $codigo, $expira_em)";

$resultado = mysqli_query($conexao, $sql);
if (!$resultado) {
    die("Erro ao cadastrar: " . mysqli_error($conexao));
}

// ---------------------------------------------------------------------
// 6. Envia e-mail
// ---------------------------------------------------------------------
$mail = new PHPMailer(true);
try {
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
    $mail->Subject = 'Confirmação de Cadastro - NAC Portal';
    $mail->Body    = "Olá $nome,<br><br>Seu código de verificação é: <strong>$codigo</strong><br><br>Ele expira em 5 minutos.<br><br>Atenciosamente,<br>Equipe NAC Portal";
    $mail->AltBody = "Olá $nome,\n\nSeu código de verificação é: $codigo\n\nEle expira em 5 minutos.\n\nAtenciosamente,\nEquipe NAC Portal";
    $mail->send();

    $_SESSION['email_verificacao'] = $email;
    header("Location: verificar_email.php?success=Código enviado! Verifique seu e-mail.");
    exit;
} catch (Exception $e) {
    $_SESSION['email_verificacao'] = $email;
    header("Location: verificar_email.php?codigo=$codigo&error=Erro no e-mail. Use este código:");
    exit;
}
?>