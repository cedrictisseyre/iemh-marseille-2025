<?php
// Données enrichies sur les stations
$stations = [
    "Chamonix" => [
        "altitude" => "1035 m - 3842 m",
        "domaine" => "Vallée de Chamonix - Mont-Blanc",
        "popularite" => "⭐⭐⭐⭐⭐",
        "description" => "Station mythique au pied du Mont-Blanc, idéale pour les skieurs confirmés et amateurs de hors-piste.",
        "activites" => ["Ski alpin", "Hors-piste", "Randonnée", "Alpinisme"],
        "prix" => "€€€€ (plutôt élevé)",
        "avantages" => ["Panorama exceptionnel", "Ambiance internationale", "Ski extrême"],
        "inconvenients" => ["Prix élevés", "Station parfois bondée"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/8/84/Chamonix_valley.jpg"
    ],
    "Morzine" => [
        "altitude" => "1000 m - 2466 m",
        "domaine" => "Portes du Soleil",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Station familiale et conviviale avec un grand domaine relié à Avoriaz et aux Gets.",
        "activites" => ["Ski alpin", "Snowpark", "Raquettes", "VTT (été)"],
        "prix" => "€€€ (moyen)",
        "avantages" => ["Accès facile", "Idéal pour familles", "Grand domaine skiable"],
        "inconvenients" => ["Altitude assez basse (neige parfois limitée en fin de saison)"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/6/6e/Morzine_vue.jpg"
    ],
    "La Clusaz" => [
        "altitude" => "1040 m - 2600 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Station-village authentique, très appréciée pour son ambiance savoyarde et ses pistes variées.",
        "activites" => ["Ski alpin", "Ski de fond", "Biathlon", "Randonnées"],
        "prix" => "€€€ (moyen)",
        "avantages" => ["Charmant village", "Pistes variées", "Bonne gastronomie"],
        "inconvenients" => ["Moins vaste que d'autres domaines", "Trajets parfois longs depuis Genève"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/2/20/La_Clusaz_-_Panorama.jpg"
    ],
    "Le Grand-Bornand" => [
        "altitude" => "952 m - 2100 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐",
        "description" => "Station conviviale, réputée pour ses paysages préservés et son ambiance familiale.",
        "activites" => ["Ski alpin", "Ski de fond", "Raquettes", "Parapente"],
        "prix" => "€€ (plutôt abordable)",
        "avantages" => ["Ambiance familiale", "Prix corrects", "Activités nordiques"],
        "inconvenients" => ["Moins de choix pour les skieurs experts", "Altitude modeste"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/f/f6/Le_Grand-Bornand_village.jpg"
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stations de ski - Haute-Savoie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f7f7f7; }
        h1 { text-align: center; }
        .tabs { display: flex; justify-content: center; margin-bottom: 20px; }
        .tab {
            padding: 12px 20px;
            background: #eee;
            margin: 0 5px;
            border-radius: 10px 10px 0 0;
            cursor: pointer;
            transition: 0.3s;
        }
        .tab.active { background: #2c3e50; color: white; }
        .content {
            border: 2px solid #2c3e50;
            border-radius: 0 10px 10px 10px;
            padding: 20px;
            background: white;
            display: none;
            max-width: 800px;
            margin: auto;
        }
        .content.active { display: block; }
        img { max-width: 100%; border-radius: 10px; margin-bottom: 15px; }
        ul { margin: 5px 0; padding-left: 20px; }
        .tag { display: inline-block; padding: 5px 10px; background: #3498db; color: white; border-radius: 5px; margin: 2px; }
        .bad { color: red; }
        .good { color: green; }
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
        <img src="<?= $infos['image'] ?>" alt="Photo de <?= $nom ?>">
        <p><strong>Altitude :</strong> <?= $infos['altitude'] ?></p>
        <p><strong>Domaine skiable :</strong> <?= $infos['domaine'] ?></p>
        <p><strong>Popularité :</strong> <?= $infos['popularite'] ?></p>
        <p><strong>Description :</strong> <?= $infos['description'] ?></p>
        
        <p><strong>Activités proposées :</strong></p>
        <ul>
            <?php foreach ($infos['activites'] as $act): ?>
                <li><?= $act ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Prix moyen :</strong> <?= $infos['prix'] ?></p>

        <p><strong>✅ Avantages :</strong></p>
        <ul>
            <?php foreach ($infos['avantages'] as $plus): ?>
                <li class="good"><?= $plus ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>⚠️ Inconvénients :</strong></p>
        <ul>
            <?php foreach ($infos['inconvenients'] as $moins): ?>
                <li class="bad"><?= $moins ?></li>
            <?php endforeach; ?>
        </ul>
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
</html><?php
// Données enrichies sur les stations
$stations = [
    "Chamonix" => [
        "altitude" => "1035 m - 3842 m",
        "domaine" => "Vallée de Chamonix - Mont-Blanc",
        "popularite" => "⭐⭐⭐⭐⭐",
        "description" => "Station mythique au pied du Mont-Blanc, idéale pour les skieurs confirmés et amateurs de hors-piste.",
        "activites" => ["Ski alpin", "Hors-piste", "Randonnée", "Alpinisme"],
        "prix" => "€€€€ (plutôt élevé)",
        "avantages" => ["Panorama exceptionnel", "Ambiance internationale", "Ski extrême"],
        "inconvenients" => ["Prix élevés", "Station parfois bondée"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/8/84/Chamonix_valley.jpg"
    ],
    "Morzine" => [
        "altitude" => "1000 m - 2466 m",
        "domaine" => "Portes du Soleil",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Station familiale et conviviale avec un grand domaine relié à Avoriaz et aux Gets.",
        "activites" => ["Ski alpin", "Snowpark", "Raquettes", "VTT (été)"],
        "prix" => "€€€ (moyen)",
        "avantages" => ["Accès facile", "Idéal pour familles", "Grand domaine skiable"],
        "inconvenients" => ["Altitude assez basse (neige parfois limitée en fin de saison)"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/6/6e/Morzine_vue.jpg"
    ],
    "La Clusaz" => [
        "altitude" => "1040 m - 2600 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐⭐",
        "description" => "Station-village authentique, très appréciée pour son ambiance savoyarde et ses pistes variées.",
        "activites" => ["Ski alpin", "Ski de fond", "Biathlon", "Randonnées"],
        "prix" => "€€€ (moyen)",
        "avantages" => ["Charmant village", "Pistes variées", "Bonne gastronomie"],
        "inconvenients" => ["Moins vaste que d'autres domaines", "Trajets parfois longs depuis Genève"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/2/20/La_Clusaz_-_Panorama.jpg"
    ],
    "Le Grand-Bornand" => [
        "altitude" => "952 m - 2100 m",
        "domaine" => "Massif des Aravis",
        "popularite" => "⭐⭐⭐",
        "description" => "Station conviviale, réputée pour ses paysages préservés et son ambiance familiale.",
        "activites" => ["Ski alpin", "Ski de fond", "Raquettes", "Parapente"],
        "prix" => "€€ (plutôt abordable)",
        "avantages" => ["Ambiance familiale", "Prix corrects", "Activités nordiques"],
        "inconvenients" => ["Moins de choix pour les skieurs experts", "Altitude modeste"],
        "image" => "https://upload.wikimedia.org/wikipedia/commons/f/f6/Le_Grand-Bornand_village.jpg"
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stations de ski - Haute-Savoie</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f7f7f7; }
        h1 { text-align: center; }
        .tabs { display: flex; justify-content: center; margin-bottom: 20px; }
        .tab {
            padding: 12px 20px;
            background: #eee;
            margin: 0 5px;
            border-radius: 10px 10px 0 0;
            cursor: pointer;
            transition: 0.3s;
        }
        .tab.active { background: #2c3e50; color: white; }
        .content {
            border: 2px solid #2c3e50;
            border-radius: 0 10px 10px 10px;
            padding: 20px;
            background: white;
            display: none;
            max-width: 800px;
            margin: auto;
        }
        .content.active { display: block; }
        img { max-width: 100%; border-radius: 10px; margin-bottom: 15px; }
        ul { margin: 5px 0; padding-left: 20px; }
        .tag { display: inline-block; padding: 5px 10px; background: #3498db; color: white; border-radius: 5px; margin: 2px; }
        .bad { color: red; }
        .good { color: green; }
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
        <img src="<?= $infos['image'] ?>" alt="Photo de <?= $nom ?>">
        <p><strong>Altitude :</strong> <?= $infos['altitude'] ?></p>
        <p><strong>Domaine skiable :</strong> <?= $infos['domaine'] ?></p>
        <p><strong>Popularité :</strong> <?= $infos['popularite'] ?></p>
        <p><strong>Description :</strong> <?= $infos['description'] ?></p>
        
        <p><strong>Activités proposées :</strong></p>
        <ul>
            <?php foreach ($infos['activites'] as $act): ?>
                <li><?= $act ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Prix moyen :</strong> <?= $infos['prix'] ?></p>

        <p><strong>✅ Avantages :</strong></p>
        <ul>
            <?php foreach ($infos['avantages'] as $plus): ?>
                <li class="good"><?= $plus ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>⚠️ Inconvénients :</strong></p>
        <ul>
            <?php foreach ($infos['inconvenients'] as $moins): ?>
                <li class="bad"><?= $moins ?></li>
            <?php endforeach; ?>
        </ul>
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
</script>

</body>
</html>
