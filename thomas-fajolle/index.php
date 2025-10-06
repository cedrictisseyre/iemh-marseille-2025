<?php
// Afficher les erreurs pour le debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fichiers PHP côté serveur
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connexion.php';
require_once __DIR__ . '/includes/header.php';
?>

<section>
    <h2>Bienvenue sur le site de la Ligue 1</h2>
    <p>Explore les équipes, les joueurs et les résultats de la saison actuelle.</p>
    <img src="<?= base_path('assets/ligue1_logo.png') ?>" alt="Logo Ligue 1" style="width:200px;">
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
