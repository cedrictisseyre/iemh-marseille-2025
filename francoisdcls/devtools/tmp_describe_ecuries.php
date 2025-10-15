<?php

require 'francoisdcls/database/bdd_formule1.php';
try {
    $stmt = $pdo->query('DESCRIBE ecuries');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . "\n";
    }
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
