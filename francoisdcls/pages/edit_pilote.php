<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/photo_helper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    set_flash('error', 'ID pilote manquant');
    header('Location: liste_pilotes.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM pilotes WHERE pilote_id = ?');
$stmt->execute([$id]);
$pilote = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pilote) {
    set_flash('error', 'Pilote introuvable');
    header('Location: liste_pilotes.php');
    exit;
}

// Charger écuries pour optionnel
$ecuries = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='utf-8'>
  <title>Modifier pilote - <?= htmlspecialchars((($pilote['prenom'] ?? '') . ' ' . ($pilote['nom'] ?? ''))) ?></title>
  <link rel='stylesheet' href='../assets/style.css'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <style>.form-row{margin-bottom:0.6rem}</style>
</head>
<body>
<?php $page_title = 'Modifier pilote';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <h1>Modifier pilote</h1>
  <form method='post' action='../services/modifier_pilote.php'>
    <input type='hidden' name='pilote_id' value='<?= (int)($pilote['pilote_id'] ?? 0) ?>'>
    <div class='form-row'><label>Prénom:<br><input type='text' name='prenom' value='<?= htmlspecialchars((string)($pilote['prenom'] ?? '')) ?>' required></label></div>
    <div class='form-row'><label>Nom:<br><input type='text' name='nom' value='<?= htmlspecialchars((string)($pilote['nom'] ?? '')) ?>' required></label></div>
    <div class='form-row'><label>Nationalité:<br><input type='text' name='nationalite' value='<?= htmlspecialchars((string)($pilote['nationalite'] ?? '')) ?>'></label></div>
    <div class='form-row'><label>Photo URL:<br><input type='url' name='photo' value='<?= htmlspecialchars((string)($pilote['photo'] ?? '')) ?>'></label></div>
    <button type='submit'>Enregistrer</button>
    <a href='liste_pilotes.php' style='margin-left:1rem'>Annuler</a>
  </form>
  <hr>
  <form method='post' action='../services/supprimer_pilote.php' onsubmit="return confirm('Supprimer ce pilote ? Cette action est irréversible.')">
    <input type='hidden' name='pilote_id' value='<?= (int)$pilote['pilote_id'] ?>'>
    <button type='submit' style='background:#b00;color:#fff;padding:0.5rem 0.8rem;border:none;border-radius:6px'>Supprimer le pilote</button>
  </form>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
