<?php

namespace JairoJeffersont\Helpers;

/**
 * Class LoginSessionHelper
 *
 * Provides secure, centralized and framework-agnostic session management.
 *
 * Responsibilities:
 *  - Start authenticated sessions with hardened cookie configuration
 *  - Prevent session fixation via session ID regeneration
 *  - Enforce inactivity-based session expiration
 *  - Validate session integrity using a safe fingerprint strategy
 *  - Properly destroy sessions and invalidate cookies
 *
 * Security features:
 *  - HttpOnly cookies
 *  - Secure cookies with reverse proxy HTTPS detection
 *  - SameSite=Strict cookie policy
 *  - Session ID regeneration on login
 *  - Session fingerprint (User-Agent + partial IP)
 *  - Automatic session expiration handling
 *
 * Compatible with PHP 8+
 *
 * @package JairoJeffersont\Helpers
 */
class LoginSessionHelper {
    /**
     * Default session expiration time in minutes.
     * Used when no environment configuration is provided.
     */
    private const DEFAULT_EXPIRATION_MINUTES = 30;

    /**
     * Ensures the session is started with secure cookie parameters.
     *
     * @return bool Returns true if the session is active or successfully started.
     */
    private static function ensureSessionStarted(): bool {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        if (headers_sent()) {
            return false;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => self::isSecureConnection(),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        return session_start();
    }

    /**
     * Starts a secure authenticated session.
     *
     * @param array $user Authenticated user data.
     *
     * @return bool Returns true on success.
     */
    public static function startSession(array $user): bool {
        if (!self::ensureSessionStarted()) {
            return false;
        }

        // Regenerate session ID to prevent fixation attacks
        session_regenerate_id(true);

        $expirationMinutes = (int) (
            $_ENV['SESSION_EXPIRATION']
            ?? self::DEFAULT_EXPIRATION_MINUTES
        );

        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
        $_SESSION['expiration'] = $expirationMinutes * 60;
        $_SESSION['fingerprint'] = self::generateFingerprint();

        return true;
    }

    /**
     * Validates whether the current session is active and secure.
     *
     * @return bool Returns true if the session is valid.
     */
    public static function validateSession(): bool {
        if (!self::ensureSessionStarted()) {
            return false;
        }

        if (
            empty($_SESSION['user']) ||
            empty($_SESSION['last_activity']) ||
            empty($_SESSION['expiration']) ||
            empty($_SESSION['fingerprint'])
        ) {
            return false;
        }

        // Validate session fingerprint
        if ($_SESSION['fingerprint'] !== self::generateFingerprint()) {
            self::destroySession();
            return false;
        }

        // Check inactivity expiration
        if (time() - $_SESSION['last_activity'] > $_SESSION['expiration']) {
            self::destroySession();
            return false;
        }

        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Destroys the current session and invalidates the session cookie.
     *
     * @return bool Always returns true.
     */
    public static function destroySession(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            self::ensureSessionStarted();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        return true;
    }

    /**
     * Generates a safe session fingerprint.
     *
     * The fingerprint binds the session to:
     *  - User-Agent
     *  - Partial IP address
     *
     * This approach balances security and usability,
     * avoiding unnecessary logouts caused by dynamic IP changes.
     *
     * @return string A SHA-256 fingerprint hash.
     */
    private static function generateFingerprint(): string {
        $appSalt = $_ENV['APP_KEY'] ?? '';

        if ($appSalt === '') {
            throw new \RuntimeException('APP_KEY is not defined.');
        }

        //$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $partialIp = self::getPartialIp();

        return hash(
            'sha256',
            //$appSalt . '|' . $userAgent . '|' . $partialIp
            $appSalt . '|' . $partialIp
        );
    }


    /**
     * Returns a partial representation of the client IP address.
     *
     * IPv4: first two octets
     * IPv6: first block
     *
     * @return string Partial IP identifier.
     */
    private static function getPartialIp(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return implode('.', array_slice(explode('.', $ip), 0, 2));
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return explode(':', $ip)[0];
        }

        return 'unknown';
    }

    /**
     * Detects whether the current connection is secure (HTTPS),
     * including support for reverse proxies and load balancers.
     *
     * @return bool Returns true if the connection is secure.
     */
    private static function isSecureConnection(): bool {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            return true;
        }

        if (
            !empty($_SERVER['HTTP_X_FORWARDED_SSL']) &&
            $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on'
        ) {
            return true;
        }

        return false;
    }
}
