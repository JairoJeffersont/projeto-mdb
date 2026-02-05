<?php

use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;

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
                    <h5 class="card-title mb-1 text-primary">Editar informações do diretório | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha as informações desse diretorio
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'email'               => $_POST['email'] ?? null,
                            'telefone'              => $_POST['telefone'] ?? null,
                            'endereco'       => $_POST['endereco'] ?? null,
                        ];

                        $result = DiretorioController::atualizarDiretorio($diretorioId, $dados);

                        if ($result['status'] == 'success') {
                            $buscaDiretorio = DiretorioController::buscarDiretorio($diretorioId);

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
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email" value="<?= $buscaDiretorio['data']['email'] ?>" required>
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="telefone" data-mask="(00) 0000-0000" value=" <?= $buscaDiretorio['data']['telefone'] ?>" placeholder=" Telefone" required>
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço" value="<?= $buscaDiretorio['data']['endereco'] ?>" required>
                        </div>

                        <div class="col-sm-1 col-4 text-start mt-2">
                            <button type="submit" class="btn btn-success w-100 btn-sm px-4 confirm-action" name="btn_salvar" data-message="Os dados estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>