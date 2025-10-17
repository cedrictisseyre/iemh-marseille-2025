<?php
include __DIR__ . '/includes/flash.php';
include __DIR__ . '/includes/site_helpers.php';
$f = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php
    $meta_desc = 'Base de données Formule 1 — pilotes, écuries et participations.' .
      ' Projet IEMH Marseille 2025.';
    ?>
  <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
  <link rel="canonical" href="https://example.org/francoisdcls/site_f1.php" />
  <!-- Open Graph -->
  <meta property="og:title" content="Base de données Formule 1" />
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://example.org/francoisdcls/site_f1.php" />
  <meta property="og:image" content="https://example.org/francoisdcls/assets/logo-f1.svg" />
  <title>Base de données Formule 1</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Aller au contenu</a>

<?php
// Render header and nav using helpers to increase modularity and testability
render_header('Base de données Formule 1');
$navLinks = [
    'pages/liste_pilotes.php' => 'Liste des pilotes',
    'pages/liste_ecuries.php' => 'Liste des écuries',
    'pages/statistiques.php' => 'Statistiques',
    'pages/recherche.php' => 'Recherche de pilotes',
    'pages/comparer_pilotes.php' => 'Comparer deux pilotes',
    'pages/palmares_annee.php' => 'Palmarès par année',
    'pages/pantheon_pilotes.php' => 'Champions du monde',
    'pages/ajout_participation.php' => 'Ajouter une participation',
];
render_nav($navLinks);
?>

<main id="main-content" role="main" class="container">
  <?php if ($f) : ?>
        <?php
        $flash_type = htmlspecialchars($f['type']);
        $flash_msg = htmlspecialchars($f['message']);
        ?>
        <?php
        $flash_classes = 'flash flash-' . $flash_type;
        $flash_style = 'padding:0.6em;border-radius:6px;margin-bottom:1em;';
        $flash_style .= 'background:#efe;color:#030;';
        ?>
  <div class="<?= $flash_classes ?>" style="<?= $flash_style ?>">
        <?= $flash_msg ?>
  </div>
  <?php endif; ?>

  <p><?= htmlspecialchars(get_welcome_message()) ?></p>

  <div id="evaluation-badge" style="float:right;margin-top:-2.5em;">Score: <span id="eval-score">—</span></div>

  <section aria-labelledby="search-label" style="margin-top:1em;">
    <h2 id="search-label">Recherche rapide de pilote</h2>
    <form
      id="form-recherche-home"
      autocomplete="off"
      role="search"
    >
      <label
        for="input-recherche-home"
        class="visually-hidden"
      >Rechercher un pilote par nom ou prénom</label>

      <input
        type="search"
        id="input-recherche-home"
        placeholder="Rechercher un pilote..."
        aria-autocomplete="list"
        aria-controls="suggestions-list"
      />

      <label for="select-recherche-home-type" class="visually-hidden">Type</label>
      <select id="select-recherche-home-type" aria-label="Type de recherche">
        <option value="both">Pilote et écurie</option>
        <option value="pilote">Pilote</option>
        <option value="ecurie">Écurie</option>
      </select>

      <label for="input-recherche-home-annee" class="visually-hidden">Année</label>
      <input
        type="number"
        id="input-recherche-home-annee"
        placeholder="Année (optionnel)"
        min="1900"
        max="2100"
      />

      <div id="suggestions-list" role="listbox" aria-live="polite"></div>
    </form>
  </section>

  <h3>Formulaires d'ajout</h3>
  <ul>
    <li><a href="pages/ajout_pilote.php">Ajouter un pilote</a></li>
    <li><a href="pages/ajout_ecurie.php">Ajouter une écurie</a></li>
    <li><a href="pages/ajout_participation.php">Ajouter une participation</a></li>
  </ul>

  <div id="stats-globales" style="margin-top:2em;"></div>
  <p style="margin-top:1em;">
    Consultez
    <a href="database/example_pdo_usage.php">un exemple d'utilisation PDO</a>
    pour voir une connexion sûre à la base de données.
  </p>
</main>

<script src="assets/stats.js" defer></script>
<script src="assets/actions.js" defer></script>
<script src="assets/recherche.js" defer></script>
<script src="assets/eval.js" defer></script>
<?php render_footer(); ?>
</body>
</html>
