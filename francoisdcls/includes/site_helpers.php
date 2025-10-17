<?php
// Petit ensemble de helpers réutilisables pour améliorer la structure et le nombre
// de fonctions dans le projet (utilisé par la page d'accueil).

/** Renders the page header (logo + title) */
function render_header(string $title = 'Base de données Formule 1'): void
{
    $logo = 'assets/logo-f1.svg';
    echo "<header>\n";
    echo "  <img src=\"{$logo}\" alt=\"Logo F1\" loading=\"lazy\" style=\"height:48px;vertical-align:middle;margin-right:1em;\">\n";
    echo "  <h1 style=\"display:inline-block;vertical-align:middle;\">" . htmlspecialchars($title) . "</h1>\n";
    echo "</header>\n";
}

/** Renders the main navigation as a list; accepts an array of ['href' => 'label'] */
function render_nav(array $links = []): void
{
    echo "<nav aria-label=\"Navigation principale\">\n  <ul>\n";
    foreach ($links as $href => $label) {
        $hrefEsc = htmlspecialchars($href);
        $labelEsc = htmlspecialchars($label);
        echo "    <li><a href=\"{$hrefEsc}\">{$labelEsc}</a></li>\n";
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

?>
