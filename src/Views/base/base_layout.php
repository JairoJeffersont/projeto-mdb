<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo $_ENV['APP_NAME']; ?></title>
    <link rel="icon" type="image/x-icon" href="<?php BASE_URL ?>/assets/favicon.ico" />
    <link href="<?php BASE_URL ?>/css/styles.css" rel="stylesheet" />
    <link href="<?php BASE_URL ?>/css/custom.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

    <?php include('../src/web.php'); ?>
    <script src="<?php BASE_URL ?>/js/jquery.min.js"></script>
    <script src="<?php BASE_URL ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?php BASE_URL ?>/js/scripts.js"></script>
    <script src="<?php BASE_URL ?>/js/jquery.mask.min.js"></script>
    <script src="<?php BASE_URL ?>/js/app.js"></script>

    <div class="modal fade" id="modalLoading" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body py-4 d-flex align-items-center justify-content-center gap-3">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mb-0 fw-bold fs-6">Aguarde, processando...</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>