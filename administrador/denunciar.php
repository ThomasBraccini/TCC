<?php
session_start();
require_once "../conexao.php";
if (!isset($_SESSION['user_id']) or !isset($_SESSION['is_admin']) or $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}
//Verificação da ação solicitada
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id_denuncia = $_GET['id'];  
    $id_admin = $_SESSION['user_id']; 
    if ($_GET['acao'] === 'aprovar') {
        $acao = 'aprovar';
    } else {
        $acao = 'rejeitar';
    }
    if ($acao === 'aprovar') {
        $sql_select = "SELECT id_publicacao FROM denuncia WHERE id_denuncia = $id_denuncia";
        $resto_select = mysqli_query($conexao, $sql_select);
        if ($resto_select && mysqli_num_rows($resto_select) > 0) {
            $publicacao = mysqli_fetch_assoc($resto_select);
            $id_publicacao = $publicacao['id_publicacao'];
            $sql_update = "UPDATE publicacao SET deleted_at = NOW() WHERE id_publicacao = $id_publicacao";
            mysqli_query($conexao, $sql_update);
        }
        $status = 'aprovada';
        $mensagem = "Publicação removida com sucesso!";
    } else {
        $status = 'rejeitada';
        $mensagem = "Denúncia rejeitada. Publicação mantida.";
    }
    $sql_update_denuncia = "UPDATE denuncia 
                            SET status = '$status', 
                                id_admin_analisou = $id_admin, 
                                data_analise = NOW() 
                            WHERE id_denuncia = $id_denuncia";
    mysqli_query($conexao, $sql_update_denuncia);
    $_SESSION['admin_msg'] = $mensagem;
    $_SESSION['admin_status'] = $status;
    header("Location: denunciar.php?modal=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denúncias • Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/css/materialize.min.css"/>
    <link type="text/css" rel="stylesheet" href="/css/style_todos.css"/>
    <style>
        .denuncia-card { 
            border-radius: 12px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            transition: all 0.3s; 
        }
        .denuncia-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.12); 
        }
        .status-pendente { background: #fffde7; }
        .modal-confirm { border-radius: 20px; overflow: hidden; }
        .modal-confirm-content { padding: 40px 30px !important; }
        .card-content { padding: 20px !important; }
        .card-title { font-size: 1.3rem !important; margin-bottom: 10px !important; }
        .denuncia-info p { margin: 5px 0 !important; font-size: 0.95rem; }
        .chip { height: 24px !important; line-height: 24px !important; font-size: 0.85rem !important; margin-top: 5px !important; }
        .media-container {
            margin-top: 15px;
            max-height: 250px;
            overflow: hidden;
            border-radius: 8px;
        }
        .media-container img { max-height: 250px; object-fit: cover; }
        .media-container video,
        .media-container audio { max-height: 250px; }
        .btn-container {
            margin-top: 20px !important;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body class="grey lighten-4">
<?php include_once "header.php"; ?>
<div class="container" style="margin-top:30px;">
    <h4 class="center teal-text text-darken-2">Revisar Denúncias Pendentes</h4>
<?php
$sql = "SELECT 
            denuncia.id_denuncia,
            denuncia.id_publicacao,
            denuncia.categoria,
            denuncia.data_denuncia,
            publicacao.titulo,
            publicacao.caminho_arquivo,
            publicacao.tipo_arquivo,
            usuario.nome
        FROM denuncia
        LEFT JOIN publicacao 
            ON denuncia.id_publicacao = publicacao.id_publicacao
        LEFT JOIN usuario 
            ON denuncia.id_usuario = usuario.id_usuario
        WHERE denuncia.status = 'pendente'
        ORDER BY denuncia.data_denuncia DESC";
$resultado_consulta = mysqli_query($conexao, query: $sql);
?>
<!-- MODAL DE EXCLUSÃO -->
<div id="modal-confirm-exclusao" class="modal modal-confirm">
    <div class="modal-content center modal-confirm-content">
        <i class="material-icons large red-text">warning</i>
        <h5 class="red-text text-darken-2">Confirmar Exclusão</h5>
        <p id="texto-excluir"></p>
        <div class="modal-footer" style="border-top: none; padding: 20px 0 0 0;">
            <a href="#!" class="modal-close waves-effect btn-flat grey-text">Cancelar</a>
            <a id="confirm-exclusao-btn" href="#" class="btn red waves-effect waves-light">Remover</a>
        </div>
    </div>
</div>
<!--CONFIRMAR MANUTENÇÃO -->
<div id="modal-confirm-manter" class="modal modal-confirm">
    <div class="modal-content center modal-confirm-content">
        <h5 class="green-text text-darken-2">Confirmar Manutenção</h5>
        <p id="texto-manter"></p>
        <div class="modal-footer" style="border-top:none; padding:20px 0 0 0;">
            <a href="#!" class="modal-close waves-effect btn-flat grey-text">Cancelar</a>
            <a id="confirm-manter-btn" href="#" class="btn green darken-1 waves-effect waves-light">Manter</a>
        </div>
    </div>
</div>
<!-- verificação de denuncias pendentes -->
<?php if (mysqli_num_rows($resultado_consulta) == 0): ?>
    <div class="card-panel center teal lighten-4">
        <h5>Nenhuma denúncia pendente!</h5>
    </div>
    <!-- se houver denuncias pendentes -->
<?php else: ?>
    <?php while ($denuncia = mysqli_fetch_assoc($resultado_consulta)): ?>
        <div class="card denuncia-card status-pendente z-depth-2">
            <div class="card-content">
                <span class="card-title"><strong><?= $denuncia['titulo'] ?></strong></span>
                <div class="denuncia-info">
                    <p><strong>Denunciante:</strong> <?= $denuncia['nome'] ?></p>
                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($denuncia['data_denuncia'])) ?></p>
                    <p><strong>Motivo:</strong>
                        <span class="chip red white-text"><?= ucfirst($denuncia['categoria']) ?></span>
                    </p>
                </div>
                <!--Exibição de mídia associada à denúncia-->
                <?php if ($denuncia['caminho_arquivo']): ?>
                <div class="media-container">
                    <?php if ($denuncia['tipo_arquivo'] == 'imagem'): ?>
                        <img src="../uploads/<?= $denuncia['caminho_arquivo'] ?>" class="materialboxed responsive-img">
                    <?php elseif ($denuncia['tipo_arquivo'] == 'video'): ?>
                        <video controls class="responsive-video">
                            <source src="../uploads/<?= $denuncia['caminho_arquivo'] ?>" type="video/mp4">
                        </video>
                    <?php elseif ($denuncia['tipo_arquivo'] == 'audio'): ?>
                        <audio controls style="width:100%;">
                            <source src="../uploads/<?= $denuncia['caminho_arquivo'] ?>">
                        </audio>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <!-- Botões de ação -->
                <div class="btn-container">
                    <!-- EXCLUIR -->
                    <a href="#modal-confirm-exclusao"
                        class="btn red modal-trigger btn-aprovar-exclusao"
                        data-id="<?= $denuncia['id_denuncia'] ?>"
                        data-titulo="<?= $denuncia['titulo'] ?>">
                        Remover
                    </a>
                    <!-- MANTER (abre modal novo) -->
                    <a href="#modal-confirm-manter"
                        class="btn green modal-trigger btn-manter"
                        data-id="<?= $denuncia['id_denuncia'] ?>"
                        data-titulo="<?= $denuncia['titulo'] ?>">
                        Manter
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>
    <!-- MODAL DE FEEDBACK -->
    <div id="modal-feedback" class="modal">
        <div class="modal-content center">
            <h4 id="titulo-feedback"></h4>
            <p id="texto-feedback"></p>
        </div>
        <div class="modal-footer">
            <a href="denunciar.php" class="modal-close btn green">OK</a>
        </div>
    </div>
    <script src="../js/materialize.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializa todos os modais da página (Materialize)
        var modais = document.querySelectorAll('.modal');
        M.Modal.init(modais);
        // MODAL DE FEEDBACK (resultado da ação do admin)
        <?php if (isset($_GET['modal']) && isset($_SESSION['admin_msg'])): ?>
            // Recupera mensagem e status enviados pelo PHP
            var mensagem = "<?= $_SESSION['admin_msg'] ?>";
            var status = "<?= $_SESSION['admin_status'] ?>";
            // Define o título conforme a ação realizada
            var titulo = (status === "aprovada") 
                ? "Publicação Removida" 
                : "Denúncia Rejeitada";
            // Insere texto no modal
            document.getElementById("titulo-feedback").innerHTML = titulo;
            document.getElementById("texto-feedback").innerHTML = mensagem;
            // Abre automaticamente o modal de feedback
            var modalFeedback = document.getElementById("modal-feedback");
            M.Modal.getInstance(modalFeedback).open();
            // Remove as mensagens da sessão após exibição
            <?php 
                unset($_SESSION['admin_msg']);
                unset($_SESSION['admin_status']);
            ?>
        <?php endif; ?>
        // CONFIRMAÇÃO DE EXCLUSÃO
        var botaoConfirmarExclusao = document.getElementById('confirm-exclusao-btn');
        // Para cada botão "Remover"
        document.querySelectorAll('.btn-aprovar-exclusao').forEach(function (botao) {
            botao.addEventListener('click', function () {
                // Obtém dados da denúncia clicada
                var idDenuncia = this.dataset.id;
                var tituloPublicacao = this.dataset.titulo;
                // Define o texto do modal de exclusão
                document.getElementById("texto-excluir").innerHTML =
                    'Tem certeza que deseja remover a publicação:<br><strong>"' 
                    + tituloPublicacao + '"</strong>?';
                // Define o link que executa a exclusão
                botaoConfirmarExclusao.href =
                    'denunciar.php?acao=aprovar&id=' + idDenuncia;
            });
        });
        // CONFIRMAÇÃO DE MANUTENÇÃO
        var botaoConfirmarManter = document.getElementById('confirm-manter-btn');
        // Para cada botão "Manter"
        document.querySelectorAll('.btn-manter').forEach(function (botao) {
            botao.addEventListener('click', function () {
                // Obtém dados da denúncia clicada
                var idDenuncia = this.dataset.id;
                var tituloPublicacao = this.dataset.titulo;
                // Define o texto do modal de manutenção
                document.getElementById("texto-manter").innerHTML =
                    'Deseja manter a publicação:<br><strong>"' 
                    + tituloPublicacao + '"</strong>?';
                // Define o link que mantém a publicação
                botaoConfirmarManter.href =
                    'denunciar.php?acao=rejeitar&id=' + idDenuncia;
            });
        });
    });
    </script>
<?php include_once "footer.php"; ?>
</body>
</html>
