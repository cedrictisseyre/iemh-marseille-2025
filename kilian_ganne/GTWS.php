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
// Calcul du classement général
$general = [];
foreach ($results as $res) {
    $name = $res['runner_name'];
    if (!isset($general[$name])) $general[$name] = 0;
    $general[$name] += (int)$res['rank'];
}
asort($general);
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
        }
        window.onload = function() { showTab('general'); };
    </script>
</head>
<body>
    <header>
        <img src="img/logo_gtws.jpg" alt="Logo GTWS" class="logo">
        <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Golden World Trail Series</span>
        <!-- Images supprimées -->
    </header>
    <div style="width:100%;height:220px;background:url('img/course3.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
    <div class="tabs">
        <div class="tab" id="tab-general" onclick="showTab('general')">Classement général</div>
        <div class="tab" id="tab-stages" onclick="showTab('stages')">Résultats par manche</div>
        <div class="tab" id="tab-search" onclick="showTab('search')">Recherche coureur</div>
        <div class="tab" id="tab-add" onclick="showTab('add')">Ajouter un coureur</div>
    </div>
    <div id="general" class="tab-content" style="position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('img/course1.jpg') center/cover no-repeat;opacity:0.25;z-index:0;"></div>
        <div style="position:relative;z-index:1;">
        <h2>Classement général</h2>
        <table border="1" cellpadding="5">
            <tr><th>Place</th><th>Nom</th><th>Total points</th></tr>
            <?php $place = 1; foreach ($general as $name => $points) { ?>
                <tr><td><?= $place++ ?></td><td><?= htmlspecialchars($name) ?></td><td><?= $points ?></td></tr>
            <?php } ?>
        </table>
        </div>
    </div>
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
        <table border="1" cellpadding="5">
            <tr><th>Course</th><th>Coureur</th><th>Classement</th><th>Temps</th></tr>
            <?php foreach ($results as $res) {
                if (isset($_POST['filter_course_id']) && $_POST['filter_course_id'] && $res['course_id'] != $_POST['filter_course_id']) continue;
            ?>
                <tr>
                    <td><?= htmlspecialchars($res['course_name']) ?></td>
                    <td><?= htmlspecialchars($res['runner_name']) ?></td>
                    <td><?= htmlspecialchars($res['rank']) ?></td>
                    <td><?= htmlspecialchars($res['time']) ?></td>
                </tr>
            <?php } ?>
        </table>
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
            <ul>
                <li>Nom : <?= htmlspecialchars($searched_runner['name']) ?></li>
                <li>Pays : <?= htmlspecialchars($searched_runner['country']) ?></li>
                <li>Date de naissance : <?= htmlspecialchars($searched_runner['birth']) ?></li>
                <li>Équipe : <?= htmlspecialchars($searched_runner['team']) ?></li>
                <li>Sexe : <?= htmlspecialchars($searched_runner['gender']) ?></li>
                <li>Infos : <?= htmlspecialchars($searched_runner['info']) ?></li>
            </ul>
        <?php } elseif (isset($_POST['search_runner'])) { ?>
            <p style="color:red">Coureur non trouvé.</p>
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
