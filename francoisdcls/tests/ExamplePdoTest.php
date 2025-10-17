<?php
namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class ExamplePdoTest extends TestCase
{
    public function testExamplePdoOutputsCount()
    {
        // The bootstrap creates a SQLite test DB and provides a PDO instance
        // We'll include the example script and capture its output
        ob_start();
        include __DIR__ . '/../database/example_pdo_usage.php';
        $out = ob_get_clean();
        $this->assertIsString($out);
        $this->assertStringContainsString('Pilotes count', $out);
    }
}
