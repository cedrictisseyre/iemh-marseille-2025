<?php

namespace Francoisdcls\Tests;

use PHPUnit\Framework\TestCase;

class ApiIntegrationTest extends TestCase
{
    private static int $serverPid;
    // try to start test server on a free port (try 8081..8090)
    private static string $baseUrl = '';

    public static function setUpBeforeClass(): void
    {
    // Use the repository root as docroot so requests to /francoisdcls/... resolve
        $repoRoot = realpath(__DIR__ . '/../../');
        $docRoot = escapeshellarg($repoRoot ?: getcwd());
        $started = false;
        $startPort = 8081;
        $endPort = 8090;
        for ($port = $startPort; $port <= $endPort; $port++) {
            $cmd = sprintf("php -S 127.0.0.1:%d -t %s > /tmp/php-server-%d.log 2>&1 & echo $!", $port, $docRoot, $port);
            $pid = trim(shell_exec($cmd));
            if ($pid === '') {
                // try next port
                continue;
            }

            // wait briefly for server to start listening on the port
            $bound = false;
            for ($i = 0; $i < 30; $i++) {
                $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
                if ($fp) {
                    fclose($fp);
                    $bound = true;
                    break;
                }
                usleep(100000);
            }

            if (! $bound) {
                // kill the process we started since it didn't bind
                @posix_kill((int)$pid, 9);
                continue;
            }

            // perform an HTTP GET for a known page to ensure the server serves the expected docroot
            $baseUrl = 'http://127.0.0.1:' . $port;
            $testUrl = $baseUrl . '/francoisdcls/site_f1.php';
            $ctx = stream_context_create(['http' => ['timeout' => 1]]);
            $page = @file_get_contents($testUrl, false, $ctx);
            $httpOk = false;
            if ($page !== false && isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $hdr) {
                    if (preg_match('#^HTTP/\d+\.\d+\s+200#', $hdr)) {
                        $httpOk = true;
                        break;
                    }
                }
            }

            // also check for an expected marker in the page to ensure correct site
            $hasMarker = is_string($page) && (strpos($page, 'Projet IEMH Marseille 2025') !== false || strpos($page, 'Projet IEMH') !== false);

            if ($httpOk && $hasMarker) {
                self::$serverPid = (int)$pid;
                self::$baseUrl = $baseUrl;
                // write debug info
                @file_put_contents('/tmp/test_server_info.txt', json_encode(['pid' => self::$serverPid, 'baseUrl' => self::$baseUrl]) . "\n");
                $started = true;
                break;
            }

            // not the right server; kill and try next port
            @posix_kill((int)$pid, 9);
        }
        if (! $started) {
            throw new \RuntimeException('Failed to start PHP built-in server on ports ' . $startPort . '-' . $endPort);
        }
        // small wait for server to boot fully
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
        $url = self::$baseUrl . '/francoisdcls/services/api_ajout_pilote.php';
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
        $pageUrl = self::$baseUrl . '/francoisdcls/pages/ajout_pilote.php';
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

        $url = self::$baseUrl . '/francoisdcls/services/api_ajout_pilote.php';
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
