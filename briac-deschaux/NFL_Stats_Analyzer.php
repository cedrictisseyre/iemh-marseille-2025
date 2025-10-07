<?php 
include __DIR__ . '/config/database_connexion.php';

// Définition de la page actuelle
$page = $_GET['page'] ?? 'joueurs';

// Fonction pour générer le menu de navigation
function nav($active) {
    $tabs = [
        'joueurs'   => 'Joueurs',
        'stats'     => 'Statistiques',
        'ranking'   => 'Classement'
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
    <style>
        table {width: 100%; border-collapse: collapse; margin-top: 1em;}
        th, td {border: 1px solid #e2e8f0; padding: 8px; text-align: center;}
        th {cursor: pointer; background: #f1f5f9;}
        th:hover {background: #e2e8f0;}
    </style>
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
        <?php if ($page === 'joueurs') : ?>
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

            <h2>Liste des joueurs</h2>
            <div class="grid">
                <?php
                $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p JOIN team t ON p.id_team = t.id_team ORDER BY p.nom");
                while ($pl = $stmt->fetch()) {
                    $experience = date('Y') - $pl['annee_debut'];
                    echo "<div class='card'>
                        <h3>" . htmlspecialchars($pl['prenom']) . " " . htmlspecialchars($pl['nom']) . "</h3>
                        <p><strong>Poste:</strong> " . htmlspecialchars($pl['poste']) . "</p>
                        <p><strong>Équipe:</strong> " . htmlspecialchars($pl['nom_team']) . "</p>
                        <p>Âge: {$pl['age']} ans</p>
                        <p>Taille: {$pl['taille_cm']} cm - Poids: {$pl['poids_kg']} kg</p>
                        <p>Expérience: {$experience} ans</p>
                    </div>";
                }
                ?>
            </div>

        <?php elseif ($page === 'stats') : ?>
            <h2>Statistiques par poste (<?= date('Y') ?>)</h2>
            <?php
            $saison = date('Y');
            $posts = [
                'QB' => 'Quarterbacks',
                'RB' => 'Running Backs',
                'WR' => 'Wide Receivers'
            ];
            foreach ($posts as $code => $label) {
                echo "<h3>$label</h3>";
                $sql = "SELECT p.prenom, p.nom, p.poste, s.* FROM stats s 
                        JOIN player p ON p.id_player = s.id_player 
                        WHERE s.saison = ? AND p.poste = ? ORDER BY p.nom";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$saison, $code]);
                $rows = $stmt->fetchAll();

                if ($rows) {
                    echo "<table class='sortable'><thead><tr>";
                    foreach (array_keys($rows[0]) as $col) {
                        if (!in_array($col, ['id_stat','id_player','saison'])) {
                            echo "<th>" . htmlspecialchars($col) . "</th>";
                        }
                    }
                    echo "</tr></thead><tbody>";
                    foreach ($rows as $row) {
                        echo "<tr>";
                        foreach ($row as $col => $val) {
                            if (!in_array($col, ['id_stat','id_player','saison'])) {
                                echo "<td>" . htmlspecialchars((string)$val) . "</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>Aucune donnée pour ce poste.</p>";
                }
            }
            ?>

        <?php elseif ($page === 'ranking') : ?>
            <h2>Classement des joueurs</h2>
            <form method="get">
                <input type="hidden" name="page" value="ranking">
                <label>Saison:
                    <input type="number" name="saison" value="<?= date('Y') ?>">
                </label>
                <label>Poste:
                    <input type="text" name="poste" placeholder="QB, RB, WR">
                </label>
                <label>Équipe ID:
                    <input type="number" name="id_team">
                </label>
                <button type="submit">Filtrer</button>
            </form>

            <?php
            $saison = (int)($_GET['saison'] ?? date('Y'));
            $poste = $_GET['poste'] ?? '';
            $id_team = (int)($_GET['id_team'] ?? 0);

            $sql = "SELECT p.prenom, p.nom, p.poste, t.nom_team,
                           COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0) AS total_td,
                           COALESCE(s.yards_passe,0) + COALESCE(s.yards_course,0) + COALESCE(s.yards_reception,0) AS total_yards
                    FROM player p
                    JOIN team t ON t.id_team = p.id_team
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ?
                    WHERE 1=1";

            $params = [$saison];
            if ($poste !== '') {
                $sql .= " AND p.poste = ?";
                $params[] = $poste;
            }
            if ($id_team > 0) {
                $sql .= " AND p.id_team = ?";
                $params[] = $id_team;
            }

            $sql .= " ORDER BY total_td DESC, total_yards DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            if ($rows) {
                echo "<table class='sortable'><thead><tr><th>Nom</th><th>Poste</th><th>Équipe</th><th>TD</th><th>Yards</th></tr></thead><tbody>";
                foreach ($rows as $r) {
                    echo "<tr>
                        <td>" . htmlspecialchars($r['prenom'] . ' ' . $r['nom']) . "</td>
                        <td>" . htmlspecialchars($r['poste']) . "</td>
                        <td>" . htmlspecialchars($r['nom_team']) . "</td>
                        <td>{$r['total_td']}</td>
                        <td>{$r['total_yards']}</td>
                    </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>Aucun joueur trouvé pour ces filtres.</p>";
            }
            ?>
        <?php endif; ?>
    </main>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>

<script>
// Tri simple des colonnes de tableaux
function sortTable(table, col) {
  const rows = Array.from(table.tBodies[0].rows);
  const asc = table.asc = !table.asc;
  rows.sort((a, b) => {
    let A = a.cells[col].innerText.trim();
    let B = b.cells[col].innerText.trim();
    if (!isNaN(A) && !isNaN(B)) { A = Number(A); B = Number(B); }
    return asc ? A > B ? 1 : -1 : A < B ? 1 : -1;
  });
  rows.forEach(r => table.tBodies[0].appendChild(r));
}

document.querySelectorAll("table.sortable").forEach(table => {
  table.querySelectorAll("th").forEach((th, i) => {
    th.addEventListener("click", () => sortTable(table, i));
  });
});
</script>
</body>
</html>
