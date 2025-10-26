<?php
session_start();
if (!isset($_SESSION['email_verificacao'])) {
    header("Location: registro.php");
    exit;
}

$email = $_SESSION['email_verificacao'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificar E-mail - NAC Portal</title>
</head>
<body>
        Verificação de E-mail
        

            <h3> CÓDIGO DE VERIFICAÇÃO:</h3>
            <p>Copie e cole no campo abaixo</p> 
        <p>E-mail sendo verificado:</strong> <?php echo ($email); ?></p>
        
        <form action="processa_verificacao.php" method="POST">
            <div class="form-group">
                <label for="codigo"><strong>Digite o código de 6 dígitos:</strong></label><br>
                <input type="text" name="codigo" id="codigo" required maxlength="6" pattern="[0-9]{6}" placeholder="123456">
            </div>
            
            <input type="submit" value=" Verificar E-mail" class="btn">
        </form>
        
        <p><a href="../index.php">Voltar</a></p>
    </div>
</body>
</html>