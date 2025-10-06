<?php
declare(strict_types=1);

/**
 * NFL_Stats_Analyzer.php
 *
 * Page principale de l'application.
 * Inclut la connexion PDO et les helpers.
 */

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

$page = (string) ($_GET['page'] ?? 'joueurs');

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE);
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>NFL Stats Analyzer</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<div class="container">
    <header class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </header>

    <?php nav($page); ?>

    <main>
        <?php
        if ($page === 'joueurs'):
            // Formulaire ajout joueur
            ?>
            <div class="card">
                <h2>Ajouter un joueur</h2>
                <form method="post" action="services/add_player.php">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>

                    <select name="poste" required>
                        <option value="">Sélectionner un poste</option>
                        <optgroup label="Offense">
                            <option value="QB">Quarterback (QB)</option>
                            <option value="RB">Running Back (RB)</option>
                            <option value="FB">Fullback (FB)</option>
                            <option value="WR">Wide Receiver (WR)</option>
                            <option value="TE">Tight End (TE)</option>
                        </optgroup>
                        <optgroup label="Defense">
                            <option value="DE">Defensive End (DE)</option>
                            <option value="DT">Defensive Tackle (DT)</option>
                            <option value="OLB">Outside Linebacker (OLB)</option>
                            <option value="ILB">Inside Linebacker (ILB)</option>
                            <option value="CB">Cornerback (CB)</option>
                            <option value="S">Safety (S)</option>
                        </optgroup>
                        <optgroup label="Special Teams">
                            <option value="K">Kicker (K)</option>
                            <option value="P">Punter (P)</option>
                        </optgroup>
                    </select>

                    <input type="number" name="age" placeholder="Âge" min="15" max="80" required>
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" min="100" max="250" required>
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" min="40" max="200" required>
                    <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" min="1900" max="<?php echo date('Y'); ?>" required>

                    <label>
                        <select name="id_team" required>
                            <option value="">Sélectionner une équipe</option>
                            <?php
                            // Récupération des teams présentes dans la BD (sécurisé)
                            $teams = $pdo->query('SELECT id_team, nom_team FROM team ORDER BY nom_team')->fetchAll();
                            foreach ($teams as $t) {
                                echo '<option value="' . h((string)$t['id_team']) . '">' . h((string)$t['nom_team']) . '</option>';
                            }
                            ?>
                        </select>
                    </label>

                    <button type="submit">Ajouter le joueur</button>
                </form>
            </div>

            <h2>Liste des joueurs</h2>
            <div class="grid">
                <?php
                $stmt = $pdo->query('SELECT p.*, t.nom_team FROM player p LEFT JOIN team t ON p.id_team = t.id_team ORDER BY p.nom');
                while ($pl = $stmt->fetch()) {
                    $anneeDeDebut = (int)($pl['annee_debut'] ?? 0);
                    $experience = $anneeDeDebut > 0 ? (date('Y') - $anneeDeDebut) : 0;
                    ?>
                    <div class="card">
                        <h3><?php echo h((string)$pl['prenom'] . ' ' . (string)$pl['nom']); ?></h3>
                        <p><strong>Poste:</strong> <?php echo h((string)$pl['poste']); ?></p>
                        <p><strong>Équipe:</strong> <?php echo h((string)($pl['nom_team'] ?? '—')); ?></p>
                        <p>Âge: <?php echo h((string)($pl['age'] ?? '—')); ?> ans</p>
                        <p>Taille: <?php echo h((string)($pl['taille_cm'] ?? '—')); ?> cm - Poids: <?php echo h((string)($pl['poids_kg'] ?? '—')); ?> kg</p>
                        <p>Expérience: <?php echo h((string)$experience); ?> ans</p>
                    </div>
                    <?php
                }
                ?>
            </div>

        <?php elseif ($page === 'stats'):
            $saison = date('Y');
            ?>
            <div class="card"><h2>Ajouter des statistiques (Saison <?php echo h((string)$saison); ?>)</h2>
                <form method="post" action="services/add_stats.php">
                    <select name="id_player" required>
                        <option value="">Sélectionner un joueur</option>
                        <?php
                        $players = $pdo->query('SELECT id_player, prenom, nom FROM player ORDER BY nom')->fetchAll();
                        foreach ($players as $p) {
                            echo '<option value="' . h((string)$p['id_player']) . '">' . h((string)$p['prenom'] . ' ' . $p['nom']) . '</option>';
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

            <h2>Statistiques <?php echo h((string)$saison); ?></h2>
            <div class="grid">
                <?php
                $stmt = $pdo->prepare('SELECT s.*, p.prenom, p.nom, p.poste FROM stats s JOIN player p ON s.id_player = p.id_player WHERE s.saison = ? ORDER BY p.nom');
                $stmt->execute([$saison]);
                while ($st = $stmt->fetch()) {
                    ?>
                    <div class="card">
                        <h3><?php echo h((string)$st['prenom'] . ' ' . $st['nom'] . ' (' . $st['poste'] . ')'); ?></h3>
                        <p>Yds Passe: <?php echo h((string)($st['yards_passe'] ?? 0)); ?> | TD: <?php echo h((string)($st['td_passe'] ?? 0)); ?> | INT: <?php echo h((string)($st['interceptions'] ?? 0)); ?></p>
                        <p>Rush: <?php echo h((string)($st['yards_course'] ?? 0)); ?> yds / <?php echo h((string)($st['td_course'] ?? 0)); ?> TD</p>
                        <p>Réceptions: <?php echo h((string)($st['receptions'] ?? 0)); ?> - <?php echo h((string)($st['yards_reception'] ?? 0)); ?> yds / <?php echo h((string)($st['td_reception'] ?? 0)); ?> TD</p>
                        <p>Plaquages: <?php echo h((string)($st['plaquages'] ?? 0)); ?> | Sacks: <?php echo h((string)($st['sacks'] ?? 0)); ?> | INT Def: <?php echo h((string)($st['interceptions_def'] ?? 0)); ?></p>
                        <p>FG: <?php echo h((string)($st['fg_reussis'] ?? 0)); ?> | Punts: <?php echo h((string)($st['punts'] ?? 0)); ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>

        <?php elseif ($page === 'classement'):
            ?>
            <h2>Classement par conférence (TD total)</h2>
            <?php
            // Calcul du total TD par joueur pour la saison courante
            $sql = 'SELECT p.nom, p.prenom, t.conference,
                           (COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0)) as total_td
                    FROM player p
                    JOIN team t ON p.id_team = t.id_team
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ?
                    ORDER BY t.conference, total_td DESC';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([date('Y')]);

            $conf = '';
            while ($row = $stmt->fetch()) {
                if ($row['conference'] !== $conf) {
                    if ($conf !== '') {
                        echo '</ol>';
                    }
                    $conf = $row['conference'];
                    echo '<h3>' . h((string)$conf) . '</h3><ol>';
                }
                echo '<li>' . h((string)$row['prenom'] . ' ' . $row['nom']) . ' - ' . h((string)$row['total_td']) . ' TD</li>';
            }
            if ($conf !== '') {
                echo '</ol>';
            }
        endif;
        ?>
    </main>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> NFL Stats Analyzer - Projet académique</p>
</footer>

</body>
</html>
