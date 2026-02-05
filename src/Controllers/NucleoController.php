<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\NucleoModel;
use Ramsey\Uuid\Uuid;

class NucleoController {

    public static function listarTodosOsNucleos(?string $diretorio_id = null): array {
        try {

            $nucleos = NucleoModel::when($diretorio_id, function ($query) use ($diretorio_id) {
                $query->where('diretorio_id', $diretorio_id);
            })
                ->orderBy('nome', 'asc')
                ->with('usuario:id,nome')
                ->get();


            if ($nucleos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum núcleo cadastrado', 'data' => []];
            }

            return ['status'  => 'success', 'total' => count($nucleos), 'data'    => $nucleos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarNucleo(string $id): array {
        try {
            $nucleo = NucleoModel::with('usuario:id,nome')
                ->find($id);

            if (!$nucleo) {
                return ['status' => 'not_found', 'message' => 'Núcleo não encontrado', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $nucleo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarNucleo(array $dados): array {
        try {

            $nucleo = NucleoModel::where('nome', $dados['nome'])->exists();

            if ($nucleo) {
                return ['status'  => 'conflict', 'message' => 'Esse núcleo já está cadastrado'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $nucleo = NucleoModel::create($dados);

            return ['status'  => 'success', 'message' => 'Núcleo criado com sucesso', 'data' => $nucleo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarNucleo(string $id, array $dados): array {
        try {

            // Verificação de permissão
            if ($_SESSION['user']['permissao_id'] != 1) {
                return ['status'  => 'forbidden', 'message' => 'Você não tem autorização para atualizar este núcleo'];
            }

            // Busca o núcleo pelo ID
            $nucleo = NucleoModel::find($id);

            if (!$nucleo) {
                return ['status'  => 'not_found', 'message' => 'Núcleo não encontrado'];
            }

            // Verifica se já existe outro núcleo com o mesmo nome
            $existeNome = NucleoModel::where('nome', $dados['nome'])
                ->where('id', '!=', $id)
                ->exists();

            if ($existeNome) {
                return ['status'  => 'conflict', 'message' => 'Esse núcleo já está cadastrado'];
            }

            // Atualiza os dados
            $nucleo->update($dados);

            return ['status'  => 'success', 'message' => 'Núcleo atualizado com sucesso', 'data'    => $nucleo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function deletarNucleo(string $id): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar um usuário'];
            }

            $nucleo = NucleoModel::find($id);

            if (!$nucleo) {
                return ['status' => 'empty', 'message' => 'Núcleo não encontrado'];
            }

            $nucleo->delete();

            return ['status' => 'success', 'message' => 'Núcleo removido com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }
}
