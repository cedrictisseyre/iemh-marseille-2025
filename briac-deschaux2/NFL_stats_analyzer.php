<?php
require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

$page = $_GET['page'] ?? 'joueurs';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<header>
    <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="NFL Logo" class="logo">
    <h1>NFL STATS ANALYZER</h1>
</header>

<nav>
    <a href="?page=joueurs" class="<?= $page === 'joueurs' ? 'active' : '' ?>">Joueurs</a>
    <a href="?page=stats" class="<?= $page === 'stats' ? 'active' : '' ?>">Statistiques</a>
    <a href="?page=classement" class="<?= $page === 'classement' ? 'active' : '' ?>">Classement</a>
</nav>

<main>
    <?php
    $file = __DIR__ . "/views/{$page}.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<p>Page non trouvée.</p>";
    }
    ?>
</main>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer — Projet universitaire</p>
</footer>

<script>
function showMessage(msg, type='success') {
    const div = document.createElement('div');
    div.className = `alert ${type}`;
    div.textContent = msg;
    document.body.prepend(div);
    setTimeout(() => div.remove(), 3000);
}
</script>
</body>
</html>
