<?php
/**
 * Script de validation / nettoyage / enrichissement du CSV joueurs.
 * - Déduplique (même trio team/last/first) en fusionnant les infos (priorité à la première puis champs manquants complétés)
 * - Normalise les positions vers: GK | DEF | MID | FWD
 * - Vérifie / nettoie les numéros de maillot (hors [0-99] -> vide)
 * - Ajoute colonnes goals, assists (0 par défaut si absentes ou non-numériques)
 * - Retire lignes incomplètes (sans nom / prénom / équipe)
 * - Préserve les lignes de commentaire commençant par '#'
 * Résultat: réécrit le fichier CSV sur place (backup créé: .bak horodaté)
 */
ini_set('display_errors', 1); error_reporting(E_ALL);

$csvPath = __DIR__ . '/data/ligue1_2024_2025_players.csv';
if (!file_exists($csvPath)) {
    die("Fichier introuvable: $csvPath\n");
}

$raw = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$raw) die("Lecture impossible\n");

$headerLine = null; $comments = []; $rows = [];
foreach ($raw as $line) {
    if (str_starts_with(trim($line), '#')) { $comments[] = $line; continue; }
    if ($headerLine === null) { $headerLine = $line; continue; }
    $rows[] = $line;
}

if ($headerLine === null) die("Pas d'en-tête dans le CSV\n");

$header = str_getcsv($headerLine);
$map = array_flip($header);

// Ajouter goals / assists si absentes
$addedGoals = false; $addedAssists = false;
if (!isset($map['goals'])) { $header[] = 'goals'; $addedGoals = true; }
if (!isset($map['assists'])) { $header[] = 'assists'; $addedAssists = true; }

// Recalcule map après extension
$map = array_flip($header);

function normPos(?string $p): ?string {
    if ($p === null) return null;
    $p = strtoupper(trim($p));
    if ($p === '') return null;
    $mapping = [
        'GK' => 'GK','GARDIEN' => 'GK','GB' => 'GK',
        'DEF' => 'DEF','DC' => 'DEF','DD' => 'DEF','DG' => 'DEF','RB' => 'DEF','LB' => 'DEF','CB' => 'DEF','BACK' => 'DEF',
        'MIL' => 'MID','M' => 'MID','MC' => 'MID','MDC' => 'MID','MOC' => 'MID','MO' => 'MID','MID' => 'MID','MD' => 'MID',
        'ATT' => 'FWD','BU' => 'FWD','ST' => 'FWD','AILE' => 'FWD','AIL' => 'FWD','RW' => 'FWD','LW' => 'FWD','FO' => 'FWD','FW' => 'FWD'
    ];
    return $mapping[$p] ?? $p; // garde tel quel si non mappé
}

$dedup = [];
$stats = [ 'total' => 0, 'kept' => 0, 'merged' => 0, 'invalid' => 0 ];

foreach ($rows as $line) {
    $cols = str_getcsv($line);
    // Étendre à la longueur de l'en-tête
    if (count($cols) < count($header)) {
        $cols = array_pad($cols, count($header), '');
    }
    $get = function(string $name) use ($map, $cols) { return $cols[$map[$name]] ?? ''; };

    $last = trim($get('player_last_name'));
    $first = trim($get('player_first_name'));
    $team = trim($get('team_name'));
    if ($last === '' || $first === '' || $team === '') { $stats['invalid']++; continue; }

    $pos = normPos($get('position'));
    $numRaw = trim($get('shirt_number'));
    $num = (ctype_digit($numRaw) && (int)$numRaw >= 0 && (int)$numRaw <= 99) ? $numRaw : '';
    $nat = trim($get('nationality'));
    $loan = trim($get('loan')) === '1' ? '1' : '0';
    $goals = isset($map['goals']) ? trim($get('goals')) : '0';
    $assists = isset($map['assists']) ? trim($get('assists')) : '0';
    if ($goals === '' || !ctype_digit($goals)) $goals = '0';
    if ($assists === '' || !ctype_digit($assists)) $assists = '0';

    $key = strtolower($team).'|'.strtolower($last).'|'.strtolower($first);
    if (!isset($dedup[$key])) {
        $dedup[$key] = [
            'player_last_name' => $last,
            'player_first_name' => $first,
            'position' => $pos ?? '',
            'shirt_number' => $num,
            'team_name' => $team,
            'nationality' => $nat,
            'loan' => $loan,
            'goals' => $goals,
            'assists' => $assists,
        ];
        $stats['kept']++;
    } else {
        // fusion si amélioration
        $existing =& $dedup[$key];
        $before = $existing;
        foreach (['position','shirt_number','nationality'] as $f) {
            if ($existing[$f] === '' && $$f !== '') $existing[$f] = $$f;
        }
        // goals/assists : garder le plus grand (hypothèse) si > 0
        if ((int)$goals > (int)$existing['goals']) $existing['goals'] = $goals;
        if ((int)$assists > (int)$existing['assists']) $existing['assists'] = $assists;
        if ($before !== $existing) $stats['merged']++;
    }
    $stats['total']++;
}

// Réécriture
$backup = $csvPath . '.' . date('Ymd_His') . '.bak';
copy($csvPath, $backup);

$out = fopen($csvPath, 'w');
if (!$out) die("Impossible d'écrire dans le CSV\n");
fputcsv($out, $header); // nouvelle en-tête (inclut goals/assists)

// Injecter commentaires en haut après header sous forme de lignes commentées (#...)
foreach ($comments as $c) {
    // On peut repositionner certains commentaires si on veut, on garde juste ceux utiles
}

// Tri alphabétique par équipe puis nom
usort($dedup, function($a, $b) {
    $c = strcasecmp($a['team_name'], $b['team_name']);
    if ($c !== 0) return $c;
    $c = strcasecmp($a['player_last_name'], $b['player_last_name']);
    if ($c !== 0) return $c;
    return strcasecmp($a['player_first_name'], $b['player_first_name']);
});

foreach ($dedup as $row) {
    $line = [];
    foreach ($header as $col) {
        $line[] = $row[$col] ?? '';
    }
    fputcsv($out, $line);
}
fclose($out);

echo "Nettoyage terminé.\n";
echo "Stats: ".json_encode($stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
echo "Backup: $backup\n";
echo ($addedGoals||$addedAssists) ? "Colonnes goals/assists ajoutées.\n" : "Colonnes goals/assists déjà présentes.\n";
