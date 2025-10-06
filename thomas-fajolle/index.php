<?php
require_once __DIR__ . '/config.php';       // 1️⃣ config global
require_once base_path('connexion.php');    // 2️⃣ connexion PDO
require_once base_path('includes/header.php'); // 3️⃣ header
?>

<section>
    <h2>Bienvenue sur le site de la Ligue 1</h2>
    <p>Explore les équipes, les joueurs et les résultats de la saison actuelle.</p>
    <img src="assets/images/ligue1_logo.png" alt="Logo Ligue 1" style="width:200px;">
</section>

<?php
require_once base_path('includes/footer.php'); // 5️⃣ footer
