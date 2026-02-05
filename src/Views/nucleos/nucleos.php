<?php

use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;
use JairoJeffersont\Controllers\NucleoController;

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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Núcleos temáticos | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Crie edite os núcleos temáticos desse diretório
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome'               => $_POST['nome'] ?? null,
                            'descricao'              => $_POST['descricao'] ?? null,
                            'diretorio_id'       => $diretorioId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = NucleoController::criarNucleo($dados);

                        if ($result['status'] == 'success') {
                            $buscaNucleos = NucleoController::listarTodosOsNucleos($diretorioId);

                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    ?>

                    <form class="row g-2 align-items-end" method="post">
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do núcleo" required>
                        </div>
                        <div class="col-md-3 col-4">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder=" Descrição do núcleo" required>
                        </div>
                        <div class="col-sm-1 col-4 text-start mt-2">
                            <button type="submit" class="btn btn-success w-100 btn-sm px-4 confirm-action" name="btn_salvar" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-0 ">
                <div class="card-body p-3">
                    <div class="table-responsive mb-0">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Núcleo</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaNucleos = NucleoController::listarTodosOsNucleos($diretorioId);
                                if ($buscaNucleos['status'] == 'success') {
                                    foreach ($buscaNucleos['data'] as $cargoEletivo) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-nucleo&nucleo=' . $cargoEletivo['id'] . '&diretorio=' . $diretorioId . '">' . $cargoEletivo['descricao'] . '</a></td>';
                                        echo '<td>' . $cargoEletivo['descricao'] . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($cargoEletivo['created_at'])) . ' | '.$cargoEletivo['usuario']['nome'].'</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaNucleos['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaNucleos['message'] . '</td>';
                                } else if ($buscaNucleos['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaNucleos['message'] . ' | ' . $buscaNucleos['error_id'] . '</td>';
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