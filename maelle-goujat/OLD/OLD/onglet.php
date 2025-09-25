<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Test Onglets</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">Accueil</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="joueurs-tab" data-bs-toggle="tab" data-bs-target="#joueurs" type="button" role="tab">Joueurs</button>
        </li>
    </ul>
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="home" role="tabpanel">
            <p>Bienvenue sur la page Accueil !</p>
        </div>
        <div class="tab-pane fade" id="joueurs" role="tabpanel">
            <p>Voici la page Joueurs !</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
