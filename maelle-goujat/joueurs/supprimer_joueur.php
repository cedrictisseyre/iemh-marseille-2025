<?php
session_start();
require_once __DIR__ . '/../connexion.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID joueur invalide.');
}
$id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erreur de sécurité (CSRF).');
    }
    $stmt = $conn->prepare('DELETE FROM joueurs WHERE id_joueur = ?');
    $stmt->execute([$id]);
    header('Location: ../index.php?msg=suppression_joueur');
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un joueur</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Supprimer un joueur</h1>
        <p>Êtes-vous sûr de vouloir supprimer ce joueur (ID <?= htmlspecialchars($id) ?>) ?</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" style="background:#ef4444;">Oui, supprimer</button>
            <a href="../index.php" style="margin-left:1em;">Annuler</a>
        </form>
    </div>
</body>
</html>
