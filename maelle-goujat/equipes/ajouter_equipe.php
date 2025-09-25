<?php
require_once __DIR__ . '/../connexion.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_equipe'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $pays = $_POST['pays'] ?? '';
    $sql = 'INSERT INTO equipes (nom_equipe, ville, pays) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nom, $ville, $pays]);
    $message = 'Équipe ajoutée !';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une équipe</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une équipe</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post">
            <div style="margin-bottom:1em;">
                <label>Nom de l'équipe : <input type="text" name="nom_equipe" required></label>
            </div>
            <div style="margin-bottom:1em;">
                <label>Ville : <input type="text" name="ville"></label>
            </div>
            <div style="margin-bottom:1em;">
                <label>Pays : <input type="text" name="pays"></label>
            </div>
            <button type="submit">Ajouter</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
