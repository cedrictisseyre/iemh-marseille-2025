<?php

// Initialisation centralisée : encapsule session et headers dans une fonction
// pour éviter les effets de bord à l'inclusion (PHPCS warning).

function init_app(): void
{
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
function set_flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['_flash'])) {
        return null;
    }
    $f = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return $f;
}
