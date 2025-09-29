<?php
require_once __DIR__ . '/../bdd_formule1.php';

// Test de connexion explicite
try {
    $base = $pdo->query('SELECT DATABASE()')->fetchColumn();
    if (!$base) {
        echo "Aucune base sélectionnée ou la connexion a échoué.\n";
        exit(1);
    }
    echo "Connexion réussie. Base courante : $base\n";
} catch (PDOException $e) {
    echo "Erreur de connexion PDO : " . $e->getMessage() . "\n";
    exit(1);
}

function affiche($sql, $pdo) {
    echo "\n==== $sql ====";
    try {
        $res = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (empty($res)) {
            echo "\nAucune donnée.";
        } else {
            foreach ($res as $row) {
                echo "\n" . implode(' | ', $row);
            }
        }
    } catch (PDOException $e) {
        echo "\nErreur SQL : " . $e->getMessage();
    }
}

affiche('SHOW TABLES', $pdo);
affiche('SELECT * FROM pilotes', $pdo);
affiche('SELECT * FROM championnats', $pdo);
affiche('SELECT * FROM participations', $pdo);

echo "\n";
