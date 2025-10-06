<?php
// Affiche toutes les erreurs pour debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure les fichiers de configuration et connexion
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connexion.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mini interface interactive - Thomas Fajolle</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #333; padding: 20px; }
        h2, h3 { color: #2c3e50; }
        ul { list-style-type: none; padding-left: 0; }
        li { margin: 5px 0; }
        a { text-decoration: none; color: #2980b9; font-weight: bold; }
        a:hover { text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #2980b9; color: white; }
        tr:nth-child(even){background-color: #f2f2f2;}
        tr:hover {background-color: #ddd;}
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #555; }
    </style>
</head>
<body>

<h2>Mini interface interactive pour la base 'thomas_fajolle'</h2>

<?php
if (!$pdo) {
    die("<p class='error'>❌ Connexion échouée...</p>");
} else {
    echo "<p class='success'>✅ Connexion réussie !</p>";
}

// Lister les tables
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tables)) {
        echo "<h3>Tables présentes :</h3><ul>";
        foreach ($tables as $table) {
            echo "<li><a href='?table=$table'>$table</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='info'>⚠️ Aucune table trouvée.</p>";
    }

    // Afficher le contenu d'une table si sélectionnée
    if (isset($_GET['table'])) {
        $selectedTable = $_GET['table'];
        echo "<h3>Contenu de la table <strong>$selectedTable</strong> :</h3>";

        $stmt = $pdo->query("SELECT * FROM $selectedTable LIMIT 100");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            echo "<table><tr>";
            foreach (array_keys($rows[0]) as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>$cell</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='info'>Aucune donnée dans cette table.</p>";
        }
    }

} catch (PDOException $e) {
    echo "<p class='error'>Erreur : " . $e->getMessage() . "</p>";
}
?>

</body>
</html>
