<?php
$conn = @new mysqli('127.0.0.1', 'root', 'INNnsk40374', 'maelle_goujat');

if ($conn->connect_error) {
    die("Erreur : " . $conn->connect_error);
} else {
    echo "Connexion MySQL OK !";
}
?>
