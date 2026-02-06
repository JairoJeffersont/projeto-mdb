<?php

use JairoJeffersont\Controllers\EleicoesController;
use JairoJeffersont\Controllers\FiliadoController;

include('../src/Views/includes/verifyLogged.php'); 

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card">
                <div class="card-body">
                    Home view example
                     <?php

                    $a = EleicoesController::pegarDadosEleicao('2024');
                    print_r($a);

                //$a = FiliadoController::importarEInserir(__DIR__ . '/../../../public/xlsx/arquivo.xlsx');
                //print_r($a);

            
            ?>
                </div>
            </div>
        </div>
    </div>
</div>