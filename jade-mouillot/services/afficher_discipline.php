<?php
require_once '../connexion.php';
$sql = "SELECT * FROM discipline";
$stmt = $conn->query($sql);
$disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste des disciplines</title>
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
	<h1>Liste des disciplines</h1>
	<table>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<!-- Ajoute d'autres colonnes si besoin -->
		</tr>
		<?php foreach ($disciplines as $discipline): ?>
		<tr>
			<td><?= htmlspecialchars($discipline['id'] ?? '') ?></td>
			<td><?= htmlspecialchars($discipline['nom'] ?? '') ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<a class="retour" href="../page.php">Retour à l'accueil</a>
</body>
</html>