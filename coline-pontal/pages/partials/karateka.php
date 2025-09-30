<?php
// Section Karateka
$db_ok = isset($pdo) && $pdo !== null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_karateka'])) {
    if ($db_ok) {
        $stmt = $pdo->prepare("INSERT INTO karateka (nom, prenom, date_naissance, sexe, grade, id_club) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], $_POST['sexe'], $_POST['grade'], $_POST['id_club']
        ]);
        echo '<p class="success">Karateka ajouté !</p>';
    } else {
        echo '<p class="success" style="color:#b91c1c;">Impossible : pas de connexion DB.</p>';
    }
}

if (isset($_GET['karateka_id'])) {
    $kid = intval($_GET['karateka_id']);
    $stmt = $db_ok ? $pdo->prepare("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club WHERE k.id_karateka = ?") : null;
    if ($stmt) $stmt->execute([$kid]);
    $k = $stmt->fetch();
    if ($k) {
        echo "<h2>" . esc($k['prenom'] . ' ' . $k['nom']) . "</h2>";
        echo "<p><strong>Grade :</strong> " . esc($k['grade']) . "<br><strong>Club :</strong> " . esc($k['nom_club']) . "</p>";
        if ($db_ok) {
            $stmt2 = $pdo->prepare("SELECT p.*, ch.nom_championnat, ch.lieu, ch.date_championnat, ch.type FROM participation p JOIN championnat ch ON p.id_championnat = ch.id_championnat WHERE p.id_karateka = ?");
            $stmt2->execute([$kid]);
            echo '<h3>Participations</h3><ul class="list">';
            while ($part = $stmt2->fetch()) {
                echo "<li><div><strong>" . esc($part['nom_championnat']) . "</strong> (<span class='meta'>" . esc($part['lieu'] . ', ' . $part['date_championnat'] . ', ' . $part['type']) . "</span>)</div><div class='meta'>Épreuve: " . esc($part['epreuve']) . " | Sexe: " . esc($part['sexe']) . " | Équipe: " . esc($part['equipe']) . " | Catégorie: " . esc($part['categorie']) . " | Résultat: " . esc($part['resultat']) . "</div></li>";
            }
            echo '</ul>';
        } else {
            echo '<p class="meta">Aucune participation affichable (connexion DB manquante).</p>';
        }
        echo '<p><a href="gestion-karate.php?page=karateka">Retour à la liste</a></p>';
    } else {
        echo '<p>Karateka introuvable.</p>';
    }
} else {
    $stmt = $db_ok ? $pdo->query("SELECT k.*, c.nom_club FROM karateka k JOIN club c ON k.id_club = c.id_club") : null;
    echo '<h2 class="title-with-icons"><svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img"><path d="M4 3c0 0 4 2 8 2s8-2 8-2v3c0 2-2 3-2 3s0 4-1 6-2 3-5 3-4-1-5-3-1-6-1-6-2-1-2-3V3z" fill="currentColor"/></svg>Liste des karatekas<svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img"><path d="M4 3c0 0 4 2 8 2s8-2 8-2v3c0 2-2 3-2 3s0 4-1 6-2 3-5 3-4-1-5-3-1-6-1-6-2-1-2-3V3z" fill="currentColor"/></svg></h2><ul class="list">';
    if ($stmt) {
        while ($k = $stmt->fetch()) {
            $label = formatName($k['prenom'], $k['nom']);
            echo "<li><a href='gestion-karate.php?page=karateka&karateka_id={$k['id_karateka']}'><strong>" . esc($label) . "</strong></a> (" . esc($k['grade']) . ") - Club : " . esc($k['nom_club']) . "</li>";
        }
    } else {
        echo "<li class='meta'>Aucun karateka listé (connexion DB manquante).</li>";
    }
    echo '</ul>';
    $clubs = $db_ok ? $pdo->query("SELECT id_club, nom_club FROM club")->fetchAll() : [];
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
                <option value="<?= $club['id_club'] ?>"><?= esc($club['nom_club']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Ajouter</button>
    </form>
    <?php
}

?>
