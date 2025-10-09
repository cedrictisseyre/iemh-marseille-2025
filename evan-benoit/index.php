<?php include 'header.php'; ?>

<div class="text-center my-5">
    <h1 class="mb-4">🏋️‍♂️ Tableau de Bord du Coach</h1>
    <p class="text-muted mb-5">Gérez vos clients, vos séances et le suivi de leur progression en toute simplicité.</p>

    <div class="row justify-content-center g-4">
        <div class="col-md-3">
            <a href="clients.php" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-card">
                    <div class="card-body text-center">
                        <h2>👥</h2>
                        <h5 class="card-title mt-3">Gestion des Clients</h5>
                        <p class="text-muted">Ajoutez, modifiez ou supprimez des clients.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="seances.php" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-card">
                    <div class="card-body text-center">
                        <h2>📅</h2>
                        <h5 class="card-title mt-3">Gestion des Séances</h5>
                        <p class="text-muted">Planifiez et suivez les entraînements.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="masse.php" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-card">
                    <div class="card-body text-center">
                        <h2>⚖️</h2>
                        <h5 class="card-title mt-3">Suivi de la Masse</h5>
                        <p class="text-muted">Visualisez la progression physique.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
</style>

<?php include 'footer.php'; ?>
