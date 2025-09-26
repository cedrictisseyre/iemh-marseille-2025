<?php
/**
 * Fonctions d'évaluation pour un dossier étudiant
 * Chaque fonction retourne un score ou un indicateur pour un critère précis
 * Utiliser ces fonctions pour automatiser l'auto-évaluation des productions
 */

/**
 * Compte le nombre de commits dans le dossier étudiant
 * @param string $dossier Chemin du dossier étudiant
 * @return int Nombre de commits
 */
function getCommitCount($dossier) {
    $count = 0;
    if (is_dir($dossier)) {
        $cmd = 'git rev-list --count HEAD -- ' . escapeshellarg($dossier);
        $output = @shell_exec($cmd);
        if ($output !== null) {
            $count = intval(trim($output));
        }
    }
    return $count;
}

/**
 * Vérifie la présence d'un fichier README ou documentation dans le dossier
 * @param string $dossier Chemin du dossier étudiant
 * @return bool True si un README est présent, False sinon
 */
function hasReadme($dossier) {
    $files = ['README.md', 'README', 'readme.md', 'readme'];
    foreach ($files as $file) {
        if (file_exists($dossier . '/' . $file)) return true;
    }
    return false;
}

/**
 * Évalue l'arborescence et les fichiers du dossier (nombre de fichiers et sous-dossiers)
 * @param string $dossier Chemin du dossier étudiant
 * @return int Score sur 10
 */
function getFileTreeAndFilesScore($dossier) {
    $dirs = 0; $files = 0;
    foreach (scandir($dossier) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullpath = $dossier . '/' . $item;
        if (is_dir($fullpath)) $dirs++;
        else $files++;
    }
    return min($dirs + $files, 10); // Score sur 10
}

/**
 * Évalue le respect des bonnes pratiques (fictif, à améliorer)
 * @param string $dossier Chemin du dossier étudiant
 * @return int Score sur 10
 */
function getBestPracticesScore($dossier) {
    // À remplacer par une vraie analyse statique (PHP_CodeSniffer, etc.)
    return rand(5, 10); // Score sur 10
}

/**
 * Évalue la fonctionnalité des scripts (fictif, à améliorer)
 * @param string $dossier Chemin du dossier étudiant
 * @return int Score sur 10
 */
function getScriptFunctionalityScore($dossier) {
    // À remplacer par des tests d'exécution ou de couverture
    return rand(5, 10); // Score sur 10
}

/**
 * Évalue l'utilisation de la base de données dans le dossier
 * Recherche de fichiers .sql ou de fonctions PDO/mysqli dans le code
 * @param string $dossier Chemin du dossier étudiant
 * @return int Score sur 10
 */
function getDatabaseUsageScore($dossier) {
    $score = 0;
    foreach (scandir($dossier) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullpath = $dossier . '/' . $item;
        if (is_file($fullpath)) {
            $ext = strtolower(pathinfo($fullpath, PATHINFO_EXTENSION));
            if ($ext === 'sql') $score += 5;
            if ($ext === 'php') {
                $content = file_get_contents($fullpath);
                if (preg_match('/(PDO|mysqli|mysql_connect)/i', $content)) $score += 5;
            }
        }
    }
    return min($score, 10); // Score sur 10
}
?>
