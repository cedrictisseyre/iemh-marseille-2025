<?php
// Formulaire d'ajout de pilote
require_once __DIR__ . '/../includes/flash.php';
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter un pilote</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Ajouter un pilote'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
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
