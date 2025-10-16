<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

class ApiSmokeTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../includes/init.php';
        require_once __DIR__ . '/../includes/csrf.php';
    }

    public function testCsrfTokenPresent(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // ensure token exists
        $t = csrf_token();
        $this->assertNotEmpty($t);
        $this->assertIsString($t);
    }

    // Note: full end-to-end API requests require a running server; here we limit to helper checks
}
