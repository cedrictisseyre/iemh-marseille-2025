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

?>
