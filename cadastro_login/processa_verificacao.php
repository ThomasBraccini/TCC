<?php
session_start(); 
require_once "../conexao.php"; 
$email = $_SESSION['email_verificacao'];
if (isset($_POST['codigo'])) {
    $codigo_digitado = $_POST['codigo'];
} else {
    $codigo_digitado = '';
}
// Código não pode estar vazio
if ($codigo_digitado === '') {
    header("Location: verificar_email.php?error=Digite o código de verificação.");
    exit; 
}
// Busca os dados do usuário pelo e-mail (código correto e data de expiração)
$sql = "SELECT id_usuario, codigo_verificacao, codigo_expira_em FROM usuario WHERE email = '$email'";
$resultado = mysqli_query($conexao, $sql);
if ($resultado) {
    $registro = mysqli_fetch_assoc($resultado);
    // Se encontrou o usuário no banco
    if ($registro) {
        $id_usuario       = $registro['id_usuario'];
        $codigo_correto   = $registro['codigo_verificacao'];
        $expira_em        = $registro['codigo_expira_em']; // Timestamp de expiração
        $encontrado       = true;
    } else {
        $encontrado = false;
    }
    mysqli_free_result($resultado);
} else {
    $encontrado = false; // Erro na consulta
}
// Se o usuário não foi encontrado
if (!$encontrado) {
    header("Location: registro.php?error=Usuário não encontrado. Faça o cadastro novamente.");
    exit;
}
// Verifica se o código já expirou (mais de 5 minutos)
if (time() > $expira_em) {
    // Apaga o cadastro não verificado
    $delete = "DELETE FROM usuario WHERE id_usuario = $id_usuario AND verificado = 0";
    mysqli_query($conexao, $delete);
    // Remove o e-mail temporário da sessão
    unset($_SESSION['email_verificacao']);
    header("Location: registro.php?error=Código expirou. Faça o cadastro novamente.");
    exit;
}
// Verifica se o código digitado está errado
if ($codigo_digitado != $codigo_correto) {
    header("Location: verificar_email.php?error=Código incorreto. Tente novamente.");
    exit;
}
// Código correto e dentro do prazo: marca o e-mail como verificado
$update = "UPDATE usuario SET verificado = 1, codigo_verificacao = NULL, codigo_expira_em = NULL WHERE id_usuario = $id_usuario";
if (mysqli_query($conexao, $update)) {
    // Busca o nome do usuário para colocar na sessão
    $sql_dados = "SELECT nome FROM usuario WHERE id_usuario = $id_usuario";
    $resultado_dados = mysqli_query($conexao, $sql_dados);
    if ($resultado_dados) {
        $registro = mysqli_fetch_assoc($resultado_dados);
        $nome = $registro['nome'];
        mysqli_free_result($resultado_dados);
    }
    // Cria a sessão do usuário logado
    $_SESSION['user_id']  = $id_usuario;
    $_SESSION['email']    = $email;
    $_SESSION['nome']     = $nome;
    // Remove o e-mail temporário da sessão
    unset($_SESSION['email_verificacao']);
    // Prepara um modal de sucesso para mostrar na página de login
    $_SESSION['modal_sucesso'] = [
        'titulo'   => 'E-mail Verificado!',
        'mensagem' => 'Seu e-mail foi verificado com sucesso. Agora você pode fazer login.',
        'botao'    => 'Ir para Login',
        'link'     => 'index.php'
    ];
    // Redireciona para a página inicial com parâmetro de sucesso
    header("Location: ../index.php?email_verificado=1");
    exit;
} else {
    // Erro ao atualizar o banco
    header("Location: registro.php?error=Erro ao finalizar cadastro. Tente novamente.");
    exit;
}
?>