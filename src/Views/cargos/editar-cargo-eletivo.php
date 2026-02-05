<?php

use JairoJeffersont\Controllers\CargoComissaoController;
use JairoJeffersont\Controllers\CargoEletivoController;
use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;

include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$cargoId = $_GET['cargo'] ?? '';

$buscaCargo = CargoEletivoController::buscarCargoEletivo($cargoId);

if ($buscaCargo['status'] != 'success') {
    header('Location: ?section=cargos-eletivos&diretorio=' . $diretorioId);
}

$ordemGet       = $_GET['ordem']        ?? 'ASC';
$ordernarPorGet = $_GET['ordernarPor']  ?? 'nome';
$itensGet       = (int) ($_GET['itens']  ?? 15);
$paginaGet      = (int) ($_GET['pagina'] ?? 1);
$sexoGet        = $_GET['sexo']         ?? '';
$ativoGet       = (int) ($_GET['ativo'] ?? 1);
$buscaGet       = $_GET['busca']        ?? '';

$buscaFiliados = FiliadoController::listarTodosOsFiliados($diretorioId, $sexoGet, $ativoGet, $itensGet, $paginaGet, $ordemGet, $ordernarPorGet, $buscaGet);


$buscarMembro = CargoEletivoController::listarMembrosCargoEletivo($cargoId);


