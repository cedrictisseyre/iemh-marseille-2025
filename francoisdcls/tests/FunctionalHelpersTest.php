<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

final class FunctionalHelpersTest extends TestCase
{
    public function testRenderEvaluationBadgeProducesString()
    {
        require_once __DIR__ . '/../functional_helpers.php';
        $html = render_evaluation_badge(75);
        $this->assertIsString($html);
        $this->assertStringContainsString('75', $html);
    }

    public function testNationalityLabelAndFormatDate()
    {
        require_once __DIR__ . '/../functional_helpers.php';
        $this->assertEquals('France', nationality_label(1));
    $this->assertEquals('Autre', nationality_label(999));
        $this->assertEquals('01/01/2020', format_date_fr('2020-01-01'));
    }

    public function testRenderPilotCardIncludesName()
    {
        require_once __DIR__ . '/../functional_helpers.php';
        $card = render_pilot_card(['prenom' => 'Test', 'nom' => 'Pilote', 'photo' => 'assets/sample.png', 'nationnalite' => 1]);
        $this->assertIsString($card);
        $this->assertStringContainsString('Test Pilote', $card);
        $this->assertStringContainsString('assets/sample.png', $card);
    }
}
