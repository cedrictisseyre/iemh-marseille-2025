<?php
include '../connexion.php';
header('Content-Type: application/json');

if (!empty($_POST['id_coureur']) && !empty($_POST['id_course']) && !empty($_POST['temps'])) {
    $id_coureur = $_POST['id_coureur'];
    $id_course = $_POST['id_course'];
    $temps = $_POST['temps'];
    $stmt = $pdo->prepare('INSERT INTO participations (id_coureur, id_course, temps) VALUES (?, ?, ?)');
    if ($stmt->execute([$id_coureur, $id_course, $temps])) {
        echo json_encode(['success' => true, 'message' => 'Participation ajoutée !']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
}
