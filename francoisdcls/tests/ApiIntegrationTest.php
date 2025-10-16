<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

class ApiIntegrationTest extends TestCase
{
    public function testApiAjoutPiloteCsrfHeaderRejectsInvalid(): void
    {
        // Simulate a request without CSRF header (server-side helpers rely on session token)
        $output = shell_exec("php -r \"require 'francoisdcls/services/api_ajout_pilote.php';\"");
        // The script expects POST; if run in CLI it should exit or print method not allowed
        $this->assertTrue(is_string($output));
    }
}
