<?php
// Calcul de la somme des 15 premiers termes (1 + 2 + ... + 15)
$sum = 0;
for ($i = 1; $i <= 15; $i++) {
    $sum += $i;
}
echo "La somme des 15 premiers termes est : $sum\n";
