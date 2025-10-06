<?php
// Affiche la factorielle des 5 premiers chiffres (1 à 5)
function factorielle($n) {
    if ($n <= 1) return 1;
    return $n * factorielle($n - 1);
}

for ($i = 1; $i <= 5; $i++) {
    echo "Factorielle de $i : " . factorielle($i) . "<br>\n";
}
?>