?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm loading-modal" href="?section=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm loading-modal" href="?section=cargos-eletivos&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Editar cargo | <?= $buscaCargo['data']['descricao'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha os dados para atualizar esse cargo
                    </p>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'descricao'               => $_POST['descricao'] ?? null,
                            'multiplo'              => $_POST['multiplo'] ?? null,
                            'diretorio_id'          => $diretorioId
                        ];

                        $result = CargoEletivoController::atualizarCargoEletivo($cargoId, $dados);

                        if ($result['status'] == 'success') {
                            $buscaCargo = CargoEletivoController::buscarCargoEletivo($cargoId);
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_apagar'])) {


                        $result = CargoEletivoController::deletarCargoEletivo($cargoId);

                        if ($result['status'] == 'success') {
                            header('Location: ?section=cargos-eletivos&diretorio=' . $diretorioId);
                            echo '<div class="alert alert-success rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['btn_atribuir'])) {

                        $result = CargoEletivoController::atribuirCargoEletivo($cargoId, $_POST['filiado_id'], $_POST['inicio_mandato'], $_POST['fim_mandato']);

                        if ($result['status'] == 'success') {
                            $buscarMembro = CargoEletivoController::listarMembrosCargoEletivo($cargoId);
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
                            <input type="text" class="form-control form-control-sm" name="descricao" value="<?= $buscaCargo['data']['descricao'] ?>" placeholder="Nome do cargo">
                        </div>

                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="multiplo">
                                <option value="0" <?= $buscaCargo['data']['multiplo'] == '0' ? 'selected' : '' ?>>Único</option>
                                <option value="1" <?= $buscaCargo['data']['multiplo'] == '1' ? 'selected' : '' ?>>Multiplo</option>
                            </select>
                        </div>

                        <div class="col-sm-4 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success btn-sm px-4 confirm-action" name="btn_salvar" data-message="Deseja adicionar esse cargo eletivo?"><i class="bi bi-floppy"></i> Salvar </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 confirm-action" name="btn_apagar" data-message="Tem certeza que deseja apagar esse cargo?"><i class="bi bi-trash"></i> Apagar </button>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <p class="mb-0 text-muted small mb-2">
                        Membros desse cargo
                    </p>
                    <ul class="list-group small">
                        <?php
                        $buscarMembro = CargoEletivoController::listarMembrosCargoEletivo($cargoId);

                        if ($buscarMembro['status'] == 'success') {
                            foreach ($buscarMembro['data'] as $membro) {
                                echo '<li class="list-group-item">'
                                    . $membro['nome']
                                    . ' ('
                                    . date('d/m/Y', strtotime($membro['inicio_mandato'])) . ' - '
                                    . date('d/m/Y', strtotime($membro['fim_mandato']))
                                    . ')</li>';
                            }
                        } else if ($buscarMembro['status'] == 'empty') {
                            echo '<li class="list-group-item">' . $buscarMembro['message'] . '</li>';
                        }
                        ?>


                    </ul>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <form class="row g-2 align-items-end mb-0" method="post">
                        <input type="hidden" name="filiado_id" id="filiado_id" />
                        <div class="col-md-1 col-4">
                            <input type="date" class="form-control form-control-sm" name="inicio_mandato">
                        </div>
                        <div class="col-md-1 col-4">
                            <input type="date" class="form-control form-control-sm" name="fim_mandato">
                        </div>
                        <div class="col-sm-2 col-12 text-start ">
                            <button type="submit" class="btn btn-primary w-100 btn-sm px-4 confirm-action" name="btn_atribuir" id="btn_atribuir" data-message="Os dados estão corretos?"><i class="bi bi-arrow-down"></i> Selecione o membro </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function getId(id) {
                    $('#filiado_id').val(id);
                    $('#btn_atribuir')
                        .toggleClass('btn-primary btn-success')
                        .html('<i class="bi bi-floppy"></i> Atribuir membro');
                }
            </script>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <p class="mb-0 text-muted small mb-2">
                        Escolha o filiado que ocupará esse cargo
                    </p>
                    <form method="GET" class="mb-2">
                        <div class="row g-2">
                            <input type="hidden" name="section" value="editar-cargo-eletivo" />
                            <input type="hidden" name="diretorio" value="<?= $diretorioId ?>" />
                            <input type="hidden" name="cargo" value="<?= $cargoId ?>" />

                            <div class="col-sm-1 col-6">
                                <select class="form-select form-select-sm" name="sexo">
                                    <option value="" <?= $sexoGet == '' ? 'selected' : '' ?>>Todos os sexos</option>
                                    <option value="MASCULINO" <?= $sexoGet == 'MASCULINO' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="FEMININO" <?= $sexoGet == 'FEMININO' ? 'selected' : '' ?>>Feminino</option>
                                    <option value="OUTRO" <?= $sexoGet == 'OUTRO' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>

                            <div class="col-sm-2 col-6">
                                <input type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Buscar por nome"
                                    name="busca"
                                    value="<?= $buscaGet ?>">
                            </div>

                            <div class="col-sm-2 col-6">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> buscar</button>
                                <a href="?section=novo-filiado&diretorio=<?= $diretorioId ?>" type="button" class="btn btn-success btn-sm confirm-action loading-modal" data-message="Deseja inserir um novo filiado?"><i class="bi bi-plus"></i> novo filiado</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 ">
                            <thead>
                                <tr class="small">
                                    <th scope="col">Nome</th>
                                    <th scope="col">Municipio</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php
                                if ($buscaFiliados['status'] == 'success') {
                                    foreach ($buscaFiliados['data'] as $filiado) {
                                        echo '<tr>';
                                        echo '<td><a href="#" onclick="getId(\'' . $filiado['id'] . '\')">' . mb_convert_case($filiado['nome'], MB_CASE_TITLE, 'UTF-8') . '</a></td>';
                                        echo '<td>' . ($filiado['cidade'] ?: 'Não informado') . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaFiliados['status'] == 'empty') {
                                    echo '<tr>';
                                    echo '<td colspan="2">' . $buscaFiliados['message'] . '</td>';
                                    echo '</tr>';
                                } else if ($buscaFiliados['status'] == 'server_error') {
                                    echo '<tr>';
                                    echo '<td colspan="2">' . $buscaFiliados['message'] . ' | ' . $buscaFiliados['error_id'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php

                    //paginacao
                    $totalPaginas = (int) ($buscaFiliados['total_paginas'] ?? 1);
                    $paginaAtual  = max(1, min($paginaGet, $totalPaginas));
                    $maxLinks     = 10;

                    $inicio = max(1, $paginaAtual - floor($maxLinks / 2));
                    $fim    = $inicio + $maxLinks - 1;

                    if ($fim > $totalPaginas) {
                        $fim = $totalPaginas;
                        $inicio = max(1, $fim - $maxLinks + 1);
                    }

                    ?>

                    <?php if ($totalPaginas > 1): ?>
                        <nav aria-label="Paginação">
                            <ul class="pagination small mb-0">

                                <!-- Primeiro -->
                                <li class="page-item <?= $paginaAtual == 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?section=editar-cargo-eletivo&cargo=<?= $cargoId ?>&diretorio=<?= $diretorioId ?>&pagina=1&itens=<?= $itensGet ?>">
                                        Primeiro
                                    </a>
                                </li>

                                <!-- Páginas numéricas -->
                                <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                                    <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                                        <a class="page-link" href="?section=editar-cargo-eletivo&cargo=<?= $cargoId ?>&diretorio=<?= $diretorioId ?>&pagina=<?= $i ?>&itens=<?= $itensGet ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Último -->
                                <li class="page-item <?= $paginaAtual == $totalPaginas ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?section=editar-cargo-eletivo&cargo=<?= $cargoId ?>&diretorio=<?= $diretorioId ?>&pagina=<?= $totalPaginas ?>&itens=<?= $itensGet ?>">
                                        Último
                                    </a>
                                </li>

                            </ul>
                        </nav>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>