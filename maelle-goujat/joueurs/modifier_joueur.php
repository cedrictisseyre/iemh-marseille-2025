<?php
session_start();
require_once __DIR__ . '/../connexion.php';
$message = '';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID joueur invalide.');
}
$id = (int)$_GET['id'];
// Récupération des infos du joueur
$stmt = $conn->prepare('SELECT * FROM joueurs WHERE id_joueur = ?');
$stmt->execute([$id]);
$joueur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$joueur) die('Joueur introuvable.');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Erreur de sécurité (CSRF).';
    } else {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $poste = $_POST['poste'] ?? null;
        $id_equipe = $_POST['id_equipe'] ?? null;
        $sql = 'UPDATE joueurs SET nom=?, prenom=?, poste=?, id_equipe=? WHERE id_joueur=?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($poste, ENT_QUOTES, 'UTF-8'),
            $id_equipe,
            $id
        ]);
        $message = 'Joueur modifié !';
        // Regénérer le token après soumission
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf_token = $_SESSION['csrf_token'];
        // Rafraîchir les données
        $stmt = $conn->prepare('SELECT * FROM joueurs WHERE id_joueur = ?');
        $stmt->execute([$id]);
        $joueur = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un joueur</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Modifier un joueur</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1em;">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($joueur['nom']) ?>" required autofocus>
            </div>
            <div style="margin-bottom:1em;">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($joueur['prenom']) ?>" required>
            </div>
            <div style="margin-bottom:1em;">
                <label for="poste">Poste :</label>
                <input type="text" id="poste" name="poste" value="<?= htmlspecialchars($joueur['poste']) ?>">
            </div>
            <div style="margin-bottom:1em;">
                <label for="id_equipe">ID équipe :</label>
                <input type="number" id="id_equipe" name="id_equipe" value="<?= htmlspecialchars($joueur['id_equipe']) ?>">
            </div>
            <button type="submit">Enregistrer</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
