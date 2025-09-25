<?php
require_once 'db_connect.php';
$message = '';
$equipes = $pdo->query('SELECT id_equipe, nom_equipe FROM Equipes')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date_match'] ?? '';
    $lieu = $_POST['lieu'] ?? '';
    $dom = $_POST['id_equipe_dom'] ?? '';
    $ext = $_POST['id_equipe_ext'] ?? '';
    $score_dom = $_POST['score_dom'] ?? 0;
    $score_ext = $_POST['score_ext'] ?? 0;
    if ($date && $dom && $ext && $dom != $ext) {
        $stmt = $pdo->prepare('INSERT INTO Matchs (date_match, lieu, id_equipe_dom, id_equipe_ext, score_dom, score_ext) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$date, $lieu, $dom, $ext, $score_dom, $score_ext]);
        $message = 'Match ajouté !';
    } else {
        $message = 'Veuillez remplir tous les champs et choisir deux équipes différentes.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un match</title>
</head>
<body>
    <h1>Ajouter un match</h1>
    <form method="post">
        <label>Date : <input type="date" name="date_match" required></label><br>
        <label>Lieu : <input type="text" name="lieu"></label><br>
        <label>Équipe domicile :
            <select name="id_equipe_dom" required>
                <option value="">Choisir</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>"><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Équipe extérieure :
            <select name="id_equipe_ext" required>
                <option value="">Choisir</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>"><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Score domicile : <input type="number" name="score_dom" min="0" value="0"></label><br>
        <label>Score extérieur : <input type="number" name="score_ext" min="0" value="0"></label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_matchs.php">Retour à la liste</a>
</body>
</html>