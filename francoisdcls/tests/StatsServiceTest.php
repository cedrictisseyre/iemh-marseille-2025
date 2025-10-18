<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class StatsServiceTest extends TestCase
{
    public function testStatsEndpointReturnsExpectedKeys()
    {
        $url = 'http://127.0.0.1:8000/services/stats_globales.php';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch stats endpoint');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('nb_pilotes', $data);
        $this->assertArrayHasKey('nb_ecuries', $data);
        $this->assertArrayHasKey('nb_championnats', $data);
        $this->assertArrayHasKey('nb_participations', $data);
        $this->assertIsNumeric($data['nb_pilotes']);
    }
}
