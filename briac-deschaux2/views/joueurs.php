<?php
require_once __DIR__ . '/../services/helpers.php';

// Récupération des joueurs
$joueurs = fetch_players($pdo);
$positions = fetch_positions($pdo);
$teams = fetch_teams($pdo);
?>

<section class="section">
    <h2>Liste des joueurs</h2>

    <form method="POST" action="?page=joueurs">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-grid">
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="number" name="age" placeholder="Âge" required>
            <input type="number" name="taille_cm" placeholder="Taille (cm)" required>
            <input type="number" name="poids_kg" placeholder="Poids (kg)" required>
            <input type="number" name="annee_debut" placeholder="Année début" required>

            <select name="position_id" required>
                <option value="">Position</option>
                <?php foreach ($positions as $pos): ?>
                    <option value="<?= $pos['id'] ?>"><?= htmlspecialchars($pos['libelle']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="id_team" required>
                <option value="">Équipe</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= $team['id_team'] ?>"><?= htmlspecialchars($team['nom_team']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="add_player" class="btn">Ajouter</button>
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_player'])) {
        if (!validate_csrf()) {
            echo "<script>showMessage('Token CSRF invalide', 'error');</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO player (prenom, nom, age, taille_cm, poids_kg, annee_debut, position_id, id_team)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['prenom'], $_POST['nom'], $_POST['age'],
                $_POST['taille_cm'], $_POST['poids_kg'], $_POST['annee_debut'],
                $_POST['position_id'], $_POST['id_team']
            ]);
            header("Location: ?page=joueurs&added=1");
            exit;
        }
    }
    ?>

    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Position</th>
                <th>Âge</th>
                <th>Taille</th>
                <th>Poids</th>
                <th>Équipe</th>
                <th>Année début</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($joueurs as $j): ?>
            <tr>
                <td><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?></td>
                <td><?= htmlspecialchars($j['position_name']) ?></td>
                <td><?= htmlspecialchars($j['age']) ?></td>
                <td><?= htmlspecialchars($j['taille_cm']) ?> cm</td>
                <td><?= htmlspecialchars($j['poids_kg']) ?> kg</td>
                <td><?= htmlspecialchars($j['nom_team']) ?></td>
                <td><?= htmlspecialchars($j['annee_debut']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
