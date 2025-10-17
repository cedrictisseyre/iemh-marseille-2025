<?php
require_once __DIR__ . '/../connexion.php';

// Simple uploader pour associer une photo à un coureur (id_coureur)
// Formulaire GET: pages/upload_photo.php?id_coureur=123
// POST handles upload: file input name="photo"

$maxSize = 2 * 1024 * 1024; // 2MB
$allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

$id = isset($_GET['id_coureur']) ? (int)$_GET['id_coureur'] : 0;
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id <= 0) {
        $errors[] = 'ID coureur invalide.';
    }
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Aucun fichier envoyé ou erreur lors de l\'upload.';
    } else {
        $f = $_FILES['photo'];
        if ($f['size'] > $maxSize) {
            $errors[] = 'Fichier trop volumineux (max 2MB).';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            if (!array_key_exists($mime, $allowedMime)) {
                $errors[] = 'Type de fichier non autorisé. JPG/PNG seulement.';
            } else {
                $ext = $allowedMime[$mime];
                $destDir = __DIR__ . '/../assets/photos';
                if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                $destPath = $destDir . '/' . $id . '.' . $ext;
                if (!move_uploaded_file($f['tmp_name'], $destPath)) {
                    $errors[] = 'Impossible de sauvegarder le fichier.';
                } else {
                    $success = 'Photo uploadée avec succès.';
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Uploader photo coureur</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:20px}
    form{max-width:480px;margin:0 auto}
  </style>
</head>
<body>
  <h1>Uploader une photo pour un coureur</h1>
  <?php if ($errors): ?>
    <div style="color:#a00;margin-bottom:1em;">
      <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
    </div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div style="color:green;margin-bottom:1em;"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>ID coureur (numeric): <input type="number" name="id_coureur" value="<?= htmlspecialchars($id) ?>" disabled></label>
    <p>
      <input type="file" name="photo" accept="image/jpeg,image/png" required>
    </p>
    <p>
      <button type="submit">Uploader</button>
    </p>
  </form>
  <p><a href="liste_coureurs.php">Retour à la liste</a></p>
</body>
</html>
