<?php

use JairoJeffersont\Controllers\CargoEletivoController;
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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Adicionar novo cargo eletivo | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Adicione e gerencia os cargos eletivos para esse diretório.
                        Para atribuir um mandatário, clique no cargo desejado
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dadosCargo = [
                            'descricao'               => $_POST['descricao'] ?? null,
                            'multiplo'              => $_POST['multiplo'] ?? null,
                            'diretorio_id'       => $diretorioId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = CargoEletivoController::criarCargoEletivo($dadosCargo);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_inserir_cargos'])) {

                        $cargos = [
                            ['descricao' => 'Presidente', 'multiplo' => false, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Vice-Presidente', 'multiplo' => false, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Senador', 'multiplo' => true, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Deputado Federal', 'multiplo' => true, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Deputado Estadual', 'multiplo' => true, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Governador', 'multiplo' => false, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Prefeito', 'multiplo' => false, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Vereador', 'multiplo' => true, 'diretorio_id' => $diretorioId, 'usuario_id' => $_SESSION['user']['id']],
                        ];


                        foreach ($cargos as $dados) {
                            $result = CargoEletivoController::criarCargoEletivo($dados);
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
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Nome do cargo">
                        </div>

                        <div class="col-md-2 col-4">
                            <select class="form-select form-select-sm" name="multiplo">
                                <option value="0">Único</option>
                                <option value="1">Multiplo</option>
                            </select>
                        </div>

                        <div class="col-sm-4 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Deseja adicionar esse cargo eletivo?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 confirm-action" name="btn_inserir_cargos" data-message="Deseja inserir os cargos padrões?"><i class="bi bi-floppy"></i> Padrões </button>
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
                                $buscaCargosEletivos = CargoEletivoController::listarTodosOsCargosEletivos($diretorioId);
                                if ($buscaCargosEletivos['status'] == 'success') {
                                    foreach ($buscaCargosEletivos['data'] as $cargoEletivo) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-cargo-eletivo&cargo=' . $cargoEletivo['id'] . '&diretorio=' . $diretorioId . '">' . $cargoEletivo['descricao'] . '</a></td>';
                                        echo '<td>' . date('d/m/Y', strtotime($cargoEletivo['created_at'])) . ' | ' . $cargoEletivo['usuario']['nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaCargosEletivos['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaCargosEletivos['message'] . '</td>';
                                } else if ($buscaCargosEletivos['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaCargosEletivos['message'] . ' | ' . $buscaCargosEletivos['error_id'] . '</td>';
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