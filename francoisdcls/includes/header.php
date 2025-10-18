<?php
// Central header include for pages.
// Usage: set $page_title = 'Titre'; then include __DIR__ . '/header.php';
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/csrf.php';
// Initialisation de l'application (session + headers)
// Avoid running init_app() automatically during unit tests (IN_TEST)
if (!defined('IN_TEST') && PHP_SAPI !== 'cli' && function_exists('init_app')) {
    init_app();
}
// Charger le helper flash (définit set_flash/get_flash)
require_once __DIR__ . '/flash.php';
// Exposer le token CSRF côté client pour les requêtes AJAX
if (function_exists('csrf_token')) {
    $csrf_for_js = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo "<script>window.CSRF_TOKEN = '" . $csrf_for_js . "';</script>";
}
// Expose the base path to JS and load the csrf helper from the base path
echo "<script>window.BASE_PATH = '" . addslashes(FRANCOIS_BASE_PATH) . "';</script>";
echo "<script src=\"" . htmlspecialchars(base_path('assets/csrf.js')) . "\" defer></script>";
?>
<header>
    <img src="<?= htmlspecialchars(base_path('assets/logo-f1.svg')) ?>"
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
