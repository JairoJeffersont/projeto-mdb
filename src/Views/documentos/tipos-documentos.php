<?php

use JairoJeffersont\Controllers\DocumentoController;
use JairoJeffersont\Controllers\DiretorioController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';

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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=documentos&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Tipos de documentos</h5>
                    <p class="mb-0 text-muted small mb-2">
                        Organize os tipos de documentos para esse diretório
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'descricao'               => $_POST['descricao'] ?? null,
                            'diretorio_id'       => $diretorioId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = DocumentoController::criarTipoDeDocumento($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_inserir_tipos'])) {

                        $tipos = [
                            ['descricao' => 'Ofício', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Memorando', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Ata de Reunião', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Ata de Convenção', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Lista de Presença', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Resolução', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Portaria', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Circular', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Edital de Convocação', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Comunicado Interno', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Requerimento', 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                        ];

                        foreach ($tipos as $dados) {
                            $result = DocumentoController::criarTipoDeDocumento($dados);
                        }

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    ?>



                    <form class="row g-2 align-items-end" method="post">
                        <div class="col-md-3 col-4">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Nome do tipo">
                        </div>
                        <div class="col-sm-4 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Deseja adicionar esse tipo de documento?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 confirm-action" name="btn_inserir_tipos" data-message="Deseja inserir os tipos padrões?"><i class="bi bi-floppy"></i> Padrões </button>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Adicionado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaTipos = DocumentoController::listarTodosOsTiposDocumentos($diretorioId);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['data'] as $tipo) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-tipo-documento&documento='.$tipo['id'].'&diretorio='.$diretorioId.'">' . $tipo['descricao'] . '</a></td>';
                                        echo '<td>' . date('d/m/Y', strtotime($tipo['created_at'])) . ' | ' . $tipo['usuario']['nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTipos['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaTipos['message'] . '</td>';
                                } else if ($buscaTipos['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaTipos['message'] . ' | ' . $buscaTipos['error_id'] . '</td>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>