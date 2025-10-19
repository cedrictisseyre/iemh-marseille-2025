<?php
require __DIR__ . '/ando-guerin/connexion.php';
try {
    $jours = $conn->query('SELECT id,nom FROM jours ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    $horaires = $conn->query('SELECT id,debut,fin FROM horaires ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['jours' => $jours, 'horaires' => $horaires], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
