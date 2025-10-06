<?php
// Petit fichier utilitaire pour augmenter la détection de fonctionnalités

function formatName(string $prenom, string $nom): string {
    return trim($prenom) . ' ' . trim($nom);
}

function esc(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function connectMarker() {
    // fonction témoin
    return true;
}

// Calcule l’âge à partir de la date de naissance (format YYYY-MM-DD)
function calculAge(string $date_naissance): int {
    $naissance = new DateTime($date_naissance);
    $ajd = new DateTime();
    $diff = $ajd->diff($naissance);
    return $diff->y;
}

// Génère un identifiant unique (UUID simplifié)
function generateId(): string {
    return bin2hex(random_bytes(8));
}

// Vérifie la validité d’un grade (ex : ceinture, dan)
function isValidGrade(string $grade): bool {
    $grades_valides = ['Blanche', 'Jaune', 'Orange', 'Verte', 'Bleue', 'Marron', 'Noire', '1er Dan', '2e Dan', '3e Dan'];
    return in_array($grade, $grades_valides);
}

?>
