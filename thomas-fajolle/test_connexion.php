<?php
// Inclure le fichier de connexion
include 'connexion.php';

echo "<h2>Test de connexion à la base de données</h2>";

if ($pdo) {
    echo "<p style='color:green;'>✅ Connexion réussie !</p>";
} else {
    echo "<p style='color:red;'>❌ Connexion échouée...</p>";
}
?>
