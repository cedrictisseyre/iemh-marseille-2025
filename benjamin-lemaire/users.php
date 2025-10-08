<?php
require_once 'config.php';

// Suppression d'un utilisateur et de ses données associées
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->beginTransaction();
    try {
        // Récupérer toutes les activités de l'utilisateur
        $stmt = $pdo->prepare("SELECT id FROM activites_sportives WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        $activites = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($activites) {
            $in = str_repeat('?,', count($activites) - 1) . '?';
            // Supprimer les performances liées aux activités
            $pdo->prepare("DELETE FROM performances WHERE id_activite IN ($in)")->execute($activites);
            // Supprimer les utilisations d'équipements liées aux activités
            $pdo->prepare("DELETE FROM utilisation_equipements WHERE id_activite IN ($in)")->execute($activites);
            // Supprimer les activités
            $pdo->prepare("DELETE FROM activites_sportives WHERE id IN ($in)")->execute($activites);
        }
        // Supprimer les équipements de l'utilisateur
        $pdo->prepare("DELETE FROM equipements WHERE id_utilisateur = ?")->execute([$id]);
        // Supprimer l'utilisateur
        $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?")->execute([$id]);
        $pdo->commit();
        header('Location: users.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupération des utilisateurs
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $nom_complet = ($prenom && $nom) ? (ucfirst($prenom) . ' ' . ucfirst(strtolower($nom))) : '';
    if ($nom_complet && $email && $mot_de_passe) {
        try {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, date_creation) VALUES (?, ?, ?, CURDATE())");
            $stmt->execute([$nom_complet, $email, $hash]);
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
            <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
            <button type="submit">Ajouter</button>
        </form>
        <h2>Liste des utilisateurs</h2>
        <table class="table-users">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) : ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['date_creation']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $u['id_utilisateur'] ?>">Modifier</a> |
                        <a href="#" onclick="confirmDelete('<?= addslashes($u['nom']) ?>', <?= $u['id_utilisateur'] ?>)">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="index.php">Retour à l'accueil</a></p>
    </div>
    <script>
    function confirmDelete(nom, id) {
        if (confirm('Supprimer définitivement l\'utilisateur ' + nom + ' et toutes ses données ?')) {
            window.location = 'users.php?delete=' + id;
        }
    }
    </script>
    </div>
</body>
</html>
