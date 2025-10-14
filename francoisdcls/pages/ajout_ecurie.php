<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter une écurie</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Ajouter une écurie'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <form method='post' action='../services/ajout_ecurie.php'>
    <label>Nom de l'écurie:<br><input type='text' name='nom' required></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
