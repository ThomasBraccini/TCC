<?php
session_start(); 
require_once "../conexao.php"; 
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
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
    $preferencias = $_POST['preferencias'];
} else {
    $preferencias = '';
}
// Validação: todos os campos obrigatórios devem estar preenchidos
if ($nome === '' || $email === '' || $senha === '' || $confirma_senha === '') {
    header("Location: registro.php?error=Preencha todos os campos obrigatórios.");
    exit;
}
// Validação: senha precisa ter no mínimo 8 caracteres
if (strlen($senha) < 8) {
    header("Location: registro.php?error=A senha deve ter no mínimo 8 caracteres.");
    exit;
}
// Validação: e-mail deve ser válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: registro.php?error=E-mail inválido.");
    exit;
}
// Validação: as duas senhas devem ser iguais
if ($senha !== $confirma_senha) {
    header("Location: registro.php?error=As senhas não conferem.");
    exit;
}
// Verifica se o e-mail já está cadastrado
$query = "SELECT id_usuario FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $_SESSION['mensagem_erro'] = "E-mail já cadastrado.";
    header("Location: registro.php?email_existente=1");
    exit;
}
mysqli_free_result($resultado); // Libera memória
// Upload da foto de perfil (opcional)
$pasta_fotos = "../meu_perfil/fotos_perfil/";
$foto_perfil = ""; // Fica vazio se não enviar foto
if (isset($_FILES["foto_perfil"]) && $_FILES["foto_perfil"]["error"] === 0) {
    $nomeArquivo = md5(time()); // Nome único para evitar conflitos
    $nomeCompleto = $_FILES["foto_perfil"]["name"];
    $partes = explode('.', $nomeCompleto);
    $extensao = strtolower(end($partes)); // Pega a extensão
    $permitidos = ["jpg", "jpeg", "png"];
    // Valida extensão da imagem
    if (!in_array($extensao, $permitidos)) {
        header("Location: registro.php?error=Extensão de imagem não permitida.");
        exit;
    }
    // Valida tamanho máximo (5MB)
    if ($_FILES["foto_perfil"]["size"] > 5 * 1024 * 1024) {
        header("Location: registro.php?error=Imagem muito grande (máx 5MB).");
        exit;
    }
    $nomeFinal = $nomeArquivo . "." . $extensao;
    // Move a imagem para a pasta definitiva
    if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $pasta_fotos . $nomeFinal)) {
        $foto_perfil = "meu_perfil/fotos_perfil/" . $nomeFinal; // Caminho para salvar no banco
    }
}
// Gera código de verificação de 6 dígitos
$codigo = rand(100000, 999999);
$expira_em = time() + 300; // Expira em 5 minutos
// Criptografa a senha de forma segura
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
// Insere o novo usuário no banco (ainda não verificado)
$sql = "INSERT INTO usuario 
        (nome, email, senha, preferencias, verificado, codigo_verificacao, codigo_expira_em, foto_perfil) 
        VALUES ('$nome', '$email', '$senhaHash', '$preferencias', 0, $codigo, $expira_em, '$foto_perfil')";
$resultado = mysqli_query($conexao, $sql);
if (!$resultado) {
    die('Erro ao cadastrar: ' . mysqli_error($conexao)); // Mostra erro se falhar
}
// Envia o código por e-mail
$mail = new PHPMailer(true);
try {
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
    $mail->Subject = 'Confirmação de Cadastro - NAC Portal';
    $mail->Body    = "Olá $nome,<br><br>Seu código de verificação é: <strong>$codigo</strong><br><br>Ele expira em 5 minutos.<br><br>Atenciosamente,<br>Equipe NAC Portal";
    $mail->AltBody = "Olá $nome,\n\nSeu código de verificação é: $codigo\n\nEle expira em 5 minutos.\n\nAtenciosamente,\nEquipe NAC Portal";
    $mail->send(); // Envia o e-mail
    // Salva o e-mail na sessão para usar na página de verificação
    $_SESSION['email_verificacao'] = $email;
    // Redireciona para a página de verificação do código
    header("Location: registro.php?success=1");
    exit;
} catch (Exception $e) {
    // Se der erro no envio, salva o e-mail na sessão mesmo assim
    $_SESSION['email_verificacao'] = $email;
    header("Location: registro.php?codigo=$codigo&error=Erro no e-mail.");
    exit;
}
?>