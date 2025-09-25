<?php
require_once 'db_connect.php';
$message = '';
// Récupérer les équipes pour la liste déroulante
$equipes = $pdo->query('SELECT id_equipe, nom_equipe FROM Equipes')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $poste = $_POST['poste'] ?? '';
    $id_equipe = $_POST['id_equipe'] !== '' ? $_POST['id_equipe'] : null;
    if ($nom) {
        $stmt = $pdo->prepare('INSERT INTO Joueurs (nom, prenom, date_naissance, poste, id_equipe) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nom, $prenom, $date_naissance, $poste, $id_equipe]);
        $message = 'Joueur ajouté !';
    } else {
        $message = 'Le nom est obligatoire.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur</title>
</head>
<body>
    <h1>Ajouter un joueur</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Prénom : <input type="text" name="prenom"></label><br>
        <label>Date de naissance : <input type="date" name="date_naissance"></label><br>
        <label>Poste : <input type="text" name="poste"></label><br>
        <label>Équipe :
            <select name="id_equipe">
                <option value="">Aucune</option>
                <?php foreach ($equipes as $equipe): ?>
                    <option value="<?= $equipe['id_equipe'] ?>"><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="liste_joueurs.php">Retour à la liste</a>
</body>
</html>