<?php
// Inclure la connexion à la BDD
include 'db_connexion.php';

$page = $_GET['page'] ?? 'clubs';

function nav($active) {
    $tabs = [
        'clubs' => 'Clubs',
        'championnats' => 'Tournois',
        'karateka' => 'Karateka'
    ];
    echo '<nav class="tabs">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a> ";
    }
    echo '</nav>';
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Karaté - Coline Pontal</title>
    <link rel="stylesheet" href="style-onglets.css">
</head>
<body>
<div class="container">
    <h1>Gestion Karaté</h1>
    <?php nav($page); ?>
    <div class="content">
    <?php
    if ($page === 'clubs') {
        // Ajout club
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_club'])) {
            $stmt = $pdo->prepare("INSERT INTO club (nom_club, ville, pays, date_creation) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nom'], $_POST['ville'], $_POST['pays'], $_POST['date_creation']
            ]);
            echo '<p class="success">Club ajouté !</p>';
        }
        // Liste clubs
        $stmt = $pdo->query("SELECT * FROM club");
        echo '<h2>Liste des clubs</h2><ul class="list">';
        while ($club = $stmt->fetch()) {
            echo "<li><strong>{$club['nom_club']}</strong> ({$club['ville']}, {$club['pays']})</li>";
        }
        echo '</ul>';
        ?>
        <h3>Ajouter un club</h3>
        <form method="post">
            <input type="hidden" name="add_club" value="1">
            <input type="text" name="nom" placeholder="Nom du club" required>
            <input type="text" name="ville" placeholder="Ville" required>
            <input type="text" name="pays" placeholder="Pays" required>
            <input type="date" name="date_creation" required>
            <button type="submit">Ajouter</button>
        </form>
        <?php
    } elseif ($page === 'championnats') {
        // Ajout tournoi
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_champ'])) {
            $stmt = $pdo->prepare("INSERT INTO championnat (nom_championnat, lieu, date_championnat, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nom'], $_POST['lieu'], $_POST['date'], $_POST['type']
            ]);
            echo '<p class="success">Tournoi ajouté !</p>';
        }
        // Liste tournois
        $stmt = $pdo->query("SELECT * FROM championnat");
        echo '<h2>Liste des tournois</h2><ul class="list">';
        while ($ch = $stmt->fetch()) {
            echo "<li><strong>{$ch['nom_championnat']}</strong> ({$ch['lieu']}, {$ch['date_championnat']})</li>";
        }
        echo '</ul>';
        ?>
        <h3>Ajouter un tournoi</h3>
        <form method="post">
            <input type="hidden" name="add_champ" value="1">
            <input type="text" name="nom" placeholder="Nom du tournoi" required>
            <input type="text" name="lieu" placeholder="Lieu" required>
            <input type="date" name="date" required>
            <input type="text" name="type" placeholder="Type" required>
            <button type="submit">Ajouter</button>
        </form>
        <?php
    } elseif ($page === 'karateka') {
        // Ajout karateka
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_karateka'])) {
            $stmt = $pdo->prepare("INSERT INTO karateka (nom, prenom, date_naissance, sexe, grade, id_club) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], $_POST['sexe'], $_POST['grade'], $_POST['id_club']
            ]);
            echo '<p class="success">Karateka ajouté !</p>';
        }
        // Liste karateka
        $stmt = $pdo->query("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club");
        echo '<h2>Liste des karateka</h2><ul class="list">';
        while ($k = $stmt->fetch()) {
            echo "<li><strong>{$k['prenom']} {$k['nom']}</strong> ({$k['grade']}) - Club : {$k['nom_club']}</li>";
        }
        echo '</ul>';
        // Liste clubs pour le formulaire
        $clubs = $pdo->query("SELECT id_club, nom_club FROM club")->fetchAll();
        ?>
        <h3>Ajouter un karateka</h3>
        <form method="post">
            <input type="hidden" name="add_karateka" value="1">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="date" name="date_naissance" required>
            <select name="sexe" required>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
            </select>
            <input type="text" name="grade" placeholder="Grade" required>
            <select name="id_club" required>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?= $club['id_club'] ?>"><?= htmlspecialchars($club['nom_club']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ajouter</button>
        </form>
        <?php
    }
    ?>
    </div>
</div>
</body>
</html>
