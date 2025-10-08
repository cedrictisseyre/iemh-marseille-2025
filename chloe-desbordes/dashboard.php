


<?php
require_once __DIR__ . '/pages/db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$tab = $_GET['tab'] ?? 'coureurs';
$search = $_GET['search'] ?? '';

function tabNav($active) {
    $tabs = [
        'coureurs' => 'Coureurs',
        'courses' => 'Courses',
        'participations' => 'Participations',
        'points' => 'Points ITRA'
    ];
    echo '<nav class="tabs">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?tab=$key' class='$class'>$label</a> ";
    }
    echo '</nav>';
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard UTMB</title>
    <style>
        body{font-family:Arial;max-width:1000px;margin:20px auto;padding:10px}
        .tabs { display: flex; justify-content: center; margin-top: 30px; }
        .tabs a { background: #2980b9; color: #fff; padding: 12px 30px; margin: 0 5px; font-size: 1.1em; border-radius: 6px 6px 0 0; text-decoration: none; transition: background 0.2s; }
        .tabs a.active { background: #34495e; }
        .content { background: #fff; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(44,62,80,0.08); margin: 0 auto; max-width: 900px; padding: 30px; }
        input[type=text]{padding:6px;width:300px;margin-bottom:1em;}
        table{width:100%;border-collapse:collapse;margin-top:1em}
        th,td{border:1px solid #ddd;padding:6px;text-align:left}
        th{background:#f1f5f9}
    </style>
</head>
<body>
    <h1>Tableau de bord UTMB</h1>
    <?php tabNav($tab); ?>
    <div class="content">
    <form method="get" style="margin-bottom:1em;">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
        <input type="text" name="search" placeholder="Recherche..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Rechercher</button>
    </form>
    <?php
    try {
        if ($tab === 'coureurs') {
            $sql = 'SELECT * FROM coureurs';
            if ($search) $sql .= ' WHERE nom LIKE :search OR prenom LIKE :search OR nationalite LIKE :search OR club LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des coureurs</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Date de naissance</th><th>Club</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_coureur']}</td><td>{$row['nom']}</td><td>{$row['prenom']}</td><td>{$row['nationalite']}</td><td>{$row['date_naissance']}</td><td>{$row['club']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucun coureur trouvé.</p>';
            }
        }
        elseif ($tab === 'courses') {
            $sql = 'SELECT * FROM courses';
            if ($search) $sql .= ' WHERE nom LIKE :search OR lieu LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des courses</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>Nom</th><th>Date</th><th>Lieu</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_course']}</td><td>{$row['nom']}</td><td>{$row['date']}</td><td>{$row['lieu']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucune course trouvée.</p>';
            }
        }
        elseif ($tab === 'participations') {
            $sql = 'SELECT * FROM participations';
            if ($search) $sql .= ' WHERE id_coureur LIKE :search OR id_course LIKE :search OR temps LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des participations</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>ID Coureur</th><th>ID Course</th><th>Temps</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_participation']}</td><td>{$row['id_coureur']}</td><td>{$row['id_course']}</td><td>{$row['temps']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucune participation trouvée.</p>';
            }
        }
        elseif ($tab === 'points') {
            $sql = 'SELECT * FROM points';
            if ($search) $sql .= ' WHERE id_coureur LIKE :search OR points LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des points ITRA</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>ID Coureur</th><th>Points</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_point']}</td><td>{$row['id_coureur']}</td><td>{$row['points']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucun point ITRA trouvé.</p>';
            }
        }
    } catch (PDOException $e) {
        echo '<div style="color:red">Erreur SQL : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
    </div>
</body>
</html>
