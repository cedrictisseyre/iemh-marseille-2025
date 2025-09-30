
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu du Pendu Sportif</title>
    <link rel="stylesheet" href="style-jeu.css">
</head>
<body>
    <div class="container">
        <h1>Jeu du Pendu Sportif</h1>
        <div class="pendu">
            <?= $perdu ? $pendu[$_SESSION['max_erreurs']] : $pendu[$_SESSION['erreurs']] ?>
        </div>
        <?php if (isset($message)): ?>
            <p><strong><?= $message ?></strong></p>
            <form method="post" class="rejouer-btn">
                <button type="submit">Rejouer</button>
            </form>
        <?php else: ?>
            <div class="mot"><?= $mot_affiche ?></div>
            <div class="lettres">
                Lettres proposées : <?= empty($_SESSION['lettres']) ? 'Aucune' : implode(', ', $_SESSION['lettres']) ?>
            </div>
            <div class="erreurs">
                Erreurs : <?= $_SESSION['erreurs'] ?> / <?= $_SESSION['max_erreurs'] ?>
            </div>
            <form method="post">
                <input type="text" name="lettre" maxlength="1" required autofocus autocomplete="off" pattern="[A-Za-zÀ-ÿ]">
                <button type="submit">Proposer</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>