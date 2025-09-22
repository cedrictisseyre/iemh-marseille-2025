<?php
// Fichier de test de multiplication
// Date : 22 septembre 2025

function multiplication($a, $b) {
    return $a * $b;
}

echo "Résultats de multiplication :\n";

for ($i = 1; $i <= 10; $i++) {
    for ($j = 1; $j <= 10; $j++) {
        echo "$i x $j = " . multiplication($i, $j) . "\n";
    }
}
?>