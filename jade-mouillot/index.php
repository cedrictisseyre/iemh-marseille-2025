<?php
// Initialisation des variables
$swim = $bike = $run = 0;
$total = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $swim = floatval($_POST["swim"]);
    $bike = floatval($_POST["bike"]);
    $run  = floatval($_POST["run"]);

    $total = $swim + $bike + $run;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculateur Triathlon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        img {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        input {
            width: 80%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background: #0073e6;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #005bb5;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Image triathlète -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Triathlete.jpg" alt="Triathlète">

        <h2>Calculateur de distance Triathlon</h2>

        <!-- Formulaire -->
        <form method="post" action="">
            <label>Natation (km) :</label><br>
            <input type="number" step="0.1" name="swim" value="<?= $swim ?>"><br>

            <label>Vélo (km) :</label><br>
            <input type="number" step="0.1" name="bike" value="<?= $bike ?>"><br>

            <label>Course à pied (km) :</label><br>
            <input type="number" step="0.1" name="run" value="<?= $run ?>"><br>

            <button type="submit">Calculer</button>
        </form>

        <!-- Résultat -->
        <?php if ($total !== null): ?>
            <div class="result">
                Distance totale : <?= $total ?> km
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
