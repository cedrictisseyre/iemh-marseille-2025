<?php
// Galerie photos/vidéos (simulée + upload utilisateur)
$galerie = [
    ['type' => 'photo', 'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRhShBFNQ-U-iCtIO6yMvUht1WcKCDlPIP9Eg&s', 'alt' => 'Entraînement karate'],
    ['type' => 'photo', 'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPPovZhrM2FePFCb9DO30JFRibLUK8X6WUSw&s', 'alt' => 'Compétition'],
    ['type' => 'video', 'src' => 'https://www.youtube.com/watch?v=LPt8W7icWhs', 'alt' => 'Démonstration kata']
];

// Ajout des photos uploadées par les utilisateurs
$upload_dir = __DIR__ . '/../../assets/galerie_uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo_user']) && $_FILES['photo_user']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['photo_user']['tmp_name'];
    $name = basename($_FILES['photo_user']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed)) {
        $dest = $upload_dir . uniqid('photo_', true) . '.' . $ext;
        if (move_uploaded_file($tmp, $dest)) {
            echo '<p class="success">Photo ajoutée !</p>';
        } else {
            echo '<p class="error">Erreur lors de l\'upload.</p>';
        }
    } else {
        echo '<p class="error">Format non autorisé.</p>';
    }
}
// Charger les photos uploadées
$uploaded_photos = array_filter(glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE));
foreach ($uploaded_photos as $up) {
    $galerie[] = [
        'type' => 'photo',
        'src' => 'assets/galerie_uploads/' . basename($up),
        'alt' => 'Photo utilisateur'
    ];
}
?>
<h3>Ajouter votre photo à la galerie</h3>
<form method="post" enctype="multipart/form-data" style="margin-bottom:20px;">
    <input type="file" name="photo_user" accept="image/*" required>
    <button type="submit">Ajouter la photo</button>
</form>
<h2>Galerie photos & vidéos</h2>
<div style="display:flex;flex-wrap:wrap;gap:20px;">
    <?php foreach ($galerie as $i => $item): ?>
        <?php if ($item['type'] === 'photo'): ?>
            <div style="width:220px;text-align:center;">
                <img src="<?= $item['src'] ?>" alt="<?= htmlspecialchars($item['alt']) ?>" style="width:200px;height:auto;border-radius:8px;box-shadow:0 2px 8px #ccc;cursor:pointer;" onclick="openLightbox('photo', '<?= $item['src'] ?>', '<?= htmlspecialchars($item['alt']) ?>')">
                <div><?= htmlspecialchars($item['alt']) ?></div>
            </div>
        <?php else: ?>
            <div style="width:220px;text-align:center;">
                <div style="cursor:pointer;" onclick="openLightbox('video', '<?= $item['src'] ?>', '<?= htmlspecialchars($item['alt']) ?>')">
                    <iframe width="200" height="120" src="<?= $item['src'] ?>" title="<?= htmlspecialchars($item['alt']) ?>" frameborder="0" allowfullscreen></iframe>
                </div>
                <div><?= htmlspecialchars($item['alt']) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Lightbox -->
<div id="lightbox" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.8);z-index:9999;justify-content:center;align-items:center;flex-direction:column;">
    <span onclick="closeLightbox()" style="color:white;font-size:2em;position:absolute;top:30px;right:50px;cursor:pointer;">&times;</span>
    <div id="lightbox-content"></div>
    <div id="lightbox-caption" style="color:white;margin-top:10px;"></div>
</div>
<script>
function openLightbox(type, src, alt) {
    const lb = document.getElementById('lightbox');
    const content = document.getElementById('lightbox-content');
    const caption = document.getElementById('lightbox-caption');
    if (type === 'photo') {
        content.innerHTML = `<img src="${src}" alt="${alt}" style="max-width:80vw;max-height:80vh;border-radius:12px;box-shadow:0 4px 24px #222;">`;
    } else {
        content.innerHTML = `<iframe width="800" height="450" src="${src}" title="${alt}" frameborder="0" allowfullscreen style="border-radius:12px;"></iframe>`;
    }
    caption.textContent = alt;
    lb.style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
