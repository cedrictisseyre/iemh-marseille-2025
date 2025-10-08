<?php
// Paramètres de connexion à la base
define('DB_HOST', '195.15.235.20');
define('DB_NAME', 'thomas_fajolle');
define('DB_USER', 'root');
define('DB_PASS', 'INNnsk40374');
define('DB_PORT', 3306);

// Fonction utilitaire pour générer le chemin absolu depuis la racine du projet
function base_path($path = '') {
    $projectRoot = '/thomas-fajolle';  // <-- adapter ici si ton projet est dans un autre dossier
    return rtrim($projectRoot, '/') . '/' . ltrim($path, '/');
}
?>
