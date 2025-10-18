<?php
// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols
// Navigation tabs for the F1 site. Adds an 'active' class based on the current URL.
$current = $_SERVER['REQUEST_URI'] ?? '';

// Build links relative to the configured base path to make the site portable
$links = [
  base_path('site_f1.php') => 'Accueil',
  base_path('pages/liste_pilotes.php') => 'Pilotes',
  base_path('pages/liste_ecuries.php') => 'Écuries',
  base_path('pages/statistiques.php') => 'Statistiques',
  base_path('pages/recherche.php') => 'Recherche',
  base_path('pages/comparer_pilotes.php') => 'Comparer',
  base_path('pages/palmares_annee.php') => 'Palmarès',
  base_path('pages/pantheon_pilotes.php') => 'Panthéon',
  // participation added via add-pilote / add-ecurie forms; no standalone page
];

// Fallback: if the current URL is the repo-root without prefix, try matching by filename
if (!function_exists('_is_active')) {
    function _is_active($current, $path)
    {
        if (strpos($current, $path) !== false) {
            return true;
        }
      // Also match by basename
        $bcur = basename(parse_url($current, PHP_URL_PATH) ?: '');
        $bpath = basename($path);
        return $bcur && $bpath && $bcur === $bpath;
    }
}
?>
<nav class="tabs" aria-label="Navigation principale">
  <ul class="tabs-list">
    <?php foreach ($links as $url => $label) : ?>
        <?php $active = _is_active($current, $url) ? 'active' : ''; ?>
      <li><a href="<?= htmlspecialchars($url) ?>" class="<?= $active ?>"><?= htmlspecialchars($label) ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>
