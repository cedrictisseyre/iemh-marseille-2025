<?php
// Données des stations
$stations = [
    "Chamonix" => [
        "altitude" => "1035 m",
        "domaine" => "Vallée de Chamonix - Mont-Blanc",
        "popularite" => "⭐⭐⭐⭐⭐",
        "description" => "Station mythique au pied du Mont-Blanc, célèbre pour son ski hors-piste."
    ],
    "Morzine" => [
        "altitude" => "1000 m",
        "domaine" => "Portes du Soleil",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Station familiale avec un grand domaine relié aux Gets et Avoriaz."
    ],
    "La Clusaz" => [
        "altitude" => "1040 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Village authentique avec une ambiance savoyarde et de belles pistes variées."
    ],
    "Le Grand-Bornand" => [
        "altitude" => "952 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐",
        "description" => "Station conviviale connue pour ses paysages et ses activités nordiques."
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stations de ski - Haute-Savoie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .tabs { display: flex; margin-bottom: 10px; }
        .tab {
            padding: 10px 20px;
            background: #eee;
            margin-right: 5px;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
        }
        .tab.active { background: #3498db; color: white; }
        .content {
            border: 2px solid #3498db;
            border-radius: 0 8px 8px 8px;
            padding: 20px;
            display: none;
        }
        .content.active { display: block; }
    </style>
</head>
<body>

<h1>Stations de ski en Haute-Savoie</h1>

<div class="tabs">
    <?php foreach ($stations as $nom => $infos): ?>
        <div class="tab" onclick="openTab('<?= $nom ?>')"><?= $nom ?></div>
    <?php endforeach; ?>
</div>

<?php foreach ($stations as $nom => $infos): ?>
    <div id="<?= $nom ?>" class="content">
        <h2><?= $nom ?></h2>
        <p><strong>Altitude :</strong> <?= $infos['altitude'] ?></p>
        <p><strong>Domaine :</strong> <?= $infos['domaine'] ?></p>
        <p><strong>Popularité :</strong> <?= $infos['popularite'] ?></p>
        <p><em><?= $infos['description'] ?></em></p>
    </div>
<?php endforeach; ?>

<script>
function openTab(station) {
    // Masquer tous les contenus
    document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

    // Afficher l'onglet choisi
    document.getElementById(station).classList.add('active');
    event.target.classList.add('active');
}

// Activer le premier onglet par défaut
document.addEventListener("DOMContentLoaded", () => {
    document.querySelector(".tab").classList.add("active");
    document.querySelector(".content").classList.add("active");
});
</script>

</body>
</html>
