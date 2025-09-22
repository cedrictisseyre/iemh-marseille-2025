<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mastère IHME - Accueil</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1 class="text-center mb-4">Mastère IHME</h1>
		<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="accueil-tab" data-bs-toggle="tab" data-bs-target="#accueil" type="button" role="tab" aria-controls="accueil" aria-selected="true">Accueil</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="edt-tab" data-bs-toggle="tab" data-bs-target="#edt" type="button" role="tab" aria-controls="edt" aria-selected="false">Emploi du temps</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="profs-tab" data-bs-toggle="tab" data-bs-target="#profs" type="button" role="tab" aria-controls="profs" aria-selected="false">Professeurs</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="eleves-tab" data-bs-toggle="tab" data-bs-target="#eleves" type="button" role="tab" aria-controls="eleves" aria-selected="false">Élèves</button>
			</li>
		</ul>
		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="accueil" role="tabpanel" aria-labelledby="accueil-tab">
				<p>Bienvenue sur le site du Mastère IHME !</p>
			</div>
			<div class="tab-pane fade" id="edt" role="tabpanel" aria-labelledby="edt-tab">
				<p>Exemple d'emploi du temps :</p>
				<table class="table table-bordered">
					<thead><tr><th>Jour</th><th>9h-12h</th><th>14h-17h</th></tr></thead>
					<tbody>
						<tr><td>Lundi</td><td>Mathématiques</td><td>Informatique</td></tr>
						<tr><td>Mardi</td><td>Physique</td><td>Projet</td></tr>
						<tr><td>Mercredi</td><td>Anglais</td><td>Libre</td></tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="profs" role="tabpanel" aria-labelledby="profs-tab">
				<ul>
					<li>Dr. Dupont (Mathématiques)</li>
					<li>Mme Martin (Informatique)</li>
					<li>M. Leroy (Physique)</li>
					<li>Mme Smith (Anglais)</li>
				</ul>
			</div>
			<div class="tab-pane fade" id="eleves" role="tabpanel" aria-labelledby="eleves-tab">
				<ul>
					<li>Camille Durand</li>
					<li>Lucas Moreau</li>
					<li>Sarah Bernard</li>
					<li>Yassine Benali</li>
				</ul>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
