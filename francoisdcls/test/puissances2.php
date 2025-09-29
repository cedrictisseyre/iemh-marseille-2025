<?php
// Affiche les puissances de 2^n pour n de 0 à 64
for ($n = 0; $n <= 64; $n++) {
    echo "2^$n = " . bcpow('2', (string)$n) . "\n";
}
?>
