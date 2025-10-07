<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connexion.php';
require_once __DIR__ . '/includes/header.php';

// Classement
$sqlClassement = "SELECT t.nom, s.goal_difference, s.points FROM standings s JOIN teams t ON s.team_id = t.id ORDER BY s.points DESC, s.goal_difference DESC";
$stmtClassement = $pdo->query($sqlClassement);
$classement = $stmtClassement->fetchAll(PDO::FETCH_ASSOC);

// Derniers résultats
$sqlMatchs = "SELECT m.*, ht.nom AS home_team, at.nom AS away_team FROM matches m JOIN teams ht ON m.home_team_id = ht.id JOIN teams at ON m.away_team_id = at.id ORDER BY m.date_match DESC LIMIT 5";
$stmtMatchs = $pdo->query($sqlMatchs);
$matchs = $stmtMatchs->fetchAll(PDO::FETCH_ASSOC);

// Meilleurs buteurs
$sqlButeurs = "SELECT p.nom, p.prenom, t.nom AS equipe, p.goals FROM players p JOIN teams t ON p.team_id = t.id ORDER BY p.goals DESC LIMIT 5";
$stmtButeurs = $pdo->query($sqlButeurs);
$buteurs = $stmtButeurs->fetchAll(PDO::FETCH_ASSOC);

// Meilleurs passeurs
$sqlPasseurs = "SELECT p.nom, p.prenom, t.nom AS equipe, p.assists FROM players p JOIN teams t ON p.team_id = t.id ORDER BY p.assists DESC LIMIT 5";
$stmtPasseurs = $pdo->query($sqlPasseurs);
$passeurs = $stmtPasseurs->fetchAll(PDO::FETCH_ASSOC);

// Prochain match
$sqlProchain = "SELECT m.*, ht.nom AS home_team, at.nom AS away_team FROM matches m JOIN teams ht ON m.home_team_id = ht.id JOIN teams at ON m.away_team_id = at.id WHERE m.date_match > NOW() ORDER BY m.date_match ASC LIMIT 1";
$stmtProchain = $pdo->query($sqlProchain);
$prochain = $stmtProchain->fetch(PDO::FETCH_ASSOC);
?>

<div class="accueil-grid">
    <section class="accueil-section classement-section">
        <h2>Classement</h2>
        <table class="accueil-table">
            <thead>
                <tr><th>Équipe</th><th>+/-</th><th>Pts</th></tr>
            </thead>
            <tbody>
                <?php foreach ($classement as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nom']) ?></td>
                    <td><?= $c['goal_difference'] ?></td>
                    <td><?= $c['points'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="accueil-section resultats-section">
        <h2>Derniers résultats</h2>
        <table class="accueil-table">
            <thead>
                <tr><th>Date</th><th>Domicile</th><th>Score</th><th>Extérieur</th></tr>
            </thead>
            <tbody>
                <?php foreach ($matchs as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['date_match']) ?></td>
                    <td><?= htmlspecialchars($m['home_team']) ?></td>
                    <td><?= $m['home_score'] . ' - ' . $m['away_score'] ?></td>
                    <td><?= htmlspecialchars($m['away_team']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="accueil-section joueurs-section">
        <h2>Meilleurs buteurs</h2>
        <ul class="top-joueurs">
            <?php foreach ($buteurs as $j): ?>
            <li><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?> (<?= htmlspecialchars($j['equipe']) ?>) : <strong><?= $j['goals'] ?> buts</strong></li>
            <?php endforeach; ?>
        </ul>
        <h2>Meilleurs passeurs</h2>
        <ul class="top-joueurs">
            <?php foreach ($passeurs as $j): ?>
            <li><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?> (<?= htmlspecialchars($j['equipe']) ?>) : <strong><?= $j['assists'] ?> passes</strong></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="accueil-section a-la-une-section">
        <h2>À la une</h2>
        <div class="a-la-une">
            <p>Retrouvez toutes les infos, stats et résultats de la Ligue 1 sur ce site !</p>
        </div>
    </section>

    <section class="accueil-section prochain-match-section">
        <h2>Prochain match</h2>
        <?php if ($prochain): ?>
            <div class="prochain-match">
                <p><strong><?= htmlspecialchars($prochain['home_team']) ?></strong> vs <strong><?= htmlspecialchars($prochain['away_team']) ?></strong></p>
                <p>Date : <?= htmlspecialchars($prochain['date_match']) ?></p>
            </div>
        <?php else: ?>
            <p>Aucun match à venir.</p>
        <?php endif; ?>
    </section>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>