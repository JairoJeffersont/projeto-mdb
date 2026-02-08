<?php

use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\DocumentoController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$documentoId = $_GET['documento'] ?? '';

$buscaDocumento = DocumentoController::buscarDocumento($documentoId);

if ($buscaDocumento['status'] != 'success') {
    header('Location: ?section=documentos&diretorio=' . $diretorioId);
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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=documentos&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-1 text-primary">Ficha do documento | <?= $buscaDocumento['data']['titulo'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Edite as informações desse documento
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'ano'               => $_POST['ano'] ?? null,
                            'tipo_id'               => $_POST['tipo_id'] ?? null,
                            'titulo'               => $_POST['titulo'] ?? null,
                            'diretorio_id'               => $diretorioId,
                        ];

                        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                            $dados['arquivo'] = $_FILES['arquivo'];
                        }

                        $result = DocumentoController::atualizarDocumento($documentoId, $dados);

                        if ($result['status'] == 'success') {
                            $buscaDocumento = DocumentoController::buscarDocumento($documentoId);

                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'conflict') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'formato_nao_permitido'  || $result['status'] == 'tamanho_maximo_excedido') {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>Arquivo não permitido ou muito grande</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_apagar'])) {

                       
                        $result = DocumentoController::apagarDocumento($documentoId);                      
                        

                        if ($result['status'] == 'success') {
                            header('Location: ?section=documentos&diretorio='.$diretorioId);
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'conflict') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'formato_nao_permitido'  || $result['status'] == 'tamanho_maximo_excedido') {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>Arquivo não permitido ou muito grande</b></div>';
                        }
                    }

                    ?>


                    <form class="row g-2 align-items-end" method="post" enctype="multipart/form-data">

                        <div class="col-md-1 col-4">
                            <input type="number" class="form-control form-control-sm" name="ano" value="<?= $buscaDocumento['data']['ano'] ?>" required>
                        </div>

                        <div class="col-md-2 col-6">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="tipo_id" required>
                                    <option value="">Tipo de documento</option>
                                    <?php
                                    $buscaTipos = DocumentoController::listarTodosOsTiposDocumentos($diretorioId);
                                    if ($buscaTipos['status'] == 'success') {
                                        foreach ($buscaTipos['data'] as $tipo) {
                                            if ($tipo['id'] == $buscaDocumento['data']['tipo_id']) {
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
                                <a href="?section=tipos-documentos&diretorio=<?= $diretorioId ?>" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de órgão?">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-4">
                            <input type="text" class="form-control form-control-sm" name="titulo" placeholder="Titulo do documento" value="<?= $buscaDocumento['data']['titulo'] ?>" required>
                        </div>
                        <div class="col-md-3 col-4">
                            <input type="file" class="form-control form-control-sm" name="arquivo">
                        </div>

                        <div class="col-sm-2 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_atualizar" data-message="Deseja atualizar documento?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 confirm-action" name="btn_apagar" data-message="Deseja apagar esse  documento?"><i class="bi bi-trash"></i> Apagar </button>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <p class="mb-0 text-muted small mb-2">
                        Clique no botão abaixo para ver o arquivo
                    </p>

                    <?php

                    if (isset($_GET['download'])) {
                        \JairoJeffersont\Helpers\ForceDownload::forcarDownload($buscaDocumento['data']['arquivo']);
                    }

                    ?>
                    <a href="?section=ficha-documento&documento=<?= $documentoId ?>&diretorio=<?= $diretorioId ?>&download=1" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-file-earmark"></i> Ver documento
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>