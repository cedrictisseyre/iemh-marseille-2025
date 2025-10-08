<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Connexion à la base de données avec PDO
require_once __DIR__ . '/../database/bdd_formule1.php';

try {
    // $pdo est déjà défini dans bdd_formule1.php
    // Récupérer tous les pilotes champions du monde (vainqueurs du championnat)
    $sql = "SELECT p.pilote_id, p.nom, p.prénom FROM pilotes p WHERE p.pilote_id IN (SELECT pilote_id FROM championnats) ORDER BY p.nom, p.prénom";
    $stmt = $pdo->query($sql);
    $champions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pid = $row['pilote_id'];
        // Nombre de victoires (nombre de championnats gagnés)
        $sql2 = "SELECT COUNT(*) as nb FROM championnats WHERE pilote_id = ?";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$pid]);
        $row['nb_victoires'] = $stmt2->fetchColumn();

        // Années de victoires
        $sql3 = "SELECT annee FROM championnats WHERE pilote_id = ? ORDER BY annee";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->execute([$pid]);
        $annees_victoires = $stmt3->fetchAll(PDO::FETCH_COLUMN);
        $row['annees_victoires'] = implode(', ', $annees_victoires);

        // Nombre de participations (nombre d'années où il a participé)
        $sql4 = "SELECT COUNT(DISTINCT annee) as nb FROM participations WHERE pilote_id = ?";
        $stmt4 = $pdo->prepare($sql4);
        $stmt4->execute([$pid]);
        $row['nb_participations'] = $stmt4->fetchColumn();

        // Années de participations
        $sql5 = "SELECT DISTINCT annee FROM participations WHERE pilote_id = ? ORDER BY annee";
        $stmt5 = $pdo->prepare($sql5);
        $stmt5->execute([$pid]);
        $annees_participations = $stmt5->fetchAll(PDO::FETCH_COLUMN);
        $row['annees_participations'] = implode(', ', $annees_participations);

        // Photo (optionnel, si colonne présente)
        $row['photo'] = isset($row['photo']) ? $row['photo'] : '';

        $champions[] = $row;
    }
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Affichage CLI si exécuté en ligne de commande
if (php_sapi_name() === 'cli') {
    echo "Champions trouvés : ".count($champions)."\n";
    foreach ($champions as $champion) {
        echo ($champion['prénom'] ?? '').' '.$champion['nom']." (Victoires: ".$champion['nb_victoires'].")\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panthéon des Pilotes Champions</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        h1 { text-align: center; margin-top: 30px; }
        .tabs { display: flex; flex-wrap: wrap; justify-content: center; margin-bottom: 20px; }
        .tab { padding: 10px 20px; margin: 0 5px; background: #ddd; border-radius: 8px 8px 0 0; cursor: pointer; }
        .tab.active { background: #fff; font-weight: bold; border-bottom: 2px solid #fff; }
        .profile { display: none; background: #fff; border-radius: 0 8px 8px 8px; box-shadow: 0 2px 8px #ccc; padding: 30px; max-width: 600px; margin: 0 auto 30px; }
        .profile.active { display: block; }
        .profile img { float: right; width: 120px; height: 120px; object-fit: cover; border-radius: 50%; margin-left: 20px; }
        .info { overflow: hidden; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Panthéon des Pilotes Champions</h1>
    <?php if (empty($champions)): ?>
        <p style="text-align:center;">Aucun champion trouvé.</p>
    <?php else: ?>
        <div class="tabs">
            <?php foreach ($champions as $i => $champion): ?>
                <div class="tab<?= $i === 0 ? ' active' : '' ?>" data-tab="tab<?= $i ?>">
                    <?= htmlspecialchars(($champion['prénom'] ?? '') . ' ' . $champion['nom']) ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php foreach ($champions as $i => $champion): ?>
            <div class="profile<?= $i === 0 ? ' active' : '' ?>" id="tab<?= $i ?>">
                <div class="info">
                    <?php if (!empty($champion['photo'])): ?>
                        <img src="<?= htmlspecialchars($champion['photo']) ?>" alt="Photo de <?= htmlspecialchars(($champion['prénom'] ?? '') . ' ' . $champion['nom']) ?>">
                    <?php endif; ?>
                    <p><span class="label">Nom :</span> <?= htmlspecialchars($champion['nom']) ?></p>
                    <p><span class="label">Prénom :</span> <?= htmlspecialchars($champion['prénom'] ?? '') ?></p>
                    <p><span class="label">Nombre de victoires :</span> <?= htmlspecialchars($champion['nb_victoires']) ?></p>
                    <p><span class="label">Nombre de participations :</span> <?= htmlspecialchars($champion['nb_participations']) ?></p>
                    <p><span class="label">Années de participations :</span> <?= htmlspecialchars($champion['annees_participations']) ?></p>
                    <p><span class="label">Années de victoires :</span> <?= htmlspecialchars($champion['annees_victoires']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        <script>
            const tabs = document.querySelectorAll('.tab');
            const profiles = document.querySelectorAll('.profile');
            tabs.forEach((tab, i) => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    profiles.forEach(p => p.classList.remove('active'));
                    tab.classList.add('active');
                    profiles[i].classList.add('active');
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
