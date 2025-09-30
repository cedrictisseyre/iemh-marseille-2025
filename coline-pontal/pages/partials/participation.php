<?php
// Section Participation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_participation'])) {
    $stmt = $pdo->prepare("INSERT INTO participation (id_karateka, id_championnat, epreuve, sexe, equipe, categorie, resultat) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['id_karateka'], $_POST['id_championnat'], $_POST['epreuve'], $_POST['sexe'], $_POST['equipe'], $_POST['categorie'], $_POST['resultat']
    ]);
    echo '<p class="success">Participation enregistrée !</p>';
}

$karatekas = $pdo->query("SELECT id_karateka, nom, prenom, sexe FROM karateka")->fetchAll();
$championnats = $pdo->query("SELECT id_championnat, nom_championnat FROM championnat")->fetchAll();
?>
<h2>Ajouter une participation</h2>
<form method="post" aria-label="Ajouter une participation">
    <input type="hidden" name="add_participation" value="1">
    <label for="id_karateka">Karateka :</label>
    <select id="id_karateka" name="id_karateka" required>
        <?php foreach ($karatekas as $k): ?>
            <option value="<?= $k['id_karateka'] ?>"><?= htmlspecialchars($k['prenom'].' '.$k['nom']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="id_championnat">Championnat :</label>
    <select id="id_championnat" name="id_championnat" required>
        <?php foreach ($championnats as $ch): ?>
            <option value="<?= $ch['id_championnat'] ?>"><?= htmlspecialchars($ch['nom_championnat']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="epreuve">Épreuve :</label>
    <select id="epreuve" name="epreuve" required>
        <option value="Kumite">Kumite</option>
        <option value="Kata">Kata</option>
        <option value="Fukugo">Fukugo</option>
    </select>
    <label for="sexe">Sexe :</label>
    <select id="sexe" name="sexe" required>
        <option value="H">Homme</option>
        <option value="F">Femme</option>
    </select>
    <label for="equipe">Équipe :</label>
    <select id="equipe" name="equipe" required>
        <option value="Individuel">Individuel</option>
        <option value="Équipe">Équipe</option>
    </select>
    <label for="categorie">Catégorie :</label>
    <select id="categorie" name="categorie" required>
        <option value="-60kg">-60kg</option>
        <option value="-67kg">-67kg</option>
        <option value="-75kg">-75kg</option>
        <option value="-84kg">-84kg</option>
        <option value="+84kg">+84kg</option>
        <option value="-50kg">-50kg</option>
        <option value="-55kg">-55kg</option>
        <option value="-61kg">-61kg</option>
        <option value="-68kg">-68kg</option>
        <option value="+68kg">+68kg</option>
        <option value="Open">Open</option>
    </select>
    <label for="resultat">Résultat :</label>
    <select id="resultat" name="resultat" required>
        <option value="Or">Or</option>
        <option value="Argent">Argent</option>
        <option value="Bronze">Bronze</option>
        <option value="Quart de finale">Quart de finale</option>
        <option value="Huitième de finale">Huitième de finale</option>
        <option value="Éliminé">Éliminé</option>
        <option value="Abandon">Abandon</option>
        <option value="Disqualification">Disqualification</option>
        <option value="Non classé">Non classé</option>
    </select>
    <button type="submit">Ajouter</button>
</form>
