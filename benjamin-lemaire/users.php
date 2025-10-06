<?php
require_once 'config.php';

// Récupération des utilisateurs
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = $_POST['email'] ?? '';
    $nom_complet = ($prenom && $nom) ? (ucfirst($prenom) . ' ' . ucfirst(strtolower($nom))) : '';
    if ($nom_complet && $email) {
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, date_creation) VALUES (?, ?, CURDATE())");
            $stmt->execute([$nom_complet, $email]);
            header('Location: users.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
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
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Gestion des utilisateurs</h1>
    <div class="container">
        <h2>Ajouter un utilisateur</h2>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Prénom : <input type="text" name="prenom" required></label><br>
            <label>Nom : <input type="text" name="nom" required></label><br>
            <label>Email : <input type="email" name="email" required></label><br>
            <button type="submit">Ajouter</button>
        </form>
        <h2>Liste des utilisateurs</h2>
        <table class="table-users">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) : ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['date_creation']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="index.php">Retour à l'accueil</a></p>
    </div>
</body>
</html>
