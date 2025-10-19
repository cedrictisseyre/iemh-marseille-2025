<?php

// Petit ensemble de helpers réutilisables pour améliorer la structure et le nombre
// de fonctions dans le projet (utilisé par la page d'accueil).

/** Renders the page header (logo + title) */
function render_header(string $title = 'Base de données Formule 1'): void
{
    // Prefer the centralized header include if present to keep one header per page
    $headerPath = __DIR__ . '/header.php';
    if (file_exists($headerPath)) {
        // make $page_title available to the header include
        $page_title = $title;
        include_once $headerPath;
        return;
    }

    $logo = 'assets/logo-f1.svg';
    echo "<header>\n";
    $imgAttrs = 'alt="Logo F1" loading="lazy"';
    $imgStyle = 'height:48px;vertical-align:middle;margin-right:1em;';
    echo "  <img src=\"{$logo}\" {$imgAttrs} style=\"{$imgStyle}\">\n";
    echo "  <h1 style=\"display:inline-block;vertical-align:middle;\">" . htmlspecialchars($title) . "</h1>\n";
    echo "</header>\n";
}

/** Renders the main navigation as a list; accepts an array of ['href' => 'label'] */
function render_nav(array $links = []): void
{
    // Canonical labels to ensure consistent tab names across pages
    $canonical = [
        'site_f1.php' => 'Accueil',
        'pages/liste_pilotes.php' => 'Liste des pilotes',
        'pages/liste_ecuries.php' => 'Liste des écuries',
        'pages/statistiques.php' => 'Statistiques',
        'pages/recherche.php' => 'Recherche de pilotes',
        'pages/comparer_pilotes.php' => 'Comparer deux pilotes',
        'pages/palmares_annee.php' => 'Palmarès par année',
        'pages/pantheon_pilotes.php' => 'Champions du monde',
        // 'pages/ajout_participation.php' removed from main nav; keep add functions elsewhere
    ];

    // Render as styled tabs to match site-wide navigation
    echo "<nav class=\"tabs\" aria-label=\"Navigation principale\">\n  <ul class=\"tabs-list\">\n";
    $current = $_SERVER['REQUEST_URI'] ?? '';
    foreach ($links as $href => $label) {
        $hrefEsc = htmlspecialchars($href);
        // prefer canonical label when available (match by basename)
        $base = basename($href);
        $labelCanonical = $canonical[$href] ?? $canonical[$base] ?? $label;
        $labelEsc = htmlspecialchars($labelCanonical);
        $active = (strpos($current, $href) !== false || basename(parse_url($current, PHP_URL_PATH) ?: '') === basename($href)) ? 'active' : '';
        echo "    <li><a href=\"{$hrefEsc}\" class=\"{$active}\">{$labelEsc}</a></li>\n";
    }
    echo "  </ul>\n</nav>\n";
}

/** Renders a simple footer */
function render_footer(string $text = 'Projet IEMH Marseille 2025'): void
{
    echo "<footer>" . htmlspecialchars($text) . "</footer>\n";
}

/** Returns a short welcome message (separate for easier testing) */
function get_welcome_message(): string
{
    return 'Bienvenue sur le site de consultation des données F1 du projet IEMH Marseille 2025.';
}
