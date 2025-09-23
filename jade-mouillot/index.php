<?php
// ------------------------------
// CONFIGURATION
// ------------------------------
$apiKey = "VOTRE_CLE_API_ICI";  // Remplacer par votre clé World Triathlon API
$baseUrl = "https://api.triathlon.org/v1/events";

// ------------------------------
// VARIABLES DU FORMULAIRE
// ------------------------------
$swim = $bike = $run = 0;
$total = null;
$events = [];

// ------------------------------
// TRAITEMENT DU FORMULAIRE
// ------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $swim = floatval($_POST["swim"]);
    $bike = floatval($_POST["bike"]);
    $run  = floatval($_POST["run"]);

    $total = $swim + $bike + $run;

    // ------------------------------
    // APPEL API POUR RECUPERER EVENTS
    // ------------------------------
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $apiKey",
        "Accept: application/json"
    ]);

    $response = curl_exec($ch);
    if ($response !== false) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            $events = $data["data"] ?? [];
        }
    }
    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculateur Triathlon + Événements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        img {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        input {
            width: 80%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background: #0073e6;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background: #005bb5;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .events {
            text-align: left;
            margin-top: 30px;
        }
        .event {
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Image triathlète -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Triathlete.jpg" alt="Triathlète">

        <h2>Calculateur Triathlon</h2>

        <!-- Formulaire -->
        <form method="post" action="">
            <label>Natation (km) :</label><br>
            <input type="number" step="0.1" name="swim" value="<?= $swim ?>"><br>

            <label>Vélo (km) :</label><br>
            <input type="number" step="0.1" name="bike" value="<?= $bike ?>"><br>

            <label>Course à pied (km) :</label><br>
            <input type="number" step="0.1" name="run" value="<?= $run ?>"><br>

            <button type="submit">Calculer & Chercher</button>
        </form>

        <!-- Résultat calcul -->
        <?php if ($total !== null): ?>
            <div class="result">
                Distance totale : <?= $total ?> km
            </div>
        <?php endif; ?>

        <!-- Résultats API -->
        <?php if (!empty($events)): ?>
            <div class="events">
                <h3>Quelques événements triathlon (World Triathlon API)</h3>
                <?php foreach ($events as $event): ?>
                    <div class="event">
                        <strong><?= htmlspecialchars($event["name"]) ?></strong><br>
                        Date : <?= htmlspecialchars($event["start_date"] ?? "N/A") ?><br>
                        Lieu : <?= htmlspecialchars($event["country"]["name"] ?? "Inconnu") ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($total !== null): ?>
            <p>Aucun événement récupéré depuis l’API.</p>
        <?php endif; ?>
    </div>
</body>
</html>
