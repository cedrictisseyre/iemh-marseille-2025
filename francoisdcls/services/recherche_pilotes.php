<?php

header('Content-Type: application/json');
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
require_once __DIR__ . '/../database/bdd_formule1.php';
$pdoLocal = get_pdo();
if (!$pdoLocal) {
    http_response_code(500);
    echo json_encode(['error' => 'Database unavailable']);
    exit;
}

// use $pdoLocal below for DB operations
$type = isset($_GET['type']) ? $_GET['type'] : 'both'; // 'pilote' | 'ecurie' | 'both'
$annee = isset($_GET['annee']) && is_numeric($_GET['annee']) ? (int)$_GET['annee'] : null;

if ($q === '') {
    echo json_encode([]);
    exit;
}

$results = [];

// Search pilots
if ($type === 'pilote' || $type === 'both') {
    // Keep SQL lines short to satisfy PHPCS line-length rules
    $sql = "SELECT pilote_id, nom, prenom, 'pilote' AS _type "
         . "FROM pilotes WHERE nom LIKE ? OR prenom LIKE ? "
         . "ORDER BY nom, prenom LIMIT 20";
    $stmt = $pdoLocal->prepare($sql);
    $stmt->execute(["%$q%", "%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $r['type'] = 'pilote';
        $results[] = $r;
    }
}

// Search teams (ecuries)
if ($type === 'ecurie' || $type === 'both') {
    // schema.sql defines the column as `nom_ecuries` and `siege`
    // Build a shorter SQL string to avoid very long lines (PHPCS warning)
    $sql = "SELECT ecurie_id, nom_ecuries AS ecurie_nom, "
        . "siege AS ecurie_siege, 'ecurie' AS _type "
        . "FROM ecuries WHERE nom_ecuries LIKE ? ORDER BY nom_ecuries LIMIT 20";
    $stmt = $pdoLocal->prepare($sql);
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
    $stmtp = $pdoLocal->prepare($sqlp);
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
        $results = array_filter($results, function ($r) {
            return $r['type'] !== 'pilote';
        });
    }
}

echo json_encode(array_values($results));
