<?php
session_start();
require_once "../conexao.php"; // Caminho correto (está dentro da pasta administrador)

// Verifica se é admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../noticias.php");
    exit;
}

// Exclusão de notícia (soft delete)
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id_excluir = (int)$_GET['excluir'];
    $sql_delete = "UPDATE noticias SET ativo = 0 WHERE id_noticia = $id_excluir";
    if (mysqli_query($conexao, $sql_delete)) {
        header("Location: listagem_noticias.php?excluido=ok");
        exit;
    } else {
        echo "<script>alert('Erro ao excluir notícia.');</script>";
    }
}

$sql = "SELECT noticias.id_noticia, 
                noticias.titulo, 
                noticias.subtitulo, 
                noticias.corpo, 
                noticias.caminho_midia, 
                noticias.data_publicacao, 
                noticias.autor 
        FROM noticias
        WHERE noticias.ativo = 1
        ORDER BY noticias.data_publicacao DESC";    

$resultado = mysqli_query($conexao, $sql);
$noticias = [];

if ($resultado) {
    while ($registro = mysqli_fetch_assoc($resultado)) {
        $noticias[] = $registro;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias • Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style_todos.css"/>
</head>
<body>
    <?php include_once "header.php"; ?>
        <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
    <div id="modalConfirmarExclusao" class="modal">
        <div class="modal-content center">
            <i class="material-icons large red-text">warning</i>
            <h5>Confirmar exclusão</h5>
            <p>Tem certeza que deseja excluir esta notícia?<br>Esta ação não pode ser desfeita.</p>
        </div>

        <div class="modal-footer">
            <a class="modal-close btn grey">Cancelar</a>
            <a id="btnConfirmarExcluir" class="btn red">Excluir</a>
        </div>
    </div>

    <!-- MODAL DE SUCESSO APÓS EXCLUSÃO -->
    <div id="modalExcluido" class="modal">
        <div class="modal-content center">
            <i class="material-icons large green-text">check_circle</i>
            <h5>Notícia excluída</h5>
            <p>A notícia foi excluída com sucesso.</p>
        </div>

        <div class="modal-footer">
            <a href="listagem_noticias.php" class="btn green modal-close">OK</a>
        </div>
    </div>
    <main class="container">
        <div class="page-title">
            <h2>Gerenciar Notícias</h2>
            <p>Lista completa das notícias publicadas - área administrativa</p>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="card-panel no-noticias">
                <i class="material-icons">newspaper</i>
                <h5>Nenhuma notícia publicada</h5>
                <p>Em breve teremos novidades para você!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($noticias as $noticia): 
                    $resumo = $noticia['corpo'];
                    $resumo = strlen($resumo) > 150 ? substr($resumo, 0, 150) . '...' : $resumo;
                    $data_formatada = date('d/m/Y', strtotime($noticia['data_publicacao']));
                    $tem_imagem = !empty($noticia['caminho_midia']) && file_exists("../uploads/noticias/" . $noticia['caminho_midia']);
                ?>
                    <div class="col s12 m6 l4">
                        <!-- Link clicável em todo o card (igual ao do usuário) -->
                        <a href="ver_noticias.php?id=<?= $noticia['id_noticia'] ?>" class="black-text" style="text-decoration: none;">
                            <div class="card noticia-card hoverable">
                                <div class="noticia-imagem-container">
                                    <?php if ($tem_imagem): ?>
                                        <img src="../uploads/noticias/<?= $noticia['caminho_midia'] ?>" 
                                            alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                                            class="noticia-imagem">
                                    <?php else: ?>
                                        <div style="background: linear-gradient(135deg, #009688, #4DB6AC); height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <i class="material-icons white-text" style="font-size: 4rem;">newspaper</i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="noticia-conteudo">
                                    <h3 class="noticia-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                                    
                                    <?php if (!empty($noticia['subtitulo'])): ?>
                                        <p class="noticia-subtitulo"><?= $noticia['subtitulo'] ?></p>
                                    <?php endif; ?>
                                    
                                    <p class="noticia-resumo"><?= htmlspecialchars($resumo) ?></p>
                                    
                                    <div class="noticia-meta">
                                        <div class="noticia-data">
                                            <i class="material-icons tiny">calendar_today</i>
                                            <?= $data_formatada ?>
                                        </div>
                                    </div>
                                    <!-- Botão Excluir (fora do link clicável) -->
                                    <div class="card-action right-align" style="padding: 8px 15px; border-top: 1px solid #eee;">
                                        <a href="listagem_noticias.php?excluir=<?= $noticia['id_noticia'] ?>" 
                                            class="btn-small red waves-effect waves-light"
                                            onclick="abrirModalExcluir(<?= $noticia['id_noticia'] ?>); return false;">
                                            <i class="material-icons left">delete</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php include_once "footer.php"; ?>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de gerenciamento de notícias carregada');
        });
    </script>
    <script>
    let excluirID = null;

    document.addEventListener('DOMContentLoaded', function() {
        var modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);

        // Se a URL vier com ?excluido=ok, abre o modal de sucesso
        <?php if (isset($_GET['excluido']) && $_GET['excluido'] == 'ok'): ?>
            var modalOK = M.Modal.getInstance(document.getElementById('modalExcluido'));
            modalOK.open();
        <?php endif; ?>
    });

    // Função para abrir o modal de confirmação
    function abrirModalExcluir(id) {
        excluirID = id;
        var modal = M.Modal.getInstance(document.getElementById('modalConfirmarExclusao'));
        modal.open();
    }

    // Quando clicar no botão confirmar exclusão
    document.addEventListener('click', function(e) {
        if (e.target.id === 'btnConfirmarExcluir') {
            window.location = `listagem_noticias.php?excluir=${excluirID}&excluido=ok`;
        }
    });
    </script>
<?php include_once "footer.php"; ?>
</body>
</html>