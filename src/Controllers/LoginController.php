<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\Helpers\EmailService;
use JairoJeffersont\Helpers\LoginSessionHelper;
use JairoJeffersont\Models\UsuarioLogModel;
use JairoJeffersont\Models\UsuarioModel;
use Ramsey\Uuid\Uuid;

/**
 * Classe LoginController
 *
 * Controlador responsável por gerenciar operações de autenticação do usuário,
 * incluindo login, recuperação de senha e atualização de senha.
 *
 * @package JairoJeffersont\Controllers
 */
class LoginController {
    /**
     * Realiza o login do usuário utilizando e-mail e senha.
     *
     * Etapas executadas:
     * 1. Busca o usuário pelo e-mail.
     * 2. Verifica se o usuário existe.
     * 3. Verifica se o usuário está ativo.
     * 4. Valida a senha.
     * 5. Inicia a sessão.
     *
     * @param array $dados Array associativo contendo:
     *                     - 'email' (string): E-mail do usuário.
     *                     - 'password' (string): Senha em texto puro.
     *
     * @return array Retorna um array associativo com:
     *               - 'status' (string): Resultado do login.
     *                   - 'success'        : Login realizado com sucesso.
     *                   - 'not_found'      : Usuário não encontrado.
     *                   - 'user_deactived' : Usuário desativado.
     *                   - 'wrong_password' : Senha incorreta.
     *                   - 'login_failed'   : Falha ao iniciar sessão.
     *                   - 'server_error'   : Erro interno do servidor.
     *               - 'message' (string): Mensagem descritiva.
     *               - 'error_id' (string|null): ID do erro registrado (quando aplicável).
     */
    public static function entrar(array $dados): array {
        try {
            $usuario = UsuarioModel::where('email', $dados['email'])->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            if (!$usuario->ativo) {
                return ['status' => 'user_deactived', 'message' => 'Usuário desativado'];
            }

            if (!password_verify($dados['password'], $usuario->senha)) {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta'];
            }

            if (LoginSessionHelper::startSession($usuario->toArray())) {
                self::registrarLogin($usuario->id);
                return ['status' => 'success', 'message' => 'Login realizado com sucesso!'];
            }

            return ['status' => 'login_failed', 'message' => 'Falha ao iniciar a sessão.'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Inicia o processo de recuperação de senha enviando um e-mail ao usuário.
     *
     * Etapas executadas:
     * 1. Busca o usuário pelo e-mail.
     * 2. Gera um token único (UUID).
     * 3. Salva o token no cadastro do usuário.
     * 4. Envia o e-mail de recuperação.
     *
     * @param string $email E-mail do usuário.
     *
     * @return array Retorna:
     *               - 'status' (string):
     *                   - 'success'     : E-mail enviado com sucesso.
     *                   - 'not_found'   : Usuário não encontrado.
     *                   - 'email_error' : Erro ao enviar o e-mail.
     *                   - 'server_error': Erro interno do servidor.
     *               - 'message' (string)
     *               - 'error_id' (string|null)
     */
    public static function recuperarSenha(string $email): array {
        try {
            $usuario = UsuarioModel::where('email', $email)->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $token = Uuid::uuid4()->toString();
            $usuario->update(['token' => $token]);

            $emailService = new EmailService();
            $emailService->sendMail($usuario->email, 'RECUPERAÇÃO DE SENHA', $token);

            return ['status' => 'success', 'message' => 'E-mail de recuperação enviado com sucesso!'];
        } catch (\RuntimeException $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'email_error',
                'message'  => 'Erro ao enviar o e-mail.',
                'error_id' => $errorId
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Define uma nova senha para o usuário a partir de um token válido.
     *
     * @param string $token Token de recuperação.
     * @param string $senha Nova senha em texto puro.
     *
     * @return array Retorna:
     *               - 'status' (string)
     *               - 'message' (string)
     *               - 'error_id' (string|null)
     */
    public static function novaSenha(string $token, string $senha): array {
        try {
            $usuario = UsuarioModel::where('token', $token)->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Token inválido ou expirado.'];
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $usuario->update([
                'token' => null,
                'senha' => $senhaHash
            ]);

            return ['status' => 'success', 'message' => 'Senha atualizada com sucesso.'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Registra o login do usuário no log.
     *
     * @param string $idUsuario UUID do usuário.
     *
     * @return array
     */
    public static function registrarLogin(string $idUsuario): array {
        try {
            UsuarioLogModel::create([
                'usuario_id' => $idUsuario
            ]);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno no servidor.',
                'error_id' => $errorId
            ];
        }
    }
}
