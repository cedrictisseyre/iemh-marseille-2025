<?php
declare(strict_types=1);

/**
 * services/helpers.php
 * Fonctions utilitaires : CSRF simple, logging applicatif.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Retourne le token CSRF de session (génère si absent)
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Valide un token CSRF
 */
function validate_csrf(?string $token): bool
{
    if (!is_string($token) || $token === '') {
        return false;
    }
    if (empty($_SESSION['_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Logger applicatif basique (logs/app.log)
 */
function app_log(string $message): void
{
    $logFile = __DIR__ . '/../logs/app.log';
    if (!is_dir(dirname($logFile))) {
        @mkdir(dirname($logFile), 0750, true);
    }
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, $logFile);
}
