<?php
// Exemple simple pour montrer l'utilité de PHP : génération dynamique de contenu

// Définir quelques variables dynamiques
$nom = "PHP";
$utilite = [
    "Générer des pages web dynamiques",
    "Gérer des formulaires et des bases de données",
    "Créer des sites interactifs",
    "Automatiser des tâches côté serveur"
];
$date = date("d/m/Y H:i:s");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Découverte de PHP</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 600px;
            margin: 40px auto;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            padding: 32px;
        }
        h1 {
            color: #2d6cdf;
            margin-bottom: 16px;
        }
        ul {
            margin: 16px 0;
        }
        .date {
            color: #888;
            font-size: 0.95em;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur une page générée avec <?php echo $nom; ?> !</h1>
        <p>
            <strong><?php echo $nom; ?></strong> est un langage de programmation côté serveur qui permet de :
        </p>
        <ul>
            <?php foreach ($utilite as $item): ?>
                <li><?php echo $item; ?></li>
            <?php endforeach; ?>
        </ul>
        <p>
            Cette page a été générée dynamiquement le <span class="date"><?php echo $date; ?></span>.
        </p>
    </div>
</body>
</html>