<?php
require_once '../connexion.php';
$date = $_POST['date'] ?? '';
if ($date !== '') {
    $sql = "INSERT INTO cours (date) VALUES (:date)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':date' => $date]);
    echo $result ? "ok" : "erreur";
} else {
    echo "erreur";
}
?>