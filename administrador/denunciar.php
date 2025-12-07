<?php
session_start();
require_once "../conexao.php";

// Proteção: só admin entra
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// ======================== AÇÕES ========================
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id_denuncia = (int)$_GET['id'];
    $acao = $_GET['acao'] === 'aprovar' ? 'aprovar' : 'rejeitar';

    if ($acao === 'aprovar') {
        // Remove a publicação
        $stmt = $conexao->prepare("SELECT id_publicacao FROM denuncia WHERE id_denuncia = ?");
        $stmt->bind_param("i", $id_denuncia);
        $stmt->execute();
        $pub = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($pub) {
            $stmt2 = $conexao->prepare("UPDATE publicacao SET deleted_at = NOW() WHERE id_publicacao = ?");
            $stmt2->bind_param("i", $pub['id_publicacao']);
            $stmt2->execute();
            $stmt2->close();
        }
        $status = 'aprovada';
        $msg = "Publicação removida com sucesso!";
    } else {
        $status = 'rejeitada';
        $msg = "Denúncia rejeitada. Publicação mantida.";
    }

    // Atualiza a denúncia
    $stmt3 = $conexao->prepare("UPDATE denuncia SET status = ?, id_admin_analisou = ?, data_analise = NOW() WHERE id_denuncia = ?");
    $stmt3->bind_param("sii", $status, $_SESSION['user_id'], $id_denuncia);
    $stmt3->execute();
    $stmt3->close();

    $_SESSION['admin_msg'] = $msg;
    header("Location: denunciar.php");
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
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
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
        .status-pendente { 
            border-left: 5px solid #ff9800; 
            background: #fffde7; 
        }
        .modal-confirm { 
            border-radius: 20px; 
            overflow: hidden; 
        }
        .modal-confirm-content { 
            padding: 40px 30px !important; 
        }
        .card-content {
            padding: 20px !important;
        }
        .card-title {
            font-size: 1.3rem !important;
            margin-bottom: 10px !important;
        }
        .denuncia-info p {
            margin: 5px 0 !important;
            font-size: 0.95rem;
        }
        .chip {
            height: 24px !important;
            line-height: 24px !important;
            font-size: 0.85rem !important;
            margin-top: 5px !important;
        }
        .media-container {
            margin-top: 15px;
            max-height: 250px;
            overflow: hidden;
            border-radius: 8px;
        }
        .media-container img {
            max-height: 250px;
            object-fit: cover;
        }
        .media-container video,
        .media-container audio {
            max-height: 250px;
        }
        .btn-container {
            margin-top: 20px !important;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-container .btn {
            padding: 0 15px !important;
            height: 36px !important;
            line-height: 36px !important;
            font-size: 0.9rem !important;
        }
        .btn-container .btn i {
            font-size: 1.1rem !important;
            margin-right: 5px !important;
        }
    </style>
</head>
<body class="grey lighten-4">

<?php include_once "../header.php"; ?>

<div class="container" style="margin-top:30px;">
    <h4 class="center teal-text text-darken-2">
        <i class="material-icons left">flag</i> Revisar Denúncias Pendentes
    </h4>

    <?php if (isset($_SESSION['admin_msg'])): ?>
        <div class="card-panel teal lighten-4 center white-text">
            <strong><?= $_SESSION['admin_msg'] ?></strong>
        </div>
        <?php unset($_SESSION['admin_msg']); ?>
    <?php endif; ?>

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
    if (!$res) die("Erro: " . mysqli_error($conexao));
    ?>

    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
    <div id="modal-confirm-exclusao" class="modal modal-confirm">
        <div class="modal-content center modal-confirm-content">
            <i class="material-icons large red-text" style="font-size: 5rem;">warning</i>
            <h5 class="red-text text-darken-2">Confirmar Exclusão</h5>
            <p>Tem certeza que deseja <strong>REMOVER PERMANENTEMENTE</strong> esta publicação?</p>
            <p class="grey-text">Esta ação <strong>não pode ser desfeita</strong> e a publicação será removida do sistema.</p>
            
            <div class="modal-footer" style="border-top: none; padding: 20px 0 0 0;">
                <a href="#!" class="modal-close waves-effect btn-flat grey-text">Cancelar</a>
                <a id="confirm-exclusao-btn" href="#" class="btn red waves-effect waves-light">
                    <i class="material-icons left">delete_forever</i> Sim, Remover
                </a>
            </div>
        </div>
    </div>

    <?php if (mysqli_num_rows($res) == 0): ?>
        <div class="card-panel center teal lighten-4">
            <i class="material-icons large teal-text">thumb_up</i>
            <h5>Nenhuma denúncia pendente!</h5>
            <p>Tudo tranquilo na comunidade</p>
        </div>
    <?php else: ?>
        <?php while ($d = mysqli_fetch_assoc($res)): ?>
            <div class="card denuncia-card status-pendente z-depth-2">
                <div class="card-content">
                    <span class="card-title"><strong><?= htmlspecialchars($d['titulo']) ?></strong></span>
                    
                    <div class="denuncia-info">
                        <p><strong>Denunciante:</strong> <?= htmlspecialchars($d['denunciante']) ?></p>
                        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($d['data_denuncia'])) ?></p>
                        
                        <p><strong>Motivo:</strong>
                            <span class="chip red white-text">
                                <?= ucfirst(htmlspecialchars($d['categoria'])) ?>
                            </span>
                        </p>
                    </div>

                    <?php if ($d['caminho_arquivo']): ?>
                        <div class="media-container">
                            <?php if ($d['tipo_arquivo'] == 'imagem'): ?>
                                <img src="../uploads/<?= htmlspecialchars($d['caminho_arquivo']) ?>" 
                                     class="materialboxed responsive-img">
                            <?php elseif ($d['tipo_arquivo'] == 'video'): ?>
                                <video controls class="responsive-video">
                                    <source src="../uploads/<?= htmlspecialchars($d['caminho_arquivo']) ?>" type="video/mp4">
                                </video>
                            <?php elseif ($d['tipo_arquivo'] == 'audio'): ?>
                                <audio controls style="width:100%;">
                                    <source src="../uploads/<?= htmlspecialchars($d['caminho_arquivo']) ?>">
                                </audio>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="btn-container">
                        <!-- Botão Aprovar Exclusão - Agora abre modal -->
                        <a href="#modal-confirm-exclusao" 
                           class="btn red waves-effect waves-light modal-trigger btn-aprovar-exclusao"
                           data-id="<?= $d['id_denuncia'] ?>"
                           data-titulo="<?= htmlspecialchars($d['titulo']) ?>">
                            <i class="material-icons left">delete_forever</i> Remover
                        </a>
                        
                        <!-- Botão Rejeitar - Mantém o comportamento original -->
                        <a href="denunciar.php?acao=rejeitar&id=<?= $d['id_denuncia'] ?>" 
                           class="btn green darken-1 waves-effect waves-light">
                            <i class="material-icons left">thumb_up</i> Manter
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <div class="center" style="margin:40px 0;">
        <a href="index.php" class="btn grey darken-2 waves-effect waves-light">
            <i class="material-icons left">arrow_back</i> Voltar ao Painel
        </a>
    </div>
