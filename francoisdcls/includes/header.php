<?php
// Central header include for pages.
// Usage: set $page_title = 'Titre'; then include __DIR__ . '/header.php';
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/csrf.php';
?>
<header>
  <img src="/francoisdcls/assets/logo-f1.svg"
       alt="Logo F1"
       style="height:48px;vertical-align:middle;"
       aria-hidden="true">

  <h1 style="display:inline-block;vertical-align:middle;">
    <?php
    if (isset($page_title)) {
        $title = htmlspecialchars($page_title);
    } else {
        $title = 'Base F1';
    }
    ?>
    <?= $title ?>
  </h1>
</header>
<?php include __DIR__ . '/nav.php'; ?>
<?php if (function_exists('get_flash')) :
    $f = get_flash();
    if ($f) :
        $flash_type = htmlspecialchars($f['type']);
        $flash_message = htmlspecialchars($f['message']);
        ?>
        <div class="flash flash-<?= $flash_type ?>"
             style="padding:0.6em;border-radius:6px;margin:1em 0;background:#efe;color:#030;">
            <?= $flash_message ?>
        </div>
        <?php
    endif;
endif;
?>
