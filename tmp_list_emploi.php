<?php
require __DIR__ . '/ando-guerin/connexion.php';
try {
    $stmt = $conn->query("SELECT * FROM emploi_temps ORDER BY week_start DESC, jour_id, horaire_id LIMIT 20");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
