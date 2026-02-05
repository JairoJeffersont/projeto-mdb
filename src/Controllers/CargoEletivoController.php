<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Models\CargoEletivoModel;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Capsule\Manager as DB;
use JairoJeffersont\Models\FiliadoModel;

class CargoEletivoController {

    public static function listarTodosOsCargosEletivos(?string $diretorio_id = null): array {
        try {

            $cargos = CargoEletivoModel::where('diretorio_id', $diretorio_id)
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

    public static function criarCargoEletivo(array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar um cargo eletivo'];
            }

            $cargo = CargoEletivoModel::where('descricao', $dados['descricao'])->where('diretorio_id', $dados['diretorio_id'])->exists();

            if ($cargo) {
                return ['status'  => 'conflict', 'message' => 'Esse cargo já está cadastrado nesse diretório'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $usuario = CargoEletivoModel::create($dados);

            return ['status'  => 'success', 'message' => 'Cargo criado com sucesso', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function buscarCargoEletivo(string $id): array {
        try {
            $cargo = CargoEletivoModel::with('usuario:id,nome')
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

    public static function deletarCargoEletivo(string $id): array {
        try {

            if ($_SESSION['user']['permissao'] == 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar um cargo eletivo'];
            }

            $cargo = CargoEletivoModel::find($id);

            if (!$cargo) {
                return ['status' => 'empty', 'message' => 'Cargo não encontrado'];
            }

            $cargo->delete();

            return ['status' => 'success', 'message' => 'Cargo removido com sucesso'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status'   => 'server_error', 'message'  => 'Erro interno do servidor', 'error_id' => $errorId];
        }
    }

    public static function atualizarCargoEletivo(string $id, array $dados): array {
        try {

            if ($_SESSION['user']['permissao_id'] != 1 && $_SESSION['user']['id'] !== $id) {
                return ['status'  => 'forbidden', 'message' => 'Você não tem autorização para atualizar este cargo eletivo'];
            }

            $cargo = CargoEletivoModel::find($id);

            if (!$cargo) {
                return ['status' => 'not_found', 'message' => 'Cargo não encontrado'];
            }

            $existeCargo = CargoEletivoModel::where('diretorio_id', $dados['diretorio_id'])
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

    public static function listarMembrosCargoEletivo(string $id_cargo): array {
        try {
            // Busca o cargo com os filiados
            $cargo = CargoEletivoModel::with('filiados')->find($id_cargo);

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

            // Mapeia os filiados
            $membros = $cargo->filiados->map(function ($filiado) {
                return [
                    'id'   => $filiado->id,
                    'nome' => $filiado->nome,
                    'inicio_mandato' => $filiado->pivot->inicio_mandato,
                    'fim_mandato'    => $filiado->pivot->fim_mandato
                ];
            })->toArray();

            return [
                'status' => 'success',
                'data'   => $membros
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


    public static function atribuirCargoEletivo(string $id_cargo, string $filiado, string $inicio_mandato, string $fim_mandato): array {
        try {

            // Verifica permissão
            if ($_SESSION['user']['permissao_id'] == 2) {
                return [
                    'status'  => 'forbidden',
                    'message' => 'Você não tem autorização para atribuir cargo eletivo'
                ];
            }

            DB::beginTransaction();

            $cargo = CargoEletivoModel::with('filiados')->find($id_cargo);

            if (!$cargo) {
                DB::rollBack();
                return [
                    'status'  => 'not_found',
                    'message' => 'Cargo não encontrado'
                ];
            }

            $filiadoObj = FiliadoModel::find($filiado);
            if (!$filiadoObj) {
                DB::rollBack();
                return [
                    'status' => 'not_found',
                    'message' => 'Filiado não encontrado'
                ];
            }

            /**
             * REGRA 1 — Checa sobreposição de mandato do filiado em qualquer cargo
             */
            $sobreposicao = DB::table('cargo_eletivo_membros')
                ->where('filiado_id', $filiado)
                ->where(function ($query) use ($inicio_mandato, $fim_mandato) {
                    $query->where(function ($q) use ($inicio_mandato, $fim_mandato) {
                        $q->where('inicio_mandato', '<', $fim_mandato)
                            ->where('fim_mandato', '>', $inicio_mandato);
                    });
                })
                ->exists();

            if ($sobreposicao) {
                DB::rollBack();
                return [
                    'status'  => 'error',
                    'message' => 'Este filiado já ocupa um cargo em um período que se sobrepõe a este mandato'
                ];
            }

            /**
             * REGRA 2 — Cargo único
             */
            if (!$cargo->multiplo) {

                // Busca qualquer membro já ocupando esse cargo no período informado
                $membrosNoPeriodo = DB::table('cargo_eletivo_membros')
                    ->where('cargo_id', $id_cargo)
                    ->where(function ($query) use ($inicio_mandato, $fim_mandato) {
                        $query->where(function ($q) use ($inicio_mandato, $fim_mandato) {
                            $q->where('inicio_mandato', '<=', $fim_mandato)
                                ->where('fim_mandato', '>=', $inicio_mandato);
                        });
                    })
                    ->pluck('filiado_id'); // pega os IDs para remover

                if ($membrosNoPeriodo->isNotEmpty()) {
                    // Remove os membros antigos que estão no período
                    $cargo->filiados()->detach($membrosNoPeriodo);
                }

                // Adiciona o novo filiado
                $cargo->filiados()->attach($filiado, [
                    'inicio_mandato' => $inicio_mandato,
                    'fim_mandato'    => $fim_mandato
                ]);
            } else {
                /**
                 * REGRA 3 — Cargo múltiplo
                 */
                $cargo->filiados()->attach($filiado, [
                    'inicio_mandato' => $inicio_mandato,
                    'fim_mandato'    => $fim_mandato
                ]);
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
}
