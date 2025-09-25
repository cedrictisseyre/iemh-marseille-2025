<?php
require_once '../connexion.php';
$id_etudiant = $_POST['id_etudiant'] ?? '';
$id_cour = $_POST['id_cour'] ?? '';
if ($id_etudiant !== '' && $id_cour !== '') {
    $sql = "INSERT INTO presence (`id-etudiant`, `id_cour`) VALUES (:id_etudiant, :id_cour)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':id_etudiant' => $id_etudiant, ':id_cour' => $id_cour]);
    echo $result ? "ok" : "erreur";
} else {
    echo "erreur";
}
?>