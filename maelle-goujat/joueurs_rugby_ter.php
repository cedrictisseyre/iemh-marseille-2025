<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Connexion à la base MySQL ---
$host = "localhost";        // ou l'host de ton phpMyAdmin
$user = "root";             // ton user phpMyAdmin
$pass = "INNnsk40374";      // ton mot de passe phpMyAdmin
$dbname = "maelle_goujat";  // nom de la base

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// --- Récupération des joueurs ---
$sql = "SELECT * FROM joueurs";
$result = $conn->query($sql);

$joueurs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $joueurs[] = $row;
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
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#accueil">Accueil</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#joueurs">Joueurs</button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="accueil">
            <p>Bienvenue sur le site <strong>Rugby & Muscu</strong> !</p>
            <p>Vous trouverez ici les records de musculation des joueurs de rugby 💪</p>
        </div>

        <div class="tab-pane fade" id="joueurs">
            <h3>Liste des joueurs et leurs max</h3>
            <?php if (!empty($joueurs)): ?>
                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>DC (kg)</th>
                            <th>Squat (kg)</th>
                            <th>Soulevé de terre (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($joueurs as $j): ?>
                        <tr>
                            <td><?= htmlspecialchars($j['nom']) ?></td>
                            <td><?= htmlspecialchars($j['poste']) ?></td>
                            <td><?= htmlspecialchars($j['dc']) ?></td>
                            <td><?= htmlspecialchars($j['squat']) ?></td>
                            <td><?= htmlspecialchars($j['terre']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun joueur trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
