<?php
include '../connexion.php';
header('Content-Type: application/json');

// Vérification des données reçues
if (!empty($_POST['nom']) && !empty($_POST['date']) && !empty($_POST['lieu'])) {
    $nom = $_POST['nom'];
    $date = $_POST['date'];
    $lieu = $_POST['lieu'];
    $stmt = $pdo->prepare('INSERT INTO courses (nom, date, lieu) VALUES (?, ?, ?)');
    if ($stmt->execute([$nom, $date, $lieu])) {
        echo json_encode(['success' => true, 'message' => 'Course ajoutée !']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
}
