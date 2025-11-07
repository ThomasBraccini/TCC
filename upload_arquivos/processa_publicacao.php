<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$titulo = $_POST["titulo"];
$descricao = $_POST["descricao"];
$id_usuario = $_SESSION['user_id'];

// Processamento do arquivo
$pasta = "../uploads/";
$nomeArquivo = md5(string: time());
$nomeCompleto = $_FILES["arquivo"]["name"];
$nomeSeparado = explode('.', $nomeCompleto);
$ultimaPosicao = count($nomeSeparado) - 1;
$extensao = $nomeSeparado[$ultimaPosicao];
$nomeArquivoExtensao = $nomeArquivo . "." . $extensao;
$extensao = strtolower($extensao);
$tiposPermitidos = ["jpg", "png", "jpeg", "gif", "mp4", "avi", "mp3", "wav"];
if (!in_array($extensao, $tiposPermitidos)) {
    header("Location: publicar_arte.php?error=Extensão de arquivo não permitida");
    exit;
}

// Tamanho máximo
$tamanhoMaximo = in_array($extensao, ["mp4", "avi"]) ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
if ($_FILES["arquivo"]['size'] > $tamanhoMaximo) {
    header("Location: publicar_arte.php?error=Arquivo muito grande");
    exit;
}

// Determina tipo de arquivo
if (in_array($extensao, ["jpg", "png", "jpeg", "gif"])) {
    $tipo_arquivo = "imagem";
} elseif (in_array($extensao, ["mp4", "avi"])) {
    $tipo_arquivo = "video";
} else {
    $tipo_arquivo = "audio";
}

// Move arquivo
$feitoUpload = move_uploaded_file($_FILES["arquivo"]["tmp_name"], $pasta . $nomeArquivoExtensao);

if ($feitoUpload) {
    // CORREÇÃO: Nome correto da tabela - apenas "publicacao"
    $sql = "INSERT INTO publicacao (id_usuario_fk, titulo, descricao, caminho_arquivo, tipo_arquivo, mime_type, tamanho_bytes, data_publicacao) VALUES ('$id_usuario', '$titulo', '$descricao', '$nomeArquivoExtensao', '$tipo_arquivo', '" . $_FILES["arquivo"]["type"] . "', '" . $_FILES["arquivo"]["size"] . "', NOW())";
    
    if (mysqli_query($conexao, $sql)) {
        header("Location: publicar_arte.php?success=Obra publicada com sucesso!");
    } else {
        header("Location: publicar_arte.php?error=Erro ao salvar no banco: " . mysqli_error($conexao));
    }
} else {
    header("Location: publicar_arte.php?error=Erro ao fazer upload do arquivo");
}
exit;
?>