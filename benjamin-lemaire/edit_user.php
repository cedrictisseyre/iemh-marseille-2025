<?php
require_once 'config.php';

// Récupération de l'utilisateur à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Utilisateur non trouvé.');
}

// Pré-remplir prénom et nom
$nom_parts = explode(' ', $user['nom'], 2);
$prenom = $nom_parts[0] ?? '';
$nom = $nom_parts[1] ?? '';

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = $_POST['email'] ?? '';
    $nom_complet = ($prenom && $nom) ? (ucfirst($prenom) . ' ' . ucfirst(strtolower($nom))) : '';
    if ($nom_complet && $email) {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id_utilisateur = ?");
            $stmt->execute([$nom_complet, $email, $id]);
            header('Location: users.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    } else {
        $error = 'Tous les champs sont obligatoires.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Modifier un utilisateur</h1>
    <div class="container">
        <?php if (!empty($error)) : ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required></label><br>
            <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required></label><br>
            <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
            <button type="submit">Enregistrer</button>
            <a href="users.php">Annuler</a>
        </form>
    </div>
</body>
</html>
