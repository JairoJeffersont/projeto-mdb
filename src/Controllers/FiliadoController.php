<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\DiretorioModel;
use JairoJeffersont\Models\FiliadoModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\IOFactory;


class FiliadoController {

    public static function listarTodosOsFiliados(?string $diretorio_id = null, ?string $sexo = '', ?bool $ativo = null, int $itens = 10, int $pagina = 1, string $ordem = 'asc', string $ordenarPor = 'nome', ?string $busca = ''): array {
        try {
            $query = FiliadoModel::query();

            if (!is_null($diretorio_id)) {
                $query->where('diretorio_id', $diretorio_id);
            }

            if (!empty($sexo)) {
                $query->where('sexo', $sexo);
            }

            if (!is_null($ativo)) {
                $query->where('ativo', $ativo);
            }

            if (!empty($busca)) {
                $query->where('nome', 'like', '%' . $busca . '%');
            }

            $ordem = strtolower($ordem) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($ordenarPor, $ordem);

            $total = $query->count();
            $total_paginas = ceil($total / $itens);

            $Filiados = $query->skip(($pagina - 1) * $itens)
                ->take($itens)
                ->with(['usuario:id,nome'])
                ->get();


            if ($Filiados->isEmpty()) {
                return [
                    'status' => 'empty',
                    'message' => 'Nenhum filiado encontrado.',
                    'total_registros' => $total,
                    'total_paginas' => $total_paginas,
                    'total_busca' => 0,
                    'data' => []
                ];
            }


            return [
                'status' => 'success',
                'total_registros' => $total,
                'total_paginas' => $total_paginas,
                'total_busca' => count($Filiados->toArray()),
                'data' => $Filiados->toArray()
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarFiliado(string $id): array {
        try {
            $filiado = FiliadoModel::with(['usuario:id,nome'])->find($id);

            if (!$filiado) {
                return ['status' => 'not_found', 'message' => 'Filiado não encontrado', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $filiado->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function deletarFiliado(string $id): array {
        try {

            $filiado = FiliadoModel::find($id);

            if (!$filiado) {
                return ['status' => 'empty', 'message' => 'Filiado não encontrado'];
            }

            $filiado->delete();

            return ['status' => 'success', 'message' => 'Filiado apagado com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarFiliado(array $dados, bool $flagAtivo = true): array {
        try {

            $dados['id'] = Uuid::uuid4()->toString();
            if (!$flagAtivo) {
                $dados['ativo'] = false;
            }
            $filiado = FiliadoModel::create($dados);
            return ['status'  => 'success', 'message' => 'Usuário criado com sucesso. Aguardando o aceite do responsável', 'data' => $filiado->toArray()];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'conflict', 'message' => 'Já existe esse filiado nesse diretório'];
            }
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarFiliado(string $id, array $dados): array {
        try {

            $filiado = FiliadoModel::find($id);

            if (!$filiado) {
                return ['status' => 'not_found', 'message' => 'Filiado não encontrado'];
            }

            $filiado->update($dados);

            return ['status'  => 'success', 'message' => 'Diretório atualizado com sucesso', 'data' => $filiado->toArray()];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'conflict', 'message' => 'Já existe esse filiado nesse diretório'];
            }
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarPorGenero(?string $diretorio_id = null) {
        try {
            $query = FiliadoModel::query()
                ->where('ativo', 1); // 👈 somente ativos

            if (!is_null($diretorio_id)) {
                $query->where('diretorio_id', $diretorio_id);
            }

            $filiados = $query->get();
            $totalGeral = $filiados->count();

            // Agrupa por sexo
            $totais = $filiados
                ->groupBy('sexo')
                ->map(fn($grupo) => $grupo->count());

            // Garante todas as categorias
            $totalMasculino = $totais->get('MASCULINO', 0);
            $totalFeminino  = $totais->get('FEMININO', 0);
            $totalOutro     = $totais->get('OUTRO', 0);

            // Porcentagens
            $masculinoPct = $totalGeral > 0 ? round(($totalMasculino / $totalGeral) * 100, 2) : 0;
            $femininoPct  = $totalGeral > 0 ? round(($totalFeminino / $totalGeral) * 100, 2) : 0;
            $outroPct     = $totalGeral > 0 ? round(($totalOutro / $totalGeral) * 100, 2) : 0;

            return [
                'status' => 'success',
                'data' => [
                    'masculino' => $totalMasculino . ' (' . $masculinoPct . '%)',
                    'feminino'  => $totalFeminino . ' (' . $femininoPct . '%)',
                    'outro'     => $totalOutro . ' (' . $outroPct . '%)',
                    'total'     => $totalGeral
                ]
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    public static function buscarPorBairro(string $diretorio_id) {
        try {
            // Monta a query, diretorio_id é obrigatório
            $query = FiliadoModel::query()->where('diretorio_id', $diretorio_id);

            $filiados = $query->get();
            $totalGeral = $filiados->count();

            // Conta por bairro usando collection
            $totaisPorBairro = $filiados->groupBy('bairro')->map(fn($grupo) => $grupo->count());

            // Monta o retorno com total e porcentagem por bairro
            $resultado = [];
            foreach ($totaisPorBairro as $bairro => $total) {
                $porcentagem = $totalGeral > 0 ? round(($total / $totalGeral) * 100, 2) : 0;
                $resultado[$bairro] = $total . ' (' . $porcentagem . '%)';
            }

            // Adiciona total geral
            $resultado['total'] = $totalGeral;

            return ['status'  => 'success', 'data' => $resultado];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    public static function contarPorSexo(string $diretorio_id = '', bool $ativo = true): array {
        $query = FiliadoModel::query();

        if (!empty($diretorio_id)) {
            $query->where('diretorio_id', $diretorio_id);
        }

        $query->where('ativo', $ativo);

        // Contagem agrupada por sexo
        $result = $query->selectRaw('sexo, COUNT(*) as total')
            ->groupBy('sexo')
            ->pluck('total', 'sexo')
            ->toArray();

        // Garantir que todos os sexos existam no array
        return [
            'MASCULINO' => $result['MASCULINO'] ?? 0,
            'FEMININO'  => $result['FEMININO'] ?? 0,
            'OUTRO'     => $result['OUTRO'] ?? 0,
        ];
    }

    //ESSE METODO SO DEVE SER USADO PARA IMPORTAR O XLSX DO DATAVENCE
    public static function importarEInserir(string $arquivo): array {
        $spreadsheet = IOFactory::load($arquivo);
        $sheet = $spreadsheet->getActiveSheet();

        // pula as 4 primeiras linhas
        $linhas = array_slice($sheet->toArray(), 4);

        $colunasDesejadas = [
            'estado'           => 0,  // uf
            'cidade'           => 1,  // municipio
            'nome'             => 2,  // nome_pessoa
            'titulo_eleitor'   => 3,  // tituloeleitor
            'data_filiacao'    => 4,  // data_filiacao
            'data_desfiliacao' => 5,  // data_desfiliacao
            'sexo'             => 6,  // sexo
            'cpf'              => 7,  // cpf
            'rg'               => 8,  // rg
            'data_nascimento'  => 9,  // nascimento (dd/mm/aaaa)
            'email'            => 12, // email
            'telefone'         => 22, // telefone
            'cep'              => 14, // cep
            'bairro'           => 17, // bairro
            'zona'             => 18, // zona
            'secao'            => 19, // secao
        ];

        $retorno = [
            'success'    => 0,
            'duplicated' => 0,
            'errors'     => 0,
            'detalhes'   => []
        ];

        foreach ($linhas as $linha) {

            // pula linha vazia
            if (empty(array_filter($linha))) {
                continue;
            }

            $registro = [];

            foreach ($colunasDesejadas as $campo => $indice) {
                $valor = $linha[$indice] ?? null;

                // trata data_nascimento (dd/mm/aaaa -> dd/mm)
                if ($campo === 'data_nascimento' && !empty($valor)) {
                    $partes = explode('/', $valor);
                    if (count($partes) >= 2) {
                        $valor = $partes[0] . '/' . $partes[1];
                    }
                }

                $registro[$campo] = $valor;
            }

            // endereco = coluna 15 + 16
            $registro['endereco'] = trim(
                ($linha[15] ?? '') . ' ' . ($linha[16] ?? '')
            );

            $diretorio = DiretorioModel::where(
                'municipio',
                $registro['cidade']
            )->first();

            $registro['diretorio_id'] = $diretorio?->id;
            $registro['usuario_id'] = $_SESSION['user']['id'];
            $registro['ativo'] = 1;

            $resultado = self::criarFiliado($registro, );

            $retorno['detalhes'][] = [
                'email'  => $registro['email'] ?? null,
                'status' => $resultado['status']
            ];

            match ($resultado['status']) {
                'success'    => $retorno['success']++,
                'duplicated' => $retorno['duplicated']++,
                default      => $retorno['errors']++
            };
        }

        return $retorno;
    }
}
