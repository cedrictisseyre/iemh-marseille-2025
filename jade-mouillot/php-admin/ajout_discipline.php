<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];

    $stmt = $pdo->prepare("INSERT INTO discipline (nom) VALUES (?)");
    $stmt->execute([$nom]);
    echo "Discipline ajoutée !";
}
?>

<form method="post">
    Nom : <input type="text" name="nom" required><br>
    <button type="submit">Ajouter</button>
</form>
