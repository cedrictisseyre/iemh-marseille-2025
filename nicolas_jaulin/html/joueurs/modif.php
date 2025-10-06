<?php
require_once 'db_connect.php';
$message = '';
$id = $_GET['id'] ?? '';
$equipes = $pdo->query('SELECT id_equipe, nom_equipe FROM Equipes')->fetchAll();
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Joueurs WHERE id_joueur = ?');
    $stmt->execute([$id]);
    $joueur = $stmt->fetch();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $date_naissance = $_POST['date_naissance'] ?? '';
        $poste = $_POST['poste'] ?? '';
        $id_equipe = $_POST['id_equipe'] !== '' ? $_POST['id_equipe'] : null;
        if ($nom) {
            $stmt = $pdo->prepare('UPDATE Joueurs SET nom = ?, prenom = ?, date_naissance = ?, poste = ?, id_equipe = ? WHERE id_joueur = ?');
            $stmt->execute([$nom, $prenom, $date_naissance, $poste, $id_equipe, $id]);
            $message = 'Joueur modifié !';
        } else {
            $message = 'Le nom est obligatoire.';
        }
    }
} else {
    header('Location: liste_joueurs.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un joueur</title>
</head>
<body>
    <h1>Modifier un joueur</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($joueur['nom']) ?>" required></label><br>
        <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($joueur['prenom']) ?>"></label><br>
        <label>Date de naissance : <input type="date" name="date_naissance" value="<?= htmlspecialchars($joueur['date_naissance']) ?>"></label><br>
        <label>Poste : <input type="text" name="poste" value="<?= htmlspecialchars($joueur['poste']) ?>"></label><br>
        <label>Équipe :
            <select name="id_equipe">
                <option value="">Aucune</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>" <?= $joueur['id_equipe'] == $equipe['id_equipe'] ? 'selected' : '' ?>><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Modifier</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_joueurs.php">Retour à la liste</a>
</body>
</html>