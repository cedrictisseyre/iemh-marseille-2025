<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter une écurie</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<header><h1>Ajouter une écurie</h1></header>
<div class='container'>
  <?php include __DIR__ . '/../includes/flash.php'; $f = get_flash(); if ($f): ?>
    <div class="flash flash-<?= htmlspecialchars($f['type']) ?>" style="padding:0.6em;border-radius:6px;margin-bottom:1em;background:#efe;color:#030;"><?= htmlspecialchars($f['message']) ?></div>
  <?php endif; ?>
  <form method='post' action='../services/ajout_ecurie.php'>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <label>Nom de l'écurie:<br><input type='text' name='nom' required></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
