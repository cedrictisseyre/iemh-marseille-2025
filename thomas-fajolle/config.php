<?php
// ==========================
// CONFIGURATION GLOBALE DU SITE
// ==========================

// Chemin racine du projet (à partir de ce fichier)
define('BASE_PATH', __DIR__);

// Informations de connexion à la base
define('DB_HOST', '195.15.235.20');
define('DB_NAME', 'thomas_fajolle');
define('DB_USER', 'root');
define('DB_PASS', 'INNnsk40374');
define('DB_PORT', 3306);

// (Optionnel) – Activer l’affichage des erreurs PHP pendant le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==========================
// FONCTION UTILITAIRE POUR INCLUDES / REQUIRES
// ==========================

/**
 * Retourne le chemin absolu d'un fichier ou dossier depuis la racine du projet
 * @param string $path Chemin relatif depuis la racine du projet
 * @return string Chemin absolu complet
 */
function base_path(string $path = ''): string {
    return BASE_PATH . '/' . ltrim($path, '/');
}
