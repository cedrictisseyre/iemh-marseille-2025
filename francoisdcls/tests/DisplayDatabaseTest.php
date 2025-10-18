<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DisplayDatabaseTest extends TestCase
{
    public function testPilotesDisplayedMatchDatabase(): void
    {
        // The tests bootstrap creates a SQLite test DB and exposes $pdo globally
        global $pdo;
        $this->assertNotNull($pdo, 'Test DB PDO should be available from bootstrap');

        // Read pilots from DB
        $stmt = $pdo->query('SELECT prenom, nom FROM pilotes ORDER BY pilote_id');
        $dbPilotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertIsArray($dbPilotes);
        $this->assertNotEmpty($dbPilotes, 'There should be at least one pilote in the test DB');

        // Capture the output of the page include (builds HTML using the same $pdo)
        $page = __DIR__ . '/../pages/liste_pilotes.php';
        $this->assertFileExists($page, 'liste_pilotes.php must exist');

        $level = ob_get_level();
        ob_start();
        try {
            // include page in same process so it uses the bootstrap $pdo
            include $page;
            if (ob_get_level() > $level) {
                $html = ob_get_clean() ?: '';
            } else {
                $html = ob_get_contents() ?: '';
                @ob_end_clean();
            }
        } finally {
            // Ensure we restore buffer level fully
            while (ob_get_level() > $level) {
                @ob_end_clean();
            }
        }
        $this->assertNotEmpty($html, 'The page should render some HTML');

        // Extract displayed last names from <span class="pantheon-nom">...'</span>
        preg_match_all('/<span[^>]*class="pantheon-nom"[^>]*>([^<]+)<\/span>/', $html, $m);
        $displayedLastNames = array_map('trim', $m[1] ?? []);

        // Build expected last name list from DB
        $expectedLastNames = array_map(function ($r) { return trim((string)$r['nom']); }, $dbPilotes);

        // The sets should be equal (order may vary); compare as sorted arrays
        sort($expectedLastNames);
        sort($displayedLastNames);

        $this->assertEquals($expectedLastNames, $displayedLastNames, 'Displayed pilotes should match DB pilotes');
    }

    public function testNoExternalSampleDataPresent(): void
    {
        global $pdo;
        // Read pilots from DB for reference
        $stmt = $pdo->query('SELECT prenom, nom FROM pilotes ORDER BY pilote_id');
        $dbPilotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Render page HTML
        $level2 = ob_get_level();
        ob_start();
        try {
            include __DIR__ . '/../pages/liste_pilotes.php';
            if (ob_get_level() > $level2) {
                $html = ob_get_clean() ?: '';
            } else {
                $html = ob_get_contents() ?: '';
                @ob_end_clean();
            }
        } finally {
            while (ob_get_level() > $level2) {
                @ob_end_clean();
            }
        }

        // Ensure each DB full name appears in HTML and there are no extra names shown
        foreach ($dbPilotes as $r) {
            $fullname = trim($r['prenom'] . ' ' . $r['nom']);
            $this->assertStringContainsString($fullname, $html, "Fullname $fullname should be present in page HTML");
        }

        // As an extra sanity: ensure no marker of another example DB like 'Echantillon' is present
        $this->assertStringNotContainsString('Echantillon', $html);
        $this->assertStringNotContainsString('sample', strtolower($html));
    }
}
