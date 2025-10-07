<?php
require_once __DIR__ . '/../connexion_database.php';
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
$runners = $conn->query("SELECT * FROM runners")->fetchAll(PDO::FETCH_ASSOC);
$results = $conn->query("SELECT results.*, courses.name AS course_name, runners.name AS runner_name FROM results JOIN courses ON results.course_id = courses.id JOIN runners ON results.runner_id = runners.id")->fetchAll(PDO::FETCH_ASSOC);
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
	<title>Classement général - GTWS</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<header>
		<img src="../img/logo_gtws.jpg" alt="Logo GTWS" class="logo">
		<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Golden World Trail Series</span>
	</header>
	<nav class="tabs">
		<a class="tab active" href="general.php">Classement général</a>
		<a class="tab" href="stages.php">Résultats par manche</a>
		<a class="tab" href="search.php">Recherche coureur</a>
		<a class="tab" href="add.php">Ajouter un coureur</a>
	</nav>
	<div style="width:100%;height:220px;background:url('../img/course3.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
	<div class="tab-content">
		<h2>Classement général Hommes</h2>
		<table border="1" cellpadding="5" id="table-general-hommes">
			<tr><th>Place</th><th>Nom</th><th>Total points</th></tr>
			<?php $place = 1; $total = count($general_m); foreach ($general_m as $name => $points) { ?>
				<tr class="<?= $place > 10 ? 'hidden-row-hommes' : '' ?>" style="<?= $place > 10 ? 'display:none' : '' ?>"><td><?= $place ?></td><td><span class="coureur-nom"><?= htmlspecialchars($name) ?></span></td><td><?= $points ?></td></tr>
				<?php $place++; } ?>
		</table>
		<?php if ($total > 10) { ?>
		<button onclick="toggleRows('hommes')" id="btn-hommes">Voir plus</button>
		<?php } ?>
		<h2>Classement général Femmes</h2>
		<table border="1" cellpadding="5" id="table-general-femmes">
			<tr><th>Place</th><th>Nom</th><th>Total points</th></tr>
			<?php $place = 1; $total = count($general_f); foreach ($general_f as $name => $points) { ?>
				<tr class="<?= $place > 10 ? 'hidden-row-femmes' : '' ?>" style="<?= $place > 10 ? 'display:none' : '' ?>"><td><?= $place ?></td><td><span class="coureur-nom"><?= htmlspecialchars($name) ?></span></td><td><?= $points ?></td></tr>
				<?php $place++; } ?>
		</table>
		<?php if ($total > 10) { ?>
		<button onclick="toggleRows('femmes')" id="btn-femmes">Voir plus</button>
		<?php } ?>
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
		</script>
	</div>
</body>
</html>
