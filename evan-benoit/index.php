<?php include 'header.html'; ?>

<div class="container text-center my-5">
    <h1 class="display-4 fw-bold mb-4">🏋️ Bienvenue sur ton tableau de bord</h1>
    <p class="lead mb-5">Gère tes clients, leurs séances et le suivi de masse corporelle facilement.</p>

    <div class="row justify-content-center g-4">
        <!-- Séances -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">📅 Séances</h5>
                    <p class="card-text">Visualise et gère les séances de tes clients.</p>
                    <a href="seances.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>

        <!-- Suivi Masse -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">⚖️ Suivi Masse</h5>
                    <p class="card-text">Ajoute, modifie et consulte les mesures corporelles de tes clients.</p>
                    <a href="masse.php" class="btn btn-success">Accéder</a>
                </div>
            </div>
        </div>

        <!-- Clients -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">👥 Clients</h5>
                    <p class="card-text">Liste et détails de tes clients.</p>
                    <a href="clients.php" class="btn btn-warning">Accéder</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    &copy; <?= date("Y") ?> Evan Benoit - Suivi Musculation
</footer>

<?php include 'footer.html'; ?>
