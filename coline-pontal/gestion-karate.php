<?php
// Contrôleur léger placé à la racine du dossier coline-pontal
include __DIR__ . '/includes/db_connexion.php';

$page = $_GET['page'] ?? 'home';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
render_nav($page);
echo '<div class="content">';

// If DB connection failed, show a notice but continue rendering so CSS is loaded
if (!isset($pdo) || $pdo === null) {
    echo '<p class="success" style="color:#b91c1c;">Attention : connexion à la base de données indisponible. Certaines fonctionnalités sont désactivées.</p>';
}

// Charger le partial correspondant
$partial = __DIR__ . '/pages/partials/' . $page . '.php';
if (file_exists($partial)) {
    include $partial;
} else {
    echo '<p>Page introuvable.</p>';
}

echo '</div>';
// footer retiré volontairement

?>
