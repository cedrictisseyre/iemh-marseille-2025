<?php
// Initialisation commune à inclure en haut des pages et des services.
// - démarre la session si nécessaire
// - définit en-têtes de sécurité basiques

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// En-têtes de sécurité recommandés
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
// CSP minimal (peut être renforcé selon les assets)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self' data:; style-src 'self' 'unsafe-inline';");

// Helper simple pour les flash messages
function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['_flash'])) {
        return null;
    }
    $f = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return $f;
}

?>
