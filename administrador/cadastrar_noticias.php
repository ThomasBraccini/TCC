<?php
session_start();
require_once "../conexao.php";

// === PROTEÇÃO: SÓ ADMINISTRADOR ACESSA ===
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

$mensagem = "";
$classeMensagem = ""; // 'green' ou 'red' para o alerta

// === PROCESSAR O FORMULÁRIO QUANDO ENVIADO ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $conteudo = trim($_POST['conteudo']);
    $status = $_POST['status'];
    $id_autor = $_SESSION['user_id'];

    // Validação básica
    if (empty($titulo) || empty($conteudo)) {
        $mensagem = "Preencha o título e o conteúdo!";
        $classeMensagem = "red";
    } else {
        // Tratar upload de imagem (se houver)
        $nomeImagem = null;
        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] == 0) {
            $extensao = pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION);
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($extensao), $extensoesPermitidas)) {
                $nomeImagem = uniqid('noticia_') . '.' . $extensao;
                $caminhoDestino = "../uploads/noticias/" . $nomeImagem;
                
                // Criar pasta se não existir
                if (!is_dir('../uploads/noticias')) {
                    mkdir('../uploads/noticias', 0777, true);
                }
                move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $caminhoDestino);
            }
        }

        // Inserir no banco (USANDO PREPARED STATEMENT PARA SEGURANÇA)
        $sql = "INSERT INTO noticia (titulo, conteudo, imagem_capa, id_autor, status, data_publicacao) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sssis", $titulo, $conteudo, $nomeImagem, $id_autor, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensagem = ($status == 'publicado') 
                ? "Notícia publicada com sucesso!" 
                : "Rascunho salvo com sucesso!";
            $classeMensagem = "green";
            
            // Limpar os campos do formulário após sucesso
            $_POST['titulo'] = $_POST['conteudo'] = '';
        } else {
            $mensagem = "Erro ao salvar: " . mysqli_error($conexao);
            $classeMensagem = "red";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Notícia • Admin</title>
    <!-- Materialize CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" />
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css" />
    <style>
        /* Estilos específicos para o editor de notícias */
        .card-noticia {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }
        .card-noticia .card-content {
            padding: 30px;
        }
        .input-field input[type=text]:focus, 
        .input-field textarea:focus {
            border-bottom: 2px solid #009688 !important;
            box-shadow: 0 1px 0 0 #009688 !important;
        }
        .btn-large {
            border-radius: 30px;
            padding: 0 40px;
            font-weight: 600;
            text-transform: none;
            height: 50px;
            line-height: 50px;
        }
        .switch label input[type=checkbox]:checked + .lever {
            background-color: #4CAF50;
        }
        .switch label .lever {
            background-color: #ccc;
        }
    </style>
</head>
<body class="grey lighten-4">
    <?php include_once "header.php"; // Usa o header específico do admin ?>

    <main class="container">
        <div class="row">
            <div class="col s12">
                <div class="card card-noticia white">
                    <div class="card-content">
                        <h4 class="teal-text text-darken-2 center" style="margin-bottom: 30px;">
                            <i class="material-icons left">post_add</i> Cadastrar Nova Notícia
                        </h4>

                        <!-- Mensagem de Feedback -->
                        <?php if (!empty($mensagem)): ?>
                            <div class="card-panel <?= $classeMensagem ?> lighten-5 <?= $classeMensagem ?>-text text-darken-4" style="border-radius: 10px;">
                                <i class="material-icons left"><?= ($classeMensagem == 'green') ? 'check_circle' : 'info' ?></i>
                                <?= htmlspecialchars($mensagem) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulário de Cadastro -->
                        <form method="POST" enctype="multipart/form-data" id="formNoticia">
                            <div class="row">
                                <!-- Título -->
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">title</i>
                                    <input id="titulo" name="titulo" type="text" class="validate" 
                                           value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>" 
                                           required>
                                    <label for="titulo">Título da Notícia *</label>
                                </div>

                                <!-- Conteúdo (Editor Simples) -->
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">description</i>
                                    <textarea id="conteudo" name="conteudo" class="materialize-textarea validate" 
                                              required><?= isset($_POST['conteudo']) ? htmlspecialchars($_POST['conteudo']) : '' ?></textarea>
                                    <label for="conteudo">Conteúdo *</label>
                                </div>

                                <!-- Upload de Imagem -->
                                <div class="file-field input-field col s12">
                                    <div class="btn teal lighten-1">
                                        <span><i class="material-icons left">image</i> Imagem de Capa</span>
                                        <input type="file" name="imagem_capa" accept="image/*">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text" 
                                               placeholder="Imagem opcional para chamada da notícia (JPEG, PNG, GIF)">
                                    </div>
                                </div>

                                <!-- Status (Rascunho/Publicado) -->
                                <div class="col s12" style="margin: 20px 0;">
                                    <p><strong>Status:</strong></p>
                                    <div class="switch">
                                        <label>
                                            Rascunho
                                            <input type="checkbox" name="status" value="publicado" checked>
                                            <span class="lever"></span>
                                            Publicar Agora
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="row center" style="margin-top: 40px;">
                                <div class="col s12 m6">
                                    <a href="index.php" class="btn-large grey waves-effect waves-light">
                                        <i class="material-icons left">arrow_back</i> Voltar
                                    </a>
                                </div>
                                <div class="col s12 m6">
                                    <button type="submit" class="btn-large teal waves-effect waves-light">
                                        <i class="material-icons left">save</i> Salvar Notícia
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts Materialize -->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar componentes
            M.updateTextFields();
            M.CharacterCounter.init(document.querySelectorAll('#titulo, #conteudo'));

            // Contador de caracteres para o título (opcional)
            var elems = document.querySelectorAll('textarea');
            M.CharacterCounter.init(elems);
        });

        // Validação básica antes do envio
        document.getElementById('formNoticia').addEventListener('submit', function(e) {
            var titulo = document.getElementById('titulo').value.trim();
            var conteudo = document.getElementById('conteudo').value.trim();
            
            if (!titulo || !conteudo) {
                e.preventDefault();
                M.toast({html: 'Preencha todos os campos obrigatórios!', classes: 'red'});
            }
        });
    </script>
</body>
</html>