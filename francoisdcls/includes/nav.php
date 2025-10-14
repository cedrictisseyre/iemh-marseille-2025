<?php
// Navigation tabs for the F1 site. Adds an 'active' class based on the current URL.
$current = $_SERVER['REQUEST_URI'] ?? '';

$links = [
  '/francoisdcls/site_f1.php' => 'Accueil',
  '/francoisdcls/pages/liste_pilotes.php' => 'Pilotes',
  '/francoisdcls/pages/liste_ecuries.php' => 'Écuries',
  '/francoisdcls/pages/statistiques.php' => 'Statistiques',
  '/francoisdcls/pages/recherche.php' => 'Recherche',
  '/francoisdcls/pages/comparer_pilotes.php' => 'Comparer',
  '/francoisdcls/pages/palmares_annee.php' => 'Palmarès',
  '/francoisdcls/pages/pantheon_pilotes.php' => 'Panthéon',
  // participation added via add-pilote / add-ecurie forms; no standalone page
];

// Fallback: if the current URL is the repo-root without prefix, try matching by filename
if (!function_exists('_is_active')) {
  function _is_active($current, $path) {
    if (strpos($current, $path) !== false) return true;
    // Also match by basename
    $bcur = basename(parse_url($current, PHP_URL_PATH) ?: '');
    $bpath = basename($path);
    return $bcur && $bpath && $bcur === $bpath;
  }
}
?>
<nav class="tabs" aria-label="Navigation principale">
  <ul class="tabs-list">
    <?php foreach ($links as $url => $label): ?>
      <?php $active = _is_active($current, $url) ? 'active' : ''; ?>
      <li><a href="<?= htmlspecialchars($url) ?>" class="<?= $active ?>"><?= htmlspecialchars($label) ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>
