<?php

use JairoJeffersont\Controllers\EleicoesController;

include('../src/Views/includes/verifyLogged.php');

$anoGet = $_GET['ano'] ?? '2024';

$tipoGet = $_GET['tipo'] ?? 'Vereador';

if (!isset($_GET['partido'])) {
    // primeiro acesso da página
    $partidoGet = '15';
} elseif ($_GET['partido'] === '') {
    // usuário escolheu "Todos"
    $partidoGet = null;
} else {
    // usuário escolheu um partido específico
    $partidoGet = $_GET['partido'];
}

if (!isset($_GET['municipio'])) {
    // primeiro acesso da página
    $municipioGet = '6050';
} elseif ($_GET['municipio'] === '') {
    // usuário escolheu "Todos"
    $municipioGet = null;
} else {
    // usuário escolheu um partido específico
    $municipioGet = $_GET['municipio'];
}

if (!isset($_GET['situacao'])) {
    // primeiro acesso da página
    $situacaoGet = null;
} elseif ($_GET['situacao'] === '') {
    // usuário escolheu "Todos"
    $situacaoGet = null;
} else {
    // usuário escolheu um partido específico
    $situacaoGet = $_GET['situacao'];
}

if ($tipoGet == 'Vereador') {
    $BuscaResultadosSelect = EleicoesController::pegarDadosEleicaoVereador($anoGet);
    $BuscaResultados = EleicoesController::pegarDadosEleicaoVereador($anoGet, $partidoGet, $situacaoGet, $municipioGet);
} else if ($tipoGet == 'Prefeito') {
    $BuscaResultadosSelect = EleicoesController::pegarDadosEleicaoPrefeito($anoGet);
    $BuscaResultados = EleicoesController::pegarDadosEleicaoPrefeito($anoGet, $partidoGet, $situacaoGet, $municipioGet);
}




//print_r($BuscaResultados);

?>
<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm loading-modal" href="?section=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Resultado das eleicoes municipais | <b><?= $tipoGet ?></b></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Consulte os resultados das eleições municipais utilizando os filtros abaixo. <b>Os dados apresentados são oficiais do TSE.</b>
                    </p>

                    <form method="GET" class="mb-2">
                        <div class="row g-2">
                            <input type="hidden" name="section" value="eleicoes-municipais" />
                            <input type="hidden" name="tipo" value="<?= $tipoGet ?>" />


                            <div class="col-sm-1 col-6">
                                <select class="form-select form-select-sm" name="ano">
                                    <option value="" <?= $anoGet == '' ? 'selected' : '' ?>>Selecione a eleição</option>
                                    <option value="2024" <?= $anoGet == '2024' ? 'selected' : '' ?>>2024</option>
                                    <option value="2020" <?= $anoGet == '2020' ? 'selected' : '' ?>>2020</option>
                                </select>
                            </div>

                            <div class="col-sm-2 col-6">
                                <select class="form-select form-select-sm" name="municipio">
                                    <option value="" selected>Todos os municípios</option>
                                    <?php
                                    $municipios = [];

                                    foreach ($BuscaResultadosSelect['data'] as $item) {
                                        $codigo = $item['CD_MUNICIPIO'];
                                        $nome   = $item['NM_MUNICIPIO'];

                                        $municipios[$codigo] = $nome; // evita duplicados automaticamente
                                    }

                                    asort($municipios);

                                    foreach ($municipios as $codigo => $nome) {
                                        if ($codigo == $municipioGet) {
                                            echo '<option value="' . $codigo . '" selected>' . $nome . '</option>';
                                        } else {
                                            echo '<option value="' . $codigo . '">' . $nome . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-sm-1 col-6">
                                <select class="form-select form-select-sm" name="partido">
                                    <option value="" selected>Todos os partidos</option>

                                    <?php
                                    $partidos = [];

                                    foreach ($BuscaResultadosSelect['data'] as $item) {
                                        $numero = $item['NR_PARTIDO'];
                                        $sigla  = $item['SG_PARTIDO'];

                                        $partidos[$numero] = $sigla; // evita duplicados
                                    }

                                    asort($partidos); // ordena alfabeticamente pela sigla

                                    foreach ($partidos as $numero => $sigla) {
                                        if ($numero == $partidoGet) {
                                            echo '<option value="' . htmlspecialchars($numero) . '" selected>' . htmlspecialchars($sigla) . '</option>';
                                        } else {
                                            echo '<option value="' . htmlspecialchars($numero) . '">' . htmlspecialchars($sigla) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-sm-2 col-6">
                                <select class="form-select form-select-sm" name="situacao">
                                    <option value="" selected>Todas as situações</option>
                                    <?php
                                    $situacoes = [];

                                    foreach ($BuscaResultadosSelect['data'] as $item) {
                                        $codigo = $item['CD_SIT_TOT_TURNO'];
                                        $texto  = $item['DS_SIT_TOT_TURNO'];

                                        $situacoes[$codigo] = $texto; // evita duplicados
                                    }

                                    asort($situacoes); // ordena alfabeticamente pelo texto

                                    foreach ($situacoes as $codigo => $texto) {
                                        if ($codigo == $situacaoGet) {
                                            echo '<option value="' . htmlspecialchars($codigo) . '" selected>' . htmlspecialchars($texto) . '</option>';
                                        } else {
                                            echo '<option value="' . htmlspecialchars($codigo) . '">' . htmlspecialchars($texto) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="col-sm-2 col-12">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> buscar</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Municipio</th>
                                    <th scope="col">Votos</th>
                                    <th scope="col">Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($BuscaResultados['status'] == 'success') {

                                    usort($BuscaResultados['data'], function ($a, $b) {
                                        return $b['QT_VOTOS_NOMINAIS_VALIDOS'] <=> $a['QT_VOTOS_NOMINAIS_VALIDOS'];
                                    });

                                    foreach ($BuscaResultados['data'] as $cargoEletivo) {
                                        echo '<tr>';
                                        echo '<td>' . $cargoEletivo['NM_URNA_CANDIDATO'] . ' (' . $cargoEletivo['DS_COMPOSICAO_COLIGACAO'] . ')</td>';
                                        echo '<td>' . $cargoEletivo['NM_MUNICIPIO'] . '</td>';
                                        echo '<td>' . $cargoEletivo['QT_VOTOS_NOMINAIS_VALIDOS'] . '</td>';
                                        echo '<td>' . $cargoEletivo['DS_SIT_TOT_TURNO'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($BuscaResultados['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $BuscaResultados['message'] . '</td>';
                                } else if ($BuscaResultados['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $BuscaResultados['message'] . ' | ' . $BuscaResultados['error_id'] . '</td>';
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