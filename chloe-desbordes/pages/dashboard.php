<?php
include '../connexion.php';
$page = $_GET['page'] ?? 'coureurs';
function nav($active) {
    $tabs = [
        'coureurs' => 'Coureurs',
        'courses' => 'Courses',
        'participations' => 'Participations',
        'points' => 'Points ITRA',
        'nationalites' => 'Nationalités'
    ];
    echo '<nav class="tabs">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a> ";
    }
    echo '</nav>';
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard UTMB</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .tabs { display: flex; justify-content: center; margin-top: 30px; }
        .tabs a { background: #2980b9; color: #fff; padding: 12px 30px; margin: 0 5px; font-size: 1.1em; border-radius: 6px 6px 0 0; text-decoration: none; transition: background 0.2s; }
        .tabs a.active { background: #34495e; }
        .content { background: #fff; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(44,62,80,0.08); margin: 0 auto; max-width: 900px; padding: 30px; }
    </style>
</head>
<body>
    <h1>Dashboard UTMB</h1>
    <?php nav($page); ?>
    <div class="content">
    <?php
    if ($page === 'coureurs') {
        $stmt = $pdo->query('SELECT * FROM coureurs');
        echo '<h2>Liste des coureurs</h2><table><thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Date de naissance</th><th>Club</th></tr></thead><tbody>';
        while ($row = $stmt->fetch()) {
            echo "<tr><td>{$row['id_coureur']}</td><td>{$row['nom']}</td><td>{$row['prenom']}</td><td>{$row['nationalite']}</td><td>{$row['date_naissance']}</td><td>{$row['club']}</td></tr>";
        }
        echo '</tbody></table>';
    }
    elseif ($page === 'courses') {
        $stmt = $pdo->query('SELECT * FROM courses');
        echo '<h2>Liste des courses</h2><table><thead><tr><th>ID</th><th>Nom</th><th>Date</th><th>Lieu</th></tr></thead><tbody>';
        while ($row = $stmt->fetch()) {
            echo "<tr><td>{$row['id_course']}</td><td>{$row['nom']}</td><td>{$row['date']}</td><td>{$row['lieu']}</td></tr>";
        }
        echo '</tbody></table>';
    }
    elseif ($page === 'participations') {
        $stmt = $pdo->query('SELECT * FROM participations');
        echo '<h2>Liste des participations</h2><table><thead><tr><th>ID</th><th>ID Coureur</th><th>ID Course</th><th>Temps</th></tr></thead><tbody>';
        while ($row = $stmt->fetch()) {
            echo "<tr><td>{$row['id_participation']}</td><td>{$row['id_coureur']}</td><td>{$row['id_course']}</td><td>{$row['temps']}</td></tr>";
        }
        echo '</tbody></table>';
    }
    elseif ($page === 'points') {
        $stmt = $pdo->query('SELECT * FROM points');
        echo '<h2>Liste des points ITRA</h2><table><thead><tr><th>ID</th><th>ID Coureur</th><th>Points</th></tr></thead><tbody>';
        while ($row = $stmt->fetch()) {
            echo "<tr><td>{$row['id_point']}</td><td>{$row['id_coureur']}</td><td>{$row['points']}</td></tr>";
        }
        echo '</tbody></table>';
    }
    elseif ($page === 'nationalites') {
        $stmt = $pdo->query('SELECT DISTINCT nationalite FROM coureurs');
        echo '<h2>Liste des nationalités</h2><table><thead><tr><th>Nationalité</th></tr></thead><tbody>';
        while ($row = $stmt->fetch()) {
            echo "<tr><td>{$row['nationalite']}</td></tr>";
        }
        echo '</tbody></table>';
    }
    ?>
    </div>
</body>
</html>
