
<?php
require_once __DIR__ . '/../connexion.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $poste = $_POST['poste'] ?? null;
    $id_equipe = $_POST['id_equipe'] ?? null;
    $sql = 'INSERT INTO joueurs (nom, prenom, poste, id_equipe) VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nom, $prenom, $poste, $id_equipe]);
    $message = 'Joueur ajouté !';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un joueur</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post">
            <div style="margin-bottom:1em;">
                <label>Nom : <input type="text" name="nom" required></label>
            </div>
            <div style="margin-bottom:1em;">
                <label>Prénom : <input type="text" name="prenom" required></label>
            </div>
            <div style="margin-bottom:1em;">
                <label>Poste : <input type="text" name="poste"></label>
            </div>
            <div style="margin-bottom:1em;">
                <label>ID équipe : <input type="number" name="id_equipe"></label>
            </div>
            <button type="submit">Ajouter</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
