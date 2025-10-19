<?php
require __DIR__ . '/ando-guerin/connexion.php';
try {
    $stmt = $conn->query('SELECT id, prenom, nom FROM eleves ORDER BY id LIMIT 10');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo $r['id'] . "\t" . $r['prenom'] . ' ' . $r['nom'] . "\n";
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
