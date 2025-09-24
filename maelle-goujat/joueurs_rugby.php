<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

// Connexion à la base
$host = "localhost";
$user = "root";   // adapte si besoin
$pass = "INNnsk40374";
$dbname = "maelle_goujat";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Traitement du formulaire (si soumis)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
    $nom = $conn->real_escape_string($_POST['nom']);
    $poste = $conn->real_escape_string($_POST['poste']);
    $dc = (int)$_POST['dc'];
    $squat = (int)$_POST['squat'];
    $terre = (int)$_POST['terre'];

    $sql = "INSERT INTO joueurs (nom, poste, dc, squat, terre) 
            VALUES ('$nom', '$poste', $dc, $squat, $terre)";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>✅ Joueur ajouté avec succès !</div>";
    } else {
        $message = "<div class='alert alert-danger'>Erreur : " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Rugby & Muscu</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1 class="text-center mb-4">🏉 Rugby & Muscu 💪</h1>
		
		<!-- Menu onglets -->
		<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="accueil-tab" data-bs-toggle="tab" data-bs-target="#accueil" type="button" role="tab">Accueil</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="joueurs-tab" data-bs-toggle="tab" data-bs-target="#joueurs" type="button" role="tab">Joueurs</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="ajouter-tab" data-bs-toggle="tab" data-bs-target="#ajouter" type="button" role="tab">Ajouter un joueur</button>
			</li>
		</ul>
		
		<!-- Contenu des onglets -->
		<div class="tab-content" id="myTabContent">
			
			<!-- Onglet Accueil -->
			<div class="tab-pane fade show active" id="accueil" role="tabpanel">
				<p>Bienvenue sur le site <strong>Rugby & Muscu</strong> !<br>
				Vous trouverez ici les records de musculation des joueurs de rugby 💪</p>
			</div>
			
			<!-- Onglet Joueurs -->
			<div class="tab-pane fade" id="joueurs" role="tabpanel">
				<h3>Liste des joueurs et leurs records</h3>
				
				<?php
				$sql = "SELECT * FROM joueurs";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					echo "<table class='table table-bordered table-striped mt-3'>";
					echo "<thead class='table-dark'><tr><th>Nom</th><th>Poste</th><th>DC (kg)</th><th>Squat (kg)</th><th>Soulevé de terre (kg)</th></tr></thead><tbody>";
					while($row = $result->fetch_assoc()) {
						echo "<tr>
								<td>".$row['nom']."</td>
								<td>".$row['poste']."</td>
								<td>".$row['dc']."</td>
								<td>".$row['squat']."</td>
								<td>".$row['terre']."</td>
							  </tr>";
					}
					echo "</tbody></table>";
				} else {
					echo "<p>Aucun joueur trouvé.</p>";
				}
				?>
			</div>
			
			<!-- Onglet Ajouter un joueur -->
			<div class="tab-pane fade" id="ajouter" role="tabpanel">
				<h3>Ajouter un nouveau joueur</h3>
				<?php if (isset($message)) echo $message; ?>
				
				<form method="POST" class="mt-3">
					<div class="mb-3">
						<label class="form-label">Nom</label>
						<input type="text" name="nom" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Poste</label>
						<input type="text" name="poste" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Développé couché (kg)</label>
						<input type="number" name="dc" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Squat (kg)</label>
						<input type="number" name="squat" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Soulevé de terre (kg)</label>
						<input type="number" name="terre" class="form-control" required>
					</div>
					<button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
				</form>
			</div>
			
		</div>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
