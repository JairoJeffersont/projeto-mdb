<?php

use JairoJeffersont\Controllers\CargoComissaoController;
use JairoJeffersont\Controllers\ComissaoController;
use JairoJeffersont\Controllers\DiretorioController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$comissaoId = $_GET['comissao'] ?? '';

$buscaComissao = ComissaoController::buscarComissao($comissaoId);

if ($buscaComissao['status'] != 'success') {
    header('Location: ?section=comissoes&diretorio=' . $diretorioId . '');
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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=comissoes&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Editar <?= $buscaComissao['data']['tipo']['descricao'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha os dados para atualizar essa comissão
                    </p>
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'data_inicio'               => $_POST['data_inicio'] ?? null,
                            'data_fim'                  => $_POST['data_fim'] ?? null,
                            'tipo_id'                   => $_POST['tipo_id'] ?? null
                        ];

                        $result = ComissaoController::atualizarComissao($comissaoId, $dados);

                        if ($result['status'] == 'success') {
                            $buscaComissao = ComissaoController::buscarComissao($comissaoId);
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_apagar'])) {

                        $result = ComissaoController::deletarComissao($comissaoId);

                        if ($result['status'] == 'success') {
                            header('Location: ?section=comissoes&diretorio=' . $diretorioId);
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    ?>
                    <form class="row g-2 align-items-end" method="post">
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="tipo_id" required>
                                <option value="">Selecione o tipo de comissão</option>
                                <?php
                                $buscaTipos = ComissaoController::listarTodosOsTiposComissoes();
                                foreach ($buscaTipos['data'] as $tipo) {
                                    if ($buscaComissao['data']['tipo_id'] == $tipo['id']) {
                                        echo '<option value="' . $tipo['id'] . '" selected>' . $tipo['descricao'] . '</option>';
                                    } else {
                                        echo '<option value="' . $tipo['id'] . '">' . $tipo['descricao'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <input type="date" class="form-control form-control-sm" name="data_inicio" value="<?= $buscaComissao['data']['data_inicio'] ?>" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <input type="date" class="form-control form-control-sm" name="data_fim" value="<?= $buscaComissao['data']['data_fim'] ?>" required>
                        </div>
                        <div class="col-sm-5 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 confirm-action" name="btn_apagar" data-message="Tem certeza que você deseja apagar essa comissão?"><i class="bi bi-trash"></i> Apagar </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body p-3">
                    <p class="mb-0 text-muted small mb-2">
                        Lista com os cargos para essa comissão.
                    </p>
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar_cargo'])) {

                        $dados = [
                            'descricao'               => $_POST['descricao'] ?? null,
                            'multiplo'                  => $_POST['multiplo'] ?? null,
                            'comissao_id'                   => $comissaoId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = CargoComissaoController::criarCargoComissao($dados);

                        if ($result['status'] == 'success') {
                            $buscaComissao = ComissaoController::buscarComissao($comissaoId);
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_inserir_cargos'])) {

                        $cargos = [
                            ['descricao' => 'Presidente', 'multiplo' => false, 'comissao_id' => $comissaoId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Vice-Presidente', 'multiplo' => false, 'comissao_id' => $comissaoId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Secretário Geral', 'multiplo' => false, 'comissao_id' => $comissaoId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Tesoureiro', 'multiplo' => false, 'comissao_id' => $comissaoId, 'usuario_id' => $_SESSION['user']['id']],
                            ['descricao' => 'Vogal', 'multiplo' => true, 'comissao_id' => $comissaoId, 'usuario_id' => $_SESSION['user']['id']],
                        ];

                        foreach ($cargos as $dados) {
                            $result = CargoComissaoController::criarCargoComissao($dados);
                        }

                        if ($result['status'] == 'success') {
                            $buscaComissao = ComissaoController::buscarComissao($comissaoId);
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }


                    ?>
                    <form class="row g-2 align-items-end mb-2" method="post">
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Nome do cargo">
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="multiplo">
                                <option value="0">Único</option>
                                <option value="1">Multiplo</option>
                            </select>
                        </div>
                        <div class="col-sm-5 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar_cargo" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 confirm-action" name="btn_inserir_cargos" data-message="Deseja inserir os cargos padrões?"><i class="bi bi-floppy"></i> Padrões </button>

                        </div>
                    </form>
                    <div class="table-responsive ">
                        <table class="table small table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Multiplo</th>
                                    <th scope="col">Criado em:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaCargos = CargoComissaoController::listarTodosOsCargosComissao($comissaoId);
                                if ($buscaCargos['status'] == 'success') {
                                    foreach ($buscaCargos['data'] as $cargo) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-cargo-comissao&cargo=' . $cargo['id'] . '&comissao=' . $comissaoId . '&diretorio=' . $diretorioId . '">' . $cargo['descricao'] . '</a></td>';
                                        echo '<td>' . ($cargo['multiplo'] ? 'Sim' : 'Não') . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($cargo['created_at'])) . ' | ' . $cargo['usuario']['nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaCargos['status'] == 'empty') {
                                    echo '<tr><td colspan="3">' . $buscaCargos['message'] . '</td></tr>';
                                } else if ($buscaCargos['status'] == 'server_error') {
                                    echo '<tr><td colspan="3">' . $buscaCargos['message'] . ' | ' . $buscaCargos['error_id'] . '</td></tr>';
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