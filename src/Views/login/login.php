<div class="d-flex justify-content-center align-items-center vh-100" id="wrapper" style="margin-top: -60px;">
    <div id="page-content-wrapper" class="w-100">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="card bg-transparent border-0" style="min-width: 300px; max-width: 400px; width: 100%;">
                    <div class="card-body">
                        <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top d-block mx-auto mb-1" alt="..." style="width: 200px; height: auto;">
                        <h4 class="card-title text-center text-white"><?= $_ENV['APP_NAME'] ?></h4>
                        <p class="card-text text-center text-white"><?= $_ENV['APP_DESCRIPTION'] ?></p>
                        <?php

                        use JairoJeffersont\Controllers\LoginController;

                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_button'])) {

                            $data = [
                                'email' => $_POST['email'],
                                'password' => $_POST['password'],
                            ];

                            $result = LoginController::entrar($data);

                            if ($result['status'] == 'success') {
                                header('Location: ?section=home');
                            } else if ($result['status'] == 'server_error') {
                                echo '<div class="alert alert-danger" role="alert text-center" data-timeout="3"><b>' . $result['message'] . ' | ' . $result['error_id'] . '</b></div>';
                            } else {
                                echo '<div class="alert alert-info rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                            }
                        }

                        ?>

                        <form method="post" enctype="application/x-www-form-urlencoded" class="mb-3">
                            <div class="mb-2">
                                <input type="email" class="form-control rounded-pill fs-6" id="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control rounded-pill fs-6" id="password" name="password" placeholder="Senha" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary rounded-pill fs-6 loading-modal" name="login_button">Entrar</button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-center mt-3 mb-3">
                            <p class="card-text text-white mb-0 me-3" style="cursor: pointer;">
                                <a href="?section=forgot-password" class="loading-modal" style="color: white;">Esqueci minha senha</a>
                            </p>
                            <p class="card-text text-white mb-0 me-3" style="cursor: pointer;">
                                <a href="?section=cadastro" class="loading-modal" style="color: white;">Faça seu cadastro</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>