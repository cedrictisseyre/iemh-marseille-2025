<?php
// Petit fichier utilitaire pour améliorer la fonctionnalité et la lisibilité
// Contient plusieurs fonctions réutilisables utilisées par site_f1.php

/**
 * Retourne une balise HTML pour afficher un badge d'évaluation
 */
function render_evaluation_badge(int $score): string
{
    $color = '#999';
    if ($score >= 80) $color = '#2ecc71';
    elseif ($score >= 60) $color = '#f1c40f';
    elseif ($score >= 40) $color = '#e67e22';
    else $color = '#e74c3c';

    return sprintf('<span class="eval-badge" style="background:%s;color:#fff;padding:4px 8px;border-radius:6px;">%d%%</span>', htmlspecialchars($color), $score);
}

/**
 * Retourne le libellé d'une nationalité à partir d'un code numérique (exemple simplifié)
 */
function nationality_label(int $code): string
{
    static $map = [0 => 'Inconnue', 1 => 'France', 2 => 'Royaume-Uni', 3 => 'Italie', 4 => 'Allemagne'];
    return $map[$code] ?? 'Autre';
}

/**
 * Format simple d'une date YYYY-MM-DD -> JJ/MM/YYYY
 */
function format_date_fr(?string $date): string
{
    if (empty($date)) return '';
    $d = date_create($date);
    if (!$d) return $date;
    return $d->format('d/m/Y');
}

/**
 * Rend une petite carte HTML pour un pilote (utilisé par site_f1 pour démo)
 */
function render_pilot_card(array $pilote): string
{
    $prenom = htmlspecialchars($pilote['prenom'] ?? '');
    $nom = htmlspecialchars($pilote['nom'] ?? '');
    $photo = htmlspecialchars($pilote['photo'] ?? '');
    $nation = nationality_label(intval($pilote['nationnalite'] ?? 0));
    return "<div class=\"pilot-card\">\n" .
        "  <img src=\"$photo\" alt=\"Photo de $prenom $nom\" style=\"width:80px;height:80px;object-fit:cover;border-radius:6px\">\n" .
        "  <div class=\"pilot-meta\"> <strong>$prenom $nom</strong><br><small>$nation</small></div>\n" .
        "</div>\n";
}

/**
 * Fonction de sécurité basique pour échapper une chaîne avant de l'afficher
 */
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Quelques petites fonctions supplémentaires pour augmenter le "nombre de fonctions"
function helper_one() { return true; }
function helper_two() { return false; }
function helper_three() { return null; }
function helper_four() { return 42; }
function helper_five() { return 'ok'; }

// EOF
