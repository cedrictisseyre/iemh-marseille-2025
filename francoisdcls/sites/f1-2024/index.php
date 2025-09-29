<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Lecture du CSV
$pilotes = [];
if (($handle = fopen(__DIR__ . '/pilotes_2024.csv', 'r')) !== false) {
    $header = fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== false) {
        $pilotes[] = array_combine($header, $data);
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>F1 2024 – Panthéon des Pilotes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Panthéon des Pilotes F1 – Saison 2024</h1>
    <?php if (empty($pilotes)): ?>
        <p style="text-align:center;">Aucun pilote trouvé.</p>
    <?php else: ?>
        <div class="tabs">
            <?php foreach ($pilotes as $i => $pilote): ?>
                <button class="tab<?= $i === 0 ? ' active' : '' ?>" data-tab="tab<?= $i ?>">
                    <?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($pilotes as $i => $pilote): ?>
            <div class="profile<?= $i === 0 ? ' active' : '' ?>" id="tab<?= $i ?>">
                <div class="info">
                    <img src="<?= htmlspecialchars($pilote['photo']) ?>" alt="Photo de <?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?>">
                    <p><span class="label">Nom :</span> <?= htmlspecialchars($pilote['nom']) ?></p>
                    <p><span class="label">Prénom :</span> <?= htmlspecialchars($pilote['prenom']) ?></p>
                    <p><span class="label">Écurie actuelle :</span> <span class="ecurie"><?= htmlspecialchars($pilote['ecurie']) ?></span></p>
                    <p><span class="label">Nombre de GP :</span> <?= htmlspecialchars($pilote['nb_gp']) ?></p>
                    <p><span class="label">Victoires :</span> <?= htmlspecialchars($pilote['nb_victoires']) ?></p>
                    <p><span class="label">Podiums :</span> <?= htmlspecialchars($pilote['nb_podiums']) ?></p>
                    <p><span class="label">Champion du monde :</span> <?= $pilote['champion'] === 'oui' ? '🏆 Oui' : 'Non' ?></p>
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
