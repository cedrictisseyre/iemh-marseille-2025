<?php
require_once 'connexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$uid = $_SESSION['user']['id'];
$profile = null;
if (isset($conn) && $conn instanceof PDO) {
    $stmt = $conn->prepare('SELECT e.* FROM eleves e WHERE e.user_id = :uid LIMIT 1');
    $stmt->execute([':uid' => $uid]);
    $profile = $stmt->fetch();
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profil - Mastère IHME</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Mon profil</h1>
    <p><a href="index.php">Retour à l'accueil</a> — <a href="index.php?action=logout">Se déconnecter</a></p>

    <?php if (!$profile): ?>
        <div class="alert alert-warning">Profil introuvable. Votre compte existe mais aucune fiche élève liée n'a été trouvée.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($profile['prenom'] . ' ' . $profile['nom']) ?></h5>
                <p>Identifiant utilisateur : <?= htmlspecialchars($profile['user_id']) ?></p>
                <p>Inscrit le : <?= htmlspecialchars($profile['created_at'] ?? '') ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
