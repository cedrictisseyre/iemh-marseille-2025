<?php
require __DIR__ . '/ando-guerin/connexion.php';
try {
    echo "COLUMNS:\n";
    $cols = $conn->query('SHOW COLUMNS FROM emploi_temps')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . "\t" . $c['Type'] . "\t" . $c['Null'] . "\t" . $c['Key'] . "\n";
    }
    echo "\nINDEXES:\n";
    $idx = $conn->query('SHOW INDEX FROM emploi_temps')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($idx as $i) {
        echo $i['Key_name'] . "\t" . $i['Column_name'] . "\t" . $i['Seq_in_index'] . "\t" . $i['Non_unique'] . "\n";
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
