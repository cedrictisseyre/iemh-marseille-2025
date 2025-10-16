<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'both'; // 'pilote' | 'ecurie' | 'both'
$annee = isset($_GET['annee']) && is_numeric($_GET['annee']) ? (int)$_GET['annee'] : null;

if ($q === '') {
    echo json_encode([]);
    exit;
}

$results = [];

// Search pilots
if ($type === 'pilote' || $type === 'both') {
    $sql = "SELECT pilote_id, nom, prenom, 'pilote' AS _type FROM pilotes WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom, prenom LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$q%", "%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $r['type'] = 'pilote';
        $results[] = $r;
    }
}

// Search teams (ecuries)
if ($type === 'ecurie' || $type === 'both') {
    $sql = "SELECT ecurie_id, nom AS ecurie_nom, pays AS ecurie_pays, 'ecurie' AS _type FROM ecuries WHERE nom LIKE ? ORDER BY nom LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $r['type'] = 'ecurie';
        $results[] = $r;
    }
}

// Optional: filter pilots by year if provided (simple post-filter using participations)
if ($annee !== null && ($type === 'pilote' || $type === 'both')) {
    // Keep only pilots who have a participation in that year
    $filtered = [];
    $sqlp = "SELECT pilote_id FROM participations WHERE annee = ?";
    $stmtp = $pdo->prepare($sqlp);
    $stmtp->execute([$annee]);
    $pids = $stmtp->fetchAll(PDO::FETCH_COLUMN, 0);
    if ($pids) {
        foreach ($results as $r) {
            if (isset($r['pilote_id']) && in_array($r['pilote_id'], $pids)) {
                $filtered[] = $r;
            } elseif ($r['type'] !== 'pilote') {
                $filtered[] = $r; // keep ecuries
            }
        }
        $results = $filtered;
    } else {
        // No participations this year -> empty
        $results = array_filter($results, function ($r) { return $r['type'] !== 'pilote'; });
    }
}

echo json_encode(array_values($results));
