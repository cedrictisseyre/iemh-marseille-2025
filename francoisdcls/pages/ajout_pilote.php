<?php
// Formulaire d'ajout de pilote
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter un pilote</title>
  <link rel='stylesheet' href='../assets/style.css'>
}</head>
<body>
<header><h1>Ajouter un pilote</h1></header>
<div class='container'>
  <?php include __DIR__ . '/../includes/flash.php'; $f = get_flash(); if ($f): ?>
    <div class="flash flash-<?= htmlspecialchars($f['type']) ?>" style="padding:0.6em;border-radius:6px;margin-bottom:1em;background:#efe;color:#030;"><?= htmlspecialchars($f['message']) ?></div>
  <?php endif; ?>
  <form method='post' action='../services/ajout_pilote.php'>
    <label>Prénom:<br><input type='text' name='prenom' required></label><br>
    <label>Nom:<br><input type='text' name='nom' required></label><br>
    <label>Nationalité:<br><input type='text' name='nationalite'></label><br>
    <label>Photo URL:<br><input type='url' name='photo'></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
