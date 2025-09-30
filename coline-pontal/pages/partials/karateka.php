<?php
// Section Karateka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_karateka'])) {
    $stmt = $pdo->prepare("INSERT INTO karateka (nom, prenom, date_naissance, sexe, grade, id_club) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], $_POST['sexe'], $_POST['grade'], $_POST['id_club']
    ]);
    echo '<p class="success">Karateka ajouté !</p>';
}

if (isset($_GET['karateka_id'])) {
    $kid = intval($_GET['karateka_id']);
    $stmt = $pdo->prepare("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club WHERE k.id_karateka = ?");
    $stmt->execute([$kid]);
    $k = $stmt->fetch();
    if ($k) {
        echo "<h2>" . htmlspecialchars($k['prenom'] . ' ' . $k['nom']) . "</h2>";
        echo "<p><strong>Grade :</strong> " . htmlspecialchars($k['grade']) . "<br><strong>Club :</strong> " . htmlspecialchars($k['nom_club']) . "</p>";
        $stmt2 = $pdo->prepare("SELECT p.*, ch.nom_championnat, ch.lieu, ch.date_championnat, ch.type FROM participation p JOIN championnat ch ON p.id_championnat = ch.id_championnat WHERE p.id_karateka = ?");
        $stmt2->execute([$kid]);
        echo '<h3>Participations</h3><ul class="list">';
        while ($part = $stmt2->fetch()) {
            echo "<li><div><strong>" . htmlspecialchars($part['nom_championnat']) . "</strong> (<span class='meta'>" . htmlspecialchars($part['lieu'] . ', ' . $part['date_championnat'] . ', ' . $part['type']) . "</span>)</div><div class='meta'>Épreuve: " . htmlspecialchars($part['epreuve']) . " | Sexe: " . htmlspecialchars($part['sexe']) . " | Équipe: " . htmlspecialchars($part['equipe']) . " | Catégorie: " . htmlspecialchars($part['categorie']) . " | Résultat: " . htmlspecialchars($part['resultat']) . "</div></li>";
        }
        echo '</ul>';
        echo '<p><a href="gestion-karate.php?page=karateka">Retour à la liste</a></p>';
    } else {
        echo '<p>Karateka introuvable.</p>';
    }
} else {
    $stmt = $pdo->query("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club");
    echo '<h2>Liste des karateka</h2><ul class="list">';
    while ($k = $stmt->fetch()) {
        echo "<li><a href='gestion-karate.php?page=karateka&karateka_id={$k['id_karateka']}'><span style='font-size:1.3em; margin-right:0.5em;'>🥋</span><strong>" . htmlspecialchars($k['prenom'] . ' ' . $k['nom']) . "</strong></a> (" . htmlspecialchars($k['grade']) . ") - Club : " . htmlspecialchars($k['nom_club']) . "</li>";
    }
    echo '</ul>';
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

?>
