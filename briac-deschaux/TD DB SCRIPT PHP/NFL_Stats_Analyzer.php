<?php 
include __DIR__ . '/config/database_connexion.php';

// Définition de la page actuelle
$page = $_GET['page'] ?? 'joueurs';

// Fonction pour générer le menu de navigation
function nav($active) {
    $tabs = [
        'joueurs'   => 'Joueurs',
        'stats'     => 'Statistiques',
        'classement'=> 'Classement'
    ];

    echo '<div class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a>";
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main>
        <?php 
        if ($page === 'joueurs') : 
        ?>
            <!-- Formulaire d'ajout joueur -->
            <div class="card">
                <h2>Ajouter un joueur</h2>
                <form method="post" action="services/add_player.php">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>
                    <input type="text" name="poste" placeholder="Poste" required>
                    <input type="number" name="age" placeholder="Âge" required>
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" required>
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" required>
                    <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" required>
                    <input type="number" name="id_team" placeholder="ID équipe" required>
                    <button type="submit">Ajouter le joueur</button>
                </form>
            </div>

            <!-- Liste des joueurs -->
            <h2>Liste des joueurs</h2>
            <div class="grid">
                <?php
                $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p JOIN team t ON p.id_team = t.id_team ORDER BY p.nom");
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
                ?>
            </div>

        <?php elseif ($page === 'stats') : 
            $saison = date('Y'); 
        ?>
            <!-- Formulaire stats -->
            <div class="card">
                <h2>Ajouter des statistiques (Saison <?= $saison ?>)</h2>
                <form method="post" action="services/add_stats.php">
                    <select name="id_player" required>
                        <option value="">Sélectionner un joueur</option>
                        <?php
                        $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll();
                        foreach ($players as $p) {
                            echo "<option value='{$p['id_player']}'>{$p['prenom']} {$p['nom']}</option>";
                        }
                        ?>
                    </select>

                    <input type="number" name="yards_passe" placeholder="Yards passés" min="0">
                    <input type="number" name="td_passe" placeholder="TD passés" min="0">
                    <input type="number" name="interceptions" placeholder="Interceptions" min="0">
                    <input type="number" name="yards_course" placeholder="Yards course" min="0">
                    <input type="number" name="td_course" placeholder="TD course" min="0">
                    <input type="number" name="receptions" placeholder="Réceptions" min="0">
                    <input type="number" name="yards_reception" placeholder="Yards réception" min="0">
                    <input type="number" name="td_reception" placeholder="TD réception" min="0">
                    <input type="number" name="plaquages" placeholder="Plaquages" min="0">
                    <input type="number" step="0.1" name="sacks" placeholder="Sacks" min="0">
                    <input type="number" name="interceptions_def" placeholder="Interceptions déf" min="0">
                    <input type="number" name="fg_reussis" placeholder="FG réussis" min="0">
                    <input type="number" name="punts" placeholder="Punts" min="0">

                    <button type="submit">Ajouter les stats</button>
                </form>
            </div>

            <!-- Affichage stats -->
            <h2>Statistiques <?= $saison ?></h2>
            <div class="grid">
                <?php
                $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste 
                                       FROM stats s 
                                       JOIN player p ON s.id_player = p.id_player 
                                       WHERE s.saison = ? 
                                       ORDER BY p.nom");
                $stmt->execute([$saison]);

                while ($st = $stmt->fetch()) {
                    echo "<div class='card'>
                        <h3>{$st['prenom']} {$st['nom']} ({$st['poste']})</h3>";

                    // Ignorer les colonnes techniques
                    $exclude = ['id_stats','id_player','prenom','nom','poste','saison'];
                    foreach ($st as $key => $val) {
                        if (!in_array($key, $exclude) && $val !== null && $val != 0) {
                            $label = ucfirst(str_replace("_", " ", $key));
                            echo "<p>{$label}: {$val}</p>";
                        }
                    }

                    echo "</div>";
                }
                ?>
            </div>

        <?php elseif ($page === 'classement') : ?>
            <!-- Classement par conférence -->
            <h2>Classement par conférence (Total TD)</h2>
            <?php
            $sql_conf = "SELECT p.nom, p.prenom, p.poste, t.conference,
                           (COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0)) as total_td
                    FROM player p 
                    JOIN team t ON p.id_team = t.id_team 
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ? 
                    ORDER BY t.conference, total_td DESC";
            $stmt_conf = $pdo->prepare($sql_conf);
            $stmt_conf->execute([date('Y')]);

            $conf = '';
            while ($row = $stmt_conf->fetch()) {
                if ($row['conference'] !== $conf) {
                    if ($conf !== '') echo '</ol>';
                    $conf = $row['conference'];
                    echo "<h3>{$conf}</h3><ol>";
                }
                echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_td']} TD</li>";
            }
            if ($conf !== '') echo '</ol>';
            ?>

            <!-- Classement par division -->
            <h2>Classement par division (Plaquages)</h2>
            <?php
            $sql_div = "SELECT p.nom, p.prenom, p.poste, t.division,
                           COALESCE(s.plaquages,0) as total_plaquages
                    FROM player p 
                    JOIN team t ON p.id_team = t.id_team 
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ? 
                    ORDER BY t.division, total_plaquages DESC";
            $stmt_div = $pdo->prepare($sql_div);
            $stmt_div->execute([date('Y')]);

            $div = '';
            while ($row = $stmt_div->fetch()) {
                if ($row['division'] !== $div) {
                    if ($div !== '') echo '</ol>';
                    $div = $row['division'];
                    echo "<h3>{$div}</h3><ol>";
                }
                echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_plaquages']} plaquages</li>";
            }
            if ($div !== '') echo '</ol>';
            ?>
        <?php endif; ?>
    </main>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>
</body>
</html>
