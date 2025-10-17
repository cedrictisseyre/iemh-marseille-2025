<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class RechercheApiTest extends TestCase
{
    public function testRechercheEndpointReturnsJson()
    {
    // Server docroot is francoisdcls/, so endpoint is under /services
        $url = 'http://127.0.0.1:8000/services/recherche_pilotes.php?q=Vettel';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch recherche endpoint');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('pilote_id', $first);
            $this->assertArrayHasKey('nom', $first);
            $this->assertArrayHasKey('prenom', $first);
        }
    }
}
