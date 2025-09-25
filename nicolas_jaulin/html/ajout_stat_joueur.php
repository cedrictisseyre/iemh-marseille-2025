<?php
require_once 'db_connect.php';
$message = '';
$matchs = $pdo->query('SELECT id_match, date_match FROM Matchs')->fetchAll();
$joueurs = $pdo->query('SELECT id_joueur, nom, prenom FROM Joueurs')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_match = $_POST['id_match'] ?? '';
    $id_joueur = $_POST['id_joueur'] ?? '';
    $essais = $_POST['essais'] ?? 0;
    $plaquages = $_POST['plaquages'] ?? 0;
    $passes = $_POST['passes_decisives'] ?? 0;
    $cartons = $_POST['cartons_jaunes'] ?? 0;
    if ($id_match && $id_joueur) {
        $stmt = $pdo->prepare('INSERT INTO Statistiques_Joueur (id_match, id_joueur, essais, plaquages, passes_decisives, cartons_jaunes) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$id_match, $id_joueur, $essais, $plaquages, $passes, $cartons]);
        $message = 'Statistique ajoutée !';
    } else {
        $message = 'Veuillez sélectionner un match et un joueur.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une statistique joueur</title>
</head>
<body>
    <h1>Ajouter une statistique joueur</h1>
    <form method="post">
        <label>Match :
            <select name="id_match" required>
                <option value="">Choisir</option>
                <?php foreach ($matchs as $match): ?>
                    <option value="<?= $match['id_match'] ?>">ID <?= $match['id_match'] ?> - <?= htmlspecialchars($match['date_match']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Joueur :
            <select name="id_joueur" required>
                <option value="">Choisir</option>
                <?php foreach ($joueurs as $joueur): ?>
                    <option value="<?= $joueur['id_joueur'] ?>"><?= htmlspecialchars($joueur['nom']) ?> <?= htmlspecialchars($joueur['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Essais : <input type="number" name="essais" min="0" value="0"></label><br>
        <label>Plaquages : <input type="number" name="plaquages" min="0" value="0"></label><br>
        <label>Passes décisives : <input type="number" name="passes_decisives" min="0" value="0"></label><br>
        <label>Cartons jaunes : <input type="number" name="cartons_jaunes" min="0" value="0"></label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_stat_joueur.php">Retour à la liste</a>
</body>
</html>