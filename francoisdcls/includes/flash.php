<?php
// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function set_flash($type, $message)
{
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
