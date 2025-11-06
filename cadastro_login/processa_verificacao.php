<?php
session_start();
require_once "../conexao.php";

$email = $_SESSION['email_verificacao'];
if (isset($_POST['codigo'])) {
    $codigo_digitado = $_POST['codigo'];
} else {
    $codigo_digitado = '';
}

if ($codigo_digitado === '') {
    header("Location: verificar_email.php?error=Digite o código de verificação.");
    exit;
}
$sql = "SELECT id_usuario, codigo_verificacao, codigo_expira_em FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $sql);
if ($resultado) {
    $registro = mysqli_fetch_assoc($resultado);
    if ($registro) {
        $id_usuario = $registro['id_usuario'];
        $codigo_correto = $registro['codigo_verificacao'];
        $expira_em = $registro['codigo_expira_em'];
        $encontrado = true;
    } else {
        $encontrado = false;
    }
    mysqli_free_result($resultado);
} else {
    $encontrado = false;
}

if (!$encontrado) {
    header("Location: registro.php?error=Usuário não encontrado. Faça o cadastro novamente.");
    exit;
}
if (time() > $expira_em) {
    $delete = "DELETE FROM usuario WHERE id_usuario = $id_usuario AND verificado = 0";
    mysqli_query($conexao, $delete);
    
    unset($_SESSION['email_verificacao']);
    header("Location: registro.php?error=Código expirou. Faça o cadastro novamente.");
    exit;
}
if ($codigo_digitado != $codigo_correto) {
    header("Location: verificar_email.php?error=Código incorreto. Tente novamente.");
    exit;
}

$update = "UPDATE usuario SET verificado = 1, codigo_verificacao = NULL, codigo_expira_em = NULL WHERE id_usuario = $id_usuario";

if (mysqli_query($conexao, $update)) {
    // BUSCAR DADOS COMPLETOS DO USUÁRIO PARA A SESSÃO
    $sql_dados = "SELECT nome FROM usuario WHERE id_usuario = $id_usuario";
    $resultado_dados = mysqli_query($conexao, $sql_dados);
    if ($resultado_dados) {
        $registro = mysqli_fetch_assoc($resultado_dados);
        $nome = $registro['nome'];
        mysqli_free_result($resultado_dados);
    }
    
    // CRIAR SESSÃO DO USUÁRIO LOGADO
    $_SESSION['user_id'] = $id_usuario;
    $_SESSION['email'] = $email;
    $_SESSION['nome'] = $nome;
    
    unset($_SESSION['email_verificacao']);
    
    // MODAL DE SUCESSO (ADICIONADO)
    $_SESSION['modal_sucesso'] = [
        'titulo' => 'E-mail Verificado!',
        'mensagem' => 'Seu e-mail foi verificado com sucesso. Agora você pode fazer login.',
        'botao' => 'Ir para Login',
        'link' => '../feed.php'
    ];
    header("Location: ../index.php?email_verificado=1");    exit;
} else {
    header("Location: registro.php?error=Erro ao finalizar cadastro. Tente novamente.");
    exit;
}
?>