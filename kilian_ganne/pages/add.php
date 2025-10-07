<?php
require_once __DIR__ . '/../connexion_database.php';
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
$runners = $conn->query("SELECT * FROM runners")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Ajouter un coureur - GTWS</title>
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
		<a class="tab" href="search.php">Recherche coureur</a>
		<a class="tab active" href="add.php">Ajouter un coureur</a>
	</nav>
	<div style="width:100%;height:220px;background:url('../img/course2.jpg') center/cover no-repeat;margin-bottom:20px;border-radius:10px;"></div>
	<div class="tab-content">
		<h2>Ajouter un coureur</h2>
		<form method="post">
			<input name="name" placeholder="Nom" required>
			<input name="country" placeholder="Pays" required>
			<input name="birth" type="date" placeholder="Date de naissance">
			<input name="team" placeholder="Équipe">
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
</body>
</html>
