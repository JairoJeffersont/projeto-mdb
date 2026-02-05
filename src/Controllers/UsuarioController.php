<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\UsuarioModel;
use Ramsey\Uuid\Uuid;

class UsuarioController {

    public static function listarTodosOsUsuarios(?string $diretorio_id = null): array {
        try {

            $usuarios = UsuarioModel::when($diretorio_id, function ($query) use ($diretorio_id) {
                $query->where('diretorio_id', $diretorio_id);
            })
                ->orderBy('nome', 'asc')
                ->get();

            if ($usuarios->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum usuário cadastrado', 'data' => []];
            }

            return ['status'  => 'success', 'total' => count($usuarios), 'data'    => $usuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarUsuario(string $id): array {
        try {
            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function deletarUsuario(string $id): array {
        try {

            if ($_SESSION['user']['permissao'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar um usuário'];
            }

            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'empty', 'message' => 'Usuário não encontrado'];
            }

            $usuario->delete();

            return ['status' => 'success', 'message' => 'Usuário removido com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarUsuario(array $dados): array {
        try {

            $usuario = UsuarioModel::where('email', $dados['email'])->exists();

            if ($usuario) {
                return ['status'  => 'conflict', 'message' => 'Esse usuário já está cadastrado'];
            }
            $dados['permissao_id'] = '2';
            $dados['ativo'] = false;
            $dados['id'] = Uuid::uuid4()->toString();
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $usuario = UsuarioModel::create($dados);

            return ['status'  => 'success', 'message' => 'Usuário criado com sucesso', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarUsuarios(string $id, array $dados): array {
        try {

            if ($_SESSION['user']['permissao'] != 1 && $_SESSION['user']['id'] !== $id) {
                return ['status'  => 'forbidden', 'message' => 'Você não tem autorização para atualizar este usuário'];
            }

            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            if (!empty($dados['senha'])) {
                unset($dados['senha']);
            }

            $usuario->update($dados);

            return ['status'  => 'success', 'message' => 'Usuário atualizado com sucesso', 'data'    => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }
}
