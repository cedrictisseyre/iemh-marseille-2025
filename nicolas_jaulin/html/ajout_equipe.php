<?php
require_once 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_equipe'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $annee = $_POST['annee_creation'] ?? '';
    if ($nom) {
        $stmt = $pdo->prepare('INSERT INTO Equipes (nom_equipe, ville, annee_creation) VALUES (?, ?, ?)');
        $stmt->execute([$nom, $ville, $annee]);
        $message = 'Équipe ajoutée !';
    } else {
        $message = 'Le nom est obligatoire.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une équipe</title>
</head>
<body>
    <h1>Ajouter une équipe</h1>
    <form method="post">
        <label>Nom de l'équipe : <input type="text" name="nom_equipe" required></label><br>
        <label>Ville : <input type="text" name="ville"></label><br>
        <label>Année de création : <input type="number" name="annee_creation" min="1800" max="2100"></label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_equipes.php">Retour à la liste</a>
</body>
</html>