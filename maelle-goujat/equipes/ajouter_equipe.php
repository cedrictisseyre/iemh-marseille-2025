<?php
session_start();
require_once __DIR__ . '/../connexion.php';
$message = '';
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
        $nom = $_POST['nom_equipe'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $pays = $_POST['pays'] ?? '';
        $sql = 'INSERT INTO equipes (nom_equipe, ville, pays) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($ville, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($pays, ENT_QUOTES, 'UTF-8')
        ]);
        $message = 'Équipe ajoutée !';
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
    <title>Ajouter une équipe</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une équipe</h1>
        <?php if (!empty($message)) : ?>
            <div class="error" style="color: #22c55e;"> <?= htmlspecialchars($message) ?> </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1em;">
                <label for="nom_equipe">Nom de l'équipe :</label>
                <input type="text" id="nom_equipe" name="nom_equipe" required autofocus>
            </div>
            <div style="margin-bottom:1em;">
                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville">
            </div>
            <div style="margin-bottom:1em;">
                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays">
            </div>
            <button type="submit" style="outline:2px solid #1a237e;outline-offset:2px;">Ajouter</button>
        </form>
        <p><a href="../index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
