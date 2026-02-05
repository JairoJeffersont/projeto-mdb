<?php

use JairoJeffersont\Controllers\ComissaoController;
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
                    <h5 class="card-title mb-1 text-primary">Comissões | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Adicione uma comissão executiva para esse diretório
                    </p>
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'data_inicio'               => $_POST['data_inicio'] ?? null,
                            'data_fim'              => $_POST['data_fim'] ?? null,
                            'tipo_id'       => $_POST['tipo_id'] ?? null,
                            'diretorio_id' => $diretorioId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = ComissaoController::criarComissao($dados);

                        if ($result['status'] == 'success') {
                            $buscaComissoes = ComissaoController::listarTodosAsComissoes($diretorioId);

                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
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
                                    echo '<option value="' . $tipo['id'] . '">' . $tipo['descricao'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <input type="date" class="form-control form-control-sm" name="data_inicio" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <input type="date" class="form-control form-control-sm" name="data_fim" required>
                        </div>
                        <div class="col-sm-1 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success w-100 btn-sm px-4 confirm-action" name="btn_salvar" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-3">
                    <p class="mb-0 text-muted small mb-2">
                        Arquivo de todas as comissões desse diretório
                    </p>
                    <div class="table-responsive mb-0">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Comissão</th>
                                    <th scope="col">Vigência</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaComissoes = ComissaoController::listarTodosAsComissoes($diretorioId, null, true);
                                if ($buscaComissoes['status'] == 'success') {
                                    foreach ($buscaComissoes['data'] as $comissao) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-comissao&comissao=' . $comissao['id'] . '&diretorio=' . $diretorioId . '">' . $comissao['tipo']['descricao'] . '</a></td>';
                                        echo '<td>'
                                            . date('d/m/Y', strtotime($comissao['data_inicio'])) . ' - '
                                            . date('d/m/Y', strtotime($comissao['data_fim']))
                                            . ' ('
                                            . (new DateTime($comissao['data_inicio']))
                                            ->diff(new DateTime($comissao['data_fim']))
                                            ->days
                                            . ' dias)'
                                            . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaComissoes['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaComissoes['message'] . '</td></tr>';
                                } else if ($buscaComissoes['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaComissoes['message'] . ' | ' . $buscaComissoes['error_id'] . '</td></tr>';
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