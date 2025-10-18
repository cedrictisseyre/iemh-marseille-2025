<?php

// Helpers pour gérer les URLs de photo et le cache local
// usage: require_once __DIR__ . '/photo_helper.php';

function resolve_photo_url(?string $photo): ?string
{
    if (empty($photo) || $photo === '0') {
        return null;
    }
  // si la valeur contient imgurl= (Google imgres), extraire
    if (strpos($photo, 'imgurl=') !== false) {
        $parts = parse_url($photo);
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (!empty($q['imgurl'])) {
                return urldecode($q['imgurl']);
            }
        }
    }
  // si déjà URL directe
    if (preg_match('#^https?://#i', $photo)) {
        return $photo;
    }
    return null;
}

function sanitize_filename(string $s): string
{
  // keep alnum, dash, underscore
    $s = preg_replace('/[^A-Za-z0-9._-]/', '_', $s);
    return substr($s, 0, 200);
}

/**
 * Retourne le chemin relatif vers le cache local de l'image si disponible ou le télécharge.
 * Paramètres de base: timeout 5s, max size 2MB
 * Renvoie null si l'image n'a pas pu être récupérée.
 */
function cached_image_url(string $remoteUrl): ?string
{
    $cacheDir = __DIR__ . '/../assets/photos_cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    $hash = hash('sha256', $remoteUrl);
    $ext = pathinfo(parse_url($remoteUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
    $ext = $ext ? '.' . preg_replace('/[^A-Za-z0-9]/', '', $ext) : '.jpg';
    $filename = $hash . $ext;
    $localPath = $cacheDir . '/' . $filename;
  // Public path uses the configured base path to remain portable
  $publicPath = base_path('assets/photos_cache/' . $filename);

  // si déjà en cache et non vide, renvoyer
    if (file_exists($localPath) && filesize($localPath) > 100) {
        return $publicPath;
    }

  // télécharger avec stream context, timeout
    $opts = [
    'http' => [
      'timeout' => 6,
      'method' => 'GET',
      'header' => "User-Agent: iemh-photo-fetcher/1.0\r\n"
    ],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ];
    $context = stream_context_create($opts);
  // limiter la taille en lecture
    $temp = @fopen($remoteUrl, 'rb', false, $context);
    if (!$temp) {
        return null;
    }
    $maxBytes = 2 * 1024 * 1024; // 2MB
    $data = '';
    $read = 0;
    while (!feof($temp) && $read < $maxBytes) {
        $chunk = fread($temp, 8192);
        if ($chunk === false) {
            break;
        }
        $data .= $chunk;
        $read += strlen($chunk);
    }
    fclose($temp);
    if (empty($data) || strlen($data) < 100) {
        return null;
    }

  // simple validation MIME en regardant le header des bytes
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($data);
    if (strpos($mime, 'image/') !== 0) {
        return null;
    }

  // écrire atomiquement
    $tmpFile = $localPath . '.tmp';
    if (@file_put_contents($tmpFile, $data) === false) {
        return null;
    }
    @rename($tmpFile, $localPath);
    @chmod($localPath, 0644);
    return $publicPath;
}
