
<?php
session_start();
require_once __DIR__ . '/../connexion.php';
$message = '';
// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Erreur de sécurité (CSRF).';
        if ($isJson) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $message]);
            exit;
        }
    } else {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $poste = $_POST['poste'] ?? null;
        $id_equipe = $_POST['id_equipe'] ?? null;
        $sql = 'INSERT INTO joueurs (nom, prenom, poste, id_equipe) VALUES (?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($poste, ENT_QUOTES, 'UTF-8'),
            $id_equipe
        ]);
        $message = 'Joueur ajouté !';
        // Regénérer le token après soumission
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf_token = $_SESSION['csrf_token'];
        if ($isJson) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $message]);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un joueur</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1em;">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required autofocus>
            </div>
            <div style="margin-bottom:1em;">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div style="margin-bottom:1em;">
                <label for="poste">Poste :</label>
                <input type="text" id="poste" name="poste">
            </div>
            <div style="margin-bottom:1em;">
                <label for="id_equipe">ID équipe :</label>
                <input type="number" id="id_equipe" name="id_equipe">
            </div>
            <button type="submit" style="outline:2px solid #1a237e;outline-offset:2px;">Ajouter</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
