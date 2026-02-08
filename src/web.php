<?php

define('BASE_PATH', dirname(__DIR__));

$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['section'] ?? 'home');

$routes = [
    'home'  => BASE_PATH . '/src/Views/home/home.php',
    'login' => BASE_PATH . '/src/Views/login/login.php',
    'logout' => BASE_PATH . '/src/Views/login/logout.php',
    'forgot-password' => BASE_PATH . '/src/Views/login/forgot-password.php',
    'new-password' => BASE_PATH . '/src/Views/login/new-password.php',
    'cadastro' => BASE_PATH . '/src/Views/cadastro/cadastro.php',
    'diretorios' => BASE_PATH . '/src/Views/diretorios/diretorios.php',
    'ficha-diretorio' => BASE_PATH . '/src/Views/diretorios/ficha-diretorio.php',
    'editar-diretorio' => BASE_PATH . '/src/Views/diretorios/editar-diretorio.php',
    'novo-filiado' => BASE_PATH . '/src/Views/filiados/novo-filiado.php',
    'ficha-filiado' => BASE_PATH . '/src/Views/filiados/ficha-filiado.php',
    'cargos-eletivos' => BASE_PATH . '/src/Views/cargos/cargos-eletivos.php',
    'editar-cargo-eletivo' => BASE_PATH . '/src/Views/cargos/editar-cargo-eletivo.php',
    'mandatarios' => BASE_PATH . '/src/Views/cargos/mandatarios.php',
    'nucleos' => BASE_PATH . '/src/Views/nucleos/nucleos.php',
    'editar-nucleo' => BASE_PATH . '/src/Views/nucleos/editar-nucleo.php',
    'comissoes' => BASE_PATH . '/src/Views/comissoes/comissoes.php',
    'editar-comissao' => BASE_PATH . '/src/Views/comissoes/editar-comissao.php',
    'editar-cargo-comissao' => BASE_PATH . '/src/Views/cargos/editar-cargo-comissao.php',
    'eleicoes-municipais' => BASE_PATH . '/src/Views/eleicoes/eleicoes-municipais.php',
    'tipos-documentos' => BASE_PATH . '/src/Views/documentos/tipos-documentos.php',
    'editar-tipo-documento' => BASE_PATH . '/src/Views/documentos/editar-tipo-documento.php'


];

if (isset($routes[$page]) && is_file($routes[$page])) {
    include $routes[$page];
} else {
    http_response_code(404);
    include BASE_PATH . '/src/Views/errors/404.php';
}
