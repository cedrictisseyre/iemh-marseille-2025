<?php
include 'database_connexion.php';

$page = $_GET['page'] ?? 'joueurs';

function nav($active) {
    $tabs = [
        'joueurs' => 'Joueurs',
        'stats' => 'Statistiques',
        'classement' => 'Classement'
    ];
    echo '<nav class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a>";
    }
    echo '</nav>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="style-page.css">
</head>
<body>
<header>
    <div class="logo-title">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="NFL Logo" class="logo">
        <h1>NFL Stats Analyzer</h1>
    </div>
    <?php nav($page); ?>
</header>

<main>
<?php
// ----- ONGLET JOUEURS -----
if ($page === 'joueurs') {
    // Formulaire ajout joueur
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajout_joueur'])) {
        $sql = "INSERT INTO player (prenom, nom, poste, age, taille_cm, poids_kg, annee_debut, id_team) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['prenom'], $_POST['nom'], $_POST['poste'], $_POST['age'],
            $_POST['taille_cm'], $_POST['poids_kg'], $_POST['annee_debut'], $_POST['id_team']
        ]);
        echo "<p class='success'>Joueur ajouté avec succès !</p>";
    }

    echo '<h2>Ajouter un joueur</h2>
    <form method="POST" class="formulaire">
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="poste" placeholder="Poste" required>
        <input type="number" name="age" placeholder="Âge" required>
        <input type="number" name="taille_cm" placeholder="Taille (cm)" required>
        <input type="number" name="poids_kg" placeholder="Poids (kg)" required>
        <input type="number" name="annee_debut" placeholder="Année début" required>
        <select name="id_team" required>';
            $teams = $pdo->query("SELECT * FROM team ORDER BY nom_team");
            while ($t = $teams->fetch()) {
                echo "<option value='{$t['id_team']}'>{$t['nom_team']} ({$t['conference']})</option>";
            }
    echo '</select>
        <button type="submit" name="ajout_joueur">Ajouter</button>
    </form>';

    // Affichage joueurs
    $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p JOIN team t ON p.id_team = t.id_team");
    echo '<h2>Liste des joueurs</h2><div class="grid">';
    while ($pl = $stmt->fetch()) {
        $experience = date('Y') - $pl['annee_debut'];
        echo "<div class='card'>
                <h3>{$pl['prenom']} {$pl['nom']}</h3>
                <p><strong>Poste:</strong> {$pl['poste']}</p>
                <p><strong>Équipe:</strong> {$pl['nom_team']}</p>
                <p>Âge: {$pl['age']} ans</p>
                <p>Taille: {$pl['taille_cm']} cm - Poids: {$pl['poids_kg']} kg</p>
                <p>Expérience: {$experience} ans</p>
              </div>";
    }
    echo '</div>';
}

// ----- ONGLET STATISTIQUES -----
elseif ($page === 'stats') {
    $saison = date('Y');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajout_stats'])) {
        $sql = "INSERT INTO stats (id_player, saison, yards_passe, td_passe, interceptions, yards_course, td_course, receptions, yards_reception, td_reception, plaquages, sacks, interceptions_def, fg_reussis, punts) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['id_player'], $saison, $_POST['yards_passe'], $_POST['td_passe'], $_POST['interceptions'],
            $_POST['yards_course'], $_POST['td_course'], $_POST['receptions'], $_POST['yards_reception'], $_POST['td_reception'],
            $_POST['plaquages'], $_POST['sacks'], $_POST['interceptions_def'], $_POST['fg_reussis'], $_POST['punts']
        ]);
        echo "<p class='success'>Statistiques ajoutées pour la saison $saison !</p>";
    }

    echo '<h2>Ajouter des statistiques</h2>
    <form method="POST" class="formulaire">
        <select name="id_player" required>';
            $players = $pdo->query("SELECT * FROM player ORDER BY nom");
            while ($p = $players->fetch()) {
                echo "<option value='{$p['id_player']}'>{$p['prenom']} {$p['nom']} ({$p['poste']})</option>";
            }
    echo '</select>
        <input type="number" name="yards_passe" placeholder="Yards passés">
        <input type="number" name="td_passe" placeholder="TD Passes">
        <input type="number" name="interceptions" placeholder="Interceptions lancées">
        <input type="number" name="yards_course" placeholder="Yards courses">
        <input type="number" name="td_course" placeholder="TD courses">
        <input type="number" name="receptions" placeholder="Réceptions">
        <input type="number" name="yards_reception" placeholder="Yards réception">
        <input type="number" name="td_reception" placeholder="TD réception">
        <input type="number" name="plaquages" placeholder="Plaquages">
        <input type="number" step="0.1" name="sacks" placeholder="Sacks">
        <input type="number" name="interceptions_def" placeholder="Interceptions déf">
        <input type="number" name="fg_reussis" placeholder="Field Goals réussis">
        <input type="number" name="punts" placeholder="Punts">
        <button type="submit" name="ajout_stats">Ajouter stats</button>
    </form>';

    $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste FROM stats s 
                           JOIN player p ON s.id_player = p.id_player 
                           WHERE s.saison = ?");
    $stmt->execute([$saison]);
    echo "<h2>Statistiques $saison</h2><div class='grid'>";
    while ($st = $stmt->fetch()) {
        echo "<div class='card'>
                <h3>{$st['prenom']} {$st['nom']} ({$st['poste']})</h3>
                <p>Yards Passes: {$st['yards_passe']} | TD: {$st['td_passe']} | INT: {$st['interceptions']}</p>
                <p>Rush: {$st['yards_course']} yds / {$st['td_course']} TD</p>
                <p>Réceptions: {$st['receptions']} - {$st['yards_reception']} yds / {$st['td_reception']} TD</p>
                <p>Tackles: {$st['plaquages']} | Sacks: {$st['sacks']} | INT Def: {$st['interceptions_def']}</p>
                <p>FG: {$st['fg_reussis']} | Punts: {$st['punts']}</p>
              </div>";
    }
    echo '</div>';
}

// ----- ONGLET CLASSEMENT -----
elseif ($page === 'classement') {
    echo '<h2>Classement par conférence (basé sur les TD totaux)</h2>';
    $sql = "SELECT p.nom, p.prenom, t.conference, 
                   (COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0)) as total_td
            FROM player p
            JOIN team t ON p.id_team = t.id_team
            LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ?
            ORDER BY t.conference, total_td DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([date('Y')]);

    $conf = '';
    while ($row = $stmt->fetch()) {
        if ($row['conference'] !== $conf) {
            $conf = $row['conference'];
            echo "<h3>{$conf}</h3><ol>";
        }
        echo "<li>{$row['prenom']} {$row['nom']} - {$row['total_td']} TD</li>";
    }
    echo '</ol>';
}
?>
</main>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Données inspirées de NFL.com & Pro-Football-Reference</p>
</footer>
</body>
</html>
