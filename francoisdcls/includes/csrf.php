<?php

// CSRF helper - place tokens in session and validate incoming POST requests.

function csrf_token(): string
{
    if (!isset($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return "<input type=\"hidden\" name=\"_csrf\" value=\"{$t}\">";
}

function validate_csrf(): bool
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true; // nothing to validate
    }
    if (!isset($_POST['_csrf']) || !isset($_SESSION['_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $_POST['_csrf']);
}
