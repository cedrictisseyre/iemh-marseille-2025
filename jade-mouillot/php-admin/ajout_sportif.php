<?php
require_once 'db_connect.php';

// Récupérer les listes pour les clés étrangères
$club = $pdo->query("SELECT id, nom FROM club")->fetchAll();
$course = $pdo->query("SELECT id, nom FROM course")->fetchAll();
$discipline = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $id_course = $_POST['id_course'];
    $id_discipline = $_POST['id_discipline'];
    $id_club = $_POST['id_club'];

    $stmt = $pdo->prepare("INSERT INTO sportif (nom, id_course, id_discipline, id_club) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $id_course, $id_discipline, $id_club]);
    echo "Sportif ajouté !";
}
?>

<form method="post">
    Nom : <input type="text" name="nom" required><br>
    Course :
    <select name="id_course" required>
        <?php foreach ($course as $course): ?>
            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['nom']) ?></option>
        <?php endforeach; ?>
    </select><br>
    Discipline :
    <select name="id_discipline" required>
        <?php foreach ($discipline as $discipline): ?>
            <option value="<?= $discipline['id'] ?>"><?= htmlspecialchars($discipline['nom']) ?></option>
        <?php endforeach; ?>
    </select><br>
    Club :
    <select name="id_club" required>
        <?php foreach ($club as $club): ?>
            <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['nom']) ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Ajouter</button>
</form>
