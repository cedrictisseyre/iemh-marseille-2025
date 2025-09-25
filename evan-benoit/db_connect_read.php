<?php<?php

// Paramètres de connexion// Paramètres de connexion

$host = 'localhost'; // ou l'adresse de votre serveur MySQL$host = 'localhost'; // ou l'adresse de votre serveur MySQL

$user = 'root'; // nom d'utilisateur MySQL$user = 'root'; // nom d'utilisateur MySQL

$password = ''; // mot de passe MySQL$password = ''; // mot de passe MySQL

$dbname = 'nom_de_votre_base'; // nom de la base de données$dbname = 'nom_de_votre_base'; // nom de la base de données



// Connexion à la base de données// Connexion à la base de données

$conn = new mysqli($host, $user, $password, $dbname);$conn = new mysqli($host, $user, $password, $dbname);



// Vérification de la connexion// Vérification de la connexion

if ($conn->connect_error) {if ($conn->connect_error) {

    die('Erreur de connexion (' . $conn->connect_errno . ') ' . $conn->connect_error);    die('Erreur de connexion (' . $conn->connect_errno . ') ' . $conn->connect_error);

}}



// Exemple de requête : lire les données d'une table "utilisateurs"// Exemple de requête : lire les données d'une table "utilisateurs"

$sql = 'SELECT * FROM utilisateurs';$sql = 'SELECT * FROM utilisateurs';

$result = $conn->query($sql);$result = $conn->query($sql);



if ($result && $result->num_rows > 0) {if ($result && $result->num_rows > 0) {

    echo '<table border="1">';    echo '<table border="1">';

    echo '<tr>';    echo '<tr>';

    // Affiche les noms de colonnes    // Affiche les noms de colonnes

    while ($fieldinfo = $result->fetch_field()) {    while ($fieldinfo = $result->fetch_field()) {

        echo '<th>' . htmlspecialchars($fieldinfo->name) . '</th>';        echo '<th>' . htmlspecialchars($fieldinfo->name) . '</th>';

    }    }

    echo '</tr>';    echo '</tr>';

    // Affiche les lignes    // Affiche les lignes

    while ($row = $result->fetch_assoc()) {    while ($row = $result->fetch_assoc()) {

        echo '<tr>';        echo '<tr>';

        foreach ($row as $value) {        foreach ($row as $value) {

            echo '<td>' . htmlspecialchars($value) . '</td>';            echo '<td>' . htmlspecialchars($value) . '</td>';

        }        }

        echo '</tr>';        echo '</tr>';

    }    }

    echo '</table>';    echo '</table>';

} else {} else {

    echo 'Aucune donnée trouvée ou erreur dans la requête.';    echo 'Aucune donnée trouvée ou erreur dans la requête.';

}}



// Fermeture de la connexion// Fermeture de la connexion

$conn->close();$conn->close();

?>?>

