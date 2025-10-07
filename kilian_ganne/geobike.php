<?php
session_start();
// Initialisation des tableaux en session
if (!isset($_SESSION['frames'])) $_SESSION['frames'] = [];
if (!isset($_SESSION['saddles'])) $_SESSION['saddles'] = [];
if (!isset($_SESSION['bars'])) $_SESSION['bars'] = [];
if (!isset($_SESSION['favorites'])) $_SESSION['favorites'] = [];

// Ajout d'un cadre
if (isset($_POST['add_frame'])) {
    $frame = [
        'type' => $_POST['type'],
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'size' => $_POST['size'],
        'release_date' => $_POST['release_date'],
        'price' => $_POST['price'],
        'stack' => $_POST['stack'],
        'reach' => $_POST['reach'],
        'seat_tube_angle' => $_POST['seat_tube_angle'],
        'fork_angle' => $_POST['fork_angle'],
        'head_tube_length' => $_POST['head_tube_length'],
        'fav' => false
    ];
    $_SESSION['frames'][] = $frame;
}
// Ajout d'une selle
if (isset($_POST['add_saddle'])) {
    $saddle = [
        'brand' => $_POST['saddle_brand'],
        'model' => $_POST['saddle_model'],
        'width' => $_POST['saddle_width'],
        'seatback' => $_POST['seatback'],
        'fav' => false
    ];
    $_SESSION['saddles'][] = $saddle;
}
// Ajout d'un cintre
if (isset($_POST['add_bar'])) {
    $bar = [
        'type' => $_POST['bar_type'],
        'width' => $_POST['bar_width'],
        'drop' => $_POST['bar_drop'],
        'reach' => $_POST['bar_reach'],
        'fav' => false
    ];
    $_SESSION['bars'][] = $bar;
}
// Mise en favori
if (isset($_POST['fav_type']) && isset($_POST['fav_index'])) {
    $type = $_POST['fav_type'];
    $index = intval($_POST['fav_index']);
    if (isset($_SESSION[$type][$index])) {
        $_SESSION[$type][$index]['fav'] = true;
        $_SESSION['favorites'][] = [
            'type' => $type,
            'data' => $_SESSION[$type][$index]
        ];
    }
}
// HTML + Onglets
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>GeoBike - Gestion des équipements vélo</title>
    <style>
        body { font-family: Arial; }
        .tabs { display: flex; margin-bottom: 1em; }
        .tab { padding: 10px 20px; cursor: pointer; background: #eee; border: 1px solid #ccc; }
        .tab.active { background: #fff; border-bottom: none; }
        .tab-content { border: 1px solid #ccc; padding: 1em; }
        .fav-btn { color: #e67e22; cursor: pointer; }
    </style>
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(e => e.style.display = 'none');
            document.getElementById(tab).style.display = 'block';
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }
        window.onload = function() { showTab('frames'); };
    </script>
</head>
<body>
    <h1>GeoBike - Gestion des équipements vélo</h1>
    <div class="tabs">
        <div class="tab" id="tab-frames" onclick="showTab('frames')">Cadres</div>
        <div class="tab" id="tab-saddles" onclick="showTab('saddles')">Selles</div>
        <div class="tab" id="tab-bars" onclick="showTab('bars')">Cintres</div>
        <div class="tab" id="tab-favorites" onclick="showTab('favorites')">Favoris</div>
    </div>
    <div id="frames" class="tab-content">
        <h2>Cadres</h2>
        <form method="post">
            <input name="type" placeholder="Type" required>
            <input name="brand" placeholder="Marque" required>
            <input name="model" placeholder="Modèle" required>
            <input name="size" placeholder="Taille" required>
            <input name="release_date" type="date" placeholder="Date de sortie" required>
            <input name="price" type="number" step="0.01" placeholder="Prix" required>
            <input name="stack" placeholder="Stack" required>
            <input name="reach" placeholder="Reach" required>
            <input name="seat_tube_angle" placeholder="Angle tube de selle" required>
            <input name="fork_angle" placeholder="Angle fourche" required>
            <input name="head_tube_length" placeholder="Longueur douille de direction" required>
            <button type="submit" name="add_frame">Ajouter cadre</button>
        </form>
        <h3>Cadres déjà renseignés :</h3>
        <ul>
        <?php foreach ($_SESSION['frames'] as $i => $f) { ?>
            <li>
                <?= htmlspecialchars($f['type']) ?>, <?= htmlspecialchars($f['brand']) ?>, <?= htmlspecialchars($f['model']) ?>, Taille: <?= htmlspecialchars($f['size']) ?>, Prix: <?= htmlspecialchars($f['price']) ?> €
                <form method="post" style="display:inline">
                    <input type="hidden" name="fav_type" value="frames">
                    <input type="hidden" name="fav_index" value="<?= $i ?>">
                    <?php if (!$f['fav']) { ?>
                        <button class="fav-btn" type="submit">Favori ★</button>
                    <?php } else { ?>
                        <span style="color:gold">★ Favori</span>
                    <?php } ?>
                </form>
            </li>
        <?php } ?>
        </ul>
    </div>
    <div id="saddles" class="tab-content" style="display:none">
        <h2>Selles</h2>
        <form method="post">
            <input name="saddle_brand" placeholder="Marque" required>
            <input name="saddle_model" placeholder="Modèle" required>
            <input name="saddle_width" placeholder="Largeur" required>
            <input name="seatback" placeholder="Seatback" required>
            <button type="submit" name="add_saddle">Ajouter selle</button>
        </form>
        <h3>Selles déjà renseignées :</h3>
        <ul>
        <?php foreach ($_SESSION['saddles'] as $i => $s) { ?>
            <li>
                <?= htmlspecialchars($s['brand']) ?>, <?= htmlspecialchars($s['model']) ?>, Largeur: <?= htmlspecialchars($s['width']) ?>, Seatback: <?= htmlspecialchars($s['seatback']) ?>
                <form method="post" style="display:inline">
                    <input type="hidden" name="fav_type" value="saddles">
                    <input type="hidden" name="fav_index" value="<?= $i ?>">
                    <?php if (!$s['fav']) { ?>
                        <button class="fav-btn" type="submit">Favori ★</button>
                    <?php } else { ?>
                        <span style="color:gold">★ Favori</span>
                    <?php } ?>
                </form>
            </li>
        <?php } ?>
        </ul>
    </div>
    <div id="bars" class="tab-content" style="display:none">
        <h2>Cintres</h2>
        <form method="post">
            <input name="bar_type" placeholder="Type de cintre" required>
            <input name="bar_width" placeholder="Largeur" required>
            <input name="bar_drop" placeholder="Drop" required>
            <input name="bar_reach" placeholder="Reach" required>
            <button type="submit" name="add_bar">Ajouter cintre</button>
        </form>
        <h3>Cintres déjà renseignés :</h3>
        <ul>
        <?php foreach ($_SESSION['bars'] as $i => $b) { ?>
            <li>
                <?= htmlspecialchars($b['type']) ?>, Largeur: <?= htmlspecialchars($b['width']) ?>, Drop: <?= htmlspecialchars($b['drop']) ?>, Reach: <?= htmlspecialchars($b['reach']) ?>
                <form method="post" style="display:inline">
                    <input type="hidden" name="fav_type" value="bars">
                    <input type="hidden" name="fav_index" value="<?= $i ?>">
                    <?php if (!$b['fav']) { ?>
                        <button class="fav-btn" type="submit">Favori ★</button>
                    <?php } else { ?>
                        <span style="color:gold">★ Favori</span>
                    <?php } ?>
                </form>
            </li>
        <?php } ?>
        </ul>
    </div>
    <div id="favorites" class="tab-content" style="display:none">
        <h2>Favoris</h2>
        <ul>
        <?php foreach ($_SESSION['favorites'] as $fav) {
            if ($fav['type'] === 'frames') {
                $f = $fav['data'];
                echo '<li>Cadre : '.htmlspecialchars($f['type']).', '.htmlspecialchars($f['brand']).', '.htmlspecialchars($f['model']).'</li>';
            } elseif ($fav['type'] === 'saddles') {
                $s = $fav['data'];
                echo '<li>Selle : '.htmlspecialchars($s['brand']).', '.htmlspecialchars($s['model']).'</li>';
            } elseif ($fav['type'] === 'bars') {
                $b = $fav['data'];
                echo '<li>Cintre : '.htmlspecialchars($b['type']).', Largeur: '.htmlspecialchars($b['width']).'</li>';
            }
        } ?>
        </ul>
    </div>
</body>
</html>
