<?php

require 'francoisdcls/database/bdd_formule1.php';
try {
    $pdo->exec("ALTER TABLE pilotes MODIFY pilote_id INT NOT NULL AUTO_INCREMENT");
    echo "OK\n";
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
