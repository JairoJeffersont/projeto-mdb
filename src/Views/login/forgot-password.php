<div class="d-flex justify-content-center align-items-center vh-100" id="wrapper" style="margin-top: -60px;">
    <div id="page-content-wrapper" class="w-100">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="card bg-transparent border-0" style="min-width: 300px; max-width: 400px; width: 100%;">
                    <div class="card-body">
                        <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top d-block mx-auto mb-2" alt="..." style="width: 200px; height: auto;">
                        <h4 class="card-title text-center text-white"><?= $_ENV['APP_NAME'] ?></h4>
                        <p class="card-text text-center text-white">Digite seu email para receber a senha</p>
                        <?php

                        use JairoJeffersont\Controllers\LoginController;

                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recover_button'])) {

                            $result = LoginController::recuperarSenha($_POST['email']);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert custom-alert alert-success rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                            } else if ($result['status'] == 'server_error') {
                                echo '<div class="alert custom-alert alert-danger" role="alert text-center" data-timeout="3"><b>' . $result['message'] . ' | ' . $result['error_id'] . '</b></div>';
                            } else {
                                echo '<div class="alert custom-alert alert-info rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                            }
                        }

                        ?>
                        <form method="post" enctype="application/x-www-form-urlencoded" class="mb-3">
                            <div class="mb-2">
                                <input type="email" class="form-control rounded-pill fs-6" id="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="d-flex">
                                <a href="?section=login" type="submit" class="btn btn-success rounded-pill fs-6 w-50 me-2 loading-modal">Voltar</a>
                                <button type="submit" class="btn btn-primary rounded-pill fs-6 w-50 me-0 loading-modal" data-modalMessage="Aguarde, enviando..." name="recover_button">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>