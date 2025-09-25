<?php
require_once '../connexion.php';
$sql = "SELECT * FROM club";
$stmt = $conn->query($sql);
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste des clubs</title>
	<style>
		table { border-collapse: collapse; width: 80%; margin: 30px auto; }
		th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
		th { background: #2980b9; color: #fff; }
		tr:nth-child(even) { background: #f4f4f4; }
		body { font-family: Arial, sans-serif; background: #f4f4f4; }
		h1 { text-align: center; color: #2c3e50; }
		.retour { display: block; text-align: center; margin: 20px; }
	</style>
</head>
<body>
	<h1>Liste des clubs</h1>
	<table>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<!-- Ajoute d'autres colonnes si besoin -->
		</tr>
		<?php foreach ($clubs as $club): ?>
		<tr>
			<td><?= htmlspecialchars($club['id'] ?? '') ?></td>
			<td><?= htmlspecialchars($club['nom'] ?? '') ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<a class="retour" href="../page.php">Retour à l'accueil</a>
</body>
</html>