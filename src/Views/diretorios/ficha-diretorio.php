<?php

use JairoJeffersont\Controllers\CargoComissaoController;
use JairoJeffersont\Controllers\CargoEletivoController;
use JairoJeffersont\Controllers\ComissaoController;
use JairoJeffersont\Controllers\DiretorioController;
use JairoJeffersont\Controllers\FiliadoController;
use JairoJeffersont\Controllers\NucleoController;
use Illuminate\Database\Capsule\Manager as DB;


include('../src/Views/includes/verifyLogged.php');

$diretorioId = $_GET['diretorio'] ?? '';
$anoGet = (int) date('Y');
$todasAscomissoes = false;

$buscaDiretorio = DiretorioController::buscarDiretorio($diretorioId);

if ($buscaDiretorio['status'] != 'success') {
    header('Location: ?section=diretorios');
}

$ordemGet       = $_GET['ordem']        ?? 'ASC';
$ordernarPorGet = $_GET['ordernarPor']  ?? 'nome';
$itensGet       = (int) ($_GET['itens']  ?? 15);
$paginaGet      = (int) ($_GET['pagina'] ?? 1);
$sexoGet        = $_GET['sexo']         ?? '';
$ativoGet       = (int) ($_GET['ativo'] ?? 1);
$buscaGet       = $_GET['busca']        ?? '';

$buscaFiliados = FiliadoController::listarTodosOsFiliados($diretorioId, $sexoGet, $ativoGet, $itensGet, $paginaGet, $ordemGet, $ordernarPorGet, $buscaGet);
$buscaFiliadosTotais = FiliadoController::buscarPorGenero($diretorioId);



