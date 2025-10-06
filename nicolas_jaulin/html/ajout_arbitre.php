<?php
require_once 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nationalite = $_POST['nationalite'] ?? '';
    if ($nom) {
        $stmt = $pdo->prepare('INSERT INTO Arbitres (nom, prenom, nationalite) VALUES (?, ?, ?)');
        $stmt->execute([$nom, $prenom, $nationalite]);
        $message = 'Arbitre ajouté !';
    } else {
        $message = 'Le nom est obligatoire.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un arbitre</title>
</head>
<body>
    <h1>Ajouter un arbitre</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Prénom : <input type="text" name="prenom"></label><br>
        <label>Nationalité : <input type="text" name="nationalite"></label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_arbitres.php">Retour à la liste</a>
</body>
</html>