</div>

<script src="../js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa Materialize
        M.AutoInit();
        
        // Inicializa modais
        var modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
        
        // Inicializa materialbox para imagens
        var materialboxes = document.querySelectorAll('.materialboxed');
        M.Materialbox.init(materialboxes);
        
        // Configura o modal de confirmação
        var confirmModal = M.Modal.getInstance(document.getElementById('modal-confirm-exclusao'));
        var confirmBtn = document.getElementById('confirm-exclusao-btn');
        
        // Quando clicar em qualquer botão "Aprovar Exclusão"
        document.querySelectorAll('.btn-aprovar-exclusao').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Obtém os dados da publicação
                var idDenuncia = this.getAttribute('data-id');
                var titulo = this.getAttribute('data-titulo');
                
                // Atualiza o conteúdo do modal com o título da publicação
                var modalContent = confirmModal.el.querySelector('.modal-content p');
                modalContent.innerHTML = 'Tem certeza que deseja <strong>REMOVER PERMANENTEMENTE</strong> a publicação:<br><strong>"' + titulo + '"</strong>?';
                
                // Configura o link de confirmação
                confirmBtn.href = 'denunciar.php?acao=aprovar&id=' + idDenuncia;
                
                // Abre o modal
                confirmModal.open();
            });
        });
        
        // Quando o modal for fechado, limpa o link de confirmação
        confirmModal.el.addEventListener('modalClose', function() {
            confirmBtn.href = '#';
        });
    });
</script>
</body>
</html>