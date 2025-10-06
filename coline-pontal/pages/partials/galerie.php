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
    <?php foreach ($galerie as $item): ?>
        <?php if ($item['type'] === 'photo'): ?>
            <div style="width:220px;text-align:center;">
                <img src="<?= $item['src'] ?>" alt="<?= htmlspecialchars($item['alt']) ?>" style="width:200px;height:auto;border-radius:8px;box-shadow:0 2px 8px #ccc;">
                <div><?= htmlspecialchars($item['alt']) ?></div>
            </div>
        <?php else: ?>
            <div style="width:220px;text-align:center;">
                <iframe width="200" height="120" src="<?= $item['src'] ?>" title="<?= htmlspecialchars($item['alt']) ?>" frameborder="0" allowfullscreen></iframe>
                <div><?= htmlspecialchars($item['alt']) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
