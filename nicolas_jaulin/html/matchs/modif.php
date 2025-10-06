<?php
require_once 'db_connect.php';
$message = '';
$id = $_GET['id'] ?? '';
$equipes = $pdo->query('SELECT id_equipe, nom_equipe FROM Equipes')->fetchAll();
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Matchs WHERE id_match = ?');
    $stmt->execute([$id]);
    $match = $stmt->fetch();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date_match'] ?? '';
        $lieu = $_POST['lieu'] ?? '';
        $dom = $_POST['id_equipe_dom'] ?? '';
        $ext = $_POST['id_equipe_ext'] ?? '';
        $score_dom = $_POST['score_dom'] ?? 0;
        $score_ext = $_POST['score_ext'] ?? 0;
        if ($date && $dom && $ext && $dom != $ext) {
            $stmt = $pdo->prepare('UPDATE Matchs SET date_match = ?, lieu = ?, id_equipe_dom = ?, id_equipe_ext = ?, score_dom = ?, score_ext = ? WHERE id_match = ?');
            $stmt->execute([$date, $lieu, $dom, $ext, $score_dom, $score_ext, $id]);
            $message = 'Match modifié !';
        } else {
            $message = 'Veuillez remplir tous les champs et choisir deux équipes différentes.';
        }
    }
} else {
    header('Location: liste.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un match</title>
</head>
<body>
    <h1>Modifier un match</h1>
    <form method="post">
        <label>Date : <input type="date" name="date_match" value="<?= htmlspecialchars($match['date_match']) ?>" required></label><br>
        <label>Lieu : <input type="text" name="lieu" value="<?= htmlspecialchars($match['lieu']) ?>"></label><br>
        <label>Équipe domicile :
            <select name="id_equipe_dom" required>
                <option value="">Choisir</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>" <?= $match['id_equipe_dom'] == $equipe['id_equipe'] ? 'selected' : '' ?>><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Équipe extérieure :
            <select name="id_equipe_ext" required>
                <option value="">Choisir</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>" <?= $match['id_equipe_ext'] == $equipe['id_equipe'] ? 'selected' : '' ?>><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Score domicile : <input type="number" name="score_dom" min="0" value="<?= htmlspecialchars($match['score_dom']) ?>"></label><br>
        <label>Score extérieur : <input type="number" name="score_ext" min="0" value="<?= htmlspecialchars($match['score_ext']) ?>"></label><br>
        <button type="submit">Modifier</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_matchs.php">Retour à la liste</a>
</body>
</html>