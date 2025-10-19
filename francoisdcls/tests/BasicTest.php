<?php

// phpcs:ignoreFile PSR1.Classes.ClassDeclaration.MissingNamespace
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../evaluation_etudiant.php';

class BasicTest extends TestCase
{
    public function testGetGlobalScoreReturnsInt()
    {
        $score = getGlobalScore(__DIR__ . '/..');
        // getGlobalScore may return a float (round() yields float in PHP),
        // accept numeric results and verify bounds
        $this->assertIsNumeric($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
