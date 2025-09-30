<?php
// Contrôleur léger: inclut la connexion, header, nav, partials et footer
include __DIR__ . '/../includes/db_connexion.php';

$page = $_GET['page'] ?? 'clubs';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
render_nav($page);
echo '<div class="content">';

// Charger le partial correspondant
$partial = __DIR__ . '/partials/' . $page . '.php';
if (file_exists($partial)) {
    include $partial;
} else {
    echo '<p>Page introuvable.</p>';
}

echo '</div>';
include __DIR__ . '/../includes/footer.php';

?>
