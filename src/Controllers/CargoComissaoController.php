<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\CargoComissaoModel;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Capsule\Manager as DB;
use JairoJeffersont\Models\FiliadoModel;


class CargoComissaoController {

    public static function listarTodosOsCargosComissao(?string $comissao_id = null): array {
        try {

            $cargos = CargoComissaoModel::where('comissao_id', $comissao_id)
                ->with('usuario:id,nome')
                ->orderBy('descricao', 'asc')
                ->get();

            if ($cargos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum cargo cadastrado', 'data' => []];
            }

            return ['status'  => 'success', 'total' => count($cargos), 'data'    => $cargos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function criarCargoComissao(array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar um cargo de uma comissão'];
            }

            $cargo = CargoComissaoModel::where('comissao_id', $dados['comissao_id'])
                ->where('descricao', $dados['descricao'])
                ->exists();

            if ($cargo) {
                return ['status'  => 'conflict', 'message' => 'Esse cargo já está cadastrado nessa comissão'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $usuario = CargoComissaoModel::create($dados);

            return ['status'  => 'success', 'message' => 'Cargo criado com sucesso', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarCargoComissao(string $id, array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar um cargo de uma comissão'];
            }

            $cargo = CargoComissaoModel::find($id);

            if (!$cargo) {
                return ['status' => 'not_found', 'message' => 'Cargo não encontrado'];
            }

            $existeCargo = CargoComissaoModel::where('comissao_id', $dados['comissao_id'])
                ->where('descricao', $dados['descricao'])
                ->where('id', '!=', $id)
                ->exists();

            if ($existeCargo) {
                return ['status' => 'conflict', 'message' => 'Esse cargo já está cadastrado nessa comissão'];
            }

            $cargo->update($dados);

            return ['status'  => 'success', 'message' => 'Cargo atualizado com sucesso', 'data'    => $cargo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarCargoComissao(string $id): array {
        try {
            $cargo = CargoComissaoModel::with('usuario:id,nome')
                ->find($id);

            if (!$cargo) {
                return ['status' => 'not_found', 'message' => 'Cargo não encontrado', 'data' => []];
            }

            return ['status'  => 'success', 'data' => $cargo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atriburCargoComissao(string $id_cargo, string $filiado): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return [
                    'status'  => 'forbidden',
                    'message' => 'Você não tem autorização para atribuir cargo em comissão'
                ];
            }

            DB::beginTransaction();

            $cargo = CargoComissaoModel::with('filiados')->find($id_cargo);

            if (!$cargo) {
                DB::rollBack();
                return [
                    'status'  => 'not_found',
                    'message' => 'Cargo não encontrado'
                ];
            }

            // validar se filiado existe
            if (!FiliadoModel::find($filiado)) {
                DB::rollBack();
                return [
                    'status' => 'not_found',
                    'message' => 'Filiado não encontrado'
                ];
            }

            /**
             * REGRA 1
             * Filiado não pode ocupar 2 cargos
             */
            $jaPossuiCargo = DB::table('cargo_comissao_membros')
                ->where('filiado_id', $filiado)
                ->exists();

            if ($jaPossuiCargo) {
                DB::rollBack();
                return [
                    'status'  => 'error',
                    'message' => 'Este filiado já ocupa um cargo em comissão'
                ];
            }

            /**
             * REGRA 2 — Cargo único
             */
            if (!$cargo->multiplo) {

                if ($cargo->filiados->count() > 0) {
                    // substitui o membro
                    $cargo->filiados()->sync([$filiado]);
                } else {
                    // adiciona
                    $cargo->filiados()->attach($filiado);
                }
            }
            /**
             * REGRA 3 — Cargo múltiplo
             */
            else {
                $cargo->filiados()->attach($filiado);
            }

            DB::commit();

            return [
                'status'  => 'success',
                'message' => 'Filiado atribuído ao cargo com sucesso'
            ];
        } catch (\Throwable $e) {

            DB::rollBack();

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');

            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    public static function listarMembrosCargoComissao(string $id_cargo): array {
        try {

            $cargo = CargoComissaoModel::with('filiados')->find($id_cargo);

            if (!$cargo) {
                return [
                    'status'  => 'not_found',
                    'message' => 'Cargo não encontrado'
                ];
            }

            // 👉 quando não houver membros
            if ($cargo->filiados->isEmpty()) {
                return [
                    'status'  => 'empty',
                    'message' => 'Nenhum membro vinculado a este cargo',
                    'data'    => []
                ];
            }

            return [
                'status' => 'success',
                'data'   => $cargo->filiados->map(function ($filiado) {
                    return [
                        'id'   => $filiado->id,
                        'nome' => $filiado->nome
                    ];
                })->values()->toArray()
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
}
