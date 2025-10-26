<?php
$bdServidor = "localhost";
$bdUsuario  = "root";
$bdSenha    = "";
$bdBanco    = "nac_portal";
$conexao = mysqli_connect($bdServidor, $bdUsuario, $bdSenha, $bdBanco);
if (!$conexao) {
    die("Erro ao conectar no banco: " . mysqli_connect_error());
}
?>
