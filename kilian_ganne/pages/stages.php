<?php
require_once __DIR__ . '/../connexion_database.php';
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
$runners = $conn->query("SELECT * FROM runners")->fetchAll(PDO::FETCH_ASSOC);
$results = $conn->query("SELECT results.*, courses.name AS course_name, runners.name AS runner_name FROM results JOIN courses ON results.course_id = courses.id JOIN runners ON results.runner_id = runners.id")->fetchAll(PDO::FETCH_ASSOC);
if (isset($_POST['add_result'])) {
    $course_id = intval($_POST['course_id']);
    $runner_id = intval($_POST['runner_id']);
    $stmt = $conn->prepare("INSERT INTO results (course_id, runner_id, rank, time) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $course_id,
        $runner_id,
        $_POST['rank'],
        $_POST['time']
    ]);
}
if (isset($_POST['delete_result'])) {
    $stmt = $conn->prepare("DELETE FROM results WHERE id = ?");
    $stmt->execute([intval($_POST['result_id'])]);
}
$edit_result = null;
if (isset($_POST['edit_result'])) {
    $stmt = $conn->prepare("SELECT * FROM results WHERE id = ?");
    $stmt->execute([intval($_POST['result_id'])]);
    $edit_result = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (isset($_POST['update_result'])) {
    $stmt = $conn->prepare("UPDATE results SET course_id = ?, runner_id = ?, rank = ?, time = ? WHERE id = ?");
    $stmt->execute([
        intval($_POST['course_id']),
        intval($_POST['runner_id']),
        $_POST['rank'],
        $_POST['time'],
        intval($_POST['result_id'])
    ]);
    unset($_POST['edit_result']);
    $edit_result = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats par manche - GTWS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <img src="../img/logo_gtws.jpg" alt="Logo GTWS" class="logo">
        <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Golden World Trail Series</span>
    </header>
    <nav class="tabs">
        <a class="tab" href="general.php">Classement général</a>
        <a class="tab active" href="stages.php">Résultats par manche</a>
        <a class="tab" href="search.php">Recherche coureur</a>
        <a class="tab" href="add.php">Ajouter un coureur</a>
    </nav>
    <div style="width:100%;height:220px;background:url('../img/course3.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
    <div class="tab-content">
        <h2>Résultats par manche</h2>
        <form method="post">
            <select name="course_id" required>
                <option value="">Sélectionner la course</option>
                <?php foreach ($courses as $c) { ?>
                    <option value="<?= $c['id'] ?>"
                        <?= (isset($_POST['filter_course_id']) && $_POST['filter_course_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php } ?>
            </select>
            <select name="runner_id" required>
                <option value="">Coureur</option>
                <?php foreach ($runners as $r) { ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php } ?>
            </select>
            <input name="rank" type="number" min="1" placeholder="Classement" required>
            <input name="time" placeholder="Temps (hh:mm:ss)" required>
            <button type="submit" name="add_result">Ajouter résultat</button>
        </form>
        <form method="post" style="margin-top:20px;">
            <label for="filter_course_id">Filtrer par course :</label>
            <select name="filter_course_id" id="filter_course_id" onchange="this.form.submit()">
                <option value="">Toutes les courses</option>
                <?php foreach ($courses as $c) { ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($_POST['filter_course_id']) && $_POST['filter_course_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php } ?>
            </select>
        </form>
        <h3>Résultats enregistrés :</h3>
        <h3>Résultats Hommes :</h3>
        <table border="1" cellpadding="5" id="table-resultats-hommes">
            <tr><th>Course</th><th>Coureur</th><th>Classement</th><th>Temps</th><th>Actions</th></tr>
            <?php $count = 0; foreach ($results as $res) {
                if (isset($_POST['filter_course_id']) && $_POST['filter_course_id'] && $res['course_id'] != $_POST['filter_course_id']) continue;
                $gender = '';
                foreach ($runners as $r) {
                    if ($r['name'] === $res['runner_name']) {
                        $gender = $r['gender'];
                        break;
                    }
                }
                if ($gender !== 'Homme') continue;
                $json = htmlspecialchars(json_encode($res), ENT_QUOTES, 'UTF-8');
                $count++;
            ?>
                <tr class="<?= $count > 10 ? 'hidden-row-resultats-hommes' : '' ?>" style="<?= $count > 10 ? 'display:none' : '' ?>">
                    <td><?= htmlspecialchars($res['course_name']) ?></td>
                    <td><span class="coureur-nom"><?= htmlspecialchars($res['runner_name']) ?></span></td>
                    <td><?= htmlspecialchars($res['rank']) ?></td>
                    <td><?= htmlspecialchars($res['time']) ?></td>
                    <td>
                        <button type="button" class="icon-btn" title="Modifier" onclick="openEditModal(<?= $json ?>)"><img src="../img/edit.svg" alt="Modifier" style="width:20px;height:20px;"></button>
                        <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce résultat ?');">
                            <input type="hidden" name="result_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="icon-btn" title="Supprimer" name="delete_result"><img src="../img/delete.svg" alt="Supprimer" style="width:20px;height:20px;"></button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php if ($count > 10) { ?>
        <button onclick="toggleRows('resultats-hommes')" id="btn-resultats-hommes">Voir plus</button>
        <?php } ?>
        <h3>Résultats Femmes :</h3>
        <table border="1" cellpadding="5" id="table-resultats-femmes">
            <tr><th>Course</th><th>Coureur</th><th>Classement</th><th>Temps</th><th>Actions</th></tr>
            <?php $count = 0; foreach ($results as $res) {
                if (isset($_POST['filter_course_id']) && $_POST['filter_course_id'] && $res['course_id'] != $_POST['filter_course_id']) continue;
                $gender = '';
                foreach ($runners as $r) {
                    if ($r['name'] === $res['runner_name']) {
                        $gender = $r['gender'];
                        break;
                    }
                }
                if ($gender !== 'Femme') continue;
                $json = htmlspecialchars(json_encode($res), ENT_QUOTES, 'UTF-8');
                $count++;
            ?>
                <tr class="<?= $count > 10 ? 'hidden-row-resultats-femmes' : '' ?>" style="<?= $count > 10 ? 'display:none' : '' ?>">
                    <td><?= htmlspecialchars($res['course_name']) ?></td>
                    <td><span class="coureur-nom"><?= htmlspecialchars($res['runner_name']) ?></span></td>
                    <td><?= htmlspecialchars($res['rank']) ?></td>
                    <td><?= htmlspecialchars($res['time']) ?></td>
                    <td>
                        <button type="button" class="icon-btn" title="Modifier" onclick="openEditModal(<?= $json ?>)"><img src="../img/edit.svg" alt="Modifier" style="width:20px;height:20px;"></button>
                        <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce résultat ?');">
                            <input type="hidden" name="result_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="icon-btn" title="Supprimer" name="delete_result"><img src="../img/delete.svg" alt="Supprimer" style="width:20px;height:20px;"></button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php if ($count > 10) { ?>
        <button onclick="toggleRows('resultats-femmes')" id="btn-resultats-femmes">Voir plus</button>
        <?php } ?>
        <!-- Modale de modification -->
        <div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:2em;border-radius:10px;max-width:400px;margin:auto;position:relative;">
                <span style="position:absolute;top:10px;right:15px;cursor:pointer;font-size:1.5em;" onclick="closeEditModal()">&times;</span>
                <h3>Modifier le résultat</h3>
                <form method="post" id="editResultForm">
                    <input type="hidden" name="result_id" id="edit_result_id">
                    <label>Course :</label>
                    <select name="course_id" id="edit_course_id" required>
                        <?php foreach ($courses as $c) { ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php } ?>
                    </select><br>
                    <label>Coureur :</label>
                    <select name="runner_id" id="edit_runner_id" required>
                        <?php foreach ($runners as $r) { ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php } ?>
                    </select><br>
                    <label>Classement :</label>
                    <input name="rank" type="number" min="1" id="edit_rank" required><br>
                    <label>Temps :</label>
                    <input name="time" id="edit_time" required><br>
                    <button type="submit" name="update_result">Valider la modification</button>
                </form>
            </div>
        </div>
        <script>
        function toggleRows(genre) {
            var rows = document.querySelectorAll('.hidden-row-' + genre);
            var btn = document.getElementById('btn-' + genre);
            var isHidden = rows[0].style.display === 'none';
            for (var i = 0; i < rows.length; i++) {
                rows[i].style.display = isHidden ? '' : 'none';
            }
            btn.textContent = isHidden ? 'Voir moins' : 'Voir plus';
        }
        function openEditModal(res) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_result_id').value = res.id;
            document.getElementById('edit_course_id').value = res.course_id;
            document.getElementById('edit_runner_id').value = res.runner_id;
            document.getElementById('edit_rank').value = res.rank;
            document.getElementById('edit_time').value = res.time;
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        document.getElementById('editResultForm').onsubmit = function() {
            closeEditModal();
        };
        </script>
    </div>
</body>
</html>
