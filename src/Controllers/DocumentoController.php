<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\DocumentoTipoModel;
use Ramsey\Uuid\Uuid;

class DocumentoController {

    public static function listarTodosOsTiposDocumentos(?string $diretorio_id = null): array {
        try {

            $nucleos = DocumentoTipoModel::when($diretorio_id, function ($query) use ($diretorio_id) {
                $query->where('diretorio_id', $diretorio_id);
            })
                ->orderBy('descricao', 'asc')
                ->with('usuario:id,nome')
                ->get();


            if ($nucleos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de documento cadastrado', 'data' => []];
            }

            return ['status'  => 'success', 'total' => count($nucleos), 'data'    => $nucleos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarTipoDeDocumento(array $dados): array {
        try {

            $tipo = DocumentoTipoModel::where('descricao', $dados['descricao'])->where('diretorio_id', $dados['diretorio_id'])->exists();

            if ($tipo) {
                return ['status'  => 'conflict', 'message' => 'Esse tipo já está cadastrado'];
            }

            $dados['id'] = Uuid::uuid4()->toString();

            $usuario = DocumentoTipoModel::create($dados);

            return ['status'  => 'success', 'message' => 'Tipo de documento criado com sucesso', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function editarTipoDeDocumento(string $id, array $dados): array {
        try {

            $tipo = DocumentoTipoModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }

            $existe = DocumentoTipoModel::where('descricao', $dados['descricao'])
                ->where('diretorio_id', $dados['diretorio_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($existe) {
                return ['status' => 'conflict', 'message' => 'Esse tipo já está cadastrado'];
            }

            $tipo->update($dados);

            return ['status' => 'success', 'message' => 'Tipo de documento atualizado com sucesso', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarTipoDeDocumento(string $id): array {
        try {

            $tipo = DocumentoTipoModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado', 'data' => []];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }
}
