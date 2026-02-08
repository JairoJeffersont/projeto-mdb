<?php

use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\DocumentoController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$anoGet = $_GET['ano'] ?? date('Y');
$tipoGet = $_GET['tipo'] ?? '';

$buscaDiretorio = DiretorioController::buscarDiretorio($diretorioId);

if ($buscaDiretorio['status'] != 'success') {
    header('Location: ?section=diretorios');
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm loading-modal" href="?section=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-1 text-primary">Arquivo de documentos | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Arquive o organize todos os documentos desse diretório
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'ano'               => $_POST['ano'] ?? null,
                            'tipo_id'               => $_POST['tipo_id'] ?? null,
                            'titulo'               => $_POST['titulo'] ?? null,
                            'arquivo'               => $_FILES['arquivo'] ?? null,
                            'diretorio_id'               => $diretorioId,
                            'usuario_id'               => $_SESSION['user']['id']
                        ];

                        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>Arquivo não enviado</b></div>';
                        }

                        $result = DocumentoController::criarDocumento($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'conflict') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'formato_nao_permitido'  || $result['status'] == 'tamanho_maximo_excedido') {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>Arquivo não permitido ou muito grande</b></div>';
                        }
                    }

                    ?>


                    <form class="row g-2 align-items-end" method="post" enctype="multipart/form-data">

                        <div class="col-md-1 col-4">
                            <input type="number" class="form-control form-control-sm" name="ano" value="<?= date('Y') ?>" required>
                        </div>

                        <div class="col-md-2 col-6">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="tipo_id" required>
                                    <option value="">Tipo de documento</option>
                                    <?php
                                    $buscaTipos = DocumentoController::listarTodosOsTiposDocumentos($diretorioId);
                                    if ($buscaTipos['status'] == 'success') {
                                        foreach ($buscaTipos['data'] as $tipo) {
                                            echo '<option value="' . $tipo['id'] . '">' . $tipo['descricao'] . '</option>';
                                        }
                                    } else if ($buscaTipos['status'] == 'empty' || $buscaTipos['status'] == 'server_error') {
                                        echo '<option value="">Tipo de documento</option>';
                                    }
                                    ?>

                                </select>
                                <a href="?section=tipos-documentos&diretorio=<?= $diretorioId ?>" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de órgão?">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-4">
                            <input type="text" class="form-control form-control-sm" name="titulo" placeholder="Titulo do documento" required>
                        </div>
                        <div class="col-md-3 col-4">
                            <input type="file" class="form-control form-control-sm" name="arquivo">
                        </div>

                        <div class="col-sm-2 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Deseja adicionar esse tipo de documento?"><i class="bi bi-floppy"></i> Salvar </button>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary"><i class="bi bi-file-text"></i> Comissões | <a class="text-primary small loading-modal" href="?section=comissoes&diretorio=<?= $diretorioId ?>" role="button"> nova comissão</a></small></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira a comissão atualmente em vigor neste diretório.
                    </p>

                    <form method="GET" class="mb-2">
                        <div class="row g-2">
                            <input type="hidden" name="section" value="documentos" />
                            <input type="hidden" name="diretorio" value="<?= $diretorioId ?>" />

                            <div class="col-sm-2 col-6">
                                <input type="number" class="form-control form-control-sm" name="ano" value="<?= $anoGet ?>">
                            </div>

                            <div class="col-md-2 col-6">
                                <select class="form-select form-select-sm" name="tipo">
                                    <option value="">Tipo de documento</option>
                                    <?php
                                    $buscaTipos = DocumentoController::listarTodosOsTiposDocumentos($diretorioId);                                  

                                    if ($buscaTipos['status'] == 'success') {
                                        foreach ($buscaTipos['data'] as $tipo) {
                                            if ($tipo['id'] == $tipoGet) {
                                                echo '<option value="' . $tipo['id'] . '" selected>' . $tipo['descricao'] . '</option>';
                                            } else {
                                                echo '<option value="' . $tipo['id'] . '">' . $tipo['descricao'] . '</option>';
                                            }
                                        }
                                    } else if ($buscaTipos['status'] == 'empty' || $buscaTipos['status'] == 'server_error') {
                                        echo '<option value="">Tipo de documento</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="col-sm-2 col-6">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> buscar</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Criado em:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaDocumentos = DocumentoController::listarDocumentos($diretorioId, $anoGet, $tipoGet);
                                
                                if ($buscaDocumentos['status'] == 'success') {
                                    foreach ($buscaDocumentos['data'] as $documento) {
                                        echo '<tr>';
                                        echo '<td>' . $documento['titulo'] . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($documento['created_at'])) . ' | ' . $documento['usuario']['nome'] . '</td>';
                                        echo '<tr>';
                                    }
                                } else if ($buscaDocumentos['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaDocumentos['message'] . '</td></tr>';
                                } else if ($buscaDocumentos['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaDocumentos['message'] . ' | ' . $buscaDocumentos['error_id'] . '</td></tr>';
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>