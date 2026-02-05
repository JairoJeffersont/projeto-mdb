<?php

use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;
use JairoJeffersont\Controllers\NucleoController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$nucleoId = $_GET['nucleo'] ?? '';

$buscaNucleo = NucleoController::buscarNucleo($nucleoId);

if ($buscaNucleo['status'] != 'success') {
    header('Location: ?section=nucleos&diretorio=' . $diretorioId);
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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=nucleos&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Editar núcleo </h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha os dados do núcleo
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome'               => $_POST['nome'] ?? null,
                            'descricao'              => $_POST['descricao'] ?? null,
                            'diretorio_id'       => $diretorioId,
                            'usuario_id' => $_SESSION['user']['id']
                        ];

                        $result = NucleoController::atualizarNucleo($nucleoId, $dados);

                        if ($result['status'] == 'success') {
                            $buscaNucleo = NucleoController::buscarNucleo($nucleoId);

                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_apagar'])) {



                        $result = NucleoController::deletarNucleo($nucleoId);

                        if ($result['status'] == 'success') {
                            header('Location: ?section=nucleos&diretorio=' . $diretorioId);
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    ?>

                    <form class="row g-2 align-items-end" method="post">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do núcleo" value="<?= $buscaNucleo['data']['nome'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder=" Descrição do núcleo" value="<?= $buscaNucleo['data']['descricao'] ?>" required>
                        </div>
                        <div class="col-sm-5 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 confirm-action" name="btn_apagar" data-message="Tem certeza que você deseja apagar esse núcleo?"><i class="bi bi-trash"></i> Apagar </button>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>