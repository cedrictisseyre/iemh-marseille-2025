<?php

// Formate une date au format français
function formatDateFr(string $date): string {
    return date('d/m/Y', strtotime($date));
}

// Transforme les URLs dans un texte en liens cliquables
function linkify(string $text): string {
    return preg_replace('/(https?:\/\/\S+)/', '<a href="$1" target="_blank">$1</a>', $text);
}

// Vérifie la validité d’un email
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Génère un mot de passe aléatoire
function generatePassword(int $length = 10): string {
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

// Stub pour redimensionner une image (à compléter avec GD ou Imagick)
function resizeImage($file, $width, $height) {
    // À implémenter selon la stack
    return $file;
}

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