<?php
// ------------------------------
// CONFIGURATION
// ------------------------------
$apiKey = "VOTRE_CLE_API_ICI";  // Remplacer par votre clé World Triathlon API
$baseUrl = "https://api.triathlon.org/v1/events";

// Tolérance (en %) pour comparer les distances
$tol_swim = 20;  // ex: ±20%
$tol_bike = 15;
$tol_run  = 15;

// ------------------------------
// VARIABLES DU FORMULAIRE
// ------------------------------
$swim = $bike = $run = 0;
$total = null;
$events = [];
$filteredEvents = [];

// ------------------------------
// FONCTION DE VERIFICATION
// ------------------------------
function estProche($valeur, $cible, $tolerancePercent) {
    if ($valeur <= 0 || $cible <= 0) return false;
    $diff = abs($valeur - $cible);
    $tol = $cible * ($tolerancePercent / 100);
    return $diff <= $tol;
}

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

    // ------------------------------
    // FILTRAGE DES EVENEMENTS
    // ------------------------------
    foreach ($events as $event) {
        // ⚠️ L’API World Triathlon peut ne pas toujours fournir les distances précises.
        // Ici on suppose qu’on a "swim_distance", "bike_distance", "run_distance".
        if (isset($event["swim_distance"], $event["bike_distance"], $event["run_distance"])) {
            if (
                estProche($event["swim_distance"], $swim, $tol_swim) &&
                estProche($event["bike_distance"], $bike, $tol_bike) &&
                estProche($event["run_distance"],  $run,  $tol_run)
            ) {
                $filteredEvents[] = $event;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Triathlons similaires</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 600px; margin: auto; padding: 20px; }
        input, button { margin: 8px; padding: 8px; }
        .event { text-align: left; margin: 15px 0; padding: 10px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Calculateur Triathlon + Événements similaires</h2>

        <!-- Formulaire -->
        <form method="post" action="">
            Natation (km): <input type="number" step="0.1" name="swim" value="<?= $swim ?>"><br>
            Vélo (km): <input type="number" step="0.1" name="bike" value="<?= $bike ?>"><br>
            Course à pied (km): <input type="number" step="0.1" name="run" value="<?= $run ?>"><br>
            <button type="submit">Calculer & Chercher</button>
        </form>

        <!-- Résultat calcul -->
        <?php if ($total !== null): ?>
            <p><strong>Distance totale : <?= $total ?> km</strong></p>
        <?php endif; ?>

        <!-- Résultats filtrés -->
        <?php if (!empty($filteredEvents)): ?>
            <h3>Événements similaires trouvés :</h3>
            <?php foreach ($filteredEvents as $event): ?>
                <div class="event">
                    <strong><?= htmlspecialchars($event["name"]) ?></strong><br>
                    Date : <?= htmlspecialchars($event["start_date"] ?? "N/A") ?><br>
                    Lieu : <?= htmlspecialchars($event["country"]["name"] ?? "Inconnu") ?><br>
                    Distances : 🏊 <?= $event["swim_distance"] ?> km,
                                🚴 <?= $event["bike_distance"] ?> km,
                                🏃 <?= $event["run_distance"] ?> km
                </div>
            <?php endforeach; ?>
        <?php elseif ($total !== null): ?>
            <p>Aucun triathlon trouvé proche de vos distances.</p>
        <?php endif; ?>
    </div>
</body>
</html>
