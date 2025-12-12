<?php
session_start();
require_once "../conexao.php";
// Proteção: só admin entra
if (!isset($_SESSION['user_id']) or !isset($_SESSION['is_admin']) or $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// ======================== AÇÕES ========================
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
    $_SESSION['admin_status'] = $status; // aprovada ou rejeitada
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
            d.id_denuncia,
            d.id_publicacao,
            d.categoria,
            d.data_denuncia,
            COALESCE(p.titulo, 'Publicação excluída') AS titulo,
            p.caminho_arquivo,
            p.tipo_arquivo,
            COALESCE(u.nome, 'Usuário excluído') AS denunciante
        FROM denuncia d
        LEFT JOIN publicacao p ON d.id_publicacao = p.id_publicacao AND p.deleted_at IS NULL
        LEFT JOIN usuario u ON d.id_usuario = u.id_usuario
        WHERE d.status = 'pendente'
        ORDER BY d.data_denuncia DESC";

$res = mysqli_query($conexao, $sql);
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

<!-- ✅ NOVO MODAL — CONFIRMAR MANUTENÇÃO -->
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
<!-- FIM DO NOVO MODAL -->

<?php if (mysqli_num_rows($res) == 0): ?>
    <div class="card-panel center teal lighten-4">
        <h5>Nenhuma denúncia pendente!</h5>
    </div>

<?php else: ?>
    <?php while ($d = mysqli_fetch_assoc($res)): ?>

        <div class="card denuncia-card status-pendente z-depth-2">
            <div class="card-content">
                <span class="card-title"><strong><?= $d['titulo'] ?></strong></span>

                <div class="denuncia-info">
                    <p><strong>Denunciante:</strong> <?= $d['denunciante'] ?></p>
                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($d['data_denuncia'])) ?></p>

                    <p><strong>Motivo:</strong>
                        <span class="chip red white-text"><?= ucfirst($d['categoria']) ?></span>
                    </p>
                </div>

                <?php if ($d['caminho_arquivo']): ?>
                <div class="media-container">
                    <?php if ($d['tipo_arquivo'] == 'imagem'): ?>
                        <img src="../uploads/<?= $d['caminho_arquivo'] ?>" class="materialboxed responsive-img">
                    <?php elseif ($d['tipo_arquivo'] == 'video'): ?>
                        <video controls class="responsive-video">
                            <source src="../uploads/<?= $d['caminho_arquivo'] ?>" type="video/mp4">
                        </video>
                    <?php elseif ($d['tipo_arquivo'] == 'audio'): ?>
                        <audio controls style="width:100%;">
                            <source src="../uploads/<?= $d['caminho_arquivo'] ?>">
                        </audio>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="btn-container">

                    <!-- EXCLUIR -->
                    <a href="#modal-confirm-exclusao"
                        class="btn red modal-trigger btn-aprovar-exclusao"
                        data-id="<?= $d['id_denuncia'] ?>"
                        data-titulo="<?= $d['titulo'] ?>">
                        Remover
                    </a>

                    <!-- MANTER (abre modal novo) -->
                    <a href="#modal-confirm-manter"
                        class="btn green modal-trigger btn-manter"
                        data-id="<?= $d['id_denuncia'] ?>"
                        data-titulo="<?= $d['titulo'] ?>">
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
<script src="../js/materialize.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================
    // INICIALIZAÇÃO DOS MODAIS
    // ============================
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);

    // ============================
    // MODAL DE FEEDBACK AUTOMÁTICO
    // ============================
    <?php if (isset($_GET['modal']) && isset($_SESSION['admin_msg'])): ?>

        var titulo = "";
        var mensagem = "<?= $_SESSION['admin_msg'] ?>";
        var status = "<?= $_SESSION['admin_status'] ?>";

        if (status === "aprovada") {
            titulo = "Publicação Removida";
        } else {
            titulo = "Denúncia Rejeitada";
        }

        document.getElementById("titulo-feedback").innerHTML = titulo;
        document.getElementById("texto-feedback").innerHTML = mensagem;

        var elem = document.getElementById("modal-feedback");
        var instance = M.Modal.getInstance(elem);
        instance.open();

        <?php 
            unset($_SESSION['admin_msg']);
            unset($_SESSION['admin_status']);
        ?>

    <?php endif; ?>

    // ============================
    // CONFIRMAR EXCLUSÃO
    // ============================
    var confirmExcluirBtn = document.getElementById('confirm-exclusao-btn');

    document.querySelectorAll('.btn-aprovar-exclusao').forEach(btn => {
        btn.addEventListener('click', function() {

            var id = this.dataset.id;
            var titulo = this.dataset.titulo;

            document.getElementById("texto-excluir").innerHTML =
                'Tem certeza que deseja <strong>REMOVER PERMANENTEMENTE</strong> a publicação:<br><strong>"' 
                + titulo + '"</strong>?';

            confirmExcluirBtn.href = 'denunciar.php?acao=aprovar&id=' + id;
        });
    });

    // ============================
    // CONFIRMAR MANUTENÇÃO
    // ============================
    var confirmManterBtn = document.getElementById('confirm-manter-btn');

    document.querySelectorAll('.btn-manter').forEach(btn => {
        btn.addEventListener('click', function() {

            var id = this.dataset.id;
            var titulo = this.dataset.titulo;

            document.getElementById("texto-manter").innerHTML =
                'Deseja realmente <strong>MANTER</strong> a publicação:<br><strong>"' 
                + titulo + '"</strong>?';

            confirmManterBtn.href = 'denunciar.php?acao=rejeitar&id=' + id;
        });
    });

});
</script>
<?php include_once "footer.php"; ?>
</body>
</html>
