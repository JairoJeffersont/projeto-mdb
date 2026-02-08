<div class="d-flex justify-content-center align-items-center vh-100" id="wrapper" style="margin-top: -60px;">
    <div id="page-content-wrapper" class="w-100">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="card bg-transparent border-0" style="min-width: 300px; max-width: 400px; width: 100%;">
                    <div class="card-body">
                        <img src="<?php

                                    use JairoJeffersont\Controllers\DiretorioController;
                                    use JairoJeffersont\Controllers\UsuarioController;

                                    BASE_URL ?>/img/logo_white.png" class="card-img-top d-block mx-auto mb-2" alt="..." style="width: 200px; height: auto;">
                        <h4 class="card-title text-center text-white"><?= $_ENV['APP_NAME'] ?></h4>
                        <p class="card-text text-center text-white">Novo usuário</p>
                        <?php


                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_button'])) {

                            if ($_POST['senha'] == $_POST['senha_confirmacao']) {
                                $data = [
                                    'nome'              => $_POST['nome'],
                                    'email'             => $_POST['email'],
                                    'senha'          => $_POST['senha'],
                                    'diretorio_id'      => $_POST['diretorio_id'],
                                ];


                                $result = UsuarioController::criarUsuario($data);

                                if ($result['status'] == 'success') {
                                    echo '<div class="alert alert-success rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>' . $result['message'] . ' | Aguarde a ativação da sua conta</b></div>';
                                } else if ($result['status'] == 'server_error') {
                                    echo '<div class="alert alert-danger" role="alert text-center" data-timeout="3"><b>' . $result['message'] . ' | ' . $result['error_id'] . '</b></div>';
                                } else {
                                    echo '<div class="alert alert-info rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                                }
                            } else {
                                echo '<div class="alert alert-info rounded-pill px-2 py-1 text-center" role="alert" data-timeout="3"><b>Senhas não conferem</b></div>';
                            }
                        }

                        ?>

                        <form method="post" enctype="application/x-www-form-urlencoded" class="mb-3">

                            <div class="mb-2">
                                <input type="text" class="form-control rounded-pill fs-6" name="nome" placeholder="Nome" required>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control rounded-pill fs-6" name="email" placeholder="Email" required>
                            </div>

                            <div class="mb-2">
                                <select class="form-control rounded-pill fs-6" name="diretorio_id" required>
                                    <option value="" selected disabled>Selecione o diretório</option>
                                    <?php
                                    $buscaDiretorios = DiretorioController::listarDiretorios();
                                    if ($buscaDiretorios['status'] === 'success') {
                                        foreach ($buscaDiretorios['data'] as $diretorio) {
                                            if ($diretorio['tipo_id'] != 1) {
                                                echo '<option value="' . $diretorio['id'] . '">' . $diretorio['municipio'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row mb-3 g-2">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <input type="password" class="form-control rounded-pill fs-6" name="senha" placeholder="Senha" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="password" class="form-control rounded-pill fs-6" name="senha_confirmacao" placeholder="Confirmar senha" required>
                                </div>
                            </div>

                            <div class="d-flex">
                                <a href="?section=login" type="submit" class="btn btn-success rounded-pill fs-6 w-50 me-2 loading-modal">Voltar</a>
                                <button type="submit" class="btn btn-primary rounded-pill fs-6 w-50 me-0 loading-modal" data-modalMessage="Aguarde, sua conta está sendo criada..." name="login_button">Salvar</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>