<?php

use JairoJeffersont\Controllers\DiretorioController;

include('../src/Views/includes/verifyLogged.php');

(int) $tipoGet = $_GET['tipo'] ?? 2;

$buscaDiretorios = DiretorioController::listarDiretorios($tipoGet);

?>
<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm" href="?section=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Diretórios do MDB Amapá</h5>
                    <p class="mb-0 text-muted  mb-2">
                        Lista com os diretórios estadual e municipais do partido.
                    </p>
                    <div class="table-responsive ">
                        <table class="table small table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Municipio</th>
                                    <th scope="col">Filiados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($buscaDiretorios['status'] == 'success') {

                                    $totalFiliados = 0;
                                    foreach ($buscaDiretorios['data'] as $diretorio) {
                                        $totalFiliados += ($diretorio['filiados_count'] ?? 0);
                                    }

                                    foreach ($buscaDiretorios['data'] as $diretorio) {
                                        $qtdFiliados = ($diretorio['filiados_count'] ?? 0);
                                        $percentual = $totalFiliados > 0 ? round(($qtdFiliados / $totalFiliados) * 100, 2) : 0;
                                        echo '<tr>';
                                        echo '<td><a href="?section=ficha-diretorio&diretorio=' . $diretorio['id'] . '" class="loading-modal" data-modalMessage="Aguarde... Procurando diretório..." >' . mb_convert_case($diretorio['municipio'], MB_CASE_TITLE, 'UTF-8') . ' </a>
                                              </td>';
                                        echo '<td>' . $qtdFiliados . ' (' . $percentual . '%)</td>';
                                        echo '</tr>';
                                    }
                                    echo '<tr><td><b>Total</b></td><td><b>' . $totalFiliados . ' (100%)</b></td></tr>';
                                } else if ($buscaDiretorios['status'] == 'empty') {
                                    echo '<tr><td>' . $buscaDiretorios['message'] . '</td></tr>';
                                } else if ($buscaDiretorios['status'] == 'server_error') {
                                    echo '<tr><td>' . $buscaDiretorios['message'] . ' | ' . $buscaDiretorios['error_id'] . '</td></tr>';
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

?>