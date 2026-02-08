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
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1 text-primary">Adicionar novo filiado | <?= $buscaDiretorio['data']['municipio'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha os dados abaixo para inserir um novo filiado nesse diretório
                    </p>

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        $dadosFiliado = [
                            'nome'               => $_POST['nome'] ?? null,
                            'email'              => $_POST['email'] ?? null,
                            'telefone'           => $_POST['telefone'] ?? null,
                            'sexo'               => $_POST['sexo'] ?? null,
                            'data_nascimento'    => $_POST['data_nascimento'] ?? null,
                            'data_filiacao'      => $_POST['data_filiacao'] ?? null,
                            'data_desfiliacao'   => $_POST['data_desfiliacao'] ?? null,
                            'cpf'                => $_POST['cpf'] ?? null,
                            'rg'                 => $_POST['rg'] ?? null,
                            'titulo_eleitoral'   => $_POST['titulo_eleitoral'] ?? null,
                            'zona_eleitoral'     => $_POST['zona_eleitoral'] ?? null,
                            'secao_eleitoral'    => $_POST['secao_eleitoral'] ?? null,
                            'endereco'           => $_POST['endereco'] ?? null,
                            'bairro'             => $_POST['bairro'] ?? null,
                            'cidade'             => $_POST['cidade'] ?? null,
                            'estado'             => $_POST['estado'] ?? null,
                            'cep'                => $_POST['cep'] ?? null,
                            'informacoes_adicionais' => $_POST['informacoes_adicionais'] ?? null,
                            'diretorio_id'       => $diretorioId,
                            'usuario_id'         => $_SESSION['user']['id'],
                        ];

                        $result = FiliadoController::criarFiliado($dadosFiliado, false);

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
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome completo" required>
                        </div>

                        <div class="col-md-3 col-8">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="E-mail">
                        </div>

                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="telefone" data-mask="(00) 0000-0000" placeholder="Telefone">
                        </div>

                        <div class="col-md-3 col-6">
                            <select class="form-select form-select-sm" name="sexo" required>
                                <option value="">Sexo</option>
                                <option value="MASCULINO">Masculino</option>
                                <option value="FEMININO">Feminino</option>
                                <option value="OUTRO">Outro</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="data_nascimento" data-mask="00/00" placeholder="Nascimento (DD/MM)" required>
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="data_filiacao" data-mask="00/00/0000" placeholder="Filiação (DD/MM/AAAA)" required>
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="data_desfiliacao" data-mask="00/00/0000" placeholder="Desfiliação (DD/MM/AAAA)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="cpf" data-mask="000.000.000-00" placeholder="CPF (000.000.000-00)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="rg" data-mask=0000000 placeholder="RG (0000000)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="titulo_eleitoral" data-mask="00000000000" placeholder="Título eleitoral" required>
                        </div>

                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="zona_eleitoral" data-mask="0000" placeholder="Zona">
                        </div>

                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="secao_eleitoral" data-mask="0000" placeholder="Seção">
                        </div>

                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço">
                        </div>

                        <div class="col-md-3 col-8">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro">
                        </div>

                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="cep" data-mask="00000-000" placeholder="CEP">
                        </div>

                        <div class="col-md-3 col-5">
                            <select class="form-select form-select-sm estado" name="estado" required>
                            </select>
                        </div>

                        <div class="col-md-4 col-7">
                            <select class="form-select form-select-sm municipio" name="cidade" required>
                                <option>Selecione o município</option>
                            </select>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" rows="3" name="informacoes_adicionais" placeholder="Informações adicionais"></textarea>
                        </div>

                        <div class="col-sm-1 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success w-100 btn-sm px-4 confirm-action" data-message="Os dados desse filiado estão corretos?"><i class="bi bi-floppy"></i> Salvar </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>