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
    // In development (built-in server) enable full error reporting to help debugging
    $isDevServer = (PHP_SAPI === 'cli-server') || (getenv('FRANCOIS_DEBUG') === '1');
    if ($isDevServer) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
    }
    // CSP minimal (peut être renforcé selon les assets)
    $csp = "default-src 'self'; ";
    $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval'; ";
    $csp .= "img-src 'self' data:; ";
    $csp .= "style-src 'self' 'unsafe-inline';";
    header('Content-Security-Policy: ' . $csp);
}

// Define a project-wide base path. Read from environment if provided to allow
// flexible docroot choices. Default is '/francoisdcls' to preserve existing URLs
// when serving from repo root. Use base_path() helper to access in PHP templates.
if (!defined('FRANCOIS_BASE_PATH')) {
    $envBase = getenv('FRANCOIS_BASE_PATH') ?: null;
    if ($envBase) {
        $base = rtrim($envBase, '/');
    } else {
        // Try to auto-detect a reasonable base path so the app works both
        // when served from the repository root and when the document root
        // is the `francoisdcls/` directory.
        // If the current request script path contains the folder name,
        // assume the app is mounted under that segment (common in dev).
        $detected = '';
        $script = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['REQUEST_URI'] ?? '');
        // look for a literal '/francoisdcls' segment in the request path
        if (strpos($script, '/francoisdcls/') !== false || preg_match('#/francoisdcls($|/)#', $script)) {
            $detected = '/francoisdcls';
        }
        // default to empty (root) if nothing detected
        $base = $detected;
    }
    define('FRANCOIS_BASE_PATH', $base);
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $p = FRANCOIS_BASE_PATH;
        if ($path === '' || $path === '/') {
            return $p;
        }
        return $p . '/' . ltrim($path, '/');
    }
}

// Ensure the canonical database connection is loaded for the site. All pages
// under francoisdcls should use this single source of truth: database/bdd_formule1.php
// which exposes a $pdo variable. We expose a helper get_pdo() to access it.
try {
    // Use require_once so multiple includes are safe.
    require_once __DIR__ . '/../database/bdd_formule1.php';
} catch (Throwable $e) {
    // Don't fatal at include time; pages can decide how to handle missing DB.
}

if (!function_exists('get_pdo')) {
    /**
     * Return the shared PDO instance or null if unavailable.
     * @return \PDO|null
     */
    function get_pdo(): ?\PDO
    {
        global $pdo;
        if (isset($pdo) && $pdo instanceof \PDO) {
            return $pdo;
        }
        return null;
    }
}

// Helper simple pour les flash messages (utilisent $_SESSION)
// Les helpers de flash existent dans includes/flash.php pour éviter les duplications
