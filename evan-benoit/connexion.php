<?php
// Connexion à la base de données MySQL (phpMyAdmin) 

$host = '195.15.235.20';
$dbname = 'evan_benoit';
$user = 'root';
$password = 'INNnsk40374'; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '❌ Erreur de connexion : ' . $e->getMessage();
    exit;
}
?>

