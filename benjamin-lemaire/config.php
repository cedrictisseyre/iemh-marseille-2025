<?php
$host = '195.15.235.20';
$dbname = 'benjamin_lemaire';
$user = 'root'; // À adapter si besoin
$pass = 'INNnsk40374';

try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
