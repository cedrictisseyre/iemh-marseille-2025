<?php
require_once 'db_connect.php';
$message = '';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Equipes WHERE id_equipe = ?');
    $stmt->execute([$id]);
    $equipe = $stmt->fetch();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom_equipe'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $annee = $_POST['annee_creation'] ?? '';
        if ($nom) {
            $stmt = $pdo->prepare('UPDATE Equipes SET nom_equipe = ?, ville = ?, annee_creation = ? WHERE id_equipe = ?');
            $stmt->execute([$nom, $ville, $annee, $id]);
            $message = 'Équipe modifiée !';
        } else {
            $message = 'Le nom est obligatoire.';
        }
    }
} else {
    header('Location: liste_equipes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une équipe</title>
</head>
<body>
    <h1>Modifier une équipe</h1>
    <form method="post">
        <label>Nom de l'équipe : <input type="text" name="nom_equipe" value="<?= htmlspecialchars($equipe['nom_equipe']) ?>" required></label><br>
        <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($equipe['ville']) ?>"></label><br>
        <label>Année de création : <input type="number" name="annee_creation" min="1800" max="2100" value="<?= htmlspecialchars($equipe['annee_creation']) ?>"></label><br>
        <button type="submit">Modifier</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_equipes.php">Retour à la liste</a>
</body>
</html>