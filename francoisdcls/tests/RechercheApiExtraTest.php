<?php
namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class RechercheApiExtraTest extends TestCase
{
    public function testEcurieSearchReturnsEcurie()
    {
        $url = 'http://127.0.0.1:8000/services/recherche_pilotes.php?q=Test&type=ecurie';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch recherche endpoint (ecurie)');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, 'Expected at least one ecurie match for "Test"');
        $first = $data[0];
        $this->assertArrayHasKey('type', $first);
        $this->assertEquals('ecurie', $first['type']);
        $this->assertArrayHasKey('ecurie_nom', $first);
    }

    public function testPiloteSearchFilteredByYear()
    {
        // We seeded Sebastian Vettel in 2022 (pilote id 3)
        $url = 'http://127.0.0.1:8000/services/recherche_pilotes.php?q=Vettel&type=pilote&annee=2022';
        $opts = ['http' => ['timeout' => 2]];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        $this->assertNotFalse($body, 'Cannot fetch recherche endpoint (pilote+annee)');
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, 'Expected Vettel to appear for annee=2022');
        $first = $data[0];
        $this->assertArrayHasKey('pilote_id', $first);
        $this->assertArrayHasKey('nom', $first);
        $this->assertEquals('Vettel', $first['nom']);
    }
}
