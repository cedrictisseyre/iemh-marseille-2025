<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

// Page active (whitelist)
$allowed_pages = ['joueurs', 'stats', 'classement'];
$page = $_GET['page'] ?? 'joueurs';
if (!in_array($page, $allowed_pages, true)) {
    $page = 'joueurs';
}

// Fonction pour générer le menu
function nav(string $active): void {
    $tabs = [
        'joueurs' => 'Joueurs',
        'stats' => 'Statistiques',
        'classement'=> 'Classement'
    ];
    echo '<div class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a>";
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main>
        <?php
        // inclure la page correspondante (fichiers dans /pages)
        $pageFile = __DIR__ . "/pages/{$page}.php";
        if (file_exists($pageFile)) {
            include $pageFile;
        } else {
            echo "<p>Page introuvable.</p>";
        }
        ?>
    </main>
</div>
<footer>
    <p>&copy; <?= date('Y') ?> NFL Stats Analyzer - Projet académique</p>
</footer>
</body>
</html>
