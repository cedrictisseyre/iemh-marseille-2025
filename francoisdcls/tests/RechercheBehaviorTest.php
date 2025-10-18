<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class RechercheBehaviorTest extends TestCase
{
    public function testSearchBothReturnsPiloteAndEcurie()
    {
        $url = 'http://127.0.0.1:8000/services/recherche_pilotes.php?q=Test&type=both';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch recherche endpoint (both)');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        // expect at least one ecurie (Test Team) and possibly pilotes
        $foundEcurie = false;
        foreach ($data as $d) {
            if (isset($d['type']) && $d['type'] === 'ecurie') $foundEcurie = true;
        }
        $this->assertTrue($foundEcurie, 'Expected at least one ecurie in both search');
    }

    public function testSearchPiloteWithYearFilter()
    {
        $url = 'http://127.0.0.1:8000/services/recherche_pilotes.php?q=Vettel&type=pilote&annee=2022';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch recherche endpoint (pilote+annee)');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, 'Expected Vettel to appear for annee=2022');
        $found = false;
        foreach ($data as $d) {
            if (isset($d['nom']) && $d['nom'] === 'Vettel') $found = true;
        }
        $this->assertTrue($found, 'Expected Vettel present in filtered results');
    }
}
