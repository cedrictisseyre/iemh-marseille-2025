<?php
// Inclure la connexion à la BDD
include 'db_connexion.php';

$page = $_GET['page'] ?? 'clubs';

function nav($active) {
    $tabs = [
        'clubs' => 'Clubs',
        'championnats' => 'Tournois',
        'karateka' => 'Karateka',
        'inscription' => 'Inscription'
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
        <form method="post" aria-label="Ajouter un club">
            <input type="hidden" name="add_club" value="1">
            <label for="nom_club">Nom du club :</label>
            <input type="text" id="nom_club" name="nom" placeholder="Nom du club" required>
            <label for="ville">Ville :</label>
            <input type="text" id="ville" name="ville" placeholder="Ville" required>
            <label for="pays">Pays :</label>
            <input type="text" id="pays" name="pays" placeholder="Pays" required>
            <label for="date_creation">Date de création :</label>
            <input type="date" id="date_creation" name="date_creation" required>
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
        <form method="post" aria-label="Ajouter un tournoi">
            <input type="hidden" name="add_champ" value="1">
            <label for="nom_championnat">Nom du tournoi :</label>
            <input type="text" id="nom_championnat" name="nom" placeholder="Nom du tournoi" required>
            <label for="lieu">Lieu :</label>
            <input type="text" id="lieu" name="lieu" placeholder="Lieu" required>
            <label for="date_championnat">Date :</label>
            <input type="date" id="date_championnat" name="date" required>
            <label for="type">Type :</label>
            <select id="type" name="type" required>
                <option value="Régional">Régional</option>
                <option value="National">National</option>
                <option value="International">International</option>
            </select>
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
        // Historique si karateka sélectionné
        if (isset($_GET['karateka_id'])) {
            $kid = intval($_GET['karateka_id']);
            $stmt = $pdo->prepare("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club WHERE k.id_karateka = ?");
            $stmt->execute([$kid]);
            $k = $stmt->fetch();
            if ($k) {
                echo "<h2>{$k['prenom']} {$k['nom']}</h2>";
                echo "<p><strong>Grade :</strong> {$k['grade']}<br><strong>Club :</strong> {$k['nom_club']}</p>";
                // Historique des participations
                $stmt2 = $pdo->prepare("SELECT p.*, ch.nom_championnat, ch.lieu, ch.date_championnat FROM participation p JOIN championnat ch ON p.id_championnat = ch.id_championnat WHERE p.id_karateka = ?");
                $stmt2->execute([$kid]);
                echo '<h3>Participations</h3><ul class="list">';
                while ($part = $stmt2->fetch()) {
                    echo "<li><strong>{$part['nom_championnat']}</strong> ({$part['lieu']}, {$part['date_championnat']})<br>Catégorie : {$part['categorie']}<br>Résultat : {$part['resultat']}</li>";
                }
                echo '</ul>';
                echo '<p><a href="gestion-karate.php?page=karateka">Retour à la liste</a></p>';
            } else {
                echo '<p>Karateka introuvable.</p>';
            }
        } else {
            // Liste karateka
            $stmt = $pdo->query("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club");
            echo '<h2>Liste des karateka</h2><ul class="list">';
            while ($k = $stmt->fetch()) {
                echo "<li><a href='gestion-karate.php?page=karateka&karateka_id={$k['id_karateka']}'><strong>{$k['prenom']} {$k['nom']}</strong></a> ({$k['grade']}) - Club : {$k['nom_club']}</li>";
            }
            echo '</ul>';
            // Liste clubs pour le formulaire
            $clubs = $pdo->query("SELECT id_club, nom_club FROM club")->fetchAll();
            ?>
            <h3>Ajouter un karateka</h3>
            <form method="post" aria-label="Ajouter un karateka">
                <input type="hidden" name="add_karateka" value="1">
                <label for="nom_karateka">Nom :</label>
                <input type="text" id="nom_karateka" name="nom" placeholder="Nom" required>
                <label for="prenom_karateka">Prénom :</label>
                <input type="text" id="prenom_karateka" name="prenom" placeholder="Prénom" required>
                <label for="date_naissance">Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
                <label for="sexe">Sexe :</label>
                <select id="sexe" name="sexe" required>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <label for="grade">Grade :</label>
                <input type="text" id="grade" name="grade" placeholder="Grade" required>
                <label for="id_club">Club :</label>
                <select id="id_club" name="id_club" required>
                    <?php foreach ($clubs as $club): ?>
                        <option value="<?= $club['id_club'] ?>"><?= htmlspecialchars($club['nom_club']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Ajouter</button>
            </form>
            <?php
        }
    }
    elseif ($page === 'inscription') {
        // Ajout d'une participation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_participation'])) {
            $stmt = $pdo->prepare("INSERT INTO participation (id_karateka, id_championnat, categorie, resultat) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id_karateka'], $_POST['id_championnat'], $_POST['categorie'], $_POST['resultat']
            ]);
            echo '<p class="success">Inscription enregistrée !</p>';
        }
        // Récupérer les listes déroulantes
        $karatekas = $pdo->query("SELECT id_karateka, nom, prenom FROM karateka")->fetchAll();
        $championnats = $pdo->query("SELECT id_championnat, nom_championnat FROM championnat")->fetchAll();
        ?>
        <h2>Inscrire un karateka à une compétition</h2>
        <form method="post">
            <input type="hidden" name="add_participation" value="1">
            <label>Karateka :</label>
            <select name="id_karateka" required>
                <?php foreach ($karatekas as $k): ?>
                    <option value="<?= $k['id_karateka'] ?>"><?= htmlspecialchars($k['prenom'].' '.$k['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Championnat :</label>
            <select name="id_championnat" required>
                <?php foreach ($championnats as $ch): ?>
                    <option value="<?= $ch['id_championnat'] ?>"><?= htmlspecialchars($ch['nom_championnat']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Catégorie :</label>
            <input type="text" name="categorie" placeholder="Catégorie" required>
            <label>Résultat :</label>
            <input type="text" name="resultat" placeholder="Résultat" required>
            <button type="submit">Inscrire</button>
        </form>
        <?php
    }
    ?>
    </div>
</div>
</body>
</html>
