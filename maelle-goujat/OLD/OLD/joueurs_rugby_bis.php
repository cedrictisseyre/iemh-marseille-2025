<?php
// --------------------
// PHP : Connexion et traitement formulaire avec PDO
// --------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1"; // ou localhost si ça marche
$dbname = "maelle_goujat";
$user = "root";
$pass = "INNnsk40374";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $poste = $_POST['poste'];
    $dc = (int)$_POST['dc'];
    $squat = (int)$_POST['squat'];
    $terre = (int)$_POST['terre'];

    try {
        $stmt = $conn->prepare("INSERT INTO joueurs (nom, poste, dc, squat, terre) VALUES (:nom, :poste, :dc, :squat, :terre)");
        $stmt->execute([
            ':nom' => $nom,
            ':poste' => $poste,
            ':dc' => $dc,
            ':squat' => $squat,
            ':terre' => $terre
        ]);
        $message = "<div class='alert alert-success mt-3'>✅ Joueur ajouté avec succès !</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger mt-3'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// Récupération des joueurs
try {
    $stmt = $conn->query("SELECT * FROM joueurs");
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération : " . $e->getMessage());
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
            <p>Bienvenue sur le site <strong>Rugby & Muscu</strong> !</p>
            <p>Vous trouverez ici les records de musculation des joueurs de rugby 💪</p>
        </div>

        <!-- Onglet Joueurs -->
        <div class="tab-pane fade" id="joueurs" role="tabpanel">
            <h3>Liste des joueurs et leurs records</h3>
            <?php if (!empty($joueurs)) : ?>
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
                        <?php foreach ($joueurs as $joueur) : ?>
                            <tr>
                                <td><?= htmlspecialchars($joueur['nom']) ?></td>
                                <td><?= htmlspecialchars($joueur['poste']) ?></td>
                                <td><?= htmlspecialchars($joueur['dc']) ?></td>
                                <td><?= htmlspecialchars($joueur['squat']) ?></td>
                                <td><?= htmlspecialchars($joueur['terre']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Aucun joueur trouvé.</p>
            <?php endif; ?>
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
