


<?php
require_once __DIR__ . '/connexion.php';
if (!isset($pdo) || !$pdo) {
    echo '<div style="color:red;font-weight:bold">Erreur : la connexion à la base de données a échoué. Vérifiez les identifiants et l’accessibilité du serveur.</div>';
    exit;
}
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', 'Roboto', Arial, sans-serif;
            background: linear-gradient(to top, #e0f7fa 0%, #fff 100%);
            max-width: 1100px;
            margin: 0 auto;
            padding: 0;
        }
        .mountain-header {
            width: 100%;
            height: 160px;
            background: linear-gradient(to top, #b3e5fc 0%, #fff 100%);
            position: relative;
            margin-bottom: -40px;
        }
        .mountain-svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 160px;
        }
        h1 {
            text-align: center;
            color: #34495e;
            font-size: 2.5em;
            margin-top: 0;
            letter-spacing: 2px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .tabs a {
            background: #388e3c;
            color: #fff;
            padding: 14px 36px;
            margin: 0 7px;
            font-size: 1.15em;
            border-radius: 12px 12px 0 0;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            font-weight: bold;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .tabs a.active {
            background: #1565c0;
            box-shadow: 0 4px 16px rgba(44,62,80,0.12);
        }
        .content {
            background: #fff;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            margin: 0 auto;
            max-width: 1000px;
            padding: 40px 32px 32px 32px;
        }
        input[type=text]{
            padding:8px;
            width:320px;
            margin-bottom:1em;
            border-radius:6px;
            border:1px solid #b3e5fc;
            font-size:1em;
        }
        button[type=submit]{
            background:#388e3c;
            color:#fff;
            border:none;
            border-radius:6px;
            padding:8px 18px;
            font-size:1em;
            cursor:pointer;
            font-weight:bold;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
            transition:background 0.2s;
        }
        button[type=submit]:hover{
            background:#1565c0;
        }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:1em;
            background:#f9fbe7;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
        }
        th,td{
            border:1px solid #c8e6c9;
            padding:10px;
            text-align:left;
        }
        th{
            background:#b3e5fc;
            color:#1565c0;
            font-size:1.05em;
        }
        tr:nth-child(even){background:#e0f2f1;}
        a[target="_blank"]{
            background:#1565c0;
            color:#fff;
            padding:6px 16px;
            border-radius:6px;
            text-decoration:none;
            font-size:0.95em;
            float:right;
            margin-bottom:10px;
            margin-left:10px;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
        }
        a[target="_blank"]:hover{
            background:#388e3c;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;color:#1565c0;font-size:3em;margin-top:32px;letter-spacing:3px;font-family:'Montserrat',Arial,sans-serif;">UTMB - Ultra-Trail du Mont-Blanc</h1>
    <div class="mountain-header">
        <svg class="mountain-svg" viewBox="0 0 1100 160" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 160L1100 160V80L900 120L800 60L700 130L600 90L500 150L400 100L300 140L200 80L100 120L0 160Z" fill="#388e3c"/>
            <path d="M0 160L1100 160V120L900 140L800 100L700 150L600 130L500 160L400 120L300 150L200 120L100 140L0 160Z" fill="#b3e5fc"/>
        </svg>
    </div>
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
            echo '<a href="pages/liste_coureurs.php" style="float:right;margin-bottom:10px;" target="_blank">Voir la liste complète</a>';
            $sql = 'SELECT * FROM coureurs_UTMB';
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
            echo '<a href="pages/liste_courses.php" style="float:right;margin-bottom:10px;" target="_blank">Voir la liste complète</a>';
            $sql = 'SELECT * FROM courses';
            if ($search) $sql .= ' WHERE nom LIKE :search OR lieu LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des courses</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>Nom</th><th>Distance (km)</th><th>Dénivelé (m)</th><th>Date</th><th>Lieu</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_course']}</td><td>{$row['nom']}</td><td>{$row['distance_km']}</td><td>{$row['denivele_m']}</td><td>{$row['date_course']}</td><td>{$row['lieu']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucune course trouvée.</p>';
            }
        }
        elseif ($tab === 'participations') {
            echo '<a href="pages/liste_participations.php" style="float:right;margin-bottom:10px;" target="_blank">Voir la liste complète</a>';
            $sql = 'SELECT * FROM participation';
            if ($search) $sql .= ' WHERE id_coureur LIKE :search OR id_course LIKE :search OR temps LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des participations</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID Coureur</th><th>ID Course</th><th>Dossard</th><th>Temps final</th><th>Statut</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo "<tr><td>{$row['id_coureur']}</td><td>{$row['id_course']}</td><td>{$row['dossard']}</td><td>{$row['temps_final']}</td><td>{$row['statut']}</td></tr>";
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucune participation trouvée.</p>';
            }
        }
        elseif ($tab === 'points') {
            echo '<a href="pages/liste_points.php" style="float:right;margin-bottom:10px;" target="_blank">Voir la liste complète</a>';
            $sql = 'SELECT * FROM points_ITRA';
            if ($search) $sql .= ' WHERE id_coureur LIKE :search OR points LIKE :search';
            $stmt = $pdo->prepare($sql);
            $params = $search ? [':search' => "%$search%"] : [];
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            echo '<h2>Liste des points ITRA</h2>';
            if (count($rows) > 0) {
                echo '<table><thead><tr><th>ID</th><th>ID Coureur</th><th>Points</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $id = isset($row['id_point']) ? $row['id_point'] : (isset($row['id']) ? $row['id'] : '');
                    $id_coureur = isset($row['id_coureur']) ? $row['id_coureur'] : (isset($row['coureur_id']) ? $row['coureur_id'] : '');
                    $points = isset($row['points']) ? $row['points'] : (isset($row['point']) ? $row['point'] : '');
                    echo "<tr><td>{$id}</td><td>{$id_coureur}</td><td>{$points}</td></tr>";
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
