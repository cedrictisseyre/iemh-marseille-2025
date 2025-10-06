<?php
// Galerie photos/vidéos (simulée)
$galerie = [
    ['type' => 'photo', 'src' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb', 'alt' => 'Entraînement karate'],
    ['type' => 'photo', 'src' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', 'alt' => 'Compétition'],
    ['type' => 'video', 'src' => 'https://www.youtube.com/embed/2Vv-BfVoq4g', 'alt' => 'Démonstration kata']
];
?>
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
