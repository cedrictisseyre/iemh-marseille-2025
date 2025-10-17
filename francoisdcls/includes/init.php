<?php

// Initialisation centralisée : encapsule session et headers dans une fonction
// pour éviter les effets de bord à l'inclusion (PHPCS warning).

// Load .env file if present (simple parser, supports KEY=VALUE and quoted values)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (!strpos($line, '=')) {
            continue;
        }
        list($key, $val) = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // remove surrounding quotes
        if ((substr($val, 0, 1) === '"' && substr($val, -1) === '"') || (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
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
