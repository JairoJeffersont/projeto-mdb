<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\DiretorioModel;

class DiretorioController {

    public static function listarDiretorios(?int $tipo_id = null): array {
        try {
            $diretorios = DiretorioModel::when($tipo_id, function ($query) use ($tipo_id) {
                $query->where('tipo_id', $tipo_id);
            })
                ->withCount([
                    'filiados as filiados_count' => function ($query) {
                        $query->where('ativo', 1); // 👈 somente ativos
                    }
                ])
                ->orderBy('municipio', 'asc')
                ->get();

            if ($diretorios->isEmpty()) {
                return [
                    'status'  => 'empty',
                    'message' => 'Nenhum diretório encontrado',
                    'data'    => []
                ];
            }

            return [
                'status' => 'success',
                'total'  => $diretorios->count(),
                'data'   => $diretorios->toArray()
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    public static function buscarDiretorio(string $id): array {
        try {
            $diretorio = DiretorioModel::find($id);

            if (!$diretorio) {
                return ['status' => 'not_found', 'message' => 'Diretório não encontrado', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $diretorio->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function deletarDiretorio(string $id): array {
        try {

            if ($_SESSION['user']['permissao'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar esse diretório'];
            }

            $diretorio = DiretorioModel::find($id);

            if (!$diretorio) {
                return ['status' => 'not_found', 'message' => 'Diretório não encontrado'];
            }

            $diretorio->delete();

            return ['status' => 'success', 'message' => 'Diretório removido com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarDiretorio(array $dados): array {
        try {

            if ($_SESSION['user']['permissao'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar um diretório'];
            }

            $diretorio = DiretorioModel::where('municipio', $dados['municipio'])->first();

            if ($diretorio) {
                return ['status' => 'conflict', 'message' => 'Esse diretório já existe'];
            }

            $diretorio = DiretorioModel::create($dados);

            return ['status'  => 'success', 'message' => 'Diretório criado com sucesso', 'data'    => $diretorio->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarDiretorio(string $id, array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar esse diretório'];
            }

            $diretorio = DiretorioModel::find($id);

            if (!$diretorio) {
                return ['status' => 'not_found', 'message' => 'Diretório não encontrado'];
            }

            if (isset($dados['municipio'])) {
                $existeMunicipio = DiretorioModel::where('municipio', $dados['municipio'])->where('id', '!=', $id)->first();
                if ($existeMunicipio) {
                    return ['status' => 'conflict', 'message' => 'Esse diretório já existe'];
                }
            }

            $diretorio->update($dados);

            return ['status'  => 'success', 'message' => 'Diretório atualizado com sucesso', 'data' => $diretorio->toArray()];
        } catch (\Throwable $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }
}
