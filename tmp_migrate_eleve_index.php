<?php
require __DIR__ . '/ando-guerin/connexion.php';
try {
    // vérifier colonne eleve_id
    $col = $conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'emploi_temps' AND COLUMN_NAME = 'eleve_id'");
    $col->execute([':db' => 'Andoni_guerin']);
    $exists = $col->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    if (!$exists) {
        echo "Ajout de la colonne eleve_id...\n";
        $conn->exec("ALTER TABLE emploi_temps ADD COLUMN eleve_id INT NULL AFTER id");
    } else {
        echo "Colonne eleve_id déjà présente.\n";
    }

    // vérifier et supprimer uniq_creneau
    $idx = $conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'emploi_temps' AND INDEX_NAME = 'uniq_creneau'");
    $idx->execute([':db' => 'Andoni_guerin']);
    if ($idx->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
        echo "Suppression de l'index uniq_creneau...\n";
        $conn->exec("ALTER TABLE emploi_temps DROP INDEX uniq_creneau");
    } else {
        echo "Index uniq_creneau absent.\n";
    }

    // supprimer l'index existant sur week/jour/horaire s'il existe
    $idx2 = $conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'emploi_temps' AND INDEX_NAME = 'idx_emploi_week_jour_horaire'");
    $idx2->execute([':db' => 'Andoni_guerin']);
    if ($idx2->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
        echo "Suppression de l'index idx_emploi_week_jour_horaire...\n";
        $conn->exec("ALTER TABLE emploi_temps DROP INDEX idx_emploi_week_jour_horaire");
    } else {
        echo "Index idx_emploi_week_jour_horaire absent.\n";
    }

    // créer l'index unique par élève
    $idx3 = $conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'emploi_temps' AND INDEX_NAME = 'uniq_emploi_par_eleve'");
    $idx3->execute([':db' => 'Andoni_guerin']);
    if ($idx3->fetch(PDO::FETCH_ASSOC)['cnt'] == 0) {
        echo "Création de l'index unique uniq_emploi_par_eleve (eleve_id, week_start, jour_id, horaire_id)...\n";
        $conn->exec("CREATE UNIQUE INDEX uniq_emploi_par_eleve ON emploi_temps (eleve_id, week_start, jour_id, horaire_id)");
    } else {
        echo "Index uniq_emploi_par_eleve déjà présent.\n";
    }

    echo "\nSchéma après modifications :\n";
    $cols = $conn->query('SHOW COLUMNS FROM emploi_temps')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . "\t" . $c['Type'] . "\t" . $c['Null'] . "\t" . $c['Key'] . "\n";
    }
    echo "\nINDEXES:\n";
    $idxs = $conn->query('SHOW INDEX FROM emploi_temps')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($idxs as $i) {
        echo $i['Key_name'] . "\t" . $i['Column_name'] . "\t" . $i['Seq_in_index'] . "\t" . $i['Non_unique'] . "\n";
    }

} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
