<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $poste = $_POST['poste'] ?? null;
    $id_equipe = $_POST['id_equipe'] ?? null;
    $sql = 'INSERT INTO joueurs (nom, prenom, poste, id_equipe) VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nom, $prenom, $poste, $id_equipe]);
    echo 'Joueur ajouté !';
}
?>
