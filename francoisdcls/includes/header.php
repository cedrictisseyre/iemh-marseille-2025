<?php
// Central header include for pages.
// Usage: set $page_title = 'Titre'; then include __DIR__ . '/header.php';
?>
<header>
  <img src="/francoisdcls/assets/logo-f1.svg" alt="Logo F1" style="height:48px;vertical-align:middle;margin-right:1em;">
  <h1 style="display:inline-block;vertical-align:middle;">
    <?= isset($page_title) ? htmlspecialchars($page_title) : 'Base de données Formule 1' ?>
  </h1>
</header>
<?php include __DIR__ . '/nav.php'; ?>
<?php if (function_exists('get_flash')) :
    $f = get_flash();
    if ($f) : ?>
    <div class="flash flash-<?= htmlspecialchars($f['type']) ?>" style="padding:0.6em;border-radius:6px;margin:1em 0;background:#efe;color:#030;"><?= htmlspecialchars($f['message']) ?></div>
        <?php
    endif;
endif;
?>
