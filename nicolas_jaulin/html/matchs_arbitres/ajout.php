<?php
require_once 'db_connect.php';
$message = '';
$matchs = $pdo->query('SELECT id_match, date_match FROM Matchs')->fetchAll();
$arbitres = $pdo->query('SELECT id_arbitre, nom, prenom FROM Arbitres')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_match = $_POST['id_match'] ?? '';
    $id_arbitre = $_POST['id_arbitre'] ?? '';
    if ($id_match && $id_arbitre) {
        $stmt = $pdo->prepare('INSERT INTO Matchs_Arbitres (id_match, id_arbitre) VALUES (?, ?)');
        $stmt->execute([$id_match, $id_arbitre]);
        $message = 'Arbitre assigné au match !';
    } else {
        $message = 'Veuillez sélectionner un match et un arbitre.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Assigner un arbitre à un match</title>
</head>
<body>
    <h1>Assigner un arbitre à un match</h1>
    <form method="post">
        <label>Match :
            <select name="id_match" required>
                <option value="">Choisir</option>
                <?php foreach ($matchs as $match): ?>
                    <option value="<?= $match['id_match'] ?>">ID <?= $match['id_match'] ?> - <?= htmlspecialchars($match['date_match']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Arbitre :
            <select name="id_arbitre" required>
                <option value="">Choisir</option>
                <?php foreach ($arbitres as $arbitre): ?>
                    <option value="<?= $arbitre['id_arbitre'] ?>"><?= htmlspecialchars($arbitre['nom']) ?> <?= htmlspecialchars($arbitre['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Assigner</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_matchs_arbitres.php">Retour à la liste</a>
</body>
</html>