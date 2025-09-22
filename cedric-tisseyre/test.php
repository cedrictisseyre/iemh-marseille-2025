<?php
// Fichier de test PHP
// Date : 22 septembre 2025

// Test d'une fonction simple
function addition($a, $b) {
    return $a + $b;
}

// Test d'affichage
echo "Test de PHP\n";
$resultat = addition(5, 3);
echo "5 + 3 = " . $resultat . "\n";

// Test de conditions
$heure = date('H');
if ($heure < 12) {
    echo "Bonjour, c'est le matin\n";
} else {
    echo "Bonjour, c'est l'après-midi\n";
}

// Test de boucle
echo "Comptons jusqu'à 5 :\n";
for ($i = 1; $i <= 5; $i++) {
    echo $i . "\n";
}
?>