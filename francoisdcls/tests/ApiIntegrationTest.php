<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

class ApiIntegrationTest extends TestCase
{
    private static int $serverPid;
    private const BASE_URL = 'http://127.0.0.1:8081';

    public static function setUpBeforeClass(): void
    {
        $docRoot = escapeshellarg(getcwd());
        $cmd = "php -S 127.0.0.1:8081 -t $docRoot > /tmp/php-server.log 2>&1 & echo $!";
        $pid = trim(shell_exec($cmd));
        if ($pid === '') {
            throw new \RuntimeException('Failed to start PHP built-in server');
        }
        self::$serverPid = (int) $pid;
        // small wait for server to boot
        usleep(200000);
    }

    public static function tearDownAfterClass(): void
    {
        if (!empty(self::$serverPid)) {
            @posix_kill(self::$serverPid, 9);
        }
    }

    public function testApiAjoutPiloteRejectsWithoutCsrfHeader(): void
    {
    $url = self::BASE_URL . '/francoisdcls/services/api_ajout_pilote.php';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['prenom' => 'E2E', 'nom' => 'Test']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(403, $code, 'Endpoint should reject requests without CSRF token');
    }

    public function testApiAjoutPiloteAcceptsWithCsrfHeader(): void
    {
    $pageUrl = self::BASE_URL . '/francoisdcls/pages/ajout_pilote.php';
        $ch = curl_init($pageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/e2e_cookiejar.txt');
        $resp = curl_exec($ch);

        if (preg_match("/window\.CSRF_TOKEN\s*=\s*'([^']+)'/", $resp, $m)) {
            $token = $m[1];
        } else {
            $this->fail('CSRF token not found on page');
            return;
        }

    $url = self::BASE_URL . '/francoisdcls/services/api_ajout_pilote.php';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['prenom' => 'E2E', 'nom' => 'Accept']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-CSRF-Token: $token"]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/e2e_cookiejar.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertNotEquals(
            403,
            $code,
            'Endpoint should not reject requests with valid CSRF token'
        );
        $this->assertStringNotContainsString(
            'Jeton CSRF invalide',
            $resp,
            'Response body should not report an invalid CSRF token'
        );
    }
}