?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm loading-modal" href="?section=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-arrow-clockwise"></i> Atualizar</a>
                    <a class="btn btn-secondary btn-sm loading-modal" href="?section=documentos&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-files"></i> Documentos</a>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-12 col-md-6 mb-0 mb-md-0">
                    <div class="card small" style="height: 114px;">
                        <div class="card-body px-3 py-2">
                            <h4 class="card-title mb-2 text-primary"><?= $buscaDiretorio['data']['municipio'] ?><small> | <a class="text-primary small loading-modal" href="?section=editar-diretorio&diretorio=<?= $diretorioId ?>" role="button"> editar</a></small></h4>
                            <p class="mb-0 text-muted">Email: <?= $buscaDiretorio['data']['email'] ?? 'Não informado' ?></p>
                            <p class="mb-0 text-muted">Endereço: <?= $buscaDiretorio['data']['endereco'] ?? 'Não informado' ?></p>
                            <p class="mb-0 text-muted">Telefone: <?= $buscaDiretorio['data']['telefone'] ?? 'Não informado' ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-0 mb-md-0">
                    <div class="card small" style="height: 114px;">
                        <div class="card-body px-3 py-2">
                            <h4 class="card-title mb-2 text-primary">Total de filiados | <?= $buscaFiliados['total_registros'] ?></h4>
                            <p class="mb-0 text-muted">
                                Masculino: <?= $buscaFiliadosTotais['data']['masculino'] ?>
                            </p>
                            <p class="mb-0 text-muted">
                                Feminino: <?= $buscaFiliadosTotais['data']['feminino'] ?>
                            </p>
                            <p class="mb-0 text-muted">
                                Outro: <?= $buscaFiliadosTotais['data']['outro'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!--<div class="card mb-2">
                <div class="card-body p-2">
                    <a class="btn btn-outline-success btn-sm loading-modal" href="?section=novo-filiado&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-plus-circle-fill"></i> filiado</a>
                    <a class="btn btn-outline-primary btn-sm loading-modal" href="?section=nova-comissao&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-plus-circle-fill"></i> comissão</a>
                    <a class="btn btn-outline-secondary btn-sm loading-modal" href="?section=novo-cargo-eletivo&diretorio=<?= $diretorioId ?>" role="button"><i class="bi bi-plus-circle-fill"></i> mandatário</a>
                </div>
            </div>-->

            <div class="card mb-2">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary"><i class="bi bi-file-text"></i> Comissões | <a class="text-primary small loading-modal" href="?section=comissoes&diretorio=<?= $diretorioId ?>" role="button"> nova comissão</a></small></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira a comissão atualmente em vigor neste diretório.
                    </p>
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Comissão</th>
                                    <th scope="col">Membros</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaComissoes = ComissaoController::listarTodosAsComissoes($diretorioId, $anoGet, $todasAscomissoes);
                                if ($buscaComissoes['status'] == 'success') {
                                    foreach ($buscaComissoes['data'] as $comissao) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=editar-comissao&comissao=' . $comissao['id'] . '&diretorio=' . $diretorioId . '">' . $comissao['tipo']['descricao'] . '</a></td>';
                                        echo '<td>';

                                        $buscaCargos = CargoComissaoController::listarTodosOsCargosComissao($comissao['id']);

                                        foreach ($buscaCargos['data'] as $cargo) {
                                            $buscaMembros = CargoComissaoController::listarMembrosCargoComissao($cargo['id']);
                                            foreach ($buscaMembros['data'] as $membro) {
                                                echo '➡️ ' . $cargo['descricao'] . ' - ' . $membro['nome'] . '<br>';
                                            }
                                        }

                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaComissoes['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaComissoes['message'] . '</td></tr>';
                                } else if ($buscaComissoes['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaComissoes['message'] . ' | ' . $buscaComissoes['error_id'] . '</td></tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary"><i class="bi bi-person-badge"></i> Mandatarios | <a class="text-primary small loading-modal" href="?section=cargos-eletivos&diretorio=<?= $diretorioId ?>" role="button"> novo mandatário</a></small></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira todos os mandatários com mandatos vigente desse diretório.
                    </p>
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Mandatário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $anoAtual = date('Y'); // ano corrente

                                $buscaCargos = CargoEletivoController::listarTodosOsCargosEletivos($diretorioId);

                                if ($buscaCargos['status'] === 'success') {

                                    $exibiuAlgum = false;

                                    foreach ($buscaCargos['data'] as $cargo) {

                                        // Filtra membros diretamente no banco usando o ano corrente
                                        $membrosAtuais = DB::table('cargo_eletivo_membros as cem')
                                            ->join('filiado as f', 'f.id', '=', 'cem.filiado_id')
                                            ->select('f.id', 'f.nome', 'cem.inicio_mandato', 'cem.fim_mandato')
                                            ->where('cem.cargo_id', $cargo['id'])
                                            ->whereYear('cem.inicio_mandato', '<=', $anoAtual)
                                            ->whereYear('cem.fim_mandato', '>=', $anoAtual)
                                            ->get()
                                            ->toArray();

                                        if (empty($membrosAtuais)) {
                                            continue; // pula cargos sem membros no ano corrente
                                        }

                                        $exibiuAlgum = true;

                                        echo '<tr>';
                                        echo '<td><a class="loading-modal" href="?section=editar-cargo-eletivo&cargo='.$cargo['id'].'&diretorio='.$diretorioId.'">' . htmlspecialchars($cargo['descricao']) . '</a></td>';
                                        echo '<td>';

                                        foreach ($membrosAtuais as $membro) {
                                            $anoInicio = date('Y', strtotime($membro->inicio_mandato));
                                            $anoFim    = date('Y', strtotime($membro->fim_mandato));
                                            echo '➡️ ' . htmlspecialchars($membro->nome) . " ({$anoInicio} - {$anoFim})<br>";
                                        }

                                        echo '</td>';
                                        echo '</tr>';
                                    }

                                    if (!$exibiuAlgum) {
                                        echo '<tr><td colspan="2">Nenhum mandatário com mandato ativo</td></tr>';
                                    }
                                } else if ($buscaCargos['status'] === 'empty') {
                                    echo '<tr><td colspan="2">' . htmlspecialchars($buscaCargos['message']) . '</td></tr>';
                                } else if ($buscaCargos['status'] === 'server_error') {
                                    echo '<tr><td colspan="2">' . htmlspecialchars($buscaCargos['message'] . ' | ' . $buscaCargos['error_id']) . '</td></tr>';
                                }
                                ?>






                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary"><i class="bi bi-boxes"></i> Núcleos | <small><a class="text-primary" href="?section=nucleos&diretorio=<?= $diretorioId ?>" role="button"> novo núcleo</a></small></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira a lista de filiados que fazem parte desse diretório.
                    </p>

                    <ul class="list-group small">
                        <?php
                        $buscaNucleos = NucleoController::listarTodosOsNucleos($diretorioId);
                        if ($buscaNucleos['status'] == 'success') {
                            foreach (array_slice($buscaNucleos['data'], 0, 5) as $nucleo) {
                                echo '<li class="list-group-item"><a href="?section=editar-nucleo&nucleo=' . $nucleo['id'] . '&diretorio=' . $diretorioId . '"><i class="bi bi-arrow-right"></i> ' . $nucleo['nome'] . '</a></li>';
                            }
                        } else if ($buscaNucleos['status'] == 'empty') {
                            echo '<li class="list-group-item">' . $buscaNucleos['message'] . '</li>';
                        } else if ($buscaNucleos['status'] == 'server_error') {
                            echo '<li class="list-group-item">' . $buscaNucleos['message'] . ' | ' . $buscaNucleos['error_id'] . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary"><i class="bi bi-people-fill"></i> Filiados | <small><a class="text-primary loading-modal" href="?section=novo-filiado&diretorio=<?= $diretorioId ?>" role="button"> novo filiado</a></small></h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira a lista de filiados que fazem parte desse diretório.
                    </p>
                    <form method="GET" class="mb-2">
                        <div class="row g-2">
                            <input type="hidden" name="section" value="ficha-diretorio" />
                            <input type="hidden" name="diretorio" value="<?= $diretorioId ?>" />

                            <div class="col-sm-1 col-6">
                                <select class="form-select form-select-sm" name="sexo">
                                    <option value="" <?= $sexoGet == '' ? 'selected' : '' ?>>Todos os sexos</option>
                                    <option value="MASCULINO" <?= $sexoGet == 'MASCULINO' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="FEMININO" <?= $sexoGet == 'FEMININO' ? 'selected' : '' ?>>Feminino</option>
                                    <option value="OUTRO" <?= $sexoGet == 'OUTRO' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>

                            <?php if ($_SESSION['user']['permissao_id'] == '1'): ?>
                                <div class="col-sm-1 col-6">
                                    <select class="form-select form-select-sm" name="ativo">
                                        <option value="1" <?= (int)$ativoGet === 1 ? 'selected' : '' ?>>
                                            Ativo
                                        </option>
                                        <option value="0" <?= (int)$ativoGet === 0 ? 'selected' : '' ?>>
                                            Desativado
                                        </option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-sm-2 col-6">
                                <input type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Buscar por nome"
                                    name="busca"
                                    value="<?= $buscaGet ?>">
                            </div>

                            <div class="col-sm-2 col-6">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> buscar</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 ">
                            <thead>
                                <tr class="small">
                                    <th scope="col">Nome</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php
                                if ($buscaFiliados['status'] == 'success') {
                                    foreach ($buscaFiliados['data'] as $filiado) {
                                        echo '<tr>';
                                        echo '<td><a href="?section=ficha-filiado&filiado=' . $filiado['id'] . '" class="loading-modal">' . mb_convert_case($filiado['nome'], MB_CASE_TITLE, 'UTF-8') . '</a></td>';
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
                                    <a class="page-link loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>&pagina=1&itens=<?= $itensGet ?>">
                                        Primeiro
                                    </a>
                                </li>

                                <!-- Páginas numéricas -->
                                <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                                    <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                                        <a class="page-link loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>&pagina=<?= $i ?>&itens=<?= $itensGet ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Último -->
                                <li class="page-item <?= $paginaAtual == $totalPaginas ? 'disabled' : '' ?>">
                                    <a class="page-link loading-modal" href="?section=ficha-diretorio&diretorio=<?= $diretorioId ?>&pagina=<?= $totalPaginas ?>&itens=<?= $itensGet ?>">
                                        Último
                                    </a>
                                </li>

                            </ul>
                        </nav>
                    <?php endif; ?>

                </div>
            </div>

            <!--<div class="row g-2">
                <div class="col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <h5 class="card-title mb-1 text-primary">Comissão executiva vigente</h5>
                            <p class="mb-0 text-muted small mb-2">
                                Confira a comissão atualmente em vigor neste diretório.
                            </p>
                            <div class="table-responsive mb-2">
                                <table class="table table-striped table-hover table-bordered mb-0 small">
                                    <thead>
                                        <tr>
                                            <th scope="col">Comissão</th>
                                            <th scope="col">Membros</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td scope="col">Comissão permanente</td>
                                            <td scope="col">Presidente: Fulano de tal</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <h5 class="card-title mb-1 text-primary">Mandatários desse diretório</h5>
                            <p class="mb-0 text-muted small mb-2">
                                Confira os cargos eletivos e seus mandatários.
                            </p>
                            <div class="table-responsive mb-2">
                                <table class="table table-striped table-hover table-bordered mb-0 small">
                                    <thead>
                                        <tr>
                                            <th scope="col">Cardo</th>
                                            <th scope="col">Mandatário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td scope="col">Deputado Federal</td>
                                            <td scope="col">Acácio Favacho</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->

            <!--<div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Comissão executiva vigente</h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira a comissão atualmente em vigor neste diretório.
                    </p>
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Comissão</th>
                                    <th scope="col">Membros</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col">Comissão permanente</td>
                                    <td scope="col">Presidente: Fulano de tal<br>Vice-presidente: Fulano de tal</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>-->

            <!--<div class="card mb-2 ">
                <div class="card-body p-3">
                    <h5 class="card-title mb-1 text-primary">Mandatários desse diretório</h5>
                    <p class="mb-0 text-muted small mb-2">
                        Confira os cargos eletivos e seus mandatários.
                    </p>
                    <div class="table-responsive mb-2">
                        <table class="table table-striped table-hover table-bordered mb-0 small">
                            <thead>
                                <tr>
                                    <th scope="col">Cardo</th>
                                    <th scope="col">Mandatário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col">Deputado Federal</td>
                                    <td scope="col">Acácio Favacho</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>-->

        </div>
    </div>
</div>