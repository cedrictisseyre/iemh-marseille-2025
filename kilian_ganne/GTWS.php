<?php
require_once __DIR__ . '/connexion_database.php';

// Ajout d'un coureur
if (isset($_POST['add_runner'])) {
    $stmt = $conn->prepare("INSERT INTO runners (name, country, birth, team, info, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['country'],
        $_POST['birth'],
        $_POST['team'],
        $_POST['info'],
        $_POST['gender']
    ]);
}
// Ajout d'un résultat de manche
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
// Suppression d'un résultat
if (isset($_POST['delete_result'])) {
    $stmt = $conn->prepare("DELETE FROM results WHERE id = ?");
    $stmt->execute([intval($_POST['result_id'])]);
}
// Préparation modification d'un résultat
$edit_result = null;
if (isset($_POST['edit_result'])) {
    $stmt = $conn->prepare("SELECT * FROM results WHERE id = ?");
    $stmt->execute([intval($_POST['result_id'])]);
    $edit_result = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Validation modification d'un résultat
if (isset($_POST['update_result'])) {
    $stmt = $conn->prepare("UPDATE results SET course_id = ?, runner_id = ?, rank = ?, time = ? WHERE id = ?");
    $stmt->execute([
        intval($_POST['course_id']),
        intval($_POST['runner_id']),
        $_POST['rank'],
        $_POST['time'],
        intval($_POST['result_id'])
    ]);
    // On force le retour à l'affichage normal (pas de formulaire de modification)
    unset($_POST['edit_result']);
    $edit_result = null;
}
// Suppression d'un coureur
if (isset($_POST['delete_runner'])) {
    $stmt = $conn->prepare("DELETE FROM runners WHERE id = ?");
    $stmt->execute([intval($_POST['runner_id'])]);
    $searched_runner = null;
}
// Préparation modification d'un coureur
$edit_runner = null;
if (isset($_POST['edit_runner'])) {
    $stmt = $conn->prepare("SELECT * FROM runners WHERE id = ?");
    $stmt->execute([intval($_POST['runner_id'])]);
    $edit_runner = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Validation modification d'un coureur
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
// Recherche d'un coureur
$searched_runner = null;
if (isset($_POST['search_runner'])) {
    $search = trim($_POST['search_name']);
    $stmt = $conn->prepare("SELECT * FROM runners WHERE name = ? LIMIT 1");
    $stmt->execute([$search]);
    $searched_runner = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Récupération des courses
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
// Récupération des coureurs
$runners = $conn->query("SELECT * FROM runners")->fetchAll(PDO::FETCH_ASSOC);
// Récupération des résultats avec jointures
$results = $conn->query("SELECT results.*, courses.name AS course_name, runners.name AS runner_name FROM results JOIN courses ON results.course_id = courses.id JOIN runners ON results.runner_id = runners.id")->fetchAll(PDO::FETCH_ASSOC);
// Calcul du classement général séparé

$points_table = [
    1 => 200,
    2 => 188,
    3 => 176,
    4 => 166,
    5 => 156,
    6 => 150,
    7 => 144,
    8 => 140,
    9 => 136,
    10 => 133
];
$general_m = [];
$general_f = [];
foreach ($results as $res) {
    $name = $res['runner_name'];
    $gender = '';
    foreach ($runners as $r) {
        if ($r['name'] === $name) {
            $gender = $r['gender'];
            break;
        }
    }
    $rank = (int)$res['rank'];
    $points = isset($points_table[$rank]) ? $points_table[$rank] : 0;
    if ($gender === 'Homme') {
        if (!isset($general_m[$name])) $general_m[$name] = 0;
        $general_m[$name] += $points;
    } elseif ($gender === 'Femme') {
        if (!isset($general_f[$name])) $general_f[$name] = 0;
        $general_f[$name] += $points;
    }
}
arsort($general_m);
arsort($general_f);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Golden World Trail Series - Résultats</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(e => e.style.display = 'none');
            document.getElementById(tab).style.display = 'block';
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            localStorage.setItem('activeTab', tab);
        }
        window.onload = function() {
            var tab = localStorage.getItem('activeTab') || 'general';
            showTab(tab);
        };
    </script>
</head>
<body>
    <header>
        <img src="img/logo_gtws.jpg" alt="Logo GTWS" class="logo">
    <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Golden Trail World Series</span>
        <!-- Images supprimées -->
    </header>
    <div style="width:100%;height:220px;background:url('img/course3.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
    <nav class="tabs">
        <a class="tab" href="pages/general.php">Classement général</a>
        <a class="tab" href="pages/stages.php">Résultats par manche</a>
        <a class="tab" href="pages/search.php">Recherche coureur</a>
        <a class="tab" href="pages/add.php">Ajouter un coureur</a>
    </nav>
    <!-- La logique d'affichage est maintenant dans les pages dédiées -->
    <div id="stages" class="tab-content" style="display:none;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('img/course1.jpg') center/cover no-repeat;opacity:0.25;z-index:0;"></div>
        <div style="position:relative;z-index:1;">
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
                        <button type="button" class="icon-btn" title="Modifier" onclick="openEditModal(<?= $json ?>)"><img src="img/edit.svg" alt="Modifier" style="width:20px;height:20px;"></button>
                        <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce résultat ?');">
                            <input type="hidden" name="result_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="icon-btn" title="Supprimer" name="delete_result"><img src="img/delete.svg" alt="Supprimer" style="width:20px;height:20px;"></button>
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
                        <button type="button" class="icon-btn" title="Modifier" onclick="openEditModal(<?= $json ?>)"><img src="img/edit.svg" alt="Modifier" style="width:20px;height:20px;"></button>
                        <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce résultat ?');">
                            <input type="hidden" name="result_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="icon-btn" title="Supprimer" name="delete_result"><img src="img/delete.svg" alt="Supprimer" style="width:20px;height:20px;"></button>
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
    </div>
    <div id="search" class="tab-content" style="display:none;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('img/course2.jpg') center/cover no-repeat;opacity:0.25;z-index:0;"></div>
        <div style="position:relative;z-index:1;">
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
                <input name="birth" type="date" value="<?= htmlspecialchars($edit_runner['birth']) ?>" required><br>
                <label>Équipe :</label>
                <input name="team" value="<?= htmlspecialchars($edit_runner['team']) ?>" required><br>
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
    </div>
    <div id="add" class="tab-content" style="display:none;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('img/course2.jpg') center/cover no-repeat;opacity:0.25;z-index:0;"></div>
        <div style="position:relative;z-index:1;">
        <h2>Ajouter un coureur</h2>
        <form method="post">
            <input name="name" placeholder="Nom" required>
            <input name="country" placeholder="Pays" required>
            <input name="birth" type="date" placeholder="Date de naissance" required>
            <input name="team" placeholder="Équipe" required>
            <select name="gender" required>
                <option value="">Sexe</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
                <option value="Autre">Autre</option>
            </select>
            <input name="info" placeholder="Infos complémentaires">
            <button type="submit" name="add_runner">Ajouter</button>
        </form>
        <h3>Coureurs déjà enregistrés :</h3>
        <ul>
        <?php foreach ($runners as $r) { ?>
            <li><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['country']) ?>)</li>
        <?php } ?>
        </ul>
        </div>
    </div>
</body>
</html>
