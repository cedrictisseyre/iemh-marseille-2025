<?php
/**
 * Calcule un score global sur 100 à partir de tous les critères d'évaluation
 * @param string $dossier Chemin du dossier étudiant
 * @return int Score global sur 100
 */
function getGlobalScore($dossier) {
    // Poids des critères (modifiable)
    $poidsCommits = 20;
    $poidsReadme = 10;
    $poidsArboFiles = 20;
    $poidsBestPractices = 15;
    $poidsFunctionality = 15;
    $poidsDbUsage = 20;

    // Commits (max 20 points)
    $maxCommits = 14; // plafond dynamique, à synchroniser avec index.php
    $commits = getCommitCount($dossier);
    $scoreCommits = min($commits, $maxCommits) * $poidsCommits / $maxCommits;

    // README (10 points)
    $scoreReadme = hasReadme($dossier) ? $poidsReadme : 0;

    // Arborescence et fichiers (20 points)
    if (function_exists('getFileTreeAndFilesScore')) {
        $scoreArboFiles = getFileTreeAndFilesScore($dossier) * $poidsArboFiles / 10;
    } else {
        $scoreArboFiles = 0;
    }

    // Bonnes pratiques (15 points)
    $scoreBestPractices = getBestPracticesScore($dossier) * $poidsBestPractices / 10;

    // Fonctionnalité (15 points)
    $scoreFunctionality = getScriptFunctionalityScore($dossier) * $poidsFunctionality / 10;

    // Base de données (20 points)
    $scoreDbUsage = getDatabaseUsageScore($dossier) * $poidsDbUsage / 10;

    $total = $scoreCommits + $scoreReadme + $scoreArboFiles + $scoreBestPractices + $scoreFunctionality + $scoreDbUsage;
    return round($total);
}
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
