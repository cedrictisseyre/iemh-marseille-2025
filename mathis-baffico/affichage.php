<?php
require_once 'connexion.php';

// Récupérer la liste des utilisateurs
$utilisateurs = $pdo->query("SELECT id, nom FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$id_user = $_GET['user'] ?? null;
$historique = [];

if ($id_user) {
    $stmt = $pdo->prepare("SELECT c.*, u.nom 
                           FROM calculs c
                           JOIN utilisateurs u ON c.utilisateur_id = u.id
                           WHERE u.id = :id
                           ORDER BY c.date_calcul DESC");
    $stmt->execute(['id' => $id_user]);
    $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des calculs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        table { border-collapse: collapse; width: 100%; margin-top: 1em; }
        th, td { border: 1px solid #ccc; padding: 0.5em; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
<h1>Historique des calculs</h1>

<form method="get">
    <label for="user">Choisir un utilisateur :</label>
    <select name="user" id="user" onchange="this.form.submit()">
        <option value="">-- Choisir --</option>
        <?php foreach ($utilisateurs as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($id_user == $u['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($id_user && $historique): ?>
    <h2>Historique pour <?= htmlspecialchars($historique[0]['nom']) ?></h2>
    <table>
        <tr>
            <th>Date</th>
            <th>NAP</th>
            <th>Niveau activité</th>
            <th>MB</th>
            <th>DEJ</th>
        </tr>
        <?php foreach ($historique as $calc): ?>
            <tr>
                <td><?= $calc['date_calcul'] ?></td>
                <td><?= $calc['nap'] ?></td>
                <td><?= $calc['niveau_activite'] ?></td>
                <td><?= round($calc['metabolisme_base'], 2) ?> kcal</td>
                <td><?= round($calc['dej'], 2) ?> kcal</td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif ($id_user): ?>
    <p>Aucun calcul enregistré pour cet utilisateur.</p>
<?php endif; ?>

<br>
<a href="formulaire.php">Retour au formulaire</a>
</body>
</html>