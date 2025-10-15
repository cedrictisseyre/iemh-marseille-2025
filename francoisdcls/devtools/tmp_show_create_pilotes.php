<?php

require 'francoisdcls/database/bdd_formule1.php';
try {
    $row = $pdo->query("SHOW CREATE TABLE pilotes")->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . "\n";
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
