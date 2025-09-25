<?php
include 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nationalite = $_POST['nationalite'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $club = $_POST['club'] ?? '';
    if ($nom && $prenom && $nationalite && $date_naissance && $club) {
        $sql = 'INSERT INTO coureurs_UTMB (nom, prenom, nationalite, date_naissance, club) VALUES (?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $nationalite, $date_naissance, $club]);
        $message = 'Coureur ajouté avec succès !';
    } else {
        $message = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un coureur</title>
</head>
<body>
    <h1>Ajouter un coureur UTMB</h1>
    <a href="liste_coureurs.php">Retour à la liste</a>
    <?php if ($message) echo '<p>' . htmlspecialchars($message) . '</p>'; ?>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Prénom : <input type="text" name="prenom" required></label><br>
        <label>Nationalité (3 lettres) : <input type="text" name="nationalite" maxlength="3" required></label><br>
        <label>Date de naissance : <input type="date" name="date_naissance" required></label><br>
        <label>Club : <input type="text" name="club" required></label><br>
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
