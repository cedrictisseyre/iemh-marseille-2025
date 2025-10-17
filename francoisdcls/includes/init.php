<?php

// Initialisation centralisée : encapsule session et headers dans une fonction
// pour éviter les effets de bord à l'inclusion (PHPCS warning).

/**
 * Load simple .env file if present. This helper does not execute automatically on include
 * to avoid side-effects during static analysis or accidental output.
 * Supports lines like KEY=VALUE and quoted values.
 *
 * @param string|null $path Path to the .env file. If null, uses repository root francoisdcls/.env
 */
function load_dotenv(?string $path = null): void
{
    $envFile = $path ?? __DIR__ . '/../.env';
    if (!file_exists($envFile)) {
        return;
    }
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        list($key, $val) = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // remove surrounding quotes
        $first = substr($val, 0, 1);
        $last = substr($val, -1);
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $val = substr($val, 1, -1);
        }
        putenv("$key=$val");
        $_ENV[$key] = $val;
    }
}

function init_app(): void
{
    // Start output buffering early to avoid "headers already sent" issues
    if (!ini_get('output_buffering')) {
        ob_start();
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // En-têtes de sécurité recommandés
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    // CSP minimal (peut être renforcé selon les assets)
    $csp = "default-src 'self'; ";
    $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval'; ";
    $csp .= "img-src 'self' data:; ";
    $csp .= "style-src 'self' 'unsafe-inline';";
    header('Content-Security-Policy: ' . $csp);
}

// Helper simple pour les flash messages (utilisent $_SESSION)
// Les helpers de flash existent dans includes/flash.php pour éviter les duplications
