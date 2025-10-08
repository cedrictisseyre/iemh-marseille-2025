<?php
// Inclure le fichier de connexion
include 'connexion.php';

echo "<h2>Test avancé de connexion à la base de données</h2>";

// Vérifier la connexion
if ($pdo) {
    echo "<p style='color:green;'>✅ Connexion réussie !</p>";

    // Récupérer la liste des tables de la base
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($tables)) {
            echo "<h3>Tables présentes dans la base 'thomas_fajolle' :</h3>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:orange;'>⚠️ La base est vide, aucune table trouvée.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur lors de la récupération des tables : " . $e->getMessage() . "</p>";
    }

} else {
    echo "<p style='color:red;'>❌ Connexion échouée...</p>";
}
?>
