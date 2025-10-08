<?php
session_start();
require_once __DIR__ . '/../connexion.php';
$message = '';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID équipe invalide.');
}
$id = (int)$_GET['id'];
// Récupération des infos de l'équipe
$stmt = $conn->prepare('SELECT * FROM equipes WHERE id_equipe = ?');
$stmt->execute([$id]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$equipe) die('Équipe introuvable.');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Erreur de sécurité (CSRF).';
    } else {
        $nom = $_POST['nom_equipe'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $pays = $_POST['pays'] ?? '';
        $sql = 'UPDATE equipes SET nom_equipe=?, ville=?, pays=? WHERE id_equipe=?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($ville, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($pays, ENT_QUOTES, 'UTF-8'),
            $id
        ]);
        $message = 'Équipe modifiée !';
        // Regénérer le token après soumission
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf_token = $_SESSION['csrf_token'];
        // Rafraîchir les données
        $stmt = $conn->prepare('SELECT * FROM equipes WHERE id_equipe = ?');
        $stmt->execute([$id]);
        $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une équipe</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Modifier une équipe</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1em;">
                <label for="nom_equipe">Nom de l'équipe :</label>
                <input type="text" id="nom_equipe" name="nom_equipe" value="<?= htmlspecialchars($equipe['nom_equipe']) ?>" required autofocus>
            </div>
            <div style="margin-bottom:1em;">
                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($equipe['ville']) ?>">
            </div>
            <div style="margin-bottom:1em;">
                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays" value="<?= htmlspecialchars($equipe['pays']) ?>">
            </div>
            <button type="submit">Enregistrer</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
