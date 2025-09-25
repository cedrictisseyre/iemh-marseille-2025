<?php
require_once '../connexion.php';
$nom = $_POST['nom'] ?? '';
if ($nom !== '') {
    $sql = "INSERT INTO sportifs (nom) VALUES (:nom)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':nom' => $nom]);
    echo $result ? "ok" : "erreur";
} else {
    echo "erreur";
}
?>