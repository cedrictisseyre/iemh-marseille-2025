<?php
require_once __DIR__ . '/../connexion_database.php';
$runners = $conn->query("SELECT * FROM runners")->fetchAll(PDO::FETCH_ASSOC);
$searched_runner = null;
if (isset($_POST['search_runner'])) {
    $search = trim($_POST['search_name']);
    $stmt = $conn->prepare("SELECT * FROM runners WHERE name = ? LIMIT 1");
    $stmt->execute([$search]);
    $searched_runner = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (isset($_POST['delete_runner'])) {
    $stmt = $conn->prepare("DELETE FROM runners WHERE id = ?");
    $stmt->execute([intval($_POST['runner_id'])]);
    $searched_runner = null;
}
$edit_runner = null;
if (isset($_POST['edit_runner'])) {
    $stmt = $conn->prepare("SELECT * FROM runners WHERE id = ?");
    $stmt->execute([intval($_POST['runner_id'])]);
    $edit_runner = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (isset($_POST['update_runner'])) {
    $stmt = $conn->prepare("UPDATE runners SET name = ?, country = ?, birth = ?, team = ?, gender = ?, info = ? WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['country'],
        $_POST['birth'],
        $_POST['team'],
        $_POST['gender'],
        $_POST['info'],
        intval($_POST['runner_id'])
    ]);
    $edit_runner = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche coureur - GTWS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <img src="../img/logo_gtws.jpg" alt="Logo GTWS" class="logo">
        <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Golden World Trail Series</span>
    </header>
    <nav class="tabs">
        <a class="tab" href="general.php">Classement général</a>
        <a class="tab" href="stages.php">Résultats par manche</a>
        <a class="tab active" href="search.php">Recherche coureur</a>
        <a class="tab" href="add.php">Ajouter un coureur</a>
    </nav>
    <div style="width:100%;height:220px;background:url('../img/course2.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
    <div class="tab-content">
        <h2>Recherche d'un coureur</h2>
        <form method="post" autocomplete="off">
            <input name="search_name" id="search_name" placeholder="Nom du coureur" required oninput="showSuggestions()">
            <button type="submit" name="search_runner">Rechercher</button>
        </form>
        <ul id="suggestions" style="list-style:none;padding-left:0;"></ul>
        <script>
        const runners = <?= json_encode($runners) ?>;
        function showSuggestions() {
            const input = document.getElementById('search_name').value.toLowerCase();
            const list = document.getElementById('suggestions');
            list.innerHTML = '';
            if (input.length < 1) return;
            runners.forEach(r => {
                if (r.name.toLowerCase().includes(input)) {
                    const li = document.createElement('li');
                    li.textContent = r.name;
                    li.style.cursor = 'pointer';
                    li.onclick = function() {
                        document.getElementById('search_name').value = r.name;
                        list.innerHTML = '';
                    };
                    list.appendChild(li);
                }
            });
        }
        </script>
        <?php if ($searched_runner) { ?>
            <h3>Informations du coureur :</h3>
            <div style="background:#fff;border:2px solid #e67e22;border-radius:10px;padding:1em;max-width:400px;margin:1em auto;box-shadow:0 2px 8px rgba(230,126,34,0.08);">
                <ul style="list-style:none;padding-left:0;">
                    <li><strong>Nom :</strong> <?= htmlspecialchars($searched_runner['name']) ?></li>
                    <li><strong>Pays :</strong> <?= htmlspecialchars($searched_runner['country']) ?></li>
                    <li><strong>Date de naissance :</strong> <?= htmlspecialchars($searched_runner['birth']) ?></li>
                    <li><strong>Équipe :</strong> <?= htmlspecialchars($searched_runner['team']) ?></li>
                    <li><strong>Sexe :</strong> <?= htmlspecialchars($searched_runner['gender']) ?></li>
                    <li><strong>Infos :</strong> <?= htmlspecialchars($searched_runner['info']) ?></li>
                </ul>
                <form method="post" style="display:inline">
                    <input type="hidden" name="runner_id" value="<?= $searched_runner['id'] ?>">
                    <button type="submit" name="edit_runner">Modifier</button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce coureur ?');">
                    <input type="hidden" name="runner_id" value="<?= $searched_runner['id'] ?>">
                    <button type="submit" name="delete_runner">Supprimer</button>
                </form>
            </div>
        <?php } elseif (isset($_POST['search_runner'])) { ?>
            <p style="color:red">Coureur non trouvé.</p>
        <?php } ?>
        <?php if ($edit_runner) { ?>
            <h3>Modifier la fiche du coureur</h3>
            <form method="post" style="background:#fff;padding:1em;border:1px solid #e67e22;border-radius:8px;max-width:400px;margin:20px auto;">
                <input type="hidden" name="runner_id" value="<?= $edit_runner['id'] ?>">
                <label>Nom :</label>
                <input name="name" value="<?= htmlspecialchars($edit_runner['name']) ?>" required><br>
                <label>Pays :</label>
                <input name="country" value="<?= htmlspecialchars($edit_runner['country']) ?>" required><br>
                <label>Date de naissance :</label>
                <input name="birth" type="date" value="<?= htmlspecialchars($edit_runner['birth']) ?>"><br>
                <label>Équipe :</label>
                <input name="team" value="<?= htmlspecialchars($edit_runner['team']) ?>"><br>
                <label>Sexe :</label>
                <select name="gender" required>
                    <option value="Homme" <?= $edit_runner['gender'] == 'Homme' ? 'selected' : '' ?>>Homme</option>
                    <option value="Femme" <?= $edit_runner['gender'] == 'Femme' ? 'selected' : '' ?>>Femme</option>
                    <option value="Autre" <?= $edit_runner['gender'] == 'Autre' ? 'selected' : '' ?>>Autre</option>
                </select><br>
                <label>Infos :</label>
                <input name="info" value="<?= htmlspecialchars($edit_runner['info']) ?>"><br>
                <button type="submit" name="update_runner">Valider la modification</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
