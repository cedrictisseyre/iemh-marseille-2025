<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

class CSRFTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../includes/csrf.php';
    }

    public function testCsrfTokenGenerationAndValidation(): void
    {
        // Ensure session is started (CSRF helper starts session if needed)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear existing token
        unset($_SESSION['_csrf_token']);

        $t1 = csrf_token();
        $this->assertNotEmpty($t1);
        $this->assertIsString($t1);

        // csrf_field returns an input containing the token
        $field = csrf_field();
        $this->assertStringContainsString('_csrf', $field);
        $this->assertStringContainsString($t1, $field);

        // validate_csrf should accept when POST contains the token
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_csrf'] = $t1;
        $this->assertTrue(validate_csrf());

        // Invalid token
        $_POST['_csrf'] = 'badtoken';
        $this->assertFalse(validate_csrf());

        // Non-POST requests bypass validation
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue(validate_csrf());
    }
}
