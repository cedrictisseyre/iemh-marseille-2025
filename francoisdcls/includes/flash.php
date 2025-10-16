<?php
// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

// Ensure the app is initialised early (session + headers). Many pages include
// this file before any HTML output, so calling init_app() here guarantees
// headers are sent before output and avoids "headers already sent" warnings.
if (file_exists(__DIR__ . '/init.php')) {
    require_once __DIR__ . '/init.php';
    if (function_exists('init_app')) {
        init_app();
    }
}

function set_flash($type, $message)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash()
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
