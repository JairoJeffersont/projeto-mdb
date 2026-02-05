<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\ComissaoModel;
use JairoJeffersont\Models\ComissaoTipoModel;
use Ramsey\Uuid\Uuid;

class ComissaoController {

    public static function listarTodosOsTiposComissoes(?string $diretorio_id = null): array {
        try {

            $query = ComissaoTipoModel::query();

            if (!is_null($diretorio_id)) {
                $query->where('diretorio_id', $diretorio_id);
            }

            $tipos = $query->get();

            return ['status'  => 'success', 'total' => count($tipos), 'data'    => $tipos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function listarTodosAsComissoes(?string $diretorio_id = null, ?int $ano = null, bool $mostrarTodasDoAno = true): array {
        try {

            $query = ComissaoModel::query();

            if ($diretorio_id) {
                $query->where('diretorio_id', $diretorio_id);
            }

            if ($mostrarTodasDoAno) {
                if ($ano) {
                    $query->whereRaw('YEAR(data_inicio) = ?', [$ano]);
                }
            } else {
                $hoje = date('Y-m-d');
                $query->where('data_inicio', '<=', $hoje)
                    ->where('data_fim', '>=', $hoje);
            }

            $comissoes = $query
                ->with('usuario:id,nome')
                ->with('tipo:id,descricao')
                ->orderBy('data_fim', 'desc')
                ->get();

            if ($comissoes->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma comissão cadastrada', 'data' => []];
            }

            return ['status'  => 'success', 'total' => count($comissoes), 'data'    => $comissoes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarComissao(array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] != 1) {
                return ['status'  => 'forbidden', 'message' => 'Você não tem autorização para criar uma comissão'];
            }

            $comissaoExistente = ComissaoModel::where(function ($query) use ($dados) {
                $query->where('data_inicio', '<=', $dados['data_fim'])
                    ->where('data_fim', '>=', $dados['data_inicio']);
            })->exists();

            if ($comissaoExistente) {
                return ['status'  => 'conflict', 'message' => 'Já existe uma comissão com esse nome nesse período'];
            }

            $dados['id'] = Uuid::uuid4()->toString();

            $comissao = ComissaoModel::create($dados);

            return ['status'  => 'success', 'message' => 'Comissao criada com sucesso', 'data' => $comissao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarComissao(string $id, array $dados): array {
        try {

            // permissão
            if ($_SESSION['user']['permissao_id'] != 1) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar uma comissão'];
            }

            // busca a comissão
            $comissao = ComissaoModel::find($id);

            if (!$comissao) {
                return ['status' => 'not_found', 'message' => 'Comissão não encontrada'];
            }

            // verifica conflito de período (ignorando a própria comissão)
            $comissaoExistente = ComissaoModel::where('id', '!=', $id)
                ->where(function ($query) use ($dados) {
                    $query->where('data_inicio', '<=', $dados['data_fim'])
                        ->where('data_fim', '>=', $dados['data_inicio']);
                })
                ->exists();

            if ($comissaoExistente) {
                return ['status' => 'conflict', 'message' => 'Já existe uma comissão nesse período'];
            }

            // atualiza
            $comissao->update($dados);

            return ['status' => 'success', 'message' => 'Comissão atualizada com sucesso', 'data' => $comissao->fresh()->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');

            return ['status' => 'server_error', 'message' => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarComissao(string $id): array {
        try {
            $comissao = ComissaoModel::with('tipo:id,descricao')
                ->with('usuario:id,nome')
                ->find($id);

            if (!$comissao) {
                return ['status' => 'not_found', 'message' => 'Comissão não encontrada', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $comissao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function deletarComissao(string $id): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar um usuário'];
            }

            $comissao = ComissaoModel::find($id);

            if (!$comissao) {
                return ['status' => 'empty', 'message' => 'Comissão não encontrada'];
            }

            $comissao->delete();

            return ['status' => 'success', 'message' => 'Usuário removido com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    
}
