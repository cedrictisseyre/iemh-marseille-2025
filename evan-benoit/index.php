<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<?php include 'header.html'; ?>

<div class="hero">
  <div class="hero-content">
    <h1>Bienvenue sur EB Coaching</h1>
    <p>Un espace pensé pour les coachs. Suis la progression et planifie tes entraînements.</p>
    <a href="clients.php">Commencer maintenant</a>
  </div>
</div>

<section class="section bg-white">
  <div class="container">
    <h2>Nos fonctionnalités</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="card card-custom p-4">
          <h4>📋 Gestion des séances</h4>
          <p>Crée et planifie des programmes d'entraînement sur mesure pour chaque athlète.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card card-custom p-4">
          <h4>📈 Suivi des performances</h4>
          <p>Analyse la progression de la masse corporelle, des charges et des performances au fil du temps.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card card-custom p-4">
          <h4>👥 Gestion des clients</h4>
          <p>Visualise et organise les profils, objectifs et suivis de tous tes athlètes en un seul endroit.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-light">
  <div class="container">
    <h2>Actualités EB Coaching</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card card-custom p-4">
          <h5>🔥 Nouvelle interface</h5>
          <p>Une expérience plus fluide et intuitive pour la gestion des séances et des suivis d'entraînement.</p>
          <small class="text-muted">Dernière mise à jour : Octobre 2025</small>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card card-custom p-4">
          <h5>⚖️ Suivi de masse optimisé</h5>
          <p>Chaque client dispose désormais de son propre graphique interactif de progression corporelle.</p>
          <small class="text-muted">Dernière mise à jour : Octobre 2025</small>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-white text-center">
  <div class="container">
    <h2>Accès rapide</h2>
    <a href="clients.php" class="btn btn-outline-dark m-2 px-5 py-3">👥 Gérer les clients</a>
    <a href="seances.php" class="btn btn-outline-dark m-2 px-5 py-3">📅 Voir les séances</a>
    <a href="masse.php" class="btn btn-outline-dark m-2 px-5 py-3">⚖️ Suivi de masse</a>
  </div>
</section>

<?php include 'footer.html'; ?>
