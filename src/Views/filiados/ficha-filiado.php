<?php

use JairoJeffersont\Controllers\CargoEletivoController;
use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;

include('../src/Views/includes/verifyLogged.php');

$filiadoId = $_GET['filiado'] ?? '';

$buscaFiliado = FiliadoController::buscarFiliado($filiadoId);

if ($buscaFiliado['status'] != 'success') {
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
                    <a class="btn btn-success btn-sm loading-modal" href="?section=ficha-diretorio&diretorio=<?= $buscaFiliado['data']['diretorio_id'] ?>" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-1 text-primary">Ficha do filiado | <?= $buscaFiliado['data']['nome'] ?></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Preencha os dados abaixo para atualizar este filiado ou, se desejar, selecione o diretório para movê-lo.
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
                            'ativo'              => (int) $_POST['ativo'] ?? null,
                            'diretorio_id'       => $_POST['diretorio_id'] ?? null,
                        ];

                        $result = FiliadoController::atualizarFiliado($filiadoId, $dadosFiliado);

                        if ($result['status'] == 'success') {
                            header('Location: ?section=ficha-filiado&filiado=' . $filiadoId);
                        } else if ($result['status'] == 'server_error' || $result['status'] == 'confilct') {
                            echo '<div class="alert alert-danger rounded-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        } else {
                            echo '<div class="alert alert-info  rounded-1 px-2 py-1 px-2 py-1 mb-2" role="alert" data-timeout="3"><b>' . $result['message'] . '</b></div>';
                        }
                    }

                    ?>

                    <form class="row g-2 align-items-end" method="post">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome completo" value="<?= $buscaFiliado['data']['nome'] ?>" required>
                        </div>

                        <div class="col-md-3 col-8">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="E-mail" value="<?= $buscaFiliado['data']['email'] ?>">
                        </div>

                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="telefone" data-mask="(00) 0000-0000" value="<?= $buscaFiliado['data']['telefone'] ?>" placeholder="Telefone">
                        </div>

                        <div class="col-md-3 col-6">
                            <select class="form-select form-select-sm" name="sexo" required>
                                <option value="">Sexo</option>
                                <option value="MASCULINO" <?= ($buscaFiliado['data']['sexo'] ?? '') === 'MASCULINO' ? 'selected' : '' ?>>Masculino</option>
                                <option value="FEMININO" <?= ($buscaFiliado['data']['sexo']) === 'FEMININO'  ? 'selected' : '' ?>>Feminino</option>
                                <option value="OUTRO" <?= ($buscaFiliado['data']['sexo']) === 'OUTRO'     ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="data_nascimento" data-mask="00/00" value="<?= $buscaFiliado['data']['data_nascimento'] ?>" placeholder="Nascimento (DD/MM)" required>
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="data_filiacao" data-mask="00/00/0000" value="<?= $buscaFiliado['data']['data_filiacao'] ?>" placeholder="Filiação (DD/MM/AAAA)">
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="data_desfiliacao" data-mask="00/00/0000" value="<?= $buscaFiliado['data']['data_desfiliacao'] ?>" placeholder="Desfiliação (DD/MM/AAAA)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="cpf" data-mask="000.000.000-00" value="<?= $buscaFiliado['data']['cpf'] ?>" placeholder="CPF (000.000.000-00)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="rg" data-mask=0000000 value="<?= $buscaFiliado['data']['rg'] ?>" placeholder="RG (0000000)">
                        </div>

                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="titulo_eleitoral" data-mask="00000000000" value="<?= $buscaFiliado['data']['titulo_eleitoral'] ?>" placeholder="Título eleitoral" required>
                        </div>

                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="zona_eleitoral" data-mask="0000" value="<?= $buscaFiliado['data']['zona_eleitoral'] ?>" placeholder="Zona">
                        </div>

                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="secao_eleitoral" data-mask="0000" value="<?= $buscaFiliado['data']['secao_eleitoral'] ?>" placeholder="Seção">
                        </div>

                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" name="endereco" value="<?= $buscaFiliado['data']['endereco'] ?>" placeholder="Endereço">
                        </div>

                        <div class="col-md-2 col-8">
                            <input type="text" class="form-control form-control-sm" name="bairro" value="<?= $buscaFiliado['data']['bairro'] ?>" placeholder="Bairro">
                        </div>

                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="cep" data-mask="00000-000" value="<?= $buscaFiliado['data']['cep'] ?>" placeholder="CEP">
                        </div>

                        <div class="col-md-2 col-5">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?= $buscaFiliado['data']['estado'] ?>" required>
                            </select>
                        </div>

                        <div class="col-md-2 col-7">
                            <select class="form-select form-select-sm municipio" name="cidade" data-selected="<?= $buscaFiliado['data']['cidade'] ?>" required>
                                <option>Selecione o município</option>
                            </select>
                        </div>

                        <?php
                        $bloqueado = ($_SESSION['user']['permissao_id'] != '1');
                        ?>

                        <?php
                        $bloqueado = ($_SESSION['user']['permissao_id'] != '1');
                        $ativo = (int)($buscaFiliado['data']['ativo'] ?? 0);
                        ?>

                        <div class="col-md-1 col-6">
                            <select
                                class="form-select form-select-sm"
                                name="ativo"
                                <?= $bloqueado ? 'disabled' : '' ?>
                                required>
                                <option value="1" <?= $ativo === 1 ? 'selected' : '' ?>>Ativo</option>
                                <option value="0" <?= $ativo === 0 ? 'selected' : '' ?>>Desativado</option>
                            </select>

                            <?php if ($bloqueado): ?>
                                <input type="hidden" name="ativo" value="<?= $ativo ?>">
                            <?php endif; ?>
                        </div>


                        <div class="col-md-3 col-6">
                            <select class="form-select form-select-sm" name="diretorio_id" <?= $bloqueado ? 'disabled' : '' ?> required>
                                <?php
                                $buscaDiretorio = DiretorioController::listarDiretorios(2);
                                foreach ($buscaDiretorio['data'] as $diretorio) {

                                    $selected = ($diretorio['id'] == $buscaFiliado['data']['diretorio_id'])
                                        ? 'selected'
                                        : '';

                                    echo '<option value="' . $diretorio['id'] . '" ' . $selected . '>
                                            Diretório - ' . $diretorio['municipio'] . '
                                          </option>';
                                }
                                ?>
                            </select>

                            <?php if ($bloqueado): ?>
                                <input type="hidden" name="diretorio_id" value="<?= $buscaFiliado['data']['diretorio_id'] ?>">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" rows="3" name="informacoes_adicionais" placeholder="Informações adicionais"><?= $buscaFiliado['data']['informacoes_adicionais'] ?></textarea>
                        </div>

                        <div class="col-sm-2 col-12 text-start mt-2">
                            <button type="submit" class="btn btn-success w-100 btn-sm px-4 confirm-action" data-message="Os dados desse filiado estão corretos?"><i class="bi bi-floppy"></i> Atualizar </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Cargos eletivos já ocupados</h5>
                    <p class="mb-0 text-muted small mb-2">
                        Veja todos os cargos eletivos que esse filiado já ocupou.
                    </p>
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 ">
                            <thead>
                                <tr class="small">
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Mandato</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php
                                    $buscaMandatos = CargoEletivoController::listarCargosPorFiliado($filiadoId);
                                    if($buscaMandatos['status'] == 'success'){
                                        foreach($buscaMandatos['data'] as $mandato){
                                            echo '<tr>';
                                            echo '<td>'.$mandato['descricao'].'</td>';
                                            echo '<td>'.date('d/m/Y', strtotime($mandato['inicio_mandato'])).' - '.date('d/m/Y', strtotime($mandato['fim_mandato'])).'</td>';
                                            echo '</tr>';
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <p class="mb-0 mb-0 text-muted ">
                        Esse filiado foi adicionado por <?= $buscaFiliado['data']['usuario']['nome'] ?> em <?= date('d/m/Y à\s H:i', strtotime($buscaFiliado['data']['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>