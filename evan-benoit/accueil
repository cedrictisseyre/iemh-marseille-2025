<?php
require_once 'connect.php';
include 'header.php';

// Récupération des chiffres clés
$total_clients = $conn->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$total_coachs = $conn->query("SELECT COUNT(*) FROM coachs")->fetchColumn();
$total_seances = $conn->query("SELECT COUNT(*) FROM seances")->fetchColumn();
$total_masse = $conn->query("SELECT COUNT(*) FROM suivi_masse")->fetchColumn();
?>

<div class="text-center my-5">
    <h1 class="fw-bold">🏋️ Tableau de Bord — Coaching Sportif</h1>
    <p class="text-muted">Vue d’ensemble de ton activité</p>
</div>

<div class="row text-center">
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-primary">👤 Clients</h5>
                <h2 class="fw-bold"><?= $total_clients ?></h2>
                <a href="clients.php" class="btn btn-outline-primary btn-sm mt-2">Voir</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-success">🏋️ Coachs</h5>
                <h2 class="fw-bold"><?= $total_coachs ?></h2>
                <a href="coachs.php" class="btn btn-outline-success btn-sm mt-2">Voir</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-warning">📅 Séances</h5>
                <h2 class="fw-bold"><?= $total_seances ?></h2>
                <a href="index.php" class="btn btn-outline-warning btn-sm mt-2">Voir</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-danger">⚖️ Suivis Masse</h5>
                <h2 class="fw-bold"><?= $total_masse ?></h2>
                <a href="masse.php" class="btn btn-outline-danger btn-sm mt-2">Voir</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
