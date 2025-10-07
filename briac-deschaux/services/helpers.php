<?php
declare(strict_types=1);

/**
 * services/helpers.php
 * Fonctions utilitaires : session, CSRF token, échappement HTML, log.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * e() - échappe pour sortie HTML
 */
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Génère/renvoie le token CSRF stocké en session
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide le token CSRF (retourne true si OK)
 */
function validate_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    // Optionnel : expiration (ex: 30 min)
    $max_age = 30 * 60;
    if ($valid && isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > $max_age) {
        // Expiré
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    return $valid;
}

/**
 * Log simple
 */
function app_log(string $msg): void {
    $log = __DIR__ . '/../logs/app.log';
    if (!is_dir(dirname($log))) @mkdir(dirname($log), 0750, true);
    error_log(date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, 3, $log);
}
