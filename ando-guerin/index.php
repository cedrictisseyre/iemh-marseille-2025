<?php
// filepath: /workspace/ando-guerin/index.php
require_once 'connexion.php';

// Récupérer les jours et horaires pour l'emploi du temps
$jours = $conn->query('SELECT * FROM jours ORDER BY id')->fetchAll();
$horaires = $conn->query('SELECT * FROM horaires ORDER BY id')->fetchAll();

// Récupérer l'emploi du temps complet (jointure)
$stmt = $conn->query('SELECT et.jour_id, et.horaire_id, m.nom AS matiere, CONCAT(p.prenom, " ", p.nom) AS professeur, s.nom AS salle
    FROM emploi_temps et
    JOIN matieres m ON et.matiere_id = m.id
    LEFT JOIN professeurs p ON et.professeur_id = p.id
    LEFT JOIN salles s ON et.salle_id = s.id');
$emploi = [];
foreach ($stmt as $row) {
    $emploi[$row['jour_id']][$row['horaire_id']] = $row;
}

// Récupérer les professeurs et leurs matières
$profs = $conn->query('SELECT p.id, p.prenom, p.nom, GROUP_CONCAT(m.nom SEPARATOR ", ") AS matieres
    FROM professeurs p
    LEFT JOIN professeurs_matieres pm ON p.id = pm.professeur_id
    LEFT JOIN matieres m ON pm.matiere_id = m.id
    GROUP BY p.id, p.prenom, p.nom
    ORDER BY p.nom')->fetchAll();

// Récupérer les élèves
$eleves = $conn->query('SELECT * FROM eleves ORDER BY nom, prenom')->fetchAll();
?>
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
                <div class="row mt-4">
                    <div class="col-md-4">
                        <h5>Jours</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($jours as $jour): ?>
                                <li class="list-group-item"><?= htmlspecialchars($jour['nom']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <h5>Horaires</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($horaires as $horaire): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars(substr($horaire['debut'],0,5)) ?> - <?= htmlspecialchars(substr($horaire['fin'],0,5)) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Matières</h5>
                        <ul class="list-group mb-3">
                            <?php $matieres = $conn->query('SELECT * FROM matieres ORDER BY nom')->fetchAll();
                            foreach ($matieres as $matiere): ?>
                                <li class="list-group-item"><?= htmlspecialchars($matiere['nom']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <h5>Salles</h5>
                        <ul class="list-group mb-3">
                            <?php $salles = $conn->query('SELECT * FROM salles ORDER BY nom')->fetchAll();
                            foreach ($salles as $salle): ?>
                                <li class="list-group-item"><?= htmlspecialchars($salle['nom']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Professeurs</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($profs as $prof): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?> (<?= htmlspecialchars($prof['matieres']) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <h5>Élèves</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($eleves as $eleve): ?>
                                <li class="list-group-item"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="edt" role="tabpanel" aria-labelledby="edt-tab">
                <h5>Emploi du temps</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Jour / Horaire</th>
                            <?php foreach ($horaires as $horaire): ?>
                                <th><?= htmlspecialchars(substr($horaire['debut'],0,5)) ?> - <?= htmlspecialchars(substr($horaire['fin'],0,5)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($jours as $jour): ?>
                        <tr>
                            <td><?= htmlspecialchars($jour['nom']) ?></td>
                            <?php foreach ($horaires as $horaire): ?>
                                <td>
                                <?php if (isset($emploi[$jour['id']][$horaire['id']])): 
                                    $e = $emploi[$jour['id']][$horaire['id']]; ?>
                                    <strong><?= htmlspecialchars($e['matiere']) ?></strong><br>
                                    <small><?= htmlspecialchars($e['professeur']) ?></small><br>
                                    <em><?= htmlspecialchars($e['salle']) ?></em>
                                <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="profs" role="tabpanel" aria-labelledby="profs-tab">
                <ul>
                <?php foreach ($profs as $prof): ?>
                    <li><?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?> (<?= htmlspecialchars($prof['matieres']) ?>)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div class="tab-pane fade" id="eleves" role="tabpanel" aria-labelledby="eleves-tab">
                <ul>
                    <?php foreach ($eleves as $eleve): ?>
                        <li><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
